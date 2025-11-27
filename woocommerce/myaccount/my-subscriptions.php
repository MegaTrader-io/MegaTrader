<?php
/**
 * My Subscriptions section on the My Account page
 *
 * @author   Prospress
 * @category WooCommerce Subscriptions/Templates
 * @version  7.2.0 - Migrated from WooCommerce Subscriptions v2.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'get_subscription_status_date_data' ) ) {
	function get_subscription_status_date_data( WC_Subscription $subscription ) {

		// Map statuses to labels
		$labels = Label::META_SUBSCRIPTIONS_BILLING['billing_label_by_status'];

		// Default label for anything else (active, trial, on hold → false)
		$status_label = $labels[$subscription->get_status()] ?? $labels['_default'];

		// Map statuses to timestamp keys
		$timestamp_keys = [
			'cancelled'       => 'cancelled',
			'expired'         => 'end',
			'pending-cancel'  => 'end',
		];

		// Default timestamp key
		$timestamp_key = $timestamp_keys[$subscription->get_status()] ?? 'next_payment';

		// Get timestamp safely
		$timestamp = $subscription->get_time( $timestamp_key );

		// Format date, fallback “—”
		//$date_formatted = $timestamp ? date_i18n( 'F j, Y', $timestamp ) : '—';
		$date_formatted = $timestamp ? date_i18n( 'Y-m-d', $timestamp ) : '—';

		return [
			'label'          => $status_label,
			'timestamp'      => $timestamp,
			'formatted_date' => $date_formatted,
		];
	}
}

if ( ! function_exists( 'get_subscription_card_last4' ) ) {

    /**
     * Get last 4 digits of the credit card used for a subscription (Stripe + Subscriptions).
     *
     * @param WC_Subscription $subscription
     * @return string|null
     */
    function get_subscription_card_last4( WC_Subscription $subscription ) {

        $user_id = $subscription->get_user_id();
        if ( ! $user_id ) {
            return null;
        }

        // All Stripe tokens for this customer
        $tokens = WC_Payment_Tokens::get_customer_tokens( $user_id, 'stripe' );
        if ( empty( $tokens ) ) {
            return null;
        }

        // Subscription ID we want to match
        $subscription_id = $subscription->get_id();

        // Find the token that belongs to THIS subscription
        foreach ( $tokens as $token ) {

            // Subscriptions linked to this token
            $subs = WCS_Payment_Tokens::get_subscriptions_from_token( $token );

            foreach ( $subs as $sub ) {
                if ( $sub->get_id() === $subscription_id ) {
                    // We found the matching token
                    return $token->get_last4();
                }
            }
        }

        // No linked token found
        return null;
    }
}

if ( ! function_exists( 'get_subscription_related_orders_data' ) ) {
    /**
     * Returns an array of related orders for a subscription
     * in a consistent, formatted structure.
     *
     * @param WC_Subscription $subscription
     * @return array
     */
    function get_subscription_related_orders_data( $subscription ) {
        if ( ! $subscription instanceof WC_Subscription ) {
            return [];
        }

        $orders = $subscription->get_related_orders(); // returns [order_id => type]
        $result = [];

        foreach ( $orders as $order_id => $type ) {
            $order = wc_get_order( $order_id );
            if ( ! $order ) continue;

            $timestamp = $order->get_date_created()
                ? $order->get_date_created()->getTimestamp()
                : null;

            $result[] = [
                'date'         		=> $timestamp ? date('Y-m-d', $timestamp) : '—',
                'transaction_id'	=> $order->get_order_number(),
                'amount'       		=> wc_price( $order->get_total(), ['currency' => $order->get_currency()] ),
                'status'      		=> wc_get_order_status_name( $order->get_status() ),
				'status_slug'		=> $order->get_status(),
            ];
        }

        return $result;
    }
}



?>

<?php if(isset( $_GET['v2'] )): ?>

<?php if ( ! empty( $subscriptions ) ) : 
	$accordion_id = 'mt-subscriptions-view';
