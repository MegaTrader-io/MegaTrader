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
$profitTarget = $performance['target'];


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

$daysFillPct = ($minDays > 0)
    ? max(0, min(100, ($daysTraded / $minDays) * 100))
    : 0;



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
$daysIconClass = mt_value_compare_icon_classes($daysTraded, $minDays); // usa el helper nuevo
$daysColorClass = ($daysTraded > 0) ? 'text-success' : 'text-white';




?>

<?php if ($has_data): ?>
    <div class="mt-card mt-card__row gap-32" data-component="account-performance">
        <div class="w-100 d-flex flex-column gap-32">
            <div class="d-flex flex-column gap-3">
                <div class="mt-card__title__text fw-medium text-uppercase">
                    Overall performance</div>
                <div class="d-flex flex-column">
                    <div class="mt-card__item">
                        <div class="mt-card__item-text">Account Balance</div>
                        <div class="mt-card__item-value text-white"><?php echo esc_html(mt_format_money($balance)); ?></div>
                    </div>
                    <div class="mt-card__item">
                        <div class="mt-card__item-text">Total Profit</div>
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
                        <div class="mt-card__item-text">Trading Days</div>
                        <div class="mt-card__item-value text-white">
                            <?php echo esc_html($daysTraded); ?>
                        </div>
                    </div>
                    <div class="mt-card__item">
                        <div class="mt-card__item-text">Days Loss Limit</div>
                        <div class="mt-card__item-value text-white d-flex gap-1 align-items-center justify-content-end">
                            <?php echo esc_html(mt_format_money(2000)); ?>
                            <span class="mt-tooltip">
                                <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0"
                                    aria-label="Daily Loss Limit information"></i>
                                <span class="mt-tooltip__panel" role="tooltip">
                                    <div class="mt-tooltip__title">Daily Loss Limit (DLL)</div>
                                    <div class="mt-tooltip__body">
                                        Reaching the DLL pauses trading for the day. It’s removed once a profit
                                        milestone is
                                        met.
                                    </div>
                                </span>
                            </span>
                        </div>

                    </div>
                    <div class="mt-card__item">
                        <div class="mt-card__item-text">Current Equity</div>
                        <div class="mt-card__item-value text-white"><?php echo esc_html(mt_format_money($equity)); ?></div>
                    </div>
                    <div class="mt-card__item">
                        <div class="mt-card__item-text d-flex gap-1 align-items-center">Daily Net P&L
                            <span class="mt-tooltip">
                                <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0"
                                    aria-label="Daily Loss Limit information"></i>
                                <span class="mt-tooltip__panel" role="tooltip">
                                    <div class="mt-tooltip__title">Daily P&L</div>
                                    <div class="mt-tooltip__body">
                                        Realised P&L amount at any time during the trading week (Sunday 5:00 PM - Friday
                                        3:10 PM CT)
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
                <div class="mt-card__title__text fw-medium text-uppercase">Your Challenge Objective</div>
                <div class="d-flex flex-column">
                    <div class="mt-card__item">
                        <div class="mt-card__item-text d-flex gap-1 align-items-center">
                            <span class="mt-icon <?php echo esc_attr($profitIconClass); ?>"></span>
                            Profit Target
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
                            Days Traded
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
                <div class="mt-card__title__text fw-medium text-uppercase">Rules</div>
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
                        <span class="text-white text-base fw-medium d-flex flex-column">Keep your Account Balance above
                            <?php echo esc_html(mt_format_money($maxLossEq)); ?></span>
                        <a class="text-primary text-14px-line-20px fw-medium text-decoration-underline">Maximum Loss
                            Limmit</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <div class="mt-card mt-card__row gap-16" data-component="account-performance-empty">
        <div class="text-a8a29e">
            <em>No performance data to render. See debug below.</em>
        </div>
    </div>
<?php endif; ?>

