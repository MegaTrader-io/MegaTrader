<?php
/**
 * Modal Template Partial
 *
 * This template expects the following parameters to be passed in the `$args` array:
 *
 * @param bool	 $args['notice']           	If "true" adds a woocommerce notice wrapper to the modal
 * @param bool   $args['autoshow']          Autoshow modal inmidiatedly if "true" or X number of seconds if provided
 * @param string $args['modalType']         Modal Type: ['', 'warning']. (default: '') [Optional]
 * @param string $args['modalId']           ID for the modal element (default: 'mtModal')
 * @param string $args['modalTitle']        Title text for the modal (default: '')
 * @param string $args['imageSrc']          URL to an optional image to display inside the modal (default: '')
 * @param string $args['imageClass']        CSS class for the image container (default: '')
 * @param string $args['bodyContent']       HTML content for the modal body (default: '')
 * @param string $args['footerContent']     HTML content for the modal footer (e.g., buttons) (default: '')
 * @param string $args['labelId']           Optional ID for the modal title label (used for aria-labelledby). Defaults to modalId + 'Label'
 * @param string $args['closeHref']         HREF value for the close button (default: 'javascript:void(0);')
 */

$map = [
	'warning' => [
		'imageSrc'   => '/wp-content/themes/megatrader-addons/assets/img/warning.svg',
		'imageAlt'   => 'Warning icon',
		'imageClass' => 'modal-image-warning',
	]
];

$type = $args['modalType'] ?? '';
$defaults = $map[ $type ] ?? [];

$modal_image = [
		'imageSrc'   => $args['imageSrc']   ?? $defaults['imageSrc']   ?? '',
		'imageAlt'   => $args['imageAlt']   ?? $defaults['imageAlt']   ?? '',
		'imageClass' => $args['imageClass'] ?? $defaults['imageClass'] ?? '',
	];

$label_id   = $args['labelId'] ?: $args['modalId'] . 'Label';

?>

<div class="modal fade" 
	 id="<?php echo esc_attr( $args['modalId'] ); ?>"
	 tabindex="-1" aria-labelledby="<?php echo esc_attr( $label_id ); ?>" role="dialog"
	 data-autoshow="<?php echo $args['autoshow'] ?>" 
	 data-notice="<?php echo $args['notice'] ?>" >
	<div class="modal-dialog modal-dialog-centered modal-fullscreen-md-down">
		<div class="modal-content flex-shrink-0 d-flex flex-column align-items-center">
			<div class="modal-header w-100 border-0 justify-content-between align-items-start p-0 mb-32">
				<h5 class="modal-title text-white heading-sm-medium text-uppercase" id="<?php echo esc_attr( $label_id ); ?>">
					<?php echo esc_html( $args['modalTitle'] ); ?>
				</h5>
				<a class="p-0 border-0 bg-transparent shadow-none" data-bs-dismiss="modal" aria-label="Close" href="<?php echo esc_attr( $args['closeHref'] ); ?>">
					<span aria-hidden="true">
						<img decoding="async" src="/wp-content/uploads/2025/05/cancel-circle-1.png" alt="Close" style="width: 24px; height: 24px;">
					</span>
				</a>
			</div>
			<?php
				if( $args['notice'] ){
					echo '<div class="woocommerce-notices-wrapper w-100"></div>';
				}
			?>
			<div class="modal-body d-flex flex-column align-items-center justify-content-center gap-32">
				<?php if ( $modal_image['imageSrc'] ) : ?>
					<div class="modal-body-image <?php echo esc_attr( $modal_image['imageClass'] ); ?>">
						<img decoding="async" src="<?php echo esc_url( $modal_image['imageSrc'] ); ?>" alt="<?php echo esc_url( $modal_image['imageAlt'] ); ?>">
					</div>
				<?php endif; ?>

				<?php if ( $args['bodyContent'] ) : ?>
					<div class="text-center text-white">
						<?php echo $args['bodyContent']; ?>
					</div>
				<?php endif; ?>

				<?php if ( $args['footer'] ) : ?>
					<div class="d-flex gap-3 w-100">
						<?php echo $args['footer']; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
