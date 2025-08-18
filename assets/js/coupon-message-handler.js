jQuery(document).ready(function ($) {
  let couponTimeoutId = null;


  function showCouponMessage(message, type = "success") {
  const container = $(".coupon-message-container").first();
  if (!container.length) return;

  const errorBox = container.find(".error-otp-message");
  const successBox = container.find(".success-otp-message");

  errorBox.removeClass("show");
  successBox.removeClass("show");

  if (couponTimeoutId) {
    clearTimeout(couponTimeoutId);
    couponTimeoutId = null;
  }

  container.removeClass("d-none").fadeIn(200);

  if (type === "success") {
    successBox.find(".success-otp-text").text(message.trim());
    successBox.addClass("show");
  } else {
    errorBox.find(".error-otp-text").text(message.trim());
    errorBox.addClass("show");
  }

  couponTimeoutId = setTimeout(() => {
    errorBox.removeClass("show");
    successBox.removeClass("show");
    container.addClass("d-none").hide();
  }, 30000);
}


  function isCouponRelated(messageText) {
    return messageText.includes("coupon") || messageText.includes("discount");
  }

  function isErrorMessage(messageText) {
    return (
      messageText.includes("removed") ||
      messageText.includes("does not exist") ||
      messageText.includes("is not valid") ||
      messageText.includes("has expired") ||
      messageText.includes("already applied") ||
      messageText.includes("not apply to your cart") ||
      messageText.includes("requires") ||
      messageText.includes("please enter a coupon code")
    );
  }

  // 👀 MutationObserver para detectar mensajes WooCommerce
  const observer = new MutationObserver(function (mutations) {
    mutations.forEach(function (mutation) {
      mutation.addedNodes.forEach(function (node) {
        if (
          node.nodeType === 1 &&
          (node.classList.contains("woocommerce-message") ||
            node.classList.contains("woocommerce-error"))
        ) {
          const messageText = $(node).text().trim().toLowerCase();

          if (isCouponRelated(messageText)) {
            const isError = isErrorMessage(messageText);
            showCouponMessage(
              $(node).text().trim(),
              isError ? "error" : "success"
            );
            $(node).hide();
          }
        }
      });
    });
  });

  const target = document.querySelector(".woocommerce");
  if (target) {
    observer.observe(target, {
      childList: true,
      subtree: true,
    });
  }

  $(document).on("click", '[name="apply_coupon"]', function () {

    let attempts = 0;
    const maxAttempts = 30;

    const interval = setInterval(() => {
      const $msg = $(".woocommerce-message, .woocommerce-error").first();
      const msgText = $msg.text().trim().toLowerCase();

      if ($msg.length && isCouponRelated(msgText)) {
        clearInterval(interval);

        const isError = isErrorMessage(msgText);
        showCouponMessage($msg.text().trim(), isError ? "error" : "success");
        $msg.hide();
      }

      attempts++;
      if (attempts >= maxAttempts) {
        clearInterval(interval);
      }
    }, 100);
  });
});
