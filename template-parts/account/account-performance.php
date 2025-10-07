<?php
/**
 * Template Part: Account Performance (datos)
 * Ruta: template-parts/account/account-performance.php
 *
 * Espera recibir:
 *   - $args['performance']  (payload normalizado)
 *   - $args['meta']['accountId'] (opcional, para debug)
 * Fallback WP antiguos: set_query_var('mt_performance', $array) y set_query_var('mt_meta', ['accountId'=>...])
 */
defined('ABSPATH') || exit;

/* ========= Entrada compatible (args / query_var) ========= */
$performance = [];
$meta = [];
$source = 'unknown';

if (isset($args) && is_array($args)) {
    if (isset($args['performance']) && is_array($args['performance'])) {
        $performance = $args['performance'];
        $source = 'args.performance';
    }
    if (isset($args['meta']) && is_array($args['meta'])) {
        $meta = $args['meta'];
    }
}

if (empty($performance)) {
    $maybe = get_query_var('mt_performance');
    if (!empty($maybe) && is_array($maybe)) {
        $performance = $maybe;
        $source = 'query_var.mt_performance';
    }
}
if (empty($meta)) {
    $maybeMeta = get_query_var('mt_meta');
    if (!empty($maybeMeta) && is_array($maybeMeta)) {
        $meta = $maybeMeta;
    }
}

/* ========= Normaliza con defaults ========= */
$defaults = [
    'currentBalance' => null,
    'currentEquity' => null,
    'currentProfit' => null,
    'currentProfitPercent' => null,
    'activeTradingDays' => null,
    'dailyTotalPnL' => null,
    'minTradingDays' => null,
    'maxLossLimitEquityLevel' => null,
    'target' => null,
    'maxDailyLossLimitPnLLevel' => null,
    'label' => '',
    'consistency' => null,
    'targetAmount' => null,
];
$performance = array_merge($defaults, (array) $performance);

/* ========= Aliases de uso en HTML ========= */
$balance = $performance['currentBalance'];
$equity = $performance['currentEquity'];
$profit = $performance['currentProfit'];
$profitPct = $performance['currentProfitPercent'];
$daysTraded = (int) $performance['activeTradingDays'];
$dailyPnL = $performance['dailyTotalPnL'];
$minDays = (int) $performance['minTradingDays'];
$maxLossEq = $performance['maxLossLimitEquityLevel'];
$maxDailyLoss = $performance['maxDailyLossLimitPnLLevel'];
$targetAmount = $performance['targetAmount'];
$stage = mt_program_stage($performance['label'] ?? '');
$isFunded = mt_is_funded($performance['label'] ?? '');
$isEvaluation = mt_is_evaluation($performance['label'] ?? '');
$profitTarget = $isFunded
  ? ($performance['targetAmount'] ?? null)
  : ($performance['target'] ?? null);


/* ========= Derivados (para barras / chips) ========= */
$profitProgressPct = (is_numeric($profitTarget) && $profitTarget > 0)
    ? max(0, min(100, ($profit / $profitTarget) * 100))
    : null;

$daysProgressPct = ($minDays > 0)
    ? max(0, min(100, ($daysTraded / $minDays) * 100))
    : null;

$profitRemaining = (is_numeric($profitTarget) && is_numeric($profit))
    ? max(0, $profitTarget - max(0, $profit))
    : null;


/* ========= Flag para saber si hay data real ========= */
$has_data = array_filter($performance, function ($v) {
    return $v !== null && $v !== '';
}) ? true : false;


// === Progress bars (UI) ===
$profitNum = is_numeric($profit) ? (float) $profit : null;
$targetNum = (is_numeric($profitTarget) && $profitTarget > 0) ? (float) $profitTarget : null;

$profitFillPct = 0; // 0..100, nunca null
if ($profitNum !== null && $targetNum !== null) {
    // si profit es negativo o 0, se queda en 0%
    $profitFillPct = max(0, min(100, (max(0, $profitNum) / $targetNum) * 100));
}


/* Formatted variables */
$ui = mt_profit_ui_from_percent($profitPct);
$profitPctNum = $ui['pctNum'];
$badgeClass = $ui['badgeClass'];
$pctText = $ui['text'];
$iconClass = $ui['iconClass'];

$dailyColorClass = mt_value_color_class($dailyPnL);
$dailyText = mt_format_signed_money($dailyPnL);

$profitColorClass = mt_value_color_class($profit);
$profitText = mt_format_signed_money($profit);

$profitIconClass = mt_value_compare_icon_classes($profit, $profitTarget);

$daysFillPct = ($minDays > 0) ? max(0, min(100, ($daysTraded / $minDays) * 100)) : 0;
$daysIconClass = mt_value_compare_icon_classes($daysTraded, $minDays);
$daysColorClass = ($daysTraded > 0) ? 'text-success' : 'text-white';

