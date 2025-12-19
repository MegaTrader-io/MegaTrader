<?php
// inc/mt-checkout-helper.php

// === Util: leer constantes de Label con Reflection ===
if (!function_exists('mtch_get_label_const')) {
    function mtch_get_label_const(string $name)
    {
        if (!class_exists('Label'))
            return null;
        try {
            $ref = new ReflectionClass('Label');
            return $ref->hasConstant($name) ? $ref->getConstant($name) : null;
        } catch (Throwable $e) {
            return null;
        }
    }
}

// === Cart: nombre del primer producto (para detectar Reset/Activation) ===
if (!function_exists('mtch_get_cart_primary_product_name')) {
    function mtch_get_cart_primary_product_name(): ?string
    {
        if (!(function_exists('WC') && WC()->cart && !WC()->cart->is_empty()))
            return null;
        $first = reset(WC()->cart->get_cart());
        return ($first['data'] ?? null) instanceof WC_Product ? $first['data']->get_name() : null;
    }
}

// === Detecta tipo (reset / activation) de forma estricta ===
if (!function_exists('mtch_detect_product_kind_strict')) {
    function mtch_detect_product_kind_strict(?WC_Product $product): ?string
    {
        if (!($product instanceof WC_Product))
            return null;
        $pid = $product->get_id();
        $name = strtolower(trim((string) $product->get_name()));

        if ($name === 'reset fee')
            return 'reset';
        if ($name === 'activation fee')
            return 'activation';

        $in_reset = function_exists('has_term') ? (
            has_term(['reset-fee', 'reset'], 'product_cat', $pid) || has_term(['reset-fee', 'reset'], 'product_tag', $pid)
        ) : false;

        $in_activation = function_exists('has_term') ? (
            has_term(['activation-fee', 'activation'], 'product_cat', $pid) || has_term(['activation-fee', 'activation'], 'product_tag', $pid)
        ) : false;

        if ($in_reset)
            return 'reset';
        if ($in_activation)
            return 'activation';
        return null;
    }
}

// === Texto del badge (Reset/Activation cuando aplique; si no, plan_type) ===
if (!function_exists('mtch_prepare_badge_text')) {
    function mtch_prepare_badge_text(?WC_Product $product, string $plan_type): string
    {
        $cart_name = mtch_get_cart_primary_product_name();
        $prod_name = ($product instanceof WC_Product) ? $product->get_name() : null;
        $name = trim((string) ($cart_name ?: $prod_name));
        if (preg_match('/^(reset fee|activation fee)$/i', $name)) {
            return $name; // "Reset Fee" o "Activation Fee"
        }
        return $plan_type;
    }
}

// === Línea secundaria "size - X" (X = plan_type en especiales, o Buying Power) ===
if (!function_exists('mtch_prepare_secondary_line')) {
    function mtch_prepare_secondary_line(string $plan_size_slug, string $plan_type, ?WC_Product $product): string
    {
        $cart_name = mtch_get_cart_primary_product_name();
        $prod_name = ($product instanceof WC_Product) ? $product->get_name() : null;
        $name = trim((string) ($cart_name ?: $prod_name));
        $is_special = (bool) preg_match('/^(reset fee|activation fee)$/i', $name);

        $price_cfg = mtch_get_label_const('PRICE');
        $buying_power = (is_array($price_cfg) && isset($price_cfg['price_sufix'])) ? $price_cfg['price_sufix'] : 'Buying Power';

        return $plan_size_slug . ' - ' . ($is_special ? $plan_type : $buying_power);
    }
}

