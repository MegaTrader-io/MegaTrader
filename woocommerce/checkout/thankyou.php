<?php
/**
 * Thankyou page
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.1.0
 */

defined('ABSPATH') || exit;

/* =========================================================
 * Helpers
 * =======================================================*/

/**
 * Acceso seguro a valores anidados en array/stdClass.
 */
if (!function_exists('mt_get_nested')) {
    function mt_get_nested($data, ...$keys)
    {
        $cur = $data;
        foreach ($keys as $k) {
            if (is_array($cur)) {
                if (!array_key_exists($k, $cur))
                    return null;
                $cur = $cur[$k];
            } elseif (is_object($cur)) {
                if (!isset($cur->{$k}))
                    return null;
                $cur = $cur->{$k};
            } else {
                return null;
            }
        }
        return $cur;
    }
}

if (!function_exists('mt_canon_brand_key')) {
    function mt_canon_brand_key($raw)
    {
        if (!$raw)
            return '';
        $k = strtolower(preg_replace('/[^a-z]/', '', (string) $raw));
        $map = [
            'americanexpress' => 'amex',
            'amex' => 'amex',
            'mastercard' => 'mastercard',
            'master' => 'mastercard',
            'mc' => 'mastercard',
            'visa' => 'visa',
            'visadebit' => 'visa',
            'visaelectron' => 'visa',
            'discover' => 'discover',
            'discovernetwork' => 'discover',
        ];
        return $map[$k] ?? '';
    }
}

/**
 * Intenta consultar Stripe (plugin Woo Stripe) para extraer brand/last4.
 * Devuelve ['brand_raw','brand','last4','api_endpoint','api_ok','raw'].
 */
if (!function_exists('mt_try_fetch_brand_last4_via_stripe_api')) {
    function mt_try_fetch_brand_last4_via_stripe_api($order)
    {
        $out = [
            'brand_raw' => '',
            'brand' => '',
            'last4' => '',
            'api_endpoint' => '',
            'api_ok' => false,
            'raw' => null,
        ];
        if (!$order instanceof WC_Order)
            return $out;
        if (!class_exists('WC_Stripe_API'))
            return $out;

        // 1) Por payment_method (pm_xxx)
        $pm_id = $order->get_meta('_stripe_source_id', true);
        if ($pm_id && is_string($pm_id) && strpos($pm_id, 'pm_') === 0) {
            try {
                $endpoint = "payment_methods/{$pm_id}";
                $resp = WC_Stripe_API::request([], $endpoint, 'GET');
                $out['api_endpoint'] = $endpoint;
                $out['raw'] = $resp;

                $type = mt_get_nested($resp, 'type');
                $brand = mt_get_nested($resp, 'card', 'brand');
                $last4 = mt_get_nested($resp, 'card', 'last4');

                if ($type === 'card' && ($brand || $last4)) {
                    $out['brand_raw'] = (string) $brand;
                    $out['brand'] = mt_canon_brand_key($brand);
                    $out['last4'] = (string) $last4;
                    $out['api_ok'] = true;
                    return $out;
                }
            } catch (Exception $e) {
            }
        }

        // 2) Por payment_intent expand latest_charge
        $pi_id = $order->get_meta('_stripe_intent_id', true);
        if ($pi_id && is_string($pi_id) && strpos($pi_id, 'pi_') === 0) {
            try {
                $endpoint = "payment_intents/{$pi_id}?expand[]=latest_charge";
                $resp = WC_Stripe_API::request([], $endpoint, 'GET');
                $out['api_endpoint'] = $endpoint;
                $out['raw'] = $resp;

                $pm_type = mt_get_nested($resp, 'latest_charge', 'payment_method_details', 'type');
                $brand = mt_get_nested($resp, 'latest_charge', 'payment_method_details', 'card', 'brand');
                $last4 = mt_get_nested($resp, 'latest_charge', 'payment_method_details', 'card', 'last4');

                if ($pm_type === 'card' && ($brand || $last4)) {
                    $out['brand_raw'] = (string) $brand;
                    $out['brand'] = mt_canon_brand_key($brand);
                    $out['last4'] = (string) $last4;
                    $out['api_ok'] = true;
                    return $out;
                }

                // Fallback extra: algunos devuelven bajo charges->data[0]
                $brand2 = mt_get_nested($resp, 'charges', 'data', 0, 'payment_method_details', 'card', 'brand');
                $last42 = mt_get_nested($resp, 'charges', 'data', 0, 'payment_method_details', 'card', 'last4');
                if ($brand2 || $last42) {
                    $out['brand_raw'] = (string) $brand2;
                    $out['brand'] = mt_canon_brand_key($brand2);
                    $out['last4'] = (string) $last42;
                    $out['api_ok'] = true;
                    return $out;
                }
            } catch (Exception $e) {
            }
        }

        return $out;
    }
}

