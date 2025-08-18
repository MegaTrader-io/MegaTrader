<?php
// Enqueue necessary scripts for dynamic repeater functionality
add_action('admin_enqueue_scripts', 'enqueue_repeater_scripts');
function enqueue_repeater_scripts($hook) {
    if ($hook === 'edit-tags.php' || $hook === 'term.php') {
        wp_enqueue_script('repeater-field-script', get_template_directory_uri() . '/assets/js/admin-script.js', array('jquery'), '1.0', true);
        wp_enqueue_style('repeater-field-style', get_template_directory_uri() . '/assets/css/admin-style.css');
    }
}

// Add repeater field to category add form
add_action('product_cat_add_form_fields', 'add_custom_repeater_field');
function add_custom_repeater_field() {
    ?>
    <div class="form-field repeater-field-container">
        <label>Custom Repeater Field</label>
        <div id="repeater-container">
            <div class="repeater-item">
                <input type="text" name="custom_repeater[]" class="repeater-input">
                <button type="button" class="remove-repeater-item">-</button>
            </div>
        </div>
        <button type="button" id="add-repeater-item">Add Item</button>
        <p class="description">Add multiple custom values</p>
    </div>
    <?php
}

// Add repeater field to category edit form
add_action('product_cat_edit_form_fields', 'edit_custom_repeater_field');
function edit_custom_repeater_field($term) {
    $values = get_term_meta($term->term_id, 'custom_repeater', true);
    $values = is_array($values) ? $values : array($values);
    ?>
    <tr class="form-field">
        <th scope="row"><label>Custom Repeater Field</label></th>
        <td>
            <div class="repeater-field-container">
                <div id="repeater-container">
                    <?php foreach ($values as $value): ?>
                        <div class="repeater-item">
                            <input type="text" name="custom_repeater[]" 
                                   class="repeater-input" 
                                   value="<?php echo esc_attr($value); ?>">
                            <button type="button" class="remove-repeater-item">-</button>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($values)): ?>
                        <div class="repeater-item">
                            <input type="text" name="custom_repeater[]" class="repeater-input">
                            <button type="button" class="remove-repeater-item">-</button>
                        </div>
                    <?php endif; ?>
                </div>
                <button type="button" id="add-repeater-item">Add Item</button>
            </div>
            <p class="description">Add multiple custom values</p>
        </td>
    </tr>
    <?php
}

// Save custom repeater field
add_action('created_product_cat', 'save_custom_repeater_field');
add_action('edited_product_cat', 'save_custom_repeater_field');
function save_custom_repeater_field($term_id) {
    if (isset($_POST['custom_repeater'])) {
        // Sanitize and filter out empty values
        $values = array_filter($_POST['custom_repeater'], function($value) {
            return !empty(trim($value));
        });
        
        // Save only if there are non-empty values
        if (!empty($values)) {
            update_term_meta($term_id, 'custom_repeater', array_values($values));
        } else {
            delete_term_meta($term_id, 'custom_repeater');
        }
    }
}

//-----------------------------------------------------------------------------------------
// Add custom fields to product data meta box
add_action('woocommerce_product_options_general_product_data', 'add_custom_product_fields');
function add_custom_product_fields() {
    woocommerce_wp_text_input(array(
        'id' => '_maximum_loss_limit',
        'label' => 'Maximum Loss Limit',
        'placeholder' => 'Enter maximum loss limit',
        'desc_tip' => true,
        'description' => 'Set the maximum loss limit for this product'
    ));

    woocommerce_wp_text_input(array(
        'id' => '_maximum_position_size',
        'label' => 'Maximum Position Size',
        'placeholder' => 'Enter maximum position size',
        'desc_tip' => true,
        'description' => 'Set the maximum position size for this product'
    ));

    woocommerce_wp_text_input(array(
        'id' => '_profit_target',
        'label' => 'Profit Target',
        'placeholder' => 'Enter profit target',
        'desc_tip' => true,
        'description' => 'Set the profit target for this product'
    ));
}

// Save custom fields
add_action('woocommerce_process_product_meta', 'save_custom_product_fields');
function save_custom_product_fields($post_id) {
    $fields = array(
        '_maximum_loss_limit',
        '_maximum_position_size',
        '_profit_target'
    );

    foreach ($fields as $field) {
        $value = isset($_POST[$field]) ? sanitize_text_field($_POST[$field]) : '';
        update_post_meta($post_id, $field, $value);
    }
}

