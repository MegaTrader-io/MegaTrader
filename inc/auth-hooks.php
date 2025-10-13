<?php

/**
 * Enqueue auth.js only when the WooCommerce login form is rendered
 * and the user is not authenticated.
 */
function mt_enqueue_auth_script_on_login_form(): void
{
    if (!is_user_logged_in()) {

        $css_path = get_template_directory() . '/assets/css/';
        $js_path = get_template_directory() . '/assets/js/';
        $css_uri = get_template_directory_uri() . '/assets/css/';
        $js_uri = get_template_directory_uri() . '/assets/js/';

        $css_version = file_exists($css_path . 'swiper-bundle.min.css') ? filemtime($css_path . 'swiper-bundle.min.css') : null;
        $js_version = file_exists($js_path . 'swiper-bundle.min.js') ? filemtime($js_path . 'swiper-bundle.min.js') : null;

        wp_enqueue_style('swiper-bundle', $css_uri . 'swiper-bundle.min.css', [], $css_version);
        wp_enqueue_script('swiper-bundle-style', $js_uri . 'swiper-bundle.min.js', [], $js_version, true);

        mt_intl_tel_input_assets();

        wp_enqueue_script(
            'mt-auth',
            get_stylesheet_directory_uri() . '/assets/js/auth.js',
            [],
            filemtime(get_stylesheet_directory() . '/assets/js/auth.js'), // Cache busting with file mtime
            true
        );

        wp_enqueue_style(
            'mt-auth-style',
            get_stylesheet_directory_uri() . '/assets/css/mgt-theme.css',
            [],
            filemtime(get_stylesheet_directory() . '/assets/css/mgt-theme.css')
        );

        $selling_location = get_option('woocommerce_allowed_countries');
        $my_post_language_details = apply_filters('wpml_post_language_details', null);
        $google_map_api_key = get_option('google_map_api_key', null);
        $mapJS = 'map-script.js';
        if (!empty($my_post_language_details) && !empty($my_post_language_details['language_code'])) {
            $current_language = $my_post_language_details['language_code'];
            wp_enqueue_script('google-map', '//maps.googleapis.com/maps/api/js?key=' . trim($google_map_api_key) . '&language=' . $current_language . '&libraries=places,geometry', array(), SHIPPING_WORKSHOP_VERSION, false);
        } else {
            wp_enqueue_script('google-map', '//maps.googleapis.com/maps/api/js?key=' . trim($google_map_api_key) . '&libraries=places,geometry', array(), SHIPPING_WORKSHOP_VERSION, false);
        }
        wp_enqueue_script('map-script', WC_ADDRESS_AUTOCOMPLETE_URL . 'assets/Public/js/' . $mapJS . '?rand=' . wp_rand(), array('google-map'), SHIPPING_WORKSHOP_VERSION, true);
        wp_localize_script(
            'map-script',
            'countries',
            array(
                'countries' => [],
                'map_display' => get_option('aafw_enable_map', 1),
                'map_validation' => get_option('aafw_allow_manual_address'),
                'enable_restriction' => get_option('aafw_enable_restriction'),
                'aafw_enable_map' => get_option('aafw_enable_map'),
                'map_style' => get_option('map_style', 1),
                'custom_msg' => '',
                'custom_zoom_map' => get_option('custom_zoom_map', 8),
                'selling_location' => $selling_location,
            )
        );

        $woo_default_country = explode(':', get_option('woocommerce_default_country'));
        $woocommerce_default_country = WC()->countries->countries[$woo_default_country[0]];
        $back_end_map = array();
        $billing_data = array();
        $shipping_data = array();
        $fieldsettings = array(
            'Postalcc' => get_option('Postalcc', 1),
            'Countryaddr' => get_option('Countryaddr', 1),
            'administrative_area_level_1addr' => get_option('administrative_area_level_1addr', 1),
            'administrative_area_level_2addr' => get_option('administrative_area_level_2addr', 1),
            'localityaddr' => get_option('localityaddr', 1),
            'neighborhoodaddr' => get_option('neighborhoodaddr', 1),
            'routeaddr' => get_option('routeaddr', 1),
            'street_numberrouteaddr' => get_option('street_numberrouteaddr', 1),
        );

        wp_localize_script(
            'map-script',
            'map_data',
            array(
                wp_json_encode(
                    array(
                        'billing_data' => $billing_data,
                        'shipping_data' => $shipping_data,
                        'fieldsettings' => $fieldsettings,
                    )
                ),
            )
        );
        wp_localize_script('map-script', 'back_end_map', $back_end_map);
        wp_localize_script('map-script', 'woocommerce_default_country', array('woocommerce_country' => $woocommerce_default_country));

        wp_enqueue_style('map-style', WC_ADDRESS_AUTOCOMPLETE_URL . 'assets/Public/css/map-style.css', array(), SHIPPING_WORKSHOP_VERSION, false);

        remove_action('admin_menu', 'add_intercom_settings_page');
        remove_action('network_admin_menu', 'add_intercom_settings_page');
        remove_action('admin_init', 'intercom_settings');
        remove_action('wp_footer', 'add_intercom_snippet', 999);
    }
}

