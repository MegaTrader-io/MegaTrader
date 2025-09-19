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
$mt_feature_content = []; // payload para account-feature-content
$mt_account_data = []; // payload para account-data


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


      // === Resolver la cuenta una sola vez y construir payloads ===
      $resolved = (!empty($mt_selected_id) && function_exists('mt_accounts_resolve_account_by_id'))
        ? mt_accounts_resolve_account_by_id($mt_selected_id)
        : null;

      if ($resolved) {
        // Performance
        if (function_exists('mt_accounts_build_performance')) {
          $mt_performance = mt_accounts_build_performance($resolved);
        }

        // Feature Content (account + apiData listo para el foreach del template)
        $mt_feature_content['account'] = $resolved;
        if (function_exists('mt_accounts_build_feature_content')) {
          $mt_feature_content['apiData'] = mt_accounts_build_feature_content($resolved);
        }

        if (!empty($resolved) && function_exists('mt_accounts_build_performance_chart')) {
          $mt_chart = mt_accounts_build_performance_chart($resolved);
        }

        if (
          !empty($mt_selected_id)
          && function_exists('mt_accounts_resolve_account_by_id')
          && function_exists('mt_accounts_build_account_data')
        ) {
          $acc3 = mt_accounts_resolve_account_by_id($mt_selected_id);
          if ($acc3)
            $mt_account_data = mt_accounts_build_account_data($acc3);
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
$GLOBALS['mt_feature_content'] = $mt_feature_content;
$GLOBALS['mt_account_data'] = $mt_account_data;
$GLOBALS['mt_chart'] = $mt_chart ?? [];
?>

<div id="mt-account-overview" class="container" data-email="<?php echo esc_attr($mt_user_email); ?>"
  data-email-api="<?php echo esc_attr($mt_user_email_api); ?>">

  <div class="mt-page">
    <div class="mt-page__sidebar">
      <?php if (function_exists('render_sidebar')) {
        render_sidebar();
      } ?>
    </div>

    <div class="mt-page__main d-flex flex-column gap-3">

      <?php if (empty($mt_account_ui['accounts'])): ?>

        <div class="mt-account-no-order">
          <?php get_template_part('template-parts/account/account-no-order'); ?>
        </div>

      <?php else: ?>

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
        <div class="d-flex flex-column gap-32">
          <div class="mt-account-data" id="mt-account-data">
            <?php
            if (!empty($mt_selected_id) && !empty($mt_account_data)) {
              get_template_part(
                'template-parts/account/account-data',
                null,
                [
                  'meta' => ['accountId' => $mt_selected_id],
                  'data' => $mt_account_data,
                ]
              );
            }
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
            <?php
            if (!empty($mt_selected_id)) {
              get_template_part(
                'template-parts/account/account-feature-content',
                null,
                [
                  'meta' => ['accountId' => $mt_selected_id],
                  'feature' => $mt_feature_content,
                ]
              );
            }
            ?>
          </div>

          <div class="mt-account-performance-chart-content">
            <?php
            get_template_part(
              'template-parts/account/account-performance-chart',
              null,
              [
                'meta' => ['accountId' => $mt_selected_id],
                'chart' => $mt_chart ?? [],
              ]
            );
            ?>
          </div>

          <div class="mt-account-performance-chart-content">
            <?php
            get_template_part(
              'template-parts/account/account-daily-journal',
              null,
              [
                'meta' => ['accountId' => $mt_selected_id],
              ]
            );
            ?>
          </div>


        </div>

      </div>
    <?php endif; ?>
  </div>
</div>
<div id="mt-feedback-modal" class="mt-popover" hidden aria-hidden="true" role="dialog" aria-labelledby="mtfb-title">
  <div class="mt-modal__panel" tabindex="-1">
    <div class="mt-feedback-header align-items-center d-flex gap-2">
      
      <div id="mtfb-title" class="mtfb-title text-white text-base fw-medium flex-grow-1">
        <?php echo Label::META_ACCOUNT_OVERVIEW['account_feedback_title']; ?>
      </div>
      <button type="button" class="mt-modal__close mt-icon mt-icon_close mt-icon-white" aria-label="Close"></button>
    </div>

    <div class="mtfb-mood px-2 pt-3 pb-2 d-flex justify-content-between gap-2" role="group" aria-label="Mood 1 to 5">
      <button type="button" data-mood="1" class="mt-icon mt-icon_very-happy mt-icon-base mt-icon-md"></button>
      <button type="button" data-mood="2" class="mt-icon mt-icon_happy mt-icon-base mt-icon-md"></button>
      <button type="button" data-mood="3" class="mt-icon mt-icon_neutral mt-icon-base mt-icon-md"></button>
      <button type="button" data-mood="4" class="mt-icon mt-icon_sad mt-icon-base mt-icon-md"></button>
      <button type="button" data-mood="5" class="mt-icon mt-icon_very-sad mt-icon-base mt-icon-md"></button>
    </div>

    <div class="mtfb-q text-white text-base fw-medium py-2">
      <?php echo Label::META_ACCOUNT_OVERVIEW['account_feedback_question']; ?></div>
    <div class="mtfb-plan" role="radiogroup" aria-label="Followed plan">
      <label><input type="radio" name="mtfb-plan"
          value="1"><?php echo Label::META_ACCOUNT_OVERVIEW['account_feedback_yes']; ?></label>
      <label><input type="radio" name="mtfb-plan"
          value="0"><?php echo Label::META_ACCOUNT_OVERVIEW['account_feedback_no']; ?></label>
    </div>

    <textarea id="mtfb-note" class="mtfb-note" rows="2" maxlength="320"
      placeholder="<?php echo Label::META_ACCOUNT_OVERVIEW['account_feedback_placeholder']; ?>"
      aria-label="Daily note"></textarea>

    <div class="mtfb-actions">
      <button type="button"
        class="mtfb-save"><?php echo Label::META_ACCOUNT_OVERVIEW['account_feedback_save']; ?></button>
      <button type="button" class="mtfb-edit"
        hidden><?php echo Label::META_ACCOUNT_OVERVIEW['account_feedback_edit']; ?></button>
    </div>

    <span class="mtfb-arrow" aria-hidden="true"></span>
  </div>
</div>



<?php get_footer(); ?>