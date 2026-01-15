function setFullPhoneInput(form, inputName) {
    if (window.itiRefs) {
        const fullNumber = window.itiRefs[inputName].getNumber();
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
}

function formatUtcToDMY(utcString) {
    try {
        const date = new Date(utcString);

        if (isNaN(date.getTime())) {
            throw new Error('Invalid date string');
        }

        const day = String(date.getUTCDate()).padStart(2, '0');
        const month = String(date.getUTCMonth() + 1).padStart(2, '0');
        const year = date.getUTCFullYear();

        return `${day}/${month}/${year}`;
    } catch (error) {
        console.error('Error formatting UTC date:', error.message);
        return null;
    }
}

function parseHTMLElement(htmlString) {
    try {
        if (typeof htmlString !== "string" || !htmlString.trim()) {
            throw new Error("El parámetro debe ser un string HTML no vacío.");
        }

        const container = document.createElement("div");
        container.innerHTML = htmlString.trim();

        // Retornar el primer nodo hijo que sea un elemento (no texto, no comentario)
        const element = container.firstElementChild;

        if (!element) {
            throw new Error("No se encontró ningún elemento válido en el HTML.");
        }

        return element;
    } catch (error) {
        console.error("Error al convertir el string en elemento DOM:", error);
        return null;
    }
}

function clearErrorBeforeSendRequest(form) {
    const globalMessage = document.querySelector('[data-form-ref="' + form.id + '"]');
    if (globalMessage) {
        globalMessage.remove();
    }

    form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
}

function displayGlobalMessage(form, message, type = 'success') {
    type = ['success', 'error'].includes(type) ? type : 'success';

    const messageContainer = window.MTHelpers.showMessage({
        message,
        type
    });

    messageContainer.setAttribute('tabindex', '-1');
    messageContainer.dataset.formRef = form.id;

    const card = form.closest('.account-settings__section');
    console.info(card.parentNode);
    card.parentNode.insertBefore(messageContainer, card);

    setTimeout(() => {
        messageContainer.classList.add('woocommerce-message', type === 'error' ? 'woocommerce-error' : null);

        setTimeout(() => {
            messageContainer.remove();
        }, 2000)
    }, 0);
}

document.addEventListener('DOMContentLoaded', function () {

    const __open = (new URLSearchParams(window.location.search).get('open') || '').toLowerCase();

    function initPhoneInput(phoneInput, countryCode = 'us') {
        const phoneName = phoneInput.name
        if (window.itiRefs && window.itiRefs[phoneName]) {
            try {
                window.itiRefs[phoneName].destroy();
                delete phoneInput.style.paddingLeft;
            } catch (err) {
                console.warn("Error destroying intlTelInput:", err);
            }
        }

        const instance = window.intlTelInput(phoneInput, {
            initialCountry: countryCode.toLowerCase(),
            nationalMode: false,
            separateDialCode: true,
            autoPlaceholder: "polite",
        });

        if (!window.itiRefs) {
            window.itiRefs = { [phoneName]: instance }
        } else {
            window.itiRefs[phoneName] = instance
        }

        return instance;
    }

    function initByPhoneNumber(phoneInput) {
        const value = phoneInput.value.trim();

        if (value && value.startsWith('+')) {
            const iti = initPhoneInput(phoneInput, 'auto');
            try {
                iti.setNumber(value); // autodetecta el país a partir del número
            } catch (err) {
                console.warn("Error setting number:", err);
            }
            return true;
        }

        return false;
    }

    function closureCountrySearch() {
        let countryCode = null;

        // Fallback: detectar país por IP
        return function fallbackToIP(phoneInput) {
            if (countryCode) {
                initPhoneInput(phoneInput, countryCode);
                return
            }

            fetch("https://ipapi.co/json/")
                .then((resp) => resp.json())
                .then((data) => {
                    countryCode = data.country || "us";
                    initPhoneInput(phoneInput, countryCode);
                })
                .catch((err) => {
                    console.error("IP detection failed:", err);
                    countryCode = "us";
                    initPhoneInput(phoneInput, countryCode);
                });
        }
    }


    const fallbackToIP = closureCountrySearch();

    document.querySelectorAll(".mt-phone-component").forEach(phoneInput => {
        console.info('phoneInput', phoneInput.name);
        phoneInput.addEventListener("blur", function () {
            const phoneName = phoneInput.name;
            const instanceIti = window.itiRefs && window.itiRefs[phoneName];

            if (!instanceIti) {
                return
            }

            const isValid = window.itiRefs[phoneName].isValidNumber();

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
                errorContainer.textContent = "Invalid phone number.";
                phoneInput.setCustomValidity("Invalid");
            }
        });

        // Lógica principal
        if (!initByPhoneNumber(phoneInput)) {
            fallbackToIP(phoneInput);
        }
    });

    (async function fetchAccountProfileData() {
        try {
            const response = await fetch('/wp-json/custom/v1/account-profile-data', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-WP-Nonce': MT_AP.nonceWpRest
                }
            });

            if (!response.ok) {
                // Handle 401, 404, 500, etc.
                const errorData = await response.json();
                console.error('Error fetching profile data:', errorData.message || 'Unknown error');
                return;
            }

            const { is_verified, personal_information: personalInformationData } = await response.json();

            const verifiedInformationList = document.querySelectorAll('.mt-verified');
            const personalInformationBtn = document.querySelector('[name=account-settings__personal-information-button]');
            const verificationBtn = document.querySelector('[name=account-settings__verification-button]');

            verifiedInformationList?.forEach(element => {
                if (is_verified !== null) {
                    element.dataset.verified = is_verified ? '1' : '0';
                }
            });

            if (verificationBtn) {
                if (is_verified === null) {
                    verificationBtn.disabled = true;
                } else {
                    verificationBtn.disabled = false;
                    document.getElementById('get-verified-btn').disabled = is_verified;
                }
            }

            if (personalInformationBtn) {
                if (personalInformationData) {
                    personalInformationBtn.disabled = false;

                    const {
                        fullname,
                        firstname,
                        lastname,
                        email,
                        address,
                        city,
                        phone,
                        state,
                        createdAt,
                        zipcode,
                        country,
                    } = personalInformationData;

                    const avatarContainer = document.querySelector('.mt-user-profile-card__avatar-container');

                    document.querySelector('.mt-user-profile-card__member-date').innerText = formatUtcToDMY(createdAt);
                    avatarContainer.querySelector('.mt-avatar__user-information > div:first-child').innerText = fullname;
                    avatarContainer.querySelector('.mt-avatar__user-information > div:last-child').innerText = email;
                    avatarContainer.querySelector('.mt-avatar__initials').innerText = firstname.at(0) + lastname.at(0);

                    document.querySelector('[name=api_billing_first_name]').value = firstname;
                    document.querySelector('[name=api_billing_last_name]').value = lastname;
                    document.querySelector('[name=api_billing_email]').value = email;

                    document.querySelector('[name=api_billing_address_1]').value = address;
                    document.querySelector('[name=api_billing_city]').value = city;

                    document.querySelector('[name=api_billing_postcode]').value = zipcode;
                    document.querySelector('[name=api_billing_country]').value = country || 'US';
                    document.querySelector('[name=api_billing_phone]').value = phone;
                    window.itiRefs && window.itiRefs['api_billing_phone']?.setNumber(phone)

                    var data = {
                        action: 'get_cities',
                        country: country,
                        state
                    };

                    $.post(woocommerce_params.ajax_url, data, function (response) {
                        const element = parseHTMLElement(response);
                        element.name = 'api_billing_state';
                        element.id = 'api_billing_state';
                        element.value = state;
                        $('#api_billing_state_wrapper').empty().append(element);
                    });
                    if (!__open) {
                        bootstrap.Collapse.getOrCreateInstance(document.getElementById('collapseOnePersonalInformation')).show();
                    }
                    avatarContainer.querySelector('.mt-avatar__user-information > div:nth-child(2)').innerText = document.querySelector('[name=api_billing_country] option:checked')?.text?.replace('(US)', '')?.trim();
                } else {
                    bootstrap.Collapse.getOrCreateInstance(document.getElementById('collapseBillingInformationOne')).show();
                    personalInformationBtn.disabled = true;
                }
            }
        } catch (error) {
            console.error('Network or server error:', error);
        } finally {
            document
                .querySelectorAll('.account-settings__sections-wrapper .mt-skeleton-pulse')
                .forEach(element => element.classList.remove('mt-skeleton-pulse'));
        }
    })();

    (function openAccordionFromURL() {
        if (!__open) return;

        const map = {
            verification: 'collapseTwo',
        };

        const targetId = map[__open];
        if (!targetId) return;

        const panel = document.getElementById(targetId);
        if (!panel || !window.bootstrap?.Collapse) return;

        window.bootstrap.Collapse.getOrCreateInstance(panel).show();
        panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    })();


});
