<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

?>

<div class="page-banner-area pt-32 pb-32">
    <div class="container">
        <div class="breadcrumb-menu">
            <ul>
                <li>
                    <a href="https://checkout.megatrader.io/">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6.96481 13.2002C6.6377 13.2002 6.37465 12.9371 6.37465 12.6099C6.37465 12.2828 6.6377 12.0197 6.96481 12.0197H11.0251C11.3522 12.0197 11.6152 12.2828 11.6152 12.6099C11.6152 12.9371 11.3522 13.2002 11.0251 13.2002H6.96481ZM9.6323 1.32053C9.4502 1.26319 9.24449 1.21934 8.99494 1.18561C8.74539 1.21934 8.53967 1.26319 8.35757 1.32053C8.17546 1.37787 8.0136 1.4487 7.8551 1.52965L7.81463 1.55326C7.54822 1.6983 7.29866 1.86695 7.0019 2.06933L6.8805 2.15366C5.33261 3.20266 4.1759 4.23143 2.82023 5.43559C2.7629 5.48618 2.90117 5.36475 2.23345 5.95502C1.79505 6.34292 1.53875 6.70045 1.39037 7.0951C1.23861 7.50997 1.19477 7.99568 1.19814 8.60957C1.19814 8.98397 1.1914 9.37862 1.18465 9.78675C1.14418 11.7296 1.09698 14.0435 2.01087 15.4703C2.39869 16.0808 3.00233 16.4012 3.68354 16.58C4.42882 16.7722 5.28539 16.7958 6.07114 16.806C6.93782 16.8194 7.9158 16.8262 8.99494 16.8262C10.0741 16.8262 11.052 16.8194 11.9187 16.806C12.7045 16.7925 13.5611 16.7722 14.3063 16.58C14.9875 16.4046 15.5912 16.0808 15.979 15.4703C16.8895 14.0435 16.8423 11.7296 16.8052 9.78675C16.7985 9.37862 16.7884 8.98734 16.7917 8.60957C16.7917 7.99568 16.7513 7.50997 16.5995 7.0951C16.4545 6.69708 16.1948 6.33954 15.7564 5.95502C15.0853 5.36475 15.227 5.48618 15.1697 5.43559C13.8174 4.23143 12.6573 3.20266 11.1094 2.15366L10.988 2.06933C10.6912 1.86695 10.4417 1.6983 10.1753 1.55326C10.0066 1.45882 9.83127 1.38124 9.63568 1.32053H9.6323ZM9.07587 0.0050595C9.42322 0.0455355 9.71662 0.109622 9.98303 0.193946C10.2528 0.278271 10.4956 0.386201 10.7351 0.514375C11.0116 0.662787 11.2982 0.858421 11.6456 1.09453L11.767 1.17886C13.4026 2.2852 14.5795 3.33083 15.952 4.55185C16.3904 4.94312 16.2657 4.83181 16.5354 5.0713C17.1323 5.59749 17.4898 6.10344 17.7056 6.68697C17.9113 7.25026 17.9687 7.8574 17.9687 8.6062C17.9687 8.96037 17.9754 9.35163 17.9822 9.75976C18.026 11.8645 18.0766 14.3707 16.9738 16.0976C16.3972 17.0016 15.5473 17.4704 14.6065 17.7133C13.733 17.9393 12.7955 17.9629 11.939 17.9764C10.87 17.9933 9.88524 18 8.99832 18C8.1114 18 7.12331 17.9899 6.05766 17.9764C5.20109 17.9629 4.26695 17.9359 3.39352 17.7133C2.45264 17.4704 1.60282 17.0016 1.02615 16.0976C-0.0765949 14.374 -0.0260069 11.8645 0.0178332 9.75976C0.0245779 9.355 0.0346874 8.96374 0.0313151 8.6062C0.0313151 7.8574 0.0886544 7.24688 0.294366 6.68697C0.506822 6.10344 0.86765 5.59749 1.46455 5.0713C1.73434 4.83181 1.60956 4.94312 2.04796 4.55185C3.4205 3.33083 4.59744 2.2852 6.23301 1.17886L6.35441 1.09453C6.70176 0.858421 6.98841 0.662787 7.26494 0.514375L7.31216 0.490772C7.5381 0.372717 7.76741 0.271525 8.02034 0.193946C8.29012 0.109622 8.58689 0.0455355 8.93761 0.0050595C8.98819 -0.0016865 9.03878 -0.0016865 9.08937 0.0050595H9.07587Z" fill="#FFD78A"/>
                        </svg>Home</a>
                </li>
                <li>Checkout</li>
            </ul>
        </div>
        <div class="title-area">
            <h2 class="sec-title">CHECKOUT</h2>
            <p class="sec-text">Almost there, please fill-up all the information and get funded.</p>
        </div>

        <div class="row align-items-start position-relative">
            <div class="col-lg-6 login-height">
                
                <?php if ( ! is_user_logged_in() ) : ?>
                    <div class="authentication-form single-checkout-widget checkout-login mb-5">
                        <?php woocommerce_login_form(); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
            <div class="row">
                <div class="col-lg-6">
                    <div class="billing-details mb-2">
                        <?php
                        // Display the WooCommerce checkout form
                        do_action('woocommerce_before_checkout_form');   
                        ?>
                            <?php
                            // Display checkout fields
                            do_action('woocommerce_checkout_before_customer_details');
                            ?>
                            <div id="customer_details">
                                <?php
                                do_action('woocommerce_checkout_billing');
                                ?>
                                <?php
                                do_action('woocommerce_checkout_shipping');
                                ?>
                            </div>
                            <?php
                            do_action('woocommerce_checkout_after_customer_details');
                            ?>
                            <?php
                            // Display the order review section
                            // do_action('woocommerce_checkout_order_review');
                            ?>
                        <?php
                        do_action('woocommerce_after_checkout_form');
                        ?>
                    </div>
                </div>
                <div class="col-lg-6 adjust-margin">
                    <div class="ps-xl-4">
                        <?php
                        // Display the order review section
                        do_action('woocommerce_checkout_order_review');
                        ?>
                        <div class="your-order">
                            
                            <?php
                            // Manually include the WooCommerce payment template
                            wc_get_template( 'checkout/payment.php', array( 'checkout' => WC()->checkout() ) );
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
