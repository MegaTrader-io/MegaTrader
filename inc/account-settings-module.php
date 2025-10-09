<?php

if (!defined('ABSPATH')) exit;

function mt_account_settings_module()
{
    $js_path = get_template_directory() . '/assets/js/';
    $js_uri = get_template_directory_uri() . '/assets/js/';
    $js_file = 'account-settings.js';
    $js_veriff_file = 'mt-veriff-validation.js';

    // ✅ Obtener versiones dinámicas para invalidar cache
    $js_version_main = file_exists($js_path . $js_file) ? filemtime($js_path . $js_file) : false;
    $js_version_veriff = file_exists($js_path . $js_veriff_file) ? filemtime($js_path . $js_veriff_file) : false;

    // ✅ Si usas intl-tel-input para campos telefónicos, lo cargas
    if (function_exists('mt_intl_tel_input_assets')) {
        mt_intl_tel_input_assets();
    }

    // ✅ Registrar SDK principal de Veriff
    wp_register_script(
        'veriff-sdk',
        'https://cdn.veriff.me/sdk/js/1.5/veriff.min.js',
        [],
        '1.5.0',
        true
    );

    // ✅ Registrar SDK “in-context” (iframe helper)
    wp_register_script(
        'veriff-incontext',
        'https://cdn.veriff.me/incontext/js/v1/veriff.js',
        ['veriff-sdk'],
        null,
        true
    );

    // ✅ Registrar tu script principal de ajustes de cuenta
    wp_register_script(
        'account-settings-module',
        $js_uri . $js_file,
        [],
        $js_version_main,
        true
    );

    // ✅ Registrar el script de validación Veriff
    wp_register_script(
        'veriff-validation-module',
        $js_uri . $js_veriff_file,
        ['veriff-incontext', 'account-settings-module'], // depende de ambos
        $js_version_veriff,
        true
    );

    // ✅ Encolar scripts en el orden correcto
    wp_enqueue_script('veriff-sdk');
    wp_enqueue_script('veriff-incontext');
    wp_enqueue_script('account-settings-module');
    wp_enqueue_script('veriff-validation-module');

    // ✅ Variables globales accesibles desde ambos JS
    $localize_data = [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'veriffKey' => VERIFF_API_KEY,
        'nonce' => wp_create_nonce('mt_veriff_nonce'),
        'isLoggedIn' => is_user_logged_in(),
    ];

    wp_localize_script('account-settings-module', 'wpAjax', $localize_data);
    wp_localize_script('veriff-validation-module', 'wpAjax', $localize_data);
}

add_action('wp_enqueue_scripts', function () {
    if (is_user_logged_in() && is_page_template('account-profile.php')) {
        mt_account_settings_module();
    }
});

if (!function_exists('mt_is_user_verified')) {
    /**
     * Check if a user is verified via Veriff.
     *
     * @param int|null $user_id Optional. User ID. Defaults to current user.
     * @return bool True if the user's Veriff status is 'approved'.
     */
    function mt_is_user_verified(?int $user_id = null): bool
    {
        // ✅ Si no se pasa un user_id, usar el usuario actual
        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        // Si no hay usuario logueado, retornar falso
        if (!$user_id) {
            return false;
        }

        // Obtener el meta de verificación
        $status = get_user_meta($user_id, 'veriff_status', true);

        // ✅ Retornar true solo si el estado es "approved"
        return $status === 'approved';
    }
}

// ✅ Endpoint AJAX seguro
add_action('wp_ajax_mt_update_password', 'mt_update_password_callback');
add_action('wp_ajax_mt_update_personal_information', 'mt_update_personal_information_callback');

