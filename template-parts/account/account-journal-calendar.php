<?php
/**
 * File: template-parts/account/account-journal-calendar.php
 * Calendario de trades con navegación Mes/Año y botón TODAY.
 * Reemplaza los <select> nativos por dropdowns custom mt-dropdown (control total del panel).
 *
 * Entrada esperada en $args['data']:
 *  - 'days' => [ ['date'=>'YYYY-MM-DD','pnl'=>float,'trades'=>int], ... ]
 *  - 'start_year' (int, opcional)
 *  - 'start_month' (int 0..11, opcional)
 *  - 'years_back' (int, opcional)
 */
defined('ABSPATH') || exit;

/* ===== Normalizar entrada ===== */
$days_arg = [];
if (isset($args['data']['days']) && is_array($args['data']['days'])) {
  $days_arg = $args['data']['days'];
} elseif (isset($args['data']) && is_array($args['data']) && isset($args['data'][0]['date'])) {
  $days_arg = $args['data'];
} elseif (isset($args['days']) && is_array($args['days'])) {
  $days_arg = $args['days'];
}

/* ===== Mock si no llega data ===== */
if (empty($days_arg)) {
  $days_arg = [
    ['date' => '2025-11-09', 'pnl' => -1200.00, 'trades' => 4],
    ['date' => '2025-11-10', 'pnl' => 1500.00, 'trades' => 8],
  ];
}

$now = new DateTimeImmutable('now');
$years_back  = isset($args['data']['years_back'])  ? (int) $args['data']['years_back']  : 20;
$years_back  = max(1, $years_back);
$start_year  = isset($args['data']['start_year'])  ? (int) $args['data']['start_year']  : (int) $now->format('Y');
$start_month = isset($args['data']['start_month']) ? (int) $args['data']['start_month'] : ((int) $now->format('n') - 1);
$start_month = max(0, min(11, $start_month));

$cal_id = 'mt-trade-cal-' . wp_rand(1000, 9999);
?>

