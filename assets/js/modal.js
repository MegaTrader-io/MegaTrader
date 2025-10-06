window.MEGATRADER = window.MEGATRADER || {};

window.MEGATRADER.showModal = function (modalId) {
  const el = document.getElementById(modalId);
  if (!el) return;

  bootstrap.Modal.getOrCreateInstance(el).show();
};

function autoshow(modal){
  const autoShow = modal.getAttribute('data-autoshow');
    const modalId = modal.id;

    if (!modalId) return;

    if (autoShow === 'true') {
      window.MEGATRADER.showModal(modalId);
    } else {
      const delay = parseInt(autoShow, 10);
      if (!isNaN(delay)) {
        setTimeout(() => {
          window.MEGATRADER.showModal(modalId);
        }, delay * 1000);
      }
    }
}

function handleAutoShow(){
  const modals = document.querySelectorAll('.modal');

  modals.forEach(modal => {
    autoshow(modal);
  });
}

function handleModalWithNotice(){
  document.body.addEventListener('show.bs.modal', function (event) {
    const modal = event.target;
    if (modal.hasAttribute('data-notice')) {
      document.querySelectorAll('.woocommerce-notices-wrapper').forEach(wrapper => {
        if ( modal.contains(wrapper) ) return;

        wrapper.classList.remove('woocommerce-notices-wrapper');
        wrapper.classList.add('woocommerce-notices-wrapper-modal-open');
      });
    }
  });

  document.body.addEventListener('hidden.bs.modal', function (event) {
    document.querySelectorAll('.woocommerce-notices-wrapper-modal-open').forEach(wrapper => {
      wrapper.classList.remove('woocommerce-notices-wrapper-modal-open');
      wrapper.classList.add('woocommerce-notices-wrapper');
    });
  });
}


document.addEventListener('DOMContentLoaded', function () {
  handleModalWithNotice();
  handleAutoShow();
});



/* global bootstrap, jQuery */
(function ($, window, document) {
  'use strict';

  var MT = window.MT = window.MT || {};

  // Minimal HTML para autoinyectar si el modal no está en el DOM
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
            '<span id="mt-error-headline" class="fw-medium text-white text-3xl text-uppercase">Ups!</span>' +
            '<span id="mt-error-message" class="fw-medium text-a8a29e text-base">Ocurrió un error inesperado.</span>' +
            '<button type="button" class="mega-btn-md mega-btn-primary-md mt-4" data-bs-dismiss="modal" aria-label="Close">Close</button>' +
          '</div>' +
        '</div>' +
      '</div>' +
    '</div>';

  function ensureModalEl() {
    var el = document.getElementById('mt-error-modal');
    if (el) return el;
    // Inyecta si no existe
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
   * Muestra un modal de error unificado
   * @param {string} title - Título del modal
   * @param {string|object} message - Texto del error (se fuerza a texto plano). Si es objeto, intenta .message o .error
   * @param {object} [opts] - { iconSrc?: string, headline?: string }
   */
  MT.showErrorModal = function (title, message, opts) {
    opts = opts || {};
    var el = ensureModalEl();

    var titleEl = el.querySelector('#mt-error-title');
    var msgEl = el.querySelector('#mt-error-message');
    var headEl = el.querySelector('#mt-error-headline');
    var iconEl = el.querySelector('#mt-error-icon');

    setText(titleEl, title || 'Error');

    var msgText = '';
    if (typeof message === 'string') msgText = message;
    else if (message && typeof message === 'object') msgText = message.message || message.error || JSON.stringify(message);
    else msgText = 'Unexpected error.';
    setText(msgEl, msgText);

    // Headline corto (si no nos pasan uno, intenta derivarlo)
    setText(headEl, opts.headline || ((title && title.length <= 12) ? title : 'Ups!'));

    // Icono opcional
    if (opts.iconSrc && iconEl) {
      iconEl.setAttribute('src', opts.iconSrc);
    }

    // Mostrar (Bootstrap si existe; si no, fallback)
    try {
      if (window.bootstrap && typeof bootstrap.Modal === 'function') {
        (window.__mtErrModal || (window.__mtErrModal = new bootstrap.Modal(el, { backdrop: 'static' }))).show();
        el.removeAttribute('hidden');
        el.setAttribute('aria-hidden', 'false');
      } else {
        // Fallback sin Bootstrap
        el.classList.add('show');
        el.style.display = 'block';
        el.removeAttribute('hidden');
        el.setAttribute('aria-hidden', 'false');
        // Cierre manual
        $(el).find('[data-bs-dismiss="modal"], .mt-modal__close').one('click', function () {
          MT.hideErrorModal();
        });
      }
    } catch (e) {
      // Último recurso
      window.alert((title || 'Error') + '\n' + msgText);
    }
  };

  MT.hideErrorModal = function () {
    var el = document.getElementById('mt-error-modal');
    if (!el) return;
    try {
      if (window.bootstrap && typeof bootstrap.Modal === 'function') {
        (window.__mtErrModal || (window.__mtErrModal = new bootstrap.Modal(el))).hide();
      } else {
        el.classList.remove('show');
        el.style.display = 'none';
        el.setAttribute('hidden', '');
        el.setAttribute('aria-hidden', 'true');
      }
    } catch (e) {
      // noop
    }
  };

})(jQuery, window, document);
