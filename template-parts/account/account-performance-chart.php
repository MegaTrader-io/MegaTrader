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
$profit_target_funded = is_numeric($chart['funded_target_amount'] ?? null) ? (float) $chart['funded_target_amount'] : null;
$label = $chart['label'] ?? ($args['label'] ?? ($args['meta']['label'] ?? ''));
$consistency_reset_balance_mark = is_numeric($chart['consistency_reset_balance_mark'] ?? null) ? (float) $chart['consistency_reset_balance_mark'] : null;

$periods = $chart['periods'] ?? [
  ['value' => 7, 'text' => 'LAST 7 DAYS'],
  ['value' => 14, 'text' => 'LAST 14 DAYS'],
  ['value' => 30, 'text' => 'LAST 30 DAYS'],
];

$isFunded = function_exists('mt_is_funded') ? mt_is_funded((string) $label) : false;

$effective_target = (float) ($isFunded
  ? $profit_target_funded + ($consistency_reset_balance_mark ?? 0)
  : $profit_target_eval);


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
  const RAW_SERIES = <?php echo wp_json_encode($js_series, JSON_UNESCAPED_SLASHES); ?>;
  const PERIODS    = <?php echo wp_json_encode($periods, JSON_UNESCAPED_SLASHES); ?>;
  const PASS_LEVEL = <?php echo ($effective_target !== null) ? json_encode((float) $effective_target) : 'null'; ?>;
  const MAX_LOSS   = <?php echo ($max_drawdown !== null) ? json_encode((float) $max_drawdown) : 'null'; ?>;
  const MIN_POINTS = <?php echo (int) $MIN_POINTS; ?>;

  const container = document.getElementById('account-performance-chart');
  const rootWrap  = container?.closest('.mt-account-performance-chart-content');
  const overlayEl = document.querySelector('.account-performance-chart__overlay');
  const selectEl  = document.getElementById('lastDaysSelect');
  if (!container) return;

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

  // ===== Utils / colores =====
  const COLORS = {
    balance:'#24b8a6',
    pt:'#ffb34a',          // Profit Target (nuevo color)
    green:'#24b8a6',
    red:'#FF4D4D'
  };
  const BASELINE_PALETTE = {
    greenLine:'#24b8a6',
    redLine:'#FF4D4D',
    greenTop1:'rgba(36,184,166,0.28)',
    greenTop2:'rgba(36,184,166,0.06)',
    redBot1:'rgba(255,77,77,0.20)',
    redBot2:'rgba(255,77,77,0.00)',
  };
  const FILL_CLEAR = {
    topFillColor1:'rgba(0,0,0,0)', topFillColor2:'rgba(0,0,0,0)',
    bottomFillColor1:'rgba(0,0,0,0)', bottomFillColor2:'rgba(0,0,0,0)'
  };
  const toSecOrIso = (t)=> (typeof t==='string') ? t : (isFinite(Date.parse(t)) ? t : null);
  function normSeries(arr){return (arr||[]).map(p=>{const d=toSecOrIso(p.date);const v=Number(p.value)||0;return d?{time:d,value:v}:null;}).filter(Boolean);}
  function lastN(d,n){return (!n||n<=0)?d:d.slice(-n);}
  function fmtMoney(n){const s=n<0?'-':'';const a=Math.abs(n);if(a>=1000){const k=a/1000;const num=(Number.isInteger(k)?k.toFixed(0):k.toFixed(2)).replace(/\.?0+$/,'');return `${s}$${num}k`;}const num=a.toFixed(2).replace(/\.?0+$/,'');return `${s}$${num}`;}
  function addDaysISO(iso,d){ if(!iso) return iso; const dt=new Date(iso); dt.setDate(dt.getDate()+d); return dt.toISOString().slice(0,10); }
  function setOverlayByCount(n){
    if(!rootWrap||!overlayEl) return;
    const min=parseInt(rootWrap.getAttribute('data-min-points'),10)||MIN_POINTS;
    rootWrap.setAttribute('data-points',String(n));
    const show=n<min;
    overlayEl.hidden=!show;
    window.mtOverlay?.toggle?.(rootWrap,show);
    if (selectEl) selectEl.disabled = show || (selectEl.options.length===1);
  }

  loadLWC().then(()=>{
    const all = normSeries(RAW_SERIES);
    setOverlayByCount(all.length);

    const chart = LightweightCharts.createChart(container, {
      layout:{ background:{ type:'Solid', color:'transparent' }, textColor:'#dcdcdc' },
      rightPriceScale:{ borderVisible:false, scaleMargins:{ top:0.12, bottom:0.10 } },
      timeScale:{ borderVisible:false, rightOffset:0, fixLeftEdge:true, barSpacing:10, timeVisible:false, secondsVisible:false },
      grid:{ vertLines:{ visible:false }, horzLines:{ visible:false } },
      crosshair:{ mode:1 },
    });

    // ----- responsive width -----
    const host = container.closest('.account-performance-chart.mt-card') || container.parentElement;
    container.style.display='block'; container.style.width='100%';
    function padX(el){ const cs=getComputedStyle(el); return (parseFloat(cs.paddingLeft)||0)+(parseFloat(cs.paddingRight)||0); }
    function readSize(){ const wHost=Math.round(host.clientWidth - padX(host)); const h=Math.max(220, container.clientHeight||505); return { w:Math.max(320,wHost), h }; }
    function resizeNow(){ const {w,h}=readSize(); chart.resize(w,h); chart.timeScale().fitContent(); }
    if(window.ResizeObserver){ new ResizeObserver(()=>requestAnimationFrame(resizeNow)).observe(host); }
    document.addEventListener('MT_SIDEBAR_TOGGLE', ()=> setTimeout(resizeNow,160));
    const sidebar=document.querySelector('.mt-page__sidebar,#mt-sidebar');
    sidebar?.addEventListener('transitionend', (e)=>{ if(['width','flex-basis','transform'].includes(e.propertyName)) resizeNow(); }, {passive:true});
    window.addEventListener('resize', ()=>requestAnimationFrame(resizeNow), {passive:true});
    resizeNow();

    // ----- Baseline (verde encima de MAX_LOSS, rojo debajo) -----
    const baseline = chart.addBaselineSeries({
      baseValue:{ type:'price', price: Number.isFinite(MAX_LOSS) ? MAX_LOSS : 0 },
      priceFormat:{ type:'price', precision:2, minMove:0.01 },
      topLineColor:    BASELINE_PALETTE.greenLine,
      bottomLineColor: BASELINE_PALETTE.redLine,
      topFillColor1:    BASELINE_PALETTE.greenTop1,
      topFillColor2:    BASELINE_PALETTE.greenTop2,
      bottomFillColor1: BASELINE_PALETTE.redBot1,
      bottomFillColor2: BASELINE_PALETTE.redBot2,
      lineWidth:1,
      title: 'Current Balance'
    });

    // series fantasma para fijar eje/labels
    const ghostOpts={ color:'rgba(0,0,0,0)', lineWidth:0, lastValueVisible:false, priceLineVisible:false, crosshairMarkerVisible:false };
    const ghostPass = Number.isFinite(PASS_LEVEL) ? chart.addLineSeries(ghostOpts) : null;
    const ghostLoss = Number.isFinite(MAX_LOSS) ? chart.addLineSeries(ghostOpts) : null;

    // price lines
    let passPL=null, lossPL=null;
    function setPriceLines(){
      if (passPL){ baseline.removePriceLine(passPL); passPL=null; }
      if (lossPL){ baseline.removePriceLine(lossPL); lossPL=null; }
      if (Number.isFinite(PASS_LEVEL)){
        passPL = baseline.createPriceLine({
          price: PASS_LEVEL,
          color: COLORS.pt,           
          lineWidth: 1, lineStyle: 0, axisLabelVisible: true,
          title: 'Profit Target'
        });
      }
      if (Number.isFinite(MAX_LOSS)){
        lossPL = baseline.createPriceLine({
          price: MAX_LOSS,
          color: COLORS.red,
          lineWidth: 1, lineStyle: 0, axisLabelVisible: true,
          title: 'Max Drawdown'
        });
      }
    }

    let overlayActive=false;

    function applyData(days){
      const data = lastN(all, days);
      baseline.setData(data);
      setPriceLines();

      const min=parseInt(rootWrap?.getAttribute('data-min-points')||MIN_POINTS,10);
      const few = data.length < min;
      overlayActive = few;

      baseline.applyOptions(few ? FILL_CLEAR : {
        topFillColor1:    BASELINE_PALETTE.greenTop1,
        topFillColor2:    BASELINE_PALETTE.greenTop2,
        bottomFillColor1: BASELINE_PALETTE.redBot1,
        bottomFillColor2: BASELINE_PALETTE.redBot2,
      });

      const left  = data[0]?.time ?? all[0]?.time;
      const right = data[data.length-1]?.time ?? all[all.length-1]?.time;
      if (ghostPass && left && right){
        ghostPass.setData([
          { time:addDaysISO(left,-2),  value:PASS_LEVEL },
          { time:left,                 value:PASS_LEVEL },
          { time:right,                value:PASS_LEVEL },
          { time:addDaysISO(right, 2), value:PASS_LEVEL },
        ]);
      }
      if (ghostLoss && left && right){
        ghostLoss.setData([
          { time:addDaysISO(left,-2),  value:MAX_LOSS },
          { time:right,                value:MAX_LOSS },
          { time:addDaysISO(right, 2), value:MAX_LOSS },
        ]);
      }

      chart.timeScale().applyOptions(few
        ? { barSpacing:18, rightOffset:2, fixLeftEdge:false }
        : { barSpacing:10, rightOffset:0, fixLeftEdge:true }
      );

      chart.timeScale().fitContent();
      setOverlayByCount(data.length);
    }

    const initialDays = (selectEl && !selectEl.disabled) ? parseInt(selectEl.value,10) : 0;
    applyData(initialDays || all.length);

    // ----- Tooltip -----
    const tip=document.createElement('div');
    tip.className='mt-chart-tip';
    document.body.appendChild(tip);
    function hideTip(){ tip.style.opacity='0'; }
    const chip=(c)=>`<span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:${c};margin-right:6px;vertical-align:-1px"></span>`;
    const toISO=(t)=> (typeof t==='object' && t?.year)
      ? `${t.year}-${String(t.month).padStart(2,'0')}-${String(t.day).padStart(2,'0')}`
      : (typeof t==='number' ? new Date(t*1000).toISOString().slice(0,10) : String(t));

    chart.subscribeCrosshairMove((param)=>{
      if (overlayActive || !param?.time || !param.point){ hideTip(); return; }
      const sd = param.seriesData.get(baseline);
      if (!sd){ hideTip(); return; }
      tip.innerHTML =
        `<div style="opacity:.8;margin-bottom:4px;"><b>${toISO(param.time)}</b></div>`+
        `<div style="display:flex;justify-content:space-between;gap:12px;"><span>${chip(COLORS.balance)}Balance</span><b>${fmtMoney(sd.value)}</b></div>`+
        (Number.isFinite(PASS_LEVEL)?`<div style="display:flex;justify-content:space-between;gap:12px;"><span>${chip(COLORS.pt)}Profit Target</span><b>${fmtMoney(PASS_LEVEL)}</b></div>`:'')+
        (Number.isFinite(MAX_LOSS)?`<div style="display:flex;justify-content:space-between;gap:12px;"><span>${chip(COLORS.red)}Max Drawdown</span><b>${fmtMoney(MAX_LOSS)}</b></div>`:'');
      const rect=container.getBoundingClientRect();
      const tw=tip.offsetWidth||220, th=tip.offsetHeight||60, m=8;
      const cx=rect.left+(param.point?.x??0), cy=rect.top+(param.point?.y??0);
      let x=cx-tw/2, y=cy-th-12; if(y<m) y=cy+12;
      x=Math.max(m,Math.min(x,window.innerWidth -tw -m));
      y=Math.max(m,Math.min(y,window.innerHeight-th -m));
      tip.style.left=`${x}px`; tip.style.top=`${y}px`; tip.style.opacity='1';
    });
    container.addEventListener('mouseleave', hideTip, {passive:true});

    selectEl?.addEventListener('change', (e)=>{
      const days=parseInt(e.target.value,10)||0;
      applyData(days);
    });
  }).catch(err => console.error('[LWC] load error:', err));
})();
</script>

