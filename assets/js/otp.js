jQuery(document).ready(function ($) {
  // Inicializar variables globales desde wp_localize_script
  window.wc_otp_nonce = wc_otp_data.nonce;
  window.ajaxurl = wc_otp_data.ajaxurl;
  let resendInterval = null;

  function showPreloader() {
    $(".preloader").css("display", "flex");
  }

  function hidePreloader() {
    $(".preloader").fadeOut();
  }

    function showMessage(message, modal = "#emailModal", type = "error") {
    const container = $(`${modal} .otp-message-container`);
    const errorBox = container.find(".error-otp-message");
    const successBox = container.find(".success-otp-message");

    if (!container.length) return;

    // 💣 Limpia clases anteriores antes de mostrar
    errorBox.removeClass("show");
    successBox.removeClass("show");

    container.removeClass("d-none").show();

    if (type === "success") {
      successBox.find(".success-otp-text").text(message.trim());
      successBox.addClass("show");
    } else {
      errorBox.find(".error-otp-text").text(message.trim());
      errorBox.addClass("show");
    }

    // Ocultar luego de 100s (puedes ajustar si quieres)
    setTimeout(() => {
      errorBox.removeClass("show");
      successBox.removeClass("show");
      container.addClass("d-none").hide();
    }, 10000);
  }

  $(".get-otp-btn").on("click", function (e) {
    e.preventDefault();
    const email = $('#emailModal input[name="username"]').val();

    if (!email) {
      return showMessage(
        "Please enter your email address",
        "#emailModal",
        "error"
      );
    }

    showPreloader();

    setTimeout(() => {
      $.post(
        window.ajaxurl,
        {
          action: "generate_otp",
          email: email,
          nonce: window.wc_otp_nonce,
        },
        function (response) {
          hidePreloader();
          if (response.success) {
            $("#otp-email-display").text(email);
            $('#otpModal input[name="username"]').val(email);
            $("#emailModal").modal("hide");
            $("#otpModal").modal("show");
          } else {
            showMessage(response.data, "#emailModal", "error");
          }
        }
      );
    }, 1500);
  });

  $(".verify-otp-btn").on("click", function (e) {
    e.preventDefault();
    const email = $('#otpModal input[name="username"]').val();

    const otp = $("#otpModal .otp-box")
      .map(function () {
        return $(this).val().trim();
      })
      .get()
      .join("");

    if (!otp || otp.length !== 6) {
      return showMessage(
        "Please enter the 6-digit OTP code",
        "#otpModal",
        "error"
      );
    }

    showPreloader();

    $.post(
      window.ajaxurl,
      {
        action: "verify_otp",
        email: email,
        otp: otp,
        nonce: window.wc_otp_nonce,
        redirect_url: window.location.href,
      },
      function (response) {
        hidePreloader();
        if (response.success && response.data.redirect) {
          sessionStorage.setItem("justLoggedIn", "true");
          window.location.href = response.data.redirect;
        } else {
          showMessage(response.data || "Invalid OTP", "#otpModal", "error");
        }
      }
    );
  });

  $(document).on("click", ".resend-otp", function (e) {
    e.preventDefault();

    const resendBtn = $(this);
    const email = $("#otp-email-display").text();

    if (!email) return;

    showMessage(
      "A new code has been sent to your email.",
      "#otpModal",
      "success"
    );

    if (resendInterval) {
      clearInterval(resendInterval);
    }

    resendBtn.addClass("otp-disabled").text("Resend in 60s");

    let countdown = 60;

    resendInterval = setInterval(() => {
      countdown--;
      if (countdown <= 0) {
        clearInterval(resendInterval);
        resendInterval = null;
        resendBtn.removeClass("otp-disabled").text("Resend OTP");

        $("#otpModal .otp-message-container").addClass("d-none").hide();
        $("#otpModal .error-otp-message").hide();
        $("#otpModal .success-otp-message").hide();
      } else {
        resendBtn.text(`Resend in ${countdown}s`);
      }
    }, 1000);

    $.post(
      window.ajaxurl,
      {
        action: "generate_otp",
        email: email,
        nonce: window.wc_otp_nonce,
      },
      function (response) {
        if (!response.success) {
          showMessage(response.data, "#otpModal", "error");
        }
      }
    );
  });

  $("#otpModal").on("shown.bs.modal", function () {
    $("#otpModal .otp-box").first().focus();
  });

  $(document).on("input", ".otp-box", function (e) {
    const input = $(this);
    const value = input.val();

    if (value.length > 1) {
      const digits = value.replace(/\D/g, "").slice(0, 6).split("");
      const inputs = $("#otpModal .otp-box");

      digits.forEach((digit, index) => {
        if (inputs[index]) {
          $(inputs[index]).val(digit);
        }
      });

      // Mueve el foco al último input rellenado
      if (digits.length === 6) {
        inputs.last().focus();
      } else if (digits.length > 0) {
        $(inputs[digits.length]).focus();
      }

      return;
    }

    if (value.length === 1) {
      input.next(".otp-box").focus();
    }
  });

  // Permite usar flechas para navegar y borrar hacia atrás
  $(document).on("keydown", ".otp-box", function (e) {
    const input = $(this);

    if (e.key === "Backspace" && !input.val()) {
      input.prev(".otp-box").focus();
    }

    if (e.key === "ArrowLeft") {
      input.prev(".otp-box").focus();
    }

    if (e.key === "ArrowRight") {
      input.next(".otp-box").focus();
    }
  });

  // Pegar un código completo en los inputs OTP
  $(document).on("paste", ".otp-box", function (e) {
    e.preventDefault();

    const clipboardData = (e.originalEvent || e).clipboardData.getData("text");
    const digits = clipboardData.replace(/\D/g, "").slice(0, 6).split("");

    const inputs = $("#otpModal .otp-box");

    inputs.each(function (index) {
      $(this).val(digits[index] || "");
    });

    if (digits.length > 0 && digits.length < 6) {
      inputs[digits.length].focus();
    } else {
      inputs.last().focus();
    }
  });

  $(document).on("click", ".back-otp-back", function () {
    $("#otpModal").modal("hide");
    setTimeout(() => {
      $("#emailModal").modal("show");
    }, 300);
  });

 let loginSuccessTimeoutId = null;

  function showLoginSuccessMessage(
    message = "You have successfully logged in to Megatrader."
  ) {
    const container = $("#loginSuccess");
    if (!container.length) return;

    const successBox = container.find(".success-otp-message");

    // Limpiar clases previas y timeout anterior si existe
    successBox.removeClass("show");

    if (loginSuccessTimeoutId) {
      clearTimeout(loginSuccessTimeoutId);
      loginSuccessTimeoutId = null;
    }

    // Actualizar el texto y mostrar
    successBox.find(".success-otp-text").text(message.trim());
    container.removeClass("d-none").fadeIn(200);
    successBox.addClass("show");

    // Ocultar después de 10 segundos
    loginSuccessTimeoutId = setTimeout(() => {
      successBox.removeClass("show");
      container.addClass("d-none").hide();
    }, 10000);
  }

  window.addEventListener("load", () => {
    if (sessionStorage.getItem("justLoggedIn") === "true") {
      showLoginSuccessMessage();
      sessionStorage.removeItem("justLoggedIn");
    }
  });

  // ----------------- COPY ORDER NUMBER (Thank You page) -----------------
(function initOrderCopyChip() {
  const COPY_FEEDBACK_MS = 2500;

  function ensureToastStyle() {
    if (window.__mtCopyToastStyle) return;
    const css = `
      #mt-copy-toast{
        position:fixed;
        left:0; top:0;
        transform:translate(-50%,-110%);
        background:#000;color:#A8A29E;padding:8px 12px;border-radius:8px;
        font-size:12px;line-height:1;z-index:9999;box-shadow:0 6px 20px rgba(0,0,0,.3);
        opacity:0;transition:opacity .18s ease;pointer-events:none;
        white-space:nowrap;
      }
      #mt-copy-toast.is-visible{opacity:1}
      .order-chip.is-copied{outline:2px solid rgba(168,162,158,.5)}
    `;
    const style = document.createElement("style");
    style.textContent = css;
    document.head.appendChild(style);
    window.__mtCopyToastStyle = true;
  }

  function showToast(text, anchorEl) {
    ensureToastStyle();
    let toast = document.getElementById("mt-copy-toast");
    if (!toast) {
      toast = document.createElement("div");
      toast.id = "mt-copy-toast";
      document.body.appendChild(toast);
    }
    toast.textContent = text || "Copied to clipboard";

    const rect =
      anchorEl && anchorEl.getBoundingClientRect
        ? anchorEl.getBoundingClientRect()
        : { left: window.innerWidth / 2, top: window.innerHeight - 24, width: 0 };

    const clamp = (n, min, max) => Math.max(min, Math.min(max, n));
    const x = clamp(rect.left + rect.width / 2, 16, window.innerWidth - 16);
    const y = clamp(rect.top - 8, 16, window.innerHeight - 16);

    toast.style.left = `${Math.round(x)}px`;
    toast.style.top = `${Math.round(y)}px`;

    toast.classList.add("is-visible");
    clearTimeout(window.__mtCopyToastTimer);
    window.__mtCopyToastTimer = setTimeout(() => {
      toast.classList.remove("is-visible");
    }, COPY_FEEDBACK_MS);
  }

  function copyText(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
      return navigator.clipboard.writeText(text);
    }
    return new Promise((resolve) => {
      const ta = document.createElement("textarea");
      ta.value = text;
      ta.setAttribute("readonly", "");
      ta.style.position = "absolute";
      ta.style.left = "-9999px";
      document.body.appendChild(ta);
      ta.select();
      try { document.execCommand("copy"); } catch (e) {}
      document.body.removeChild(ta);
      resolve();
    });
  }

  // Click handler (delegated)
  document.addEventListener("click", (e) => {
    const chip = e.target.closest(".order-chip[data-order]");
    if (!chip) return;

    const order = (chip.getAttribute("data-order") || "").trim();
    if (!order) return;

    copyText(order)
      .then(() => {
        chip.classList.add("is-copied");
        showToast(`Copied: ${order}`, chip);
        setTimeout(() => chip.classList.remove("is-copied"), 800);
      })
      .catch(() => {
        // Even if clipboard rejects, still give feedback (UX > silence)
        showToast("Copied to clipboard", chip);
      });
  });

  // Keyboard accessibility: Enter/Space
  document.addEventListener("keydown", (e) => {
    const chip = e.target && e.target.closest ? e.target.closest(".order-chip[data-order]") : null;
    if (!chip) return;

    const isEnter = e.key === "Enter";
    const isSpace = e.key === " " || e.key === "Spacebar";
    if (!isEnter && !isSpace) return;

    e.preventDefault();
    chip.click();
  });
})();

});
