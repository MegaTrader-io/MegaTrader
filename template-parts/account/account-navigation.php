<?php
/**
 * Template part: Account Navigation (custom)
 *
 * Items:
 *  - Trade Area (active por defecto)
 *  - Manage Subscription
 *  - Payment Method
 */

if ( ! defined('ABSPATH') ) exit;

/** Helpers */
$req_path = rtrim( parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ), '/' );

// URLs
$trade_url = trailingslashit( home_url( '/my-account/overview' ) );

// Woo Subscriptions list endpoint (fallback si no existe)
$subs_url = function_exists('wc_get_account_endpoint_url')
  ? wc_get_account_endpoint_url( 'subscriptions' )
  : trailingslashit( home_url( '/my-account/subscriptions' ) );

// Payment methods endpoint
$paym_url = function_exists('wc_get_account_endpoint_url')
  ? wc_get_account_endpoint_url( 'payment-methods' )
  : trailingslashit( home_url( '/my-account/payment-methods' ) );

// Active detection (por defecto: Trade Area)
$is_trade = ( $req_path === rtrim( parse_url( $trade_url, PHP_URL_PATH ), '/' ) );
$is_subs  = ( $req_path === rtrim( parse_url( $subs_url,  PHP_URL_PATH ), '/' ) );
$is_paym  = ( $req_path === rtrim( parse_url( $paym_url,  PHP_URL_PATH ), '/' ) );

// Si ninguno coincide, marca Trade Area
if ( ! $is_trade && ! $is_subs && ! $is_paym ) {
  $is_trade = true;
}

?>
<nav class="woocommerce-MyAccount-navigation d-none d-lg-block" aria-label="<?php esc_attr_e('Account pages','megatrader'); ?>">
  <ul class="mega-navigation-list text-capitalize">
    <li class="woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--mega-trade-area <?php echo $is_trade ? 'is-active' : ''; ?>">
      <a href="<?php echo esc_url( $trade_url ); ?>" <?php echo $is_trade ? 'aria-current="page"' : ''; ?>>
        <?php esc_html_e('Trade Area','megatrader'); ?>
      </a>
    </li>

    <li class="woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--mega-manage-subscription <?php echo $is_subs ? 'is-active' : ''; ?>">
      <a href="<?php echo esc_url( $subs_url ); ?>" <?php echo $is_subs ? 'aria-current="page"' : ''; ?>>
        <?php esc_html_e('Manage Subscription','megatrader'); ?>
      </a>
    </li>

    <li class="woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--payment-methods <?php echo $is_paym ? 'is-active' : ''; ?>">
      <a href="<?php echo esc_url( $paym_url ); ?>" <?php echo $is_paym ? 'aria-current="page"' : ''; ?>>
        <?php esc_html_e('Payment Method','megatrader'); ?>
      </a>
    </li>
  </ul>
</nav>
