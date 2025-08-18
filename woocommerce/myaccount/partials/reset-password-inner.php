<?php
$errors = wc_get_notices('error');

$error_password_1 = $error_password_2 = '';

foreach ($errors as $error) {
    if (!isset($error['data']['field'])) {
        continue;
    }
    switch ($error['data']['field']) {
        case 'password_1':
            $error_password_1 = $error['notice'];
            break;
        case 'password_2':
            $error_password_2 = $error['notice'];
            break;
    }
}

//do_action('woocommerce_before_reset_password_form');

?>

    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/landing-page/logo-mt.svg"
         width="72" height="72" alt="Logo MegaTraderX"/>

    <div>
        <h2 class="auth-form__title"><?php esc_html_e('SET PASSWORD', 'woocommerce'); ?></h2>
        <p class="auth-form__subtitle auth-form__subtitle--mb-none">
            Enter and confirm your new password.
        </p>
    </div>

    <form method="post" class="auth-form__form-wrapper" novalidate>

        <div class="password-wrapper">
            <input class="form-control password-wrapper__password <?= !empty($error_password_1) ? 'auth-form--error-message' : '' ?>"
                   type="password"
                   name="password_1"
                   id="password_1"
                   autocomplete="new-password"
                   placeholder="New Password" required
                   aria-required="true"/>

            <?php if (!empty($error_password_1)): ?>
                <span id="error-password"
                      class="auth-form__error_message"> <?= $error_password_1 ?></span>
            <?php endif; ?>
        </div>

        <div class="password-wrapper">
            <input class="form-control password-wrapper__password <?= !empty($error_password_2) ? 'auth-form--error-message' : '' ?>"
                   type="password"
                   name="password_2"
                   id="password_2"
                   autocomplete="new-password"
                   required
                   placeholder="Confirm New Password"
                   aria-required="true"/>

            <?php if (!empty($error_password_2)): ?>
                <span id="error-password"
                      class="auth-form__error_message"> <?= $error_password_2 ?></span>
            <?php endif; ?>
        </div>

        <input type="hidden" name="reset_key" value="<?php echo esc_attr($args['key']); ?>"/>
        <input type="hidden" name="reset_login" value="<?php echo esc_attr($args['login']); ?>"/>

        <div class="clear"></div>

        <?php do_action('woocommerce_resetpassword_form'); ?>

        <div class="form-group mb-0">
            <input type="hidden" name="wc_reset_password" value="true"/>
            <button type="submit"
                    class="btn w-100 mega-btn-md mega-btn-primary-md w-100 <?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>"
                    value="<?php esc_attr_e('Save', 'woocommerce'); ?>"><?php esc_html_e('UPDATE', 'woocommerce'); ?></button>
        </div>

        <div class="auth-form__footer-wrapper">
            <a class="mega-btn-md mega-btn-outline-md w-100"
               href="<?= home_url('/my-account') ?>">
                <div class="text-neutral-50 text-base font-medium uppercase leading-normal">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <mask id="mask0_13482_8278" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                              width="24" height="24">
                            <rect width="24" height="24" fill="#D9D9D9"/>
                        </mask>
                        <g mask="url(#mask0_13482_8278)">
                            <path d="M14 18L8 12L14 6L15.4 7.4L10.8 12L15.4 16.6L14 18Z" fill="white"/>
                        </g>
                    </svg>

                    RETURN TO LOGIN
                </div>
            </a>
        </div>

        <?php wp_nonce_field('reset_password', 'woocommerce-reset-password-nonce'); ?>
    </form>
<?php
do_action('woocommerce_after_reset_password_form');