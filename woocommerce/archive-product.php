<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );
?>
 <!-- Start Page Banner Area -->
 <div class="page-banner-area pt-100">
	<div class="container">
		<div class="page-banner-content">
			<span class="top-title">Best themes ever</span>
			<h2>Build startup with attractive <span>themes</span></h2>
			<p>Being part of Megatrader, we designed websites that has been used by more than 500k+ users. Using those experiences we craft unique and user friendly themes.</p>
			<div class="subscribe-content">
				<form class="subscribe-form d-sm-flex">
					<input type="email" class="form-control" placeholder="Search your item">
					<button type="submit" class="default-btn border-0 ms-3 button"><span class="button_circle"></span> Search <i class="ri-arrow-right-down-line"></i></button>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- End Page Banner Area -->
<div class="theme-area ptb-100">
	<div class="container">
		<div class="section-title">
			<span class="top-title">Themes</span>
			<h2>Download our best <span>selling templates</span></h2>
		</div>
		
		<div class="row">
			<div class="col-lg-3">
				<?php
				/**
				 * Hook: woocommerce_sidebar.
				 *
				 * @hooked woocommerce_get_sidebar - 10
				 */
				do_action( 'woocommerce_sidebar' );
				?>
			</div>
			<div class="col-lg-9">
				<?php
				/**
				 * Hook: woocommerce_before_main_content.
				 *
				 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
				 * @hooked woocommerce_breadcrumb - 20
				 * @hooked WC_Structured_Data::generate_website_data() - 30
				 */
				remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
				do_action( 'woocommerce_before_main_content' );
					/**
					 * Hook: woocommerce_shop_loop_header.
					 *
					 * @since 8.6.0
					 *
					 * @hooked woocommerce_product_taxonomy_archive_header - 10
					 */
					do_action( 'woocommerce_shop_loop_header' );

					if ( woocommerce_product_loop() ) {

						/**
						 * Hook: woocommerce_before_shop_loop.
						 *
						 * @hooked woocommerce_output_all_notices - 10
						 * @hooked woocommerce_result_count - 20
						 * @hooked woocommerce_catalog_ordering - 30
						 */
						do_action( 'woocommerce_before_shop_loop' );

						woocommerce_product_loop_start();

						if ( wc_get_loop_prop( 'total' ) ) {
							while ( have_posts() ) {
								the_post();

								/**
								 * Hook: woocommerce_shop_loop.
								 */
								do_action( 'woocommerce_shop_loop' );

								wc_get_template_part( 'content', 'product' );
							}
						}

						woocommerce_product_loop_end();

						/**
						 * Hook: woocommerce_after_shop_loop.
						 *
						 * @hooked woocommerce_pagination - 10
						 */
						do_action( 'woocommerce_after_shop_loop' );
					} else {
						/**
						 * Hook: woocommerce_no_products_found.
						 *
						 * @hooked wc_no_products_found - 10
						 */
						do_action( 'woocommerce_no_products_found' );
					}

				/**
				 * Hook: woocommerce_after_main_content.
				 *
				 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
				 */
				do_action( 'woocommerce_after_main_content' );
				?>
		</div>
	</div>
</div>
<?php
get_footer( 'shop' );