<?php
/**
 * Orders
 *
 * Shows orders on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/orders.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.2.0
 */

defined('ABSPATH') || exit;

/**
 * Fallback: asegurar $has_orders si no viene desde el hook de WooCommerce.
 */
if (!isset($has_orders)) {
    $user_id = get_current_user_id();
    if ($user_id && function_exists('wc_get_orders')) {
        $has_orders = (bool) wc_get_orders([
            'customer_id' => $user_id,
            'limit' => 1,
            'return' => 'ids',
        ]);
    } else {
        $has_orders = false;
    }
}

/**
 * Redirección: si NO hay órdenes, enviar a /subscriptions/
 * (antes de cualquier salida para permitir wp_safe_redirect)
 */
if (!$has_orders) {
    $dest = home_url('/subscriptions/');
    if (!headers_sent()) {
        wp_safe_redirect($dest);
        exit;
    }
    // Fallback JS si ya hubo salida
    echo '<script>window.location.replace(' . wp_json_encode(esc_url_raw($dest)) . ');</script>';
    return;
}

do_action('woocommerce_before_account_orders', $has_orders);

?>

<?php if ($has_orders): ?>

    <div class="order-table-wrap">
        <table
            class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
            <thead>
                <tr>
                    <?php foreach (wc_get_account_orders_columns() as $column_id => $column_name): ?>
                        <th scope="col"
                            class="woocommerce-orders-table__header woocommerce-orders-table__header-<?php echo esc_attr($column_id); ?>">
                            <span class="nobr"><?php echo esc_html($column_name); ?></span>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>

            <tbody>
                <?php
                foreach ($customer_orders->orders as $customer_order) {
                    $order = wc_get_order($customer_order); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                    $item_count = $order->get_item_count() - $order->get_item_count_refunded();
                    ?>
                    <tr
                        class="woocommerce-orders-table__row woocommerce-orders-table__row--status-<?php echo esc_attr($order->get_status()); ?> order">
                        <?php foreach (wc_get_account_orders_columns() as $column_id => $column_name):
                            $is_order_number = 'order-number' === $column_id;
                            ?>
                            <?php if ($is_order_number): ?>
                                <th class="woocommerce-orders-table__cell woocommerce-orders-table__cell-<?php echo esc_attr($column_id); ?>"
                                    data-title="<?php echo esc_attr($column_name); ?>" scope="row">
                                <?php else: ?>
                                <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-<?php echo esc_attr($column_id); ?>"
                                    data-title="<?php echo esc_attr($column_name); ?>">
                                <?php endif; ?>

                                <?php if (has_action('woocommerce_my_account_my_orders_column_' . $column_id)): ?>
                                    <?php do_action('woocommerce_my_account_my_orders_column_' . $column_id, $order); ?>

                                <?php elseif ($is_order_number): ?>
                                    <?php /* translators: %s: the order number, usually accompanied by a leading # */ ?>
                                    <a href="<?php echo esc_url($order->get_view_order_url()); ?>"
                                        aria-label="<?php echo esc_attr(sprintf(__('View order number %s', 'woocommerce'), $order->get_order_number())); ?>">
                                        <?php echo esc_html(_x('#', 'hash before order number', 'woocommerce') . $order->get_order_number()); ?>
                                    </a>

                                <?php elseif ('order-date' === $column_id): ?>
                                    <time
                                        datetime="<?php echo esc_attr($order->get_date_created()->date('c')); ?>"><?php echo esc_html(date_i18n('M j, Y', strtotime($order->get_date_created()))); ?></time>

                                <?php elseif ('order-total' === $column_id): ?>
                                    <?php
                                    /* translators: 1: formatted order total 2: total order items */
                                    echo wp_kses_post(sprintf(_n('%1$s for %2$s item', '%1$s for %2$s items', $item_count, 'woocommerce'), $order->get_formatted_order_total(), $item_count));
                                    ?>

                                <?php elseif ('order-status' === $column_id): ?>
                                    <span><?php echo esc_html(wc_get_order_status_name($order->get_status())); ?></span>

                                <?php elseif ('order-actions' === $column_id): ?>
                                    <?php
                                    $actions = wc_get_account_orders_actions($order);

                                    if (!empty($actions)) {
                                        foreach ($actions as $key => $action) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                                            echo '<a href="' . esc_url($action['url']) . '" class="woocommerce-button' . esc_attr($wp_button_class) . ' button ' . sanitize_html_class($key) . '">' . esc_html($action['name']) . '</a>';
                                        }
                                    }
                                    ?>
                                <?php endif; ?>

                                <?php if ($is_order_number): ?>
                                    </th>
                                <?php else: ?>
                                </td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>

    <?php do_action('woocommerce_before_account_orders_pagination'); ?>

    <?php if (1 < $customer_orders->max_num_pages): ?>
        <div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">
            <?php if (1 !== $current_page): ?>
                <a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button<?php echo esc_attr($wp_button_class); ?>"
                    href="<?php echo esc_url(wc_get_endpoint_url('orders', $current_page - 1)); ?>"><?php esc_html_e('Previous', 'woocommerce'); ?></a>
            <?php endif; ?>

            <?php if (intval($customer_orders->max_num_pages) !== $current_page): ?>
                <a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button<?php echo esc_attr($wp_button_class); ?>"
                    href="<?php echo esc_url(wc_get_endpoint_url('orders', $current_page + 1)); ?>"><?php esc_html_e('Next', 'woocommerce'); ?></a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

