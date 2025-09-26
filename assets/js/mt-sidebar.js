// ==== Profile modal (Bootstrap-like, sin submit/save) ====
(function () {
  function qs(s, r = document) { return r.querySelector(s); }
  function qsa(s, r = document) { return Array.from(r.querySelectorAll(s)); }

  function showOnly(modal, tabId) {
    // Tabs
    qsa(".mt-tab", modal).forEach(function (b) {
      var on = b.getAttribute("data-tab") === tabId;
      b.classList.toggle("active", on);
      // estilo activo (como tu diseño)
      b.style.background = on ? "#1E1E1E" : "";
      b.style.outline = on ? "1px solid #fff" : "";
    });
    // Panels
    qsa("[data-panel]", modal).forEach(function (p) {
      var show = p.getAttribute("data-panel") === tabId;
      p.hidden = !show;
      p.style.display = show ? "block" : "none"; // por si tu CSS pisa [hidden]
    });
  }

  function openModal(modal) {
    if (modal.classList.contains("show")) return;
    showOnly(modal, "pi"); // por defecto abrir en "Personal information"

    modal.classList.add("show");
    modal.style.display = "block";
    modal.removeAttribute("aria-hidden");
    document.body.classList.add("modal-open");

    // Backdrop consistente con tus otros modales
    var bd = document.createElement("div");
    bd.className = "modal-backdrop fade show";
    bd.dataset.role = "mt-profile-backdrop";
    document.body.appendChild(bd);
  }

  function closeModal(modal) {
    modal.classList.remove("show");
    modal.style.display = "";
    modal.setAttribute("aria-hidden", "true");

    var bd = document.querySelector('.modal-backdrop[data-role="mt-profile-backdrop"]');
    if (bd) bd.remove();

    if (!document.querySelector(".modal.show")) {
      document.body.classList.remove("modal-open");
    }
  }

  function init() {
    var modal = document.getElementById("mt-profile-modal");
    if (!modal) return;

    // Triggers (.mt-account-settings-js) – evita "#" en URL
    qsa(".mt-account-settings-js").forEach(function (el) {
      el.setAttribute("href", "javascript:void(0)");
      el.setAttribute("role", "button");
      el.setAttribute("aria-controls", "mt-profile-modal");

      el.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        openModal(modal);
      }, { passive: false });

      el.addEventListener("keydown", function (e) {
        if (e.key === "Enter" || e.key === " ") {
          e.preventDefault();
          openModal(modal);
        }
      });
    });

    // Cerrar (botones con .js-close-profile-modal)
    qsa(".js-close-profile-modal", modal).forEach(function (btn) {
      btn.addEventListener("click", function (e) {
        e.preventDefault();
        closeModal(modal);
      });
    });

    // Cerrar (ESC)
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && modal.classList.contains("show")) {
        closeModal(modal);
      }
    });

    // Cerrar (click fuera del panel)
    document.addEventListener("click", function (e) {
      if (!modal.classList.contains("show")) return;
      var inside = e.target.closest(".modal-content") || e.target.closest(".mt-account-settings-js");
      if (!inside) closeModal(modal);
    });

    // Tabs
    qsa(".mt-tab", modal).forEach(function (btn) {
      btn.addEventListener("click", function () {
        showOnly(modal, btn.getAttribute("data-tab") || "pi");
      });
    });

    // Estado inicial por si el HTML llega sin hidden correcto
    showOnly(modal, "pi");
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init, { once: true });
  } else {
    init();
  }
})();