/**
 * Orquestador: tokens → metas → API → notas → título
 * Devuelve ['brand','brand_raw','last4','source','api'].
 */
if (!function_exists('mt_get_order_card_brand_last4')) {
    function mt_get_order_card_brand_last4($order)
    {
        $out = ['brand' => '', 'brand_raw' => '', 'last4' => '', 'source' => '', 'api' => null];
        if (!$order instanceof WC_Order)
            return $out;

        // 1) Tokens
        $tokens = $order->get_payment_tokens();
        if (!empty($tokens)) {
            foreach ($tokens as $tid) {
                $tok = WC_Payment_Tokens::get($tid);
                if (!$tok)
                    continue;

                $last4 = method_exists($tok, 'get_last4') ? (string) $tok->get_last4() : '';
                $brand_raw = '';
                foreach (['get_brand', 'get_card_type', 'get_type'] as $m) {
                    if (method_exists($tok, $m) && $tok->$m()) {
                        $brand_raw = (string) $tok->$m();
                        break;
                    }
                }
                if ($brand_raw || $last4) {
                    $out['brand_raw'] = $brand_raw;
                    $out['brand'] = mt_canon_brand_key($brand_raw);
                    $out['last4'] = $last4;
                    $out['source'] = 'token';
                    return $out;
                }
            }
        }

        // 2) Metas UPE / comunes
        $meta_last4_keys = ['_stripe_upe_last4', '_stripe_last4', '_stripe_card_last4', 'card_last4', 'last4', 'wc_payment_token_last4'];
        $meta_brand_keys = ['_stripe_upe_card_brand', '_stripe_card_brand', 'stripe_brand', 'card_brand', 'brand', '_payment_method_card_type', 'cc_type', 'card_type', 'payment_card_brand'];

        foreach ($meta_last4_keys as $k) {
            $v = $order->get_meta($k, true);
            if ($v) {
                $out['last4'] = (string) $v;
                $out['source'] = 'meta';
                break;
            }
        }
        foreach ($meta_brand_keys as $k) {
            $v = $order->get_meta($k, true);
            if ($v) {
                $out['brand_raw'] = (string) $v;
                $out['brand'] = mt_canon_brand_key($v);
                $out['source'] = $out['source'] ?: 'meta';
                break;
            }
        }
        if ($out['brand'] || $out['last4'])
            return $out;

        // 3) API Stripe
        $api = mt_try_fetch_brand_last4_via_stripe_api($order);
        $out['api'] = $api;
        if ($api['api_ok'] && ($api['brand'] || $api['last4'])) {
            $out['brand_raw'] = $api['brand_raw'];
            $out['brand'] = $api['brand'];
            $out['last4'] = $api['last4'];
            $out['source'] = 'api';

            // Persistir para próximos loads
            if ($api['brand'] && !$order->get_meta('_stripe_upe_card_brand', true)) {
                $order->update_meta_data('_stripe_upe_card_brand', $api['brand']);
            }
            if ($api['last4'] && !$order->get_meta('_stripe_upe_last4', true)) {
                $order->update_meta_data('_stripe_upe_last4', $api['last4']);
            }
            $order->save();

            return $out;
        }

        // 4) Notas
        $notes = wc_get_order_notes(['order_id' => $order->get_id()]);
        if (!empty($notes)) {
            foreach ($notes as $note) {
                $content = wp_strip_all_tags($note->content, true);
                if (preg_match('/(visa|mastercard|american express|amex|discover)/i', $content, $m)) {
                    $out['brand_raw'] = $m[1];
                    $out['brand'] = mt_canon_brand_key($m[1]);
                }
                if (preg_match('/(?:\*{2,}|•{2,}|x{2,}|ending in\s*)(\d{4})/i', $content, $m2)) {
                    $out['last4'] = $m2[1];
                } elseif (preg_match('/\b(\d{4})\b(?!.*\d)/', $content, $m3)) {
                    $out['last4'] = $m3[1];
                }
                if ($out['brand'] || $out['last4']) {
                    $out['source'] = 'note';
                    break;
                }
            }
        }
        if ($out['brand'] || $out['last4'])
            return $out;

        // 5) Título del método
        $pm_title = (string) $order->get_payment_method_title();
        if (stripos($pm_title, 'visa') !== false)
            $out['brand'] = 'visa';
        elseif (stripos($pm_title, 'master') !== false)
            $out['brand'] = 'mastercard';
        elseif (stripos($pm_title, 'amex') !== false || stripos($pm_title, 'american express') !== false)
            $out['brand'] = 'amex';
        elseif (stripos($pm_title, 'discover') !== false)
            $out['brand'] = 'discover';
        $out['brand_raw'] = $pm_title ?: $out['brand'];
        $out['source'] = 'title';

        return $out;
    }
}
?>

