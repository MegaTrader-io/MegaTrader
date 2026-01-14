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

<div class="price-table" style="--price-table-slide-width: 0px; --price-table-slide-left: 0px; --current-slider-height: 0px;">
    <div class="slider__track glide__track" data-glide-el="track">
        <ul class="slider__slides glide__slides"
            style="grid-template-columns: repeat(<?= count($mt_plan_list) ?>, 1fr);">
            <?php foreach ($mt_plan_list as $mt_index => $mt_plan): ?>
                <?php
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
                                    <svg class="price-table__most-popular-badge-icon" width="24" height="24"
                                         viewBox="0 0 24 24" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <mask id="mask0_17404_34902" style="mask-type:alpha"
                                              maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
                                            <rect width="24" height="24" fill="#D9D9D9"/>
                                        </mask>
                                        <g mask="url(#mask0_17404_34902)">
                                            <path d="M8 22L9 15H4L13 2H15L14 10H20L10 22H8Z"
                                                  fill="#FFB34A"/>
                                        </g>
                                    </svg>
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
                               class="mega-btn-md <?= $mt_is_most_popular
                                       ? 'mega-btn-primary-md mega-btn-primary--icon-md'
                                       : 'mega-btn-default-md' ?> w-100">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <mask id="mask0_18861_2652" style="mask-type:alpha"
                                          maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
                                        <rect width="24" height="24" fill="#D9D9D9"/>
                                    </mask>
                                    <g mask="url(#mask0_18861_2652)">
                                        <path d="M8 22L9 15H4L13 2H15L14 10H20L10 22H8Z"
                                              fill="#14B8A6"/>
                                    </g>
                                </svg>
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

    <div class="slider__bullets glide__bullets" data-glide-el="controls[nav]">
        <?php foreach ($mt_plan_list as $key => $item): ?>
            <button class="slider__bullet glide__bullet" data-glide-dir="=<?= esc_attr($key) ?>"></button>
        <?php endforeach; ?>
    </div>
</div>
