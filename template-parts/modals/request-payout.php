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
<div id="mt-request-payout-modal" class="modal modal-subcription fade" tabindex="-1" aria-labelledby="mtpayout-title"
    aria-hidden="true" data-user-email="<?php echo esc_attr($user_email); ?>"
    data-checkout-base="<?php echo esc_url(function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : '/checkout'); ?>">

    <div class="modal-dialog modal-dialog-centered modal-fullscreen-md-down">
        <div class="modal-content gap-32">

            <!-- Header -->
            <div class="modal-header w-100 border-0 justify-content-between align-items-center p-0">
                <span id="mtpayout-title"
                    class="modal-title text-white heading-sm-medium d-flex align-items-center gap-2 text-uppercase">
                    <?php echo esc_html(Label::META_ACCOUNT_PAYOUT['modalTitle']); ?></span>
                <button type="button" class="p-0 border-0 bg-transparent shadow-none mt-modal__close"
                    data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        <img src="/wp-content/uploads/2025/05/cancel-circle-1.png" alt="Close"
                            style="width:24px;height:24px;">
                    </span>
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body d-flex flex-column gap-32">

                <!-- Aquí se inyecta la UI -->
                <div id="mt-payout-accounts" class="d-flex flex-column gap-2">
                    <div class="text-muted">Loading accounts…</div>
                </div>


                <!-- Método de pago (placeholder; luego lo conectamos a tu flujo) -->
                <div class="d-flex flex-column gap-2">
                    <span
                        class="text-a8a29e text-base fw-bold"><?php echo esc_html(Label::META_ACCOUNT_PAYOUT['paymentMethod']); ?></span>
                    <div class="d-flex gap-2 flex-wrap flex-md-nowrap flex-wrap-reverse">
                        <button type="button"
                            class="mt-btn mt-btn--md mt-btn--primary flex-1-1-0 order-2 order-md-1"><span
                                class="mt-icon mt-icon_rise"></span><?php echo esc_html(Label::META_ACCOUNT_PAYOUT['riseworks']); ?></button>
                        <button type="button"
                            class="mt-btn mt-btn--md mt-btn--secondary flex-1-1-0 order-1 order-md-2"><span
                                class="mt-icon mt-icon_btc"></span><?php echo esc_html(Label::META_ACCOUNT_PAYOUT['btc']); ?></button>
                        <button type="button"
                            class="mt-btn mt-btn--md mt-btn--secondary flex-1-1-0 order-1 order-md-3"><span
                                class="mt-icon mt-icon_eth"></span><?php echo esc_html(Label::META_ACCOUNT_PAYOUT['eth']); ?></button>
                    </div>
                </div>

                <!-- Monto -->
                <div class="d-flex flex-column gap-2">
                    <span
                        class="text-a8a29e fw-bold text-base"><?php echo esc_html(Label::META_ACCOUNT_PAYOUT['withdrawalAmount']); ?></span>

                    <!-- Error arriba del input -->
                    <div id="mt-payout-error" class="text-danger" style="display:none;"></div>

                    <input id="mt-payout-amount" type="number" min="0" step="1" class="form-control" placeholder="0"
                        inputmode="numeric" autocomplete="off">

                    <!-- El JS setea data-max y el texto visible -->
                    <small id="mt-payout-max" class="text-a8a29e"
                        data-max="0"><?php echo esc_html(Label::META_ACCOUNT_PAYOUT['maxWithdrawal']); ?>: —</small>
                </div>


                <!-- Email fijo del usuario -->
                <div class="d-flex flex-column gap-2">
                    <span
                        class="text-a8a29e fw-bold"><?php echo esc_html(Label::META_ACCOUNT_PAYOUT['email']); ?></span>
                    <input type="email" class="form-control" value="<?php echo esc_attr($user_email); ?>" disabled>
                </div>

            </div>

            <!-- Footer -->
            <div class="modal-footer border-0 d-flex gap-2">
                <button type="button" class="flex-1-1-0 mt-btn mt-btn--link text-white"
                    data-bs-dismiss="modal"><?php echo esc_html(Label::META_ACCOUNT_PAYOUT['cancelButton']); ?></button>
                <button id="mt-payout-continue" type="button" class="flex-1-1-0 mt-btn mt-btn--md mt-btn--primary"
                    disabled><?php echo esc_html(Label::META_ACCOUNT_PAYOUT['submitButton']); ?></button>
            </div>
        </div>
    </div>
</div>