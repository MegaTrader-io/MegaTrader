<?php
/**
 * Lost password confirmation text.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/lost-password-confirmation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.9.0
 */

defined( 'ABSPATH' ) || exit;

wc_print_notice( esc_html__( 'Password reset email has been sent.', 'woocommerce' ) );
?>

<?php do_action( 'woocommerce_before_lost_password_confirmation_message' ); ?>



<div class="space authentication-area">
    <div class="container space-top">
        <div class="authentication-form">
            <p class="text-body"><?php echo esc_html( apply_filters( 'woocommerce_lost_password_confirmation_message', esc_html__( 'A password reset email has been sent to the email address on file for your account. but may take several minutes to show up in your inbox. Please wait at least 10 minutes before attempting another reset.', 'woocommerce' ) ) ); ?></p>

            <a href="/checkout/" class="link-btn text-theme">
                Back to checkout 
                <svg width="10" height="11" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9.24249 0.507328C9.6567 0.507328 9.99249 0.843114 9.99249 1.25733L9.99249 6.91418C9.99249 7.3284 9.6567 7.66418 9.24249 7.66418C8.82828 7.66418 8.49249 7.3284 8.49249 6.91418L8.49249 3.06799L1.28754 10.2729C0.994645 10.5658 0.519771 10.5658 0.226878 10.2729C-0.0660156 9.98005 -0.0660152 9.50517 0.226878 9.21228L7.43183 2.00733L3.58564 2.00733C3.17142 2.00733 2.83564 1.67154 2.83564 1.25733C2.83564 0.843114 3.17142 0.507328 3.58564 0.507328H9.24249Z" fill="#FFB34A"/>
                </svg>
            </a>
        </div>
    </div>
</div>

<?php do_action( 'woocommerce_after_lost_password_confirmation_message' ); ?>
