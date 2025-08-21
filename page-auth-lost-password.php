<?php
/**
 * Template Name: Auth – Lost Password
 */
defined('ABSPATH') || exit;

get_header();

if (function_exists('wc_get_template')) {
    ?>

    <main id="primary" class="site-main mgt-theme">
        <?php
        while (have_posts()) :
            the_post();
            get_template_part('template-parts/content', 'page');
            wc_get_template('myaccount/form-lost-password.php');
        endwhile;
        ?>
    </main>
    <?php
} else {
    echo '<p>WooCommerce template not found.</p>';
}

get_footer();
