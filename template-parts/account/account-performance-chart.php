<?php
if (!defined('ABSPATH'))
  exit;

if (!isset($args) || !is_array($args) || empty($args))
  return;

$chart = isset($args['chart']) && is_array($args['chart']) ? $args['chart'] : [];
$accountId = $chart['accountId'] ?? (isset($args['meta']['accountId']) ? (string) $args['meta']['accountId'] : '');
$chart_title = $chart['title'] ?? (($args['title'] ?? '') ?: 'Account');
$plan_revenue = $chart['plan_revenue'] ?? ($chart['series'] ?? []);
$max_drawdown = is_numeric($chart['max_drawdown'] ?? null) ? (float) $chart['max_drawdown'] : null;
$profit_target_eval = is_numeric($chart['profit_target'] ?? null) ? (float) $chart['profit_target'] : null;
$funded_target_amount = is_numeric($chart['funded_target_amount'] ?? null) ? (float) $chart['funded_target_amount'] : null;
$label = $chart['label'] ?? ($args['label'] ?? ($args['meta']['label'] ?? ''));

$periods = $chart['periods'] ?? [
  ['value' => 7, 'text' => 'LAST 7 DAYS'],
  ['value' => 14, 'text' => 'LAST 14 DAYS'],
  ['value' => 30, 'text' => 'LAST 30 DAYS'],
];

$isFunded = function_exists('mt_is_funded') ? mt_is_funded((string) $label) : false;

$effective_target = $isFunded ? $funded_target_amount : $profit_target_eval;


$js_series = [];
if (is_array($plan_revenue)) {
  foreach ($plan_revenue as $r) {
    $d = substr((string) ($r['date'] ?? $r['x'] ?? ''), 0, 10);
    $v = isset($r['value']) ? (float) $r['value'] : (isset($r['y']) ? (float) $r['y'] : null);
    if ($d && $v !== null)
      $js_series[] = ['date' => $d, 'value' => $v];
  }
}

$MIN_POINTS = 7;
$points_count = count($js_series);
$has_enough = ($points_count >= $MIN_POINTS);
$card_class = 'account-performance-chart mt-card' . ($has_enough ? '' : ' is-empty');
?>
<div class="<?= esc_attr($card_class) ?>" data-account-id="<?php echo esc_attr($accountId); ?>">
  <div class="account-performance-chart__overlay" <?= $has_enough ? 'hidden' : '' ?>>
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

      <select id="lastDaysSelect" class="form-select w-fit w-sm-100" name="last-days-select" <?= $has_enough ? '' : 'disabled' ?>>
        <?php foreach ($periods as $period): ?>
          <option value="<?= (int) $period['value']; ?>"><?= esc_html($period['text']); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="account-performance-chart__header">
    <div class="mt-account-performance-chart-content" data-min-points="<?= (int) $MIN_POINTS; ?>"
      data-points="<?= (int) $points_count; ?>">
      <div id="account-performance-chart" style="height:505px;"></div>
    </div>
  </div>
</div>


