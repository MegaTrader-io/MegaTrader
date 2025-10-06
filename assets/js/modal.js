window.MEGATRADER = window.MEGATRADER || {};

// === Helpers to close modals/backdrops ===
function hideModalEl(el){
  try {
    if (window.bootstrap && typeof bootstrap.Modal === 'function') {
      (bootstrap.Modal.getOrCreateInstance(el)).hide();
    }
  } catch(_) {}
  // Force hide even if no BS instance
  el.classList.remove('show');
  el.style.display = 'none';
  el.setAttribute('hidden', '');
  el.setAttribute('aria-hidden', 'true');
  el.removeAttribute('aria-modal');
  el.removeAttribute('role');
}

function purgeBackdrops(){
  document.querySelectorAll('.modal-backdrop').forEach(function(b){ b.remove(); });
  document.body.classList.remove('modal-open');
  document.body.style.removeProperty('padding-right');
}

function closeAllModals(exceptId){
  document.querySelectorAll('.modal.show, .modal[aria-hidden="false"]').forEach(function(m){
    if (exceptId && m.id === exceptId) return;
    hideModalEl(m);
  });
  purgeBackdrops();
}

// === Show any modal (respects error exclusivity) ===
window.MEGATRADER.showModal = function (modalId) {
  const el = document.getElementById(modalId);
  if (!el) return;

  // Block opening others while error modal is visible
  if (window.__mtErrorVisible && modalId !== 'mt-error-modal') return;

  closeAllModals(modalId);
  purgeBackdrops();

  bootstrap.Modal.getOrCreateInstance(el).show();
};

function autoshow(modal) {
  const autoShow = modal.getAttribute("data-autoshow");
  const modalId = modal.id;
  if (!modalId) return;

  const tryShow = () => {
    if (window.__mtErrorVisible && modalId !== 'mt-error-modal') {
      setTimeout(() => autoshow(modal), 1000);
      return;
    }
    window.MEGATRADER.showModal(modalId);
  };

  if (autoShow === "true") {
    tryShow();
  } else {
    const delay = parseInt(autoShow, 10);
    if (!isNaN(delay)) setTimeout(tryShow, delay * 1000);
  }
}

function handleAutoShow() {
  const modals = document.querySelectorAll(".modal");
  modals.forEach((modal) => { autoshow(modal); });
}

function handleModalWithNotice() {
  document.body.addEventListener("show.bs.modal", function (event) {
    const modal = event.target;
    if (modal.hasAttribute("data-notice")) {
      document.querySelectorAll(".woocommerce-notices-wrapper").forEach((wrapper) => {
        if (modal.contains(wrapper)) return;
        wrapper.classList.remove("woocommerce-notices-wrapper");
        wrapper.classList.add("woocommerce-notices-wrapper-modal-open");
      });
    }
  });

  document.body.addEventListener("hidden.bs.modal", function () {
    document.querySelectorAll(".woocommerce-notices-wrapper-modal-open").forEach((wrapper) => {
      wrapper.classList.remove("woocommerce-notices-wrapper-modal-open");
      wrapper.classList.add("woocommerce-notices-wrapper");
    });
  });
}

document.addEventListener("DOMContentLoaded", function () {
  handleModalWithNotice();
  handleAutoShow();
});

