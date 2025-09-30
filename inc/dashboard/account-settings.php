<?php

if (!defined('ABSPATH')) exit;
//
//add_filter('woocommerce_account_menu_items', function ($items) {
//    $new_items = [];
//
//    $new_items['profile'] = __('Personal Information', 'woocommerce');
//    $new_items['verification'] = __('Verification', 'woocommerce');
//    $new_items['password'] = __('Password', 'woocommerce');
//    $new_items['two-factor-authentication'] = __('2FA (Two-factor-authentication)', 'woocommerce');
//
//    return $new_items;
//}, 21);

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

    // Validar campos obligatorios
    $required = ['billing_first_name', 'billing_last_name', 'billing_address_1', 'billing_city', 'billing_phone', 'billing_email'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            wc_add_notice(sprintf(__('%s is required.', 'woocommerce'), ucfirst(str_replace('_', ' ', $field))), 'error');
        }
    }

    if (!empty($_POST['billing_email']) && !is_email($_POST['billing_email'])) {
        wc_add_notice(__('Please enter a valid email address.', 'woocommerce'), 'error');
    }

    if (!wc_notice_count('error')) {
        $customer = new WC_Customer($user_id);

        $customer->set_billing_first_name(sanitize_text_field($_POST['billing_first_name']));
        $customer->set_billing_last_name(sanitize_text_field($_POST['billing_last_name']));
        $customer->set_billing_address_1(sanitize_text_field($_POST['billing_address_1']));
        $customer->set_billing_city(sanitize_text_field($_POST['billing_city']));
        $customer->set_billing_state(sanitize_text_field($_POST['billing_state']));
        $customer->set_billing_postcode(sanitize_text_field($_POST['billing_postcode']));
        $customer->set_billing_phone(sanitize_text_field($_POST['billing_phone']));
        $customer->set_billing_email(sanitize_email($_POST['billing_email']));

        $customer->save();

        wc_add_notice(__('Great! Your personal information have been updated successfully', 'woocommerce'), 'success');

        wp_safe_redirect(wc_get_account_endpoint_url('profile'));
        exit;
    }
}

add_action('wp', 'mt_process_billing_form');