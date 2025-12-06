<?php
/**
 * Checkout login form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-login.php.
 *
 * @package WooCommerce/Templates
 * @version 9.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>

<div class="custom-login-form">
    <h2 class="mb-3 text-white text-size-48"><?php esc_html_e('SIGN IN'); ?></h2>
        <div class="form-group mb-3">
            <label class="label"><?php esc_html_e('Log into your account to complete your purchase faster.', 'woocommerce'); ?></label>            
        </div>
        <div class="form-group mb-0 d-flex flex-column flex-lg-row gap-2 align-items-center">
          <a
            href="#" class="mt-btn mt-btn--secondary mt-btn--md w-100" role="button"
            data-bs-toggle="modal"
            data-bs-target="#emailModal"
            >
          <?php esc_html_e('Sign In', 'woocommerce'); ?>
          </a>
          <div class="google-signin-btn">
            <div class="googlesitekit-sign-in-with-google__frontend-output-button"><!-- Here's where googlesitekit injects btn iframe --></div>
          </div>
        </div>     
</div>


<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>