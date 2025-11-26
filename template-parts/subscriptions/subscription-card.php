<?php
/**
 * Subscriptions Card
 *
 * @category Megatrader TemplateParts/Subscriptions
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$subscription = $args['subscription'] ?? null;
$mt_id = $args['mt_id'] ?? null;
$accordion_id = esc_attr($args['accordion_id'] ?? '');
$status = $args['subscription_status'] ?? '';
$subscription_id = $args['subscription_id'] ?? '';
$details_list = $args['details_list'] ?? [];
$title = $args['title'] ?? [];
$related_orders = $args['related_orders'] ?? [];

$collapse_id = esc_attr('collapse-' . $subscription_id);

$status_classes = [
	'active' => 'badge-mega-active',
	'on-hold' => 'badge-mega-hold',
	'pending' => 'badge-mega-pending',
	'expired' => 'badge-mega-expired',
	'cancelled' => 'badge-mega-cancelled',
	'pending-cancel' => 'badge-mega-pending-cancel',
	'completed' => 'badge-mega-active',
	'refunded' => 'badge-mega-active',
	'failed' => 'badge-mega-cancelled',
];
$status_badge_class = $status_classes[$status] ?? 'badge-mega-default';

?>

<style>

.mt-order-collapse__trigger .mt-order-collapse__trigger__icon {
    transition: transform 160ms ease;
}

.mt-order-collapse__trigger[aria-expanded="true"] .mt-order-collapse__trigger__icon {
    transform: rotate(180deg);
}

</style>

<div class="mt-subscription-card">
    <div class="mt-subscription-card__wrapper mt-card gap-3">
        <div class="mt-subscription-card__section d-flex flex-column gap-3 border-bottom-gray pb-3">
            <div class="mt-subscription-card__block d-flex flex-column flex-md-row gap-3">

                <!-- Platform Logo + Size + Plan -->
                <div class="d-flex gap-3 align-items-center flex-fill">
                    <img decoding="async" src="<?= $title['platform_logo']?>" alt="<?= $title['platform_name']?>"
                         title="<?= $title['platform_name']?>"
                         width="48" height="48"
                         class="bg-black rounded-circle overflow-hidden d-flex align-items-center">
                    <div class="">
                        <div class="text-white text-size-20 fw-medium text-uppercase"><?= $title['product_size']['slug'] . ' ' . $title['product_name']?></div>
                        <div class="text-a8a29e small fw-medium"><?= esc_html($mt_id)?></div>
                    </div>

    			</div>

                <!-- Progress -->
                <?php if ( ! in_array($status, ['cancelled', 'expired', 'on-hold'], true)):
                	// 3. Dates and days
                    $status = $subscription->get_status();
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
                ?>
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
                                    <!-- <div class="text-white fs-6 fw-medium">Days until payment</div> -->
                                    <div class="text-a8a29e small fw-medium">
                                        Day <?php echo esc_html($current_day); ?> of
                                        <?php echo esc_html($total_days); ?> <!-- in billing cycle -->
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>


                <!-- Badge + Menu Dots -->
                <div role="button" class="d-flex gap-3 justify-content-between order-first order-md-0">
                    <span class="badge-mega badge-mega-sm <?php echo esc_attr($status_badge_class); ?>">
                        <?php
                            if ($status === 'pending-cancel') {
                                echo esc_html__('Pending Cancellation', 'woocommerce');
                            } else {
                                echo esc_html(ucwords(str_replace('-', ' ', $status)));
                            }
                        ?>
                    </span>
                    <div class="dropdown">
                        <i  class="mt-icon mt-icon-white mt-icon_menu-dots dropdown-toggle" 
                            role="button" 
                            title="Menu"
                            aria-expanded="false"
                            data-bs-toggle="dropdown"></i>
                        <ul class="dropdown-menu">
                            <li><button class="dropdown-item" type="button">Action</button></li>
                            <li><button class="dropdown-item" type="button">Another action</button></li>
                            <li><button class="dropdown-item" type="button">Something else here</button></li>
                                           <!-- Menu Items -->
                            <?php do_action('woocommerce_subscription_before_actions', $subscription); ?>

                            <?php if ('on-hold' === $status):
                                $related_orders = $subscription->get_related_orders('renewal');
                                foreach ($related_orders as $order_id => $order_type) {
                                    $order = wc_get_order($order_id);
                                    if ($order && $order->has_status(['pending', 'failed'])) {
                                        $pay_url = $order->get_checkout_payment_url();
                                        ?>
                                        <a class="btn w-100 mega-btn-md mega-btn-primary-md" href="<?= esc_url($pay_url); ?>">
                                            Complete Payment
                                        </a>
                                        <?php
                                        break;
                                    }
                                }
                                ?>
                            <?php elseif ('pending-cancel' === $status && $subscription->get_time('end') > current_time('timestamp')):
                                $reactivate_url = add_query_arg(
                                    [
                                        'wcs_reactivate_subscription' => $subscription_id,
                                        '_wpnonce' => wp_create_nonce('wcs_reactivate_subscription_' . $subscription_id),
                                    ],
                                    wc_get_endpoint_url('view-subscription', $subscription_id, wc_get_page_permalink('myaccount'))
                                );
                                ?>
                                <a  class="dropdown-item wcs_reactivate_subscription"
                                    href="<?= esc_url($reactivate_url); ?>">
                                    Reactivate
                                </a>
                                <a  class="dropdown-item change_payment_method disabled"
                                    href="#" 
                                    aria-disabled="true">
                                    Change Payment
                                </a>
                                <a  class="dropdown-item cancel disabled"
                                    href="#" 
                                    aria-disabled="true">
                                    Cancel
                                </a>
                            <?php elseif (in_array($status, ['cancelled', 'expired'], true)): ?>
                                <a  class="btn w-100 mega-btn-md mega-btn-secondary-md change_payment_method disabled"
                                    href="#" 
                                    aria-disabled="true">
                                    Change Payment
                                </a>
                                <a  class="btn w-100 mega-btn-md mega-btn-secondary-md cancel disabled"
                                    href="#"
                                    aria-disabled="true">
                                    Cancel
                                </a>
                            <?php else:
                                $actions = wcs_get_all_user_actions_for_subscription($subscription, get_current_user_id());
                                $desired_order = ['subscription_renewal_early', 'change_payment_method', 'cancel'];
                                $sorted_actions = [];
                                foreach ($desired_order as $key) {
                                    if (isset($actions[$key])) {
                                        $sorted_actions[$key] = $actions[$key];
                                    }
                                }

                                foreach ($sorted_actions as $key => $action):
                                    $classes = ['mega-btn-md', 'mega-btn-secondary-md', 'w-100', sanitize_html_class($key)];
                                    if (!empty($action['block_ui'])) {
                                        $classes[] = 'wcs_block_ui_on_click';
                                    }
                                    ?>
                                    <?php if ($key === 'change_payment_method' && false /* change_payment_modal_should_show()*/): ?>
                                        <a  class="<?= esc_attr(implode(' ', $classes)); ?>"
                                            href="#"
                                            data-bs-toggle="modal"
                                            data-bs-target="#<?= esc_attr($change_payment_modal_id); ?>">
                                                <?= esc_html($action['name']); ?>
                                        </a>
                                    <?php else: ?>
                                        <a  class="<?= esc_attr(implode(' ', $classes)); ?>"
                                            href="<?= esc_url($action['url']); ?>">
                                                <?= esc_html($action['name']); ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endforeach; ?>

                            <?php endif; ?>

                            <?php do_action('woocommerce_subscription_after_actions', $subscription); ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Summary List -->
            <?php if ( ! empty( $details_list ) ) : ?>
                <div class="mt-subscription-card__block row row-gap-3">
                    <?php foreach ( $details_list as $details_item ) : ?>
                        <div class="col-4">
                            <div class="fw-medium text-base text-white"><?= esc_html($details_item['label']) ?></div>
                            <div class="fw-medium text-sm text-a8a29e <?= $details_item['value-class'] ?? '' ?>">
                                <?= esc_html($details_item['value'])?></div>
                            </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="mt-subscription-card__section d-flex flex-column gap-3">
            
            <!-- Auto Renew Toggle -->

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

            <div class="d-flex align-items-center gap-2">
				<span class="text-a8a29e text-sm fw-medium">Auto renew</span>
				<div class="wcs-auto-renew-toggle">
					<a href="#" class="subscription-auto-renew-toggle subscription-auto-renew-toggle--off" aria-label="Enable auto renew">
						<i class="subscription-auto-renew-toggle__i" aria-hidden="true"></i>
					</a>
				</div>
			</div>

            <?php if ( ! empty( $related_orders ) ) : ?>
                <!-- Related Orders Collapse -->
                <div class="mt-order-collapse mt-card mt-card-dark gap-0 p-0">
                    <a class="mt-order-collapse__trigger d-flex gap-2 justify-content-between p-12" data-bs-toggle="collapse" href="#<?= $collapse_id ?>" role="button" aria-expanded="false" aria-controls="<?= $collapse_id ?>">
                        <div class="mt-order-collapse__trigger__title d-flex gap-2">
                            <div class="fw-medium text-sm text-white">View Transactions</div>
                            <div class="fw-medium text-sm text-a8a29e">(<?= count($related_orders)?>)</div>
                        </div>
                        <i class="mt-order-collapse__trigger__icon mt-icon mt-icon-white mt-icon_caret-down-solid"></i>
                    </a>
                    <div class="mt-order-collapse__content collapse overflow-x-auto" id="<?= $collapse_id ?>" data-bs-parent="#<?= $accordion_id ?>">
                        <table class="mt-order-collapse__table border-0 m-0">
                            <thead>
                                <tr class="border-top-gray-800">
                                    <th class="fw-medium text-sm text-white border-0">Date</th>
                                    <th class="fw-medium text-sm text-white border-0">Transaction ID</th>
                                    <th class="fw-medium text-sm text-white border-0">Amount</th>
                                    <th class="fw-medium text-sm text-white border-0 text-end">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $related_orders as $order ) : 
                                    $order_status_badge_class = $status_classes[$order['status_slug']] ?? 'badge-mega-default';
                                ?>
                                    <tr class="border-top-gray-800">
                                        <td class="fw-medium text-sm text-a8a29e border-0"><?= $order['date'] ?></td>
                                        <td class="fw-medium text-sm text-a8a29e border-0"><?= $order['transaction_id'] ?></td>
                                        <td class="fw-medium text-sm text-a8a29e border-0 text-primary"><?= $order['amount'] ?></td>
                                        <td class="fw-medium text-sm text-a8a29e border-0 text-end">
                                            <span class="badge-mega badge-mega-sm <?= esc_attr($order_status_badge_class); ?> d-inline"><?= $order['status'] ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>