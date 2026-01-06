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
							<div class="form-group position-relative d-flex flex-row">
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
$pid = (int) ($cart_item['product_id'] ?? 0);
$vid = (int) ($cart_item['variation_id'] ?? 0);

$p_product = $pid ? wc_get_product($pid) : null;      // parent
$v_product = $vid ? wc_get_product($vid) : null;      // variation

echo '<div style="border:2px dashed #f00;padding:10px;margin:10px 0;background:#fff;">';
echo '<strong>DEBUG CART ITEM</strong><br>';

echo 'cart_item_key: <code>' . esc_html($cart_item_key) . '</code><br>';
echo 'product_id: <code>' . esc_html($pid) . '</code><br>';
echo 'variation_id: <code>' . esc_html($vid) . '</code><br>';

echo '<br><strong>cart_item["variation"]</strong><br><pre style="white-space:pre-wrap">';
print_r($cart_item['variation'] ?? []);
echo '</pre>';

echo '<br><strong>PARENT product</strong><br>';
if ($p_product) {
  echo 'type: <code>' . esc_html($p_product->get_type()) . '</code><br>';
  echo 'name: <code>' . esc_html($p_product->get_name()) . '</code><br>';
  echo 'pa_platform: <code>' . esc_html($p_product->get_attribute('pa_platform')) . '</code><br>';
  echo 'pa_account-size: <code>' . esc_html($p_product->get_attribute('pa_account-size')) . '</code><br>';
  echo 'pa_account-types: <code>' . esc_html($p_product->get_attribute('pa_account-types')) . '</code><br>';
}

echo '<br><strong>VARIATION product</strong><br>';
if ($v_product) {
  echo 'type: <code>' . esc_html($v_product->get_type()) . '</code><br>';
  echo 'name: <code>' . esc_html($v_product->get_name()) . '</code><br>';
  echo 'pa_platform: <code>' . esc_html($v_product->get_attribute('pa_platform')) . '</code><br>';
  echo 'pa_account-size: <code>' . esc_html($v_product->get_attribute('pa_account-size')) . '</code><br>';
  echo 'pa_account-types: <code>' . esc_html($v_product->get_attribute('pa_account-types')) . '</code><br>';

  echo '<br><strong>variation_attributes</strong><br><pre style="white-space:pre-wrap">';
  print_r($v_product->get_variation_attributes());
  echo '</pre>';
}

echo '</div>';
?>

							<?php
$var = $cart_item['variation'] ?? [];

// Labels bonitos (vienen bien desde atributos)
$platform_label = trim((string) $_product->get_attribute('pa_platform'));
$plan_label     = trim((string) $_product->get_attribute('pa_account-types'));

// Size: usar el slug del carrito (ej: 50k) -> 50K
$size_slug = $var['attribute_pa_account-size'] ?? '';
$size      = $size_slug ? strtoupper(trim($size_slug)) : '';

// Si tenemos lo necesario, imprimimos el formato deseado
if ($size && $plan_label && $platform_label) {
    echo esc_html($size . ' ' . $plan_label . ' - ' . $platform_label);
} else {
    // Fallback: Activation Fee / Reset Fee (como lo tenías)
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
    } elseif ($is_reset_fee) {
        echo 'Reset Fee';
    } else {
        // Último fallback: nombre real del producto
        echo esc_html($_product->get_name());
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