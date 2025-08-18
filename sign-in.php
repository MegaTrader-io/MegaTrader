<?php
/**
 * Template Name: Sign In
 */

ob_start();

if (is_user_logged_in()) {
    wp_redirect(home_url( '/my-account/' ));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login_nonce']) && wp_verify_nonce($_POST['login_nonce'], 'login_form')) {
    $creds = array(
        'user_login'    => sanitize_text_field($_POST['username']),
        'user_password' => $_POST['password'],
        'remember'      => isset($_POST['rememberme']) ? true : false,
    );

    $user = wp_signon($creds, false);

    if (is_wp_error($user)) {
        $error_message = $user->get_error_message();
    } else {
        wp_redirect(home_url( '/my-account/' ));
        exit;
    }
}

ob_end_flush();

get_header(); 

?>

    <div class="space authentication-area">
        <div class="container space-top">
            <div class="authentication-form">
                <div>
                    <h3 class="mb-30">Sign in to your account conpp</h3>
                </div>
                <form method="post" class="woocommerce-form woocommerce-form-login login">
                    <div class="form-group mb-3">
                        <label for="username">Email addressdddddd*</label>
                        <div class="input-group">
                            <input type="email" class="form-control" name="username" placeholder="<?php esc_attr_e('Enter your email', 'woocommerce'); ?>" required>
                            <button type="button" class="get-otp-btn">Get OTP</button>
                        </div>
                    </div>
                    
                    <div class="form-group mb-35">
                        <label for="password">OTP*</label>
                        <input type="text" class="form-control" name="password" placeholder="<?php esc_attr_e('Enter your OTP', 'woocommerce'); ?>" required>
                    </div>

                    <div class="form-group mb-0">
                        <button type="submit" class="ot-btn checkout-login border-0 w-100 rounded-4"><b><?php esc_html_e('Sign In', 'woocommerce'); ?></b></button>
                    </div>
                    <div class="message-container mt-2" style="display: none;">
                        <div class="alert alert-success"></div>
                        <div class="alert alert-danger"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php
get_footer();
?>
