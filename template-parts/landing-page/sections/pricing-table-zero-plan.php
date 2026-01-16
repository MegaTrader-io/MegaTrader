<?php
$page_slug = pathinfo(__FILE__, PATHINFO_FILENAME);
$remember_previous_selection = true;

/**
 * 1. Cargar data base de WooCommerce
 */
$mt_products_data = get_products_with_attributes();
$mt_attributes = $mt_products_data['attributes'] ?? [];
$mt_products_raw = $mt_products_data['products'] ?? [];

/**
 * 2. Clasificar atributos por taxonomía
 */
$mt_account_sizes = [];
$mt_account_types = [];
$mt_platforms = [];
$mt_market_types = [];
$mt_billing_types = [];

foreach ($mt_attributes as $attr) {
    switch ($attr['taxonomy']) {
        case 'pa_market-type':
            $mt_market_types[] = $attr;
            break;
        case 'pa_account-size':
            $mt_account_sizes[] = $attr['slug'];
            break;
        case 'pa_account-types':
            $mt_account_types[] = $attr;
            break;
        case 'pa_billing-type':
            $mt_billing_types[] = $attr;
            break;
        case 'pa_platform':
            $mt_platforms[] = $attr;
            break;
    }
}

/**
 * 3. Definir valores iniciales
 */
$mt_default_market_type = $mt_market_types[0] ?? null;
$mt_default_platform = $mt_platforms[0] ?? null;
$mt_platform_thumbnail_url = $mt_default_platform['thumbnail_url'] ?? '';
$mt_default_market_type_slug = $mt_default_market_type ?? [];
$market_types_allowed = [];

/**
 * 4. Filtrar productos válidos (excluye -fee)
 */
$mt_products = array_values(array_filter($mt_products_raw, function ($product) use (&$market_types_allowed) {
    $slug = $product['slug'] ?? '';
    if (str_ends_with($slug, '-fee')) {
        return false;
    }

    $marketType = $product['tree_map']['market-type'] ?? null;
    if ($marketType && !in_array($marketType, $market_types_allowed, true)) {
        $market_types_allowed[] = $marketType;
    }

    return true;
}));

/**
 * 5. Filtrar market types según los productos existentes
 */

$mt_market_types = array_values(array_filter(
        $mt_market_types,
        fn($type) => in_array($type['slug'], $market_types_allowed, true)
));


/**
 * 6. Filtrar productos por market-type por defecto
 */
$account_sizes_allowed = [];
$account_types_allowed = [];

$mt_filtered_products = array_values(array_filter($mt_products, function ($product) use (
        $mt_default_market_type, $mt_account_types, $mt_account_sizes,
        &$account_sizes_allowed, &$account_types_allowed
) {
    if ($product['tree_map']['account-types'] != 'zero-plan') {
        return false;
    }

    $slug = $product['slug'];
    $variants = $product[$slug] ?? [];

    foreach ($mt_account_sizes as $size) {
        if (!isset($variants[$size])) {
            continue;
        }

        if (!in_array($size, $account_sizes_allowed, true)) {
            $account_sizes_allowed[] = $size;
        }

        foreach ($mt_account_types as $type) {
            $type_slug = $type['slug'];
            if (isset($variants[$size][$type_slug]) && !in_array($type_slug, $account_types_allowed, true)) {
                $account_types_allowed[] = $type_slug;
            }
        }
    }

    return true;
}));

/**
 * 7. Definir account types y sizes finales
 */
$mt_account_types = array_values(array_filter(
        $mt_account_types,
        fn($type) => in_array($type['slug'], $account_types_allowed, true)
));

$mt_default_account_type = $mt_account_types[0] ?? [];
$mt_default_slug = $mt_default_account_type['slug'] ?? '';
$mt_account_thumbnail_url = $mt_default_account_type['thumbnail_url'] ?? '';
$mt_account_sizes = $account_sizes_allowed;
$mt_size = $mt_account_sizes[0] ?? '';

/**
 * 8. Agrupar market-type → account-types (para los tabs)
 */
$grouped = [];
foreach ($mt_products as $product) {
    $map = $product['tree_map'] ?? [];
    $marketType = $map['market-type'] ?? null;
    $accountType = $map['account-types'] ?? null;

    if (!$marketType || !$accountType) continue;

    if (!isset($grouped[$marketType])) {
        $grouped[$marketType] = [
                'market-type' => $marketType,
                'account-types' => [],
        ];
    }

    if (!in_array($accountType, $grouped[$marketType]['account-types'], true)) {
        $grouped[$marketType]['account-types'][] = $accountType;
    }
}

$result = array_values($grouped);
$current_market_type = array_values(array_filter(
        $result,
        fn($item) => $item['market-type'] === $mt_default_market_type_slug['slug']
))[0] ?? [];

$mt_account_types = array_values(array_filter(
        $mt_account_types,
        fn($type) => in_array($type['slug'], $current_market_type['account-types'] ?? [], true)
));

/**
 * 9. Construcción de lista de planes
 */
$mt_product = array_find($mt_filtered_products, function ($product) use ($mt_default_slug) {
    return isset($product['tree_map']) && isset($product['tree_map']['account-types']) && $product['tree_map']['account-types'] === $mt_default_slug;
});

$mt_plan_list = [];
$mt_default_meta_info = [];
$has_coupon_global = null;

