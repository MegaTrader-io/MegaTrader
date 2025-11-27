<?php
$success = wc_get_notices('success');
$errors = wc_get_notices('error');

foreach ($errors as $error) {
    if (!isset($error['data']['field'])) {
        continue;
    }

    MT_WC_Error::$field_errors[$error['data']['field']] = $error['notice'];
}

$billing_country = esc_attr(get_user_meta(get_current_user_id(), 'billing_country', true));
if (!$billing_country) {
    $billing_country = 'US';
}

$current_user = wp_get_current_user();
$user_email = $current_user->user_email;

$valid_states = WC()->countries->get_states($billing_country);
$billing_state = esc_attr(get_user_meta(get_current_user_id(), 'billing_state', true));

?>
<div>
    <?php wp_nonce_field('mt_save_billing_address', 'mt_billing_nonce'); ?>

    <div class="space-y-3">
        <div class="row">
            <div class="col-lg-6">
                <label class="mb-1"
                       for="billing_first_name"><?php esc_html_e('First Name', 'megatrader'); ?></label>
                <input type="text"
                       class="form-control"
                       name="billing_first_name" id="billing_first_name"
                       placeholder="<?php esc_attr_e('First Name', 'megatrader'); ?>"
                       value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'billing_first_name', true)); ?>">
            </div>
            <div class="col-lg-6">
                <label class="mb-1 mt-3 mt-lg-0"
                       for="billing_last_name"><?php esc_html_e('Last Name', 'megatrader'); ?></label>
                <input type="text"
                       class="form-control"
                       name="billing_last_name" id="billing_last_name"
                       placeholder="<?php esc_attr_e('Last Name', 'megatrader'); ?>"
                       value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'billing_last_name', true)); ?>">
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <label class="mb-1"
                       for="billing_email"><?php esc_html_e('Email', 'megatrader'); ?></label>
                <input type="email" class="form-control" name="billing_email" id="billing_email"
                       placeholder="<?php esc_attr_e('Enter your email', 'megatrader'); ?>"
                       value="<?php echo esc_attr($user_email); ?>" readonly>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="form-group mb-0">
                    <label class="label mb-1" for="wp_billing_phone"><?php esc_html_e('Phone', 'megatrader'); ?></label>
                    <input type="tel" class="form-control mt-phone-component" name="wp_billing_phone" id="wp_billing_phone"
                           placeholder="<?php esc_attr_e('Phone Number', 'megatrader'); ?>"
                           value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'billing_phone', true)); ?>">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <label class="mb-1" for="billing_address_1"><?php _e('Address', 'woocommerce'); ?></label>
                <input type="text" name="billing_address_1" id="billing_address_1"
                       class="form-control"
                       placeholder="Street address (e.g. 123 Main Str)"
                       value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'billing_address_1', true)); ?>"/>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <input type="text" name="billing_address_2" id="billing_address_2"
                       class="form-control"
                       placeholder="Apartment, Suite, Unit, etc, (optional)"
                       value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'billing_address_2', true)); ?>"/>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3">
                <div class="col">
                    <label class="label mb-1"
                           for="billing_country"><?php esc_html_e('Country', 'megatrader'); ?></label>
                    <select name="billing_country" id="billing_country"
                            class="form-select form-control woocommerce-select">
                        <option value="" disabled>Country</option>
                        <?php foreach (WC()->countries->get_allowed_countries() as $key => $value): ?>
                            <option value="<?= esc_attr($key) ?>" <?= selected($billing_country, $key, false) ?> ><?= esc_html($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-lg-3 mt-3 mt-lg-0">
                <label class="label mb-1" for="billing_city"><?php esc_html_e('Town / City', 'megatrader'); ?></label>
                <input type="text" name="billing_city" id="billing_city"
                       class="form-control"
                       placeholder="Town / City"
                       value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'billing_city', true)); ?>"/>
            </div>
            <div class="col-lg-3 mt-3 mt-lg-0">
                <label class="label mb-1" for="billing_state"><?php esc_html_e('State', 'megatrader'); ?></label>
                <div id="billing_state_wrapper">
                    <?php if (!empty($valid_states)): ?>
                        <select name="billing_state" id="billing_state"
                                class="form-select form-control woocommerce-select">
                            <option value=""
                                    disabled>
                                State
                            </option>
                            <?php foreach ($valid_states as $key => $value): ?>
                                <option value="<?= esc_attr($key) ?>" <?= $key == $billing_state ? 'selected' : ''; ?> ><?= esc_html($value) ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php elseif (is_string($billing_state)): ?>
                        <input type="text" name="billing_state" id="billing_state"
                               class="form-control"
                               value="<?php echo esc_attr($billing_state); ?>"/>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-3 mt-3 mt-lg-0">
                <label class="label mb-1"
                       for="billing_postcode"><?php esc_html_e('Post code/ZIP*', 'megatrader'); ?></label>
                <input type="text" name="billing_postcode" id="billing_postcode"
                       class="form-control"
                       placeholder="Post code/ZIP*"
                       value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'billing_postcode', true)); ?>"/>
            </div>
        </div>
        <div>
            <button type="submit" class="mega-btn-md mega-btn-primary-md" name="mt_save_billing" value="1">
                <?php _e('Save details', 'woocommerce'); ?>
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('billing-information-form');
        const submitBtn = form.querySelector('button[type="submit"]');

        const protectedErrors = new Map();

        const validationRules = {
            billing_first_name: [
                (value) =>
                    value.trim() !== "" || "Billing First Name is a required field",
            ],
            billing_last_name: [
                (value) => value.trim() !== "" || "Billing Last Name is a required field",
            ],
            wp_billing_phone: [
                (value) => value.trim() !== "" || "Billing Phone is a required field.",
            ],
            billing_address_1: [
                (value) =>
                    value.trim() !== "" || "Billing Street address is a required field.",
            ],
            billing_city: [
                (value) =>
                    value.trim() !== "" || "Billing Town / City is a required field.",
            ],
            billing_postcode: [
                (value) => value.trim() !== "" || "Billing ZIP code is a required field.",
            ],
            billing_country: [
                (value) => value.trim() !== "" || "Billing Country is a required field.",
            ],
            billing_state: [
                (value) => value.trim() !== "" || "Billing State is a required field.",
            ],
        };

        function validateFormFields(values, rules) {
            const errors = {};
            for (const [field, ruleSet] of Object.entries(rules)) {
                const inputEl = document.querySelector(`[name="${field}"]`);
                if (!inputEl) continue;

                const value = values[field] || "";
                for (const rule of ruleSet) {
                    const result = rule(value);
                    if (result !== true) {
                        errors[field] = result;
                        break;
                    }
                }
            }
            return errors;
        }

        function showErrors(form, errors) {
            for (const [field, message] of Object.entries(errors)) {
                const input = form.querySelector(`[name="${field}"]`);
                if (!input) continue;

                const errorNode = document.createElement("div");
                errorNode.className = Selector.ErrorMessageClass;
                errorNode.textContent = message;

                // Prevent Douplicate Errors
                const existingErrorNode = input.parentNode.querySelector(
                    `.${Selector.ErrorMessageClass}`
                );
                if (existingErrorNode) {
                    existingErrorNode.remove();
                }

                // Insert New Error
                input.parentElement.appendChild(errorNode);
                input.classList.add(Selector.InvalidFieldClass);
                protectedErrors.set(field, errorNode);
            }
        }

        function startErrorProtection() {
            const observer = new MutationObserver(() => {
                for (const [field, node] of protectedErrors.entries()) {
                    const input = document.querySelector(`[name="${field}"]`);
                    if (!input) continue;

                    const existing = input.parentNode.querySelector(".invalid-feedback");
                    if (!existing) {
                        input.parentElement.appendChild(node);
                        input.classList.add("is-invalid");
                    }
                }
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true,
            });
        }

        startErrorProtection();

        function validateBillingFormOnly() {
            const container = form || document;

            const val = (id) => document.getElementById(id)?.value?.trim() || "";

            const values = {
                billing_first_name: val("billing_first_name"),
                billing_last_name: val("billing_last_name"),
                wp_billing_phone: val("wp_billing_phone"),
                billing_address_1: val("billing_address_1"),
                billing_city: val("billing_city"),
                billing_postcode: val("billing_postcode"),
                billing_country: val("billing_country"),
                billing_state: val("billing_state"),
            };

            const rules = {...validationRules};

            const errors = validateFormFields(values, rules);

            // Validación extra con intl-tel-input (si está activo)
            try {
                const telInput = document.getElementById("wp_billing_phone");
                const iti = window.itiRefs[wp_billing_phone];
                if (telInput && iti && typeof iti.isValidNumber === "function") {
                    if (!iti.isValidNumber()) {
                        errors.billing_phone = "Please enter a valid phone number.";
                    } else {
                        // guarda en formato E.164 para el servidor
                        const full = iti.getNumber();
                        if (full) telInput.value = full;
                    }
                }
            } catch (_) {
            }

            if (Object.keys(errors).length) {
                showErrors(container, errors);

                // focus/scroll al primer inválido
                const firstInvalid =
                    container.querySelector("." + Selector.InvalidFieldClass) ||
                    container.querySelector("." + Selector.ErrorMessageClass)
                        ?.previousElementSibling;

                if (firstInvalid && typeof firstInvalid.scrollIntoView === "function") {
                    firstInvalid.scrollIntoView({behavior: "smooth", block: "center"});
                    setTimeout(() => firstInvalid.focus && firstInvalid.focus(), 250);
                }
                return false;
            }

            return true;
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            e.stopPropagation();

            if (!validateBillingFormOnly()) {
                return;
            }

            setFullPhoneInput(form, 'wp_billing_phone');

            protectedErrors.clear();
            clearErrorBeforeSendRequest(form);

            $.preloader.show();
            submitBtn.disabled = true;

            const formData = new FormData(form);
            formData.append('action', 'mt_update_billing_information');

            try {
                const response = await fetch(window.MT_AP.ajaxUrl, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });

                const result = await response.json();

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
        $('#billing_country').on('input', function () {
            var country = $(this).val();
            var data = {
                action: 'get_cities',
                country: country,
                state: '<?php echo $billing_state; ?>'
            };

            $.post(woocommerce_params.ajax_url, data, function (response) {
                $('#billing_state_wrapper').html(response);
            });
        });
    });
</script>