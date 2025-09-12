<?php
// inc/mt-checkout-helper.php

// --- Util: leer const de Label con Reflection ---
if (!function_exists('mtch_get_label_const')) {
    function mtch_get_label_const(string $name)
    {
        if (!class_exists('Label'))
            return null;
        $ref = new ReflectionClass('Label');
        return $ref->hasConstant($name) ? $ref->getConstant($name) : null;
    }
}

// --- Cart: nombre del primer producto ---
if (!function_exists('mtch_get_cart_primary_product_name')) {
    function mtch_get_cart_primary_product_name(): ?string
    {
        if (!(function_exists('WC') && WC()->cart && !WC()->cart->is_empty()))
            return null;
        $first = reset(WC()->cart->get_cart());
        return ($first['data'] ?? null) instanceof WC_Product ? $first['data']->get_name() : null;
    }
}

// --- Detecta tipo estrictamente (reset/activation) ---
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
        $in_reset = function_exists('has_term') ? (has_term(['reset-fee', 'reset'], 'product_cat', $pid) || has_term(['reset-fee', 'reset'], 'product_tag', $pid)) : false;
        $in_activation = function_exists('has_term') ? (has_term(['activation-fee', 'activation'], 'product_cat', $pid) || has_term(['activation-fee', 'activation'], 'product_tag', $pid)) : false;
        if ($in_reset)
            return 'reset';
        if ($in_activation)
            return 'activation';
        return null;
    }
}

// --- Línea secundaria "size - X" según producto especial ---
if (!function_exists('mtch_prepare_secondary_line')) {
    function mtch_prepare_secondary_line(string $plan_size_slug, string $plan_type, ?WC_Product $product): string
    {
        $cart_name = mtch_get_cart_primary_product_name();
        $prod_name = ($product instanceof WC_Product) ? $product->get_name() : null;
        $name = trim((string) ($cart_name ?: $prod_name));
        $is_special = (bool) preg_match('/^(reset fee|activation fee)$/i', $name);
        return $plan_size_slug . ' - ' . ($is_special ? $plan_type : Label::PRICE['price_sufix']);
    }
}

// --- Meta items + clase de grid (x4 solo en highlights) ---
if (!function_exists('mtch_prepare_meta_items')) {
    function mtch_prepare_meta_items(?WC_Product $product, array $rich): array
    {
        $meta_order = (class_exists('Label') && defined('Label::META_ORDER')) ? Label::META_ORDER
            : (class_exists('Label') ? array_keys(Label::PRODUCT_META) : []);
        $icon_map = (class_exists('Label') && defined('Label::PRODUCT_META_ICONS')) ? Label::PRODUCT_META_ICONS : [];

        $items = [];
        $used_highlights = false;
        $pid = ($product instanceof WC_Product) ? $product->get_id() : 0;
        $kind = mtch_detect_product_kind_strict($product);

        if ($pid && !empty($meta_order)) {
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

        if (empty($items) && $kind === 'reset') {
            $cfg = mtch_get_label_const('META_RESET');
            if (is_array($cfg) && !empty($cfg)) {
                $items = array_map(fn($s) => [
                    'key' => $s['key'],
                    'label' => $s['label'],
                    'value' => '',
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
                    'value' => '',
                    'icon_class' => $s['icon'],
                ], $cfg);
                $used_highlights = true;
            }
        }

        $grid_extra_class = $used_highlights ? ' mt-meta-grid--x4' : '';
        return [$items, $grid_extra_class];
    }
}

// --- Activation: ocultar addons y limpiar request ---
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

// --- Debug (opcional) ---
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
