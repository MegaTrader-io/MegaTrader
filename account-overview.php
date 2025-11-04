<?php
/**
 *  Template Name: Account Overview
 */
defined('ABSPATH') || exit;

/* === Helpers === */
if (file_exists(get_stylesheet_directory() . '/inc/mt-accounts-helpers.php')) {
  require_once get_stylesheet_directory() . '/inc/mt-accounts-helpers.php';
}

/* === Benchmark mínimo (sin plugins) === */
$t0 = microtime(true);
if (!function_exists('mt_bench')) {
  function mt_bench($label, $t0) {
    error_log('[MT BENCH] ' . $label . ' +' . number_format((microtime(true) - $t0) * 1000, 1) . 'ms');
  }
}
register_shutdown_function(function() use ($t0){
  // Log total al finalizar
  mt_bench('TOTAL_PAGE', $t0);
});

/* === Estado base === */
$mt_user_email = '';
$mt_user_email_api = '';
$mt_account_ui = ['current' => null, 'accounts' => []];
$mt_selected_id = '';
$mt_performance = [];
$mt_fetch_variant = '';
$mt_cnt_plain = 0;
$mt_cnt_encoded = 0;
$mt_feature_content = [];
$mt_account_data = [];
$mt_daily_journal = [];
$cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : '/cart';
$__can_manage_subscription = true;

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
      mt_bench('email_sanitized', $t0);

      /* === 1) Traer cuentas del usuario (probar plain y encoded para evitar doble encoding) === */
      $accounts_plain = [];
      $accounts_enc = [];

      try {
        $accounts_plain = class_exists('MT_Api') ? MT_Api::fetch_accounts_by_email($mt_user_email, 1, 50) : [];
      } catch (Throwable $e) {
        if (defined('WP_DEBUG') && WP_DEBUG) error_log('[MT][accounts_plain][EX] ' . $e->getMessage());
      }

      try {
        $accounts_enc = class_exists('MT_Api') ? MT_Api::fetch_accounts_by_email($mt_user_email_api, 1, 50) : [];
      } catch (Throwable $e) {
        if (defined('WP_DEBUG') && WP_DEBUG) error_log('[MT][accounts_enc][EX] ' . $e->getMessage());
      }

      $mt_cnt_plain = is_array($accounts_plain) ? count($accounts_plain) : 0;
      $mt_cnt_encoded = is_array($accounts_enc) ? count($accounts_enc) : 0;

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
      mt_bench('accounts_fetched_'.$mt_fetch_variant, $t0);

      // === Agreement Modal ===
      $__mt_agreement = (function_exists('mt_get_agreement_status_by_email') && $mt_user_email_api)
        ? mt_get_agreement_status_by_email($mt_user_email_api, 0)
        : null;

      $__mt_agreement_url = (is_array($__mt_agreement) && !empty($__mt_agreement['agreementURL']))
        ? (string) $__mt_agreement['agreementURL']
        : '';

      $__mt_agreement_show = (is_array($__mt_agreement)
        && array_key_exists('agreementSigned', $__mt_agreement)
        && $__mt_agreement['agreementSigned'] === false) ? '1' : '0';
      mt_bench('agreement_status_checked', $t0);

      /* === 2) Preparar UI SIEMPRE (todas las cuentas; Active y no Active) === */
      if (class_exists('MT_Accounts')) {
        $mt_account_ui = MT_Accounts::prepare_ui((array) $accounts);
      }
      mt_bench('prepare_ui_done', $t0);

      /* === Preferencia de cookie para cuenta seleccionada (si existe y es válida) === */
      $cookie_selected_id = '';
      if (is_user_logged_in()) {
        $uid = get_current_user_id();
        $cookie_keys = array(
          'mt:lastAccountId' . ($uid ? (':' . $uid) : ''), // nombre con sufijo uid
          'mt:lastAccountId',
        );
        foreach ($cookie_keys as $ck) {
          if (!empty($_COOKIE[$ck])) {
            $cookie_selected_id = sanitize_text_field(wp_unslash($_COOKIE[$ck]));
            break;
          }
        }
      }

      /* IDs válidos (de prepare_ui) */
      $__valid_ids = array();
      if (!empty($mt_account_ui['accounts']) && is_array($mt_account_ui['accounts'])) {
        foreach ($mt_account_ui['accounts'] as $row) {
          if (!empty($row['id'])) $__valid_ids[(string) $row['id']] = true;
        }
      }
      if (!empty($mt_account_ui['current']['id'])) {
        $__valid_ids[(string) $mt_account_ui['current']['id']] = true;
      }

      /* Resolver seleccionado con prioridad: ?acc → cookie → current */
      $mt_selected_id = '';
      $param_acc = isset($_GET['acc']) ? sanitize_text_field((string) $_GET['acc']) : '';

      if ($param_acc && isset($__valid_ids[$param_acc])) {
        $mt_selected_id = $param_acc;
      } elseif ($cookie_selected_id && isset($__valid_ids[$cookie_selected_id])) {
        $mt_selected_id = $cookie_selected_id;
      } else {
        $mt_selected_id = (string) ($mt_account_ui['current']['id'] ?? '');
      }
      mt_bench('selected_id_resolved', $t0);

      /* === 3) Resolver cuenta seleccionada === */
      if ($mt_selected_id === '') {
        $mt_selected_id = (string) ($mt_account_ui['current']['id'] ?? '');
      }

      // === Resolver la cuenta una sola vez y construir payloads ===
      $resolved = (!empty($mt_selected_id) && function_exists('mt_accounts_resolve_account_by_id'))
        ? mt_accounts_resolve_account_by_id($mt_selected_id)
        : null;
      mt_bench('account_resolved', $t0);

      if ($resolved) {
        // Performance
        if (function_exists('mt_accounts_build_performance')) {
          $mt_performance = mt_accounts_build_performance($resolved);
        }
        mt_bench('performance_payload', $t0);

        // Feature Content (account + apiData)
        $mt_feature_content['account'] = $resolved;
        if (function_exists('mt_accounts_build_feature_content')) {
          $mt_feature_content['apiData'] = mt_accounts_build_feature_content($resolved);
        }
        mt_bench('feature_payload', $t0);

        // Daily Journal payload
        if (!empty($mt_selected_id) && function_exists('mt_accounts_build_daily_journal')) {
          $mt_daily_journal = mt_accounts_build_daily_journal($mt_selected_id, 1, 30);
        }
        mt_bench('daily_journal_payload', $t0);

        // Performance Chart
        if (!empty($resolved) && function_exists('mt_accounts_build_performance_chart')) {
          $mt_chart = mt_accounts_build_performance_chart($resolved);
        }
        mt_bench('chart_payload', $t0);

        if (
          !empty($mt_selected_id)
          && function_exists('mt_accounts_resolve_account_by_id')
          && function_exists('mt_accounts_build_account_data')
        ) {
          $acc3 = mt_accounts_resolve_account_by_id($mt_selected_id);
          if ($acc3) $mt_account_data = mt_accounts_build_account_data($acc3);
        }
        mt_bench('account_data_payload', $t0);
      }

      /* === Mapa id => order y order activo === */
      $__orders_map_by_id = [];

      if (!empty($mt_account_ui['accounts']) && is_array($mt_account_ui['accounts'])) {
        foreach ($mt_account_ui['accounts'] as $row) {
          $id = (string) ($row['id'] ?? '');
          $ord = (int) ($row['order'] ?? $row['orderId'] ?? $row['orderID'] ?? 0);
          if ($id !== '') $__orders_map_by_id[$id] = $ord;
        }
      }
      if (!empty($mt_account_ui['current']['id'])) {
        $cid = (string) $mt_account_ui['current']['id'];
        if (!isset($__orders_map_by_id[$cid])) {
          $__orders_map_by_id[$cid] = (int) ($mt_account_ui['current']['order'] ?? $mt_account_ui['current']['orderId'] ?? $mt_account_ui['current']['orderID'] ?? 0);
        }
      }

      $__active_order_id = 0;
      if ($mt_selected_id !== '') {
        $__active_order_id = (int) ($__orders_map_by_id[$mt_selected_id] ?? 0);
        if (!$__active_order_id && !empty($mt_account_ui['current']) && (string) $mt_account_ui['current']['id'] === (string) $mt_selected_id) {
          $__active_order_id = (int) ($mt_account_ui['current']['order'] ?? 0);
        }
      }

      $__mt_selected_status = (string) (
        $resolved['status']
        ?? ($mt_account_ui['current']['status'] ?? '')
      );

      $__breach_key = 'BREACHED';
      if (class_exists('Label')) {
        $__breach_key = \Label::ACCOUNT_STATUS_MAP['BREACHED'] ?? 'BREACHED';
      }

      $__mt_breach_show = (strcasecmp($__mt_selected_status, $__breach_key) === 0) ? '1' : '0';
      $__breach_reset_url = '/my-account/reset';
      $__reset_product_id = (string) (
        $resolved['rules']['resetProductId']
        ?? ($mt_account_ui['current']['resetProductId'] ?? '')
      );

      if ($__reset_product_id !== '' && function_exists('wc_get_checkout_url')) {
        $__breach_reset_url = wc_get_checkout_url() . '?add-to-cart=' . urlencode($__reset_product_id);
      }

      /* ==== Passed/Activation meta & helpers ==== */

      $__normalize_id = function ($v) {
        if ($v === null) return '';
        $s = trim((string) $v);
        $sl = strtolower($s);
        return ($s === '' || $s === '0' || $sl === 'null') ? '' : $s;
      };

      $__status_raw = (string) ($resolved['status'] ?? ($mt_account_ui['current']['status'] ?? ''));
      $__status_norm = strtoupper(trim($__status_raw));

      $__activation_product_id = (string) (
        $resolved['rules']['activationProductId'] ??
        ($mt_account_ui['current']['activationProductId'] ?? '')
      );

      $__has_activation_id = ($__normalize_id($__activation_product_id) !== '');

      $__activation_url = ($__has_activation_id && function_exists('wc_get_checkout_url'))
        ? wc_get_checkout_url() . '?add-to-cart=' . urlencode($__activation_product_id)
        : '#';

      $__mt_account_passed_show = (in_array($__status_norm, ['PENDING_ACTIVATION', 'PASSED'], true)) ? '1' : '0';

      $__note_passed_with_id = Label::META_ACCOUNT_OVERVIEW['passed_modal_note_status_passed_w_activation_id'];
      $__note_passed_no_id = Label::META_ACCOUNT_OVERVIEW['passed_modal_note_status_passed_no_activation_id'];

      $__body_subtitle = Label::META_ACCOUNT_OVERVIEW['passed_modal_body_subtitle_default'];
      $__body_subtitle_w_activation_id = Label::META_ACCOUNT_OVERVIEW['passed_modal_body_subtitle_w_activation_id'];
      $__body_subtitle_no_activation_id = Label::META_ACCOUNT_OVERVIEW['passed_modal_body_subtitle_no_activation_id'];

      $__note_text = '';
      $__show_note = false;
      $__body_text = $__body_subtitle;

      if ($__status_norm === 'ACTIVATION_PENDING') {
        $__status_norm = 'PENDING_ACTIVATION';
      }

      if ($__status_norm === 'PASSED' && $__has_activation_id) {
        $__show_note = true;
        $__note_text = $__note_passed_with_id;
      } elseif ($__status_norm === 'PASSED' && !$__has_activation_id) {
        $__show_note = true;
        $__note_text = $__note_passed_no_id;
      } else {
        $__show_note = false;
      }

      if ($__status_norm === 'PENDING_ACTIVATION' && $__has_activation_id) {
        $__body_text = $__body_subtitle;
      } elseif ($__status_norm === 'PASSED' && $__has_activation_id) {
        $__body_text = $__body_subtitle_w_activation_id;
      } elseif ($__status_norm === 'PASSED' && !$__has_activation_id) {
        $__body_text = $__body_subtitle_no_activation_id;
      } else {
        $__body_text = $__body_subtitle;
      }

      $__btn_classes = [];
      if ($__status_norm === 'PENDING_ACTIVATION' && $__has_activation_id) {
      } elseif ($__status_norm === 'PASSED' && $__has_activation_id) {
        $__btn_classes[] = 'disabled';
      } elseif ($__status_norm === 'PASSED' && !$__has_activation_id) {
        $__btn_classes[] = 'd-none';
      } else {
        $__btn_classes[] = 'd-none';
      }
      $__btn_classes_attr = implode(' ', $__btn_classes);

      $__main_product_id = (string) (
        $resolved['rules']['mainProductId']
        ?? ($mt_account_ui['current']['mainProductId'] ?? '')
      );

      $__can_manage_subscription = true;

      if (!empty($mt_account_ui['accounts']) || !empty($mt_account_ui['current'])) {
        $selRow = null;

        if (!empty($mt_selected_id) && !empty($mt_account_ui['accounts'])) {
          foreach ($mt_account_ui['accounts'] as $row) {
            if ((string) ($row['id'] ?? '') === (string) $mt_selected_id) {
              $selRow = $row;
              break;
            }
          }
        }
        if (
          !$selRow && !empty($mt_account_ui['current']) &&
          (empty($mt_selected_id) || (string) $mt_account_ui['current']['id'] === (string) $mt_selected_id)
        ) {
          $selRow = $mt_account_ui['current'];
        }

        $__selected_subscription_id = '';
        if (is_array($selRow)) {
          $__selected_subscription_id = (string) ($selRow['subscriptionId'] ?? '');
          $hasSubLegacy = !empty($selRow['hasSubscription']);

          $__can_manage_subscription = ($__selected_subscription_id !== '' || $hasSubLegacy) ? true : false;
        }
      }

    } else {
      echo '<div class="mt-alert mt-alert--error">Email inválido. Actualiza tu perfil.</div>';
    }
  }
}

