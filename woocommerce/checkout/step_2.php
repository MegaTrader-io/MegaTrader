<?php
/**
 * Template Name: Checkout Step 2 (New)
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!isset($checkout) && function_exists('WC')) {
    $checkout = WC()->checkout();
}
?>

<div class="container">
    <div class="mt-page">
        <div class="mt-page__main">
            <?php render_step_selector(2); ?>

            <form id="checkout-form" name="checkout" method="post"
                class="checkout woocommerce-checkout d-flex flex-column gap-32" novalidate
                action="<?php echo esc_url($checkout_url); ?>" enctype="multipart/form-data">

                <?php if ($mt_selected_id !== ''): ?>
                    <input type="hidden" name="account_id" id="mt_account_id"
                        value="<?php echo esc_attr($mt_selected_id); ?>">
                <?php endif; ?>

                <?php do_action('woocommerce_before_checkout_form', $checkout); ?>

                <div class="two-columns">
                    <div class="two-columns__col">
                        <div class="mt-login-section">
                            <?php if (!is_user_logged_in()): ?>
                                <div class="mt-card mt-card-md mb-32">
                                    <?php wc_get_template('myaccount/form-login-checkout.php'); ?>
                                </div>

                                <div class="d-flex flex-column gap-3 mb-32">
                                    <h5 class="fw-light leading-7 text-primary text-uppercase text-size-24 mb-0">
                                        <?php echo esc_html(Label::CHECKOUT_META['create_account_title']); ?>
                                    </h5>
                                    <span
                                        class="fw-normal text-a8a29e text-base"><?php echo esc_html(Label::CHECKOUT_META['create_account_subtitle']); ?></span>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="mt-card flex-row flex-wrap mt-card--login-bg justify-content-between p-32 mb-32">
                                <div class="d-flex flex-column gap-1">
                                    <?php
                                    $first = trim($checkout->get_value('billing_first_name'));
                                    $last = trim($checkout->get_value('billing_last_name'));
                                    $name = trim($first . ' ' . $last);

                                    if (empty($name) && is_user_logged_in()) {
                                        $current_user = wp_get_current_user();
                                        $name = $current_user->display_name;
                                    }
                                    ?>
                                    <span
                                        class="title-black-color fw-medium fs-4 text-truncate text-uppercase"><?php echo esc_html($name); ?></span>
                                    <span
                                        class="title-black-color fw-medium text-size-20"><?php echo esc_html($country_name); ?></span>

                                </div>
                                <div class="d-flex flex-row gap-1">
                                    <a href="<?php echo esc_url(home_url('/my-account/orders/')); ?>"
                                        title="<?php echo esc_attr(Label::CHECKOUT_META['btn_manage_subcription']); ?>"
                                        class="mt-btn mt-btn--default mt-btn--sm"><?php echo esc_html(Label::CHECKOUT_META['btn_manage_subcription']); ?></a>
                                    <a href="<?php echo esc_url(
                                        add_query_arg(
                                            'time',
                                            time(),
                                            wp_logout_url('https://megatrader.io/auth/login/')
                                        )
                                    ); ?>" class="mt-btn mt-btn--default mt-btn--sm btn-logout" title="Logout">
                                        <span class="mt-icon mt-icon-sm mt-icon_logout"></span>
                                    </a>

                                </div>
                            </div>

                        <?php endif; ?>

                        <?php
                        do_action('woocommerce_checkout_before_customer_details');
                        ?>
                        <div id="customer_details" style="display:none;">
                            <?php
                            do_action('woocommerce_checkout_billing');
                            do_action('woocommerce_checkout_shipping');
                            ?>
                        </div>
                        <?php
                        do_action('woocommerce_checkout_after_customer_details');
                        ?>

                        <div id="billing-container" class="d-flex flex-column gap-3">
                            <?php wc_get_template('checkout/form-billing-v2.php', ['checkout' => $checkout]); ?>
                        </div>
                    </div>


                    <div class="two-columns__col">
                        <div class="two-columns__col-wrapper d-flex flex-column gap-3">
                            <div class="mt-coupon-card mt-card h-auto">
                                <?php wc_get_template('checkout/review-order.php'); ?>
                            </div>
                            <?php if (function_exists('WC') && WC()->cart && !$mt_is_activation): ?>
                                <?php $has_subscription = true;
                                if (!WC()->cart->is_empty()) {
                                    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                                        $product = $cart_item['data'] ?? null;
                                        $product_id = $cart_item['product_id'] ?? 0;
                                        $wc_product = $product instanceof WC_Product ? $product : wc_get_product($product_id);
                                        $product_type = $wc_product ? $wc_product->get_type() : '';

                                        if (
                                            $product_type === 'subscription' ||
                                            $product_type === 'variable-subscription' ||
                                            $product_type === 'subscription_variation' ||
                                            (function_exists('wcs_is_subscription_product') && wcs_is_subscription_product($wc_product)) ||
                                            has_term('subscription', 'product_type', $product_id)
                                        ) {
                                            $has_subscription = true;
                                            break;
                                        }
                                    }
                                }

                                $addon_options = [];

                                $checkout_obj = WC()->checkout();
                                if ($checkout_obj) {
                                    $checkout_fields = $checkout_obj->get_checkout_fields();

                                    if (!empty($checkout_fields['add_ons']) && is_array($checkout_fields['add_ons'])) {
                                        $first_addon_field = reset($checkout_fields['add_ons']);

                                        if (!empty($first_addon_field['options']) && is_array($first_addon_field['options'])) {
                                            $addon_options = $first_addon_field['options'];
                                        }
                                    }
                                }
                                ?>

                                <?php if ($has_subscription && !empty($addon_options)): ?>
                                    <div class="mt-addons">
                                        <div class="addons-container">
                                            <div class="addons-block d-flex flex-column gap-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="mt-icon mt-icon_plus mt-icon-primary"></span>
                                                    <span class="text-white fw-bold text-base">
                                                        <?php echo esc_html(Label::CHECKOUT_META['addons_title']); ?>
                                                    </span>
                                                </div>

                                                <div class="checkout-addons">
                                                    <div class="available-info d-flex flex-column gap-3">
                                                        <?php foreach ($addon_options as $option_key => $option_data): ?>
                                                            <div class="addons-item addons-item-new d-flex gap-3 align-items-center bg-1e1e1e rounded-16px w-100 justify-content-between <?php echo esc_attr($option_key); ?>"
                                                                data-addon-key="<?php echo esc_attr($option_key); ?>">
                                                                <div class="addons-header d-flex flex-column gap-1">
                                                                    <div class="text-base text-white fw-medium">
                                                                        <?php
                                                                        $label_full = $option_data['label'];
                                                                        preg_match('/^(.*?)\s*\((.*?)\)$/', wp_strip_all_tags($label_full), $matches);
                                                                        $label_text = $matches[1] ?? wp_strip_all_tags($label_full);
                                                                        $price_html = $matches[2] ?? '';
                                                                        echo esc_html($label_text);
                                                                        ?>
                                                                    </div>
                                                                    <?php if (!empty($option_data['description'])): ?>
                                                                        <span class="text-a8a29e text-14px-line-20px fw-bold">
                                                                            <?php echo esc_html(trim($option_data['description'])); ?>
                                                                        </span>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div class="title">
                                                                    <?php
                                                                    if ($price_html) {
                                                                        echo '<div>' . esc_html($price_html) . '</div>';
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>

                                                <span class="text-a8a293 fw-medium text-14px-line-20px">
                                                    <?php echo esc_html(Label::CHECKOUT_META['addons_discalimer']); ?>
                                                </span>
                                            </div>
                                        </div>

                                    </div>
                                <?php endif; ?>

                            <?php endif; ?>

                            <div class="payment-container review-container">
                                <div class="fw-medium leading-8 text-size-20 text-white pb-3">
                                    <?php echo esc_html(Label::CHECKOUT_META['payment_title']); ?>
                                </div>
                                <div class="mt-payment-cards mt-payment-cards-v2" id="mt-payment">
                                    <?php wc_get_template('checkout/payment.php', array('checkout' => WC()->checkout())); ?>
                                </div>
                                <div class="mt-card mt-3">
                                    <div class="form-row place-order">
                                        <noscript>
                                            <?php
                                            printf(
                                                esc_html__('Since your browser does not support JavaScript, or it is disabled, please ensure you click the %1$sUpdate Totals%2$s button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce'),
                                                '<em>',
                                                '</em>'
                                            );
                                            ?>
                                            <br />
                                            <button type="submit" class="button alt"
                                                name="woocommerce_checkout_update_totals"
                                                value="<?php esc_attr_e('Update totals', 'woocommerce'); ?>">
                                                <?php esc_html_e('Update totals', 'woocommerce'); ?>
                                            </button>
                                        </noscript>
                                        <?php do_action('woocommerce_review_order_before_submit'); ?>
                                        <?php
                                        if (!isset($order_button_text)) {
                                            $order_button_text = apply_filters('woocommerce_order_button_text', __('Place order', 'woocommerce'));
                                        }
                                        echo apply_filters(
                                            'woocommerce_order_button_html',
                                            '<button type="submit" class="mega-btn-md mega-btn-primary-md w-100" name="woocommerce_checkout_place_order" value="' . esc_attr($order_button_text) . '" data-value="' . esc_attr($order_button_text) . '">' . esc_html($order_button_text) . '</button>'
                                        );
                                        ?>
                                        <div class="d-flex gap-1 align-items-center pt-3">
                                            <i class="mt-icon mt-icon_lock"></i>
                                            <span class="text-base fw-light text-a8a29e">
                                                <?php echo esc_html(Label::CHECKOUT_META['payment_disclaimer']); ?>
                                            </span>
                                        </div>
                                        <?php do_action('woocommerce_review_order_after_submit'); ?>
                                        <?php wp_nonce_field('woocommerce-process_checkout', 'woocommerce-process-checkout-nonce'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
    <?php wc_get_template('myaccount/partials/email-modal.php'); ?>
    <?php wc_get_template('myaccount/partials/otp-modal.php'); ?>
</div>