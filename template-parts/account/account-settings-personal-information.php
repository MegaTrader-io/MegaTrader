<div class="woocommerce-MyAccount-content">

    <?php wc_print_notices(); ?>

    <form method="post" class="woocommerce-EditAccountForm edit-account">

        <?php wp_nonce_field('mt_save_billing_address', 'mt_billing_nonce'); ?>

        <div class="line-input-no row g-3">
            <div class="col-lg-6">
                <div class="form-group no-label">
                    <label class="label"
                           for="billing_first_name"><?php esc_html_e('First Name', 'megatrader'); ?></label>
                    <input type="text" class="form-control" name="billing_first_name" id="billing_first_name"
                           placeholder="<?php esc_attr_e('First Name', 'megatrader'); ?>"
                           value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'billing_first_name', true)); ?>">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group no-label">
                    <label class="label" for="billing_last_name"><?php esc_html_e('Last Name', 'megatrader'); ?></label>
                    <input type="text" class="form-control" name="billing_last_name" id="billing_last_name"
                           placeholder="<?php esc_attr_e('Last Name', 'megatrader'); ?>"
                           value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'billing_last_name', true)); ?>">
                </div>
            </div>
        </div>

        <p class="form-row form-row-wide">
            <label for="billing_address_1"><?php _e('Address', 'woocommerce'); ?>&nbsp;<span
                    class="required">*</span></label>
            <input type="text" name="billing_address_1" id="billing_address_1"
                   value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'billing_address_1', true)); ?>"/>
        </p>

        <p class="form-row form-row-wide">
            <label for="billing_city"><?php _e('City', 'woocommerce'); ?>&nbsp;<span
                    class="required">*</span></label>
            <input type="text" name="billing_city" id="billing_city"
                   value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'billing_city', true)); ?>"/>
        </p>

        <p class="form-row form-row-first">
            <label for="billing_state"><?php _e('State', 'woocommerce'); ?></label>
            <input type="text" name="billing_state" id="billing_state"
                   value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'billing_state', true)); ?>"/>
        </p>

        <p class="form-row form-row-last">
            <label for="billing_postcode"><?php _e('ZIP Code', 'woocommerce'); ?></label>
            <input type="text" name="billing_postcode" id="billing_postcode"
                   value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'billing_postcode', true)); ?>"/>
        </p>

        <div class="form-group no-label">
            <label class="label" for="billing_phone"><?php esc_html_e('Phone', 'megatrader'); ?>*</label>
            <input type="tel" class="form-control" name="billing_phone" id="billing_phone"
                   placeholder="<?php esc_attr_e('Phone Number', 'megatrader'); ?>"
                   value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'billing_phone', true)); ?>">
        </div>

        <p class="form-row form-row-wide">
            <label for="billing_email"><?php _e('Email', 'woocommerce'); ?>&nbsp;<span
                    class="required">*</span></label>
            <input type="email" name="billing_email" id="billing_email"
                   value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'billing_email', true)); ?>"/>
        </p>

        <p>
            <button type="submit" class="mega-btn-md mega-btn-primary-md" name="mt_save_billing" value="1">
                <?php _e('Save changes', 'woocommerce'); ?>
            </button>
        </p>
    </form>
</div>