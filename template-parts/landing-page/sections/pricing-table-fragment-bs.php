<?php
/**
 * Partial: Pricing Table Fragment
 * Ubicación: template-parts/landing-page/sections/pricing-table-fragment-bs.php
 * Propósito: Renderizar los tipos de cuenta, beneficios y planes (slider)
 * Autor: Luis Viera (levieraf)
 */

// 1️⃣ Extraer las variables que vienen del get_template_part()
$mt_account_types = $args['mt_account_types'] ?? [];
$mt_default_platform = $args['mt_default_platform'] ?? [];
$mt_default_market_type = $args['mt_default_market_type'] ?? [];
$mt_default_slug = $args['mt_default_slug'] ?? '';
$mt_plan_list = $args['mt_plan_list'] ?? [];
$mt_default_meta_info = $args['mt_default_meta_info'] ?? [];
$mt_best_products = $args['mt_best_products'] ?? [];
$mt_render_template_meta_info = $args['mt_render_template_meta_info'] ?? null;
$mt_size = $args['mt_size'] ?? '';
$mt_product = $args['mt_product'] ?? [];
$mt_account_sizes = $args['mt_account_sizes'] ?? [];

// 2️⃣ Validar que haya data suficiente para renderizar
if (empty($mt_plan_list) || empty($mt_account_types)) {
    echo '<p class="text-center text-muted">No plans available at this time.</p>';
    return;
}

?>

<div class="pricing-table-container-options">
    <?php
    // Renderizar select de tipos de cuenta
    get_template_part("template-parts/landing-page/sections/select-account-type", null, [
            'account_types' => $mt_account_types,
            'mt_default_platform' => $mt_default_platform,
            'mt_default_market_type' => $mt_default_market_type,
    ]);
    ?>

    <div class="mt-pricing-table-plan-options__wrapper">
        <?php foreach ($mt_account_types as $index => $item): ?>
            <?php
            $slug = esc_attr($item['slug']);
            $parsed = parse_attribute_meta($item['attribute_meta'] ?? []);
            ?>

            <?php if (!empty($parsed['data'])): ?>
                <div class="mt-pricing-table-benefits <?= $index > 0 ? 'd-none' : '' ?>"
                     data-account-type-benefits="<?= $slug ?>">
                    <?php foreach ($parsed['data'] as $index => $text): ?>
                        <?php if ($index > 0): ?>
                            <img class="mt-pricing-table-benefits__icon"
                                 src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/quick-flash.svg'); ?>"
                                 alt="flash" width="24" height="24">
                        <?php endif; ?>
                        <div class="mt-pricing-table-benefits__item"><?= esc_html($text) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