if ($mt_product && isset($mt_product[$mt_product['slug']][$mt_size][$mt_default_slug])) {
    $mt_product_level = $mt_product[$mt_product['slug']][$mt_size][$mt_default_slug];
    $mt_default_platform = array_key_first($mt_product_level);
    $mt_default_market_type = array_key_first($mt_product_level[$mt_default_platform]);

    $mt_slug = $mt_product['slug'] ?? null;
    $parent_id = $mt_product['id'];

    foreach ($mt_account_sizes as $size) {
        $properties = [];
        foreach (array_keys($mt_product['tree_map']) as $attr) {
            $properties = count($properties) === 0 ? array_values($mt_product[$mt_slug][$size]) : array_values($properties)[0];
        }

        if (count($properties) == 0) continue;

        $variation_id = -1;
        $price = '0.00';
        $meta_info = [];

        foreach ($properties as $property) {
            foreach ($property as $key => $value) {
                match ($key) {
                    'id' => $variation_id = $value,
                    'price-monthly' => $price = (int)str_replace('$', '', $value),
                    'meta-info' => $meta_info = $value,
                    default => null
                };
            }
        }

        foreach (Label::PRODUCT_META as $meta_key => $meta_label) {
            if (!empty($meta_info[$meta_key])) {
                $mt_default_meta_info[$meta_key] = true;
            }
        }

        $mt_plan_list[] = [
                'id' => $variation_id,
                'parent_id' => $parent_id,
                'price' => $price,
                'size' => $size,
                'meta_info_list' => $meta_info
        ];
    }
}

/**
 * 10. Productos más populares
 */
$mt_best_products = mt_most_popular_products();

/**
 * 11. Helper de renderizado de meta info
 */
function mt_render_template_meta_info($value = '', $label = '', $classes = '')
{
    return <<<HTML
<div class="mega-info-row {$classes}">
    <div class="mega-info-row__label">{$label}</div>
    <div class="mega-info-row__value text-truncate">{$value}</div>
</div>
HTML;
}

?>

