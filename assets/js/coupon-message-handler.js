jQuery(document).ready(function ($) {
  const COUPON_TTL = 30000; // 30s

  let couponTimeoutId = null;
  let lastNotice = null; // { text, type, expireAt }
  let currentAction = null; // 'apply' | 'remove' | null

  // ---------- DOM helpers ----------
  function getCouponForm() {
    return $(".coupon-form").first();
  }
  function getFormGroup() {
    return getCouponForm().find(".form-group").first();
  }
  function getCouponInput() {
    const $g = getFormGroup();
    return $g.find('input[name="coupon_code"], #coupon_code').first();
  }
  // Crea/asegura un contenedor fijo para evitar "brinquito"
  function ensureContainer() {
    const $form = getCouponForm();
    const $g = getFormGroup();
    if (!$form.length || !$g.length) return $();

    let $c = $form.children(".coupon_text").first();
    if (!$c.length) {
      $c = $('<div class="coupon_text" role="status" aria-live="polite" style="display:none;"></div>');
      $g.after($c);
    }
    return $c;
  }

  function clearTimer() {
    if (couponTimeoutId) {
      clearTimeout(couponTimeoutId);
      couponTimeoutId = null;
    }
  }

  function hideMessageNow() {
    clearTimer();
    const $input = getCouponInput();
    const $c = ensureContainer();
    $c.stop(true, true).css("opacity", 0).hide().text("").removeClass("invalid-text sucefull-text");
    if ($input.length) $input.removeClass("invalid_coupon");
  }

  function persist(text, type) {
    lastNotice = {
      text: String(text || "").trim(),
      type: type === "error" ? "error" : "success",
      expireAt: Date.now() + COUPON_TTL
    };
  }

  function remainingMs() {
    if (!lastNotice) return 0;
    return Math.max(0, lastNotice.expireAt - Date.now());
  }

  function renderWithRemaining() {
    if (!lastNotice) return;
    const left = remainingMs();
    if (left <= 0) {
      lastNotice = null;
      return;
    }
    renderMessage(lastNotice.text, lastNotice.type, left);
  }

  // Render no intrusivo: usa el contenedor persistente y solo anima opacidad
  function renderMessage(message, type = "success", timeout = COUPON_TTL) {
    const $c = ensureContainer();
    const $input = getCouponInput();
    if (!$c.length) return;

    clearTimer();

    const txt = String(message || "").trim();

    // Estado visual input
    if (type === "error") $input.addClass("invalid_coupon");
    else $input.removeClass("invalid_coupon");

    // Variante de color (sin reinsertar nodos)
    $c.removeClass("invalid-text sucefull-text")
      .addClass(type === "error" ? "invalid-text" : "sucefull-text")
      .text(txt);

    // Evita "brinquito": solo fade
    $c.stop(true, true).show().animate({ opacity: 1 }, 120);

    couponTimeoutId = setTimeout(() => {
      $c.stop(true, true).animate({ opacity: 0 }, 120, function () {
        $(this).hide().text("").removeClass("invalid-text sucefull-text");
      });
      if (type === "error") $input.removeClass("invalid_coupon");
      couponTimeoutId = null;
      lastNotice = null;
    }, timeout);
  }

  function showCouponMessage(message, type = "success") {
    // Reemplazo inmediato del mensaje activo
    hideMessageNow();
    persist(message, type);
    renderMessage(message, type, COUPON_TTL);
  }

  // ---------- Clasificación de mensajes ----------
  function isCouponRelated(text) {
    const t = (text || "").toLowerCase();
    return t.includes("coupon") || t.includes("discount");
  }
  function isErrorMessage(text) {
    const t = (text || "").toLowerCase();
    return (
      t.includes("removed") ||
      t.includes("does not exist") ||
      t.includes("is not valid") ||
      t.includes("has expired") ||
      t.includes("already applied") ||
      t.includes("not apply to your cart") ||
      t.includes("requires") ||
      t.includes("please enter a coupon code") ||
      t.includes("invalid") ||
      ((t.includes("usage limit") || t.includes("maximum usage")) && t.includes("has been reached"))
    );
  }

  // ---------- Observer de notices Woo ----------
  const target = document.querySelector(".woocommerce");
  if (target) {
    const observer = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        mutation.addedNodes.forEach(function (node) {
          if (
            node.nodeType === 1 &&
            (node.classList.contains("woocommerce-message") ||
             node.classList.contains("woocommerce-error"))
          ) {
            const raw = $(node).text().trim();
            const low = raw.toLowerCase();

            if (isCouponRelated(low)) {
              // Si venimos de "remove", forzamos estilo error y reemplazamos al instante
              const isErr = currentAction === "remove" ? true : isErrorMessage(low);
              showCouponMessage(raw, isErr ? "error" : "success");
              $(node).hide();
              currentAction = null;
            }
          }
        });
      });
    });
    observer.observe(target, { childList: true, subtree: true });
  }

  // ---------- Click: aplicar cupón ----------
  $(document).on("click", '[name="apply_coupon"]', function () {
    currentAction = "apply";
    // Reemplazo inmediato del mensaje anterior
    hideMessageNow();

    // Poll corto por si el notice se retrasa
    let attempts = 0;
    const maxAttempts = 30;
    const iv = setInterval(() => {
      const $msg = $(".woocommerce-message, .woocommerce-error").first();
      const raw = $msg.text().trim();
      const low = raw.toLowerCase();

      if ($msg.length && isCouponRelated(low)) {
        clearInterval(iv);
        const isErr = isErrorMessage(low);
        showCouponMessage(raw, isErr ? "error" : "success");
        $msg.hide();
        currentAction = null;
      }

      attempts++;
      if (attempts >= maxAttempts) clearInterval(iv);
    }, 100);
  });

  // ---------- Click: remover cupón ----------
  $(document).on("click", ".woocommerce-remove-coupon", function () {
    currentAction = "remove";
    const code = $(this).data("coupon") || $(this).attr("data-coupon") || "";

    // Reemplaza de inmediato cualquier mensaje previo y muestra fallback de removido (error)
    const fallback = code ? `Coupon "${code}" has been removed.` : `Coupon has been removed.`;
    showCouponMessage(fallback, "error");

    // Poll para capturar el notice real si Woo lo pinta
    let attempts = 0;
    const maxAttempts = 30;
    const iv = setInterval(() => {
      const $msg = $(".woocommerce-message, .woocommerce-error").first();
      const raw = $msg.text().trim();
      const low = raw.toLowerCase();

      if ($msg.length && isCouponRelated(low)) {
        clearInterval(iv);
        // Forzamos error cuando es remove, y reemplazamos el fallback por el texto real
        showCouponMessage(raw, "error");
        $msg.hide();
        currentAction = null;
      }

      attempts++;
      if (attempts >= maxAttempts) clearInterval(iv);
    }, 100);
  });

  // ---------- Rehidratación tras refresh del checkout ----------
  const REHYDRATE = [
    "updated_checkout",
    "wc_fragments_loaded",
    "updated_cart_totals",
    "applied_coupon_in_checkout",
    "removed_coupon_in_checkout"
  ];
  REHYDRATE.forEach(evt => {
    $(document.body).on(evt, function (_e, maybeCode) {
      // Si Woo anuncia removed y tenemos código, refuerza el mensaje como error
      if (evt === "removed_coupon_in_checkout" && maybeCode) {
        showCouponMessage(`Coupon "${maybeCode}" has been removed.`, "error");
      } else {
        renderWithRemaining();
      }
    });
  });
});