// === Prepara meta items + clase de grid (x4 solo si usamos META_RESET/META_ACTIVATION) ===
if (!function_exists('mtch_prepare_meta_items')) {
    function mtch_prepare_meta_items(?WC_Product $product, array $rich): array
    {
        $label_product_meta = mtch_get_label_const('PRODUCT_META') ?: [];
        $meta_order = mtch_get_label_const('META_ORDER');
        if (!is_array($meta_order) || empty($meta_order)) {
            $meta_order = array_keys($label_product_meta);
        }
        $icon_map = mtch_get_label_const('PRODUCT_META_ICONS') ?: [];

        $items = [];
        $used_highlights = false;
        $pid = ($product instanceof WC_Product) ? $product->get_id() : 0;
        $kind = mtch_detect_product_kind_strict($product);

        if ($pid && !empty($meta_order)) {
            foreach ($meta_order as $key) {
                if (!isset($label_product_meta[$key]))
                    continue;
                $val = $rich['meta'][$key] ?? get_post_meta($pid, $key, true);
                if ($val === '' || $val === null)
                    continue;
                $items[] = [
                    'key' => $key,
                    'label' => $label_product_meta[$key],
                    'value' => $val,
                    'icon_class' => $icon_map[$key] ?? 'mt-icon',
                ];
            }
        }

        // Fallback a destacados si no hay metas reales
        if (empty($items) && $kind === 'reset') {
            $cfg = mtch_get_label_const('META_RESET');
            if (is_array($cfg) && !empty($cfg)) {
                $items = array_map(fn($s) => [
                    'key' => $s['key'],
                    'label' => $s['label'],
                    'value' => $s['value'],
                    'icon_class' => $s['icon'],
                ], $cfg);
                $used_highlights = true;
            }
        } elseif (empty($items) && $kind === 'activation') {
            $cfg = mtch_get_label_const('META_ACTIVATION');
            if (is_array($cfg) && !empty($cfg)) {
                $items = array_map(fn($s) => [
                    'key' => $s['key'],
                    'label' => $s['label'],
                    'value' => $s['value'],
                    'icon_class' => $s['icon'],
                ], $cfg);
                $used_highlights = true;
            }
        }

        $grid_extra_class = $used_highlights ? ' mt-meta-grid--x4' : '';
        return [$items, $grid_extra_class];
    }
}

// === ¿Es Activation Fee? (para ocultar/limpiar addons) ===
if (!function_exists('mtch_is_activation_product')) {
    function mtch_is_activation_product(?WC_Product $product): bool
    {
        $cart_name = mtch_get_cart_primary_product_name();
        $prod_name = ($product instanceof WC_Product) ? $product->get_name() : null;
        $name = trim((string) ($cart_name ?: $prod_name));
        if (strcasecmp($name, 'Activation Fee') === 0)
            return true;
        return mtch_detect_product_kind_strict($product) === 'activation';
    }
}

// === Fuerza que los add-ons no queden seleccionados en Activation Fee ===
if (!function_exists('mtch_enforce_no_addons_on_activation')) {
    function mtch_enforce_no_addons_on_activation()
    {
        add_filter('woocommerce_checkout_posted_data', function ($data) {
            if (isset($data['add_ons']) && is_array($data['add_ons'])) {
                foreach ($data['add_ons'] as $k => $_) {
                    $data['add_ons'][$k] = '';
                }
            }
            return $data;
        }, 9999);
    }
}

// === Resolver el account_id para el checkout (POST silencioso -> GET -> fallback UI) ===

if (!function_exists('mtch_resolve_ids')) {
    function mtch_resolve_ids(): array
    {
        $acc = isset($_POST['account_id']) ? sanitize_text_field(wp_unslash($_POST['account_id'])) : '';
        if ($acc === '' && isset($_GET['account_id'])) {
            $acc = sanitize_text_field(wp_unslash($_GET['account_id']));
        }
        if ($acc === '' && function_exists('WC') && WC()->session) {
            $acc = (string) WC()->session->get('mt_account_id', '');
        }
        $main = isset($_POST['main_product_id']) ? sanitize_text_field(wp_unslash($_POST['main_product_id'])) : '';
        if ($main === '' && isset($_GET['main_product_id'])) {
            $main = sanitize_text_field(wp_unslash($_GET['main_product_id']));
        }
        return ['account_id' => (string) $acc, 'main_product_id' => (string) $main];
    }
}



// (opcional) compatibilidad con el nombre que ya usabas:
if (!function_exists('mtch_resolve_account_id')) {
    function mtch_resolve_account_id(): string
    {
        $ids = mtch_resolve_ids();
        return (string) ($ids['account_id'] ?? '');
    }
}


