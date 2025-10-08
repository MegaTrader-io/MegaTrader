/* mt-account-picker.js — selección de cuentas + AJAX + preloader robusto */
(function () {
  'use strict';

  var CFG = (window.MT_DATA || {});
  var sel = (CFG.selectors || {});
  var grid = document.querySelector(sel.grid);
  var selectBtn = document.querySelector(sel.select);
  var perf = document.querySelector(sel.performance) || document.querySelector('.mt-account-performance');
  var modalEl = document.querySelector(sel.modal);

  // ================ PRELOADER =================
  function injectFallbackCSS() {
    if (document.getElementById('mt-fallback-loader-style')) return;
    var css = document.createElement('style');
    css.id = 'mt-fallback-loader-style';
    css.textContent =
      '@keyframes mtSpin{to{transform:rotate(360deg)}}' +
      '#mt-fallback-loader{position:fixed;inset:0;display:none;align-items:center;justify-content:center;z-index:200000;background:rgba(0,0,0,.35)}' +
      '#mt-fallback-loader .mt-spinner{width:48px;height:48px;border-radius:50%;border:4px solid #fff;border-top-color:transparent;animation:mtSpin 1s linear infinite}';
    document.head.appendChild(css);
  }
  function ensureFallbackOverlay() {
    if (document.getElementById('mt-fallback-loader')) return;
    injectFallbackCSS();
    var el = document.createElement('div');
    el.id = 'mt-fallback-loader';
    el.innerHTML = '<div class="mt-spinner" role="status" aria-label="Loading"></div>';
    document.body.appendChild(el);
  }
  function fallbackOn()  { ensureFallbackOverlay(); var el = document.getElementById('mt-fallback-loader'); if (el) el.style.display = 'flex'; document.documentElement.classList.add('mt-busy'); if (selectBtn) selectBtn.classList.add('is-loading'); }
  function fallbackOff() { var el = document.getElementById('mt-fallback-loader'); if (el) el.style.display = 'none'; document.documentElement.classList.remove('mt-busy'); if (selectBtn) selectBtn.classList.remove('is-loading'); }

  function fireLoadingEvents(on) {
    try { document.dispatchEvent(new CustomEvent('mt:loading', { detail: { on: !!on } })); } catch (_) {}
    try { window.dispatchEvent(new CustomEvent('mt:loading', { detail: { on: !!on } })); } catch (_) {}
    // aliases
    try { document.dispatchEvent(new Event(on ? 'loading:start' : 'loading:stop')); } catch (_) {}
    try { window.dispatchEvent(new Event(on ? 'loading:start' : 'loading:stop')); } catch (_) {}
    if (window.jQuery) {
      try { window.jQuery(document).trigger('mt:loading', [{ on: !!on }]); } catch (_) {}
      try { window.jQuery(document).trigger(on ? 'loading:start' : 'loading:stop'); } catch (_) {}
    }
  }

  var Loader = {
    start: function (p) {
      // Si existe una API "withLoader(promise)" aprovechémosla
      try {
        if (window.MEGATRADER && typeof MEGATRADER.withLoader === 'function' && p && typeof p.finally === 'function') {
          return MEGATRADER.withLoader(p);
        }
      } catch (_) {}

      // Intentos directos
      try {
        if (window.MEGATRADER) {
          if (typeof MEGATRADER.showLoader === 'function') return MEGATRADER.showLoader();
          if (typeof MEGATRADER.showPreloader === 'function') return MEGATRADER.showPreloader();
          if (typeof MEGATRADER.loading === 'function') return MEGATRADER.loading(true);
          if (MEGATRADER.loader && typeof MEGATRADER.loader.show === 'function') return MEGATRADER.loader.show();
          if (MEGATRADER.preloader && typeof MEGATRADER.preloader.show === 'function') return MEGATRADER.preloader.show();
          if (typeof MEGATRADER.togglePreloader === 'function') return MEGATRADER.togglePreloader(true);
        }
      } catch (_) {}

      // Libs comunes
      try { if (window.NProgress && typeof NProgress.start === 'function') return NProgress.start(); } catch (_) {}

      // Eventos + fallback visual
      fireLoadingEvents(true);
      fallbackOn();
    },
    stop: function () {
      try {
        if (window.MEGATRADER) {
          if (typeof MEGATRADER.hideLoader === 'function') return MEGATRADER.hideLoader();
          if (typeof MEGATRADER.hidePreloader === 'function') return MEGATRADER.hidePreloader();
          if (typeof MEGATRADER.loading === 'function') return MEGATRADER.loading(false);
          if (MEGATRADER.loader && typeof MEGATRADER.loader.hide === 'function') return MEGATRADER.loader.hide();
          if (MEGATRADER.preloader && typeof MEGATRADER.preloader.hide === 'function') return MEGATRADER.preloader.hide();
          if (typeof MEGATRADER.togglePreloader === 'function') return MEGATRADER.togglePreloader(false);
        }
      } catch (_) {}

      try { if (window.NProgress && typeof NProgress.done === 'function') return NProgress.done(); } catch (_) {}

      fireLoadingEvents(false);
      fallbackOff();
    }
  };
  // ============== FIN PRELOADER ==============

  // Estado selección
  var selectedId = CFG.currentId || null;

  // Util
  function $(root, q) { return (root || document).querySelector(q); }
  function $all(root, q) { return Array.prototype.slice.call((root || document).querySelectorAll(q)); }
  function enableSelect(on) {
    if (!selectBtn) return;
    selectBtn.disabled = !on;
    selectBtn.classList.toggle('disabled', !on);
  }

  function setActiveCard(card) {
    if (!card || card.classList.contains('d-none')) return;
    $all(grid, sel.card + '.' + CFG.selectionClass).forEach(function (el) {
      el.classList.remove(CFG.selectionClass);
      var chk = $(el, sel.check);
      if (chk) chk.style.display = 'none';
    });
    card.classList.add(CFG.selectionClass);
    var check = $(card, sel.check);
    if (check) check.style.display = '';
    selectedId = card.getAttribute('data-account-id') || null;

    window.mtAccounts = window.mtAccounts || {};
    window.mtAccounts.selectedId = selectedId;

    enableSelect(true);
  }

  function preselectIfVisible() {
    if (!selectedId) { enableSelect(false); return; }
    var cur = grid && grid.querySelector(sel.card + '[data-account-id="' + CSS.escape(selectedId) + '"]');
    if (cur && !cur.classList.contains('d-none')) setActiveCard(cur);
    else enableSelect(false);
  }

  function updateHeaderFromCard(card) {
    if (!card) return;
    var sizeVal = card.getAttribute('data-size') || '';
    var nameVal = card.getAttribute('data-name') || 'Account';
    var logoVal = card.getAttribute('data-logo') || '';

    var sizeEl = document.querySelector(sel.size);
    var nameEl = document.querySelector(sel.name);
    var logoEl = document.querySelector(sel.platformLogo);
    var badgeEl = document.querySelector(sel.badge);

    if (sizeEl) sizeEl.textContent = sizeVal;
    if (nameEl) nameEl.textContent = nameVal;
    if (logoEl && logoVal) logoEl.src = logoVal;

    if (badgeEl) {
      var status = (card.getAttribute('data-status') || '').toLowerCase();
      badgeEl.textContent = status
        ? status.replace(/-/g, ' ').replace(/\b\w/g, function (m) { return m.toUpperCase(); })
        : 'NoStatusDefine';
    }
  }

  function hideModal() {
    if (!modalEl || !window.bootstrap) return;
    var inst = window.bootstrap.Modal.getInstance(modalEl) || new window.bootstrap.Modal(modalEl);
    inst.hide();
  }

  // Click en grid
  if (grid) {
    grid.addEventListener('click', function (e) {
      var card = e.target.closest(sel.card);
      if (!card || card.classList.contains('d-none')) return;
      setActiveCard(card);
    });
  }

  // Click en "Select"
  if (selectBtn) {
    selectBtn.addEventListener('click', function () {
      if (!selectedId) {
        if (window.MEGATRADER && typeof MEGATRADER.showError === 'function') {
          MEGATRADER.showError('Select an account', 'Please choose an account to continue.', {});
        } else {
          console.error('[MT] No account selected');
        }
        return;
      }

      var fd = new FormData();
      fd.append('action', CFG.ajax.action);
      fd.append('nonce', CFG.ajax.nonce);
      fd.append('accountId', selectedId);

      enableSelect(false);

      var url = (CFG.ajax && CFG.ajax.url) || '';
      var fetcher = (window.MEGATRADER && typeof MEGATRADER.fetchJSON === 'function')
        ? window.MEGATRADER.fetchJSON
        : function (u, opts) { return fetch(u, opts).then(function (r) { return r.json(); }); };

      var req = fetcher(url, { method: 'POST', body: fd, credentials: 'same-origin' });

      // Arranca loader (si la app tiene withLoader, lo usará; si no, fallback)
      try { Loader.start(req); } catch (_) { Loader.start(); }

      req.then(function (res) {
        if (!res || !res.success) throw new Error(res && res.data && res.data.message || 'AJAX failed');

        if (window.mtTooltips?.closeAll) window.mtTooltips.closeAll();
        if (perf && res.data && typeof res.data.html === 'string') {
          perf.innerHTML = res.data.html || '';
          if (window.mtTooltips?.refresh) window.mtTooltips.refresh(perf);
        }

        hideModal();

        var active = grid && grid.querySelector(sel.card + '.' + CFG.selectionClass);
        if (active) {
          updateHeaderFromCard(active);
          var orderId = parseInt(active.getAttribute('data-order') || '0', 10) || 0;
          var root = document.getElementById('mt-account-overview');
          if (root) root.setAttribute('data-order-id', orderId ? String(orderId) : '');

          document.dispatchEvent(new CustomEvent('mt:accountSelected', {
            detail: { accountId: selectedId, id: selectedId, order: orderId, orderId: orderId }
          }));
        }

        if (typeof window.passedGuardCheck === 'function') window.passedGuardCheck(selectedId);
        if (typeof window.breachGuardCheck === 'function') window.breachGuardCheck(selectedId);
      })
      .catch(function (err) {
        if (window.MEGATRADER && typeof MEGATRADER.showError === 'function') {
          MEGATRADER.showError('Account Performance Error', err && (err.message || err), { headline: 'Oops!' });
        } else {
          console.error('[MT][Account Performance Error]', err);
        }
      })
      .finally(function () {
        try { Loader.stop(); } catch (_) {}
        enableSelect(true);
      });
    });
  }

  if (grid) preselectIfVisible();
})();
