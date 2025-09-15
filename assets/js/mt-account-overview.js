(function () {
  // util
  function cssEscapeSafe(s){ if(window.CSS&&CSS.escape) return CSS.escape(s); return String(s).replace(/[^a-zA-Z0-9_-]/g,'\\$&'); }

  function setup(root){
    const scroller = root.querySelector('[data-fc-tabs]');
    const leftBtn  = root.querySelector('[data-fc-arrow="left"]');
    const rightBtn = root.querySelector('[data-fc-arrow="right"]');
    const gradL    = root.querySelector('[data-fc-grad-left]');
    const gradR    = root.querySelector('[data-fc-grad-right]');
    const panel    = root.querySelector('[data-fc-panel]');
    if(!scroller) return;

    // demo (reemplaza por API)
    const PANEL_DATA = {
      overview:'Overview · demo hardcode',
      es:'E-mini S&P 500 · demo hardcode',
      nq:'E-mini NASDAQ 100 · demo hardcode',
      rt:'E-mini Russell 2000 · demo hardcode',
      ng:'E-mini Natural Gas · demo hardcode',
      nkd:'Nikkei NKD · demo hardcode',
      aud:'Australian Dollar · demo hardcode',
      gbp:'British Pound · demo hardcode'
    };

    // ---- precomputar comienzos de cada tab (offsetLeft)
    function tabStarts(){
      return Array.from(scroller.querySelectorAll('[data-fc-tab]')).map(el => el.offsetLeft);
    }
    function clamp(x){
      const max = Math.max(0, scroller.scrollWidth - scroller.clientWidth);
      return Math.max(0, Math.min(x, max));
    }

    // ---- flechas 1 paso (basado en starts)
    function stepRight(){
      const starts = tabStarts();
      const cur = Math.round(scroller.scrollLeft);
      // primer start estrictamente mayor que la posición actual
      const next = starts.find(s => s > cur + 1);
      const target = clamp(next !== undefined ? next : (scroller.scrollWidth - scroller.clientWidth));
      if (target > cur + 1) scroller.scrollTo({ left: target, behavior:'smooth' });
      updateGradientsSoon();
    }
    function stepLeft(){
      const starts = tabStarts();
      const cur = Math.round(scroller.scrollLeft);
      // último start estrictamente menor que la posición actual
      let prev = 0;
      for (let i=starts.length-1;i>=0;i--){
        if (starts[i] < cur - 1) { prev = starts[i]; break; }
      }
      const target = clamp(prev);
      if (target < cur - 1) scroller.scrollTo({ left: target, behavior:'smooth' });
      updateGradientsSoon();
    }

    // ---- activar tab (clase active + mantener visible con scroll mínimo)
    function setActive(id){
      scroller.querySelectorAll('[data-fc-tab]').forEach(tab=>{
        const active = tab.getAttribute('data-id') === id;
        tab.setAttribute('data-status', active ? 'active' : 'normal');
        tab.setAttribute('aria-selected', active ? 'true' : 'false');
        tab.setAttribute('tabindex', active ? '0' : '-1');
        tab.classList.toggle('active', active);
        // (visual mínimo para test)
        tab.style.background = active ? 'var(--Primary-500,#F1A035)' : 'var(--Surface-Dark,#292524)';
        tab.style.outline    = `1px ${active ? 'var(--Primary-500,#F1A035)' : 'var(--Colors-Gray-700,#404040)'} solid`;
        const label = tab.querySelector('.mt-fc-tab-label');
        if (label) label.style.color = active ? 'var(--Surface-Body,#131210)' : 'var(--Colors-Gray-400,#A8A29E)';
      });

      const el = scroller.querySelector(`[data-fc-tab][data-id="${cssEscapeSafe(id)}"]`);
      if (el){
        const L = Math.round(scroller.scrollLeft);
        const R = L + scroller.clientWidth;
        const left = el.offsetLeft, right = left + el.offsetWidth;
        let target = L;
        if (left < L) target = left;
        else if (right > R) target = right - scroller.clientWidth;
        target = clamp(target);
        if (Math.abs(target - L) > 1) scroller.scrollTo({ left: target, behavior:'smooth' });
      }

      if (panel) panel.textContent = PANEL_DATA[id] || '';
      document.dispatchEvent(new CustomEvent('mt:feature-tab-change', { detail:{ id } }));
      updateGradientsSoon();
    }

    // ---- gradientes
    function updateGradients(){
      const canOverflow = scroller.scrollWidth > scroller.clientWidth + 1;
      const L  = Math.round(scroller.scrollLeft);
      const R  = L + scroller.clientWidth;
      if (gradL) gradL.style.opacity = canOverflow && L > 1 ? '1' : '0';
      if (gradR) gradR.style.opacity = canOverflow && R < scroller.scrollWidth - 1 ? '1' : '0';
    }
    function updateGradientsSoon(){ setTimeout(updateGradients, 240); }

    // ---- listeners
    leftBtn  && leftBtn.addEventListener('click', stepLeft);
    rightBtn && rightBtn.addEventListener('click', stepRight);
    leftBtn  && leftBtn.addEventListener('keydown',  e=>{ if(e.key===' '||e.key==='Enter'){ e.preventDefault(); stepLeft(); }});
    rightBtn && rightBtn.addEventListener('keydown', e=>{ if(e.key===' '||e.key==='Enter'){ e.preventDefault(); stepRight(); }});

    scroller.addEventListener('click', (e)=>{
      const tab = e.target.closest && e.target.closest('[data-fc-tab]');
      if (!tab) return;
      setActive(tab.getAttribute('data-id'));
    });
    scroller.addEventListener('keydown', (e)=>{
      const tab = e.target.closest && e.target.closest('[data-fc-tab]');
      if(!tab) return;
      if(e.key===' '||e.key==='Enter'){ e.preventDefault(); setActive(tab.getAttribute('data-id')); }
      if(e.key==='ArrowRight'||e.key==='ArrowLeft'){
        e.preventDefault();
        const tabs = Array.from(scroller.querySelectorAll('[data-fc-tab]'));
        const idx  = tabs.indexOf(tab);
        const next = e.key==='ArrowRight' ? Math.min(idx+1,tabs.length-1) : Math.max(idx-1,0);
        (tabs[next]||tab).focus();
      }
    });

    // ---- init
    function init(){
      const active = scroller.querySelector('[data-fc-tab].active')
                 || scroller.querySelector('[data-fc-tab][data-status="active"]')
                 || scroller.querySelector('[data-fc-tab]');
      if (active) setActive(active.getAttribute('data-id'));
      updateGradients();
    }
    window.addEventListener('resize', updateGradients);
    scroller.addEventListener('scroll', updateGradients);
    init();
  }

  document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('[data-fc]').forEach(setup);
  });
})();