<div class="price-table"
     style="--price-table-slide-width: 0px; --price-table-slide-left: 0px; --current-slider-height: 0px;">
    <div class="slider__track glide__track" data-glide-el="track">
        <ul class="slider__slides glide__slides"
            style="grid-template-columns: repeat(<?= min(count($mt_plan_list), 4) ?>, 1fr);">
            <?php foreach ($mt_plan_list as $mt_index => $mt_plan): ?>
                <?php
                if ($mt_index > 3) {
                    continue;
                }
                $mt_id = $mt_plan['id'];
                $mt_size = $mt_plan['size'];
                $mt_parent_id = $mt_plan['parent_id'];
                $mt_price = $mt_plan['price'];
                $mt_meta_info_list = $mt_plan['meta_info_list'];

                $mt_coupon = mt_get_best_coupon_for_variation($mt_id);
                $mt_has_coupon = !empty($mt_coupon);
                $mt_price_plan = $mt_price;

                $mt_scan_product = $mt_best_products[$mt_parent_id] ?? null;
                $mt_is_most_popular = $mt_scan_product && $mt_scan_product['variation_id'] == $mt_id;

                $CHECKOUT_URL = home_url('/checkout/?add-to-cart=' . $mt_id);
                $URL_GO_TO = home_url('/auth/register/?redirect_to=');

                $mt_get_plan_url = is_user_logged_in() ? $CHECKOUT_URL : $URL_GO_TO . $CHECKOUT_URL;
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
                                    <div class="price-table__most-popular-badge-text">
                                        <?php esc_html_e('Most popular', 'megatrader'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="price-table__title">
                                <?= esc_html($mt_size) ?> Account
                            </div>
                        </div>

                        <div class="price-table__right-line price-information"
                             data-price="<?= esc_attr($mt_size) ?>">
                            <div class="w-100">
                                <div style="display: none;" class="price-information__summary">
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
                                            <div class="badge-coupon__code tw-uppercase tw-justify-start">
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
                                        <?= $mt_default_slug !== 'funded-plan'
                                                ? esc_html__('per month', 'megatrader')
                                                : esc_html__('one time fee', 'megatrader') ?>
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
                                $mt_value = $mt_meta_info_list[$mt_field] ?? '';
                                echo mt_render_template_meta_info(value: $mt_value, label: $mt_label);
                                ?>
                            <?php endforeach; ?>
                        </div>

                        <div class="price-table__right-line price-table__footer"
                             data-price="<?= esc_attr($mt_size) ?>">
                            <a href="<?= esc_url($mt_get_plan_url) ?>"
                               class="proceed-to-checkout-btn mega-btn-md <?= $mt_is_most_popular
                                       ? 'mega-btn-primary-md mega-btn-primary--icon-md'
                                       : 'mega-btn-default-md' ?> w-100">
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
            <img src="<?= esc_url(get_template_directory_uri() . '/assets/img/landing-page/arrow-left.svg'); ?>"
                 alt="control left">
        </button>
        <button class="glide__arrow glide__arrow--next" data-glide-dir=">">
            <img src="<?= esc_url(get_template_directory_uri() . '/assets/img/landing-page/arrow-right.svg'); ?>"
                 alt="control right">
        </button>
    </div>

    <a href="javascript:void(0);" class="mt-prices-left mt-prices-disabled d-none">
        <svg width="41" height="41" viewBox="0 0 41 41" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20.0049 39.7468C9.23617 39.6122 0.615466 30.7734 0.750007 20.0047C0.884548 9.23597 9.72338 0.615275 20.4921 0.749816C31.2608 0.884357 39.8815 9.72319 39.747 20.4919C39.6124 31.2606 30.7736 39.8813 20.0049 39.7468Z"
                  fill="#1E1E1E"/>
            <path d="M20.0049 39.7468C9.23617 39.6122 0.615466 30.7734 0.750007 20.0047C0.884548 9.23597 9.72338 0.615275 20.4921 0.749816C31.2608 0.884357 39.8815 9.72319 39.747 20.4919C39.6124 31.2606 30.7736 39.8813 20.0049 39.7468Z"
                  stroke="#404040"/>
            <mask id="mask0_19096_9613" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="8" y="8" width="25"
                  height="25">
                <rect width="24" height="24"
                      transform="matrix(0.0124927 -0.999922 -0.999922 -0.0124927 32.0977 32.3973)" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_19096_9613)">
                <path d="M14.2488 20.1733L20.1734 26.2478L21.5908 24.8654L18.036 21.2207L27.2353 21.3357L27.2603 19.3358L18.061 19.2209L21.7057 15.6661L20.3233 14.2488L14.2488 20.1733Z"
                      fill="white"/>
            </g>
        </svg>
    </a>

    <a href="javascript:void(0);" class="mt-prices-right d-none">
        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 0.5C30.7696 0.5 39.5 9.23045 39.5 20C39.5 30.7696 30.7696 39.5 20 39.5C9.23045 39.5 0.5 30.7696 0.5 20C0.5 9.23045 9.23045 0.5 20 0.5Z"
                  fill="#1E1E1E"/>
            <path d="M20 0.5C30.7696 0.5 39.5 9.23045 39.5 20C39.5 30.7696 30.7696 39.5 20 39.5C9.23045 39.5 0.5 30.7696 0.5 20C0.5 9.23045 9.23045 0.5 20 0.5Z"
                  stroke="#404040"/>
            <mask id="mask0_19005_11909" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="8" y="8" width="24"
                  height="24">
                <rect width="24" height="24" transform="matrix(0 1 1 0 8 8)" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_19005_11909)">
                <path d="M26 20L20 14L18.6 15.4L22.2 19H13V21H22.2L18.6 24.6L20 26L26 20Z" fill="white"/>
            </g>
        </svg>
    </a>

    <div class="slider__bullets glide__bullets" data-glide-el="controls[nav]">
        <?php foreach ($mt_plan_list as $key => $item): ?>
            <?php
            if ($key > 3) {
                continue;
            }
            ?>
            <button class="slider__bullet glide__bullet" data-glide-dir="=<?= esc_attr($key) ?>"></button>
        <?php endforeach; ?>
    </div>
</div>
