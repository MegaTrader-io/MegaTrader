<?php
/**
 * Template Part: Account Trades
 * Path: template-parts/account/account-trades.php
 *
 * Requirements:
 * - Design tokens/classes already present (mt-*, colors, etc.)
 * - Tooltip markup available (mt-tooltip)
 * - Accepts mock via JS; server integration will call endpoint with { type: 'CLOSED'|'OPEN' }
 */
if (!defined('ABSPATH')) exit;

$root_id  = 'mt-trades';
$pager_id = 'mt-trades-pager';

$user_id = get_current_user_id();
$acc_id  = '';
if (isset($args['meta']['accountId']))      $acc_id = (string)$args['meta']['accountId'];
elseif (isset($args['accountId']))          $acc_id = (string)$args['accountId'];
elseif (isset($current_account_id))         $acc_id = (string)$current_account_id;
if ($acc_id === '')                         $acc_id = (string)get_user_meta($user_id, 'mt_current_account_id', true);

$per_page = isset($args['data']['per_page']) ? (int)$args['data']['per_page'] : 10;
?>
<style>
/* Scroll/grid alias (como Daily Journal) */
#<?php echo $root_id; ?> { --cols:11; --colw:120px; --bg:var(--Surface-Page,#1E1E1E); }
#<?php echo $root_id; ?> .dj-viewport{max-inline-size:100%}
#<?php echo $root_id; ?> .dj-clip{position:relative;overflow:hidden;border-radius:inherit;background:var(--bg)}
#<?php echo $root_id; ?> .dj-scroll{overflow-x:auto;overflow-y:hidden;background:var(--bg);scrollbar-width:thin;scrollbar-color:rgba(255,255,255,.15) transparent}
#<?php echo $root_id; ?> .dj-grid{display:grid;grid-template-columns:repeat(var(--cols),var(--colw));inline-size:max-content}
#<?php echo $root_id; ?> .dj-headrow{border-bottom:1px solid rgba(255,255,255,.12)}
#<?php echo $root_id; ?> .dj-row{border-bottom:1px solid var(--Colors-Gray-800,#292524)}
#<?php echo $root_id; ?> .dj-head{color:#fff;font-weight:700;font-size:.875rem;line-height:1.25;padding:.75rem .9rem}
#<?php echo $root_id; ?> .dj-cell{color:var(--Text-Body,#A8A29E);font-weight:500;font-size:.875rem;line-height:1.125rem;padding:.75rem .9rem;white-space:normal}
#<?php echo $root_id; ?> .is-right{text-align:right} .is-left{text-align:left}

/* Header & segmented buttons */
#<?php echo $root_id; ?> .tr-headbar{display:flex;align-items:center;gap:8px;justify-content:space-between}
#<?php echo $root_id; ?> .tr-title{color:var(--Text-Body,#A8A29E);font:500 16px/24px Roboto,system-ui,sans-serif}
#<?php echo $root_id; ?> .tr-seg{display:flex;gap:4px;padding:4px;border:1px solid var(--Colors-Gray-700,#404040);border-radius:8px;background:var(--Surface-Page,#1E1E1E)}
#<?php echo $root_id; ?> .tr-seg__btn{padding:4px 12px;border-radius:4px;font:500 14px/20px Roboto,system-ui,sans-serif;text-transform:uppercase;color:#fff;background:transparent;border:0;cursor:pointer}
#<?php echo $root_id; ?> .tr-seg__btn[aria-pressed="true"]{background:#fff;color:#292524}

/* Pills */
#<?php echo $root_id; ?> .mt-pill{display:inline-flex;align-items:center;justify-content:center;padding:4px 8px;border-radius:12px;font-weight:700;text-transform:uppercase;font-size:.75rem;line-height:16px}
#<?php echo $root_id; ?> .mt-pill--sec{background:var(--Secondary-500,#14B8A6);color:var(--Surface-Body,#131210)}
#<?php echo $root_id; ?> .mt-pill--err{background:var(--Error-500,#F43F5E);color:var(--Surface-Body,#131210)}

/* Pager */
#<?php echo $root_id; ?> .dj-pager{display:flex;align-items:center;justify-content:flex-end;gap:8px;margin-top:8px}
#<?php echo $root_id; ?> .dj-btn{display:inline-flex;align-items:center;justify-content:center;padding:4px;background:var(--Surface-Dark,#292524);border:1px solid var(--Colors-Gray-700,#404040);border-radius:4px}
#<?php echo $root_id; ?> .dj-pages{color:var(--Text-Body,#A8A29E);font:500 14px/20px Roboto,system-ui,sans-serif}
</style>