?>
	<div class="d-flex flex-column gap-3" id="<?= $accordion_id ?>">
	<?php foreach ( $subscriptions as $subscription_idx => $subscription ) :
		
		$related_orders_2 = array_keys($subscription->get_related_orders());
		$order_id = end($related_orders_2);
		$order = wc_get_order($order_id);
		$order_number = $order->get_order_number();

		$item = current($subscription->get_items());
		$product = $item ? $item->get_product() : null;
		$product_name = $product ? $product->get_name() : '';

		// Get Product Size (Slug/Name)
		$product_size = $product ? $product->get_attribute('pa_account-size') : '';
		$all_size_terms = get_terms([
			'taxonomy' => 'pa_account-size',
			'hide_empty' => false,
		]);
		$matched_term = null;
		foreach ($all_size_terms as $term) {
			if ($term->name === $product_size) {
				$matched_term = $term;
				break;
			}
		}
		$size_name = $matched_term ? $matched_term->name : '';
		$size_slug = $matched_term ? $matched_term->slug : '';

		// Get Platform (Logo/Name)
		$platform_value = $item ? $item->get_meta('pa_platform') : '';
		$platform_term = get_term_by('slug', $platform_value, 'pa_platform');
		$platform_meta = $platform_term ? get_term_meta($platform_term->term_id) : [];
		$image_id = $platform_meta['attribute_image_id'][0] ?? null;
		$platform_logo = $image_id ? wp_get_attachment_url($image_id) : '';
		$platform_name = $platform_term->name ?? ucfirst($platform_value);

		$title = [
			'platform_logo' => $platform_logo,
			'platform_name' => $platform_name,
			'product_name'	=> $product_name,
			'product_size'  => [
				'slug'		=> $size_slug,
				'name'		=> $size_name,
			],
		];

		echo '<script>console.log("product ",' . wp_json_encode( $product-> get_data() ) . ');</script>';
		
		// Get Price
		$subtotal_raw = $item ? $item->get_subtotal() : 0;
		$formatted_price = number_format((float) $subtotal_raw, 2, ',', ''); // "49,99"
		
		$subscription_id = $subscription->get_id();
		$subscription_status = $subscription->get_status();
		$payment_method = $subscription->get_payment_method_title();
		$start_date = $subscription->get_time('start') ? date_i18n( 'Y-m-d', $subscription->get_time('start') ) : '-';
		$billing_date = get_subscription_status_date_data($subscription);
		$cc_last4 = get_subscription_card_last4($subscription);
		$cc_last4 = $cc_last4 ? 'XXXX ' . $cc_last4 : '—';
		$related_orders = get_subscription_related_orders_data($subscription);
	?>
		<?php get_template_part('template-parts/subscriptions/subscription-card', null, [
			'subscription' => $subscription,
			'mt_id' => '—', //$order_number,
			'accordion_id' => $accordion_id,
			'subscription_id' => $subscription_id,
			'subscription_status' => $subscription_status,
			'title' => $title,
			'details_list' => [
				[
					'label' => 'Subscription ID',
					'value' => $subscription_id,
				],
				[
					'label' => 'Payment Method',
					'value' => $payment_method,
				],
				[
					'label' => 'Start Date',
					'value' => $start_date,
				],
				[
					'label' => $billing_date['label'],
					'value' => $billing_date['formatted_date'],
				],
				[
					'label' => 'Card Number',
					'value' => $cc_last4,
				],
				[
					'label' 		=> 'Price',
					'value' 		=> $formatted_price,
					'value-class'	=> 'text-primary',
				],
			],
			'related_orders' => $related_orders
		]); ?>
	<?php endforeach; ?>
	</div>
<?php else : ?>
	<p class="no_subscriptions woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
		<?php if ( 1 < $current_page ) :
			printf( esc_html__( 'You have reached the end of subscriptions. Go to the %sfirst page%s.', 'woocommerce-subscriptions' ), '<a href="' . esc_url( wc_get_endpoint_url( 'subscriptions', 1 ) ) . '">', '</a>' );
		else :
			esc_html_e( 'You have no active subscriptions.', 'woocommerce-subscriptions' );
			?>
			<a class="woocommerce-Button button" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
				<?php esc_html_e( 'Browse products', 'woocommerce-subscriptions' ); ?>
			</a>
		<?php
	endif; ?>
	</p>

