<?php
/**
 * megatrader functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package megatrader
 */

if ( ! defined( '_MEGATRADER_VERSION' ) ) {
	define( '_MEGATRADER_VERSION', time() );
}
if ( ! defined( 'REALTIME_VERSION' ) ) {
	define( 'REALTIME_VERSION', time() );
}
define('MEGATRADER_THEME_URI', get_template_directory_uri());
define('MEGATRADER_THEME_DIR', get_template_directory());
define('MEGATRADER_CSS', get_template_directory_uri() . '/assets/css/');
define('MEGATRADER_JS', get_template_directory_uri() . '/assets/js/');


/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function megatrader_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on megatrader, use a find and replace
		* to change 'megatrader' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'megatrader', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );


	add_theme_support( 'woocommerce' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'primary-menu' => esc_html__( 'Primary Menu', 'megatrader' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );
}
add_action( 'after_setup_theme', 'megatrader_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function megatrader_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'megatrader_content_width', 640 );
}
add_action( 'after_setup_theme', 'megatrader_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function megatrader_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'megatrader' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'megatrader' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'megatrader_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function megatrader_scripts() {

	wp_enqueue_style( 'bootstrap',				MEGATRADER_CSS .'bootstrap.min.css', array(), _MEGATRADER_VERSION );
	// wp_enqueue_style( 'scrollCue-css',		MEGATRADER_CSS .'scrollCue.min.css', array(), _MEGATRADER_VERSION );
	wp_enqueue_style( 'megatrader-main',		MEGATRADER_CSS .'style.css', array(), REALTIME_VERSION );
    wp_enqueue_style( 'megatrader-style',       get_stylesheet_uri(), array(), _MEGATRADER_VERSION );
	wp_enqueue_style( 'megatrader-dev',         MEGATRADER_CSS .'megatrader-dev.css', array('megatrader-style'), REALTIME_VERSION);
    wp_enqueue_style( 'mt-components',          MEGATRADER_CSS .'mt-components.css', array(), REALTIME_VERSION);
    wp_enqueue_style( 'mt-navbar-style',         MEGATRADER_CSS . 'mt-navbar.css', array(), REALTIME_VERSION);

  



	//Register All JS
	wp_enqueue_script( 'bootstrap-bundle',	MEGATRADER_JS .'bootstrap.bundle.min.js', array('jquery'), _MEGATRADER_VERSION, true);
	// wp_enqueue_script( 'scrollCue',			MEGATRADER_JS .'scrollCue.min.js', array('jquery'), _MEGATRADER_VERSION, true);
	// wp_enqueue_script( 'smoothscroll',		MEGATRADER_JS .'smoothscroll.min.js', array('jquery'), _MEGATRADER_VERSION, true);
    wp_enqueue_script( 'megatrader-modal',	MEGATRADER_JS .'modal.js', array('jquery', 'bootstrap-bundle'), REALTIME_VERSION, true);
    wp_enqueue_script( 'mt-tabs',	        MEGATRADER_JS .'mt-tabs.js', array(), REALTIME_VERSION, true);
    wp_enqueue_script( 'mt-addons',	        MEGATRADER_JS .'mt-addons.js', array(), REALTIME_VERSION, true);
    wp_enqueue_script( 'mt-payment',	        MEGATRADER_JS .'payment-methods.js', array(), REALTIME_VERSION, true);
    wp_enqueue_script( 'mt-account-picker',	        MEGATRADER_JS .'mt-account-picker.js', array(), REALTIME_VERSION, true);
    wp_enqueue_script( 'mt-navbar-js',	        MEGATRADER_JS .'mt-navbar.js', array(), REALTIME_VERSION, true);
    wp_enqueue_script( 'mt-tooltips-js',	        MEGATRADER_JS .'mt-tooltips.js', array(), REALTIME_VERSION, true);
    wp_enqueue_script( 'mt-billing-validation-js',	        MEGATRADER_JS .'billing-validation.js', array(), REALTIME_VERSION, true);
   



    wp_enqueue_script( 'megatrader-main',	MEGATRADER_JS .'main.js', array('jquery', 'mt-tabs'), REALTIME_VERSION, true);

	wp_localize_script('megatrader-main', 'theme_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

add_action('after_setup_theme', function () {
  $inc = trailingslashit( get_stylesheet_directory() ) . 'inc/init.php';
  if ( file_exists($inc) ) { require_once $inc; }
}, 0);


add_action('wp', function () {
    if (!is_page_template('landing-page.php')) {
        add_action('wp_enqueue_scripts', 'megatrader_scripts' );
    }
});

require_once get_template_directory() . '/inc/mt-navbar.php';
require_once get_template_directory() . '/inc/landing-page-hooks.php';

add_action('wp_enqueue_scripts', function () {
    if (
            is_front_page()
    ) {
        megatrader_landing_page_scripts();
    }
});

/**
 * Post Reading Time Function
 */
function reading_time() {
    $content = get_post_field('post_content', get_the_ID());
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // Assuming 200 words per minute reading speed
    return $reading_time;
}
function set_html_content_type() {
  return 'text/html';
}
add_filter( 'wp_mail_content_type', 'set_html_content_type' );
/**
 * Modify the Search Query
 */
function custom_search_filter( $query ) {
    if ( ! is_admin() && $query->is_search && $query->is_main_query() ) {
        // Check if searching for the custom post type 'wc_themes'
        if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'wc_themes' ) {
            $query->set( 'post_type', 'wc_themes' );

            // Filter by custom taxonomy 'wc_themes_category'
            if ( isset( $_GET['wc_themes_category'] ) && ! empty( $_GET['wc_themes_category'] ) ) {
                $tax_query = array(
                    array(
                        'taxonomy' => 'wc_themes_category',
                        'field'    => 'slug',
                        'terms'    => sanitize_text_field( $_GET['wc_themes_category'] ),
                    ),
                );
                $query->set( 'tax_query', $tax_query );
            }
        }
    }
}
add_action( 'pre_get_posts', 'custom_search_filter' );

/**
 *Constant.
 */
require get_template_directory() . '/inc/constants.php';
/**
 * Custom Widget & Function
 */
require get_template_directory() . '/inc/widgets.php';
require get_template_directory() . '/inc/functions.php';

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Custom Fields
 */
require get_template_directory() . '/inc/custom-fields.php';

/**
 * All API
 */
require get_template_directory() . '/inc/all-api.php';

/**
 * OTP Login
 */
require get_template_directory() . '/inc/otp-login.php';

/**
 * Payment Methods
 */
require get_template_directory() . '/inc/class-mt-payment-methods.php';

/**
 * Utils Functions
 */
require_once get_template_directory() . '/inc/attributes-meta-parser.php';
require_once get_template_directory() . '/inc/validate_coupon_for_variation.php';

// Load navigation module
require_once get_template_directory() . '/inc/account-navigation-module.php';

/**
 * Plugin Scripts
 */
// require get_template_directory() . '/inc/plugin-scripts.php';



// add_filter( 'woocommerce_get_template', 'custom_override_checkout_login_template', 10, 2 );

// function custom_override_checkout_login_template( $template, $template_name ) {
//     if ( 'checkout/form-login.php' === $template_name ) {
//         $template = get_stylesheet_directory() . '/woocommerce/checkout/form-login.php';
//     }
//     return $template;
// }


function custom_logout_redirect() {
    wp_clear_auth_cookie();
    wp_destroy_current_session();
    return wp_redirect('https://megatrader.io/');
    exit();
}
add_action('wp_logout', 'custom_logout_redirect');

add_filter('logout_redirect', function ($redirect_to, $requested_redirect_to, $user) {
    if (is_admin()) {
        return $redirect_to ?: admin_url();
    }

    if (!empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], admin_url()) !== false) {
        return $redirect_to ?: admin_url();
    }

    nocache_headers();

    return home_url('/auth/login/?logged_out=1');
}, 10, 3);

/* Update Billing Information */
add_action( 'wp_ajax_update_billing_address', 'update_billing_address' );
add_action( 'wp_ajax_nopriv_update_billing_address', 'update_billing_address' );

function update_billing_address() {
    // Check if the user is logged in
    if ( ! is_user_logged_in() ) {
        wp_send_json_error( 'User not logged in' );
        return;
    }

    // Get current user ID
    $user_id = get_current_user_id();

    // Update billing information
    $billing_fields = array(
        'billing_first_name',
        'billing_last_name',
        'billing_address_1',
        'billing_city',
        'billing_postcode',
        'billing_country',
        'billing_state',
        'billing_phone',
    );

    foreach ( $billing_fields as $field ) {
        if ( isset( $_POST[ $field ] ) ) {
            update_user_meta( $user_id, $field, sanitize_text_field( $_POST[ $field ] ) );
        }
    }

    wp_send_json_success(); // Send a success response back to the front-end
}

