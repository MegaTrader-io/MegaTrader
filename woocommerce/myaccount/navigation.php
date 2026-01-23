<?php
/**
 * My Account navigation
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.3.0
 */

if (!defined('ABSPATH')) {
	exit;
}

global $wp;

$current_url_path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$selected_data_id = ''; 

if (preg_match('#my-account/view-subscription/(\d+)#', $current_url_path, $matches)) {
	$selected_data_id = 'sub-' . $matches[1];
} elseif (preg_match('#my-account/view-order/(\d+)#', $current_url_path, $matches)) {
	$selected_data_id = 'order-' . $matches[1];
}

do_action('woocommerce_before_account_navigation');

$user_id = get_current_user_id();
$custom_url = home_url('/select-plan/');

if ($user_id) {
	$all_orders = wc_get_orders([
		'customer_id' => $user_id,
		'orderby' => 'date',
		'order' => 'DESC',
		'limit' => -1,
		'return' => 'objects',
	]);

	$latest = wc_get_orders([
        'customer_id' => $user_id,
        'limit'       => 1,
        'orderby'     => 'date',
        'order'       => 'DESC',
        'return'      => 'ids',
    ]);
    if (!empty($latest)) {
        $order_id = $latest[0];

        if (function_exists('wcs_get_subscriptions_for_order')) {
            $subs = wcs_get_subscriptions_for_order($order_id, ['order_type' => 'any']);
            if (!empty($subs)) {
                $sub        = reset($subs);
                $custom_url = wc_get_endpoint_url('view-subscription', $sub->get_id());
            } else {
                $custom_url = wc_get_endpoint_url('view-order', $order_id);
            }
        } else {
            $custom_url = wc_get_endpoint_url('view-order', $order_id);
        }
    }

	

	$filtered_orders = [];           
	$subscription_order_ids = [];   

	foreach ($all_orders as $order) {
		if (function_exists('wcs_get_subscriptions_for_order')) {
			$related_subs = wcs_get_subscriptions_for_order($order->get_id(), ['order_type' => 'any']);

			if (!empty($related_subs)) {
				// La orden tiene una suscripción relacionada, guardamos la orden hija
				foreach ($related_subs as $sub) {
					$sub_order_id = $sub->get_id();
					$subscription_order_ids[] = $sub_order_id;
					$filtered_orders[$sub_order_id] = $sub; // Guardamos la orden hija para mostrar
				}
			} else {
				// No tiene suscripción relacionada, guardamos la orden padre sólo si no está ya filtrada
				if (!in_array($order->get_id(), $subscription_order_ids)) {
					$filtered_orders[$order->get_id()] = $order;
				}
			}
		} else {
			// Si no existe función de suscripción, guardamos todas las órdenes
			$filtered_orders[$order->get_id()] = $order;
		}
	}

}

$current_endpoint = WC()->query->get_current_endpoint();
$is_manage_subscription_active = in_array($current_endpoint, ['view-order', 'view-subscription'], true);

$customer_subscriptions = wcs_get_users_subscriptions($user_id);

$all_size_terms = get_terms([
	'taxonomy' => 'pa_account-size',
	'hide_empty' => false,
]);

$all_items = [];

foreach ($customer_subscriptions as $sub_modal) {
	$product_modal = current($sub_modal->get_items())->get_product();
	$product_name_modal = $product_modal ? $product_modal->get_name() : '';
	$product_size_modal = $product_modal ? $product_modal->get_attribute('pa_account-size') : '';

	$size_slug_modal = '';
	$matched_term_modal = array_values(array_filter($all_size_terms, function ($t) use ($product_size_modal) {
		return $t->name === $product_size_modal;
	}));
	if (!empty($matched_term_modal)) {
		$size_slug_modal = $matched_term_modal[0]->slug;
	}

	$subscription_id_modal = $sub_modal->get_id();
	$platform_value_modal = $product_modal ? $product_modal->get_attribute('pa_platform') : '';

	$platform_term_modal = get_term_by('slug', $platform_value_modal, 'pa_platform');
	$platform_meta_modal = $platform_term_modal ? get_term_meta($platform_term_modal->term_id) : [];
	$image_id_modal = $platform_meta_modal['attribute_image_id'][0] ?? null;
	$image_url_modal = $image_id_modal ? wp_get_attachment_url($image_id_modal) : null;

	$subscription_status_modal = $sub_modal->get_status();

	$date_obj = $sub_modal->get_date('start');
	if (is_string($date_obj)) {
		try {
			$date_obj = new DateTime($date_obj);
		} catch (Exception $e) {
			$date_obj = null;
		}
	}
	$timestamp = $date_obj ? $date_obj->getTimestamp() : 0;

	$all_items[] = [
		'type' => 'subscription',
		'id' => $subscription_id_modal,
		'date' => $timestamp,
		'name' => $product_name_modal,
		'size_slug' => $size_slug_modal,
		'logos' => $image_url_modal ? [$image_url_modal] : [],
		'status' => $subscription_status_modal,
		'platform_value' => $platform_value_modal,
		'object' => $sub_modal,
	];
}

