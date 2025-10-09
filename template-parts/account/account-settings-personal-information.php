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

?>
<div>
    <?php get_template_part("template-parts/user-profile-card"); ?>

    <div class="space-y-3">
        <?php wp_nonce_field('mt_save_billing_address', 'mt_billing_nonce'); ?>

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
                <label class="mb-1"
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
                <label class="mb-1" for="personal_email"><?php _e('Email', 'woocommerce'); ?></label>
                <input type="email" readonly name="personal_email" id="personal_email"
                       class="form-control"
                       value="<?php echo $user_email; ?>"/>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <label class="mb-1" for="billing_address_1"><?php _e('Address', 'woocommerce'); ?></label>
                <input type="text" name="billing_address_1" id="billing_address_1"
                       class="form-control"
                       value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'billing_address_1', true)); ?>"/>
            </div>
            <div class="col-lg-6">
                <label class="mb-1" for="billing_city"><?php _e('City', 'woocommerce'); ?></label>
                <input type="text" name="billing_city" id="billing_city"
                       class="form-control"
                       value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'billing_city', true)); ?>"/>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <label class="mb-1" for="billing_state"><?php _e('State', 'woocommerce'); ?></label>
                <input type="text" name="billing_state" id="billing_state"
                       class="form-control"
                       value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'billing_state', true)); ?>"/>
            </div>
            <div class="col-lg-6">
                <label class="mb-1" for="billing_postcode"><?php _e('ZIP Code', 'woocommerce'); ?></label>
                <input type="text" name="billing_postcode" id="billing_postcode"
                       class="form-control"
                       value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'billing_postcode', true)); ?>"/>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="col">
                    <label class="mb-1" for="billing_country"><?php esc_html_e('Country', 'megatrader'); ?></label>
                    <select name="billing_country" id="billing_country"
                            class="form-select form-control woocommerce-select">
                        <option value="" disabled>Country</option>
                        <?php foreach (WC()->countries->get_allowed_countries() as $key => $value): ?>
                            <option value="<?= esc_attr($key) ?>" <?= selected($billing_country, $key, false) ?> ><?= esc_html($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="w-phone-full form-group mb-0">
                    <label class="mb-1" for="personal_phone"><?php esc_html_e('Phone', 'megatrader'); ?></label>
                    <input type="tel"
                           class="form-control"
                           name="billing_phone" id="personal_phone"
                           placeholder="<?php esc_attr_e('Phone Number', 'megatrader'); ?>"
                           value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'billing_phone', true)); ?>">
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
        const preloader = document.querySelector('.preloader');
        const submitBtn = form.querySelector('button[type="submit"]');

        function setPhoneInput(form) {
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
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            e.stopPropagation();

            setPhoneInput(form);

            submitBtn.disabled = true;

            const formData = new FormData(form);
            formData.append('action', 'mt_update_personal_information');

            try {
                const response = await fetch(window.wpAjax.ajaxUrl, {
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
                preloader.style.display = 'none';
            }
        });
    })
</script>