add_action( 'wp_ajax_get_cities', 'get_cities_by_country' );
add_action( 'wp_ajax_nopriv_get_cities', 'get_cities_by_country' );

function get_cities_by_country() {
    $country = sanitize_text_field( $_POST['country'] );
    $cities = WC()->countries->get_states( $country ); // Fetch states based on country

    if ( ! empty( $cities ) ) {
        echo '<select name="billing_state" id="billing_state" class="form-select form-control">';
        echo '<option value="">' . esc_html__( 'Select An Option', 'megatrader' ) . '</option>';
        foreach ( $cities as $key => $city ) {
            echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $city ) . '</option>';
        }
        echo '</select>';
    } else {
        echo '<input type="text" name="billing_state" id="billing_state" class="form-control" placeholder="' . esc_attr__( 'Enter State / County', 'megatrader' ) . '" />';
    }

    wp_die();
}

remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );


/* Update Account Information 
// Hook for logged-in users
add_action('admin_post_update_account_details', 'handle_account_update');
// Hook for non-logged-in users (if necessary)
add_action('admin_post_nopriv_update_account_details', 'handle_account_update');

function handle_account_update() {
    // Verify nonce
    if (!isset($_POST['update_account_nonce']) || !wp_verify_nonce($_POST['update_account_nonce'], 'update_account_details')) {
        wp_die('Security check failed');
    }

    // Get the current user
    $user_id = get_current_user_id();
    if (!$user_id) {
        wp_die('User not logged in');
    }

    // Update display name if provided
    if (isset($_POST['display_name']) && !empty($_POST['display_name'])) {
        $display_name = sanitize_text_field($_POST['display_name']);
        wp_update_user([
            'ID' => $user_id,
            'display_name' => $display_name
        ]);
    }

    // Handle password change
    if (!empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Check if the current password is correct
        $user = wp_get_current_user();
        if (!wp_check_password($current_password, $user->user_pass, $user->ID)) {
            wp_die('Current password is incorrect');
        }

        // Check if new password matches confirmation
        if ($new_password !== $confirm_password) {
            wp_die('New password and confirmation do not match');
        }

        // Update password
        wp_set_password($new_password, $user_id);
        wp_redirect(home_url('/my-account/')); // Redirect to the account page after updating password
        exit;
    }

    // Redirect after successful update
    wp_redirect(home_url('/my-account/orders/')); // Redirect to your account page
    exit;
}


// Step 1: Validate account fields during checkout process
add_action( 'woocommerce_checkout_process', 'validate_account_on_checkout' );
function validate_account_on_checkout() {
    if ( ! is_user_logged_in() ) {
        // Sanitize the username, email, and password from the checkout form
        $username = sanitize_text_field( $_POST['account_username'] );
        $email    = sanitize_email( $_POST['billing_email'] );
        $password = sanitize_text_field( $_POST['account_password'] );

        // Check if the username is provided
        if ( empty( $username ) ) {
            wc_add_notice( __( 'Please enter a username to create an account.', 'megatrader' ), 'error' );
        }

        // Ensure username doesn't already exist
        if ( username_exists( $username ) ) {
            wc_add_notice( __( 'Username already exists. Please choose another one.', 'megatrader' ), 'error' );
        }

        // Check if email is provided
        if ( empty( $email ) ) {
            wc_add_notice( __( 'Please enter an email address.', 'megatrader' ), 'error' );
        }

        // Ensure email doesn't already exist
        if ( email_exists( $email ) ) {
            wc_add_notice( __( 'Email already registered. Please log in or use a different email.', 'megatrader' ), 'error' );
        }

        // Check if password is provided
        if ( empty( $password ) ) {
            wc_add_notice( __( 'Please enter a password to create an account.', 'megatrader' ), 'error' );
        }
    }
}


// Step 1: Validate account fields during checkout process
add_action('woocommerce_checkout_process', 'custom_checkout_fields_validation');
function custom_checkout_fields_validation() {
    if (!is_user_logged_in()) {
        if (empty($_POST['account_username'])) {
            wc_add_notice(__('Please enter a username to create an account.', 'megatrader'), 'error');
        }
        if (empty($_POST['account_password'])) {
            wc_add_notice(__('Please enter a password to create an account.', 'megatrader'), 'error');
        }
    }
}

// Override WooCommerce username and password during registration
add_filter('woocommerce_new_customer_data', 'override_woocommerce_username_password', 10, 1);
function override_woocommerce_username_password($new_customer_data) {
    if (!is_user_logged_in() && !empty($_POST['account_username']) && !empty($_POST['account_password'])) {
        $new_customer_data['user_login'] = sanitize_text_field($_POST['account_username']);
        $new_customer_data['user_pass'] = sanitize_text_field($_POST['account_password']);
    }
    return $new_customer_data;
}


// Set username and password for new accounts during checkout
add_action('woocommerce_created_customer', 'set_account_username_password', 10, 3);
function set_account_username_password($customer_id, $new_customer_data, $password_generated) {
    if (!empty($_POST['account_username']) && !empty($_POST['account_password'])) {
        wp_update_user([
            'ID' => $customer_id,
            'user_login' => sanitize_text_field($_POST['account_username']),
            'user_pass' => sanitize_text_field($_POST['account_password']),
        ]);
    }
}

// AJAX check for username availability
add_action('wp_ajax_check_username_availability', 'check_username_availability');
add_action('wp_ajax_nopriv_check_username_availability', 'check_username_availability');

function check_username_availability() {
    $username = sanitize_text_field($_POST['username']);
    $is_available = !username_exists($username);

    if ($is_available) {
        wp_send_json([
            'available' => true,
            'message' => __('Username is available.', 'megatrader')
        ]);
    } else {
        wp_send_json([
            'available' => false,
            'message' => __('Username is already taken.', 'megatrader')
        ]);
    }

    wp_die();
}

*/


/* Billing Fields Remove */
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );

function custom_override_checkout_fields( $fields ) {

    // unset($fields['billing']['billing_company']);
    // unset($fields['billing']['billing_address_1']);
    // unset($fields['billing']['billing_address_2']);
    // unset($fields['billing']['billing_city']);
    // unset($fields['billing']['billing_postcode']);
    // unset($fields['billing']['billing_country']);
    // unset($fields['billing']['billing_state']);
    // unset($fields['billing']['billing_phone']);

    unset($fields['shipping']);
    unset($fields['order']['order_comments']);

    return $fields;
}

// add_action('login_enqueue_scripts', 'redirect_to_login_or_admin');
// function redirect_to_login_or_admin() {
//     if ( !is_user_logged_in() ) {
//         wp_redirect(site_url('/sign-in/'));
//         exit();
//     }
// }

// Remove the coupon form from the checkout page
function remove_woocommerce_checkout_coupon_form() {
    remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
}
add_action( 'wp', 'remove_woocommerce_checkout_coupon_form' );


function add_file_types_to_uploads($file_types){
    $new_filetypes = array();
    $new_filetypes['svg'] = 'image/svg+xml';
    $file_types = array_merge($file_types, $new_filetypes );
    return $file_types;
    }
add_filter('upload_mimes', 'add_file_types_to_uploads');


// Policies mandatory in policy in checkout
add_action( 'woocommerce_review_order_before_submit', 'add_privacy_checkbox', 9 );
function add_privacy_checkbox() {
    woocommerce_form_field(
        'privacy_policy',
        array(
            'type'         => 'checkbox',
            'id'           => 'privacy_policy',
            'class'        => array( 'form-row', 'privacy', 'form-check', 'd-flex', 'align-items-center' ),
            'label_class'  => array( 'woocommerce-form__label', 'woocommerce-form__label-for-checkbox', 'checkbox', 'form-check-label' ),
            'input_class'  => array( 'woocommerce-form__input', 'woocommerce-form__input-checkbox', 'input-checkbox', 'form-check-input' ),
            'required'     => true,
            'label'        => 'Agree to our <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms of Service</a> and <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal">Privacy Policy</a>.',
        )
    );
}




add_action('woocommerce_checkout_process', 'validate_privacy_checkbox');
function validate_privacy_checkbox() {
    if (!isset($_POST['privacy_policy'])) {
        wc_add_notice(__('Please accept our Terms of Service and Privacy Policy to continue'), 'error');
    }
}



