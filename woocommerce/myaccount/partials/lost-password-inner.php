<?php

$errors = wc_get_notices('error');

$error_user_login = '';

foreach ($errors as $error) {
    if (!isset($error['data']['field'])) {
        continue;
    }
    switch ($error['data']['field']) {
        case 'user_login':
            $error_user_login = $error['notice'];
            break;
    }
}

do_action('woocommerce_before_lost_password_form');

?>

    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/landing-page/logo-mt.svg"
         width="72" height="72" alt="Logo MegaTraderX"/>

    <div>
        <h2 class="auth-form__title"><?php esc_html_e('RESET PASSWORD', 'woocommerce'); ?></h2>
        <p class="auth-form__subtitle auth-form__subtitle--mb-none">
            Enter your email to reset your password.
        </p>
    </div>

    <form method="post" class="auth-form__form-wrapper lost_reset_password" novalidate>
        <p class="d-none"><?php echo apply_filters('woocommerce_lost_password_message', esc_html__('Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'woocommerce')); ?></p><?php // @codingStandardsIgnoreLine ?>

        <div>
            <input type="text" name="user_login" id="user_login" autocomplete="username" required
                   aria-required="true"
                   class="form-control <?= !empty($error_user_login) ? 'auth-form--error-message' : '' ?>"
                   placeholder="Email">

            <?php if (!empty($error_user_login)): ?>
                <span id="error-username"
                      class="auth-form__error_message"> <?= $error_user_login ?></span>
            <?php endif; ?>
        </div>

        <div class="clear"></div>

        <?php do_action('woocommerce_lostpassword_form'); ?>

        <div class="form-group mb-0">
            <input type="hidden" name="wc_reset_password" value="true"/>
            <button type="submit"
                    class="btn w-100 mega-btn-md mega-btn-primary-md w-100 <?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>"
                    value="<?php esc_attr_e('Reset password', 'woocommerce'); ?>"><?php esc_html_e('SEND EMAIL', 'woocommerce'); ?>
            </button>
        </div>

        <div class="auth-form__footer-wrapper">
            <a class="mega-btn-md mega-btn-outline-md w-100"
               href="<?= home_url('/auth/login') ?>">
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

        <?php wp_nonce_field('lost_password', 'woocommerce-lost-password-nonce'); ?>
    </form>
<?php

do_action('woocommerce_after_lost_password_form');