<?php
/**
 * Payment methods
 *
 * Shows customer payment methods on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/payment-methods.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.9.0
 */

defined( 'ABSPATH' ) || exit;

$came_from_add = isset( $_GET['from'] ) && $_GET['from'] === 'add-payment-method';

if ( $came_from_add ) {
    echo "<script>
        if (window.history.replaceState) {
            const url = new URL(window.location);
            url.searchParams.delete('from');
            window.history.replaceState({}, '', url.pathname + url.search);
        }
    </script>";
}

function render_add_payment_method_button_and_modal($came_from_add) {
	if ( WC()->payment_gateways->get_available_payment_gateways() && class_exists( 'WC_Shortcode_My_Account' ) ): 
		
		ob_start();
		wc_get_template( 'myaccount/form-add-payment-method.php' );
		$template_html = ob_get_clean();

		$modal_html = render_modal([
			'autoshow'      => $came_from_add,
			'notice'		=> true,
			'modalId'     	=> 'addNewPaymentMethodModal',
			'modalTitle'  	=> 'ADD NEW PAYMENT METHOD',
			'bodyContent' 	=> $template_html,
		]);

	?>
		 <a class="mega-btn-md mega-btn-primary-md mega-btn-add-icon-md" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addNewPaymentMethodModal">
			<?php esc_html_e( 'Add payment method', 'woocommerce' ); ?>
		</a>
		<?php echo $modal_html ?>
	<?php endif;
}

function render_update_subscription_method_modal( $args = [] ){
	$token_id = $args['token_id'];
	$modal_id = $args['modal_id'];
	$brand    = $args['brand'];
	$last4    = $args['last4'];
	$exp      = $args['exp'];
	$no_link  = $args['no_link'];;
	$yes_link = $args['yes_link'];;

	return render_modal([
		'modalType'	  => 'warning',
		'modalId'     => $modal_id,
		'modalTitle'  => 'MAKE DEFAULT',
		'bodyContent' => '<p class="d-flex flex-column gap-4"><span>Would you like to update your subscriptions to use this new payment method:</span><span>' . esc_html( $brand ) . ' ending in ' . esc_html( $last4 ) . ' (expires ' . esc_html( $exp ) . ')</span></p>',
		'footer'      => '<a class="mega-btn-md mega-btn-outline-md w-100" href="' . esc_url( $no_link ) . '">NO</a><a class="mega-btn-md mega-btn-primary-md w-100" href="' . esc_url( $yes_link ) . '">YES</a>',
	]);
}

$saved_methods = wc_get_customer_saved_methods_list( get_current_user_id() );
$has_methods = (bool) $saved_methods;
$types = wc_get_account_payment_methods_types();

$cannot_delete_last_method_with_subscriptions_modal_id = esc_attr( 'cannotDeleteLastMethodWithSubscriptionsModal' );
$cannot_delete_last_method_with_subscriptions_modal = render_modal([
	'modalType'	  => 'warning',
	'modalId'     => $cannot_delete_last_method_with_subscriptions_modal_id,
	'modalTitle'  => 'PAYMENT INFO',
	'bodyContent' => 'This payment method cannot be deleted because it is linked to an automatic subscription. Please add a payment method, before trying again.',
	'footer'      => '<a class="mega-btn-md mega-btn-outline-md w-100" href="#" data-bs-dismiss="modal">CLOSE</a>',
]);

echo $cannot_delete_last_method_with_subscriptions_modal;

do_action( 'woocommerce_before_account_payment_methods', $has_methods ); ?>

