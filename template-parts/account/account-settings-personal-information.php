<?php
$success = wc_get_notices('success');
$errors = wc_get_notices('error');

foreach ($errors as $error) {
    if (!isset($error['data']['field'])) {
        continue;
    }

    MT_WC_Error::$field_errors[$error['data']['field']] = $error['notice'];
}

$api_billing_country = esc_attr(get_user_meta(get_current_user_id(), 'api_billing_country', true));
if (!$api_billing_country) {
    $api_billing_country = 'US';
}

$current_user = wp_get_current_user();
$user_email = $current_user->user_email;

$data = [
        'address' => '',
        'city' => '',
        'state' => '',
        'zipcode' => '',
        'country' => '',
]; //MT_Api::fetch_user_by_email(email: $user_email);

$api_billing_address = $data['address'] ?? '';
$api_billing_city = $data['city'] ?? '';
$api_billing_state = $data['state'] ?? '';
$api_billing_zipcode = $data['zipcode'] ?? '';
$api_billing_country = $data['country'] ?? '';

$valid_states = WC()->countries->get_states($api_billing_country);

?>
<div>
    <?php get_template_part("template-parts/user-profile-card"); ?>

    <div class="space-y-3">
        <?php wp_nonce_field('mt_save_personal_address', 'mt_personal_nonce'); ?>

        <div class="row">
            <div class="col-lg-6">
                <label class="mb-1"><?php esc_html_e('First Name', 'megatrader'); ?></label>
                <input type="text"
                       disabled
                       class="form-control"
                       name="api_billing_first_name"
                       placeholder="<?php esc_attr_e('First Name', 'megatrader'); ?>">
            </div>
            <div class="col-lg-6">
                <label class="mb-1"><?php esc_html_e('Last Name', 'megatrader'); ?></label>
                <input type="text" class="form-control" disabled name="api_billing_last_name"
                       placeholder="<?php esc_attr_e('Last Name', 'megatrader'); ?>">
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <label class="mb-1"><?php _e('Email', 'woocommerce'); ?></label>
                <input type="email" disabled name="api_billing_email"
                       class="form-control"
                       value="<?php echo $user_email; ?>"/>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <label class="mb-1" for="api_billing_address_1"><?php _e('Address', 'woocommerce'); ?></label>
                <input type="text" name="api_billing_address_1" id="api_billing_address_1"
                       class="form-control"
                       value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'api_billing_address_1', true)); ?>"/>
            </div>
            <div class="col-lg-6">
                <label class="mb-1" for="api_billing_city"><?php _e('City', 'woocommerce'); ?></label>
                <input type="text" name="api_billing_city" id="api_billing_city"
                       class="form-control"
                       value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'api_billing_city', true)); ?>"/>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <label class="mb-1" for="api_billing_state"><?php _e('State', 'woocommerce'); ?></label>
                <div id="api_billing_state_wrapper">
                    <?php if (!empty($valid_states)): ?>
                        <select name="api_billing_state" id="api_billing_state"
                                class="form-select form-control woocommerce-select">
                            <option value=""
                                    disabled <?php echo (!empty($_POST['api_billing_state']) && is_string($_POST['api_billing_state'])) ? '' : 'selected'; ?>>
                                State
                            </option>
                            <?php foreach ($valid_states as $key => $value): ?>
                                <option value="<?= esc_attr($key) ?>" <?php echo (!empty($api_billing_state) && is_string($api_billing_state)) ? 'selected' : ''; ?> ><?= esc_html($value) ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php elseif (is_string($api_billing_state)): ?>
                        <input type="text" name="api_billing_state" id="api_billing_state"
                               class="form-control"
                               value="<?php echo esc_attr($api_billing_state); ?>"/>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-6">
                <label class="mb-1" for="api_billing_postcode"><?php _e('ZIP Code', 'woocommerce'); ?></label>
                <input type="text" name="api_billing_postcode" id="api_billing_postcode"
                       class="form-control"
                       value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'api_billing_postcode', true)); ?>"/>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="col">
                    <label class="mb-1" for="api_billing_country"><?php esc_html_e('Country', 'megatrader'); ?></label>
                    <select name="api_billing_country" id="api_billing_country"
                            class="form-select form-control woocommerce-select">
                        <option value="" disabled>Country</option>
                        <?php foreach (WC()->countries->get_allowed_countries() as $key => $value): ?>
                            <option value="<?= esc_attr($key) ?>" <?= selected($api_billing_country, $key, false) ?> ><?= esc_html($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="w-phone-full form-group mb-0">
                    <label class="mb-1" for="api_billing_phone"><?php esc_html_e('Phone', 'megatrader'); ?></label>
                    <input type="tel"
                           class="form-control mt-phone-component"
                           name="api_billing_phone" id="api_billing_phone"
                           placeholder="<?php esc_attr_e('Phone Number', 'megatrader'); ?>">
                </div>
            </div>
        </div>

        <div>
            <button type="submit" class="mega-btn-md mega-btn-primary-md" name="mt_save_billing" value="1">
                <?php _e('Save changes', 'woocommerce'); ?>
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('personal-information-form');
        const submitBtn = form.querySelector('button[type="submit"]');
        const PAC_HIDE_CLASS = "pac-hidden";

        const debouncedUpdate = (() => {
            let t;
            return () => {
                clearTimeout(t);
                t = setTimeout(() => {
                    if (typeof jQuery !== "undefined") {
                        jQuery(document.body).trigger("update_checkout");
                    }
                }, 300);
            };
        })();

        function lockStateSelection(stateCode, ttlMs = 7000) {
            if (!stateCode) return;
            const wrapper = document.getElementById("api_billing_state_wrapper");
            if (!wrapper) return;

            const start = Date.now();
            const apply = () => {
                const select = document.getElementById("api_billing_state");
                if (!select) return;
                const opt = [...select.options].find((o) => o.value === stateCode);
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

        function setStateWhenReady(stateCode) {
            if (!stateCode) return;
            const wrapper = document.getElementById("api_billing_state_wrapper");

            const tryApply = () => {
                const select = document.getElementById("api_billing_state");
                if (!select) return false;

                const match = [...select.options].find((o) => o.value === stateCode);
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

        function forceClosePlaces() {
            document.body.classList.add(PAC_HIDE_CLASS);

            const addr = document.getElementById("api_billing_address_1");
            if (addr) addr.blur();

            document.querySelectorAll(".pac-container").forEach((el) => {
                el.innerHTML = '';
                el.removeAttribute("style");
                el.style.display = "none";
                el.setAttribute("aria-hidden", "true");
            });
        }

        function allowPlaces() {
            document.body.classList.remove(PAC_HIDE_CLASS);
            document
                .querySelectorAll('.pac-container[aria-hidden="true"]')
                .forEach((el) => {
                    el.style.display = "";
                    el.removeAttribute("aria-hidden");
                });
        }

        // ========== GOOGLE AUTOCOMPLETE ==========
        const addressInput = document.getElementById("api_billing_address_1");

        if (addressInput && window.google && google.maps && google.maps.places) {
            const autocomplete = new google.maps.places.Autocomplete(addressInput, {
                types: ["address"],
                componentRestrictions: {country: ["us"]},
            });

            // Mantener cerrado de inicio (evita que se abra al re-entrar en "Change")
            forceClosePlaces();

            // Abrir SOLO cuando el usuario interactúa con el input
            const enablePacOnUserInput = () => allowPlaces();
            addressInput.addEventListener("focus", enablePacOnUserInput);
            addressInput.addEventListener("keydown", enablePacOnUserInput);
            addressInput.addEventListener("input", enablePacOnUserInput);

            // Cerrar al salir del input (con pequeño delay para permitir click en la sugerencia)
            addressInput.addEventListener("blur", () => {
                setTimeout(forceClosePlaces, 150);
            });

            // Cerrar si el usuario hace click fuera del input y del dropdown
            document.addEventListener("click", (e) => {
                const pac = document.querySelector(".pac-container");
                if (!pac) return;
                if (e.target?.name === 'billing_address_1' || e.target === addressInput || pac.contains(e.target)) return;
                forceClosePlaces();
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

                document.getElementById("api_billing_address_1").value =
                    fields.billing_address_1;
                document.getElementById("api_billing_city").value = fields.billing_city;
                document.getElementById("api_billing_postcode").value =
                    fields.billing_postcode;

                const countrySelect = document.getElementById("api_billing_country");
                if (fields.billing_country && countrySelect) {
                    const option = [...countrySelect.options].find(
                        (opt) => opt.value === fields.billing_country
                    );
                    if (option) {
                        countrySelect.value = fields.billing_country;
                        countrySelect.classList.add("selected-by-google");
                        countrySelect.dispatchEvent(new Event("change"));
                    }
                }

                setStateWhenReady(fields.billing_state);
                lockStateSelection(fields.billing_state, 7000);

                // Cerrar el dropdown tras seleccionar
                forceClosePlaces();
                debouncedUpdate();
            });
        }

        function setAddressHTML(id, linesArray) {
            const el = document.getElementById(id);
            if (!el) return;
            const esc = (s) =>
                String(s)
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#39;");
            const html = (linesArray || []).filter(Boolean).map(esc).join("<br>");
            el.innerHTML = html;
        }

        function initBillingSummary() {
            const summary = document.getElementById("mt-billing-summary");
            const formBox = document.getElementById("mt-billing-form");
            const changeLn = document.getElementById("mt-billing-change");
            const saveBtn = document.getElementById("mt-save-billing");

            function getStoredState() {
                try {
                    return localStorage.getItem("mt_billing_state") || "";
                } catch (_) {
                    return "";
                }
            }

            function restoreStateIfEmpty() {
                const el = document.getElementById("api_billing_state");
                if (!el) return;

                // usa lo que haya guardado; si no hay, no toques nada
                const saved = getStoredState();
                if (!saved) return;

                // Fuerza el valor guardado aunque el select ya tenga otro (Woo lo puede haber reescrito)
                if (el.tagName === "SELECT") {
                    if (el.value !== saved) {
                        setStateWhenReady(saved);
                        // Mantén el lock un poco más (Woo puede refrescar fragmentos con retraso)
                        lockStateSelection(saved, 7000);
                    }
                } else {
                    if (el.value !== saved) {
                        el.value = saved;
                        el.dispatchEvent(new Event("change", {bubbles: true}));
                    }
                }
            }

            const stEl = document.getElementById("api_billing_state");
            if (stEl && !stEl.dataset.mtRemember) {
                stEl.dataset.mtRemember = "1";
                stEl.addEventListener("change", () => {
                    try {
                        localStorage.setItem("mt_billing_state", stEl.value || "");
                    } catch (_) {
                    }
                    // Recalcula cuando cambie el estado
                    debouncedUpdate();
                });
            }

            const stOrig = document.getElementById("billing_state_current");
            if (stEl && stOrig && !stEl.value) {
                setStateWhenReady(stOrig.value);
                lockStateSelection(stOrig.value, 2000);
            }

            const countryEl = document.getElementById("api_billing_country");
            if (countryEl && !countryEl.dataset.mtReset) {
                countryEl.dataset.mtReset = "1";
                countryEl.addEventListener("change", () => {
                    // Olvida el state guardado
                    try {
                        localStorage.removeItem("mt_billing_state");
                    } catch (_) {
                    }

                    // Limpia el campo state para que Woo repueble según el país
                    const st = document.getElementById("api_billing_state");
                    if (st) {
                        st.value = "";
                        st.dispatchEvent(new Event("change", {bubbles: true}));
                    }

                    // Vuelve a calcular impuestos/totales
                    debouncedUpdate();
                });
            }

            // Recalcular al editar manualmente ZIP, City y Address 1
            ["api_billing_postcode", "api_billing_city", "api_billing_address_1"].forEach((id) => {
                const el = document.getElementById(id);
                if (el && !el.dataset.mtDebounced) {
                    el.dataset.mtDebounced = "1";
                    el.addEventListener("input", debouncedUpdate);
                    el.addEventListener("change", debouncedUpdate);
                }
            });

            function showForm() {
                if (!summary || !formBox) return;
                formBox.classList.remove("d-none");
                summary.classList.add("d-none");
                restoreStateIfEmpty();

                if (typeof jQuery !== "undefined" && jQuery.fn && jQuery.fn.slideDown) {
                    jQuery(formBox).stop(true, true).hide().slideDown(200);
                }
            }

            function showSummary() {
                if (!summary || !formBox) return;
                forceClosePlaces(); // <- cerrarlo al volver al resumen
                summary.classList.remove("d-none");
                formBox.classList.add("d-none");
                if (typeof jQuery !== "undefined" && jQuery.fn && jQuery.fn.slideDown) {
                    jQuery(summary).stop(true, true).hide().slideDown(200);
                }
            }

            // evita listeners duplicados cuando Woo refresca fragmentos
            if (changeLn && !changeLn.dataset.bound) {
                changeLn.dataset.bound = "1";
                changeLn.addEventListener("click", (e) => {
                    e.preventDefault();
                    showForm();
                });
            }
        }

        // Inicializa y reata tras fragment refresh
        initBillingSummary();

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            e.stopPropagation();

            setFullPhoneInput(form, 'api_billing_phone');

            $.preloader.show();
            submitBtn.disabled = true;

            const formData = new FormData(form);
            formData.append('action', 'mt_update_personal_information');

            try {
                const response = await fetch(window.MT_AP.ajaxUrl, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });

                const result = await response.json();

                clearErrorBeforeSendRequest(form);

                if (!result.success) {
                    const errors = result.data?.errors || {};
                    displayGlobalMessage(form, 'Something went wrong. Please try again later.', 'error');

                    Object.entries(errors).forEach(([field, message]) => {
                        const input = document.getElementById(field);
                        if (input) {
                            input.classList.add('is-invalid');

                            const feedback = document.createElement('div');
                            feedback.className = 'invalid-feedback';
                            feedback.textContent = message;
                            input.parentNode.appendChild(feedback);
                        }
                    });

                    submitBtn.disabled = false;
                    return;
                }

                displayGlobalMessage(form, result.data.message);
            } catch (error) {
                console.error('Password change failed:', error);
                displayGlobalMessage(form, 'Unexpected error. Please try again later.', 'error');
            } finally {
                submitBtn.disabled = false;
                $.preloader.hide();
            }
        });
    })
</script>

<script>
    jQuery(document).ready(function ($) {
        $('#api_billing_country').on('input', function () {
            var country = $(this).val();
            var data = {
                action: 'get_cities',
                country: country,
            };

            $.post(woocommerce_params.ajax_url, data, function (response) {
                const element = parseHTMLElement(response);
                element.name = 'api_billing_state';
                element.id = 'api_billing_state';
                element.value = '';
                $('#api_billing_state_wrapper').empty().append(element);
            });

        });
    });
</script>