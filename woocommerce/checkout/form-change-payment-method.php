<?php
/**
 * Pay for order form displayed after a customer has clicked the "Change Payment method" button
 * next to a subscription on their My Account page.
 *
 * @package WooCommerce/Templates
 * @version 1.0.0 - Migrated from WooCommerce Subscriptions v2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_url  = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http" );
$current_url .= "://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

?>
<form id="order_review" class="d-flex flex-column gap-32 text-start" data-sub="<?php echo esc_attr($subscription); ?>"
	  method="post"
	  action="<?php echo esc_url($current_url); ?>" >
	<?php
		$user_id    = get_current_user_id();
		$tokens     = WC_Payment_Tokens::get_customer_tokens( $user_id, 'stripe' );
		$subscription_id = absint( $_GET['change_payment_method'] ); // the subscription we care about
		$linked_token = null;

		foreach ( $tokens as $token ) {
			$subscriptions = WCS_Payment_Tokens::get_subscriptions_from_token( $token );

			foreach ( $subscriptions as $sub ) {
				if ( $sub->get_id() === $subscription_id ) {
					$linked_token = $token;
					break 2; // stop outer loop too
				}
			}
		}

		if ( $linked_token ):
			global $current_subscription_token_id;
			$current_subscription_token_id = $linked_token->get_id();
			$token 		= $linked_token;
			$brand_raw  = $token->get_card_type(); 
			$brand 		= wc_get_credit_card_type_label( $brand_raw );
			$last4      = $token->get_last4();
			$exp_date   = $token->get_expiry_month() . '/' . $token->get_expiry_year();
			$is_default = $token->is_default();
		?>
		<div class="saved-cc">
			<div class="saved-cc-content">
				<div class="saved-cc-body">
					<div class="saved-cc-number">
						<span class="saved-cc-brand"><?php echo "$brand"; ?></span>
						<span class="saved-cc-mask"><?php echo "••••"; ?></span>
						<span class="saved-cc-last4"><?php echo "$last4"; ?></span>
					</div>
					<div class="saved-cc-exp-date">Expires <?php echo "$exp_date"; ?> - Current payment method</div>
				</div>
				<?php if ( $is_default ) : ?>
					<div class="saved-cc-badge">DEFAULT</div>
				<?php endif; ?>
			</div>
		</div>
		<div class="d-flex justify-content-center align-items-center gap-3 w-100">
			<div class="flex-fill border-bottom-separator-2"></div>
			<div class="text-body">Update to new payment method</div>
			<div class="flex-fill border-bottom-separator-2"></div>
		</div>
	<?php endif ?>

	<div id="payment">
		<?php
		if ( $subscription->has_payment_gateway() ) {
			$pay_order_button_text = _x( 'Change payment method', 'text on button on checkout page', 'woocommerce-subscriptions' );
		} else {
			$pay_order_button_text = _x( 'Add payment method', 'text on button on checkout page', 'woocommerce-subscriptions' );
		}

		$pay_order_button_text     = apply_filters( 'woocommerce_change_payment_button_text', $pay_order_button_text );
		$customer_subscription_ids = WCS_Customer_Store::instance()->get_users_subscription_ids( $subscription->get_customer_id() );
		$payment_gateways_handler  = WC_Subscriptions_Core_Plugin::instance()->get_gateways_handler_class();
		$available_gateways        = WC()->payment_gateways->get_available_payment_gateways();

		if ( $available_gateways ) :
			?>
			<ul class="payment_methods methods">
				<?php

				if ( count( $available_gateways ) ) {
					current( $available_gateways )->set_current();
				}

				foreach ( $available_gateways as $gateway ) :
					$supports_payment_method_changes = WC_Subscriptions_Change_Payment_Gateway::can_update_all_subscription_payment_methods( $gateway, $subscription );
					?>
					<li class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?>">
						<input id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio" class="input-radio <?php echo $supports_payment_method_changes ? 'supports-payment-method-changes' : ''; ?>" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> data-order_button_text="<?php echo esc_attr( apply_filters( 'wcs_gateway_change_payment_button_text', $pay_order_button_text, $gateway ) ); ?>"/>
						<label for="payment_method_<?php echo esc_attr( $gateway->id ); ?>"><?php echo esc_html( $gateway->get_title() ); ?><?php echo wp_kses_post( $gateway->get_icon() ); ?></label>
						<?php
						if ( $gateway->has_fields() || $gateway->get_description() ) {
							echo '<div class="payment_box payment_method_' . esc_attr( $gateway->id ) . '">';
							$gateway->payment_fields();
							echo '</div>';
						}
						?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<div class="woocommerce-error">
				<p> <?php echo esc_html( apply_filters( 'woocommerce_no_available_payment_methods_message', __( 'Sorry, it seems no payment gateways support changing the recurring payment method. Please contact us if you require assistance or to make alternate arrangements.', 'woocommerce-subscriptions' ) ) ); ?></p>
			</div>
		<?php endif; ?>

		<?php if ( $available_gateways ) : ?>
			<?php if ( count( $customer_subscription_ids ) > 1 && $payment_gateways_handler::one_gateway_supports( 'subscription_payment_method_change_admin' ) ) : ?>
			<span class="update-all-subscriptions-payment-method-wrap">
				<?php
				// translators: $1: opening <strong> tag, $2: closing </strong> tag
				$label = sprintf( esc_html__( 'Use this payment method for %1$sall%2$s of my current subscriptions', 'woocommerce-subscriptions' ), '<strong>', '</strong>' );

				woocommerce_form_field(
					'update_all_subscriptions_payment_method',
					array(
						'type'     => 'checkbox',
						'class'    => array( 'form-row-wide' ),
						'label'    => $label,
						'required' => true, // Making the field required to help make it more prominent on the page.
						'default'  => apply_filters( 'wcs_update_all_subscriptions_payment_method_checked', true ),
					)
				);
				?>
			</span>
			<?php endif; ?>
		<div class="form-row">
			<?php wp_nonce_field( 'wcs_change_payment_method', '_wcsnonce', true, true ); ?>

			<?php do_action( 'woocommerce_subscriptions_change_payment_before_submit' ); ?>

			<div class="d-flex gap-2">
			<?php	
				echo '<button type="button" class="modal-cancel mega-btn-md mega-btn-outline-md w-100 d-none" data-bs-dismiss="modal">CANCEL</button>';
				echo wp_kses(
					apply_filters( 'woocommerce_change_payment_button_html', '<input type="submit" class="mega-btn-md mega-btn-primary-md w-100' . esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ) . '" id="place_order" value="' . esc_attr( $pay_order_button_text ) . '" data-value="' . esc_attr( $pay_order_button_text ) . '" />' ),
					array(
						'input' => array(
							'type'       => array(),
							'class'      => array(),
							'id'         => array(),
							'value'      => array(),
							'data-value' => array(),
						),
					)
				);
				?>
			</div>

			<?php do_action( 'woocommerce_subscriptions_change_payment_after_submit' ); ?>

			<input type="hidden" name="woocommerce_change_payment" value="<?php echo esc_attr( $subscription->get_id() ); ?>" />
		</div>
		<?php endif; ?>

	</div>

</form>
