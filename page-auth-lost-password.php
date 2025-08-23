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
        while ( have_posts() ) :
            the_post();
            get_template_part( 'template-parts/content', 'page' );
            // If comments are open or we have at least one comment, load up the comment template.
            if ( comments_open() || get_comments_number() ) :
                comments_template();
            endif;
        endwhile; // End of the loop.
        ?>
    </main>
    <?php
} else {
    echo '<p>WooCommerce template not found.</p>';
}

get_footer();
