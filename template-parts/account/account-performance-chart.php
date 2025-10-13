<?php
if (!defined('ABSPATH'))
  exit;

if (!isset($args) || !is_array($args) || empty($args)) {
  return;
}




$chart = isset($args['chart']) && is_array($args['chart']) ? $args['chart'] : [];
$accountId = $chart['accountId'] ?? (isset($args['meta']['accountId']) ? (string) $args['meta']['accountId'] : '');
$chart_title = $chart['title'] ?? (($args['title'] ?? '') ?: 'Account');
$plan_revenue = $chart['plan_revenue'] ?? ($chart['series'] ?? []); // [{date,value}] o [{x,y}]
$upper_bound = $chart['upper_bound'] ?? null; // Profit Target
$lower_bound = $chart['lower_bound'] ?? null; // Max Drawdown
$periods = $chart['periods'] ?? [
  ['value' => 7, 'text' => 'LAST 7 DAYS'],
  ['value' => 14, 'text' => 'LAST 14 DAYS'],
  ['value' => 30, 'text' => 'LAST 30 DAYS'],
];

// Normaliza la serie para el JS: {date: 'YYYY-MM-DD', value: float}
$js_series = [];
if (is_array($plan_revenue)) {
  foreach ($plan_revenue as $r) {
    $d = substr((string) ($r['date'] ?? $r['x'] ?? ''), 0, 10);
    $v = isset($r['value']) ? (float) $r['value'] : (isset($r['y']) ? (float) $r['y'] : null);
    if ($d && $v !== null) {
      $js_series[] = ['date' => $d, 'value' => $v];
    }
  }
}

// === Validación: mínimo 7 puntos ===
$MIN_POINTS = 7;
$points_count = count($js_series);
$has_enough_points = ($points_count >= $MIN_POINTS);
$card_class = 'account-performance-chart mt-card' . ($has_enough_points ? '' : ' is-empty');


