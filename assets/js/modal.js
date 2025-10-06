/* modal.js — unified error modal + conditional blocking */
window.MEGATRADER = window.MEGATRADER || {};

/* =================== Config =================== */
/** Only block these modals WHEN the error modal is visible */
const MT_BLOCK_WHEN_ERROR = new Set(['mt-breach-alert-modal', 'mt-agreement-modal']);

/* Flag toggled while the error modal is shown */
window.__mtErrorVisible = !!window.__mtErrorVisible;

/* =================== Helpers =================== */
function hideModalEl(el){
  try {
    if (window.bootstrap && typeof bootstrap.Modal === 'function') {
      bootstrap.Modal.getOrCreateInstance(el).hide();
    }
  } catch(_) {}
  el.classList.remove('show');
  el.style.display = 'none';
  el.setAttribute('hidden','');
  el.setAttribute('aria-hidden','true');
  el.removeAttribute('aria-modal');
  el.removeAttribute('role');
}

function purgeBackdrops(){
  document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
  document.body.classList.remove('modal-open');
  document.body.style.removeProperty('padding-right');
}

function closeModalsByIds(idSet){
  idSet.forEach(id => {
    const el = document.getElementById(id);
    if (el) hideModalEl(el);
  });
}

function closeAllModals(exceptId){
  document.querySelectorAll('.modal').forEach(m => {
    if (exceptId && m.id === exceptId) return;
    hideModalEl(m);
  });
  purgeBackdrops();
}

/* =================== Core modal API =================== */
window.MEGATRADER.showModal = function (modalId) {
  const el = document.getElementById(modalId);
  if (!el) return;

  // Only block targeted modals while the error modal is visible
  if (window.__mtErrorVisible && MT_BLOCK_WHEN_ERROR.has(modalId)) return;

  // If error modal is not active, allow normal flow
  if (window.bootstrap && typeof bootstrap.Modal === 'function') {
    bootstrap.Modal.getOrCreateInstance(el).show();
  } else {
    // Fallback show
    el.classList.add('show');
    el.style.display = 'block';
    el.removeAttribute('hidden');
    el.setAttribute('aria-hidden', 'false');
    // add a backdrop if needed
    if (!document.querySelector('.modal-backdrop.show')) {
      const bd = document.createElement('div');
      bd.className = 'modal-backdrop fade show';
      document.body.appendChild(bd);
      document.body.classList.add('modal-open');
    }
  }
};

/* Autoshow support via data-autoshow */
function autoshow(modal){
  const autoShow = modal.getAttribute('data-autoshow');
  const modalId  = modal.id;
  if (!modalId) return;

  const tryShow = () => {
    if (window.__mtErrorVisible && MT_BLOCK_WHEN_ERROR.has(modalId)) return;
    window.MEGATRADER.showModal(modalId);
  };

  if (autoShow === 'true') tryShow();
  else {
    const delay = parseInt(autoShow, 10);
    if (!isNaN(delay)) setTimeout(tryShow, delay * 1000);
  }
}

function handleAutoShow(){
  document.querySelectorAll('.modal').forEach(autoshow);
}

function handleModalWithNotice(){
  document.body.addEventListener('show.bs.modal', function (event) {
    const modal = event.target;
    if (modal && modal.hasAttribute('data-notice')) {
      document.querySelectorAll('.woocommerce-notices-wrapper').forEach(wrapper => {
        if (modal.contains(wrapper)) return;
        wrapper.classList.remove('woocommerce-notices-wrapper');
        wrapper.classList.add('woocommerce-notices-wrapper-modal-open');
      });
    }
  });

  document.body.addEventListener('hidden.bs.modal', function () {
    document.querySelectorAll('.woocommerce-notices-wrapper-modal-open').forEach(wrapper => {
      wrapper.classList.remove('woocommerce-notices-wrapper-modal-open');
      wrapper.classList.add('woocommerce-notices-wrapper');
    });
  });
}

/* Block by Bootstrap event ONLY when error modal is visible */
document.body.addEventListener('show.bs.modal', function(ev){
  const id = ev.target?.id || '';
  if (window.__mtErrorVisible && MT_BLOCK_WHEN_ERROR.has(id)) {
    ev.preventDefault();
    hideModalEl(ev.target);
  }
}, true);

