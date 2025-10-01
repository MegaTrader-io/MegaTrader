<?php
/**
 * Template Name: Account Profile
 */

defined('ABSPATH') || exit;

get_header();

$endpoints = [
        'profile' => 'account-settings-personal-information',
        'profile/verification' => 'account-settings-verification',
        'profile/password' => 'account-settings-password',
        'profile/two-factor-authentication' => 'account-settings-two-factor-authentication',
];

$current_url = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

$current_endpoint = preg_replace('#^my-account/#', '', $current_url);

$template_to_load = 'account-settings-personal-information';

if (array_key_exists($current_endpoint, $endpoints)) {
    $template_to_load = $endpoints[$current_endpoint];
} elseif (str_starts_with($current_endpoint, 'profile')) {
    $template_to_load = $endpoints['profile'];
}
?>

<div id="mt-account-profile" class="container">
    <div class="mt-page">
        <div class="mt-page__sidebar">
            <?php if (function_exists('render_sidebar')) {
                render_sidebar();
            } ?>
        </div>
        <div class="mt-page__main">
            <div class="mb-3">
                <?php account_settings_navigation_render(); ?>
            </div>

            <?php
            get_template_part('template-parts/account/' . $template_to_load);
            ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
