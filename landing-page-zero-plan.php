<?php
/**
 * Template Name: Landing Page Zero Plan
 */

get_header('landing-page-bs');
?>
    <main class="landing-bs">
        <?php get_template_part('template-parts/landing-page/sections/hero-zero-plan'); ?>
        <?php get_template_part('template-parts/landing-page/sections/how-it-works-zero-plan'); ?>
        <?php get_template_part('template-parts/landing-page/sections/pricing-table-zero-plan'); ?>
        <?php get_template_part('template-parts/landing-page/sections/payouts-and-comparison-bs'); ?>
        <?php get_template_part('template-parts/landing-page/sections/testimonials-bs'); ?>
        <?php get_template_part('template-parts/landing-page/sections/upcoming-events-bs'); ?>
        <?php get_template_part('template-parts/landing-page/sections/path-to-payout-bs'); ?>
        <?php get_template_part('template-parts/landing-page/sections/resource-to-help-your-grow-bs'); ?>
        <?php get_template_part('template-parts/landing-page/sections/faq-bs'); ?>
        <?php get_template_part('template-parts/landing-page/sections/social-section-bs'); ?>
    </main>
<?php require 'template-parts/landing-page/footer-bs.php' ?>
<?php get_footer(); ?>