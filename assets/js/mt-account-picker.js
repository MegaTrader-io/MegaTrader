/* mt-account-picker.js — usa solo $('.preloader') del theme */
(function () {
  "use strict";

  // Polyfill súper simple por si falta CSS.escape en algún browser
  if (!window.CSS || typeof CSS.escape !== "function") {
    window.CSS = window.CSS || {};
    CSS.escape = function (s) {
      return String(s).replace(/[^a-zA-Z0-9_\-]/g, "\\$&");
    };
  }

  function getLastAccountKey() {
    try {
      // compat con payload: si no hay uid, se usa la key simple
      var uid =
        (window.MT_DATA && (MT_DATA.userId || MT_DATA.user || MT_DATA.uid)) ||
        "";
      return "mt:lastAccountId" + (uid ? ":" + String(uid) : "");
    } catch (_) {
      return "mt:lastAccountId";
    }
  }

  function loadLastAccountId() {
    var key = getLastAccountKey();
    try {
      var m = document.cookie.match(
        new RegExp(
          "(?:^|;)\\s*" +
            key.replace(/[-[\]/{}()*+?.\\^$|]/g, "\\$&") +
            "=([^;]+)"
        )
      );
      return m ? decodeURIComponent(m[1]) : "";
    } catch (_) {
      return "";
    }
  }

  function saveLastAccountId(id) {
    var key = getLastAccountKey();
    var val = String(id || "");
    try {
      // solo cookie (1 año)
      document.cookie =
        key + "=" + encodeURIComponent(val) + ";path=/;max-age=31536000";
    } catch (_) {}
  }

  // Esperar DOM listo (soporta inline/defer)
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init, { once: true });
  } else {
    init();
  }

  function init() {
    if (!window.MT_DATA) return;

    var CFG = window.MT_DATA || {};
    var SEL = CFG.selectors || {};
    var grid = document.querySelector(SEL.grid || "#mt-accounts-grid");
    var btn = document.querySelector(SEL.select || "#select-subscription-btn");
    var perf =
      document.querySelector(SEL.performance || ".mt-account-performance") ||
      document.querySelector(".mt-account-performance");
    var modal = document.querySelector(SEL.modal || "#changeSubcriptionModal");

    var filterSel = document.getElementById("mt-acc-filter");
    var filterLbl = document.getElementById("mt-acc-filter-label");
    var filterMenu = document.getElementById("mt-acc-filter-menu");

    var selectedId = loadLastAccountId() || CFG.currentId || null;
    var pendingPreloader = false;
    var preloaderFallbackTimer = null;

    // ========= Helpers =========
    function q(root, sel) {
      return (root || document).querySelector(sel);
    }
    function qAll(root, sel) {
      return Array.prototype.slice.call(
        (root || document).querySelectorAll(sel)
      );
    }
    function log() {
      if (CFG.debug)
        try {
          console.debug.apply(
            console,
            ["[MT]"].concat([].slice.call(arguments))
          );
        } catch (_) {}
    }

    function showErr(t, m, o) {
      if (window.MEGATRADER && typeof MEGATRADER.showError === "function")
        return MEGATRADER.showError(t, m, o || {});
      console.error("[MT][Error]", t, m);
    }

    function normalizeStatus(s) {
      var st = (s || "").toString().trim().toUpperCase();
      return st === "ACTIVATION_PENDING" ? "PENDING_ACTIVATION" : st;
    }

    function enableBtn(on) {
      if (!btn) return;
      btn.disabled = !on;
      btn.classList.toggle("disabled", !on);
    }

    // ========= Preloader (tu .preloader con jQuery) =========
    function showPreloader() {
      if (
        window.jQuery &&
        window.jQuery.fn &&
        window.jQuery(".preloader").length
      ) {
        pendingPreloader = true;
        window.jQuery(".preloader").stop(true, true).fadeIn(150);
        clearTimeout(preloaderFallbackTimer);
        preloaderFallbackTimer = setTimeout(hidePreloader, 7000);
        log("preloader: show");
      } else {
        log("preloader no disponible (.preloader + jQuery)");
      }
    }
    function hidePreloader() {
      if (!pendingPreloader) return;
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

    // Ocultar preloader cuando cambie el DOM del performance (cuando llega el HTML)
    if (perf) {
      var perfObserver = new MutationObserver(function (muts) {
        var changed = muts.some(function (m) {
          return (
            m.type === "childList" &&
            (m.addedNodes.length || m.removedNodes.length)
          );
        });
        if (changed) hidePreloader();
      });
      perfObserver.observe(perf, { childList: true, subtree: true });
    }
    // Por si algún otro script usa $.ajax
    if (window.jQuery && window.jQuery(document)) {
      window.jQuery(document).ajaxComplete(function () {
        hidePreloader();
      });
    }

    // ========= Filtro (helpers) =========
    function statusToBucket(st) {
      st = normalizeStatus(st);
      if (st === "ACTIVE") return "ACTIVE";
      if (st === "PENDING_ACTIVATION") return "PENDING_ACTIVATION";
      if (st === "PASSED" || st === "UPGRADED") return "PASSED";
      if (st === "BREACHED" || st === "RESET") return "BREACHED";
      return "ACTIVE";
    }

    function applyFilter(val) {
      if (!grid) return;
      var want = (val || "").toString().trim().toUpperCase();

      grid.querySelectorAll(".subscription-card").forEach(function (card) {
        var st = normalizeStatus(card.getAttribute("data-status"));
        var match = false;

        if (want === "ACTIVE") {
          match = st === "ACTIVE";
        } else if (want === "BREACHED") {
          match = st === "BREACHED" || st === "RESET";
        } else if (want === "PASSED") {
          match = st === "PASSED" || st === "UPGRADED";
        } else if (want === "PENDING_ACTIVATION") {
          match = st === "PENDING_ACTIVATION";
        }

        if (match) {
          card.classList.add("d-flex");
          card.classList.remove("d-none");
          card.style.removeProperty("display");
        } else {
          card.classList.remove("d-flex");
          card.classList.add("d-none");
          card.style.setProperty("display", "none");
        }
      });

      var active = grid.querySelector(".subscription-card.active");
      if (active && active.classList.contains("d-none")) {
        active.classList.remove("active");
        var c = active.querySelector(".checkmark-icon");
        if (c) c.style.display = "none";
        enableBtn(false);
      }
    }

    function forceFilter(bucket) {
      if (filterSel) filterSel.value = bucket;
      if (filterLbl) {
        var txt = "Active";
        if (bucket === "BREACHED") txt = "Breached";
        else if (bucket === "PASSED") txt = "Passed";
        else if (bucket === "PENDING_ACTIVATION") txt = "Pending activation";
        filterLbl.textContent = txt;
      }
      applyFilter(bucket);
    }

    // ========= UI de selección =========
    function setActiveCard(card) {
      if (!card || card.classList.contains("d-none")) return;

      qAll(grid, (SEL.card || ".subscription-card") + ".active").forEach(
        function (el) {
          el.classList.remove("active");
          var c = q(el, SEL.check || ".checkmark-icon");
          if (c) c.style.display = "none";
        }
      );

      card.classList.add("active");
      var ch = q(card, SEL.check || ".checkmark-icon");
      if (ch) ch.style.display = "block";

      selectedId = card.getAttribute("data-account-id") || null;
      window.mtAccounts = window.mtAccounts || {};
      window.mtAccounts.selectedId = selectedId;
      saveLastAccountId(selectedId);

      enableBtn(true);
      updateAccountCTAs();
    }

    function preselectIfVisible() {
      if (!grid || !selectedId) {
        enableBtn(false);
        return;
      }
      var cur = grid.querySelector(
        (SEL.card || ".subscription-card") +
          '[data-account-id="' +
          CSS.escape(selectedId) +
          '"]'
      );
      if (cur && !cur.classList.contains("d-none")) setActiveCard(cur);
      else enableBtn(false);
    }

    function titleCase(s) {
      return (s || "")
        .toString()
        .replace(/-/g, " ")
        .replace(/\b\w/g, function (m) {
          return m.toUpperCase();
        });
    }

    function updateHeaderFromCard(card) {
      if (!card) return;
      var sizeVal = card.getAttribute("data-size") || "";
      var nameVal = card.getAttribute("data-name") || "Account";
      var logoVal = card.getAttribute("data-logo") || "";
      var status = (card.getAttribute("data-status") || "").toLowerCase();

      var sizeEl = document.querySelector(SEL.size || "#mt-size");
      var nameEl = document.querySelector(SEL.name || "#mt-name");
      var logoEl = document.querySelector(
        SEL.platformLogo || "#mt-platform-logo"
      );
      var badgeEl = document.querySelector(SEL.badge || "#mt-badge");

      if (sizeEl) sizeEl.textContent = sizeVal;
      if (nameEl) nameEl.textContent = nameVal;
      if (logoEl && logoVal) logoEl.src = logoVal;
      if (badgeEl) {
        badgeEl.textContent = status ? titleCase(status) : "NoStatusDefine";
      }
    }

    function closeModal() {
      if (!modal) return;
      if (window.bootstrap && window.bootstrap.Modal) {
        var inst =
          window.bootstrap.Modal.getInstance(modal) ||
          new window.bootstrap.Modal(modal);
        inst.hide();
      } else {
        modal.classList.remove("show");
      }
    }

    // ========= CTAs de modales (breach / passed) =========
    function updateAccountCTAs() {
      if (!grid) return;
      var cardSel = SEL.card || ".subscription-card";
      var active = grid.querySelector(cardSel + ".active");
      if (!active) return;

      var accId = active.getAttribute("data-account-id") || "";
      var mainId = active.getAttribute("data-main-id") || "";
      var resetId = active.getAttribute("data-reset-id") || "";
      var actId = active.getAttribute("data-activation-id") || "";
      var statusRaw = active.getAttribute("data-status") || "";
      var status = normalizeStatus(statusRaw);
      var base =
        CFG.checkoutBase ||
        (window.MT_DATA && window.MT_DATA.checkoutBase) ||
        "/checkout";

      function buildUrl(b, pid) {
        b = (b || "/checkout").replace(/(\?|#).*$/, "");
        return pid
          ? b + "?add-to-cart=" + encodeURIComponent(String(pid))
          : "#";
      }

      // === BREACHED ===
      var breachModal = document.getElementById("mt-breach-alert-modal");
      if (breachModal) {
        breachModal.setAttribute("data-account-id", accId);
        breachModal.setAttribute("data-main-product-id", mainId);

        var baseBreach = breachModal.getAttribute("data-checkout-base") || base;
        var breachBtn = breachModal.querySelector(".mt-breach-reset-button");
        if (breachBtn) {
          breachBtn.setAttribute("data-account-id", accId);
          breachBtn.setAttribute("data-main-product-id", mainId);
          if (resetId) {
            breachBtn.href = buildUrl(baseBreach, resetId);
            breachBtn.classList.remove("disabled", "d-none");
            breachBtn.removeAttribute("aria-disabled");
          } else {
            breachBtn.href = "#";
            breachBtn.classList.add("disabled");
            breachBtn.setAttribute("aria-disabled", "true");
          }
        }
      }

      // === PASSED / ACTIVATION ===
      var passedModal = document.getElementById("mt-account-passed-modal");
      if (passedModal) {
        passedModal.setAttribute("data-account-id", accId);
        passedModal.setAttribute("data-main-product-id", mainId);
        passedModal.setAttribute("data-current-status", status);
        passedModal.setAttribute("data-activation-id", actId);

        var baseAct = passedModal.getAttribute("data-checkout-base") || base;
        var actBtn = passedModal.querySelector("#mt-activation-btn");
        if (actBtn) {
          actBtn.setAttribute("data-account-id", accId);
          actBtn.setAttribute("data-main-product-id", mainId);
          if (actId) {
            actBtn.href = buildUrl(baseAct, actId);
            actBtn.classList.remove("disabled", "d-none");
            actBtn.removeAttribute("aria-disabled");
          } else {
            actBtn.href = "#";
            actBtn.classList.add("disabled");
            actBtn.setAttribute("aria-disabled", "true");
          }
        }

        if (typeof window.__mtPassedSyncUI === "function") {
          window.__mtPassedSyncUI(passedModal);
        }
      }
    }

    // ========= Selección por filtro =========
    if (filterMenu) {
      filterMenu.addEventListener("click", function (e) {
        var opt = e.target.closest(".mt-filter-option");
        if (!opt) return;
        var val = (opt.getAttribute("data-value") || "ACTIVE").toUpperCase();
        if (filterLbl) filterLbl.textContent = opt.textContent.trim();
        if (filterSel) filterSel.value = val;
        applyFilter(val);
      });
    }

    // ========= Al abrir modal: forzar filtro/selección según cookie/actual =========
    // ========= Al abrir modal: forzar filtro/selección según cookie/actual =========
if (modal && window.bootstrap) {
  modal.addEventListener("show.bs.modal", function () {
    // Evita doble-sync si hay otro listener
    if (modal.__mtSyncing) return;
    modal.__mtSyncing = true;

    // Oculta la grilla momentáneamente para evitar parpadeo
    var gridWrap = document.getElementById("mt-accounts-grid");
    if (gridWrap) gridWrap.style.visibility = "hidden";

    var wantId =
      loadLastAccountId() ||
      selectedId ||
      (window.MT_DATA && MT_DATA.currentId) ||
      null;

    // función para visibilizar al final
    function reveal() {
      requestAnimationFrame(function () {
        if (gridWrap) gridWrap.style.visibility = "visible";
        modal.__mtSyncing = false;
      });
    }

    if (!grid) {
      reveal();
      return;
    }

    var cardSel = (SEL.card || ".subscription-card");
    var card =
      wantId &&
      grid.querySelector(cardSel + '[data-account-id="' + CSS.escape(wantId) + '"]');

    if (card) {
      // Ajusta filtro al bucket de esa cuenta y márcala active
      var st = (card.getAttribute("data-status") || "").toUpperCase();
      var bucket = (function (s) {
        if (s === "ACTIVATION_PENDING") s = "PENDING_ACTIVATION";
        if (s === "ACTIVE") return "ACTIVE";
        if (s === "PENDING_ACTIVATION") return "PENDING_ACTIVATION";
        if (s === "PASSED" || s === "UPGRADED") return "PASSED";
        if (s === "BREACHED" || s === "RESET") return "BREACHED";
        return "ACTIVE";
      })(st);

      // Actualiza label/selector del filtro ANTES de mostrar
      if (filterSel) filterSel.value = bucket;
      if (filterLbl) {
        filterLbl.textContent =
          bucket === "BREACHED" ? "Breached" :
          bucket === "PASSED" ? "Passed" :
          bucket === "PENDING_ACTIVATION" ? "Pending activation" :
          "Active";
      }

      // Aplica filtro y selección
      (function applyFilter(val){
        var want = String(val || "").trim().toUpperCase();
        grid.querySelectorAll(".subscription-card").forEach(function (c) {
          var s = (c.getAttribute("data-status") || "").toUpperCase();
          if (s === "ACTIVATION_PENDING") s = "PENDING_ACTIVATION";
          var match =
            (want === "ACTIVE" && s === "ACTIVE") ||
            (want === "BREACHED" && (s === "BREACHED" || s === "RESET")) ||
            (want === "PASSED" && (s === "PASSED" || s === "UPGRADED")) ||
            (want === "PENDING_ACTIVATION" && s === "PENDING_ACTIVATION");

          if (match) {
            c.classList.add("d-flex");
            c.classList.remove("d-none");
            c.style.removeProperty("display");
          } else {
            c.classList.remove("d-flex");
            c.classList.add("d-none");
            c.style.setProperty("display", "none");
          }
        });
      })(bucket);

      // Marca active (no tocar header ni AJAX aquí)
      grid.querySelectorAll(cardSel + ".active").forEach(function (el) {
        el.classList.remove("active");
        var ck = el.querySelector(".checkmark-icon");
        if (ck) ck.style.display = "none";
      });
      card.classList.add("active");
      var ch = card.querySelector(".checkmark-icon");
      if (ch) ch.style.display = "block";

      // Habilita el botón
      if (btn) {
        btn.disabled = false;
        btn.classList.remove("disabled");
      }

      reveal();
      return;
    }

    // Fallback: si no hay preferida o no se encuentra la card
    var first =
      (filterSel && filterSel.querySelector("option")?.value) || "ACTIVE";
    if (filterSel) filterSel.value = first;
    if (filterLbl) {
      filterLbl.textContent =
        first === "BREACHED" ? "Breached" :
        first === "PASSED" ? "Passed" :
        first === "PENDING_ACTIVATION" ? "Pending activation" : "Active";
    }

    // Muestra algo del bucket por defecto y respeta .active si ya hay
    (function applyFilter(val){
      var want = String(val || "").trim().toUpperCase();
      grid.querySelectorAll(".subscription-card").forEach(function (c) {
        var s = (c.getAttribute("data-status") || "").toUpperCase();
        if (s === "ACTIVATION_PENDING") s = "PENDING_ACTIVATION";
        var match =
          (want === "ACTIVE" && s === "ACTIVE") ||
          (want === "BREACHED" && (s === "BREACHED" || s === "RESET")) ||
          (want === "PASSED" && (s === "PASSED" || s === "UPGRADED")) ||
          (want === "PENDING_ACTIVATION" && s === "PENDING_ACTIVATION");

        if (match) {
          c.classList.add("d-flex");
          c.classList.remove("d-none");
          c.style.removeProperty("display");
        } else {
          c.classList.remove("d-flex");
          c.classList.add("d-none");
          c.style.setProperty("display", "none");
        }
      });
    })(first);

    var active =
      grid && grid.querySelector((SEL.card || ".subscription-card") + ".active");
    if (active && !active.classList.contains("d-none")) {
      // ya está visible, deja habilitado
      if (btn) {
        btn.disabled = false;
        btn.classList.remove("disabled");
      }
    } else {
      if (btn) {
        btn.disabled = true;
        btn.classList.add("disabled");
      }
    }

    reveal();
  });
}


    if (grid) {
      grid.addEventListener("click", function (e) {
        var card = e.target.closest(SEL.card || ".subscription-card");
        if (!card || card.classList.contains("d-none")) return;
        setActiveCard(card);
      });
    }

    if (btn) {
      btn.addEventListener("click", function () {
        if (!selectedId) {
          showErr("Select an account", "Please choose an account to continue.");
          return;
        }
        var active =
          grid &&
          grid.querySelector((SEL.card || ".subscription-card") + ".active");
        if (!active) {
          showErr("Invalid account", "The selected account is not available.");
          return;
        }

        // Actualiza cabecera inmediatamente y muestra preloader
        updateHeaderFromCard(active);
        updateAccountCTAs();
        showPreloader();
        closeModal();
        enableBtn(false);

        // AJAX performance
        var fd = new FormData();
        fd.append(
          "action",
          (CFG.ajax && CFG.ajax.action) || "mt_accounts_performance"
        );
        fd.append("nonce", (CFG.ajax && CFG.ajax.nonce) || "");
        fd.append("accountId", selectedId);

        var ajaxURL = (CFG.ajax && CFG.ajax.url) || "";
        var fetcher =
          window.MEGATRADER && typeof MEGATRADER.fetchJSON === "function"
            ? window.MEGATRADER.fetchJSON
            : function (u, opts) {
                return fetch(u, opts).then(function (r) {
                  return r.json();
                });
              };

        fetcher(ajaxURL, {
          method: "POST",
          body: fd,
          credentials: "same-origin",
        })
          .then(function (res) {
            if (!res || !res.success)
              throw new Error(
                (res && res.data && res.data.message) || "AJAX failed"
              );

            if (
              window.mtTooltips &&
              typeof window.mtTooltips.closeAll === "function"
            )
              window.mtTooltips.closeAll();
            if (perf && res.data && typeof res.data.html === "string") {
              perf.innerHTML = res.data.html || "";
              if (
                window.mtTooltips &&
                typeof window.mtTooltips.refresh === "function"
              )
                window.mtTooltips.refresh(perf);
            }

            // Exponer orderId en el root y disparar eventos globales
            var orderId =
              parseInt(active.getAttribute("data-order") || "0", 10) || 0;
            var root = document.getElementById("mt-account-overview");
            if (root)
              root.setAttribute(
                "data-order-id",
                orderId ? String(orderId) : ""
              );

            document.dispatchEvent(
              new CustomEvent("mt:accountSelected", {
                detail: {
                  accountId: selectedId,
                  id: selectedId,
                  order: orderId,
                  orderId: orderId,
                },
              })
            );

            try {
              if (typeof window.passedGuardCheck === "function")
                window.passedGuardCheck(selectedId);
              if (typeof window.breachGuardCheck === "function")
                window.breachGuardCheck(selectedId);
            } catch (e) {
              log("guards error", e);
            }
          })
          .catch(function (err) {
            showErr("Account Performance Error", err && (err.message || err), {
              headline: "Oops!",
            });
          })
          .finally(function () {
            hidePreloader();
            enableBtn(true);
          });
      });
    }

    // Estado básico del label del filtro (no filtra aún)
    if (filterSel)
      filterSel.value = filterSel.querySelector("option")?.value || "ACTIVE";
    if (filterLbl)
      filterLbl.textContent =
        (filterSel && filterSel.selectedOptions?.[0]?.textContent) ||
        "Active";

    // NO forzamos filtro aquí; lo hace el inline de account-selection al cargar
    // y/o este archivo al abrir el modal.
    preselectIfVisible();
    log("picker init", { selectedId: selectedId });
  }
})();