// Display custom fields on product page (optional)
add_action('woocommerce_single_product_summary', 'display_custom_product_fields');
function display_custom_product_fields() {
    global $product;
    $fields = array(
        'Maximum Loss Limit' => '_maximum_loss_limit',
        'Maximum Position Size' => '_maximum_position_size',
        'Profit Target' => '_profit_target'
    );

    foreach ($fields as $label => $meta_key) {
        $value = get_post_meta($product->get_id(), $meta_key, true);
        if ($value) {
            echo '<div class="custom-product-field">';
            echo '<strong>' . esc_html($label) . ':</strong> ' . esc_html($value);
            echo '</div>';
        }
    }
}

//----------------------------------------------------------------------------------
/**
 * Add Objectives and Rules Repeater Field to Product
 */
function add_objectives_rules_fields() {
    ?>
    <div class="options_group">
        <p class="form-field">
            <label><?php _e( 'Objectives & Rules', 'woocommerce' ); ?></label>
            <div id="objectives_rules_repeater" class="objectives_rules_wrapper">
                <?php
                $objectives_rules = get_post_meta( get_the_ID(), 'objectives_rules', true );
                
                if ( is_array( $objectives_rules ) && count( $objectives_rules ) > 0 ) {
                    $counter = 0;
                    foreach ( $objectives_rules as $item ) {
                        ?>
                        <div class="objectives_rules_item">
                            <p>
                                <input type="text" class="widefat" name="objectives_rules[<?php echo $counter; ?>]" value="<?php echo esc_attr( $item ); ?>" />
                                <a href="#" class="button remove_objectives_rules"><?php _e( 'Remove', 'woocommerce' ); ?></a>
                            </p>
                        </div>
                        <?php
                        $counter++;
                    }
                }
                ?>
                <div class="objectives_rules_item hidden" id="objectives_rules_template">
                    <p>
                        <input type="text" class="widefat" name="objectives_rules[{{index}}]" value="" />
                        <a href="#" class="button remove_objectives_rules"><?php _e( 'Remove', 'woocommerce' ); ?></a>
                    </p>
                </div>
                <p>
                    <a href="#" class="button add_objectives_rules"><?php _e( 'Add Objective/Rule', 'woocommerce' ); ?></a>
                </p>
            </div>
        </p>
    </div>
    
    <script type="text/javascript">
        jQuery(document).ready(function($) {
        
            var index = <?php echo isset( $counter ) ? $counter : 0; ?>;
            
            $('.add_objectives_rules').on('click', function(e) {
                e.preventDefault();
                
                var template = $('#objectives_rules_template').html();
                template = template.replace(/{{index}}/g, index);
                
                $('#objectives_rules_repeater').append('<div class="objectives_rules_item">' + template + '</div>');
                index++;
            });
            
            $(document).on('click', '.remove_objectives_rules', function(e) {
                e.preventDefault();
                $(this).closest('.objectives_rules_item').remove();
            });
        });
    </script>
    <style>
        .objectives_rules_item {
            padding: 5px 0;
        }
        .objectives_rules_item.hidden {
            display: none;
        }
        .objectives_rules_item input[type="text"] {
            width: calc(100% - 80px);
            margin-right: 10px;
            vertical-align: middle;
        }
        .objectives_rules_item .button {
            vertical-align: middle;
        }
    </style>
    <?php
}
add_action( 'woocommerce_product_options_general_product_data', 'add_objectives_rules_fields' );

/**
 * Save Objectives and Rules Fields
 */
function save_objectives_rules_fields( $post_id ) {
    // Save repeater fields
    if ( isset( $_POST['objectives_rules'] ) ) {
        $objectives_rules = array();
        
        foreach ( $_POST['objectives_rules'] as $item ) {
            if ( !empty( $item ) ) {
                $objectives_rules[] = sanitize_text_field( $item );
            }
        }
        
        update_post_meta( $post_id, 'objectives_rules', $objectives_rules );
    } else {
        delete_post_meta( $post_id, 'objectives_rules' );
    }
}
add_action( 'woocommerce_process_product_meta', 'save_objectives_rules_fields' );

