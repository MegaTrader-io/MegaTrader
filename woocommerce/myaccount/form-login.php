<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.9.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$show_login = (bool)apply_filters('woocommerce_login_form_enabled', true);
$show_register = (get_option('woocommerce_enable_myaccount_registration', 'no') === 'yes');
if ($show_login) $show_register = false;

if ($show_login) {
    ob_start();
    wc_get_template('myaccount/partials/sign-in-inner.php');
    $slot = ob_get_clean();

    wc_get_template('myaccount/partials/auth-layout.php', [
            'content' => $slot,
    ]);
}

if ($show_register) {
    ob_start();
    wc_get_template('myaccount/partials/sign-up-inner.php');
    $slot = ob_get_clean();

    wc_get_template('myaccount/partials/auth-layout.php', [
            'content' => $slot,
    ]);
}