<?php
if (!defined('ABSPATH')) exit;

if (!isset($args) || !is_array($args) || empty($args)) return;

$chart = isset($args['chart']) && is_array($args['chart']) ? $args['chart'] : [];
$accountId    = $chart['accountId'] ?? (isset($args['meta']['accountId']) ? (string)$args['meta']['accountId'] : '');
$chart_title  = $chart['title'] ?? (($args['title'] ?? '') ?: 'Account');
$plan_revenue = $chart['plan_revenue'] ?? ($chart['series'] ?? []); // [{date,value}] o [{x,y}]
$upper_bound  = $chart['upper_bound'] ?? null; // Profit Target
$lower_bound  = $chart['lower_bound'] ?? null; // Max Drawdown
$periods = $chart['periods'] ?? [
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
    if ($d && $v !== null) $js_series[] = ['date' => $d, 'value' => $v];
  }
}

$MIN_POINTS = 7;
$points_count = count($js_series);
$has_enough_points = ($points_count >= $MIN_POINTS);
$card_class = 'account-performance-chart mt-card' . ($has_enough_points ? '' : ' is-empty');

$overlay_img = trailingslashit(get_stylesheet_directory_uri()) . 'assets/img/graph-empty.svg';
?>

<!-- HTML -->
<div class="<?= esc_attr($card_class) ?>" data-account-id="<?php echo esc_attr($accountId); ?>">
  <div class="account-performance-chart__overlay" <?= $has_enough_points ? 'hidden' : '' ?>>
    <span class="apc-overlay__text">
      <?= esc_html(Label::META_ACCOUNT_OVERVIEW['account_chart_overlay_no_data']); ?>
    </span>
  </div>

  <div class="account-performance-chart__header">
    <div class="d-flex flex-column flex-md-row align-items-center gap-3">
      <div class="d-flex align-items-center gap-2 w-100">
        <div class="mt-card__title__text fw-medium text-uppercase">
          <?= esc_html($chart_title) ?>
        </div>
      </div>

      <select id="lastDaysSelect" class="form-select w-fit w-sm-100" name="last-days-select" <?= $has_enough_points ? '' : 'disabled' ?>>
        <?php foreach ($periods as $period): ?>
          <option value="<?= (int)$period['value']; ?>"><?= esc_html($period['text']); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="account-performance-chart__header">
    <div id="account-performance-chart" style="margin-left: -20px;"></div>
  </div>
</div>

<!-- Estilos del contenido del tooltip (no tocan el contenedor de Apex) -->
<style>
.apexcharts-tooltip .mt-apex-tip{
  background:#111; color:#fff; padding:8px 10px; border-radius:8px;
  font-size:12px; line-height:1.3; min-width:200px;
}
.mt-apex-tip__date{ opacity:.8; margin-bottom:4px }
.mt-apex-tip__row{ display:flex; justify-content:space-between; gap:12px; margin-top:4px }
.mt-apex-tip__marker{ display:inline-block; width:10px; height:10px; border-radius:2px; margin-right:6px; vertical-align:-2px }
</style>