/**
 * Display Objectives and Rules on Product Page
 */
function display_objectives_rules() {
    global $post;
    
    $objectives_rules = get_post_meta( $post->ID, 'objectives_rules', true );
    
    if ( is_array( $objectives_rules ) && count( $objectives_rules ) > 0 ) {
        echo '<div class="product-objectives-rules">';
        echo '<h3>' . esc_html__( 'Objectives & Rules', 'woocommerce' ) . '</h3>';
        echo '<ul class="objectives-rules-list">';
        
        foreach ( $objectives_rules as $item ) {
            echo '<li>' . esc_html( $item ) . '</li>';
        }
        
        echo '</ul>';
        echo '</div>';
    }
}
add_action( 'woocommerce_after_single_product_summary', 'display_objectives_rules', 15 );



//----------------------------------------------------------------------------------------------
/**
 * Add custom repeater field to WooCommerce attribute term add/edit pages
 */

// Add repeater field to attribute term edit form
function custom_attribute_edit_repeater_field($term) { 
    $values = get_term_meta($term->term_id, 'custom_repeater_field', true);
    $values = is_array($values) ? $values : [];
    ?>
    <tr class="form-field">
        <th scope="row">
            <label><?php _e('Custom Repeater Field', 'text-domain'); ?></label>
        </th>
        <td>
            <div class="repeater-container">
                <?php 
                if (!empty($values)) {
                    foreach ($values as $value) : ?>
                        <div class="repeater-row">
                            <input type="text" name="custom_repeater_field[]" value="<?php echo esc_attr($value); ?>" />
                            <button type="button" class="button remove-row"><?php _e('Remove', 'text-domain'); ?></button>
                        </div>
                    <?php endforeach;
                } else { ?>
                    <div class="repeater-row">
                        <input type="text" name="custom_repeater_field[]" value="" />
                        <button type="button" class="button remove-row"><?php _e('Remove', 'text-domain'); ?></button>
                    </div>
                <?php } ?>
            </div>
            <div class="repeater-template" style="display: none;">
                <div class="repeater-row">
                    <input type="text" name="custom_repeater_field[]" value="" />
                    <button type="button" class="button remove-row"><?php _e('Remove', 'text-domain'); ?></button>
                </div>
            </div>
            <button type="button" class="button add-row"><?php _e('Add Field', 'text-domain'); ?></button>
            <p class="description"><?php _e('Add custom repeater fields', 'text-domain'); ?></p>
        </td>
    </tr>
    <?php
}

// Add repeater field to attribute term add form
function custom_attribute_add_repeater_field() {
    ?>
    <div class="form-field">
        <label><?php _e('Custom Repeater Field', 'text-domain'); ?></label>
        <div class="repeater-container">
            <div class="repeater-row">
                <input type="text" name="custom_repeater_field[]" value="" />
                <button type="button" class="button remove-row"><?php _e('Remove', 'text-domain'); ?></button>
            </div>
        </div>
        <div class="repeater-template" style="display: none;">
            <div class="repeater-row">
                <input type="text" name="custom_repeater_field[]" value="" />
                <button type="button" class="button remove-row"><?php _e('Remove', 'text-domain'); ?></button>
            </div>
        </div>
        <button type="button" class="button add-row"><?php _e('Add Field', 'text-domain'); ?></button>
        <p class="description"><?php _e('Add custom repeater fields', 'text-domain'); ?></p>
    </div>
    <?php
}

// Save repeater field data
function save_custom_attribute_repeater_field($term_id) {
    // Skip saving if this is an AJAX request
    if (defined('DOING_AJAX') && DOING_AJAX) {
        return;
    }

    // Check if our field is submitted
    if (!isset($_POST['custom_repeater_field'])) {
        return;
    }
    
    $values = isset($_POST['custom_repeater_field']) ? 
              array_map('sanitize_text_field', $_POST['custom_repeater_field']) : 
              [];
              
    // Remove empty values
    $values = array_filter($values, function($value) {
        return $value !== '';
    });
    
    update_term_meta($term_id, 'custom_repeater_field', $values);
}

