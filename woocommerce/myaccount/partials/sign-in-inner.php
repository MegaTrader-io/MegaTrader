<?php

$successes = wc_get_notices('success');
$errors = wc_get_notices('error');

$error_username = $error_password = '';

foreach ($errors as $error) {
    if (!isset($error['data']['field'])) {
        continue;
    }
    switch ($error['data']['field']) {
        case 'username':
            $error_username = $error['notice'];
            break;
        case 'password':
            $error_password = $error['notice'];
            break;
        // case 'general': // si quieres un mensaje general arriba
        //     $error_general = $error['notice'];
        //     break;
    }
}

$product_id = null;
if (isset($_GET['redirect_to'])) {
    $redirect_to = rawurldecode($_GET['redirect_to']);
    $product_id = mt_extract_add_to_cart_id($redirect_to);
}

//do_action('woocommerce_before_customer_login_form');
?>

<?php foreach ($successes as $success) : ?>
    <div class="woocommerce-notices-wrapper">
        <div class="woocommerce-message" role="alert" tabindex="-1">
            <?= $success['notice'] ?>
        </div>
    </div>
<?php endforeach; ?>

    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/landing-page/logo-mt.svg"
         width="72" height="72" alt="Logo MegaTraderX"/>

    <div>
        <h2 class="auth-form__title"><?php esc_html_e('SIGN IN', 'woocommerce'); ?></h2>
        <p class="auth-form__subtitle auth-form__subtitle--mb-none">Welcome back! Please enter your
            details.</p>
    </div>

    <?php if (!empty($product_id)) : ?>
        <?php get_template_part('template-parts/plan-detail-selection', null, ['card_product_id' => $product_id]); ?>
    <?php endif; ?>

    <form id="form-login" class="auth-form__form-wrapper" method="post"
          novalidate <?php do_action('woocommerce_register_form_tag'); ?>>

        <?php //do_action('woocommerce_register_form_start'); ?>

        <div>
            <input type="text"
                   class="form-control <?= !empty($error_username) ? 'auth-form--error-message' : '' ?>"
                   name="username" id="username"
                   autocomplete="username"
                   placeholder="Email"
                   value="<?php echo (!empty($_POST['username']) && is_string($_POST['username'])) ? esc_attr(wp_unslash($_POST['username'])) : ''; ?>"
                   required aria-required="true"/><?php // @codingStandardsIgnoreLine ?>

            <?php if (!empty($error_username)): ?>
                <span id="error-username"
                      class="auth-form__error_message"> <?= $error_username ?></span>
            <?php endif; ?>
        </div>

        <div class="password-wrapper">
            <input class="form-control password-wrapper__password <?= !empty($error_password) ? 'auth-form--error-message' : '' ?>"
                   type="password"
                   name="password"
                   id="password"
                   autocomplete="current-password"
                   placeholder="Password" required
                   aria-required="true"/>

            <?php if (!empty($error_password)): ?>
                <span id="error-password"
                      class="auth-form__error_message"> <?= $error_password ?></span>
            <?php endif; ?>
        </div>

        <?php do_action('woocommerce_login_form'); ?>

        <div class="d-flex justify-content-between align-items-center gap-2">
            <div class="checkbox-container">
                <input class="checkbox-container__input" name="rememberme" type="checkbox"
                       id="rememberme" value="forever"/>
                <label for="rememberme" class="checkbox-container__label m-0">
                    <span class="auth-form__remember-link"><?php esc_html_e('Remember me', 'woocommerce'); ?></span>
                </label>
            </div>
            <a href="<?php echo home_url('/auth/lost-password'); ?>"
               class="auth-form__forgot-password"><?php esc_html_e('Forgot Password?', 'woocommerce'); ?>
            </a>
        </div>
        <?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>
        <p class="">
            <button type="submit"
                    class="btn w-100 mega-btn-md mega-btn-primary-md w-100 <?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>"
                    name="login"
                    value="<?php esc_attr_e('Log in', 'woocommerce'); ?>"><?php esc_html_e('SIGN IN', 'woocommerce'); ?></button>
        </p>

        <div class="google-signin-btn">
            <div class="googlesitekit-sign-in-with-google__frontend-output-button">
                <!-- Here's where googlesitekit injects btn iframe --></div>
        </div>

        <?php do_action('woocommerce_login_form_end'); ?>
    </form>

<?php if ('yes' === get_option('woocommerce_enable_myaccount_registration')) : ?>
    <div class="auth-form__footer-wrapper">
        <div class="auth-form__footer-dont-have-an-account">
            Don’t have an account?
        </div>
        <?php
        $url = home_url('/auth/register');
        if (isset($_GET['redirect_to'])) {
            $url = add_query_arg('redirect_to', rawurlencode($_GET['redirect_to']), $url);
        }
        ?>
        <a class="btn w-100 mega-btn-md mega-btn-secondary-md w-100"
           href="<?= $url ?>">
            <div class="text-neutral-50 text-base font-medium uppercase leading-normal">Create
                account
            </div>
        </a>
    </div>
<?php endif; ?>