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



// Helpers 
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

/** ==== Data for User Billing ==== */

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
        // fallback al email de la cuenta
        $u = wp_get_current_user();
        if ($u && $u->user_email)
            $billing['email'] = $u->user_email;
    }
}
$has_billing = !empty($billing['first_name']) || !empty($billing['address_1']) || !empty($billing['phone']) || !empty($billing['email']);

// Mapeo legible de país/estado (si existen)
$country_name = $billing['country'];
$state_name = $billing['state'];
if (function_exists('WC') && WC()->countries) {
    $country_name = WC()->countries->countries[$billing['country']] ?? $billing['country'];
    $state_name = WC()->countries->states[$billing['country']][$billing['state']] ?? $billing['state'];
}
$mt_billing_nonce = wp_create_nonce('mt_save_billing');


$fflag = isset($_GET['v2']);

if ($fflag): ?>

    <div class="main-container pt-32 pb-32">
        <div class="container">
            <?php include get_stylesheet_directory() . '/woocommerce/checkout/step_2.php'; ?>
        </div>
    </div>

<?php else: ?>

    <div class="container">
        <div class="mt-page">
            <div class="mt-page__sidebar">
                <?php render_sidebar() ?>
            </div>
            <div class="mt-page__main">
                <?php render_step_selector(true); ?>
                <form id="checkout-form" name="checkout" method="post"
                    class="checkout woocommerce-checkout d-flex flex-column gap-32" novalidate
                    action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">
                    <div class="product-container">
                        <div class="mt-card">
                            <div class="mt-card-wrapper d-flex flex-column gap-4">
                                <div class="mt-card-header d-flex gap-3 align-items-center flex-wrap">
                                    <div class="mt-card-plan d-flex flex-column flex-grow-1">
                                        <div
                                            class="mt-card-plan-size text-white text-40px fw-medium text-uppercase leading-48px">
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

                                <hr class="border-gray m-0" />

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
                                                <div class="mt-meta-item" data-meta-key="<?php echo esc_attr($it['key']); ?>">
                                                    <i class="mt-icon mt-icon-white <?php echo esc_attr($it['icon_class']); ?>"></i>
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
                    </div>

                    <div class="addons-container">
                        <?php
                        $has_subscription = true;
                        if (!WC()->cart->is_empty()) {
                            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                                $product = $cart_item['data'];
                                $product_id = $cart_item['product_id'];

                                $wc_product = wc_get_product($product_id);
                                $product_type = $product->get_type();

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
                                    <div class="fw-medium leading-8 text-size-20 text-white">Customize Your Plan (Optional)
                                    </div>
                                    <div class="checkout-addons">
                                        <div class="available-info d-flex flex-column flex-lg-row flex-md-row gap-2">
                                            <?php foreach ($addon_options as $option_key => $option_data): ?>
                                                <div
                                                    class="addons-item addons-item-new d-flex gap-3 align-items-center bg-1e1e1e rounded-16px w-100  <?php echo esc_attr($option_key); ?>">
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



                    <div id="billing-container" class="d-flex flex-column gap-3">
                        <div class="fw-medium leading-8 text-size-20 text-white">Billing Details </div>
                        <div class="billing-container mt-billing-card mt-card">
                            <!-- ====== VISTA RESUMEN (visible si hay datos) ====== -->
                            <div id="mt-billing-summary" class="<?php echo $has_billing ? '' : 'd-none'; ?>">
                                <div class="d-flex flex-column gap-3 w-100 position-relative">
                                    <div class="d-flex gap-3 justify-content-end position-absolute end-0">
                                        <a href="#" id="mt-billing-change" class="text-decoration-underline fw-medium"
                                            style="color:#FFD78A;">Edit Billing</a>
                                    </div>

                                    <div class="d-flex justify-content-between gap-3">
                                        <div class="flex-fill d-flex flex-column gap-3">
                                            <div class="d-flex align-items-start gap-2">
                                                <i class="mt-icon mt-icon_account"></i>
                                                <div class="text-base fw-medium text-white" id="mt-sum-name">
                                                    <?php echo esc_html(trim(($billing['first_name'] ?? '') . ' ' . ($billing['last_name'] ?? '')) ?: '—'); ?>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center gap-2">
                                                <i class="mt-icon mt-icon_mail"></i>
                                                <div class="text-base fw-medium text-white" id="mt-sum-email">
                                                    <?php echo esc_html($billing['email'] ?: '—'); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-fill d-flex flex-column gap-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="mt-icon mt-icon_phone"></i>
                                                <div class="text-base fw-medium text-white" id="mt-sum-phone">
                                                    <?php echo esc_html($billing['phone'] ?: '—'); ?>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-start gap-2">
                                                <i class="mt-icon mt-icon_home"></i>
                                                <?php
                                                $line1 = trim(($billing['address_1'] ?? '') . (!empty($billing['address_2']) ? ', ' . $billing['address_2'] : ''));
                                                $line2 = trim(implode(', ', array_filter([($billing['city'] ?? ''), $state_name, ($billing['postcode'] ?? '')])));
                                                $line3 = trim($country_name ?: '');

                                                $addr_lines = array_filter([$line1, $line2, $line3], fn($v) => $v !== '');
                                                ?>
                                                <div id="mt-sum-address" class="text-base fw-medium text-white">
                                                    <?php echo implode('<br>', array_map('esc_html', $addr_lines)); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ====== VISTA FORM (WooCommerce) ====== -->
                            <div id="mt-billing-form" class="<?php echo $has_billing ? 'd-none' : ''; ?>">
                                <div class="billing-details pt-3">
                                    <?php
                                    $user_id = get_current_user_id();
                                    $current_state = $user_id ? get_user_meta($user_id, 'billing_state', true) : '';
                                    ?>

                                    <?php
                                    do_action('woocommerce_before_checkout_form');
                                    do_action('woocommerce_checkout_before_customer_details');
                                    ?>
                                    <div id="customer_details">
                                        <input type="hidden" id="billing_state_current"
                                            value="<?php echo esc_attr($current_state); ?>">
                                        <?php do_action('woocommerce_checkout_billing'); ?>
                                        <?php do_action('woocommerce_checkout_shipping'); ?>
                                    </div>
                                    <?php
                                    do_action('woocommerce_checkout_after_customer_details');
                                    do_action('woocommerce_after_checkout_form');
                                    ?>

                                    <!-- Botón para guardar en el perfil -->
                                    <input type="hidden" id="mt_save_billing_nonce"
                                        value="<?php echo esc_attr($mt_billing_nonce); ?>">
                                    <button type="button" id="mt-save-billing"
                                        class="ot-btn bg-mgt-primary text-black fw-medium mt-3 w-100">
                                        Save details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="payment-container review-container">
                        <div class="fw-medium leading-8 text-size-20 text-white">Payment Method</div>

                        <div class="mt-payment-cards" id="mt-payment">
                            <?php
                            wc_get_template('checkout/payment.php', array('checkout' => WC()->checkout()));
                            ?>
                        </div>

                        <div class="mt-card mt-3">

                            <?php
                            wc_get_template('checkout/review-order.php');
                            ?>
                            <hr class="border-gray m-0">
                            <div class="form-row place-order">
                                <noscript>
                                    <?php
                                    /* translators: $1 and $2 opening and closing emphasis tags respectively */
                                    printf(esc_html__('Since your browser does not support JavaScript, or it is disabled, please ensure you click the %1$sUpdate Totals%2$s button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce'), '<em>', '</em>');
                                    ?>
                                    <br /><button type="submit" class="button alt" name="woocommerce_checkout_update_totals"
                                        value="<?php esc_attr_e('Update totals', 'woocommerce'); ?>"><?php esc_html_e('Update totals', 'woocommerce'); ?></button>
                                </noscript>


                                <?php do_action('woocommerce_review_order_before_submit'); ?>

                                <?php
                                if (!isset($order_button_text)) {
                                    $order_button_text = apply_filters(
                                        'woocommerce_order_button_text',
                                        __('Place order', 'woocommerce')
                                    );
                                }
                                ?>
                                <?php echo apply_filters('woocommerce_order_button_html', '<button type="submit" class="mega-btn-md mega-btn-primary-md w-100" name="woocommerce_checkout_place_order" value="' . esc_attr($order_button_text) . '" data-value="' . esc_attr($order_button_text) . '">' . esc_html($order_button_text) . '</button>'); // @codingStandardsIgnoreLine ?>

                                <div class="d-flex gap-1 align-items-center pt-3">
                                    <i class="mt-icon mt-icon_lock"></i>
                                    <span class="text-base fw-light text-a8a29e">All payments are secured and
                                        encrypted.</span>
                                </div>

                                <?php do_action('woocommerce_review_order_after_submit'); ?>

                                <?php wp_nonce_field('woocommerce-process_checkout', 'woocommerce-process-checkout-nonce'); ?>
                            </div>

                        </div>
                    </div>
                </form>

            </div>

            <?php do_action('woocommerce_after_checkout_form', $checkout); ?>
        </div>
    </div>


    </div>
<?php endif; ?>