<!-- ===================== DEBUG PANEL (visual) ===================== -->
<?php
$accountId = isset($meta['accountId']) ? (string) $meta['accountId'] : '';
$debug_payload = [
    'source' => $source,
    'accountId' => $accountId,
    'has_data' => $has_data,
    'performance' => $performance,
    'derived' => [
        'profitProgressPct' => $profitProgressPct,
        'daysProgressPct' => $daysProgressPct,
        'profitRemaining' => $profitRemaining,
    ],
];
$json_pretty = wp_json_encode($debug_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
<!--
<div class="mt-debug" style="margin-top:16px;">
    <div style="border:1px dashed #525252; background:#18181b; color:#e4e4e7; border-radius:12px; padding:16px;">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:12px;">
            <strong style="text-transform:uppercase; letter-spacing:.08em;">DEBUG · Account Performance</strong>
            <button type="button"
                onclick="(function(btn){var box=btn.closest('.mt-debug').querySelector('.mt-debug__body'); var s=getComputedStyle(box); box.style.display=(s.display==='none'?'block':'none');})(this)"
                style="background:#27272a; color:#e4e4e7; border:1px solid #3f3f46; border-radius:8px; padding:6px 10px; font-size:12px; cursor:pointer;">
                Toggle
            </button>
        </div>

        <div class="mt-debug__body" style="display:block;">
            <div style="display:grid; grid-template-columns: 260px 1fr; gap:16px;">
                <div style="background:#0b0b0c; border:1px solid #27272a; border-radius:10px; padding:12px;">
                    <div style="font-size:12px; color:#a1a1aa; text-transform:uppercase; margin-bottom:8px;">Meta
                    </div>
                    <div style="display:flex; flex-direction:column; gap:6px; font-size:14px;">
                        <div><span style="color:#a1a1aa;">Source:</span>
                            <strong><?php echo esc_html($source); ?></strong>
                        </div>
                        <div><span style="color:#a1a1aa;">Account ID:</span>
                            <strong><?php echo esc_html($accountId ?: '—'); ?></strong>
                        </div>
                        <div><span style="color:#a1a1aa;">Has Data:</span>
                            <strong><?php echo $has_data ? 'yes' : 'no'; ?></strong>
                        </div>
                        <div><span style="color:#a1a1aa;">Profit Target:</span>
                            <strong><?php echo esc_html(($profitTarget !== null) ? $profitTarget : '—'); ?></strong>
                        </div>
                        <div><span style="color:#a1a1aa;">Balance:</span>
                            <strong><?php echo esc_html(($balance !== null) ? $balance : '—'); ?></strong>
                        </div>
                        <div><span style="color:#a1a1aa;">Equity:</span>
                            <strong><?php echo esc_html(($equity !== null) ? $equity : '—'); ?></strong>
                        </div>
                        <div><span style="color:#a1a1aa;">Profit:</span>
                            <strong><?php echo esc_html(($profit !== null) ? $profit : '—'); ?></strong>
                        </div>
                        <div><span style="color:#a1a1aa;">Profit %:</span>
                            <strong><?php echo esc_html(($profitPct !== null) ? $profitPct . '%' : '—'); ?></strong>
                        </div>
                        <div><span style="color:#a1a1aa;">Active Days:</span>
                            <strong><?php echo esc_html($daysTraded); ?></strong>
                        </div>
                        <div><span style="color:#a1a1aa;">Min Days:</span>
                            <strong><?php echo esc_html($minDays); ?></strong>
                        </div>
                        <div><span style="color:#a1a1aa;">Max Loss Eq.:</span>
                            <strong><?php echo esc_html(($maxLossEq !== null) ? $maxLossEq : '—'); ?></strong>
                        </div>
                    </div>
                </div>

                <div style="background:#0b0b0c; border:1px solid #27272a; border-radius:10px; padding:12px;">
                    <div style="font-size:12px; color:#a1a1aa; text-transform:uppercase; margin-bottom:8px;">Payload
                        (JSON)</div>
                    <textarea readonly rows="16"
                        style="width:100%; font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace; font-size:12px; line-height:1.4; color:#e4e4e7; background:#0b0b0c; border:1px solid #27272a; border-radius:8px; padding:10px;"><?php echo esc_textarea($json_pretty); ?></textarea>
                    <div style="margin-top:8px; font-size:12px; color:#a1a1aa;">
                        Usa este JSON para comparar con Postman. Si <strong>accountId</strong> no coincide con la
                        cuenta
                        seleccionada, revisa el flujo del selector.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 
-->