<div id="<?php echo esc_attr($root_id); ?>"
     class="mt-card"
     data-account-id="<?php echo esc_attr($acc_id); ?>"
     data-per-page="<?php echo (int)$per_page; ?>"
     data-nonce="<?php echo esc_attr(wp_create_nonce('mt-acc-nonce')); ?>">

  <!-- Header -->
  <div class="tr-headbar">
    <div class="d-flex align-items-center gap-2">
      <div class="tr-title">Trades</div>
      <span class="mt-tooltip">
        <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0" aria-label="Trades info"></i>
        <span class="mt-tooltip__panel" role="tooltip">
          <div class="mt-tooltip__title">About trades</div>
          <div class="mt-tooltip__body">Switch between Closed and Open positions.</div>
        </span>
      </span>
    </div>
    <div class="tr-seg" role="group" aria-label="Trades filter">
      <button type="button" class="tr-seg__btn" data-type="CLOSED" aria-pressed="true">Close</button>
      <button type="button" class="tr-seg__btn" data-type="OPEN" aria-pressed="false">Open</button>
    </div>
  </div>

  <!-- Grid -->
  <div class="dj-viewport" aria-busy="true">
    <div class="dj-clip">
      <div class="dj-scroll">
        <!-- Header columns -->
        <div class="dj-grid dj-headrow" style="--cols:11;">
          <div class="dj-head is-left">Symbol</div>
          <div class="dj-head is-right">Close Date</div>
          <div class="dj-head is-right">Net P&amp;L</div>
          <div class="dj-head is-right">Net ROI</div>
          <div class="dj-head is-right">Duration</div>
          <div class="dj-head is-right">Avg. Entry</div>
          <div class="dj-head is-right">Avg. Exit</div>
          <div class="dj-head is-right">Open Date</div>
          <div class="dj-head is-right">Tag</div>
          <div class="dj-head is-right">Status</div>
          <div class="dj-head is-right">Account Name</div>
        </div>
        <!-- Rows container -->
        <div class="dj-rows"></div>
      </div>
    </div>
  </div>

  <!-- Pager -->
  <nav id="<?php echo esc_attr($pager_id); ?>" class="dj-pager" aria-label="Trades pagination" hidden>
    <span class="dj-pages">Showing <span class="js-showing">0</span>/<span class="js-total">0</span></span>
    <div class="d-flex gap-1">
      <button class="dj-btn js-prev" type="button" disabled>
        <span class="mt-icon mt-icon-white mt-icon_chevron-left" aria-hidden="true"></span>
      </button>
      <button class="dj-btn js-next" type="button" disabled>
        <span class="mt-icon mt-icon-white mt-icon_chevron-right" aria-hidden="true"></span>
      </button>
    </div>
  </nav>
</div>

