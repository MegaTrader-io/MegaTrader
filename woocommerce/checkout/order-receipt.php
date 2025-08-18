<?php
/**
 * Order Receipt Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/order-receipt.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.2.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_thankyou', $order->get_id() );

?>
<h2><?php esc_html_e( 'Order details', 'woocommerce' ); ?></h2>

<table class="shop_table order_details">
	<thead>
		<tr>
			<th class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
			<th class="product-total"><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ( $order->get_items() as $item_id => $item ) {
			$product = $item->get_product();

			wc_get_template(
				'checkout/thankyou-item.php',
				array(
					'order'   => $order,
					'item_id' => $item_id,
					'item'    => $item,
					'product' => $product,
				)
			);
		}
		?>
	</tbody>
	<tfoot>
		<?php
		foreach ( $order->get_order_item_totals() as $key => $total ) {
			?>
			<tr>
				<th scope="row"><?php echo esc_html( $total['label'] ); ?></th>
				<td><?php echo wp_kses_post( $total['value'] ); ?></td>
			</tr>
			<?php
		}
		?>
	</tfoot>
</table>

<?php
if ( $order->get_customer_note() ) {
	?>
	<h2><?php esc_html_e( 'Customer note', 'woocommerce' ); ?></h2>
	<p><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></p>
	<?php
}

do_action( 'woocommerce_thankyou', $order->get_id() );

// Add this line to display the payment methods
wc_get_template( 'checkout/payment.php' );
?>
