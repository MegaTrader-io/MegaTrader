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

        remove_action('admin_menu', 'add_intercom_settings_page');
        remove_action('network_admin_menu', 'add_intercom_settings_page');
        remove_action('admin_init', 'intercom_settings');
        remove_action('wp_footer', 'add_intercom_snippet', 999);
    }
}

add_action('woocommerce_lostpassword_form', 'mt_enqueue_auth_script_form');
add_action('woocommerce_resetpassword_form', 'mt_enqueue_auth_script_form');

add_action('init', function () {
    remove_filter('lostpassword_url', 'wc_lostpassword_url', 10);
    add_filter('lostpassword_url', 'mt_lostpassword_url', 10, 1);
}, 20);

add_action('init', function () {
    add_shortcode('mt_auth_lost_password', 'mt_render_auth_lost_password_shortcode');
});

add_action('init', function () {
    if (class_exists('WC_Form_Handler')) {
        remove_action('wp_loaded', ['WC_Form_Handler', 'process_lost_password'], 20);
        add_action('wp_loaded', 'mt_process_lost_password', 20);

        remove_action('wp_loaded', ['WC_Form_Handler', 'process_reset_password'], 20);
        add_action('wp_loaded', 'mt_process_reset_password', 20);

        remove_action('wp_loaded', ['WC_Form_Handler', 'redirect_reset_password_link'], 20);
        add_action('wp_loaded', 'mt_redirect_reset_password_link', 20);
    }
}, 11);

function mt_render_auth_lost_password_shortcode(): string
{
    ob_start();

    // Encola assets solo aquí (o usa tu add_action existente si prefieres)
    mt_enqueue_auth_script_form();

    // 1) Confirmación de envío (core agrega ?reset-link-sent=1)
    if (!empty($_GET['reset-link-sent'])) {
        wc_get_template('myaccount/lost-password-confirmation.php');
        return ob_get_clean();
    }

    // 2) Mostrar reset form si vienes del email y la cookie está OK
    if (!empty($_GET['show-reset-form'])) {
        $cookie_name = 'wp-resetpass-' . COOKIEHASH;

        if (!empty($_COOKIE[$cookie_name]) && strpos($_COOKIE[$cookie_name], ':') > 0) {
            list($rp_id, $rp_key) = array_map('wc_clean', explode(':', wp_unslash($_COOKIE[$cookie_name]), 2));
            $userdata = get_userdata(absint($rp_id));
            $rp_login = $userdata ? $userdata->user_login : '';
            $user = WC_Shortcode_My_Account::check_password_reset_key($rp_key, $rp_login);

            if ($user instanceof WP_User) {
                wc_get_template('myaccount/form-reset-password.php', [
                    'key' => $rp_key,
                    'login' => $rp_login,
                ]);
                return ob_get_clean();
            }

            // Si la key no es válida, cae al formulario de lost password
            wc_add_notice(__('The reset link is invalid or has expired. Please request a new one.', 'your-td'), 'error');
        }
    }

    // 3) Por defecto, formulario de lost password
    wc_get_template('myaccount/form-lost-password.php', ['form' => 'lost_password']);

    return ob_get_clean();
}

function mt_lostpassword_url(string $default_url = ''): string
{
    if (!did_action('init') || did_action('login_form_login')) {
        return $default_url;
    }

    // Si usas multisite y detectas redirect_to al network admin, respeta el default
    if (is_multisite() && isset($_GET['redirect_to']) && false !== strpos(wp_unslash($_GET['redirect_to']), network_admin_url())) {
        return $default_url;
    }

    // Manda todo a tu página
    return home_url('/auth/lost-password/');
}

function mt_redirect_reset_password_link()
{
    $current_path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

    if ($current_path === 'auth/lost-password' && isset($_GET['key']) && (isset($_GET['id']) || isset($_GET['login']))) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        // If available, get $user_id from query string parameter for fallback purposes.
        if (isset($_GET['login'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $user = get_user_by('login', sanitize_user(wp_unslash($_GET['login']))); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $user_id = $user ? $user->ID : 0;
        } else {
            $user_id = absint($_GET['id']); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        }

        // If the reset token is not for the current user, ignore the reset request (don't redirect).
        $logged_in_user_id = get_current_user_id();
        if ($logged_in_user_id && $logged_in_user_id !== $user_id) {
            wc_add_notice(__('This password reset key is for a different user account. Please log out and try again.', 'woocommerce'), 'error');
            return;
        }

        $action = isset($_GET['action']) ? sanitize_text_field(wp_unslash($_GET['action'])) : '';
        $value = sprintf('%d:%s', $user_id, wp_unslash($_GET['key'])); // phpcs:ignore
        WC_Shortcode_My_Account::set_reset_password_cookie($value);

        wp_safe_redirect(
            add_query_arg(
                array(
                    'show-reset-form' => 'true',
                    'action' => $action,
                ),
                mt_lostpassword_url()
            )
        );
        exit;
    }
}

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

            $redirect = add_query_arg('reset-pass', 'success', home_url('/auth/login'));
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