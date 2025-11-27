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