<?php
/**
 * Sample implementation of the Custom Header feature
 *
 * You can add an optional custom header image to header.php like so ...
 *
 * <?php the_header_image_tag(); ?>
 *
 * @link https://developer.wordpress.org/themes/functionality/custom-headers/
 *
 * @package megatrader
 */

/**
 * Set up the WordPress core custom header feature.
 *
 * @uses megatrader_header_style()
 */
function megatrader_custom_header_setup()
{
    add_theme_support(
            'custom-header',
            apply_filters(
                    'megatrader_custom_header_args',
                    array(
                            'default-image' => '',
                            'default-text-color' => '000000',
                            'width' => 1000,
                            'height' => 250,
                            'flex-height' => true,
                            'wp-head-callback' => 'megatrader_header_style',
                    )
            )
    );
}

add_action('after_setup_theme', 'megatrader_custom_header_setup');

if (!function_exists('megatrader_header_style')) :
    /**
     * Styles the header image and text displayed on the blog.
     *
     * @see megatrader_custom_header_setup().
     */
    function megatrader_header_style()
    {
        $header_text_color = get_header_textcolor();

        /*
         * If no custom options for text are set, let's bail.
         * get_header_textcolor() options: Any hex value, 'blank' to hide text. Default: add_theme_support( 'custom-header' ).
         */
        if (get_theme_support('custom-header', 'default-text-color') === $header_text_color) {
            return;
        }

        // If we get this far, we have custom styles. Let's do this.
        ?>
        <style type="text/css">
            <?php
            // Has the text been hidden?
            if ( ! display_header_text() ) :
                ?>
            .site-title,
            .site-description {
                position: absolute;
                clip: rect(1px, 1px, 1px, 1px);
            }

            <?php
            // If the user has set a custom color for the text use that.
        else :
            ?>
            .site-title a,
            .site-description {
                color: #<?php echo esc_attr( $header_text_color ); ?>;
            }

            <?php endif; ?>
        </style>
        <?php
    }
endif;


if (!function_exists('mt_render_user_section')) {
    function mt_render_user_section()
    {
        $current_user = wp_get_current_user();
        // Retrieve first and last name from user meta
        $first_name = get_user_meta($current_user->ID, 'first_name', true);
        $last_name = get_user_meta($current_user->ID, 'last_name', true);

        // Determine display name: prefer "First Last", fallback to WordPress display_name
        if ($first_name || $last_name) {
            $display_name = trim($first_name . ' ' . $last_name);
        } else {
            $display_name = $current_user->display_name;
        }

        // Get billing country code and map to full country name
        $billing_country_code = get_user_meta($current_user->ID, 'billing_country', true);
        $countries = WC()->countries->countries;
        $billing_country = isset($countries[$billing_country_code])
                ? $countries[$billing_country_code]
                : '';

        // User email address
        $user_email = $current_user->user_email;

        // Build uppercase initials from first and last name
        $initials = '';
        if ($first_name) {
            $initials .= mb_substr($first_name, 0, 1);
        }
        if ($last_name) {
            $initials .= mb_substr($last_name, 0, 1);
        }
        $initials = mb_strtoupper($initials);

        /// Genera la URL de Gravatar con default=404
        $avatar_url = get_avatar_url($current_user->ID, [
                'size' => 64,
                'default' => '404',
        ]);

        // Intenta hacer una petición HEAD para validar existencia real
        $response = wp_safe_remote_head($avatar_url, [
                'timeout' => 2,
        ]);

        $has_real_avatar = false;
        if (!is_wp_error($response)) {
            $code = wp_remote_retrieve_response_code($response);
            // DEBUG: loguea el código HTTP
            error_log("Gravatar HTTP status for user {$current_user->ID}: {$code}");
            if (200 === $code) {
                $has_real_avatar = true;
            }
        }

        $logout_url = wp_logout_url();

        return <<<HTML

HTML;

    }
}