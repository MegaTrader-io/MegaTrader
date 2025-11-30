<?php
defined('ABSPATH') || exit;

$items = $args['items'] ?? null;
$selected_data_id = $args['selected_data_id'] ?? null;

?>

<div class="modal modal-subcription fade" id="changeSubcriptionModal" tabindex="-1"
	aria-labelledby="changeSubcriptionModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content gap-4">
			<div class="modal-header w-100 border-0 justify-content-between align-items-start p-0">
				<h5 class="modal-title text-white heading-sm-medium" id="changeSubcriptionModalLabel">Select account
				</h5>
				<button type="button" class="p-0 border-0 bg-transparent shadow-none" data-bs-dismiss="modal"
					aria-label="Close">
					<span aria-hidden="true">
						<img src="/wp-content/uploads/2025/05/cancel-circle-1.png"
							alt="Close" style="width: 24px; height: 24px;" />
					</span>
				</button>
			</div>

			<div class="modal-body">
				<div class="subscription-scroll-area px-lg-3 p-0">
					<div class="subscription-grid">
						<?php
						// Recorrer el array combinado y mostrar todas las tarjetas ordenadas
						foreach ($items as $item) {
							$id = $item['id'];
							$type = $item['type'];
							$name = $item['name'];
							$size_slug = $item['size_slug'];
							$logos = $item['logos'];
							$platform_value = $item['platform_value'];

							if ($type === 'subscription') {
								$status = $item['status'];
								$dot_class = 'dot-status-' . $status;
								$order_obj = wc_get_order($id);
								$order_number = $order_obj ? $order_obj->get_order_number() : $id;

								echo '<div class="subscription-card position-relative d-flex flex-column gap-2" role="button" data-id="sub-' . esc_attr($id) . '">';
								echo '<div class="checkmark-icon position-absolute" style="top: 10px; right: 10px;">';
								echo '  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">';
								echo '    <circle cx="12" cy="12" r="10" fill="#FFB34A"/>';
								echo '    <path d="M10.6 16.6L17.65 9.55L16.25 8.15L10.6 13.8L7.75 10.95L6.35 12.35L10.6 16.6Z" fill="black"/>';
								echo '  </svg>';
								echo '</div>';

								echo '<div class="subscription-card__header text-center position-relative d-flex flex-column align-items-center">';
								if (!empty($logos)) {
									echo '<div class="logo-container position-relative d-inline-block">';
									echo '<img src="' . esc_url($logos[0]) . '" alt="' . esc_attr($platform_value) . ' logo" style="max-height: 40px;">';
									echo '<div class="' . esc_attr($dot_class) . ' dot-indicator position-absolute" title="' . esc_attr($status) . '">';
									echo '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">';
									echo '<circle cx="6" cy="6" r="6" fill="white"/>';
									echo '<circle cx="6" cy="6" r="4" fill="currentColor"/>';
									echo '</svg>';
									echo '</div>';
									echo '</div>';
								}
								echo '</div>';

								echo '<div class="subscription-card__body text-center">';
								echo '<div class="subscription-card__name fw-medium text-16px text-white">' . esc_html($size_slug) . ' ' . esc_html($name) . '</div>';
								echo '<div class="subscription-card__id text-14px text-a8a29e text-uppercase">#' . esc_html($order_number) . '</div>';
								echo '</div>';

								echo '</div>';
							} else if ($type === 'order') {
								$order_obj = $item['object'];
								$order_number = $order_obj ? $order_obj->get_order_number() : $id;

								echo '<div class="subscription-card position-relative d-flex flex-column gap-2" role="button" data-id="order-' . esc_attr($id) . '">';
								echo '<div class="checkmark-icon position-absolute" style="top: 8px; right: 8px;">';
								echo '  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">';
								echo '    <circle cx="12" cy="12" r="10" fill="#FFB34A"/>';
								echo '    <path d="M10.6 16.6L17.65 9.55L16.25 8.15L10.6 13.8L7.75 10.95L6.35 12.35L10.6 16.6Z" fill="black"/>';
								echo '  </svg>';
								echo '</div>';
								echo '<div class="subscription-card__header text-center position-relative d-flex flex-row justify-content-center align-items-center">';
								foreach ($logos as $logo_url) {
									echo '<div class="logo-container position-relative d-inline-block">';
									echo '<img src="' . esc_url($logo_url) . '" alt="Product logo" style="max-height: 40px; ">';
									echo '</div>';
								}
								echo '</div>';

								echo '<div class="subscription-card__body text-center">';
								echo '<div class="subscription-card__name fw-medium text-16px text-white">' . esc_html($name) . '</div>';
								echo '<div class="subscription-card__id text-14px text-a8a29e text-uppercase">#' . esc_html($order_number) . '</div>';
								echo '</div>';

								echo '</div>';
							}
						}
						?>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<div class="align-items-center d-flex gap-2 justify-content-end">
					<div class="mega-btn mega-btn-md rounded-12 text-white" data-bs-dismiss="modal">
						Cancel
					</div>
					<button id="select-subscription-btn" class="mega-btn mega-btn-md mega-btn-secondary-md disabled">
						Select
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	window.selectedSubscriptionId = "<?php echo esc_js($selected_data_id); ?>";
</script>