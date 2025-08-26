document.addEventListener('DOMContentLoaded', function () {
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

    // Initialize Slider
    function initializeSwiper() {
        (new Swiper('.swiper', {
            direction: 'horizontal',
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
            },
            loop: true,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            scrollbar: {
                el: '.swiper-scrollbar',
            },
        }))
    }

    initializeSwiper();

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
                    console.log("Country Code 1:", countryCode);
                    initPhoneInput(countryCode);

                    if (countryCode) {
                        document.getElementById("billing_country").value = countryCode;
                    }
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
                            console.log("Country Code 2:", countryCode);
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

    // ========== GOOGLE AUTOCOMPLETE ==========
    const addressInput = document.getElementById("billing_address_1");

    if (addressInput && window.google && google.maps && google.maps.places) {
        const autocomplete = new google.maps.places.Autocomplete(addressInput, {
            types: ["address"],
            componentRestrictions: {country: ["us"]},
        });

        autocomplete.addListener("place_changed", function () {
            const place = autocomplete.getPlace();
            if (!place.address_components) return;

            const fields = {
                billing_address_1: "",
                billing_address_2: "",
                billing_city: "",
                billing_state: "",
                billing_postcode: "",
                billing_country: "",
            };

            place.address_components.forEach((component) => {
                const types = component.types;

                if (types.includes("street_number")) {
                    fields.billing_address_1 =
                        component.long_name + " " + fields.billing_address_1;
                }
                if (types.includes("route")) {
                    fields.billing_address_1 += component.long_name;
                }
                if (types.includes("subpremise")) {
                    fields.billing_address_2 = component.long_name;
                }
                if (types.includes("locality")) {
                    fields.billing_city = component.long_name;
                }
                if (types.includes("administrative_area_level_1")) {
                    fields.billing_state = component.short_name;
                }
                if (types.includes("postal_code")) {
                    fields.billing_postcode = component.long_name;
                }
                if (types.includes("country")) {
                    fields.billing_country = component.short_name;
                }
            });

            document.getElementById("billing_address_1").value =
                fields.billing_address_1;
            document.getElementById("billing_address_2").value =
                fields.billing_address_2;
            document.getElementById("billing_city").value = fields.billing_city;
            document.getElementById("billing_postcode").value =
                fields.billing_postcode;

            const countrySelect = document.getElementById("billing_country");
            if (fields.billing_country && countrySelect) {
                const option = [...countrySelect.options].find(
                    (opt) => opt.value === fields.billing_country
                );
                if (option) {
                    countrySelect.value = fields.billing_country;
                    countrySelect.classList.add("selected-by-google");
                    countrySelect.dispatchEvent(new Event("change"));
                    hasBeenOverwrittenByAutocomplete = true;
                }
            }

            setStateWhenReady(fields.billing_state);
            lockStateSelection(fields.billing_state, 7000);


        });
    }
});