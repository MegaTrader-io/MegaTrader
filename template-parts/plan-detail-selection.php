<?php

/** @var int|null $card_product_id */
$card_product_id = $args['card_product_id'] ?? null;

function render_plan_detail_selection($card_product_id): void
{
  if (!$card_product_id) return;

  $products_data = get_products_with_attributes();
  $attributes = $products_data['attributes'] ?? [];
  $account_types = $platforms = [];

  foreach ($attributes as $attr) {
    switch ($attr['taxonomy']) {
      case 'pa_account-types':
        $account_types[] = $attr;
        break;
      case 'pa_platform':
        $platforms[] = $attr;
        break;
    }
  }

  $product = wc_get_product($card_product_id);

  if (!$product) {
    return;
  }

  $attributes = $product->get_attributes();

  $account_size = $attributes['pa_account-size'];
  $slug_account_type = $attributes['pa_account-types'];
  $platform_slug = $attributes['pa_platform'];

  $mt_filtered_products = array_filter($products_data['products'], function ($mt_product) use ($slug_account_type) {
    return $mt_product['slug'] === $slug_account_type;
  });

  $product_data = reset($mt_filtered_products) ?: null;

  $mt_property = $product_data[$attributes['pa_account-types']][$account_size][$slug_account_type][$platform_slug][$attributes['pa_market-type']];

  $mt_meta_info_list = $mt_default_meta_info = [];
  foreach ($mt_property as $mt_property_item) {
    if (!isset($mt_property_item['meta-info'])) {
      continue;
    }

    $mt_meta_info_list = $mt_property_item['meta-info'];
  }

  foreach (Label::PRODUCT_META as $mt_key => $mt_label) {
    if (isset($mt_meta_info_list[$mt_key]) && $mt_meta_info_list[$mt_key]) {
      $mt_default_meta_info[$mt_key] = true;
    }
  }

  $index = array_search($slug_account_type, array_column($account_types, 'slug'));
  $account_type = $index !== false ? $account_types[$index] : null;


  $index = array_search($platform_slug, array_column($platforms, 'slug'));
  $platform = $index !== false ? $platforms[$index] : null;

  $account_thumbnail_url = $account_type['thumbnail_url'];
  $platform_url = $platform['thumbnail_url'];

  function render_meta_info($value = '', $label = '')
  {
    $check_icon = get_template_directory_uri() . '/assets/img/landing-page/check.svg';
    return <<<HTML
<img src="{$check_icon}" alt="check" style="width: 24px; height: 24px">
<div class="mega-info-row__label">{$label}: {$value}</div>
HTML;
  }

  ?>

    <div class="plan-detail-selection">
        <div class="plan-detail-selection__title">
            Selected Plan Details
        </div>

        <div class="plan-detail-selection__card mt-card">
            <div class="btn-plan-detail plan-detail-selection__header">
                <img class="plan-detail-selection__account-icon"
                     src="<?= esc_url($account_thumbnail_url); ?>"
                     alt="Plan Icon">
                <img class="plan-detail-selection__platform-icon"
                     src="<?= esc_url($platform_url); ?>"
                     alt="Platform Icon">

                <div class="plan-detail-selection__name">
                  <?= strtoupper($account_size . ' ' . $account_type['name']); ?>
                </div>

                <div class="plan-detail-selection__checked-wrapper">
                    <i class="mt-icon mt-icon-white mt-icon_caret-up-solid" style="width: 30px;height: 30px;"></i>
                </div>
            </div>

            <div class="plan-detail-selection__body">
                <div class="plan-detail-selection__features-title">
                    Objectives and Rules
                </div>

                <ul class="plan-detail-selection__features-list mt-card__check-list disabled-target">
                  <?php foreach ($mt_default_meta_info as $mt_field => $mt_value): ?>
                      <li class="plan-detail-selection__features-item">
                        <?php
                        $mt_label = Label::PRODUCT_META[$mt_field];
                        $mt_value = $mt_meta_info_list[$mt_field];
                        echo render_meta_info(value: $mt_value, label: $mt_label);
                        ?>
                      </li>
                  <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="plan-detail-selection__note">
            You're just one step away from activating your plan and starting to trade with confidence.
        </div>
    </div>

    <script>
        const header = document.querySelector('.btn-plan-detail');
        const body = header.nextElementSibling;

        header.addEventListener('click', (e) => {
            const el = e.currentTarget;
            const isActive = el.classList.toggle('btn-plan-detail--active');

            if (isActive) {
                body.style.height = body.scrollHeight + 'px';
                body.addEventListener(
                    'transitionend',
                    () => (body.style.height = 'auto'),
                    {once: true}
                );
            } else {
                body.style.height = body.scrollHeight + 'px';
                requestAnimationFrame(() => {
                    body.style.height = '0';
                });
            }
        });

        // const authLayout = document.querySelector('.auth');
        //
        // authLayout.style.height = '100%';
        // authLayout.style.maxHeight = '100%';
    </script>

  <?php
}

render_plan_detail_selection($card_product_id);