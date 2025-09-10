<?php
/**
 * Template Part: Account Performance (datos)
 * Ruta: template-parts/account/account-performance.php
 *
 * Espera recibir un array en $args['performance'] (WP >= 5.5)
 * o, en WP antiguos, vía set_query_var('mt_performance', $array).
 */

defined('ABSPATH') || exit;

// Recibe args de forma compatible
$performance = [];
if (isset($args) && is_array($args) && isset($args['performance'])) {
    $performance = $args['performance'];
} else {
    $maybe = get_query_var('mt_performance');
    if (!empty($maybe) && is_array($maybe)) {
        $performance = $maybe;
    }
}

if (empty($performance)) {
    // Nada que mostrar aún (no maquetamos aquí)
    return;
}

// Normaliza claves (con default) para evitar notices
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
$performance = array_merge($defaults, $performance);


?>
<div class="mt-card mt-card__row gap-32" data-component="account-performance">
    <div class="w-100 d-flex flex-column gap-32">
        <div class="d-flex flex-column gap-3">
            <div class="mt-card__title__text fw-medium text-uppercase">
                Overall performance</div>
            <div
                style="align-self: stretch; flex-direction: column; justify-content: flex-start; align-items: flex-start; display: flex">
                <div
                    style="align-self: stretch; padding-top: 16px; padding-bottom: 16px; border-bottom: 1px var(--Colors-Gray-700, #404040) solid; justify-content: space-between; align-items: center; display: inline-flex">
                    <div
                        style="flex: 1 1 0; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">
                        Account Balance</div>
                    <div
                        style="text-align: right; color: var(--Error-500, #F43F5E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">
                        $47,850.30</div>
                </div>
                <div
                    style="align-self: stretch; padding-top: 16px; padding-bottom: 16px; border-bottom: 1px var(--Colors-Gray-700, #404040) solid; justify-content: space-between; align-items: center; display: inline-flex">
                    <div
                        style="flex: 1 1 0; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">
                        Total Profit</div>
                    <div
                        style="align-self: stretch; justify-content: flex-start; align-items: flex-start; gap: 8px; display: flex">
                        <div
                            style="color: var(--Error-500, #F43F5E); font-size: 16px; font-family: Roboto; font-weight: 500; text-transform: uppercase; line-height: 24px; word-wrap: break-word">
                            -$1,149.70</div>
                        <div style="justify-content: flex-start; align-items: center; display: flex">
                            <div style="width: 24px; height: 24px; position: relative">
                                <div
                                    style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9">
                                </div>
                                <div
                                    style="width: 12px; height: 13px; left: 6px; top: 5px; position: absolute; background: var(--Icon-Error, #F43F5E)">
                                </div>
                            </div>
                            <div data-shape="Pill" data-size="sm" data-type="error"
                                style="padding-left: 8px; padding-right: 8px; padding-top: 4px; padding-bottom: 4px; background: var(--Error-500, #F43F5E); border-radius: 12px; justify-content: center; align-items: center; gap: 10px; display: flex">
                                <div
                                    style="color: var(--Surface-Body, #131210); font-size: 14px; font-family: Roboto; font-weight: 700; text-transform: uppercase; line-height: 16px; word-wrap: break-word">
                                    - 4.30%</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    style="align-self: stretch; padding-top: 16px; padding-bottom: 16px; border-bottom: 1px var(--Colors-Gray-700, #404040) solid; justify-content: space-between; align-items: center; display: inline-flex">
                    <div
                        style="flex: 1 1 0; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">
                        Days Loss Limit</div>
                    <div style="width: 148px; justify-content: flex-end; align-items: center; gap: 8px; display: flex">
                        <div
                            style="text-align: right; color: var(--Text-Headings, white); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">
                            $2,000</div>
                        <div style="width: 24px; height: 24px; position: relative">
                            <div
                                style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9">
                            </div>
                            <div
                                style="width: 20px; height: 20px; left: 2px; top: 2px; position: absolute; background: var(--Text-Body, #A8A29E)">
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    style="align-self: stretch; padding-top: 16px; padding-bottom: 16px; border-bottom: 1px var(--Colors-Gray-700, #404040) solid; justify-content: space-between; align-items: center; display: inline-flex">
                    <div
                        style="flex: 1 1 0; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">
                        Current Equity</div>
                    <div
                        style="text-align: right; color: var(--Text-Headings, white); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">
                        $47,850.30</div>
                </div>
                <div
                    style="align-self: stretch; padding-top: 16px; padding-bottom: 16px; justify-content: space-between; align-items: center; display: inline-flex">
                    <div style="flex: 1 1 0; justify-content: flex-start; align-items: center; gap: 4px; display: flex">
                        <div
                            style="color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">
                            Weekly Net P&L</div>
                        <div style="width: 24px; height: 24px; position: relative">
                            <div
                                style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9">
                            </div>
                            <div
                                style="width: 20px; height: 20px; left: 2px; top: 2px; position: absolute; background: var(--Text-Body, #A8A29E)">
                            </div>
                        </div>
                    </div>
                    <div
                        style="text-align: right; color: var(--Error-500, #F43F5E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">
                        -$1,560.40</div>
                </div>
            </div>
        </div>
    </div>
    <div class="w-100 d-flex flex-column gap-32">
        <div class="d-flex flex-column gap-3">
            <div class="mt-card__title__text fw-medium text-uppercase">Your Challenge Objective</div>
            <div
                style="align-self: stretch; flex-direction: column; justify-content: flex-start; align-items: flex-start; display: flex">
                <div
                    style="align-self: stretch; padding-top: 16px; padding-bottom: 16px; border-bottom: 1px var(--Colors-Gray-700, #404040) solid; justify-content: flex-start; align-items: center; gap: 16px; display: inline-flex">
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
                <div
                    style="align-self: stretch; padding-top: 16px; padding-bottom: 16px; border-bottom: 1px var(--Colors-Gray-700, #404040) solid; justify-content: flex-start; align-items: center; gap: 16px; display: inline-flex">
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
    <!-- TODO: reemplazar por tu HTML real -->
    <pre style="display:none;"><?php echo esc_html(wp_json_encode($performance)); ?></pre>

    <?php /* Ejemplo de variables listas para usar:
$balance      = $performance['currentBalance'];
$equity       = $performance['currentEquity'];
$profit       = $performance['currentProfit'];
$profitPct    = $performance['currentProfitPercent'];
$days         = $performance['activeTradingDays'];
$dailyPnL     = $performance['dailyTotalPnL'];
$minDays      = $performance['minTradingDays'];
$maxLossEq    = $performance['maxLossLimitEquityLevel'];
$target       = $performance['target'];
*/ ?>
</div>