add_action('woocommerce_login_form', 'mt_enqueue_auth_script_on_login_form');

add_action('init', function () {
    remove_action('woocommerce_before_customer_login_form', 'woocommerce_output_all_notices', 10);
}, 10);

add_action('init', function () {
    if (class_exists('WC_Form_Handler')) {
        remove_action('wp_loaded', ['WC_Form_Handler', 'process_login'], 20);
        add_action('wp_loaded', 'mt_process_login', 20);
    }
}, 11);

function mt_process_login(): void
{
    $var = $_REQUEST['woocommerce-login-nonce'] ?? null;
    $var1 = $_REQUEST['_wpnonce'] ?? null;
    $nonce = wc_get_var($var, wc_get_var($var1, ''));

    if (isset($_GET['reset-pass']) && $_GET['reset-pass'] === 'success') {
        wc_add_notice(__('Your password has been reset successfully.', 'woocommerce'), 'success');
    }

    if (
        !wp_verify_nonce($nonce, 'woocommerce-login') ||
        !isset($_POST['login'], $_POST['username'], $_POST['password']) ||
        !is_string($_POST['username']) || !is_string($_POST['password'])
    ) {
        return;
    }

    $username = trim((string) wp_unslash($_POST['username']));
    $password = (string) $_POST['password'];
    $remember = isset($_POST['rememberme']);

    if ($username === '') {
        wc_add_notice(__('Email is required.', 'your-td'), 'error', ['field' => 'username']);
    } elseif (!is_email($username)) {
        wc_add_notice(__('Enter a valid email address.', 'your-td'), 'error', ['field' => 'username']);
    }
    if ($password === '') {
        wc_add_notice(__('Password is required.', 'your-td'), 'error', ['field' => 'password']);
    }
    if (wc_notice_count('error') > 0) {
        return;
    }

    $validation_error = apply_filters('woocommerce_process_login_errors', new WP_Error(), $username, $password);
    if ($validation_error->get_error_code()) {
        foreach ($validation_error->get_error_codes() as $code) {
            $field = in_array($code, ['empty_username', 'invalid_username'], true) ? 'username'
                : (in_array($code, ['empty_password'], true) ? 'password' : 'general');
            foreach ($validation_error->get_error_messages($code) as $msg) {
                wc_add_notice($msg, 'error', ['field' => $field]);
            }
        }
        return;
    }

    if (is_multisite()) {
        $user_data = get_user_by(is_email($username) ? 'email' : 'login', $username);
        if ($user_data && !is_user_member_of_blog($user_data->ID, get_current_blog_id())) {
            add_user_to_blog(get_current_blog_id(), $user_data->ID, 'customer');
        }
    }

    $user = wp_signon(apply_filters('woocommerce_login_credentials', [
        'user_login' => $username,
        'user_password' => $password,
        'remember' => $remember,
    ]), is_ssl());

    if (is_wp_error($user)) {
        foreach ($user->get_error_codes() as $code) {
            $field = match ($code) {
                'empty_username', 'invalid_username' => 'username',
                'empty_password', 'incorrect_password' => 'password',
                default => 'general',
            };
            foreach ($user->get_error_messages($code) as $msg) {
                if ($code === 'incorrect_password') {
                    wc_add_notice('Incorrect password', 'error', ['field' => $field]);
                    continue;
                }
                if ($code === 'invalid_email') {
                    wc_add_notice('No account found with this email.', 'error', ['field' => $field]);
                    continue;
                }
                wc_add_notice($msg, 'error', ['field' => $field]);
            }
        }
        do_action('woocommerce_login_failed');
        return;
    }

    $url = profile_url(
        user_email: $user->user_email
    );

    wp_safe_redirect(
        $url
    );
    exit;
}



add_action('wp_enqueue_scripts', function () {
    // Obtén la ruta relativa de la URL actual
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';

    // Solo aplica si la URL empieza con /auth/
    if (strpos($request_uri, '/auth/') !== 0) {
        return;
    }

    wp_enqueue_script('mt-auth', get_stylesheet_directory_uri() . '/assets/js/auth.js', [], '1.0.0', true);

    $inline = <<<JS
(function cleanResetPassParam(){'use strict';try{if(!('URL'in window)||!('history'in window)||typeof history.replaceState!=='function'){return}const url=new URL(window.location.href);const PARAM='reset-pass';const removeIfExists=true;const onlyWhenTrue=false;const hasParam=url.searchParams.has(PARAM);if(!hasParam)return;if(onlyWhenTrue){const value=url.searchParams.get(PARAM);if(value!=='true')return}url.searchParams.delete(PARAM);const newUrl=url.origin+url.pathname+(url.search?url.search:'')+(url.hash||'');history.replaceState(null,document.title,newUrl)}catch(err){console.warn('[cleanResetPassParam] Failed:',err)}})();
JS;
    wp_add_inline_script('mt-auth', $inline, 'after');
});

