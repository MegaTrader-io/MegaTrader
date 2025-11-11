<?php
/**
 * File: template-parts/account/account-data-global.php
 * Grid 3x2 de métricas globales (mock). Card #1: LWC Baseline puro (solo canvas).
 */
defined('ABSPATH') || exit;

/* ===================== MOCK DATA ===================== */
$mock = [
  'net_pnl'          => -15070.00,
  'trades_count'     => 407,
  'starting_balance' => 50000.00,
  'max_drawdown'     => null,
  'profit_target'    => null,
  'series' => [
    ['date'=>'2025-10-28','value'=>51050],
    ['date'=>'2025-10-29','value'=>50720],
    ['date'=>'2025-10-30','value'=>49880],
    ['date'=>'2025-10-31','value'=>49210],
    ['date'=>'2025-11-01','value'=>48700],
    ['date'=>'2025-11-02','value'=>48550],
    ['date'=>'2025-11-03','value'=>48620],
    ['date'=>'2025-11-04','value'=>48600],
    ['date'=>'2025-11-05','value'=>48640],
    ['date'=>'2025-11-06','value'=>48610],
  ],

  'avg_win'  => 277.66,
  'avg_loss' => 101.73,
  'avg_net'  => 0.62,

  'current_day_streak_value' => '1 Day',
  'current_day_streak_w'     => 1,
  'current_day_streak_l'     => 3,

  'trade_win_pct' => 55.28,
  'wins_count'    => 225,
  'loss_count'    => 182,

  'profit_factor' => 0.76,

  'current_trades_value' => '2 Trades',
  'current_w_total'      => 25,
  'current_l_total'      => 15,
];

