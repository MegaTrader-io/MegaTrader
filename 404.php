<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package megatrader
 */

get_header();
?>

	<main id="primary" class="site-main">

		<section class="error-404 not-found">
			<div class="container z-index-common">
				<div class="breadcumb-content text-center">
					<div class="title-404" data-cue="slideInUp"><span class="text-gradient">404</span></div>
					<h1 class="breadcumb-title" data-cue="slideInUp" data-delay="100">Page Not Found</h1>
					<div class="btn-group breadcumb-btn mt-5" data-cue="slideInUp" data-delay="200">
						<a href="/" class="ot-btn">Back to Home</a>
					</div>
				</div>
			</div>
		</section><!-- .error-404 -->

	</main><!-- #main -->

<?php
get_footer();
