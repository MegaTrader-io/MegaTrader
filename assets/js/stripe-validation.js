jQuery(function ($) {
  console.log("🟣 Stripe script con observer corregido");

  const targetSelector = '.woocommerce-NoticeGroup.woocommerce-NoticeGroup-checkout';
  const insertBeforeSelector = '.page-banner-area.pt-32.pb-32';

  const observer = new MutationObserver(function (mutations) {
    mutations.forEach(function (mutation) {
      mutation.addedNodes.forEach(function (node) {
        if (node.nodeType === 1 && node.matches(targetSelector)) {

          const $notice = $(node);
          const $insertBefore = $(insertBeforeSelector);

          if ($insertBefore.length) {
            // ⚠️ Detener el observer antes de mover el nodo
            observer.disconnect();

            // Mover el nodo y mostrarlo
            $notice.hide().insertBefore($insertBefore).fadeIn(200);

            // 🧼 Ocultar preloader
            $(".preloader").fadeOut();

            // 🕒 Quitar el mensaje automáticamente después de 30 segundos con fade suave
            setTimeout(() => {
              $notice.fadeOut(800, function () {
                $(this).remove();
              });
            }, 30000); // 30 segundos

            // 🕒 Reactivar el observer después de un pequeño delay
            setTimeout(() => {
              observer.observe(document.body, {
                childList: true,
                subtree: true,
              });
            }, 500);
          }
        }
      });
    });
  });

  // Activar observer desde el inicio
  observer.observe(document.body, {
    childList: true,
    subtree: true,
  });
});
