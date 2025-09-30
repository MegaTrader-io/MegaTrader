<?php
if (!defined('ABSPATH')) exit;

if (!isset($args) || !is_array($args) || empty($args)) {
  return;
}

$chart        = isset($args['chart']) && is_array($args['chart']) ? $args['chart'] : [];
$chart_title  = $chart['title'] ?? (($args['title'] ?? '') ?: 'Account');
$plan_revenue = $chart['plan_revenue'] ?? ($chart['series'] ?? []); // [{date,value}] o [{x,y}]
$upper_bound  = $chart['upper_bound'] ?? null; // Profit Target
$lower_bound  = $chart['lower_bound'] ?? null; // Max Drawdown
$periods      = $chart['periods'] ?? [
  ['value' => 7,  'text' => 'LAST 7 DAYS'],
  ['value' => 14, 'text' => 'LAST 14 DAYS'],
  ['value' => 30, 'text' => 'LAST 30 DAYS'],
];

// Normaliza la serie para el JS: {date: 'YYYY-MM-DD', value: float}
$js_series = [];
if (is_array($plan_revenue)) {
  foreach ($plan_revenue as $r) {
    $d = substr((string)($r['date'] ?? $r['x'] ?? ''), 0, 10);
    $v = isset($r['value']) ? (float)$r['value'] : (isset($r['y']) ? (float)$r['y'] : null);
    if ($d && $v !== null) {
      $js_series[] = ['date' => $d, 'value' => $v];
    }
  }
}
?>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<div class="account-performance-chart mt-card">
  <div class="account-performance-chart__header">
    <div class="d-flex flex-column flex-md-row align-items-center gap-3">
      <div class="d-flex align-items-center gap-2 w-100">
        <div class="mt-card__title__text fw-medium text-uppercase">
          <?= esc_html($chart_title) ?>
        </div>
        <?php if (isset($chart_title_tooltip) && !empty($chart_title_tooltip)): ?>
          <span class="mt-tooltip">
            <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0" aria-label="Chart information"></i>
            <span class="mt-tooltip__panel" role="tooltip">
              <?php if (isset($chart_title_tooltip['title'])): ?>
                <div class="mt-tooltip__title"><?= $chart_title_tooltip['title'] ?></div>
              <?php endif; ?>
              <?php if (isset($chart_title_tooltip['description'])): ?>
                <div class="mt-tooltip__body"><?= $chart_title_tooltip['description'] ?></div>
              <?php endif; ?>
            </span>
          </span>
        <?php endif; ?>
      </div>

      <select id="lastDaysSelect" class="form-select w-fit w-sm-100" name="last-days-select">
        <?php foreach ($periods as $period): ?>
          <option value="<?= (int)$period['value']; ?>">
            <?= esc_html($period['text']); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="account-performance-chart__header">
    <div id="account-performance-chart" style="margin-left: -20px;"></div>
  </div>
</div>

