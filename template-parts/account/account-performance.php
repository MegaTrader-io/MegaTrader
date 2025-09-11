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

/* ===== Cálculo de clases y textos para % y el ícono ===== */
$profitNum = (is_numeric($profit) ? (float) $profit : null);
$profitColorClass = 'text-white';
if ($profitNum !== null) {
    if ($profitNum > 0) {
        $profitColorClass = 'text-success';
    } elseif ($profitNum < 0) {
        $profitColorClass = 'text-error';
    }
}

$profitPctNum = (is_numeric($profitPct) ? (float) $profitPct : null);

/* Badge class según reglas */
if ($profitPctNum === null) {
    $badgeClass = 'mt-badge-light';
} elseif ($profitPctNum > 0) {
    $badgeClass = 'mt-badge-secondary';
} elseif ($profitPctNum < 0) {
    $badgeClass = 'mt-badge-error';
} else { // == 0
    $badgeClass = 'mt-badge-light';
}

/* Texto del porcentaje (con signo) */
if ($profitPctNum === null) {
    $pctText = '—';
} elseif ($profitPctNum > 0) {
    $pctText = mt_format_percent($profitPctNum, true);  // +4.30%
} elseif ($profitPctNum < 0) {
    $pctText = mt_format_percent($profitPctNum, true);  // -4.30%
} else {
    $pctText = mt_format_percent(0, false);             // 0.00%
}

/* Clases del ícono (no se pinta si es 0 o null) */
$iconClass = '';
if ($profitPctNum !== null && $profitPctNum > 0) {
    $iconClass = 'mt-icon_arrow-up mt-icon-success';
} elseif ($profitPctNum !== null && $profitPctNum < 0) {
    $iconClass = 'mt-icon_arrow-down mt-icon-error';
}

