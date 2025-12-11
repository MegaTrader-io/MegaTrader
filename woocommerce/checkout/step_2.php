<?php
/**
 * Template Name: Checkout Step 2 (New)
 * URL: /checkout/?v2&add-to-cart=ID[&variation_id=VID&attribute_pa_*=...&quantity=n]
 * Enfocado en WC_Product, debug enriquecido + Billing + Payment (sin T&C ni botón) + Order Summary.
 */

if (!defined('ABSPATH'))
    exit;

/* -------------------- Helpers mínimos -------------------- */
if (!function_exists('mt_get_param')) {
    function mt_get_param($key, $default = '')
    {
        if (!isset($_GET[$key]))
            return $default;
        return wc_clean(wp_unslash($_GET[$key]));
    }
}

if (!function_exists('mt_collect_variation_attrs_from_query')) {
    function mt_collect_variation_attrs_from_query()
    {
        $attrs = [];
        foreach ($_GET as $k => $v) {
            if (strpos($k, 'attribute_') === 0) {
                $attrs[$k] = wc_clean(wp_unslash($v));
            }
        }
        return $attrs;
    }
}

/** Consigue el producto seleccionado (variación si hay; si no, parent/simple; si nada, 1º del cart) */
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
            $cart = WC()->cart->get_cart();               // <- evitar notice de referencia
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

/** Añade al carrito si viene por URL; luego limpia la query manteniendo ?v2 (para evitar duplicados al refrescar) */
if (!function_exists('mt_maybe_add_to_cart_from_query')) {
    function mt_maybe_add_to_cart_from_query()
    {
        $parent_id = absint(mt_get_param('add-to-cart'));
        $variation_id = absint(mt_get_param('variation_id'));
        $qty = max(1, absint(mt_get_param('quantity', 1)));
        $attrs = mt_collect_variation_attrs_from_query();

        if (!$parent_id || !function_exists('WC') || !WC()->cart)
            return;

        // Evitar duplicados
        $already = false;
        foreach (WC()->cart->get_cart() as $item) {
            if ($variation_id) {
                if ((int) $item['variation_id'] === (int) $variation_id) {
                    $already = true;
                    break;
                }
            } else {
                if ((int) $item['product_id'] === (int) $parent_id) {
                    $already = true;
                    break;
                }
            }
        }

        if (!$already) {
            $parent = wc_get_product($parent_id);
            if ($parent) {
                if ($variation_id) {
                    WC()->cart->add_to_cart($parent_id, $qty, $variation_id, $attrs);
                } else {
                    if ($parent->is_type('simple')) {
                        WC()->cart->add_to_cart($parent_id, $qty);
                    }
                }
                WC()->cart->calculate_totals();
            }
        }

        // Limpia URL (mantén ?v2)
        $params_to_remove = ['add-to-cart', 'variation_id', 'quantity', 'type', 'platform', 'size'];
        foreach ($_GET as $k => $v) {
            if (strpos($k, 'attribute_') === 0)
                $params_to_remove[] = $k;
        }
        $clean = remove_query_arg($params_to_remove);
        if (array_key_exists('v2', $_GET) && strpos($clean, 'v2') === false) {
            $clean = add_query_arg(['v2' => ''], $clean);
        }
        foreach ($params_to_remove as $p) {
            if (isset($_GET[$p])) {
                wp_safe_redirect($clean);
                exit;
            }
        }
    }
}

/**
 * Devuelve el valor “bonito” de un atributo taxonómico (ej. 'pa_account-size').
 */
