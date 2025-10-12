document.addEventListener('DOMContentLoaded', function () {
    const url = new URL(window.location.href);

    if (url.searchParams.has("logged_out")) {
        // Aquí ya WooCommerce debió agregar el notice desde PHP,
        // pero si quieres forzar un mensaje en el front:
        // alert("You have successfully logged out");

        // Eliminar el parámetro de la URL sin recargar la página
        url.searchParams.delete("logged_out");

        history.pushState(null, "", "/auth/login/");
        window.onpopstate = () => {
            history.pushState(null, "", "/auth/login/");
        };

        // window.history.replaceState({}, document.title, url.toString());
    }

    function setStateWhenReady(stateCode) {
        if (!stateCode) return;
        const wrapper = document.getElementById("billing_state_wrapper");

        const tryApply = () => {
            const select = document.getElementById("billing_state");
            if (!select) return false;

            const match = [...select.options].find(o => o.value === stateCode);
            if (!match) return false;

            select.value = stateCode;
            select.dispatchEvent(new Event("change"));
            select.dataset.googleSet = "true";
            return true;
        };

        if (tryApply()) return;

        const obs = new MutationObserver(() => {
            if (tryApply()) obs.disconnect();
        });
        obs.observe(wrapper, {childList: true, subtree: true});
    }

    function lockStateSelection(stateCode, ttlMs = 7000) {
        if (!stateCode) return;
        const wrapper = document.getElementById("billing_state_wrapper");
        if (!wrapper) return;

        const start = Date.now();
        const apply = () => {
            const select = document.getElementById("billing_state");
            if (!select) return;
            const opt = [...select.options].find(o => o.value === stateCode);
            if (opt) {
                select.value = stateCode;
                select.dataset.googleSet = "true";
            }
        };

        apply();

        const obs = new MutationObserver(() => {
            apply();
            if (Date.now() - start > ttlMs) {
                obs.disconnect();
            }
        });
        obs.observe(wrapper, {childList: true, subtree: true});
    }

    const wrapper = document.querySelector('.woocommerce-notices-wrapper');
    if (wrapper && wrapper.innerHTML.trim() === '') {
        wrapper.remove();
    }

    const passwordWrapper = document.querySelectorAll('.password-wrapper');

    passwordWrapper.forEach(wrapper => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.classList.add('password-wrapper__btn_eye');
        wrapper.appendChild(btn);

        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const inputPassword = e.currentTarget.parentElement.querySelector('input');
            if (!inputPassword) {
                return;
            }

            inputPassword.type = inputPassword.type.toLowerCase() === 'password' ? 'text' : 'password';
        })
    });

    const emailInput = document.querySelector('[name=username]') || document.querySelector('[name=user_login]') || document.querySelector('[name=email]');
    if (emailInput) {
        function validateEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        function removeEmailError(input) {
            const errorUsername = input.parentElement.querySelector('[id=error-username]');
            input.classList.remove('auth-form--error-message');

            if (errorUsername) {
                errorUsername.remove();
            }
        }

        emailInput.addEventListener('blur', function (ev) {
            const errorUsername = emailInput.parentElement.querySelector('[id=error-username]');
            const value = ev.currentTarget.value.trim();
            if (!value) {
                ev.currentTarget.value = '';
                removeEmailError(emailInput);
                return;
            }

            const isValidEmail = validateEmail(value);

            if (isValidEmail) {
                removeEmailError(emailInput);
                return;
            }

            if (errorUsername) {
                return;
            }

            emailInput.classList.add('auth-form--error-message');
            const errorMessage = document.createElement('span');

            errorMessage.id = 'error-username';
            errorMessage.classList.add('auth-form__error_message');
            errorMessage.innerHTML = 'Enter a valid email address.';

            emailInput.parentElement.appendChild(errorMessage);
        });
    }

    const phoneInput = document.getElementById("phone");
    if (phoneInput) {
        function initPhoneInput(countryCode = "us") {
            if (window.iti) {
                window.iti.destroy();
            }
            const iti = window.intlTelInput(phoneInput, {
                initialCountry: countryCode.toLowerCase(),
                nationalMode: false,
                separateDialCode: true,
            });

            window.iti = iti;

            phoneInput.addEventListener("blur", function () {
                const isValid = iti.isValidNumber();
                let errorContainer = phoneInput
                    .closest(".form-group")
                    ?.querySelector(".invalid-feedback");

                if (!errorContainer) {
                    errorContainer = document.createElement("div");
                    errorContainer.className = "invalid-feedback";
                    errorContainer.textContent = "Invalid phone number.";
                    phoneInput.parentNode.appendChild(errorContainer);
                }

                if (isValid) {
                    phoneInput.classList.remove("is-invalid");
                    errorContainer.style.display = "none";
                    phoneInput.setCustomValidity("");
                } else {
                    phoneInput.classList.add("is-invalid");
                    errorContainer.style.display = "block";
                    phoneInput.setCustomValidity("Invalid");
                }
            });
        }

        function fallbackToIP() {
            fetch("https://ipapi.co/json/")
                .then((resp) => resp.json())
                .then((resp) => {
                    const countryCode = resp.country || "us";
                    initPhoneInput(countryCode);
                })
                .catch(() => {
                    initPhoneInput("auto");
                });
        }

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const {latitude, longitude} = position.coords;
                    fetch(
                        `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`
                    )
                        .then((res) => res.json())
                        .then((data) => {
                            const countryCode = data.address?.country_code?.toUpperCase();
                            initPhoneInput(countryCode || "auto");
                        })
                        .catch(() => {
                            fallbackToIP();
                        });
                },
                (err) => {
                    console.warn("⚠️ Geolocation blocked or failed:", err.message);
                    fallbackToIP();
                },
                {timeout: 5000}
            );
        } else {
            fallbackToIP();
        }
    }
});

window.addEventListener("pageshow", function (event) {
    if (event.persisted) {
        console.info('reload auth', new Date());
        window.location.reload();
    }
});