<script>
  // ===== Datos base desde PHP =====
  const RAW_SERIES   = <?php echo wp_json_encode($js_series); ?>; // [{date,value}]
  const PERIODS      = <?php echo wp_json_encode($periods); ?>;
  const UPPER_BOUND  = <?php echo ($upper_bound !== null) ? json_encode((float)$upper_bound) : 'null'; ?>;
  const LOWER_BOUND  = <?php echo ($lower_bound !== null) ? json_encode((float)$lower_bound) : 'null'; ?>;
  const MIN_POINTS   = <?php echo (int)$MIN_POINTS; ?>;

  // ===== Lazy-load de ApexCharts =====
  let __apexPromise;
  function loadApex() {
    if (window.ApexCharts) return Promise.resolve(window.ApexCharts);
    if (__apexPromise)     return __apexPromise;
    __apexPromise = new Promise(function(resolve, reject){
      const s = document.createElement('script');
      s.src = 'https://cdn.jsdelivr.net/npm/apexcharts';
      s.async = true; s.defer = true;
      s.onload = () => resolve(window.ApexCharts);
      s.onerror = () => reject(new Error('Failed to load ApexCharts'));
      document.head.appendChild(s);
    });
    return __apexPromise;
  }

  // ===== Utilidades =====
  const constLine = (val, len) => (Number.isFinite(val) ? Array(len).fill(val) : Array(len).fill(null));
  const toMoney = (v) => `$ ${Number(v).toFixed(2)}`;
  function hasEnoughPoints(arr){ return Array.isArray(arr) && arr.length >= MIN_POINTS; }

  function setOverlay(shouldHide){
    const card = document.querySelector('.account-performance-chart.mt-card');
    const ov   = document.querySelector('.account-performance-chart__overlay');
    if (!card || !ov) return;
    if (shouldHide) { ov.hidden = true; card.classList.remove('is-empty'); }
    else            { ov.hidden = false; card.classList.add('is-empty');   }
  }

  function hardClean(container) {
    try { if (window.__mtChartInstance?.destroy) window.__mtChartInstance.destroy(); } catch(e){}
    window.__mtChartInstance = null;
    (container || document).querySelectorAll(
      '.apexcharts-tooltip,.apexcharts-xcrosshairs,.apexcharts-ycrosshairs,.apexcharts-canvas'
    ).forEach(n => n.remove());
  }

  function hideTip(){
  const t = document.querySelector('.apexcharts-tooltip');
  if (!t) return;
  t.style.opacity = 0;
  t.style.visibility = 'hidden';
  t.style.transform = 'translate3d(-9999px,-9999px,0)';
}

  function customTip({ series, dataPointIndex }) {
    const d  = (window.__DATE_LABELS__?.[dataPointIndex]) || '';
    const val= (series?.[0]?.[dataPointIndex] ?? null);
    const ub = window.__UB__; const lb = window.__LB__;
    return `
      <div class="mt-apex-tip">
        <div class="mt-apex-tip__date"><b>Date:</b> ${d}</div>
        <div class="mt-apex-tip__row"><span><i class="mt-apex-tip__marker" style="background:#FFE7B8"></i>Current Balance:</span><b>${val!=null?toMoney(val):'-'}</b></div>
        ${Number.isFinite(ub)?`<div class="mt-apex-tip__row"><span><i class="mt-apex-tip__marker" style="background:#24b8a6"></i>Profit Target:</span><b>${toMoney(ub)}</b></div>`:''}
        ${Number.isFinite(lb)?`<div class="mt-apex-tip__row"><span><i class="mt-apex-tip__marker" style="background:#FF4D4D"></i>Max Drawdown:</span><b>${toMoney(lb)}</b></div>`:''}
      </div>`;
  }

  function getChartConfig(){
    return {
      height: '100%',
      chart: {
        toolbar: { show: false },
        parentHeightOffset: 0,
        animations: { enabled: true },
        events: { mounted(){ setTimeout(()=>{ try{ window.__mtChartInstance?.updateOptions({}, false, true);}catch(e){} },0); } }
      },
      dataLabels: { enabled: false },
      colors: ["#FFE7B8", "#24b8a6", "#FF4D4D"],
      stroke: { lineCap: "round", curve: "smooth", width: [2,2,2] },
      markers:{ size:[0,5,5], colors:["#FF4D4D","#24b8a6"], strokeColors:'transparent', strokeWidth:0 },
      legend: { show: false },
      xaxis: { categories: [], axisTicks:{show:false}, axisBorder:{show:false},
        labels:{ style:{ colors:"#A8A29E", fontSize:"12px", fontFamily:"inherit", fontWeight:400 } } },
      yaxis: { labels:{ formatter:(v)=>`$ ${Number(v).toFixed(2)}`, style:{ colors:"#A8A29E", fontSize:"12px", fontFamily:"inherit", fontWeight:400 } } },
      grid:  { show:true, borderColor:"#374151", strokeDashArray:5 },
      fill:  { opacity:0.8 },
      // IMPORTANTE: follower externo requiere followCursor:false
      tooltip:{ enabled:true, shared:false, followCursor:false, intersect:false, fixed:{enabled:false}, custom: customTip }
    };
  }

  function sliceData(series, days) {
    const n = Math.max(1, parseInt(days || series.length || 7));
    const slice = series.slice(-n);
    const dateLabels = slice.map(p => p.date);
    const categories = Array.from({ length: slice.length }, (_, i) => i + 1);
    const main = slice.map(p => Number(p.value));
    return { categories, dateLabels, main };
  }

  function getOptions(series, days){
    const s = sliceData(series, days);
    window.__DATE_LABELS__ = s.dateLabels;
    const cfg = getChartConfig();
    cfg.xaxis.categories = s.categories;
    cfg.series = [
      { name: "Current Balance", data: s.main },
      { name: "Profit Target",   data: constLine(window.__UB__, s.categories.length) },
      { name: "Max Drawdown",    data: constLine(window.__LB__, s.categories.length) },
    ];
    return cfg;
  }

  function attachInlineTipFollower(root) {
    const base   = root.querySelector('.apexcharts-inner') || root;
    const target = root.querySelector('.apexcharts-svg') || root.querySelector('.apexcharts-canvas') || root;
    const tipEl  = () => root.querySelector('.apexcharts-tooltip');
    if (!target) return;
    let raf = 0, want = { x:-9999, y:-9999, mx:0 };
    function render(){
      raf = 0;
      const tip = tipEl(); if (!tip) return;
      const tw = tip.offsetWidth || 240, th = tip.offsetHeight || 60;
      const r  = base.getBoundingClientRect();
      let x = Math.max(6, Math.min(want.x, r.width - tw - 6));
      let y = Math.max(6, Math.min(want.y, r.height - th - 6));
      tip.classList.remove('mt-tip-hidden');
      tip.style.transform = `translate3d(${Math.round(x)}px, ${Math.round(y)}px, 0)`;
      tip.style.opacity = 1; tip.style.visibility = 'visible';
      const arrowX = Math.max(12, Math.min(want.mx - x, tw - 12));
      tip.style.setProperty('--arrow-x', Math.round(arrowX) + 'px');
    }
    function queue(nx,ny,mx){ want.x=nx; want.y=ny; want.mx=mx; if(!raf) raf=requestAnimationFrame(render); }
    function pos(ev){
      const r = base.getBoundingClientRect();
      const tip = tipEl(); const th = tip ? (tip.offsetHeight||60) : 60; const tw = tip ? (tip.offsetWidth||240) : 240;
      const mx = ev.clientX - r.left, my = ev.clientY - r.top;
      return { x: mx - tw/2, y: my - th - 14, mx };
    }
    function onMove(ev){ const p = pos(ev); queue(p.x,p.y,p.mx); }
    function onEnter(ev){ const p = pos(ev); queue(p.x,p.y,p.mx); }
    function onLeave(){ const tip = tipEl(); if (!tip) return; tip.classList.add('mt-tip-hidden'); tip.style.transform='translate3d(-9999px,-9999px,0)'; tip.style.opacity=0; tip.style.visibility='hidden'; }
    target.addEventListener('pointermove', onMove, {passive:true});
    target.addEventListener('mousemove',  onMove, {passive:true});
    target.addEventListener('pointerenter', onEnter,{passive:true});
    target.addEventListener('mouseenter',   onEnter,{passive:true});
    target.addEventListener('pointerleave', onLeave);
    target.addEventListener('mouseleave',   onLeave);
    onLeave();
    requestAnimationFrame(()=>{ const r = base.getBoundingClientRect(); onEnter({ clientX:r.left+24, clientY:r.top+24 }); });
  }

  // ===== Montaje / actualización central =====
  (function ChartController(){
    const container = document.getElementById("account-performance-chart");
    const lastDays  = document.getElementById("lastDaysSelect");

    window.__UB__ = UPPER_BOUND;
    window.__LB__ = LOWER_BOUND;

    function mount(series){
      if (!series?.length){ setOverlay(false); hardClean(container); return; }
      setOverlay(hasEnoughPoints(series));
      const initialDays = lastDays ? lastDays.value : (PERIODS?.[0]?.value || series.length || 7);
      hardClean(container);
      loadApex().then(function(ApexCharts){
        window.__mtChartInstance = new ApexCharts(container, getOptions(series, initialDays));
        window.__mtChartInstance.render();
        setTimeout(()=>{ try{ window.__mtChartInstance?.updateOptions({}, false, true);}catch(e){} }, 50);
        // Si usas follower externo, lo enganchas aquí:
        attachInlineTipFollower(container?.parentElement || document);
      }).catch(console.error);
    }

    // Lazy la primera vez
    if ('IntersectionObserver' in window) {
      const io = new IntersectionObserver((entries)=>{
        entries.forEach((e)=>{
          if (e.isIntersecting){
            mount(RAW_SERIES);
            io.unobserve(e.target);
          }
        });
      }, {rootMargin:'200px 0px'});
      if (container) io.observe(container);

      // Si cambia de cuenta antes de intersectar, montamos ya
      window.addEventListener('mt:account-switched', function(ev){
        window.__UB__ = Number.isFinite(ev.detail?.upper) ? ev.detail.upper : UPPER_BOUND;
        window.__LB__ = Number.isFinite(ev.detail?.lower) ? ev.detail.lower : LOWER_BOUND;
        mount(ev.detail?.series || []);
      });

    } else {
      // Fallback
      const ric = window.requestIdleCallback || function(cb){ return setTimeout(cb,1); };
      ric(()=> mount(RAW_SERIES));
      window.addEventListener('mt:account-switched', function(ev){
        window.__UB__ = Number.isFinite(ev.detail?.upper) ? ev.detail.upper : UPPER_BOUND;
        window.__LB__ = Number.isFinite(ev.detail?.lower) ? ev.detail.lower : LOWER_BOUND;
        mount(ev.detail?.series || []);
      });
    }

    // Cambio de período
    lastDays?.addEventListener('change', function(e){
      if (!window.__mtChartInstance) return;
      const current = window.__mtChartInstance.w.config.series?.[0]?.data?.map((v,i)=>({ date: window.__DATE_LABELS__?.[i]||'', value: v })) || RAW_SERIES;
      window.__mtChartInstance.updateOptions( getOptions(current, e.target.value) );
      attachInlineTipFollower(container?.parentElement || document);
    });

    // API pública
    window.MT_OV_CHART = {
      update: function(series, bounds){
        if (bounds){
          window.__UB__ = Number.isFinite(bounds.upper)?bounds.upper:window.__UB__;
          window.__LB__ = Number.isFinite(bounds.lower)?bounds.lower:window.__LB__;
        }
        mount(series || []);
      }
    };
  })();
</script>