<div class="mt-card">
  <div class="mt-card__wrapper" style="display:flex;flex-direction:column;gap:16px;align-items:flex-end;">
    <!-- Header -->
    <div class="mt-card__header" style="display:flex;gap:8px;align-items:center;width:100%;justify-content:flex-end;">
      <div style="flex:1;display:flex;align-items:center;justify-content:space-between;">
        <div class="mt-card__title" style="display:flex;align-items:center;gap:8px;">
          <div class="mt-card__title__journal" style="color:var(--Text-Body,#A8A29E);font-size:16px;line-height:24px;font-weight:500;">Trade calendar</div>
          <span class="mt-tooltip">
            <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0" aria-label="Calendar info"></i>
            <span class="mt-tooltip__panel" role="tooltip">
              <div class="mt-tooltip__title"><?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_dpl_tooltip_title'] ?? 'Calendar'); ?></div>
              <div class="mt-tooltip__body"><?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_dpl_tooltip_description'] ?? 'PnL diario (verde/rojo) y número de trades por día.'); ?></div>
            </span>
          </span>
        </div>
      </div>

      <!-- Prev -->
      <button type="button" class="mt-btn mt-btn--sm mt-btn--secondary mt-btn--outline" id="<?php echo esc_attr($cal_id); ?>-prev" aria-label="Previous" style="padding:4px;border-radius:4px;">
        <span class="mt-icon mt-icon_caret-left"></span>
      </button>

      <!-- Month dropdown -->
      <div class="mt-dd" id="<?php echo esc_attr($cal_id); ?>-month-dd" data-type="month" role="combobox" aria-expanded="false" aria-haspopup="listbox" aria-controls="<?php echo esc_attr($cal_id); ?>-month-list" aria-label="Month" tabindex="0">
        <button type="button" class="mt-dd__button" data-dd-btn>
          <span data-dd-label>—</span>
          <i class="mt-icon mt-icon_caret-down"></i>
        </button>
        <div class="mt-dd__panel" id="<?php echo esc_attr($cal_id); ?>-month-list" role="listbox" tabindex="-1" hidden></div>
      </div>

      <!-- Year dropdown -->
      <div class="mt-dd" id="<?php echo esc_attr($cal_id); ?>-year-dd" data-type="year" role="combobox" aria-expanded="false" aria-haspopup="listbox" aria-controls="<?php echo esc_attr($cal_id); ?>-year-list" aria-label="Year" tabindex="0">
        <button type="button" class="mt-dd__button" data-dd-btn>
          <span data-dd-label>—</span>
          <i class="mt-icon mt-icon_caret-down"></i>
        </button>
        <div class="mt-dd__panel" id="<?php echo esc_attr($cal_id); ?>-year-list" role="listbox" tabindex="-1" hidden></div>
      </div>

      <!-- Next -->
      <button type="button" class="mt-btn mt-btn--sm mt-btn--secondary mt-btn--outline" id="<?php echo esc_attr($cal_id); ?>-next" aria-label="Next" style="padding:4px;border-radius:4px;">
        <span class="mt-icon mt-icon_caret-right"></span>
      </button>

      <!-- TODAY -->
      <button type="button" class="mt-btn mt-btn--sm mt-btn--secondary" id="<?php echo esc_attr($cal_id); ?>-today"
        style="padding:12px 16px;background:var(--Surface-Dark,#292524);border-radius:12px;outline:1px solid var(--Colors-Gray-700,#404040);outline-offset:-1px;color:#FAFAFA;text-transform:uppercase;font-weight:500;">
        TODAY
      </button>
    </div>

    <!-- Body -->
    <div class="mt-card__body" style="width:100%;">
      <!-- Head (DOW) -->
      <div class="mt-cal-head" style="display:grid;grid-template-columns:repeat(7,1fr);gap:8px;padding:16px 0;">
        <?php foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $dow): ?>
          <div class="mt-cal-dow" style="text-align:center;color:var(--Text-Body,#A8A29E);font-size:14px;line-height:20px;font-weight:500;"><?php echo esc_html($dow); ?></div>
        <?php endforeach; ?>
      </div>

      <!-- Grid -->
      <div id="<?php echo esc_attr($cal_id); ?>" class="mt-trade-calendar"
           data-days='<?php echo wp_json_encode($days_arg, JSON_UNESCAPED_SLASHES); ?>'
           data-years-back="<?php echo (int) $years_back; ?>"
           data-start-year="<?php echo (int) $start_year; ?>"
           data-start-month="<?php echo (int) $start_month; ?>"
           style="display:grid;grid-template-columns:repeat(7,1fr);grid-auto-rows:92px;gap:8px;"></div>
    </div>
  </div>
</div>

