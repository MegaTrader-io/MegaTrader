<?php
/**
 * File: template-parts/account/account-data-global.php
 * Grid 3x2 de métricas globales (mock). Card #1: LWC Baseline puro (solo canvas).
 */
defined('ABSPATH') || exit;

/* ===================== MOCK DATA ===================== */
$mock = [
  'net_pnl' => -15070.00,
  'trades_count' => 407,
  'starting_balance' => 50000.00,
  'max_drawdown' => null,
  'profit_target' => null,
  'series' => [
    ['date' => '2025-10-28', 'value' => 51050],
    ['date' => '2025-10-29', 'value' => 50720],
    ['date' => '2025-10-30', 'value' => 49880],
    ['date' => '2025-10-31', 'value' => 49210],
    ['date' => '2025-11-01', 'value' => 48700],
    ['date' => '2025-11-02', 'value' => 48550],
    ['date' => '2025-11-03', 'value' => 48620],
    ['date' => '2025-11-04', 'value' => 48600],
    ['date' => '2025-11-05', 'value' => 48640],
    ['date' => '2025-11-06', 'value' => 48610],
  ],

  /* === Card #2 (solo lógica de barra basada en # trades) === */
  'trades_total' => 40,
  'trades_win' => 25,
  'trades_loss' => 15,

  'avg_win' => 277.66,
  'avg_loss' => 101.73,
  'avg_net' => 0.62,

  'current_day_streak_value' => '1 Day',
  'current_day_streak_w' => 1,
  'current_day_streak_l' => 3,

  'trade_win_pct' => 55.28,
  'wins_count' => 225,
  'loss_count' => 182,

  'profit_factor' => 0.76,

  'current_trades_value' => '2 Trades',
  'current_w_total' => 25,
  'current_l_total' => 15,
];

/* helpers */
function mt_money_compact($n)
{
  $s = $n < 0 ? '-' : '';
  $a = abs($n);
  if ($a >= 1000) {
    $k = $a / 1000;
    $num = (floor($k) == $k) ? number_format($k, 0) : rtrim(rtrim(number_format($k, 2, '.', ''), '0'), '.');
    return $s . '$' . $num . 'k';
  }
  $num = rtrim(rtrim(number_format($a, 2, '.', ''), '0'), '.');
  return $s . '$' . $num;
}
$net_pnl_str = mt_money_compact($mock['net_pnl']);
$wins_count = (int) $mock['wins_count'];
$loss_count = (int) $mock['loss_count'];

/* Card #2: widths por # de trades */
$tt = max(0, (int) $mock['trades_total']);
$tw = max(0, min($tt, (int) $mock['trades_win']));
$tl = max(0, (int) $mock['trades_loss']);
if ($tw + $tl !== $tt)
  $tl = max(0, $tt - $tw);
