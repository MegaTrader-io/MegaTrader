<div class="space-y-3">
    <?php wp_nonce_field('mt_save_password', 'mt_password_nonce'); ?>

    <div>
        <label class="mb-1"
               for="current_password"><?php esc_html_e('Current password', 'megatrader'); ?></label>
        <div class="mt-password-wrapper">
            <input type="password"
                   class="mt-password-wrapper__password form-control <?= MT_WC_Error::has_error('current_password') ? 'is-invalid' : '' ?>"
                   name="current_password" id="current_password"
                   value="">
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
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('change-password-form');
        const preloader = document.querySelector('.preloader');
        const passwordWrapper = document.querySelectorAll('.mt-password-wrapper');

        passwordWrapper.forEach(wrapper => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.classList.add('mt-password-wrapper__btn_eye');
            wrapper.appendChild(btn);

            wrapper.querySelectorAll('input').forEach(input => {
                input.addEventListener('input', function (ev) {
                    ev.currentTarget.classList.remove('is-invalid');
                    ev.currentTarget.parentElement.querySelector('.invalid-feedback').remove();
                })
            })

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

        const submitBtn = form.querySelector('button[type="submit"]');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            e.stopPropagation();

            // Deshabilitar botón mientras se envía
            submitBtn.disabled = true;

            // Crear objeto FormData con todos los campos del formulario
            const formData = new FormData(form);
            formData.append('action', 'mt_update_password'); // Acción AJAX obligatoria

            try {
                // Enviar petición al endpoint AJAX de WordPress
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

                    // Si hay errores por campo
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

                form.reset();
                displayGlobalMessage(form, result.data.message);
            } catch (error) {
                console.error('Password change failed:', error);
                displayGlobalMessage(form, 'Unexpected error. Please try again later.', 'error');
            } finally {
                submitBtn.disabled = false;
                preloader.style.display = 'none';
            }
        });
    });
</script>