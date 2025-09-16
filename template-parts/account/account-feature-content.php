<?php
/**
 * Account Overview – Feature Content (tabs + panels)
 * Ajusta $tabs y $apiData a tu realidad / API.
 */

// Tabs de ejemplo
$tabs = [
  ['id' => 'overview', 'label' => 'Overview',       'active' => true],
  ['id' => 'es',       'label' => 'E-mini S&P 500', 'active' => false],
  ['id' => 'nq',       'label' => 'E-mini NASDAQ 100', 'active' => false],
  ['id' => 'rt',       'label' => 'E-mini Russell 2000', 'active' => false],
];

// Simulación de API por tab (reemplaza por tu data real)
$apiData = [
  'overview' => ['averageWin'=>0,      'averageLoss'=>0,       'winRate'=>0,   'lossRate'=>0],
  'es'       => ['averageWin'=>137.37, 'averageLoss'=>-209.26, 'winRate'=>0.30,'lossRate'=>0.70],
  'nq'       => ['averageWin'=> 85.12, 'averageLoss'=>-140.00, 'winRate'=>0.42,'lossRate'=>0.58],
  'rt'       => ['averageWin'=> 62.00, 'averageLoss'=> -90.00, 'winRate'=>0.55,'lossRate'=>0.45],
];

