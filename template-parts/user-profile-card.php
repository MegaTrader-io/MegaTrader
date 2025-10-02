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
            <div class="mt-user-profile-card__status mt-user-profile-card__status--unverified">
                <div class="mt-user-profile-card__status-icon">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <mask id="mask0_15865_48540" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                              width="20" height="20">
                            <rect width="20" height="20" fill="#D9D9D9"/>
                        </mask>
                        <g mask="url(#mask0_15865_48540)">
                            <path d="M8.83366 13.8332L14.7087 7.95817L13.542 6.7915L8.83366 11.4998L6.45866 9.12484L5.29199 10.2915L8.83366 13.8332ZM10.0003 18.3332C8.84755 18.3332 7.76421 18.1144 6.75033 17.6769C5.73644 17.2394 4.85449 16.6457 4.10449 15.8957C3.35449 15.1457 2.76074 14.2637 2.32324 13.2498C1.88574 12.2359 1.66699 11.1526 1.66699 9.99984C1.66699 8.84706 1.88574 7.76373 2.32324 6.74984C2.76074 5.73595 3.35449 4.854 4.10449 4.104C4.85449 3.354 5.73644 2.76025 6.75033 2.32275C7.76421 1.88525 8.84755 1.6665 10.0003 1.6665C11.1531 1.6665 12.2364 1.88525 13.2503 2.32275C14.2642 2.76025 15.1462 3.354 15.8962 4.104C16.6462 4.854 17.2399 5.73595 17.6774 6.74984C18.1149 7.76373 18.3337 8.84706 18.3337 9.99984C18.3337 11.1526 18.1149 12.2359 17.6774 13.2498C17.2399 14.2637 16.6462 15.1457 15.8962 15.8957C15.1462 16.6457 14.2642 17.2394 13.2503 17.6769C12.2364 18.1144 11.1531 18.3332 10.0003 18.3332Z"
                                  fill="currentColor"/>
                        </g>
                    </svg>
                </div>
                <div class="mt-user-profile-card__status-text">
                    UNVERIFIED
                </div>
            </div>
        </div>
    </div>
</div>