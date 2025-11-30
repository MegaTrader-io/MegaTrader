<?php
/**
 * Order details with subscription-like modal and order recurring trigger
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.0.0
 */

defined('ABSPATH') || exit;

// Get the order and validate
$order = wc_get_order($order_id);
if (!$order) {
	return;
}

// Prepare order details
$order_items = $order->get_items(apply_filters('woocommerce_purchase_order_item_types', 'line_item'));
$show_purchase_note = $order->has_status(apply_filters('woocommerce_purchase_note_order_statuses', ['completed', 'processing']));
$downloads = $order->get_downloadable_items();
$show_customer_details = $order->get_user_id() === get_current_user_id();

// --- Product data ---
$item = current($order_items);
$product = $item ? $item->get_product() : null;
$product_name = $product ? $product->get_name() : '';

// Attributes: account size
$size_slug = '';
if ($product) {
	$size_value = $product->get_attribute('pa_account-size');
	if ($size_value) {
		$term = get_term_by('name', $size_value, 'pa_account-size');
		if ($term) {
			$size_slug = $term->slug;
		}
	}
}

// --- Meta data for Objectives and Rules ---
$product_meta = [];
$rules_meta = [];
$objectives_meta = [];
$rules_keys = ['max_contracts', 'daily_loss_limit', 'daily_loss_limit_soft_breach', 'trailing_max_drawdown', 'drawdown_mode', 'max_accounts'];
$objectives_keys = ['min_trading_days', 'min_trading_days_to_payout', 'consistency', 'profit_target'];
if ($product) {
	foreach ($product->get_meta_data() as $meta) {
		if ('' === $meta->value) {
			continue;
		}
		$product_meta[$meta->key] = $meta->value;
		if (in_array($meta->key, $rules_keys, true)) {
			$rules_meta[$meta->key] = $meta->value;
		} elseif (in_array($meta->key, $objectives_keys, true)) {
			$objectives_meta[$meta->key] = $meta->value;
		}
	}
}

// --- Status and badge class ---
$status = $order->get_status();
$status_classes = [
	'completed' => 'badge-mega-active',
	'processing' => 'badge-mega-hold',
	'on-hold' => 'badge-mega-pending',
	'pending' => 'badge-mega-pending',
	'pending-cancel' => 'badge-mega-pending-cancel',
	'cancelled' => 'badge-mega-cancelled',
	'expired' => 'badge-mega-expired',
	'refunded' => 'badge-mega-cancelled',
	'failed' => 'badge-mega-cancelled',
];
$badge_class = $status_classes[$status] ?? 'badge-mega-default';

// Order product ID for Order Again
$product_id = $product ? $product->get_id() : 0;

// Format price
$raw_price = $product ? $product->get_price() : 0;
$formatted_price = number_format((float) $raw_price, 2, ',', '');

// --- Fetch all user orders ---
$all_orders = wc_get_orders([
	'customer_id' => get_current_user_id(),
	'status' => ['completed', 'processing', 'on-hold', 'pending'],
	'limit' => -1,
	'return' => 'objects',
]);

// --- Filter out orders with related subscriptions ---
$filtered_orders = [];
foreach ($all_orders as $o) {
	if (function_exists('wcs_order_contains_subscription') && wcs_order_contains_subscription($o)) {
		continue;
	}
	$filtered_orders[] = $o;
}

// --- Prepare current composite key ---
$current_key = strtolower(trim("{$size_slug} {$product_name}"));

// --- Compare and build logos array ---
$logos = [];
$orderIgual = 0;
$related_orders = [];
$platform_names = [];

