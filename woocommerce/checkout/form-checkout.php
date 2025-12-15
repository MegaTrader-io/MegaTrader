<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/* ================== Hooks default desactivados ================== */
remove_action('woocommerce_before_checkout_form', 'wc_print_notices', 10);
remove_action('woocommerce_before_checkout_form', 'woocommerce_output_all_notices', 10);

/* ================== Helper externo centralizado ================== */
require_once get_stylesheet_directory() . '/inc/mt-checkout-helper.php';


/* ================== Helpers locales mínimos ================== */
if (!function_exists('mt_get_param')) {
    function mt_get_param($key, $default = '')
    {
        if (!isset($_GET[$key]))
            return $default;
        return wc_clean(wp_unslash($_GET[$key]));
    }
}
if (!function_exists('mt_get_selected_product')) {
    function mt_get_selected_product()
    {
        $variation_id = absint(mt_get_param('variation_id'));
        $parent_id = absint(mt_get_param('add-to-cart'));

        if ($variation_id) {
            $p = wc_get_product($variation_id);
            if ($p instanceof WC_Product)
                return $p;
        }
        if ($parent_id) {
            $p = wc_get_product($parent_id);
            if ($p instanceof WC_Product)
                return $p;
        }
        if (function_exists('WC') && WC()->cart && !WC()->cart->is_empty()) {
            $cart = WC()->cart->get_cart();
            $first = $cart ? reset($cart) : null;
            if ($first) {
                $pid = !empty($first['variation_id']) ? (int) $first['variation_id'] : (int) $first['product_id'];
                $p = wc_get_product($pid);
                if ($p instanceof WC_Product)
                    return $p;
            }
        }
        return null;
    }
}
if (!function_exists('mt_get_attr_label')) {
    function mt_get_attr_label(WC_Product $product = null, $taxonomy = '')
    {
        if (!$product || !$taxonomy)
            return '';
        $val = $product->get_attribute($taxonomy);
        if (!empty($val))
            return $val;

        if ($product->get_type() === 'variation') {
            $va = $product->get_variation_attributes();
            $key = 'attribute_' . $taxonomy;
            if (isset($va[$key]) && $va[$key] !== '') {
                $slug = $va[$key];
                $term = get_term_by('slug', $slug, $taxonomy);
                return ($term && !is_wp_error($term)) ? $term->name : $slug;
            }
        }
        return '';
    }
}
if (!function_exists('mt_build_rich_debug_payload')) {
    function mt_build_rich_debug_payload($product = null)
    {
        $payload = ['attributes' => [], 'meta' => []];
        if (!($product instanceof WC_Product))
            return $payload;

        $pid = $product->get_id();
        $ptype = $product->get_type();
        $taxos = ['pa_account-size', 'pa_account-types', 'pa_platform', 'pa_market-type'];

        foreach ($taxos as $tx) {
            $label = $product->get_attribute($tx);
            $slug = '';
            if ($ptype === 'variation') {
                $va = (array) $product->get_variation_attributes();
                $key = 'attribute_' . $tx;
                $slug = isset($va[$key]) ? (string) $va[$key] : '';
            } else {
                if (function_exists('wc_get_product_terms')) {
                    $slugs = (array) wc_get_product_terms($pid, $tx, ['fields' => 'slugs']);
                    $slug = $slugs[0] ?? '';
                }
            }
            $term = $slug ? get_term_by('slug', $slug, $tx) : ($label ? get_term_by('name', $label, $tx) : null);

            $term_data = null;
            if ($term && !is_wp_error($term)) {
                $img_id = get_term_meta($term->term_id, 'attribute_image_id', true);
                $img_url = '';
                if ($img_id) {
                    $src = wp_get_attachment_image_src($img_id, 'full');
                    $img_url = is_array($src) ? ($src[0] ?? '') : '';
                }
                $custom_repeater = get_term_meta($term->term_id, 'custom_repeater_field', true);
                if (!is_array($custom_repeater))
                    $custom_repeater = $custom_repeater ? (array) $custom_repeater : [];
                $attribute_meta = get_term_meta($term->term_id, 'attribute_meta', true);

                $term_data = [
                    'term_id' => $term->term_id,
                    'taxonomy' => $tx,
                    'name' => $term->name,
                    'slug' => $term->slug,
                    'description' => $term->description,
                    'image_id' => $img_id,
                    'image_url' => $img_url,
                    'custom_repeater_field' => $custom_repeater,
                    'attribute_meta' => $attribute_meta,
                ];
            }
            $payload['attributes'][$tx] = ['label' => $label, 'slug' => $slug, 'term' => $term_data];
        }

        foreach ([
            'profit_target',
            'max_contracts',
            'daily_loss_limit',
            'daily_loss_limit_soft_breach',
            'trailing_max_drawdown',
            'drawdown_mode',
            'min_trading_days',
            'min_trading_days_to_payout',
            'reset_fee',
            'activation_fee',
            'consistency',
            'max_accounts',
            'objectives_rules'
        ] as $k) {
            $payload['meta'][$k] = get_post_meta($pid, $k, true);
        }
        return $payload;
    }
}

