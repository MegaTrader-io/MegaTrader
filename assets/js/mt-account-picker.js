/* mt-account-picker.js — usa solo $('.preloader') del theme */
(function () {
  "use strict";

  // Polyfill seguro para CSS.escape (por si algún browser legacy)
  if (!window.CSS || typeof CSS.escape !== "function") {
    window.CSS = window.CSS || {};
    CSS.escape = function (s) {
      return String(s).replace(/[^a-zA-Z0-9_\-]/g, "\\$&");
    };
  }

  // ===== Cookie helpers (única implementación) =====
  function getLastAccountKey() {
    try {
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
      document.cookie =
        key + "=" + encodeURIComponent(val) + ";path=/;max-age=31536000";
    } catch (_) {}
  }

  // ===== DOM Ready =====
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
    var openerBtn = document.querySelector(
      '[data-bs-target="#changeSubcriptionModal"]'
    );

    var filterSel = document.getElementById("mt-acc-filter");
    var filterLbl = document.getElementById("mt-acc-filter-label");
    var filterMenu = document.getElementById("mt-acc-filter-menu");

    var selectedId = loadLastAccountId() || CFG.currentId || null;
    var pendingPreloader = false;
    var preloaderFallbackTimer = null;

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
    function statusToBucket(st) {
      st = normalizeStatus(st);
      if (st === "ACTIVE") return "ACTIVE";
      if (st === "PENDING_ACTIVATION") return "PENDING_ACTIVATION";
      if (st === "PASSED" || st === "UPGRADED") return "PASSED";
      //if (st === "BREACHED" || st === "RESET") return "BREACHED";
      if (st === "BREACHED") return "BREACHED";
      return "ACTIVE";
    }

    function enableBtn(on) {
      if (!btn) return;
      btn.disabled = !on;
      btn.classList.toggle("disabled", !on);
    }

    // ===== Preloader =====
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
    if (window.jQuery && window.jQuery(document)) {
      window.jQuery(document).ajaxComplete(function () {
        hidePreloader();
      });
    }

    // ===== Filtro =====
    function applyFilter(bucket) {
      if (!grid) return;
      var want = (bucket || "").toString().trim().toUpperCase();

      grid.querySelectorAll(".subscription-card").forEach(function (card) {
        var st = normalizeStatus(card.getAttribute("data-status"));
        var match =
          (want === "ACTIVE" && st === "ACTIVE") ||
          //(want === "BREACHED" && (st === "BREACHED" || st === "RESET")) ||
          (want === "BREACHED" && st === "BREACHED" ) ||
          (want === "PASSED" && (st === "PASSED" || st === "UPGRADED")) ||
          (want === "PENDING_ACTIVATION" && st === "PENDING_ACTIVATION");

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

    function setFilterUI(bucket) {
      if (filterSel) filterSel.value = bucket;
      if (filterLbl) {
        var txt = "Active";
        if (bucket === "BREACHED") txt = "Breached";
        else if (bucket === "PASSED") txt = "Passed";
        else if (bucket === "PENDING_ACTIVATION") txt = "Pending activation";
        filterLbl.textContent = txt;
      }
    }

    function forceFilter(bucket) {
      setFilterUI(bucket);
      applyFilter(bucket);
    }

    // ===== Selección de card =====
    function setActiveCard(card) {
      if (!grid || !card || card.classList.contains("d-none")) return;

      grid.querySelectorAll(".subscription-card.active").forEach(function (el) {
        el.classList.remove("active");
        var c = el.querySelector(".checkmark-icon");
        if (c) c.style.display = "none";
      });

      card.classList.add("active");
      var ch = card.querySelector(".checkmark-icon");
      if (ch) ch.style.display = "block";

      selectedId = card.getAttribute("data-account-id") || null;
      var hasSub = (card.getAttribute("data-has-subscription") || "") === "1";

      var subId = card.getAttribute("data-subscription-id") || "";
      var orderId = card.getAttribute("data-order-id") || "";

      window.mtAccounts = window.mtAccounts || {};
      window.mtAccounts.selectedId = selectedId;
      window.mtAccounts.hasSubscription = hasSub;
      window.mtAccounts.subscriptionId = subId;

      saveLastAccountId(selectedId);

      // Sincroniza el botón opener y el root con el ID activo
      if (openerBtn) {
        openerBtn.setAttribute("data-account-id", selectedId);
        openerBtn.setAttribute("data-has-subscription", hasSub ? "1" : "0");
        openerBtn.setAttribute("data-subscription-id", subId);
        openerBtn.setAttribute("data-order-id", orderId);
      }
      var root = document.getElementById("mt-account-overview");
      if (root) {
        root.setAttribute("data-account-id", selectedId);
        root.setAttribute("data-has-subscription", hasSub ? "1" : "0");
        root.setAttribute("data-subscription-id", subId);
        root.setAttribute("data-order-id", orderId);
      }

      enableBtn(true);
      updateAccountCTAs();

      document.dispatchEvent(
        new CustomEvent("mt:hasSubscriptionChanged", {
          detail: { accountId: selectedId, hasSubscription: hasSub },
        })
      );
    }

    function selectByIdAndSync(id) {
      if (!grid || !id) return false;
      var card = grid.querySelector(
        (SEL.card || ".subscription-card") +
          '[data-account-id="' +
          CSS.escape(id) +
          '"]'
      );
      if (!card) return false;

      var bucket = statusToBucket(card.getAttribute("data-status") || "");
      forceFilter(bucket);

      if (!card.classList.contains("d-none")) {
        setActiveCard(card);
        return true;
      }
      return false;
    }

    // ===== Cabecera + CTAs =====
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

    function updateAccountCTAs() {
      if (!grid) return;
      var active = grid.querySelector(".subscription-card.active");
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

      // Breach
      var breachModal = document.getElementById("mt-breach-alert-modal");
      if (breachModal) {
        breachModal.setAttribute("data-account-id", accId);
        breachModal.setAttribute("data-main-id", mainId);
        breachModal.setAttribute("data-reset-id", resetId || "");

        var baseBreach = breachModal.getAttribute("data-checkout-base") || base;
        var breachBtn = breachModal.querySelector(".mt-breach-reset-button");

        if (breachBtn) {
          breachBtn.setAttribute("data-account-id", accId);
          breachBtn.setAttribute("data-main-id", mainId);
          if (resetId && String(resetId) !== "0") {
            breachBtn.classList.remove("d-none", "disabled");
            breachBtn.removeAttribute("aria-disabled");
            breachBtn.href = buildUrl(baseBreach, resetId);
          } else {
            breachBtn.classList.add("d-none", "disabled");
            breachBtn.setAttribute("aria-disabled", "true");
            breachBtn.removeAttribute("href");
          }
        }
      }

      // Passed / Activation
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

    // ===== Dropdown (UI) =====
    if (filterMenu) {
      filterMenu.addEventListener("click", function (e) {
        var opt = e.target.closest(".mt-filter-option");
        if (!opt) return;
        var val = (opt.getAttribute("data-value") || "ACTIVE").toUpperCase();
        setFilterUI(val);
        applyFilter(val);
      });
    }

    // ===== Estado inicial: asegura que haya algo visible =====
    (function initialSync() {
      if (!grid) return;

      var wantId =
        loadLastAccountId() ||
        document
          .querySelector(
            '[data-bs-target="#changeSubcriptionModal"][data-account-id]'
          )
          ?.getAttribute("data-account-id") ||
        CFG.currentId ||
        "";

      var card = wantId
        ? grid.querySelector(
            '.subscription-card[data-account-id="' + CSS.escape(wantId) + '"]'
          )
        : null;

      if (card) {
        var bucket = statusToBucket(card.getAttribute("data-status") || "");
        forceFilter(bucket);
        setActiveCard(card);

        // NUEVO: inicializar data-subscription-id en opener/root
        var subId0 = card.getAttribute("data-subscription-id") || "";
        var order0 = card.getAttribute("data-order-id") || "";

        if (openerBtn && (loadLastAccountId() || CFG.currentId)) {
          openerBtn.setAttribute("data-subscription-id", subId0);
          openerBtn.setAttribute("data-order-id", order0);
        }
        var root0 = document.getElementById("mt-account-overview");
        if (root0 && (loadLastAccountId() || CFG.currentId)) {
          root0.setAttribute("data-subscription-id", subId0);
          root0.setAttribute("data-order-id", order0);
        }

        if (openerBtn && selectedId)
          openerBtn.setAttribute("data-account-id", selectedId);
        var root = document.getElementById("mt-account-overview");
        if (root && selectedId)
          root.setAttribute("data-account-id", selectedId);
      } else {
        var first =
          (filterSel && filterSel.querySelector("option")?.value) || "ACTIVE";
        setFilterUI(first);
        applyFilter(first);
        var active = grid.querySelector(
          ".subscription-card.active:not(.d-none)"
        );
        enableBtn(
          !!active || !!grid.querySelector(".subscription-card:not(.d-none)")
        );
      }
    })();

    // ===== Antes de mostrar modal (evita glitch) =====
    if (modal && window.bootstrap) {
      modal.addEventListener("show.bs.modal", function () {
        var wantId = loadLastAccountId() || selectedId || CFG.currentId || null;
        if (wantId && selectByIdAndSync(wantId)) {
          enableBtn(true);
          return;
        }
        var active =
          grid &&
          grid.querySelector(
            (SEL.card || ".subscription-card") + ".active:not(.d-none)"
          );
        enableBtn(
          !!active || !!grid.querySelector(".subscription-card:not(.d-none)")
        );
      });
    }
    // Fallback si no hay bootstrap (al hacer click en el botón que abre el modal)
    if (openerBtn) {
      openerBtn.addEventListener("click", function () {
        var wantId = loadLastAccountId() || selectedId || CFG.currentId || null;
        if (wantId && selectByIdAndSync(wantId)) {
          enableBtn(true);
          return;
        }
        var active =
          grid &&
          grid.querySelector(
            (SEL.card || ".subscription-card") + ".active:not(.d-none)"
          );
        enableBtn(
          !!active || !!grid.querySelector(".subscription-card:not(.d-none)")
        );
      });
    }

    // ===== Click en cards =====
    if (grid) {
      grid.addEventListener("click", function (e) {
        var card = e.target.closest(SEL.card || ".subscription-card");
        if (!card || card.classList.contains("d-none")) return;
        setActiveCard(card);
      });
    }

    // ===== Botón Select (AJAX) =====
    if (btn) {
      btn.addEventListener("click", function () {
        if (!selectedId) {
          showErr("Select an account", "Please choose an account to continue.");
          return;
        }
        var active =
          grid &&
          grid.querySelector((SEL.card || ".subscription-card") + ".active");

        if (active) {
          var orderId =
            parseInt(active.getAttribute("data-order-id") || "0", 10) || 0;

          var hasSub =
            (active.getAttribute("data-has-subscription") || "") === "1";
          var subId = active.getAttribute("data-subscription-id") || "";
          var root = document.getElementById("mt-account-overview");
          if (root) {
            root.setAttribute("data-has-subscription", hasSub ? "1" : "0");
            root.setAttribute("data-subscription-id", subId);
            root.setAttribute("data-order-id", orderId);
          }
          if (openerBtn) {
            openerBtn.setAttribute("data-subscription-id", subId);
            openerBtn.setAttribute("data-order-id", orderId);
          }
        }
        if (!active) {
          showErr("Invalid account", "The selected account is not available.");
          return;
        }

        updateHeaderFromCard(active);
        updateAccountCTAs();
        showPreloader();
        if (modal) {
          const ae = document.activeElement;
          if (ae && modal.contains(ae)) {
            const opener = document.querySelector(
              '[data-bs-target="#changeSubcriptionModal"]'
            );
            (opener || document.body).focus({ preventScroll: true }); // saca el foco del modal
            if (ae.blur) ae.blur(); // quita el foco del botón Select
            modal.setAttribute("inert", ""); // bloquea interacción durante el cierre
          }
        }
        // cierra en el próximo tick para asegurar que el cambio de foco se aplique
        setTimeout(closeModal, 0);
        enableBtn(false);

        var fd = new FormData();
        fd.append(
          "action",
          (CFG.ajax && CFG.ajax.action) || "mt_accounts_performance"
        );
        fd.append("nonce", (CFG.ajax && CFG.ajax.nonce) || "");
        fd.append("accountId", selectedId);

        var ajaxURL = (CFG.ajax && CFG.ajax.url) || "";
        // Refuerza sincronización al confirmar selección
        if (openerBtn && selectedId)
          openerBtn.setAttribute("data-account-id", selectedId);
        var root = document.getElementById("mt-account-overview");
        if (root && selectedId)
          root.setAttribute("data-account-id", selectedId);

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

            var orderId =
              parseInt(active.getAttribute("data-order-id") || "0", 10) || 0;
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
                  hasSubscription:
                    (active.getAttribute("data-has-subscription") || "") ===
                    "1",
                  subscriptionId:
                    active.getAttribute("data-subscription-id") || "",
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

    // ===== Exportar helpers (opcional, por si un día necesitas llamarlos desde otro inline) =====
    window.mtPicker = {
      normalizeStatus: normalizeStatus,
      statusToBucket: statusToBucket,
      applyFilter: applyFilter,
      forceFilter: forceFilter,
      selectById: selectByIdAndSync,
    };

    log("picker init", { selectedId: selectedId });
  }
})();

// ==== Focus + inert guard para el modal del picker (sin romper lógica) ====
(function () {
  var modal = document.getElementById("changeSubcriptionModal");
  if (!modal) return;

  // recordamos el botón que abre el modal para devolverle el foco al cerrar
  var opener = null;
  document.addEventListener(
    "click",
    function (e) {
      var t =
        e.target && e.target.closest
          ? e.target.closest('[data-bs-target="#changeSubcriptionModal"]')
          : null;
      if (t) opener = t;
    },
    true
  );

  modal.addEventListener("show.bs.modal", function () {
    modal.removeAttribute("inert");
    modal.setAttribute("aria-hidden", "false");
  });

  document.addEventListener(
    "click",
    function (e) {
      var closeBtn =
        e.target && e.target.closest
          ? e.target.closest(
              '#changeSubcriptionModal [data-bs-dismiss="modal"], #changeSubcriptionModal .mt-modal__close'
            )
          : null;
      if (!closeBtn) return;
      (opener || document.body).focus({ preventScroll: true });
    },
    true
  );

  document.addEventListener(
    "keydown",
    function (e) {
      if (e.key !== "Escape") return;
      if (!modal.classList.contains("show")) return;
      (opener || document.body).focus({ preventScroll: true });
    },
    true
  );

  modal.addEventListener("hide.bs.modal", function () {
    var ae = document.activeElement;
    if (ae && modal.contains(ae)) {
      (opener || document.body).focus({ preventScroll: true });
    }
  });

  modal.addEventListener("hidden.bs.modal", function () {
    modal.setAttribute("inert", "");
  });
})();
