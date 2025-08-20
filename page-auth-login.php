<?php
/**
 * Template Name: Auth – Login
 */
defined('ABSPATH') || exit;

if (is_user_logged_in()) {
    wp_safe_redirect(wc_get_page_permalink('myaccount'));
    exit;
}

get_header();

if (function_exists('wc_get_template')) {
    ?>
    <main id="primary" class="site-main mgt-theme">
        <?php
        while (have_posts()) :
            the_post();
            get_template_part('template-parts/content', 'page');
            wc_get_template('myaccount/form-login.php');
        endwhile; // End of the loop.
        ?>
    </main><!-- #main -->
    <?php
} else {
    echo '<p>WooCommerce template not found.</p>';
}

get_footer();