function mt_update_password_callback()
{
    // ✅ Verificar login
    if (!is_user_logged_in()) {
        wp_send_json_error([
            'errors' => [
                'global' => __('You must be logged in to change your password.', 'megatrader')
            ]
        ], 403);
    }

    // ✅ Validar nonce
    if (!isset($_POST['mt_password_nonce']) || !wp_verify_nonce($_POST['mt_password_nonce'], 'mt_save_password')) {
        wp_send_json_error([
            'errors' => [
                'global' => __('Invalid security token. Please reload and try again.', 'megatrader')
            ]
        ], 400);
    }

    // ✅ Sanitizar entradas
    $current_password = sanitize_text_field($_POST['current_password'] ?? '');
    $new_password = sanitize_text_field($_POST['new_password'] ?? '');
    $confirm_password = sanitize_text_field($_POST['confirm_password'] ?? '');

    $user_id = get_current_user_id();
    $user = get_userdata($user_id);

    $errors = [];

    // ✅ Validar campos vacíos
    if (empty($current_password)) {
        $errors['current_password'] = __('This field is required', 'megatrader');
    }

    if (empty($new_password)) {
        $errors['new_password'] = __('This field is required', 'megatrader');
    }

    if (empty($confirm_password)) {
        $errors['confirm_password'] = __('This field is required', 'megatrader');
    }

    // Si faltan campos, terminamos aquí
    if (!empty($errors)) {
        wp_send_json_error(['errors' => $errors], 422);
    }

    // ✅ Verificar contraseña actual
    if (!wp_check_password($current_password, $user->user_pass, $user_id)) {
        $errors['current_password'] = __('Your current password is incorrect.', 'megatrader');
        wp_send_json_error(['errors' => $errors], 401);
    }

    // ✅ Verificar coincidencia de contraseñas
    if ($new_password !== $confirm_password) {
        $errors['confirm_password'] = __('New passwords do not match.', 'megatrader');
    }

    // ✅ Validar longitud mínima
    if (strlen($new_password) < 8) {
        $errors['new_password'] = __('The new password must be at least 8 characters long.', 'megatrader');
    }

    // Si hay errores en validaciones
    if (!empty($errors)) {
        wp_send_json_error(['errors' => $errors], 422);
    }

    // ✅ Actualizar sin cerrar sesión
    try {
        $update_result = wp_update_user([
            'ID' => $user_id,
            'user_pass' => $new_password,
        ]);

        if (is_wp_error($update_result)) {
            wp_send_json_error([
                'errors' => ['global' => $update_result->get_error_message()],
            ], 500);
        }

        wp_send_json_success([
            'message' => __('Password updated successfully.', 'megatrader'),
        ]);
    } catch (Throwable $e) {
        wp_send_json_error([
            'errors' => [
                'global' => __('Unexpected error updating password.', 'megatrader')
            ],
            'debug' => WP_DEBUG ? $e->getMessage() : null
        ], 500);
    }
}

function mt_update_personal_information_callback()
{
    if (!is_user_logged_in()) {
        wp_send_json_error([
            'errors' => [
                'global' => __('You must be logged in to change your personal information.', 'megatrader')
            ]
        ], 403);
    }

    if (!isset($_POST['mt_billing_nonce']) || !wp_verify_nonce($_POST['mt_billing_nonce'], 'mt_save_billing_address')) {
        wp_send_json_error([
            'errors' => [
                'global' => __('Security check failed. Please try again.', 'megatrader')
            ]
        ], 403);
        return;
    }

    $user_id = get_current_user_id();
    $errors = [];

    $required = ['billing_first_name', 'billing_last_name', 'billing_address_1', 'billing_city', 'billing_state', 'billing_postcode', 'billing_phone', 'billing_country'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $errors[$field] = __('This field is required', 'megatrader');
        }
    }

    if (count($errors) > 0) {
        wp_send_json_error(['errors' => $errors], 422);
    }

    $customer = new WC_Customer($user_id);

    $customer->set_billing_first_name(sanitize_text_field($_POST['billing_first_name']));
    $customer->set_billing_last_name(sanitize_text_field($_POST['billing_last_name']));
    $customer->set_billing_address_1(sanitize_text_field($_POST['billing_address_1']));
    $customer->set_billing_city(sanitize_text_field($_POST['billing_city']));
    $customer->set_billing_state(sanitize_text_field($_POST['billing_state']));
    $customer->set_billing_postcode(sanitize_text_field($_POST['billing_postcode']));
    $customer->set_billing_country(sanitize_text_field($_POST['billing_country']));
    $customer->set_billing_phone(sanitize_text_field($_POST['billing_phone_full']));

    $customer->save();

    wp_send_json_success([
        'message' => __('Great! Your personal information have been updated successfully', 'megatrader'),
    ]);
}

require_once 'veriff-module.php';
