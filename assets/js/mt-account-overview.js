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

  /* ========= Progress genérico ========= */
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

    const activeChip = $('[data-fc-tab].active', root) || $('[data-fc-tab][aria-selected="true"]', root);
    if (activeChip) {
      activeChip.scrollIntoView({ inline: "center", block: "nearest" });
    }

    updateGradients();
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

        $$('.mt-toggle-button', tablist).forEach(c=>{
          const active = (c === chip);
          c.classList.toggle('active', active);
          c.setAttribute('aria-selected', active ? 'true' : 'false');
          c.tabIndex = active ? 0 : -1;
        });

        chip.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });

        panels.forEach(p=>{
          const match = p.getAttribute('data-panel-for') === id;
          if (match) {
            p.removeAttribute('hidden');
            initDonuts(p);
            initDualBars(p);
            initProgressBars(p);
          } else {
            p.setAttribute('hidden', '');
          }
        });

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

/* ======= Interacciones de cuenta (copy + toggle pwd + AJAX) ======= */
(function () {
  /* --- Toast “Copied to clipboard” anclado sobre el click --- */
  const COPY_FEEDBACK_MS = 10000; // 10s
  function ensureCopyToastStyle(){
    if (window.__mtCopyToastStyle) return;
    const css = `
      #mt-copy-toast{
        position:fixed;
        left:0; top:0; /* dinámico */
        transform:translate(-50%,-100%);
        background:#1f2937;color:#fff;padding:8px 12px;border-radius:8px;
        font-size:12px;line-height:1;z-index:9999;box-shadow:0 6px 20px rgba(0,0,0,.3);
        opacity:0;transition:opacity .18s ease;pointer-events:none;
        white-space:nowrap;
      }
      #mt-copy-toast.is-visible{opacity:1}
    `;
    const style = document.createElement('style');
    style.textContent = css;
    document.head.appendChild(style);
    window.__mtCopyToastStyle = true;
  }
  function showCopyToast(text, anchorEl){
    ensureCopyToastStyle();
    let toast = document.getElementById('mt-copy-toast');
    if (!toast) {
      toast = document.createElement('div');
      toast.id = 'mt-copy-toast';
      document.body.appendChild(toast);
    }
    toast.textContent = text || 'Copied to clipboard';

    const rect = (anchorEl && anchorEl.getBoundingClientRect)
      ? anchorEl.getBoundingClientRect()
      : { left: window.innerWidth/2, top: window.innerHeight-24, width: 0 };

    const clampNum = (n, min, max) => Math.max(min, Math.min(max, n));
    const centerX = clampNum(rect.left + rect.width/2, 16, window.innerWidth - 16);
    const aboveY  = clampNum(rect.top - 8, 16, window.innerHeight - 16);

    toast.style.left = `${Math.round(centerX)}px`;
    toast.style.top  = `${Math.round(aboveY)}px`;

    toast.classList.add('is-visible');
    clearTimeout(window.__mtCopyToastTimer);
    window.__mtCopyToastTimer = setTimeout(()=>{
      toast.classList.remove('is-visible');
    }, COPY_FEEDBACK_MS);
  }

  // Copiar (con toast anclado)
  document.addEventListener('click', (e) => {
    const t = e.target.closest('[data-copy]');
    if (!t) return;
    const v = t.getAttribute('data-copy') || '';
    if (!v) return;

    const onDone = () => {
      t.classList.add('is-copied');
      showCopyToast('Copied to clipboard', t);
      setTimeout(()=> t.classList.remove('is-copied'), 1200);
    };

    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(v).then(onDone).catch(onDone);
    } else {
      const ta = document.createElement('textarea');
      ta.value = v; document.body.appendChild(ta); ta.select();
      try { document.execCommand('copy'); } catch (err) {}
      document.body.removeChild(ta);
      onDone();
    }
  });

  // Toggle password
  document.addEventListener('click', (e) => {
    const t = e.target.closest('.js-pwd-toggle');
    if (!t) return;

    const row  = t.closest('[data-pwd-row]') || t.closest('.mt-account-data') || document;
    const mask = row.querySelector('.js-pwd-mask');
    if (!mask) return;

    const real   = t.getAttribute('data-pwd') || '';
    const hidden = mask.textContent.trim().startsWith('•') || mask.textContent.trim() === '--';

    mask.textContent = hidden ? real : '••••••••••••';
    t.setAttribute('aria-expanded', String(hidden));

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

/* ===== Floating tooltips (portal to <body>) ===== */
(function () {
  if (window.__mtTooltipsBound) return;
  window.__mtTooltipsBound = true;

  // Portal y burbuja reutilizable
  const portal = document.createElement('div');
  portal.id = 'mt-tooltips-portal';
  Object.assign(portal.style, { position:'fixed', inset:'0', pointerEvents:'none', zIndex:'9999' });
  document.body.appendChild(portal);
  document.documentElement.classList.add('has-portal-tooltips');

  const bubble = document.createElement('div');
  bubble.className = 'mt-tooltip__panel is-portal';
  portal.appendChild(bubble);

  let anchor = null, hideTimer = 0;

  const clampNum = (n, min, max) => Math.max(min, Math.min(max, n));

  function positionBubble(el) {
    anchor = el;
    bubble.style.visibility = 'hidden';
    bubble.style.display = 'block';

    const r  = el.getBoundingClientRect();
    const bw = bubble.offsetWidth;
    const bh = bubble.offsetHeight;
    const vw = window.innerWidth;
    const vh = window.innerHeight;
    const pad = 8;

    // Arriba por defecto, flip abajo si no cabe
    let top = r.top - bh - pad;
    let placement = 'top';
    if (top < pad) { top = r.bottom + pad; placement = 'bottom'; }

    let left = r.left + (r.width/2) - (bw/2);
    left = clampNum(left, pad, vw - bw - pad);

    bubble.style.left = Math.round(left) + 'px';
    bubble.style.top  = Math.round(top)  + 'px';
    bubble.setAttribute('data-placement', placement);

    const arrowLeft = clampNum(r.left + r.width/2 - left, 10, bw - 10);
    bubble.style.setProperty('--arrow-left', arrowLeft + 'px');

    bubble.style.visibility = 'visible';
  }

  function showFor(target) {
    const tip = target.closest('.mt-tooltip');
    if (!tip) return;
    const panel = tip.querySelector('.mt-tooltip__panel');
    if (!panel) return;

    clearTimeout(hideTimer);
    if (anchor === tip && bubble.style.display === 'block') return;

    bubble.innerHTML = panel.innerHTML; // reutiliza tu HTML
    positionBubble(tip);
  }

  function hideSoon() {
    clearTimeout(hideTimer);
    hideTimer = setTimeout(() => {
      bubble.style.display = 'none';
      anchor = null;
    }, 120);
  }

  // Mantener visible si pasas el mouse a la burbuja
  bubble.addEventListener('mouseenter', () => clearTimeout(hideTimer));
  bubble.addEventListener('mouseleave', hideSoon);

  // Delegación: mouseover/mouseout
  document.addEventListener('mouseover', (e) => {
    const tip = e.target.closest('.mt-tooltip');
    if (!tip) return;
    showFor(e.target);
  }, true);

  document.addEventListener('mouseout', (e) => {
    const from = e.target.closest('.mt-tooltip');
    if (!from) return;
    const to = e.relatedTarget;
    if (to && (to.closest?.('.mt-tooltip') || to === bubble || bubble.contains(to))) return;
    hideSoon();
  }, true);

  // Teclado: focus/blur (icons con tabindex/role)
  document.addEventListener('focusin',  (e)=> showFor(e.target));
  document.addEventListener('focusout', hideSoon);
  document.addEventListener('keydown',  (e)=> { if (e.key === 'Escape') hideSoon(); });

  // Reposicionar en scroll/resize
  window.addEventListener('scroll', ()=> { if (anchor) positionBubble(anchor); }, true);
  window.addEventListener('resize', ()=> { if (anchor) positionBubble(anchor); });
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

/* ===== Daily Journal (AJAX + pagination) ===== */
(function(){
  const qs  = (s, r=document)=> r.querySelector(s);
  const qsa = (s, r=document)=> Array.from(r.querySelectorAll(s));

  function stateFrom(root){
    const per = parseInt(root.getAttribute('data-per-page'), 10) || 7;
    const tot = parseInt(root.getAttribute('data-total-pages'), 10) || 1;
    return { perPage: per, totalPages: tot, totalRows: 0, currentPage: 1 };
  }

  function updatePagerUI(root, st){
    const curEl   = qs('.mt-dj-current', root);
    const totEl   = qs('.mt-dj-total', root);
    const btnPrev = qs('.mt-dj-prev', root);
    const btnNext = qs('.mt-dj-next', root);
    const countEl = qs('#mt-dj-count', root);

    curEl && (curEl.textContent = String(st.currentPage));
    totEl && (totEl.textContent = String(st.totalPages));

    btnPrev && (btnPrev.disabled = st.currentPage <= 1);
    btnNext && (btnNext.disabled = st.currentPage >= st.totalPages);

    const start = (st.currentPage - 1) * st.perPage + 1;
    const end   = Math.min(start + st.perPage - 1, st.totalRows);
    const a = st.totalRows ? start : 0;
    const b = st.totalRows ? end   : 0;
    countEl && (countEl.innerHTML = `Showing <strong>${a}–${b}</strong> of <strong>${st.totalRows}</strong>`);
  }

  function applyPageVisibility(root, st){
    const tbody = qs('#mt-daily-journal-table tbody', root);
    if (!tbody) return;
    qsa('tr[data-page]', tbody).forEach(tr => {
      const p = parseInt(tr.getAttribute('data-page'), 10) || 1;
      tr.style.display = (p === st.currentPage) ? '' : 'none';
    });
  }

  function goTo(root, st, page){
    st.currentPage = Math.max(1, Math.min(st.totalPages, page));
    applyPageVisibility(root, st);
    updatePagerUI(root, st);
  }

  function fetchDailyJournal(root, accountId){
    const acctEl = qs('#mt-daily-journal-account', root);
    acctEl && (acctEl.textContent = accountId || '—');

    const url   = (window.mtAccounts && mtAccounts.ajaxUrl) || '/wp-admin/admin-ajax.php';
    const nonce = (window.mtAccounts && mtAccounts.nonce)   || '';

    const body = new URLSearchParams();
    body.set('action', 'mt_account_daily_journal');
    body.set('nonce',  nonce);
    body.set('account_id', String(accountId || ''));

    const tbody = qs('#mt-daily-journal-table tbody', root);
    return fetch(url, {
      method: 'POST',
      headers: {'Content-Type':'application/x-www-form-urlencoded'},
      body
    })
    .then(r=>r.json())
    .then(j=>{
      if (!j?.success || !j?.data) return;
      const { rowsHtml, total, per_page, total_pages } = j.data;
      if (tbody) tbody.innerHTML = rowsHtml || '';

      root.setAttribute('data-per-page', String(per_page || 7));
      root.setAttribute('data-total-pages', String(total_pages || 1));

      const st = stateFrom(root);
      st.totalRows  = parseInt(total, 10) || 0;
      st.totalPages = parseInt(total_pages, 10) || 1;
      st.currentPage= 1;

      applyPageVisibility(root, st);
      updatePagerUI(root, st);

      // guardar en dataset para navegares subsiguientes si hace falta
      root.__djState = st;
    });
  }

  function bindDailyJournal(root){
    if (!root || root.__djBound) return;
    root.__djBound = true;

    const st = root.__djState || stateFrom(root);
    const btnPrev = qs('.mt-dj-prev', root);
    const btnNext = qs('.mt-dj-next', root);

    btnPrev && btnPrev.addEventListener('click', ()=> goTo(root, st, st.currentPage - 1));
    btnNext && btnNext.addEventListener('click', ()=> goTo(root, st, st.currentPage + 1));

    // primera UI (vacía)
    updatePagerUI(root, st);
  }

  function getRoot(){
    return document.getElementById('mt-daily-journal');
  }

  // Bind al cargar
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => bindDailyJournal(getRoot()));
  } else {
    bindDailyJournal(getRoot());
  }

  // Registrar en el bus global
  if (window.mtRefresh && typeof window.mtRefresh.register === 'function') {
    window.mtRefresh.register('dailyJournal', function(accountId){
      const root = getRoot();
      if (!root) return;
      bindDailyJournal(root);
      return fetchDailyJournal(root, accountId);
    });
  } else {
    // si el bus aún no existe, registra cuando esté listo
    document.addEventListener('DOMContentLoaded', () => {
      if (window.mtRefresh && typeof window.mtRefresh.register === 'function') {
        window.mtRefresh.register('dailyJournal', function(accountId){
          const root = getRoot();
          if (!root) return;
          bindDailyJournal(root);
          return fetchDailyJournal(root, accountId);
        });
      }
    });
  }
})();