/* === Exponer opcionalmente en $GLOBALS === */
$GLOBALS['mt_can_manage_subscription'] = $__can_manage_subscription;

$GLOBALS['mt_user_email'] = $mt_user_email;
$GLOBALS['mt_account_ui'] = $mt_account_ui;
$GLOBALS['mt_selected_id'] = $mt_selected_id;
$GLOBALS['mt_performance'] = $mt_performance;
$GLOBALS['mt_feature_content'] = $mt_feature_content;
$GLOBALS['mt_account_data'] = $mt_account_data;
$GLOBALS['mt_chart'] = $mt_chart ?? [];
$GLOBALS['mt_active_order_id'] = isset($__active_order_id) ? (int) $__active_order_id : 0;

if (empty($mt_account_ui['accounts'])) {
  wp_safe_redirect(trailingslashit(home_url('/subscriptions')));
  exit;
}

get_header();
mt_bench('header_sent', $t0);
?>
<?php wp_body_open(); ?>
<div id="mt-account-overview" class="container" data-email="<?php echo esc_attr($mt_user_email); ?>"
  data-email-api="<?php echo esc_attr($mt_user_email_api); ?>"
  data-account-id="<?php echo esc_attr($mt_selected_id); ?>"
  data-subscription-id="<?php echo esc_attr($__selected_subscription_id ?? ''); ?>"
  data-has-subscription="<?php echo $__can_manage_subscription ? '1' : '0'; ?>"
  data-order-id="<?php echo esc_attr($__active_order_id); ?>">

  <div class="mt-page">
    <div class="mt-page__sidebar">
      <?php if (function_exists('render_sidebar')) { render_sidebar(); } ?>
    </div>
    <div class="mt-page__main d-flex flex-column gap-3">

      <div class="mt-page__main-header">
        <div class="mt-page__title text-white text-size-20 fw-medium text-uppercase">
          <?php echo Label::META_ACCOUNT_OVERVIEW['page_title_overview']; ?>
        </div>
        <span class="mt-page__subtitle text-a8a29e text-14px-line-20px fw-medium">
          <?php echo Label::META_ACCOUNT_OVERVIEW['page_subtitle_overview']; ?>
        </span>
      </div>
      <div class="mt-account-navigation mega-navigation">
        <?php
          if (function_exists('account_navigation_render')) {
            account_navigation_render();
          } else {
            get_template_part(
              'template-parts/account/account-navigation',
              null,
              function_exists('account_navigation_get_args') ? account_navigation_get_args() : []
            );
          }
        ?>
        <form id="mt-manage-subs-form" action="<?php echo esc_url(trailingslashit(home_url('my-account/orders'))); ?>"
          method="post" class="d-none">
          <input type="hidden" name="orderId" value="">
          <input type="hidden" name="optionalOrderId" value="">
        </form>
      </div>

      <div class="mt-account-selection" data-fit-main>
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
      <?php mt_bench('selection_rendered', $t0); ?>

      <div class="d-flex flex-column gap-32" data-fit-main>
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
        <?php mt_bench('account_data_rendered', $t0); ?>

        <div class="mt-account-performance" id="mt-performance-container">
          <?php
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
        <?php mt_bench('performance_rendered', $t0); ?>

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
        <?php mt_bench('feature_rendered', $t0); ?>

        <div class="mt-account-performance-chart-content">
          <?php
          if (!empty($mt_selected_id)) {
            get_template_part(
              'template-parts/account/account-performance-chart',
              null,
              [
                'meta' => ['accountId' => $mt_selected_id],
                'chart' => $mt_chart ?? [],
              ]
            );
          }
          ?>
        </div>
        <?php mt_bench('chart_rendered', $t0); ?>

        <div class="mt-account-daily-journal">
          <?php
          if (!empty($mt_selected_id)) {
            get_template_part(
              'template-parts/account/account-daily-journal',
              null,
              [
                'meta' => ['accountId' => $mt_selected_id],
                'data' => $mt_daily_journal,
              ]
            );
          }
          ?>
        </div>
        <?php mt_bench('daily_journal_rendered', $t0); ?>
      </div>
    </div>
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
      <?php echo Label::META_ACCOUNT_OVERVIEW['account_feedback_question']; ?>
    </div>
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

