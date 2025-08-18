<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class('font-roboto antialiased'); ?>>
<div class="loading-overlay hidden fixed inset-0 bg-[#131210]/90 justify-center items-center z-[9999999]">
    <div class="loader"></div>
</div>
<?php require 'landing-page/header.php' ?>