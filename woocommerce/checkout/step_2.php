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
                action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">
                <?php if ($mt_selected_id !== ''): ?>
                    <input type="hidden" name="account_id" id="mt_account_id"
                        value="<?php echo esc_attr($mt_selected_id); ?>">
                <?php endif; ?>
                <div class="two-columns">
                    <div class="two-columns__col">
                        <div id="billing-container" class="d-flex flex-column gap-3">
                            <?php wc_get_template('checkout/form-billing-v2.php', ['checkout' => $checkout]); ?>
                        </div>
                    </div>
                    <div class="two-columns__col">

                    </div>
                </div>


            </form>
        </div>
    </div>
</div>