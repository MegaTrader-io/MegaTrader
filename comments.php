<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package megatrader
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">

	<?php
	
	comment_form();
	
	// You can start editing here -- including this comment!
	
	if ( have_comments() ) :
		?>
		<h3 class="mb-4 mt-5 pt-xl-3" data-cue="slideInUp"><?php echo get_comments_number();?> <?php esc_html_e('Comments', 'megatrader'); ?></h3>
		<?php the_comments_navigation(); ?>

		<ul class="ps-0 mb-0 list-unstyled comments-list" data-cue="slideInUp">
			<?php
			wp_list_comments(array(
				'style'      => 'ul',
				'short_ping' => true,
				'callback'   => 'megatrader_comment_list'
			));
			?>
		</ul><!-- .comment-list -->

		<?php
		the_comments_navigation();

		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() ) :
			?>
			<p class="no-comments" data-cue="slideInUp"><?php esc_html_e( 'Comments are closed.', 'megatrader' ); ?></p>
			<?php
		endif;

	endif; // Check for have_comments().
	?>

</div><!-- #comments -->