/* Checkout page vairation update ----------------------------*/
add_action( 'wp_footer', 'custom_checkout_variation_script' );
function custom_checkout_variation_script() {
    if ( is_checkout() && ! is_wc_endpoint_url() ) :
    ?>
    <script type="text/javascript">
    jQuery( function($){
        if (typeof wc_checkout_params === 'undefined')
            return false;

        var couponCode = '';

        $(document).on('input change', 'input[name="coupon_code"]', function(){
            couponCode = $(this).val();
        });

        $(document).on('click', 'button[name="apply_coupon"]', function(e){
            e.preventDefault(); // Prevent default form submission
            var $button = $(this);
            $button.prop('disabled', true);
            $.ajax({
                type: 'POST',
                url: wc_checkout_params.ajax_url,
                data: {
                    'action': 'apply_checkout_coupon',
                    'coupon_code': couponCode,
                },
                success: function (response) {
                    $(document.body).trigger("update_checkout"); // Refresh checkout
                    $('.woocommerce-error,.woocommerce-message').remove(); // Remove other notices
                    $('input[name="coupon_code"]').val(''); // Empty coupon code input field
                    $('form.checkout').before(response); // Display notices
                    $button.prop('disabled', false); // Re-enable button
                },
                error: function() {
                    $button.prop('disabled', false); // Re-enable button on failure
                }
            });
        });
    });
    </script>
    <?php
    endif;
}

