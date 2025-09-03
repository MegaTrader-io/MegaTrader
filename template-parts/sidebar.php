<?php 

defined('ABSPATH') || exit;

// Get current user object
$current_user = wp_get_current_user();

// Retrieve first and last name from user meta
$first_name = get_user_meta($current_user->ID, 'first_name', true);
$last_name = get_user_meta($current_user->ID, 'last_name', true);

// Determine display name: prefer "First Last", fallback to WordPress display_name
if ($first_name || $last_name) {
    $display_name = trim($first_name . ' ' . $last_name);
} else {
    $display_name = $current_user->display_name;
}

// Get billing country code and map to full country name
$billing_country_code = get_user_meta($current_user->ID, 'billing_country', true);
$countries = WC()->countries->countries;
$billing_country = isset($countries[$billing_country_code])
    ? $countries[$billing_country_code]
    : '';

// User email address
$user_email = $current_user->user_email;

// Build uppercase initials from first and last name
$initials = '';
if ($first_name) {
    $initials .= mb_substr($first_name, 0, 1);
}
if ($last_name) {
    $initials .= mb_substr($last_name, 0, 1);
}
$initials = mb_strtoupper($initials);

/// Genera la URL de Gravatar con default=404
$avatar_url = get_avatar_url($current_user->ID, [
    'size' => 64,
    'default' => '404',
]);

// Intenta hacer una petición HEAD para validar existencia real
$response = wp_safe_remote_head($avatar_url, [
    'timeout' => 2,
]);

$has_real_avatar = false;
if (!is_wp_error($response)) {
    $code = wp_remote_retrieve_response_code($response);
    // DEBUG: loguea el código HTTP
    error_log("Gravatar HTTP status for user {$current_user->ID}: {$code}");
    if (200 === $code) {
        $has_real_avatar = true;
    }
}

$logout_url = wp_logout_url();

?>

<aside class="mt-sidebar w-100 d-flex gap-32 flex-column">
    <div class="d-flex gap-3 align-items-center justify-content-start">
        <div class="align-items-center d-flex flex-fill gap-3">
            <?php if ($has_real_avatar): ?>
                <div class="avatar-initials">
                    <img src="<?php echo esc_url($avatar_url); ?>"
                        alt="<?php echo esc_attr($display_name); ?>" />
                </div>
            <?php else: ?>

                <div class="avatar-initials">
                    <?php echo esc_html($initials); ?>
                </div>
            <?php endif; ?>
            <div class="flex-fill d-flex flex-column">
                <div class="text-white text-16px fw-medium">
                    <?php echo esc_html($display_name); ?>
                </div>

                <?php if ($billing_country): ?>
                    <div class="user-country text-a8a29e text-16px fw-medium">
                        <?php echo esc_html($billing_country); ?>
                    </div>
                <?php endif; ?>
                <div class="text-a8a29e text-14px-line-20px fw-medium">
                    <?php echo esc_html($user_email); ?>
                </div>
            </div>
        </div>
        <div class="my-acount-logout">
            <a href="<?php echo esc_url($logout_url); ?>" class="logout-link" aria-label="Logout">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="none">
                    <mask id="mask0_12101_26191" style="mask-type:alpha" maskUnits="userSpaceOnUse"
                        x="0" y="0" width="24" height="24">
                        <rect width="24" height="24" fill="#D9D9D9" />
                    </mask>
                    <g mask="url(#mask0_12101_26191)">
                        <path
                            d="M5 21C4.45 21 3.97917 20.8042 3.5875 20.4125C3.19583 20.0208 3 19.55 3 19V5C3 4.45 3.19583 3.97917 3.5875 3.5875C3.97917 3.19583 4.45 3 5 3H12V5H5V19H12V21H5ZM16 17L14.625 15.55L17.175 13H9V11H17.175L14.625 8.45L16 7L21 12L16 17Z"
                            fill="white" />
                    </g>
                </svg>
            </a>
        </div>
    </div>
    <div class="traders-area">
        <a href="https://app.megatrader.io/"
            class="mega-btn-md mega-btn-secondary-md w-100">
            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="24" viewBox="0 0 25 24"
                fill="none" style="margin-right:8px;">
                <mask id="mask0_12909_1841" mask-type="alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                    width="25" height="24">
                    <rect x="0.5" width="24" height="24" fill="#D9D9D9" />
                </mask>
                <g mask="url(#mask0_12909_1841)">
                    <path d="M16.5 20V13H20.5V20H16.5ZM10.5 20V4H14.5V20H10.5ZM4.5 20V9H8.5V20H4.5Z"
                        fill="white" />
                </g>
            </svg>
            Traders area
        </a>

    </div>
    <div class="mt-card gap-3">
        <div class="text-white text-size-20 fw-medium text-uppercase">
            Explore the plans</div>
        <div class="text-16px fw-medium text-a8a29e text-wrap">
            Find the perfect plan to enhance your experience.</div>
        <div class="btn-challenge">
            <a href="<?php echo esc_url(home_url('/subscriptions')); ?>"
                class="mega-btn-md mega-btn-default-md w-100">
                Buy a new chanllenge
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="24" viewBox="0 0 25 24"
                    fill="none">
                    <mask id="mask0_12909_529" style="mask-type:alpha" maskUnits="userSpaceOnUse"
                        x="0" y="0" width="24" height="24">
                        <rect width="24" height="24" fill="#D9D9D9" />
                    </mask>
                    <g mask="url(#mask0_12909_529)">
                        <path d="M12.6 12L8 7.4L9.4 6L15.4 12L9.4 18L8 16.6L12.6 12Z"
                            fill="black" />
                    </g>
                </svg>
            </a>
        </div>
    </div>
</aside>