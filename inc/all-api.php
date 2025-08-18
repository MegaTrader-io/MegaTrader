<?php
add_action('rest_api_init', 'register_custom_product_endpoint');

function register_custom_product_endpoint() {
    register_rest_route('custom/v1', '/products-with-categories', array(
        'methods' => 'GET',
        'callback' => 'get_products_with_categories',
        'permission_callback' => '__return_true'
    ));
}

function get_products_with_categories() {
    // Retrieve products
    $products_query = new WP_Query(array(
        'post_type' => 'product',
        'posts_per_page' => -1
    ));

    $products_data = array();
    $categories_data = array();

    // Collect products
    if ($products_query->have_posts()) {
        while ($products_query->have_posts()) {
            $products_query->the_post();
            $product = wc_get_product(get_the_ID());

            // Get product categories
            $product_cats = get_the_terms($product->get_id(), 'product_cat');
            $category_ids = array();

            if ($product_cats) {
                foreach ($product_cats as $cat) {
                    $category_ids[] = $cat->term_id;
                }
            }

            // Collect custom meta fields
            $custom_meta = array(
                'maximum_loss_limit' => get_post_meta($product->get_id(), '_maximum_loss_limit', true),
                'maximum_position_size' => get_post_meta($product->get_id(), '_maximum_position_size', true),
                'profit_target' => get_post_meta($product->get_id(), '_profit_target', true)
            );

            $products_data[] = array(
                'id' => $product->get_id(),
                'title' => $product->get_name(),
                'price' => $product->get_price(),
                'category_ids' => $category_ids,
                'custom_meta' => $custom_meta
            );
        }
        wp_reset_postdata();
    }

    // Retrieve categories
    $categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false
    ));

    foreach ($categories as $category) {
        // Get category thumbnail
        $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
        $thumbnail_url = $thumbnail_id 
            ? wp_get_attachment_url($thumbnail_id) 
            : '';

        // Get category custom meta
        $category_custom_meta = get_term_meta($category->term_id, 'custom_repeater', true);

        $categories_data[] = array(
            'id' => $category->term_id,
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'thumbnail_url' => $thumbnail_url,
            'custom_meta' => $category_custom_meta ? $category_custom_meta : null
        );
    }

    return array(
        'products' => $products_data,
        'categories' => $categories_data
    );
}



//---------------------------------------------------------------------------------
add_action('rest_api_init', 'register_product_attributes_endpoint');

function register_product_attributes_endpoint() {
    register_rest_route('custom/v1', '/products-with-attributes', array(
        'methods' => 'GET',
        'callback' => 'get_products_with_attributes',
        'permission_callback' => '__return_true'
    ));
}

