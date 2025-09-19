<?php

defined('ABSPATH') || exit;

$avatar_size = $args['avatar_size'] ?? 64;
$go_to_dashboard = $args['go_to_dashboard'] ?? false;;
$hide_user_information = $args['hide_user_information'] ?? false;

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
        'size' => $avatar_size,
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

<div class="mt-my-profile d-flex gap-3 align-items-center justify-content-start">
    <?php if ($go_to_dashboard) : ?>
        <a href="<?= home_url('/my-account/') ?>" class="avatar-area align-items-center d-flex flex-fill gap-3">
            <?php get_template_part('template-parts/avatar', null, [
                    'avatar_url' => $avatar_url,
                    'avatar_size' => $avatar_size,
                    'has_real_avatar' => $has_real_avatar,
                    'initials' => $initials,
                    'display_name' => $display_name,
                    'user_email' => $user_email,
                    'billing_country' => $billing_country,
                    'hide_user_information' => $hide_user_information
            ]); ?>
        </a>
    <?php else: ?>
        <div class="align-items-center d-flex flex-fill gap-3">
            <?php get_template_part('template-parts/avatar', null, [
                    'avatar_url' => $avatar_url,
                    'avatar_size' => $avatar_size,
                    'has_real_avatar' => $has_real_avatar,
                    'initials' => $initials,
                    'display_name' => $display_name,
                    'user_email' => $user_email,
                    'billing_country' => $billing_country
            ]); ?>
        </div>
    <?php endif; ?>

    <button class="mt-my-profile__notification mega-btn-md mega-btn-secondary-md w-100" type="button" aria-label="Notifications">
        <div class="mt-icon mt-icon-white mt-icon_notifications"></div>
    </button>
    <div class="my-acount-logout">
        <a href="<?php echo esc_url($logout_url); ?>" class="logout-link" aria-label="Logout">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                 fill="none">
                <mask id="mask0_12101_26191" style="mask-type:alpha" maskUnits="userSpaceOnUse"
                      x="0" y="0" width="24" height="24">
                    <rect width="24" height="24" fill="#D9D9D9"/>
                </mask>
                <g mask="url(#mask0_12101_26191)">
                    <path
                            d="M5 21C4.45 21 3.97917 20.8042 3.5875 20.4125C3.19583 20.0208 3 19.55 3 19V5C3 4.45 3.19583 3.97917 3.5875 3.5875C3.97917 3.19583 4.45 3 5 3H12V5H5V19H12V21H5ZM16 17L14.625 15.55L17.175 13H9V11H17.175L14.625 8.45L16 7L21 12L16 17Z"
                            fill="white"/>
                </g>
            </svg>
        </a>
    </div>
</div>