// Add scripts for repeater functionality
function custom_attribute_repeater_scripts() {
    $screen = get_current_screen();
    
    // Only add on attribute edit screens
    if (strpos($screen->id, 'edit-pa_') === false && strpos($screen->id, 'pa_') === false) {
        return;
    }
    
    ?>
    <script>
    jQuery(document).ready(function($) {
        $(document).on('click', '.add-row', function() {
            var container = $(this).closest('.form-field, tr.form-field').find('.repeater-container');
            var template = $(this).siblings('.repeater-template').find('.repeater-row').clone();
            container.append(template);
        });

        $(document).on('click', '.remove-row', function() {
            // Don't remove if it's the only row
            if ($(this).closest('.repeater-container').find('.repeater-row').length > 1) {
                $(this).closest('.repeater-row').remove();
            } else {
                $(this).closest('.repeater-row').find('input').val('');
            }
        });
    });
    </script>
    <?php
}

// Hook into all WooCommerce attribute taxonomies
function custom_attribute_repeater_init() {
    // Make sure WooCommerce is active
    if (!function_exists('wc_get_attribute_taxonomies')) {
        return;
    }
    
    $attribute_taxonomies = wc_get_attribute_taxonomies();
    
    if (!empty($attribute_taxonomies)) {
        foreach ($attribute_taxonomies as $tax) {
            $taxonomy = wc_attribute_taxonomy_name($tax->attribute_name);
            
            add_action("{$taxonomy}_add_form_fields", 'custom_attribute_add_repeater_field');
            add_action("{$taxonomy}_edit_form_fields", 'custom_attribute_edit_repeater_field', 10, 1);
            add_action("created_{$taxonomy}", 'save_custom_attribute_repeater_field');
            add_action("edited_{$taxonomy}", 'save_custom_attribute_repeater_field');
        }
    }
}

// Use admin_init which runs after WooCommerce has registered its taxonomies
add_action('admin_init', 'custom_attribute_repeater_init');

// Add the scripts just once in the admin footer
add_action('admin_footer', 'custom_attribute_repeater_scripts');


//----------------------------------------------------------------------------------------------
// Add custom fields to product variations
// List of custom fields
function bmc_get_custom_variation_fields() {
    return [
        'profit_target'             => 'Profit Target',
        'max_contracts'             => 'Max Contracts',
        'daily_loss_limit'          => 'Daily Loss Limit',
		'daily_loss_limit_soft_breach'          => 'Daily Loss Limit (Soft Breach):',
        'trailing_max_drawdown'     => 'Trailing Max Drawdown',
        'drawdown_mode'             => 'Drawdown Mode',
        'min_trading_days'          => 'Min Trading Days to Pass',
		'min_trading_days_to_payout'          => 'Min Trading Days to Payout',
        'reset_fee'                 => 'Reset Fee',
        'activation_fee'            => 'Activation Fee',
        'consistency'               => 'Consistency',
        'max_accounts'              => 'Max Accounts'
    ];
}

// Display fields in variation settings
add_action('woocommerce_product_after_variable_attributes', function ($loop, $variation_data, $variation) {
    foreach (bmc_get_custom_variation_fields() as $key => $label) {
        woocommerce_wp_text_input([
            'id'          => $key . '[' . $loop . ']',
            'name'        => $key . '[' . $loop . ']',
            'value'       => get_post_meta($variation->ID, $key, true),
            'label'       => $label,
            'desc_tip'    => true,
            'description' => '',
        ]);
    }
}, 10, 3);

// Save variation fields
add_action('woocommerce_save_product_variation', function ($variation_id, $i) {
    foreach (bmc_get_custom_variation_fields() as $key => $label) {
        if (isset($_POST[$key][$i])) {
            update_post_meta($variation_id, $key, sanitize_text_field($_POST[$key][$i]));
        }
    }
}, 10, 2);


/*
add_filter('woocommerce_available_variation', function ($variation_data, $product, $variation) {
    foreach (bmc_get_custom_variation_fields() as $key => $label) {
        $variation_data[$key] = get_post_meta($variation->get_id(), $key, true);
    }
    return $variation_data;
}, 10, 3);
*/