// === Debug visual opcional (?mtdebug=1 o WP_DEBUG) ===
if (!function_exists('mtch_render_debug_panel')) {
    function mtch_render_debug_panel(?WC_Product $product, array $rich, array $meta_info = [])
    {
        if (!((isset($_GET['mtdebug']) && $_GET['mtdebug'] !== '') || (defined('WP_DEBUG') && WP_DEBUG)))
            return;

        $pid = ($product instanceof WC_Product) ? $product->get_id() : 0;
        $cats = $pid ? wp_get_post_terms($pid, 'product_cat', ['fields' => 'names']) : [];
        $tags = $pid ? wp_get_post_terms($pid, 'product_tag', ['fields' => 'names']) : [];
        $raw_meta = $pid ? get_post_meta($pid) : [];
        $cart_snapshot = (function_exists('WC') && WC()->cart) ? WC()->cart->get_cart() : null;

        $product_basic = ($product instanceof WC_Product) ? [
            'id' => $pid,
            'type' => $product->get_type(),
            'name' => $product->get_name(),
            'sku' => $product->get_sku(),
            'price' => $product->get_price(),
            'regular_price' => $product->get_regular_price(),
            'sale_price' => $product->get_sale_price(),
            'stock_status' => $product->get_stock_status(),
            'manage_stock' => $product->get_manage_stock(),
            'stock_quantity' => $product->get_stock_quantity(),
            'variation_attrs' => $product->is_type('variation') ? $product->get_variation_attributes() : [],
        ] : null;

        echo '<div class="mt-debug-log" style="margin-top:24px;padding:16px;background:#111;border:1px solid #333;border-radius:8px;color:#FFD78A;">';
        echo '<div style="margin-bottom:8px;font-weight:600;">DEBUG: Producto</div>';

        echo '<details open><summary style="cursor:pointer;color:#fff;">Resumen</summary><pre style="white-space:pre-wrap;color:#A8A29E;">';
        print_r(['query_params' => $_GET, 'product_basic' => $product_basic, 'cats' => $cats, 'tags' => $tags]);
        echo '</pre></details>';

        echo '<details><summary style="cursor:pointer;color:#fff;">Rich payload</summary><pre style="white-space:pre-wrap;color:#A8A29E;">';
        print_r($rich);
        echo '</pre></details>';

        echo '<details><summary style="cursor:pointer;color:#fff;">Config (meta)</summary><pre style="white-space:pre-wrap;color:#A8A29E;">';
        print_r($meta_info);
        echo '</pre></details>';

        echo '<details><summary style="cursor:pointer;color:#fff;">Post Meta (raw)</summary><pre style="white-space:pre-wrap;color:#A8A29E;max-height:360px;overflow:auto;">';
        print_r($raw_meta);
        echo '</pre></details>';

        echo '<details><summary style="cursor:pointer;color:#fff;">Carrito (snapshot)</summary><pre style="white-space:pre-wrap;color:#A8A29E;">';
        if (is_array($cart_snapshot)) {
            $simple = [];
            foreach ($cart_snapshot as $k => $ci) {
                $simple[$k] = [
                    'product_id' => $ci['product_id'] ?? null,
                    'variation_id' => $ci['variation_id'] ?? null,
                    'quantity' => $ci['quantity'] ?? null,
                    'variation' => $ci['variation'] ?? null,
                    'data_name' => ($ci['data'] ?? null) instanceof WC_Product ? $ci['data']->get_name() : null,
                    'data_type' => ($ci['data'] ?? null) instanceof WC_Product ? $ci['data']->get_type() : null,
                ];
            }
            print_r($simple);
        } else {
            print_r($cart_snapshot);
        }
        echo '</pre></details></div>';
    }
}

// =============================================
// MT: Auto-aplicar cupón por URL en checkout
// + inyecta notice oculto para que coupon-message-handler.js lo convierta a inline
// URL ejemplo: /checkout/?add-to-cart=2459&coupon=MEGA5
// =============================================

if (!function_exists('mtch_is_checkout_request')) {
    function mtch_is_checkout_request(): bool
    {
        if (function_exists('is_checkout') && is_checkout()) return true;

        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if ($uri && strpos($uri, '/checkout') !== false) return true;

        $checkout_id = (int) get_option('woocommerce_checkout_page_id');
        if ($checkout_id && function_exists('is_page') && is_page($checkout_id)) return true;

        return false;
    }
}