function get_products_with_attributes() {
    // Retrieve products - only variable and variable subscription products
    $products_query = new WP_Query(array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'product_type',
                'field' => 'slug',
                'terms' => array('variable', 'variable-subscription'),
                'operator' => 'IN'
            )
        )
    ));

    $products_data = array();
    $attributes_data = array();
    $structured_data = array();

    // Collect products
    if ($products_query->have_posts()) {
        while ($products_query->have_posts()) {
            $products_query->the_post();
            $product_id = get_the_ID();
            $product = wc_get_product($product_id);
            
            if (!$product || !($product->is_type('variable') || $product->is_type('variable-subscription'))) {
                continue;
            }

            // // Get product custom meta fields
            // $custom_meta = array(
            //     'profit_target' => get_post_meta($product_id, '_profit_target', true),
            //     'max_contracts' => get_post_meta($product_id, '_max_contracts', true),
            //     'daily_loss_limit' => get_post_meta($product_id, '_daily_loss_limit', true),
            //     'daily_loss_limit_soft_breach' => get_post_meta($product_id, '_daily_loss_limit_soft_breach', true),
            //     'trailing_max_drawdown' => get_post_meta($product_id, '_trailing_max_drawdown', true),
            //     'drawdown_mode' => get_post_meta($product_id, '_drawdown_mode', true),
            //     'min_trading_days' => get_post_meta($product_id, '_min_trading_days', true),
            //     'min_trading_days_to_payout' => get_post_meta($product_id, '_min_trading_days_to_payout', true),
            //     'reset_fee' => get_post_meta($product_id, '_reset_fee', true),
            //     'activation_fee' => get_post_meta($product_id, '_activation_fee', true),
            //     'consistency' => get_post_meta($product_id, '_consistency', true),
            //     'max_accounts' => get_post_meta($product_id, '_max_accounts', true)
            // );

            // Get product attributes
            $attributes = $product->get_attributes();
            $product_attribute_ids = array();

            if ($attributes) {
                foreach ($attributes as $attribute) {
                    if ($attribute->is_taxonomy()) {
                        $attribute_taxonomy_name = $attribute->get_taxonomy();
                        $attribute_terms = $attribute->get_terms();
                        
                        if ($attribute_terms) {
                            foreach ($attribute_terms as $term) {
                                $product_attribute_ids[] = $term->term_id;
                            }
                        }
                    }
                }
            }

            // Build structured variation data
            $product_name_slug = sanitize_title($product->get_name());
            $variations = $product->get_available_variations();

            // Get ordered attribute names and remove 'pa_' prefix
            $attribute_names = array_map(function($attr) {
                return str_replace('pa_', '', $attr->get_name());
            }, $attributes);

            // Initialize the structure for this product
            if (!isset($structured_data[$product_name_slug])) {
                $structured_data[$product_name_slug] = array();
            }

            // Process variations
            foreach ($variations as $variation) {
                if (!isset($variation['variation_id'])) {
                    continue; // Skip if variation_id is missing
                }
                
                $variation_obj = wc_get_product($variation['variation_id']);
                if (!$variation_obj) {
                    continue; // Skip if variation couldn't be loaded
                }
                
                $variation_attributes = $variation_obj->get_variation_attributes();
                
                // Clean up variation attributes
                $cleaned_variation_attributes = array();
                foreach ($variation_attributes as $key => $value) {
                    $clean_key = str_replace('attribute_pa_', '', $key);
                    $clean_key = str_replace('attribute_', '', $clean_key);
                    $cleaned_variation_attributes[$clean_key] = $value;
                }
                
                // Clean up the price
                $price = strip_tags($variation_obj->get_price_html());
                $price = html_entity_decode($price, ENT_QUOTES, 'UTF-8');
                
                // Start at the top level for this product
                $current_level = &$structured_data[$product_name_slug];
                
                // Build the nested structure
                foreach ($attribute_names as $attr_name) {
                    if (isset($cleaned_variation_attributes[$attr_name])) {
                        $attr_value = $cleaned_variation_attributes[$attr_name];
                        $attr_slug = sanitize_title($attr_value);
                        
                        // Check if we're at the last attribute
                        $is_last_attribute = $attr_name === end($attribute_names);
                        
                        if ($is_last_attribute) {
                            // Parse the price string to extract monthly and sign-up fees
                            $price_parts = explode(' and a ', $price);
                            $monthly_price = trim(str_replace(' / month', '', $price_parts[0]));
                            $signup_price = isset($price_parts[1]) ? trim($price_parts[1]) : '';
                            $signup_price = str_replace(' sign-up fee', '', $signup_price);
                            
                            // Get product metadata
                            $variation_id = $variation['variation_id'];
                            $meta_info = [
                                'profit_target' => get_post_meta($variation_id, 'profit_target', true),
                                'max_contracts' => get_post_meta($variation_id, 'max_contracts', true),
                                'daily_loss_limit' => get_post_meta($variation_id, 'daily_loss_limit', true),
                                'daily_loss_limit_soft_breach' => get_post_meta($variation_id, 'daily_loss_limit_soft_breach', true),
                                'trailing_max_drawdown' => get_post_meta($variation_id, 'trailing_max_drawdown', true),
                                'drawdown_mode' => get_post_meta($variation_id, 'drawdown_mode', true),
                                'min_trading_days' => get_post_meta($variation_id, 'min_trading_days', true),
                                'min_trading_days_to_payout' => get_post_meta($variation_id, 'min_trading_days_to_payout', true),
                                'reset_fee' => get_post_meta($variation_id, 'reset_fee', true),
                                'activation_fee' => get_post_meta($variation_id, 'activation_fee', true),
                                'consistency' => get_post_meta($variation_id, 'consistency', true),
                                'max_accounts' => get_post_meta($variation_id, 'max_accounts', true)
                            ];
                            
                            // Store the variation data in the new format
                            $current_level[$attr_slug] = [
                                ['id' => (string)$variation['variation_id']],
                                ['price-monthly' => $monthly_price],
                                ['price-signup' => $signup_price],
                                ['meta-info' => $meta_info]
                            ];
                        } else {
                            // Create nested array if needed
                            if (!isset($current_level[$attr_slug])) {
                                $current_level[$attr_slug] = array();
                            }
                            // Move to next level
                            $current_level = &$current_level[$attr_slug];
                        }
                    }
                }
            }

            // Add product data to array with proper reference to variations structure
            $products_data[] = array(
                'id' => $product_id,
                'title' => $product->get_name(),
                'slug' => $product->get_slug(),
                // 'attribute_ids' => $product_attribute_ids,
                $product->get_slug() => isset($structured_data[$product_name_slug]) ? $structured_data[$product_name_slug] : array()
            );
        }
        wp_reset_postdata();
    }

    // Retrieve all product attributes
    $attribute_taxonomies = wc_get_attribute_taxonomies();
    
    foreach ($attribute_taxonomies as $tax) {
        $taxonomy_name = 'pa_' . $tax->attribute_name;
        $terms = get_terms(array(
            'taxonomy' => $taxonomy_name,
            'hide_empty' => false
        ));
        
        if (!empty($terms) && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                // Get attribute image
                $image_id = get_term_meta($term->term_id, 'attribute_image_id', true);
                $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
                
                $crepeater_values = get_term_meta($term->term_id, 'custom_repeater_field', true);

                $attributes_data[] = array(
                    'id' => $term->term_id,
                    'name' => $term->name,
                    'slug' => $term->slug,
                    'taxonomy' => $taxonomy_name,
                    'taxonomy_label' => $tax->attribute_label,
                    'description' => $term->description,
                    'thumbnail_url' => $image_url,
                    'attribute_meta' => $crepeater_values ? $crepeater_values : null
                );
            }
        }
    }

    return array(
        'products' => $products_data,
        'attributes' => $attributes_data
    );
}
