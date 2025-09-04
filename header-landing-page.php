<?php
ob_start();
do_action('mega_sticky_promo_render_banner');
$sticky_promo_html = ob_get_clean();
$header_height = empty($sticky_promo_html) ? 100 : 100 + 54;
?>

    <!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php wp_head(); ?>
    </head>
<body <?php body_class('font-roboto antialiased'); ?> style="--header-height: <?= $header_height ?>px;">
<div class="loading-overlay tw-hidden tw-fixed tw-inset-0 tw-bg-[#131210]/90 tw-justify-center tw-items-center tw-z-[9999999]">
    <div class="loader"></div>
</div>
<?php require 'landing-page/header.php' ?>