/* helpers */
function mt_money_compact($n){
  $s = $n < 0 ? '-' : '';
  $a = abs($n);
  if ($a >= 1000) {
    $k = $a/1000;
    $num = (floor($k) == $k) ? number_format($k,0) : rtrim(rtrim(number_format($k,2,'.',''), '0'), '.');
    return $s.'$'.$num.'k';
  }
  $num = rtrim(rtrim(number_format($a,2,'.',''), '0'), '.');
  return $s.'$'.$num;
}
$net_pnl_str = mt_money_compact($mock['net_pnl']);
$wins_count  = (int)$mock['wins_count'];
$loss_count  = (int)$mock['loss_count'];
?>
<!-- ===================== GRID 3x2 ===================== -->
<div class="mt-grid mt-grid--3x2" style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;">

  <!-- ========== CARD 1: Net P&L + LWC Baseline (solo canvas, sin ejes/bordes) ========== -->
  <div class="mt-card mt-card-chart" style="padding:16px;background:var(--Surface-Page,#1E1E1E);border-radius:16px;outline:none;outline-offset:0;">
    <div class="d-flex justify-content-between align-items-center" style="display:flex;justify-content:space-between;align-items:center;">
      <div class="d-flex align-items-center gap-2" style="display:flex;align-items:center;gap:8px;">
        <div class="mt-card__title__text fw-medium text-uppercase" style="color:var(--Text-Body,#A8A29E);font-size:16px;line-height:24px;">
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
        <div style="color:var(--Surface-Body,#131210);font-size:14px;font-weight:700;line-height:16px;"><?php echo (int)$mock['trades_count']; ?></div>
      </div>
    </div>

    <div class="mt-card__kpi" style="margin-top:8px;color:var(--Text-Headings,#fff);font-size:20px;line-height:32px;">
      <?php echo esc_html($net_pnl_str); ?>
    </div>

    <div class="mt-card__chart" style="margin-top:16px;">
      <!-- SOLO CANVAS: sin bordes ni ejes; bloqueo de interacción -->
      <div id="adg-netpnl-chart" style="height:60px;width:100%;pointer-events:none;border:none;outline:none;"></div>
    </div>
  </div>

  <!-- ========== CARD 2: Avg. win/loss trade ========== -->
  <div class="mt-card" style="padding:16px;background:var(--Surface-Page,#1E1E1E);border-radius:16px;outline:1px solid var(--Colors-Gray-700,#404040);outline-offset:-1px;">
    <div class="d-flex justify-content-between align-items-center" style="display:flex;justify-content:space-between;align-items:center;">
      <div class="d-flex align-items-center gap-2" style="display:flex;gap:8px;align-items:center;">
        <div style="color:var(--Text-Body,#A8A29E);font-size:16px;">Avg. win/loss trade</div>
        <span class="mt-tooltip">
          <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0" aria-label="Avg win/loss info"></i>
          <span class="mt-tooltip__panel" role="tooltip">
            <div class="mt-tooltip__title"><?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_dpl_tooltip_title']); ?></div>
            <div class="mt-tooltip__body"><?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_dpl_tooltip_description']); ?></div>
          </span>
        </span>
      </div>
    </div>
    <div style="margin-top:8px;color:#fff;font-size:20px;line-height:32px;">
      <?php echo '$'.number_format($mock['avg_net'],2); ?>
    </div>
    <div class="d-flex gap-1" style="display:flex;gap:4px;margin-top:16px;align-items:flex-end;">
      <div style="flex:1;">
        <div style="color:var(--Text-Success,#2DD4BF);font-weight:700;"><?php echo '$'.number_format($mock['avg_win'],2); ?></div>
        <div style="height:8px;background:var(--Colors-Gray-700,#404040);border-top-left-radius:4px;border-bottom-left-radius:4px;overflow:hidden;">
          <div style="width:75%;height:8px;background:var(--Success-400,#2DD4BF);"></div>
        </div>
      </div>
      <div style="flex:1;">
        <div style="text-align:right;color:var(--Error-500,#F43F5E);font-weight:700;"><?php echo '$'.number_format($mock['avg_loss'],2); ?></div>
        <div style="height:8px;background:var(--Colors-Gray-700,#404040);border-top-right-radius:4px;border-bottom-right-radius:4px;overflow:hidden;">
          <div style="width:40%;height:8px;background:var(--Error-500,#F43F5E);float:right;"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- ========== CARD 3: Current Day Streak ========== -->
  <div class="mt-card" style="padding:16px;background:var(--Surface-Page,#1E1E1E);border-radius:16px;outline:1px solid var(--Colors-Gray-700,#404040);outline-offset:-1px;">
    <div class="d-flex justify-content-between align-items-center" style="display:flex;justify-content:space-between;align-items:center;">
      <div class="d-flex align-items-center gap-2" style="display:flex;gap:8px;align-items:center;">
        <div style="color:var(--Text-Body,#A8A29E);font-size:16px;">Current Day Streak</div>
        <span class="mt-tooltip">
          <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0" aria-label="Streak info"></i>
          <span class="mt-tooltip__panel" role="tooltip">
            <div class="mt-tooltip__title"><?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_dpl_tooltip_title']); ?></div>
            <div class="mt-tooltip__body"><?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_dpl_tooltip_description']); ?></div>
          </span>
        </span>
      </div>
    </div>
    <div style="margin-top:8px;display:flex;align-items:center;gap:8px;color:#fff;font-size:20px;">
      <?php echo esc_html($mock['current_day_streak_value']); ?>
      <span class="mt-icon mt-icon-base mt-icon_error-solid" aria-hidden="true"></span>
    </div>
    <div style="margin-top:16px;display:flex;gap:8px;">
      <div style="padding:4px 8px;background:var(--Success-800,#115E59);border-radius:8px;color:var(--Success-400,#2DD4BF);font-weight:700;"><?php echo (int)$mock['current_day_streak_w']; ?>W</div>
      <div style="padding:4px 8px;background:var(--Error-900,#881337);border-radius:8px;color:var(--Error-300,#FDA4AF);font-weight:700;"><?php echo (int)$mock['current_day_streak_l']; ?>L</div>
    </div>
  </div>

  <!-- ========== CARD 4: Trade Win % ========== -->
  <div class="mt-card" style="padding:16px;background:var(--Surface-Page,#1E1E1E);border-radius:16px;outline:1px solid var(--Colors-Gray-700,#404040);outline-offset:-1px;">
    <div class="d-flex justify-content-between align-items-center" style="display:flex;justify-content:space-between;align-items:center;">
      <div class="d-flex align-items-center gap-2" style="display:flex;gap:8px;align-items:center;">
        <div style="color:var(--Text-Body,#A8A29E);font-size:16px;">Trade Win %</div>
        <span class="mt-tooltip">
          <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0" aria-label="Trade Win info"></i>
          <span class="mt-tooltip__panel" role="tooltip">
            <div class="mt-tooltip__title"><?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_dpl_tooltip_title']); ?></div>
            <div class="mt-tooltip__body"><?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_dpl_tooltip_description']); ?></div>
          </span>
        </span>
      </div>
    </div>
    <div style="margin-top:8px;color:#fff;font-size:20px;"><?php echo number_format($mock['trade_win_pct'],2); ?>%</div>
    <div style="margin-top:16px;display:flex;gap:4px;align-items:flex-end;">
      <div style="flex:1;">
        <div style="color:var(--Text-Success,#2DD4BF);font-weight:700;"><?php echo $wins_count; ?></div>
        <div style="height:8px;background:#404040;border-top-left-radius:4px;border-bottom-left-radius:4px;overflow:hidden;">
          <div style="width:70%;height:8px;background:#2DD4BF;"></div>
        </div>
      </div>
      <div style="flex:1;">
        <div style="text-align:right;color:#F43F5E;font-weight:700;"><?php echo $loss_count; ?></div>
        <div style="height:8px;background:#404040;border-top-right-radius:4px;border-bottom-right-radius:4px;overflow:hidden;">
          <div style="width:45%;height:8px;background:#F43F5E;float:right;"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- ========== CARD 5: Profit Factor ========== -->
  <div class="mt-card" style="padding:16px;background:var(--Surface-Page,#1E1E1E);border-radius:16px;outline:1px solid var(--Colors-Gray-700,#404040);outline-offset:-1px;">
    <div class="d-flex justify-content-between align-items-center" style="display:flex;justify-content:space-between;align-items:center;">
      <div class="d-flex align-items-center gap-2" style="display:flex;gap:8px;align-items:center;">
        <div style="color:var(--Text-Body,#A8A29E);font-size:16px;">Profit Factor</div>
        <span class="mt-tooltip">
          <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0" aria-label="Profit Factor info"></i>
          <span class="mt-tooltip__panel" role="tooltip">
            <div class="mt-tooltip__title"><?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_dpl_tooltip_title']); ?></div>
            <div class="mt-tooltip__body"><?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_dpl_tooltip_description']); ?></div>
          </span>
        </span>
      </div>
    </div>
    <div style="margin-top:8px;color:#fff;font-size:20px;"><?php echo number_format($mock['profit_factor'],2); ?></div>
    <div style="margin-top:16px;width:60px;height:60px;border-radius:9999px;background:conic-gradient(var(--Success-400,#2DD4BF) 40%, var(--Error-500,#F43F5E) 0);"></div>
  </div>

  <!-- ========== CARD 6: Current Day Trades ========== -->
  <div class="mt-card" style="padding:16px;background:var(--Surface-Page,#1E1E1E);border-radius:16px;outline:1px solid var(--Colors-Gray-700,#404040);outline-offset:-1px;">
    <div class="d-flex justify-content-between align-items-center" style="display:flex;justify-content:space-between;align-items:center;">
      <div class="d-flex align-items-center gap-2" style="display:flex;gap:8px;align-items:center;">
        <div style="color:var(--Text-Body,#A8A29E);font-size:16px;">Current Day Trades</div>
        <span class="mt-tooltip">
          <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0" aria-label="Current trades info"></i>
          <span class="mt-tooltip__panel" role="tooltip">
            <div class="mt-tooltip__title"><?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_dpl_tooltip_title']); ?></div>
            <div class="mt-tooltip__body"><?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_dpl_tooltip_description']); ?></div>
          </span>
        </span>
      </div>
    </div>
    <div style="margin-top:8px;display:flex;align-items:center;gap:8px;color:#fff;font-size:20px;">
      <?php echo esc_html($mock['current_trades_value']); ?>
      <span class="mt-icon mt-icon-base mt-icon_error-solid" aria-hidden="true"></span>
    </div>
    <div style="margin-top:16px;display:flex;gap:8px;">
      <div style="padding:4px 8px;background:#115E59;border-radius:8px;color:#2DD4BF;font-weight:700;"><?php echo (int)$mock['current_w_total']; ?>W</div>
      <div style="padding:4px 8px;background:#881337;border-radius:8px;color:#FDA4AF;font-weight:700;"><?php echo (int)$mock['current_l_total']; ?>L</div>
    </div>
  </div>
