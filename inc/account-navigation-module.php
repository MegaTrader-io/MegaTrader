<?php
if (!defined('ABSPATH')) exit;

/**
 * Menú de Mi cuenta
 */
add_filter('woocommerce_account_menu_items', function ($items) {
    $new_items = [];

    $new_items['trade-area']        = __('Account Metrics', 'woocommerce');
    $new_items['trading-journal']   = __('Trading Journal', 'woocommerce');

    return $new_items;
}, 20);

/**
 * URLs de endpoints del menú
 * - trade-area    -> /my-account/overview
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

    // base para /my-account/view-subscription/*
    $view_sub_base = rtrim(parse_url(home_url('my-account/view-subscription'), PHP_URL_PATH), '/');
    // base de overview (para metrics/journal)
    $overview_path = rtrim(parse_url(home_url('my-account/overview'), PHP_URL_PATH), '/');

    foreach ($menu_items as $endpoint => $label) {
        $url      = wc_get_account_endpoint_url($endpoint);
        $url_path = rtrim(parse_url($url, PHP_URL_PATH), '/');

        $is_active = ($req_path === $url_path);

        $item = [
            'label'   => $label,
            'url'     => $url,
            'active'  => (bool) $is_active,
            'view'    => ($endpoint === 'trade-area'
                ? 'metrics'
                : ($endpoint === 'trading-journal' ? 'journal' : '')),
            'item_id' => ($endpoint === 'subscriptions' ? 'mt-nav-manage-subscription' : ''),
        ];

        $items[] = $item;
    }

    // fallback: si nadie quedó activo, activa el primero
    if (!array_filter($items, fn($i) => !empty($i['active'])) && !empty($items[0])) {
        $items[0]['active'] = true;
    }

    return ['items' => $items, 'aria_label' => $aria_label, 'select_id' => $select_id];
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


/**
 * Subscriptions & Billing Navigation
 */
function subscriptions_billing_navigation_render(): void
{
    $nav_config = [
        [
            'label'         => Label::META_SUBSCRIPTIONS_BILLING['tab_subscriptions_label'],
            'endpoint'      => 'orders',
            'alt_endpoints' => ['orders', 'view-order', 'view-subscription'],
        ],
        [
            'label'         => Label::META_SUBSCRIPTIONS_BILLING['tab_billing_label'],
            'endpoint'      => 'payment-methods',
        ],
    ];

    $items = [];

    // final items builder
    foreach ($nav_config as $item) {
        $endpoints = $item['alt_endpoints'] ?? [$item['endpoint']];

        $is_active = false;
        foreach ($endpoints as $ep) {
            if (is_wc_endpoint_url($ep)) {
                $is_active = true;
                break;
            }
        }

        $items[] = [
            'label'  => $item['label'],
            'url'    => $is_active ? '#' : wc_get_account_endpoint_url($item['endpoint']),
            'active' => $is_active,
        ];
    }

    $args = [
        'items'      => $items,
        'aria_label' => Label::META_SUBSCRIPTIONS_BILLING['page_title'] . ' Nav',
    ];

    get_template_part('template-parts/account/account-navigation', null, $args);
}
