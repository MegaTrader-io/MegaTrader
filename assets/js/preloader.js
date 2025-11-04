jQuery(function ($) {
  const preloader = $(".preloader");
  const MT_DEBUG = true; // pon true si quieres logs

  // Helpers
  function show() {
    preloader.length && preloader.stop(true, true).show();
  }
  function hide() {
    preloader.length && preloader.stop(true, true).hide();
  }
  function log(...a) {
    if (MT_DEBUG) console.log.apply(console, a);
  }

  // 1) Oculta rápido en primera carga (evita “long-task” visual)
  document.addEventListener("DOMContentLoaded", () => hide(), { once: true });

  // 2) Enlaces: sólo internos, sin target _blank ni modificadores
  $(document).on("click", "a", function (e) {
    const a = this;
    const href = a.getAttribute("href") || "";
    if (
      !href ||
      href.startsWith("#") ||
      href.startsWith("javascript:") ||
      href.startsWith("mailto:") ||
      href.startsWith("tel:")
    )
      return;

    // click principal sin Ctrl/Cmd/Alt/Shift y sin target=_blank
    const isPlainClick =
      e.which === 1 && !e.metaKey && !e.ctrlKey && !e.altKey && !e.shiftKey;
    const isInternal = href.startsWith(location.origin) || href.startsWith("/");
    const isNewTab = a.target && a.target === "_blank";

    if (isPlainClick && isInternal && !isNewTab) {
      show(); // no animación costosa antes de unload
      log("🔗 preloader (link):", href);
    }
  });

  // 3) Formularios: limita el alcance (nada de $('form').text())
  $(document).on("submit", "form", function () {
    show();
    const $form = $(this);

    // Revisión ligera de errores sólo en ese form, no en todo el DOM
    setTimeout(() => {
      const hasErrors =
        $form.find(
          ".woocommerce-error, .woocommerce-invalid, .invalid-feedback:visible"
        ).length > 0;
      if (hasErrors) {
        hide();
        log("🛑 errores detectados en form → hide");
      }
    }, 1500);
  });

  // 4) Eventos Woo conocidos → hide
  $(document.body).on("checkout_error checkout_place_order_errored", hide);

  // 5) Modals propios: si abres un modal, no dejes el overlay activo
  $(document).on("shown.bs.modal hidden.bs.modal", "#emailModal", hide);

  // 6) Cupón: animación corta, sin trabajo extra
  $(document).on(
    "click",
    ".apply-btn, .woocommerce-remove-coupon",
    function () {
      show();
      setTimeout(hide, 800);
    }
  );

  // 7) Fallback: sólo si sigue visible y NO hay errores en pantalla
    if (!preloader.is(":visible")) return;
    const hasAnyErrors =
        $(".woocommerce-error:visible, .invalid-feedback:visible").length > 0;
    if (!hasAnyErrors) {
        hide();
        log("⏳ fallback → hide");
    }

  // 8) Observers SOLO donde toca (evita trabajo en overview)
  // Woo errors dinámicos (ligero)
  const wooRoot = document.querySelector(".woocommerce");
  if (wooRoot && document.body.classList.contains("woocommerce-checkout")) {
    const wooObserver = new MutationObserver((list) => {
      for (const m of list) {
        for (const n of m.addedNodes) {
          if (
            n.nodeType === 1 &&
            (n.classList.contains("woocommerce-error") ||
              n.classList.contains("woocommerce-invalid"))
          ) {
            log(
              "🛑 Woo error en DOM:",
              (n.textContent || "").trim().slice(0, 140)
            );
          }
        }
      }
    });
    wooObserver.observe(wooRoot, { childList: true, subtree: true });
    document.addEventListener(
      "visibilitychange",
      () => {
        if (document.hidden) wooObserver.disconnect();
      },
      { once: true }
    );
    window.addEventListener("beforeunload", () => wooObserver.disconnect(), {
      once: true,
    });
  }

  // Add-ons: sólo observa si existe (checkout), no en overview
  const addons = document.querySelector(
    ".single-checkout-widget.checkout-addons"
  );
  if (addons && document.body.classList.contains("woocommerce-checkout")) {
    const addonObserver = new MutationObserver((list) => {
      let touched = false;
      for (const m of list) {
        if (
          m.type === "attributes" &&
          m.attributeName === "class" &&
          m.target instanceof Element &&
          m.target.classList.contains("addons-item")
        ) {
          touched = true;
          break;
        }
      }
      if (touched) {
        show();
        setTimeout(hide, 800);
      }
    });
    addons.querySelectorAll(".addons-item").forEach((el) => {
      addonObserver.observe(el, {
        attributes: true,
        attributeFilter: ["class"],
      });
    });
    document.addEventListener(
      "visibilitychange",
      () => {
        if (document.hidden) addonObserver.disconnect();
      },
      { once: true }
    );
    window.addEventListener("beforeunload", () => addonObserver.disconnect(), {
      once: true,
    });
  }
});

