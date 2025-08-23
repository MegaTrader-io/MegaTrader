<?php

$errors = wc_get_notices('error');

$error_firstname = $error_lastname =
$error_address = $error_billing_country = $error_billing_city =
$error_address_optional =
$error_email = $error_phone =
$error_password = $error_confirm_password =
$error_privacy_policy = '';

$field_errors = array();

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

$default_country = 'US';
$valid_states = WC()->countries->get_states($default_country);
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

    <div class="full-name-wrapper">
        <div class="full-name-wrapper__field">
            <input type="text"
                   class="form-control <?= MT_WC_Error::has_error('firstname') ? 'auth-form--error-message' : '' ?>"
                   name="firstname" id="firstname"
                   autocomplete="firstname"
                   placeholder="First Name"
                   value="<?php echo (!empty($_POST['firstname']) && is_string($_POST['firstname'])) ? esc_attr(wp_unslash($_POST['firstname'])) : ''; ?>"
                   required aria-required="true"/><?php // @codingStandardsIgnoreLine ?>

            <?php if (MT_WC_Error::has_error('firstname')): ?>
                <span id="error-firstname"
                      class="auth-form__error_message"> <?= MT_WC_Error::get_error('firstname') ?></span>
            <?php endif; ?>
        </div>

        <div class="full-name-wrapper__field">
            <input type="text"
                   class="form-control <?= MT_WC_Error::has_error('lastname') ? 'auth-form--error-message' : '' ?>"
                   name="lastname" id="lastname"
                   autocomplete="lastname"
                   placeholder="Last Name"
                   value="<?php echo (!empty($_POST['lastname']) && is_string($_POST['lastname'])) ? esc_attr(wp_unslash($_POST['lastname'])) : ''; ?>"
                   required aria-required="true"/><?php // @codingStandardsIgnoreLine ?>

            <?php if (MT_WC_Error::has_error('lastname')): ?>
                <span id="error-lastname"
                      class="auth-form__error_message"> <?= MT_WC_Error::get_error('lastname') ?></span>
            <?php endif; ?>
        </div>
    </div>

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

    <div class="form-group auth-form__phone-wrapper">
        <input type="tel"
               class="form-control <?= MT_WC_Error::has_error('phone') ? 'auth-form--error-message' : '' ?>"
               name="phone" id="phone"
               autocomplete="phone"
               placeholder="Phone Number"
               value="<?php echo (!empty($_POST['phone']) && is_string($_POST['phone'])) ? esc_attr(wp_unslash($_POST['phone'])) : ''; ?>"
               required aria-required="true"/><?php // @codingStandardsIgnoreLine ?>

        <?php if (MT_WC_Error::has_error('phone')): ?>
            <span id="error-phone"
                  class="auth-form__error_message"> <?= MT_WC_Error::get_error('phone') ?></span>
        <?php endif; ?>
    </div>

    <div class="auth-form__container">
        <div class="form-group auth-form__address-wrapper">
            <input type="text"
                   class="form-control <?= MT_WC_Error::has_error('address') ? 'auth-form--error-message' : '' ?>"
                   name="address" id="address"
                   autocomplete="address"
                   placeholder="Address"
                   value="<?php echo (!empty($_POST['address']) && is_string($_POST['address'])) ? esc_attr(wp_unslash($_POST['address'])) : ''; ?>"
                   required aria-required="true"/><?php // @codingStandardsIgnoreLine ?>

            <?php if (MT_WC_Error::has_error('phone')): ?>
                <span id="error-address"
                      class="auth-form__error_message"> <?= MT_WC_Error::get_error('address') ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group auth-form__address-wrapper">
            <input type="text"
                   class="form-control <?= MT_WC_Error::has_error('address_optional') ? 'auth-form--error-message' : '' ?>"
                   name="address_optional" id="address_optional"
                   autocomplete="address_optional"
                   placeholder="Apartment, suite, etc. (optional)"
                   value="<?php echo (!empty($_POST['address_optional']) && is_string($_POST['address_optional'])) ? esc_attr(wp_unslash($_POST['address_optional'])) : ''; ?>"
                   required aria-required="true"/><?php // @codingStandardsIgnoreLine ?>

            <?php if (MT_WC_Error::has_error('address_optional')): ?>
                <span id="error-address_optional"
                      class="auth-form__error_message"> <?= MT_WC_Error::get_error('address_optional') ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="auth-form__container">
        <div class="col">
            <select name="billing_country" id="billing_country"
                    class="form-select form-control woocommerce-select <?= MT_WC_Error::has_error('billing_country') ? 'auth-form--error-message' : '' ?>">
                <option value="" disabled>Country</option>
                <?php foreach (WC()->countries->get_allowed_countries() as $key => $value): ?>
                    <option value="<?= esc_attr($key) ?>" <?= selected($default_country, $key, false) ?> ><?= esc_html($value) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (MT_WC_Error::has_error('billing_country')): ?>
                <span id="error-billing_country"
                      class="auth-form__error_message"> <?= MT_WC_Error::get_error('billing_country') ?></span>
            <?php endif; ?>
        </div>
        <div class="col">
            <input type="text"
                   class="form-control <?= MT_WC_Error::has_error('billing_city') ? 'auth-form--error-message' : '' ?>"
                   name="billing_city" id="billing_city"
                   placeholder="City"
                   value="<?php echo (!empty($_POST['billing_city']) && is_string($_POST['billing_city'])) ? esc_attr(wp_unslash($_POST['billing_city'])) : ''; ?>"
                   required aria-required="true"/><?php // @codingStandardsIgnoreLine ?>

            <?php if (MT_WC_Error::has_error('billing_city')): ?>
                <span id="error-billing_city"
                      class="auth-form__error_message"> <?= MT_WC_Error::get_error('billing_city') ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="auth-form__container">
        <div class="col">
            <select name="billing_state" id="billing_state"
                    class="form-select form-control woocommerce-select <?= MT_WC_Error::has_error('billing_state') ? 'auth-form--error-message' : '' ?>">
                <option value=""
                        disabled <?php echo (!empty($_POST['billing_state']) && is_string($_POST['billing_state'])) ? '' : 'selected'; ?>>
                    State
                </option>
                <?php foreach ($valid_states as $key => $value): ?>
                    <option value="<?= esc_attr($key) ?>" <?php echo (!empty($_POST['billing_state']) && is_string($_POST['billing_state'])) ? 'selected' : ''; ?> ><?= esc_html($value) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (MT_WC_Error::has_error('billing_state')): ?>
                <span id="error-billing_state"
                      class="auth-form__error_message"> <?= MT_WC_Error::get_error('billing_state') ?></span>
            <?php endif; ?>
        </div>
        <div class="col">
            <input type="text"
                   class="form-control <?= MT_WC_Error::has_error('billing_postcode') ? 'auth-form--error-message' : '' ?>"
                   name="billing_postcode" id="billing_postcode"
                   placeholder="Zip Code"
                   value="<?php echo (!empty($_POST['billing_postcode']) && is_string($_POST['billing_postcode'])) ? esc_attr(wp_unslash($_POST['billing_postcode'])) : ''; ?>"
                   required aria-required="true"/><?php // @codingStandardsIgnoreLine ?>

            <?php if (MT_WC_Error::has_error('billing_postcode')): ?>
                <span id="error-billing_postcode"
                      class="auth-form__error_message"> <?= MT_WC_Error::get_error('billing_postcode') ?></span>
            <?php endif; ?>
        </div>
    </div>

    <?php if ('no' === get_option('woocommerce_registration_generate_password')) : ?>
        <div class="auth-form__container">
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
       href="<?= home_url('/auth/login') ?>">
        <div class="text-neutral-50 text-base font-medium uppercase leading-normal">
            GO TO LOGIN
        </div>
    </a>
</div>