/* ================== Datos principales ================== */
$product = mt_get_selected_product();
$plan_size = $product ? mt_get_attr_label($product, 'pa_account-size') : '';
$plan_size_slug = $product ? (
    $product->is_type('variation')
    ? ($product->get_variation_attributes()['attribute_pa_account-size'] ?? '')
    : (wc_get_product_terms($product->get_id(), 'pa_account-size', ['fields' => 'slugs'])[0] ?? '')
) : '';
$plan_type = $product ? $product->get_attribute('pa_account-types') : '';
$market_type = $product ? $product->get_attribute('pa_market-type') : '';

$rich = mt_build_rich_debug_payload($product);
$platform_logo_url = $rich['attributes']['pa_platform']['term']['image_url'] ?? '';
$platform_label = $rich['attributes']['pa_platform']['label'] ?? ($rich['attributes']['pa_platform']['term']['name'] ?? 'Platform');
if (empty($platform_logo_url)) {
    $platform_logo_url = get_template_directory_uri() . '/assets/img/diamond.svg';
}

/* ================== Datos de Billing ================== */
$user_id = get_current_user_id();
$billing = array_fill_keys(
    ['first_name', 'last_name', 'email', 'phone', 'address_1', 'address_2', 'city', 'state', 'postcode', 'country'],
    ''
);
if ($user_id) {
    foreach ($billing as $k => $v) {
        $billing[$k] = (string) get_user_meta($user_id, 'billing_' . $k, true);
    }
    if (empty($billing['email'])) {
        $u = wp_get_current_user();
        if ($u && $u->user_email)
            $billing['email'] = $u->user_email;
    }
}
$required_billing_keys = ['first_name', 'last_name', 'email', 'phone', 'address_1', 'country', 'city', 'state', 'postcode'];
$has_complete_billing = true;
foreach ($required_billing_keys as $k) {
    if (empty($billing[$k])) {
        $has_complete_billing = false;
        break;
    }
}
$country_name = $billing['country'];
$state_name = $billing['state'];
if (function_exists('WC') && WC()->countries) {
    $country_name = WC()->countries->countries[$billing['country']] ?? $billing['country'];
    $state_name = WC()->countries->states[$billing['country']][$billing['state']] ?? $billing['state'];
}
$mt_billing_nonce = wp_create_nonce('mt_save_billing');

/* ================== Feature flag ================== */
/*
$fflag = isset($_GET['v2']);
if ($fflag) {
    $step2_path = get_stylesheet_directory() . '/woocommerce/checkout/step_2.php';

    if (file_exists($step2_path)) {
        include $step2_path;
        return;
    }
}

*/

/* ================== Preparación de UI (header y metas) ================== */
// Línea secundaria compacta (size - {plan_type | Buying Power})
$secondary_line = mtch_prepare_secondary_line($plan_size_slug, $plan_type, $product);
$badge_text = mtch_prepare_badge_text($product, $plan_type);

// Ítems de metas + clase de grid (x4 si se usan highlights)
list($items, $grid_extra_class) = mtch_prepare_meta_items($product, $rich);
$meta_info_debug = [
    'meta_order' => (class_exists('Label') && defined('Label::META_ORDER')) ? Label::META_ORDER : [],
    'icon_map_keys' => (class_exists('Label') && defined('Label::PRODUCT_META_ICONS')) ? array_keys(Label::PRODUCT_META_ICONS) : [],
];

/* ================== Reglas de Add-ons (Activation Fee) ================== */
$mt_is_activation = mtch_is_activation_product($product);
if ($mt_is_activation) {
    mtch_enforce_no_addons_on_activation();
}


// Account + Main product (desde POST>GET)
$__ids = function_exists('mtch_resolve_ids') ? mtch_resolve_ids() : ['account_id' => ''];
$mt_selected_id = (string) ($__ids['account_id'] ?? '');

if (function_exists('WC') && WC()->session) {
    WC()->session->set('mt_account_id', $mt_selected_id ?: '');
}


