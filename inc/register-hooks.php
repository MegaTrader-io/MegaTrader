<?php
if (!defined('ABSPATH')) exit;

/**
 * 1) Si NO está logueado y llegó por /register (o ?action=register),
 *    ocultamos el bloque de login para que se vea SOLO el formulario de registro.
 *    Si el registro está deshabilitado, no ocultamos el login.
 */
add_filter('woocommerce_login_form_enabled', function (bool $enabled): bool {
    $registration_enabled = get_option('woocommerce_enable_myaccount_registration', 'no') === 'yes';
    if (!$registration_enabled) {
        return $enabled; // no ocultes login si no hay registro
    }

    $is_register_endpoint = (string)get_query_var('register', '');
    $is_register_action = isset($_GET['action']) && $_GET['action'] === 'register';

    if (!is_user_logged_in() && ($is_register_endpoint !== '' || $is_register_action)) {
        return false; // apaga el bloque de login
    }
    return $enabled;
}, 10, 1);

add_action('init', function () {
    if (class_exists('WC_Form_Handler')) {
        remove_action('wp_loaded', ['WC_Form_Handler', 'process_registration'], 20);
        add_action('wp_loaded', 'mt_process_registration', 20);
    }
}, 11);

function mt_process_registration(): void
{
    // 1) Gate: solo procesa cuando viene el submit correcto + nonce válido.
    $nonce = isset($_POST['woocommerce-register-nonce'])
        ? wp_unslash($_POST['woocommerce-register-nonce'])
        : (isset($_POST['_wpnonce']) ? wp_unslash($_POST['_wpnonce']) : '');

    if (
        !isset($_POST['register']) ||
        !wp_verify_nonce($nonce, 'woocommerce-register')
    ) {
        return;
    }

    // 2) Inputs (sanitizados y tipados)
    $firstname = isset($_POST['firstname']) && is_string($_POST['firstname'])
        ? (string)sanitize_text_field($_POST['firstname']) : '';

    $lastname = isset($_POST['lastname']) && is_string($_POST['lastname'])
        ? (string)sanitize_text_field($_POST['lastname']) : '';

    $email = isset($_POST['email']) && is_string($_POST['email'])
        ? sanitize_email(wp_unslash($_POST['email'])) : '';

    $phone = isset($_POST['phone']) && is_string($_POST['phone'])
        ? trim((string)wp_unslash($_POST['phone'])) : '';

    $billing_address_1 = isset($_POST['billing_address_1']) && is_string($_POST['billing_address_1'])
        ? trim((string)sanitize_text_field($_POST['billing_address_1'])) : '';

    $billing_address_2 = isset($_POST['billing_address_2']) && is_string($_POST['billing_address_2'])
        ? trim((string)sanitize_text_field($_POST['billing_address_2'])) : '';

    $billing_country = isset($_POST['billing_country']) && is_string($_POST['billing_country'])
        ? trim((string)sanitize_text_field($_POST['billing_country'])) : '';

    $billing_state = isset($_POST['billing_state']) && is_string($_POST['billing_state'])
        ? trim((string)sanitize_text_field($_POST['billing_state'])) : '';

    $billing_city = isset($_POST['billing_city']) && is_string($_POST['billing_city'])
        ? trim((string)sanitize_text_field($_POST['billing_city'])) : '';

    $billing_postcode = isset($_POST['billing_postcode']) && is_string($_POST['billing_postcode'])
        ? trim((string)sanitize_text_field($_POST['billing_postcode'])) : '';


    $password = isset($_POST['password']) && is_string($_POST['password'])
        ? (string)$_POST['password'] : '';

    $confirm = isset($_POST['confirm_password']) && is_string($_POST['confirm_password'])
        ? (string)$_POST['confirm_password'] : '';

    $privacy_ok = isset($_POST['privacy_policy']) && (string)$_POST['privacy_policy'] === '1';

    // 3) Validaciones propias (antes de filtros de Woo)
    if ($firstname === '') {
        wc_add_notice(__('First Name is required.', 'your-td'), 'error', ['field' => 'firstname']);;
    }

    if ($lastname === '') {
        wc_add_notice(__('Last Name is required.', 'your-td'), 'error', ['field' => 'lastname']);;
    }

    if ($billing_address_1 === '') {
        wc_add_notice(__('Address is required.', 'your-td'), 'error', ['field' => 'billing_address_1']);;
    }

    if ($billing_country === '') {
        wc_add_notice(__('Country is required.', 'your-td'), 'error', ['field' => 'billing_country']);;
    }

    if ($billing_state === '') {
        wc_add_notice(__('State is required.', 'your-td'), 'error', ['field' => 'billing_state']);;
    }

    if ($billing_city === '') {
        wc_add_notice(__('City is required.', 'your-td'), 'error', ['field' => 'billing_city']);;
    }

    if ($billing_postcode === '') {
        wc_add_notice(__('Zip code is required.', 'your-td'), 'error', ['field' => 'billing_postcode']);;
    }

    if ($email === '') {
        wc_add_notice(__('Email is required.', 'your-td'), 'error', ['field' => 'email']);
    } elseif (!is_email($email)) {
        wc_add_notice(__('Enter a valid email address.', 'your-td'), 'error', ['field' => 'email']);
    }

    // Password requerido solo si Woo no lo genera automáticamente
    $generate_password = get_option('woocommerce_registration_generate_password') === 'yes';
    if (!$generate_password) {
        if ($password === '') {
            wc_add_notice(__('Password is required.', 'your-td'), 'error', ['field' => 'password']);
        } elseif (strlen($password) < 8) {
            wc_add_notice(__('Use at least 8 characters.', 'your-td'), 'error', ['field' => 'password']);
        }

        if ($confirm === '') {
            wc_add_notice(__('Please confirm your password.', 'your-td'), 'error', ['field' => 'confirm_password']);
        } elseif ($password !== $confirm) {
            wc_add_notice(__('Passwords do not match.', 'your-td'), 'error', ['field' => 'confirm_password']);
        }
    }

    if ($phone === '') {
        wc_add_notice(__('Phone number is required.', 'your-td'), 'error', ['field' => 'phone']);
    } elseif (!preg_match('/^[0-9+\-\s().]{7,}$/', $phone)) {
        wc_add_notice(__('Enter a valid phone number.', 'your-td'), 'error', ['field' => 'phone']);
    }

    if (!$privacy_ok) {
        wc_add_notice(__('You must accept the Terms and the Privacy Policy.', 'your-td'), 'error', ['field' => 'privacy_policy']);
    }

    // Si ya hay errores, no sigas
    if (wc_notice_count('error') > 0) {
        return;
    }

    // 4) Validaciones de Woo (permite a plugins/tema meter reglas)
    // Para compat: Woo espera $username (aunque no lo uses) y $password.
    $tmp_username = '';
    $validation_error = apply_filters('woocommerce_process_registration_errors', new WP_Error(), $tmp_username, $password, $email);
    if ($validation_error instanceof WP_Error && $validation_error->get_error_codes()) {
        foreach ($validation_error->get_error_codes() as $code) {
            $field = match ($code) {
                'registration-error-invalid-email',
                'registration-error-email-exists' => 'email',
                default => 'general',
            };
            foreach ($validation_error->get_error_messages($code) as $msg) {
                wc_add_notice($msg, 'error', ['field' => $field]);
            }
        }
        return;
    }

    // 5) Username a usar según ajustes de Woo
    $generate_username = get_option('woocommerce_registration_generate_username') === 'yes';
    if ($generate_username) {
        // base: parte local del email
        $username = '';
    } else {
        $username = isset($_POST['username']) && is_string($_POST['username'])
            ? sanitize_user(wp_unslash($_POST['username']), true)
            : '';
        if ($username === '') {
            // fallback sensato
            $username = strstr($email, '@', true) ?: $email;
        }
    }

    // 6) Password final a usar
    $final_password = $generate_password ? wp_generate_password() : $password;

    // 7) Crear usuario
    $new_customer = wc_create_new_customer($email, wc_clean($username), $final_password);
    if (is_wp_error($new_customer)) {
        foreach ($new_customer->get_error_codes() as $code) {
            $field = match ($code) {
                'registration-error-invalid-email',
                'registration-error-email-exists' => 'email',
                default => 'general',
            };
            foreach ($new_customer->get_error_messages($code) as $msg) {
                wc_add_notice($msg, 'error', ['field' => $field]);
            }
        }
        return;
    }

    // 8) Guardar metadatos y nombre
    // billing_phone (Woo estándar)
    update_user_meta($new_customer, 'billing_phone', $phone);
    update_user_meta($new_customer, 'billing_address_1', $billing_address_1);
    update_user_meta($new_customer, 'billing_address_2', $billing_address_2);


    update_user_meta($new_customer, 'billing_country', $billing_country);
    update_user_meta($new_customer, 'billing_state', $billing_state);
    update_user_meta($new_customer, 'billing_city', $billing_city);
    update_user_meta($new_customer, 'billing_postcode', $billing_postcode);



    wp_update_user([
        'ID' => $new_customer,
        'first_name' => $firstname,
        'last_name' => $lastname,
        'display_name' => $firstname . ' ' . $lastname,
    ]);

    // 9) Mensaje de éxito (coherente con ajustes de Woo)
    if ($generate_password) {
        wc_add_notice(__('Your account was created successfully and a password has been sent to your email address.', 'woocommerce'));
    } else {
        wc_add_notice(__('Your account was created successfully. Your login details have been sent to your email address.', 'woocommerce'));
    }

    // 10) Autologin + redirect seguro
    if (apply_filters('woocommerce_registration_auth_new_customer', true, $new_customer)) {
        wc_set_customer_auth_cookie($new_customer);
        $redirect = !empty($_POST['redirect'])
            ? wp_unslash($_POST['redirect'])
            : (wc_get_raw_referer() ?: wc_get_page_permalink('myaccount'));

        $redirect = remove_query_arg(['wc_error', 'password-reset'], $redirect);

        wp_safe_redirect(
            wp_validate_redirect(
                apply_filters('woocommerce_registration_redirect', $redirect, $new_customer),
                wc_get_page_permalink('myaccount')
            )
        );
        exit;
    }
}