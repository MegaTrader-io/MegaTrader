(function () {
  function setup(root) {
    const scroller = root.querySelector('[data-fc-tabs]');
    const leftBtn  = root.querySelector('[data-fc-arrow="left"]');
    const rightBtn = root.querySelector('[data-fc-arrow="right"]');
    const gradR    = root.querySelector('[data-fc-grad-right"]') || root.querySelector('[data-fc-grad-right]');

    if (!scroller) return;

    // ---- Scroll con flechas
    const STEP = 180;
    function scrollByDir(dir) {
      scroller.scrollBy({ left: dir * STEP, behavior: 'smooth' });
      setTimeout(updateGradient, 220);
    }
    leftBtn  && leftBtn.addEventListener('click', () => scrollByDir(-1));
    rightBtn && rightBtn.addEventListener('click', () => scrollByDir(+1));
    leftBtn  && leftBtn.addEventListener('keydown', (e)=>{ if(e.key==='Enter'||e.key===' ') { e.preventDefault(); scrollByDir(-1);} });
    rightBtn && rightBtn.addEventListener('keydown', (e)=>{ if(e.key==='Enter'||e.key===' ') { e.preventDefault(); scrollByDir(+1);} });

    // ---- Activación de tabs (solo estados/ARIA + evento)
    function setActive(id) {
      const tabs = scroller.querySelectorAll('[data-fc-tab]');
      tabs.forEach(tab => {
        const active = tab.getAttribute('data-id') === id;
        tab.setAttribute('data-status', active ? 'active' : 'normal');
        tab.setAttribute('aria-selected', active ? 'true' : 'false');
        tab.setAttribute('tabindex', active ? '0' : '-1');
      });

      // Asegurar visibilidad
      const el = scroller.querySelector(`[data-fc-tab][data-id="${CSS.escape(id)}"]`);
      if (el && el.scrollIntoView) el.scrollIntoView({ behavior: 'smooth', inline: 'nearest', block: 'nearest' });

      // Dispara evento para que OTRO script actualice el contenido del panel si quiere
      document.dispatchEvent(new CustomEvent('mt:feature-tab-change', { detail: { id } }));
      updateGradient();
    }

    // Click y teclado en tabs
    scroller.addEventListener('click', (e) => {
      const tab = e.target.closest && e.target.closest('[data-fc-tab]');
      if (!tab) return;
      setActive(tab.getAttribute('data-id'));
    });
    scroller.addEventListener('keydown', (e) => {
      const tab = e.target.closest && e.target.closest('[data-fc-tab]');
      if (!tab) return;
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); setActive(tab.getAttribute('data-id')); }
      if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
        e.preventDefault();
        const tabs = Array.from(scroller.querySelectorAll('[data-fc-tab]'));
        const idx = tabs.indexOf(tab);
        const next = e.key === 'ArrowRight' ? Math.min(idx + 1, tabs.length - 1) : Math.max(idx - 1, 0);
        (tabs[next] || tab).focus();
      }
    });

    // ---- Gradiente (opcional, sin inyectar HTML)
    function updateGradient() {
      if (!gradR) return;
      const atRight = scroller.scrollLeft + scroller.clientWidth >= scroller.scrollWidth - 2;
      gradR.style.opacity = atRight ? '0' : '1';
    }
    scroller.addEventListener('scroll', updateGradient);
    window.addEventListener('resize', updateGradient);

    // Init: respeta el que venga activo en HTML
    const active = scroller.querySelector('[data-fc-tab][data-status="active"]');
    if (active) setActive(active.getAttribute('data-id')); else {
      const first = scroller.querySelector('[data-fc-tab]');
      first && setActive(first.getAttribute('data-id'));
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-fc]').forEach(setup);
  });
})();
