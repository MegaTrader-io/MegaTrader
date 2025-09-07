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

        return sprintf($format, $currency_symbol, $formatted);
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