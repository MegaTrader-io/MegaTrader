<?php
$user_profile = mt_get_current_user_profile_data(avatar_size: 96);
?>

<div class="mt-user-profile-card mt-card mt-card-dark h-auto">
    <div class="mt-user-profile-card__wrapper">
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