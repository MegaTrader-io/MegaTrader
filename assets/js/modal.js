window.MEGATRADER = window.MEGATRADER || {};

window.MEGATRADER.showModal = function (modalId) {
  const el = document.getElementById(modalId);
  if (!el) return;

  bootstrap.Modal.getOrCreateInstance(el).show();
};

function autoshow(modal){
  const autoShow = modal.getAttribute('data-autoshow');
    const modalId = modal.id;

    if (!modalId) return;

    if (autoShow === 'true') {
      window.MEGATRADER.showModal(modalId);
    } else {
      const delay = parseInt(autoShow, 10);
      if (!isNaN(delay)) {
        setTimeout(() => {
          window.MEGATRADER.showModal(modalId);
        }, delay * 1000);
      }
    }
}

function handleAutoShow(){
  const modals = document.querySelectorAll('.modal');

  modals.forEach(modal => {
    autoshow(modal);
  });
}

function handleModalWithNotice(){
  document.body.addEventListener('show.bs.modal', function (event) {
    const modal = event.target;
    if (modal.hasAttribute('data-notice')) {
      document.querySelectorAll('.woocommerce-notices-wrapper').forEach(wrapper => {
        if ( modal.contains(wrapper) ) return;

        wrapper.classList.remove('woocommerce-notices-wrapper');
        wrapper.classList.add('woocommerce-notices-wrapper-modal-open');
      });
    }
  });

  document.body.addEventListener('hidden.bs.modal', function (event) {
    document.querySelectorAll('.woocommerce-notices-wrapper-modal-open').forEach(wrapper => {
      wrapper.classList.remove('woocommerce-notices-wrapper-modal-open');
      wrapper.classList.add('woocommerce-notices-wrapper');
    });
  });
}


document.addEventListener('DOMContentLoaded', function () {
  handleModalWithNotice();
  handleAutoShow();
});



