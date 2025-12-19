<?php
/**
 * WooCommerce OTP Login Functions ============================================================
 * Updated to work on both checkout and sign-in pages
 */

// 1. Generate and send OTP
function generate_and_send_otp() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wc_otp_nonce')) {
        wp_send_json_error('Invalid security token');
        return;
    }

    $email = sanitize_email($_POST['email']);

    if (!is_email($email)) {
        wp_send_json_error('Invalid email address');
        return;
    }

    $otp = sprintf("%06d", mt_rand(0, 999999));
    $user = get_user_by('email', $email);

    if ($user) {
        update_user_meta($user->ID, 'wc_login_otp', [
            'code'    => wp_hash($otp),
            'expires' => time() + (15 * 60)
        ]);

        $subject = 'One-Time Password (OTP) Request';
        $username = $user->display_name ?: $email;

        $message = '
        <div style="background: url(https://subscriptions.megatrader.io/wp-content/uploads/2025/05/dot-bg.png) 0% 0% / 5px 5px repeat, #131210; padding: 40px;">
            <table style="max-width:600px; margin:auto; background-color:#1e1e1e; border-radius:8px; overflow:hidden; font-family:\'Space Grotesk\', Arial, sans-serif;">
                <tr>
                    <td style="background-color:#F1A035; padding:20px; text-align:center; color:#000;">
                        <h2 style="color:#000; font-weight:500; margin:0;">One-Time Password (OTP) Request</h2>
                    </td>
                </tr>
                <tr>
                    <td style="padding:30px; color:#A8A29E; font-size:16px;">
                        <p style="margin-top:0;">Hi <strong>' . esc_html($username) . '</strong>,</p>
                        <p style="font-size:14px">Someone has requested a one-time password (OTP) to verify access to your account on <strong style="color:#F1A035;">MEGATRADER</strong>.</p>
                        <div style="background-color:#1e1e1e; border-radius:6px; margin:20px 0;">
                            <p style="color:#F1A035; font-size:18px;">' . esc_html($otp) . '</p>
                            <p style="margin:5px 0 0; font-size:14px"><strong>Expires in:</strong> 15 minutes</p>
                        </div>
                        <p style="font-size:14px">If you did not make this request, you can safely ignore this email. Otherwise, enter the code above to continue.</p>
                        <p style="margin-top:40px;font-size:14px;">Thanks for using <strong>MEGATRADER</strong>.</p>
                    </td>
                </tr>
            </table>
        </div>
        ';

        $headers = ['Content-Type: text/html; charset=UTF-8'];
        if (wp_mail($email, $subject, $message, $headers)) {
            wp_send_json_success('OTP sent successfully');
            } else {
                wp_send_json_error('Failed to send OTP email. Please try again.');
            }
    } else {
        wp_send_json_error('User not found. Please check the email and try again.');
    }

    die();
}
add_action('wp_ajax_nopriv_generate_otp', 'generate_and_send_otp');
add_action('wp_ajax_generate_otp', 'generate_and_send_otp');

// 2. Verify OTP and log in user
function verify_otp_and_login() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wc_otp_nonce')) {
        wp_send_json_error('Invalid security token');
        return;
    }

    $email = sanitize_email($_POST['email']);
    $otp   = sanitize_text_field($_POST['otp']);
    $user  = get_user_by('email', $email);

    if (!$user) {
        wp_send_json_error('User not found');
        return;
    }

    $stored_otp = get_user_meta($user->ID, 'wc_login_otp', true);

    if (
        !$stored_otp ||
        !is_array($stored_otp) ||
        time() > $stored_otp['expires'] ||
        !hash_equals($stored_otp['code'], wp_hash($otp))
    ) {
        wp_send_json_error('Invalid or expired OTP');
        return;
    }

    delete_user_meta($user->ID, 'wc_login_otp');
    wp_set_auth_cookie($user->ID);

    $redirect_url = isset($_POST['redirect_url']) ? esc_url_raw($_POST['redirect_url']) : home_url('/my-account/');
    wp_send_json_success(['redirect' => $redirect_url]);

    die();
}
add_action('wp_ajax_nopriv_verify_otp', 'verify_otp_and_login');

// 3. Enqueue external OTP script
add_action('wp_enqueue_scripts', 'enqueue_otp_script');
function enqueue_otp_script() {

    $is_step2 = is_page_template('woocommerce/checkout/step_2.php');

    if ( ! is_checkout() && ! is_page_template('sign-in.php') && ! $is_step2 ) {
        return;
    }

    wp_enqueue_script(
        'otp-script',
        get_template_directory_uri() . '/assets/js/otp.js',
        ['jquery'],
        filemtime(get_template_directory() . '/assets/js/otp.js'),
        true
    );

    wp_localize_script('otp-script', 'wc_otp_data', [
        'nonce'    => wp_create_nonce('wc_otp_nonce'),
        'ajaxurl'  => admin_url('admin-ajax.php')
    ]);
}
