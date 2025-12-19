<?php

class MT_Subscriptions_Toggle {

    /**
     * Initialize hooks.
     */
    public static function init() {
        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_scripts' ] );
    }

    /**
     * Enqueue JS on the /my-account/subscriptions/ endpoint.
     */
    public static function enqueue_scripts() {
        // Only load on "My Account > Subscriptions" page
        
        if ( ! is_account_page() || ! is_wc_endpoint_url( 'subscriptions' ) ) {
            return;
        }

        $subs_data = [];

        // Get all subscriptions for the current user
        $subscriptions = wcs_get_users_subscriptions();
        if ( empty( $subscriptions ) ) {
            return;
        }

        foreach ( $subscriptions as $subscription ) {
            $id = $subscription->get_id();

            $subs_data[ $id ] = [
                'subscription_id'        => $id,
                'ajax_url'               => WC()->ajax_url(),
                'add_payment_method_msg' => __(
                    'To enable automatic renewals for this subscription, you will first need to add a payment method.',
                    'MT'
                ) . "\n\n" . __(
                    'Would you like to add a payment method now?',
                    'MT'
                ),
                'auto_renew_nonce'       => WCS_My_Account_Auto_Renew_Toggle::can_user_toggle_auto_renewal( $subscription )
                    ? wp_create_nonce( "toggle-auto-renew-{$id}" )
                    : false,
                'add_payment_method_url' => esc_url( $subscription->get_change_payment_method_url() ),
                'has_payment_gateway'    => $subscription->has_payment_gateway() &&
                    wc_get_payment_gateway_by_order( $subscription )->supports( 'subscriptions' ),
                'is_manual'              => $subscription->get_payment_method() === 'manual' ? 'yes' : 'no',
            ];
        }

        // Enqueue your JS that handles toggles for multiple subscriptions
        wp_enqueue_script(
            'my-subscriptions-toggle',
            get_stylesheet_directory_uri() . '/assets/js/mt-subscriptions-toggle.js',
            [ 'jquery', 'jquery-blockui' ],
            '1.0',
            true
        );

        // Pass subscriptions data to the script
        wp_localize_script(
            'my-subscriptions-toggle',
            'WCSListSubscriptions',
            $subs_data
        );
    }
}
