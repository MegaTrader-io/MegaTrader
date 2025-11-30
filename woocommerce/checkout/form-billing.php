<?php
/**
 * Custom WooCommerce Billing Form
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.2.0
 */
defined('ABSPATH') || exit;

?>

<div class="mb-0">
    <?php if (!is_user_logged_in()): ?>
        <h5 class="mb-6 text text-theme text-size-24"><?php esc_html_e('OR, CEATE A NEW ACCOUNT', 'megatrader'); ?></h5>
        <span><?php esc_html_e(' Set up a new account in just a few steps to start trading instantly.', 'megatrader'); ?></span>
    <?php endif; ?>
    <div class="line-input-no row g-3 <?php if (!is_user_logged_in()): ?>pt-32<?php endif; ?>">
        <div class="col-lg-6">
            <div class="form-group no-label">
                <label class="label"
                    for="billing_first_name"><?php esc_html_e('First Name', 'megatrader'); ?>*</label>
                <input type="text" class="form-control" name="billing_first_name" id="billing_first_name"
                    placeholder="<?php esc_attr_e('First Name', 'megatrader'); ?>"
                    value="<?php echo esc_attr($checkout->get_value('billing_first_name')); ?>">
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group no-label">
                <label class="label" for="billing_last_name"><?php esc_html_e('Last Name', 'megatrader'); ?>*</label>
                <input type="text" class="form-control" name="billing_last_name" id="billing_last_name"
                    placeholder="<?php esc_attr_e('Last Name', 'megatrader'); ?>"
                    value="<?php echo esc_attr($checkout->get_value('billing_last_name')); ?>">
            </div>
        </div>

        <div class="form-group no-label">
            <label class="label" for="billing_email"><?php esc_html_e('Email Address', 'megatrader'); ?>*</label>
            <input type="email" class="form-control" name="billing_email" id="billing_email"
                placeholder="<?php esc_attr_e('Enter your email', 'megatrader'); ?>"
                value="<?php echo esc_attr($checkout->get_value('billing_email')); ?>">
        </div>
        <div class="form-group no-label">
            <label class="label" for="billing_phone"><?php esc_html_e('Phone', 'megatrader'); ?>*</label>
            <input type="tel" class="form-control" name="billing_phone" id="billing_phone"
                placeholder="<?php esc_attr_e('Phone Number', 'megatrader'); ?>"
                value="<?php echo esc_attr($checkout->get_value('billing_phone')); ?>">
        </div>

        <!--
      
        <div class="col-lg-12">
            <div class="form-group no-label">
                <label class="label" for="billing_company"><?php esc_html_e('Company Name', 'megatrader'); ?>*</label>
                <input type="text" class="form-control" name="billing_company" id="billing_company" placeholder="<?php esc_attr_e('Company Name', 'megatrader'); ?>" value="<?php echo esc_attr($checkout->get_value('billing_company')); ?>">
            </div>
        </div>
        -->
        <div class="col-lg-12">
            <div class="form-group no-label">
                <label class="label"
                    for="billing_address_1"><?php esc_html_e('House number and street name', 'megatrader'); ?>*</label>
                <input type="text" class="form-control" name="billing_address_1" id="billing_address_1"
                    placeholder="<?php esc_attr_e('Street address (e.g., 123 Main St)', 'megatrader'); ?>"
                    value="<?php echo esc_attr($checkout->get_value('billing_address_1')); ?>">
            </div>
        </div>
        <div class="col-lg-12">
            <div class="form-group no-label">
                <label class="label"
                    for="billing_address_2"><?php esc_html_e('Aparment, Suite, Unit, etc, (opcional)', 'megatrader'); ?></label>
                <input type="text" class="form-control" name="billing_address_2" id="billing_address_2"
                    placeholder="<?php esc_attr_e('Aparment, Suite, Unit, etc, (opcional)', 'megatrader'); ?>"
                    value="<?php echo esc_attr($checkout->get_value('billing_address_2')); ?>">
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="form-group no-label">
                <label class="label"
                    for="billing_country"><?php esc_html_e('Country/Region', 'megatrader'); ?>*</label>
                <select name="billing_country" id="billing_country" class="form-select form-control woocommerce-select">
                    <?php
                    foreach (WC()->countries->get_allowed_countries() as $key => $value) {
                        echo '<option value="' . esc_attr($key) . '"' . selected($checkout->get_value('billing_country'), $key, false) . '>' . esc_html($value) . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="form-group no-label">
                <label class="label" for="billing_city"><?php esc_html_e('Town / City', 'megatrader'); ?>*</label>
                <input type="text" class="form-control" name="billing_city" id="billing_city"
                    placeholder="<?php esc_attr_e('Town / City', 'megatrader'); ?>"
                    value="<?php echo esc_attr($checkout->get_value('billing_city')); ?>">
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="form-group no-label">
                <label class="label" for="billing_state"><?php esc_html_e('State / County', 'megatrader'); ?>*</label>
                <div id="billing_state_wrapper">
                    <?php
                    $country = $checkout->get_value('billing_country');
                    $state = $checkout->get_value('billing_state');
                    $cities = WC()->countries->get_states($country);

                    if (!empty($cities)) {
                        echo '<select name="billing_state" id="billing_state" class="form-select form-control" required>';
                        echo '<option value="" disabled selected>' . esc_html__('Select An Option', 'megatrader') . '</option>';
                        foreach ($cities as $key => $city) {
                            echo '<option value="' . esc_attr($key) . '"' . selected($state, $key, false) . '>' . esc_html($city) . '</option>';
                        }
                        echo '</select>';
                    } elseif ($country && empty($cities)) {
                        echo '<input type="text" name="billing_state" id="billing_state" class="form-control" placeholder="' . esc_attr__('Enter State / County', 'megatrader') . '" value="' . esc_attr($state) . '" />';
                    } else {
                        echo '<input type="hidden" name="billing_state" id="billing_state" value="' . esc_attr($state) . '" />';
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="form-group no-label">
                <label class="label"
                    for="billing_postcode"><?php esc_html_e('Post code/ZIP*', 'megatrader'); ?>*</label>
                <input type="text" class="form-control" name="billing_postcode" id="billing_postcode"
                    placeholder="<?php esc_attr_e('Post code/ZIP*', 'megatrader'); ?>"
                    value="<?php echo esc_attr($checkout->get_value('billing_postcode')); ?>">
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function ($) {
        $('#billing_country').on('input', function () {
            var country = $(this).val();
            var data = {
                action: 'get_cities',
                country: country,
                state: '<?php echo esc_js($checkout->get_value('billing_state')); ?>'
            };

            $.post(woocommerce_params.ajax_url, data, function (response) {
                $('#billing_state_wrapper').html(response);
            });
        });
    });
</script>