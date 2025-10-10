<?php
// inc/mt-checkout-helper.php

// === Util: leer constantes de Label con Reflection ===
if (!function_exists('mtch_get_label_const')) {
function mtch_get_label_const(string $name) {
    if (!class_exists('Label')) return null;
    try {
        $ref = new ReflectionClass('Label');
        return $ref->hasConstant($name) ? $ref->getConstant($name) : null;
    } catch (Throwable $e) {
        return null;
    }
}}

// === Cart: nombre del primer producto (para detectar Reset/Activation) ===
if (!function_exists('mtch_get_cart_primary_product_name')) {
function mtch_get_cart_primary_product_name(): ?string {
    if (!(function_exists('WC') && WC()->cart && !WC()->cart->is_empty())) return null;
    $first = reset(WC()->cart->get_cart());
    return ($first['data'] ?? null) instanceof WC_Product ? $first['data']->get_name() : null;
}}

// === Detecta tipo (reset / activation) de forma estricta ===
if (!function_exists('mtch_detect_product_kind_strict')) {
function mtch_detect_product_kind_strict(?WC_Product $product): ?string {
    if (!($product instanceof WC_Product)) return null;
    $pid  = $product->get_id();
    $name = strtolower(trim((string)$product->get_name()));

    if ($name === 'reset fee')      return 'reset';
    if ($name === 'activation fee') return 'activation';

    $in_reset = function_exists('has_term') ? (
        has_term(['reset-fee','reset'], 'product_cat', $pid) || has_term(['reset-fee','reset'], 'product_tag', $pid)
    ) : false;

    $in_activation = function_exists('has_term') ? (
        has_term(['activation-fee','activation'], 'product_cat', $pid) || has_term(['activation-fee','activation'], 'product_tag', $pid)
    ) : false;

    if ($in_reset)      return 'reset';
    if ($in_activation) return 'activation';
    return null;
}}

// === Texto del badge (Reset/Activation cuando aplique; si no, plan_type) ===
if (!function_exists('mtch_prepare_badge_text')) {
function mtch_prepare_badge_text(?WC_Product $product, string $plan_type): string {
    $cart_name = mtch_get_cart_primary_product_name();
    $prod_name = ($product instanceof WC_Product) ? $product->get_name() : null;
    $name      = trim((string)($cart_name ?: $prod_name));
    if (preg_match('/^(reset fee|activation fee)$/i', $name)) {
        return $name; // "Reset Fee" o "Activation Fee"
    }
    return $plan_type;
}}

// === Línea secundaria "size - X" (X = plan_type en especiales, o Buying Power) ===
if (!function_exists('mtch_prepare_secondary_line')) {
function mtch_prepare_secondary_line(string $plan_size_slug, string $plan_type, ?WC_Product $product): string {
    $cart_name = mtch_get_cart_primary_product_name();
    $prod_name = ($product instanceof WC_Product) ? $product->get_name() : null;
    $name      = trim((string)($cart_name ?: $prod_name));
    $is_special = (bool) preg_match('/^(reset fee|activation fee)$/i', $name);

    $price_cfg = mtch_get_label_const('PRICE');
    $buying_power = (is_array($price_cfg) && isset($price_cfg['price_sufix'])) ? $price_cfg['price_sufix'] : 'Buying Power';

    return $plan_size_slug . ' - ' . ($is_special ? $plan_type : $buying_power);
}}

