<?php
/**
 * Template Name: Landing Page Bootstrap
 */

get_header('landing-page-bs');
?>
    <main class="landing-bs">
        <?php get_template_part('template-parts/landing-page/sections/hero-bs'); ?>
        <?php get_template_part('template-parts/landing-page/sections/verified-bs'); ?>
        <?php get_template_part('template-parts/landing-page/sections/payout-bs'); ?>
        <?php get_template_part('template-parts/landing-page/sections/platforms-bs'); ?>
        <?php get_template_part('template-parts/landing-page/sections/journal-bs'); ?>
        <?php get_template_part('template-parts/landing-page/sections/pricing-table-bs'); ?>
        <?php get_template_part('template-parts/landing-page/sections/payouts-and-comparison-bs'); ?>
        <?php get_template_part('template-parts/landing-page/sections/testimonials-bs'); ?>
    </main>

<?php get_footer('landing-page-bs'); ?>