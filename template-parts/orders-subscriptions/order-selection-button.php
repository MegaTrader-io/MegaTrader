<?php
defined('ABSPATH') || exit;

$status = $args['status'] ?? null;
$size_slug = $args['size_slug'] ?? null;
$product_name = $args['product_name'] ?? null;

$status_classes = [
	'active' => 'badge-mega-active',
	'on-hold' => 'badge-mega-hold',
	'pending' => 'badge-mega-pending',
	'expired' => 'badge-mega-expired',
	'cancelled' => 'badge-mega-cancelled',
	'pending-cancel' => 'badge-mega-pending-cancel',
	// Add other statuses for orders here if they differ
	'completed' => 'badge-mega-active', // For orders
	'refunded' => 'badge-mega-active', // For orders
	'failed' => 'badge-mega-cancelled', // For orders
];

$badge_class = isset($status_classes[$status]) ? $status_classes[$status] : 'badge-mega-default';
$label = $size_slug . ' ' . $product_name;

?>

<button type="button" class="mega-btn-md mega-btn-dark-md w-100 mb-3"
	title="<?= esc_attr($label) ?>"
	data-bs-toggle="modal"
	data-bs-target="#changeSubcriptionModal">
	<div class="d-flex gap-3 flex-grow-1 flex-shirk-0 align-items-center min-w-0">
		<div class="badge-mega badge-mega-sm <?php echo esc_attr($badge_class); ?>">
			<?php
			if ($status === 'pending-cancel') {
				echo esc_html__('Pending Cancellation', 'woocommerce');
			} else {
				echo esc_html(ucwords(str_replace('-', ' ', $status)));
			}
			?>
		</div>
		<div class="d-flex gap-2 align-items-center min-w-0">
			<div class="plan-svg d-flex align-items-center h-24px">
				<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
					<mask id="mask0_11632_14551" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
						width="30" height="30">
						<rect width="30" height="30" fill="#D9D9D9" />
					</mask>
					<g mask="url(#mask0_11632_14551)">
						<path
							d="M11.5 10.3125L14.8125 3.75H15.1875L18.5 10.3125H11.5ZM14.0625 25.125L3.28125 12.1875H14.0625V25.125ZM15.9375 25.125V12.1875H26.7188L15.9375 25.125ZM20.5625 10.3125L17.3125 3.75H23.75L27.0312 10.3125H20.5625ZM2.96875 10.3125L6.25 3.75H12.6875L9.4375 10.3125H2.96875Z"
							fill="#FFB34A" />
					</g>
				</svg>
			</div>
			<div class="fw-medium plan-name text-size-24 text-uppercase text-white text-truncate">
				<?= esc_html($label) ?>
			</div>
		</div>
	</div>
	<span class="svg-button">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
			<mask id="mask0_12854_16591" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24"
				height="24">
				<rect width="24" height="24" fill="#D9D9D9" />
			</mask>
			<g mask="url(#mask0_12854_16591)">
				<path d="M12 15L7 10H17L12 15Z" fill="white" />
			</g>
		</svg>
	</span>
</button>