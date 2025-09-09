<?php

if (!function_exists('mt_validate_coupon_for_variation')) {
    function mt_validate_coupon_for_variation($product_id, $coupon_code): array
    {
        $cart = new WC_Cart();
        $cart->empty_cart();
        $cart->add_to_cart($product_id);

        $discounts = new WC_Discounts($cart);
        $coupon = new WC_Coupon($coupon_code);

        $is_valid = $discounts->is_coupon_valid($coupon);
        if ($is_valid instanceof WP_Error) {
            return [
                'valid' => false,
                'reason' => $is_valid->get_error_message(),
            ];
        }

        $original_total = 0;
        foreach ($discounts->get_items() as $item) {
            $original_total += wc_remove_number_precision_deep($item->price);
        }

        $discounts->apply_coupon($coupon);

        $discounts_by_coupon = $discounts->get_discounts_by_coupon();
        $discount_total = $discounts_by_coupon[$coupon_code] ?? 0;

        $discounted_total = $original_total - $discount_total;

        $cart->remove_coupon($coupon_code);
        $cart->empty_cart();

        return [
            'valid' => true,
            'coupon' => $coupon_code,
            'product_id' => $product_id,
            'original_total' => $original_total,
            'discount_total' => $discount_total,
            'final_total' => $discounted_total,
        ];
    }
}

if (!function_exists('mt_price_plain')) {
    function mt_price_plain($amount, $currency = '')
    {
        if (floor($amount) == $amount) {
            $formatted = number_format_i18n($amount, 0);
        } else {
            $decimals = wc_get_price_decimals();
            $formatted = number_format_i18n($amount, $decimals);
        }

        $currency_symbol = get_woocommerce_currency_symbol($currency ?: get_woocommerce_currency());

        $format = get_woocommerce_price_format();

        return trim(sprintf($format, $currency_symbol, $formatted));
    }
}

if (!function_exists('mt_get_best_coupon_for_variation')) {
    function mt_get_best_coupon_for_variation($product_id): array
    {
        $best_coupon = null;
        $best_discount = 0;
        $best_result = null;

        $args = [
            'posts_per_page' => -1,
            'post_type' => 'shop_coupon',
            'post_status' => 'publish',
        ];

        $coupons = get_posts($args);

        if (empty($coupons)) {
            return [
                'valid' => false,
                'message' => 'No coupons available',
            ];
        }

        foreach ($coupons as $coupon_post) {
            $coupon_code = $coupon_post->post_title;
            $result = mt_validate_coupon_for_variation($product_id, wc_format_coupon_code(wp_unslash($coupon_code)));

            if ($result['valid'] && $result['discount_total'] > $best_discount) {
                $best_coupon = $coupon_code;
                $best_discount = $result['discount_total'];
                $best_result = $result;
            }
        }

        if (!$best_coupon) {
            return [
                'valid' => false,
                'message' => 'No applicable coupons found for this product',
            ];
        }

        return $best_result;
    }
}


/**
 * Get best selling variation of the current month
 *
 * @param int $product_id Optional. If set, filters by parent product_id
 * @return array|null      Returns array with variation_id and qty, or null if none found
 */
if (!function_exists('mt_get_best_selling_variation_this_month')) {
    function mt_get_best_selling_variation_this_month(?int $product_id = null): ?array
    {
        global $wpdb;

        try {
            $start_date = date('Y-m-01 00:00:00');
            $end_date = current_time('mysql'); // fecha actual según WP timezone

            $where_product = '';
            $params = [$start_date, $end_date, 'wc-completed'];

            if ($product_id !== null) {
                $where_product = "AND wpl.product_id = %d";
                $params[] = $product_id;
            }

            $sql = "
            SELECT
                wpl.variation_id,
                SUM(wpl.product_qty) AS qty
            FROM
                {$wpdb->prefix}wc_order_product_lookup wpl
                INNER JOIN {$wpdb->prefix}wc_orders wo ON wo.id = wpl.order_id
            WHERE
                wpl.date_created >= %s
                AND wpl.date_created <= %s
                AND wo.status = %s
                {$where_product}
            GROUP BY
                wpl.variation_id
            ORDER BY
                qty DESC
            LIMIT 1
        ";

            $query = $wpdb->prepare($sql, $params);

            $result = $wpdb->get_row($query, ARRAY_A);

            if (!$result) {
                return null;
            }

            return [
                'variation_id' => (int)$result['variation_id'],
                'qty' => (int)$result['qty'],
            ];
        } catch (Exception $e) {
            error_log("Error in get_best_selling_variation_this_month: " . $e->getMessage());
            return null;
        }
    }
}

if (!function_exists('mt_most_popular_products')) {
    function mt_most_popular_products(): array
    {
        $products_data = get_products_with_attributes();
        $product_ids = [];
        foreach ($products_data['products'] as $product) {
            if ($product['slug'] === 'reset-fee' || $product['slug'] === 'activation-fee') {
                continue;
            }

            $product_ids [] = $product['id'];
        }

        $best_products = [];
        foreach ($product_ids as $product_id) {
            $best_variation = mt_get_best_selling_variation_this_month($product_id);
            $best_products[$product_id] = $best_variation;
        }

        return $best_products;
    }
}