<script>
(() => {
  const root = document.getElementById('<?php echo $root_id; ?>');
  if (!root) return;

  /* -------- Mock data (para test) -------- */
  const MOCK = [
    { symbol:'MNQ',  type:'CLOSED', closeDate:'2025-07-12', netPl:-73.40, netRoi:-73.40, durationSec:2057, avgEntry:25.90, avgExit:25.90, openDate:'2025-07-12', tag:'1',     status:'LOSS',  accountName:'SGDBHSBTEBDHBJBSB' },
    { symbol:'MNQ',  type:'CLOSED', closeDate:'2025-07-13', netPl: 45.10, netRoi: 45.10, durationSec:1932, avgEntry:25.90, avgExit:25.90, openDate:'2025-07-13', tag:'',      status:'LOSS',  accountName:'SGDBHSBTEBDHBJBSB' },
    { symbol:'MNQ',  type:'CLOSED', closeDate:'2025-07-10', netPl:-234.28,netRoi:-234.28,durationSec:1458, avgEntry:139.28,avgExit:139.28,openDate:'2025-07-10', tag:'',      status:'WIN',   accountName:'SGDBHSBTEBDHBJBSB' },
    { symbol:'MNQ',  type:'CLOSED', closeDate:'2025-07-13', netPl:120.50, netRoi:120.50, durationSec:1810, avgEntry:25.90, avgExit:25.90, openDate:'2025-07-13', tag:'',      status:'WIN',   accountName:'SGDBHSBTEBDHBJBSB' },
    { symbol:'ES',   type:'OPEN',   closeDate:null,         netPl:  12.00, netRoi:  0.95, durationSec: 620, avgEntry:4825.50,avgExit:null,  openDate:'2025-11-14',tag:'ALGO', status:'OPEN',  accountName:'Primary' },
    { symbol:'NQ',   type:'OPEN',   closeDate:null,         netPl:  -8.75, netRoi: -0.40, durationSec: 420, avgEntry:16450.25,avgExit:null, openDate:'2025-11-14',tag:'',     status:'OPEN',  accountName:'Primary' },
  ];

  /* -------- Helpers -------- */
  const perPage  = parseInt(root.dataset.perPage || '10', 10);
  const rowsWrap = root.querySelector('.dj-rows');
  const pager    = root.querySelector('.dj-pager');
  const showing  = pager?.querySelector('.js-showing');
  const totalEl  = pager?.querySelector('.js-total');
  const btnPrev  = pager?.querySelector('.js-prev');
  const btnNext  = pager?.querySelector('.js-next');
  const segBtns  = root.querySelectorAll('.tr-seg__btn');

  let page = 1;
  let currentType = 'CLOSED'; // default

  const fmtMoney = v => (v==null || v==='-' || isNaN(v)) ? '-' :
    ((v<0?'-':'') + '$' + Math.abs(+v).toFixed(2));
  const fmtPct   = v => (v==null || v==='-' || isNaN(v)) ? '-' :
    (Number(v).toFixed(2) + '%');
  const fmtDate  = s => {
    if(!s) return '-';
    if (/^\d{2}\/\d{2}\/\d{4}$/.test(s)) return s;
    const t = Date.parse(s); if (isNaN(t)) return '-';
    const d = new Date(t); const mm = String(d.getMonth()+1).padStart(2,'0');
    const dd = String(d.getDate()).padStart(2,'0'); const yy = d.getFullYear();
    return `${mm}/${dd}/${yy}`;
  };
  const fmtDur   = sec => {
    if(sec==null || isNaN(sec)) return '-';
    sec = Math.floor(sec);
    const h = Math.floor(sec/3600), m = Math.floor((sec%3600)/60), s = sec%60;
    return h>0 ? `${h}h ${String(m).padStart(2,'0')}m ${String(s).padStart(2,'0')}s`
               : `${m}m ${String(s).padStart(2,'0')}s`;
  };

  /* -------- Render -------- */
  function buildRow(r, idx, isLast=false){
    const netCls = (typeof r.netPl === 'number') ? (r.netPl>0?'text-success':(r.netPl<0?'text-danger':'')) : '';
    const roiCls = (typeof r.netRoi=== 'number') ? (r.netRoi>0?'text-success':(r.netRoi<0?'text-danger':'')) : '';
    const tagHtml = (r.tag && String(r.tag).trim()!=='-') ? `<span class="mt-pill mt-pill--sec">${String(r.tag)}</span>` : '-';
    let statusHtml = '-';
    const st = String(r.status||'').toUpperCase();
    if (st==='WIN')  statusHtml = '<span class="mt-pill mt-pill--sec">Win</span>';
    if (st==='LOSS') statusHtml = '<span class="mt-pill mt-pill--err">Loss</span>';
    if (st==='OPEN') statusHtml = '<span class="mt-pill mt-pill--sec">Open</span>';

    return `
      <div class="dj-grid dj-row${isLast?' is-last':''}" style="--cols:11;">
        <div class="dj-cell is-left">${r.symbol ?? '-'}</div>
        <div class="dj-cell is-right">${fmtDate(r.closeDate)}</div>
        <div class="dj-cell is-right ${netCls}">${fmtMoney(r.netPl)}</div>
        <div class="dj-cell is-right ${roiCls}">${fmtPct(r.netRoi)}</div>
        <div class="dj-cell is-right">${fmtDur(r.durationSec)}</div>
        <div class="dj-cell is-right">${fmtMoney(r.avgEntry)}</div>
        <div class="dj-cell is-right">${fmtMoney(r.avgExit)}</div>
        <div class="dj-cell is-right">${fmtDate(r.openDate)}</div>
        <div class="dj-cell is-right">${tagHtml}</div>
        <div class="dj-cell is-right">${statusHtml}</div>
        <div class="dj-cell is-right">${r.accountName ?? '-'}</div>
      </div>`;
  }

  function currentSource(){
    // futuro: aquí llamamos al endpoint con { type: currentType }
    // return fetch(url,{method:'POST',body:JSON.stringify({type:currentType})}).then(r=>r.json())
    // Mock local:
    return Promise.resolve(MOCK.filter(x => x.type === currentType));
  }

  function renderPage(data){
    const total = data.length;
    const start = (page-1)*perPage;
    const end   = Math.min(start+perPage, total);
    const slice = data.slice(start, end);

    rowsWrap.innerHTML = slice.map((r,i)=>buildRow(r,i, i===slice.length-1)).join('');
    if (pager){
      pager.hidden = total===0;
      showing.textContent = String(end);
      totalEl.textContent = String(total);
      btnPrev.disabled = page<=1;
      btnNext.disabled = end>=total;
    }
    root.querySelector('.dj-viewport').setAttribute('aria-busy','false');
  }

  function apply(){
    root.querySelector('.dj-viewport').setAttribute('aria-busy','true');
    currentSource().then(renderPage);
  }

  /* -------- Events -------- */
  segBtns.forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const t = btn.getAttribute('data-type'); // 'CLOSED' | 'OPEN'
      if (t === currentType) return;
      currentType = t;
      segBtns.forEach(b=>b.setAttribute('aria-pressed', String(b===btn)));
      page = 1;
      apply();
    });
  });
  btnPrev?.addEventListener('click', ()=>{ if(page>1){ page--; apply(); } });
  btnNext?.addEventListener('click', ()=>{ page++; apply(); });

  // init
  apply();
})();
</script>