/* Enforcer: only act when error modal is visible */
(function setupModalEnforcer(){
  const obs = new MutationObserver(muts => {
    if (!window.__mtErrorVisible) return;
    for (const m of muts) {
      if (m.type === 'attributes' && m.target?.classList?.contains('modal')) {
        const id = m.target.id || '';
        if (MT_BLOCK_WHEN_ERROR.has(id) && m.target.classList.contains('show')) {
          hideModalEl(m.target);
        }
      }
      if (m.addedNodes) {
        m.addedNodes.forEach(n => {
          if (n.nodeType !== 1) return;
          if (n.matches?.('.modal.show') && MT_BLOCK_WHEN_ERROR.has(n.id || '')) hideModalEl(n);
          n.querySelectorAll?.('.modal.show').forEach(el => {
            if (MT_BLOCK_WHEN_ERROR.has(el.id || '')) hideModalEl(el);
          });
        });
      }
    }
  });
  obs.observe(document.body, {
    attributes: true,
    attributeFilter: ['class','style','aria-hidden'],
    childList: true,
    subtree: true
  });
})();

/* Boot */
document.addEventListener('DOMContentLoaded', function () {
  handleModalWithNotice();
  handleAutoShow();
});

/* =================== Error modal =================== */
/* global bootstrap, jQuery */
(function ($, window, document) {
  'use strict';

  var MT = (window.MT = window.MT || {});

  // Auto-injected error modal (English defaults)
  var AUTOINJECT_HTML =
    '<div id="mt-error-modal" class="modal modal-subcription fade" tabindex="-1" aria-labelledby="mt-error-title" aria-hidden="true" hidden>' +
      '<div class="modal-dialog modal-dialog-centered modal-sm">' +
        '<div class="modal-content gap-32">' +
          '<div class="modal-header w-100 border-0 justify-content-between align-items-center p-0">' +
            '<span id="mt-error-title" class="modal-title text-white heading-sm-medium">ERROR</span>' +
            '<button type="button" class="p-0 border-0 bg-transparent shadow-none mt-modal__close" data-bs-dismiss="modal" aria-label="Close">' +
              '<span aria-hidden="true"><img src="/wp-content/uploads/2025/05/cancel-circle-1.png" alt="Close" style="width:24px;height:24px;"></span>' +
            '</button>' +
          '</div>' +
          '<div class="modal-body d-flex flex-column align-items-center text-center gap-2">' +
            '<div aria-hidden="true"><div class="modal-body-image modal-image-warning">' +
              '<img id="mt-error-icon" decoding="async" src="/wp-content/uploads/2025/07/warning.svg" alt="Warning icon">' +
            '</div></div>' +
            '<span id="mt-error-headline" class="fw-medium text-white text-3xl text-uppercase">Oops!</span>' +
            '<span id="mt-error-message" class="fw-medium text-a8a29e text-base">An unexpected error occurred.</span>' +
            '<button type="button" class="mega-btn-md mega-btn-primary-md mt-4" data-bs-dismiss="modal" aria-label="Close">Close</button>' +
          '</div>' +
        '</div>' +
      '</div>' +
    '</div>';

  function ensureModalEl() {
    var el = document.getElementById('mt-error-modal');
    if (el) return el;
    var wrap = document.createElement('div');
    wrap.innerHTML = AUTOINJECT_HTML;
    var node = wrap.firstElementChild;
    document.body.appendChild(node);
    return node;
  }

  function setText(node, text) {
    if (!node) return;
    node.textContent = (text == null) ? '' : String(text);
  }

  /**
   * Unified error modal
   * @param {string}  title
   * @param {string|object} message
   * @param {object} [opts]   { iconSrc?: string, headline?: string }
   */
  MT.showErrorModal = function (title, message, opts) {
    opts = opts || {};
    var el = ensureModalEl();

    var titleEl = el.querySelector('#mt-error-title');
    var msgEl   = el.querySelector('#mt-error-message');
    var headEl  = el.querySelector('#mt-error-headline');
    var iconEl  = el.querySelector('#mt-error-icon');

    setText(titleEl, title || 'Error');

    var msgText = '';
    if (typeof message === 'string') msgText = message;
    else if (message && typeof message === 'object') msgText = message.message || message.error || JSON.stringify(message);
    else msgText = 'Unexpected error.';
    setText(msgEl, msgText);

    setText(headEl, opts.headline || ((title && title.length <= 12) ? title : 'Oops!'));
    if (opts.iconSrc && iconEl) iconEl.setAttribute('src', opts.iconSrc);

    // Error is now visible → block targeted modals
    window.__mtErrorVisible = true;

    // Make sure any targeted modals aren’t left open behind
    closeModalsByIds(MT_BLOCK_WHEN_ERROR);

    try {
      if (window.bootstrap && typeof bootstrap.Modal === 'function') {
        (window.__mtErrModal || (window.__mtErrModal = new bootstrap.Modal(el, { backdrop: 'static' }))).show();
        el.removeAttribute('hidden');
        el.setAttribute('aria-hidden', 'false');

        // When error modal hides, lift the block
        el.addEventListener('hidden.bs.modal', function onHidden(){
          el.removeEventListener('hidden.bs.modal', onHidden);
          window.__mtErrorVisible = false;
          purgeBackdrops();
        });
      } else {
        // Fallback show
        el.classList.add('show');
        el.style.display = 'block';
        el.removeAttribute('hidden');
        el.setAttribute('aria-hidden', 'false');

        // Basic backdrop
        if (!document.querySelector('.modal-backdrop.show')) {
          const bd = document.createElement('div');
          bd.className = 'modal-backdrop fade show';
          document.body.appendChild(bd);
          document.body.classList.add('modal-open');
        }

        $(el).find('[data-bs-dismiss="modal"], .mt-modal__close').one('click', function () {
          MT.hideErrorModal();
        });
      }
    } catch (e) {
      window.alert((title || 'Error') + '\n' + msgText);
    }
  };

  // Global alias
  window.MEGATRADER = window.MEGATRADER || {};
  window.MEGATRADER.showError = function (title, message, opts) {
    if (window.MT && typeof window.MT.showErrorModal === 'function') {
      return window.MT.showErrorModal(title, message, opts || {});
    }
    console.error('[MT][Error]', title, message);
    try {
      alert((title || 'Error') + '\n\n' + (message?.message || message?.error || message));
    } catch(_) {}
  };

  // Global JSON fetch with diagnostics
  window.MEGATRADER.fetchJSON = function(url, options){
    return fetch(url, options).then(function(r){
      return r.text().then(function(txt){
        var ct = (r.headers.get('content-type') || '').toLowerCase();
        var looksJSON = ct.indexOf('application/json') > -1;
        var data = null;
        if (looksJSON) { try { data = JSON.parse(txt); } catch(e){} }

        function extractLabel(html){
          if (!html) return '';
          var m = html.match(/<title[^>]*>([^<]*)<\/title>/i);
          if (m && m[1]) return m[1].trim();
          var h = html.match(/<h1[^>]*>([^<]*)<\/h1>/i);
          if (h && h[1]) return h[1].trim();
          if (/not\s+found|404/i.test(html)) return 'Not Found';
          if (/forbidden|denied|401|403/i.test(html)) return 'Forbidden/Unauthorized';
          if (/fatal\s+error|exception|stack/i.test(html)) return 'Server Error';
          return 'HTML response (not JSON)';
        }

        var actionName =
          options && options.body && typeof options.body.get === 'function'
            ? (options.body.get('action') || '') : '';

        if (!r.ok) {
          var label = extractLabel(txt);
          throw new Error(
            'HTTP ' + r.status + ' ' + (r.statusText || '') +
            '\nURL: ' + url +
            (actionName ? '\nAction: ' + actionName : '') +
            (label ? '\nServer says: ' + label : '')
          );
        }

        if (!data) {
          var label2 = extractLabel(txt);
          throw new Error(
            'Non-JSON server response.' +
            '\nURL: ' + url +
            (actionName ? '\nAction: ' + actionName : '') +
            (label2 ? '\nServer says: ' + label2 : '')
          );
        }

        return data;
      });
    });
  };

  MT.hideErrorModal = function () {
    var el = document.getElementById('mt-error-modal');
    if (!el) { window.__mtErrorVisible = false; return; }
    try {
      if (window.bootstrap && typeof bootstrap.Modal === 'function') {
        (window.__mtErrModal || (window.__mtErrModal = new bootstrap.Modal(el))).hide();
      } else {
        hideModalEl(el);
      }
    } catch (e) {
      // noop
    } finally {
      window.__mtErrorVisible = false; // lift the block
      purgeBackdrops();
    }
  };
})(jQuery, window, document);
