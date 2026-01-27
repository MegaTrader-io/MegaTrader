<?php
/**
 * Archivo: inc/mt-pricing-table-fragment.php
 * Propósito: Endpoint REST API que devuelve el fragmento HTML del pricing table filtrado por marketType.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('mt_render_pricing_table_fragment')) {
    /**
     * Renderiza el fragmento HTML del pricing table según el marketType recibido.
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    function mt_render_pricing_table_fragment(WP_REST_Request $request)
    {
        try {
            $marketTypeSlug = sanitize_text_field($request->get_param('marketType'));
            $accountTypeSlug = sanitize_text_field($request->get_param('accountType'));

            if (empty($marketTypeSlug)) {
                return new WP_REST_Response([
                    'success' => false,
                    'message' => 'Missing marketType parameter.'
                ], 400);
            }

            if (!function_exists('get_products_with_attributes')) {
                return new WP_REST_Response([
                    'success' => false,
                    'message' => 'Function get_products_with_attributes() not found.'
                ], 500);
            }

            /**
             * 1. Cargar data base de WooCommerce
             */
            $mt_products_data = get_products_with_attributes();
            $mt_attributes = $mt_products_data['attributes'] ?? [];
            $mt_products_raw = $mt_products_data['products'] ?? [];

            /**
             * 2. Clasificar atributos por taxonomía
             */
            $mt_account_sizes = [];
            $mt_account_types = [];
            $mt_platforms = [];
            $mt_market_types = [];
            $mt_billing_types = [];

            foreach ($mt_attributes as $attr) {
                switch ($attr['taxonomy']) {
                    case 'pa_market-type':
                        $mt_market_types[] = $attr;
                        break;
                    case 'pa_account-size':
                        $mt_account_sizes[] = $attr['slug'];
                        break;
                    case 'pa_account-types':
                        $mt_account_types[] = $attr;
                        break;
                    case 'pa_platform':
                        $mt_platforms[] = $attr;
                        break;
                }
            }

            $marketTypeFound = array_find($mt_market_types, function ($marketType) use ($marketTypeSlug) {
                return $marketType['slug'] === $marketTypeSlug;
            });

            if (!$marketTypeFound) {
                return new WP_REST_Response([
                    'success' => false,
                    'message' => 'Wrong marketType parameter.'
                ], 400);
            }

            /**
             * 3. Filtrar productos válidos (excluye -fee y market-type)
             */
            $mt_products = array_values(array_filter($mt_products_raw, function ($product) use ($marketTypeSlug, $accountTypeSlug) {
                $slug = $product['slug'] ?? '';
                if (str_ends_with($slug, '-fee')) {
                    return false;
                }

                if ($accountTypeSlug && isset($product['tree_map']) && $product['tree_map']['account-types'] != $accountTypeSlug) {
                    return false;
                }

                return isset($product['tree_map']['market-type']) &&
                    $product['tree_map']['market-type'] === $marketTypeSlug;
            }));

            if (empty($mt_products)) {
                return new WP_REST_Response([
                    'success' => false,
                    'message' => "No products found for marketType: {$marketTypeSlug}"
                ], 404);
            }

            /**
             * 4. Filtrar tamaños y tipos de cuenta permitidos (idéntico a pricing-table-bs.php)
             */
            $account_sizes_allowed = [];
            $account_types_allowed = [];

            $mt_filtered_products = array_values(array_filter($mt_products, function ($product) use (
                $marketTypeSlug, $mt_account_types, $mt_account_sizes,
                &$account_sizes_allowed, &$account_types_allowed
            ) {
                $marketType = $product['tree_map']['market-type'] ?? null;
                if ($marketType !== $marketTypeSlug) {
                    return false;
                }

                $slug = $product['slug'];
                $variants = $product[$slug] ?? [];

                foreach ($mt_account_sizes as $size) {
                    if (!isset($variants[$size])) {
                        continue;
                    }

                    if (!in_array($size, $account_sizes_allowed, true)) {
                        $account_sizes_allowed[] = $size;
                    }

                    foreach ($mt_account_types as $type) {
                        $type_slug = $type['slug'];
                        if (isset($variants[$size][$type_slug]) && !in_array($type_slug, $account_types_allowed, true)) {
                            $account_types_allowed[] = $type_slug;
                        }
                    }
                }

                return true;
            }));

            /**
             * 5. Definir account types y sizes finales
             */
            $mt_account_types = array_values(array_filter(
                $mt_account_types,
                fn($type) => in_array($type['slug'], $account_types_allowed, true)
            ));

            $mt_default_account_type = $mt_account_types[0] ?? [];
            $mt_default_slug = $mt_default_account_type['slug'] ?? '';
            $mt_account_thumbnail_url = $mt_default_account_type['thumbnail_url'] ?? '';
            $mt_account_sizes = $account_sizes_allowed;
            $mt_size = $mt_account_sizes[0] ?? '';

            /**
             * 7. Construcción de lista de planes (idéntico a pricing-table-bs.php)
             */
            if (!function_exists('mt_get_plan_list')) {
                function mt_get_plan_list(array $mt_product, array $mt_account_sizes): array
                {
                    $mt_plan_list = [];
                    $mt_default_meta_info = [];

                    if (!$mt_product) return [$mt_plan_list, $mt_default_meta_info];

                    $mt_slug = $mt_product['slug'] ?? null;
                    if (!$mt_slug || !isset($mt_product[$mt_slug])) {
                        return [$mt_plan_list, $mt_default_meta_info];
                    }

                    $mt_size = $mt_account_sizes[0] ?? null;
                    if (!$mt_size) {
                        return [$mt_plan_list, $mt_default_meta_info];
                    }

                    $mt_default_platform = $mt_product['tree_map']['platform'] ?? null;
                    $mt_default_market_type = $mt_product['tree_map']['market-type'] ?? null;
                    $parent_id = $mt_product['id'];

                    foreach ($mt_account_sizes as $size) {

                        $properties = [];
                        foreach (array_keys($mt_product['tree_map']) as $attr) {
                            $properties = count($properties) === 0 ? array_values($mt_product[$mt_slug])[0] : array_values($properties)[0];
                        }

                        if (count($properties) == 0) continue;

                        $variation_id = -1;
                        $price = '0.00';
                        $meta_info = [];

                        foreach ($properties as $property) {
                            foreach ($property as $key => $value) {
                                match ($key) {
                                    'id' => $variation_id = $value,
                                    'price-monthly' => $price = (int)str_replace('$', '', $value),
                                    'meta-info' => $meta_info = $value,
                                    default => null
                                };
                            }
                        }

                        foreach (Label::PRODUCT_META as $meta_key => $meta_label) {
                            if (!empty($meta_info[$meta_key])) {
                                $mt_default_meta_info[$meta_key] = true;
                            }
                        }

                        $mt_plan_list[] = [
                            'id' => $variation_id,
                            'parent_id' => $parent_id,
                            'price' => $price,
                            'size' => $size,
                            'meta_info_list' => $meta_info,
                        ];
                    }

                    return [$mt_plan_list, $mt_default_meta_info, $mt_default_platform, $mt_default_market_type];
                }
            }

            $mt_product = array_find($mt_filtered_products, function ($product) use ($mt_default_slug) {
                return isset($product['tree_map']) && isset($product['tree_map']['account-types']) && $product['tree_map']['account-types'] === $mt_default_slug;
            });

            [$mt_plan_list, $mt_default_meta_info, $mt_default_platform, $mt_default_market_type] = mt_get_plan_list($mt_product, $mt_account_sizes);

            /**
             * 8. Productos más populares
             */
            if (!function_exists('mt_most_popular_products')) {
                require_once get_template_directory() . '/inc/validate_coupon_for_variation.php';
            }

            $mt_best_products = mt_most_popular_products();

            /**
             * 9. Helper render meta info
             */
            if (!function_exists('mt_render_template_meta_info')) {
                function mt_render_template_meta_info($value = '', $label = '', $classes = ''): string
                {
                    return <<<HTML
<div class="mega-info-row {$classes}">
    <div class="mega-info-row__label">{$label}</div>
    <div class="mega-info-row__value text-truncate">{$value}</div>
</div>
HTML;
                }
            }

            /**
             * 10. Renderizar fragmento
             */


            ob_start();
            get_template_part('template-parts/landing-page/sections/pricing-table-fragment-bs', null, [
                'layout_type' => $accountTypeSlug && $accountTypeSlug == AccountType::ZERO_PLAN->value ? LayoutType::ZeroPlan->value : 'default',
                'mt_account_types' => $mt_account_types,
                'mt_default_platform' => $mt_default_platform,
                'mt_default_market_type' => $mt_default_market_type,
                'mt_default_slug' => $mt_default_slug,
                'mt_plan_list' => $mt_plan_list,
                'mt_default_meta_info' => $mt_default_meta_info,
                'mt_best_products' => $mt_best_products,
            ]);
            $html = ob_get_clean();

            /**
             * 11. Respuesta final
             */
            return new WP_REST_Response([
                'success' => true,
                'marketType' => $marketTypeSlug,
                'html' => $html,
            ], 200);
        } catch (Throwable $e) {
            return new WP_REST_Response([
                'success' => false,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }
}

/**
 * Registro del endpoint REST
 */
add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/pricing-fragment', [
        'methods' => 'GET',
        'callback' => 'mt_render_pricing_table_fragment',
        'permission_callback' => '__return_true',
        'args' => [
            'marketType' => [
                'required' => true,
                'type' => 'string',
                'description' => 'Market type (required)',
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => function ($param) {
                    return !empty($param);
                },
            ],
            'accountType' => [
                'required' => false,
                'type' => 'string',
                'description' => 'Account type slug (optional)',
                'sanitize_callback' => 'sanitize_text_field',
            ],
        ],
    ]);
});
