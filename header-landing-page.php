    <!DOCTYPE html>
<?php error_log('[LV] BEFORE language_attributes'); ?>
<html <?php language_attributes(); ?>>
    <?php error_log('[LV] after language_attributes'); ?>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php error_log('[LV] before <?php wp_head(); ?>'); ?>
        <?php wp_head(); ?>
        <?php error_log('[LV] after <?php wp_head(); ?>'); ?>
    </head>
<body <?php body_class('font-roboto antialiased'); ?>>
<?php wp_body_open(); ?>

<div class="loading-overlay tw-hidden tw-fixed tw-inset-0 tw-bg-[#131210]/90 tw-justify-center tw-items-center tw-z-[9999999]">
    <div class="loader"></div>
</div>

<?php error_log('[LV] header-landing-page.php'); ?>
<?php require 'template-parts/landing-page/header.php' ?>