foreach ($filtered_orders as $fo) {
	foreach ($fo->get_items() as $it) {
		$p = $it->get_product();
		if (!$p) {
			continue;
		}
		// Get account-size slug
		$size2 = $p->get_attribute('pa_account-size');
		$term2 = get_term_by('name', $size2, 'pa_account-size');
		$slug2 = $term2 ? $term2->slug : '';
		// Build composite key for this item
		$other_key = strtolower(trim("{$slug2} {$p->get_name()}"));
		if ($other_key === $current_key) {
			$orderIgual++;
			$related_orders[] = $fo->get_id();
			// Fetch platform logo URL
			$platform_slug = $p->get_attribute('pa_platform');
			$term_pl = get_term_by('slug', $platform_slug, 'pa_platform');
			$img_id = $term_pl ? get_term_meta($term_pl->term_id, 'attribute_image_id', true) : '';
			$logo_url = $img_id ? wp_get_attachment_url($img_id) : '';
			if ($logo_url && !in_array($logo_url, $logos, true)) {
				$logos[] = $logo_url;
			}
			$plat_name = $term_pl ? $term_pl->name : ucfirst($platform_slug);
			if (!in_array($plat_name, $platform_names, true)) {
				$platform_names[] = $plat_name;
			}
			// Only count one match per order
			break;
		}
	}
}

$related_orders = array_unique($related_orders);

// --- Count platforms (logos) ---
$platform = count($logos);
$platform = count($platform_names);

$paged = isset($_GET['related_orders_page']) ? max(1, intval($_GET['related_orders_page'])) : 1;
$per_page = 5;
$total_orders = count($related_orders);
$total_pages = (int) ceil($total_orders / $per_page);
$offset = ($paged - 1) * $per_page;

// 2) Obtener sólo los pedidos de la página actual
$orders_to_show = array_slice($related_orders, $offset, $per_page);

// 3) Calcular rango mostrado
$first_item = $offset + 1;
$last_item = min($offset + count($orders_to_show), $total_orders);


?>

<?php get_template_part('template-parts/orders-subscriptions/order-selector', null, [
	'selected_item' => [
		'status' 		=> $status,
		'size_slug' 	=> $size_slug,
		'product_name' 	=> $product_name,
	]
]); ?>

<?php if ($product_id): 
	$order_again_url = home_url( '/checkout/?add-to-cart=' . $product_id );
?>
	<div class="mb-32 mt-card">
		<div
			class="d-flex flex-column flex-lg-row flex-md-row gap-3 h-full justify-content-lg-between justify-content-md-between w-full">
			<div class="d-flex flex-column flex-grow-1 flex-shrink-0 justify-content-center">
				<div class="w-100 text-white text-size-20 fw-medium text-uppercase leading-6">
					<?php printf(esc_html__('Manage Order', 'woocommerce')); ?>
				</div>
				<div class="w-100 text-a8a29e text-14px fw-medium leading-5">
					<?php esc_html_e('View all your orders for this product and current access status', 'woocommerce'); ?>
				</div>
			</div>
			<div class="align-items-center d-flex flex-column flex-md-row flex-lg-row gap-2">
				<a href="<?= esc_attr($order_again_url) ?>" class="btn w-100 mega-btn-md mega-btn-primary-md">
					<?= Label::META_SUBSCRIPTIONS_BILLING['btn_order_again_label']; ?>
				</a>
			</div>
		</div>
	</div>
<?php endif; ?>

