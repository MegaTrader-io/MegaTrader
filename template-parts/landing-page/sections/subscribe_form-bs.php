<form id="form-subscription" novalidate class="footer-bs__column footer-bs__column--subscribe">
    <div class="footer-bs__alert d-none">
        <div role="alert" tabindex="-1" class="woocommerce-message">
            Congratulations, you have successfully subscribed.
        </div>
    </div>

    <div class="footer-bs__form">
        <div class="footer-bs__input-group">
            <label for="email" class="w-100 mb-0">
                <input type="email" id="email" name="email" autocomplete="off" placeholder="Enter your email"
                       class="form-control">
            </label>
            <div id="email-error" class="email_text invalid-text d-none"></div>
        </div>

        <button type="submit" disabled class="btn mega-btn-md mega-btn-primary-md">
            SUBSCRIBE
        </button>
    </div>

    <div class="footer-bs__consent">
        <div class="checkbox-container">
            <input class="checkbox-container__input" type="checkbox"
                   id="email_consent"
                   name="email_consent">

            <label for="email_consent" class="checkbox-container__label m-0">
                <span class="footer-bs__consent-text">
                    I consent to the use of my email address to receive news, updates, and important notifications.
                </span>
            </label>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const formSubscription = document.getElementById('form-subscription');
        if (!formSubscription) {
            return;
        }

        const input = formSubscription.querySelector('[name="email"]');
        const emailError = formSubscription.querySelector('#email-error');
        const submitBtn = formSubscription.querySelector('[type="submit"]');
        const emailConsent = formSubscription.querySelector('[name="email_consent"]');

        function setErrorMessage(message) {
            emailError.innerText = message ? message : '';

            submitBtn.disabled = !message;

            if (message) {
                submitBtn.disabled = true;
                input.classList.add('invalid_email');
                emailError.classList.remove('d-none')
            } else {
                input.classList.remove('invalid_email');
                submitBtn.disabled = !emailConsent.checked;
                emailError.classList.add('d-none');
            }
        }

        function lockForm(locked) {
            input.disabled = locked;
            submitBtn.disabled = locked;
            emailConsent.disabled = locked;
        }

        async function handlerSubmitForm(ev) {
            ev.preventDefault();

            if (!validateEmail()) {
                if (!input.value.trim()) {
                    setErrorMessage("This field is required");
                    input.focus();
                }

                return;
            }

            input?.blur();

            const alertSuccess = document.querySelector('.footer-bs__alert');
            alertSuccess.classList.add('d-none');

            try {
                $.preloader.show();
                lockForm(true);

                const response = await fetch(MG_GLOBAL.adminAjaxApi, {
                    method: 'POST', headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    }, body: new URLSearchParams({
                        action: 'subscription_form_submit',
                        nonce: MG_GLOBAL.subscriptionNonce,
                        email: input.value.trim(),
                        email_consent: emailConsent.checked
                    })
                });

                const responseData = await response.json();
                const {data} = responseData;

                if (!responseData.success) {
                    setErrorMessage(data.message);
                    return;
                }

                alertSuccess.classList.remove('d-none');

                input.value = '';
                emailConsent.checked = false;

                submitBtn.disabled = true;

                setTimeout(() => {
                    input.focus();
                }, 0);
            } catch (e) {
            } finally {
                $.preloader.hide();
                lockForm(false)
            }
        }

        function handlerEmailConsent() {
            validateEmail();

            if (emailConsent.checked) {
                submitBtn.disabled = false;
                return;
            }

            submitBtn.disabled = true;
        }

        function validateEmail() {
            const value = input.value.trim();

            if (value === '') {
                return;
            }

            if (!input.validity.valid) {
                setErrorMessage("Invalid email");
                return false;
            }

            setErrorMessage('');
            return true;
        }

        input.addEventListener('blur', validateEmail);
        emailConsent.addEventListener('click', handlerEmailConsent);
        submitBtn.addEventListener('click', handlerSubmitForm);
    });
</script>