<section class="my-account-payment-methods">

	<?php if ( $has_methods ) :
		$available_gateways = WC()->payment_gateways->get_available_payment_gateways();
	 ?>

		<?php foreach ( $saved_methods as $type => $methods ) : 
			$default_token_subscription_count = 0;

			foreach ( $methods as $method ){
				$subs_count = $method['method']['subs_count'] ?? 0;
				$is_default = $method['is_default'];
				$default_token_subscription_count = $is_default ? $subs_count : $default_token_subscription_count;
			}

		?>
			<?php foreach ( $methods as $method ) :

				$gateway_id = $method['method']['gateway'] ?? null;

				/* Use filter 'woocommerce_payment_methods_list_item' for customizing methods structure */
				
				$token_id   = $method['method']['token_id'] ?? '';
				$subs_count = $method['method']['subs_count'] ?? 0;
				$gateway    = esc_attr( $method['method']['gateway'] );
				$brand      = esc_html( wc_get_credit_card_type_label( $method['method']['brand'] ) );
				$last4      = esc_html( $method['method']['last4'] );
				$exp_date   = esc_html( $method['expires'] );
				$is_default = $method['is_default'];
				$saved_cc_class = $is_default ? 'saved-cc-default' : '';

			?>
				<div 	class="saved-cc <?php echo $saved_cc_class; ?>" data-gateway="<?php echo $gateway ; ?>"
					 	data-token="<?php echo esc_attr( $token_id ) ; ?>"
					 	data-subs-count="<?php echo esc_attr( $subs_count ) ; ?>">
					<div class="saved-cc-content">
						<div class="saved-cc-body">
							<div class="saved-cc-number">
								<span class="saved-cc-brand"><?php echo "$brand"; ?></span>
								<span class="saved-cc-mask"><?php echo "••••"; ?></span>
								<span class="saved-cc-last4"><?php echo "$last4"; ?></span>
							</div>
							<div class="saved-cc-exp-date">Expires <?php echo "$exp_date"; ?><?php echo $is_default ? ' - Current payment method' : ''; ?></div>
						</div>

						<?php if ( $is_default ) : ?>
							<div class="saved-cc-badge">DEFAULT</div>
						<?php endif; ?>

						<div class="saved-cc-actions">
							<?php foreach ( $method['actions'] as $key => $action ) :
								$action_class = esc_attr( sanitize_html_class( $key ) );
								$action_url   = esc_url( $action['url'] );
								$action_label = esc_html( $action['name'] );

								$update_subscription_method_modal_id = esc_attr( 'updateSubscriptionMethodModal_' . $token_id );
								$update_subscription_method_default_link = '/my-account/mt_set_default_payment_method/' . $token_id;
								$update_subscription_method_transfer_link = $update_subscription_method_default_link . '/?transfer=true';
							?>
								<?php if ( $default_token_subscription_count > 0 && ! $is_default && $action_class === 'default') : ?>
									<a  class="mega-btn-md mega-btn-secondary-md w-100 <?php echo $action_class; ?>"
										href="javascript:void(0)"
										data-bs-toggle="modal"
										data-bs-target="#<?php echo $update_subscription_method_modal_id ?>">
										<?php echo $action_label; ?>
									</a>
									<?php echo render_update_subscription_method_modal([
										'token_id' => $token_id,
										'modal_id' => $update_subscription_method_modal_id,
										'brand'    => $brand,
										'last4'    => $last4,
										'exp'      => $exp_date,
										'no_link'  => $update_subscription_method_default_link,
										'yes_link' => $update_subscription_method_transfer_link,
									]); ?>
								<?php elseif ( $action_class === 'default') : ?>
									<a  class="mega-btn-md mega-btn-secondary-md w-100 <?php echo $action_class; ?>"
										href="<?php echo $update_subscription_method_default_link ?>">
										<?php echo $action_label; ?>
									</a>
								<?php elseif ( $action_class === 'wcs_deletion_error') : ?>
									<a  class="mega-btn-md mega-btn-secondary-md w-100"
										href="javascript:void(0)"
										data-bs-toggle="modal"
										data-bs-target="#<?php echo $cannot_delete_last_method_with_subscriptions_modal_id ?>">
										<?php echo $action_label; ?>
									</a>
								<?php else : ?>
									<a class="mega-btn-md mega-btn-secondary-md w-100  <?php echo $action_class; ?>" href="<?php echo $action_url; ?>">
										<?php echo $action_label; ?>
									</a>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					</div>
				</div>

			<?php endforeach; ?>
		<?php endforeach; ?>

		<div class="add-method order-2">
			<?php render_add_payment_method_button_and_modal($came_from_add) ?>
		</div>

	<?php else : ?>
		<div class="no-methods">
			<span class="no-methods_title">No payment methods</span>
			<span class="no-methods_description">You haven’t added any payment methods yet. Add a payment method to manage your subscription and make purchases.</span>
			<span class="no-methods_types_title">We Accept:</span>
			<span class="no-methods_types_list">
				<img class="no-methods_types_list_item -visa" src="/wp-content/uploads/2025/07/visa.svg"/>
				<img class="no-methods_types_list_item -mastercard" src="/wp-content/uploads/2025/07/mastercard.svg"/>
				<img class="no-methods_types_list_item -amex" src="/wp-content/uploads/2025/07/amex.svg"/>
				<img class="no-methods_types_list_item -discover" src="/wp-content/uploads/2025/07/discover.svg"/>
			</span>

			<?php render_add_payment_method_button_and_modal($came_from_add) ?>

		</div>
	<?php endif; ?>

	<?php do_action( 'woocommerce_after_account_payment_methods', $has_methods ); ?>

</section>
