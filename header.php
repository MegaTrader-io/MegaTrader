<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package megatrader
 */
global $product;

?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

		<?php wp_head(); ?>
    </head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

	<div class="preloader">
		<div class="preloader-inner">
			<span class="loader"></span>
		</div>
	</div>

	<div class="ot-header header-layout1">
		<div class="sticky-wrapper">
		<?php do_action( 'mega_sticky_promo_render_banner' ); ?>
			<div class="menu-area">
				<div class="container">
					<div class="row align-items-center justify-content-between">
						<div class="col-auto">
							<div class="d-flex logo-wrap">
								<div class="header-logo">
									<a href="https://megatrader.io/">
										<img class="logo" src="<?php echo get_template_directory_uri(); ?>/assets/img/megatrader-original.svg" alt="Logo" width="326" height="60">
										<img class="mobile-logo" src="<?php echo get_template_directory_uri(); ?>/assets/img/megatrader-mobile-original.svg" alt="Logo" width="60" height="60">
									</a>
								</div>
								<!--
								<h1 class="header-page-title">
									<?php
										if (is_archive()) {
											the_archive_title();
										} elseif (is_home()) {
											echo 'Checkout'; // Customize as needed
										} elseif (is_single() || is_page()) {
											the_title();
										} elseif (is_search()) {
											printf(__('Search Results for: %s'), get_search_query());
										} elseif (is_wc_endpoint_url('order-received')) {
											echo 'Order received thank you';
										} else {
											echo 'Page'; // Customize as needed
										}
									?>
								</h1>-->
							</div>
						</div>
						<div class="col-auto">
							<div class="header-button">
								<a href="https://megatrader.io/" class="icon-btn">
									<svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M8.5 19.25L13 14.75L17.5 19.25L19.25 17.5L14.75 13L19.25 8.5L17.5 6.75L13 11.25L8.5 6.75L6.75 8.5L11.25 13L6.75 17.5L8.5 19.25ZM13 25.5C11.2708 25.5 9.64583 25.1719 8.125 24.5156C6.60417 23.8594 5.28125 22.9688 4.15625 21.8438C3.03125 20.7188 2.14063 19.3958 1.48438 17.875C0.828125 16.3542 0.5 14.7292 0.5 13C0.5 11.2708 0.828125 9.64583 1.48438 8.125C2.14063 6.60417 3.03125 5.28125 4.15625 4.15625C5.28125 3.03125 6.60417 2.14063 8.125 1.48438C9.64583 0.828125 11.2708 0.5 13 0.5C14.7292 0.5 16.3542 0.828125 17.875 1.48438C19.3958 2.14063 20.7188 3.03125 21.8438 4.15625C22.9688 5.28125 23.8594 6.60417 24.5156 8.125C25.1719 9.64583 25.5 11.2708 25.5 13C25.5 14.7292 25.1719 16.3542 24.5156 17.875C23.8594 19.3958 22.9688 20.7188 21.8438 21.8438C20.7188 22.9688 19.3958 23.8594 17.875 24.5156C16.3542 25.1719 14.7292 25.5 13 25.5Z" fill="white"/>
									</svg>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


	<script>
		function handleLogout(e) {
			fetch('<?php echo wp_logout_url(); ?>', {
				method: 'GET',
				credentials: 'include'
			}).then(() => {
				window.location.href = 'https://megatrader.io/';
			});
		}
	</script>