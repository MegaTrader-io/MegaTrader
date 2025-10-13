<?php
/**
 * Sección de Overview con tabs/slider + panel por tab.
 * - Flechas usan: mt-btn mt-btn--sm mt-btn--secondary
 * - Iconos flecha: .mt-icon .mt-icon_chevron-left / right
 * - Tabs (chips):  .mt-toggle-button
 * - Panel: .mt-feature-panel (solo visible el del tab activo)
 */

defined('ABSPATH') || exit;

$accountId = isset($args['meta']['accountId']) ? sanitize_text_field((string) $args['meta']['accountId']) : '';
$feature = isset($args['feature']) && is_array($args['feature']) ? $args['feature'] : [];
$account = $feature['account'] ?? null;

$apiData = isset($feature['apiData']) && is_array($feature['apiData']) ? $feature['apiData'] : null;

/** 2) Fallback: construir apiData aquí si no vino desde el overview */
if (!$apiData) {
    // Leer desde metrics|metric en la cuenta (API trae win/loss en 0..100)
    $m = is_array($account) ? ($account['metrics'] ?? $account['metric'] ?? []) : [];
    $avgWin = is_numeric($m['averageWin'] ?? null) ? (float) $m['averageWin'] : 0.0;
    $avgLoss = is_numeric($m['averageLoss'] ?? null) ? (float) $m['averageLoss'] : 0.0;
    $winRate = is_numeric($m['winRate'] ?? null) ? (float) $m['winRate'] : 0.0; // 0..100
    $lossRate = is_numeric($m['lossRate'] ?? null) ? (float) $m['lossRate'] : 0.0; // 0..100

    // Normalizar a 0..1 (lo que espera la UI)
    if ($winRate > 1)
        $winRate /= 100;
    if ($lossRate > 1)
        $lossRate /= 100;
    $winRate = max(0.0, min(1.0, $winRate));
    $lossRate = max(0.0, min(1.0, $lossRate));

    $apiData = [
        'overview' => [
            'accountId' => $accountId,
            'averageWin' => $avgWin,
            'averageLoss' => $avgLoss,
            'winRate' => $winRate,   // 0..1
            'lossRate' => $lossRate,  // 0..1
        ],
    ];
}

/* ------- EJEMPLO DE TABS ------- */
$tabs = [
    ['id' => 'overview', 'label' => 'Overview', 'active' => true],
];
?>

