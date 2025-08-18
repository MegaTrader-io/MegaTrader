<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package megatrader
 */

get_header();
?>

<!-- Start Single Blog Area -->
<div class="blog-details-area pt-100">
	<div class="container">
		<?php
		while ( have_posts() ) : the_post();
			?>
			<div class="blog-details-content">
				<div class="text-center mb-50" data-cue="slideInUp">
					<?php
						$post_categories = get_the_category();

						if (!empty($post_categories) && count($post_categories) > 1) {
							$second_category = $post_categories[1];
							?>
							<span class="top-title">
								<?php echo esc_html($second_category->name); ?>
							</span>
							<?php
						}
					?>

					<h2><?php the_title();?></h2>
					<ul class="ps-0 list-unstyled d-flex justify-content-center date-time">
						<li><?php echo get_the_date('F j, Y'); ?></li>
						<li><?php echo date('h:i A'); ?></li>
					</ul>
					<div class="info">
						<?php
						$author_id = get_the_author_meta('ID');
						$avatar = get_avatar($author_id, 56, '', 'user', ['class' => 'rounded-circle']);
						if ($avatar) {
							echo wp_kses($avatar, array(
								'img' => array(
									'src' => array(),
									'alt' => array(),
									'class' => array(),
									'width' => array(),
									'height' => array(),
								),
							));
						}
						?>
						<span class="d-block"><?php echo esc_html(get_the_author()); ?></span>
					</div>
				</div>
				<?php
					if ( class_exists('ACF') ) {
						$image_taken		= get_field('image_taken');
						$blockquote_text	= get_field('blockquote_text');
						$quote_span 		= get_field('quote_span');
					}else{
						$image_taken = $blockquote_text = $quote_span = '';
					}
				?>
				<div class="blog-single-img mb-50" data-cue="slideInUp">
					<?php if (has_post_thumbnail()): ?>
						<img src="<?php echo esc_url(get_the_post_thumbnail_url()); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
					<?php endif; ?>
					<?php if($image_taken): ?>
						<span class="taken"><?php echo esc_html($image_taken); ?></span>
					<?php endif; ?>
				</div>
				
				<div class="details-content" >
					<?php the_content();?>

					<div class="mb-50"></div>

					<?php if($blockquote_text): ?>
						<blockquote class="blockquote mb-0">
							<p><?php echo esc_html($blockquote_text); ?></p>
							<?php if($quote_span): ?>
								<span><?php echo esc_html($quote_span); ?></span>
							<?php endif; ?>
						</blockquote>
					<?php endif; ?>

					<div class="mb-50"></div>

					<h4>Share</h4>
					<ul class="social-list ps-0 list-unstyled d-flex">
						<li>
							<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode( get_permalink() ); ?>" target="_blank">
								<i class="ri-facebook-fill"></i>
							</a>
						</li>
						<li>
							<a href="https://twitter.com/intent/tweet?url=<?php echo urlencode( get_permalink() ); ?>&text=<?php echo urlencode( get_the_title() ); ?>" target="_blank">
								<i class="ri-twitter-x-line"></i>
							</a>
						</li>
						<li>
							<a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode( get_permalink() ); ?>" target="_blank">
								<i class="ri-linkedin-fill"></i>
							</a>
						</li>
						<li>
							<a href="https://pinterest.com/pin/create/button/?url=<?php echo urlencode( get_permalink() ); ?>&media=<?php echo urlencode( wp_get_attachment_url( get_post_thumbnail_id() ) ); ?>&description=<?php echo urlencode( get_the_title() ); ?>" target="_blank">
								<i class="ri-pinterest-fill"></i>
							</a>
						</li>
					</ul>


					<div class="mb-50"></div>

					<div class="leave-comment">
						<!--Comment Template-->
						<?php 
							if ( comments_open() || get_comments_number() ) :
								comments_template();
							endif;
						?>
						<!--End Comment Template-->
					</div>

					<div class="mb-50"></div>
				</div>
			</div>
			<?php
		endwhile;
		?>
	</div>
</div>
<!-- End Single Blog Area -->

<?php
get_footer();
