(function (w, d) {
  const MTSuccessModal = {
    init(modalEl, opts) {
      if (!modalEl || modalEl.__mtBound) return;
      modalEl.__mtBound = true;

      const DEST =
        modalEl.getAttribute("data-overview-url") ||
        (opts && opts.overviewUrl) ||
        "/my-account/overview/";

      const dialogEl  = modalEl.querySelector(".modal-dialog");
      const contentEl = modalEl.querySelector(".modal-content");
      const btnGo     = modalEl.querySelector(".js-goto-account");

      let didRedirect = false;
      let wasEverVisible = false;

      console.log("[MT modal] init", { DEST });

      // ---------- utils visibilidad ----------
      const cs = (el) => (el ? w.getComputedStyle(el) : null);
      const hasZeroScale = (el) => {
        const st = cs(el); if (!st) return false;
        const t = st.transform; if (!t || t === "none") return false;
        const m2d = t.match(/matrix\(([-0-9.,\s]+)\)/);
        if (m2d) { const v = m2d[1].split(",").map(x => parseFloat(x.trim()));
          if (v.length >= 4 && (v[0] === 0 || v[3] === 0)) return true; }
        return /scale\(\s*0/.test(t);
      };
      const isHiddenBasic = (el) => {
        if (!el) return true;
        const s = cs(el); if (!s) return true;
        if (s.display === "none" || s.visibility === "hidden" || Number(s.opacity) === 0) return true;
        if (hasZeroScale(el)) return true;
        const r = el.getBoundingClientRect();
        if (r.width === 0 || r.height === 0) return true;
        const vw = w.innerWidth || d.documentElement.clientWidth;
        const vh = w.innerHeight || d.documentElement.clientHeight;
        return (r.bottom < 0 || r.top > vh || r.right < 0 || r.left > vw);
      };
      const isModalTrulyVisible = () => {
        const rootShown = modalEl.classList.contains("show");
        return rootShown && !isHiddenBasic(modalEl) && !isHiddenBasic(dialogEl) && !isHiddenBasic(contentEl);
      };

      const hardHide = () => {
        modalEl.classList.remove("show");
        modalEl.style.display = "none";
        modalEl.setAttribute("aria-hidden", "true");
        if (dialogEl) dialogEl.style.display = "none";
      };

      const go = () => {
        console.log("[MT modal] go() → preloader ON + closing", {
          wasEverVisible,
          isShown: modalEl.classList.contains("show"),
        });
        if (didRedirect) return;
        didRedirect = true;

        try { obsModal && obsModal.disconnect(); } catch (_) {}
        try { obsBody  && obsBody.disconnect();  } catch (_) {}

        try { w.showSitePreloader && w.showSitePreloader(); } catch (_) {}
        try { w.wcBlockCheckout   && w.wcBlockCheckout();   } catch (_) {}

        if (w.bootstrap && typeof w.bootstrap.Modal === "function") {
          const inst = w.bootstrap.Modal.getOrCreateInstance(modalEl);
          if (!modalEl.classList.contains("show")) {
            hardHide();
            return w.location.assign(DEST);
          }
          modalEl.addEventListener("hidden.bs.modal", () => {
            console.log("[MT modal] HIDDEN event → redirect");
            w.location.assign(DEST);
          }, { once: true });
          inst.hide();
        } else {
          hardHide();
          w.location.assign(DEST);
        }
      };

      // ---------- Inicializar y mostrar ----------
      try {
        if (w.bootstrap && typeof w.bootstrap.Modal === "function") {
          const instance = w.bootstrap.Modal.getOrCreateInstance(modalEl, {
            backdrop: "static",
            keyboard: false,
          });

          modalEl.addEventListener("shown.bs.modal", () => {
            wasEverVisible = true;
            console.log("[MT modal] SHOWN event");
          }, { once: true });

          modalEl.addEventListener("hidden.bs.modal", () => {
            if (wasEverVisible) go();
          });

          modalEl.addEventListener("hidePrevented.bs.modal", () => {
            console.log("[MT modal] hidePrevented → go()");
            if (wasEverVisible) go();
          });

          console.log("[MT modal] SHOW called");
          instance.show();
        } else {
          // Fallback sin Bootstrap
          modalEl.classList.add("show");
          modalEl.style.display = "block";
          modalEl.removeAttribute("aria-hidden");
          if (dialogEl) dialogEl.style.display = "";
          wasEverVisible = true;
          console.log("[MT modal] SHOW fallback (no Bootstrap)");
        }
      } catch (_) {}

      // ---------- CTA ----------
      if (btnGo) {
        btnGo.addEventListener("click", (ev) => {
          ev.preventDefault();
          go();
        });
      }

      // ---------- Click fuera / ESC ----------
      d.addEventListener("mousedown", (ev) => {
        if (!wasEverVisible || didRedirect) return;
        if (!dialogEl) return;
        const path = ev.composedPath ? ev.composedPath() : [];
        const inside = dialogEl.contains(ev.target) || path.includes(dialogEl);
        const anyBackdrop = !!d.querySelector(".modal-backdrop");
        if (!inside && anyBackdrop) go();
      }, true);

      d.addEventListener("keydown", (ev) => {
        if (!wasEverVisible || didRedirect) return;
        if (ev.key === "Escape" || ev.key === "Esc") go();
      }, true);

      // ---------- Observers (sin spam) ----------
      let obsModal;
      try {
        obsModal = new MutationObserver(() => {
          // Ya no logueamos aquí; y solo observamos el propio modal (sin subtree)
          if (!wasEverVisible || didRedirect) return;
          // Si de repente deja de ser visible (raro), redirecciona
          if (!isModalTrulyVisible()) go();
        });
        obsModal.observe(modalEl, {
          attributes: true,
          attributeFilter: ["class", "style", "aria-hidden"]
          // subtree: false
        });
      } catch (_) {}

      let obsBody;
      try {
        obsBody = new MutationObserver((mutations) => {
          if (!wasEverVisible || didRedirect) return;
          // Solo reaccionamos si EL MODAL fue removido del DOM
          for (const m of mutations) {
            if (m.type === "childList" && m.removedNodes && m.removedNodes.length) {
              for (const n of m.removedNodes) {
                if (n === modalEl || (n.contains && n.contains(modalEl))) {
                  return go();
                }
              }
            }
          }
        });
        // Observa SOLO cambios directos en children de <body>, sin subtree
        obsBody.observe(d.body, { childList: true });
      } catch (_) {}

      // Sin intervalos; solo eventos “reales”
      try { w.addEventListener("resize",  () => { if (!wasEverVisible || didRedirect) return; if (!isModalTrulyVisible()) go(); }, { passive: true }); } catch(_) {}
      try { w.addEventListener("scroll",  () => { if (!wasEverVisible || didRedirect) return; if (!isModalTrulyVisible()) go(); }, { passive: true }); } catch(_) {}

      // ---------- Copiar número de orden ----------
      (function setupCopyChip() {
        const chip = d.getElementById("order-copy-chip") || modalEl.querySelector(".order-chip");
        if (!chip) return;
        const label = chip.querySelector(".order-chip-label");

        const findValue = () => {
          if (chip.dataset.order) return chip.dataset.order.trim();
          const node = chip.querySelector("[data-order-value], .order-chip-value");
          if (node && node.textContent) return node.textContent.trim();
          const walker = d.createTreeWalker(chip, NodeFilter.SHOW_TEXT, null);
          let txt, best = "";
          while ((txt = walker.nextNode())) {
            const t = (txt.nodeValue || "").trim();
            if (t && !/^order:?$/i.test(t) && /[A-Za-z0-9]/.test(t)) { best = t; break; }
          }
          return best;
        };

        function afterCopy() {
          if (!label) return;
          const original = label.textContent;
          label.textContent = "Copied!";
          chip.classList.add("copied");
          setTimeout(() => {
            label.textContent = original;
            chip.classList.remove("copied");
          }, 1500);
        }

        function copyOrder() {
          const value = findValue();
          if (!value) return;
          if (navigator.clipboard && w.isSecureContext) {
            navigator.clipboard.writeText(value).then(afterCopy, afterCopy);
          } else {
            const ta = d.createElement("textarea");
            ta.value = value;
            ta.style.position = "fixed";
            ta.style.left = "-9999px";
            d.body.appendChild(ta);
            ta.focus(); ta.select();
            try { d.execCommand("copy"); } catch {}
            d.body.removeChild(ta);
            afterCopy();
          }
        }

        chip.addEventListener("click", copyOrder);
        chip.addEventListener("keydown", (e) => {
          if (e.key === "Enter" || e.key === " ") { e.preventDefault(); copyOrder(); }
        });
      })();
    },
  };

  w.MTSuccessModal = MTSuccessModal;
})(window, document);