<!-- One-Time Purchase Info Block -->
<div class="mb-32 mt-card">
	<div class="d-flex flex-column gap-32 w-100">
		<div class="d-flex flex-column flex-lg-row flex-md-row flex-sm-row gap-3 justify-content-between w-100">
			<div class="d-flex gap-3 align-items-center flex-grow-1 flex-shrink-1">
				<?php if ($platform > 1): ?>
					<div class="d-flex">
						<?php foreach ($logos as $logo_url): ?>
							<div class="logo-container position-relative d-inline-block">
								<img src="<?php echo esc_url($logo_url); ?>" width="48" height="48" style="max-height: 40px;">
							</div>
						<?php endforeach; ?>
					</div>
					<div class="">
						<div class="text-white text-size-20 fw-medium text-uppercase">
							<?php echo esc_html($platform . ' Platforms'); ?>
						</div>
						<div class="w-100 text-a8a29e text-14px fw-medium leading-normal">
							<?php echo esc_html(implode(', ', $platform_names)); ?>
						</div>
					</div>

				<?php else: ?>
					<?php if (!empty($logos[0])): ?>
						<img src="<?php echo esc_url($logos[0]); ?>" width="48" height="48"
							style="border-radius:999px;background:black;">
					<?php endif; ?>
					<div class="text-white text-size-20 fw-medium text-uppercase">
						<?php
						echo esc_html(implode(', ', $platform_names));
						?>
					</div>

				<?php endif; ?>
			</div>
			<div class="flex-fill text-lg-end text-md-end text-sm-end">
				<div class="fw-medium text-base text-white"><?php esc_html_e('Next payment date:', 'woocommerce'); ?>
				</div>
				<div class="flex-fill text-lg-end text-md-end text-sm-end fw-medium text-sm text-a8a29e">
					<?php esc_html_e('N/A - One-time purchase', 'woocommerce'); ?>
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
							<circle cx="18" cy="18" r="16" stroke="#2DD4BF" stroke-width="4" fill="none" />
						</svg>
						<div
							class="d-flex justify-content-center align-items-center w-100 h-100 position-absolute top-0 start-0">
							<span class="circle-label"><?php echo esc_html($orderIgual); ?></span>
						</div>
					</div>
					<div class="d-flex flex-column justify-content-center align-items-start flex-grow-1">
						<div class="text-white fs-6 fw-medium"><?php esc_html_e('Total purchases', 'woocommerce'); ?>
						</div>
						<div class="text-a8a29e small fw-medium">
							<?php printf(esc_html__('Purchased %d times on this plan', 'woocommerce'), $orderIgual); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="d-flex gap-2">
				<span class="fs-5 leading-normal text-primary">$</span>
				<span
					class="fw-medium text-32px text-primary leading-10 text-uppercase"><?php echo esc_html($formatted_price); ?></span>
				<span
					class="align-items-center d-flex fw-medium text-base text-white"><?php esc_html_e('One-time fee', 'woocommerce'); ?></span>
			</div>
		</div>
		<div class="separator"></div>
		<div class="d-flex justify-content-between align-items-center w-100">
			<div>
				<span
					class="text-a8a29e text-sm fw-medium"><?php esc_html_e('Questions? Check out', 'woocommerce'); ?></span>
				<a href="/faq-billing" class="faq-link"><?php esc_html_e('Billing FAQ', 'woocommerce'); ?></a>
			</div>
		</div>
	</div>
</div>

<div class="d-flex gap-3 d-flex flex-column flex-lg-row flex-md-row gap-3 mb-32">
	<div class="mt-card h-auto">
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

	<div class="mt-card h-auto">
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