<section class="mt-feature-tabs" data-account-id="<?php echo esc_attr($accountId); ?>">
    <div class="mt-tabs-row d-none">
        <!-- Flecha izquierda -->
        <button type="button" class="mt-btn mt-btn--sm mt-btn--secondary js-tabs-prev" aria-label="Previous">
            <span class="mt-icon mt-icon_chevron-left"></span>
        </button>

        <!-- Viewport + gradientes + lista -->
        <div class="mt-tabs-viewport">
            <div class="mt-tabs-gradient mt-tabs-gradient--left" aria-hidden="true"></div>

            <div data-fc-tabs role="tablist" aria-label="Markets" class="mt-toggle-group">
                <?php foreach ($tabs as $t):
                    $active = !empty($t['active']); ?>
                    <div data-fc-tab data-id="<?php echo esc_attr($t['id']); ?>"
                        data-status="<?php echo $active ? 'active' : 'normal'; ?>"
                        class="mt-toggle-button <?php echo $active ? 'active' : ''; ?>" role="tab"
                        aria-selected="<?php echo $active ? 'true' : 'false'; ?>"
                        tabindex="<?php echo $active ? '0' : '-1'; ?>">
                        <span class="mt-fc-tab-label"><?php echo esc_html($t['label']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-tabs-gradient mt-tabs-gradient--right" aria-hidden="true"></div>
        </div>

        <!-- Flecha derecha -->
        <button type="button" class="mt-btn mt-btn--sm mt-btn--secondary js-tabs-next" aria-label="Next">
            <span class="mt-icon mt-icon_chevron-right"></span>
        </button>
    </div>

    <?php
    /* ------- PANELS (uno por tab) ------- */
    foreach ($tabs as $t):
        $id = $t['id'];
        $active = !empty($t['active']);

        $d = $apiData[$id] ?? ['averageWin' => 0, 'averageLoss' => 0, 'winRate' => 0, 'lossRate' => 0];
        $avgWin = (float) ($d['averageWin'] ?? 0);
        $avgLoss = (float) ($d['averageLoss'] ?? 0);
        $winRate = max(0.0, min(1.0, (float) ($d['winRate'] ?? 0)));
        $lossRate = max(0.0, min(1.0, (float) ($d['lossRate'] ?? 0)));

        // ¿Hay data?
        $hasData = ($avgWin != 0 || $avgLoss != 0 || $winRate > 0 || $lossRate > 0);

        // Donuts (porcentaje)
        $winPct = (int) round($winRate * 100);
        $lossPct = (int) round($lossRate * 100);

        // Barras Reward/Risk basadas en los averages
        $reward = 0;
        $risk = 0;

        if ($hasData) {
            $reward = max(0, $avgWin);
            $risk = abs($avgLoss);

            $sum = ($reward + $risk) ?: 1; // seguridad ante 0/0
            $rewardPct = (int) round($reward / $sum * 100);
            $riskPct = 100 - $rewardPct;
        } else {
            // Sin data: ambas barras vacías (0%)
            $rewardPct = 0;
            $riskPct = 0;
        }

        // Clases extra para dualbar cuando uno es 100%
        $dualbarMods = [];
        if ($rewardPct === 100) $dualbarMods[] = 'mt-dualbar--reward-full';
        if ($riskPct   === 100) $dualbarMods[] = 'mt-dualbar--risk-full';

        // Clases de barra (solo agregamos color si hay % > 0)
        $rewardBarClass = 'mt-progress-bar' . ($rewardPct > 0 ? ' mt-progress-bar--success' : '');
        $riskBarClass = 'mt-progress-bar' . ($riskPct > 0 ? ' mt-progress-bar--error' : '');

        // Texto ratio solo si ambas partes > 0
        $tradesCount = isset($account['metrics']['tradesCount'])
            ? (int) $account['metrics']['tradesCount']
            : null;

        // No trades → "—"
        if ($tradesCount !== null && $tradesCount <= 0) {
            $ratioText = null; // el template ya muestra "—" cuando es null
        } elseif ((float) $avgLoss === 0.0) {
            // No losses → "1:∞" (o usa null si prefieres "—")
            $ratioText = '1:∞';
        } else {
            // Normal: 1 : (avgWin / |avgLoss|)
            $ratio = (float) $avgWin / max(0.000001, abs((float) $avgLoss));
            $ratioText = '1:' . number_format($ratio, 2);
        }
        ?>

        <div class="mt-feature-panel" data-fc-panel data-panel-for="<?php echo esc_attr($id); ?>" <?php echo $active ? '' : 'hidden'; ?>>
            <div class="mt-summary w-100">
                <!-- Col izquierda -->
                <div class="mt-summary__col">
                    <div class="mt-summary__title">
                        <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['feature_content_winning_trades']); ?></div>

                    <div class="mt-donut <?php echo $winPct ? 'is-success' : 'is-empty'; ?>"
                        data-donut-value="<?php echo $winPct; ?>">
                        <svg class="mt-donut__svg" viewBox="0 0 100 100" aria-hidden="true">
                            <circle class="mt-donut__track" cx="50" cy="50" r="45" pathLength="99"></circle>
                            <circle class="mt-donut__value" cx="50" cy="50" r="45" pathLength="99"></circle>
                        </svg>
                        <div class="mt-donut__center">
                            <span class="mt-donut__percent"><?php echo $winPct ? $winPct . '%' : '--'; ?></span>
                        </div>
                    </div>

                    <div class="mt-summary__avg">
                        <div class="mt-summary__avg-label">
                            <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['feature_content_avg_winning_trade']); ?></div>
                        <div class="mt-summary__avg-value mt-summary__avg-value--success">
                            <?php echo $avgWin ? '$' . number_format($avgWin, 2) : '$0.00'; ?>
                        </div>
                    </div>
                </div>

                <div class="mt-summary__divider" aria-hidden="true"></div>

                <!-- Col centro -->
                <div class="mt-summary__col">
                    <div class="mt-summary__title mt-summary__title--center">
                        <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['feature_content_risk_reward_ratio']); ?>
                        <span class="mt-tooltip">
                            <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0"
                                aria-label="Reward-to-risk ratio"></i>
                            <span class="mt-tooltip__panel" role="tooltip">
                                <div class="mt-tooltip__title">
                                    <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['feature_content_risk_reward_ratio_tooltip_title']); ?>
                                </div>
                                <div class="mt-tooltip__body">
                                    <?php echo wp_kses_post(Label::META_ACCOUNT_OVERVIEW['feature_content_risk_reward_ratio_tooltip_description']); ?>
                                </div>

                            </span>
                        </span>
                    </div>

                    <?php if ($hasData && $ratioText): ?>
                        <div class="mt-summary__ratio">
                            <div class="mt-summary__ratio-value"><?php echo esc_html($ratioText); ?></div>
                        </div>
                    <?php else: ?>
                        <div class="mt-summary__nodata">
                            <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['feature_content_no_data']); ?></div>
                    <?php endif; ?>

                    <!-- Nueva barra combinada -->
                    <div class="mt-rr">
                        <div class="mt-rr__head">
                            <span
                                class="mt-rr-title success"><?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['feature_content_reward']); ?></span>
                            <span
                                class="mt-rr-title error"><?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['feature_content_risk']); ?></span>
                        </div>

                        <?php
                        $isEmptyBars = ($rewardPct === 0 && $riskPct === 0);
                        $dualbarClass = 'mt-dualbar' . ($isEmptyBars ? ' is-empty' : '') . (!empty($dualbarMods) ? ' ' . implode(' ', $dualbarMods) : '');
                        ?>
                        <div class="<?php echo esc_attr($dualbarClass); ?>"
                            data-reward="<?php echo $rewardPct; ?>" data-risk="<?php echo $riskPct; ?>">
                            <span class="mt-dualbar__seg mt-dualbar__seg--reward"></span>
                            <span class="mt-dualbar__seg mt-dualbar__seg--risk"></span>
                        </div>
                    </div>
                </div>

                <div class="mt-summary__divider" aria-hidden="true"></div>

                <!-- Col derecha -->
                <div class="mt-summary__col">
                    <div class="mt-summary__title">
                        <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['feature_content_losing_trades']); ?></div>

                    <div class="mt-donut <?php echo $lossPct ? 'is-error' : 'is-empty'; ?>"
                        data-donut-value="<?php echo $lossPct; ?>">
                        <svg class="mt-donut__svg" viewBox="0 0 100 100" aria-hidden="true">
                            <circle class="mt-donut__track" cx="50" cy="50" r="45" pathLength="99"></circle>
                            <circle class="mt-donut__value" cx="50" cy="50" r="45" pathLength="99"></circle>
                        </svg>
                        <div class="mt-donut__center">
                            <span class="mt-donut__percent"><?php echo $lossPct ? $lossPct . '%' : '--' ?></span>
                        </div>
                    </div>

                    <div class="mt-summary__avg">
                        <div class="mt-summary__avg-label">
                            <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['feature_content_avg_losing_trade']); ?></div>
                        <div class="mt-summary__avg-value mt-summary__avg-value--error">
                            <?php
                            if ($avgLoss) {
                                $sign = $avgLoss < 0 ? '-' : '';
                                echo $sign . '$' . number_format(abs($avgLoss), 2);
                            } else {
                                echo '$0.00';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</section>
