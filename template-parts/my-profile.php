<?php

defined('ABSPATH') || exit;

$avatar_size = $args['avatar_size'] ?? 64;
$go_to_dashboard = $args['go_to_dashboard'] ?? false;
$hide_user_information = $args['hide_user_information'] ?? false;

$user_profile = mt_get_current_user_profile_data(avatar_size: $avatar_size);

$logout_url = wp_logout_url();

$collapsed = $args['collapsed'] ?? false;
$collapsed_class = $collapsed ? 'collapsed' : '';
$account_overview_url = profile_url(user_email: $user_profile['user_email']);

?>
<div class="mt-my-profile <?= esc_attr($collapsed_class); ?>">
    <?php if ($go_to_dashboard): ?>
        <a href="<?= esc_url($account_overview_url); ?>" class="mt-my-profile__avatar mt-my-profile__avatar_link">
            <?php get_template_part('template-parts/avatar', null, [
                'avatar_url' => $user_profile['avatar_url'],
                'avatar_size' => $user_profile['avatar_size'],
                'has_real_avatar' => $user_profile['has_real_avatar'],
                'initials' => $user_profile['initials'],
                'display_name' => $user_profile['display_name'],
                'user_email' => $user_profile['user_email'],
                'billing_country' => $user_profile['billing_country'],
                'hide_user_information' => $hide_user_information,
                'collapsed' => $collapsed,
            ]); ?>
        </a>

        <div class="my-account-logout">
            <a href="<?php echo esc_url($logout_url); ?>" class="logout-link" aria-label="Logout">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <mask id="mask0_12101_26191" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24"
                        height="24">
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
    <?php else: ?>

        <div class="mt-my-profile__avatar">
            <?php get_template_part('template-parts/avatar', null, [
                'avatar_url' => $user_profile['avatar_url'],
                'avatar_size' => $user_profile['avatar_size'],
                'has_real_avatar' => $user_profile['has_real_avatar'],
                'initials' => $user_profile['initials'],
                'display_name' => $user_profile['display_name'],
                'user_email' => $user_profile['user_email'],
                'billing_country' => $user_profile['billing_country'],
                'collapsed' => $collapsed,
            ]); ?>
        </div>

        <!-- NOTIFICATIONS: sin tooltip -->
        <div class="mt-my-profile__notifications">
            <a id="mt-notifications-toggle" class="mt-sidebar__menu__link" href="javascript:void(0)" aria-haspopup="dialog"
                aria-expanded="false">
                <i class="mt-icon mt-icon_notifications"></i>
            </a>

            <?php
            // TEMP: email fijo para pruebas
            $mt_user_email = 'jordantest@megatrader.io';

            // Render del panel (sin ID, con clase)
            get_template_part(
                'template-parts/account/account-notification',
                null,
                ['user_email' => $mt_user_email]
            );
            ?>
        </div>




    <?php endif; ?>
</div>