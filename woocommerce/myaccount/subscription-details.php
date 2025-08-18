<?php
/**
 * Subscription details table
 *
 * @author  Prospress
 * @package WooCommerce_Subscription/Templates
 * @since 1.0.0 - Migrated from WooCommerce Subscriptions v2.2.19
 * @version 1.0.0 - Migrated from WooCommerce Subscriptions v2.6.5
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}


if (!isset($subscription) || !is_a($subscription, 'WC_Subscription')) {
	echo '<p>Subscription not found.</p>';
	return;
}

$item = current($subscription->get_items());
$product = $item ? $item->get_product() : null;
$product_name = $product ? $product->get_name() : '';

// Cache term data for 'pa_account-size' to avoid multiple calls
$all_size_terms = get_terms([
	'taxonomy' => 'pa_account-size',
	'hide_empty' => false,
]);

$product_size = $product ? $product->get_attribute('pa_account-size') : '';
$matched_term = null;
$size_name = '';
$size_slug = '';

foreach ($all_size_terms as $term) {
	if ($term->name === $product_size) {
		$matched_term = $term;
		break;
	}
}
$size_name = $matched_term ? $matched_term->name : '';
$size_slug = $matched_term ? $matched_term->slug : '';

$product_meta = [];
$rules_meta = [];
$objectives_meta = [];

$rules_keys = [
	'max_contracts',
	'daily_loss_limit',
	'daily_loss_limit_soft_breach',
	'trailing_max_drawdown',
	'drawdown_mode',
	'max_accounts',
];

$objectives_keys = [
	'min_trading_days',
	'min_trading_days_to_payout',
	'consistency',
	'profit_target',
];

if ($product) {
	foreach ($product->get_meta_data() as $meta) {
		$key = $meta->key;
		$value = $meta->value;

		if (empty($value)) {
			continue;
		}

		$product_meta[$key] = $value;

		if (in_array($key, $rules_keys, true)) {
			$rules_meta[$key] = $value;
		} elseif (in_array($key, $objectives_keys, true)) {
			$objectives_meta[$key] = $value;
		}
	}
}

if (!empty($rules_meta['max_contracts']) && strpos($rules_meta['max_contracts'], '/') !== false) {
	[$main, $micro] = array_map('trim', explode('/', $rules_meta['max_contracts']));
	$rules_meta['max_contracts'] = "{$main} ({$micro})";
}

$status = $subscription->get_status();
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

$related_orders = $subscription->get_related_orders();


// --- Get ID for Reset Fee ---
$reset_product_id = null;
$full_product_name = trim($size_slug . ' ' . $product_name);


if ($full_product_name) {
	$args = [
		'limit' => -1,
		'status' => 'publish',
		'return' => 'objects',
	];
	$query = new WC_Product_Query($args);
	$products_found = $query->get_products();

	foreach ($products_found as $prod) {
		if (!$prod) {
			continue;
		}
		// Comparamos el nombre completo (case insensitive)
		if (strcasecmp($prod->get_name(), $full_product_name) === 0) {
			// Obtener categorías del producto
			$categories = get_the_terms($prod->get_id(), 'product_cat');
			if ($categories && !is_wp_error($categories)) {
				foreach ($categories as $cat) {
					$cat_slug = strtolower($cat->slug);
					$cat_name = strtolower($cat->name);
					if ($cat_slug === 'reset-fee' || $cat_name === 'reset fee') {
						$reset_product_id = $prod->get_id();
						break 2; // Salir de ambos foreach si encontramos
					}
				}
			}
		}
	}
}

$reset_url = '';
if ($reset_product_id) {
	$reset_url = wc_get_checkout_url() . '?add-to-cart=' . $reset_product_id;
}

$paged = isset($_GET['related_orders_page']) ? max(1, intval($_GET['related_orders_page'])) : 1;
$per_page = 5;
$total_orders = count($related_orders);
$total_pages = (int) ceil($total_orders / $per_page);
$offset = ($paged - 1) * $per_page;

// Obtener sólo los pedidos de la página actual
$orders_to_show = array_slice($related_orders, $offset, $per_page);

// Calcular rango mostrado
$first_item = $offset + 1;
$last_item = min($offset + count($orders_to_show), $total_orders);

$change_payment_modal_id = 'changePaymentMethodModal';
function render_change_payment_method_modal($subscription, $modal_id){
	ob_start();

		// Mock needed for stripe to render
		add_filter( 'woocommerce_is_order_pay_page', '__return_true' );
		add_filter( 'woocommerce_is_checkout', '__return_true' );

		// Force Stripe scripts to enqueue in this mocked context
		$gateway = WC()->payment_gateways()->payment_gateways()['stripe'];
		if ( method_exists( $gateway, 'payment_scripts' ) ) {
			$gateway->payment_scripts();
		}
		
		$close_btn = true;
		echo '<div class="woocommerce-checkout wc-checkout-in-modal">';
			wc_get_template(
				'checkout/form-change-payment-method.php',
				array( 'subscription' => $subscription )
			);
		echo '</div>';

		// Remove mocks
		remove_filter( 'woocommerce_is_order_pay_page', '__return_true' );
		remove_filter( 'woocommerce_is_checkout', '__return_true' );

	$template_html = ob_get_clean();

	$autoshow = change_payment_modal_should_show();

	if ( $autoshow ) {
		echo "<script>
			if (window.history.replaceState) {
				const url = new URL(window.location);
				url.searchParams.delete('pay_for_order');
				url.searchParams.delete('key');
				url.searchParams.delete('change_payment_method');
				window.history.replaceState({}, '', url.pathname + url.search);
			}
		</script>";
	}

	$modal_html = render_modal([
		'autoshow'		=> $autoshow,
		'notice'		=> true,
		'modalId'     	=> $modal_id,
		'modalTitle'  	=> 'Change payment method',
		'bodyContent' 	=> $template_html,
	]);

	echo $modal_html;
}

function change_payment_modal_should_show() {
    return (
        isset($_GET['pay_for_order']) &&
        isset($_GET['key']) &&
        isset($_GET['change_payment_method'])
    );
}

?>



<button type="button" class="w-100 p-0 border-0 bg-131210 text-start btn-reset" data-bs-toggle="modal"
	data-bs-target="#changeSubcriptionModal">
	<div class="border-gray d-flex flex-wrap align-items-center gap-2 mb-3 p-3 rounded-2xl">
		<div class="d-flex gap-3 flex-grow-1 flex-shirk-0 align-items-center">
			<div class="badge-mega badge-mega-sm <?php echo esc_attr($badge_class); ?>">
				<?php
				if ($status === 'pending-cancel') {
					echo esc_html__('Pending Cancellation', 'woocommerce');
				} else {
					echo esc_html(ucwords(str_replace('-', ' ', $status)));
				}
				?>
			</div>
			<div class="d-flex gap-2 align-items-center">
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
				<div class="fw-medium plan-name text-size-24 text-uppercase text-white">
					<?= esc_html($size_slug) ?> <?= esc_html($product_name) ?>
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
	</div>
</button>



<div class="account-box mb-32">
	<?php
	$subscription_id = $subscription->get_id();
	if ($subscription):
		$order = $subscription->get_order();
		$order_number = $order ? $order->get_order_number() : 'No order number';
		?>
		<div
			class="d-flex flex-column flex-lg-row flex-md-row gap-3 h-full justify-content-lg-between justify-content-md-between w-full flex-wrap">
			<div class="d-flex flex-column flex-grow-1 flex-shrink-0 justify-content-center">
				<div class="w-100 text-white text-size-20 fw-medium text-uppercase leading-6">
					<?php esc_html_e('Manage subscription', 'woocommerce'); ?>
				</div>
				<div class="w-100 text-a8a29e text-14px fw-medium leading-5">
					<?php if ('on-hold' === $status): ?>
						<?php esc_html_e('Complete payment to reactivate membership and restore access', 'woocommerce'); ?>
					<?php elseif ('expired' === $status): ?>
						<?php esc_html_e('Subscription has expired – Renew to restore access and benefits', 'woocommerce'); ?>
					<?php else: ?>
						<?php esc_html_e('Renew, change payment, or cancel subscription', 'woocommerce'); ?>
					<?php endif; ?>
				</div>
			</div>

			<div class="align-items-center d-flex flex-column flex-md-row flex-lg-row gap-2">
				<?php do_action('woocommerce_subscription_before_actions', $subscription); ?>

				<?php
				$actions = wcs_get_all_user_actions_for_subscription($subscription, get_current_user_id());
				$desired_order = ['subscription_renewal_early', 'change_payment_method', 'cancel'];
				$sorted_actions = [];
				foreach ($desired_order as $key) {
					if (isset($actions[$key])) {
						$sorted_actions[$key] = $actions[$key];
					}
				}
				?>

				<?php if ('on-hold' === $status): ?>

					<?php
					$related_orders = $subscription->get_related_orders('renewal');
					foreach ($related_orders as $order_id => $order_type) {
						$order = wc_get_order($order_id);
						if ($order && $order->has_status(['pending', 'failed'])) {
							$pay_url = $order->get_checkout_payment_url();
							?>
							<a href="<?php echo esc_url($pay_url); ?>" class="btn w-100 mega-btn-md mega-btn-primary-md">
								Complete Payment
							</a>
							<?php
							break;
						}
					}
					?>

				<?php elseif ('pending-cancel' === $status && $subscription->get_time('end') > current_time('timestamp')): ?>

					<?php
					$reactivate_url = add_query_arg(
						[
							'wcs_reactivate_subscription' => $subscription_id,
							'_wpnonce' => wp_create_nonce('wcs_reactivate_subscription_' . $subscription_id),
						],
						wc_get_endpoint_url('view-subscription', $subscription_id, wc_get_page_permalink('myaccount'))
					);
					?>
					<a href="<?php echo esc_url($reactivate_url); ?>"
						class="btn w-100 mega-btn-md mega-btn-primary-md wcs_reactivate_subscription">
						Reactivate
					</a>
					<a href="#" class="btn w-100 mega-btn-md mega-btn-secondary-md change_payment_method disabled"
						aria-disabled="true">
						Change Payment
					</a>
					<a href="#" class="btn w-100 mega-btn-md mega-btn-secondary-md cancel disabled" aria-disabled="true">
						Cancel
					</a>

				<?php elseif (in_array($status, ['cancelled', 'expired'], true)): ?>

					<a href="#" class="btn w-100 mega-btn-md mega-btn-primary-md disabled" aria-disabled="true">
						Reset
					</a>
					<a href="#" class="btn w-100 mega-btn-md mega-btn-secondary-md change_payment_method disabled"
						aria-disabled="true">
						Change Payment
					</a>
					<a href="#" class="btn w-100 mega-btn-md mega-btn-secondary-md cancel disabled" aria-disabled="true">
						Cancel
					</a>

				<?php else: ?>

					<?php
					if ('active' === $status): ?>
						<a id="custom_reset_btn" href="<?php echo esc_url($reset_url ?: '#'); ?>"
							class="mega-btn-md mega-btn-primary-md w-100" <?php echo $reset_url ? '' : 'onclick="alert(\'Reset product not found.\'); return false;"'; ?>>
							Reset
						</a>
					<?php endif; ?>

					<?php
					foreach ($sorted_actions as $key => $action):
						$classes = ['mega-btn-md', 'mega-btn-secondary-md', 'w-100', sanitize_html_class($key)];
						if (!empty($action['block_ui'])) {
							$classes[] = 'wcs_block_ui_on_click';
						}
						?>
						<?php if ($key === 'change_payment_method'): ?>
							<?php if (change_payment_modal_should_show()): ?>
								<a class="<?php echo esc_attr(implode(' ', $classes)); ?>" href="#"
								   data-bs-toggle="modal"
								   data-bs-target="#<?php echo esc_attr($change_payment_modal_id); ?>">
									<?php echo esc_html($action['name']); ?>
								</a>
							<?php else: ?>
								<a class="<?php echo esc_attr(implode(' ', $classes)); ?>" href="<?php echo esc_url($action['url']); ?>">
									<?php echo esc_html($action['name']); ?>
								</a>
							<?php endif; ?>
						<?php else: ?>
							<a href="<?php echo esc_url($action['url']); ?>" class="<?php echo esc_attr(implode(' ', $classes)); ?>">
								<?php echo esc_html($action['name']); ?>
							</a>
						<?php endif; ?>
					<?php endforeach; ?>

				<?php endif; ?>

				<?php do_action('woocommerce_subscription_after_actions', $subscription); ?>
			</div>
		</div>
		<?php render_change_payment_method_modal($subscription, $change_payment_modal_id); ?>
	<?php else: ?>
		<h5 class="mb-4">Subscription Not Found</h5>
	<?php endif; ?>
</div>

<div class="account-box mb-32">
	<?php
	// 1. Get the subscription and its first item (already done above)
	// $item = current($subscription->get_items()); // Already retrieved
	// $product = $item->get_product(); // Already retrieved
	$variation_id = $item ? $item->get_variation_id() : null;
	$variation = $variation_id ? wc_get_product($variation_id) : null;

	// 2. Get platform (pa_platform) and define its logo and name
	$platform_value = $item ? $item->get_meta('pa_platform') : '';

	$platform_term = get_term_by('slug', $platform_value, 'pa_platform');
	$platform_meta = $platform_term ? get_term_meta($platform_term->term_id) : [];

	$image_id = $platform_meta['attribute_image_id'][0] ?? null;
	$platform_logo = $image_id ? wp_get_attachment_url($image_id) : '';
	$platform_name = $platform_term->name ?? ucfirst($platform_value);

	// 3. Dates and days
	$next_payment = $subscription->get_time('next_payment');
	$start_date = $subscription->get_time('start');
	$end_date = $subscription->get_time('end');
	$today = current_time('timestamp');

	if ('pending-cancel' === $status && $end_date && $start_date) {
		$days_until_payment = floor(($end_date - $today) / DAY_IN_SECONDS);
		$total_days = floor(($end_date - $start_date) / DAY_IN_SECONDS);
		$current_day = $total_days - $days_until_payment + 1;

		$progress = ($total_days > 0) ? ($current_day / $total_days) * 100 : 0;
		$progress = max(0, min(100, round($progress)));
	} elseif ($next_payment && $start_date) {
		$days_until_payment = floor(($next_payment - $today) / DAY_IN_SECONDS);
		$total_days = floor(($next_payment - $start_date) / DAY_IN_SECONDS);
		$current_day = $total_days - $days_until_payment + 1;

		$progress = ($total_days > 0) ? ($current_day / $total_days) * 100 : 0;
		$progress = max(0, min(100, round($progress)));
	} else {
		$days_until_payment = '—';
		$total_days = 0;
		$current_day = 0;
		$progress = 0;
	}

	// 4. Price
	$total = $subscription->get_formatted_order_total();
	// $item = current($subscription->get_items()); // Already retrieved
	$subtotal_raw = $item ? $item->get_subtotal() : 0;
	$billing_interval = $subscription->get_billing_interval();
	$billing_period = $subscription->get_billing_period();
	$formatted_price = number_format((float) $subtotal_raw, 2, ',', ''); // "49,99"
	?>

	<div class="d-flex flex-column gap-32 w-100">
		<div class="d-flex flex-column flex-lg-row flex-md-row flex-sm-row gap-3 justify-content-between w-100">
			<div class="d-flex gap-3 align-items-center flex-grow-1 flex-shrink-1">
				<?php if ($platform_logo): ?>
					<img src="<?php echo esc_url($platform_logo); ?>" alt="<?php echo esc_attr($platform_name); ?>"
						width="48" height="48" style="border-radius: 999px; background: black;">
				<?php endif; ?>
				<div class="text-white text-size-20 fw-medium text-uppercase">
					<?php echo esc_html($platform_name); ?>
				</div>
			</div>
			<div class="flex-fill text-lg-end text-md-end text-sm-end">
				<?php
				// Etiqueta según estado
				$status_label = 'Next payment date:';
				if ($subscription->has_status('on-hold')) {
					$status_label = 'Payment due:';
				} elseif ($subscription->has_status('pending-cancel')) {
					$status_label = 'Cancellation date:';
				} elseif ($subscription->has_status('expired')) {
					$status_label = 'Expired date:';
				} elseif ($subscription->has_status('cancelled')) {
					$status_label = 'Cancellation date:';
				}
				?>
				<div class="fw-medium text-base text-white"><?php echo esc_html($status_label); ?></div>
				<?php
				// Selección de la fecha a mostrar, usando timestamps
				if ($subscription->has_status('cancelled')) {
					$timestamp = $subscription->get_time('cancelled');
				} elseif ($subscription->has_status('expired')) {
					$timestamp = $subscription->get_time('end');
				} elseif ($subscription->has_status('pending-cancel')) {
					$timestamp = $subscription->get_time('end');
				} else {
					$timestamp = $subscription->get_time('next_payment');
				}
				?>

				<div class="flex-fill text-lg-end text-md-end text-sm-end fw-medium text-sm text-a8a29e">
					<?php echo $timestamp ? date_i18n('F j, Y', $timestamp) : '—'; ?>
				</div>

			</div>
		</div>

		<div
			class="d-flex flex-column flex-lg-row flex-md-row flex-sm-row gap-3 justify-content-between w-100 align-items-center">
			<div class="d-flex gap-2 align-items-center flex-fill">
				<div class="d-inline-flex align-items-center gap-2 w-100 h-100">
					<div class="progress-circle position-relative">
						<svg width="48" height="48" viewBox="0 0 36 36"
							class="position-absolute top-0 start-0 rotate-svg">
							<circle cx="18" cy="18" r="16" stroke="#404040" stroke-width="4" fill="none" />

							<circle cx="18" cy="18" r="16"
								stroke="<?php echo ('on-hold' === $status) ? '#404040' : '#2DD4BF'; ?>" stroke-width="4"
								fill="none" stroke-linecap="round" stroke-dasharray="100"
								stroke-dashoffset="<?php echo 100 - $progress; ?>" />
						</svg>
						<div
							class="d-flex justify-content-center align-items-center w-100 h-100 position-absolute top-0 start-0">
							<span class="circle-label">
								<?php
								if (in_array($status, ['cancelled', 'expired'], true)) {
									echo 'X';
								} elseif ('on-hold' === $status) {
									echo '$';
								} else {
									echo esc_html($days_until_payment);
								}
								?>
							</span>
						</div>
					</div>


					<div class="d-flex flex-column justify-content-center align-items-start flex-grow-1">
						<?php if (in_array($status, ['cancelled'], true)): ?>
							<div class="text-f43f5e fs-6 fw-medium">No billing cycles</div>
							<div class="small fw-medium">Subscription ended</div>
						<?php elseif ('expired' === $status): ?>
							<div class="text-f43f5e fs-6 fw-medium">Subscription expired</div>
							<div class="small fw-medium">No access to benefits and services </div>
						<?php elseif ('on-hold' === $status): ?>
							<div class="text-f43f5e fs-6 fw-medium">Awaiting payment</div>
							<div class="small fw-medium">Access suspended until payment is completed </div>
						<?php else: ?>
							<div class="text-white fs-6 fw-medium">Days until payment</div>
							<div class="text-a8a29e small fw-medium">
								Day <?php echo esc_html($current_day); ?> of
								<?php echo esc_html($total_days); ?> in billing cycle
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<div class="d-flex gap-2">
				<span class="fs-5 leading-normal text-primary">$</span>
				<span
					class="fw-medium text-32px text-primary leading-10 text-uppercase"><?php echo esc_html($formatted_price); ?></span>
				<span class="align-items-center d-flex fw-medium text-base text-white">per
					<?php echo esc_html($billing_period); ?></span>
			</div>
		</div>

		<div class="separator"></div>

		<div class="d-flex justify-content-between align-items-center w-100">
			<div>
				<span class="text-a8a29e text-sm fw-medium">Questions? Check out </span>
				<a href="/faq-billing" class="faq-link">Billing
					FAQ</a>
			</div>
			<div class="d-flex align-items-center gap-2">
				<span class="text-a8a29e text-sm fw-medium">Auto renew</span>
				<div class="wcs-auto-renew-toggle">
					<?php
					$is_active = 'active' === $status;
					$toggle_classes = [
						'subscription-auto-renew-toggle',
						'subscription-auto-renew-toggle--hidden',
					];

					if ($is_active) {
						if ($subscription->is_manual()) {
							$toggle_label = __('Enable auto renew', 'woocommerce-subscriptions');
							$toggle_classes[] = 'subscription-auto-renew-toggle--off';
						} else {
							$toggle_label = __('Disable auto renew', 'woocommerce-subscriptions');
							$toggle_classes[] = 'subscription-auto-renew-toggle--on';
						}
					} else {
						$toggle_label = __('Auto renew not available', 'woocommerce-subscriptions');
						$toggle_classes[] = 'subscription-auto-renew-toggle--off'; // Always force OFF
						$toggle_classes[] = 'subscription-auto-renew-toggle--disabled';
						$toggle_classes[] = 'subscription-auto-renew-toggle--visually-disabled';
					}

					if (!$is_active) {
						$toggle_classes[] = 'subscription-auto-renew-toggle--disabled no-active';
						$toggle_classes[] = 'subscription-auto-renew-toggle--visually-disabled';
					}
					?>

					<a <?php if ($is_active): ?> href="#" <?php endif; ?>
						class="<?php echo esc_attr(implode(' ', $toggle_classes)); ?>"
						aria-label="<?php echo esc_attr($toggle_label); ?>" <?php if (!$is_active): ?>
							style="pointer-events: none; cursor: not-allowed;" <?php endif; ?>>
						<i class="subscription-auto-renew-toggle__i" aria-hidden="true"></i>
					</a>
				</div>

			</div>
		</div>
	</div>
</div>
<div class="block-object-rules d-flex gap-3 d-flex gap-3 mb-32">
	<div class="account-box w-100">
		<div class="text-white text-size-20 fw-medium text-uppercase">Objectives</div>
		<div class="d-flex flex-column">
			<?php foreach ($objectives_meta as $key => $value): ?>
				<div class="border-bottom-separator d-flex justify-content-between py-3">
					<div class="text-a8a29e fw-medium text-14px">
						<?= esc_html(ucwords(str_replace('_', ' ', $key))) ?>
					</div>
					<div class="text-a8a29e fw-medium text-14px">
						<?= esc_html($value) ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<div class="account-box w-100">
		<div class="text-white text-size-20 fw-medium text-uppercase">Rules</div>
		<div class="d-flex flex-column">
			<?php foreach ($rules_meta as $key => $value): ?>

				<div class="border-bottom-separator d-flex justify-content-between py-3">
					<div class="text-a8a29e fw-medium text-14px">
						<?= esc_html(ucwords(str_replace('_', ' ', $key))) ?>
					</div>
					<div class="text-a8a29e fw-medium text-14px">
						<?= esc_html($value) ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
<?php if (!empty($orders_to_show)): ?>
	<div class="mb-32">
		<div class="subscription-orders-accordion mb-4 overflow-hidden rounded-2xl">
			<div class="fw-medium text-size-20 text-uppercase text-white p-3 bg-1e1e1e">
				Related Orders
			</div>
			<div class="accordion mega-accordion" id="relatedOrdersAccordion">
				<?php foreach ($orders_to_show as $index => $order_id):
					$order = wc_get_order($order_id);
					if (!$order)
						continue;

					// Datos del pedido
					$order_number = $order->get_order_number();
					$order_date = $order->get_date_created();
					$order_total = $order->get_formatted_order_total();
					$order_status = wc_get_order_status_name($order->get_status());
					$status_slug = $order->get_status();
					$badge_class = $status_classes[$status_slug] ?? 'badge-mega-default';

					// Etiquetas de producto
					$product_labels = [];
					foreach ($order->get_items() as $item_in_order) {
						$product_name = $item_in_order->get_name();
						$account_type = '';
						$platform = '';
						foreach ($item_in_order->get_meta_data() as $meta_in_order) {
							if (stripos($meta_in_order->key, 'pa_account-size') !== false) {
								$term_match = array_filter($all_size_terms, function ($t) use ($meta_in_order) {
									return $t->slug === $meta_in_order->value;
								});
								$term_match = reset($term_match);
								if ($term_match && !is_wp_error($term_match)) {
									$account_type = $term_match->name;
								}
							}
							if (stripos($meta_in_order->key, 'pa_platform') !== false) {
								$term_pl = get_term_by('slug', $meta_in_order->value, 'pa_platform');
								if ($term_pl && !is_wp_error($term_pl)) {
									$platform = $term_pl->name;
								}
							}
						}
						$label = trim($account_type . ' - ' . $platform);
						if ($label === '-' || $label === '') {
							$label = $product_name;
						}
						$product_labels[] = $label;
					}

					// Add-ons
					$addons = [];
					foreach ($order->get_items('fee') as $fee) {
						foreach ($fee->get_meta_data() as $meta) {
							if ('_wc_checkout_add_on_label' === $meta->key) {
								if (is_array($meta->value)) {
									$addons = array_merge($addons, $meta->value);
								} else {
									$addons[] = $meta->value;
								}
							}
						}
					}
					$addons_list = implode(', ', $addons);
					$show_addons = !empty($addons_list);

					$subtotal = $order->get_subtotal();
					$total = $order->get_total();
					$addons_total = $total - $subtotal;
					$payment_method = $order->get_payment_method_title();

					// IDs collapse
					$collapse_id = 'collapseOrder' . ($offset + $index);
					$heading_id = 'headingOrder' . ($offset + $index);
					?>
					<div class="accordion-item mega-accordion-item">
						<h2 class="accordion-header" id="<?php echo esc_attr($heading_id); ?>">
							<button
								class="accordion-button mega-accordion-button collapsed d-flex justify-content-between align-items-center"
								type="button" data-bs-toggle="collapse"
								data-bs-target="#<?php echo esc_attr($collapse_id); ?>" aria-expanded="false"
								aria-controls="<?php echo esc_attr($collapse_id); ?>">
								<div class="flex-grow-1">
									<div class="fw-medium text-14px-line-20px text-white">
										#<?php echo esc_html($order_number); ?> - <?php echo esc_html($platform); ?>
									</div>
									<div class="text-a8a29e text-14px-line-20px fw-medium">
										<?php echo esc_html($order_date->date_i18n(get_option('date_format'))); ?>
									</div>
								</div>
								<div class="d-flex align-items-center gap-3">
									<span
										class="text-white text-14px-line-20px"><?php echo wp_kses_post($order_total); ?></span>
									<span class="badge-mega badge-mega-sm <?php echo esc_attr($badge_class); ?>">
										<?php echo esc_html($order_status); ?>
									</span>
								</div>
								<svg class="accordion-arrow ms-3 transition" xmlns="http://www.w3.org/2000/svg" width="24"
									height="24" viewBox="0 0 24 24" fill="none">
									<mask id="mask0_11632_14710" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
										width="24" height="24">
										<rect width="24" height="24" fill="#D9D9D9" />
									</mask>
									<g mask="url(#mask0_11632_14710)">
										<path d="M12 15.5L16.5 11L15.075 9.6L12 12.675L8.925 9.6L7.5 11L12 15.5Z"
											fill="white" />
									</g>
								</svg>
							</button>
						</h2>
						<div id="<?php echo esc_attr($collapse_id); ?>" class="accordion-collapse collapse"
							aria-labelledby="<?php echo esc_attr($heading_id); ?>" data-bs-parent="#relatedOrdersAccordion">
							<div class="accordion-body d-flex gap-3 pt-0">
								<div class="accordion-body-list d-flex flex-column flex-grow-1">
									<div class="accordion-body-list-item py-3 d-flex align-items-center">
										<div class="fw-bold text-base text-white">Order Details</div>
									</div>
									<div class="accordion-body-list-item py-3 d-flex align-items-center gap-3">
										<div class="fw-medium text-14px text-a8a29e w-50">Product</div>
										<div class="fw-medium text-14px text-a8a29e w-50">Total</div>
									</div>
									<div class="accordion-body-list-item py-3 d-flex align-items-center gap-3">
										<div class="fw-medium text-14px text-primary w-50">
											<?php echo esc_html(implode(', ', $product_labels)); ?>
										</div>
										<div class="fw-medium text-14px text-a8a29e w-50"><?php echo wc_price($subtotal); ?>
										</div>
									</div>
									<div class="accordion-body-list-item py-3 d-flex align-items-center gap-3">
										<div class="fw-medium text-14px text-a8a29e w-50">Subtotal</div>
										<div class="fw-medium text-14px text-a8a29e w-50"><?php echo wc_price($subtotal); ?>
										</div>
									</div>
									<?php if ($show_addons): ?>
										<?php
										$addons_words = explode(' ', $addons_list);
										$first_two = array_slice($addons_words, 0, 2);
										$rest = array_slice($addons_words, 2);
										?>
										<div class="accordion-body-list-item py-3 d-flex align-items-center gap-3">
											<div class="fw-medium text-14px text-a8a29e w-50">
												Addons: <?php echo esc_html(implode(' ', $first_two)); ?><br
													class="d-md-none" />
												<?php echo esc_html(implode(' ', $rest)); ?>
											</div>
											<div class="fw-medium text-14px text-a8a29e w-50">
												<?php echo wc_price($addons_total); ?>
											</div>
										</div>
									<?php endif; ?>
									<div class="accordion-body-list-item py-3 d-flex align-items-center gap-3">
										<div class="fw-medium text-14px text-a8a29e w-50">Total</div>
										<div class="fw-medium text-14px text-a8a29e w-50"><?php echo wc_price($total); ?>
										</div>
									</div>
									<div class="accordion-body-list-item py-3 d-flex align-items-center gap-3">
										<div class="fw-medium text-14px text-a8a29e w-50">Payment Method</div>
										<div class="fw-medium text-14px text-a8a29e w-50">
											<?php echo esc_html($payment_method); ?></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<?php if ($total_orders > $per_page): // Solo mostrar paginación si hay más órdenes que $per_page ?>
				<div class="align-items-center bg-1e1e1e d-flex gap-2 justify-content-end p-3 w-100">
					<div class="text-a8a29e text-14px-line-20px fw-medium">
						<?php echo esc_html("Showing {$last_item}/{$total_orders}"); ?>
					</div>
					<div class="d-flex gap-1 align-items-center justify-content-start">
						<!-- Flecha Prev -->
						<?php if ($paged > 1): ?>
							<a href="<?php echo esc_url(add_query_arg('related_orders_page', $paged - 1)); ?>"
								class="pagination-arrow pagination-arrow--prev">
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
									<mask id="mask0_12964_3403_disabled" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0"
										y="0" width="20" height="20">
										<rect width="20" height="20" fill="#D9D9D9" />
									</mask>
									<g mask="url(#mask0_12964_3403_disabled)">
										<path
											d="M11.6665 15L6.6665 10L11.6665 5L12.8332 6.16667L8.99984 10L12.8332 13.8333L11.6665 15Z"
											fill="white" />
									</g>
								</svg> </a>
						<?php else: ?>
							<div class="pagination-arrow pagination-arrow--prev pagination-arrow--disabled">
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
									<mask id="mask0_12964_3403_disabled" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0"
										y="0" width="20" height="20">
										<rect width="20" height="20" fill="#D9D9D9" />
									</mask>
									<g mask="url(#mask0_12964_3403_disabled)">
										<path
											d="M11.6665 15L6.6665 10L11.6665 5L12.8332 6.16667L8.99984 10L12.8332 13.8333L11.6665 15Z"
											fill="white" />
									</g>
								</svg>
							</div>
						<?php endif; ?>

						<!-- Números de página -->
						<?php for ($i = 1; $i <= $total_pages; $i++): ?>
							<?php if ($i === $paged): ?>
								<div class="pagination-number-wrapper pagination-number-wrapper--active">
									<div class="pagination-number"><?php echo esc_html($i); ?></div>
								</div>
							<?php else: ?>
								<a href="<?php echo esc_url(add_query_arg('related_orders_page', $i)); ?>"
									class="pagination-number-wrapper">
									<div class="pagination-number"><?php echo esc_html($i); ?></div>
								</a>
							<?php endif; ?>
						<?php endfor; ?>

						<!-- Flecha Next -->
						<?php if ($paged < $total_pages): ?>
							<a href="<?php echo esc_url(add_query_arg('related_orders_page', $paged + 1)); ?>"
								class="pagination-arrow pagination-arrow--next">
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
									<mask id="mask0_12964_3193" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="-1" y="0"
										width="21" height="20">
										<rect x="-0.416504" width="20" height="20" fill="#D9D9D9" />
									</mask>
									<g mask="url(#mask0_12964_3193)">
										<path d="M10.0833 10L6.25 6.16667L7.41667 5L12.4167 10L7.41667 15L6.25 13.8333L10.0833 10Z"
											fill="white" />
									</g>
								</svg> </a>
						<?php else: ?>
							<div class="pagination-arrow pagination-arrow--next pagination-arrow--disabled">
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
									<mask id="mask0_12964_3193" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="-1" y="0"
										width="21" height="20">
										<rect x="-0.416504" width="20" height="20" fill="#D9D9D9" />
									</mask>
									<g mask="url(#mask0_12964_3193)">
										<path d="M10.0833 10L6.25 6.16667L7.41667 5L12.4167 10L7.41667 15L6.25 13.8333L10.0833 10Z"
											fill="white" />
									</g>
								</svg>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>

		</div>
	</div>
<?php else: ?>
	<p class="text-a8a29e">No related orders found for this subscription.</p>
<?php endif; ?>