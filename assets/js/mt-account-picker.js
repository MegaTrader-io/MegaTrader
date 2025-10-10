/* mt-account-picker.js — usa solo $('.preloader') del theme */
(function () {
  "use strict";

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

    var selectedId = CFG.currentId || null;
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

      enableBtn(true);
      updateAccountCTAs(); // sincroniza CTAs con la card activa
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
        // refresca data-* en el contenedor del modal
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
        // refresca data-* en el contenedor del modal
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

        // si tu overview expone sincronizador extra, respétalo
        if (typeof window.__mtPassedSyncUI === "function") {
          window.__mtPassedSyncUI(passedModal);
        }
      }
    }

    // ========= Eventos UI =========
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

    // Re-sincronizar cuando se abre el modal
    if (modal) {
      modal.addEventListener("shown.bs.modal", function () {
        // Asegura que la card activa se marque y CTAs/btn queden bien
        var active =
          grid &&
          grid.querySelector((SEL.card || ".subscription-card") + ".active");
        if (active) setActiveCard(active);
        else enableBtn(false);
      });
    }

    ["mt-breach-alert-modal", "mt-account-passed-modal"].forEach(function (id) {
      var m = document.getElementById(id);
      if (m) {
        m.addEventListener("show.bs.modal", function () {
          updateAccountCTAs();
        });
      }
    });

    // Init
    preselectIfVisible();
    log("picker init", { selectedId: selectedId });
  }
})();