</div>
<style>
  /* Scope solo al primer card con el mini-chart */
  .mt-card.mt-card-chart .mt-card__chart{
    margin-top: 0 !important;  /* quita el margin-top de 16px */
  }

  /* Tabla dentro del card: sin bordes ni márgenes */
  .mt-card.mt-card-chart table{
    border: 0 !important;
    margin: 0 !important;
    border-collapse: collapse;
  }
  .mt-card.mt-card-chart table td,
  .mt-card.mt-card-chart table th{
    border: 0 !important;
    margin: 0 !important;
    padding: 0; /* opcional: elimina relleno si lo deseas totalmente “plano” */
  }
</style>


<!-- ===================== SCRIPT: LWC Baseline (solo canvas) ===================== -->
<script>
(function(){
  const container = document.getElementById('adg-netpnl-chart');
  if (!container) return;
  if (container.dataset.mounted === '1') return;
  container.dataset.mounted = '1';

  function loadLWC(){
    if (window.LightweightCharts) return Promise.resolve();
    if (window.__LWC_LOADING__) return window.__LWC_LOADING__;
    const url = "https://unpkg.com/lightweight-charts@4.1.1/dist/lightweight-charts.standalone.production.js";
    window.__LWC_LOADING__ = new Promise((res,rej)=>{
      const s=document.createElement('script'); s.src=url; s.async=true;
      s.onload=()=>res(); s.onerror=()=>rej(new Error('LWC load failed'));
      document.head.appendChild(s);
    });
    return window.__LWC_LOADING__;
  }

  const RAW_SERIES = <?php echo wp_json_encode($mock['series'], JSON_UNESCAPED_SLASHES); ?>;
  const START_BAL  = <?php echo json_encode((float)$mock['starting_balance']); ?>;

  const data = (RAW_SERIES||[]).map(p=>{
    const v = Number(p?.value);
    const d = String(p?.date||'').slice(0,10);
    if (!d || !Number.isFinite(v)) return null;
    return { time:d, value:v };
  }).filter(Boolean);

  loadLWC().then(()=>{
    const chart = LightweightCharts.createChart(container, {
      layout:{ background:{ type:'Solid', color:'transparent' }, textColor:'#dcdcdc' },
      // SIN ejes, SIN bordes, SIN etiquetas
      leftPriceScale:  { visible:false },
      rightPriceScale: { visible:false, borderVisible:false },
      timeScale:       { visible:false,  borderVisible:false, lockVisibleTimeRangeOnResize:true, fixLeftEdge:true, fixRightEdge:true, timeVisible:false, secondsVisible:false },
      grid:            { vertLines:{ visible:false }, horzLines:{ visible:false } },
      crosshair:       { mode:0 },
      handleScroll:    { mouseWheel:false, pressedMouseMove:false, horzTouchDrag:false, vertTouchDrag:false },
      handleScale:     { axisPressedMouseMove:false, mouseWheel:false, pinch:false },
    });

    const baseline = chart.addBaselineSeries({
      baseValue:{ type:'price', price: START_BAL },
      priceFormat:{ type:'price', precision:2, minMove:0.01 },
      topLineColor:'#24b8a6',
      bottomLineColor:'#FF4D4D',
      topFillColor1:'rgba(36,184,166,0.28)',
      topFillColor2:'rgba(36,184,166,0.06)',
      bottomFillColor1:'rgba(255,77,77,0.20)',
      bottomFillColor2:'rgba(255,77,77,0.00)',
      lineWidth:1,
      lastValueVisible:false,
      priceLineVisible:false
    });

    baseline.setData(data);
    chart.timeScale().fitContent();

    // Garantizar “solo canvas”
    container.style.border = 'none';
    container.style.outline = 'none';
    container.style.pointerEvents = 'none';
  }).catch(e=>console.error('[ADG] LWC error', e));
})();
</script>
