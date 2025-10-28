<?php
defined('ABSPATH') || exit;

/* === Helpers === */
if (file_exists(get_stylesheet_directory() . '/inc/mt-accounts-helpers.php')) {
  require_once get_stylesheet_directory() . '/inc/mt-accounts-helpers.php';
}
/** @var array $args */
$user_email = isset($args['user_email']) ? sanitize_email($args['user_email']) : '';

/**
 * Modal: Request Payout
 * - data-user-email: lo toma el JS para pedir el payload al AJAX.
 * - data-checkout-base: ya lo vienes usando en otros modales.
 */
?>
<div id="mt-request-payout-modal"
     class="modal modal-subcription fade"
     tabindex="-1"
     aria-labelledby="mtpayout-title"
     aria-hidden="true"
     data-user-email="<?php echo esc_attr($user_email); ?>"
     data-checkout-base="<?php echo esc_url(function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : '/checkout'); ?>">

  <div class="modal-dialog modal-dialog-centered modal-fullscreen-md-down">
    <div class="modal-content gap-32">

      <!-- Header -->
      <div class="modal-header w-100 border-0 justify-content-between align-items-center p-0">
        <span id="mtpayout-title" class="modal-title text-white heading-sm-medium d-flex align-items-center gap-2 text-uppercase">
          Request Payout
        </span>
        <button type="button"
                class="p-0 border-0 bg-transparent shadow-none mt-modal__close"
                data-bs-dismiss="modal"
                aria-label="Close">
          <span aria-hidden="true">
            <img src="/wp-content/uploads/2025/05/cancel-circle-1.png" alt="Close" style="width:24px;height:24px;">
          </span>
        </button>
      </div>

      <!-- Body -->
      <div class="modal-body d-flex flex-column gap-3">            

          <!-- Aquí se inyecta la UI -->
          <div id="mt-payout-accounts" class="d-flex flex-column gap-2">
            <div class="text-muted">Loading accounts…</div>
          </div>
      

        <!-- Método de pago (placeholder; luego lo conectamos a tu flujo) -->
        <div class="d-flex flex-column gap-2">
          <span class="text-a8a29e fw-bold">Payment method</span>
          <div class="d-flex gap-2">
            <button type="button" class="mega-btn-md mega-btn-primary-md flex-fill" disabled>Riseworks</button>
            <button type="button" class="mega-btn-md mega-btn-dark-md flex-fill" disabled>BTC</button>
            <button type="button" class="mega-btn-md mega-btn-dark-md flex-fill" disabled>ETH</button>
          </div>
        </div>

        <!-- Monto (placeholder; luego conectamos límites desde eligibility) -->
        <div class="d-flex flex-column gap-2">
          <span class="text-a8a29e fw-bold">Enter the amount you wish to withdraw</span>
          <div class="rounded-3 p-3" style="background: rgba(30,30,30,0.70); outline:1px solid var(--Colors-Gray-700,#404040);">
            <input id="mt-payout-amount" type="number" min="0" step="1" class="form-control bg-transparent text-white border-0 p-0" placeholder="0" disabled>
          </div>
          <small id="mt-payout-max" class="text-a8a29e">Max withdrawal: —</small>
        </div>

        <!-- Email fijo del usuario -->
        <div class="d-flex flex-column gap-2">
          <span class="text-a8a29e fw-bold">Email</span>
          <input type="email" class="form-control" value="<?php echo esc_attr($user_email); ?>" disabled>
        </div>

      </div>

      <!-- Footer -->
      <div class="modal-footer border-0 d-flex gap-2">
        <button type="button" class="mega-btn-md mega-btn-dark-md flex-fill" data-bs-dismiss="modal">Cancel</button>
        <button id="mt-payout-continue" type="button" class="mega-btn-md mega-btn-primary-md flex-fill" disabled>Continue</button>
      </div>
    </div>
  </div>
</div>