/* global bootstrap, jQuery */
(function ($, window, document) {
  "use strict";

  var MT = (window.MT = window.MT || {});

  // Auto-injected error modal HTML (English)
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
    var el = document.getElementById("mt-error-modal");
    if (el) return el;
    var wrap = document.createElement("div");
    wrap.innerHTML = AUTOINJECT_HTML;
    var node = wrap.firstElementChild;
    document.body.appendChild(node);
    return node;
  }

  function setText(node, text) {
    if (!node) return;
    node.textContent = text == null ? "" : String(text);
  }

  // Unified error modal
  MT.showErrorModal = function (title, message, opts) {
    opts = opts || {};
    var el = ensureModalEl();

    var titleEl = el.querySelector("#mt-error-title");
    var msgEl   = el.querySelector("#mt-error-message");
    var headEl  = el.querySelector("#mt-error-headline");
    var iconEl  = el.querySelector("#mt-error-icon");

    setText(titleEl, title || "Error");

    var msgText = "";
    if (typeof message === "string") msgText = message;
    else if (message && typeof message === "object")
      msgText = message.message || message.error || JSON.stringify(message);
    else msgText = "Unexpected error.";
    setText(msgEl, msgText);

    setText(headEl, opts.headline || ((title && title.length <= 12) ? title : "Oops!"));
    if (opts.iconSrc && iconEl) iconEl.setAttribute("src", opts.iconSrc);

    // Ensure exclusivity: close others, purge backdrops, raise z-index
    closeAllModals('mt-error-modal');
    purgeBackdrops();
    window.__mtErrorVisible = true;
    el.style.zIndex = '1065'; // above typical Bootstrap modal z-index

    try {
      if (window.bootstrap && typeof bootstrap.Modal === "function") {
        (window.__mtErrModal || (window.__mtErrModal = new bootstrap.Modal(el, { backdrop: "static" }))).show();
        el.removeAttribute("hidden");
        el.setAttribute("aria-hidden", "false");
        el.addEventListener('hidden.bs.modal', function onHidden(){
          window.__mtErrorVisible = false;
          el.removeEventListener('hidden.bs.modal', onHidden);
        });
      } else {
        el.classList.add("show");
        el.style.display = "block";
        el.removeAttribute("hidden");
        el.setAttribute("aria-hidden", "false");
        $(el).find('[data-bs-dismiss="modal"], .mt-modal__close').one("click", function () {
          MT.hideErrorModal();
        });
      }
    } catch (e) {
      window.alert((title || "Error") + "\n" + msgText);
    }
  };

  // Global API
  window.MEGATRADER = window.MEGATRADER || {};
  window.MEGATRADER.showError = function (title, message, opts) {
    if (window.MT && typeof window.MT.showErrorModal === "function") {
      return window.MT.showErrorModal(title, message, opts || {});
    }
    console.error("[MT][Error]", title, message);
    try {
      alert((title || "Error") + "\n\n" + (message?.message || message?.error || message));
    } catch (_) {}
  };

  // Global JSON fetch with diagnostics
  window.MEGATRADER.fetchJSON = function (url, options) {
    return fetch(url, options).then(function (r) {
      return r.text().then(function (txt) {
        var ct = (r.headers.get("content-type") || "").toLowerCase();
        var looksJSON = ct.indexOf("application/json") > -1;
        var data = null;

        if (looksJSON) { try { data = JSON.parse(txt); } catch (e) {} }

        function extractLabel(html) {
          if (!html) return "";
          var m = html.match(/<title[^>]*>([^<]*)<\/title>/i);
          if (m && m[1]) return m[1].trim();
          var h = html.match(/<h1[^>]*>([^<]*)<\/h1>/i);
          if (h && h[1]) return h[1].trim();
          if (/not\s+found|404/i.test(html)) return "Not Found";
          if (/forbidden|denied|401|403/i.test(html)) return "Forbidden/Unauthorized";
          if (/fatal\s+error|exception|stack/i.test(html)) return "Server Error";
          return "HTML response (not JSON)";
        }

        var actionName =
          options && options.body && typeof options.body.get === "function"
            ? options.body.get("action") || ""
            : "";

        if (!r.ok) {
          var label = extractLabel(txt);
          var msg =
            "HTTP " + r.status + " " + (r.statusText || "") +
            "\nURL: " + url +
            (actionName ? "\nAction: " + actionName : "") +
            (label ? "\nServer says: " + label : "");
          throw new Error(msg);
        }

        if (!data) {
          var label2 = extractLabel(txt);
          var msg2 =
            "Non-JSON server response." +
            "\nURL: " + url +
            (actionName ? "\nAction: " + actionName : "") +
            (label2 ? "\nServer says: " + label2 : "");
          throw new Error(msg2);
        }

        return data;
      });
    });
  };

  MT.hideErrorModal = function () {
    var el = document.getElementById("mt-error-modal");
    if (!el) return;
    try {
      if (window.bootstrap && typeof bootstrap.Modal === "function") {
        (window.__mtErrModal || (window.__mtErrModal = new bootstrap.Modal(el))).hide();
      } else {
        el.classList.remove("show");
        el.style.display = "none";
        el.setAttribute("hidden", "");
        el.setAttribute("aria-hidden", "true");
      }
    } catch (e) {
      // noop
    } finally {
      window.__mtErrorVisible = false;
      purgeBackdrops();
    }
  };
})(jQuery, window, document);
