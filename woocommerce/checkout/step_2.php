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
                        <div class="two-columns__col-wrapper d-flex flex-column gap-3">
                            <div class="mt-card mt-coupon-card h-auto">
                                <?php wc_get_template('checkout/review-order-v2.php'); ?>
                            </div>
                            <div class="mt-addons">
                                <?php if (!$mt_is_activation): ?>
                                    <div class="addons-container">
                                        <?php
                                        $has_subscription = true;
                                        if (!WC()->cart->is_empty()) {
                                            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                                                $product = $cart_item['data'];
                                                $product_id = $cart_item['product_id'];
                                                $wc_product = wc_get_product($product_id);
                                                $product_type = $product->get_type();

                                                if (
                                                    $product_type === 'subscription' ||
                                                    $product_type === 'variable-subscription' ||
                                                    $product_type === 'subscription_variation' ||
                                                    (function_exists('wcs_is_subscription_product') && wcs_is_subscription_product($product)) ||
                                                    has_term('subscription', 'product_type', $product_id)
                                                ) {
                                                    $has_subscription = true;
                                                    break;
                                                }
                                            }
                                        }
                                        ?>
                                        <?php if (function_exists('WC') && WC()->cart): ?>
                                            <?php
                                            $add_on_fields = WC()->checkout()->checkout_fields['add_ons'] ?? [];
                                            $addon_options = $add_on_fields['e0e87f1']['options'] ?? [];
                                            ?>
                                            <?php if ($has_subscription && !empty($addon_options)): ?>
                                                <div class="addons-block d-flex flex-column gap-3">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="mt-icon mt-icon_plus  mt-icon-primary"></span>
                                                        <span
                                                            class="text-white fw-bold text-base"><?php echo esc_html(Label::CHECKOUT_META['addons_title']); ?></span>
                                                    </div>
                                                    <div class="checkout-addons">
                                                        <div class="available-info d-flex flex-column gap-3">
                                                            <?php foreach ($addon_options as $option_key => $option_data): ?>
                                                                <div
                                                                    class="addons-item addons-item-new d-flex gap-3 align-items-center bg-1e1e1e rounded-16px w-100 justify-content-between <?php echo esc_attr($option_key); ?>">
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
                                                                        <?php if ($price_html)
                                                                            echo '<div>' . esc_html($price_html) . '</div>'; ?>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>


            </form>
        </div>
    </div>
</div>