<script>
  // ===== Datos base desde PHP =====
  const RAW_SERIES  = <?php echo wp_json_encode($js_series); ?>; // [{date,value}]
  const PERIODS     = <?php echo wp_json_encode($periods); ?>;
  const UPPER_BOUND = <?php echo ($upper_bound !== null) ? json_encode((float)$upper_bound) : 'null'; ?>; // Profit Target
  const LOWER_BOUND = <?php echo ($lower_bound !== null) ? json_encode((float)$lower_bound) : 'null'; ?>; // Max Drawdown
  const chartTitle  = <?php echo json_encode($chart_title); ?>;

  // ===== Logs (puedes quitar luego) =====
  console.log('[MT][Chart] title:', chartTitle);
  console.log('[MT][Chart] periods:', PERIODS);
  console.log('[MT][Chart] raw points:', RAW_SERIES.length, RAW_SERIES);

  // ==== Limpieza extra (evita tooltips/crosshairs pegados tras AJAX) ====
  try {
    if (window.__mtChartInstance && typeof window.__mtChartInstance.destroy === 'function') {
      window.__mtChartInstance.destroy();
      window.__mtChartInstance = null;
    }
  } catch(e) {}
  (function hardClean(container){
    // elimina remanentes globales y dentro del contenedor
    document.querySelectorAll('.apexcharts-tooltip, .apexcharts-xcrosshairs, .apexcharts-ycrosshairs').forEach(n => n.remove());
    (container||document).querySelectorAll('.apexcharts-tooltip, .apexcharts-xcrosshairs, .apexcharts-ycrosshairs, .apexcharts-canvas').forEach(n => n.remove());
  })(document.getElementById("account-performance-chart"));

  // ===== Utilidades =====
  const constLine = (val, len) => (Number.isFinite(val) ? Array(len).fill(val) : Array(len).fill(null));
  const toMoney   = (v) => `$ ${Number(v).toFixed(2)}`;

  // Tooltip HTML custom (visual como el mock)
  function customTip({ series, seriesIndex, dataPointIndex }) {
    const d   = (window.__DATE_LABELS__?.[dataPointIndex]) || '';
    const val = (series?.[0]?.[dataPointIndex] ?? null);
    const ub  = UPPER_BOUND;
    const lb  = LOWER_BOUND;

    return `
      <div class="mt-apex-tip">
        <div class="mt-apex-tip__date"><b>Date:</b> ${d}</div>
        <div class="mt-apex-tip__row"><span>Current Balance</span><b>${val != null ? toMoney(val) : '-'}</b></div>
        ${Number.isFinite(ub) ? `<div class="mt-apex-tip__row"><span>Profit Target</span><b>${toMoney(ub)}</b></div>` : ''}
        ${Number.isFinite(lb) ? `<div class="mt-apex-tip__row"><span>Max Drawdown</span><b>${toMoney(lb)}</b></div>` : ''}
      </div>`;
  }

  function getChartConfig(len) {
    return {
      height: '100%',
      chart: {
        toolbar: { show: false },
        parentHeightOffset: 0, // ayuda al posicionado del tooltip
        animations: { enabled: true },
        events: {
          mounted: function() {
            // forzar un relayout por si el contenedor cambió con AJAX
            setTimeout(() => {
              try { window.__mtChartInstance && window.__mtChartInstance.updateOptions({}, false, true); } catch(e) {}
            }, 0);
          }
        }
      },
      dataLabels: { enabled: false },
      colors: ["#FFE7B8", "#24b8a6", "#FF4D4D"], // Current, Profit Target, Max Drawdown
      stroke: { lineCap: "round", curve: "smooth", width: [2,2,2] },
      markers: { size: [0,5,5], colors: ["#FF4D4D","#24b8a6"], strokeColors: 'transparent', strokeWidth: 0 },
      legend: { show: false },
      xaxis: {
        categories: [], // 1..N
        axisTicks: { show: false }, axisBorder: { show: false },
        labels: { style: { colors: "#A8A29E", fontSize: "12px", fontFamily: "inherit", fontWeight: 400 } }
      },
      yaxis: {
        labels: { formatter: (v) => `$ ${Number(v).toFixed(2)}`, style: { colors: "#A8A29E", fontSize: "12px", fontFamily: "inherit", fontWeight: 400 } }
      },
      grid: { show: true, borderColor: "#374151", strokeDashArray: 5 },
      fill: { opacity: 0.8 },
      tooltip: {
        enabled: true,
        shared: false,
        followCursor: true,
        intersect: false,
        fixed: { enabled: false },
        custom: customTip
      }
    };
  }

  // Recorte de datos a últimos "days" puntos
  function sliceData(days) {
    const n = Math.max(1, parseInt(days));
    const slice = RAW_SERIES.slice(-n);
    const dateLabels = slice.map(p => p.date);                    // fecha real (tooltip)
    const categories = Array.from({length: slice.length}, (_, i) => i + 1); // eje X: 1..N
    const main = slice.map(p => Number(p.value));
    return {
      categories,
      dateLabels,
      main,
      upper: constLine(UPPER_BOUND, slice.length),
      lower: constLine(LOWER_BOUND, slice.length),
    };
  }

  function getChartOptions(days) {
    const s = sliceData(days);
    window.__DATE_LABELS__ = s.dateLabels; // para tooltip.x
    const cfg = getChartConfig(s.categories.length);
    cfg.xaxis.categories = s.categories;
    cfg.series = [
      { name: "Current Balance", data: s.main },
      { name: "Profit Target",   data: s.upper },
      { name: "Max Drawdown",    data: s.lower },
    ];
    return cfg;
  }

  // ===== Init =====
  const lastDaysSelect = document.getElementById("lastDaysSelect");
  if (lastDaysSelect && lastDaysSelect.options.length === 1) lastDaysSelect.disabled = true;

  const initialDays = lastDaysSelect
    ? lastDaysSelect.value
    : (PERIODS[0]?.value || RAW_SERIES.length || 7);

  window.__mtChartInstance = new ApexCharts(
    document.getElementById("account-performance-chart"),
    getChartOptions(initialDays)
  );
  window.__mtChartInstance.render();

  // fuerza reposicionamiento del tooltip si el contenedor cambia tras render
  setTimeout(() => { try { window.__mtChartInstance?.updateOptions({}, false, true); } catch(e) {} }, 50);

  lastDaysSelect?.addEventListener('change', function (e) {
    window.__mtChartInstance.updateOptions(getChartOptions(e.target.value));
  });
</script>

<style>
  /* ===== Tooltip custom ===== */
  .mt-apex-tip{
    background:#000; /* negro */
    color:#fff;
    border:1px solid var(--Colors-Gray-700, #404040); /* borde pedido */
    border-radius:8px;
    padding:10px 12px;
    min-width:220px;
    box-shadow:0 6px 16px rgba(0,0,0,0.35);
  }
  .mt-apex-tip__date{
    color:#fff; /* blanco */
    font-size:14px;
    line-height:20px;
    margin-bottom:8px;
  }
  .mt-apex-tip__row{
    display:flex; justify-content:space-between; gap:12px;
    font-size:14px; line-height:20px;
    color: var(--Text-Body, #A8A29E); /* texto cuerpo */
  }
  .mt-apex-tip__row b{ font-weight:600; color:#fff; }

  /* (opcional) si tu tema aplica transform en contenedores, ayuda al posicionamiento */
  .account-performance-chart { position: relative; }
</style>
