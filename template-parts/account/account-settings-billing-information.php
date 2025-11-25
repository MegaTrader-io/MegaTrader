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
            <div class="col-lg-12">
                <div class="col">
                    <select name="billing_country" id="billing_country"
                            class="form-select form-control woocommerce-select">
                        <option value="" disabled>Country</option>
                        <?php foreach (WC()->countries->get_allowed_countries() as $key => $value): ?>
                            <option value="<?= esc_attr($key) ?>" <?= selected($billing_country, $key, false) ?> ><?= esc_html($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <input type="text" name="billing_city" id="billing_city"
                       class="form-control"
                       placeholder="Town / City"
                       value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'billing_city', true)); ?>"/>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
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
        </div>

        <div class="row">
            <div class="col-lg-12">
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

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            e.stopPropagation();

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