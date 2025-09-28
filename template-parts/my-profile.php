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

$collapsed = $args['collapsed'] ?? false;
$collapsed_class = $collapsed ? 'collapsed' : '';

?>
<div class="mt-my-profile <?= $collapsed_class ?>">
    <?php if ( $go_to_dashboard) : ?>
        <a href="<?= profile_url() ?>" class="mt-my-profile__avatar-link flex-fill">
            <?php get_template_part('template-parts/avatar', null, [
                    'avatar_url' => $avatar_url,
                    'avatar_size' => $avatar_size,
                    'has_real_avatar' => $has_real_avatar,
                    'initials' => $initials,
                    'display_name' => $display_name,
                    'user_email' => $user_email,
                    'billing_country' => $billing_country,
                    'hide_user_information' => $hide_user_information,
                    'collapsed' => $collapsed,
            ]); ?>
        </a>
    <?php else: ?>
        <div class="mt-my-profile__avatar flex-fill">
            <?php get_template_part('template-parts/avatar', null, [
                    'avatar_url' => $avatar_url,
                    'avatar_size' => $avatar_size,
                    'has_real_avatar' => $has_real_avatar,
                    'initials' => $initials,
                    'display_name' => $display_name,
                    'user_email' => $user_email,
                    'billing_country' => $billing_country,
                    'collapsed' => $collapsed,
            ]); ?>
        </div>
    <?php endif; ?>

    <div class="mt-my-profile__notifications">
        <span class="mt-tooltip" data-placement="right">                        
            <a class="mt-sidebar__menu__link <?= $overview_active_class ?>" href="javascript:void(0)">
                <i class="mt-icon mt-icon-sm mt-icon_notifications "></i>
            </a>
            <span class="mt-tooltip__panel" role="tooltip">
                <div class="mt-tooltip__body">Notifications</div>
            </span>
        </span>
    </div>
</div>