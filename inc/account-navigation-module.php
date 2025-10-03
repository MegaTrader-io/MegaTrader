<?php

if ( ! defined('ABSPATH') ) exit;



add_filter('woocommerce_account_menu_items', function ($items) {
    $new_items = [];

    $new_items['trade-area'] = __('Account Metrics', 'woocommerce');

   
    $new_items['subscriptions'] = __('Manage Subscription', 'woocommerce');

    if (isset($items['payment-methods'])) {
        $new_items['payment-methods'] = $items['payment-methods'];
    }

    return $new_items;
}, 20);

add_filter( 'woocommerce_get_endpoint_url', function( $url, $endpoint, $value, $permalink ) {
    if ( $endpoint === 'trade-area' ) {
        $url = site_url( '/my-account/overview' );
    }
    if ( $endpoint === 'subscriptions' ) {
        return trailingslashit( home_url( 'my-account/orders' ) );
    }
    return $url;
}, 10, 4 );

/**
 * Get account navigation args
 *
 * @return array
 */
function account_navigation_get_args() {
    if ( ! function_exists('wc_get_account_menu_items') ) {
        return [];
    }

    return items_navigation_get_args(
        wc_get_account_menu_items()
    );
}

function items_navigation_get_args($menu_items = [], $aria_label = '', $select_id = '') {
    $aria_label = $aria_label ?: __( 'Account pages', 'woocommerce' );
    $select_id = $select_id ?: 'mega-navigation-select';

    $items = [];
    $req_path  = rtrim( parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ), '/' );

    foreach ( $menu_items as $endpoint => $label ) {
        $url = wc_get_account_endpoint_url( $endpoint );
        $url_path = rtrim( parse_url( $url, PHP_URL_PATH ), '/' );

        $is_active = $req_path === $url_path ;

        if(!$is_active && $label === 'Manage Subscription'){
            $current_endpoint = WC()->query->get_current_endpoint();
            $is_active = in_array($current_endpoint, ['view-order', 'view-subscription', 'orders'], true);
        }

        $items[] = [
            'label'  => $label,
            'url'    => $url,
            'active' => $is_active,
        ];
    }

    // Fallback: if none marked active, set first one
    if ( ! array_filter( $items, fn($i) => !empty($i['active']) ) && !empty($items[0]) ) {
        $items[0]['active'] = true;
    }

    return [
        'items'      => $items,
        'aria_label' => $aria_label,
        'select_id'  => $select_id,
    ];
}
 
 /**
 * Render the navigation
 */
function account_navigation_render() {
    $args = account_navigation_get_args();
    get_template_part( 'template-parts/account/account-navigation', null, $args );
}

function account_settings_navigation_render(): void
{
    $endpoints = [
        'profile' => __('Personal Information', 'woocommerce'),
        'profile/verification' => __('Verification', 'woocommerce'),
        'profile/password' => __('Password', 'woocommerce'),
        'profile/two-factor-authentication' => __('2FA (Two-factor-authentication)', 'woocommerce'),
    ];

    get_template_part('template-parts/account/account-navigation', null, items_navigation_get_args($endpoints));
}