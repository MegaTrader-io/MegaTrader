<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<?php error_log('[LV] after language_attributes'); ?>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
          rel="stylesheet">
    <?php wp_head(); ?>
</head>
<body <?php body_class('font-roboto antialiased'); ?>>
<?php wp_body_open(); ?>
<div class="preloader" style="display: block;">
    <div class="preloader-inner">
        <span class="loader"></span>
    </div>
</div>
<?php Mt_Navbar::render_navbar_bs(
        section: 'landing-page-bs',
        classes_navbar: 'mt-navbar--landing-page-bs'
); ?>
