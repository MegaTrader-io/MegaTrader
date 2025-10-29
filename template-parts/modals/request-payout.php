<?php
defined('ABSPATH') || exit;

/* === Helpers === */
if (file_exists(get_stylesheet_directory() . '/inc/mt-accounts-helpers.php')) {
    require_once get_stylesheet_directory() . '/inc/mt-accounts-helpers.php';
}
/** @var array $args */
$user_email = isset($args['user_email']) ? sanitize_email($args['user_email']) : '';

?>
<div id="mt-request-payout-modal" class="modal modal-subcription fade" tabindex="-1" aria-labelledby="mtpayout-title"
    aria-hidden="true" data-user-email="<?php echo esc_attr($user_email); ?>"
    data-checkout-base="<?php echo esc_url(function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : '/checkout'); ?>">

    <div class="modal-dialog modal-dialog-centered modal-fullscreen-md-down">
        <div class="modal-content">

            <div class="modal-header w-100 border-0 justify-content-between align-items-center p-0">
                <span id="mtpayout-title"
                    class="modal-title text-white heading-sm-medium d-flex align-items-center gap-2 text-uppercase">
                    <?php echo esc_html(Label::META_ACCOUNT_PAYOUT['modalTitle']); ?></span>
                <button type="button" class="p-0 border-0 bg-transparent shadow-none mt-modal__close"
                    data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        <img src="/wp-content/uploads/2025/05/cancel-circle-1.png" alt="Close"
                            style="width:30px;height:30px;">
                    </span>
                </button>
            </div>

            <div class="modal-body d-flex flex-column gap-32 mt-4">

                <div id="mt-payout-step1" class="d-flex flex-column gap-32">
                    <div id="mt-payout-accounts" class="d-flex flex-column gap-2">
                        <div class="text-muted">Loading accounts…</div>
                    </div>

                    <div class="d-flex flex-column gap-2">
                        <span
                            class="text-a8a29e text-base fw-bold"><?php echo esc_html(Label::META_ACCOUNT_PAYOUT['paymentMethod']); ?></span>
                        <div class="d-flex gap-2 flex-wrap flex-md-nowrap flex-wrap-reverse">
                            <button type="button"
                                class="mt-btn mt-btn--md mt-btn--primary flex-1-1-0 order-2 order-md-1">
                                <span
                                    class="mt-icon mt-icon_rise"></span><?php echo esc_html(Label::META_ACCOUNT_PAYOUT['riseworks']); ?>
                            </button>
                            <button type="button"
                                class="mt-btn mt-btn--md mt-btn--secondary flex-1-1-0 order-1 order-md-2" disabled>
                                <span
                                    class="mt-icon mt-icon_btc"></span><?php echo esc_html(Label::META_ACCOUNT_PAYOUT['btc']); ?>
                            </button>
                            <button type="button"
                                class="mt-btn mt-btn--md mt-btn--secondary flex-1-1-0 order-1 order-md-3" disabled>
                                <span
                                    class="mt-icon mt-icon_eth mt-icon-sm"></span><?php echo esc_html(Label::META_ACCOUNT_PAYOUT['eth']); ?>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2">
                        <span
                            class="text-a8a29e fw-bold text-base"><?php echo esc_html(Label::META_ACCOUNT_PAYOUT['withdrawalAmount']); ?></span>
                        <div id="mt-payout-error" class="text-danger" style="display:none;"></div>
                        <input id="mt-payout-amount" type="number" min="0" step="1" class="form-control" placeholder="0"
                            inputmode="numeric" autocomplete="off">
                        <small id="mt-payout-max" class="text-a8a29e" data-max="0">
                            <?php echo esc_html(Label::META_ACCOUNT_PAYOUT['maxWithdrawal']); ?>: —
                        </small>
                    </div>

                    <div class="d-flex flex-column gap-2">
                        <span
                            class="text-a8a29e fw-bold"><?php echo esc_html(Label::META_ACCOUNT_PAYOUT['email']); ?></span>
                        <input id="mt-payout-email" type="email" class="form-control"
                            value="<?php echo esc_attr($user_email); ?>" disabled>
                    </div>
                </div>

                <div id="mt-payout-step2" class="d-none">
                    <div class="d-flex flex-column">
                        <div class="py-3 d-flex justify-content-between align-items-center border-bottom-gray">
                            <div class="text-a8a29e text-base fw-medium">Email</div>
                            <div id="mt-review-email" class="text-white text-base fw-medium"></div>
                        </div>
                        <div class="py-3 d-flex justify-content-between align-items-center border-bottom-gray">
                            <div class="text-a8a29e text-base fw-medium">Account Number</div>
                            <div id="mt-review-account" class="text-white text-base fw-medium"></div>
                        </div>
                        <div class="py-3 d-flex justify-content-between align-items-center border-bottom-gray">
                            <div class="text-a8a29e text-base fw-medium">Amount to Withdraw</div>
                            <div id="mt-review-amount" class="text-white text-base fw-medium"></div>
                        </div>
                        <div class="py-3 d-flex justify-content-between align-items-center border-bottom-gray">
                            <div class="text-a8a29e text-base fw-medium">Transaction Fee (10%)</div>
                            <div id="mt-review-fee" class="text-white text-base fw-medium"></div>
                        </div>
                        <div class="py-3 d-flex justify-content-between align-items-center border-bottom-gray">
                            <div class="text-a8a29e text-base fw-medium">Amount to Receive</div>
                            <div id="mt-review-receive" class="text-white text-base fw-medium"></div>
                        </div>
                    </div>

                    <div class="mt-3 p-3 rounded-2 bg-1e1e1e">
                        <div class="text-60A5FA d-flex align-items-center gap-3">
                            <span class="mt-icon mt-icon_info-solid"></span>
                            <span
                                class="text-base fw-medium"><?php echo esc_html(Label::META_ACCOUNT_PAYOUT['verifiedDetails']); ?></span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer border-0 d-flex gap-2 p-0 mt-2">
                <button id="mt-payout-cancel" type="button" class="flex-1-1-0 mt-btn mt-btn--link text-white"
                    data-bs-dismiss="modal">
                    <?php echo esc_html(Label::META_ACCOUNT_PAYOUT['cancelButton']); ?>
                </button>

                <button id="mt-payout-back" type="button" class="flex-1-1-0 mt-btn mt-btn--link text-white d-none">
                    Go back
                </button>

                <button id="mt-payout-continue" type="button" class="flex-1-1-0 mt-btn mt-btn--md mt-btn--primary"
                    disabled>
                    <?php echo esc_html(Label::META_ACCOUNT_PAYOUT['submitButton']); ?>
                </button>
            </div>

        </div>
    </div>
</div>