<?php
if (!defined('ABSPATH')) exit;

/**
 * Menú de Mi cuenta
 */
add_filter('woocommerce_account_menu_items', function ($items) {
    $new_items = [];

    $new_items['trade-area']    = __('Account Metrics', 'woocommerce');
    $new_items['trading-journal']   = __('Trading Journal', 'woocommerce');
    $new_items['subscriptions'] = __('Manage Subscription', 'woocommerce');

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
        // métrica como vista “metrics”
        return site_url('/my-account/overview?view=metrics');
    }

    if ($endpoint === 'trading-journal') {
        // misma página, vista “journal”
        return site_url('/my-account/overview?view=journal');
    }

    if ($endpoint === 'subscriptions') {
        $base = trailingslashit(home_url('my-account/orders'));
        $oid  = isset($GLOBALS['mt_active_order_id']) ? (int) $GLOBALS['mt_active_order_id'] : 0;
        if ($oid > 0) $base = add_query_arg(['orderId' => $oid], $base);
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
    $req_view = isset($_GET['view']) ? strtolower(sanitize_text_field($_GET['view'])) : 'metrics';

    foreach ($menu_items as $endpoint => $label) {
        $url      = wc_get_account_endpoint_url($endpoint);
        $url_path = rtrim(parse_url($url, PHP_URL_PATH), '/');

        $is_active = ($req_path === $url_path);

        // activar por ?view=*
        if (in_array($endpoint, ['trade-area','trading-journal'], true)) {
            $want = $endpoint === 'trade-area' ? 'metrics' : 'journal';
            $is_active = ($req_view === $want);
        }

        $item = [
            'label'   => $label,
            'url'     => $url,
            'active'  => (bool) $is_active,
            // data-view para el JS (tabs)
            'view'    => ($endpoint === 'trade-area' ? 'metrics' : ($endpoint === 'trading-journal' ? 'journal' : '')),
            'item_id' => ($endpoint === 'subscriptions' ? 'mt-nav-manage-subscription' : '')
        ];

        $items[] = $item;
    }

    if (!array_filter($items, fn($i) => !empty($i['active'])) && !empty($items[0])) {
        $items[0]['active'] = true;
    }

    return ['items'=>$items,'aria_label'=>$aria_label,'select_id'=>$select_id];
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
