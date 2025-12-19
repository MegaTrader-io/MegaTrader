<?php
/** @var array $account_types */
$account_types = $args['account_types'] ?? [];

/** @var string $mt_default_platform */
$mt_default_platform = $args['mt_default_platform'] ?? '';

/** @var string $mt_default_market_type */
$mt_default_market_type = $args['mt_default_market_type'] ?? '';
?>

<div class="mt-pricing-table-plan-options">
    <?php foreach ($account_types as $index => $item): ?>
        <?php
        $slug = esc_attr($item['slug']);
        $name = esc_html($item['name']);
        $thumbnail = esc_url($item['thumbnail_url']);
        $radio_id = esc_attr('account-type-' . $slug);
        $checked_attr = $index === 0 ? 'checked="true"' : '';
        ?>

        <input type="radio" name="account-type"
               data-default-platform="<?= $mt_default_platform ?>"
               data-default-market-type="<?= $mt_default_market_type ?>"
               value="<?= $slug ?>" id="<?= $radio_id ?>" <?= $checked_attr ?>/>
        <div class="mt-pricing-table-plan-options__item">
            <label for="<?= $radio_id ?>">
                <?php if ($thumbnail): ?>
                    <img class="mt-pricing-table-plan-options__icon" src="<?= $thumbnail; ?>" alt="Icon">
                <?php endif; ?>

                <div class="mt-pricing-table-plan-options__text">
                    <?= $name ?>
                </div>
            </label>
        </div>

    <?php endforeach; ?>
</div>


<div class="mt-select-ac-type mt-dropdown dropdown w-100" style="margin-inline: 16px;max-width: calc(100% - 32px);">
    <button class="btn w-100 mega-btn-md mega-btn-secondary-md dropdown-toggle"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false">

        <?php
        $mt_default_account_type = $account_types[0];
        $mt_account_thumbnail_url = $mt_default_account_type['thumbnail_url'];
        $mt_default_radio_id = esc_attr('account-type-' . $mt_default_account_type['slug']);

        $slug = esc_attr($mt_default_account_type['slug']);
        $name = esc_html($mt_default_account_type['name']);
        $thumbnail = esc_url($mt_default_account_type['thumbnail_url']);
        $radio_id = esc_attr('account-type-' . $slug);
        $checked_attr = $index === 0 ? 'checked="true"' : '';

        $parsed = parse_attribute_meta($mt_default_account_type['attribute_meta'] ?? []);
        $config = isset($parsed['config']) ? $parsed['config'] : [];
        $badge = isset($parsed['config']['badge']) ? $parsed['config']['badge'] : [];
        ?>

        <div class="mt-select-ac-type__selection d-flex flex-1-1-0" data-account-type-slug="<?= $slug ?>">
            <div class="mt-dropdown__btn-inner">
                <img class="mt-dropdown__btn-icon" src="<?= $thumbnail; ?>" alt="Icon" width="20" height="20">

                <div class="mt-dropdown__btn-label"><?= $name ?></div>
            </div>

            <?php if ($badge):
                $badge_style_class = isset($badge['style']) ? 'mt-badge-' . $badge['style'] : 'mt-badge-light';
                $badge_text = $badge['text'] ?? '';
                ?>
                <div class="mt-dropdown__badge mt-card__badge mt-badge mt-badge-rounded-sm <?= $badge_style_class ?>"><?= $badge_text ?></div>
            <?php endif; ?>
        </div>

    </button>

    <ul class="dropdown-menu dropdown-menu--without-arrow w-100 mt-dropdown__menu">
        <?php foreach ($account_types as $index => $item): ?>
            <?php
            $slug = esc_attr($item['slug']);
            $name = esc_html($item['name']);
            $thumbnail = esc_url($item['thumbnail_url']);
            $radio_id = esc_attr('account-type-' . $slug);
            $checked_attr = $index === 0 ? 'checked="true"' : '';

            $parsed = parse_attribute_meta($item['attribute_meta'] ?? []);
            $config = isset($parsed['config']) ? $parsed['config'] : [];
            $badge = isset($parsed['config']['badge']) ? $parsed['config']['badge'] : [];
            ?>

            <li>

                <div class="dropdown-item__wrapper" data-account-type-slug="<?= $slug ?>">
                    <label for="<?= $radio_id ?>" class="dropdown-item mt-dropdown__item"
                           href="javascript:void(0);">
                        <?php if ($thumbnail): ?>
                            <img src="<?= $thumbnail; ?>" alt="Icon" width="20" height="20">
                        <?php endif; ?>

                        <div class="mt-dropdown__item-label"><?= $name ?></div>

                        <?php if ($badge):
                            $badge_style_class = isset($badge['style']) ? 'mt-badge-' . $badge['style'] : 'mt-badge-light';
                            $badge_text = isset($badge['text']) ? $badge['text'] : '';
                            ?>
                            <div class="mt-card__badge mt-badge mt-badge-rounded-sm <?= $badge_style_class ?>"><?= $badge_text ?></div>
                        <?php endif; ?>
                    </label>
                </div>
            </li>

        <?php endforeach; ?>
    </ul>
</div>
