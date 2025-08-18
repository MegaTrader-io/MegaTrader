<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package megatrader
 */

get_header();
?>

<!-- Start Page Banner Area -->
<div class="page-banner-area pt-100">
    <div class="container">
        <div class="page-banner-content">
            <span class="top-title">Best themes ever</span>
            <h2>Build startup with attractive <span>themes</span></h2>
            <p>Being part of Megatrader, we designed websites that has been used by more than 500k+ users. Using those experiences we craft unique and user friendly themes.</p>
            <div class="subscribe-content">
                <form class="subscribe-form d-sm-flex" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <input type="hidden" name="post_type" value="wc_themes"> <!-- Specify the custom post type -->
                    <input type="text" name="s" class="form-control" placeholder="Search your item" value="<?php the_search_query(); ?>">
                    <select name="wc_themes_category" class="form-control ms-3">
                        <option value=""><?php _e('Select Category', 'megatrader'); ?></option>
                        <?php
                        // Get all terms for the custom taxonomy 'wc_themes_category'
                        $terms = get_terms( array(
                            'taxonomy'   => 'wc_themes_category',
                            'hide_empty' => false,
                        ) );
                        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                            foreach ( $terms as $term ) {
                                echo '<option value="' . esc_attr( $term->slug ) . '">' . esc_html( $term->name ) . '</option>';
                            }
                        }
                        ?>
                    </select>
                    <button type="submit" class="default-btn border-0 ms-3 button"><span class="button_circle"></span> Search <i class="ri-arrow-right-down-line"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Page Banner Area -->

<!-- Start Theme Area -->
<div class="theme-area ptb-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <?php
                    $args = array(
                        'post_type' => 'wc_themes',
                        'posts_per_page' => 60, // Adjust this as needed
                    );
                    $themes = new WP_Query($args);

                    if ($themes->have_posts()) :
                        while ($themes->have_posts()) : $themes->the_post(); 
                            ?>
                            <div class="col-lg-4 col-md-6">
                                <div class="single-theme-card">
                                    <div class="theme-img">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php if (has_post_thumbnail()): ?>
                                                <img src="<?php echo esc_url(get_the_post_thumbnail_url()); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                    <div class="content">
                                        <?php
                                        // Fetch all terms for the custom taxonomy 'wc_themes_category'
                                        $terms = get_terms( array(
                                            'taxonomy'   => 'wc_themes_category',
                                            'hide_empty' => true, // Set to false if you want to show terms with no posts
                                        ) );

                                        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) :
                                            echo '<div class="cats">';
                                            foreach ( $terms as $term ) :
                                                // Generate the term link and name
                                                $term_link = get_term_link( $term );
                                                $term_name = esc_html( $term->name );
                                                ?>
                                                <a href="<?php echo esc_url( $term_link ); ?>"><?php echo $term_name; ?></a>
                                                <?php
                                            endforeach;
                                            echo '</div>';
                                        endif;
                                        ?>
                                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                        <?php if ( has_excerpt() ) : ?>
                                            <p><?php echo get_the_excerpt(); ?></p>
                                        <?php endif; ?>
                                        <?php
                                            if ( class_exists('ACF') ) {
                                                $product_price      = get_field('product_price');
                                                $buy_p_megatrader    = get_field('buy_p_megatrader');
                                                $preview            = get_field('preview');
                                            }else{
                                                $product_price = $buy_p_megatrader = $preview = '';
                                            }
                                        ?>
                                        <h5 class="price"><?php echo esc_html($product_price); ?></h5>
                                        <div class="btns">
                                            <?php if($buy_p_megatrader): ?>
                                                <a href="<?php echo esc_url($buy_p_megatrader); ?>" class="buy-btn"><i class="ri-shopping-cart-2-line"></i> Buy </a>
                                            <?php endif; ?>

                                            <?php if($preview): ?>
                                                <a href="<?php echo esc_url($preview); ?>" target="_blank" class="preview"><i class="ri-arrow-right-up-line"></i>Preview</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        endwhile;
                        wp_reset_postdata();
                        else :
                        echo '<p>No themes found</p>';
                    endif;
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Theme Area -->

<?php
get_footer();
