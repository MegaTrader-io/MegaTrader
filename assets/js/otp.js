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

});
