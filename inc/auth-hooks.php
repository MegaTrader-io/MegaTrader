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
    }
}

add_action('woocommerce_login_form', 'mt_enqueue_auth_script_on_login_form');

add_filter('template_include', 'load_custom_auth_template', 99);
function load_custom_auth_template($template)
{
    if (!function_exists('wc_get_page_id')) return $template;

    $is_register = get_query_var('register', null);
    $my_account_id = wc_get_page_id('myaccount');

    if ($my_account_id > 0 && (is_page($my_account_id) && !is_user_logged_in() || $is_register && !is_user_logged_in())) {
        $auth_template = get_theme_file_path('page-auth.php');
        if (file_exists($auth_template)) {
            return $auth_template;
        }
    }
    return $template;
}

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

    $username = trim((string)wp_unslash($_POST['username']));
    $password = (string)$_POST['password'];
    $remember = isset($_POST['rememberme']);

    if ($username === '') {
        wc_add_notice(__('Email is required.', 'your-td'), 'error', ['field' => 'username']);
    } else if (!is_email($username)) {
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
                if ($code == 'incorrect_password') {
                    wc_add_notice('Incorrect password', 'error', ['field' => $field]);
                    continue;
                }

                if ($code == 'invalid_email') {
                    wc_add_notice('No account found with this email.', 'error', ['field' => $field]);
                    continue;
                }

                wc_add_notice($msg, 'error', ['field' => $field]);
            }

        }
        do_action('woocommerce_login_failed');
        return;
    }

    $redirect = !empty($_POST['redirect'])
        ? wp_unslash($_POST['redirect'])
        : (wc_get_raw_referer() ?: wc_get_page_permalink('myaccount'));

    $redirect = remove_query_arg(['wc_error', 'password-reset'], $redirect);

    wp_safe_redirect(
        wp_validate_redirect(
            apply_filters('woocommerce_login_redirect', $redirect, $user),
            wc_get_page_permalink('myaccount')
        )
    );
    exit;
}

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('mt-auth', get_stylesheet_directory_uri() . '/assets/js/auth.js', [], '1.0.0', true);

    $inline = <<<JS
(function cleanResetPassParam(){'use strict';try{if(!('URL'in window)||!('history'in window)||typeof history.replaceState!=='function'){return}const url=new URL(window.location.href);const PARAM='reset-pass';const removeIfExists=true;const onlyWhenTrue=false;const hasParam=url.searchParams.has(PARAM);if(!hasParam)return;if(onlyWhenTrue){const value=url.searchParams.get(PARAM);if(value!=='true')return}url.searchParams.delete(PARAM);const newUrl=url.origin+url.pathname+(url.search?url.search:'')+(url.hash||'');history.replaceState(null,document.title,newUrl)}catch(err){console.warn('[cleanResetPassParam] Failed:',err)}})();
JS;
    wp_add_inline_script('mt-auth', $inline, 'after');
});