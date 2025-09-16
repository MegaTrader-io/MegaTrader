(function () {
  const clamp = (v, min, max) => Math.min(Math.max(v, min), max);

  document.querySelectorAll('.mt-feature-tabs').forEach(root => {
    const viewport = root.querySelector('.mt-tabs-viewport');
    const scroller = root.querySelector('.mt-toggle-group');
    const prevBtn  = root.querySelector('.js-tabs-prev');
    const nextBtn  = root.querySelector('.js-tabs-next');
    const gradL    = root.querySelector('.mt-tabs-gradient--left');
    const gradR    = root.querySelector('.mt-tabs-gradient--right');
    const tabs     = Array.from(root.querySelectorAll('[data-fc-tab]'));
    const panels   = Array.from(root.querySelectorAll('[data-fc-panel]'));

    /* ---------- helpers ---------- */
    const updateGradients = () => {
      const max = scroller.scrollWidth - scroller.clientWidth;
      const x   = Math.round(scroller.scrollLeft);
      const atStart = x <= 0;
      const atEnd   = x >= max - 1;

      if (gradL) gradL.style.opacity = atStart ? 0 : 1;
      if (gradR) gradR.style.opacity = atEnd ? 0 : 1;

      if (prevBtn) prevBtn.disabled = atStart;
      if (nextBtn) nextBtn.disabled = atEnd;
    };

    const ensureVisible = (chip) => {
      const v = viewport.getBoundingClientRect();
      const r = chip.getBoundingClientRect();
      const pad = 8;
      if (r.left < v.left + pad) {
        scroller.scrollBy({ left: r.left - v.left - pad, behavior: 'smooth' });
      } else if (r.right > v.right - pad) {
        scroller.scrollBy({ left: r.right - v.right + pad, behavior: 'smooth' });
      }
    };

    const step = (dir) => {
      const v = viewport.getBoundingClientRect();
      if (dir > 0) { // right
        const target = tabs.find(t => t.getBoundingClientRect().right > v.right - 1);
        if (target) {
          scroller.scrollBy({ left: (target.getBoundingClientRect().right - v.right) + 8, behavior: 'smooth' });
        }
      } else { // left
        const target = [...tabs].reverse().find(t => t.getBoundingClientRect().left < v.left + 1);
        if (target) {
          scroller.scrollBy({ left: (target.getBoundingClientRect().left - v.left) - 8, behavior: 'smooth' });
        }
      }
    };

    const activate = (id) => {
      tabs.forEach(t => {
        const on = t.dataset.id === id;
        t.classList.toggle('active', on);
        t.setAttribute('aria-selected', on ? 'true' : 'false');
        t.tabIndex = on ? 0 : -1;
      });
      panels.forEach(p => {
        const show = p.getAttribute('data-panel-for') === id;
        if (show) p.removeAttribute('hidden'); else p.setAttribute('hidden', '');
      });
    };

    /* ---------- events ---------- */
    tabs.forEach(t => {
      t.addEventListener('click', () => {
        const id = t.dataset.id;
        if (!id) return;
        activate(id);
        ensureVisible(t);
      });
    });
    if (prevBtn) prevBtn.addEventListener('click', () => step(-1));
    if (nextBtn) nextBtn.addEventListener('click', () => step(1));
    scroller.addEventListener('scroll', updateGradients);
    window.addEventListener('resize', updateGradients);

    /* ---------- init (active tab + gradients) ---------- */
    const initial = tabs.find(t => t.classList.contains('active') || t.getAttribute('aria-selected') === 'true') || tabs[0];
    if (initial) {
      activate(initial.dataset.id);
      ensureVisible(initial);
    }
    updateGradients();

    /* ---------- donuts ---------- */
    root.querySelectorAll('.mt-donut').forEach(el => {
      const v = clamp(parseFloat(el.getAttribute('data-donut-value') || '0'), 0, 100);
      el.style.setProperty('--mt-donut-value', String(v));
    });

    /* ---------- progress bars ---------- */
    root.querySelectorAll('.mt-summary__bar[data-progress]').forEach(bar => {
      const pct = clamp(parseFloat(bar.getAttribute('data-progress') || '0'), 0, 100);
      const track = bar.querySelector('.mt-progress-bar');
      if (track) track.style.setProperty('--mt-progress-value', pct + '%');
    });
  });
})();