<?php
if ( isset($order) && $order && !$order->has_status('failed')):

    // Datos básicos para modal
    $order_number = $order->get_order_number();
    $order_email = $order->get_billing_email();

    // Producto principal (primer ítem no fee)
    $main_item_name = '';
    $main_item_price = 0;
    $main_item_price_suffix = '/ One Time'; // fallback
    foreach ($order->get_items() as $it) {
        $prod = is_callable([$it, 'get_product']) ? $it->get_product() : null;

        // Obtener size_slug del atributo pa_account-size
        $size_slug = '';
        if ($prod) {
            $size_attr = $prod->get_attribute('pa_account-size');
            if (!empty($size_attr)) {
                $t = get_term_by('name', $size_attr, 'pa_account-size');
                if ($t && isset($t->slug)) {
                    $size_slug = $t->slug;
                }
            }
        }

        // Concatenar como: "<size_slug> <product_name>"
        $main_item_name = trim(($size_slug ? $size_slug . ' ' : '') . $it->get_name());
        $main_item_price = (float) $it->get_total();
        break;
    }

    // Sufijo de precio según suscripción relacionada
    if (function_exists('wcs_get_subscriptions_for_order')) {
        $subs = wcs_get_subscriptions_for_order($order->get_id(), ['order_type' => 'any']);
        if (!empty($subs)) {
            $sub = array_shift($subs);
            if ($sub && is_a($sub, 'WC_Subscription')) {
                $main_item_price_suffix = '/ ' . ucfirst($sub->get_billing_period()); // '/ Month', '/ Year', etc.
            }
        }
    }


    // Add-ons (fees)
    $addons_names = array();
    $addons_total = 0.0;
    $addon_configs_lookup = array(
        'drawdown_buffer' => 'Drawdown buffer',
        'anytime_payouts' => 'Anytime Payouts',
    );
    foreach ($order->get_items('fee') as $fee) {
        $val = $fee->get_meta('_wc_checkout_add_on_value');
        $addons_total += (float) $fee->get_total();
        if (is_array($val)) {
            foreach ($val as $slug) {
                $key = str_replace('-', '_', $slug);
                $addons_names[] = $addon_configs_lookup[$key] ?? $fee->get_name();
            }
        } else {
            $addons_names[] = $fee->get_name();
        }
    }
    $addons_names = array_values(array_unique(array_filter($addons_names)));
    $total_paid = wc_price($order->get_total());

    // Método de pago + icono
    $pm_id = $order->get_payment_method();
    $pm_title = $order->get_payment_method_title();

    $card_info = mt_get_order_card_brand_last4($order);
    $brand = $card_info['brand'];      // visa/mastercard/amex/discover o ''
    $brand_raw = $card_info['brand_raw'];
    $last4 = $card_info['last4'];
    $source = $card_info['source'];
    $api_dbg = isset($card_info['api']) ? $card_info['api'] : null;

    $pm_suffix = $last4 ? sprintf('Ending in %s', esc_html($last4)) : esc_html($pm_title);

    $base = trailingslashit(home_url('/wp-content/'));

    $icons_map = [
        'mastercard' => $base . 'uploads/2025/07/mastercard.svg',
        'visa' => $base . 'uploads/2025/07/visa.svg',
        'discover' => $base . 'uploads/2025/07/discover.svg',
        'amex' => $base . 'uploads/2025/07/amex.svg',
    ];
    $card_icon = ($brand && isset($icons_map[$brand])) ? $icons_map[$brand] : '';

?>

