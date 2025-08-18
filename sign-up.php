<?php
/**
 * Template Name: Sign Up
 */

 if (is_user_logged_in()) {
    wp_redirect(home_url('/my-account/'));
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_user'])) {
    $username = sanitize_user($_POST['username']);
    $email = sanitize_email($_POST['email']);
    $password = $_POST['password'];
    $terms = isset($_POST['terms']);
    $receive_emails = isset($_POST['receive_emails']);

    $errors = array();
    
    // Basic validation
    if (empty($username) || empty($email) || empty($password)) {
        $errors[] = 'Please fill in all required fields.';
    }
    if (!is_email($email)) {
        $errors[] = 'Invalid email address.';
    }
    if (username_exists($username) || email_exists($email)) {
        $errors[] = 'Username or email already exists.';
    }
    if (!$terms) {
        $errors[] = 'You must agree to the terms and privacy policy.';
    }
    
    // If no errors, proceed with registration
    if (empty($errors)) {
        $user_id = wp_create_user($username, $password, $email);
        if (!is_wp_error($user_id)) {
            // Optionally, handle user meta here (e.g., storing 'receive_emails' preference)
            wp_redirect(home_url('/sign-in/'));
            exit; 
        } else {
            $errors[] = $user_id->get_error_message();
        }
    }
}

get_header(); 

?>

    <div class="authentication-area">
        <div class="container">
            <div class="authentication-form">
                <div data-cue="slideInUp">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="logo d-inline-block">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/logo.svg'); ?>" alt="">
                    </a>
                    <h3>Create a new <span>account</span></h3>
                    <p>Megatrader, we designed websites that have been used by more than 500k+ users.</p>
                </div>
                <?php if (!empty($errors)): ?>
                    <div class="form-group mb-4">
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error): ?>
                                <p><?php echo esc_html($error); ?></p>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <form method="post" data-cue="slideInUp" data-delay="200">
                    <div class="form-group mb-4">
                        <label for="username" class="label">Username*</label>
                        <input type="text" name="username" class="form-control" placeholder="Enter your username" required>
                    </div>
                    <div class="form-group mb-4">
                        <label for="email" class="label">Email*</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter your email*" required>
                    </div>
                    <div class="form-group mb-4">
                        <label for="password" class="label">Password*</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                    </div>
                    <div class="form-group mb-4">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="terms" value="1" id="flexCheckDefault" required>
                            <label class="form-check-label" for="flexCheckDefault">
                                Agree to our <a href="<?php echo esc_url(home_url('/terms-of-use/')); ?>">Terms of Use</a> and <a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>">Privacy Policy*</a>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="receive_emails" value="1" id="flexCheckOptional">
                            <label class="form-check-label" for="flexCheckOptional">
                                Agree to receive Megatrader emails & updates (Optional)
                            </label>
                        </div>
                    </div>
                    <div class="form-group mb-4">
                        <button type="submit" name="register_user" class="default-btn border-0 w-100">Register</button>
                    </div>
                    <div class="form-group mb-4 text-center">
                        <p class="m-auto">Already have an account? <a href="<?php echo esc_url(wp_login_url()); ?>">Sign in</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php
get_footer();
?>