<div id="mt-agreement-modal" class="modal modal-subcription fade" tabindex="-1" aria-labelledby="mtag-title"
  aria-hidden="true" data-show="<?php echo $__mt_agreement_show; ?>">
  <div class="modal-dialog modal-dialog-centered modal-fullscreen-md-down">
    <div class="modal-content gap-32">
      <div class="modal-header w-100 border-0 justify-content-between align-items-center p-0">
        <span id="mtag-title" class="modal-title text-white heading-sm-medium">
          <?php echo Label::META_ACCOUNT_OVERVIEW['agreement_modal_title']; ?>
        </span>
        <button type="button" class="p-0 border-0 bg-transparent shadow-none mt-modal__close" data-bs-dismiss="modal"
          aria-label="Close">
          <span aria-hidden="true">
            <img src="/wp-content/uploads/2025/05/cancel-circle-1.png" alt="Close" style="width: 24px; height: 24px;" />
          </span>
        </button>
      </div>

      <div class="modal-body d-flex flex-column align-items-center text-center gap-2">
        <div aria-hidden="true">
          <div class="modal-body-image modal-image-warning">
            <img decoding="async" src="/wp-content/themes/megatrader-addons/assets/img/warning.svg"
              alt="http://Warning%20icon">
          </div>
        </div>
        <span class="fw-medium leading-60px text-5xl text-uppercase text-white mt-2">
          <?php echo Label::META_ACCOUNT_OVERVIEW['agreement_modal_body_title']; ?>
        </span>
        <span class="text-white fw-medium text-uppercase text-2xl leading-7">
          <?php echo Label::META_ACCOUNT_OVERVIEW['agreement_modal_body_description']; ?>
        </span>
        <span class="fw-medium text-a8a29e text-base">
          <?php echo Label::META_ACCOUNT_OVERVIEW['agreement_modal_body_subtitle']; ?>
        </span>

        <a href="<?php echo esc_url($__mt_agreement_url ?: '#'); ?>" target="_blank" rel="noopener"
          class="mega-btn-md mega-btn-primary-md mt-agreement-button mt-4">
          <?php echo Label::META_ACCOUNT_OVERVIEW['agreement_modal_button']; ?>
        </a>
      </div>
    </div>
  </div>
