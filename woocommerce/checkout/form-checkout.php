<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

if (!defined('ABSPATH')) {
    exit;
}

remove_action('woocommerce_before_checkout_form', 'wc_print_notices', 10);


// If checkout registration is disabled and not logged in, the user cannot checkout.
if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
    echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
    return;
}



// Helpers mínimos
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


/* --------- Cálculo de variables exactamente como en step_2 --------- */
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
$platform_label = $rich['attributes']['pa_platform']['label']
    ?? ($rich['attributes']['pa_platform']['term']['name'] ?? 'Platform');

/* Fallbacks suaves para evitar “agujeros” visuales */
if (empty($platform_logo_url)) {
    $platform_logo_url = get_template_directory_uri() . '/assets/img/diamond.svg';
}
/** ==== END: Hydrate data ==== */

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
$platform_label = $rich['attributes']['pa_platform']['label']
    ?? ($rich['attributes']['pa_platform']['term']['name'] ?? 'Platform');

if (empty($platform_logo_url)) {
    $platform_logo_url = get_template_directory_uri() . '/assets/img/diamond.svg';
}




$fflag = isset($_GET['v2']);

if ($fflag): ?>

    <div class="main-container pt-32 pb-32">
        <div class="container">
            <?php include get_stylesheet_directory() . '/woocommerce/checkout/step_2.php'; ?>
        </div>
    </div>