<section id="pricing" class="zero-pricing-table pricing-table-container landing-bs-container">
    <header class="pricing-table-container__header-wrapper text-center">
        <h2 class="pricing-table-container__title">
            Choose <span class="pricing-table-container__title--hidden-md text-white">your</span> <span>account</span>
            type
        </h2>
        <p class="pricing-table-container__subtitle">
            Choose from flexible account sizes and plans tailored to your trading style—whether you're growing your
            skills or ready to trade real capital with confidence.
        </p>
    </header>

    <div class="pricing-table-container-options d-none">
        <?php
        get_template_part("template-parts/landing-page/sections/select-account-type", null, [
                'mt_extra_classes' => 'd-none',
                'account_types' => $mt_account_types,
                'mt_default_platform' => $mt_default_platform,
                'mt_default_market_type' => $mt_default_market_type,
        ]);
        ?>

        <div class="mt-pricing-table-plan-options__wrapper">
            <?php
            foreach ($mt_account_types as $index => $item) {
                $slug = esc_attr($item['slug']);
                $parsed = parse_attribute_meta($item['attribute_meta'] ?? []);

                ?>

                <?php if (!empty($parsed['data'])): ?>
                    <div class="mt-pricing-table-benefits d-none" data-account-type-benefits="<?= $slug ?>">
                        <?php foreach ($parsed['data'] as $index => $text): ?>
                            <?php if ($index > 0): ?>
                                <img class="mt-pricing-table-benefits__icon"
                                     src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/quick-flash.svg'); ?>"
                                     alt="flash" width="24" height="24">
                            <?php endif; ?>
                            <div class="mt-pricing-table-benefits__item"><?php echo esc_html($text) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php
            }
            ?>
        </div>
    </div>

    <div class="price-table price-table__glide slider glide"
         style="--price-table-slide-width: 0px; --price-table-slide-left: 0px;--current-slider-height: 0px">
        <div class="slider__track glide__track" data-glide-el="track">
            <ul class="slider__slides glide__slides">
                <?php foreach ($mt_plan_list as $mt_index => $mt_plan) : ?>
                    <?php
                    $mt_id = $mt_plan['id'];
                    $mt_size = $mt_plan['size'];
                    $mt_parent_id = $mt_plan['parent_id'];
                    $mt_price = $mt_plan['price'];
                    $mt_meta_info_list = $mt_plan['meta_info_list'];

                    $mt_coupon = mt_get_best_coupon_for_variation($mt_id);
                    $mt_has_coupon = false;
                    $mt_price_plan = $mt_price;

                    $mt_scan_product = $mt_best_products[$mt_parent_id];
                    $mt_is_most_popular = $mt_scan_product && $mt_scan_product['variation_id'] == $mt_id;

                    $CHECKOUT_URL = home_url('/checkout/?add-to-cart=' . $mt_id);
                    $URL_GO_TO = home_url('/auth/register/?redirect_to=');

                    $mt_get_plan_url = $URL_GO_TO . $CHECKOUT_URL;
                    if (is_user_logged_in()) {
                        $mt_get_plan_url = $CHECKOUT_URL;
                    }

                    ?>
                    <li class="slider__frame glide__slide">
                        <div class="price-table__plan <?= $mt_is_most_popular ? 'price-table__plan--most-popular' : 'price-table__plan--regular-plan' ?>"
                             data-price="<?= esc_attr($mt_size) ?>">
                            <div class="price-table__size">
                                <div class="price-table__most-popular-badge">
                                    <div class="price-table__most-popular-badge-wrapper">
                                        <img src="<?php echo get_template_directory_uri() . '/assets/img/landing-page/flash.svg'; ?>"
                                             class="price-table__most-popular-badge-icon"
                                             alt="flash"
                                             width="24"
                                             height="24">
                                        <div class="price-table__most-popular-badge-text"><?php esc_html_e('Most popular', 'megatrader'); ?></div>
                                    </div>
                                </div>
                                <div class="price-table__title">
                                    <?= esc_html($mt_size) ?> Account
                                </div>
                            </div>

                            <div class="price-table__right-line price-information"
                                 data-price="<?= esc_attr($mt_size) ?>">
                                <div class="w-100">
                                    <div style="display: none;"
                                         class="price-information__summary">
                                        <div class="coupon-before-price" data-price="<?= esc_attr($mt_size) ?>">
                                            <span></span>
                                        </div>
                                        <div class="badge-coupon w-100" data-price="<?= esc_attr($mt_size) ?>">
                                            <div class="badge-coupon__wrapper">
                                                <div class="badge-coupon__text text-truncate">
                                                    <?php esc_html_e('Save', 'megatrader'); ?>
                                                    <span class="badge-coupon__discount_total">
                                            <?= $mt_has_coupon ? mt_price_plain($mt_coupon['discount_total']) : 0 ?>
                                        </span>
                                                    <?php esc_html_e('with code', 'megatrader'); ?>
                                                </div>
                                                <svg width="1" height="24" viewBox="0 0 1 24" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <line x1="0.5" y1="0" x2="0.5" y2="24" stroke="#404040"/>
                                                </svg>
                                                <div class="badge-coupon__code">
                                                    <?= $mt_has_coupon ? esc_html(strtoupper($mt_coupon['coupon'])) : '' ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="price-information__price">
                            <span class="price-plan" data-price="<?= esc_attr($mt_size) ?>">
                                <?= mt_price_plain($mt_price_plan) ?>
                            </span>
                                        <span class="frequency-plan" data-price="<?= esc_attr($mt_size) ?>">
                                <?= $mt_default_slug !== 'funded-plan' ? esc_html__('per month', 'megatrader') : esc_html__('one time fee', 'megatrader') ?>
                            </span>
                                    </div>
                                </div>
                            </div>

                            <?= mt_render_template_meta_info(classes: 'd-none template-metaInfo') ?>

                            <div class="price-table__right-line price-table-attributes metaInfo"
                                 data-price="<?= esc_attr($mt_size) ?>">
                                <?php foreach ($mt_default_meta_info as $mt_field => $mt_value): ?>
                                    <?php
                                    $mt_label = Label::PRODUCT_META[$mt_field];
                                    $mt_value = $mt_meta_info_list[$mt_field];
                                    echo mt_render_template_meta_info(value: $mt_value, label: $mt_label);
                                    ?>
                                <?php endforeach; ?>
                            </div>

                            <div class="price-table__right-line price-table__footer"
                                 data-price="<?= esc_attr($mt_size) ?>">
                                <a href="<?= esc_url($mt_get_plan_url) ?>"
                                   class="proceed-to-checkout-btn mega-btn-md <?= $mt_is_most_popular ? 'mega-btn-primary-md mega-btn-primary--icon-md' : 'mega-btn-default-md' ?> w-100">
                                    <img src="<?php echo get_template_directory_uri() . '/assets/img/landing-page/flash-teal.svg'; ?>"
                                         alt="flash teal"
                                         width="24"
                                         height="24">
                                    <?= esc_html__('GET FUNDED WITH $', 'megatrader') . esc_html($mt_size) ?>
                                </a>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div data-glide-el="controls" class="glide__arrows">
            <button class="glide__arrow glide__arrow--prev" data-glide-dir="<">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/arrow-left.svg'); ?>"
                     alt="control left">
            </button>
            <button class="glide__arrow glide__arrow--next" data-glide-dir=">">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/arrow-right.svg'); ?>"
                     alt="control right">
            </button>
        </div>

        <div class="slider__bullets glide__bullets" data-glide-el="controls[nav]">
            <button class="slider__bullet glide__bullet" data-glide-dir="=0"></button>
            <button class="slider__bullet glide__bullet" data-glide-dir="=1"></button>
            <button class="slider__bullet glide__bullet" data-glide-dir="=2"></button>
            <button class="slider__bullet glide__bullet" data-glide-dir="=3"></button>
        </div>
    </div>

    <div class="testimonials">
        <div class="testimonials__card">
            <img
                    class="testimonials__image"
                    src="<?= get_template_directory_uri() ?>/assets/img/landing-page/testimonial-1.png"
                    alt="Angela's Testimony"
            />
            <div class="testimonials__content">
                <p class="testimonials__quote">
                    The rules are fair and easy to follow. Everything’s clear, and the platform feels built for traders.
                </p>
                <div class="testimonials__info">
                    <div class="testimonials__author">
                        <span class="testimonials__name">Daniel Ruiz, United States</span>
                        <svg
                                class="testimonials__icon"
                                width="20"
                                height="20"
                                viewBox="0 0 20 20"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                        >
                            <mask
                                    id="mask0_17091_43407"
                                    style="mask-type:alpha"
                                    maskUnits="userSpaceOnUse"
                                    x="0"
                                    y="0"
                                    width="20"
                                    height="20"
                            >
                                <rect width="20" height="20" fill="#D9D9D9"/>
                            </mask>
                            <g mask="url(#mask0_17091_43407)">
                                <path
                                        d="M4.854 17.5L6.20817 11.6458L1.6665 7.70832L7.6665 7.18749L9.99984 1.66666L12.3332 7.18749L18.3332 7.70832L13.7915 11.6458L15.1457 17.5L9.99984 14.3958L4.854 17.5Z"
                                        fill="#FFB34A"
                                />
                            </g>
                        </svg>
                    </div>
                    <div class="testimonials__role">Professional Trader</div>
                </div>
            </div>
        </div>

        <div class="testimonials__card">
            <img
                    class="testimonials__image"
                    src="<?= get_template_directory_uri() ?>/assets/img/landing-page/testimonial-2.png"
                    alt="Angela's Testimony"
            />
            <div class="testimonials__content">
                <p class="testimonials__quote">
                    Got my payout within an hour — no delays, no confusion. Super smooth process.
                </p>
                <div class="testimonials__info">
                    <div class="testimonials__author">
                        <span class="testimonials__name">Ava Thompson, Canada</span>
                        <svg
                                class="testimonials__icon"
                                width="20"
                                height="20"
                                viewBox="0 0 20 20"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                        >
                            <mask
                                    id="mask0_17091_43407"
                                    style="mask-type:alpha"
                                    maskUnits="userSpaceOnUse"
                                    x="0"
                                    y="0"
                                    width="20"
                                    height="20"
                            >
                                <rect width="20" height="20" fill="#D9D9D9"/>
                            </mask>
                            <g mask="url(#mask0_17091_43407)">
                                <path
                                        d="M4.854 17.5L6.20817 11.6458L1.6665 7.70832L7.6665 7.18749L9.99984 1.66666L12.3332 7.18749L18.3332 7.70832L13.7915 11.6458L15.1457 17.5L9.99984 14.3958L4.854 17.5Z"
                                        fill="#FFB34A"
                                />
                            </g>
                        </svg>
                    </div>
                    <div class="testimonials__role">Professional Trader</div>
                </div>
            </div>
        </div>
    </div>

    <div id="competition">
        <div class="competition landing-bs-container">
            <div class="competition__wrapper ">
                <div class="competition__content">
                    <h2 class="competition__title">
                        JOIN THE FASTEST GROWING FIRM
                    </h2>

                    <div class="competition__rewards">
                        <div class="competition__reward-card">
                            <div class="competition__reward-header">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <mask id="mask0_17091_79275" style="mask-type:alpha" maskUnits="userSpaceOnUse"
                                          x="0" y="0" width="24" height="24">
                                        <rect width="24" height="24" fill="#D9D9D9"/>
                                    </mask>
                                    <g mask="url(#mask0_17091_79275)">
                                        <path d="M6 20C4.9 20 3.95833 19.6083 3.175 18.825C2.39167 18.0417 2 17.1 2 16V8C2 6.9 2.39167 5.95833 3.175 5.175C3.95833 4.39167 4.9 4 6 4H18C19.1 4 20.0417 4.39167 20.825 5.175C21.6083 5.95833 22 6.9 22 8V16C22 17.1 21.6083 18.0417 20.825 18.825C20.0417 19.6083 19.1 20 18 20H6ZM6 8H18C18.3667 8 18.7167 8.04167 19.05 8.125C19.3833 8.20833 19.7 8.34167 20 8.525V8C20 7.45 19.8042 6.97917 19.4125 6.5875C19.0208 6.19583 18.55 6 18 6H6C5.45 6 4.97917 6.19583 4.5875 6.5875C4.19583 6.97917 4 7.45 4 8V8.525C4.3 8.34167 4.61667 8.20833 4.95 8.125C5.28333 8.04167 5.63333 8 6 8ZM4.15 11.25L15.275 13.95C15.425 13.9833 15.575 13.9833 15.725 13.95C15.875 13.9167 16.0167 13.85 16.15 13.75L19.625 10.85C19.4417 10.6 19.2083 10.3958 18.925 10.2375C18.6417 10.0792 18.3333 10 18 10H6C5.56667 10 5.1875 10.1125 4.8625 10.3375C4.5375 10.5625 4.3 10.8667 4.15 11.25Z"
                                              fill="#FFB34A"/>
                                    </g>
                                </svg>

                                <span class="competition__reward-amount text-truncate">1 HOUR PAYOUTS</span>
                            </div>
                            <p class="competition__reward-label">Average Processing Time</p>
                        </div>

                        <div class="competition__reward-card">
                            <div class="competition__reward-header">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <mask id="mask0_17091_79280" style="mask-type:alpha" maskUnits="userSpaceOnUse"
                                          x="0" y="0" width="24" height="24">
                                        <rect width="24" height="24" fill="#D9D9D9"/>
                                    </mask>
                                    <g mask="url(#mask0_17091_79280)">
                                        <path d="M11.1 19H12.85V17.75C13.6833 17.6 14.4 17.275 15 16.775C15.6 16.275 15.9 15.5333 15.9 14.55C15.9 13.85 15.7 13.2083 15.3 12.625C14.9 12.0417 14.1 11.5333 12.9 11.1C11.9 10.7667 11.2083 10.475 10.825 10.225C10.4417 9.975 10.25 9.63333 10.25 9.2C10.25 8.76667 10.4042 8.425 10.7125 8.175C11.0208 7.925 11.4667 7.8 12.05 7.8C12.5833 7.8 13 7.92917 13.3 8.1875C13.6 8.44583 13.8167 8.76667 13.95 9.15L15.55 8.5C15.3667 7.91667 15.0292 7.40833 14.5375 6.975C14.0458 6.54167 13.5 6.3 12.9 6.25V5H11.15V6.25C10.3167 6.43333 9.66667 6.8 9.2 7.35C8.73333 7.9 8.5 8.51667 8.5 9.2C8.5 9.98333 8.72917 10.6167 9.1875 11.1C9.64583 11.5833 10.3667 12 11.35 12.35C12.4 12.7333 13.1292 13.075 13.5375 13.375C13.9458 13.675 14.15 14.0667 14.15 14.55C14.15 15.1 13.9542 15.5042 13.5625 15.7625C13.1708 16.0208 12.7 16.15 12.15 16.15C11.6 16.15 11.1125 15.9792 10.6875 15.6375C10.2625 15.2958 9.95 14.7833 9.75 14.1L8.1 14.75C8.33333 15.55 8.69583 16.1958 9.1875 16.6875C9.67917 17.1792 10.3167 17.5167 11.1 17.7V19ZM12 22C10.6167 22 9.31667 21.7375 8.1 21.2125C6.88333 20.6875 5.825 19.975 4.925 19.075C4.025 18.175 3.3125 17.1167 2.7875 15.9C2.2625 14.6833 2 13.3833 2 12C2 10.6167 2.2625 9.31667 2.7875 8.1C3.3125 6.88333 4.025 5.825 4.925 4.925C5.825 4.025 6.88333 3.3125 8.1 2.7875C9.31667 2.2625 10.6167 2 12 2C13.3833 2 14.6833 2.2625 15.9 2.7875C17.1167 3.3125 18.175 4.025 19.075 4.925C19.975 5.825 20.6875 6.88333 21.2125 8.1C21.7375 9.31667 22 10.6167 22 12C22 13.3833 21.7375 14.6833 21.2125 15.9C20.6875 17.1167 19.975 18.175 19.075 19.075C18.175 19.975 17.1167 20.6875 15.9 21.2125C14.6833 21.7375 13.3833 22 12 22Z"
                                              fill="#FFB34A"/>
                                    </g>
                                </svg>

                                <span class="competition__reward-amount text-truncate">90% PROFIT SHARE</span>
                            </div>
                            <p class="competition__reward-label">Earn More From Every Trade</p>
                        </div>
                    </div>

                    <p class="competition__entry-note">
                        Experience instant withdrawals, verified through RiseWorks, and enjoy full transparency from
                        challenge to payout.
                    </p>

                    <div class="competition__perks">
                        <div class="competition__perk">
                            <img class="payouts-and-comparison__checked"
                                 src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/checked-circle.svg'); ?>"
                                 alt="checked circle">

                            <span class="competition__perk-text">
          Trade, Profit, Withdraw — Instantly
        </span>
                        </div>
                        <div class="competition__perk">
                            <img class="payouts-and-comparison__checked"
                                 src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/checked-circle.svg'); ?>"
                                 alt="checked circle">

                            <span class="competition__perk-text">
          Available across all of our plans
        </span>
                        </div>
                    </div>

                    <div class="competition__cta">
                        <a data-menu="pricing" href="<?= home_url('#pricing') ?>"
                           class="btn-get-funded-now btn mega-btn-md mega-btn-primary-md">
                            Get Funded Now
                        </a>
                    </div>
                </div>

                <div class="competition__image">
                    <div class="competition__image-placeholder">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/landing-page/join_usd.png"
                             alt="JOIN THE FASTEST GROWING FIRM IMAGE"/>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const PAGE_KEY = '<?= $page_slug ?>-storage';
    window[PAGE_KEY] = {
        screenLoaded: false
    };

    document.addEventListener("DOMContentLoaded", () => {
        async function fetchCouponInBatch(productIds) {
            const couponURL = `/wp-json/custom/v1/best-coupon-in-batch?ids=${productIds}`;
            if (!couponsCache[couponURL]) {
                const responseCoupons = await fetch(couponURL);
                couponsCache[couponURL] = await responseCoupons.json();
            }

            return couponsCache[couponURL];
        }

        function buildProductUrl(productId) {
            const CHECKOUT_URL = MG_GLOBAL.CHECKOUT_URL;
            let checkoutUrl = CHECKOUT_URL.replace('PRODUCT_ID', productId);

            const couponElement = document.querySelector('.badge-coupon__code');
            const coupon = couponElement?.innerText?.trim();

            if (coupon) {
                checkoutUrl = checkoutUrl + '&coupon=' + coupon;
            }

            return checkoutUrl;
        }

        function formatNumber(value) {
            return '$' + parseInt(value.toString().replace('$', ''));
        }

        function updatePoints() {
            let priceTable = document.querySelector('.price-table');
            let bottomPoints = 0;

            priceTable?.style.setProperty('--current-slider-height', bottomPoints + 'px');
        }

        setTimeout(() => {
            const saved = localStorage.getItem(PAGE_KEY);

            if (saved) {
                const values = JSON.parse(saved);

                const event = new CustomEvent("trigger:select-account-type", {
                    detail: {
                        accountType: values['account-type'],
                        defaultPlatform: values['platform'],
                        defaultMarketType: values['market-type'],
                        defaultAccountSize: values['account-size'],
                    }
                });

                document.getElementById('pricing').dispatchEvent(event);

                localStorage.removeItem(PAGE_KEY)
            }
        }, 0);

        document.addEventListener('click', function (e) {
            const target = e.target.closest('.proceed-to-checkout-btn');
            if (!target) return;

            try {
                const parent = target.parentElement;
                const accountSize = parent?.dataset?.price;
                const input = document.querySelector('input[name="account-type"]:checked');

                if (!input) {
                    console.warn('No account type selected.');
                    return;
                }

                const values = {
                    'market-type': input.dataset.defaultMarketType || null,
                    'account-size': accountSize || null,
                    'account-type': input.value || null,
                    'platform': input.dataset.defaultPlatform || null
                };

                // Validación mínima antes de guardar
                if (!values['account-size'] || !values['account-type']) {
                    console.error('Incomplete data to save in localStorage', values);
                    return;
                }

                localStorage.setItem(PAGE_KEY, JSON.stringify(values));
                console.info('Saved to localStorage:', values);
            } catch (err) {
                console.error('Error handling proceed-to-checkout click:', err);
            }
        });

        let priceTable = document.querySelector('.price-table');

        function loadChooseYourAccountSize(fn) {
            document.getElementById('pricing')
                .addEventListener("trigger:select-account-type", async (e) => {
                    const firstMarketType = document.querySelector('.market-type-bs [name="market-type"]:nth-child(1)').value;
                    const {accountType, defaultAccountSize, defaultMarketType} = e.detail

                    if (firstMarketType !== defaultMarketType) {
                        const target = document.querySelector(`.market-type-bs [name="market-type"][value="${defaultMarketType}"]`)
                        target.checked = true;
                        await handlerSelectByMarketType(target, accountType);
                    }

                    const accountTypeSelected = document.querySelector('input[name="account-type"][value=' + accountType + ']')
                    accountTypeSelected.checked = true;

                    handlerSelectByAccountType(accountTypeSelected);

                    const pricingTableGlide = document.querySelector('.price-table.price-table__glide');
                    if (pricingTableGlide) {
                        const cardPlanSize = pricingTableGlide.querySelector('.price-table__plan[data-price="' + defaultAccountSize + '"]');

                        const items = Array.from(pricingTableGlide.querySelectorAll('.slider__slides > li'));

                        const planSizeIndexSelection = items.indexOf(cardPlanSize.parentElement);

                        if (window.tableSliderInstance && planSizeIndexSelection !== -1) {
                            window.tableSliderInstance.go(`=${planSizeIndexSelection}`);
                        }
                    }
                });


            function handlerSelectByAccountType(target) {
                if (!target) {
                    return;
                }

                const input = target.currentTarget || target;
                const accountType = input.value;

                document.querySelectorAll('.mt-pricing-table-benefits').forEach(element => {
                    element.classList.add('d-none');
                });

                document.querySelector(`.mt-pricing-table-benefits[data-account-type-benefits="${accountType}"]`)?.classList.remove('d-none');

                void fn({
                    accountType,
                    defaultPlatform: input.dataset.defaultPlatform,
                    defaultMarketType: input.dataset.defaultMarketType,
                });
            }

            async function handlerSelectByMarketType(target, accountType = null) {
                const input = target.currentTarget || target;
                const marketType = input.value;

                console.info('[MarketType Selected]', marketType);
                try {
                    const endpoint = `/wp-json/custom/v1/pricing-fragment?marketType=${encodeURIComponent(marketType)}`;
                    const response = await fetch(endpoint, {cache: 'no-store'});

                    if (!response.ok) {
                        throw new Error(`Error ${response.status} al obtener el fragmento`);
                    }

                    const data = await response.json();
                    if (!data.success) {
                        console.error('Backend error:', data.message);
                        return;
                    }

                    const htmlContainer = document.querySelector('.pricing-table-fragment-wrapper');
                    if (!htmlContainer) {
                        console.warn('No se encontró el contenedor .pricing-table-fragment-wrapper');
                        return;
                    }

                    document.dispatchEvent(new CustomEvent('mt:destroySliderPricingTable', {detail: {}}));

                    // 🔄 Inyectar nuevo fragmento
                    htmlContainer.innerHTML = data.html;

                    // 🕒 Esperar un tick para asegurar que el DOM esté actualizado
                    await new Promise(resolve => requestAnimationFrame(resolve));

                    // ✅ Buscar el primer input[name="account-type"] del nuevo fragmento
                    let firstAccountTypeInput = htmlContainer.querySelector('[name="account-type"]');
                    if (accountType) {
                        firstAccountTypeInput = htmlContainer.querySelector(`[name="account-type"][value="${accountType}"]`);
                    }

                    document.dispatchEvent(new CustomEvent('mt:refreshSliderPricingTable', {detail: {}}));

                    if (firstAccountTypeInput) {
                        console.info('[Auto-select AccountType]', firstAccountTypeInput.value);

                        void fn({
                            accountType: firstAccountTypeInput.value,
                            defaultPlatform: firstAccountTypeInput.dataset.defaultPlatform,
                            defaultMarketType: firstAccountTypeInput.dataset.defaultMarketType,
                        });
                    } else {
                        console.warn('No se encontró ningún input[name="account-type"] en el nuevo fragmento.');
                    }

                    // 🔁 Reasignar listeners dentro del nuevo HTML renderizado
                    htmlContainer.querySelectorAll('[name="account-type"]').forEach(btn => {
                        btn.addEventListener('click', handlerSelectByAccountType);
                    });
                } catch (error) {
                    console.error('Error al cargar el fragmento de pricing:', error);
                }
            }

            // 📌 Inicializar listeners principales
            document.querySelectorAll('[name="account-type"]').forEach(btn => btn.addEventListener('click', handlerSelectByAccountType));
            document.querySelectorAll('[name="market-type"]').forEach(btn => btn.addEventListener('click', handlerSelectByMarketType));

            const targetSelection = document.querySelector('[name="account-type"]:checked');
            targetSelection && handlerSelectByAccountType(targetSelection);
        }

        window.couponsCache = {};
        loadChooseYourAccountSize(async (params) => {
            console.info('params', params);
            const priceTable = document.querySelector('.price-table');
            const height = document.querySelector('.price-table .glide__slide--active .price-table__plan--most-popular') || document.querySelector('.price-table .glide__slide--active .price-table__plan--regular-plan') ? 0 : 24;
            priceTable.style.setProperty('--current-slider-height', height + 'px');

            const dropdownAccountTypeComponent = document.querySelector('.mt-select-ac-type');

            dropdownAccountTypeComponent.querySelector('.selected')?.classList.remove('selected');

            const dropdownAccountTypeOption = dropdownAccountTypeComponent.querySelector('.dropdown-item__wrapper[data-account-type-slug=' + params['accountType'] + ']');
            const productSelected = MG_GLOBAL.products.find(product => product.tree_map['account-types'] === params.accountType && product.tree_map['market-type'] === params.defaultMarketType);
            const productPlatformDetail = productSelected[productSelected.slug];


            dropdownAccountTypeOption.querySelector('label').classList.add('selected');
            const accountTypeIcon = dropdownAccountTypeOption.querySelector('img');
            const accountTypeText = dropdownAccountTypeOption.querySelector('.mt-dropdown__item-label');
            const accountTypeBadge = dropdownAccountTypeOption.querySelector('.mt-card__badge');

            if (accountTypeIcon) {
                dropdownAccountTypeComponent.querySelector('.mt-dropdown__btn-icon').src = accountTypeIcon.src;
            }
            dropdownAccountTypeComponent.querySelector('.mt-dropdown__btn-label').innerText = accountTypeText.innerText;

            dropdownAccountTypeComponent
                .querySelector('.mt-dropdown__btn-inner')
                .nextElementSibling
                ?.remove();

            if (accountTypeBadge) {
                const badge = accountTypeBadge.cloneNode(true);

                dropdownAccountTypeComponent
                    .querySelector('.mt-select-ac-type__selection')
                    .appendChild(badge);
            }

            const defaultMetaInfo = {}
            for (const priceSize in productPlatformDetail) {
                let attributes = null;
                Object.keys(productSelected?.tree_map || []).forEach(e => {
                    attributes = !attributes ? productPlatformDetail[priceSize] : Object.values(attributes).at(0);
                });

                const metaInfo = attributes.find(item => item['meta-info'])['meta-info'];
                for (const metaInfoKey in metaInfo) {
                    if (metaInfo[metaInfoKey]) {
                        defaultMetaInfo[metaInfoKey] = true;
                    }
                }
            }

            const validMetaInfo = Object.keys(defaultMetaInfo);
            let metaInfoList = [];
            Object.keys(MG_GLOBAL.productMetaLabel).forEach(key => {
                if (validMetaInfo.includes(key)) {
                    metaInfoList.push({key, label: MG_GLOBAL.productMetaLabel[key]})
                }
            })

            const mostPopularElement = document.querySelector('.price-table__plan.price-table__plan--most-popular');

            if (mostPopularElement) {
                mostPopularElement.classList.remove('price-table__plan--most-popular');
                mostPopularElement.classList.add('price-table__plan--regular-plan');

                const btnGetPlan = mostPopularElement.querySelector('.mega-btn-md');
                if (btnGetPlan) {
                    btnGetPlan.classList.remove('mega-btn-primary-md');
                    btnGetPlan.classList.add('mega-btn-default-md');
                }
            }

            const products = [];
            for (const priceSize in productPlatformDetail) {
                const billingType = productSelected.tree_map['billing-type'];

                let attributes = null;
                Object.keys(productSelected?.tree_map || []).forEach(_ => {
                    attributes = !attributes ? productPlatformDetail[priceSize] : Object.values(attributes).at(0);
                });

                const priceObject = attributes.find(item => item['price-monthly'])['price-monthly'] || '$0.00';
                console.info('priceObject', priceObject);
                const productId = attributes.find(item => item['id'])['id'];

                products.push({productId, priceSize, priceObject});

                const isMostPopular = !!Object.values(MG_GLOBAL.bestProducts).find(item => item && item.variation_id === Number(productId))
                const priceCard = document.querySelector(`.price-table__plan[data-price="${priceSize}"]`)

                if (isMostPopular) {
                    priceCard.classList.add('price-table__plan--most-popular');
                    priceCard.classList.remove('price-table__plan--regular-plan');

                    const btnGetPlan = priceCard.querySelector('.mega-btn-md');
                    if (btnGetPlan) {
                        btnGetPlan.classList.add('mega-btn-primary-md', 'mega-btn-primary--icon-md');
                        btnGetPlan.classList.remove('mega-btn-default-md');
                    }
                } else {
                    priceCard.classList.remove('price-table__plan--most-popular');
                    priceCard.classList.add('price-table__plan--regular-plan');

                    const btnGetPlan = priceCard.querySelector('.mega-btn-md');
                    if (btnGetPlan) {
                        btnGetPlan.classList.remove('mega-btn-primary-md', 'mega-btn-primary--icon-md');
                        btnGetPlan.classList.add('mega-btn-default-md');
                    }
                }

                let price = formatNumber(priceObject);
                const priceInformation = document.querySelector(`.price-information[data-price="${priceSize}"]`);
                const pricePanel = document.querySelector(`.price-plan[data-price="${priceSize}"]`);
                const frequencyPanel = document.querySelector(`.frequency-plan[data-price="${priceSize}"]`);
                if (pricePanel) {
                    priceInformation.querySelector('.price-information__summary').style.display = 'none';
                    priceInformation.classList.remove('has-coupon', 'tw-min-h-[140px]', 'tw-items-center');

                    pricePanel.innerText = price;
                }

                if (frequencyPanel) {
                    frequencyPanel.innerText = ` ${billingType === 'monthly' ? 'per month' : 'one time fee'}`;
                }

                const metaInfoObject = attributes.find(item => item['meta-info']);
                if (metaInfoObject) {
                    const metaInfoContext = metaInfoObject['meta-info'];
                    const metaInfoElement = document.querySelector(`.metaInfo[data-price="${priceSize}"]`);
                    const template = document.querySelector(`.template-metaInfo`);

                    metaInfoElement.innerHTML = '';

                    metaInfoList.forEach(metaInfo => {
                        const row = template.cloneNode(true);
                        row.classList.remove('template-metaInfo', 'd-none');
                        const labelHTML = row.querySelector('.mega-info-row__label');
                        labelHTML.dataset.key = metaInfo.key;
                        labelHTML.innerText = metaInfo.label;

                        row.querySelector('.mega-info-row__value').innerText = metaInfoContext[metaInfo.key];
                        metaInfoElement.appendChild(row)
                    })
                }

                const url = buildProductUrl(productId);
                const link = document.querySelector(`.price-table__footer[data-price="${priceSize}"] a`);
                link.href = url;
            }

            const productIds = products.map(product => Number(product.productId)).join(',')

            const {data: coupons} = await fetchCouponInBatch(productIds);

            products.forEach(({productId, priceSize, priceObject}) => {
                const coupon = coupons[productId];
                let price = formatNumber(priceObject);

                const priceInformation = document.querySelector(`.price-information[data-price="${priceSize}"]`);
                const pricePanel = document.querySelector(`.price-plan[data-price="${priceSize}"]`);
                if (pricePanel) {

                    const badgeCoupon = document.querySelector(`.badge-coupon[data-price="${priceSize}"]`);
                    const couponBeforePrice = document.querySelector(`.coupon-before-price[data-price="${priceSize}"]`);

                    const isValidCoupon = coupon && coupon.valid;
                    priceInformation.querySelector('.price-information__summary').style.display = isValidCoupon ? 'flex' : 'none';

                    let couponValue = '';

                    if (isValidCoupon) {
                        priceInformation.classList.add('has-coupon');

                        couponBeforePrice.querySelector('span').innerText = price;
                        pricePanel.innerText = formatNumber(coupon.final_total);
                        badgeCoupon.querySelector('.badge-coupon__discount_total').innerText = formatNumber(coupon.discount_total);
                        couponValue = coupon.coupon.toUpperCase();
                    } else {
                        priceInformation.classList.remove('has-coupon', 'price-information--min-h-112', 'align-items-center');
                        pricePanel.innerText = price;
                    }

                    badgeCoupon.querySelector('.badge-coupon__code').dataset.coupon = couponValue;
                    badgeCoupon.querySelector('.badge-coupon__code').innerText = couponValue;

                    const url = buildProductUrl(productId);
                    const link = document.querySelector(`.price-table__footer[data-price="${priceSize}"] a`);
                    link.href = url;
                }
            });

            if (document.querySelector('.price-information.has-coupon')) {
                document.querySelectorAll('.price-information:not(.has-coupon)').forEach(element => {
                    element.classList.add('price-information--min-h-112', 'align-items-center');
                })
            }

            updatePoints();
        });

        document.addEventListener('mt:refreshSliderPricingTable', function () {
            priceTable = document.querySelector('.price-table');
            destroyGlide();
            handleResize();
        });

        document.addEventListener('mt:destroySliderPricingTable', function () {
            priceTable = document.querySelector('.price-table');
            destroyGlide();
        });

        let glideInstance = null;
        let isGlideMounted = false;
        const parentGlideClasses = ['price-table__glide', 'slider', 'glide'];

        function addClassToPriceTable() {
            parentGlideClasses.map(className => priceTable.classList.add(className));
        }

        function removeClassToPriceTable(extraClasses = ['glide--swipeable']) {
            (parentGlideClasses.concat(extraClasses)).map(className => priceTable.classList.remove(className));
        }

        function initGlide() {
            if (glideInstance || isGlideMounted) return;
            addClassToPriceTable();
            try {
                glideInstance = new Glide(priceTable, {
                    type: 'slider', gap: 16, autoplay: false, rewind: false, animationDuration: 200
                });

                window.tableSliderInstance = glideInstance;

                glideInstance.on(['swipe.start', 'run.after'], () => {
                    updatePoints();
                });

                glideInstance
                    .mutate([function (Glide, Components) {
                        return {
                            modify(translate) {
                                const slideWidth = Components.Sizes.slideWidth;
                                const gap = Components.Gaps.value;
                                const viewportWidth = document.documentElement.clientWidth;
                                const offsetToCenter = (viewportWidth - slideWidth) / 2;
                                const slideIndex = Math.round(Math.abs(translate) / (slideWidth + gap));
                                const adjustedTranslate = -(slideIndex * (slideWidth + gap) - offsetToCenter);
                                const containerGap = viewportWidth <= 1024 ? 0 : 32;

                                return -1 * (adjustedTranslate - containerGap);
                            }
                        };
                    }])
                    .mount({
                        Sizes: function CustomSizes(Glide, Components, Events) {
                            const Sizes = {
                                setupSlides() {
                                    const width = this.slideWidth + 'px';
                                    const slides = Components.Html.slides;
                                    for (let i = 0; i < slides.length; i++) {
                                        slides[i].style.width = width;
                                    }
                                }, setupWrapper() {
                                    Components.Html.wrapper.style.width = `${this.wrapperSize}px`;
                                }, remove() {
                                    const slides = Components.Html.slides;
                                    for (let i = 0; i < slides.length; i++) {
                                        slides[i].style.width = '';
                                    }
                                    Components.Html.wrapper.style.width = '';
                                }
                            };

                            Object.defineProperty(Sizes, 'length', {
                                get() {
                                    return Components.Html.slides.length;
                                }
                            });

                            Object.defineProperty(Sizes, 'width', {
                                get() {
                                    return Components.Html.track.offsetWidth;
                                }
                            });

                            Object.defineProperty(Sizes, 'wrapperSize', {
                                get() {
                                    return (this.slideWidth * this.length + Components.Gaps.grow + Components.Clones.grow);
                                }
                            });

                            Object.defineProperty(Sizes, 'slideWidth', {
                                get() {
                                    const viewport = document.documentElement.clientWidth;
                                    let cardWidth = 346;

                                    if (viewport <= 768) {
                                        cardWidth = 320;
                                    }

                                    priceTable.style.setProperty('--price-table-slide-width', cardWidth + 'px');

                                    return cardWidth;
                                }
                            });

                            Events.on(['build.before', 'resize', 'update'], () => {
                                Sizes.setupSlides();
                                Sizes.setupWrapper();
                                updatePoints();
                            });

                            Events.on('destroy', () => Sizes.remove());
                            return Sizes;
                        }
                    });

                isGlideMounted = true;
                console.info('[Glide] mounted');
            } catch (err) {
                console.error('Error initializing Glide:', err);
            }
        }

        function destroyGlide() {
            console.info('glideInstance && isGlideMounted', glideInstance && isGlideMounted);
            if (glideInstance && isGlideMounted) {
                try {
                    glideInstance.destroy();
                    glideInstance = null;
                    window.tableSliderInstance = null;
                    isGlideMounted = false;
                    removeClassToPriceTable();

                    console.info('[Glide] destroyed');
                } catch (err) {
                    console.error('Error destroying Glide:', err);
                }
            }
        }

        function handleResize() {
            const viewportWidth = window.innerWidth;
            if (viewportWidth > 1024) {
                destroyGlide();
            } else {
                initGlide();
            }
        }

        // Inicializa solo si el viewport es menor o igual a 800
        if (window.innerWidth <= 1024) initGlide();

        // Escucha cambios de tamaño con debounce
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(handleResize, 250);
        });

        handleResize();
        updatePoints();
    });
</script>