if (!function_exists('mt_get_attr_label')) {
    function mt_get_attr_label(WC_Product $product = null, $taxonomy = '')
    {
        if (!$product || !$taxonomy)
            return '';
        $val = $product->get_attribute($taxonomy);
        if (!empty($val))
            return $val; // ej. "$150,000"
        if ('variation' === $product->get_type()) {
            $va = $product->get_variation_attributes(); // ['attribute_pa_*' => 'slug']
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

/** ---------- Debug enriquecido ---------- */
if (!function_exists('mt_build_rich_debug_payload')) {
    function mt_build_rich_debug_payload($product = null)
    {
        $payload = [
            'query' => $_GET,
            'selection' => [
                'parent_id' => isset($_GET['add-to-cart']) ? absint($_GET['add-to-cart']) : 0,
                'variation_id' => isset($_GET['variation_id']) ? absint($_GET['variation_id']) : 0,
                'quantity' => isset($_GET['quantity']) ? max(1, absint($_GET['quantity'])) : 1,
            ],
            'cart_first_item' => null,
            'product' => null,
            'attributes' => [],
            'meta' => [],
        ];

        if (function_exists('WC') && WC()->cart && !WC()->cart->is_empty()) {
            $cart = WC()->cart->get_cart();               // <- evitar notice de referencia
            $first = $cart ? reset($cart) : null;
            if ($first) {
                $payload['cart_first_item'] = [
                    'product_id' => $first['product_id'] ?? null,
                    'variation_id' => $first['variation_id'] ?? null,
                    'quantity' => $first['quantity'] ?? null,
                    'variation' => $first['variation'] ?? null,
                ];
            }
        }

        if (!($product instanceof WC_Product))
            return $payload;

        $pid = $product->get_id();
        $ptype = $product->get_type();
        $payload['product'] = [
            'id' => $pid,
            'type' => $ptype,
            'name' => $product->get_name(),
            'sku' => $product->get_sku(),
            'price' => $product->get_price(),
            'regular_price' => $product->get_regular_price(),
            'sale_price' => $product->get_sale_price(),
        ];

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

            $term = null;
            if ($slug)
                $term = get_term_by('slug', $slug, $tx);
            elseif ($label)
                $term = get_term_by('name', $label, $tx);

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

            $payload['attributes'][$tx] = [
                'label' => $label,
                'slug' => $slug,
                'term' => $term_data,
            ];
        }

        $custom_meta_keys = [
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
        ];
        foreach ($custom_meta_keys as $k) {
            $payload['meta'][$k] = get_post_meta($pid, $k, true);
        }

        if ($ptype === 'variation') {
            $payload['product']['variation'] = [
                'parent_id' => $product->get_parent_id(),
                'attributes' => $product->get_variation_attributes(),
                'price' => $product->get_price(),
                'regular_price' => $product->get_regular_price(),
                'sale_price' => $product->get_sale_price(),
            ];
        }

        return $payload;
    }
}

/* -------------------- Arranque -------------------- */
mt_maybe_add_to_cart_from_query();

$checkout = WC()->checkout();
if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
    echo esc_html__('You must be logged in to checkout.', 'woocommerce');
    return;
}

/* Scripts nativos Woo */
wp_enqueue_script('wc-country-select');
wp_enqueue_script('wc-address-i18n');
wp_enqueue_script('wc-checkout');

/* -------------------- DATA para la card -------------------- */
$product = mt_get_selected_product();
$plan_size = $product ? mt_get_attr_label($product, 'pa_account-size') : '';
$plan_size_slug = $product ? ($product->is_type('variation')
    ? ($product->get_variation_attributes()['attribute_pa_account-size'] ?? '')
    : (wc_get_product_terms($product->get_id(), 'pa_account-size', ['fields' => 'slugs'])[0] ?? '')
) : '';
$plan_type = $product ? $product->get_attribute('pa_account-types') : '';
$market_type = $product ? $product->get_attribute('pa_market-type') : '';

/* -------------------- Payload enriquecido y datos de plataforma -------------------- */
$rich = mt_build_rich_debug_payload($product);
$platform_logo_url = $rich['attributes']['pa_platform']['term']['image_url'] ?? '';
$platform_label = $rich['attributes']['pa_platform']['label'] ?? ($rich['attributes']['pa_platform']['term']['name'] ?? 'Platform');
$platform_desc = $rich['attributes']['pa_platform']['term']['description'] ?? '';
$platform_features = (array) ($rich['attributes']['pa_platform']['term']['custom_repeater_field'] ?? []);

?>
<form id="checkout-form" name="checkout" method="post" class="checkout woocommerce-checkout d-flex flex-column gap-32"
    novalidate action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">

    <!-- ===== CARD de producto ===== -->
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
                            <div class="badge-mega badge-mega-sm badge-mega-default"><?php echo esc_html($plan_type); ?>
                            </div>
                            <div class="badge-mega badge-mega-sm badge-mega-primary">
                                <?php echo esc_html($market_type); ?></div>
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
                            <?php echo esc_html(Label::PLATFORM['title']); ?></div>
                        <?php if ($platform_label): ?>
                            <div class="mt-platform-name fw-medium text-a8a29e text-base">
                                <?php echo esc_html($platform_label); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <hr class="border-gray my-4" />

            <!-- metas -->
            <div class="mt-card-body">
                <?php
                $meta_order = [
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
                ];
                $icon_map = [
                    'profit_target' => 'mt-icon_profit',
                    'max_contracts' => 'mt-icon_max-contract',
                    'daily_loss_limit' => 'mt-icon_daily-loss-limit',
                    'daily_loss_limit_soft_breach' => 'mt-icon_daily-loss-limit',
                    'trailing_max_drawdown' => 'mt-icon_max-drawdown',
                    'drawdown_mode' => 'mt-icon_drawdown-mode',
                    'min_trading_days' => 'mt-icon_calendar',
                    'min_trading_days_to_payout' => 'mt-icon_calendar',
                    'reset_fee' => 'mt-icon_reset-fee',
                    'activation_fee' => 'mt-icon_lightning',
                    'consistency' => 'mt-icon_checkmark-solid',
                    'max_accounts' => 'mt-icon_arrow-circle-solid',
                ];
                $items = [];
                $pid = ($product instanceof WC_Product) ? $product->get_id() : 0;
                if ($pid) {
                    foreach ($meta_order as $key) {
                        if (!isset(Label::PRODUCT_META[$key]))
                            continue;
                        $val = $rich['meta'][$key] ?? get_post_meta($pid, $key, true);
                        if ($val === '' || $val === null)
                            continue;
                        $items[] = ['key' => $key, 'label' => Label::PRODUCT_META[$key], 'value' => $val, 'icon_class' => $icon_map[$key] ?? 'mt-icon'];
                    }
                }
                ?>
                <?php if (!empty($items)): ?>
                    <div class="mt-meta-grid">
                        <?php foreach ($items as $it): ?>
                            <div class="mt-meta-item">
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

    <!-- ===== Add-ons (si aplica) ===== -->
    <?php
    $has_subscription = false;
    if ($product instanceof WC_Product) {
        $ptype = $product->get_type();
        if (
            $ptype === 'subscription' || $ptype === 'variable-subscription' || $ptype === 'subscription_variation'
            || (function_exists('wcs_is_subscription_product') && wcs_is_subscription_product($product))
            || has_term('subscription', 'product_type', $product->get_id())
        ) {
            $has_subscription = true;
        }
    }
    if (!$has_subscription && function_exists('WC') && WC()->cart && !WC()->cart->is_empty()) {
        foreach (WC()->cart->get_cart() as $ci) {
            $p = $ci['data'];
            if ($p instanceof WC_Product) {
                $ptype = $p->get_type();
                if (
                    $ptype === 'subscription' || $ptype === 'variable-subscription' || $ptype === 'subscription_variation'
                    || (function_exists('wcs_is_subscription_product') && wcs_is_subscription_product($p))
                    || has_term('subscription', 'product_type', $p->get_id())
                ) {
                    $has_subscription = true;
                    break;
                }
            }
        }
    }
    $add_on_fields = WC()->checkout()->checkout_fields['add_ons'] ?? [];
    if ($has_subscription && !empty($add_on_fields)): ?>
        <div class="addons-block d-flex flex-column gap-3">
            <div class="fw-medium leading-8 text-size-20 text-white">Customize Your Plan (Optional)</div>
            <div class="checkout-addons">
                <div class="available-info d-flex flex-column flex-lg-row flex-md-row gap-2">
                    <?php foreach ($add_on_fields as $field_key => $field_conf):
                        $options = $field_conf['options'] ?? [];
                        foreach ($options as $option_key => $option_data):
                            $label_full = is_array($option_data) ? ($option_data['label'] ?? '') : $option_data;
                            $label_text = wp_strip_all_tags((string) $label_full);
                            $price_html = '';
                            if (preg_match('/^(.*?)\s*\((.*?)\)$/', $label_text, $m)) {
                                $label_text = trim($m[1]);
                                $price_html = $m[2];
                            }
                            $desc = (is_array($option_data) && !empty($option_data['description'])) ? trim((string) $option_data['description']) : '';
                            ?>
                            <div
                                class="addons-item addons-item-new d-flex gap-3 bg-1e1e1e rounded-16px w-100 <?php echo esc_attr($option_key); ?>">
                                <div class="addons-header d-flex flex-column gap-1">
                                    <div class="text-base text-white fw-medium"><?php echo esc_html($label_text); ?></div>
                                    <?php if ($desc): ?>
                                        <div class="text-a8a29e text-14px-line-20px fw-bold"><?php echo esc_html($desc); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="price"><?php if ($price_html)
                                    echo '<div>' . esc_html($price_html) . '</div>'; ?></div>
                            </div>
                        <?php endforeach; endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- ===== Billing Details ===== -->
    <div class="mt-billing-card mt-card" id="mt-billing">
        <div class="text-white fw-medium text-base mb-3">Billing Details</div>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="visually-hidden"
                    for="billing_first_name"><?php esc_html_e('First Name', 'megatrader'); ?></label>
                <input type="text" class="form-control" name="billing_first_name" id="billing_first_name"
                    placeholder="<?php esc_attr_e('First Name', 'megatrader'); ?>"
                    value="<?php echo esc_attr($checkout->get_value('billing_first_name')); ?>" required />
            </div>
            <div class="col-md-6">
                <label class="visually-hidden"
                    for="billing_last_name"><?php esc_html_e('Last Name', 'megatrader'); ?></label>
                <input type="text" class="form-control" name="billing_last_name" id="billing_last_name"
                    placeholder="<?php esc_attr_e('Last Name', 'megatrader'); ?>"
                    value="<?php echo esc_attr($checkout->get_value('billing_last_name')); ?>" required />
            </div>

            <div class="col-12">
                <label class="visually-hidden"
                    for="billing_email"><?php esc_html_e('Email Address', 'megatrader'); ?></label>
                <input type="email" class="form-control" name="billing_email" id="billing_email"
                    placeholder="<?php esc_attr_e('Email Address', 'megatrader'); ?>"
                    value="<?php echo esc_attr($checkout->get_value('billing_email')); ?>" required />
            </div>

            <div class="col-12">
                <label class="visually-hidden" for="billing_phone"><?php esc_html_e('Phone', 'megatrader'); ?></label>
                <input type="tel" class="form-control" name="billing_phone" id="billing_phone"
                    placeholder="<?php esc_attr_e('Phone', 'megatrader'); ?>"
                    value="<?php echo esc_attr($checkout->get_value('billing_phone')); ?>" required />
            </div>

            <div class="col-12">
                <label class="visually-hidden"
                    for="billing_address_1"><?php esc_html_e('Address', 'megatrader'); ?></label>
                <input type="text" class="form-control" name="billing_address_1" id="billing_address_1"
                    placeholder="<?php esc_attr_e('House number and street name', 'megatrader'); ?>"
                    value="<?php echo esc_attr($checkout->get_value('billing_address_1')); ?>" required />
            </div>

            <div class="col-12">
                <label class="visually-hidden"
                    for="billing_address_2"><?php esc_html_e('Apartment, suite, etc. (optional)', 'megatrader'); ?></label>
                <input type="text" class="form-control" name="billing_address_2" id="billing_address_2"
                    placeholder="<?php esc_attr_e('Apartment, suite, etc. (optional)', 'megatrader'); ?>"
                    value="<?php echo esc_attr($checkout->get_value('billing_address_2')); ?>" />
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="visually-hidden"
                    for="billing_country"><?php esc_html_e('Country/Region', 'megatrader'); ?></label>
                <select name="billing_country" id="billing_country" class="form-select form-control woocommerce-select"
                    required>
                    <?php foreach (WC()->countries->get_allowed_countries() as $key => $value) {
                        echo '<option value="' . esc_attr($key) . '" ' . selected($checkout->get_value('billing_country'), $key, false) . '>' . esc_html($value) . '</option>';
                    } ?>
                </select>
            </div>

            <div class="col-lg-3 col-md-6">
                <div id="billing_state_wrapper">
                    <?php
                    $country = $checkout->get_value('billing_country');
                    $state = $checkout->get_value('billing_state');
                    $states = WC()->countries->get_states($country);
                    if (!empty($states)) {
                        echo '<select name="billing_state" id="billing_state" class="form-select form-control" required>';
                        echo '<option value="" disabled selected>' . esc_html__('Select an option', 'megatrader') . '</option>';
                        foreach ($states as $key => $label) {
                            echo '<option value="' . esc_attr($key) . '" ' . selected($state, $key, false) . '>' . esc_html($label) . '</option>';
                        }
                        echo '</select>';
                    } elseif ($country) {
                        echo '<input type="text" name="billing_state" id="billing_state" class="form-control" placeholder="' . esc_attr__('State / County', 'megatrader') . '" value="' . esc_attr($state) . '" required />';
                    } else {
                        echo '<input type="hidden" name="billing_state" id="billing_state" value="' . esc_attr($state) . '" />';
                    }
                    ?>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="visually-hidden" for="billing_city"><?php esc_html_e('City', 'megatrader'); ?></label>
                <input type="text" class="form-control" name="billing_city" id="billing_city"
                    placeholder="<?php esc_attr_e('City', 'megatrader'); ?>"
                    value="<?php echo esc_attr($checkout->get_value('billing_city')); ?>" required />
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="visually-hidden"
                    for="billing_postcode"><?php esc_html_e('Postcode / ZIP', 'megatrader'); ?></label>
                <input type="text" class="form-control" name="billing_postcode" id="billing_postcode"
                    placeholder="<?php esc_attr_e('Postcode / ZIP', 'megatrader'); ?>"
                    value="<?php echo esc_attr($checkout->get_value('billing_postcode')); ?>" required />
            </div>
        </div>
    </div>

    <!-- ===== Payment (solo métodos) ===== -->
    <div class="mt-payment-cards" id="mt-payment">
        <div class="text-white fw-medium text-base mb-3"><?php esc_html_e('Payment', 'megatrader'); ?></div>
        <?php
        // Render del template nativo y eliminación del bloque "place-order" (T&C + botón)
        ob_start();
        // Esto trae la UI completa de gateways (incluye tokens guardados si el gateway los pinta)
        if (function_exists('woocommerce_checkout_payment')) {
            woocommerce_checkout_payment();
        }
        $payment_html = ob_get_clean();

        // Quitar el contenedor de submit/T&C (coincide con .place-order)
        $payment_html = preg_replace('/<div[^>]*class="[^"]*place-order[^"]*"[^>]*>.*?<\/div>\s*/is', '', $payment_html);

        echo $payment_html; // queda igual que tu captura, sin T&C ni botón
        ?>
    </div>

    <!-- ===== Order Summary  ===== -->

    <div class="mt-card">
        <?php
        do_action('woocommerce_checkout_order_review');
        ?>
        <div class="form-row place-order">
            <noscript>
                <?php
                /* translators: $1 and $2 opening and closing emphasis tags respectively */
                printf(esc_html__('Since your browser does not support JavaScript, or it is disabled, please ensure you click the %1$sUpdate Totals%2$s button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce'), '<em>', '</em>');
                ?>
                <br /><button type="submit" class="button alt" name="woocommerce_checkout_update_totals"
                    value="<?php esc_attr_e('Update totals', 'woocommerce'); ?>"><?php esc_html_e('Update totals', 'woocommerce'); ?></button>
            </noscript>

            <!-- COUPONS SECTION GOES HERE -->


            <?php do_action('woocommerce_review_order_before_submit'); ?>


            <?php echo apply_filters('woocommerce_order_button_html', '<button type="submit" class="mega-btn-md mega-btn-primary-md w-100" name="woocommerce_checkout_place_order" value="' . esc_attr($order_button_text) . '" data-value="' . esc_attr($order_button_text) . '">' . esc_html($order_button_text) . '</button>'); // @codingStandardsIgnoreLine ?>

            <p class="info-text fw-light">
                <svg width="16" height="21" viewBox="0 0 16 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M2 21C1.45 21 0.979167 20.8042 0.5875 20.4125C0.195833 20.0208 0 19.55 0 19V9C0 8.45 0.195833 7.97917 0.5875 7.5875C0.979167 7.19583 1.45 7 2 7H3V5C3 3.61667 3.4875 2.4375 4.4625 1.4625C5.4375 0.4875 6.61667 0 8 0C9.38333 0 10.5625 0.4875 11.5375 1.4625C12.5125 2.4375 13 3.61667 13 5V7H14C14.55 7 15.0208 7.19583 15.4125 7.5875C15.8042 7.97917 16 8.45 16 9V19C16 19.55 15.8042 20.0208 15.4125 20.4125C15.0208 20.8042 14.55 21 14 21H2ZM8 16C8.55 16 9.02083 15.8042 9.4125 15.4125C9.80417 15.0208 10 14.55 10 14C10 13.45 9.80417 12.9792 9.4125 12.5875C9.02083 12.1958 8.55 12 8 12C7.45 12 6.97917 12.1958 6.5875 12.5875C6.19583 12.9792 6 13.45 6 14C6 14.55 6.19583 15.0208 6.5875 15.4125C6.97917 15.8042 7.45 16 8 16ZM5 7H11V5C11 4.16667 10.7083 3.45833 10.125 2.875C9.54167 2.29167 8.83333 2 8 2C7.16667 2 6.45833 2.29167 5.875 2.875C5.29167 3.45833 5 4.16667 5 5V7Z"
                        fill="var(--body-color)" />
                </svg>
                <span>All payments are secured and encrypted. </span>
            </p>

            <?php do_action('woocommerce_review_order_after_submit'); ?>

            <?php wp_nonce_field('woocommerce-process_checkout', 'woocommerce-process-checkout-nonce'); ?>
        </div>
    </div>



</form>





<script>
    jQuery(function ($) {
        // refrescar estados al cambiar país (usa tu endpoint actual)
        $('#billing_country').on('change', function () {
            var country = $(this).val();
            $.post(
                (typeof woocommerce_params !== 'undefined' ? woocommerce_params.ajax_url : '<?php echo admin_url('admin-ajax.php'); ?>'),
                { action: 'get_cities', country: country, state: $('#billing_state').val() || '' },
                function (html) { $('#billing_state_wrapper').html(html); }
            );
        });
    });
</script>