//----------------------------------------------------------------------------------------------
// Add custom image field to attribute term form
function add_attribute_image_field() {
    ?>
    <div class="form-field term-image-wrap">
        <label for="attribute_image"><?php _e('Attribute Icon', 'woocommerce'); ?></label>
        <div id="attribute_image_container">
            <img src="<?php echo wc_placeholder_img_src(); ?>" width="60px" height="60px" />
        </div>
        <div>
            <input type="hidden" id="attribute_image_id" name="attribute_image_id" />
            <button type="button" class="upload_image_button button"><?php _e('Upload/Add image', 'woocommerce'); ?></button>
            <button type="button" class="remove_image_button button"><?php _e('Remove image', 'woocommerce'); ?></button>
        </div>
        <p><?php _e('Upload an image to represent this attribute.', 'woocommerce'); ?></p>
    </div>
    <?php
}

// Add image field to edit attribute term form
function add_attribute_image_field_edit($term) {
    $image_id = get_term_meta($term->term_id, 'attribute_image_id', true);
    $image = $image_id ? wp_get_attachment_thumb_url($image_id) : wc_placeholder_img_src();
    ?>
    <tr class="form-field term-image-wrap">
        <th scope="row"><label for="attribute_image"><?php _e('Attribute Icon', 'woocommerce'); ?></label></th>
        <td>
            <div id="attribute_image_container">
                <img src="<?php echo esc_url($image); ?>" width="60px" height="60px" />
            </div>
            <div>
                <input type="hidden" id="attribute_image_id" name="attribute_image_id" value="<?php echo esc_attr($image_id); ?>" />
                <button type="button" class="upload_image_button button"><?php _e('Upload/Add image', 'woocommerce'); ?></button>
                <button type="button" class="remove_image_button button"><?php _e('Remove image', 'woocommerce'); ?></button>
            </div>
            <p><?php _e('Upload an image to represent this attribute.', 'woocommerce'); ?></p>
        </td> 
    </tr>
    <?php
}

// Save attribute image
function save_attribute_image($term_id) {
    if (isset($_POST['attribute_image_id'])) {
        update_term_meta($term_id, 'attribute_image_id', absint($_POST['attribute_image_id']));
    }
}

// Add media scripts for image upload
function attribute_image_scripts() {
    wp_enqueue_media();
    ?>
    <script>
        jQuery(document).ready(function($) {
            // Media uploader
            var mediaUploader;
            
            $(document).on('click', '.upload_image_button', function(e) {
                e.preventDefault();
                
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                
                mediaUploader = wp.media.frames.file_frame = wp.media({
                    title: '<?php _e('Choose an image', 'woocommerce'); ?>',
                    button: {
                        text: '<?php _e('Use this image', 'woocommerce'); ?>'
                    },
                    multiple: false
                });
                
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#attribute_image_container').find('img').attr('src', attachment.url);
                    $('#attribute_image_id').val(attachment.id);
                });
                
                mediaUploader.open();
            });
            
            $(document).on('click', '.remove_image_button', function() {
                $('#attribute_image_container').find('img').attr('src', '<?php echo wc_placeholder_img_src(); ?>');
                $('#attribute_image_id').val('');
                return false;
            });
        });
    </script>
    <?php
}

// Hook into all attribute taxonomies
function add_attribute_image_fields() {
    $attribute_taxonomies = wc_get_attribute_taxonomies();
    
    if ($attribute_taxonomies) {
        foreach ($attribute_taxonomies as $tax) {
            $taxonomy = wc_attribute_taxonomy_name($tax->attribute_name);
            
            add_action($taxonomy . '_add_form_fields', 'add_attribute_image_field');
            add_action($taxonomy . '_edit_form_fields', 'add_attribute_image_field_edit');
            add_action('created_' . $taxonomy, 'save_attribute_image');
            add_action('edited_' . $taxonomy, 'save_attribute_image');
            add_action('admin_head', 'attribute_image_scripts');
        }
    }
}
add_action('admin_init', 'add_attribute_image_fields');

/* 
// Display attribute icon on product page
function display_attribute_icon($html, $term) {
    $image_id = get_term_meta($term->term_id, 'attribute_image_id', true);
    
    if ($image_id) {
        $image = wp_get_attachment_image($image_id, array(20, 20), false, array('class' => 'attribute-icon'));
        $html = $image . ' ' . $html;
    }
    
    return $html;
}
add_filter('woocommerce_attribute_term_name', 'display_attribute_icon', 10, 2);
*/

