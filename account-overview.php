<?php
/**
 * Template: Account Overview (parent)
 */
defined('ABSPATH') || exit;

/* === Helpers === */
if (file_exists(get_stylesheet_directory() . '/inc/mt-accounts-helpers.php')) {
  require_once get_stylesheet_directory() . '/inc/mt-accounts-helpers.php';
}

get_header();

/* === Estado base === */
$mt_user_email = '';
$mt_user_email_api = '';
$mt_account_ui = ['current' => null, 'accounts' => []];
$mt_selected_id = ''; // ID de cuenta seleccionada (query o por defecto)
$mt_performance = [];
$mt_fetch_variant = ''; // plain|encoded según el que gane
$mt_cnt_plain = 0;
$mt_cnt_encoded = 0;

/* === Usuario + email saneado === */
if (is_user_logged_in()) {
  $u = wp_get_current_user();
  if ($u && $u->exists()) {
    $raw_email = (string) ($u->user_email ?? '');

    // Saneado (lowercase, trim, validación, urlencode para API)
    $san = function_exists('mt_sanitize_email')
      ? mt_sanitize_email($raw_email)
      : [
        'ok' => true,
        'email' => strtolower(trim($raw_email)),
        'api' => rawurlencode(strtolower(trim($raw_email))),
        'error' => '',
      ];

    if (!empty($san['ok'])) {
      $mt_user_email = (string) ($san['email'] ?? ''); // plain, normalizado
      $mt_user_email_api = (string) ($san['api'] ?? '');   // encoded (%2B, %40, ...)

      /* === 1) Traer cuentas del usuario (probar plain y encoded para evitar doble encoding) === */
      $accounts_plain = [];
      $accounts_enc = [];

      try {
        // PRIMERO: email normalizado en minúsculas, SIN encoding
        $accounts_plain = class_exists('MT_Api') ? MT_Api::fetch_accounts_by_email($mt_user_email, 1, 50) : [];
      } catch (Throwable $e) {
        if (defined('WP_DEBUG') && WP_DEBUG)
          error_log('[MT][accounts_plain][EX] ' . $e->getMessage());
      }

      try {
        // SEGUNDO (fallback): email URL-encoded (por si la API espera encoding aquí)
        $accounts_enc = class_exists('MT_Api') ? MT_Api::fetch_accounts_by_email($mt_user_email_api, 1, 50) : [];
      } catch (Throwable $e) {
        if (defined('WP_DEBUG') && WP_DEBUG)
          error_log('[MT][accounts_enc][EX] ' . $e->getMessage());
      }

      $mt_cnt_plain = is_array($accounts_plain) ? count($accounts_plain) : 0;
      $mt_cnt_encoded = is_array($accounts_enc) ? count($accounts_enc) : 0;

      // Elegir variante con más resultados
      if ($mt_cnt_plain >= $mt_cnt_encoded) {
        $accounts = $accounts_plain;
        $mt_fetch_variant = 'plain';
      } else {
        $accounts = $accounts_enc;
        $mt_fetch_variant = 'encoded';
      }

      if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('[MT][email] plain=' . $mt_user_email . ' | encoded=' . $mt_user_email_api . ' | cnt_plain=' . $mt_cnt_plain . ' | cnt_enc=' . $mt_cnt_encoded . ' | variant=' . $mt_fetch_variant);
      }

      /* === 2) Preparar UI SIEMPRE (todas las cuentas; Active y no Active) === */
      if (class_exists('MT_Accounts')) {
        $mt_account_ui = MT_Accounts::prepare_ui((array) $accounts);
      }

      /* === 3) Resolver cuenta seleccionada === */
      $mt_selected_id = isset($_GET['acc']) ? sanitize_text_field((string) $_GET['acc']) : '';
      if ($mt_selected_id === '') {
        $mt_selected_id = (string) ($mt_account_ui['current']['id'] ?? '');
      }

      /* === 4) (Opcional) Performance de la seleccionada (si tus helpers lo permiten por status) === */
      if ($mt_selected_id && function_exists('mt_accounts_fetch_account_json_by_shortcode')) {
        $json = mt_accounts_fetch_account_json_by_shortcode($mt_selected_id, 1, 10);
        $account = function_exists('mt_accounts_pick_account_from_json')
          ? mt_accounts_pick_account_from_json($json, $mt_selected_id)
          : null;

        if ($account && function_exists('mt_accounts_build_performance')) {
          $mt_performance = mt_accounts_build_performance($account);
        }
      }
    } else {
      echo '<div class="mt-alert mt-alert--error">Email inválido. Actualiza tu perfil.</div>';
    }
  }
}

/* === Exponer opcionalmente en $GLOBALS === */
$GLOBALS['mt_user_email'] = $mt_user_email;
$GLOBALS['mt_account_ui'] = $mt_account_ui;
$GLOBALS['mt_selected_id'] = $mt_selected_id;
$GLOBALS['mt_performance'] = $mt_performance;
?>

<div id="mt-account-overview" class="container" data-email="<?php echo esc_attr($mt_user_email); ?>"
  data-email-api="<?php echo esc_attr($mt_user_email_api); ?>">

  <div class="mt-page">
    <div class="mt-page__sidebar">
      <?php if (function_exists('render_sidebar')) {
        render_sidebar();
      } ?>
    </div>

    <div class="mt-page__main d-flex flex-column gap-32">
      <?php get_template_part('template-parts/account/account-no-order'); ?>

      <div class="mt-account-navigation mega-navigation">
        <?php get_template_part('template-parts/account/account-navigation'); ?>
      </div>

      <div class="mt-account-selection">
        <?php
        get_template_part(
          'template-parts/account/account-selection',
          null,
          [
            'prepared' => $mt_account_ui,
            'selectedId' => $mt_selected_id,
          ]
        );
        ?>
      </div>

      <div class="mt-account-performance" id="mt-performance-container">
        <?php
        // Renderiza performance si hay payload construido (tu helper decide si aplica por status)
        if (!empty($mt_performance)) {
          get_template_part(
            'template-parts/account/account-performance',
            null,
            [
              'performance' => $mt_performance,
              'meta' => ['accountId' => $mt_selected_id],
            ]
          );
        }
        ?>
      </div>

      <div class="mt-account-feature-content">
        <?php get_template_part('template-parts/account/account-feature-content'); ?>
      </div>

      <div class="mt-account-graph-content">
        <?php get_template_part('template-parts/account/account-graph'); ?>
      </div>

      <div class="mt-account-account-daily-journal">
        <?php get_template_part('template-parts/account/account-daily-journal'); ?>
      </div>
    </div>
  </div>
</div>

<?php get_footer(); ?>