/**
 * Auth routes redirector (/auth/* <-> /my-account).
 * - Si usuario logueado entra a /auth/* => redirige a /my-account.
 * - Si NO logueado entra a /my-account o endpoints => redirige a /auth/* correspondiente.
 * - Preserva redirect_to con la URL original.
 */
add_action('template_redirect', function () {
    if (isset($_GET['logged_out']) && $_GET['logged_out'] == 1) {
        nocache_headers();

        if (function_exists('wc_add_notice')) {
            wc_add_notice(__('You have successfully logged out.', 'your-td'), 'success');
        }
    }

    if (!function_exists('is_account_page') || !function_exists('wc_get_page_permalink')) {
        return;
    }

    // Ruta solicitada (sin query ni hash)
    $path = (string) wp_parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $path = trailingslashit($path);

    // ¿Es una ruta /auth/* ?
    $is_auth_route = (strpos($path, '/auth/') === 0);

    // URL absoluta actual (para redirect_to)
    $current_url = (is_ssl() ? 'https://' : 'http://') .
        ($_SERVER['HTTP_HOST'] ?? '') .
        ($_SERVER['REQUEST_URI'] ?? '/');

    // 1) Ya logueado en /auth/* => manda al dashboard
    if (is_user_logged_in() && $is_auth_route) {
        wp_safe_redirect(wc_get_page_permalink('myaccount'), 302);
        exit;
    }

    // 2) No logueado en /my-account o endpoints => manda a /auth/*
    if (!is_user_logged_in() && !$is_auth_route && (is_account_page() || is_wc_endpoint_url())) {

        // Mapea endpoint -> ruta de auth
        $target = '/auth/login';
        if (is_wc_endpoint_url('lost-password') || get_query_var('lost-password')) {
            $target = '/auth/lost-password';
        } elseif (is_wc_endpoint_url('register') || (isset($_GET['action']) && $_GET['action'] === 'register')) {
            $target = '/auth/register';
        }

        // Solo agrega redirect_to si NO existe (evita nesting infinito)
        $redir = home_url($target);
        if (!isset($_GET['redirect_to']) && !isset($_POST['redirect_to'])) {
            $redir = add_query_arg('redirect_to', rawurlencode($current_url), $redir);
        }

        wp_safe_redirect($redir, 302);
        exit;
    }
}, 9); // prioridad baja para que ocurra antes de elegir plantilla

/**
 * Post-login: respeta ?redirect_to si es mismo host, si no, manda a /my-account.
 */
add_filter('woocommerce_login_redirect', function ($redirect, $user) {
    $requested = isset($_REQUEST['redirect_to']) ? esc_url_raw(wp_unslash($_REQUEST['redirect_to'])) : '';
    if ($requested) {
        $homeHost = wp_parse_url(home_url('/'), PHP_URL_HOST);
        $reqHost = wp_parse_url($requested, PHP_URL_HOST);
        if ($homeHost && $homeHost === $reqHost) {
            return $requested;
        }
    }
    return wc_get_account_endpoint_url('orders');
}, 10, 2);

/**
 * Post-register: igual que login.
 */
add_filter('woocommerce_registration_redirect', function ($redirect) {
    $requested = isset($_REQUEST['redirect_to']) ? esc_url_raw(wp_unslash($_REQUEST['redirect_to'])) : '';
    if ($requested) {
        $homeHost = wp_parse_url(home_url('/'), PHP_URL_HOST);
        $reqHost = wp_parse_url($requested, PHP_URL_HOST);
        if ($homeHost && $homeHost === $reqHost) {
            return $requested;
        }
    }
    return wc_get_account_endpoint_url('orders');
}, 10);

add_action('wp_head', function () {
    $path = (string) wp_parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    if (strpos(trailingslashit($path), '/auth/') === 0) {
        echo "<meta name=\"robots\" content=\"noindex,nofollow\" />\n";
    }
}, 1);

add_action('init', function () {
    // Obtiene el path real (soporta jerarquías si algún día "Auth" tiene padre)
    $auth = get_page_by_path('auth');

    if (!$auth instanceof WP_Post) {
        return;
    }
    $auth_path = trim(get_page_uri($auth->ID), '/'); // ej: 'auth'
    $re = preg_quote($auth_path, '/');

    // IMPORTANTE: 'top' para que quede antes que las reglas de endpoints
    add_rewrite_rule('^' . $re . '/login/?$', 'index.php?pagename=' . $auth_path . '/login', 'top');
    add_rewrite_rule('^' . $re . '/register/?$', 'index.php?pagename=' . $auth_path . '/register', 'top');
    add_rewrite_rule('^' . $re . '/lost-password/?$', 'index.php?pagename=' . $auth_path . '/lost-password', 'top');
}, 1);

// Haz flush una vez (cambia tema o guarda permalinks) o deja este hook de una sola ejecución.
add_action('after_switch_theme', function () {
    flush_rewrite_rules(false);
});