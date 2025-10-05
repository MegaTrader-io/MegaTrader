<form method="post" class="space-y-3">
    <div class="row">
        <div class="col-lg-12">
            <label class="mb-1"
                   for="current_password"><?php esc_html_e('Current password', 'megatrader'); ?></label>
            <input type="password"
                   class="form-control <?= MT_WC_Error::has_error('current_password') ? 'is-invalid' : '' ?>"
                   name="current_password" id="current_password"
                   placeholder="<?php esc_attr_e('Current password', 'megatrader'); ?>"
                   value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'current_password', true)); ?>">

            <?php if (MT_WC_Error::has_error('current_password')): ?>
                <span id="error-current_password"
                      class="invalid-feedback"> <?= MT_WC_Error::get_error('current_password') ?></span>
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <label class="mb-1"
                   for="new_password"><?php esc_html_e('New password', 'megatrader'); ?></label>
            <input type="password"
                   class="form-control <?= MT_WC_Error::has_error('new_password') ? 'is-invalid' : '' ?>"
                   name="new_password" id="new_password"
                   placeholder="<?php esc_attr_e('New password', 'megatrader'); ?>"
                   value="">

            <?php if (MT_WC_Error::has_error('new_password')): ?>
                <span id="error-new_password"
                      class="invalid-feedback"> <?= MT_WC_Error::get_error('new_password') ?></span>
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <label class="mb-1"
                   for="confirm_password"><?php esc_html_e('Confirm password', 'megatrader'); ?></label>
            <input type="password"
                   class="form-control <?= MT_WC_Error::has_error('confirm_password') ? 'is-invalid' : '' ?>"
                   name="confirm_password" id="confirm_password"
                   placeholder="<?php esc_attr_e('Confirm password', 'megatrader'); ?>"
                   value="">

            <?php if (MT_WC_Error::has_error('confirm_password')): ?>
                <span id="error-confirm_password"
                      class="invalid-feedback"> <?= MT_WC_Error::get_error('confirm_password') ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div>
        <button type="submit" class="mega-btn-md mega-btn-primary-md" name="mt_save_password" value="1">
            <?php _e('Save changes', 'woocommerce'); ?>
        </button>
    </div>
</form>