$subscription_ids = array_map(fn($sub) => $sub->get_id(), $customer_subscriptions);

$grouped_orders = [];

$fee_slugs    = [ 'reset-fee', 'activation-fee' ];
$fee_cat_names = [ 'Reset Fee', 'Activation Fee' ];

foreach ( $filtered_orders as $order ) {
    // 1) Si la orden es una suscripción, la ignoramos aquí
    if ( in_array( $order->get_id(), $subscription_ids, true ) ) {
        continue;
    }

    // 2) Detectar cualquier ítem de tipo reset-fee o activation-fee
    $skip_order = false;
    foreach ( $order->get_items() as $item_check ) {
        $prod_check = $item_check->get_product();
        if ( ! $prod_check ) {
            continue;
        }
        $slug = $prod_check->get_slug();
        if ( in_array( $slug, $fee_slugs, true ) ) {
            $skip_order = true;
            break;
        }
        // Por si la “categoría” del producto se llama literal “Reset Fee” en lugar de slug
        $cats = get_the_terms( $prod_check->get_id(), 'product_cat' );
        if ( $cats ) {
            foreach ( $cats as $cat ) {
                if ( in_array( $cat->name, $fee_cat_names, true ) ) {
                    $skip_order = true;
                    break 2;
                }
            }
        }
    }
    if ( $skip_order ) {
        continue; // omitimos la orden completa
    }

    // 3) Si llegamos acá, la orden NO tiene fees: la agrupamos como antes
    foreach ( $order->get_items() as $item ) {
        $product = $item->get_product();
        if ( ! $product ) {
            continue;
        }

        // Extraer nombre, tamaño y logo tal como ya lo hacías
        $product_name  = $product->get_name();
        $product_size  = $product->get_attribute( 'pa_account-size' );

        // Encontrar slug del tamaño
        $size_slug = '';
        foreach ( $all_size_terms as $t ) {
            if ( $t->name === $product_size ) {
                $size_slug = $t->slug;
                break;
            }
        }

        // Logo de la plataforma
        $platform_value = $product->get_attribute( 'pa_platform' );
        $term_platform  = get_term_by( 'slug', $platform_value, 'pa_platform' );
        $meta_platform  = $term_platform ? get_term_meta( $term_platform->term_id ) : [];
        $logo_id        = $meta_platform['attribute_image_id'][0] ?? null;
        $logo_url       = $logo_id ? wp_get_attachment_url( $logo_id ) : '';

        $order_id = $order->get_id();
        $key      = $size_slug . '|' . $product_name;

        if ( ! isset( $grouped_orders[ $key ] ) ) {
            $grouped_orders[ $key ] = [
                'logos'          => [],
                'order_id'       => $order_id,
                'text'           => trim( $size_slug . ' ' . $product_name ),
                'platform_value' => $platform_value,
            ];
        }

        if ( $logo_url && ! in_array( $logo_url, $grouped_orders[ $key ]['logos'], true ) ) {
            $grouped_orders[ $key ]['logos'][] = $logo_url;
        }

        if ( $order_id > $grouped_orders[ $key ]['order_id'] ) {
            $grouped_orders[ $key ]['order_id'] = $order_id;
        }
    }
}

$grouped_orders_indexed = array_values($grouped_orders);

foreach ($grouped_orders_indexed as $group) {
	$order_obj = wc_get_order($group['order_id']);
	$date_obj = $order_obj ? $order_obj->get_date_created() : null;

	$timestamp = 0;
	if ($date_obj instanceof DateTime) {
		$timestamp = $date_obj->getTimestamp();
	} elseif (is_string($date_obj)) {
		try {
			$dt = new DateTime($date_obj);
			$timestamp = $dt->getTimestamp();
		} catch (Exception $e) {
			$timestamp = 0;
		}
	}

	$all_items[] = [
		'type' => 'order',
		'id' => $group['order_id'],
		'date' => $timestamp,
		'name' => $group['text'],
		'size_slug' => '', // Ya viene en text completo
		'logos' => $group['logos'],
		'platform_value' => $group['platform_value'],
		'object' => $order_obj,
	];
}

usort($all_items, function ($a, $b) {
	return $b['date'] <=> $a['date'];
});

if (empty($selected_data_id) && !empty($all_items)) {
    $first = $all_items[0];
    $selected_data_id = ($first['type'] === 'subscription' ? 'sub-' : 'order-') . $first['id'];
}

do_action('woocommerce_before_account_navigation');

?>
<?php account_navigation_render(); ?>`

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
						foreach ($all_items as $item) {
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