$maxDailyLossFormat = is_numeric($maxDailyLoss ?? null) ? abs((float) $maxDailyLoss) : 0;

/* Title Right Column */
if ($isFunded) {
    $titleRight = Label::META_ACCOUNT_OVERVIEW['performance_title_right_funded'] ?? '';
} elseif ($isEvaluation) {
    $titleRight = Label::META_ACCOUNT_OVERVIEW['performance_title_right_evaluation'] ?? '';
} else {
    $titleRight = Label::META_ACCOUNT_OVERVIEW['performance_title_right_evaluation'] ?? '';
}



?>

<?php
/* === DEBUG VISUAL (activar con ?mt_debug_perf=1 o si eres admin) === */
$__mt_debug_perf = true;

if ($__mt_debug_perf):
  $now = function_exists('current_time') ? current_time('mysql') : date('Y-m-d H:i:s');
  $rawTargetAmount = $performance['targetAmount'] ?? null;
  $rawTarget       = $performance['target'] ?? null;
?>
  <div class="position-fixed bottom-0 end-0 m-3 p-3 rounded-2 bg-dark text-white"
       style="max-width: 420px; z-index: 9999; opacity:.95">
    <div class="fw-bold mb-2">Account Performance · Debug</div>
    <div class="small text-a8a29e mb-2">Rendered: <?php echo esc_html($now); ?></div>

    <div class="small"><b>AccountId:</b> <?php echo esc_html($meta['accountId'] ?? ''); ?></div>
    <div class="small"><b>Label:</b> <?php echo esc_html($performance['label'] ?? ''); ?></div>
    <div class="small"><b>Stage:</b> <?php echo esc_html($stage); ?></div>
    <div class="small"><b>isFunded:</b> <?php echo $isFunded ? 'true' : 'false'; ?></div>
    <div class="small"><b>isEvaluation:</b> <?php echo $isEvaluation ? 'true' : 'false'; ?></div>

    <hr class="my-2" />

    <div class="small"><b>targetAmount (raw):</b>
      <?php echo esc_html(is_scalar($rawTargetAmount) ? (string)$rawTargetAmount : var_export($rawTargetAmount, true)); ?>
    </div>
    <div class="small"><b>targetAmount (fmt):</b>
      <?php echo esc_html(mt_format_money($rawTargetAmount)); ?>
    </div>

    <div class="small mt-1"><b>target (raw):</b>
      <?php echo esc_html(is_scalar($rawTarget) ? (string)$rawTarget : var_export($rawTarget, true)); ?>
    </div>
    <div class="small"><b>target (fmt):</b>
      <?php echo esc_html(mt_format_money($rawTarget)); ?>
    </div>

    <hr class="my-2" />

    <div class="small"><b>→ profitTarget usado:</b>
      <?php echo esc_html(mt_format_money($profitTarget)); ?>
    </div>
    <div class="small"><b>currentProfit:</b>
      <?php echo esc_html(mt_format_money($profit)); ?>
    </div>
    <div class="small"><b>progress %:</b> <?php echo (int) round($profitFillPct); ?>%</div>
  </div>
<?php endif; ?>


