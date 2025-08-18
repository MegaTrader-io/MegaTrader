<?php
    $products_data = get_products_with_attributes();
    $attributes = $products_data['attributes'] ?? [];

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
    
    function render_account_types($account_types) {
        if (empty($account_types)) return;

        foreach ($account_types as $index => $type) {
            $slug = esc_attr($type['slug']);
            $name = esc_html($type['name']);
            $description = esc_html($type['description']);
            $thumbnail = esc_url($type['thumbnail_url']);
            $is_active = $index === 0 ? ' active' : '';
            ?>
            <div data-value="<?= $slug ?>" id="<?= $slug ?>" class="button <?= $slug . $is_active ?>">
                <img src="<?= $thumbnail ?>" alt="Icon">
                <span class="content">
                    <span class="title"><?= $name ?></span>
                    <span class="text"><?= $description ?></span>
                </span>
            </div>
            <?php
        }
    }
    
    function render_platforms($platforms) {
        if (empty($platforms)) return;

        foreach ($platforms as $index => $platform) {
            $slug = esc_attr($platform['slug']);
            $name = esc_html($platform['name']);
            $description = esc_html($platform['description']);
            $thumbnail = esc_url($platform['thumbnail_url']);

            $comingSoon = '';
            if (!empty($platform['attribute_meta']) && is_array($platform['attribute_meta'])) {
                foreach ($platform['attribute_meta'] as $meta) {
                    $normalized = strtolower(str_replace(' ', '', trim($meta)));
                    if ($normalized === 'comingsoon') {
                        $comingSoon = ' coming-soon';
                        break;
                    }
                }
            }

            $is_active = $index === 0 ? ' active' : '';

            ?>
            <div data-value="<?= $slug ?>" id="<?= $slug ?>" class="button <?= $slug . $is_active . $comingSoon?>">
                <img src="<?= $thumbnail ?>" alt="Icon" width="57" height="57">
                <span class="content">
                    <?php if ($comingSoon): ?> <span class="mbadge">Coming Soon</span> <?php endif; ?>
                    <span class="title"> <?= $name ?> </span>
                    <span class="text"><?= $description ?></span>
                </span>
            </div>
            <?php
        }
    }

    function render_account_sizes($account_sizes) {
        if (empty($account_sizes)) return;

        foreach ($account_sizes as $index => $size) {
            $slug = esc_attr($size['slug']); // e.g. 25k
            $name = esc_html($size['name']); // e.g. $25.000
            $description = esc_html($size['description']);
            $thumbnail = esc_url($size['thumbnail_url']);
            $price = ''; // this price is updated in the frontend based on selection [type][platform][size]
            $is_active = $index === 0 ? ' active' : '';

            ?>
            <div data-value="<?= $slug ?>" id="<?= $slug ?>" class="button<?= $is_active ?>">
                <img src="<?= $thumbnail ?>" alt="Icon" width="57" height="57">
                <span class="content">
                    <span class="title">
                        <span class="label"><?= $name ?></span>
                        <span class="mbadge d-none">On sale</span> 
                        <b><span class="a-price"><?= $price ?></span> / Monthly</b>
                    </span>
                    <span class="text"><?= $description ?></span>
                </span>
            </div>
            <?php
        }
    }
?>

<style>
    /* To Migrate to CSS */ 
    .mt-tabs {
        background: red;
    }
</style>

<div class="choose-subscription">
    <div class="mt-tabs">
        <div class="mt-tabs__container">
            <div class="mt-tabs__header">
                <div class="mt-tabs__header__item">
                    Futures
                </div>
                <div class="mt-tabs__header__item">
                    Forex
                </div>
                <div class="mt-tabs__header__item">
                    Crypto
                </div>
            </div>
            <div class="mt-tabs__body">
                <div class="mt-tabs__body__item">
                    Futures Content
                </div>
                <div class="mt-tabs__body__item">
                    Forex Content
                </div>
                <div class="mt-tabs__body__item">
                    Crypto Content
                </div>
            </div>
        </div>
    </div>
</div>