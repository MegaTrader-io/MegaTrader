<?php
$user_profile = mt_get_current_user_profile_data(avatar_size: 96);
?>

<div class="mt-user-profile-card mt-card mt-card-dark h-auto">
    <div class="d-flex align-items-center justify-content-between">
        <div class="mt-user-profile-card__avatar-container">

            <?php get_template_part('template-parts/avatar', null, [
                    'avatar_url' => $user_profile['avatar_url'],
                    'avatar_size' => $user_profile['avatar_size'],
                    'has_real_avatar' => $user_profile['has_real_avatar'],
                    'initials' => $user_profile['initials'],
                    'display_name' => $user_profile['display_name'],
                    'user_email' => $user_profile['user_email'],
                    'billing_country' => $user_profile['billing_country']
            ]); ?>

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
                <div>
                    <div>Member since:</div>
                    <?php echo esc_html($user_profile['member_since']); ?>
                </div>
            </div>
            <?php
            get_template_part('template-parts/verified');
            ?>
        </div>
    </div>
</div>