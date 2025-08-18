<?php
/**
 * Show messages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/notices/notice.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $notices ) {
	return;
}

function get_notice_class( $notice ) {
    $type = isset( $notice['type'] ) ? $notice['type'] : 'info';
    return 'woocommerce-' . esc_attr( $type );
}

function maybe_generate_update_subscription_modal( $notice ) {
	if ( strpos( $notice['notice'], 'Would you like to update your subscriptions' ) === false ) {
		return null;
	}

	preg_match(
		'/use this new payment method - (.+?) ending in (\d{4}) \(expires (\d{2}\/\d{2})\)\?/',
		$notice['notice'],
		$card_matches
	);
	preg_match(
		'/<a href="([^"]+)"><strong>Yes<\/strong><\/a>/',
		$notice['notice'],
		$link_matches
	);

	if ( $card_matches && $link_matches ) {
		$brand    = $card_matches[1];
		$last4    = $card_matches[2];
		$exp      = $card_matches[3];
		$yes_link = $link_matches[1];

		$modal_html = render_modal([
			'autoshow'    => true,
			'modalType'		=> 'warning',
			'modalId'     	=> 'updateSubscriptionMethodModal',
			'modalTitle'  	=> 'Update Payment Method',
			'bodyContent' 	=> '<span>Would you like to update your subscriptions to use this new payment method:</span><span>' . esc_html( $brand ) . ' ending in ' . esc_html( $last4 ) . ' (expires ' . esc_html( $exp ) . ')',
			'footerContent'	=> '<a class="ot-btn w-100 bg-transparent text-white" href="' . esc_url( $yes_link ) . '">YES</a><a class="ot-btn w-100" href="#" data-bs-dismiss="modal">NO</a>',
		]);

		return [
			'notice' => $modal_html,
			'type'   => 'custom',
		];
	}

	return null;
}

function maybe_generate_deleted_card_reassignment_modal( $notice ) {
	if ( strpos( $notice['notice'], 'The deleted payment method was used for automatic subscription payments' ) === false ) {
		return null;
	}

	preg_match(
		'/use your (.+?) ending in (\d{4}) \(expires (\d{2}\/\d{2})\)/',
		$notice['notice'],
		$card_matches
	);

	if ( $card_matches ) {
		$brand = $card_matches[1];
		$last4 = $card_matches[2];
		$exp   = $card_matches[3];

		$body_content = '<span>Your deleted payment method was used for auto-renewals. We\'ve switched your subscription to ' .
			esc_html( $brand ) . ' ending in ' . esc_html( $last4 ) .
			' (expires ' . esc_html( $exp ) . ') to avoid payment issues.</span>';

		$modal_html = render_modal([
			'autoshow'    => true,
			'modalType'	  => 'warning',
			'modalId'     => 'subscriptionMethodUpdatedModal',
			'modalTitle'  => 'PAYMENT INFO',
			'bodyContent' => $body_content,
			'footer'      => '<a class="ot-btn w-100 bg-transparent text-white" href="#" data-bs-dismiss="modal">CLOSE</a>',
		]);

		return [
			'notice' => $modal_html,
			'type'   => 'custom',
		];
	}

	return null;
}


foreach ( $notices as $key => $notice ) {
	$custom_notice = maybe_generate_update_subscription_modal( $notice ) ?? 
				     maybe_generate_deleted_card_reassignment_modal( $notice );

	if ( $custom_notice ) {
		$notices[ $key ] = $custom_notice;
	}
}


?>

<?php foreach ( $notices as $notice ) : ?>
	<div class="<?php echo esc_attr( get_notice_class( $notice ) ); ?>"<?php echo wc_get_notice_data_attr( $notice ); ?>>
		<?php 
			echo wc_kses_notice( $notice['notice'] );
		?>
	</div>
<?php endforeach; ?>

