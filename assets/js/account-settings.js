document.addEventListener('DOMContentLoaded', function () {
    const phoneInput = document.getElementById("billing_phone");
    console.info('phoneInput', phoneInput);
    if (phoneInput) {
        window.iti = window.intlTelInput(phoneInput, {
            initialCountry: 'us',
            nationalMode: false,
            separateDialCode: true,
        });

        function initPhoneInput(countryCode = "us") {
            if (window.iti) {
                window.iti.destroy();
                delete phoneInput.style.paddingLeft;
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

        const form = phoneInput.closest("form");
        if (form) {
            form.addEventListener("submit", function () {
                if (window.iti) {
                    const fullNumber = window.iti.getNumber();
                    console.info("📞 Full number on submit:", fullNumber);

                    let hiddenInput = form.querySelector("input[name='billing_phone_full']");
                    if (!hiddenInput) {
                        hiddenInput = document.createElement("input");
                        hiddenInput.type = "hidden";
                        hiddenInput.name = "billing_phone_full";
                        form.appendChild(hiddenInput);
                    }
                    hiddenInput.value = fullNumber;
                }
            });
        }
    }
});