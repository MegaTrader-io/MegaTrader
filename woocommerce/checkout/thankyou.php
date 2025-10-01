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

<div class="woocommerce-order pb-30 pt-32">
    <div class="container">
        <div class="top-menu">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="actived">
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M12.1333 20.1334L21.5333 10.7334L19.6667 8.86669L12.1333 16.4L8.33332 12.6L6.46666 14.4667L12.1333 20.1334ZM14 27.3334C12.1555 27.3334 10.4222 26.9834 8.79999 26.2834C7.17777 25.5834 5.76666 24.6334 4.56666 23.4334C3.36666 22.2334 2.41666 20.8222 1.71666 19.2C1.01666 17.5778 0.666656 15.8445 0.666656 14C0.666656 12.1556 1.01666 10.4222 1.71666 8.80002C2.41666 7.1778 3.36666 5.76669 4.56666 4.56669C5.76666 3.36669 7.17777 2.41669 8.79999 1.71669C10.4222 1.01669 12.1555 0.666687 14 0.666687C15.8444 0.666687 17.5778 1.01669 19.2 1.71669C20.8222 2.41669 22.2333 3.36669 23.4333 4.56669C24.6333 5.76669 25.5833 7.1778 26.2833 8.80002C26.9833 10.4222 27.3333 12.1556 27.3333 14C27.3333 15.8445 26.9833 17.5778 26.2833 19.2C25.5833 20.8222 24.6333 22.2334 23.4333 23.4334C22.2333 24.6334 20.8222 25.5834 19.2 26.2834C17.5778 26.9834 15.8444 27.3334 14 27.3334Z"
                        fill="#FFB34A" />
                </svg>
            </a>
            <span class="actived">
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M12.1333 20.1334L21.5333 10.7334L19.6667 8.86669L12.1333 16.4L8.33332 12.6L6.46666 14.4667L12.1333 20.1334ZM14 27.3334C12.1555 27.3334 10.4222 26.9834 8.79999 26.2834C7.17777 25.5834 5.76666 24.6334 4.56666 23.4334C3.36666 22.2334 2.41666 20.8222 1.71666 19.2C1.01666 17.5778 0.666656 15.8445 0.666656 14C0.666656 12.1556 1.01666 10.4222 1.71666 8.80002C2.41666 7.1778 3.36666 5.76669 4.56666 4.56669C5.76666 3.36669 7.17777 2.41669 8.79999 1.71669C10.4222 1.01669 12.1555 0.666687 14 0.666687C15.8444 0.666687 17.5778 1.01669 19.2 1.71669C20.8222 2.41669 22.2333 3.36669 23.4333 4.56669C24.6333 5.76669 25.5833 7.1778 26.2833 8.80002C26.9833 10.4222 27.3333 12.1556 27.3333 14C27.3333 15.8445 26.9833 17.5778 26.2833 19.2C25.5833 20.8222 24.6333 22.2334 23.4333 23.4334C22.2333 24.6334 20.8222 25.5834 19.2 26.2834C17.5778 26.9834 15.8444 27.3334 14 27.3334Z"
                        fill="#FFB34A" />
                </svg>
            </span>
            <span class="active">3</span>
        </div>

        <?php if (isset($order) && $order): ?>

            <?php do_action('woocommerce_before_thankyou', $order->get_id()); ?>

            <?php if ($order->has_status('failed')): ?>

                <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed">
                    <?php esc_html_e('Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce'); ?>
                </p>

                <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
                    <a href="<?php echo esc_url($order->get_checkout_payment_url()); ?>"
                        class="button pay"><?php esc_html_e('Pay', 'woocommerce'); ?></a>
                    <?php if (is_user_logged_in()): ?>
                        <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>"
                            class="button pay"><?php esc_html_e('My account', 'woocommerce'); ?></a>
                    <?php endif; ?>
                </p>

            <?php else: ?>

                <div class="thankyou-info">
                    <div class="text-center mb-15">
                        <img fetchpriority="high" decoding="async" class="d-none d-sm-inline-block"
                            src="/wp-content/themes/megatrader-addons/assets/img/thank-you.png" alt="thank you" width="690"
                            height="132">
                        <img decoding="async" class="d-inline-block d-sm-none"
                            src="/wp-content/themes/megatrader-addons/assets/img/thank-you2.png" alt="thank you" width="327"
                            height="132">
                    </div>
                    <h2 class="mb-3">Amazing !</h2>
                    <h3 class="box-title">Congratulations! You've got covered</h3>
                    <p class="thanks-text">Your trading account have been created successfully</p>
                    <p class="order-number">Order Number : <?php echo esc_html($order->get_order_number()); ?></p>
                    <p class="thanks-text order-email"><?php echo esc_html($order->get_billing_email()); ?></p>
                </div>

                <?php
                // ===== Tu render actual de productos/plan/plataforma/tamaño/add-ons (igual que antes) =====
                if ($order) {
                    foreach ($order->get_items() as $item_id => $item) {
                        $product = $item->get_product();
                        if ($product) {

                            $product_image = wp_get_attachment_image_url($product->get_image_id(), 'medium');
                            $product_title = $product->get_name();
                            $product_short_description = $product->get_short_description();

                            $plan_attr = $product->get_attribute('pa_account-types');
                            if (!empty($plan_attr)) {
                                $plan_term = get_term_by('name', $plan_attr, 'pa_account-types');
                                if ($plan_term && isset($plan_term->term_id)) {
                                    $image_id = get_term_meta($plan_term->term_id, 'attribute_image_id', true);
                                    $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
                                }
                            }

                            $size_attr = $product->get_attribute('pa_account-size');
                            if (!empty($size_attr)) {
                                $size_term = get_term_by('name', $size_attr, 'pa_account-size');
                                if ($size_term && isset($size_term->term_id)) {
                                    $size_attr_slug = $size_term->slug;
                                    $size_description = $size_term->description;
                                }
                            }

                            $platform_attr = $product->get_attribute('pa_platform');
                            if (!empty($platform_attr)) {
                                $platform_term = get_term_by('name', $platform_attr, 'pa_platform');
                                if ($platform_term && isset($platform_term->term_id)) {
                                    $platform_image_id = get_term_meta($platform_term->term_id, 'attribute_image_id', true);
                                    $platform_image_url = $platform_image_id ? wp_get_attachment_url($platform_image_id) : '';
                                    $platform_description = $platform_term->description;
                                }
                            }

                            $terms = get_the_terms($product->get_id(), 'product_cat');
                            $is_activation_fee = false;
                            $is_reset_fee = false;
                            if ($terms && !is_wp_error($terms)) {
                                foreach ($terms as $term) {
                                    if ($term->slug === 'activation-fee') {
                                        $term_description = $term->description;
                                        $is_activation_fee = true;
                                        break;
                                    }
                                    if ($term->slug === 'reset-fee') {
                                        $term_description = $term->description;
                                        $is_reset_fee = true;
                                        break;
                                    }
                                }
                            }

                            $tags = get_the_terms($product->get_id(), 'product_tag');
                            $tag = !empty($tags) ? $tags[0] : null;
                            if ($tag) {
                                $tagName = str_replace('$', '', $tag->name);
                            }

                            if ($is_activation_fee) { ?>
                                <div class="order-product-wrap">
                                    <div class="order-plan">
                                        <div class="box-icon"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/diamond.svg"
                                                alt="Icon" width="36" height="36"></div>
                                        <h4 class="box-title"><?php echo esc_html($product_title . ' - Activation Fee'); ?></h4>
                                    </div>
                                    <div class="order-product">
                                        <span class="icon">
                                            <!-- icono inline -->
                                            <svg width="58" height="57" viewBox="0 0 58 57" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M0.5 28.5C0.5 12.7599 13.2599 0 29 0C44.7401 0 57.5 12.7599 57.5 28.5C57.5 44.2401 44.7401 57 29 57C13.2599 57 0.5 44.2401 0.5 28.5Z"
                                                    fill="#F1A035" />
                                                <mask id="mask0_11289_3680" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="14" y="13"
                                                    width="31" height="31">
                                                    <rect x="14.5" y="13.5" width="30" height="30" fill="#D9D9D9" />
                                                </mask>
                                                <g mask="url(#mask0_11289_3680)">
                                                    <path
                                                        d="M29.5 41C27.7708 41 26.1458 40.6719 24.625 40.0156C23.1042 39.3594 21.7812 38.4688 20.6562 37.3438C19.5312 36.2188 18.6406 34.8958 17.9844 33.375C17.3281 31.8542 17 30.2292 17 28.5C17 26.75 17.3281 25.1198 17.9844 23.6094C18.6406 22.099 19.5312 20.7812 20.6562 19.6562L22.4063 21.4063C21.4896 22.3229 20.776 23.3854 20.2656 24.5938C19.7552 25.8021 19.5 27.1042 19.5 28.5C19.5 31.2917 20.4688 33.6562 22.4063 35.5938C24.3438 37.5312 26.7083 38.5 29.5 38.5C32.2917 38.5 34.6562 37.5312 36.5938 35.5938C38.5312 33.6562 39.5 31.2917 39.5 28.5C39.5 27.1042 39.2448 25.8021 38.7344 24.5938C38.224 23.3854 37.5104 22.3229 36.5938 21.4063L38.3438 19.6562C39.4688 20.7812 40.3594 22.099 41.0156 23.6094C41.6719 25.1198 42 26.75 42 28.5C42 30.2292 41.6719 31.8542 41.0156 33.375C40.3594 34.8958 39.4688 36.2188 38.3438 37.3438C37.2188 38.4688 35.8958 39.3594 34.375 40.0156C32.8542 40.6719 31.2292 41 29.5 41ZM28.25 29.75V16H30.75V29.75H28.25Z"
                                                        fill="black" />
                                                </g>
                                            </svg>
                                        </span>
                                        <div class="box-content">
                                            <h4 class="box-title">Activation Fee
                                                <b><?php echo wp_kses_post(wc_price($product->get_price())); ?> / One Time</b>
                                            </h4>
                                            <p class="box-text"><?php echo esc_html($term_description); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php } elseif ($is_reset_fee) { ?>
                                <div class="order-product-wrap">
                                    <div class="order-plan">
                                        <div class="box-icon"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/diamond.svg"
                                                alt="Icon" width="36" height="36"></div>
                                        <h4 class="box-title"><?php echo esc_html($product_title . ' - Reset Fee'); ?></h4>
                                    </div>
                                    <div class="order-product">
                                        <span class="icon">
                                            <!-- icono inline -->
                                            <svg width="58" height="57" viewBox="0 0 58 57" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M0.5 28.5C0.5 12.7599 13.2599 0 29 0C44.7401 0 57.5 12.7599 57.5 28.5C57.5 44.2401 44.7401 57 29 57C13.2599 57 0.5 44.2401 0.5 28.5Z"
                                                    fill="#F1A035" />
                                                <mask id="mask0_11289_3949" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="14" y="13"
                                                    width="31" height="31">
                                                    <rect x="14.5" y="13.5" width="30" height="30" fill="#D9D9D9" />
                                                </mask>
                                                <g mask="url(#mask0_11289_3949)">
                                                    <path
                                                        d="M29.5 38.5C26.7083 38.5 24.3438 37.5313 22.4063 35.5938C20.4687 33.6563 19.5 31.2917 19.5 28.5C19.5 25.7083 20.4687 23.3438 22.4063 21.4063C24.3438 19.4687 26.7083 18.5 29.5 18.5C30.9375 18.5 32.3125 18.7969 33.625 19.3906C34.9375 19.9844 36.0625 20.8333 37 21.9375V18.5H39.5V27.25H30.75V24.75H36C35.3333 23.5833 34.4219 22.6667 33.2656 22C32.1094 21.3333 30.8542 21 29.5 21C27.4167 21 25.6458 21.7292 24.1875 23.1875C22.7292 24.6458 22 26.4167 22 28.5C22 30.5833 22.7292 32.3542 24.1875 33.8125C25.6458 35.2708 27.4167 36 29.5 36C31.1042 36 32.5521 35.5417 33.8438 34.625C35.1354 33.7083 36.0417 32.5 36.5625 31H39.1875C38.6042 33.2083 37.4167 35.0104 35.625 36.4062C33.8333 37.8021 31.7917 38.5 29.5 38.5Z"
                                                        fill="black" />
                                                </g>
                                            </svg>
                                        </span>
                                        <div class="box-content">
                                            <h4 class="box-title">Reset Fee <b><?php echo wp_kses_post(wc_price($product->get_price())); ?>
                                                    / One Time</b></h4>
                                            <p class="box-text"><?php echo esc_html($term_description); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="order-product-wrap">
                                    <?php if ($plan_attr): ?>
                                        <div class="order-plan">
                                            <div class="box-icon">
                                                <?php if (!empty($image_url)): ?>
                                                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($plan_attr); ?>" width="36"
                                                        height="36">
                                                <?php endif; ?>
                                            </div>
                                            <h4 class="box-title"><?php echo esc_html($plan_attr); ?></h4>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($platform_attr): ?>
                                        <div class="order-product">
                                            <?php if (!empty($platform_image_url)): ?>
                                                <div class="box-img">
                                                    <img src="<?php echo esc_url($platform_image_url); ?>" alt="<?php echo esc_attr($platform_attr); ?>"
                                                        width="57" height="57">
                                                </div>
                                            <?php endif; ?>
                                            <div class="box-content">
                                                <h4 class="box-title"><?php echo esc_html($platform_attr); ?></h4>
                                                <p class="box-text"><?php echo esc_html($platform_description); ?></p>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($size_attr): ?>
                                        <div class="order-product">
                                            <span class="icon"><?php echo esc_html($size_attr_slug); ?></span>
                                            <div class="box-content">
                                                <h4 class="box-title">
                                                    <?php echo esc_html($size_attr_slug . ' Account'); ?>
                                                    <b>
                                                        <?php echo wp_kses_post(wc_price($product->get_price())); ?>
                                                        <?php
                                                        $__ty_billing_suffix = '/ One Time';
                                                        if (function_exists('wcs_get_subscriptions_for_order')) {
                                                            $__ty_subs = wcs_get_subscriptions_for_order($order->get_id(), ['order_type' => 'any']);
                                                            if (!empty($__ty_subs)) {
                                                                $__ty_sub = array_shift($__ty_subs);
                                                                if ($__ty_sub && is_a($__ty_sub, 'WC_Subscription')) {
                                                                    $__ty_billing_suffix = '/ ' . ucfirst($__ty_sub->get_billing_period());
                                                                }
                                                            }
                                                        }
                                                        echo $__ty_billing_suffix;
                                                        ?>
                                                    </b>
                                                </h4>
                                                <p class="box-text"><?php echo esc_html($size_description); ?></p>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php
                                    // Add-ons desde fees
                                    $order_id = get_query_var('order-received');
                                    if (!$order_id) {
                                        global $wp;
                                        $order_id = absint($wp->query_vars['order-received'] ?? 0);
                                    }
                                    $order_for_addons = wc_get_order($order_id);
                                    $order_add_ons = array();

                                    if ($order_for_addons) {
                                        foreach ($order_for_addons->get_items('fee') as $fee_id => $fee) {
                                            if (empty($fee->get_meta('_wc_checkout_add_on_id'))) {
                                                continue;
                                            }
                                            $add_on_id = $fee->get_meta('_wc_checkout_add_on_id');
                                            $add_on_value = $fee->get_meta('_wc_checkout_add_on_value');
                                            $add_on_label = $fee->get_meta('_wc_checkout_add_on_label');
                                            $order_add_ons[$add_on_id] = array(
                                                'name' => $fee->get_name(),
                                                'value' => $add_on_value,
                                                'label' => $add_on_label,
                                                'total' => $fee->get_total(),
                                                'fee_id' => $fee_id,
                                            );
                                        }
                                    }

                                    $addon_configs = array(
                                        'drawdown_buffer' => array(
                                            'name' => 'Drawdown buffer',
                                            'icon' => 'drawdown-icon.svg',
                                            'description' => 'Add $500 to your trailing drawdown and extend your cushion, giving you more flexibility while managing trades and risk.'
                                        ),
                                        'anytime_payouts' => array(
                                            'name' => 'Anytime Payouts',
                                            'icon' => 'payout-icon.svg',
                                            'description' => 'Waive the 10 days trading rule and get paid as soon as you hit your consistency and profit targets.'
                                        )
                                    );

                                    if (!empty($order_add_ons)) {
                                        foreach ($order_add_ons as $add_on_id => $add_on_data) {
                                            $add_on_values = $add_on_data['value'];
                                            if (is_array($add_on_values)) { ?>
                                                <div class="order-product d-flex flex-column gap-4">
                                                    <?php
                                                    $first = true;
                                                    foreach ($add_on_values as $value_slug) {
                                                        $config_key = str_replace('-', '_', $value_slug);
                                                        if (isset($addon_configs[$config_key])) {
                                                            $config = $addon_configs[$config_key];
                                                            $price = wc_price($add_on_data['total']);
                                                            ?>
                                                            <div class="order-product-wrapper d-flex gap-3 align-items-center">
                                                                <div class="box-img">
                                                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/<?php echo esc_attr($config['icon']); ?>"
                                                                        alt="<?php echo esc_attr($config['name']); ?> Icon" width="57" height="57">
                                                                </div>
                                                                <div class="box-content">
                                                                    <h4 class="box-title">
                                                                        <?php echo esc_html($config['name']); ?>
                                                                        <?php if ($first): ?><b><?php echo wp_kses_post($price); ?></b><?php endif; ?>
                                                                    </h4>
                                                                    <p class="box-text"><?php echo esc_html($config['description']); ?></p>
                                                                </div>
                                                            </div>
                                                            <?php
                                                            $first = false;
                                                        }
                                                    } ?>
                                                </div>
                                            <?php }
                                        }
                                    }
                                    ?>
                                </div>
                            <?php } ?>

                            <?php
                        }
                    }
                } else {
                    echo '<p>Order not found.</p>';
                }
                ?>

                <div class="order-process-wrap">
                    <h4 class="box-title">What's next</h4>
                    <div class="order-process">
                        <div class="box-content">
                            <h5 class="title">Step 1 :</h5>
                            <p class="text">You'll quickly get an email to set your MegaTrader password. Check spam if it
                                doesn't arrive.</p>
                        </div>
                        <div class="box-content">
                            <h5 class="title">Step 2 :</h5>
                            <p class="text">After setting your password, log in to access your dashboard and view platform
                                credentials.</p>
                        </div>
                        <div class="box-content">
                            <h5 class="title">Step 3 :</h5>
                            <p class="text">Use the credentials to access the platform and start placing trades toward your
                                payout.</p>
                        </div>
                    </div>
                </div>

                <div class="d-none">
                    <?php do_action('woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id()); ?>
                </div>

            <?php endif; ?>
        <?php else: ?>

            <p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received">
                <?php echo apply_filters('woocommerce_thankyou_order_received_text', __('Thank you. Your order has been received.', 'woocommerce'), null); ?>
            </p>

        <?php endif; ?>
    </div>
</div>

<?php
// =====================
//  MODAL (Bootstrap)
// =====================
if (isset($order) && $order && !$order->has_status('failed')):

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
    <!-- Modal -->
    <div class="modal fade" id="orderSuccessModal" tabindex="-1" aria-labelledby="orderSuccessLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen-md-down" style="--bs-modal-width: 600px;">
            <div class="modal-content align-items-center d-flex flex-column gap-4">

                <!-- Header -->
                <div class="modal-header w-100 border-0 justify-content-between align-items-start p-0">
                    <h5 class="modal-title text-white heading-sm-medium text-uppercase" id="orderSuccessLabel">
                        <?php echo esc_html(Label::THANKYOU_META['success']); ?>
                    </h5>
                    <button type="button" class="p-0 border-0 bg-transparent shadow-none" data-bs-dismiss="modal"
                        aria-label="Close">
                        <img src="wp-content/uploads/2025/05/cancel-circle-1.png'); ?>" alt="Close"
                            style="width:24px;height:24px;" />
                    </button>
                </div>

                <div class="modal-body d-flex flex-column align-items-center justify-content-center gap-3 w-100">

                    <!-- Imágenes -->
                    <div class="text-center w-100">
                        <img fetchpriority="high" decoding="async" class="d-none d-sm-inline-block"
                            src="<?php echo esc_url(home_url('/wp-content/themes/megatrader-addons/assets/img/thank-you.png')); ?>"
                            alt="thank you" width="690" height="132">
                        <img decoding="async" class="d-inline-block d-sm-none"
                            src="<?php echo esc_url(home_url('/wp-content/themes/megatrader-addons/assets/img/thank-you2.png')); ?>"
                            alt="thank you" width="327" height="132">
                    </div>

                    <!-- Títulos -->
                    <div class="d-flex flex-column align-items-center gap-2">
                        <div class="text-white text-uppercase fw-medium leading-10 text-32px">
                            <?php echo esc_html(Label::THANKYOU_META['order_successful']); ?>
                        </div>
                        <div class="text-A8A29E text-center">
                            <?php echo esc_html(Label::THANKYOU_META['trading_challenge_ready']); ?>
                        </div>
                    </div>

                    <!-- Chip de Order -->
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



                    <!-- Resumen -->
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

                    </div><!-- /max-width wrapper -->

                </div><!-- /.modal-body -->
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const DEST = '/my-account/overview/';
            const modalEl = document.getElementById('orderSuccessModal');
            if (!modalEl) return;

            const dialogEl = modalEl.querySelector('.modal-dialog');
            const contentEl = modalEl.querySelector('.modal-content');
            const btnGo = document.querySelector('.js-goto-account');

            let didRedirect = false;
            let wasEverVisible = false;
            let tick = null;

            const go = () => {
                if (didRedirect) return;
                didRedirect = true;
                try { obsModal.disconnect(); } catch (e) { }
                try { obsBody.disconnect(); } catch (e) { }
                if (tick) { clearInterval(tick); tick = null; }
                window.location.assign(DEST);
            };

            // ---------- utilidades de visibilidad/on-screen ----------
            const cs = (el) => (el ? window.getComputedStyle(el) : null);

            const hasZeroScale = (el) => {
                const st = cs(el);
                if (!st) return false;
                const t = st.transform;
                if (!t || t === 'none') return false;
                const m2d = t.match(/matrix\(([-0-9.,\s]+)\)/);
                if (m2d) {
                    const v = m2d[1].split(',').map(x => parseFloat(x.trim()));
                    if (v.length >= 4 && (v[0] === 0 || v[3] === 0)) return true;
                }
                if (/scale\(\s*0/.test(t)) return true;
                return false;
            };

            const isHiddenBasic = (el) => {
                if (!el) return true;
                const s = cs(el);
                if (!s) return true;
                if (s.display === 'none' || s.visibility === 'hidden' || Number(s.opacity) === 0) return true;
                if (hasZeroScale(el)) return true;
                const rect = el.getBoundingClientRect();
                if (rect.width === 0 || rect.height === 0) return true;
                const vw = window.innerWidth || document.documentElement.clientWidth;
                const vh = window.innerHeight || document.documentElement.clientHeight;
                if (rect.bottom < 0 || rect.top > vh || rect.right < 0 || rect.left > vw) return true;
                return false;
            };

            const isModalTrulyVisible = () => {
                const rootShown = modalEl.classList.contains('show');
                return rootShown && !isHiddenBasic(modalEl) && !isHiddenBasic(dialogEl) && !isHiddenBasic(contentEl);
            };

            const maybeRedirect = () => {
                if (!wasEverVisible || didRedirect) return;
                if (!document.body.contains(modalEl)) return go();
                if (!isModalTrulyVisible()) return go();
            };

            // ---------- Inicializar y mostrar modal ----------
            try {
                if (window.bootstrap && typeof bootstrap.Modal === 'function') {
                    const instance = bootstrap.Modal.getOrCreateInstance(modalEl, {
                        backdrop: 'static',
                        keyboard: false
                    });

                    modalEl.addEventListener('shown.bs.modal', function () {
                        wasEverVisible = true;
                    }, { once: true });

                    modalEl.addEventListener('hidden.bs.modal', function () {
                        if (wasEverVisible) go();
                    });

                    modalEl.addEventListener('hidePrevented.bs.modal', function () {
                        if (wasEverVisible) go();
                    });

                    instance.show();
                } else {
                    // Fallback sin Bootstrap
                    setTimeout(function () {
                        modalEl.classList.add('show');
                        modalEl.style.display = 'block';
                        modalEl.removeAttribute('aria-hidden');
                        wasEverVisible = true;
                    }, 0);
                }
            } catch (e) { }

            // ---------- Botón "Go to my account" ----------
            if (btnGo) {
                btnGo.addEventListener('click', function (ev) {
                    ev.preventDefault();
                    go();
                });
            }

            // ---------- Click fuera (por si el backdrop se vuelve "libre") ----------
            document.addEventListener('mousedown', function (ev) {
                if (!wasEverVisible || didRedirect) return;
                if (!dialogEl) return;
                const path = ev.composedPath ? ev.composedPath() : [];
                const inside = dialogEl.contains(ev.target) || path.includes(dialogEl);
                const anyBackdrop = !!document.querySelector('.modal-backdrop');
                if (!inside && anyBackdrop) go();
            }, true);

            // ---------- ESC (si alguien lo re-habilita) ----------
            document.addEventListener('keydown', function (ev) {
                if (!wasEverVisible || didRedirect) return;
                if (ev.key === 'Escape' || ev.key === 'Esc') go();
            }, true);

            // ---------- Observadores ----------
            const obsModal = new MutationObserver(function () {
                if (!wasEverVisible || didRedirect) return;
                maybeRedirect();
            });
            obsModal.observe(modalEl, {
                attributes: true,
                attributeFilter: ['class', 'style', 'aria-hidden'],
                subtree: true // << clave para detectar cambios en .modal-dialog/.modal-content
            });

            const obsBody = new MutationObserver(function () {
                if (!wasEverVisible || didRedirect) return;
                if (!document.getElementById('orderSuccessModal')) return go();
                maybeRedirect();
            });
            obsBody.observe(document.body, { childList: true, subtree: true });

            window.addEventListener('resize', maybeRedirect, { passive: true });
            window.addEventListener('scroll', maybeRedirect, { passive: true });
            tick = setInterval(maybeRedirect, 500); // red de seguridad

            // ---------- Copiar número de orden ----------
            const chip = document.getElementById('order-copy-chip') || document.querySelector('.order-chip');
            if (chip) {
                const label = chip.querySelector('.order-chip-label');

                const findValue = () => {
                    if (chip.dataset.order) return chip.dataset.order.trim();
                    const node = chip.querySelector('[data-order-value], .order-chip-value');
                    if (node && node.textContent) return node.textContent.trim();
                    // fallback: texto interno con alfanuméricos
                    const walker = document.createTreeWalker(chip, NodeFilter.SHOW_TEXT, null);
                    let txt, best = '';
                    while ((txt = walker.nextNode())) {
                        const t = txt.nodeValue.trim();
                        if (t && !/^order:?$/i.test(t) && /[A-Za-z0-9]/.test(t)) { best = t; break; }
                    }
                    return best;
                };

                function copyOrder() {
                    const value = findValue();
                    if (!value) return;

                    const afterCopy = () => {
                        if (!label) return;
                        const original = label.textContent;
                        label.textContent = 'Copied!';
                        chip.classList.add('copied');
                        setTimeout(() => {
                            label.textContent = original;
                            chip.classList.remove('copied');
                        }, 1500);
                    };

                    if (navigator.clipboard && window.isSecureContext) {
                        navigator.clipboard.writeText(value).then(afterCopy, afterCopy);
                    } else {
                        const ta = document.createElement('textarea');
                        ta.value = value;
                        ta.style.position = 'fixed';
                        ta.style.left = '-9999px';
                        document.body.appendChild(ta);
                        ta.focus(); ta.select();
                        try { document.execCommand('copy'); } catch (e) { }
                        document.body.removeChild(ta);
                        afterCopy();
                    }
                }

                chip.addEventListener('click', copyOrder);
                chip.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); copyOrder(); }
                });
            }
        });
    </script>





<?php endif; ?>