/* Account Overview – Tabs + Panel + Donuts + Barras */

(function () {
  const clamp = (v, min, max) => Math.min(Math.max(v, min), max);

  // ===== Init donuts
  document.querySelectorAll('.mt-donut').forEach(el => {
    const v = parseFloat(el.getAttribute('data-donut-value') || '0');
    el.style.setProperty('--mt-donut-value', String(clamp(v, 0, 100)));
  });

  // ===== Init barras Reward/Risk
  document.querySelectorAll('.mt-summary__bar[data-progress]').forEach(bar => {
    const pct = clamp(parseFloat(bar.getAttribute('data-progress') || '0'), 0, 100);
    const fill = bar.querySelector('.mt-progress-bar__fill');
    if (fill && fill.parentElement) {
      fill.parentElement.style.setProperty('--mt-progress-value', pct + '%');
    }
  });

  // ===== Tabs -> mostrar panel correspondiente
  const rootTabs = document.querySelector('.mt-feature-tabs');
  if (!rootTabs) return;

  const tablist  = rootTabs.querySelector('[data-fc-tabs]');
  const viewport = rootTabs.querySelector('.mt-tabs-viewport');
  const group    = rootTabs.querySelector('.mt-toggle-group');
  const prevBtn  = rootTabs.querySelector('[data-fc-prev]');
  const nextBtn  = rootTabs.querySelector('[data-fc-next]');
  const gradL    = rootTabs.querySelector('.mt-tabs-gradient--left');
  const gradR    = rootTabs.querySelector('.mt-tabs-gradient--right');
  const tabs     = Array.from(rootTabs.querySelectorAll('[data-fc-tab]'));
  const panels   = Array.from(rootTabs.querySelectorAll('[data-fc-panel]'));

  const activate = (id) => {
    // tabs
    tabs.forEach(t => {
      const active = t.getAttribute('data-id') === id;
      t.classList.toggle('active', active);
      t.setAttribute('aria-selected', active ? 'true' : 'false');
      t.tabIndex = active ? 0 : -1;
    });
    // panels
    panels.forEach(p => {
      const show = p.getAttribute('data-panel-for') === id;
      if (show) p.removeAttribute('hidden');
      else p.setAttribute('hidden', '');
    });
  };

  tabs.forEach(t => {
    t.addEventListener('click', () => {
      const id = t.getAttribute('data-id');
      if (!id) return;
      activate(id);
    });
  });

  // ===== Carrusel paso-a-paso (sin swiper)
  const updateOverflowUI = () => {
    const canScroll = group.scrollWidth > viewport.clientWidth + 1;
    const atStart = group.scrollLeft <= 2;
    const atEnd   = group.scrollLeft >= (group.scrollWidth - viewport.clientWidth - 2);

    prevBtn.disabled = !canScroll || atStart;
    nextBtn.disabled = !canScroll || atEnd;

    gradL.style.opacity = (!canScroll || atStart) ? '0' : '1';
    gradR.style.opacity = (!canScroll || atEnd)   ? '0' : '1';
  };

  const rectOf = el => el.getBoundingClientRect();
  const firstNotFullyVisibleOnLeft = () => {
    const vw = rectOf(viewport);
    for (let i = 0; i < tabs.length; i++) {
      const r = rectOf(tabs[i]);
      if (r.left < vw.left - 1) return i; // hay algo cortado a la izquierda
    }
    return -1;
  };
  const firstNotFullyVisibleOnRight = () => {
    const vw = rectOf(viewport);
    for (let i = tabs.length - 1; i >= 0; i--) {
      const r = rectOf(tabs[i]);
      if (r.right > vw.right + 1) return i; // hay algo cortado a la derecha
    }
    return -1;
  };

  const scrollToIndex = (idx, align = 'start') => {
    if (idx < 0 || idx >= tabs.length) return;
    tabs[idx].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: align });
  };

  const stepPrev = () => {
    // Busca el primer item que está “cortado” a la izquierda; si no, toma el anterior al primero visible
    const idxCut = firstNotFullyVisibleOnLeft();
    if (idxCut >= 0) { scrollToIndex(idxCut, 'start'); return; }

    const vw = rectOf(viewport);
    const firstVisibleIdx = tabs.findIndex(el => {
      const r = rectOf(el);
      return r.left >= vw.left - 1 && r.right <= vw.right + 1;
    });
    if (firstVisibleIdx > 0) scrollToIndex(firstVisibleIdx - 1, 'start');
  };

  const stepNext = () => {
    // Busca el primer item “cortado” a la derecha; si no, el siguiente al último visible
    const idxCut = firstNotFullyVisibleOnRight();
    if (idxCut >= 0) { scrollToIndex(idxCut, 'end'); return; }

    const vw = rectOf(viewport);
    let lastVisibleIdx = -1;
    tabs.forEach((el, i) => {
      const r = rectOf(el);
      const fully = r.left >= vw.left - 1 && r.right <= vw.right + 1;
      if (fully) lastVisibleIdx = i;
    });
    if (lastVisibleIdx >= 0 && lastVisibleIdx < tabs.length - 1) {
      scrollToIndex(lastVisibleIdx + 1, 'end');
    }
  };

  prevBtn.addEventListener('click', stepPrev);
  nextBtn.addEventListener('click', stepNext);
  group.addEventListener('scroll', () => requestAnimationFrame(updateOverflowUI));
  window.addEventListener('resize', () => requestAnimationFrame(updateOverflowUI));

  // Asegura que el tab activo inicial esté a la vista
  const initialActive = tabs.find(t => t.classList.contains('active')) || tabs[0];
  if (initialActive) {
    initialActive.scrollIntoView({ block: 'nearest', inline: 'center' });
  }
  updateOverflowUI();
})();
