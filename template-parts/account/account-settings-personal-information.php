<?php

$errors = wc_get_notices('error');

final class MT_WC_Error
{
    public static $field_errors = array();

    public static function has_error($field): bool
    {
        return isset(self::$field_errors[$field]) && !empty(self::$field_errors[$field]);
    }

    public static function get_error($field)
    {
        if (!isset(self::$field_errors[$field])) {
            return '';
        }

        return self::$field_errors[$field];
    }
}

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

$valid_states = WC()->countries->get_states($billing_country);

?>
<div class="woocommerce-MyAccount-content">
    <?php get_template_part("template-parts/user-profile-card"); ?>

    <?php wc_print_notices(); ?>

    <form method="post" class="space-y-4">
        <?php wp_nonce_field('mt_save_billing_address', 'mt_billing_nonce'); ?>

        <div class="row">
            <div class="col-lg-6">
                <label class="mb-1"
                       for="billing_first_name"><?php esc_html_e('First Name', 'megatrader'); ?></label>
                <input type="text"
                       class="form-control <?= MT_WC_Error::has_error('billing_first_name') ? 'is-invalid' : '' ?>"
                       name="billing_first_name" id="billing_first_name"
                       placeholder="<?php esc_attr_e('First Name', 'megatrader'); ?>"
                       value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'billing_first_name', true)); ?>">

                <?php if (MT_WC_Error::has_error('billing_first_name')): ?>
                    <span id="error-billing_first_name"
                          class="invalid-feedback"> <?= MT_WC_Error::get_error('billing_first_name') ?></span>
                <?php endif; ?>
            </div>
            <div class="col-lg-6">
                <label class="mb-1"
                       for="billing_last_name"><?php esc_html_e('Last Name', 'megatrader'); ?></label>
                <input type="text"
                       class="form-control <?= MT_WC_Error::has_error('billing_last_name') ? 'is-invalid' : '' ?>"
                       name="billing_last_name" id="billing_last_name"
                       placeholder="<?php esc_attr_e('Last Name', 'megatrader'); ?>"
                       value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'billing_last_name', true)); ?>">

                <?php if (MT_WC_Error::has_error('billing_last_name')): ?>
                    <span id="error-billing_last_name"
                          class="invalid-feedback"> <?= MT_WC_Error::get_error('billing_last_name') ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <label class="mb-1" for="billing_email"><?php _e('Email', 'woocommerce'); ?></label>
                <input type="email" readonly name="billing_email" id="billing_email"
                       class="form-control <?= MT_WC_Error::has_error('billing_email') ? 'is-invalid' : '' ?>"
                       value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'billing_email', true)); ?>"/>

                <?php if (MT_WC_Error::has_error('billing_email')): ?>
                    <span id="error-billing_email"
                          class="invalid-feedback"> <?= MT_WC_Error::get_error('billing_email') ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <label class="mb-1" for="billing_address_1"><?php _e('Address', 'woocommerce'); ?></label>
                <input type="text" name="billing_address_1" id="billing_address_1"
                       class="form-control <?= MT_WC_Error::has_error('billing_address_1') ? 'is-invalid' : '' ?>"
                       value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'billing_address_1', true)); ?>"/>
                <?php if (MT_WC_Error::has_error('billing_address_1')): ?>
                    <span id="error-billing_address_1"
                          class="invalid-feedback"> <?= MT_WC_Error::get_error('billing_address_1') ?></span>
                <?php endif; ?>
            </div>
            <div class="col-lg-6">
                <label class="mb-1" for="billing_city"><?php _e('City', 'woocommerce'); ?></label>
                <input type="text" name="billing_city" id="billing_city"
                       class="form-control <?= MT_WC_Error::has_error('billing_city') ? 'is-invalid' : '' ?>"
                       value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'billing_city', true)); ?>"/>
                <?php if (MT_WC_Error::has_error('billing_city')): ?>
                    <span id="error-billing_city"
                          class="invalid-feedback"> <?= MT_WC_Error::get_error('billing_city') ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <label class="mb-1" for="billing_state"><?php _e('State', 'woocommerce'); ?></label>
                <input type="text" name="billing_state" id="billing_state"
                       class="form-control <?= MT_WC_Error::has_error('billing_state') ? 'is-invalid' : '' ?>"
                       value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'billing_state', true)); ?>"/>
                <?php if (MT_WC_Error::has_error('billing_state')): ?>
                    <span id="error-billing_state"
                          class="invalid-feedback"> <?= MT_WC_Error::get_error('billing_state') ?></span>
                <?php endif; ?>
            </div>
            <div class="col-lg-6">
                <label class="mb-1" for="billing_postcode"><?php _e('ZIP Code', 'woocommerce'); ?></label>
                <input type="text" name="billing_postcode" id="billing_postcode"
                       class="form-control  <?= MT_WC_Error::has_error('billing_postcode') ? 'is-invalid' : '' ?>"
                       value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'billing_postcode', true)); ?>"/>

                <?php if (MT_WC_Error::has_error('billing_postcode')): ?>
                    <span id="error-billing_postcode"
                          class="invalid-feedback"> <?= MT_WC_Error::get_error('billing_postcode') ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="col">
                    <label class="mb-1" for="billing_phone"><?php esc_html_e('Country', 'megatrader'); ?></label>
                    <select name="billing_country" id="billing_country"
                            class="form-select form-control woocommerce-select <?= MT_WC_Error::has_error('billing_country') ? 'is-invalid' : '' ?>">
                        <option value="" disabled>Country</option>
                        <?php foreach (WC()->countries->get_allowed_countries() as $key => $value): ?>
                            <option value="<?= esc_attr($key) ?>" <?= selected($billing_country, $key, false) ?> ><?= esc_html($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (MT_WC_Error::has_error('billing_country')): ?>
                        <span id="error-billing_country"
                              class="invalid-feedback"> <?= MT_WC_Error::get_error('billing_country') ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-6 w-phone-full">
                <label class="mb-1" for="billing_phone"><?php esc_html_e('Phone', 'megatrader'); ?></label>
                <input type="tel" class="form-control <?= MT_WC_Error::has_error('billing_phone') ? 'is-invalid' : '' ?>" name="billing_phone" id="billing_phone"
                       placeholder="<?php esc_attr_e('Phone Number', 'megatrader'); ?>"
                       value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'billing_phone', true)); ?>">
                <?php if (MT_WC_Error::has_error('billing_phone')): ?>
                    <span id="error-billing_phone"
                          class="invalid-feedback"> <?= MT_WC_Error::get_error('billing_phone') ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div>
            <button type="submit" class="mega-btn-md mega-btn-primary-md" name="mt_save_billing" value="1">
                <?php _e('Save changes', 'woocommerce'); ?>
            </button>
        </div>
    </form>
</div>