document.addEventListener('DOMContentLoaded', function () {
    const phoneInput = document.getElementById("billing_phone");
    const form = phoneInput.closest("form");

    console.info('phoneInput', phoneInput.value);

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
    }

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

    const btn = document.getElementById('get-verified-btn');
    if (!btn || typeof Veriff === 'undefined') return;

    btn.addEventListener('click', async () => {
        btn.disabled = true;

        // Llamar tu endpoint AJAX que crea la sesión en Veriff
        const response = await fetch(wpAjax.ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'mt_start_veriff_verification',
                security: wpAjax.nonce,
            }),
        });

        const result = await response.json();
        if (!result.success) {
            alert(result.data?.message || 'Error starting verification.');
            btn.disabled = false;
            return;
        }

        const sessionToken = result.data.token;

        // 🔹 Iniciar Veriff embebido
        const veriff = Veriff({
            host: 'https://stationapi.veriff.com',
            apiKey: wpAjax.veriffKey,
            parentId: 'veriff-container',
            onSession: function (err, response) {
                if (err) {
                    console.error(err);
                    return;
                }
                veriff.mount({
                    sessionToken,
                    onFinish: (res) => console.log('Verification finished:', res),
                });
            },
        });

        veriff.setSession({ sessionToken });
    });
});