// === Prepara meta items + clase de grid (x4 solo si usamos META_RESET/META_ACTIVATION) ===
if (!function_exists('mtch_prepare_meta_items')) {
function mtch_prepare_meta_items(?WC_Product $product, array $rich): array {
    $label_product_meta = mtch_get_label_const('PRODUCT_META') ?: [];
    $meta_order = mtch_get_label_const('META_ORDER');
    if (!is_array($meta_order) || empty($meta_order)) {
        $meta_order = array_keys($label_product_meta);
    }
    $icon_map = mtch_get_label_const('PRODUCT_META_ICONS') ?: [];

    $items = [];
    $used_highlights = false;
    $pid  = ($product instanceof WC_Product) ? $product->get_id() : 0;
    $kind = mtch_detect_product_kind_strict($product);

    if ($pid && !empty($meta_order)) {
        foreach ($meta_order as $key) {
            if (!isset($label_product_meta[$key])) continue;
            $val = $rich['meta'][$key] ?? get_post_meta($pid, $key, true);
            if ($val === '' || $val === null) continue;
            $items[] = [
                'key'        => $key,
                'label'      => $label_product_meta[$key],
                'value'      => $val,
                'icon_class' => $icon_map[$key] ?? 'mt-icon',
            ];
        }
    }

    // Fallback a destacados si no hay metas reales
    if (empty($items) && $kind === 'reset') {
        $cfg = mtch_get_label_const('META_RESET');
        if (is_array($cfg) && !empty($cfg)) {
            $items = array_map(fn($s) => [
                'key' => $s['key'], 'label' => $s['label'], 'value' => $s['value'], 'icon_class' => $s['icon'],
            ], $cfg);
            $used_highlights = true;
        }
    } elseif (empty($items) && $kind === 'activation') {
        $cfg = mtch_get_label_const('META_ACTIVATION');
        if (is_array($cfg) && !empty($cfg)) {
            $items = array_map(fn($s) => [
                'key' => $s['key'], 'label' => $s['label'], 'value' => $s['value'], 'icon_class' => $s['icon'],
            ], $cfg);
            $used_highlights = true;
        }
    }

    $grid_extra_class = $used_highlights ? ' mt-meta-grid--x4' : '';
    return [$items, $grid_extra_class];
}}

// === ¿Es Activation Fee? (para ocultar/limpiar addons) ===
if (!function_exists('mtch_is_activation_product')) {
function mtch_is_activation_product(?WC_Product $product): bool {
    $cart_name = mtch_get_cart_primary_product_name();
    $prod_name = ($product instanceof WC_Product) ? $product->get_name() : null;
    $name      = trim((string)($cart_name ?: $prod_name));
    if (strcasecmp($name, 'Activation Fee') === 0) return true;
    return mtch_detect_product_kind_strict($product) === 'activation';
}}

// === Fuerza que los add-ons no queden seleccionados en Activation Fee ===
if (!function_exists('mtch_enforce_no_addons_on_activation')) {
function mtch_enforce_no_addons_on_activation() {
    add_filter('woocommerce_checkout_posted_data', function($data) {
        if (isset($data['add_ons']) && is_array($data['add_ons'])) {
            foreach ($data['add_ons'] as $k => $_) { $data['add_ons'][$k] = ''; }
        }
        return $data;
    }, 9999);
}}

// === Resolver el account_id para el checkout (POST silencioso -> GET -> fallback UI) ===

if (!function_exists('mtch_resolve_ids')) {
function mtch_resolve_ids(): array {
    // 1) POST (desde los modales con submit silencioso)
    $acc_post  = isset($_POST['account_id'])       ? sanitize_text_field( wp_unslash($_POST['account_id']) )       : '';
    $main_post = isset($_POST['main_product_id'])  ? sanitize_text_field( wp_unslash($_POST['main_product_id']) )  : '';

    if ($acc_post !== '' && $main_post !== '') {
        return [
            'account_id'      => (string) $acc_post,
            'main_product_id' => (string) $main_post,
        ];
    }

    // 2) GET (compatibilidad legacy)
    $acc_get  = isset($_GET['account_id'])       ? sanitize_text_field( wp_unslash($_GET['account_id']) )       : '';
    $main_get = isset($_GET['main_product_id'])  ? sanitize_text_field( wp_unslash($_GET['main_product_id']) )  : '';

    if ($acc_get !== '' && $main_get !== '') {
        return [
            'account_id'      => (string) $acc_get,
            'main_product_id' => (string) $main_get,
        ];
    }

    // 3) Fallback plano (si faltara algo, devolvemos string vacío)
    return [
        'account_id'      => '',
        'main_product_id' => '',
    ];
}}

