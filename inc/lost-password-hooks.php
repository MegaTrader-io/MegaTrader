<?php

function mt_enqueue_auth_script_form()
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

add_action('woocommerce_lostpassword_form', 'mt_enqueue_auth_script_form');
add_action('woocommerce_resetpassword_form', 'mt_enqueue_auth_script_form');

add_action('init', function () {
    if (class_exists('WC_Form_Handler')) {
        remove_action('wp_loaded', ['WC_Form_Handler', 'process_lost_password'], 20);
        add_action('wp_loaded', 'mt_process_lost_password', 20);

        remove_action('wp_loaded', ['WC_Form_Handler', 'process_reset_password'], 20);
        add_action('wp_loaded', 'mt_process_reset_password', 20);
    }
}, 11);

function mt_process_reset_password()
{
    $nonce_value = wc_get_var(
        $_REQUEST['woocommerce-reset-password-nonce'],
        wc_get_var($_REQUEST['_wpnonce'], '')
    ); // @codingStandardsIgnoreLine.

    if (!wp_verify_nonce($nonce_value, 'reset_password')) {
        return;
    }

    $posted_fields_names = array('wc_reset_password', 'password_1', 'password_2', 'reset_key', 'reset_login');
    $posted_fields = [];

    foreach ($posted_fields_names as $field) {
        if (!isset($_POST[$field])) {
            return;
        }

        if (in_array($field, array('password_1', 'password_2'), true)) {
            // No unslash para passwords
            $posted_fields[$field] = $_POST[$field];
        } else {
            $posted_fields[$field] = wp_unslash($_POST[$field]);
        }
    }

    $user = WC_Shortcode_My_Account::check_password_reset_key(
        $posted_fields['reset_key'],
        $posted_fields['reset_login']
    );

    if ($user instanceof WP_User) {

        // Validaciones personalizadas
        if ($posted_fields['password_1'] === '') {
            wc_add_notice(__('Password is required.', 'your-td'), 'error', ['field' => 'password_1']);
        }

        if ($posted_fields['password_2'] === '') {
            wc_add_notice(__('Password is required.', 'your-td'), 'error', ['field' => 'password_2']);
        }

        if (
            $posted_fields['password_1'] !== '' &&
            $posted_fields['password_2'] !== '' &&
            $posted_fields['password_1'] !== $posted_fields['password_2']
        ) {
            wc_add_notice(__('The passwords do not match.', 'your-td'), 'error', ['field' => 'password_1']);
            wc_add_notice(__('The passwords do not match.', 'your-td'), 'error', ['field' => 'password_2']);
        }

        $errors = new WP_Error();
        do_action('validate_password_reset', $errors, $user);
        wc_add_wp_error_notices($errors);

        // Si no hay errores, resetear
        if (0 === wc_notice_count('error')) {
            WC_Shortcode_My_Account::reset_password($user, $posted_fields['password_1']);

            if (is_user_logged_in()) {
                wp_logout();
            }

            wc_clear_notices();

            $redirect = add_query_arg('reset-pass', 'success', wc_get_page_permalink('myaccount') ?: home_url('/'));
            wp_safe_redirect($redirect);
            exit;
        }
    }
}

function mt_process_lost_password(): void
{
    // Sólo maneja el POST del formulario de "Lost password"
    if (!isset($_POST['wc_reset_password'], $_POST['user_login'])) {
        return;
    }

    // Nonce (soporta templates antiguos con _wpnonce)
    $nonce_value = wc_get_var($_REQUEST['woocommerce-lost-password-nonce'], wc_get_var($_REQUEST['_wpnonce'], ''));

    if (!wp_verify_nonce($nonce_value, 'lost_password')) {
        return; // CSRF fail → no procesar
    }

    // Sanitiza input
    $user_login = trim((string)wp_unslash($_POST['user_login']));

    // Validaciones por campo (coherente con tu login/register)
    if ($user_login === '') {
        wc_add_notice(__('The email field is required.', 'your-td'), 'error', ['field' => 'user_login']);
        return;
    }

    // Si parece un email (contiene @) y no es válido, márcalo como error de campo
    if (strpos($user_login, '@') !== false && !is_email($user_login)) {
        wc_add_notice(__('Enter a valid email address.', 'your-td'), 'error', ['field' => 'user_login']);
        return;
    }

    /**
     * Delegamos a Woo para que ejecute el flujo estándar (incluye filtros/hardening).
     * - Internamente intenta resolver usuario por email o username.
     * - Si hay errores, añadirá notices de tipo "error".
     * - Si todo bien, añade notice de "success".
     */
    $success = WC_Shortcode_My_Account::retrieve_password();

    if ($success) {
        wc_clear_notices();
        wc_add_notice(
            __('The email have been sent successfully. Visit your email address, and follow the link to change your password.', 'your-td'),
            'success'
        );

        return;
    }


    // Si $success === false, Woo ya añadió errores (invalid_email/invalidcombo).
    // Si quieres etiquetar esos errores con 'field' => 'user_login', puedes interceptarlos:
    $all = wc_get_notices('error');
    if (!empty($all)) {
        wc_clear_notices();
        foreach ($all as $err) {
            $msg = is_string($err['notice']) ? $err['notice'] : __('Something went wrong.', 'your-td');
            wc_add_notice($msg, 'error', ['field' => 'user_login']); // etiqueta el campo
        }
    }
}

function mt_only_success_notice_types(array $types): array
{
    // Imprime únicamente notices de éxito
    return ['success'];
}

// Antes del printer (prio 10) activamos el filtro
add_action('woocommerce_before_lost_password_form', function () {
    add_filter('woocommerce_notice_types', 'mt_only_success_notice_types', 9999);
}, 9);

// Después del printer, removemos el filtro para no contaminar otros lugares
add_action('woocommerce_before_lost_password_form', function () {
    remove_filter('woocommerce_notice_types', 'mt_only_success_notice_types', 9999);
}, 11);