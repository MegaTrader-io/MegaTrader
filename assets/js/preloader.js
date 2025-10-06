jQuery(document).ready(function ($) {
  const preloader = $(".preloader");

  $.preloader = {
      show: function () {
          preloader.fadeIn(150);
      },
      hide: function () {
          preloader.fadeOut(150);
      },
      toggle: function () {
          preloader.fadeToggle(150);
      },
      isVisible: function () {
          return preloader.is(':visible');
      }
  }

  $(document).on("click", "a", function (e) {
    const href = $(this).attr("href");
    if (
      !href ||
      href.startsWith("#") ||
      href.startsWith("javascript:") ||
      href.includes("mailto:") ||
      href.includes("tel:")
    )
      return;

    const isInternal = href.startsWith(location.origin) || href.startsWith("/");
    if (isInternal && preloader.length) {
      console.log("🔗 Mostrando preloader (enlace interno):", href);
      preloader.fadeIn(150);
    }
  });

  $("form").on("submit", function (e) {
    if (preloader.length) {
      console.log("📤 Mostrando preloader (formulario enviado)");
      preloader.fadeIn(150);

    // Verificar errores 2 segundos después
    setTimeout(() => {
        const visibleErrors = $('.woocommerce-error, .woocommerce-invalid, .invalid-feedback:visible').length > 0;
        const isInvalidCard = $('form').text().toLowerCase().includes('invalid card');

        if (visibleErrors || isInvalidCard) {
            console.log('🛑 Error detectado (tarjeta u otro) → ocultando preloader');
            preloader.fadeOut(200);
        }
    }, 2000);
    }
  });

  $(document).on("click", ".open-email-modal-btn", function (e) {
    e.preventDefault();
    if (preloader.length) {
      console.log("👆 Click en .open-email-modal-btn");
      preloader.fadeIn(150, function () {
        setTimeout(() => {
          $("#emailModal").modal("show");
        }, 1000);
      });
    }
  });

  $("#emailModal").on("shown.bs.modal", function () {
    console.log("📩 Modal #emailModal mostrado → ocultando preloader");
    preloader.fadeOut(150);
  });

// WooCommerce: Mostrar preloader al hacer clic en #place_order
// $(document).on('click', '#place_order', function () {
//     console.log('🛒 Click en botón PLACE ORDER');
//     preloader.fadeIn(150);

//     // Verificar errores 2 segundos después
//     setTimeout(() => {
//         const visibleErrors = $('.woocommerce-error, .woocommerce-invalid, .invalid-feedback:visible').length > 0;
//         const isInvalidCard = $('form').text().toLowerCase().includes('invalid card');

//         if (visibleErrors || isInvalidCard) {
//             console.log('🛑 Error detectado (tarjeta u otro) → ocultando preloader');
//             preloader.fadeOut(200);
//         }
//     }, 2000);
// });


  $(document.body).on("checkout_error", function () {
    console.log("✅ Evento checkout_error detectado");
    preloader.fadeOut(150);
  });

  $(document.body).on("checkout_place_order_errored", function () {
    console.log("✅ Evento checkout_place_order_errored detectado");
    preloader.fadeOut(150);
  });

  $(document).on("click", ".apply-btn", function () {
    console.log("🏷️ Click en .apply-btn → mostrando preloader");
    preloader.fadeIn(150, function () {
      setTimeout(() => {
        preloader.fadeOut(150);
        console.log("🏷️ Delay Apply Coupon → ocultando preloader");
      }, 1000);
    });
  });

  $(document).on("click", ".woocommerce-remove-coupon", function () {
    console.log("❌ Click en [Remove Coupon] → mostrando preloader");
    preloader.fadeIn(150, function () {
      setTimeout(() => {
        preloader.fadeOut(150);
        console.log("❌ Delay Remove Coupon → ocultando preloader");
      }, 1000);
    });
  });

 setTimeout(() => {
    const hasErrors = $('.woocommerce-error, .woocommerce-invalid, .invalid-feedback:visible').length > 0;
    const isInvalidCard = $('form').text().toLowerCase().includes('invalid card');

    if (preloader.is(':visible')) {
        console.log('⚠️ Fallback: preloader aún visible...');

        if (hasErrors || isInvalidCard) {
            console.log('🚫 Errores detectados en fallback → ocultando preloader');
        } else {
            console.log('⏳ Sin errores, ocultando por seguridad');
        }

        preloader.fadeOut(200);
    }
}, 7000);

  const wooObserver = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      mutation.addedNodes.forEach((node) => {
        if (
          node.nodeType === 1 &&
          (node.classList.contains("woocommerce-error") ||
            node.classList.contains("woocommerce-invalid"))
        ) {
          console.log(
            "🛑 WooCommerce insertó error en DOM:",
            node.textContent.trim()
          );
        }
      });
    });
  });

  const wooTarget = document.querySelector(".woocommerce");
  if (wooTarget) {
    wooObserver.observe(wooTarget, { childList: true, subtree: true });
  }

    // Preloader para cambios en los Add-ons (activar/desactivar)
  const addonsContainer = document.querySelector('.single-checkout-widget.checkout-addons');

  if (addonsContainer) {
    const addonObserver = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        if (
          mutation.type === 'attributes' &&
          mutation.attributeName === 'class' &&
          mutation.target.classList.contains('addons-item')
        ) {
          console.log("🔄 Cambio en .addons-item:", mutation.target);
          console.log("📦 Ejecutando preloader por cambio en clase active");
          preloader.fadeIn(150);

          setTimeout(() => {
            preloader.fadeOut(150);
            console.log("⏳ Preloader finalizado para .addons-item");
          }, 1000);
        }
      });
    });

    const addonItems = addonsContainer.querySelectorAll('.addons-item');
    addonItems.forEach((item) => {
      addonObserver.observe(item, { attributes: true, attributeFilter: ['class'] });
    });
  }

  
});
