<?php
/**
 * Review order table
 *
 * @package WooCommerce\Templates
 */

defined('ABSPATH') || exit;


?>

<div class="your-order">
	<table class="single-checkout-widget shop_table woocommerce-checkout-review-order-table">
		<thead>
			<tr>
				<th class="product-name"><?php esc_html_e('Product', 'woocommerce'); ?></th>
				<th class="product-total"><?php esc_html_e('Subtotal', 'woocommerce'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="2">
					<div class="apply-item">
						<div class="coupon-form">
							<div class="form-group position-relative d-flex flex-column flex-lg-row flex-md-row">
								<input type="text" name="coupon_code" placeholder="Enter coupon code"
									class="form-control" id="coupon_code" value="">
								<button type="button" name="apply_coupon" value="Apply coupon"
									class="apply-btn ot-btn bg-title">Apply</button>
							</div>
							<div class="clear"></div>
						</div>
						<hr class="border-gray">
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="text-white fw-medium text-base text-start">Order Summary</div>

				</td>
			</tr>
			<?php
			do_action('woocommerce_review_order_before_cart_contents');

			foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
				$_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);

				if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key)) {
					?>
					<tr
						class="<?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
						<td class="product-name">
							<?php
							$platform_label = $_product->get_attribute('pa_platform');
							$account_size_label = $_product->get_attribute('pa_account-size');
							$account_size = preg_replace_callback('/\$(\d{1,3}),000(?:\s.*)?/', function ($matches) {
								return intval($matches[1]) . 'k';
							}, $account_size_label);
							if ($account_size) {
								echo esc_html($platform_label . ' - ' . $account_size);
							} else {
								// echo esc_html($_product->get_name());
					
								$terms = get_the_terms($_product->get_id(), 'product_cat');
								$is_activation_fee = false;
								$is_reset_fee = false;
								if ($terms && !is_wp_error($terms)) {
									foreach ($terms as $term) {
										if ($term->slug === 'activation-fee') {
											$is_activation_fee = true;
											break;
										}
										if ($term->slug === 'reset-fee') {
											$is_reset_fee = true;
											break;
										}
									}
								}
								if ($is_activation_fee) {
									echo 'Activation Fee';
								} else if ($is_reset_fee) {
									echo 'Reset Fee';
								}
							}
							?>
						</td>
						<td class="product-total">
							<?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</td>
					</tr>
					<?php
				}
			}

			do_action('woocommerce_review_order_after_cart_contents');
			?>
		</tbody>

		<tfoot>


			<?php /* ?>
	   <tr class="cart-subtotal">
		   <th class="fw-medium"><?php esc_html_e('Subtotal', 'woocommerce'); ?></th>
		   <td><?php wc_cart_totals_subtotal_html(); ?></td>
	   </tr>
	   <?php */ ?>

			<?php foreach (WC()->cart->get_coupons() as $code => $coupon): ?>
				<tr class="cart-discount coupon-<?php echo esc_attr(sanitize_title($code)); ?>">
					<th><?php wc_cart_totals_coupon_label($coupon); ?></th>
					<td><?php wc_cart_totals_coupon_html($coupon); ?></td>
				</tr>
			<?php endforeach; ?>

			<?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()): ?>

				<?php do_action('woocommerce_review_order_before_shipping'); ?>

				<?php wc_cart_totals_shipping_html(); ?>

				<?php do_action('woocommerce_review_order_after_shipping'); ?>

			<?php endif; ?>

			<?php foreach (WC()->cart->get_fees() as $fee): ?>
				<tr class="fee">
					<th><?php echo esc_html($fee->name); ?></th>
					<td><?php wc_cart_totals_fee_html($fee); ?></td>
				</tr>
			<?php endforeach; ?>

			<?php if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax()): ?>
				<?php if ('itemized' === get_option('woocommerce_tax_total_display')): ?>
					<?php foreach (WC()->cart->get_tax_totals() as $code => $tax): // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
						<tr class="tax-rate tax-rate-<?php echo esc_attr(sanitize_title($code)); ?>">
							<th><?php echo esc_html($tax->label); ?></th>
							<td><?php echo wp_kses_post($tax->formatted_amount); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php else: ?>
					<tr class="tax-total">
						<th><?php echo esc_html(WC()->countries->tax_or_vat()); ?></th>
						<td><?php wc_cart_totals_taxes_total_html(); ?></td>
					</tr>
				<?php endif; ?>
			<?php endif; ?>

			<?php do_action('woocommerce_review_order_before_order_total'); ?>

			<tr class="order-total">
				<th class="fw-medium"><?php esc_html_e('Total', 'woocommerce'); ?></th>
				<td><?php wc_cart_totals_order_total_html(); ?></td>
			</tr>

			<?php do_action('woocommerce_review_order_after_order_total'); ?>

		</tfoot>
	</table>



</div>