// (opcional) compatibilidad con el nombre que ya usabas:
if (!function_exists('mtch_resolve_account_id')) {
function mtch_resolve_account_id(): string {
    $ids = mtch_resolve_ids();
    return (string) ($ids['account_id'] ?? '');
}}



// === Debug visual opcional (?mtdebug=1 o WP_DEBUG) ===
if (!function_exists('mtch_render_debug_panel')) {
function mtch_render_debug_panel(?WC_Product $product, array $rich, array $meta_info = []) {
    if (!( (isset($_GET['mtdebug']) && $_GET['mtdebug'] !== '') || (defined('WP_DEBUG') && WP_DEBUG) )) return;

    $pid  = ($product instanceof WC_Product) ? $product->get_id() : 0;
    $cats = $pid ? wp_get_post_terms($pid, 'product_cat', ['fields' => 'names']) : [];
    $tags = $pid ? wp_get_post_terms($pid, 'product_tag', ['fields' => 'names']) : [];
    $raw_meta = $pid ? get_post_meta($pid) : [];
    $cart_snapshot = (function_exists('WC') && WC()->cart) ? WC()->cart->get_cart() : null;

    $product_basic = ($product instanceof WC_Product) ? [
        'id'=>$pid,
        'type'=>$product->get_type(),
        'name'=>$product->get_name(),
        'sku'=>$product->get_sku(),
        'price'=>$product->get_price(),
        'regular_price'=>$product->get_regular_price(),
        'sale_price'=>$product->get_sale_price(),
        'stock_status'=>$product->get_stock_status(),
        'manage_stock'=>$product->get_manage_stock(),
        'stock_quantity'=>$product->get_stock_quantity(),
        'variation_attrs'=>$product->is_type('variation') ? $product->get_variation_attributes() : [],
    ] : null;

    echo '<div class="mt-debug-log" style="margin-top:24px;padding:16px;background:#111;border:1px solid #333;border-radius:8px;color:#FFD78A;">';
    echo '<div style="margin-bottom:8px;font-weight:600;">DEBUG: Producto</div>';

    echo '<details open><summary style="cursor:pointer;color:#fff;">Resumen</summary><pre style="white-space:pre-wrap;color:#A8A29E;">';
    print_r(['query_params'=>$_GET,'product_basic'=>$product_basic,'cats'=>$cats,'tags'=>$tags]); echo '</pre></details>';

    echo '<details><summary style="cursor:pointer;color:#fff;">Rich payload</summary><pre style="white-space:pre-wrap;color:#A8A29E;">';
    print_r($rich); echo '</pre></details>';

    echo '<details><summary style="cursor:pointer;color:#fff;">Config (meta)</summary><pre style="white-space:pre-wrap;color:#A8A29E;">';
    print_r($meta_info); echo '</pre></details>';

    echo '<details><summary style="cursor:pointer;color:#fff;">Post Meta (raw)</summary><pre style="white-space:pre-wrap;color:#A8A29E;max-height:360px;overflow:auto;">';
    print_r($raw_meta); echo '</pre></details>';

    echo '<details><summary style="cursor:pointer;color:#fff;">Carrito (snapshot)</summary><pre style="white-space:pre-wrap;color:#A8A29E;">';
    if (is_array($cart_snapshot)) {
        $simple = [];
        foreach ($cart_snapshot as $k => $ci) {
            $simple[$k] = [
                'product_id'  => $ci['product_id'] ?? null,
                'variation_id'=> $ci['variation_id'] ?? null,
                'quantity'    => $ci['quantity'] ?? null,
                'variation'   => $ci['variation'] ?? null,
                'data_name'   => ($ci['data'] ?? null) instanceof WC_Product ? $ci['data']->get_name() : null,
                'data_type'   => ($ci['data'] ?? null) instanceof WC_Product ? $ci['data']->get_type() : null,
            ];
        }
        print_r($simple);
    } else {
        print_r($cart_snapshot);
    }
    echo '</pre></details></div>';
}}
