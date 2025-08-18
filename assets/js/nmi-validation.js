jQuery(function ($) {
  function updateFocusState() {
    const active = document.activeElement;

    if (
      active &&
      active.tagName === "IFRAME" &&
      active.classList.contains("CollectJSInlineIframe")
    ) {
      $(".wc-nmi-elements-field").removeClass("is-focused");
      const $wrapper = $(active).closest(".wc-nmi-elements-field");
      if ($wrapper.length) {
        $wrapper.addClass("is-focused");
      }
    } else {
      $(".wc-nmi-elements-field").removeClass("is-focused");
    }
  }

  $(document).on("click", function () {
    setTimeout(updateFocusState, 50);
  });

  $(window).on("blur focus", function () {
    setTimeout(updateFocusState, 50);
  });

  function markInvalidField(messageText) {
    $(".wc-nmi-elements-field").removeClass("is-invalid");
    $(".invalid-feedback").remove();

    const msg = messageText.toLowerCase();
    let targetSelector = null;

    if (msg.includes("card number")) {
      targetSelector = "#nmi-card-number-element";
    } else if (msg.includes("expiry") || msg.includes("expiration")) {
      targetSelector = "#nmi-card-expiry-element";
    } else if (
      msg.includes("cvc") ||
      msg.includes("security code") ||
      msg.includes("card code")
    ) {
      targetSelector = "#nmi-card-cvc-element";
    }

    const $targets = targetSelector
      ? [$(targetSelector).closest(".wc-nmi-elements-field")]
      : $(".wc-nmi-elements-field");

    $targets.forEach(($field) => {
      $field.removeClass("is-focused").addClass("is-invalid");

      const $msgDiv = $(
        `<div class="invalid-feedback">${messageText.trim()}</div>`
      );
      $field.after($msgDiv);
    });
 
  }

  $(document).on("click", "#place_order", function () {
    console.log("🧾 Click en botón #place_order → revisando errores NMI...");

    setTimeout(() => {
      const container = document.querySelector(".nmi-source-errors");
      if (!container) {
        console.warn("⛔ .nmi-source-errors no encontrado");
        return;
      }

      const li = container.querySelector("li");
      if (li) {
        const message = li.textContent || "There was an error with your card.";
        console.log("💥 Error detectado:", message);

        container.style.setProperty("display", "none", "important");
        markInvalidField(message);
      } else {
        console.log("✅ No hay error visible en .nmi-source-errors");
      }
    }, 150);
  });
});
