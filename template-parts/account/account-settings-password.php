<form method="post" class="space-y-3">
    <div>
        <label class="mb-1"
               for="current_password"><?php esc_html_e('Current password', 'megatrader'); ?></label>
        <div class="mt-password-wrapper">
            <input type="password"
                   class="mt-password-wrapper__password form-control <?= MT_WC_Error::has_error('current_password') ? 'is-invalid' : '' ?>"
                   name="current_password" id="current_password"
                   placeholder="<?php esc_attr_e('Current Password', 'megatrader'); ?>"
                   value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'current_password', true)); ?>">
        </div>
        <?php if (MT_WC_Error::has_error('current_password')): ?>
            <span id="error-current_password"
                  class="invalid-feedback"> <?= MT_WC_Error::get_error('current_password') ?></span>
        <?php endif; ?>
    </div>


    <div>
        <label class="mb-1"
               for="new_password"><?php esc_html_e('New password', 'megatrader'); ?></label>
        <div class="mt-password-wrapper">
            <input type="password"
                   class="mt-password-wrapper__password form-control <?= MT_WC_Error::has_error('new_password') ? 'is-invalid' : '' ?>"
                   name="new_password" id="new_password"
                   placeholder="<?php esc_attr_e('New Password', 'megatrader'); ?>"
                   value="">
        </div>
        <?php if (MT_WC_Error::has_error('new_password')): ?>
            <span id="error-new_password"
                  class="invalid-feedback"> <?= MT_WC_Error::get_error('new_password') ?></span>
        <?php endif; ?>
    </div>

    <div>
        <label class="mb-1"
               for="confirm_password"><?php esc_html_e('Confirm password', 'megatrader'); ?></label>
        <div class="mt-password-wrapper">
            <input type="password"
                   class="mt-password-wrapper__password form-control <?= MT_WC_Error::has_error('confirm_password') ? 'is-invalid' : '' ?>"
                   name="confirm_password" id="confirm_password"
                   placeholder="<?php esc_attr_e('Confirm New Password', 'megatrader'); ?>"
                   value="">
        </div>
        <?php if (MT_WC_Error::has_error('confirm_password')): ?>
            <span id="error-confirm_password"
                  class="invalid-feedback"> <?= MT_WC_Error::get_error('confirm_password') ?></span>
        <?php endif; ?>
    </div>

    <div>
        <button type="submit" class="mega-btn-md mega-btn-primary-md" name="mt_save_password" value="1">
            <?php _e('Save changes', 'woocommerce'); ?>
        </button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const passwordWrapper = document.querySelectorAll('.mt-password-wrapper');

        passwordWrapper.forEach(wrapper => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.classList.add('mt-password-wrapper__btn_eye');
            wrapper.appendChild(btn);

            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const inputPassword = e.currentTarget.parentElement.querySelector('input');
                if (!inputPassword) {
                    return;
                }

                inputPassword.type = inputPassword.type.toLowerCase() === 'password' ? 'text' : 'password';
            })
        });
    });
</script>