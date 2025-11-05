<?php

$card_product_id = $args['card_product_id'] ?? null;

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
$attributes = $product->get_attributes();

$account_size = $attributes['pa_account-size'];
$slug_account_type = $attributes['pa_account-types'];

$index = array_search($slug_account_type, array_column($account_types, 'slug'));
$account_type = $index !== false ? $account_types[$index] : null;

$platform_slug = $attributes['pa_platform'];

$index = array_search($platform_slug, array_column($platforms, 'slug'));
$platform = $index !== false ? $platforms[$index] : null;

$parsed = parse_attribute_meta($account_type['attribute_meta'] ?? []);
$features = $parsed['data'] ?? [];

$account_thumbnail_url = $account_type['thumbnail_url'];
$platform_url = $platform['thumbnail_url'];
?>

<div class="plan-detail-selection">
    <div class="plan-detail-selection__title">
        Selected Plan Details
    </div>

    <div class="plan-detail-selection__card mt-card active">
        <div class="plan-detail-selection__header">
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
                <img class="plan-detail-selection__checked"
                     src="<?= esc_url(get_template_directory_uri() . '/assets/img/landing-page/checked-circle.svg'); ?>"
                     alt="Checked Circle">
            </div>
        </div>

      <?php if (count($features) > 0): ?>
          <ul class="plan-detail-selection__features-list mt-card__check-list disabled-target">
            <?php foreach ($features as $text): ?>
                <li class="plan-detail-selection__features-item">
                    <i class="mt-icon mt-icon_checkmark mt-icon-primary"></i>
                    <span class="plan-detail-selection__features-text"><?= esc_html($text); ?></span>
                </li>
            <?php endforeach; ?>
          </ul>
      <?php endif; ?>
    </div>

    <div class="plan-detail-selection__note">
        You're just one step away from activating your plan and starting to trade with confidence.
    </div>
</div>
