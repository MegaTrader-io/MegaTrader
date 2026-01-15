<?php
defined('ABSPATH') || exit;

/** @var array $args */
$account_id = isset($args['account_id']) ? (string) $args['account_id'] : '';

global $mt_user_email;
$user_email = isset($mt_user_email) ? $mt_user_email : '';

// =====================
// Defaults
// =====================
$btn_disabled      = true;
$payout_eligible   = '0';
$max_withdrawal_ui = 0.0;
$user_kyc_verified = true; // default: no overlay

// =====================
// Primary source: mt_prepare_ui_payout (by email → list)
// =====================
$foundInList = false;

if ($user_email && $account_id && function_exists('mt_prepare_ui_payout')) {
  $payout = mt_prepare_ui_payout($user_email);

  if (!empty($payout['items']) && is_array($payout['items'])) {
    foreach ($payout['items'] as $item) {
      if ((string) ($item['id'] ?? '') !== $account_id) {
        continue;
      }

      $foundInList = true;

      $eligibleForPayout = !empty($item['eligibleForPayout']);
      $meta = (isset($item['meta']) && is_array($item['meta'])) ? $item['meta'] : [];

      $raw = $meta['userKYCVerified'] ?? null;
      $user_kyc_verified = in_array($raw, [true, 1, '1', 'true', 'yes', 'on'], true);

      $max_withdrawal_ui = isset($meta['maxWithdrawalUI'])
        ? (float) $meta['maxWithdrawalUI']
        : 0.0;

      if ($eligibleForPayout && $max_withdrawal_ui > 0) {
        $btn_disabled = false;
        $payout_eligible = '1';
      }

      if (!$user_kyc_verified) {
        $btn_disabled = true;
        $payout_eligible = '0';
      }

      break;
    }
  }
}

// =====================
// Fallback: mt_get_account_payout_eligibility (by accountId)
// =====================
if (!$foundInList && function_exists('mt_get_account_payout_eligibility')) {
  $eligOne = mt_get_account_payout_eligibility($account_id);
  $metaOne = (isset($eligOne['meta']) && is_array($eligOne['meta'])) ? $eligOne['meta'] : [];

  $raw = $metaOne['userKYCVerified'] ?? null;
  $user_kyc_verified = in_array($raw, [true, 1, '1', 'true', 'yes', 'on'], true);

  $max_withdrawal_ui = isset($eligOne['maxWithdrawalUI'])
    ? (float) $eligOne['maxWithdrawalUI']
    : 0.0;

  if (!$user_kyc_verified) {
    $btn_disabled = true;
    $payout_eligible = '0';
  }
}
?>

<div
  class="mt-card mt-payout-request-card mt-card__row"
  data-account-id="<?php echo esc_attr($account_id); ?>"
  data-role="mt-request-payout-card"
  data-payout-eligible="<?php echo esc_attr($payout_eligible); ?>"
  data-max-withdrawal="<?php echo esc_attr((string) $max_withdrawal_ui); ?>"
>
  <div class="mt-card-content d-flex align-items-center gap-3 w-100 justify-content-between flex-column flex-lg-row flex-md-row<?php echo !$user_kyc_verified ? ' mt-payout-not-verify' : ''; ?>">
    <div class="d-flex align-items-center gap-3 flex-wrap flex-1-1-0">
      <span class="text-white fw-medium">
        <?php echo esc_html(Label::META_ACCOUNT_PAYOUT['paymentMethodTitle']); ?>
      </span>

      <div class="d-flex align-items-center gap-2">
        <span class="mt-icon mt-icon_color_rise" aria-hidden="true"></span>
        <span class="mt-icon mt-icon_color_btc" aria-hidden="true"></span>
        <span class="mt-icon mt-icon_color_eth" aria-hidden="true"></span>
      </div>
    </div>

    <button
      type="button"
      class="mt-btn mt-btn--md mt-btn--primary"
      data-role="mt-open-payout-modal"
      aria-disabled="<?php echo $btn_disabled ? 'true' : 'false'; ?>"
      <?php echo $btn_disabled ? 'disabled' : ''; ?>
    >
      <?php echo esc_html(Label::META_ACCOUNT_PAYOUT['requestBtn']); ?>
    </button>
  </div>

  <div class="mt-payout-overlay" <?php echo $user_kyc_verified ? 'hidden aria-hidden="true"' : 'aria-hidden="false"'; ?>>
    <div class="mt-payout-overlay__inner text-center">
      <div class="mt-payout-overlay__text">
        <?php echo Label::META_ACCOUNT_OVERVIEW['payment_verify_description']; ?>
      </div>

      <a
        href="<?php echo esc_url('/my-account/profile/?open=verification'); ?>"
        class="mt-btn mt-btn--md mt-btn--primary mt-payout-button"
      >
        <?php echo Label::META_ACCOUNT_OVERVIEW['payment_verify_button']; ?>
      </a>
    </div>
  </div>
</div>

<?php
if ($user_email) {
  get_template_part(
    'template-parts/modals/request-payout-modal',
    null,
    ['user_email' => $user_email]
  );
}
?>