</div>

<div id="mt-breach-alert-modal" class="modal modal-subcription fade" tabindex="-1" aria-labelledby="mtbreach-title"
  aria-hidden="true" data-show="<?php echo $__mt_breach_show; ?>"
  data-account-id="<?php echo esc_attr($mt_selected_id); ?>" data-main-id="<?php echo esc_attr($__main_product_id); ?>"
  data-reset-id="<?php echo esc_attr($__reset_product_id); ?>"
  data-account-type="<?php echo esc_attr($mt_account_data['type'] ?? ''); ?>"
  data-checkout-base="<?php echo esc_url(function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : '/checkout'); ?>"
  data-funded-title="<?php echo esc_attr(Label::META_ACCOUNT_OVERVIEW['breach_modal_body_description_funded']); ?>"
  data-evaluation-title="<?php echo esc_attr(Label::META_ACCOUNT_OVERVIEW['breach_modal_body_description_evaluation']); ?>"
  data-btn-default="<?php echo esc_attr(Label::META_ACCOUNT_OVERVIEW['breach_modal_button']); ?>"
  data-btn-no-reset="<?php echo esc_attr(Label::META_ACCOUNT_OVERVIEW['breach_modal_button_no_reset']); ?>"
  data-subscriptions-url="/subscriptions/">
  <div class="modal-dialog modal-dialog-centered modal-fullscreen-md-down">
    <div class="modal-content gap-32">
      <div class="modal-header w-100 border-0 justify-content-between align-items-center p-0">
        <span id="mtbreach-title" class="modal-title text-white heading-sm-medium">
          <?php echo Label::META_ACCOUNT_OVERVIEW['breach_modal_title']; ?></span>
        <button type="button" class="p-0 border-0 bg-transparent shadow-none mt-modal__close" data-bs-dismiss="modal"
          aria-label="Close">
          <span aria-hidden="true">
            <img src="/wp-content/uploads/2025/05/cancel-circle-1.png" alt="Close" style="width:24px;height:24px;">
          </span>
        </button>
      </div>
      <div class="modal-body d-flex flex-column align-items-center text-center gap-2">
        <div aria-hidden="true">
          <div class="modal-body-image modal-image-warning">
            <img decoding="async" src="/wp-content/themes/megatrader-addons/assets/img/warning.svg"
              alt="http://Warning%20icon">
          </div>
        </div>
        <span class="fw-medium leading-60px text-5xl text-uppercase text-white mt-2">
          <?php echo Label::META_ACCOUNT_OVERVIEW['breach_modal_body_title']; ?>
        </span>
        <span id="mtbreach-desc" class="text-white fw-medium text-uppercase text-2xl leading-7">
          <?php echo Label::META_ACCOUNT_OVERVIEW['breach_modal_body_description']; ?>
        </span>
        <span class="fw-medium text-a8a29e text-base">
          <?php echo Label::META_ACCOUNT_OVERVIEW['breach_modal_body_subtitle']; ?>
        </span>
        <?php
        $has_reset = !empty($__reset_product_id) && $__reset_product_id !== '0';
        $btn_classes = 'mega-btn-md mega-btn-primary-md mt-breach-reset-button mt-4';
        $btn_aria = 'false';
        $btn_href = $has_reset ? esc_url($__breach_reset_url) : '#';
        ?>
        <a class="<?php echo esc_attr($btn_classes); ?>" href="<?php echo $btn_href; ?>"
          aria-disabled="<?php echo $btn_aria; ?>">
          <?php echo Label::META_ACCOUNT_OVERVIEW['breach_modal_button']; ?>
        </a>

      </div>
    </div>
  </div>
