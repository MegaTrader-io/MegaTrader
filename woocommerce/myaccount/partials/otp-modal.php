<div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content gap-32">
            <!-- Header -->
            <div class="modal-header w-100 border-0 justify-content-between align-items-start p-0">
                <span class="modal-title text-white heading-sm-medium"
                    id="otpModalLabel"><?php echo esc_html(Label::CHECKOUT_META['otp_modal_title']); ?></span>
                <button type="button" class="p-0 border-0 bg-transparent shadow-none" data-bs-dismiss="modal"
                    aria-label="Close">
                    <img src="/wp-content/uploads/2025/05/cancel-circle-1.png" alt="Close"
                        style="width: 24px; height: 24px;" />
                </button>
            </div>
            <div class="modal-body d-flex flex-column align-items-center text-center gap-32">
                <!-- Unified message container (copiar igual al de otpModal) -->
                <div class="otp-message-container w-100 d-flex justify-content-start d-none">
                    <!-- Error -->
                    <div class="error-otp-message notifications notifications-error w-100">
                        <div class="w-6 h-6 d-flex align-items-center justify-content-center rounded-full bg-red-400">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                                aria-hidden="true" data-slot="icon" class="w-5 h-5 text-black">
                                <path
                                    d="M5.28 4.22a.75.75 0 0 0-1.06 1.06L6.94 8l-2.72 2.72a.75.75 0 1 0 1.06 1.06L8 9.06l2.72 2.72a.75.75 0 1 0 1.06-1.06L9.06 8l2.72-2.72a.75.75 0 0 0-1.06-1.06L8 6.94 5.28 4.22Z">
                                </path>
                            </svg>
                        </div>
                        <span class="error-otp-text">Some error</span>
                    </div>
                    <!-- Success -->
                    <div class="success-otp-message notifications notifications-success w-100">
                        <div class="w-6 h-6 d-flex align-items-center justify-content-center rounded-full bg-teal-400">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                                aria-hidden="true" data-slot="icon" class="w-5 h-5 text-black">
                                <path fill-rule="evenodd"
                                    d="M12.416 3.376a.75.75 0 0 1 .208 1.04l-5 7.5a.75.75 0 0 1-1.154.114l-3-3a.75.75 0 0 1 1.06-1.06l2.353 2.353 4.493-6.74a.75.75 0 0 1 1.04-.207Z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <span
                            class="success-otp-text"><?php echo esc_html(Label::CHECKOUT_META['otp_modal_success_text']); ?></span>
                    </div>
                </div>
                <!-- Logo -->
                <div class="sign-in__logo" style="height: 72px; width: 72px;">
                    <img src="/wp-content/uploads/2025/06/appIcon.svg" alt="mt logo" class="rounded-4" />
                </div>
                <!-- Instruction -->
                <p class="text-body text-center mb-0" style="color: #E4E4E7;">
                    We sent an OTP to <strong class="text-white" id="otp-email-display">john@doe.com</strong><br>
                    Enter it below to continue
                </p>
                <!-- OTP input boxes -->
                <form method="post" class="woocommerce-form w-100 px-4" style="max-width: 360px;">
                    <div class="otp-inputs d-flex justify-content-between gap-2 mb-4">
                        <input type="hidden" name="username" value="">
                        <input type="text" maxlength="1" class="otp-box" />
                        <input type="text" maxlength="1" class="otp-box" />
                        <input type="text" maxlength="1" class="otp-box" />
                        <input type="text" maxlength="1" class="otp-box" />
                        <input type="text" maxlength="1" class="otp-box" />
                        <input type="text" maxlength="1" class="otp-box" />
                    </div>
                    <!-- Resend + Timer -->
                    <div class="d-flex justify-content-center align-items-center gap-3 mb-4">
                        <a href="#"
                            class="resend-otp fw-semibold text-decoration-underline"><?php echo esc_html(Label::CHECKOUT_META['otp_modal_resend_text']); ?></a>
                    </div>
                    <div class="otp-cta d-flex flex-column gap-3">
                        <!-- Submit buttons -->
                        <button type="submit" class="mt-btn mt-btn--md mt-btn--primary verify-otp-btn">
                            <?php echo esc_html(Label::CHECKOUT_META['otp_modal_verify_btn']); ?>
                        </button>
                        <button class="back-otp-back mt-btn mt-btn--md mt-btn--secondary" type="button">
                            <?php echo esc_html(Label::CHECKOUT_META['otp_modal_back_btn']); ?>

                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>