?>
<section class="mt-feature-tabs" id="mt-account-feature">

  <!-- fila de tabs / carrusel -->
  <div class="mt-tabs-row">

    <button type="button"
            class="mt-btn mt-btn--sm mt-btn--secondary"
            data-fc-prev
            aria-label="Previous tab">
      <span class="mt-icon mt-icon_chevron-left"></span>
    </button>

    <div class="mt-tabs-viewport">
      <div class="mt-tabs-gradient mt-tabs-gradient--left" aria-hidden="true"></div>

      <div class="mt-toggle-group"
           data-fc-tabs
           role="tablist"
           aria-label="Markets">
        <?php foreach ($tabs as $t): ?>
          <?php $active = !empty($t['active']); ?>
          <button
            class="mt-toggle-button<?php echo $active ? ' active' : ''; ?>"
            data-fc-tab
            data-id="<?php echo esc_attr($t['id']); ?>"
            data-status="<?php echo $active ? 'active' : 'normal'; ?>"
            role="tab"
            aria-selected="<?php echo $active ? 'true' : 'false'; ?>"
            tabindex="<?php echo $active ? '0' : '-1'; ?>">
            <span class="mt-fc-tab-label"><?php echo esc_html($t['label']); ?></span>
          </button>
        <?php endforeach; ?>
      </div>

      <div class="mt-tabs-gradient mt-tabs-gradient--right" aria-hidden="true"></div>
    </div>

    <button type="button"
            class="mt-btn mt-btn--sm mt-btn--secondary"
            data-fc-next
            aria-label="Next tab">
      <span class="mt-icon mt-icon_chevron-right"></span>
    </button>

  </div>

  <?php
  // ==== PANELS ====
  foreach ($tabs as $t):
    $id     = $t['id'];
    $active = !empty($t['active']);

    $d        = $apiData[$id] ?? ['averageWin'=>0,'averageLoss'=>0,'winRate'=>0,'lossRate'=>0];
    $avgWin   = (float)($d['averageWin'] ?? 0);
    $avgLoss  = (float)($d['averageLoss'] ?? 0);
    $winRate  = max(0.0, min(1.0, (float)($d['winRate']  ?? 0)));
    $lossRate = max(0.0, min(1.0, (float)($d['lossRate'] ?? 0)));

    $hasData  = ($avgWin != 0 || $avgLoss != 0 || $winRate > 0 || $lossRate > 0);

    $winPct   = (int)round($winRate  * 100);
    $lossPct  = (int)round($lossRate * 100);

    $reward   = max(0, $avgWin);
    $risk     = abs($avgLoss);
    $sum      = ($reward + $risk) ?: 1;
    $rewardPct = (int)round($reward / $sum * 100);
    $riskPct   = 100 - $rewardPct;

    $ratioText = ($reward > 0 && $risk > 0)
      ? ('1:' . number_format($risk / $reward, 2))
      : null;
  ?>
    <div class="mt-feature-panel"
         data-fc-panel
         data-panel-for="<?php echo esc_attr($id); ?>"
         <?php echo $active ? '' : 'hidden'; ?>>

      <div class="mt-summary">

        <!-- LEFT: Winning -->
        <div class="mt-summary__col">
          <div class="mt-summary__title">Winning Trade</div>

          <div class="mt-donut <?php echo $winPct ? 'is-success' : 'is-empty'; ?>"
               data-donut-value="<?php echo $winPct; ?>">
            <svg class="mt-donut__svg" viewBox="0 0 100 100" aria-hidden="true">
              <circle class="mt-donut__track" cx="50" cy="50" r="45" pathLength="100"></circle>
              <circle class="mt-donut__value" cx="50" cy="50" r="45" pathLength="100"></circle>
            </svg>
            <div class="mt-donut__center">
              <span class="mt-donut__percent"><?php echo $winPct ? $winPct.'%' : '--'; ?></span>
            </div>
          </div>

          <div class="mt-summary__avg">
            <div class="mt-summary__avg-label">Avg. Win</div>
            <div class="mt-summary__avg-value mt-summary__avg-value--success">
              <?php echo $avgWin ? '$'.number_format($avgWin, 2) : '--'; ?>
            </div>
          </div>
        </div>

        <div class="mt-summary__divider" aria-hidden="true"></div>

        <!-- CENTER: Ratio + Reward/Risk bars -->
        <div class="mt-summary__col">
          <div class="mt-summary__title mt-summary__title--center">
            Reward-to-Risk Ratio
            <span class="mt-icon mt-icon_info-solid" aria-hidden="true"></span>
          </div>

          <?php if ($hasData && $ratioText): ?>
            <div class="mt-summary__ratio">
              <div class="mt-summary__ratio-value"><?php echo esc_html($ratioText); ?></div>
            </div>
          <?php else: ?>
            <div class="mt-summary__nodata">NO DATA AVAILABLE</div>
          <?php endif; ?>

          <div class="mt-summary__bars">
            <div class="mt-summary__bar" data-bar="reward" data-progress="<?php echo $rewardPct; ?>">
              <div class="mt-summary__bar-label mt-summary__bar-label--success">Reward</div>
              <div class="mt-progress-bar mt-progress-bar--success">
                <div class="mt-progress-bar__fill"></div>
              </div>
            </div>
            <div class="mt-summary__bar" data-bar="risk" data-progress="<?php echo $riskPct; ?>">
              <div class="mt-summary__bar-label mt-summary__bar-label--error">Risk</div>
              <div class="mt-progress-bar mt-progress-bar--error">
                <div class="mt-progress-bar__fill"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-summary__divider" aria-hidden="true"></div>

        <!-- RIGHT: Losing -->
        <div class="mt-summary__col">
          <div class="mt-summary__title">Lossing Trade</div>

          <div class="mt-donut <?php echo $lossPct ? 'is-error' : 'is-empty'; ?>"
               data-donut-value="<?php echo $lossPct; ?>">
            <svg class="mt-donut__svg" viewBox="0 0 100 100" aria-hidden="true">
              <circle class="mt-donut__track" cx="50" cy="50" r="45" pathLength="100"></circle>
              <circle class="mt-donut__value" cx="50" cy="50" r="45" pathLength="100"></circle>
            </svg>
            <div class="mt-donut__center">
              <span class="mt-donut__percent"><?php echo $lossPct ? $lossPct.'%' : '--'; ?></span>
            </div>
          </div>

          <div class="mt-summary__avg">
            <div class="mt-summary__avg-label">Avg. Loss</div>
            <div class="mt-summary__avg-value mt-summary__avg-value--error">
              <?php
              if ($avgLoss) {
                $sign = $avgLoss < 0 ? '-' : '';
                echo $sign.'$'.number_format(abs($avgLoss), 2);
              } else {
                echo '--';
              }
              ?>
            </div>
          </div>
        </div>

      </div>
    </div>
  <?php endforeach; ?>

</section>
