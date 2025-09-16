/* mt-account-overview.js */
(function () {
  // ===== Utilidades DOM =====
  const $  = (sel, root=document) => root.querySelector(sel);
  const $$ = (sel, root=document) => Array.from(root.querySelectorAll(sel));
  const clamp = (n, min, max) => Math.max(min, Math.min(max, parseInt(n, 10) || 0));

  // ===== Donuts (si los usas en performance) =====
  function initDonuts(root=document){
    $$(".mt-donut", root).forEach(d=>{
      const v = clamp(d.dataset.donutValue, 0, 100);
      d.style.setProperty("--mt-donut-value", v);
      d.classList.toggle("is-empty", v === 0);
      const t = $(".mt-donut__percent", d);
      if (t) t.textContent = v ? (v + "%") : "--";
    });
  }

  // ===== Barras divididas Reward/Risk (si las usas) =====
  function initSplitBars(root=document){
    $$(".mt-dualbar", root).forEach(el=>{
      const reward = clamp(el.dataset.reward, 0, 100);
      const risk   = clamp(el.dataset.risk,   0, 100);
      const hasAny = (reward + risk) > 0;

      el.style.setProperty("--split", reward + "%");
      el.classList.toggle("is-empty", !hasAny);

      const left  = el.querySelector(".mt-dualbar__left");
      const right = el.querySelector(".mt-dualbar__right");
      if (left && right) {
        left.style.width  = hasAny ? (reward + "%") : "0%";
        right.style.width = hasAny ? (risk   + "%") : "0%";
        right.classList.toggle("is-full", risk === 100);
      }
    });
  }

  // ===== Progress genérico =====
  function initProgressBars(root=document){
    $$(".mt-progress-bar", root).forEach(el=>{
      const v = clamp(el.dataset.progress, 0, 100);
      el.style.setProperty("--mt-progress-value", v + "%");
    });
  }

  // ===== Interacciones account-data (copiar / ver ocultar) =====
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-copy]');
    if (!btn) return;
    const val = btn.getAttribute('data-copy') || '';
    if (!val) return;
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(val).catch(()=>{});
    }
  });

  document.addEventListener('click', (e) => {
    const t = e.target.closest('.js-pwd-toggle');
    if (!t) return;
    const wrap = t.closest('.d-flex') || t.parentElement;
    const mask = wrap && wrap.querySelector('.js-pwd-mask');
    const real = t.getAttribute('data-pwd') || '';
    if (!mask) return;
    const hidden = mask.textContent.trim().startsWith('•') || mask.textContent.trim() === '--';
    mask.textContent = hidden ? real : '••••••••••••';
    t.setAttribute('aria-expanded', String(hidden));
    t.textContent = hidden ? 'Hide' : 'Show';
  });

  // ===== Bus central de refrescos (maneja el preloader) =====
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

  // ===== AJAX helpers =====
  function getAjaxUrl() {
    return (window.mtAccounts && mtAccounts.ajaxUrl) || '/wp-admin/admin-ajax.php';
  }
  function getNonce() {
    return (window.mtAccounts && mtAccounts.nonce) || '';
  }

  // ===== Account Data: refresco por AJAX (PROMISE) =====
  window.mtRefreshAccountData = function (accountId) {
    const container =
      document.getElementById('mt-account-data-container') ||
      document.getElementById('mt-performance-data'); // fallback por naming antiguo
    if (!container) return Promise.resolve();

    const body = new URLSearchParams();
    body.set('action', 'mt_accounts_data');
    body.set('nonce', getNonce());
    body.set('account_id', String(accountId || 0));

    return fetch(getAjaxUrl(), {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body
    })
    .then(r => {
      const ct = r.headers.get('content-type') || '';
      if (ct.includes('application/json')) return r.json();
      return r.text().then(html => ({ success: true, data: { html } }));
    })
    .then(resp => {
      const html = resp?.data?.html || resp?.html || '';
      if (html) {
        container.innerHTML = html;
      }
      // reinit visuales por si el template trae componentes
      initDonuts(container);
      initSplitBars(container);
      initProgressBars(container);
    });
  };

  // ===== Performance: si YA existe, NO lo tocamos; si no, definimos wrapper (PROMISE) =====
  if (typeof window.mtRefreshAccountPerformance !== 'function') {
    window.mtRefreshAccountPerformance = function (accountId) {
      const container = document.getElementById('mt-performance-container');
      if (!container) return Promise.resolve();

      const body = new URLSearchParams();
      body.set('action', 'mt_accounts_performance'); // coincide con tu AJAX actual
      body.set('nonce', getNonce());
      body.set('account_id', String(accountId || 0));

      return fetch(getAjaxUrl(), {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body
      })
      .then(r => {
        const ct = r.headers.get('content-type') || '';
        if (ct.includes('application/json')) return r.json();
        return r.text().then(html => ({ success: true, data: { html } }));
      })
      .then(resp => {
        const html = resp?.data?.html || resp?.html || '';
        if (html) {
          container.innerHTML = html;
          // reinit visuales en performance
          initDonuts(container);
          initSplitBars(container);
          initProgressBars(container);
        }
      });
    };
  }

  // ===== Registro en el bus (no rompe nada si alguno no existe) =====
  if (window.mtRefresh && window.mtRefresh.register) {
    if (typeof window.mtRefreshAccountPerformance === 'function') {
      window.mtRefresh.register('performance', window.mtRefreshAccountPerformance);
    }
    window.mtRefresh.register('accountData', window.mtRefreshAccountData);
  }

  // ===== Disparo global al cambiar cuenta =====
  document.addEventListener('mt:accountSelected', (e) => {
    const id = e && e.detail && e.detail.accountId;
    if (!id) return;
    window.mtRefresh.refreshAll(id);
  });

  // ===== Init visual al cargar (por si hay contenido server-side ya pintado) =====
  document.addEventListener('DOMContentLoaded', () => {
    initDonuts(document);
    initSplitBars(document);
    initProgressBars(document);
  });
})();
