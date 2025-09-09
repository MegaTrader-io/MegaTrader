<?php
/**
 * Template: Account Overview
 * - Usa helpers en /inc
 * - Hace 1 llamada a la API, prepara payload y lo pasa al partial de UI
 */
defined('ABSPATH') || exit;

// Carga helpers (o inclúyelo en functions.php y quita esta línea)
require_once get_stylesheet_directory() . '/inc/init.php';

$mt_has_active_account = false;
$mt_user_email = '';
$mt_account_ui = ['current' => null, 'accounts' => []];

if (is_user_logged_in()) {
  $u = wp_get_current_user();
  if ($u && $u->exists()) {
    $mt_user_email = $u->user_email;

    // 1 llamada al backend
    $accounts = MT_Api::fetch_accounts_by_email($mt_user_email, 1, 50);

    // ¿hay activas?
    foreach ($accounts as $acc) {
      if (MT_Accounts::is_active_status($acc['status'] ?? '')) {
        $mt_has_active_account = true;
        break;
      }
    }

    if ($mt_has_active_account) {
      $mt_account_ui = MT_Accounts::prepare_ui($accounts);
    }
  }
}

// Exponer si lo necesitas
$GLOBALS['mt_has_active_account'] = $mt_has_active_account;
$GLOBALS['mt_user_email'] = $mt_user_email;
$GLOBALS['mt_account_ui'] = $mt_account_ui;

?>
<div class="container">
  <div class="mt-page">
    <div class="mt-page__sidebar">
      <?php render_sidebar() ?>
    </div>
    <div class="mt-page__main">
      <?php get_template_part('template-parts/account/no-order') ?>
    </div>
  </div>
</div>



<div class="mt-account-test">
  <?php
  // Partial solo renderiza el UI (sin tocar API)
  get_template_part(
    'template-parts/account/account-selection',
    null,
    ['prepared' => $mt_account_ui]
  );
  ?>
</div>