// coupon-message-handler.js
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
      $c = $(
        '<div class="coupon_text" role="status" aria-live="polite" style="display:none;"></div>'
      );
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
    $c.stop(true, true)
      .css("opacity", 0)
      .hide()
      .text("")
      .removeClass("invalid-text sucefull-text");
    if ($input.length) $input.removeClass("invalid_coupon");
  }

  function persist(text, type) {
    lastNotice = {
      text: String(text || "").trim(),
      type: type === "error" ? "error" : "success",
      expireAt: Date.now() + COUPON_TTL,
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

    const raw = String(message || "").trim();
    const txt = $("<textarea/>").html(raw).text(); // decodifica &quot; &amp; etc.

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

  function isGenericRemoved(text) {
    const t = String(text || "")
      .trim()
      .toLowerCase();
    return t === "coupon has been removed." || t === "coupon has been removed";
  }

  function isErrorMessage(text) {
    const t = (text || "").toLowerCase();
    return (
      t.includes("does not exist") ||
      t.includes("is not valid") ||
      t.includes("has expired") ||
      t.includes("already applied") ||
      t.includes("not apply to your cart") ||
      t.includes("requires") ||
      t.includes("please enter a coupon code") ||
      t.includes("invalid") ||
      ((t.includes("usage limit") || t.includes("maximum usage")) &&
        t.includes("has been reached"))
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

            if (!isCouponRelated(low)) return;

            // ✅ REMOVE: deja solo TU mensaje (verde) y mata el de Woo
            if (currentAction === "remove") {
              $(node).hide();
              currentAction = null;
              return;
            }

            // APPLY / otros: usa notice real de Woo
            const isErr = isErrorMessage(low);
            showCouponMessage(raw, isErr ? "error" : "success");
            $(node).hide();
            currentAction = null;
          }
        });
      });
    });
    observer.observe(target, { childList: true, subtree: true });
  }

  // ---------- Click: aplicar cupón ----------
  $(document).on("click", '[name="apply_coupon"]', function () {
    currentAction = "apply";
    hideMessageNow();
  });

  // ---------- Click: remover cupón ----------
  $(document).on("click", ".woocommerce-remove-coupon", function () {
    currentAction = "remove";
    const code = $(this).data("coupon") || $(this).attr("data-coupon") || "";

    // ✅ Solo tu mensaje (verde) y NO marca input
    const fallback = code
      ? `Coupon "${code}" has been removed.`
      : `Coupon has been removed.`;
    showCouponMessage(fallback, "success");

    // intenta ocultar cualquier notice visible inmediato
    $(".woocommerce-message, .woocommerce-error").hide();
  });

  // ---------- Rehidratación tras refresh del checkout ----------
  const REHYDRATE = [
    "updated_checkout",
    "wc_fragments_loaded",
    "updated_cart_totals",
    "applied_coupon_in_checkout",
    "removed_coupon_in_checkout",
  ];
  REHYDRATE.forEach((evt) => {
    $(document.body).on(evt, function () {
      renderWithRemaining();
    });
  });
});
