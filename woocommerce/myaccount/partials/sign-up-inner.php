<?php

$errors = wc_get_notices('error');

foreach ($errors as $error) {
    if (!isset($error['data']['field'])) {
        continue;
    }

    MT_WC_Error::$field_errors[$error['data']['field']] = $error['notice'];
}

$product_id = null;
if (isset($_GET['redirect_to'])) {
    $redirect_to = rawurldecode($_GET['redirect_to']);
    $product_id = mt_extract_add_to_cart_id($redirect_to);
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

<?php if ($product_id !== null): ?>
    <?php get_template_part('template-parts/plan-detail-selection', null, ['card_product_id' => $product_id]); ?>
<?php endif; ?>

<form id="register-form" class="auth-form__form-wrapper" method="post"
      novalidate>

    <?php do_action('woocommerce_register_form_start'); ?>

    <div>
        <input type="text"
               class="form-control <?= MT_WC_Error::has_error('email') ? 'auth-form--error-message' : '' ?>"
               name="email" id="email"
               autocomplete="email"
               placeholder="Email Address"
               value="<?php echo (!empty($_POST['email']) && is_string($_POST['email'])) ? esc_attr(wp_unslash($_POST['email'])) : ''; ?>"
               required aria-required="true"/><?php // @codingStandardsIgnoreLine ?>

        <?php if (MT_WC_Error::has_error('email')): ?>
            <span id="error-email"
                  class="auth-form__error_message"> <?= MT_WC_Error::get_error('email') ?></span>
        <?php endif; ?>
    </div>

    <?php if ('no' === get_option('woocommerce_registration_generate_password')) : ?>
        <div class="password-wrapper">
            <input class="form-control password-wrapper__password <?= MT_WC_Error::has_error('password') ? 'auth-form--error-message' : '' ?>"
                   type="password"
                   name="password"
                   id="password"
                   autocomplete="current-password"
                   placeholder="Password" required
                   value="<?php echo (!empty($_POST['password']) && is_string($_POST['password'])) ? esc_attr(wp_unslash($_POST['password'])) : ''; ?>"
                   aria-required="true"/>

            <?php if (MT_WC_Error::has_error('password')): ?>
                <span id="error-password"
                      class="auth-form__error_message"> <?= MT_WC_Error::get_error('password') ?></span>
            <?php endif; ?>
        </div>
        <div class="password-wrapper">
            <input class="form-control password-wrapper__password <?= MT_WC_Error::has_error('confirm_password') ? 'auth-form--error-message' : '' ?>"
                   type="password"
                   name="confirm_password"
                   id="confirm_password"
                   autocomplete="current-confirm_password"
                   placeholder="Confirm Password" required
                   value="<?php echo (!empty($_POST['confirm_password']) && is_string($_POST['confirm_password'])) ? esc_attr(wp_unslash($_POST['confirm_password'])) : ''; ?>"
                   aria-required="true"/>

            <?php if (MT_WC_Error::has_error('confirm_password')): ?>
                <span id="error-confirm-password"
                      class="auth-form__error_message"> <?= MT_WC_Error::get_error('confirm_password') ?></span>
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

        <?php if (MT_WC_Error::has_error('privacy_policy')): ?>
            <span id="error-confirm-password"
                  class="auth-form__error_message"> <?= MT_WC_Error::get_error('privacy_policy') ?></span>
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
    <?php
    $url = home_url('/auth/login');
    if (isset($_GET['redirect_to'])) {
        $url = add_query_arg('redirect_to', rawurlencode($_GET['redirect_to']), $url);
    }
    ?>
    <a class="btn w-100 mega-btn-md mega-btn-secondary-md w-100"
       href="<?= $url ?>">
        <div class="text-neutral-50 text-base font-medium uppercase leading-normal">
            GO TO LOGIN
        </div>
    </a>
</div>