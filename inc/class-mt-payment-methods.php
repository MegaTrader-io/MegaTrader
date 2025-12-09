<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'template_redirect', 'redirect_add_payment_method_to_payment_methods' );

function redirect_add_payment_method_to_payment_methods() {
    if ( is_account_page() && is_wc_endpoint_url( 'add-payment-method' ) ) {
        wp_safe_redirect( wc_get_account_endpoint_url( 'payment-methods' ) . '?from=add-payment-method' );
        exit;
    }
}

add_action( 'template_redirect', function() {
    if ( is_wc_endpoint_url( 'order-pay' ) && isset( $_GET['change_payment_method'] ) ) {

        $subscription_id = absint( $_GET['change_payment_method'] );

        $allowed_params = [ 'pay_for_order', 'key', 'change_payment_method' ];
        $params = array_intersect_key( $_GET, array_flip( $allowed_params ) );

        $query_string = http_build_query( $params );

        $new_url = home_url( "/my-account/subscriptions/" );
        if ( $query_string ) {
            $new_url .= '?v2&' . $query_string . '#' . $subscription_id;
        }

        wp_safe_redirect( $new_url, 302 );
        exit;
    }
});


class MT_Payment_Methods {
	public static function init() {
		add_action( 'init', [ __CLASS__, 'register_endpoint' ] );
		add_filter( 'woocommerce_get_query_vars', [ __CLASS__, 'add_query_var' ] );
		add_action( 'template_redirect', [ __CLASS__, 'handle_redirect' ] );
        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_scripts' ] );
	}

	public static function register_endpoint() {
		add_rewrite_endpoint( 'mt_set_default_payment_method', EP_PAGES );
	}

	public static function add_query_var( $vars ) {
		$vars['mt_set_default_payment_method'] = 'mt_set_default_payment_method';
		return $vars;
	}

    public static function enqueue_scripts() {
		//should target payment_method page and change_payment_method modal
        if ( is_account_page()) {			
            wp_enqueue_script(
                'payment-methods-script',
                get_stylesheet_directory_uri() . '/assets/js/payment-methods.js',
                ['jquery'],
                filemtime( get_stylesheet_directory() . '/assets/js/payment-methods.js' ),
                true
            );
        }
    }

	public static function handle_redirect() {
		global $wp;

		if (
			! is_user_logged_in() ||
			! isset( $wp->query_vars['mt_set_default_payment_method'] )
		) {
			return;
		}

		$payment_method_id = absint( $wp->query_vars['mt_set_default_payment_method'] );
		$transfer = isset( $_GET['transfer'] ) ? filter_var( $_GET['transfer'], FILTER_VALIDATE_BOOLEAN ) : false;

		if ( ! $payment_method_id ) {
			self::redirect_with_notice( __( 'Invalid payment method ID.', 'megatrader' ), 'error' );
		}

		$payment_methods = WC_Payment_Tokens::get_customer_tokens( get_current_user_id() );

		if ( ! isset( $payment_methods[ $payment_method_id ] ) ) {
			self::redirect_with_notice( __( 'Payment method not found.', 'megatrader' ), 'error' );
		}

		$token = $payment_methods[ $payment_method_id ];
		$token->set_default( true );

		if ( ! $token->save() ) {
			self::redirect_with_notice( __( 'Failed to set default payment method.', 'megatrader' ), 'error' );
		}

		if ( $transfer ) {
			self::transfer_subscriptions_to_token( $token, $payment_method_id );
		}

		self::redirect_with_notice( __( 'Default payment method updated.', 'megatrader' ), 'success' );
	}

	protected static function redirect_with_notice( $message, $type ) {
		wc_add_notice( $message, $type );
		wp_safe_redirect( wc_get_account_endpoint_url( 'payment-methods' ) );
		exit;
	}

	protected static function transfer_subscriptions_to_token( $token, $excluded_token_id ) {
		if ( ! class_exists( 'WCS_Payment_Tokens' ) ) {
			return;
		}

		WC()->payment_gateways();
		$tokens = WCS_Payment_Tokens::get_customer_tokens( get_current_user_id(), $token->get_gateway_id() );
		unset( $tokens[ $excluded_token_id ] );

		foreach ( $tokens as $old_token ) {
			$subscriptions = WCS_Payment_Tokens::get_subscriptions_from_token( $old_token );

			foreach ( $subscriptions as $subscription ) {
				if (
					! empty( $subscription ) &&
					WCS_Payment_Tokens::update_subscription_token( $subscription, $token, $old_token )
				) {
					$subscription->add_order_note( sprintf(
						_x(
							'Payment method meta updated after customer changed their default token and opted to update their subscriptions. Payment meta changed from %1$s to %2$s',
							'used in subscription note',
							'woocommerce-subscriptions'
						),
						$old_token->get_token(),
						$token->get_token()
					) );
				}
			}
		}
	}
}

MT_Payment_Methods::init();