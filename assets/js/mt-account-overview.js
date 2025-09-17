/* mt-account-overview.js */

(function () {
  const $  = (sel, root=document) => root.querySelector(sel);
  const $$ = (sel, root=document) => Array.from(root.querySelectorAll(sel));
  const clamp = (n, min, max) => Math.max(min, Math.min(max, parseInt(n, 10) || 0));

  /* ========= Donuts ========= */
  function initDonuts(root=document){
    $$(".mt-donut", root).forEach(d=>{
      const v = clamp(d.dataset.donutValue, 0, 100);
      d.style.setProperty("--mt-donut-value", v);
      d.classList.toggle("is-empty", v === 0);
      const t = $(".mt-donut__percent", d);
      if (t) t.textContent = v ? (v + "%") : "--";
    });
  }

  /* ========= Dual bar (Reward/Risk con separador 4px) ========= */
  function initDualBars(root=document){
    $$(".mt-dualbar", root).forEach(bar=>{
      const reward = clamp(bar.dataset.reward, 0, 100);
      const risk   = clamp(bar.dataset.risk,   0, 100);
      const hasAny = (reward > 0 || risk > 0);

      bar.classList.toggle("is-empty", !hasAny);
      bar.classList.toggle("has-data", hasAny);

      // Solo seteamos --split si hay data; si no, dejamos que el CSS ponga el estado "vacío"
      if (hasAny) {
        bar.style.setProperty("--split", reward + "%");
      } else {
        bar.style.removeProperty("--split");
      }

      const left  = $(".mt-dualbar__seg--reward", bar);
      const right = $(".mt-dualbar__seg--risk", bar);

      if (left) {
        left.style.width = hasAny ? (reward + "%") : "0%";
        left.classList.toggle("is-full", reward === 100);
      }
      if (right) {
        right.style.width = hasAny ? (risk   + "%") : "0%";
        right.classList.toggle("is-full", risk === 100);
      }
    });
  }

  /* ========= Progress genérico (si lo usas en otras partes) ========= */
  function initProgressBars(root=document){
    $$(".mt-progress-bar", root).forEach(el=>{
      const v = clamp(el.dataset.progress, 0, 100);
      el.style.setProperty("--mt-progress-value", v + "%");
    });
  }

  /* ========= Tabs + carrusel + gradientes ========= */
  function setupTabsCarousel(root){
    const viewport = $(".mt-tabs-viewport", root);
    const group    = $(".mt-toggle-group", root);
    const gradL    = $(".mt-tabs-gradient--left", root);
    const gradR    = $(".mt-tabs-gradient--right", root);
    const btnPrev  = $(".js-tabs-prev", root);
    const btnNext  = $(".js-tabs-next", root);

    if (!viewport || !group) return;

    const SCROLL_STEP = () => Math.max(120, group.clientWidth * 0.7);

    function updateGradients(){
      const max = group.scrollWidth - group.clientWidth;

      if (max <= 1) {
        // No hay overflow: ocultar ambos y deshabilitar flechas
        if (gradL) gradL.style.opacity = "0";
        if (gradR) gradR.style.opacity = "0";
        if (btnPrev) btnPrev.disabled = true;
        if (btnNext) btnNext.disabled = true;
        return;
      }

      const x = group.scrollLeft;

      if (gradL) gradL.style.opacity = (x > 1) ? "1" : "0";
      if (gradR) gradR.style.opacity = (x < max - 1) ? "1" : "0";

      if (btnPrev) btnPrev.disabled = (x <= 1);
      if (btnNext) btnNext.disabled = (x >= max - 1);
    }

    group.addEventListener("scroll", updateGradients);
    window.addEventListener("resize", updateGradients);

    btnPrev && btnPrev.addEventListener("click", () => {
      group.scrollBy({ left: -SCROLL_STEP(), behavior: "smooth" });
    });
    btnNext && btnNext.addEventListener("click", () => {
      group.scrollBy({ left: SCROLL_STEP(), behavior: "smooth" });
    });

    // Asegurar que el tab activo esté a la vista al cargar
    const activeChip = $('[data-fc-tab].active', root) || $('[data-fc-tab][aria-selected="true"]', root);
    if (activeChip) {
      activeChip.scrollIntoView({ inline: "center", block: "nearest" });
    }

    // Primer cálculo
    updateGradients();

    // Devuelve un actualizador por si necesitas llamarlo tras interacciones
    return updateGradients;
  }

  function initFeatureTabs(){
    $$('.mt-feature-tabs').forEach(tabsRoot => {
      const tablist = $('[data-fc-tabs]', tabsRoot);
      const panels  = $$('.mt-feature-panel', tabsRoot);
      const refreshGradients = setupTabsCarousel(tabsRoot);

      tablist?.addEventListener('click', (ev)=>{
        const chip = ev.target.closest('[data-fc-tab]');
        if (!chip) return;

        const id = chip.getAttribute('data-id');

        // chips
        $$('.mt-toggle-button', tablist).forEach(c=>{
          const active = (c === chip);
          c.classList.toggle('active', active);
          c.setAttribute('aria-selected', active ? 'true' : 'false');
          c.tabIndex = active ? 0 : -1;
        });

        // Llevar el chip al centro visual
        chip.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });

        // panel correspondiente
        panels.forEach(p=>{
          const match = p.getAttribute('data-panel-for') === id;
          if (match) {
            p.removeAttribute('hidden');
            // Re-init medidores dentro del panel activo
            initDonuts(p);
            initDualBars(p);
            initProgressBars(p);
          } else {
            p.setAttribute('hidden', '');
          }
        });

        // Recalcular gradientes tras el cambio
        if (typeof refreshGradients === 'function') refreshGradients();
      });
    });
  }

  function init(){
    initFeatureTabs();
    initDonuts();
    initDualBars();
    initProgressBars();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();

(function () {
  // Copiar
  document.addEventListener('click', (e) => {
    const t = e.target.closest('[data-copy]');
    if (!t) return;
    const v = t.getAttribute('data-copy') || '';
    if (!v) return;
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(v).catch(()=>{});
    } else {
      // Fallback legacy
      const ta = document.createElement('textarea');
      ta.value = v; document.body.appendChild(ta); ta.select();
      try { document.execCommand('copy'); } catch (err) {}
      document.body.removeChild(ta);
    }
  });

  // Toggle password (fix: buscar correctamente la máscara)
  document.addEventListener('click', (e) => {
    const t = e.target.closest('.js-pwd-toggle');
    if (!t) return;

    // intenta con un contenedor marcado, luego el bloque de card
    const row  = t.closest('[data-pwd-row]') || t.closest('.mt-account-data') || document;
    const mask = row.querySelector('.js-pwd-mask');
    if (!mask) return;

    const real   = t.getAttribute('data-pwd') || '';
    const hidden = mask.textContent.trim().startsWith('•') || mask.textContent.trim() === '--';

    mask.textContent = hidden ? real : '••••••••••••';
    t.setAttribute('aria-expanded', String(hidden));

    // si hay icono, alterna variante (opcional)
    const ic = t.querySelector('.mt-icon');
    if (ic) {
      ic.classList.toggle('mt-icon_visibility');
      ic.classList.toggle('mt-icon_visibility-off');
    }
  });

  // Refrescar Account Data (global)
  window.mtRefreshAccountData = function (accountId) {
    const container = document.getElementById('mt-account-data-container') || document.getElementById('mt-performance-data');
    if (!container) return;
    const url = (window.mtAccounts && mtAccounts.ajaxUrl) || '/wp-admin/admin-ajax.php';
    const nonce = (window.mtAccounts && mtAccounts.nonce) || '';

    document.querySelector('.preloader')?.classList.add('is-active');

    const body = new URLSearchParams();
    body.set('action', 'mt_accounts_data');
    body.set('nonce', nonce);
    body.set('account_id', String(accountId || 0));

    fetch(url, { method: 'POST', headers: {'Content-Type':'application/x-www-form-urlencoded'}, body })
      .then(r => r.json())
      .then(j => { if (j && j.success && j.data && j.data.html) container.innerHTML = j.data.html; })
      .finally(() => document.querySelector('.preloader')?.classList.remove('is-active'));
  };
})();

// ===== Refresh Bus (centraliza todos los componentes) =====
window.mtRefresh = (function () {
  const handlers = {};
  let inflight = 0;

  const show = () => document.querySelector('.preloader')?.classList.add('is-active');
  const hide = () => document.querySelector('.preloader')?.classList.remove('is-active');

  function track(maybePromise) {
    inflight++; show();
    const done = () => { if (--inflight <= 0) hide(); };
    if (maybePromise && typeof maybePromise.finally === 'function') {
      return maybePromise.finally(done);
    }
    // Si el handler no devuelve Promise, cerramos luego de un tick corto
    setTimeout(done, 200);
  }

  return {
    register(name, fn) { handlers[name] = fn; },
    refreshAll(id) { Object.values(handlers).forEach(fn => fn && track(fn(id))); },
  };
})();

// Dispara refresh de TODOS ante selección de cuenta
document.addEventListener('mt:accountSelected', (e) => {
  const id = e?.detail?.accountId; if (!id) return;
  window.mtRefresh.refreshAll(id);
});
