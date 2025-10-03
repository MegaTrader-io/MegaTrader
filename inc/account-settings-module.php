<?php

if (!defined('ABSPATH')) exit;

function mt_account_settings_module()
{
    $js_path = get_template_directory() . '/assets/js/';
    $js_uri = get_template_directory_uri() . '/assets/js/';
    $js_file = 'account-settings.js';

    $js_version = file_exists($js_path . $js_file)
        ? filemtime($js_path . $js_file)
        : false;

    if (function_exists('mt_intl_tel_input_assets')) {
        mt_intl_tel_input_assets();
    }

    $deps = [];

    wp_register_script(
        'account-settings-module',
        $js_uri . $js_file,
        $deps,
        $js_version,
        true
    );

    wp_enqueue_script('account-settings-module');
}

add_action('wp_enqueue_scripts', function () {
    if (is_user_logged_in() && is_page_template('account-profile.php')) {
        mt_account_settings_module();
    }
});

function mt_process_billing_form()
{
    if (!isset($_POST['mt_save_billing'])) {
        return;
    }

    if (!isset($_POST['mt_billing_nonce']) || !wp_verify_nonce($_POST['mt_billing_nonce'], 'mt_save_billing_address')) {
        wc_add_notice(__('Security check failed. Please try again.', 'woocommerce'), 'error');
        return;
    }

    $user_id = get_current_user_id();
    if (!$user_id) {
        wc_add_notice(__('You must be logged in to update your information.', 'woocommerce'), 'error');
        return;
    }

    $required = ['billing_first_name', 'billing_last_name', 'billing_address_1', 'billing_city', 'billing_state', 'billing_postcode', 'billing_phone', 'billing_country'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            wc_add_notice(__("This field is required", 'your-td'), 'error', ['field' => $field]);;
        }
    }

    if (!wc_notice_count('error')) {
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

        wc_add_notice(__('Great! Your personal information have been updated successfully', 'woocommerce'), 'success');

        wp_safe_redirect(wc_get_account_endpoint_url('profile'));
        exit;
    }
}

add_action('wp', 'mt_process_billing_form');