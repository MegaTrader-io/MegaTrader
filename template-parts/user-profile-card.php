<?php

$avatar_size = 96;

/** @var null|WP_User $current_user */
$current_user = wp_get_current_user();

$first_name = get_user_meta($current_user->ID, 'first_name', true);
$last_name = get_user_meta($current_user->ID, 'last_name', true);

if ($first_name || $last_name) {
    $display_name = trim($first_name . ' ' . $last_name);
} else {
    $display_name = $current_user->display_name;
}

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

$response = wp_safe_remote_head($avatar_url, [
    'timeout' => 2,
]);

$has_real_avatar = false;
if (!is_wp_error($response)) {
    $code = wp_remote_retrieve_response_code($response);
    error_log("Gravatar HTTP status for user {$current_user->ID}: {$code}");
    if (200 === $code) {
        $has_real_avatar = true;
    }
}

?>

<div class="mt-user-profile-card mt-card mt-card-dark h-auto">
    <div class="d-flex align-items-center">
        <div class="mt-user-profile-card__avatar-container">
            <?php if ($has_real_avatar): ?>
                <img class="mt-user-profile-card__avatar-image flex-shrink-0" src="<?php echo esc_url($avatar_url); ?>"
                     alt="<?php echo esc_attr($display_name); ?>" alt="User Avatar"/>
            <?php else: ?>
                <div class="mt-user-profile-card__avatar-image mt-avatar__initials flex-shrink-0"
                     style="--avatar-size: <?php echo esc_attr($avatar_size); ?>px;">
                    <?php echo esc_html($initials); ?>
                </div>
            <?php endif; ?>
            <div class="mt-user-profile-card__edit-icon">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <mask id="mask0_15946_2688" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                          width="20" height="20">
                        <rect width="20" height="20" fill="#D9D9D9"/>
                    </mask>
                    <g mask="url(#mask0_15946_2688)">
                        <path d="M2.5 17.5V13.9583L13.5 2.97917C13.6667 2.82639 13.8507 2.70833 14.0521 2.625C14.2535 2.54167 14.4653 2.5 14.6875 2.5C14.9097 2.5 15.125 2.54167 15.3333 2.625C15.5417 2.70833 15.7222 2.83333 15.875 3L17.0208 4.16667C17.1875 4.31944 17.309 4.5 17.3854 4.70833C17.4618 4.91667 17.5 5.125 17.5 5.33333C17.5 5.55556 17.4618 5.76736 17.3854 5.96875C17.309 6.17014 17.1875 6.35417 17.0208 6.52083L6.04167 17.5H2.5ZM14.6667 6.5L15.8333 5.33333L14.6667 4.16667L13.5 5.33333L14.6667 6.5Z"
                              fill="black"/>
                    </g>
                </svg>
            </div>
        </div>

        <div class="mt-user-profile-card__details">
            <div class="mt-user-profile-card__member-since">
                <div class="user-profile-card__edit-member-icon">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <mask id="mask0_15865_48537" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                              width="20"
                              height="20">
                            <rect width="20" height="20" fill="#D9D9D9"/>
                        </mask>
                        <g mask="url(#mask0_15865_48537)">
                            <path d="M2.5 17.5V13.9583L13.5 2.97917C13.6667 2.82639 13.8507 2.70833 14.0521 2.625C14.2535 2.54167 14.4653 2.5 14.6875 2.5C14.9097 2.5 15.125 2.54167 15.3333 2.625C15.5417 2.70833 15.7222 2.83333 15.875 3L17.0208 4.16667C17.1875 4.31944 17.309 4.5 17.3854 4.70833C17.4618 4.91667 17.5 5.125 17.5 5.33333C17.5 5.55556 17.4618 5.76736 17.3854 5.96875C17.309 6.17014 17.1875 6.35417 17.0208 6.52083L6.04167 17.5H2.5ZM14.6667 6.5L15.8333 5.33333L14.6667 4.16667L13.5 5.33333L14.6667 6.5Z"
                                  fill="#A8A29E"/>
                        </g>
                    </svg>
                </div>
                Member
                since: <?php echo esc_html($current_user && $current_user->user_registered ? date_i18n('m/d/Y', strtotime($current_user->user_registered)) : '—'); ?>
            </div>
            <?php
                get_template_part('template-parts/verified', null, [
                        'verified' => false
            ]);
            ?>
        </div>
    </div>
</div>