document.addEventListener('DOMContentLoaded', function () {
    const phoneInput = document.getElementById("billing_phone");
    console.info('phoneInput', phoneInput);
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