<script>
(function () {
  // ===== Datos desde PHP =====
  const RAW_SERIES  = <?php echo wp_json_encode($js_series, JSON_UNESCAPED_SLASHES); ?>; // [{date,value}]
  const PERIODS     = <?php echo wp_json_encode($periods, JSON_UNESCAPED_SLASHES); ?>;
  const PASS_LEVEL  = <?php echo ($effective_target !== null) ? json_encode((float) $effective_target) : 'null'; ?>;
  const MAX_LOSS    = <?php echo ($max_drawdown !== null) ? json_encode((float) $max_drawdown) : 'null'; ?>;
  const MIN_POINTS  = <?php echo (int) $MIN_POINTS; ?>;

  const container = document.getElementById('account-performance-chart');
  const rootWrap  = container?.closest('.mt-account-performance-chart-content');
  const overlayEl = document.querySelector('.account-performance-chart__overlay');
  const selectEl  = document.getElementById('lastDaysSelect');
  if (!container) return;

  // ===== Carga LWC =====
  function loadLWC() {
    if (window.LightweightCharts) return Promise.resolve();
    if (window.__LWC_LOADING__)   return window.__LWC_LOADING__;
    const urls = [
      "https://unpkg.com/lightweight-charts@4.1.1/dist/lightweight-charts.standalone.production.js",
      "https://cdn.jsdelivr.net/npm/lightweight-charts@4.1.1/dist/lightweight-charts.standalone.production.js"
    ];
    window.__LWC_LOADING__ = new Promise((res, rej) => {
      let i = 0; (function next(){
        const u = urls[i++]; if (!u) return rej(new Error("CDNs failed"));
        const s = document.createElement('script'); s.src = u; s.async = true;
        s.onload = () => res(); s.onerror = next; document.head.appendChild(s);
      })();
    });
    return window.__LWC_LOADING__;
  }

  // ===== Utils =====
  const COLORS = { balance:'#FFE7B8', profit:'#24b8a6', drawdown:'#FF4D4D' };
  const toSecOrIso = (t) => (typeof t === 'string') ? t : (isFinite(Date.parse(t)) ? t : null);
  function fmtMoney(n){
    const sign = n < 0 ? '-' : ''; const abs = Math.abs(n);
    if (abs >= 1000){ const k = abs/1000; const num = (Number.isInteger(k)?k.toFixed(0):k.toFixed(2)).replace(/\.?0+$/,''); return `${sign}$${num}k`; }
    const num = abs.toFixed(2).replace(/\.?0+$/,''); return `${sign}$${num}`;
  }
  function normSeries(arr){ return (arr||[]).map(p=>{const d=toSecOrIso(p.date); const v=Number(p.value)||0; return d?{time:d,value:v}:null;}).filter(Boolean); }
  function lastN(data,n){ if(!n||n<=0) return data; return data.slice(-n); }
  function setOverlayByCount(n){
    if (!rootWrap || !overlayEl) return;
    const min = parseInt(rootWrap.getAttribute('data-min-points'),10) || MIN_POINTS;
    rootWrap.setAttribute('data-points', String(n));
    const show = n < min;
    overlayEl.hidden = !show;
    window.mtOverlay?.toggle?.(rootWrap, show);
    if (selectEl) selectEl.disabled = show || (selectEl.options.length === 1);
  }
  const addDaysISO = (iso, d) => {
    if (!iso) return iso;
    const dt = new Date(iso); dt.setDate(dt.getDate()+d);
    return dt.toISOString().slice(0,10);
  };

  loadLWC().then(() => {
    const all = normSeries(RAW_SERIES);
    setOverlayByCount(all.length);

    const chart = LightweightCharts.createChart(container, {
      layout: { background:{ type:'Solid', color:'transparent' }, textColor:'#dcdcdc' },
      rightPriceScale: { borderVisible:false, scaleMargins:{ top:0.12, bottom:0.10 } },
      timeScale: { borderVisible:false, rightOffset:0, fixLeftEdge:true, barSpacing:10, timeVisible:false, secondsVisible:false },
      grid: { vertLines:{ visible:false }, horzLines:{ visible:false } },
      crosshair: { mode: 1 },
    });

    const FILL_ON  = {
      topFillColor1:'rgba(255,231,184,0.32)', topFillColor2:'rgba(255,231,184,0.08)',
      bottomFillColor1:'rgba(255,231,184,0.18)', bottomFillColor2:'rgba(255,231,184,0.00)',
    };
    const FILL_OFF = {
      topFillColor1:'rgba(0,0,0,0)', topFillColor2:'rgba(0,0,0,0)',
      bottomFillColor1:'rgba(0,0,0,0)', bottomFillColor2:'rgba(0,0,0,0)',
    };

    const baseline = chart.addBaselineSeries({
      baseValue:{ type:'price', price: Number.isFinite(PASS_LEVEL) ? PASS_LEVEL : 0 },
      priceFormat:{ type:'price', precision:2, minMove:0.01 },
      topLineColor:COLORS.balance, bottomLineColor:COLORS.balance, ...FILL_ON, lineWidth:1,
    });

    // series “fantasma” para fijar eje/labels
    const ghostOpts = { color:'rgba(0,0,0,0)', lineWidth:0, lastValueVisible:false, priceLineVisible:false, crosshairMarkerVisible:false };
    const ghostPass = Number.isFinite(PASS_LEVEL) ? chart.addLineSeries(ghostOpts) : null;
    const ghostLoss = Number.isFinite(MAX_LOSS)   ? chart.addLineSeries(ghostOpts) : null;

    let passPL=null, lossPL=null;
    function setPriceLines(){
      if (passPL) { baseline.removePriceLine(passPL); passPL=null; }
      if (lossPL) { baseline.removePriceLine(lossPL); lossPL=null; }
      if (Number.isFinite(PASS_LEVEL)){
        passPL = baseline.createPriceLine({ price:PASS_LEVEL, color:COLORS.profit, lineWidth:1, lineStyle:0, axisLabelVisible:true, title:'Profit Target' });
      }
      if (Number.isFinite(MAX_LOSS)){
        lossPL = baseline.createPriceLine({ price:MAX_LOSS, color:COLORS.drawdown, lineWidth:1, lineStyle:0, axisLabelVisible:true, title:'Max Drawdown' });
      }
    }

    let overlayActive = false;

    function applyData(days){
      const data = lastN(all, days);
      baseline.setData(data);
      setPriceLines();

      const min = parseInt(rootWrap?.getAttribute('data-min-points') || MIN_POINTS, 10);
      const few = data.length < min;
      overlayActive = few;

      // gradiente on/off
      baseline.applyOptions(few ? FILL_OFF : FILL_ON);

      // mantener labels del tiempo cuando hay pocos puntos
      const left  = data[0]?.time ?? all[0]?.time;
      const right = data[data.length-1]?.time ?? all[all.length-1]?.time;

      // Estira el rango con puntos invisibles +/-2 días para forzar ticks abajo
      if (ghostPass && left && right) {
        ghostPass.setData([
          { time:addDaysISO(left, -2),  value:PASS_LEVEL },
          { time:left,                  value:PASS_LEVEL },
          { time:right,                 value:PASS_LEVEL },
          { time:addDaysISO(right, 2),  value:PASS_LEVEL },
        ]);
      }
      if (ghostLoss && left && right) {
        ghostLoss.setData([
          { time:addDaysISO(left, -2),  value:MAX_LOSS },
          { time:right,                 value:MAX_LOSS },
          { time:addDaysISO(right, 2),  value:MAX_LOSS },
        ]);
      }

      // spacing y offsets para que se vean los números del eje inferior
      chart.timeScale().applyOptions(few
        ? { barSpacing:18, rightOffset:2, fixLeftEdge:false }
        : { barSpacing:10, rightOffset:0, fixLeftEdge:true }
      );

      chart.timeScale().fitContent();
      setOverlayByCount(data.length);
    }

    const initialDays = (selectEl && !selectEl.disabled) ? parseInt(selectEl.value, 10) : 0;
    applyData(initialDays || all.length);

    // === Tooltip (off si overlay activo) ===
    const tip = document.createElement('div');
    tip.className = 'mt-chart-tip';
    document.body.appendChild(tip);
    function hideTip(){ tip.style.opacity = '0'; }
    const chip = (c)=>`<span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:${c};margin-right:6px;vertical-align:-1px"></span>`;
    const toISO = (t)=> (typeof t==='object' && t?.year)
      ? `${t.year}-${String(t.month).padStart(2,'0')}-${String(t.day).padStart(2,'0')}`
      : (typeof t==='number' ? new Date(t*1000).toISOString().slice(0,10) : String(t));

    chart.subscribeCrosshairMove((param) => {
      if (overlayActive || !param?.time || !param.point) { hideTip(); return; }
      const sd = param.seriesData.get(baseline);
      if (!sd) { hideTip(); return; }

      tip.innerHTML =
        `<div style="opacity:.8;margin-bottom:4px;"><b>${toISO(param.time)}</b></div>` +
        `<div style="display:flex;justify-content:space-between;gap:12px;"><span>${chip('#FFE7B8')}Balance</span><b>${fmtMoney(sd.value)}</b></div>` +
        (Number.isFinite(PASS_LEVEL) ? `<div style="display:flex;justify-content:space-between;gap:12px;"><span>${chip('#24b8a6')}Profit Target</span><b>${fmtMoney(PASS_LEVEL)}</b></div>` : '') +
        (Number.isFinite(MAX_LOSS)   ? `<div style="display:flex;justify-content:space-between;gap:12px;"><span>${chip('#FF4D4D')}Max Drawdown</span><b>${fmtMoney(MAX_LOSS)}</b></div>` : '');

      const rect = container.getBoundingClientRect();
      const tw = tip.offsetWidth || 220, th = tip.offsetHeight || 60, m = 8;
      const cx = rect.left + (param.point?.x ?? 0);
      const cy = rect.top  + (param.point?.y ?? 0);
      let x = cx - tw/2, y = cy - th - 12;
      if (y < m) y = cy + 12;
      x = Math.max(m, Math.min(x, window.innerWidth  - tw - m));
      y = Math.max(m, Math.min(y, window.innerHeight - th - m));
      tip.style.left = `${x}px`; tip.style.top = `${y}px`; tip.style.opacity = '1';
    });
    container.addEventListener('mouseleave', hideTip, { passive:true });

    // Cambio de período
    selectEl?.addEventListener('change', (e) => {
      const days = parseInt(e.target.value, 10) || 0;
      applyData(days);
    });
  }).catch(err => console.error('[LWC] load error:', err));
})();
</script>

