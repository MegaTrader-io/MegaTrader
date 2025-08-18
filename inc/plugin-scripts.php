<?php
/**
 * Plugin Name: WooCommerce Attribute Text Repeater
 * Description: Adds a custom repeater field with text inputs to WooCommerce product attributes
 * Version: 1.0.0
 * Author: Your Name
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class WC_Attribute_Text_Repeater {

    public function __construct() {
        // Add fields to attribute forms
        add_action('woocommerce_after_product_attribute_settings', array($this, 'add_attribute_repeater_field'), 10, 2);
        
        // Save attribute meta
        add_action('woocommerce_attribute_added', array($this, 'save_attribute_repeater_meta'), 10, 2);
        add_action('woocommerce_attribute_updated', array($this, 'save_attribute_repeater_meta'), 10, 2);
        
        // Add repeater fields to product edit page
        add_action('woocommerce_product_option_terms', array($this, 'add_product_attribute_repeater'), 10, 3);
        
        // Save product meta
        add_action('woocommerce_process_product_meta', array($this, 'save_product_attribute_repeater'), 10, 1);
        
        // Display on frontend
        add_filter('woocommerce_attribute', array($this, 'display_attribute_repeater_values'), 10, 3);
        
        // Add scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    /**
     * Add repeater field to attribute settings
     */
    public function add_attribute_repeater_field($attribute, $i) {
        $has_repeater = get_option('wc_attr_repeater_' . $attribute->id, 'no');
        ?>
        <tr>
            <th scope="row">
                <label for="attribute_has_repeater<?php echo esc_attr($i); ?>">
                    <?php esc_html_e('Enable Text Repeater', 'woocommerce'); ?>
                </label>
            </th>
            <td>
                <input name="attribute_has_repeater[<?php echo esc_attr($i); ?>]" id="attribute_has_repeater<?php echo esc_attr($i); ?>" type="checkbox" value="1" <?php checked($has_repeater, 'yes'); ?> />
                <p class="description"><?php esc_html_e('Allow multiple text values for this attribute', 'woocommerce'); ?></p>
            </td>
        </tr>
        <?php
    }

    /**
     * Save attribute repeater setting
     */
    public function save_attribute_repeater_meta($id, $data) {
        $has_repeater = isset($_POST['attribute_has_repeater']) && !empty($_POST['attribute_has_repeater']) ? 'yes' : 'no';
        update_option('wc_attr_repeater_' . $id, $has_repeater);
    }

    /**
     * Add repeater fields to product attribute selection
     */
    public function add_product_attribute_repeater($attribute_taxonomy, $i, $attribute) {
        $has_repeater = get_option('wc_attr_repeater_' . $attribute_taxonomy->attribute_id, 'no');
        
        // Only proceed if repeater is enabled for this attribute
        if ($has_repeater !== 'yes') {
            return;
        }

        global $post, $product_object;
        $product_id = $product_object->get_id();
        
        $attribute_name = sanitize_title($attribute_taxonomy->attribute_name);
        $repeater_values = get_post_meta($product_id, '_attr_repeater_' . $attribute_name, true);
        if (!is_array($repeater_values)) {
            $repeater_values = array('');
        }
        ?>
        <div class="attribute-repeater-container" data-attribute="<?php echo esc_attr($attribute_name); ?>">
            <p><?php esc_html_e('Text Repeater Values:', 'woocommerce'); ?></p>
            <div class="repeater-fields">
                <?php foreach ($repeater_values as $index => $value) : ?>
                <div class="repeater-row">
                    <input 
                        type="text" 
                        name="attribute_repeater[<?php echo esc_attr($attribute_name); ?>][]" 
                        value="<?php echo esc_attr($value); ?>" 
                        placeholder="<?php esc_attr_e('Enter text value', 'woocommerce'); ?>"
                    />
                    <button type="button" class="button remove-repeater-row"><?php esc_html_e('Remove', 'woocommerce'); ?></button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button add-repeater-row"><?php esc_html_e('Add Value', 'woocommerce'); ?></button>
        </div>
        <?php
    }

    /**
     * Save product attribute repeater values 
     */
    public function save_product_attribute_repeater($product_id) {
        if (!isset($_POST['attribute_repeater'])) {
            return;
        }
        
        foreach ($_POST['attribute_repeater'] as $attribute_name => $values) {
            // Filter out empty values
            $values = array_filter($values, function($value) {
                return trim($value) !== '';
            });
            
            if (!empty($values)) {
                update_post_meta($product_id, '_attr_repeater_' . sanitize_title($attribute_name), $values);
            } else {
                delete_post_meta($product_id, '_attr_repeater_' . sanitize_title($attribute_name));
            }
        }
    }

    /**
     * Display attribute repeater values on the frontend
     */
    public function display_attribute_repeater_values($text, $attribute, $values) {
        global $product;
        
        if (!$product) {
            return $text;
        }
        
        $attribute_name = sanitize_title($attribute->get_name());
        $repeater_values = get_post_meta($product->get_id(), '_attr_repeater_' . $attribute_name, true);
        
        if (is_array($repeater_values) && !empty($repeater_values)) {
            return implode(', ', $repeater_values);
        }
        
        return $text;
    }

    /**
     * Enqueue scripts and styles for admin
     */
    public function enqueue_admin_scripts($hook) {
        if ('post.php' !== $hook && 'post-new.php' !== $hook) {
            return;
        }
        
        global $post;
        
        if ('product' !== get_post_type($post)) {
            return;
        }
        
        wp_enqueue_script(
            'wc-admin-scripts',
            plugins_url('assets/js/admin-scripts.js', __FILE__),
            array('jquery'),
            '1.0.0',
            true
        );
        
        wp_enqueue_style(
            'wc-admin-style',
            plugins_url('assets/css/admin-style.css', __FILE__),
            array(),
            '1.0.0'
        );
    }
}

// Initialize the plugin
new WC_Attribute_Text_Repeater();