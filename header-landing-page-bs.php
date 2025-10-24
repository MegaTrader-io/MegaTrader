<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<?php error_log('[LV] after language_attributes'); ?>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class('font-roboto antialiased'); ?>>
<?php wp_body_open(); ?>

<?php Mt_Navbar::render_navbar_bs(
        section: 'landing-page-bs',
        classes_navbar: 'mt-navbar--landing-page-bs'
); ?>
