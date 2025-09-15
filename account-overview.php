<?php
/**
 * Template: Account Overview (parent)
 * - Usa helpers en /inc (MT_Api, MT_Accounts)
 * - Hace UNA llamada a la API para listar cuentas
 * - La cuenta seleccionada llega por ?acc=ID (o se usa la más reciente)
 * - Para performance: se llama al shortcode con el ID seleccionado
 *
 * Pasa data a:
 *   - template-parts/account/account-selection.php   (selector de cuentas)
 *   - template-parts/account/account-performance.php (métricas de la cuenta seleccionada)
 */
defined('ABSPATH') || exit;

get_header();

// Estado
$mt_has_active_account = false;
$mt_user_email         = '';
$mt_account_ui         = ['current' => null, 'accounts' => []];
$mt_performance        = [];
$mt_selected_id        = ''; // ID de la cuenta seleccionada (query o default)

// Helper: actualizar "current" del UI con un ID concreto
$set_current_by_id = function(array $ui, string $id) {
    if (empty($id) || empty($ui['accounts'])) return $ui;
    foreach ($ui['accounts'] as $a) {
        if ((string)($a['id'] ?? '') === $id) {
            // mapear modal -> current
            $ui['current'] = [
                'id'         => (string)($a['id'] ?? ''),
                'status'     => (string)($a['status'] ?? ''),
                'badgeClass' => MT_Accounts::badge_class((string)($a['status'] ?? '')),
                'size'       => (string)($a['size'] ?? ''),
                'name'       => (string)($a['name'] ?? 'Account'),
            ];
            break;
        }
    }
    return $ui;
};

// 1) Usuario logueado
if ( is_user_logged_in() ) {
    $u = wp_get_current_user();
    if ( $u && $u->exists() ) {
        $mt_user_email = $u->user_email;

        if ( class_exists('MT_Api') && class_exists('MT_Accounts') ) {
            // 2) UNA llamada a la API para listar cuentas del usuario
            $accounts = MT_Api::fetch_accounts_by_email( $mt_user_email, 1, 50 );

            // 3) ¿Hay alguna activa?
            foreach ( (array) $accounts as $acc ) {
                if ( MT_Accounts::is_active_status( $acc['status'] ?? '' ) ) {
                    $mt_has_active_account = true;
                    break;
                }
            }

            // 4) UI para el selector (si hay activas)
            if ( $mt_has_active_account ) {
                $mt_account_ui = MT_Accounts::prepare_ui( (array) $accounts );

                // 5) Resolver ID seleccionado: ?acc=... o el "current" por defecto (más reciente)
                $mt_selected_id = isset($_GET['acc']) ? sanitize_text_field((string) $_GET['acc']) : '';
                if ( $mt_selected_id === '' ) {
                    $mt_selected_id = (string)($mt_account_ui['current']['id'] ?? '');
                }

                // 6) Si el ID de query difiere del "current" por defecto, reubicar current
                if ( $mt_selected_id && $mt_selected_id !== (string)($mt_account_ui['current']['id'] ?? '') ) {
                    $mt_account_ui = $set_current_by_id($mt_account_ui, $mt_selected_id);
                }

                // 7) Performance: consumir shortcode con el ID seleccionado y armar payload
                if ( $mt_selected_id && function_exists('mt_accounts_fetch_account_json_by_shortcode') ) {
                    $json    = mt_accounts_fetch_account_json_by_shortcode($mt_selected_id, 1, 10);
                    $account = function_exists('mt_accounts_pick_account_from_json')
                        ? mt_accounts_pick_account_from_json($json, $mt_selected_id)
                        : null;

                    if ($account && function_exists('mt_accounts_build_performance')) {
                        $mt_performance = mt_accounts_build_performance($account);
                    }
                }
            }
        }
    }
}

// Exponer (opcional)
$GLOBALS['mt_has_active_account'] = $mt_has_active_account;
$GLOBALS['mt_user_email']         = $mt_user_email;
$GLOBALS['mt_account_ui']         = $mt_account_ui;
$GLOBALS['mt_selected_id']        = $mt_selected_id;
?>

<div class="container">
  <div class="mt-page">
    <div class="mt-page__sidebar">
      <?php render_sidebar(); ?>
    </div>

    <div class="mt-page__main">
      <?php get_template_part('template-parts/account/account-no-order'); ?>

      <div class="mt-account-test">
        <?php
        // Selector (pasa UI + el seleccionado actual para marcarlo)
        get_template_part(
          'template-parts/account/account-selection',
          null,
          [
            'prepared'   => $mt_account_ui,
            'selectedId' => $mt_selected_id, // ← clave para el marcado activo y para el redirect
          ]
        );
        ?>
      </div>

      <div class="mt-account-performance" id="mt-performance-container">
        <?php
        if ( ! empty($mt_has_active_account) && ! empty($mt_performance) ) {
          get_template_part(
            'template-parts/account/account-performance',
            null,
            [
              'performance' => $mt_performance,
              'meta'        => ['accountId' => $mt_selected_id],
            ]
          );
        }
        ?>
      </div>
      <div class="mt-account-feature-content">
      <?php get_template_part('template-parts/account/account-feature-content'); ?>
      </div>
    </div>
  </div>
</div>

<?php get_footer(); ?>
