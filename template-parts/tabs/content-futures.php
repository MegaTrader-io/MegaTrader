<?php

    $products_data = get_products_with_attributes();
    $attributes = $products_data['attributes'] ?? [];

    //TODO: remove
    $json = wp_json_encode( $products_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

    $account_sizes = [];
    $account_types = [];
    $platforms = [];

    foreach ($attributes as $attr) {
        switch ($attr['taxonomy']) {
            case 'pa_account-size':
                $account_sizes[] = $attr;
                break;
            case 'pa_account-types':
                $account_types[] = $attr;
                break;
            case 'pa_platform':
                $platforms[] = $attr;
                break;
        }
    }
    
    function render_account_sizes($account_sizes) {
        if (empty($account_sizes)) return;

        foreach ($account_sizes as $index => $item) {
            $slug = esc_attr($item['slug']);
            $name = esc_html($item['name']);
            $description = esc_html($item['description']);
            
            $checked_attr = $index === 0 ? 'checked="true"' : '';

            $parsed = parse_attribute_meta($item['attribute_meta'] ?? []);
            $config = isset($parsed['config']) ? $parsed['config'] : [];
            $badge = isset($parsed['config']['badge']) ? $parsed['config']['badge'] : [];
            $is_disabled = isset($config['status']) && $config['status'] === 'disabled';
            $disabled_class = $is_disabled ? ' disabled-within' : '';
            $radio_id = esc_attr('account-size-' . $slug);
            ?>
            <input type="radio" name="account-size" value="<?= $slug ?>" id="<?= $radio_id ?>" hidden <?= $checked_attr ?>">
            <div class="radio__label__wrapper">
                <label class="mt-card mt-card-dark mt-card-radio <?= $slug . $disabled_class?>"
                    for="<?= $radio_id ?>" 
                    data-value="<?= $slug ?>" 
                    title="<?= $description ?>"
                >
                    <div class="mt-card__header">
                        <i class="mt-card__radio disabled-target"></i>
                        <?php if ($badge): 
                            $badge_style_class =  isset($badge['style']) ? 'mt-badge-' . $badge['style'] : 'mt-badge-light';
                            $badge_text =  isset($badge['text']) ? $badge['text'] : '';
                        ?>
                            <div class="mt-card__badge mt-badge <?= $badge_style_class ?>"><?= $badge_text ?></div>
                        <?php endif; ?>    
                    </div>
                    <div class="mt-card__title disabled-target justify-content-center">
                        <span class="mt-card__title__text"><?= esc_html($name); ?></span>
                    </div>
                    <?php if (!empty($parsed['data'])): ?>
                        <ul class="mt-card__check-list disabled-target">
                            <?php foreach ($parsed['data'] as $text): ?>
                                <li class="mt-card__check-list__item">
                                    <i class="mt-icon mt-icon_checkmark mt-icon-primary"></i>
                                    <span class="mt-card_check-list__item__text"><?= esc_html($text); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </label>
            </div>
            <?php
        }
    }

    function render_account_types($account_types) {
        if (empty($account_types)) return;

        foreach ($account_types as $index => $item) {
            $slug = esc_attr($item['slug']);
            $name = esc_html($item['name']);
            $description = esc_attr($item['description']);
            $thumbnail = esc_url($item['thumbnail_url']);
            
            $checked_attr = $index === 0 ? 'checked' : '';

            $parsed = parse_attribute_meta($item['attribute_meta'] ?? []);
            $config = isset($parsed['config']) ? $parsed['config'] : [];
            $badge = isset($parsed['config']['badge']) ? $parsed['config']['badge'] : [];

            $is_disabled = isset($config['status']) && $config['status'] === 'disabled';
            $disabled_class = $is_disabled ? ' disabled-within' : '';
            $radio_id = esc_attr('account-type-' . $slug);
            ?>
            <input type="radio" name="account-type" value="<?= $slug ?>" id="<?= $radio_id ?>" hidden <?= $checked_attr ?>/>
            <div class="radio__label__wrapper">
                <label class="mt-card mt-card-dark mt-card-md mt-card-radio <?= $slug . $disabled_class?>"
                    for="<?= $radio_id ?>" 
                    data-value="<?= $slug ?>" 
                    title="<?= $description ?>"
                >
                    <div class="mt-card__header">
                        <i class="mt-card__radio disabled-target"></i>
                        <?php if ($badge): 
                            $badge_style_class =  isset($badge['style']) ? 'mt-badge-' . $badge['style'] : 'mt-badge-light';
                            $badge_text =  isset($badge['text']) ? $badge['text'] : '';
                        ?>
                            <div class="mt-card__badge mt-badge <?= $badge_style_class ?>"><?= $badge_text ?></div>
                        <?php endif; ?>    
                    </div>
                    <div class="mt-card__title disabled-target">
                        <?php if ($thumbnail): ?>
                            <img class="mt-card__title__icon" src="<?= $thumbnail; ?>" alt="Icon">
                        <?php endif; ?>   
                        <span class="mt-card__title__text"><?= $name; ?></span>
                    </div>
                    <?php if (!empty($parsed['data'])): ?>
                        <ul class="mt-card__check-list disabled-target">
                            <?php foreach ($parsed['data'] as $text): ?>
                                <li class="mt-card__check-list__item">
                                    <i class="mt-icon mt-icon_checkmark mt-icon-primary"></i>
                                    <span class="mt-card_check-list__item__text"><?= esc_html($text); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </label>
            </div>
            <?php
        }
    }
    
    function render_platforms($platforms) {
        if (empty($platforms)) return;

        foreach ($platforms as $index => $item) {
            $slug = esc_attr($item['slug']);
            $name = esc_html($item['name']);
            $description = esc_html($item['description']);
            $thumbnail = esc_url($item['thumbnail_url']);

            $comingSoon = '';
            if (!empty($item['attribute_meta']) && is_array($item['attribute_meta'])) {
                foreach ($item['attribute_meta'] as $meta) {
                    $normalized = strtolower(str_replace(' ', '', trim($meta)));
                    if ($normalized === 'comingsoon') {
                        $comingSoon = ' coming-soon';
                        break;
                    }
                }
            }

            $checked_attr = $index === 0 ? 'checked' : '';

            $parsed = parse_attribute_meta($item['attribute_meta'] ?? []);
            $config = isset($parsed['config']) ? $parsed['config'] : [];
            $badge = isset($config['badge']) ? $config['badge'] : [];
            $is_disabled = isset($config['status']) && $config['status'] === 'disabled';
            $disabled_class = $is_disabled ? ' disabled-within' : '';
            $radio_id = esc_attr('platform-' . $slug);
            ?>
            <input type="radio" name="platform" value="<?= $slug ?>" id="<?= $radio_id ?>" hidden <?= $checked_attr ?>/>
            <div class="radio__label__wrapper">
                <label class="mt-card mt-card-dark mt-card-md mt-card-radio <?= $slug . $disabled_class?>"
                    for="<?= $radio_id ?>" 
                    data-value="<?= $slug ?>" 
                    title="<?= $description ?>"
                >
                    <div class="mt-card__header">
                        <i class="mt-card__radio disabled-target"></i>
                        <?php if ($badge): 
                            $badge_style_class =  isset($badge['style']) ? 'mt-badge-' . $badge['style'] : 'mt-badge-light';
                            $badge_text =  isset($badge['text']) ? $badge['text'] : '';
                        ?>
                            <div class="mt-card__badge mt-badge <?= $badge_style_class ?>"><?= $badge_text ?></div>
                        <?php endif; ?> 
                    </div>
                    <div class="mt-card__title disabled-target">
                        <?php if ($thumbnail): ?>
                            <img class="mt-card__title__icon" src="<?= $thumbnail; ?>" alt="Icon">
                        <?php endif; ?>   
                        <span class="mt-card__title__text"><?= $name; ?></span>
                    </div>
                    <?php if (!empty($parsed['data'])): ?>
                        <ul class="mt-card__check-list disabled-target">
                            <?php foreach ($parsed['data'] as $text): ?>
                                <li class="mt-card__check-list__item">
                                    <i class="mt-icon mt-icon_checkmark mt-icon-primary"></i>
                                    <span class="mt-card__check-list__item__text"><?= esc_html($text); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <?php if (!empty($config['links'])): ?>
                        <div class="mt-card__links disabled-target">
                            <?php foreach ($config['links'] as $link): 
                                $href = isset($link['url']) ? $link['icon'] : 'javascript:void(0);';
                            ?>
                            <a class="mt-card__links__item" href="<?= esc_attr($href); ?>">
                                <div class="mt-badge mt-badge-md mt-badge-pill mt-badge-dark">
                                <?php if(isset($link['icon'])): ?>
                                    <i class="mt-icon mt-icon_<?= esc_attr($link['icon']) ?>"></i>
                                <?php endif; ?>
                                <?php if(isset($link['text'])): ?>
                                    <span class="mt-card__links__text"><?= esc_html($link['text']); ?></span>
                                <?php endif; ?>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </label>
            </div>
            <?php
        }
    }
  
 ?>

<form class="futures-form d-flex flex-column gap-32" id="futures-form">
    <input type="hidden" name="pa_market-type" value="futures" />
    <section class="product-section" id="account-size">
        <h2 class="product-section__header mb-3">
            <i class="mt-icon mt-icon_wallet mt-icon-md mt-icon-primary" aria-hidden="true"></i>
            <span class="product-section__title"><?= Label::FUTURES['size_section_title']; ?></span>
        </h2>
        <div class="product-section__list product-section__list_grid">
            <?php render_account_sizes($account_sizes); ?>
        </div>
    </section>
    <section class="product-section" id="account-type">
        <h2 class="product-section__header mb-3">
            <i class="mt-icon mt-icon_lightning mt-icon-md mt-icon-primary" aria-hidden="true"></i>
            <span class="product-section__title"><?= Label::FUTURES['plan_section_title']; ?></span>
        </h2>
        <div class="product-section__list">
            <?php render_account_types($account_types); ?>
        </div>
    </section>
    <section class="product-section" id="account-platform">
        <h2 class="product-section__header mb-3">
            <i class="mt-icon mt-icon_grid mt-icon-md mt-icon-primary" aria-hidden="true"></i>
            <span class="product-section__title"><?= Label::FUTURES['platform_section_title']; ?></span>
        </h2>
        <div class="product-section__list">
            <?php render_platforms($platforms); ?>
        </div>
    </section>
    <?php 
        $plan_includes_list = Label::FUTURES['plan_includes_list'];
        if (!empty($plan_includes_list) && count($plan_includes_list) > 0): ?>
        <section class="plan-includes">
            <h2 class="plan-includes__title mb-3"><?= Label::FUTURES['plan_includes_title']; ?></h2>
            <ul class="plan-includes__list row list-reboot">
            <?php foreach ($plan_includes_list as $item) : ?>
                <li class="plan-includes-list__item col-6 py-1 d-flex align-items-center gap-2">
                    <i class="mt-icon mt-icon_<?= htmlspecialchars($item['icon']); ?> mt-icon-primary flex-shrink-0" aria-hidden="true"></i>
                    <span><?= htmlspecialchars($item['text']); ?></span>
                </li>
            <?php endforeach; ?>
            </ul>
        </section>
    <?php endif; ?>
    <hr class="m-0">
    <button class="mega-btn-md mega-btn-primary-md"><?= Label::FUTURES['submit_btn_text'] ?></button>
</form>


<script>
  const products = <?= wp_json_encode( $products_data['products'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ); ?>;
  console.log({ products });
  
  function updateSelectedProduct(){

  }

  document.querySelectorAll('.product-section input[type="radio"]').forEach( radio => {
    radio.addEventListener('change', checkedRadio => {
        // console.log({checkedRadio})
    })
  })

const form = document.getElementById("futures-form");

function getFormValues(formEl) {
  const data = {};
  const formData = new FormData(formEl);

  for (const [key, value] of formData.entries()) {
    data[key] = value;
  }
  return data;
}

// validate whenever something changes
form.addEventListener("change", () => {
  const values = getFormValues(form);
  console.log("Form values:", values);

});
</script>