?>
<div class="container">
    <div class="mt-page">
        <div class="mt-page__main">
            <?php render_step_selector(2); ?>

            <form id="checkout-form" name="checkout" method="post"
                class="checkout woocommerce-checkout d-flex flex-column gap-32" novalidate
                action="<?php echo esc_url($checkout_url); ?>" enctype="multipart/form-data">

                <?php if ($mt_selected_id !== ''): ?>
                    <input type="hidden" name="account_id" id="mt_account_id"
                        value="<?php echo esc_attr($mt_selected_id); ?>">
                <?php endif; ?>

                <?php do_action('woocommerce_before_checkout_form', $checkout); ?>

                <div class="two-columns">
                    <div class="two-columns__col">
                        <div class="mt-login-section">
                            <?php if (!is_user_logged_in()): ?>
                                <div class="mt-card mt-card-md mb-32">
                                    <?php wc_get_template('myaccount/form-login-checkout.php'); ?>
                                </div>

                                <div class="d-flex flex-column gap-3 mb-32">
                                    <h5 class="fw-light leading-7 text-primary text-uppercase text-size-24 mb-0">
                                        <?php echo esc_html(Label::CHECKOUT_META['create_account_title']); ?>
                                    </h5>
                                    <span
                                        class="fw-normal text-a8a29e text-base"><?php echo esc_html(Label::CHECKOUT_META['create_account_subtitle']); ?></span>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="mt-card flex-row flex-wrap mt-card--login-bg justify-content-between p-32 mb-32">
                                <div class="d-flex flex-column gap-1">
                                    <?php
                                    $first = trim($checkout->get_value('billing_first_name'));
                                    $last = trim($checkout->get_value('billing_last_name'));
                                    $name = trim($first . ' ' . $last);

                                    if (empty($name) && is_user_logged_in()) {
                                        $current_user = wp_get_current_user();
                                        $name = $current_user->display_name;
                                    }
                                    ?>
                                    <span
                                        class="title-black-color fw-medium fs-4 text-truncate text-uppercase"><?php echo esc_html($name); ?></span>
                                    <span
                                        class="title-black-color fw-medium text-size-20"><?php echo esc_html($country_name); ?></span>

                                </div>
                                <div class="d-flex flex-row gap-1">
                                    <a href="<?php echo esc_url(home_url('/my-account/orders/')); ?>"
                                        title="<?php echo esc_attr(Label::CHECKOUT_META['btn_manage_subcription']); ?>"
                                        class="mt-btn mt-btn--default mt-btn--sm"><?php echo esc_html(Label::CHECKOUT_META['btn_manage_subcription']); ?></a>
                                          <!--
                                    <a href="<?php echo esc_url(
                                        add_query_arg(
                                            'time',
                                            time(),
                                            wp_logout_url('https://megatrader.io/auth/login/')
                                        )
                                    ); ?>" class="mt-btn mt-btn--default mt-btn--sm btn-logout" title="Logout">
                                        <span class="mt-icon mt-icon-sm mt-icon_logout"></span>
                                    </a>
                                     -->

                                </div>
                            </div>

                        <?php endif; ?>

                        <div id="billing-container" class="d-flex flex-column gap-3">
                            <?php wc_get_template('checkout/form-billing.php', ['checkout' => $checkout]); ?>
                        </div>
                    </div>


                    <div class="two-columns__col">
                        <div class="two-columns__col-wrapper d-flex flex-column gap-3">
                            <div class="mt-coupon-card mt-card h-auto">
                                <?php wc_get_template('checkout/review-order.php'); ?>
                            </div>
                            <?php if (function_exists('WC') && WC()->cart && !$mt_is_activation): ?>
                                <?php $has_subscription = true;
                                if (!WC()->cart->is_empty()) {
                                    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                                        $product = $cart_item['data'] ?? null;
                                        $product_id = $cart_item['product_id'] ?? 0;
                                        $wc_product = $product instanceof WC_Product ? $product : wc_get_product($product_id);
                                        $product_type = $wc_product ? $wc_product->get_type() : '';

                                        if (
                                            $product_type === 'subscription' ||
                                            $product_type === 'variable-subscription' ||
                                            $product_type === 'subscription_variation' ||
                                            (function_exists('wcs_is_subscription_product') && wcs_is_subscription_product($wc_product)) ||
                                            has_term('subscription', 'product_type', $product_id)
                                        ) {
                                            $has_subscription = true;
                                            break;
                                        }
                                    }
                                }

                                $addon_options = [];

                                $checkout_obj = WC()->checkout();
                                if ($checkout_obj) {
                                    $checkout_fields = $checkout_obj->get_checkout_fields();

                                    if (!empty($checkout_fields['add_ons']) && is_array($checkout_fields['add_ons'])) {
                                        $first_addon_field = reset($checkout_fields['add_ons']);

                                        if (!empty($first_addon_field['options']) && is_array($first_addon_field['options'])) {
                                            $addon_options = $first_addon_field['options'];
                                        }
                                    }
                                }
                                ?>

                                <?php if ($has_subscription && !empty($addon_options)): ?>
                                    <div class="mt-addons">
                                        <div class="addons-container">
                                            <div class="addons-block d-flex flex-column gap-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="mt-icon mt-icon_plus mt-icon-primary"></span>
                                                    <span class="text-white fw-bold text-base">
                                                        <?php echo esc_html(Label::CHECKOUT_META['addons_title']); ?>
                                                    </span>
                                                </div>

                                                <div class="checkout-addons">
                                                    <div class="available-info d-flex flex-column gap-3">
                                                        <?php foreach ($addon_options as $option_key => $option_data): ?>
                                                            <div class="addons-item addons-item-new d-flex gap-3 align-items-center bg-1e1e1e rounded-16px w-100 justify-content-between <?php echo esc_attr($option_key); ?>"
                                                                data-addon-key="<?php echo esc_attr($option_key); ?>">
                                                                <div class="addons-header d-flex flex-column gap-1">
                                                                    <div class="text-base text-white fw-medium">
                                                                        <?php
                                                                        $label_full = $option_data['label'];
                                                                        preg_match('/^(.*?)\s*\((.*?)\)$/', wp_strip_all_tags($label_full), $matches);
                                                                        $label_text = $matches[1] ?? wp_strip_all_tags($label_full);
                                                                        $price_html = $matches[2] ?? '';
                                                                        echo esc_html($label_text);
                                                                        ?>
                                                                    </div>
                                                                    <?php if (!empty($option_data['description'])): ?>
                                                                        <span class="text-a8a29e text-14px-line-20px fw-bold">
                                                                            <?php echo esc_html(trim($option_data['description'])); ?>
                                                                        </span>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div class="title">
                                                                    <?php
                                                                    if ($price_html) {
                                                                        echo '<div>' . esc_html($price_html) . '</div>';
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>

                                                <span class="text-a8a293 fw-medium text-14px-line-20px">
                                                    <?php echo esc_html(Label::CHECKOUT_META['addons_discalimer']); ?>
                                                </span>
                                            </div>
                                        </div>

                                    </div>
                                <?php endif; ?>

                            <?php endif; ?>

                            <div class="payment-container review-container">
                                <div class="fw-medium leading-8 text-size-20 text-white pb-3">
                                    <?php echo esc_html(Label::CHECKOUT_META['payment_title']); ?>
                                </div>
                                <div class="mt-payment-cards mt-payment-cards-v2" id="mt-payment">
                                    <?php wc_get_template('checkout/payment.php', array('checkout' => WC()->checkout())); ?>
                                </div>
                                <div class="mt-card mt-3">
                                    <div class="form-row place-order">
                                        <noscript>
                                            <?php
                                            printf(
                                                esc_html__('Since your browser does not support JavaScript, or it is disabled, please ensure you click the %1$sUpdate Totals%2$s button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce'),
                                                '<em>',
                                                '</em>'
                                            );
                                            ?>
                                            <br />
                                            <button type="submit" class="button alt"
                                                name="woocommerce_checkout_update_totals"
                                                value="<?php esc_attr_e('Update totals', 'woocommerce'); ?>">
                                                <?php esc_html_e('Update totals', 'woocommerce'); ?>
                                            </button>
                                        </noscript>
                                        <?php do_action('woocommerce_review_order_before_submit'); ?>
                                        <?php
                                        if (!isset($order_button_text)) {
                                            $order_button_text = apply_filters('woocommerce_order_button_text', __('Place order', 'woocommerce'));
                                        }
                                        echo apply_filters(
                                            'woocommerce_order_button_html',
                                            '<button type="submit" class="mega-btn-md mega-btn-primary-md w-100" name="woocommerce_checkout_place_order" value="' . esc_attr($order_button_text) . '" data-value="' . esc_attr($order_button_text) . '">' . esc_html($order_button_text) . '</button>'
                                        );
                                        ?>
                                        <div class="d-flex gap-1 align-items-center pt-3">
                                            <i class="mt-icon mt-icon_lock"></i>
                                            <span class="text-base fw-light text-a8a29e">
                                                <?php echo esc_html(Label::CHECKOUT_META['payment_disclaimer']); ?>
                                            </span>
                                        </div>
                                        <?php do_action('woocommerce_review_order_after_submit'); ?>
                                        <?php wp_nonce_field('woocommerce-process_checkout', 'woocommerce-process-checkout-nonce'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
    <?php wc_get_template('myaccount/partials/email-modal.php'); ?>
    <?php wc_get_template('myaccount/partials/otp-modal.php'); ?>
</div>