<style>
/* ===== Dropdown custom (mt-dd) ===== */
.mt-dd{ position:relative; width: 160px; }
.mt-dd .mt-dd__button{
  width:100%; display:flex; align-items:center; justify-content:space-between; gap:8px;
  padding:12px 16px; border-radius:12px; border:1px solid var(--Colors-Gray-700,#404040);
  background: rgba(30,30,30,0.70); color: var(--Text-Body,#A8A29E);
  font: 500 16px/24px Roboto, system-ui, sans-serif; cursor:pointer;
}
.mt-dd .mt-dd__button:focus{ outline:1px solid #ffb34a; box-shadow:0 0 0 2px rgba(255,179,74,.15); }
.mt-dd[aria-expanded="true"] .mt-dd__button .mt-icon_caret-down{ transform: rotate(180deg); }

.mt-dd .mt-dd__panel{
  position:absolute; inset: auto 0 0 0; transform: translateY(calc(100% + 6px));
  background:#1e1e1e; border-radius:12px; border:1px solid var(--Colors-Gray-700,#404040);
  max-height:280px; overflow:auto; padding:6px; z-index: 20;
  /* Scrollbar del sitio */
  scrollbar-color: rgba(255,255,255,.15) transparent; scrollbar-width: thin;
}
.mt-dd .mt-dd__option{
  display:flex; align-items:center; width:100%;
  color: var(--Text-Body,#A8A29E); background:transparent;
  border:0; border-radius:8px; padding:10px 12px; text-align:left; cursor:pointer;
  font: 500 16px/24px Roboto, system-ui, sans-serif;
}
.mt-dd .mt-dd__option:hover{ background:#2a2a2a; color:#fff; }
.mt-dd .mt-dd__option[aria-selected="true"],
.mt-dd .mt-dd__option.is-active{
  background:#ffb34a; color:#000;
}

/* webkit scrollbar fallback */
.mt-dd .mt-dd__panel::-webkit-scrollbar{ width:8px; height:8px; }
.mt-dd .mt-dd__panel::-webkit-scrollbar-thumb{ background:rgba(255,255,255,.15); border-radius:8px; }
.mt-dd .mt-dd__panel::-webkit-scrollbar-thumb:hover{ background:rgba(255,255,255,.25); }

/* caret down (re-usa tu sistema de íconos) */
.mt-icon_caret-down{
  mask-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"><path d="M7 10l5 5 5-5H7z" fill="black"/></svg>');
  background-color: currentColor; width:20px; height:20px; display:inline-block;
}
</style>

<script>
(function(){
  const ROOT = document.getElementById('<?php echo $cal_id; ?>');
  if(!ROOT) return;

  const BTN_PREV = document.getElementById('<?php echo $cal_id; ?>-prev');
  const BTN_NEXT = document.getElementById('<?php echo $cal_id; ?>-next');
  const BTN_TOD  = document.getElementById('<?php echo $cal_id; ?>-today');

  const DD_MONTH = document.getElementById('<?php echo $cal_id; ?>-month-dd');
  const DD_YEAR  = document.getElementById('<?php echo $cal_id; ?>-year-dd');

  const MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];

  const now = new Date();
  const YEARS_BACK  = Math.max(1, parseInt(ROOT.dataset.yearsBack || ROOT.getAttribute('data-years-back') || '20', 10));
  const START_YEAR  = parseInt(ROOT.dataset.startYear  || ROOT.getAttribute('data-start-year')  || now.getFullYear(), 10);
  const START_MONTH = parseInt(ROOT.dataset.startMonth || ROOT.getAttribute('data-start-month') || now.getMonth(),   10);

  // Dataset de días
  const raw = (()=>{ try{ return JSON.parse(ROOT.dataset.days || '[]'); }catch(_){ return []; }})();
  const byDate = Object.create(null);
  raw.forEach(item=>{
    if(!item) return;
    const key = String(item.date||'').slice(0,10);
    if(!key) return;
    byDate[key] = { pnl: Number(item.pnl)||0, trades: parseInt(item.trades||0,10) };
  });

  // Utils
  const pad = n => (n<10?('0'+n):(''+n));
  const isLeap = y => (y%4===0 && y%100!==0) || (y%400===0);
  const daysInMonth = (y,m) => [31, isLeap(y)?29:28,31,30,31,30,31,31,30,31,30,31][m];
  const moneyCompact = (n)=>{
    const s = n<0?'-':'';
    const a = Math.abs(n);
    if(a>=1000){ const k=a/1000; const num=(Math.floor(k)===k)?k.toFixed(0):k.toFixed(1).replace(/\.0$/,''); return s+'$'+num+'k'; }
    return s+'$'+a.toFixed(0);
  };

  /* ===== Dropdown helper ===== */
  function createDropdown(ddEl, items, {formatLabel=(v)=>String(v), value, onChange}){
    const btn  = ddEl.querySelector('[data-dd-btn]');
    const lab  = ddEl.querySelector('[data-dd-label]');
    const list = ddEl.querySelector('.mt-dd__panel');

    // pintar opciones
    list.innerHTML = '';
    items.forEach((it,idx)=>{
      const opt = document.createElement('button');
      opt.type='button';
      opt.className='mt-dd__option';
      opt.role='option';
      opt.dataset.value = String(it.value);
      opt.textContent = formatLabel(it.value);
      if (String(it.value)===String(value)){ opt.setAttribute('aria-selected','true'); opt.classList.add('is-active'); }
      list.appendChild(opt);
    });

    function open(){
      ddEl.setAttribute('aria-expanded','true');
      list.hidden = false;
      // focus opción seleccionada
      const sel = list.querySelector('.mt-dd__option[aria-selected="true"]') || list.querySelector('.mt-dd__option');
      if(sel){ sel.focus({preventScroll:true}); sel.scrollIntoView({block:'nearest'}); }
      // cerrar si click fuera
      document.addEventListener('click', onDocClick);
    }
    function close(){
      ddEl.setAttribute('aria-expanded','false');
      list.hidden = true;
      btn.focus({preventScroll:true});
      document.removeEventListener('click', onDocClick);
    }
    function onDocClick(e){
      if(!ddEl.contains(e.target)) close();
    }
    function setValue(v){
      lab.textContent = formatLabel(v);
      [...list.children].forEach(ch=>{
        const sel = (ch.dataset.value===String(v));
        ch.toggleAttribute('aria-selected', sel);
        ch.classList.toggle('is-active', sel);
      });
      onChange(v);
    }

    // eventos
    btn.addEventListener('click', (e)=>{
      const expanded = ddEl.getAttribute('aria-expanded')==='true';
      expanded ? close() : open();
    });
    ddEl.addEventListener('keydown',(e)=>{
      const expanded = ddEl.getAttribute('aria-expanded')==='true';
      if(!expanded && (e.key==='ArrowDown' || e.key==='Enter' || e.key===' ')){ e.preventDefault(); open(); return; }
      if(expanded){
        const opts = [...list.querySelectorAll('.mt-dd__option')];
        const idx  = opts.findIndex(o=>o===document.activeElement);
        if(e.key==='Escape'){ e.preventDefault(); close(); }
        else if(e.key==='ArrowDown'){ e.preventDefault(); const n=opts[Math.min(opts.length-1, idx+1)]||opts[0]; n.focus(); n.scrollIntoView({block:'nearest'}); }
        else if(e.key==='ArrowUp'){ e.preventDefault(); const p=opts[Math.max(0, idx-1)]||opts[opts.length-1]; p.focus(); p.scrollIntoView({block:'nearest'}); }
        else if(e.key==='Home' || e.key==='PageUp'){ e.preventDefault(); const f=opts[0]; f.focus(); f.scrollIntoView({block:'nearest'}); }
        else if(e.key==='End' || e.key==='PageDown'){ e.preventDefault(); const l=opts[opts.length-1]; l.focus(); l.scrollIntoView({block:'nearest'}); }
        else if(e.key==='Enter' || e.key===' '){
          e.preventDefault();
          const cur = document.activeElement;
          if(cur && cur.classList.contains('mt-dd__option')){
            setValue(cur.dataset.value);
            close();
          }
        }
      }
    });
    list.addEventListener('click',(e)=>{
      const opt = e.target.closest('.mt-dd__option');
      if(!opt) return;
      setValue(opt.dataset.value);
      close();
    });

    // api pública mínima
    return {
      set(v){ setValue(v); },
      get(){ return [...list.children].find(ch=>ch.getAttribute('aria-selected')==='true')?.dataset.value; }
    };
  }

  /* ===== Pintar dropdowns ===== */
  const years = (()=>{
    const end = now.getFullYear(), start = end - YEARS_BACK;
    const arr = [];
    for(let y=start; y<=end; y++) arr.push({value:y});
    return arr;
  })();
  const months = MONTHS.map((name, idx)=>({value: idx, label: name}));

  let currentYear = START_YEAR;
  let currentMonth = START_MONTH;

  const ddMonth = createDropdown(
    DD_MONTH,
    months,
    {
      formatLabel: v => MONTHS[parseInt(v,10)],
      value: currentMonth,
      onChange: (v)=>{ currentMonth = parseInt(v,10); render(currentYear, currentMonth); }
    }
  );
  const ddYear = createDropdown(
    DD_YEAR,
    years,
    {
      formatLabel: v => String(v),
      value: currentYear,
      onChange: (v)=>{ currentYear = parseInt(v,10); render(currentYear, currentMonth); }
    }
  );

  /* ===== Render calendario ===== */
  function buildMatrix(y,m){
    const first = new Date(y,m,1), firstDow = first.getDay(), total = daysInMonth(y,m);
    const prevM = (m+11)%12, prevY = (m===0? y-1 : y), prevTotal = daysInMonth(prevY, prevM);
    const cells = [];
    for(let i=firstDow-1; i>=0; i--) cells.push({ y:prevY, m:prevM, d:prevTotal-i, other:true });
    for(let d=1; d<=total; d++)   cells.push({ y, m, d, other:false });
    const after = 42 - cells.length, nextM = (m+1)%12, nextY = (m===11? y+1 : y);
    for(let d=1; d<=after; d++)   cells.push({ y:nextY, m:nextM, d, other:true });
    return cells;
  }

  function render(y,m){
    ROOT.innerHTML = '';
    const cells = buildMatrix(y,m);
    const todayKey = `${now.getFullYear()}-${String(now.getMonth()+1).padStart(2,'0')}-${String(now.getDate()).padStart(2,'0')}`;

    cells.forEach(c=>{
      const key = `${c.y}-${String(c.m+1).padStart(2,'0')}-${String(c.d).padStart(2,'0')}`;
      const info = byDate[key];
      const pnl  = info ? info.pnl : null;
      const tr   = info ? info.trades : null;

      const cell = document.createElement('button');
      cell.type='button';
      cell.className='mt-cal-cell';
      cell.style.cssText = `
        min-width:60px;min-height:60px;padding:4px;border:none;border-radius:8px;
        background: rgba(255,255,255,0.05); color:#fff; display:flex; flex-direction:column; justify-content:center; align-items:center;
        ${c.other ? 'opacity:.6;' : ''}
      `;
      cell.setAttribute('aria-label', `${key}, ${pnl!==null?('$'+pnl.toFixed(0)):'no data'}, ${tr||0} trades`);

      const top=document.createElement('div');
      top.textContent=c.d;
      top.style.cssText='width:100%;text-align:center;color:#fff;font-size:16px;line-height:24px;font-weight:700;';

      const mid=document.createElement('div');
      if(pnl!==null){
        mid.textContent = moneyCompact(pnl);
        mid.style.cssText = 'width:100%;text-align:center;font-size:16px;line-height:24px;font-weight:700;'+(pnl>0?'color:var(--Success-400,#2DD4BF);':'color:var(--Error-300,#FDA4AF);');
      }else{
        mid.textContent=''; mid.style.cssText='height:0;line-height:0;';
      }

      const bot=document.createElement('div');
      bot.textContent = (tr!=null)? `${tr} Trades` : '';
      bot.style.cssText='width:100%;text-align:center;color:#fff;font-size:14px;line-height:20px;font-weight:700;';

      if(pnl!==null){
        if(pnl>0) cell.style.background='var(--Success-800,#115E59)';
        else if(pnl<0) cell.style.background='var(--Error-900,#881337)';
      }
      if(key===todayKey){
        cell.style.outline='1px solid var(--Colors-Gray-700,#404040)';
        cell.style.outlineOffset='-1px';
      }

      cell.appendChild(top);
      if(pnl!==null) cell.appendChild(mid);
      if(tr!=null)   cell.appendChild(bot);
      ROOT.appendChild(cell);
    });
  }

  // Botones navegación
  BTN_TOD.addEventListener('click', ()=>{ currentYear=now.getFullYear(); currentMonth=now.getMonth(); ddYear.set(currentYear); ddMonth.set(currentMonth); render(currentYear,currentMonth); });
  BTN_PREV.addEventListener('click', ()=>{ let y=currentYear, m=currentMonth-1; if(m<0){m=11;y--;} currentYear=y; currentMonth=m; ddYear.set(y); ddMonth.set(m); render(y,m); });
  BTN_NEXT.addEventListener('click', ()=>{ let y=currentYear, m=currentMonth+1; if(m>11){m=0;y++;} currentYear=y; currentMonth=m; ddYear.set(y); ddMonth.set(m); render(y,m); });

  // Init
  ddYear.set(currentYear);
  ddMonth.set(currentMonth);
  render(currentYear,currentMonth);
})();
</script>
