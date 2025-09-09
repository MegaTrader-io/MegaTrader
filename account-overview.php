<?php
/**
 * Template: Account Overview (parent)
 * - Usa helpers en /inc (MT_Api, MT_Accounts)
 * - Hace UNA llamada a la API
 * - Prepara payload y lo pasa al partial: template-parts/account/account-selection.php
 */
defined('ABSPATH') || exit;

get_header();

// Estado
$mt_has_active_account = false;
$mt_user_email         = '';
$mt_account_ui         = ['current' => null, 'accounts' => []];

// Fetch + preparar payload
if ( is_user_logged_in() ) {
    $u = wp_get_current_user();
    if ( $u && $u->exists() ) {
        $mt_user_email = $u->user_email;

        if ( class_exists('MT_Api') && class_exists('MT_Accounts') ) {
            $accounts = MT_Api::fetch_accounts_by_email( $mt_user_email, 1, 50 );

            foreach ( (array) $accounts as $acc ) {
                if ( MT_Accounts::is_active_status( $acc['status'] ?? '' ) ) {
                    $mt_has_active_account = true;
                    break;
                }
            }
            if ( $mt_has_active_account ) {
                $mt_account_ui = MT_Accounts::prepare_ui( (array) $accounts );
            }
        }
    }
}

// Exponer (opc.)
$GLOBALS['mt_has_active_account'] = $mt_has_active_account;
$GLOBALS['mt_user_email']         = $mt_user_email;
$GLOBALS['mt_account_ui']         = $mt_account_ui;
?>

<div class="container">
  <div class="mt-page">
    <div class="mt-page__sidebar">
      <?php render_sidebar(); ?>
    </div>
    <div class="mt-page__main">
      <?php get_template_part('template-parts/account/no-order'); ?>

      <div class="mt-account-test">
        <?php
        get_template_part(
          'template-parts/account/account-selection',
          null,
          ['prepared' => $mt_account_ui]
        );
        ?>
      </div>
    </div>
  </div>
</div>

<?php get_footer(); ?>
