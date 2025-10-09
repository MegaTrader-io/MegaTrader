document.addEventListener('DOMContentLoaded', function () {
    const phoneInput = document.getElementById("personal_phone");
    if (!phoneInput) return console.error("Phone input not found");

    // Función para inicializar el input con un país
    function initPhoneInput(countryCode = 'us') {
        if (window.iti) {
            try {
                window.iti.destroy();
                delete phoneInput.style.paddingLeft;
            } catch (err) {
                console.warn("Error destroying intlTelInput:", err);
            }
        }

        const iti = window.intlTelInput(phoneInput, {
            initialCountry: countryCode.toLowerCase(),
            nationalMode: false,
            separateDialCode: true,
            autoPlaceholder: "polite",
        });

        window.iti = iti;
        return iti;
    }

    // Detección según el número existente
    function initByPhoneNumber() {
        const value = phoneInput.value.trim();

        if (value && value.startsWith('+')) {
            const iti = initPhoneInput('auto');
            try {
                iti.setNumber(value); // autodetecta el país a partir del número
            } catch (err) {
                console.warn("Error setting number:", err);
            }
            return true;
        }

        return false;
    }

    // Fallback: detectar país por IP
    function fallbackToIP() {
        fetch("https://ipapi.co/json/")
            .then((resp) => resp.json())
            .then((data) => {
                const countryCode = data.country || "us";
                initPhoneInput(countryCode);
            })
            .catch((err) => {
                console.error("IP detection failed:", err);
                initPhoneInput("us");
            });
    }

    // Validación en blur
    phoneInput.addEventListener("blur", function () {
        if (!window.iti) return;

        const isValid = window.iti.isValidNumber();
        let errorContainer = phoneInput
            .closest(".form-group")
            ?.querySelector(".invalid-feedback");

        if (!errorContainer) {
            errorContainer = document.createElement("div");
            errorContainer.className = "invalid-feedback";
            phoneInput.parentNode.appendChild(errorContainer);
        }

        if (isValid) {
            phoneInput.classList.remove("is-invalid");
            errorContainer.style.display = "none";
            phoneInput.setCustomValidity("");
        } else {
            phoneInput.classList.add("is-invalid");
            errorContainer.style.display = "block";
            errorContainer.textContent = "Número de teléfono inválido.";
            phoneInput.setCustomValidity("Invalid");
        }
    });

    // Lógica principal
    if (!initByPhoneNumber()) {
        fallbackToIP();
    }
});
