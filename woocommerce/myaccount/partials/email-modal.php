<div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-md-down">
        <div class="authentication-form modal-content align-items-center d-flex flex-column flex-shrink-0">
            <div class="modal-header w-100 border-0 justify-content-between align-items-start p-0">
                <h5 class="modal-title text-white heading-sm-medium" id="emailModalLabel">SIGN IN</h5>
                <button type="button" class="p-0 border-0 bg-transparent shadow-none" data-bs-dismiss="modal"
                    aria-label="Close">
                    <span aria-hidden="true">
                        <img src="/wp-content/uploads/2025/05/cancel-circle-1.png"
                            alt="Close" style="width: 24px; height: 24px;" /></span>
                </button>
            </div>
            <div class="modal-body d-flex flex-column align-items-center justify-content-center gap-32">
                <div class="otp-message-container w-100 d-flex justify-content-start d-none">
                    <!-- Error -->
                    <div class="error-otp-message notifications notifications-error">
                        <div class="w-6 h-6 d-flex align-items-center justify-content-center rounded-full bg-red-400">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                                aria-hidden="true" data-slot="icon" class="w-5 h-5 text-black">
                                <path
                                    d="M5.28 4.22a.75.75 0 0 0-1.06 1.06L6.94 8l-2.72 2.72a.75.75 0 1 0 1.06 1.06L8 9.06l2.72 2.72a.75.75 0 1 0 1.06-1.06L9.06 8l2.72-2.72a.75.75 0 0 0-1.06-1.06L8 6.94 5.28 4.22Z">
                                </path>
                            </svg>
                        </div>
                        <span class="error-otp-text">This is an error message</span>
                    </div>
                    <!-- Éxito -->
                    <div class="success-otp-message notifications notifications-success" style="max-width: 600px;">
                        <div class="w-6 h-6 d-flex align-items-center justify-content-center rounded-full bg-teal-400">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                                aria-hidden="true" data-slot="icon" class="w-5 h-5 text-black">
                                <path fill-rule="evenodd"
                                    d="M12.416 3.376a.75.75 0 0 1 .208 1.04l-5 7.5a.75.75 0 0 1-1.154.114l-3-3a.75.75 0 0 1 1.06-1.06l2.353 2.353 4.493-6.74a.75.75 0 0 1 1.04-.207Z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>

                        <span class="success-otp-text">A new code has been sent to your email.</span>
                    </div>
                </div>
                <div class="sign-in__logo" style="height: 72px; width:72px">
                    <img src="/wp-content/uploads/2025/06/appIcon.svg" alt="mt logo"
                        class="rounded-4" />
                </div>
                <form method="post" class="woocommerce-form woocommerce-form-login login w-100 m-0"
                    style="max-width: 360px;">
                    <p class="text-body pb-2 text-center">Enter your email, and We will send an email with a code
                        verification.</p>
                    <input type="email" name="username" class="form-control otp-email-input" placeholder="Email"
                        data-gtm-form-interact-field-id="1" style="background-color: var(--smoke-color) !important;">
                    <button type="button" class="get-otp-btn mt-4 ot-btn text-black w-100"
                        style="color: #000 !important;font-weight: 500 !important;">SEND</button>
                </form>
            </div>
        </div>
    </div>
</div>