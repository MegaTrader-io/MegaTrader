<?php
if (!defined('ABSPATH')) exit;

/**
 * Menú de Mi cuenta
 */
add_filter('woocommerce_account_menu_items', function ($items) {
    $new_items = [];

    $new_items['trade-area'] = __('Account Metrics', 'woocommerce');

<<<<<<< HEAD
=======
    // Mostrar u ocultar "Manage Subscription" según el flag global (si no existe, mostramos)
    $can_manage = $GLOBALS['mt_can_manage_subscription'] ?? null;
    if ($can_manage === null || $can_manage === true) {
        $new_items['subscriptions'] = __('Manage Subscription', 'woocommerce');
    }

>>>>>>> 17eba48 (saving changes)
    /*
    if (isset($items['payment-methods'])) {
        $new_items['payment-methods'] = $items['payment-methods'];
    }
    */

    return $new_items;
}, 20);


/**
 * URLs de endpoints del menú
 * - trade-area    -> /my-account/overview
 * - subscriptions -> /my-account/orders?orderId=XXXX (si tenemos el ID activo)
 */
add_filter('woocommerce_get_endpoint_url', function ($url, $endpoint, $value, $permalink) {

    if ($endpoint === 'trade-area') {
        return site_url('/my-account/overview');
    }

    if ($endpoint === 'subscriptions') {
        $base = trailingslashit(home_url('my-account/orders'));

        // Tomamos el orderId calculado en account-overview.php
        $oid = isset($GLOBALS['mt_active_order_id']) ? (int)$GLOBALS['mt_active_order_id'] : 0;

        // Si no hay global (p.ej. otras pantallas), dejamos el base sin query
        if ($oid > 0) {
            // Evita duplicados si algo ya trae query
            $base = add_query_arg(['orderId' => $oid], $base);
        }

        return $base;
    }

    return $url;

}, 10, 4);

/**
 * Args para el template de navegación
 */
function account_navigation_get_args()
{
    if (!function_exists('wc_get_account_menu_items')) {
        return [];
    }

    return items_navigation_get_args(wc_get_account_menu_items());
}

function items_navigation_get_args($menu_items = [], $aria_label = '', $select_id = '')
{
    $aria_label = $aria_label ?: __('Account pages', 'woocommerce');
    $select_id  = $select_id  ?: 'mega-navigation-select';

    $items    = [];
    $req_path = rtrim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');

    foreach ($menu_items as $endpoint => $label) {
        // wc_get_account_endpoint_url ya pasa por el filtro de arriba
        $url      = wc_get_account_endpoint_url($endpoint);
        $url_path = rtrim(parse_url($url, PHP_URL_PATH), '/');

        $is_active = ($req_path === $url_path);

        // Marcar activo cuando estamos en orders / view-subscription
        if (!$is_active && $label === 'Manage Subscription') {
            $current_endpoint = function_exists('WC') ? WC()->query->get_current_endpoint() : '';
            $is_active = in_array($current_endpoint, ['view-order', 'view-subscription', 'orders', 'subscriptions'], true)
                      || strpos($req_path, '/my-account/view-subscription') !== false
                      || strpos($req_path, '/my-account/orders') !== false;
        }

        $item = [
            'label'  => $label,
            'url'    => $url,
            'active' => (bool) $is_active,
        ];

        // >>> ÚNICO EXTRA: pasar item_id para el li de "Manage Subscription"
        if ($endpoint === 'subscriptions') {
            $item['item_id'] = 'mt-nav-manage-subscription';
        }

        $items[] = $item;
    }

    // Fallback: si ninguno quedó activo, activa el primero
    if (!array_filter($items, fn($i) => !empty($i['active'])) && !empty($items[0])) {
        $items[0]['active'] = true;
    }

    return [
        'items'      => $items,
        'aria_label' => $aria_label,
        'select_id'  => $select_id,
    ];
}

/**
 * Render
 */
function account_navigation_render()
{
    $args = account_navigation_get_args();
    get_template_part('template-parts/account/account-navigation', null, $args);
}

/**
 * Navegación de Settings
 */
function account_settings_navigation_render(): void
{
    $endpoints = [
        'profile'                           => __('Personal Information', 'woocommerce'),
        'profile/verification'              => __('Verification', 'woocommerce'),
        'profile/password'                  => __('Password', 'woocommerce'),
        'profile/two-factor-authentication' => __('2FA (Two-factor-authentication)', 'woocommerce'),
    ];

    get_template_part('template-parts/account/account-navigation', null, items_navigation_get_args($endpoints));
}