<?php if ( ! empty( $orders_to_show ) ): ?>
    <div class="mb-32">
        <div class="border-0 mb-4 mt-card overflow-hidden p-0">
            <div class="fw-medium text-size-20 text-uppercase text-white p-3 bg-1e1e1e">
                Related Orders
            </div>
            <div class="accordion mega-accordion" id="relatedOrdersAccordion">
                <?php foreach ( $orders_to_show as $index => $order_id ): 
                    $related_order = wc_get_order( $order_id );
                    if ( ! $related_order ) {
                        continue;
                    }

                    // Datos básicos
                    $order_number        = $related_order->get_order_number();
                    $order_date          = $related_order->get_date_created();
                    $order_total_display = $related_order->get_formatted_order_total();
                    $order_status_name   = wc_get_order_status_name( $related_order->get_status() );
                    $status_slug         = $related_order->get_status();
                    $badge_class         = $status_classes[ $status_slug ] ?? 'badge-mega-default';

                    // Etiquetas de productos
                    $product_labels = [];
                    foreach ( $related_order->get_items() as $item ) {
                        $product_labels[] = $item->get_name();
                    }
                    $labels_display = implode( ', ', $product_labels );

                    // Add-ons
                    $addons = [];
                    foreach ( $related_order->get_items( 'fee' ) as $fee ) {
                        foreach ( $fee->get_meta_data() as $meta ) {
                            if ( '_wc_checkout_add_on_label' === $meta->key ) {
                                $addons = array_merge( $addons, (array) $meta->value );
                            }
                        }
                    }
                    $addons_list = implode( ', ', $addons );
                    $show_addons = ! empty( $addons_list );

                    // Totales
                    $subtotal       = $related_order->get_subtotal();
                    $total          = $related_order->get_total();
                    $addons_total   = $total - $subtotal;
                    $payment_method = $related_order->get_payment_method_title();

                    // IDs para collapse
                    $collapse_id = 'collapseOrder' . ( $offset + $index );
                    $heading_id  = 'headingOrder' . ( $offset + $index );
                ?>
                <div class="accordion-item mega-accordion-item">
                    <h2 class="accordion-header" id="<?php echo esc_attr( $heading_id ); ?>">
                        <button class="accordion-button mega-accordion-button collapsed d-flex justify-content-between align-items-center"
                                type="button" data-bs-toggle="collapse"
                                data-bs-target="#<?php echo esc_attr( $collapse_id ); ?>"
                                aria-expanded="false"
                                aria-controls="<?php echo esc_attr( $collapse_id ); ?>">
                            <div class="flex-grow-1">
                                <div class="fw-medium text-14px-line-20px text-white">
                                    #<?php echo esc_html( $order_number ); ?>
                                </div>
                                <div class="text-a8a29e text-14px-line-20px fw-medium">
                                    <?php echo $order_date ? esc_html( $order_date->date_i18n( get_option( 'date_format' ) ) ) : ''; ?>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <span class="text-white text-14px-line-20px"><?php echo wp_kses_post( $order_total_display ); ?></span>
                                <span class="badge-mega badge-mega-md <?php echo esc_attr( $badge_class ); ?>">
                                    <?php echo esc_html( $order_status_name ); ?>
                                </span>
                            </div>
                            <svg class="accordion-arrow ms-3 transition" xmlns="http://www.w3.org/2000/svg" width="24"
                                 height="24" viewBox="0 0 24 24" fill="none">
                                <mask id="mask0">
                                    <rect width="24" height="24" fill="#D9D9D9" />
                                </mask>
                                <g mask="url(#mask0)">
                                    <path d="M12 15.5L16.5 11L15.075 9.6L12 12.675L8.925 9.6L7.5 11L12 15.5Z"
                                          fill="white" />
                                </g>
                            </svg>
                        </button>
                    </h2>
                    <div id="<?php echo esc_attr( $collapse_id ); ?>" class="accordion-collapse collapse"
                         aria-labelledby="<?php echo esc_attr( $heading_id ); ?>" data-bs-parent="#relatedOrdersAccordion">
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
                                    <div class="fw-medium text-primary w-50"><?php echo esc_html( $labels_display ); ?></div>
                                    <div class="fw-medium text-a8a29e w-50"><?php echo wc_price( $subtotal ); ?></div>
                                </div>
                                <div class="accordion-body-list-item py-3 d-flex align-items-center gap-3">
                                    <div class="fw-medium text-a8a29e w-50">Subtotal</div>
                                    <div class="fw-medium text-a8a29e w-50"><?php echo wc_price( $subtotal ); ?></div>
                                </div>
                                <?php if ( $show_addons ): ?>
                                    <?php
                                    $addons_words = explode( ' ', $addons_list );
                                    $first_two    = array_slice( $addons_words, 0, 2 );
                                    $rest         = array_slice( $addons_words, 2 );
                                    ?>
                                    <div class="accordion-body-list-item py-3 d-flex align-items-center gap-3">
                                        <div class="fw-medium text-a8a29e w-50">
                                            Addons: <?php echo esc_html( implode( ' ', $first_two ) ); ?><br class="d-md-none" />
                                            <?php echo esc_html( implode( ' ', $rest ) ); ?>
                                        </div>
                                        <div class="fw-medium text-a8a29e w-50"><?php echo wc_price( $addons_total ); ?></div>
                                    </div>
                                <?php endif; ?>
                                <div class="accordion-body-list-item py-3 d-flex align-items-center gap-3">
                                    <div class="fw-medium text-a8a29e w-50">Total</div>
                                    <div class="fw-medium text-a8a29e w-50"><?php echo wc_price( $total ); ?></div>
                                </div>
                                <div class="accordion-body-list-item py-3 d-flex align-items-center gap-3">
                                    <div class="fw-medium text-a8a29e w-50">Payment Method</div>
                                    <div class="fw-medium text-a8a29e w-50"><?php echo esc_html( $payment_method ); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if ( $total_orders > $per_page ): ?>
                <div class="align-items-center bg-1e1e1e d-flex gap-2 justify-content-end p-3 w-100">
                    <div class="text-a8a29e text-14px-line-20px fw-medium">
                        <?php echo esc_html( "Showing {$last_item}/{$total_orders}" ); ?>
                    </div>
                    <div class="d-flex gap-1 align-items-center justify-content-start">
                        <!-- Flecha Prev -->
                        <?php if ( $paged > 1 ): ?>
                            <a href="<?php echo esc_url( add_query_arg( 'related_orders_page', $paged - 1 ) ); ?>"
                               class="pagination-arrow prev-active">
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
							</svg>                            </a>
                        <?php else: ?>
                            <div class="pagination-arrow prev-disable">
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
							</svg>                            </div>
                        <?php endif; ?>

                        <!-- Números de página -->
                        <?php for ( $i = 1; $i <= $total_pages; $i++ ): ?>
                            <?php if ( $i === $paged ): ?>
                                <div class="pagination-number-wrapper">
                                    <div class="pagination-number"><?php echo esc_html( $i ); ?></div>
                                </div>
                            <?php else: ?>
                                <a href="<?php echo esc_url( add_query_arg( 'related_orders_page', $i ) ); ?>"
                                   class="pagination-number-wrapper">
                                    <div class="pagination-number"><?php echo esc_html( $i ); ?></div>
                                </a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <!-- Flecha Next -->
                        <?php if ( $paged < $total_pages ): ?>
                            <a href="<?php echo esc_url( add_query_arg( 'related_orders_page', $paged + 1 ) ); ?>"
                               class="pagination-arrow next-active">
<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
								<mask id="mask0_12964_3193" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="-1" y="0"
									width="21" height="20">
									<rect x="-0.416504" width="20" height="20" fill="#D9D9D9" />
								</mask>
								<g mask="url(#mask0_12964_3193)">
									<path d="M10.0833 10L6.25 6.16667L7.41667 5L12.4167 10L7.41667 15L6.25 13.8333L10.0833 10Z"
										fill="white" />
								</g>
							</svg>                            </a>
                        <?php else: ?>
                            <div class="pagination-arrow next-disable">
<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
								<mask id="mask0_12964_3193" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="-1" y="0"
									width="21" height="20">
									<rect x="-0.416504" width="20" height="20" fill="#D9D9D9" />
								</mask>
								<g mask="url(#mask0_12964_3193)">
									<path d="M10.0833 10L6.25 6.16667L7.41667 5L12.4167 10L7.41667 15L6.25 13.8333L10.0833 10Z"
										fill="white" />
								</g>
							</svg>                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
<?php else: ?>
    <p class="text-muted"><?php esc_html_e( 'No related orders found for this subscription.', 'woocommerce' ); ?></p>
<?php endif; ?>