<?php endif; ?>

<?php else: ?>
<div class="woocommerce_account_subscriptions">

	<?php if ( ! empty( $subscriptions ) ) : ?>
		<table class="my_account_subscriptions my_account_orders woocommerce-orders-table woocommerce-MyAccount-subscriptions shop_table shop_table_responsive woocommerce-orders-table--subscriptions">

		<thead>
			<tr>
				<th class="subscription-id order-number woocommerce-orders-table__header woocommerce-orders-table__header-order-number woocommerce-orders-table__header-subscription-id"><span class="nobr"><?php esc_html_e( 'ID', 'woocommerce-subscriptions' ); ?></span></th>

				<th class="subscription-next-payment woocommerce-orders-table__header-product-name woocommerce-orders-table__header-subscription-product-name"><span class="nobr"><?php echo esc_html_x( 'Challenge Name', 'table heading', 'woocommerce-subscriptions' ); ?></span></th>

				<th class="subscription-next-payment order-date woocommerce-orders-table__header woocommerce-orders-table__header-order-date woocommerce-orders-table__header-subscription-next-payment"><span class="nobr"><?php echo esc_html_x( 'Next payment', 'table heading', 'woocommerce-subscriptions' ); ?></span></th>

				<th class="subscription-total order-total woocommerce-orders-table__header woocommerce-orders-table__header-order-total woocommerce-orders-table__header-subscription-total"><span class="nobr"><?php echo esc_html_x( 'Amount', 'table heading', 'woocommerce-subscriptions' ); ?></span></th>
				<th class="subscription-status order-status woocommerce-orders-table__header woocommerce-orders-table__header-order-status woocommerce-orders-table__header-subscription-status"><span class="nobr"><?php esc_html_e( 'Status', 'woocommerce-subscriptions' ); ?></span></th>
				<th class="subscription-actions order-actions woocommerce-orders-table__header woocommerce-orders-table__header-order-actions woocommerce-orders-table__header-subscription-actions">&nbsp;</th>
			</tr>
		</thead>

		<tbody>
		<?php /** @var WC_Subscription $subscription */ ?>
		<?php foreach ( $subscriptions as $subscription_id => $subscription ) : ?>
			<tr class="order woocommerce-orders-table__row woocommerce-orders-table__row--status-<?php echo esc_attr( $subscription->get_status() ); ?>">
				<td class="subscription-id order-number woocommerce-orders-table__cell woocommerce-orders-table__cell-subscription-id woocommerce-orders-table__cell-order-number" data-title="<?php esc_attr_e( 'ID', 'woocommerce-subscriptions' ); ?>">
					<?php // translators: placeholder is a subscription number. ?>
					<a href="<?php echo esc_url( $subscription->get_view_order_url() ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View subscription number %s', 'woocommerce-subscriptions' ), $subscription->get_order_number() ) ) ?>">
						<?php echo esc_html( sprintf( _x( '#%s', 'hash before order number', 'woocommerce-subscriptions' ), $subscription->get_order_number() ) ); ?>
					</a>
					<?php do_action( 'woocommerce_my_subscriptions_after_subscription_id', $subscription ); ?>
				</td>

				<td class="subscription-product-name woocommerce-orders-table__cell woocommerce-orders-table__cell-subscription-product-name woocommerce-orders-table__cell-product-name" data-title="<?php echo esc_attr_x( 'Challenge name', 'table heading', 'woocommerce-subscriptions' ); ?>">
					<?php
					$items = $subscription->get_items();
					foreach ( $items as $item_id => $item ) {
						$_product = $item->get_product();
						$product_cats = strip_tags(wc_get_product_category_list($_product->get_id(), ', '));
						$product_tags = strip_tags(wc_get_product_tag_list($_product->get_id(), ', '));
						
						$replacements = [
							'$50k'  => '$50,000',
							'$100k' => '$100,000',
							'$150k' => '$150,000'
						];
						
						$product_tags = str_replace(
							array_keys($replacements), 
							array_values($replacements), 
							$product_tags
						);
						
						echo $_product->get_attribute('pa_account-size')  . ' - '  . $_product->get_attribute('pa_platform');
					}
					?>
				</td>

				<td class="subscription-next-payment order-date woocommerce-orders-table__cell woocommerce-orders-table__cell-subscription-next-payment woocommerce-orders-table__cell-order-date" data-title="<?php echo esc_attr_x( 'Next Payment', 'table heading', 'woocommerce-subscriptions' ); ?>">
					<?php echo esc_attr( $subscription->get_date_to_display( 'next_payment' ) ); ?>
					<?php if ( ! $subscription->is_manual() && $subscription->has_status( 'active' ) && $subscription->get_time( 'next_payment' ) > 0 ) : ?>
					<br/><small><?php echo esc_attr( $subscription->get_payment_method_to_display( 'customer' ) ); ?></small>
					<?php endif; ?>
				</td>
				<td class="subscription-total order-total woocommerce-orders-table__cell woocommerce-orders-table__cell-subscription-total woocommerce-orders-table__cell-order-total" data-title="<?php echo esc_attr_x( 'Total', 'Used in data attribute. Escaped', 'woocommerce-subscriptions' ); ?>">
					<?php echo wp_kses_post( $subscription->get_formatted_order_total() ); ?>
				</td>
				<td class="subscription-status order-status woocommerce-orders-table__cell woocommerce-orders-table__cell-subscription-status woocommerce-orders-table__cell-order-status" data-title="<?php esc_attr_e( 'Status', 'woocommerce-subscriptions' ); ?>">
					<?php
					$status_label = wcs_get_subscription_status_name( $subscription->get_status() );
					if ( $subscription->get_status() === 'pending-cancel' ) {
						$status_label = 'Cancelling';
						}
					?>
					<span><?php echo esc_html( $status_label ); ?></span>
				</td>
				<td class="subscription-actions order-actions woocommerce-orders-table__cell woocommerce-orders-table__cell-subscription-actions woocommerce-orders-table__cell-order-actions">
					<a href="<?php echo esc_url( $subscription->get_view_order_url() ) ?>" class="woocommerce-button button view"><?php echo esc_html_x( 'View', 'view a subscription', 'woocommerce-subscriptions' ); ?></a>
					<?php do_action( 'woocommerce_my_subscriptions_actions', $subscription ); ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>

		</table>
		<?php if ( 1 < $max_num_pages ) : ?>
			<div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">
			<?php if ( 1 !== $current_page ) : ?>
				<a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button" href="<?php echo esc_url( wc_get_endpoint_url( 'subscriptions', $current_page - 1 ) ); ?>"><?php esc_html_e( 'Previous', 'woocommerce-subscriptions' ); ?></a>
			<?php endif; ?>

			<?php if ( intval( $max_num_pages ) !== $current_page ) : ?>
				<a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button" href="<?php echo esc_url( wc_get_endpoint_url( 'subscriptions', $current_page + 1 ) ); ?>"><?php esc_html_e( 'Next', 'woocommerce-subscriptions' ); ?></a>
			<?php endif; ?>
			</div>
		<?php endif; ?>
	<?php else : ?>
		<p class="no_subscriptions woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
			<?php if ( 1 < $current_page ) :
				printf( esc_html__( 'You have reached the end of subscriptions. Go to the %sfirst page%s.', 'woocommerce-subscriptions' ), '<a href="' . esc_url( wc_get_endpoint_url( 'subscriptions', 1 ) ) . '">', '</a>' );
			else :
				esc_html_e( 'You have no active subscriptions.', 'woocommerce-subscriptions' );
				?>
				<a class="woocommerce-Button button" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
					<?php esc_html_e( 'Browse products', 'woocommerce-subscriptions' ); ?>
				</a>
			<?php
		endif; ?>
		</p>

	<?php endif; ?>

</div>

<?php endif; ?>