$w_pct = $tt ? ($tw / $tt) * 100 : 0;
$l_pct = 100 - $w_pct;
$has_gap = ($w_pct > 0 && $l_pct > 0);
?>
<!-- ===================== GRID 3x2 ===================== -->
<div class="mt-grid mt-grid--3x2" style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;">

  <!-- ========== CARD 1: Net P&L + LWC Baseline (solo canvas, sin ejes/bordes) ========== -->
  <div class="mt-card mt-card-chart">
    <div class="d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-2">
        <div class="mt-card__title__journal">
          Net P&amp;L
        </div>

        <!-- Tooltip -->
        <span class="mt-tooltip">
          <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0" aria-label="Net P&L information"></i>
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

      <div data-shape="Pill" data-size="sm" data-type="onHold"
        style="padding:4px 8px;background:var(--Colors-Gray-400,#A8A29E);border-radius:12px;display:flex;align-items:center;justify-content:center;">
        <div style="color:var(--Surface-Body,#131210);font-size:14px;font-weight:700;line-height:16px;">
          <?php echo (int) $mock['trades_count']; ?>
        </div>
      </div>
    </div>

    <div class="mt-card__kpi" style="margin-top:8px;color:var(--Text-Headings,#fff);font-size:20px;line-height:32px;">
      <?php echo esc_html($net_pnl_str); ?>
    </div>

    <div class="mt-card__chart" style="margin-top:16px;">
      <div id="adg-netpnl-chart" style="height:60px;width:100%;pointer-events:none;border:none;outline:none;"></div>
    </div>
  </div>

  <!-- ========== CARD 2: Avg. win/loss trade (barra apilada con mt-progress-bar) ========== -->
  <div class="mt-card">
    <div class="mt-card__wrapper">
      <div class="mt-card__header mt-card__header-col">
        <div class="mt-card__title">
          <div class="mt-card__title__journal">Avg. win/loss trade</div>
          <span class="mt-tooltip">
            <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0" aria-label="Avg win/loss info"></i>
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
        <div class="mt-card__header-info">
          <?php echo '$' . number_format($mock['avg_net'], 2); ?>
        </div>
      </div>
      <div class="mt-card__body">
        <div class="d-flex gap-1">
          <div style="flex:1;">
            <div style="color:var(--Text-Success,#2DD4BF);font-weight:700;">
              <?php echo '$' . number_format($mock['avg_win'], 2); ?>
            </div>
          </div>
          <div style="flex:1;">
            <div style="text-align:right;color:var(--Error-500,#F43F5E);font-weight:700;">
              <?php echo '$' . number_format($mock['avg_loss'], 2); ?>
            </div>
          </div>
        </div>

        <!-- Barra apilada usando TU componente mt-progress-bar -->
        <?php
        // % desde #trades
        $den = max(1, (int) $mock['trades_win'] + (int) $mock['trades_loss']);
        $rewardPct = (int) round(((int) $mock['trades_win'] / $den) * 100);
        $riskPct = 100 - $rewardPct;

        // mods para estados extremos / vacío
        $dualbarMods = [];
        if ($rewardPct === 100)
          $dualbarMods[] = 'mt-dualbar--reward-full';
        if ($riskPct === 100)
          $dualbarMods[] = 'mt-dualbar--risk-full';
        $isEmptyBars = ($rewardPct === 0 && $riskPct === 0);
        $dualbarClass = 'mt-dualbar' . ($isEmptyBars ? ' is-empty' : '') . (!empty($dualbarMods) ? ' ' . implode(' ', $dualbarMods) : '');
        ?>

        <div class="mt-rr" style="margin-top:8px;">
          <div class="<?php echo esc_attr($dualbarClass); ?>" data-reward="<?php echo (int) $rewardPct; ?>"
            data-risk="<?php echo (int) $riskPct; ?>">
            <span class="mt-dualbar__seg mt-dualbar__seg--reward"></span>
            <span class="mt-dualbar__seg mt-dualbar__seg--risk"></span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ========== CARD 3: Current Day Streak (misma estructura que Card 2/4) ========== -->
  <div class="mt-card">
    <div class="mt-card__wrapper">
      <div class="mt-card__header mt-card__header-col">
        <div class="mt-card__title">
          <div class="mt-card__title__journal">Current Day Streak</div>
          <span class="mt-tooltip">
            <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0" aria-label="Streak info"></i>
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
        <div class="mt-card__header-info d-flex align-items-center gap-2">
          <?php echo esc_html($mock['current_day_streak_value']); ?>
          <span class="mt-icon mt-icon-error mt-icon_arrow-down-solid" tabindex="0" aria-label="Streak info"></span>
        </div>
      </div>

      <div class="mt-card__body">
        <div class="d-flex gap-2">
          <div class="mt-badge mt-badge-xs mt-badge-calendar mt-badge-secondary-800 ">
            <?php echo (int) $mock['current_day_streak_w']; ?>W
          </div>
          <div class="mt-badge mt-badge-xs mt-badge-calendar mt-badge-error-900">
            <?php echo (int) $mock['current_day_streak_l']; ?>L
          </div>
        </div>
      </div>
    </div>
  </div>


  <!-- ========== CARD 4: Trade Win % (misma estructura que Card 2) ========== -->
  <?php
  // % desde wins_count / loss_count
  $tt2 = max(1, (int) $wins_count + (int) $loss_count);
  $rewardPct2 = (int) round(((int) $wins_count / $tt2) * 100);
  $riskPct2 = 100 - $rewardPct2;

  // mods/extremos
  $dualbarMods2 = [];
  if ($rewardPct2 === 100)
    $dualbarMods2[] = 'mt-dualbar--reward-full';
  if ($riskPct2 === 100)
    $dualbarMods2[] = 'mt-dualbar--risk-full';
  $isEmptyBars2 = ($rewardPct2 === 0 && $riskPct2 === 0);
  $dualbarClass2 = 'mt-dualbar' . ($isEmptyBars2 ? ' is-empty' : '') . (!empty($dualbarMods2) ? ' ' . implode(' ', $dualbarMods2) : '');
  ?>
  <div class="mt-card">
    <div class="mt-card__wrapper">
      <div class="mt-card__header mt-card__header-col">
        <div class="mt-card__title">
          <div class="mt-card__title__journal">Trade Win %</div>
          <span class="mt-tooltip">
            <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0" aria-label="Trade Win info"></i>
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
        <div class="mt-card__header-info">
          <?php echo number_format($mock['trade_win_pct'], 2); ?>%
        </div>
      </div>

      <div class="mt-card__body">
        <div class="d-flex gap-1" style="display:flex;gap:4px;align-items:flex-end;">
          <div style="flex:1;">
            <div style="color:var(--Text-Success,#2DD4BF);font-weight:700;"><?php echo (int) $wins_count; ?></div>
          </div>
          <div style="flex:1;">
            <div style="text-align:right;color:var(--Error-500,#F43F5E);font-weight:700;">
              <?php echo (int) $loss_count; ?>
            </div>
          </div>
        </div>

        <div class="mt-rr" style="margin-top:8px;">
          <div class="<?php echo esc_attr($dualbarClass2); ?>" data-reward="<?php echo (int) $rewardPct2; ?>"
            data-risk="<?php echo (int) $riskPct2; ?>">
            <span class="mt-dualbar__seg mt-dualbar__seg--reward"></span>
            <span class="mt-dualbar__seg mt-dualbar__seg--risk"></span>
          </div>
        </div>
      </div>
    </div>
  </div>


  <!-- ========== CARD 5: Profit Factor (misma estructura que Card 2/4) ========== -->
  <?php
  $pf = (float) ($mock['profit_factor'] ?? 0);
  $f = max(0.0, min(1.0, $pf / (1.0 + $pf)));   // parte verde (0..1)
  $R = 24;                                      // radio del trazo
  $C = 2 * M_PI * $R;                           // circunferencia
  $dashRed = $C * (1 - $f);                    // largo rojo
  $offsetRed = $C * $f;                          // arranca tras el verde
  $epsilon = 0.05;                             // anti-costura
  $maskId = 'pfmask_' . wp_rand(1000, 9999);
  $green = 'var(--Success-400,#2DD4BF)';
  $red = 'var(--Error-500,#F43F5E)';
  ?>
  <div class="mt-card">
    <div class="mt-card__wrapper">
      <div class="mt-card__header mt-card__header-col">
        <div class="mt-card__title">
          <div class="mt-card__title__journal">Profit Factor</div>
          <span class="mt-tooltip">
            <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0" aria-label="Profit Factor info"></i>
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
        <div class="mt-card__header-info">
          <?php echo number_format($pf, 2); ?>
        </div>
      </div>

      <div class="mt-card__body">
        <div class="pf-wrap">
          <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 60 60" fill="none"
            shape-rendering="geometricPrecision">
            <!-- Aro verde base (Figma) -->
            <path
              d="M60 30C60 46.5685 46.5685 60 30 60C13.4315 60 0 46.5685 0 30C0 13.4315 13.4315 0 30 0C46.5685 0 60 13.4315 60 30ZM6 30C6 43.2548 16.7452 54 30 54C43.2548 54 54 43.2548 54 30C54 16.7452 43.2548 6 30 6C16.7452 6 6 16.7452 6 30Z"
              fill="<?php echo esc_attr($green); ?>" />
            <!-- Máscara del anillo -->
            <mask id="<?php echo esc_attr($maskId); ?>" maskUnits="userSpaceOnUse" x="0" y="0" width="60" height="60">
              <path
                d="M60 30C60 46.5685 46.5685 60 30 60C13.4315 60 0 46.5685 0 30C0 13.4315 13.4315 0 30 0C46.5685 0 60 13.4315 60 30ZM6 30C6 43.2548 16.7452 54 30 54C43.2548 54 54 43.2548 54 30C54 16.7452 43.2548 6 30 6C16.7452 6 6 16.7452 6 30Z"
                fill="#fff" />
            </mask>
            <!-- Arco rojo encima (recortado), con offset según proporción verde -->
            <g mask="url(#<?php echo esc_attr($maskId); ?>)">
              <circle cx="30" cy="30" r="<?php echo $R; ?>" fill="none" stroke="<?php echo esc_attr($red); ?>"
                stroke-width="12" stroke-linecap="butt" transform="rotate(-90 30 30)"
                stroke-dasharray="<?php echo ($dashRed + $epsilon) . ' ' . $C; ?>"
                stroke-dashoffset="<?php echo $offsetRed; ?>" />
            </g>
          </svg>
        </div>
      </div>
    </div>
  </div>


  <!-- ========== CARD 6: Current Day Trades (misma estructura que Card 3) ========== -->
  <div class="mt-card">
    <div class="mt-card__wrapper">
      <div class="mt-card__header mt-card__header-col">
        <div class="mt-card__title">
          <div class="mt-card__title__journal">Current Day Trades</div>
          <span class="mt-tooltip">
            <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0" aria-label="Current trades info"></i>
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
        <div class="mt-card__header-info d-flex align-items-center gap-2">
          <?php echo esc_html($mock['current_trades_value']); ?>
          <span class="mt-icon mt-icon-error mt-icon_arrow-down-solid" tabindex="0" aria-label="Trades info"></span>
        </div>
      </div>

      <div class="mt-card__body">
        <div class="d-flex gap-2">
          <div class="mt-badge mt-badge-xs mt-badge-calendar mt-badge-secondary-800">
            <?php echo (int) $mock['current_w_total']; ?>W
          </div>
          <div class="mt-badge mt-badge-xs mt-badge-calendar mt-badge-error-900">
            <?php echo (int) $mock['current_l_total']; ?>L
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<style>
  /* Scope solo al primer card con el mini-chart */
  .mt-card.mt-card-chart .mt-card__chart {
    margin-top: 0 !important;
  }

  .mt-card.mt-card-chart table {
    border: 0 !important;
    margin: 0 !important;
    border-collapse: collapse;
  }

  .mt-card.mt-card-chart table td,
  .mt-card.mt-card-chart table th {
    border: 0 !important;
    margin: 0 !important;
    padding: 0;
  }
