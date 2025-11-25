<?php
/**
 * Subscriptions Card
 *
 * @author   leoraez
 * @category Megatrader TemplateParts/Subscriptions
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$mt_id = $args['mt_id'] ?? null;
$accordion_id = esc_attr($args['accordion_id'] ?? '');
$subscription_id = $args['subscription_id'] ?? '';
$details_list = $args['details_list'] ?? [];
$related_orders = $args['related_orders'] ?? [];

$collapse_id = esc_attr('collapse-' . $subscription_id);
?>

<div class="mt-subscription-card">
    <div class="mt-subscription-card__wrapper mt-card gap-3">
        <div class="mt-subscription-card__section d-flex flex-column gap-3 border-bottom-gray pb-3">
            <div class="mt-subscription-card__block d-flex flex-column flex-md-row gap-3">

                <!-- Platform Logo + Size + Plan -->
                <div class="d-flex gap-3 align-items-center flex-fill">
                    <img decoding="async" src="https://test.megatrader.io/wp-content/uploads/2025/07/Stylecolor-Sizelg.svg" alt="MegaTraderX" width="48" height="48" style="border-radius: 999px; background: black;">
                    <div class="">
                        <div class="text-white text-size-20 fw-medium text-uppercase">MegaTraderX</div>
                        <div class="text-a8a29e small fw-medium"><?= esc_html($mt_id)?></div>
                    </div>

    			</div>

                <!-- Progress -->
                <div class="d-flex gap-2 align-items-center flex-fill">
                    <div class="d-inline-flex align-items-center gap-2 w-100 h-100">
                        <div class="progress-circle position-relative">
                            <svg width="48" height="48" viewBox="0 0 36 36" class="position-absolute top-0 start-0 rotate-svg">
                                <circle cx="18" cy="18" r="16" stroke="#404040" stroke-width="4" fill="none"></circle>
                                <circle cx="18" cy="18" r="16" stroke="#2DD4BF" stroke-width="4" fill="none" stroke-linecap="round" stroke-dasharray="100" stroke-dashoffset="90"></circle>
                            </svg>
                            <div class="d-flex justify-content-center align-items-center w-100 h-100 position-absolute top-0 start-0">
                                <span class="circle-label">28</span>
                            </div>
                        </div>
                        <div class="d-flex flex-column justify-content-center align-items-start flex-grow-1">
                            <div class="text-white fs-6 fw-medium">Days until payment</div>
                            <div class="text-a8a29e small fw-medium">Day 3 of 30 in billing cycle </div>
                        </div>
                    </div>
                </div>

                <!-- Badge + Menu Dots -->
                <div role="button" class="d-flex gap-3 justify-content-between order-first order-md-0">
                    <div class="badge-mega badge-mega-sm badge-mega-active">Active</div>
                    <div><i class="mt-icon mt-icon-white mt-icon_menu-dots" aria-hidden="true"></i></div>
                </div>
            </div>

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
                <div class="mt-card mt-card-dark gap-0 p-0">
                    <a class="d-flex gap-2 justify-content-between p-12" data-bs-toggle="collapse" href="#<?= $collapse_id ?>" role="button" aria-expanded="false" aria-controls="<?= $collapse_id ?>">
                        <div class="d-flex gap-2">
                            <div class="fw-medium text-base text-white">View Transactions</div>
                            <div class="fw-medium text-sm text-a8a29e">(3)</div>
                        </div>
                        <i class="mt-details__summary__icon mt-icon mt-icon-white mt-icon_caret-down-solid"></i>
                    </a>
                    <div class="collapse" id="<?= $collapse_id ?>" data-bs-parent="#<?= $accordion_id ?>">
                        <table class="border-0 m-0">
                            <thead>
                                <tr class="border-top-gray">
                                    <th class="border-0">Date</th>
                                    <th class="border-0">Transaction ID</th>
                                    <th class="border-0">Amount</th>
                                    <th class="border-0 text-end">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $related_orders as $order ) : ?>
                                    <tr class="border-top-gray">
                                        <td class="fw-medium text-sm text-a8a29e border-0"><?= $order['date'] ?></td>
                                        <td class="fw-medium text-sm text-a8a29e border-0"><?= $order['transaction_id'] ?></td>
                                        <td class="fw-medium text-sm text-a8a29e border-0 text-primary"><?= $order['amount'] ?></td>
                                        <td class="fw-medium text-sm text-a8a29e border-0 text-end">
                                            <span class="badge-mega badge-mega-sm badge-mega-active d-inline"><?= $order['status'] ?></span>
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