<?php
/**
 * Template: Account Overview (parent)
 * - Usa helpers en /inc (MT_Api, MT_Accounts)
 * - Hace UNA llamada a la API
 * - Pasa data a:
 *    - template-parts/account/account-selection.php  (selector de cuentas)
 *    - template-parts/account/account-performance.php (performance)
 */
defined('ABSPATH') || exit;

get_header();

// Estado inicial
$mt_has_active_account = false;
$mt_user_email         = '';
$mt_account_ui         = ['current' => null, 'accounts' => []];
$mt_performance        = [];

// Fetch + preparar payload
if ( is_user_logged_in() ) {
    $u = wp_get_current_user();
    if ( $u && $u->exists() ) {
        $mt_user_email = $u->user_email;

        if ( class_exists('MT_Api') && class_exists('MT_Accounts') ) {
            // UNA llamada a la API (puede devolver ['data'=>[]] o lista plana)
            $accounts = MT_Api::fetch_accounts_by_email( $mt_user_email, 1, 50 );

            // ¿Hay alguna activa?
            foreach ( (array) $accounts as $acc ) {
                if ( MT_Accounts::is_active_status( $acc['status'] ?? '' ) ) {
                    $mt_has_active_account = true;
                    break;
                }
            }

            // UI para el selector
            if ( $mt_has_active_account ) {
                $mt_account_ui = MT_Accounts::prepare_ui( (array) $accounts );
            }

            // Payload para "account-performance" usando el helper nuevo
            if ( function_exists('mt_accounts_prepare_performance_from_accounts') && $mt_has_active_account ) {
                $mt_performance = mt_accounts_prepare_performance_from_accounts( $accounts );
            }
        }
    }
}

// Exponer (opcional)
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
        // Account selection (usa el payload preparado)
        get_template_part(
          'template-parts/account/account-selection',
          null,
          ['prepared' => $mt_account_ui]
        );
        ?>
      </div>

      <div class="mt-account-performance">
        <?php
        // Account performance (solo si hay cuenta activa y payload)
        if ( ! empty($mt_has_active_account) && ! empty($mt_performance) ) {
          get_template_part(
            'template-parts/account/account-performance',
            null,
            ['performance' => $mt_performance]
          );
        }
        ?>
      </div>
    </div>
  </div>
</div>

<?php get_footer(); ?>