</style>

<!-- ===================== SCRIPT: LWC Baseline (solo canvas) ===================== -->
<script>
  (function () {
    const container = document.getElementById('adg-netpnl-chart');
    if (!container) return;
    if (container.dataset.mounted === '1') return;
    container.dataset.mounted = '1';

    function loadLWC() {
      if (window.LightweightCharts) return Promise.resolve();
      if (window.__LWC_LOADING__) return window.__LWC_LOADING__;
      const url = "https://unpkg.com/lightweight-charts@4.1.1/dist/lightweight-charts.standalone.production.js";
      window.__LWC_LOADING__ = new Promise((res, rej) => {
        const s = document.createElement('script'); s.src = url; s.async = true;
        s.onload = () => res(); s.onerror = () => rej(new Error('LWC load failed'));
        document.head.appendChild(s);
      });
      return window.__LWC_LOADING__;
    }

    const RAW_SERIES = <?php echo wp_json_encode($mock['series'], JSON_UNESCAPED_SLASHES); ?>;
    const START_BAL = <?php echo json_encode((float) $mock['starting_balance']); ?>;

    const data = (RAW_SERIES || []).map(p => {
      const v = Number(p?.value);
      const d = String(p?.date || '').slice(0, 10);
      if (!d || !Number.isFinite(v)) return null;
      return { time: d, value: v };
    }).filter(Boolean);

    loadLWC().then(() => {
      const chart = LightweightCharts.createChart(container, {
        layout: { background: { type: 'Solid', color: 'transparent' }, textColor: '#dcdcdc' },
        leftPriceScale: { visible: false },
        rightPriceScale: { visible: false, borderVisible: false },
        timeScale: { visible: false, borderVisible: false, lockVisibleTimeRangeOnResize: true, fixLeftEdge: true, fixRightEdge: true, timeVisible: false, secondsVisible: false },
        grid: { vertLines: { visible: false }, horzLines: { visible: false } },
        crosshair: { mode: 0 },
        handleScroll: { mouseWheel: false, pressedMouseMove: false, horzTouchDrag: false, vertTouchDrag: false },
        handleScale: { axisPressedMouseMove: false, mouseWheel: false, pinch: false },
      });

      const baseline = chart.addBaselineSeries({
        baseValue: { type: 'price', price: START_BAL },
        priceFormat: { type: 'price', precision: 2, minMove: 0.01 },
        topLineColor: '#24b8a6',
        bottomLineColor: '#FF4D4D',
        topFillColor1: 'rgba(36,184,166,0.28)',
        topFillColor2: 'rgba(36,184,166,0.06)',
        bottomFillColor1: 'rgba(255,77,77,0.20)',
        bottomFillColor2: 'rgba(255,77,77,0.00)',
        lineWidth: 1,
        lastValueVisible: false,
        priceLineVisible: false
      });

      baseline.setData(data);
      chart.timeScale().fitContent();

      container.style.border = 'none';
      container.style.outline = 'none';
      container.style.pointerEvents = 'none';
    }).catch(e => console.error('[ADG] LWC error', e));
  })();
</script>