/* Texto de “Total Profit” formateado con signo */
$profitText = mt_format_signed_money($profit);
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
                        <div class="mt-card__item-value text-white"><?php echo esc_html(mt_format_money($balance)); ?>
                        </div>
                    </div>
                    <div class="mt-card__item">
                        <div class="mt-card__item-text">Total Profit</div>
                        <div class="mt-card__item-value d-flex align-items-center gap-2"> <span
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
                        <div class="mt-card__item-text">Days Loss Limit</div>
                        <div class="mt-card__item-value text-white d-flex gap-1 align-items-center">
                            <?php echo esc_html(mt_format_money(2000)); ?>
                            <span class="mt-tooltip">
                                <i class="mt-icon mt-icon-gray mt-icon_info-solid" tabindex="0"
                                    aria-label="Daily Loss Limit information"></i>
                                <span class="mt-tooltip__panel" role="tooltip">
                                    <div class="mt-tooltip__title">Daily Loss Limit (DLL)</div>
                                    <div class="mt-tooltip__body">
                                        Reaching the DLL pauses trading for the day. It’s removed once a profit milestone is
                                        met.
                                    </div>
                                </span>
                            </span>
                        </div>

                    </div>
                    <div class="mt-card__item">
                        <div class="mt-card__item-text">Current Equity</div>
                        <div class="mt-card__item-value">$47,850.30</div>
                    </div>
                    <div class="mt-card__item">
                        <div class="mt-card__item-text">Weekly Net P&L</div>
                        <div class="mt-card__item-value">-$1,560.40</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-100 d-flex flex-column gap-32">
            <div class="d-flex flex-column gap-3">
                <div class="mt-card__title__text fw-medium text-uppercase">Your Challenge Objective</div>
                <div class="d-flex flex-column">
                    <div class="mt-card__item">
                        <div style="flex: 1 1 0; justify-content: flex-start; align-items: center; gap: 8px; display: flex">
                            <div style="width: 24px; height: 24px; position: relative">
                                <div
                                    style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9">
                                </div>
                                <div
                                    style="width: 20px; height: 20px; left: 2px; top: 2px; position: absolute; background: var(--Icon-Error, #F43F5E)">
                                </div>
                            </div>
                            <div
                                style="flex: 1 1 0; color: var(--White, white); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">
                                Profit Target</div>
                        </div>
                        <div
                            style="flex: 1 1 0; flex-direction: column; justify-content: center; align-items: flex-end; gap: 4px; display: inline-flex">
                            <div
                                style="align-self: stretch; justify-content: flex-end; align-items: center; gap: 4px; display: inline-flex">
                                <div
                                    style="color: var(--Error-500, #F43F5E); font-size: 16px; font-family: Roboto; font-weight: 500; text-transform: uppercase; line-height: 24px; word-wrap: break-word">
                                    -$2,149.70</div>
                                <div
                                    style="color: var(--Basic-White, white); font-size: 16px; font-family: Roboto; font-weight: 500; text-transform: uppercase; line-height: 24px; word-wrap: break-word">
                                    / $3,000.00</div>
                            </div>
                            <div
                                style="align-self: stretch; height: 8px; position: relative; background: var(--Colors-Gray-700, #404040); border-radius: 4px">
                            </div>
                        </div>
                    </div>
                    <div class="mt-card__item">
                        <div style="flex: 1 1 0; justify-content: flex-start; align-items: center; gap: 8px; display: flex">
                            <div style="width: 24px; height: 24px; position: relative">
                                <div
                                    style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9">
                                </div>
                                <div
                                    style="width: 20px; height: 20px; left: 2px; top: 2px; position: absolute; background: var(--Icon-Success, #2DD4BF)">
                                </div>
                            </div>
                            <div
                                style="flex: 1 1 0; color: var(--White, white); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">
                                Days Traded</div>
                        </div>
                        <div
                            style="flex: 1 1 0; flex-direction: column; justify-content: center; align-items: flex-end; gap: 4px; display: inline-flex">
                            <div
                                style="align-self: stretch; justify-content: flex-end; align-items: center; gap: 4px; display: inline-flex">
                                <div
                                    style="color: var(--Text-Success, #2DD4BF); font-size: 16px; font-family: Roboto; font-weight: 500; text-transform: uppercase; line-height: 24px; word-wrap: break-word">
                                    4</div>
                                <div
                                    style="color: var(--Basic-White, white); font-size: 16px; font-family: Roboto; font-weight: 500; text-transform: uppercase; line-height: 24px; word-wrap: break-word">
                                    / 1</div>
                            </div>
                            <div
                                style="align-self: stretch; height: 8px; position: relative; background: var(--Colors-Gray-700, #404040); overflow: hidden; border-radius: 4px">
                                <div
                                    style="width: 172px; height: 8px; left: 172px; top: 8px; position: absolute; transform: rotate(180deg); transform-origin: top left; background: var(--Success-400, #2DD4BF)">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div
                style="align-self: stretch; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 16px; display: flex">
                <div
                    style="align-self: stretch; color: white; font-size: 20px; font-family: Roboto; font-weight: 500; text-transform: uppercase; line-height: 24px; word-wrap: break-word">
                    Rules</div>
                <div
                    style="align-self: stretch; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                    <div style="width: 24px; height: 24px; position: relative">
                        <div
                            style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9">
                        </div>
                        <div
                            style="width: 20px; height: 20px; left: 2px; top: 2px; position: absolute; background: var(--Icon-Error, #F43F5E)">
                        </div>
                    </div>
                    <div style="flex: 1 1 0; align-self: stretch"><span
                            style="color: var(--White, white); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">Keep
                            your Account Balance above $48,000 </span><span
                            style="color: var(--Text-Link, #FFD78A); font-size: 14px; font-family: Roboto; font-weight: 500; text-decoration: underline; line-height: 20px; word-wrap: break-word">Maximum
                            Loss Limmit</span></div>
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
                    <div style="font-size:12px; color:#a1a1aa; text-transform:uppercase; margin-bottom:8px;">Meta</div>
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
                        Usa este JSON para comparar con Postman. Si <strong>accountId</strong> no coincide con la cuenta
                        seleccionada, revisa el flujo del selector.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>