// 1) Captura cupón MUY temprano: antes del redirect de Woo por ?add-to-cart=...
//    (Woo suele redirigir en wp_loaded, así que lo guardamos aquí con prioridad 1)
add_action('wp_loaded', function () {
    if (!function_exists('WC') || !WC()->session) return;

    $coupon = isset($_GET['coupon']) ? wc_format_coupon_code(wp_unslash($_GET['coupon'])) : '';
    if ($coupon === '') return;

    // Asegura cookie de sesión desde el primer hit
    if (method_exists(WC()->session, 'set_customer_session_cookie')) {
        WC()->session->set_customer_session_cookie(true);
    }

    WC()->session->set('mt_pending_coupon', $coupon);
    WC()->session->set('mt_coupon_from_url', '1'); // para CSS
}, 1);


// 2) Aplica cupón cuando ya hay carrito en checkout
add_action('wp_loaded', function () {
    if (!function_exists('WC') || !WC()->cart || !WC()->session) return;
    if (!mtch_is_checkout_request()) return;

    $code = (string) WC()->session->get('mt_pending_coupon', '');
    if ($code === '') return;
    if (WC()->cart->is_empty()) return;

    // Si ya está aplicado, no hagas nada (y no muestres mensaje)
    if (WC()->cart->has_discount($code)) {
        WC()->session->set('mt_pending_coupon', '');
        return;
    }

    // Aplica cupón
    $ok = WC()->cart->apply_coupon($code);
    WC()->cart->calculate_totals();

    $msg_type = 'success';
    $msg_text = sprintf('Coupon "%s" has been applied.', $code);

    if (!$ok || !WC()->cart->has_discount($code)) {
        $msg_type = 'error';
        $msg_text = sprintf('Coupon "%s" could not be applied.', $code);

        // Si Woo generó un error más específico, úsalo
        if (function_exists('wc_get_notices')) {
            $errs = wc_get_notices('error');
            if (!empty($errs[0]['notice'])) {
                $msg_text = wp_strip_all_tags($errs[0]['notice']);
            }
        }
    }

    // Flash para inyectarlo en el DOM (tu JS lo convierte a inline)
    WC()->session->set('mt_coupon_flash', [
        'type' => $msg_type,
        'text' => $msg_text,
    ]);

    // Limpia pending para no re-aplicar
    WC()->session->set('mt_pending_coupon', '');
}, 60);


// 3) Body class (para ocultar el banner grande SOLO cuando viene por URL)
add_filter('body_class', function ($classes) {
    if (!function_exists('WC') || !WC()->session) return $classes;

    if ((string) WC()->session->get('mt_coupon_from_url', '') === '1') {
        $classes[] = 'mt-coupon-from-url';
    }
    return $classes;
}, 20);


// 4) Inyecta notice oculto dentro de .woocommerce (tu JS lo captura y lo muestra inline)
add_action('wp_footer', function () {
    if (is_admin() || wp_doing_ajax()) return;
    if (!function_exists('WC') || !WC()->session) return;
    if (!mtch_is_checkout_request()) return;

    $flash = WC()->session->get('mt_coupon_flash');
    if (!is_array($flash) || empty($flash['text'])) return;

    $type = ($flash['type'] === 'error') ? 'woocommerce-error' : 'woocommerce-message';
    $text = wp_json_encode((string) $flash['text']);

    ?>
    <script>
      (function($){
        $(function(){
          var $w = $('.woocommerce').first();
          if (!$w.length) return;

          var cls = <?php echo wp_json_encode($type); ?>;
          var txt = <?php echo $text; ?>;

          // Node oculto -> MutationObserver lo detecta -> coupon-message-handler.js lo convierte a inline
          var $node = $('<div/>', { 'class': cls, 'style': 'display:none;' }).text(txt);
          $w.prepend($node);
        });
      })(jQuery);
    </script>
    <?php

    // Limpieza (1 vez)
    WC()->session->set('mt_coupon_flash', null);
    WC()->session->set('mt_coupon_from_url', '');
}, 9999);

