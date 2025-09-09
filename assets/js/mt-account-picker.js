(function () {
  // Espera DOM (por si el script se carga en head por error)
  function onReady(fn) {
    if (document.readyState !== 'loading') fn();
    else document.addEventListener('DOMContentLoaded', fn, { once: true });
  }

  onReady(function () {
    if (!window.MT_DATA) return;

    var DEBUG = !!window.MT_DATA.debug;
    var SEL   = window.MT_DATA.selectors || {};
    var ACCS  = Array.isArray(window.MT_DATA.accounts) ? window.MT_DATA.accounts : [];
    var selectedId = window.MT_DATA.currentId || '';

    var q  = function (s) { return (typeof s === 'string' && s) ? document.querySelector(s) : null; };
    var qa = function (root, s) { return root ? root.querySelectorAll(s) : []; };

    var grid    = q(SEL.grid || '#mt-accounts-grid');
    var btnSel  = q(SEL.select || '#select-subscription-btn');
    var elBadge = q(SEL.badge || '#mt-badge');
    var elSize  = q(SEL.size  || '#mt-size');
    var elName  = q(SEL.name  || '#mt-name');
    var modalSel= SEL.modal || '#changeSubcriptionModal';
    var CARD    = SEL.card  || '.subscription-card';
    var CHECK   = SEL.check || '.checkmark-icon';

    function log() { if (DEBUG) { try { console.debug.apply(console, ['[MT]'].concat([].slice.call(arguments))); } catch(_){} } }

    // Si no hay currentId válido, usa la primera cuenta disponible
    function ensureSelectedId() {
      if (selectedId && ACCS.some(function(a){ return a.id === selectedId; })) return;
      if (ACCS.length > 0) {
        selectedId = ACCS[0].id;
        log('selectedId fallback ->', selectedId);
      }
    }

    function titleCase(s) {
      return (s || '').toString().replace(/-/g, ' ').replace(/\b\w/g, function (m) { return m.toUpperCase(); });
    }

    function getAccountById(id) {
      for (var i = 0; i < ACCS.length; i++) if (ACCS[i].id === id) return ACCS[i];
      return null;
    }

    function markSelected() {
      if (!grid) return;
      var cards = qa(grid, CARD);
      cards.forEach(function (card) {
        var cid = card.getAttribute('data-account-id');
        var check = card.querySelector(CHECK);
        var isSel = (cid === selectedId);
        card.classList.toggle('active', isSel);
        if (check) check.style.display = isSel ? 'block' : 'none';
      });
    }

    function toggleSelectBtn() {
      if (!btnSel) return;
      if (selectedId) {
        btnSel.classList.remove('disabled');
        btnSel.removeAttribute('disabled');
      } else {
        btnSel.classList.add('disabled');
        btnSel.setAttribute('disabled', 'disabled');
      }
    }

    function closeModal() {
      var modalEl = q(modalSel);
      if (!modalEl) return;
      if (window.bootstrap && window.bootstrap.Modal) {
        var inst = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        inst.hide();
      } else {
        modalEl.classList.remove('show');
      }
    }

    // Delegación de clicks en el grid
    if (grid) {
      grid.addEventListener('click', function (e) {
        var card = e.target.closest(CARD);
        if (!card) return;
        selectedId = card.getAttribute('data-account-id') || '';
        log('clicked ->', selectedId);
        markSelected();
        toggleSelectBtn();
      });
    }

    // Botón Select
    if (btnSel) {
      btnSel.addEventListener('click', function () {
        if (!selectedId) return;
        var obj = getAccountById(selectedId);
        if (!obj) return;

        // Actualizar cabecera
        if (elBadge) {
          elBadge.className = 'badge-mega badge-mega-sm ' + (obj.badgeClass || 'badge-mega-default');
          elBadge.textContent = titleCase(obj.status || 'Active');
        }
        if (elSize) elSize.textContent = obj.size || '';
        if (elName) elName.textContent = obj.name || 'Account';

        closeModal();
      });
    }

    // Re-sincronizar cuando el modal se abre (útil si se re-renderiza algo)
    var modalEl = q(modalSel);
    if (modalEl) {
      modalEl.addEventListener('shown.bs.modal', function () {
        markSelected();
        toggleSelectBtn();
      });
    }

    // INIT
    ensureSelectedId();
    markSelected();
    toggleSelectBtn();

    log('init ok', { selectedId: selectedId, total: ACCS.length });
  });
})();
