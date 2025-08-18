<?php

$errors = wc_get_notices('error');

$error_fullname = $error_email = $error_phone = $error_password = $error_confirm_password = $error_privacy_policy = '';

foreach ($errors as $error) {
    if (!isset($error['data']['field'])) {
        continue;
    }
    switch ($error['data']['field']) {
        case 'fullname':
            $error_fullname = $error['notice'];
            break;
        case 'email':
            $error_email = $error['notice'];
            break;
        case 'phone':
            $error_phone = $error['notice'];
            break;
        case 'password':
            $error_password = $error['notice'];
            break;
        case 'confirm_password':
            $error_confirm_password = $error['notice'];
            break;
        case 'privacy_policy':
            // Puedes mostrarlo como un help-text bajo el checkbox o arriba del form.
            $error_privacy_policy = $error['notice'];
            break;
        // case 'general': // si quieres un mensaje general arriba
        //     $error_general = $error['notice'];
        //     break;
    }
}

?>

<?php //do_action('woocommerce_before_customer_login_form'); ?>

<img src="<?php echo get_template_directory_uri(); ?>/assets/img/landing-page/logo-mt.svg"
     width="72" height="72" alt="Logo MegaTraderX"/>

<div>
    <h2 class="auth-form__title"><?php esc_html_e('REGISTER', 'woocommerce'); ?></h2>
    <p class="auth-form__subtitle auth-form__subtitle--mb-none">Create your account to get
        started!</p>
</div>

<form class="auth-form__form-wrapper" method="post"
      novalidate>

    <?php do_action('woocommerce_register_form_start'); ?>

    <div>
        <input type="text"
               class="form-control <?= !empty($error_fullname) ? 'auth-form--error-message' : '' ?>"
               name="fullname" id="fullname"
               autocomplete="fullname"
               placeholder="Full Name"
               value="<?php echo (!empty($_POST['fullname']) && is_string($_POST['fullname'])) ? esc_attr(wp_unslash($_POST['fullname'])) : ''; ?>"
               required aria-required="true"/><?php // @codingStandardsIgnoreLine ?>

        <?php if (!empty($error_fullname)): ?>
            <span id="error-fullname"
                  class="auth-form__error_message"> <?= $error_fullname ?></span>
        <?php endif; ?>
    </div>

    <div>
        <input type="text"
               class="form-control <?= !empty($error_email) ? 'auth-form--error-message' : '' ?>"
               name="email" id="email"
               autocomplete="email"
               placeholder="Email"
               value="<?php echo (!empty($_POST['email']) && is_string($_POST['email'])) ? esc_attr(wp_unslash($_POST['email'])) : ''; ?>"
               required aria-required="true"/><?php // @codingStandardsIgnoreLine ?>

        <?php if (!empty($error_email)): ?>
            <span id="error-email"
                  class="auth-form__error_message"> <?= $error_email ?></span>
        <?php endif; ?>
    </div>

    <div class="form-group auth-form__phone-wrapper">
        <input type="tel"
               class="form-control <?= !empty($error_phone) ? 'auth-form--error-message' : '' ?>"
               name="phone" id="phone"
               autocomplete="phone"
               placeholder="Phone Number"
               value="<?php echo (!empty($_POST['phone']) && is_string($_POST['phone'])) ? esc_attr(wp_unslash($_POST['phone'])) : ''; ?>"
               required aria-required="true"/><?php // @codingStandardsIgnoreLine ?>

        <?php if (!empty($error_phone)): ?>
            <span id="error-phone"
                  class="auth-form__error_message"> <?= $error_phone ?></span>
        <?php endif; ?>
    </div>

    <?php if ('no' === get_option('woocommerce_registration_generate_password')) : ?>
        <div class="password-wrapper">
            <input class="form-control password-wrapper__password <?= !empty($error_password) ? 'auth-form--error-message' : '' ?>"
                   type="password"
                   name="password"
                   id="password"
                   autocomplete="current-password"
                   placeholder="Password" required
                   value="<?php echo (!empty($_POST['password']) && is_string($_POST['password'])) ? esc_attr(wp_unslash($_POST['password'])) : ''; ?>"
                   aria-required="true"/>

            <?php if (!empty($error_password)): ?>
                <span id="error-password"
                      class="auth-form__error_message"> <?= $error_password ?></span>
            <?php endif; ?>
        </div>

        <div class="password-wrapper">
            <input class="form-control password-wrapper__password <?= !empty($error_password) ? 'auth-form--error-message' : '' ?>"
                   type="password"
                   name="confirm_password"
                   id="confirm_password"
                   autocomplete="current-confirm_password"
                   placeholder="Confirm Password" required
                   value="<?php echo (!empty($_POST['confirm_password']) && is_string($_POST['confirm_password'])) ? esc_attr(wp_unslash($_POST['confirm_password'])) : ''; ?>"
                   aria-required="true"/>

            <?php if (!empty($error_password)): ?>
                <span id="error-confirm-password"
                      class="auth-form__error_message"> <?= $error_password ?></span>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php do_action('woocommerce_login_form'); ?>

    <div class="">
        <div class="checkbox-container">
            <input class="checkbox-container__input" name="privacy_policy" type="checkbox"
                   id="privacy_policy"
                   value="1" <?php echo (!empty($_POST['privacy_policy']) && is_string($_POST['privacy_policy'])) ? 'checked' : ''; ?>/>
            <label for="privacy_policy" class="checkbox-container__label m-0">
                Agree to our
                <a class="auth-form__terms-and-conditions" href="#" data-bs-toggle="modal"
                   data-bs-target="#termsModal"><?php esc_html_e('Terms of Service', 'woocommerce'); ?>
                </a>
                and
                <a class="auth-form__terms-and-conditions" href="#" data-bs-toggle="modal"
                   data-bs-target="#privacyModal"><?php esc_html_e('Privacy Policy', 'woocommerce'); ?>
                </a>
            </label>
        </div>

        <?php if (!empty($error_privacy_policy)): ?>
            <span id="error-confirm-password"
                  class="auth-form__error_message"> <?= $error_privacy_policy ?></span>
        <?php endif; ?>
    </div>
    <?php do_action('woocommerce_register_form'); ?>
    <p class="">
        <?php wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce'); ?>
        <button type="submit"
                class="btn w-100 mega-btn-md mega-btn-primary-md w-100 <?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>"
                name="register"
                value="<?php esc_attr_e('Register', 'woocommerce'); ?>"><?php esc_html_e('REGISTER', 'woocommerce'); ?>
        </button>
    </p>

    <div class="google-signin-btn">
        <div class="googlesitekit-sign-in-with-google__frontend-output-button">
            <!-- Here's where googlesitekit injects btn iframe --></div>
    </div>

    <?php do_action('woocommerce_login_form_end'); ?>
</form>

<div class="auth-form__footer-wrapper">
    <div class="auth-form__footer-dont-have-an-account">
        Already have an account?
    </div>
    <a class="btn w-100 mega-btn-md mega-btn-secondary-md w-100"
       href="<?= home_url('/my-account') ?>">
        <div class="text-neutral-50 text-base font-medium uppercase leading-normal">
            GO TO LOGIN
        </div>
    </a>
</div>