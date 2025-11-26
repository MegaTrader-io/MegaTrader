<?php
defined('ABSPATH') || exit;

/** @var array $args */
$account_id = isset($args['account_id']) ? (string) $args['account_id'] : '';

global $mt_user_email;
$user_email = isset($mt_user_email) ? $mt_user_email : '';

// Estado por defecto: NO elegible
$btn_disabled       = true;
$payout_eligible    = '0';
$max_withdrawal_ui  = 0.0;

// Conectar helper SOLO si tenemos email, account_id y la función existe
if ($user_email && $account_id && function_exists('mt_prepare_ui_payout')) {
    $payout = mt_prepare_ui_payout($user_email);

    if (!empty($payout['items']) && is_array($payout['items'])) {
        foreach ($payout['items'] as $item) {
            if ((string)($item['id'] ?? '') !== $account_id) {
                continue;
            }

            $eligibleForPayout = !empty($item['eligibleForPayout']);
            $meta              = isset($item['meta']) && is_array($item['meta']) ? $item['meta'] : [];
            $max_withdrawal_ui = isset($meta['maxWithdrawalUI']) ? (float)$meta['maxWithdrawalUI'] : 0.0;

            if ($eligibleForPayout && $max_withdrawal_ui > 0) {
                $btn_disabled    = false;
                $payout_eligible = '1';
            }

            break; // ya encontramos la cuenta
        }
    }
}
?>

<div
  class="mt-card mt-payout-request-card mt-card__row"
  data-account-id="<?php echo esc_attr($account_id); ?>"
  data-payout-eligible="<?php echo esc_attr($payout_eligible); ?>"
  data-max-withdrawal="<?php echo esc_attr((string)$max_withdrawal_ui); ?>"
  data-role="mt-request-payout-card"
>
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
    REQUEST WITHDRAWAL
  </button>
</div>

<?php
// Movemos aquí el modal (usando el email que ya calculaste en account-overview.php)
if ($user_email) {
  get_template_part(
    'template-parts/modals/request-payout-modal',
    null,
    ['user_email' => $user_email]
  );
}
?>