<?php else: ?>

    <div class="page-banner-area pt-32 pb-32">
        <div class="container">
            <div class="top-menu">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="actived">
                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12.1333 20.1334L21.5333 10.7334L19.6667 8.86669L12.1333 16.4L8.33332 12.6L6.46666 14.4667L12.1333 20.1334ZM14 27.3334C12.1555 27.3334 10.4222 26.9834 8.79999 26.2834C7.17777 25.5834 5.76666 24.6334 4.56666 23.4334C3.36666 22.2334 2.41666 20.8222 1.71666 19.2C1.01666 17.5778 0.666656 15.8445 0.666656 14C0.666656 12.1556 1.01666 10.4222 1.71666 8.80002C2.41666 7.1778 3.36666 5.76669 4.56666 4.56669C5.76666 3.36669 7.17777 2.41669 8.79999 1.71669C10.4222 1.01669 12.1555 0.666687 14 0.666687C15.8444 0.666687 17.5778 1.01669 19.2 1.71669C20.8222 2.41669 22.2333 3.36669 23.4333 4.56669C24.6333 5.76669 25.5833 7.1778 26.2833 8.80002C26.9833 10.4222 27.3333 12.1556 27.3333 14C27.3333 15.8445 26.9833 17.5778 26.2833 19.2C25.5833 20.8222 24.6333 22.2334 23.4333 23.4334C22.2333 24.6334 20.8222 25.5834 19.2 26.2834C17.5778 26.9834 15.8444 27.3334 14 27.3334Z"
                            fill="#FFB34A" />
                    </svg>
                </a>
                <span class="active">2</span>
                <span>3</span>
            </div>

            <form id="checkout-form" name="checkout" method="post" class="checkout woocommerce-checkout" novalidate
                action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">
                <div class="two-columns">
                    <div class="two-columns__col">
                        <div class="login-height">
                            <div class="position-relative d-flex flex-column">
                                <?php if (is_user_logged_in()):
                                    $current_user = wp_get_current_user();
                                    $name = esc_html($current_user->display_name);
                                    $country_code = get_user_meta($current_user->ID, 'billing_country', true);
                                    $country_name = $country_code ? WC()->countries->countries[$country_code] : '';
                                    $country_name = preg_replace('/\s*\(.*\)$/', '', $country_name);
                                    ?>
                                    <!-- Success login message -->
                                    <div id="loginSuccess" class="otp-message-container mb-32 d-none">
                                        <div class="success-otp-message notifications notifications-success w-100">
                                            <div
                                                class="w-6 h-6 d-flex align-items-center justify-content-center rounded-full bg-teal-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                                                    aria-hidden="true" data-slot="icon" class="w-5 h-5 text-black">
                                                    <path fill-rule="evenodd"
                                                        d="M12.416 3.376a.75.75 0 0 1 .208 1.04l-5 7.5a.75.75 0 0 1-1.154.114l-3-3a.75.75 0 0 1 1.06-1.06l2.353 2.353 4.493-6.74a.75.75 0 0 1 1.04-.207Z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                            <span class="success-otp-text">You have successfully logged in to Megatrader.</span>
                                        </div>
                                    </div>

                                    <!-- Logged-in user card -->
                                    <div
                                        class="user-card w-100 rounded-4 d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center p-32 mb-3 gap-3">
                                        <div class="user-card_info_name d-flex flex-column gap-1 min-w-0">
                                            <div class="title-black-color fw-medium fs-4 text-truncate text-uppercase"
                                                title="<?php echo esc_attr($name); ?>"><?php echo $name; ?></div>
                                            <div class="user-card_info_contry title-black-color fw-medium text-size-20">
                                                <?php echo esc_html($country_name); ?>
                                            </div>
                                        </div>
                                        <div class="user-cta d-flex flex-row flex-shrink-0 w-sm-100 w-md-100">
                                            <?php
                                            $subscription_url = home_url('/my-account/orders/'); // Valor por defecto
                                    
                                            $user_id = get_current_user_id();
                                            if ($user_id) {
                                                $customer_orders = wc_get_orders([
                                                    'customer_id' => $user_id,
                                                    'limit' => 1,
                                                    'orderby' => 'date',
                                                    'order' => 'DESC',
                                                    'return' => 'objects'
                                                ]);

                                                if (!empty($customer_orders)) {
                                                    $latest_order = $customer_orders[0];
                                                    $order_id = $latest_order->get_id();

                                                    // Buscar suscripción relacionada
                                                    if (function_exists('wcs_get_subscriptions_for_order')) {
                                                        $related_subs = wcs_get_subscriptions_for_order($order_id, ['order_type' => 'any']);

                                                        if (!empty($related_subs)) {
                                                            $first_sub = reset($related_subs);
                                                            $subscription_url = $first_sub->get_view_order_url();
                                                        } else {
                                                            $subscription_url = $latest_order->get_view_order_url();
                                                        }
                                                    } else {
                                                        $subscription_url = $latest_order->get_view_order_url();
                                                    }
                                                }
                                            }
                                            ?>

                                            <a href="<?php echo esc_url($subscription_url); ?>"
                                                class="btn otp-subscriptions mega-btn mega-btn-sm bg-white fw-medium rounded-12 flex-grow-1 flex-lg-grow-0 text-action-light-solid">
                                                MANAGE SUBSCRIPTIONS
                                            </a>
                                            <a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>"
                                                class="btn bg-white fw-medium mega-btn mega-btn-md rounded-12 p-12"
                                                title="Logout">
                                                <img src="https://subscriptions.megatrader.io/wp-content/uploads/2025/05/logout.svg"
                                                    alt="Logout Icon" style="height: 24px;">
                                            </a>
                                        </div>

                                    </div>
                                <?php else: ?>
                                    <div class="authentication-form single-checkout-widget checkout-login mb-35">
                                        <?php woocommerce_login_form(); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                    <div class="two-columns__col">
                        <div class="variation-list right-box mb-32">
                            <?php
                            if (!WC()->cart->is_empty()) {
                                foreach (WC()->cart->get_cart() as $cart_item) {
                                    $product = $cart_item['data'];
                                    $product_id = $product->get_id();

                                    $terms = get_the_terms($product_id, 'product_cat');
                                    $is_activation_fee = false;
                                    $is_reset_fee = false;
                                    if ($terms && !is_wp_error($terms)) {
                                        foreach ($terms as $term) {
                                            if ($term->slug === 'activation-fee') {
                                                $is_activation_fee = true;
                                                break;
                                            }
                                            if ($term->slug === 'reset-fee') {
                                                $is_reset_fee = true;
                                                break;
                                            }
                                        }
                                    }

                                    $variation_attributes = $product->get_attributes();
                                    $platform_meta_list = [];
                                    $platform_terms = [];

                                    if (taxonomy_exists('platform')) {
                                        $platform_terms = wp_get_post_terms($product_id, 'platform', ['fields' => 'all']);
                                        if (!is_wp_error($platform_terms) && !empty($platform_terms)) {
                                            foreach ($platform_terms as $platform) {
                                                if (isset($platform->term_id)) {
                                                    $platform_meta_values = get_term_meta($platform->term_id, 'custom_repeater_field', true);
                                                    if (!empty($platform_meta_values) && is_array($platform_meta_values)) {
                                                        $platform_meta_list = array_merge($platform_meta_list, $platform_meta_values);
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        // If platform isn't a taxonomy, try getting it as a product attribute
                                        $platform_attr = $product->get_attribute('pa_platform');
                                        if (!empty($platform_attr)) {
                                            $platform_term = get_term_by('name', $platform_attr, 'pa_platform');
                                            if ($platform_term && isset($platform_term->term_id)) {
                                                $platform_meta_values = get_term_meta($platform_term->term_id, 'custom_repeater_field', true);
                                                if (!empty($platform_meta_values) && is_array($platform_meta_values)) {
                                                    $platform_meta_list = $platform_meta_values;
                                                }
                                            }
                                        }
                                    }

                                    $plan_attr = $product->get_attribute('pa_account-types');
                                    if (!empty($plan_attr)) {
                                        $plan_term = get_term_by('name', $plan_attr, 'pa_account-types');
                                        if ($plan_term && isset($plan_term->term_id)) {
                                            $image_id = get_term_meta($plan_term->term_id, 'attribute_image_id', true);
                                            $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
                                        }
                                    }

                                    $custom_meta = array(
                                        'profit_target' => get_post_meta($product_id, 'profit_target', true),
                                        'max_contracts' => get_post_meta($product_id, 'max_contracts', true),
                                        'daily_loss_limit' => get_post_meta($product_id, 'daily_loss_limit', true),
                                        'daily_loss_limit_soft_breach' => get_post_meta($product_id, 'daily_loss_limit_soft_breach', true),
                                        'trailing_max_drawdown' => get_post_meta($product_id, 'trailing_max_drawdown', true),
                                        'drawdown_mode' => get_post_meta($product_id, 'drawdown_mode', true),
                                        'min_trading_days' => get_post_meta($product_id, 'min_trading_days', true),
                                        'min_trading_days_to_payout' => get_post_meta($product_id, 'min_trading_days_to_payout', true),
                                        'reset_fee' => get_post_meta($product_id, 'reset_fee', true),
                                        'activation_fee' => get_post_meta($product_id, 'activation_fee', true),
                                        'consistency' => get_post_meta($product_id, 'consistency', true),
                                        'max_accounts' => get_post_meta($product_id, 'max_accounts', true)
                                    );

                                    $labels = [
                                        'profit_target' => 'Profit Target',
                                        'max_contracts' => 'Max Contracts',
                                        'daily_loss_limit' => 'Daily Loss Limit',
                                        'daily_loss_limit_soft_breach' => 'Daily Loss Limit (Soft Breach)',
                                        'trailing_max_drawdown' => 'Trailing Max Drawdown',
                                        'drawdown_mode' => 'Drawdown Mode',
                                        'min_trading_days' => 'Min Trading Days to Pass',
                                        'min_trading_days_to_payout' => 'Min Trading Days to Payout',
                                        'reset_fee' => 'Reset Fee',
                                        'activation_fee' => 'Activation Fee',
                                        'consistency' => 'Consistency',
                                        'max_accounts' => 'Max Accounts'
                                    ];
                                    ?>
                                    <div class="accordion" id="checkoutAccordion" data-cue="slideInUp" data-delay="100">
                                        <div class="accordion-card d-flex flex-column gap-3">
                                            <div class="accordion-header box-title <?php if (!($plan_attr)) {
                                                echo 'activation-style';
                                            } ?>" id="collapse-item-1">
                                                <button class="accordion-button collapsed d-flex gap-2 align-items-start"
                                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapse-1"
                                                    aria-expanded="false" aria-controls="collapse-1"><span class="icon">
                                                        <?php
                                                        if (!empty($image_url)) {
                                                            $image_url = wp_get_attachment_image_src($image_id, 'full')[0];
                                                        } else {
                                                            $image_url = get_template_directory_uri() . '/assets/img/diamond.svg';
                                                        }
                                                        ?>
                                                        <img src="<?php echo esc_url($image_url); ?>" alt="Icon">
                                                    </span>
                                                    <?php
                                                    $account_size_label = $product->get_attribute('pa_account-size');
                                                    $account_size = preg_replace_callback('/\$(\d{1,3}),000(?:\s.*)?/', function ($matches) {
                                                        return intval($matches[1]) . 'k';
                                                    }, $account_size_label);
                                                    echo '<span class="d-flex flex-column">';
                                                    if ($account_size) {
                                                        echo '<span class="pp">' . esc_html($account_size . '  ' . $product->get_attribute('pa_account-types')) . '</span>';
                                                    } else {
                                                        echo '<span>' . esc_html($product->get_name()) . '</span>';
                                                        if ($is_activation_fee) {
                                                            echo '<span class="fs-6 fw-medium text-capitalize">Activation Fee</span>';
                                                        } else if ($is_reset_fee) {
                                                            echo '<span class="fs-6 fw-medium text-capitalize">Reset Fee</span>';
                                                        }
                                                    }
                                                    echo '</span';


                                                    ?>
                                                </button>
                                            </div>
                                            <div id="collapse-1" class="accordion-collapse collapse"
                                                aria-labelledby="collapse-item-1" data-bs-parent="#checkoutAccordion">
                                                <div class="accordion-body pt-0">
                                                    <div class="info-boxs">
                                                        <?php
                                                        if ($product->get_attribute('pa_platform')) { ?>
                                                            <div class="info-boxs__block">
                                                                <h5 class="title product_title">
                                                                    <?php echo esc_html($product->get_attribute('pa_platform')); ?>
                                                                    Platform
                                                                </h5>

                                                                <div class="checklist">
                                                                    <ul class="cat-meta-list">
                                                                        <?php
                                                                        if (!empty($platform_meta_list)) {
                                                                            foreach ($platform_meta_list as $meta_value) {
                                                                                echo '<li>' . esc_html($meta_value) . '</li>';
                                                                            }
                                                                        } else {
                                                                            // echo '<li>No platform data available.</li>';
                                                                        }
                                                                        ?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                        <div class="info-boxs__block">
                                                            <?php
                                                            if ($is_activation_fee) {
                                                                $title = 'Funded Account Fee Details';
                                                            } elseif ($is_reset_fee) {
                                                                $title = 'Evaluation Restart Details';
                                                            } else {
                                                                $title = 'Objectives and Rules';
                                                            }
                                                            ?>
                                                            <h5 class="title"><?= $title ?></h5>
                                                            <div class="checklist mb-0">
                                                                <ul class="metaInfo">
                                                                    <?php foreach ($custom_meta as $key => $value) {
                                                                        $classes = [$key];
                                                                        if (empty($value)) {
                                                                            $classes[] = 'metaInfo_no-default';
                                                                        }
                                                                        echo '<li class="' . esc_attr(implode(' ', $classes)) . '">';
                                                                        echo '<span class="metaInfo__label">' . esc_html($labels[$key]) . ': </span><span class="metaInfo__value">' . esc_html($value) . '</span>';
                                                                        echo '</li>';
                                                                    }

                                                                    // for activation fee
                                                                    $objectives_rules = get_post_meta($product_id, 'objectives_rules', true);
                                                                    if (is_array($objectives_rules) && count($objectives_rules) > 0) {
                                                                        foreach ($objectives_rules as $item) {
                                                                            echo '<li>' . esc_html($item) . '</li>';
                                                                        }
                                                                    }
                                                                    ?>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    break;
                                }
                            }
                            ?>
                        </div>
                        <div class="coupon-message-container w-100 mb-32 position-relative d-block">
                            <!-- Error -->
                            <div class="error-otp-message notifications notifications-error w-100">
                                <div
                                    class="w-6 h-6 d-flex align-items-center justify-content-center rounded-full bg-red-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                                        aria-hidden="true" data-slot="icon" class="w-5 h-5 text-black">
                                        <path
                                            d="M5.28 4.22a.75.75 0 0 0-1.06 1.06L6.94 8l-2.72 2.72a.75.75 0 1 0 1.06 1.06L8 9.06l2.72 2.72a.75.75 0 1 0 1.06-1.06L9.06 8l2.72-2.72a.75.75 0 0 0-1.06-1.06L8 6.94 5.28 4.22Z">
                                        </path>
                                    </svg>
                                </div>
                                <span class="error-otp-text">Coupon has been removed.</span>
                            </div>
                            <!-- Éxito -->
                            <div class="success-otp-message notifications notifications-success w-100">
                                <div
                                    class="w-6 h-6 d-flex align-items-center justify-content-center rounded-full bg-teal-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                                        aria-hidden="true" data-slot="icon" class="w-5 h-5 text-black">
                                        <path fill-rule="evenodd"
                                            d="M12.416 3.376a.75.75 0 0 1 .208 1.04l-5 7.5a.75.75 0 0 1-1.154.114l-3-3a.75.75 0 0 1 1.06-1.06l2.353 2.353 4.493-6.74a.75.75 0 0 1 1.04-.207Z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <span class="success-otp-text">Coupon code applied successfully.</span>
                            </div>
                        </div>
                        <?php
                        do_action('woocommerce_checkout_order_review');
                        ?>



                        <?php
                        // your-order variation-list right-box
                        wc_get_template('checkout/payment.php', array('checkout' => WC()->checkout()));
                        ?>
                    </div>
                </div>


                <div class="mt-card">
                    <div class="mt-card-wrapper d-flex flex-column gap-4">
                        <div class="mt-card-header d-flex gap-3 align-items-center flex-wrap">
                            <div class="mt-card-plan d-flex flex-column flex-grow-1">
                                <div class="mt-card-plan-size text-white text-40px fw-medium text-uppercase leading-48px">
                                    <?php echo esc_html($plan_size); ?>
                                </div>
                                <div class="mt-card-plan-info d-flex gap-3 align-items-center flex-wrap">
                                    <div class="text-white fw-bold text-size-20 leading-36px">
                                        <?php echo esc_html($plan_size_slug . ' - ' . Label::PRICE['price_sufix']); ?>
                                    </div>
                                    <div class="d-flex gap-3">
                                        <div class="badge-mega badge-mega-sm badge-mega-default">
                                            <?php echo esc_html($plan_type); ?>
                                        </div>
                                        <div class="badge-mega badge-mega-sm badge-mega-primary">
                                            <?php echo esc_html($market_type); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-card-plataform d-flex align-items-center bg-131210 gap-3 p-3 rounded-3">
                                <div class="mt-card-plataform-logo">
                                    <?php if ($platform_logo_url): ?>
                                        <img src="<?php echo esc_url($platform_logo_url); ?>"
                                            alt="<?php echo esc_attr($platform_label); ?>" width="57" height="57" />
                                    <?php endif; ?>
                                </div>
                                <div class="mt-card-plataform-info d-flex flex-column">
                                    <div class="mt-platform-title text-white fw-medium text-base">
                                        <?php echo esc_html(Label::PLATFORM['title']); ?>
                                    </div>
                                    <?php if ($platform_label): ?>
                                        <div class="mt-platform-name fw-medium text-a8a29e text-base">
                                            <?php echo esc_html($platform_label); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray my-4" />

                        <!-- metas -->
                        <div class="mt-card-body">
                            <?php
                            $meta_order = (defined('Label::META_ORDER') ? Label::META_ORDER : array_keys(Label::PRODUCT_META));
                            $icon_map = (defined('Label::PRODUCT_META_ICONS') ? Label::PRODUCT_META_ICONS : []);

                            $items = [];
                            $pid = ($product instanceof WC_Product) ? $product->get_id() : 0;
                            if ($pid) {
                                foreach ($meta_order as $key) {
                                    if (!isset(Label::PRODUCT_META[$key]))
                                        continue;
                                    $val = $rich['meta'][$key] ?? get_post_meta($pid, $key, true);
                                    if ($val === '' || $val === null)
                                        continue;

                                    $items[] = [
                                        'key' => $key,
                                        'label' => Label::PRODUCT_META[$key],
                                        'value' => $val,
                                        'icon_class' => $icon_map[$key] ?? 'mt-icon',
                                    ];
                                }
                            }
                            ?>

                            <?php if (!empty($items)): ?>
                                <div class="mt-meta-grid">
                                    <?php foreach ($items as $it): ?>
                                        <div class="mt-meta-item">
                                            <i class="mt-icon <?php echo esc_attr($it['icon_class']); ?>"></i>
                                            <div class="mt-meta-text">
                                                <span class="mt-meta-label"><?php echo esc_html($it['label']); ?></span>
                                                <span class="mt-meta-value"><?php echo esc_html($it['value']); ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
                <div class="addons-block">
                    <?php
                    $has_subscription = true;
                    if (!WC()->cart->is_empty()) {
                        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                            $product = $cart_item['data'];
                            $product_id = $cart_item['product_id'];

                            $wc_product = wc_get_product($product_id);
                            $product_type = $product->get_type();

                            // echo 'Product Type: ' . $product_type . '<br>';
                            if (
                                $product_type === 'subscription' ||
                                $product_type === 'variable-subscription' ||
                                $product_type === 'subscription_variation' ||
                                (function_exists('wcs_is_subscription_product') && wcs_is_subscription_product($product)) ||
                                has_term('subscription', 'product_type', $product_id)
                            ) {
                                $has_subscription = true;
                                break;
                            }
                        }
                    } ?>
                    <?php if (function_exists('WC') && WC()->cart): ?>
                        <?php
                        $add_on_fields = WC()->checkout()->checkout_fields['add_ons'] ?? [];
                        $addon_options = $add_on_fields['e0e87f1']['options'] ?? [];
                        ?>
                        <?php if ($has_subscription && !empty($addon_options)): ?>
                            <div class="addons-block d-flex flex-column gap-3">
                                <div class="fw-medium leading-8 text-size-20 text-white">Customize Your Plan (Optional)</div>
                                <div class="checkout-addons">
                                    <div class="available-info d-flex flex-column flex-lg-row flex-md-row gap-2">
                                        <?php foreach ($addon_options as $option_key => $option_data): ?>
                                            <div
                                                class="addons-item addons-item-new d-flex gap-3 bg-1e1e1e rounded-16px w-100 <?php echo esc_attr($option_key); ?>">
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
                                                        <p class="text-a8a29e text-14px-line-20px fw-bold">
                                                            <?php echo esc_html(trim($option_data['description'])); ?>
                                                        </p>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="title"><?php if ($price_html)
                                                    echo '<div>' . esc_html($price_html) . '</div>'; ?>
                                                </div>

                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="billing-block mt-billing-card mt-card">
                    <div class="text-white fw-medium text-base">Billing Details</div>
                    <div class="billing-details">
                        <?php
                        // Display the WooCommerce checkout form
                        do_action('woocommerce_before_checkout_form');
                        ?>
                        <?php
                        // Display checkout fields
                        do_action('woocommerce_checkout_before_customer_details');
                        ?>
                        <div id="customer_details">
                            <?php
                            do_action('woocommerce_checkout_billing');
                            ?>
                            <?php
                            do_action('woocommerce_checkout_shipping');
                            ?>
                        </div>
                        <?php
                        do_action('woocommerce_checkout_after_customer_details');
                        ?>
                        <?php
                        // Display the order review section
                        // do_action('woocommerce_checkout_order_review');
                        ?>
                        <?php
                        do_action('woocommerce_after_checkout_form');
                        ?>
                    </div>
                </div>

            </form>

        </div>

        <div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-fullscreen-md-down">
                <div class="authentication-form modal-content align-items-center d-flex flex-column flex-shrink-0">
                    <div class="modal-header w-100 border-0 justify-content-between align-items-start p-0">
                        <h5 class="modal-title text-white heading-sm-medium" id="emailModalLabel">SIGN IN</h5>
                        <button type="button" class="p-0 border-0 bg-transparent shadow-none" data-bs-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">
                                <img src="https://subscriptions.megatrader.io/wp-content/uploads/2025/05/cancel-circle-1.png"
                                    alt="Close" style="width: 24px; height: 24px;" /></span>
                        </button>
                    </div>
                    <div class="modal-body d-flex flex-column align-items-center justify-content-center gap-32">
                        <div class="otp-message-container w-100 d-flex justify-content-start d-none">
                            <!-- Error -->
                            <div class="error-otp-message notifications notifications-error">
                                <div
                                    class="w-6 h-6 d-flex align-items-center justify-content-center rounded-full bg-red-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                                        aria-hidden="true" data-slot="icon" class="w-5 h-5 text-black">
                                        <path
                                            d="M5.28 4.22a.75.75 0 0 0-1.06 1.06L6.94 8l-2.72 2.72a.75.75 0 1 0 1.06 1.06L8 9.06l2.72 2.72a.75.75 0 1 0 1.06-1.06L9.06 8l2.72-2.72a.75.75 0 0 0-1.06-1.06L8 6.94 5.28 4.22Z">
                                        </path>
                                    </svg>
                                </div>
                                <span class="error-otp-text">This is an error message</span>
                            </div>
                            <!-- Éxito -->
                            <div class="success-otp-message notifications notifications-success" style="max-width: 600px;">
                                <div
                                    class="w-6 h-6 d-flex align-items-center justify-content-center rounded-full bg-teal-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                                        aria-hidden="true" data-slot="icon" class="w-5 h-5 text-black">
                                        <path fill-rule="evenodd"
                                            d="M12.416 3.376a.75.75 0 0 1 .208 1.04l-5 7.5a.75.75 0 0 1-1.154.114l-3-3a.75.75 0 0 1 1.06-1.06l2.353 2.353 4.493-6.74a.75.75 0 0 1 1.04-.207Z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>

                                <span class="success-otp-text">A new code has been sent to your email.</span>
                            </div>
                        </div>
                        <div class="sign-in__logo" style="height: 72px; width:72px">
                            <img src="https://subscriptions.megatrader.io/wp-content/uploads/2025/06/appIcon.svg"
                                alt="mt logo" class="rounded-4" />
                        </div>
                        <form method="post" class="woocommerce-form woocommerce-form-login login w-100 m-0"
                            style="max-width: 360px;">
                            <p class="text-body pb-2 text-center">Enter your email, and We will send an email with a code
                                verification.</p>
                            <input type="email" name="username" class="form-control otp-email-input" placeholder="Email"
                                data-gtm-form-interact-field-id="1"
                                style="background-color: var(--smoke-color) !important;">
                            <button type="button" class="get-otp-btn mt-4 ot-btn text-black w-100"
                                style="color: #000 !important;font-weight: 500 !important;">SEND</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Second Modal: OTP Input -->
        <!-- OTP Verification Modal -->
        <div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-fullscreen-md-down">
                <div class="authentication-form modal-content align-items-center d-flex flex-column gap-4">
                    <!-- Header -->
                    <div class="modal-header w-100 border-0 justify-content-between align-items-start p-0">
                        <h5 class="modal-title text-white heading-sm-medium" id="otpModalLabel">VERIFY OTP</h5>
                        <button type="button" class="p-0 border-0 bg-transparent shadow-none" data-bs-dismiss="modal"
                            aria-label="Close">
                            <img src="https://subscriptions.megatrader.io/wp-content/uploads/2025/05/cancel-circle-1.png"
                                alt="Close" style="width: 24px; height: 24px;" />
                        </button>
                    </div>
                    <div class="modal-body d-flex flex-column align-items-center justify-content-center gap-32">
                        <!-- Unified message container (copiar igual al de otpModal) -->
                        <div class="otp-message-container w-100 d-flex justify-content-start d-none">
                            <!-- Error -->
                            <div class="error-otp-message notifications notifications-error">
                                <div
                                    class="w-6 h-6 d-flex align-items-center justify-content-center rounded-full bg-red-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                                        aria-hidden="true" data-slot="icon" class="w-5 h-5 text-black">
                                        <path
                                            d="M5.28 4.22a.75.75 0 0 0-1.06 1.06L6.94 8l-2.72 2.72a.75.75 0 1 0 1.06 1.06L8 9.06l2.72 2.72a.75.75 0 1 0 1.06-1.06L9.06 8l2.72-2.72a.75.75 0 0 0-1.06-1.06L8 6.94 5.28 4.22Z">
                                        </path>
                                    </svg>
                                </div>
                                <span class="error-otp-text">Some error</span>
                            </div>
                            <!-- Success -->
                            <div class="success-otp-message notifications notifications-success">
                                <div
                                    class="w-6 h-6 d-flex align-items-center justify-content-center rounded-full bg-teal-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                                        aria-hidden="true" data-slot="icon" class="w-5 h-5 text-black">
                                        <path fill-rule="evenodd"
                                            d="M12.416 3.376a.75.75 0 0 1 .208 1.04l-5 7.5a.75.75 0 0 1-1.154.114l-3-3a.75.75 0 0 1 1.06-1.06l2.353 2.353 4.493-6.74a.75.75 0 0 1 1.04-.207Z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <span class="success-otp-text">A new code has been sent</span>
                            </div>
                        </div>
                        <!-- Logo -->
                        <div class="sign-in__logo" style="height: 72px; width: 72px;">
                            <img src="https://subscriptions.megatrader.io/wp-content/uploads/2025/06/appIcon.svg"
                                alt="mt logo" class="rounded-4" />
                        </div>
                        <!-- Instruction -->
                        <p class="text-body text-center mb-0" style="color: #E4E4E7;">
                            We sent an OTP to <strong class="text-white" id="otp-email-display">john@doe.com</strong><br>
                            Enter it below to continue
                        </p>
                        <!-- OTP input boxes -->
                        <form method="post" class="woocommerce-form w-100 px-4" style="max-width: 360px;">
                            <div class="otp-inputs d-flex justify-content-between gap-2 mb-4">
                                <input type="hidden" name="username" value="">
                                <input type="text" maxlength="1" class="otp-box" />
                                <input type="text" maxlength="1" class="otp-box" />
                                <input type="text" maxlength="1" class="otp-box" />
                                <input type="text" maxlength="1" class="otp-box" />
                                <input type="text" maxlength="1" class="otp-box" />
                                <input type="text" maxlength="1" class="otp-box" />
                            </div>
                            <!-- Resend + Timer -->
                            <div class="d-flex justify-content-center align-items-center gap-3 mb-4">
                                <a href="#" class="resend-otp fw-semibold text-decoration-underline">Resend OTP</a>
                            </div>
                            <!-- Submit buttons -->
                            <button type="submit"
                                class="verify-otp-btn mt-2 ot-btn w-100 fw-medium text-black rounded-xl p-y-12-mega p-x-16-mega bg-mgt-primary">
                                <?php esc_html_e('VERIFY', 'woocommerce'); ?>
                            </button>
                            <button
                                class="btn w-100 d-flex back-otp-back justify-content-center align-items-center text-uppercase text-white mt-3 fw-medium rounded-xl border border-neutral-700 bg-stone-800 p-x-16-mega p-y-12-mega"
                                type="button">
                                <?php esc_html_e('BACK TO LOGIN', 'woocommerce'); ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <?php do_action('woocommerce_after_checkout_form', $checkout); ?>

    <?php endif; ?>