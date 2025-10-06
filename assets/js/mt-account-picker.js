(function () {
  function onReady(fn) {
    if (document.readyState !== "loading") fn();
    else document.addEventListener("DOMContentLoaded", fn, { once: true });
  }

  onReady(function () {
    if (!window.MT_DATA) return;

    // === Error modal alias ===
    var showErr = function (t, m, o) {
      if (window.MEGATRADER && typeof MEGATRADER.showError === "function")
        return MEGATRADER.showError(t, m, o || {});
      console.error("[MT][Error]", t, m);
    };

    var DEBUG = !!window.MT_DATA.debug;
    var SEL = window.MT_DATA.selectors || {};
    var ACCS = Array.isArray(window.MT_DATA.accounts)
      ? window.MT_DATA.accounts
      : [];
    var selectedId = window.MT_DATA.currentId || "";

    // Selectores
    var q = function (s) {
      return typeof s === "string" && s ? document.querySelector(s) : null;
    };
    var qa = function (root, s) {
      return root ? root.querySelectorAll(s) : [];
    };

    var grid = q(SEL.grid || "#mt-accounts-grid");
    var btnSel = q(SEL.select || "#select-subscription-btn");
    var elBadge = q(SEL.badge || "#mt-badge");
    var elSize = q(SEL.size || "#mt-size");
    var elName = q(SEL.name || "#mt-name");
    var modalSel = SEL.modal || "#changeSubcriptionModal";
    var CARD = SEL.card || ".subscription-card";
    var CHECK = SEL.check || ".checkmark-icon";

    // Contenedor donde se renderiza el performance (para detectar cambios)
    var PERF_SEL = ".mt-account-performance";
    var perfContainer = q(PERF_SEL);

    var pendingPreloader = false; // solo ocultamos si nosotros lo mostramos
    var preloaderFallbackTimer = null;

    function log() {
      if (DEBUG) {
        try {
          console.debug.apply(
            console,
            ["[MT]"].concat([].slice.call(arguments))
          );
        } catch (_) {}
      }
    }

    // Preloader helpers (usa tu global .preloader)
    function showPreloader() {
      if (
        window.jQuery &&
        window.jQuery.fn &&
        window.jQuery(".preloader").length
      ) {
        pendingPreloader = true;
        window.jQuery(".preloader").stop(true, true).fadeIn(150);
        // Fallback por si algo falla y no hay cambios o no hay ajax
        clearTimeout(preloaderFallbackTimer);
        preloaderFallbackTimer = setTimeout(hidePreloader, 7000);
        log("preloader: show");
      } else {
        showErr("Preloader not available", ".preloader or jQuery not found.");
      }
    }
    function hidePreloader() {
      if (!pendingPreloader) return; // no interrumpir si no lo activamos nosotros
      if (
        window.jQuery &&
        window.jQuery.fn &&
        window.jQuery(".preloader").length
      ) {
        window.jQuery(".preloader").stop(true, true).fadeOut(150);
        pendingPreloader = false;
        clearTimeout(preloaderFallbackTimer);
        log("preloader: hide");
      }
    }

    // Si no hay currentId válido, usa la primera cuenta disponible
    function ensureSelectedId() {
      if (
        selectedId &&
        ACCS.some(function (a) {
          return a.id === selectedId;
        })
      )
        return;
      if (ACCS.length > 0) {
        selectedId = ACCS[0].id;
        log("selectedId fallback ->", selectedId);
      } else {
        showErr("No accounts", "No accounts found for this user.");
      }
    }

    function titleCase(s) {
      return (s || "")
        .toString()
        .replace(/-/g, " ")
        .replace(/\b\w/g, function (m) {
          return m.toUpperCase();
        });
    }

    function getAccountById(id) {
      for (var i = 0; i < ACCS.length; i++)
        if (ACCS[i].id === id) return ACCS[i];
      return null;
    }

    function markSelected() {
      if (!grid) return;
      var cards = qa(grid, CARD);
      cards.forEach(function (card) {
        var cid = card.getAttribute("data-account-id");
        var check = card.querySelector(CHECK);
        var isSel = cid === selectedId;
        card.classList.toggle("active", isSel);
        if (check) check.style.display = isSel ? "block" : "none";
      });
    }

    function toggleSelectBtn() {
      if (!btnSel) return;
      if (selectedId) {
        btnSel.classList.remove("disabled");
        btnSel.removeAttribute("disabled");
      } else {
        btnSel.classList.add("disabled");
        btnSel.setAttribute("disabled", "disabled");
      }
    }

    function closeModal() {
      var modalEl = q(modalSel);
      if (!modalEl) return;
      if (window.bootstrap && window.bootstrap.Modal) {
        var inst =
          bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        inst.hide();
      } else {
        modalEl.classList.remove("show");
      }
    }

    // Delegación de clicks en el grid
    if (grid) {
      grid.addEventListener("click", function (e) {
        var card = e.target.closest(CARD);
        if (!card) return;
        selectedId = card.getAttribute("data-account-id") || "";
        log("clicked ->", selectedId);
        markSelected();
        toggleSelectBtn();
      });
    }

    // === OBSERVER: cuando cambie el performance en el DOM, ocultamos el preloader
    if (perfContainer) {
      var perfObserver = new MutationObserver(function (mutations) {
        // Si hubo cambios en hijos, asumimos que el nuevo HTML ya llegó
        var hasChildChanges = mutations.some(function (m) {
          return (
            m.type === "childList" &&
            (m.addedNodes.length || m.removedNodes.length)
          );
        });
        if (hasChildChanges) {
          log("performance DOM changed -> hide preloader");
          hidePreloader();
        }
      });
      perfObserver.observe(perfContainer, { childList: true, subtree: true });
    }

    // Si tu actualización usa jQuery.ajax, cuando termine cualquier ajax, ocultamos por si acaso
    if (window.jQuery && window.jQuery(document)) {
      window.jQuery(document).ajaxComplete(function () {
        log("ajaxComplete -> hide preloader (safety)");
        hidePreloader();
      });
    }

    // Botón Select
    if (btnSel) {
      btnSel.addEventListener("click", function () {
        if (!selectedId) {
          showErr("Select an account", "Please choose an account to continue.");
          return;
        }
        var obj = getAccountById(selectedId);
        if (!obj) {
          showErr("Invalid account", "The selected account is not available.");
          return;
        }

        // Actualizar cabecera
        if (elBadge) {
          var statusKey =
            (obj.status || "")
              .toLowerCase()
              .trim()
              .replace(/[^a-z0-9]+/g, "-") || "default";

          // Texto visible (Title Case)
          elBadge.textContent = statusKey
            .replace(/-/g, " ")
            .replace(/\b\w/g, (m) => m.toUpperCase());

          // Clase FINAL del badge del botón (sin mapas, solo por status):
          elBadge.className =
            "badge-mega badge-mega-sm badge-mega-" + statusKey;
        }
        if (elSize) elSize.textContent = obj.size || "";
        if (elName) elName.textContent = obj.name || "Account";

        if (!elBadge && !elSize && !elName) {
          showErr("UI not ready", "Header targets not found.");
        }

        // Mostrar preloader y cerrar modal
        showPreloader();
        closeModal();

        // Lanza un evento por si otro script hace el AJAX del performance
        var ev = new CustomEvent("mt:accountSelected", {
          detail: { accountId: selectedId },
        });
        document.dispatchEvent(ev);
        log("event dispatched: mt:accountSelected", selectedId);
        try {
          if (typeof window.breachGuardCheck === "function")
            window.breachGuardCheck(selectedId);
        } catch (e) {
          showErr("Breach check failed", e);
        }
      });
    }

    // Re-sincronizar cuando el modal se abre
    var modalEl = q(modalSel);
    if (modalEl) {
      modalEl.addEventListener("shown.bs.modal", function () {
        markSelected();
        toggleSelectBtn();
      });
    }

    // INIT
    ensureSelectedId();
    markSelected();
    toggleSelectBtn();

    log("init ok", { selectedId: selectedId, total: ACCS.length });
  });
})();
