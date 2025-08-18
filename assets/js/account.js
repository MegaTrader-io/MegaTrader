// js/account.js

document.addEventListener('DOMContentLoaded', function () {
  // Utility to initialize modal behavior
  function initModal(modalSelector) {
    const modalBody    = document.querySelector(`${modalSelector} .modal-body`);
    const selectButton = document.querySelector(`${modalSelector} #select-subscription-btn`);

    if (!modalBody || !selectButton) return;

    /**
     * Enable or disable the Select button
     * based on whether a card is active.
     */
    function updateSelectButtonState() {
      const activeCard = modalBody.querySelector('.subscription-card.active');
      selectButton.classList.toggle('disabled', !activeCard);
      selectButton.style.opacity = activeCard ? '1' : '0.5';
      selectButton.style.cursor  = activeCard ? 'pointer' : 'not-allowed';
    }

    /**
     * Delegate clicks on any .subscription-card inside the modal.
     * Only cards that are currently visible respond.
     */
    modalBody.addEventListener('click', function (e) {
      const card = e.target.closest('.subscription-card');
      if (!card || card.offsetParent === null) return;

      // Clear previous selection and mark this one
      modalBody.querySelectorAll('.subscription-card.active')
               .forEach(c => c.classList.remove('active'));
      card.classList.add('active');
      updateSelectButtonState();
    });

    /**
     * When Select is clicked, navigate to the chosen item's detail view.
     * Checks prefix to build correct URL.
     */
    selectButton.addEventListener('click', () => {
      if (selectButton.classList.contains('disabled')) return;

      const activeCard = modalBody.querySelector('.subscription-card.active');
      if (activeCard) {
        const dataId = activeCard.getAttribute('data-id');
        if (!dataId) return;

        let url = '';
        if (dataId.startsWith('sub-')) {
          const subId = dataId.replace('sub-', '');
          url = `${window.location.origin}${wc_account_base_path()}view-subscription/${subId}/`;
        } else if (dataId.startsWith('order-')) {
          const orderId = dataId.replace('order-', '');
          url = `${window.location.origin}${wc_account_base_path()}view-order/${orderId}/`;
        }

        if (url) {
          window.location.href = url;
        }
      }
    });

    /**
     * Select card by data-id on modal load, based on URL
     */
    function selectCardById(modalBody, selectedId) {
      if (!selectedId) return;
      const card = modalBody.querySelector(`.subscription-card[data-id="${selectedId}"]`);
      if (card) {
        // Select the card
        modalBody.querySelectorAll('.subscription-card.active').forEach(c => c.classList.remove('active'));
        card.classList.add('active');
      }
    }

    // Initialize button state on load
    updateSelectButtonState();

    // Select card based on URL param passed from PHP
    selectCardById(modalBody, window.selectedSubscriptionId);
    updateSelectButtonState();
  }

  /**
   * Helper to get WooCommerce account base path (eg: '/my-account/')
   */
  function wc_account_base_path() {
    // Try to detect the base path from current URL, fallback to '/my-account/'
    const match = window.location.pathname.match(/\/my-account\//);
    return match ? '/my-account/' : '/my-account/';
  }

  // Initialize the subscription modal
  initModal('#changeSubcriptionModal');

  
});