<?php if ($has_data): ?>
    <div class="mt-card mt-card__row gap-32" data-component="account-performance">
        <div class="w-100 d-flex flex-column gap-32">
            <div class="d-flex flex-column gap-3">
                <div class="mt-card__title__text fw-medium text-uppercase">
                    <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_title_left']); ?>
                </div>
                <div class="d-flex flex-column">
                    <div class="mt-card__item">
                        <div class="mt-card__item-text">
                            <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_account_balance']); ?>
                        </div>
                        <div class="mt-card__item-value text-white"><?php echo esc_html(mt_format_money($balance)); ?></div>
                    </div>
                    <div class="mt-card__item">
                        <div class="mt-card__item-text">
                            <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_total_profit']); ?>
                        </div>
                        <div class="mt-card__item-value d-flex align-items-center gap-2 justify-content-end"> <span
                                class="<?php echo esc_attr($profitColorClass); ?>">
                                <?php echo esc_html($profitText); ?>
                            </span>
                            <div class="d-flex align-items-center">
                                <?php if (!empty($iconClass)): ?>
                                    <span class="mt-icon <?php echo esc_attr($iconClass); ?>"></span>
                                <?php endif; ?>
                                <span class="mt-badge <?php echo esc_attr($badgeClass); ?> mt-badge-sm">
                                    <?php echo esc_html($pctText); ?>
                                </span>

                            </div>
                        </div>
                    </div>
                    <div class="mt-card__item">
                        <div class="mt-card__item-text">
                            <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_trading_days']); ?>
                        </div>
                        <div class="mt-card__item-value text-white">
                            <?php echo esc_html($daysTraded); ?>
                        </div>
                    </div>
                    <?php

                    if ($maxDailyLossFormat > 0): ?>
                        <div class="mt-card__item">
                            <div class="mt-card__item-text">
                                <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_daily_loss_limit']); ?>
                            </div>
                            <div class="mt-card__item-value text-white d-flex gap-1 align-items-center justify-content-end">
                                <?php echo esc_html(mt_format_money_no_cents($maxDailyLossFormat)); ?>
                                <span class="mt-tooltip">
                                    <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0"
                                        aria-label="Daily Loss Limit information"></i>
                                    <span class="mt-tooltip__panel" role="tooltip">
                                        <div class="mt-tooltip__title">
                                            <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_dll_tooltip_title']); ?>
                                        </div>
                                        <div class="mt-tooltip__body">
                                            <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_dll_tooltip_description']); ?>
                                        </div>
                                    </span>
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mt-card__item">
                        <div class="mt-card__item-text">
                            <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_current_equity']); ?>
                        </div>
                        <div class="mt-card__item-value text-white"><?php echo esc_html(mt_format_money($equity)); ?></div>
                    </div>
                    <div class="mt-card__item">
                        <div class="mt-card__item-text d-flex gap-1 align-items-center">
                            <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_daily_net_pl']); ?>
                            <span class="mt-tooltip">
                                <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0"
                                    aria-label="Daily Loss Limit information"></i>
                                <span class="mt-tooltip__panel" role="tooltip">
                                    <div class="mt-tooltip__title">
                                        <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_dpl_tooltip_title']); ?>
                                    </div>
                                    <div class="mt-tooltip__body">
                                        <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_dpl_tooltip_description']); ?>
                                    </div>
                                </span>
                            </span>
                        </div>
                        <div class="mt-card__item-value">
                            <span class="<?php echo esc_attr($dailyColorClass); ?>">
                                <?php echo esc_html($dailyText); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-100 d-flex flex-column gap-32">
            <div class="d-flex flex-column gap-3">
                <div class="mt-card__title__text fw-medium text-uppercase">
                    <?php echo esc_html($titleRight); ?>
                </div>
                <div class="d-flex flex-column">
                    <div class="mt-card__item">
                        <div class="mt-card__item-text d-flex gap-1 align-items-center">
                            <span class="mt-icon <?php echo esc_attr($profitIconClass); ?>"></span>
                            <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_profit_target']); ?>
                        </div>
                        <div class="mt-card__item-value text-white">
                            <span class="<?php echo esc_attr($profitColorClass); ?>">
                                <?php echo esc_html($profitText); ?>
                            </span>
                            /
                            <?php echo esc_html(mt_format_money($profitTarget)); ?>

                            <!-- barra de progreso que ya tienes, si aplica -->
                            <div class="mt-progress-bar mt-progress-bar--md mt-progress-bar--success" role="progressbar"
                                aria-valuemin="0" aria-valuemax="100"
                                aria-valuenow="<?php echo (int) round($profitFillPct); ?>"
                                style="--mt-progress-value: <?php echo esc_attr($profitFillPct); ?>%;">
                                <span class="mt-progress-bar__fill"></span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-card__item">
                        <div class="mt-card__item-text d-flex gap-1 align-items-center">
                            <span class="mt-icon <?php echo esc_attr($daysIconClass); ?>"></span>
                            <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_days_traded']); ?>
                        </div>

                        <div class="mt-card__item-value text-white">
                            <span class="<?php echo esc_attr($daysColorClass); ?>">
                                <?php echo esc_html($daysTraded); ?>
                            </span>
                            /
                            <?php echo esc_html($minDays); ?>

                            <div class="mt-progress-bar mt-progress-bar--md mt-progress-bar--success" role="progressbar"
                                aria-valuemin="0" aria-valuemax="100"
                                aria-valuenow="<?php echo (int) round($daysFillPct); ?>"
                                style="--mt-progress-value: <?php echo esc_attr($daysFillPct); ?>%;">
                                <span class="mt-progress-bar__fill"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-column gap-3">
                <div class="mt-card__title__text fw-medium text-uppercase">
                    <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_rules']); ?>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="mt-icon <?php
                    echo (is_numeric($balance) && is_numeric($maxLossEq))
                        ? (((float) $balance < (float) $maxLossEq)
                            ? 'mt-icon-error mt-icon_cancel'
                            : 'mt-icon-success mt-icon_checkmark-solid'
                        )
                        : 'mt-icon-error mt-icon_cancel';
                    ?>"></span>
                    <div class="text-white text-base fw-medium">
                        <span
                            class="text-white text-base fw-medium d-flex flex-column"><?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_rules_description']); ?>
                            <?php echo esc_html(mt_format_money_no_cents($maxLossEq)); ?></span>
                        <a
                            class="text-primary text-14px-line-20px fw-medium text-decoration-underline"><?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_max_loss_limit']); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <div class="mt-card mt-card__row gap-16" data-component="account-performance-empty">
        <div class="text-a8a29e">
            <em><?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_no_data']); ?></em>
        </div>
    </div>
<?php endif; ?>