// Ajax receiver function for applying the coupon
add_action( 'wp_ajax_apply_checkout_coupon', 'apply_checkout_coupon_ajax_receiver' );
add_action( 'wp_ajax_nopriv_apply_checkout_coupon', 'apply_checkout_coupon_ajax_receiver' );
function apply_checkout_coupon_ajax_receiver() {
    if ( isset($_POST['coupon_code']) && ! empty($_POST['coupon_code']) ) {
        WC()->cart->add_discount( wc_format_coupon_code( wp_unslash( $_POST['coupon_code'] ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
    } else {
        wc_add_notice( WC_Coupon::get_generic_coupon_error( WC_Coupon::E_WC_COUPON_PLEASE_ENTER ), 'error' );
    }
    wc_print_notices();
    wp_die();
}




//Remove multiple products
add_action( 'woocommerce_before_add_to_cart_button', 'custom_remove_existing_product_from_cart' );

function custom_remove_existing_product_from_cart() {
    // Clear the cart
    WC()->cart->empty_cart();
}

add_action( 'template_redirect', 'empty_cart_redirection' );
function empty_cart_redirection(){
    if( WC()->cart->is_empty() && is_cart() ){
        wp_safe_redirect( esc_url( add_query_arg( 'add-to-cart', 67, wc_get_checkout_url() ) ) );
        exit;
    }
}

add_filter( 'woocommerce_add_cart_item_data', 'wdm_empty_cart', 10,  3);
function wdm_empty_cart( $cart_item_data, $product_id, $variation_id )
{
    global $woocommerce;
    $woocommerce->cart->empty_cart();
    // Do nothing with the data and return
    return $cart_item_data;
}






add_action( 'woocommerce_checkout_update_order_meta', 'save_optional_updates' );
function save_optional_updates( $order_id ) {
    if ( ! empty( $_POST['updates'] ) ) {
        update_post_meta( $order_id, 'agreed_to_updates', sanitize_text_field( $_POST['updates'] ) );
    }
}

// Remove WooCommerce added to cart message
add_filter('wc_add_to_cart_message_html', '__return_null');




// Add a custom 'Product Name' column to the My Account orders table in the second position
add_filter( 'woocommerce_account_orders_columns', 'add_product_name_column_to_orders' );
function add_product_name_column_to_orders( $columns ) {
    $new_columns = array();

    // Add 'order-number' first
    if ( isset( $columns['order-number'] ) ) {
        $new_columns['order-number'] = $columns['order-number'];
    }

    // Add 'Product Name' column in the second position
    $new_columns['product-name'] = __( 'Challenge Name', 'megatrader' );

    // Add the rest of the columns after 'Product Name'
    foreach ( $columns as $key => $column ) {
        if ( $key !== 'order-number' ) {
            $new_columns[ $key ] = $column;
        }
    }

    return $new_columns;
}

// Populate the 'Product Name' column in the orders table
add_action( 'woocommerce_my_account_my_orders_column_product-name', 'populate_product_category_and_tags_in_orders' );

function populate_product_category_and_tags_in_orders( $order ) {
    // Get the products in the order
    $items = $order->get_items();

    foreach ( $items as $item_id => $item ) {
        $_product = $item->get_product();

        if ( $_product ) {
            $terms = get_the_terms($_product->get_id(), 'product_cat');
            $is_activation_fee = false;
            $is_reset_fee = false;
            if ($terms && !is_wp_error($terms)) {
                foreach ($terms as $term) {
                    if ($term->slug === 'activation-fee') {
                        $is_activation_fee = true;
                        break;
                    }
                    if ($term->slug === 'reset-fee') {
                        $is_reset_fee = true;
                        break;
                    }

                }
            }

            if ($is_activation_fee) {
                echo '<div>' . esc_html( $_product->get_title() ) . '</div>';
            } else if ($is_reset_fee) {
                echo '<div>' . esc_html( $_product->get_title() ) . '</div>';
            } else {
                echo '<div>' . esc_html( $_product->get_attribute('pa_account-size') . ' - ' . $_product->get_attribute('pa_platform') ) . '</div>';
            }
        }
    }
}



function customize_my_account_menu_items( $items ) {
    $items['dashboard'] = 'Home';
    $items['orders'] = 'Orders History';
    $items['subscriptions'] = 'Manage Subscription';
    $items['downloads'] = 'Download Files';
    $items['edit-address'] = 'Billing Details';
    $items['edit-account'] = 'Account Settings';
    $items['customer-logout'] = 'Sign Out';

    return $items;
}
add_filter( 'woocommerce_account_menu_items', 'customize_my_account_menu_items' );


function output_buffered_subscriptions_content() {
    if ( is_wc_endpoint_url( 'subscriptions' ) ) {
        $content = ob_get_clean();
        echo $content;
    }
}
add_action( 'woocommerce_account_subscriptions_endpoint', 'output_buffered_subscriptions_content', 15 );


add_filter('woocommerce_my_account_my_orders_columns', 'move_order_status_column', 10, 1);

function move_order_status_column($columns) {
    // Remove the 'order-status' column
    if (isset($columns['order-status'])) {
        $status_column = $columns['order-status'];
        unset($columns['order-status']);
    }

    // Add the 'order-status' column at the end
    $columns['order-status'] = $status_column;

    return $columns;
}


add_filter('woocommerce_my_account_my_orders_columns', 'rename_order_total_to_amount', 10, 1);

function rename_order_total_to_amount($columns) {
    // Rename the 'order-number' column to 'ID'
    if (isset($columns['order-number'])) {
        $columns['order-number'] = __('ID', 'woocommerce');
    }

    // Rename the 'order-total' column to 'Amount'
    if (isset($columns['order-total'])) {
        $columns['order-total'] = __('Amount', 'woocommerce');
    }

    return $columns;
}



function add_product_name_column_to_subscriptions_table($columns) {
    // Add a new column after the subscription ID column
    $new_columns = array();

    foreach ($columns as $key => $column) {
        $new_columns[$key] = $column;

        if ('subscription_id' === $key) {
            // Add the product name column after subscription ID
            $new_columns['product_name'] = __('Product Name', 'woocommerce');
        }
    }

    return $new_columns;
}
add_filter('woocommerce_my_subscriptions_columns', 'add_product_name_column_to_subscriptions_table');

function show_product_name_in_subscription_column($subscription) {
    // Get the items (products) in the subscription
    $items = $subscription->get_items();

    // Loop through each item and display the product name
    foreach ($items as $item_id => $item) {
        $product_name = $item->get_name();
        echo esc_html($product_name);
    }
}
add_action('woocommerce_my_subscriptions_column_product_name', 'show_product_name_in_subscription_column');


add_filter( 'woocommerce_save_account_details_required_fields', 'remove_required_fields_from_edit_account' );

function remove_required_fields_from_edit_account( $required_fields ) {
    unset( $required_fields['account_first_name'] );
    unset( $required_fields['account_last_name'] );
    unset( $required_fields['account_email'] );

    return $required_fields;
}

// Allow SVG file uploads
// Allow SVG file uploads
function allow_svg_uploads($mime_types) {
    $mime_types['svg'] = 'image/svg+xml';
    return $mime_types;
}
add_filter('upload_mimes', 'allow_svg_uploads');

// Security: Sanitize SVG files during upload
function sanitize_svg_on_upload($data, $file, $filename, $mimes = null) {
    $filetype = wp_check_filetype($filename, $mimes);
    if ($filetype['ext'] === 'svg') {
        // Disable Real MIME type checking for SVG
        add_filter('wp_check_filetype_and_ext', function($data, $file, $filename, $mimes) {
            $wp_filetype = wp_check_filetype($filename, $mimes);
            $ext = $wp_filetype['ext'];
            $type = $wp_filetype['type'];
            $proper_filename = $data['proper_filename'];
            return compact('ext', 'type', 'proper_filename');
        }, 10, 4);
    }
    return $data;
}
add_filter('wp_check_filetype_and_ext', 'sanitize_svg_on_upload', 10, 4);  // Changed to 4

add_filter( 'email_change_email', 'custom_megatrader_email_changed_template', 10, 3 );
function custom_megatrader_email_changed_template( $email, $user, $userdata ) {
    $username = isset($user['display_name']) && !empty($user['display_name'])
        ? esc_html($user['display_name'])
        : esc_html($user['user_login']);

    $new_email = sanitize_email( $userdata['user_email'] );
    $recipient_email = sanitize_email( $user['user_email'] ); // email original

    $email['subject'] = '[MEGATRADER] Email Changed';
    $email['message'] = '
    <div style="background: url(https://subscriptions.megatrader.io/wp-content/uploads/2025/05/dot-bg.png) 0% 0% / 5px 5px repeat, #131210; padding: 40px;">
        <table style="max-width:600px; margin:auto; background-color:#1e1e1e; border-radius:8px; font-family:\'Space Grotesk\', Arial, sans-serif; color:#A8A29E;">
            <tr>
                <td style="background-color:#F1A035; padding:20px; text-align:center;">
                    <h2 style="margin:0; color:#000;">Email Address Change Confirmation</h2>
                </td>
            </tr>
            <tr>
                <td style="padding:30px; font-size:14px;">
                    <p>Hi <strong>' . $username . '</strong>,</p>
                    <p>This notice confirms that your email address on <strong>MEGATRADER</strong> was changed to 
                        <a href="mailto:' . esc_html($new_email) . '" style="color:#F1A035;">' . esc_html($new_email) . '</a>.
                    </p>
                    <p>If you did not change your email, please contact the Site Administrator at 
                        <a href="mailto:admin@megatrader.io" style="color:#F1A035;">admin@megatrader.io</a>.
                    </p>
                    <p>This email has been sent to 
                        <a href="mailto:' . $recipient_email . '" style="color:#F1A035;">' . $recipient_email . '</a>.
                    </p>
                    <p>Thanks for using <strong>MEGATRADER</strong>.</p>
                </td>
            </tr>
        </table>
    </div>';

    return $email;
}


add_filter( 'retrieve_password_title', 'megatrader_custom_reset_subject', 10, 2 );
function megatrader_custom_reset_subject( $title, $user_login ) {
    return '[MEGATRADER] Password Reset Request';
}

add_filter( 'retrieve_password_message', 'megatrader_custom_reset_message', 10, 4 );
function megatrader_custom_reset_message( $message, $key, $user_login, $user_data ) {
    $site_name = 'MEGATRADER';
    $display_name = esc_html( $user_data->display_name );
    $reset_url = home_url( '/wp-login.php?action=rp&key=' . $key . '&login=' . rawurlencode( $user_login ) );

    $message = '
    <div style="background: url(https://subscriptions.megatrader.io/wp-content/uploads/2025/05/dot-bg.png) 0% 0% / 5px 5px repeat, #131210; padding: 40px;">
        <table style="max-width:600px; margin:auto; background-color:#1e1e1e; border-radius:8px; font-family:\'Space Grotesk\', Arial, sans-serif; color:#A8A29E;">
            <tr>
                <td style="background-color:#F1A035; padding:20px; text-align:center;">
                    <h2 style="margin:0; color:#000;">Password Reset Request</h2>
                </td>
            </tr>
            <tr>
                <td style="padding:30px; font-size:14px;">
                    <p>Hi <strong>' . $display_name . '</strong>,</p>
                    <p>Someone has requested a password reset for the following account:</p>
                    <p><strong>Site Name:</strong> ' . esc_html($site_name) . '</p>
                    <p>If this was a mistake, you can safely ignore this email and nothing will happen.</p>
                    <p>To reset your password, click the button below:</p>
                    <p style="text-align:center; margin: 30px 0;">
                        <a href="' . esc_url($reset_url) . '" 
                           style="background-color:#F1A035; color:#000; text-decoration:none; padding:12px 24px; border-radius:6px; font-weight:bold; display:inline-block;">
                           Reset Password
                        </a>
                    </p>
                    <p style="font-size:12px;">Or copy and paste this link into your browser:</p>
                    <p style="font-size:12px;"><a href="' . esc_url($reset_url) . '" style="color:#F1A035;">' . esc_html($reset_url) . '</a></p>
                    <p>Thanks for using <strong>' . esc_html($site_name) . '</strong>.</p>
                </td>
            </tr>
        </table>
    </div>';

    return $message;
}

function enqueue_preloader_script() {
  wp_enqueue_script('preloader', get_template_directory_uri() . '/assets/js/preloader.js', array('jquery'), time(), true);
}
add_action('wp_enqueue_scripts', 'enqueue_preloader_script');

add_action('wp_enqueue_scripts', function () {
    // Desactiva el loader de WooCommerce (blockUI)
    wp_add_inline_script('woocommerce', '
        jQuery(document).ready(function ($) {
            $.fn.block = function() { return this; };
            $.fn.unblock = function() { return this; };
        });
    ');

    // Oculta el spinner de WooCommerce con CSS
    wp_add_inline_style('woocommerce-inline', '
        .woocommerce .blockUI.blockOverlay,
        .woocommerce .blockUI.blockOverlay::before,
        .woocommerce .blockUI.blockOverlay::after {
            display: none !important;
            opacity: 0 !important;
            visibility: hidden !important;
        }
    ');
}, 100);
function enqueue_thankyou_validation_script() {
    if (is_checkout()) {
        wp_enqueue_script(
            'thankyou-modal"',
            get_stylesheet_directory_uri() . '/assets/js/thankyou-modal.js"',
            array(),
            time(),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'enqueue_thankyou_validation_script');


function enqueue_coupon_message_script() {
    if (is_checkout()) {
        wp_enqueue_script(
            'coupon-message-handler',
            get_template_directory_uri() . '/assets/js/coupon-message-handler.js',
            array('jquery'),
            time(),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'enqueue_coupon_message_script');


function enqueue_nmi_validation_script() {
    if (is_checkout()) {
        wp_enqueue_script(
            'nmi-validation',
            get_stylesheet_directory_uri() . '/assets/js/nmi-validation.js',
            array(),
            time(),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'enqueue_nmi_validation_script');

function enqueue_stripe_validation_script() {
    if (is_checkout()) {
        wp_enqueue_script(
            'stripe-validation',
            get_stylesheet_directory_uri() . '/assets/js/stripe-validation.js',
            array(),
            time(),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'enqueue_stripe_validation_script');

/**
 * Enqueue account.js on WooCommerce My Account pages
 */
function mt_enqueue_myaccount_script() {
    // Only load on My Account (and its endpoints)
    if ( function_exists('is_account_page') && is_account_page() ) {
        wp_enqueue_script(
            'mt-account',
            get_stylesheet_directory_uri() . '/assets/js/account.js',
            [],      // no dependencies
            filemtime( get_stylesheet_directory() . '/assets/js/account.js' ), // version by file mtime
            true     // load in footer
        );
    }
}
add_action( 'wp_enqueue_scripts', 'mt_enqueue_myaccount_script' );

/**
 * Enqueue mt-account-overview.js on WooCommerce My Account Overview
 */
function mt_enqueue_overview_script_path_only() {
  $req_path = rtrim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
  if ($req_path !== '/my-account/overview') return;

  $candidates = [
    [ get_stylesheet_directory(), get_stylesheet_directory_uri() ],
    [ get_template_directory(),   get_template_directory_uri()   ],
  ];

  foreach ($candidates as [$dir, $uri]) {
    $file = $dir . '/assets/js/mt-account-overview.js';
    if (file_exists($file)) {
      wp_enqueue_script(
        'mt-account-overview',
        $uri . '/assets/js/mt-account-overview.js',
        ['jquery'],
        filemtime($file),
        true
      );
      wp_localize_script('mt-account-overview', 'mtAccounts', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('mt-acc-nonce'),
      ]);
      return;
    }
  }
}
add_action('wp_enqueue_scripts', 'mt_enqueue_overview_script_path_only', 101);

require_once get_template_directory() . '/inc/auth-hooks.php';
require_once get_template_directory() . '/inc/register-hooks.php';
require_once get_template_directory() . '/inc/lost-password-hooks.php';

add_filter( 'gettext', 'custom_change_cvc_label', 20, 3 );
function custom_change_cvc_label( $translated_text, $text, $domain ) {
	if ( $text === 'Card Code (CVC)' ) {
		return 'Card Code';
	}
    if ( $text === 'Save to account' ) {
		return 'Save payment method to account';
	}
	return $translated_text;
}

function mt_intl_tel_input_assets () {
    wp_enqueue_style(
            'intl-tel-input-css',
            'https://cdn.jsdelivr.net/npm/intl-tel-input@16.0.3/build/css/intlTelInput.css'
    );

    wp_enqueue_script(
            'intl-tel-input-js',
            'https://cdn.jsdelivr.net/npm/intl-tel-input@16.0.3/build/js/intlTelInput.min.js',
            array(),
            null,
            true
    );

    wp_enqueue_script(
            'intl-tel-utils',
            'https://cdn.jsdelivr.net/npm/intl-tel-input@16.0.3/build/js/utils.js',
            array(),
            null,
            true
    );
}

function enqueue_intl_tel_input_assets() {
    
    $target_slugs = array( 
        'overview', 
    );
    if ( function_exists( 'is_checkout' ) ) {
        if ( is_checkout() || is_page( $target_slugs ) ) {
            
            mt_intl_tel_input_assets();
        }
    }
}
add_action('wp_enqueue_scripts', 'enqueue_intl_tel_input_assets');


add_filter( 'wc_stripe_upe_params', function( $p ) {
  $commonFocusStyle = (object)[
    'outline'          => '0',
    'WebkitBoxShadow'  => 'none',
    'boxShadow'        => 'none',
    'borderColor'      => '#FFB34A',
    'backgroundColor'  => 'rgba(30, 30, 30, 0.7)',
  ];

  $appearance = (object) [
    'rules' => (object) [

      // Label
      '.Label' => (object)[
        'paddingLeft'   => '0px',
        'paddingBottom' => '8px',
        'fontFamily'    => 'Roboto',
        'fontSize'      => '20px',
        'fontStyle'     => 'normal',
        'fontWeight'    => '300',
        'lineHeight'    => '32px',
        'color'         => '#ffffff',
      ],

      // Input base
      '.Input' => (object)[
        'height'           => '48px',
        'lineHeight'       => '48px',
        'padding'          => '0 20px 0 20px',
        'border'           => '1px solid #404040',
        'color'            => '#A8A29E',
        'backgroundColor'  => 'rgba(30, 30, 30, 0.7)',
        'borderRadius'     => '12px',
        'fontSize'         => '16px',
        'fontWeight'       => '500',
        'width'            => '100%',
        'fontFamily'       => 'Roboto, sans-serif',
        'WebkitTransition' => '0.3s ease-in-out',
        'transition'       => '0.3s ease-in-out',
        'position'         => 'relative',
        'zIndex'           => '2',
      ],

      // Hover
      '.Input:hover' => (object)[
        'borderColor'     => '#ffffff',
        'backgroundColor' => 'rgba(30, 30, 30, 0.7)',
      ],

      // Focus
      '.Input:focus' => $commonFocusStyle,

      // Active
      '.Input:active' => $commonFocusStyle,

    ],
  ];

  $p['appearance'] = $appearance;
  $p['blocksAppearance'] = $appearance;
  return $p;
});


add_action('init', function () {
  delete_transient('wc_stripe_appearance');
  delete_transient('wc_stripe_blocks_appearance');
});

function enqueue_swiper_assets() {
    // CSS
    wp_enqueue_style(
        'swiper-css',
        'https://cdnjs.cloudflare.com/ajax/libs/Swiper/10.0.4/swiper-bundle.min.css',
        [],
        '10.0.4'
    );

    // JS
    wp_enqueue_script(
        'swiper-js',
        'https://cdnjs.cloudflare.com/ajax/libs/Swiper/10.0.4/swiper-bundle.min.js',
        [],
        '10.0.4',
        true
    );

    // Tu inicialización
    wp_enqueue_script(
        'swiper-init',
        get_template_directory_uri() . '/assets/js/swiper-init.js',
        ['swiper-js'],
        null,
        true
    );
}
add_action( 'wp_enqueue_scripts', 'enqueue_swiper_assets' );


/* --- Checkout - Display Only Default Payment Method --- */
add_filter( 'wc_payment_gateway_form_saved_payment_methods_html', 'custom_show_only_default_payment_method', 10, 2 );

function custom_show_only_default_payment_method( $html, $gateway ) {
    global $current_subscription_token_id;

    $available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
    $tokens = $gateway->get_tokens();

    if ( empty( $tokens ) ) {
        return $html;
    }

    if ( isset($_GET['pay_for_order']) &&
         isset($_GET['key']) &&
         isset($_GET['change_payment_method'])) {

         if ( $current_subscription_token_id ) {
            $tokens = array_filter( $tokens, function( $token ) use ( $current_subscription_token_id ) {
                return $token->get_id() !== $current_subscription_token_id;
            });
        }

        $token_count = count( $tokens );
        $output = '<ul class="woocommerce-SavedPaymentMethods wc-saved-payment-methods" data-count="' . $token_count . '">';
        foreach ( $tokens as $token ) {
            $output .= $gateway->get_saved_payment_method_option_html( $token );
        }
        $output .= $gateway->get_new_payment_method_option_html(); // option to add new card
        $output .= '</ul>';

        return $output;

    } else {
        // Find the default token
        $default_token = null;
        foreach ( $tokens as $token ) {
            if ( method_exists( $token, 'is_default' ) && $token->is_default() ) {
                $default_token = $token;
                break;
            }
        }

        // Fallback: first token for gateway
        if ( ! $default_token ) {
            // $default_token = reset( $tokens ); //grabs first token

            // Sort tokens descending by ID
            usort( $tokens, function( $a, $b ) {
                return $b->get_id() - $a->get_id();
            });

            $default_token = $tokens[0] ?? null;

            if ( $default_token ) {
                WC_Payment_Tokens::set_users_default( get_current_user_id(), $default_token->get_id() );
            }
        }

        if ( ! $default_token ) {
            return $html;
        }

        // Generate custom HTML for just the default token
        $output  = '<ul class="woocommerce-SavedPaymentMethods wc-saved-payment-methods default-only" data-count="1">';
        $output .= $gateway->get_saved_payment_method_option_html( $default_token );
        $output .= $gateway->get_new_payment_method_option_html(); // Keep the option to add a new method
        $output .= '</ul>';

        return $output;
    }
}

/* --- Checkout - Adjust Saved Payment Method DOM --- */
add_filter( 'woocommerce_payment_gateway_get_saved_payment_method_option_html', 'custom_saved_payment_method_li_html', 10, 3 );

function custom_saved_payment_method_li_html( $html, $token, $gateway ) {
    $is_change_payment_method = isset($_GET['pay_for_order']) && isset($_GET['key']) && isset($_GET['change_payment_method']);

    // Get card info
    $brand_raw = $token->get_card_type();
	$brand     = wc_get_credit_card_type_label( $brand_raw );
    // $brand     = ucfirst( $token->get_card_type() );
    $last4     = $token->get_last4();
    $exp_month = $token->get_expiry_month();
    $exp_year  = $token->get_expiry_year();
    $is_default = $token->is_default();

    // IDs WooCommerce expects
    $input_id = 'wc-' . esc_attr( $gateway->id ) . '-payment-token-' . esc_attr( $token->get_id() );
    $input_name = 'wc-' . esc_attr( $gateway->id ) . '-payment-token';

    $change_url = wc_get_account_endpoint_url( 'payment-methods' );

    ob_start(); ?>
    <li class="woocommerce-SavedPaymentMethods-token">
        <input
            id="<?php echo esc_attr( $input_id ); ?>"
            type="radio"
            name="<?php echo esc_attr( $input_name ); ?>"
            value="<?php echo esc_attr( $token->get_id() ); ?>"
            class="woocommerce-SavedPaymentMethods-tokenInput"
            <?php checked( $is_default ); ?>
        >
        <label for="<?php echo esc_attr( $input_id ); ?>" class="saved-cc-label">
            <div class="saved-cc-content">
                <div class="saved-cc-body">
                    <div class="saved-cc-number">
                        <span class="saved-cc-brand"><?php echo esc_html( "$brand" ); ?></span>
                        <span class="saved-cc-mask"><?php echo esc_html( "••••" ); ?></span>
                        <span class="saved-cc-last4"><?php echo esc_html( "$last4" ); ?></span>
                    </div>
                    <div class="saved-cc-exp-date">Expires <?php echo esc_html( "$exp_month/$exp_year" ); ?></div>
                    <?php if ( $is_default && ! $is_change_payment_method) : ?>
                        <a class="saved-cc-change" href="<?php echo esc_url( $change_url ); ?>">Change Default Card</a>
                    <?php endif; ?>
                </div>
                <?php if ( $is_default ) : ?>
                    <div class="saved-cc-badge badge-mega badge-mega-md badge-mega-secondary">DEFAULT</div>
                <?php endif; ?>
            </div>
        </label>
    </li>
    <?php

    return ob_get_clean();
}

/* --- My Account - Payment Methods = Replace "Delete" with "Remove" from Action Btn --- */

add_filter( 'woocommerce_payment_methods_list_item', function ( $item, $payment_token ) {
	if ( isset( $item['actions']['delete']['name'] ) ) {
		$item['actions']['delete']['name'] = __( 'Remove', 'woocommerce' );
	}
	return $item;
}, 10, 2 );

/* --- My Account - Payment Methods = Include Token_Id to Saved_Methods List --- */

add_filter( 'woocommerce_payment_methods_list_item', function( $item, $payment_token ) {
    $item['method']['subs_count'] = count( WCS_Payment_Tokens::get_subscriptions_from_token( $payment_token ) );
	$item['method']['token_id'] = $payment_token->get_id();
	return $item;
}, 20, 2 );

/* --- My Account - Payment Methods = Add AJAX Endpoint for AddPaymentMethod --- */
add_action( 'wp_ajax_get_add_payment_method_form', 'load_add_payment_method_form' );
add_action( 'wp_ajax_nopriv_get_add_payment_method_form', 'load_add_payment_method_form' );

function load_add_payment_method_form() {
	// Set proper headers
	wp_send_json_success([
		'html' => wc_get_template_html( 'myaccount/form-add-payment-method.php', array(), '', WC()->template_path() )
	]);
}

/* --- Subscriptions - Reactivate ---- */

add_action( 'template_redirect', 'gg_reactivate_subscription_endpoint', 5 );
function gg_reactivate_subscription_endpoint() {
    if (
        is_user_logged_in() &&
        is_account_page() &&
        get_query_var('view-subscription') &&
        isset( $_GET['wcs_reactivate_subscription'], $_GET['_wpnonce'] )
    ) {
        $subscription_id = absint( get_query_var('view-subscription') );
        if (
            absint( $_GET['wcs_reactivate_subscription'] ) === $subscription_id &&
            wp_verify_nonce( $_GET['_wpnonce'], 'wcs_reactivate_subscription_' . $subscription_id )
        ) {
            $subscription = wcs_get_subscription( $subscription_id );
            $now          = current_time( 'timestamp' );
            $end_date     = $subscription->get_time( 'end' );
            if (
                $subscription &&
                $subscription->get_user_id() === get_current_user_id() &&
                $subscription->has_status( 'pending-cancel' ) &&
                $end_date > $now
            ) {
                $subscription->update_status(
                    'active',
                    __( 'Subscription reactivated manually.', 'woocommerce-subscriptions' )
                );
                $redirect_url = wc_get_endpoint_url(
                    'view-subscription',
                    $subscription_id,
                    wc_get_page_permalink( 'myaccount' )
                );
                wp_safe_redirect( $redirect_url );
                exit;
            }
        }
    }
}


/* --- Checkout - Save Card Text ---- */

add_filter( 'wc_stripe_save_to_account_text', function( $text ) {
    return __( 'Save payment method to account', 'megatrader' );
} );

/* --------- MODAL Render Function -------- */

if (!function_exists('render_modal')) {
    function render_modal( $args = [] ) {
        $args = wp_parse_args( $args, [
            'notice'      => null,
            'autoshow'    => null,
            'modalType'   => '',
            'modalId'     => 'mtModal_' . wp_generate_password(8, false, false),
            'modalTitle'  => '',
            'imageSrc'    => null,
            'imageClass'  => null,
            'bodyContent' => null,
            'footer'      => null,
            'labelId'     => null,
            'closeHref'   => 'javascript:void(0);',
        ] );

        ob_start();
        include locate_template( 'template-parts/modal.php' );
        return ob_get_clean();
    }
}

/* --------- TABS Render Function -------- */

if (!function_exists('render_tabs')) {
    function render_tabs($tabs, $selected_id = null) {
        set_query_var('tabs', $tabs);
        set_query_var('selected_id', $selected_id);
        get_template_part('template-parts/mt-tabs');
    }
}

if (!function_exists('load_tab_content')) {
    function load_tab_content($template_path, LayoutType $layoutType = LayoutType::MyAccount): ?string {
        if (!locate_template($template_path . '.php')) {
            return null;
        }

        ob_start();
        get_template_part($template_path, null, ['layoutType' => $layoutType]);;
        return ob_get_clean();
    }
}


/* --------- Step Selector Render Function -------- */

if (!function_exists('render_step')) {
    function render_step_selector($step) {
        set_query_var('step', $step);
        get_template_part('template-parts/step-selector');
    }
}


/* --------- Step Selector Render Function -------- */

if (!function_exists('render_sidebar')) {
    function render_sidebar() {
        get_template_part('template-parts/sidebar');
    }
}

/**** Block Subscription list page */

add_action( 'template_redirect', 'mt_redirect_subscriptions_endpoint', 1 );
function mt_redirect_subscriptions_endpoint() {
    if ( ! function_exists( 'is_wc_endpoint_url' ) || ! is_wc_endpoint_url( 'subscriptions' ) ) {
        return;
    }

    if ( ! is_user_logged_in() ) {
        return;
    }

    $user_id       = get_current_user_id();
    $myaccount_url = wc_get_page_permalink( 'myaccount' );
    $redirect_url  = wc_get_endpoint_url( 'orders', '', $myaccount_url );

    $latest = wc_get_orders( [
        'customer_id' => $user_id,
        'limit'       => 1,
        'orderby'     => 'date',
        'order'       => 'DESC',
        'return'      => 'ids',
    ] );

    if ( ! empty( $latest ) ) {
        $order_id = $latest[0];

        if ( function_exists( 'wcs_get_subscriptions_for_order' ) ) {
            $subs = wcs_get_subscriptions_for_order( $order_id, [ 'order_type' => 'any' ] );
            if ( ! empty( $subs ) ) {
                $sub_id      = reset( $subs )->get_id();
                $redirect_url = wc_get_endpoint_url( 'view-subscription', $sub_id, $myaccount_url );
            } else {
                $redirect_url = wc_get_endpoint_url( 'view-order', $order_id, $myaccount_url );
            }
        } else {
            $redirect_url = wc_get_endpoint_url( 'view-order', $order_id, $myaccount_url );
        }
    }

    wp_safe_redirect( $redirect_url );
    exit;
}

add_action( 'template_redirect', 'mt_redirect_my_account_orders' );
function mt_redirect_my_account_orders() {
    // Sólo para usuarios logueados en el endpoint “orders” de My Account
    if ( ! is_user_logged_in() || ! is_wc_endpoint_url( 'orders' ) ) {
        return;
    }

    $user_id = get_current_user_id();

    // 1) Obtener todas las órdenes del usuario (procesando/completadas)
    $orders = wc_get_orders( array(
        'customer_id' => $user_id,
        'status'      => array( 'wc-processing', 'wc-completed' ),
        'limit'       => -1,
        'orderby'     => 'date',
        'order'       => 'DESC',
    ) );

    // 2) Filtrar las órdenes que **no** sean sólo “reset fee” o “activation fee”
    $valid_orders = array_filter( $orders, function( $order ) {
        foreach ( $order->get_items() as $item ) {
            $product = $item->get_product();
            if ( ! $product ) {
                continue;
            }
            $slug = $product->get_slug();
            // Si encuentro un producto distinto de estos fees, la orden es válida
            if ( ! in_array( $slug, array( 'reset-fee', 'activation-fee' ), true ) ) {
                return true;
            }
        }
        return false;
    } );

    // 3) Si no hay órdenes “válidas”, dejamos que cargue la página normal de Orders
    if ( empty( $valid_orders ) ) {
        return;
    }

    /** @var WC_Order $last_order */
    $last_order = array_values( $valid_orders )[0];

    // 5) ¿Tiene suscripción relacionada? (WooCommerce Subscriptions)
    if ( function_exists( 'wcs_get_subscriptions_for_order' ) ) {
        $subs = wcs_get_subscriptions_for_order( $last_order, array( 'order_type' => 'any' ) );
    } else {
        $subs = array();
    }

    if ( ! empty( $subs ) ) {
        // Redirigir a la página de esa suscripción
        $sub = reset( $subs );
        $url = wc_get_endpoint_url( 'view-subscription', $sub->get_id(), wc_get_page_permalink( 'myaccount' ) );
    } else {
        // Redirigir a la vista de orden individual
        $url = wc_get_endpoint_url( 'view-order', $last_order->get_id(), wc_get_page_permalink( 'myaccount' ) );
    }

    wp_safe_redirect( $url );
    exit;
}


// === Guardar billing via AJAX ===
add_action('wp_ajax_mt_save_billing_profile', 'mt_save_billing_profile_cb');

function mt_save_billing_profile_cb() {
    if ( ! check_ajax_referer('mt_save_billing', 'nonce', false) ) {
        wp_send_json_error(['message' => __('Invalid security token.', 'your-txt')], 403);
    }

    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        wp_send_json_error(['message' => __('You must be logged in to save billing details.', 'your-txt')], 401);
    }

    $in = wp_unslash($_POST); 
    $val = function($key, $type = 'text') use ($in) {
        $v = isset($in[$key]) ? $in[$key] : '';
        if ($type === 'email') return sanitize_email($v);
        return sanitize_text_field($v);
    };

    $customer = new WC_Customer($user_id);
    $customer->set_billing_first_name( $val('billing_first_name') );
    $customer->set_billing_last_name ( $val('billing_last_name')  );
    $customer->set_billing_email     ( $val('billing_email','email') );
    $customer->set_billing_phone     ( $val('billing_phone') );
    $customer->set_billing_address_1 ( $val('billing_address_1') );
    $customer->set_billing_address_2 ( $val('billing_address_2') );
    $customer->set_billing_city      ( $val('billing_city') );
    $customer->set_billing_state     ( $val('billing_state') );
    $customer->set_billing_postcode  ( $val('billing_postcode') );
    $customer->set_billing_country   ( strtoupper($val('billing_country')) );
    $customer->save();

    $country   = $customer->get_billing_country();
    $state     = $customer->get_billing_state();
    $countries = wc()->countries;
    $country_name = $countries->countries[ $country ] ?? $country;
    $state_name   = $countries->states[ $country ][ $state ] ?? $state;

    wp_send_json_success([
        'country_name' => $country_name,
        'state_name'   => $state_name,
    ]);
}
// Remove Notices 
add_action( 'template_redirect', function () {
    if ( ! function_exists( 'wc_get_notices' ) || ! WC()->session ) {
        return;
    }

    $notices = wc_get_notices();
    if ( empty( $notices ) ) {
        return;
    }

    $needles = array(
        'subscription has been removed from your cart',
        'products and subscriptions can not be purchased at the same time',
    );

    $changed = false;

    foreach ( array( 'error', 'notice', 'success' ) as $type ) {
        if ( empty( $notices[ $type ] ) || ! is_array( $notices[ $type ] ) ) {
            continue;
        }
        foreach ( $notices[ $type ] as $i => $entry ) {
            // Cada notice puede venir como string o como array con la key 'notice'
            $msg = is_array( $entry ) && isset( $entry['notice'] ) ? $entry['notice'] : $entry;
            $msg_l = strtolower( wp_strip_all_tags( (string) $msg ) );

            $hits = 0;
            foreach ( $needles as $needle ) {
                if ( $needle !== '' && strpos( $msg_l, $needle ) !== false ) {
                    $hits++;
                }
            }
            if ( $hits >= 2 || strpos( $msg_l, $needles[0] ) !== false ) {
                unset( $notices[ $type ][ $i ] );
                $changed = true;
            }
        }
        if ( $changed ) {
            $notices[ $type ] = array_values( $notices[ $type ] );
        }
    }

    if ( $changed ) {
        WC()->session->set( 'wc_notices', $notices );
    }
}, 0 ); 


add_action('template_redirect', function () {
  if ( is_user_logged_in() ) {
    nocache_headers();  
    return;
  };

  if ( is_admin() && ! wp_doing_ajax() ) return;
  if ( wp_doing_ajax() || wp_doing_cron() ) return;

  $path = strtolower( trailingslashit( parse_url( $_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH ) ?: '/' ) );

  // Allow REST API 
  if ( strpos($path, '/wp-json/') === 0 ) return;

  // ====== White List ======
  $public_paths = [
    '/',              // Home
    '/auth/login/',   // login Page
    '/auth/register/',   // Register Page
    '/auth/lost-password/',   // Lost Password Page
  ];

  $public_paths = apply_filters('mt_public_paths', $public_paths, $path);

  if ( ! in_array($path, $public_paths, true) ) {

    $login_url = home_url('/auth/login/');
    wp_safe_redirect( $login_url, 302 );
    exit;
  }
}, 0);

// Render del HTML del modal de éxito
add_action('wp_ajax_nopriv_mt_render_order_success_modal', 'mt_render_order_success_modal');
add_action('wp_ajax_mt_render_order_success_modal', 'mt_render_order_success_modal');

function mt_render_order_success_modal() {
  // Validación de nonce
  if ( ! isset($_POST['nonce']) || ! wp_verify_nonce( $_POST['nonce'], 'mt_render_order_success_modal' ) ) {
    wp_send_json_error( array( 'message' => 'Invalid request (nonce).' ), 403 );
  }

  // Obtener order_id (con fallback por order_key)
  $order_id = isset($_POST['order_id']) ? absint($_POST['order_id']) : 0;
  if ( ! $order_id && ! empty($_POST['order_key']) ) {
    $order_id = wc_get_order_id_by_order_key( sanitize_text_field( wp_unslash( $_POST['order_key'] ) ) );
  }
  if ( ! $order_id ) {
    wp_send_json_error( array( 'message' => 'Missing order_id.' ) );
  }

  // Cargar la orden
  $order = wc_get_order( $order_id );
  if ( ! $order ) {
    wp_send_json_error( array( 'message' => 'Order not found.' ) );
  }

  // Hacer accesible la orden dentro del template (dos formas)
  $GLOBALS['mt_order'] = $order; // por si el template la usa como global
  // y también como variable local $order disponible en el include

  // Localizar template por ruta absoluta (child theme → parent theme)
  $tpl = trailingslashit( get_stylesheet_directory() ) . 'template-parts/order-success-modal.php';
  if ( ! file_exists( $tpl ) ) {
    $tpl = trailingslashit( get_template_directory() ) . 'template-parts/order-success-modal.php';
  }
  if ( ! file_exists( $tpl ) ) {
    wp_send_json_error( array( 'message' => 'Template not found', 'path' => $tpl ) );
  }

  // Render del template
  ob_start();
  include $tpl; // $order disponible aquí
  $html = ob_get_clean();

  if ( ! $html || ! trim( $html ) ) {
    wp_send_json_error( array( 'message' => 'Empty modal HTML (check template)', 'path' => $tpl ) );
  }

  wp_send_json_success( array( 'html' => $html ) );
}

// === Feature Content AJAX ===
add_action('wp_ajax_mt_account_feature_content', 'mt_ajax_account_feature_content');
add_action('wp_ajax_nopriv_mt_account_feature_content', 'mt_ajax_account_feature_content');

function mt_ajax_account_feature_content() {
  $nonce = $_POST['nonce'] ?? '';
  if ( ! wp_verify_nonce($nonce, 'mt-acc-nonce') ) {
    wp_send_json_error(['message' => 'Invalid nonce'], 403);
  }

  $accountId = sanitize_text_field((string)($_POST['accountId'] ?? ''));
  if ($accountId === '') {
    wp_send_json_error(['message' => 'Missing accountId'], 400);
  }

  // Resolver cuenta
  $acc = function_exists('mt_accounts_resolve_account_by_id')
    ? mt_accounts_resolve_account_by_id($accountId)
    : null;

  // Payload para el template
  $feature = [];
  if ($acc) {
    $feature['account'] = $acc;
    if (function_exists('mt_accounts_build_feature_content')) {
      $feature['apiData'] = mt_accounts_build_feature_content($acc);
    }
  }

  ob_start();
  get_template_part(
    'template-parts/account/account-feature-content',
    null,
    [
      'meta'    => ['accountId' => $accountId],
      'feature' => $feature,
    ]
  );
  $html = ob_get_clean();

  wp_send_json_success(['html' => $html]);
}

// === Performance Chart AJAX ===
add_action('wp_ajax_mt_account_performance_chart', 'mt_ajax_account_performance_chart');
add_action('wp_ajax_nopriv_mt_account_performance_chart', 'mt_ajax_account_performance_chart');
function mt_ajax_account_performance_chart() {
  $nonce = $_POST['nonce'] ?? '';
  if (!wp_verify_nonce($nonce, 'mt-acc-nonce')) {
    wp_send_json_error(['message' => 'Invalid nonce'], 403);
  }
  $accountId = sanitize_text_field((string)($_POST['accountId'] ?? ''));
  if ($accountId === '') {
    wp_send_json_error(['message' => 'Missing accountId'], 400);
  }

  $acc = function_exists('mt_accounts_resolve_account_by_id') ? mt_accounts_resolve_account_by_id($accountId) : null;
  $chart = [];
  if ($acc && function_exists('mt_accounts_build_performance_chart')) {
    $chart = mt_accounts_build_performance_chart($acc);
  }

  ob_start();
  get_template_part(
    'template-parts/account/account-performance-chart',
    null,
    [
      'meta'  => ['accountId' => $accountId],
      'chart' => $chart,   // <— payload con series/green/red
    ]
  );
  $html = ob_get_clean();

  wp_send_json_success(['html' => $html]);
}

// === AJAX: Account Data  ===
add_action('wp_ajax_mt_account_data', 'mt_account_data_ajax');
add_action('wp_ajax_nopriv_mt_account_data', 'mt_account_data_ajax');

function mt_account_data_ajax() {
  try {
    if (!check_ajax_referer('mt-acc-nonce', 'nonce', false)) {
      wp_send_json_error(['message' => 'Invalid nonce'], 403);
    }
    $accountId = isset($_POST['accountId']) ? sanitize_text_field(wp_unslash($_POST['accountId'])) : '';
    if ($accountId === '') wp_send_json_error(['message' => 'Missing accountId'], 400);

    if (!function_exists('mt_accounts_resolve_account_by_id')) wp_send_json_error(['message' => 'Resolver missing'], 500);
    $acc = mt_accounts_resolve_account_by_id($accountId);
    if (!$acc) wp_send_json_error(['message' => 'Account not found'], 404);

    $payload = function_exists('mt_accounts_build_account_data') ? mt_accounts_build_account_data($acc) : [];

    ob_start();
    get_template_part('template-parts/account/account-data', null, [
      'meta' => ['accountId' => $accountId],
      'data' => $payload,
    ]);
    $html = ob_get_clean();

    wp_send_json_success(['html' => $html]);
  } catch (Throwable $e) {
    if (defined('WP_DEBUG') && WP_DEBUG) error_log('[MT][account_data_ajax] '.$e->getMessage());
    wp_send_json_error(['message' => 'Server error'], 500);
  }
}

// ===== MT Daily Feedback (tabla + AJAX) =====
add_action('after_setup_theme', function () {
    global $wpdb;
    $table = $wpdb->prefix . 'mt_daily_feedback';
    $charset = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table (
      id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      user_id BIGINT UNSIGNED NOT NULL,
      account_id BIGINT UNSIGNED NOT NULL,
      trade_date DATE NOT NULL,
      mood TINYINT UNSIGNED NOT NULL DEFAULT 0,           -- 1..5
      followed_plan TINYINT(1) NOT NULL DEFAULT 0,        -- 0/1
      note TEXT NULL,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      UNIQUE KEY uniq_user_account_date (user_id, account_id, trade_date),
      KEY idx_account_date (account_id, trade_date)
    ) $charset;";
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
});

// Helper para leer feedback de una fecha
function mt_get_daily_feedback($user_id, $account_id, $trade_date) {
    global $wpdb;
    $table = $wpdb->prefix . 'mt_daily_feedback';
    return $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table WHERE user_id=%d AND account_id=%d AND trade_date=%s",
        $user_id, $account_id, $trade_date
    ), ARRAY_A);
}

// AJAX: guardar feedback
add_action('wp_ajax_mt_save_daily_feedback', function () {
    if (!is_user_logged_in()) wp_send_json_error(['msg' => 'Auth required'], 401);

    // Usa el mismo nonce que ya localizas (mtAccounts.nonce)
    if (empty($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'mt-acc-nonce')) {
        wp_send_json_error(['msg' => 'Bad nonce'], 403);
    }

    $user_id     = get_current_user_id();
    $account_id  = isset($_POST['account_id']) ? absint($_POST['account_id']) : 0;
    $trade_date  = isset($_POST['trade_date']) ? sanitize_text_field($_POST['trade_date']) : '';
    $mood        = max(1, min(5, intval($_POST['mood'] ?? 0)));
    $followed    = isset($_POST['followed_plan']) && $_POST['followed_plan'] == '1' ? 1 : 0;
    $note        = isset($_POST['note']) ? wp_kses_post(wp_unslash($_POST['note'])) : '';

    if (!$account_id || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $trade_date)) {
        wp_send_json_error(['msg' => 'Bad data'], 400);
    }

    global $wpdb;
    $table = $wpdb->prefix . 'mt_daily_feedback';
    $data = [
        'user_id' => $user_id,
        'account_id' => $account_id,
        'trade_date' => $trade_date,
        'mood' => $mood,
        'followed_plan' => $followed,
        'note' => $note,
    ];
    $format = ['%d','%d','%s','%d','%d','%s'];

    // UPSERT
    $exists = mt_get_daily_feedback($user_id, $account_id, $trade_date);
    if ($exists) {
        $ok = $wpdb->update($table, $data, ['id' => $exists['id']], $format, ['%d']);
    } else {
        $ok = $wpdb->insert($table, $data, $format);
    }

    if ($ok === false) wp_send_json_error(['msg' => 'DB error'], 500);

    wp_send_json_success([
        'saved' => true,
        'payload' => $data,
    ]);
});

// Billing completo (solo lectura) debajo del bloque Billing en Admin > Pedido

add_action('woocommerce_checkout_order_processed', function($order_id, $posted){
    $o = wc_get_order($order_id);
    $map = [
      'billing_first_name' => 'set_billing_first_name',
      'billing_last_name'  => 'set_billing_last_name',
      'billing_company'    => 'set_billing_company',
      'billing_address_1'  => 'set_billing_address_1',
      'billing_address_2'  => 'set_billing_address_2',
      'billing_city'       => 'set_billing_city',
      'billing_state'      => 'set_billing_state',
      'billing_postcode'   => 'set_billing_postcode',
      'billing_country'    => 'set_billing_country',
      'billing_email'      => 'set_billing_email',
      'billing_phone'      => 'set_billing_phone',
    ];
    foreach($map as $k=>$setter){
        if (isset($posted[$k])) $o->$setter( wc_clean( wp_unslash($posted[$k]) ) );
    }
    $o->save();

    // Log de verificación (puedes quitarlo cuando confirmes)
    error_log('BILLING SAVED: ' . wp_json_encode([
      'address_1' => $o->get_billing_address_1(),
      'city'      => $o->get_billing_city(),
      'state'     => $o->get_billing_state(),
      'postcode'  => $o->get_billing_postcode(),
      'country'   => $o->get_billing_country(),
    ]));
}, 999, 2);


// Cargar scripts de Checkout (p.ej. Address Autocomplete) también en My Account > Overview
add_filter('woocommerce_is_checkout', function ($is_checkout) {
    if ($is_checkout) {
        return true;
    }

    // Detecta el endpoint /my-account/overview/ (o una página "overview" si no es endpoint)
    $on_overview_endpoint = function_exists('is_account_page') && is_account_page()
        && function_exists('is_wc_endpoint_url') && is_wc_endpoint_url('overview');

    $on_overview_page = function_exists('is_page') && is_page('overview');

    return ($on_overview_endpoint || $on_overview_page) ? true : $is_checkout;
}, 9);