// Ruta imagen overlay
$overlay_img = trailingslashit(get_stylesheet_directory_uri()) . 'assets/img/graph-empty.svg';
?>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<!-- HTML / PHP -->
<div class="account-performance-chart mt-card <?= esc_attr($card_class) ?>" data-account-id="<?php echo esc_attr($accountId); ?>">
  <div class="account-performance-chart__overlay" <?= $has_enough_points ? 'hidden' : '' ?>>
    <span class="apc-overlay__text"><?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['account_chart_overlay_no_data']); ?></span>
  </div>

  <div class="account-performance-chart__header">
    <div class="d-flex flex-column flex-md-row align-items-center gap-3">
      <div class="d-flex align-items-center gap-2 w-100">
        <div class="mt-card__title__text fw-medium text-uppercase">
          <?= esc_html($chart_title) ?>
        </div>
        <?php if (!empty($chart_title_tooltip)): ?>
          <span class="mt-tooltip">
            <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0" aria-label="Chart information"></i>
            <span class="mt-tooltip__panel" role="tooltip">
              <?php if (!empty($chart_title_tooltip['title'])): ?>
                <div class="mt-tooltip__title"><?= $chart_title_tooltip['title'] ?></div>
              <?php endif; ?>
              <?php if (!empty($chart_title_tooltip['description'])): ?>
                <div class="mt-tooltip__body"><?= $chart_title_tooltip['description'] ?></div>
              <?php endif; ?>
            </span>
          </span>
        <?php endif; ?>
      </div>

      <select id="lastDaysSelect" class="form-select w-fit w-sm-100" name="last-days-select" <?= $has_enough_points ? '' : 'disabled' ?>>
        <?php foreach ($periods as $period): ?>
          <option value="<?= (int) $period['value']; ?>"><?= esc_html($period['text']); ?></option>
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
  const RAW_SERIES = <?php echo wp_json_encode($js_series); ?>; // [{date,value}]
  const PERIODS = <?php echo wp_json_encode($periods); ?>;
  const UPPER_BOUND = <?php echo ($upper_bound !== null) ? json_encode((float) $upper_bound) : 'null'; ?>; // Profit Target
  const LOWER_BOUND = <?php echo ($lower_bound !== null) ? json_encode((float) $lower_bound) : 'null'; ?>; // Max Drawdown
  const chartTitle = <?php echo json_encode($chart_title); ?>;

  // ===== Lógica booleana solicitada =====
  const MIN_POINTS = <?php echo (int) $MIN_POINTS; ?>;
  const hasEnoughPoints = () => Array.isArray(RAW_SERIES) && RAW_SERIES.length >= MIN_POINTS;

    try {
      if (window.__mtChartInstance && typeof window.__mtChartInstance.destroy === 'function') {
        window.__mtChartInstance.destroy();
        window.__mtChartInstance = null;
      }
    } catch (e) { }
    (function hardClean(container) {
      document.querySelectorAll('.apexcharts-tooltip, .apexcharts-xcrosshairs, .apexcharts-ycrosshairs').forEach(n => n.remove());
      (container || document).querySelectorAll('.apexcharts-tooltip, .apexcharts-xcrosshairs, .apexcharts-ycrosshairs, .apexcharts-canvas').forEach(n => n.remove());
    })(document.getElementById("account-performance-chart"));

    // ===== Utilidades =====
    const constLine = (val, len) => (Number.isFinite(val) ? Array(len).fill(val) : Array(len).fill(null));
    const toMoney = (v) => `$ ${Number(v).toFixed(2)}`;

    function customTip({ series, dataPointIndex }) {
      const d = (window.__DATE_LABELS__?.[dataPointIndex]) || '';
      const val = (series?.[0]?.[dataPointIndex] ?? null);
      const ub = UPPER_BOUND;
      const lb = LOWER_BOUND;

      return `
        <div class="mt-apex-tip">
          <div class="mt-apex-tip__date"><b>Date:</b> ${d}</div>

          <div class="mt-apex-tip__row">
            <span>
              <i class="mt-apex-tip__marker" style="background:#FFE7B8"></i>
              Current Balance:
            </span>
            <b>${val != null ? toMoney(val) : '-'}</b>
          </div>

          ${Number.isFinite(ub) ? `
          <div class="mt-apex-tip__row">
            <span>
              <i class="mt-apex-tip__marker" style="background:#24b8a6"></i>
              Profit Target:
            </span>
            <b>${toMoney(ub)}</b>
          </div>` : ''}

          ${Number.isFinite(lb) ? `
          <div class="mt-apex-tip__row">
            <span>
              <i class="mt-apex-tip__marker" style="background:#FF4D4D"></i>
              Max Drawdown:
            </span>
            <b>${toMoney(lb)}</b>
          </div>` : ''}
        </div>`;
    }

    function getChartConfig(len) {
      return {
        height: '100%',
        chart: {
          toolbar: { show: false },
          parentHeightOffset: 0,
          animations: { enabled: true },
          events: {
            mounted: function () {
              setTimeout(() => {
                try { window.__mtChartInstance && window.__mtChartInstance.updateOptions({}, false, true); } catch (e) { }
              }, 0);
            }
          }
        },
        dataLabels: { enabled: false },
        colors: ["#FFE7B8", "#24b8a6", "#FF4D4D"], 
        stroke: { lineCap: "round", curve: "smooth", width: [2, 2, 2] },
        markers: { size: [0, 5, 5], colors: ["#FF4D4D", "#24b8a6"], strokeColors: 'transparent', strokeWidth: 0 },
        legend: { show: false },
        xaxis: {
          categories: [],
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
          followCursor: false,
          intersect: false,
          fixed: { enabled: false },
          custom: customTip
        }
      };
    }

    function sliceData(days) {
      const n = Math.max(1, parseInt(days));
      const slice = RAW_SERIES.slice(-n);
      const dateLabels = slice.map(p => p.date);
      const categories = Array.from({ length: slice.length }, (_, i) => i + 1);
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
      window.__DATE_LABELS__ = s.dateLabels;
      const cfg = getChartConfig(s.categories.length);
      cfg.xaxis.categories = s.categories;
      cfg.series = [
        { name: "Current Balance", data: s.main },
        { name: "Profit Target", data: s.upper },
        { name: "Max Drawdown", data: s.lower },
      ];
      return cfg;
    }

    function attachInlineTipFollower(root) {
      const base = root.querySelector('.apexcharts-inner') || root;
      const target = root.querySelector('.apexcharts-svg') || root.querySelector('.apexcharts-canvas') || root;
      const tipEl = () => root.querySelector('.apexcharts-tooltip');

      let raf = 0, want = { x: -9999, y: -9999, mx: 0 };

      function render() {
        raf = 0;
        const tip = tipEl();
        if (!tip) return;

        const tw = tip.offsetWidth || 240;
        const th = tip.offsetHeight || 60;
        const r = base.getBoundingClientRect();

        let x = Math.max(6, Math.min(want.x, r.width - tw - 6));
        let y = Math.max(6, Math.min(want.y, r.height - th - 6));

        tip.classList.remove('mt-tip-hidden');
        tip.style.transform = `translate3d(${Math.round(x)}px, ${Math.round(y)}px, 0)`;
        tip.style.opacity = 1;
        tip.style.visibility = 'visible';

        const arrowX = Math.max(12, Math.min(want.mx - x, tw - 12));
        tip.style.setProperty('--arrow-x', Math.round(arrowX) + 'px');
      }

      function queue(nx, ny, mx) {
        want.x = nx; want.y = ny; want.mx = mx;
        if (!raf) raf = requestAnimationFrame(render);
      }

      function pos(ev) {
        const r = base.getBoundingClientRect();
        const tip = tipEl();
        const th = tip ? (tip.offsetHeight || 60) : 60;
        const tw = tip ? (tip.offsetWidth || 240) : 240;

        const mx = ev.clientX - r.left;
        const my = ev.clientY - r.top;

        const x = mx - tw / 2;
        const y = my - th - 14;
        return { x, y, mx };
      }

      function onMove(ev) { const p = pos(ev); queue(p.x, p.y, p.mx); }
      function onEnter(ev) { const p = pos(ev); queue(p.x, p.y, p.mx); }
      function onLeave() {
        const tip = tipEl(); if (!tip) return;
        tip.classList.add('mt-tip-hidden');
        tip.style.transform = 'translate3d(-9999px,-9999px,0)';
        tip.style.opacity = 0; tip.style.visibility = 'hidden';
      }

      target.addEventListener('pointermove', onMove, { passive: true });
      target.addEventListener('mousemove', onMove, { passive: true });
      target.addEventListener('pointerenter', onEnter, { passive: true });
      target.addEventListener('mouseenter', onEnter, { passive: true });
      target.addEventListener('pointerleave', onLeave, { passive: true });
      target.addEventListener('mouseleave', onLeave, { passive: true });

      onLeave();

      requestAnimationFrame(() => {
        const r = base.getBoundingClientRect();
        onEnter({ clientX: r.left + 24, clientY: r.top + 24 });
      });
    }

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

    (function () {
      const root = document.getElementById('account-performance-chart')?.parentElement || document;
      attachInlineTipFollower(root);
    })();

    setTimeout(() => { try { window.__mtChartInstance?.updateOptions({}, false, true); } catch (e) { } }, 50);

    lastDaysSelect?.addEventListener('change', function (e) {
      window.__mtChartInstance.updateOptions(getChartOptions(e.target.value));
      const root = document.getElementById('account-performance-chart')?.parentElement || document;
      attachInlineTipFollower(root);
    });
  
</script>