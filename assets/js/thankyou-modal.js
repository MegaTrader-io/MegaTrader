(function (w, d) {
  const MTSuccessModal = {
    init(modalEl, opts) {
      if (!modalEl || modalEl.__mtBound) return;
      modalEl.__mtBound = true;

      // DESTINO por defecto (puedes setearlo en el HTML con data-overview-url)
      const DEST =
        modalEl.getAttribute("data-overview-url") ||
        (opts && opts.overviewUrl) ||
        "/my-account/overview/";

      const dialogEl = modalEl.querySelector(".modal-dialog");
      const contentEl = modalEl.querySelector(".modal-content");
      const btnGo = modalEl.querySelector(".js-goto-account");

      let didRedirect = false;
      let wasEverVisible = false;
      let tick = null;

      // ---------------- utilidades visibilidad ----------------
      const cs = (el) => (el ? w.getComputedStyle(el) : null);

      const hasZeroScale = (el) => {
        const st = cs(el);
        if (!st) return false;
        const t = st.transform;
        if (!t || t === "none") return false;
        const m2d = t.match(/matrix\(([-0-9.,\s]+)\)/);
        if (m2d) {
          const v = m2d[1].split(",").map((x) => parseFloat(x.trim()));
          if (v.length >= 4 && (v[0] === 0 || v[3] === 0)) return true;
        }
        if (/scale\(\s*0/.test(t)) return true;
        return false;
      };

      const isHiddenBasic = (el) => {
        if (!el) return true;
        const s = cs(el);
        if (!s) return true;
        if (
          s.display === "none" ||
          s.visibility === "hidden" ||
          Number(s.opacity) === 0
        )
          return true;
        if (hasZeroScale(el)) return true;
        const rect = el.getBoundingClientRect();
        if (rect.width === 0 || rect.height === 0) return true;
        const vw = w.innerWidth || d.documentElement.clientWidth;
        const vh = w.innerHeight || d.documentElement.clientHeight;
        if (
          rect.bottom < 0 ||
          rect.top > vh ||
          rect.right < 0 ||
          rect.left > vw
        )
          return true;
        return false;
      };

      const isModalTrulyVisible = () => {
        const rootShown = modalEl.classList.contains("show");
        return (
          rootShown &&
          !isHiddenBasic(modalEl) &&
          !isHiddenBasic(dialogEl) &&
          !isHiddenBasic(contentEl)
        );
      };

      const maybeRedirect = () => {
        if (!wasEverVisible || didRedirect) return;
        if (!d.body.contains(modalEl)) return go();
        if (!isModalTrulyVisible()) return go();
      };

      // ---------------- cerrar SIEMPRE antes de redirigir ----------------
      const hardHide = () => {
        // Fallback para asegurar ocultado visual
        modalEl.classList.remove("show");
        modalEl.style.display = "none";
        modalEl.setAttribute("aria-hidden", "true");
        if (dialogEl) dialogEl.style.display = "none";
      };

      const go = () => {
        if (didRedirect) return;
        didRedirect = true;
        try {
          obsModal.disconnect();
        } catch (e) {}
        try {
          obsBody.disconnect();
        } catch (e) {}
        if (tick) {
          clearInterval(tick);
          tick = null;
        }

        // 🔥 Preloader ON + overlay nativo ON
        try {
          w.showSitePreloader && w.showSitePreloader();
        } catch (_) {}
        try {
          w.wcBlockCheckout && w.wcBlockCheckout();
        } catch (_) {}

        if (w.bootstrap && typeof w.bootstrap.Modal === "function") {
          const inst = w.bootstrap.Modal.getOrCreateInstance(modalEl);
          if (!modalEl.classList.contains("show")) {
            // ya está oculto → no esperes animación
            modalEl.classList.remove("show");
            modalEl.style.display = "none";
            modalEl.setAttribute("aria-hidden", "true");
            if (dialogEl) dialogEl.style.display = "none";
            return w.location.assign(DEST);
          }
          modalEl.addEventListener(
            "hidden.bs.modal",
            () => {
              w.location.assign(DEST);
            },
            { once: true }
          );
          inst.hide();
        } else {
          // Fallback
          modalEl.classList.remove("show");
          modalEl.style.display = "none";
          modalEl.setAttribute("aria-hidden", "true");
          if (dialogEl) dialogEl.style.display = "none";
          w.location.assign(DEST);
        }
      };

      // ---------------- Inicializar y mostrar modal ----------------
      try {
        if (w.bootstrap && typeof w.bootstrap.Modal === "function") {
          const instance = w.bootstrap.Modal.getOrCreateInstance(modalEl, {
            backdrop: "static",
            keyboard: false,
          });

          modalEl.addEventListener(
            "shown.bs.modal",
            () => {
              wasEverVisible = true;
            },
            { once: true }
          );

          modalEl.addEventListener("hidden.bs.modal", () => {
            if (wasEverVisible) go();
          });

          modalEl.addEventListener("hidePrevented.bs.modal", () => {
            if (wasEverVisible) go();
          });

          instance.show();
        } else {
          // Fallback sin Bootstrap
          setTimeout(() => {
            modalEl.classList.add("show");
            modalEl.style.display = "block";
            modalEl.removeAttribute("aria-hidden");
            if (dialogEl) dialogEl.style.display = ""; // visible
            wasEverVisible = true;
          }, 0);
        }
      } catch (e) {}

      // ---------------- Botón "Go to my account" ----------------
      if (btnGo) {
        btnGo.addEventListener("click", (ev) => {
          ev.preventDefault();
          go();
        });
      }

      // ---------------- Click fuera (si backdrop se libera) ----------------
      d.addEventListener(
        "mousedown",
        (ev) => {
          if (!wasEverVisible || didRedirect) return;
          if (!dialogEl) return;
          const path = ev.composedPath ? ev.composedPath() : [];
          const inside =
            dialogEl.contains(ev.target) || path.includes(dialogEl);
          const anyBackdrop = !!d.querySelector(".modal-backdrop");
          if (!inside && anyBackdrop) go();
        },
        true
      );

      // ---------------- ESC (si alguien lo re-habilita) ----------------
      d.addEventListener(
        "keydown",
        (ev) => {
          if (!wasEverVisible || didRedirect) return;
          if (ev.key === "Escape" || ev.key === "Esc") go();
        },
        true
      );

      // ---------------- Observadores ----------------
      const obsModal = new MutationObserver(() => {
        if (!wasEverVisible || didRedirect) return;
        maybeRedirect();
      });
      obsModal.observe(modalEl, {
        attributes: true,
        attributeFilter: ["class", "style", "aria-hidden"],
        subtree: true, // detectar cambios en .modal-dialog/.modal-content
      });

      const obsBody = new MutationObserver(() => {
        if (!wasEverVisible || didRedirect) return;
        if (!d.getElementById("orderSuccessModal")) return go();
        maybeRedirect();
      });
      obsBody.observe(d.body, { childList: true, subtree: true });

      w.addEventListener("resize", maybeRedirect, { passive: true });
      w.addEventListener("scroll", maybeRedirect, { passive: true });
      tick = w.setInterval(maybeRedirect, 500); // red de seguridad

      // ---------------- Copiar número de orden ----------------
      (function setupCopyChip() {
        const chip =
          d.getElementById("order-copy-chip") ||
          modalEl.querySelector(".order-chip");
        if (!chip) return;
        const label = chip.querySelector(".order-chip-label");

        const findValue = () => {
          if (chip.dataset.order) return chip.dataset.order.trim();
          const node = chip.querySelector(
            "[data-order-value], .order-chip-value"
          );
          if (node && node.textContent) return node.textContent.trim();
          // fallback: primer texto alfanumérico dentro del chip
          const walker = d.createTreeWalker(chip, NodeFilter.SHOW_TEXT, null);
          let txt,
            best = "";
          while ((txt = walker.nextNode())) {
            const t = (txt.nodeValue || "").trim();
            if (t && !/^order:?$/i.test(t) && /[A-Za-z0-9]/.test(t)) {
              best = t;
              break;
            }
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
            ta.focus();
            ta.select();
            try {
              d.execCommand("copy");
            } catch {}
            d.body.removeChild(ta);
            afterCopy();
          }
        }

        chip.addEventListener("click", copyOrder);
        chip.addEventListener("keydown", (e) => {
          if (e.key === "Enter" || e.key === " ") {
            e.preventDefault();
            copyOrder();
          }
        });
      })();
    },
  };

  w.MTSuccessModal = MTSuccessModal;
})(window, document);
