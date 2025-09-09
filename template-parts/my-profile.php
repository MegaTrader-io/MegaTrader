<?php

defined('ABSPATH') || exit;

$avatar_size = $args['avatar_size'] ?? 64;
$go_to_dashboard = $args['go_to_dashboard'] ?? false;
$hidden_email = $args['hidden_email'] ?? false;

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

function wrapper_avatar_initials($fragment, $avatar_size, $go_to_dashboard)
{
    if ($go_to_dashboard) {
        $url = home_url('/my-account/');
        return <<<HTML
<a href="{$url}" class="avatar-initials" style="--avatar-size: {$avatar_size}px;">
 {$fragment}
</a>
HTML;
    }

    return <<<HTML
<div class="avatar-initials" style="--avatar-size: {$avatar_size}px;">
 {$fragment}
</div>
HTML;
}

$esc_url_avatar_url = esc_url($avatar_url);
$esc_attr_display_name = esc_attr($display_name);
$esc_html_initials = esc_html($initials);
?>

<div class="d-flex gap-3 align-items-center justify-content-start">
    <div class="align-items-center d-flex flex-fill gap-3">
        <?php if ($has_real_avatar): ?>
            <?= wrapper_avatar_initials(
                    fragment: "<img src='{$esc_url_avatar_url}' alt='{$esc_attr_display_name}'/>",
                    avatar_size: $avatar_size,
                    go_to_dashboard: $go_to_dashboard
            ); ?>
        <?php else: ?>
            <?= wrapper_avatar_initials(
                    fragment: $esc_html_initials,
                    avatar_size: $avatar_size,
                    go_to_dashboard: $go_to_dashboard
            ); ?>
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