</div>

<div id="mt-account-passed-modal" class="modal modal-subcription fade" tabindex="-1"
  aria-labelledby="mtactivation-title" aria-hidden="true" data-show="<?php echo $__mt_account_passed_show; ?>"
  data-note-pending="<?php echo esc_attr(Label::META_ACCOUNT_OVERVIEW['passed_modal_note_pending']); ?>"
  data-note-pending-no-button="<?php echo esc_attr(Label::META_ACCOUNT_OVERVIEW['passed_modal_note_pending_no_button']); ?>"
  data-note-passed-with-id="<?php echo esc_attr($__note_passed_with_id); ?>"
  data-note-passed-no-id="<?php echo esc_attr($__note_passed_no_id); ?>"
  data-current-status="<?php echo esc_attr($__status_norm); ?>"
  data-activation-id="<?php echo esc_attr($__activation_product_id); ?>"
  data-account-id="<?php echo esc_attr($mt_selected_id); ?>"
  data-checkout-base="<?php echo esc_url(function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : '/checkout'); ?>"
  data-body-default="<?php echo esc_attr($__body_subtitle); ?>"
  data-body-w-id="<?php echo esc_attr($__body_subtitle_w_activation_id); ?>"
  data-body-no-id="<?php echo esc_attr($__body_subtitle_no_activation_id); ?>">

  <div class="modal-dialog modal-dialog-centered modal-fullscreen-md-down">
    <div class="modal-content gap-32">
      <div class="modal-header w-100 border-0 justify-content-between align-items-center p-0">
        <span id="mtactivation-title" class="modal-title text-white heading-sm-medium">
          <?php echo Label::META_ACCOUNT_OVERVIEW['passed_modal_title']; ?>
        </span>
        <button type="button" class="p-0 border-0 bg-transparent shadow-none mt-modal__close" data-bs-dismiss="modal"
          aria-label="Close">
          <span aria-hidden="true">
            <img src="/wp-content/uploads/2025/05/cancel-circle-1.png" alt="Close" style="width:24px;height:24px;">
          </span>
        </button>
      </div>

      <div class="modal-body d-flex flex-column align-items-center text-center">
        <div aria-hidden="true">
          <div class="modal-body-image ">
            <img decoding="async" src="/wp-content/themes/megatrader-addons/assets/img/thank-you.png"
              alt="http://Thank%20you%20icon">
          </div>
        </div>

        <span class="fw-medium leading-60px text-5xl text-uppercase text-white">
          <?php echo Label::META_ACCOUNT_OVERVIEW['passed_modal_body_title']; ?>
        </span>
        <span class="text-white fw-medium text-uppercase text-2xl leading-7 py-1">
          <?php echo Label::META_ACCOUNT_OVERVIEW['passed_modal_body_description']; ?>
        </span>
        <span class="fw-medium text-a8a29e text-base" data-body-text>
          <?php echo esc_html($__body_text); ?>
        </span>

        <div class="mt-note bg-1e1e1e d-flex gap-3 mt-4 p-3 rounded-3" id="mt-passed-note" data-status="info" <?php echo $__show_note ? '' : 'hidden aria-hidden="true"'; ?>>
          <span class="mt-icon mt-icon-info mt-icon_info-solid"></span>
          <span class="text-60A5FA fw-medium text-base" data-note-text>
            <?php echo esc_html($__note_text ?: $__note_passed_with_id); ?>
          </span>
        </div>

        <a id="mt-activation-btn"
          class="mega-btn-md mega-btn-primary-md mt-activation-button mt-4 <?php echo esc_attr($__btn_classes_attr); ?>"
          href="<?php echo esc_url($__activation_url); ?>"
          aria-disabled="<?php echo (strpos($__btn_classes_attr, 'disabled') !== false) ? 'true' : 'false'; ?>">
          <?php echo Label::META_ACCOUNT_OVERVIEW['passed_modal_button']; ?>
        </a>
      </div>
    </div>
  </div>
</div>

<?php get_footer(); ?>