<div class="container">
    <div class="mt-page">
        <div class="mt-page__sidebar">
            <?php render_sidebar(); ?>
        </div>
        <div class="mt-page__main">
            <?php render_step_selector(3); ?>
            <div class="mt-thankyou d-flex flex-column align-items-center gap-32">    
                <div class="d-flex flex-column align-items-center gap-3">
                    <!-- Image -->
                    <div class="text-center w-100">
                        <img fetchpriority="high" decoding="async" class="d-none d-sm-inline-block"
                            src="<?php echo esc_url(home_url('/wp-content/themes/megatrader-addons/assets/img/thank-you.png')); ?>"
                            alt="thank you" width="690" height="132">
                        <img decoding="async" class="d-inline-block d-sm-none"
                            src="<?php echo esc_url(home_url('/wp-content/themes/megatrader-addons/assets/img/thank-you2.png')); ?>"
                            alt="thank you" width="327" height="132">
                    </div>

                    <!-- Titles -->
                    <div class="d-flex flex-column align-items-center gap-2">
                        <div class="text-white text-uppercase fw-medium leading-10 text-32px">
                            <?php echo esc_html(Label::THANKYOU_META['order_successful']); ?>
                        </div>
                        <div class="text-A8A29E text-center">
                            <?php echo esc_html(Label::THANKYOU_META['trading_challenge_ready']); ?>
                        </div>
                    </div>

                    <!-- Order Number + Copy -->
                    <div id="order-copy-chip test-pp"
                        class="px-3 py-2 bg-1e1e1e outline-dark rounded-2 d-inline-flex align-items-center gap-2 order-chip"
                        data-order="<?php echo esc_attr($order_number); ?>" role="button" tabindex="0"
                        aria-label="Copy order number">
                        <div class="text-white fw-bold order-chip-label">
                            <?php echo esc_html(Label::THANKYOU_META['order']); ?>:
                        </div>
                        <div class="fw-bold order-chip-value text-a8a29e">
                            <?php echo esc_html($order_number); ?>
                        </div>
                        <div class="mt-icon mt-icon-white mt-icon_content-copy"></div>
                    </div>
                </div>
                <!-- Summary -->
                <div class="w-100 w-max-600px">
                    <div class="p-3 rounded-3 d-flex flex-column gap-3">
                        <div class="text-white fw-bold text-base">
                            <?php echo esc_html(Label::THANKYOU_META['order_summary']); ?>
                        </div>

                        <!-- Producto principal -->
                        <?php if ($main_item_name): ?>
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="fw-light text-base text-white"><?php echo esc_html($main_item_name); ?></div>
                                <div class="fw-medium text-base text-primary">
                                    <?php echo wp_kses_post(wc_price($main_item_price)); ?>
                                    <?php echo esc_html($main_item_price_suffix); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="h-1px border-top-gray"></div>

                        <!-- Add-ons -->
                        <?php if ($addons_total > 0): ?>
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="text-white text-base fw-bold">
                                    <?php echo esc_html(Label::THANKYOU_META['addons']); ?>
                                </div>
                                <div class="fw-medium text-primary text-base">
                                    <?php echo wp_kses_post(wc_price($addons_total)); ?>
                                </div>
                            </div>

                            <?php foreach ($addons_names as $an): ?>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="text-white text-base fw-light"><?php echo esc_html($an); ?></div>
                                </div>
                            <?php endforeach; ?>

                            <div class="h-1px border-top-gray"></div>
                        <?php endif; ?>

                        <!-- Total + método de pago -->
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="text-white text-base fw-medium">
                                <?php echo esc_html(Label::THANKYOU_META['total_paid']); ?>
                            </div>
                            <div class="text-primary text-base fw-medium">
                                <?php echo wp_kses_post($total_paid); ?>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between">
                            <div class="text-white fw-medium text-base">
                                <?php echo esc_html(Label::THANKYOU_META['payment_method']); ?>
                            </div>
                            <div class="d-flex align-items-center gap-2"
                                data-debug-brand="<?php echo esc_attr($brand_raw); ?>"
                                data-debug-source="<?php echo esc_attr($source); ?>">
                                <div class="w-36px h-36px overflow-hidden">
                                    <?php if ($card_icon): ?>
                                        <img src="<?php echo esc_url($card_icon); ?>"
                                            alt="<?php echo esc_attr(strtoupper($brand)); ?>" width="36" height="36" />
                                    <?php else: ?>
                                        <span class="text-white text-base fw-medium text-uppercase">
                                            <?php echo esc_html($brand ?: 'CARD'); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-white text-base fw-medium"><?php echo esc_html($pm_suffix); ?></div>
                            </div>
                        </div>

                        <div class="p-3 rounded-2 d-flex align-items-start gap-3 mt-2 bg-1e1e1e">
                            <div class="flex-grow-1 text-2dd4bf">
                                <?php echo esc_html(Label::THANKYOU_META['confirmation_email_sent']); ?>
                            </div>
                        </div>

                        <a href="<?php echo esc_url(home_url('/my-account/overview/')); ?>"
                            class="js-goto-account ot-btn w-100 fw-medium text-black rounded-xl p-y-12-mega p-x-16-mega bg-mgt-primary text-center text-uppercase">
                            <?php echo esc_html(Label::THANKYOU_META['go_to_my_account']); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