<?php else: ?>
    <div class="container p-0">
        <div class="no-order-wrapper d-flex flex-column gap-32">

            <div class="align-items-center bg-1e1e1e d-flex gap-3 overflow-hidden p-3 rounded-16px">
                <div class="d-flex flex-column flex-grow-1 flex-shrink-1 justify-content-center">
                    <div class="fw-medium text-size-20 text-uppercase text-white">
                        <?php esc_html_e('No active membership', 'woocommerce'); ?>
                    </div>
                    <div class="fw-medium text-14px text-a8a29e">
                        <?php esc_html_e('You don’t have any active plans or memberships at the moment.', 'woocommerce'); ?>
                    </div>
                </div>
                <div class="get-started">
                    <?php
                    $shop_url = home_url('/');
                    ?>
                    <a id="get_started_btn" href="<?php echo esc_url($shop_url); ?>"
                        class="btn w-100 mega-btn-md mega-btn-primary-md">
                        <?php esc_html_e('Get Started123', 'woocommerce'); ?>
                    </a>

                </div>
            </div>

            <div class="align-items-center bg-1e1e1e d-flex flex-column gap-32 overflow-hidden p-3 rounded-16px">
                <div class="text-white text-size-20 fw-medium text-uppercase align-self-start">
                    <?php esc_html_e('Get started in 3 steps', 'woocommerce'); ?>
                </div>
                <div class="d-flex align-items-center flex-column w-100 mt-2">
                    <div class="no-order stepper position-relative w-100">
                        <div class="stepper-line"></div>
                        <div class="stepper-numbers-wrapper">
                            <div class="stepper-numbers-circle">
                                <div class="stepper-numbers-text">
                                    <?php esc_html_e('1', 'woocommerce'); ?>
                                </div>
                            </div>
                            <div class="stepper-numbers-circle">
                                <div class="stepper-numbers-text">
                                    <?php esc_html_e('2', 'woocommerce'); ?>
                                </div>

                            </div>
                            <div class="stepper-numbers-circle">
                                <div class="stepper-numbers-text">
                                    <?php esc_html_e('3', 'woocommerce'); ?>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="d-flex gap-32 justify-content-between w-100">
                        <div class="d-flex flex-column gap-1 flex-grow-1 flex-shrink-1 w-max-224px">
                            <div class="text-primary text-center text-base fw-medium">
                                <?php esc_html_e('Choose your plan', 'woocommerce'); ?>
                            </div>
                            <div class="text-center fw-medium text-14px text-a8a29e">
                                <?php esc_html_e('Select from our range of trading challenges', 'woocommerce'); ?>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-1 flex-grow-1 flex-shrink-1 w-max-224px">
                            <div class="text-primary text-center text-base fw-medium">
                                <?php esc_html_e('Complete purchase', 'woocommerce'); ?>
                            </div>
                            <div class="text-center fw-medium text-14px text-a8a29e">
                                <?php esc_html_e('Secure payment and instant activation', 'woocommerce'); ?>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-1 flex-grow-1 flex-shrink-1 w-max-224px">
                            <div class="text-primary text-center text-base fw-medium">
                                <?php esc_html_e('Start trading', 'woocommerce'); ?>
                            </div>
                            <div class="text-center fw-medium text-14px text-a8a29e">
                                <?php esc_html_e('Access your platform and begin your challenge', 'woocommerce'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column flex-md-row gap-3 pb-35 align-items-start">
                <div class="bg-1e1e1e d-flex flex-column gap-3 overflow-hidden p-3 rounded-16px w-100">
                    <div class="text-white fw-medium text-size-20 text-uppercase">
                        <?php esc_html_e('Why join us?', 'woocommerce'); ?>
                    </div>
                    <div class="d-flex flex-column">
                        <div class="py-3 d-flex gap-2 align-items-center border-bottom-dark">
                            <div class="svg-check">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none">
                                    <mask id="mask0_12164_24055" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0"
                                        y="0" width="24" height="24">
                                        <rect width="24" height="24" fill="#D9D9D9" />
                                    </mask>
                                    <g mask="url(#mask0_12164_24055)">
                                        <path
                                            d="M9.54961 17.9996L3.84961 12.2996L5.27461 10.8746L9.54961 15.1496L18.7246 5.97461L20.1496 7.39961L9.54961 17.9996Z"
                                            fill="#A8A29E" />
                                    </g>
                                </svg>
                            </div>
                            <div class="text-a8a29e text-base fw-medium">
                                <?php esc_html_e('Professional trading environment', 'woocommerce'); ?>
                            </div>
                        </div>
                        <div class="py-3 d-flex gap-2 align-items-center border-bottom-dark">
                            <div class="svg-check">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none">
                                    <mask id="mask0_12164_24055" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0"
                                        y="0" width="24" height="24">
                                        <rect width="24" height="24" fill="#D9D9D9" />
                                    </mask>
                                    <g mask="url(#mask0_12164_24055)">
                                        <path
                                            d="M9.54961 17.9996L3.84961 12.2996L5.27461 10.8746L9.54961 15.1496L18.7246 5.97461L20.1496 7.39961L9.54961 17.9996Z"
                                            fill="#A8A29E" />
                                    </g>
                                </svg>
                            </div>
                            <div class="text-a8a29e text-base fw-medium">
                                <?php esc_html_e('Real-time performance tracking', 'woocommerce'); ?>
                            </div>
                        </div>
                        <div class="py-3 d-flex gap-2 align-items-center border-bottom-dark">
                            <div class="svg-check">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none">
                                    <mask id="mask0_12164_24055" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0"
                                        y="0" width="24" height="24">
                                        <rect width="24" height="24" fill="#D9D9D9" />
                                    </mask>
                                    <g mask="url(#mask0_12164_24055)">
                                        <path
                                            d="M9.54961 17.9996L3.84961 12.2996L5.27461 10.8746L9.54961 15.1496L18.7246 5.97461L20.1496 7.39961L9.54961 17.9996Z"
                                            fill="#A8A29E" />
                                    </g>
                                </svg>
                            </div>
                            <div class="text-a8a29e text-base fw-medium">
                                <?php esc_html_e('Expert support and guidance', 'woocommerce'); ?>
                            </div>
                        </div>
                        <div class="py-3 d-flex gap-2 align-items-center">
                            <div class="svg-check">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none">
                                    <mask id="mask0_12164_24055" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0"
                                        y="0" width="24" height="24">
                                        <rect width="24" height="24" fill="#D9D9D9" />
                                    </mask>
                                    <g mask="url(#mask0_12164_24055)">
                                        <path
                                            d="M9.54961 17.9996L3.84961 12.2996L5.27461 10.8746L9.54961 15.1496L18.7246 5.97461L20.1496 7.39961L9.54961 17.9996Z"
                                            fill="#A8A29E" />
                                    </g>
                                </svg>
                            </div>
                            <div class="text-a8a29e text-base fw-medium">
                                <?php esc_html_e('Flexible challenge options', 'woocommerce'); ?>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="bg-1e1e1e d-flex flex-column gap-3 overflow-hidden p-3 rounded-16px w-100">
                    <div class="text-white fw-medium text-size-20 text-uppercase">
                        <?php esc_html_e('Frequently Asked Question', 'woocommerce'); ?>
                    </div>
                    <div class="d-flex flex-column">
                        <div class="py-3 border-bottom-dark d-flex flex-column gap-1">
                            <div class="text-white text-base fw-medium">
                                <?php esc_html_e('How do I get started?', 'woocommerce'); ?>
                            </div>
                            <div class="text-14px text-a8a29e fw-medium">
                                <?php esc_html_e('Simply choose a plan that fits your trading experience and goals, complete the purchase, and
                                you’ll get instant access to your trading challenge.', 'woocommerce'); ?>
                            </div>
                        </div>
                        <div class="py-3 border-bottom-dark d-flex flex-column gap-1">
                            <div class="text-white text-base fw-medium">
                                <?php esc_html_e('What’s included in each plan?', 'woocommerce'); ?>
                            </div>
                            <div class="text-14px text-a8a29e fw-medium">
                                <?php esc_html_e('Each plan includes access to our trading platform, real-time performance tracking, support resources and specific challenge objectives based on your chosen tier.', 'woocommerce'); ?>
                            </div>
                        </div>
                        <div class="py-3 d-flex flex-column gap-1">
                            <div class="text-white text-base fw-medium">
                                <?php esc_html_e('Can I upgrade my plan later?', 'woocommerce'); ?>
                            </div>
                            <div class="text-14px text-a8a29e fw-medium">
                                <?php esc_html_e('Yes, you can upgrade to a higher tier plan at any time. Contact our support team for assistance with plan changes.', 'woocommerce'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>

<?php do_action('woocommerce_after_account_orders', $has_orders); ?>