<?php
/**
 * Template Name: Home Page
 * 
 * The template for displaying front page
 * The Home template file
 *
 *
 * @package megatrader
 */

nocache_headers();

$default_redirect_to = is_user_logged_in() ? profile_url(user_email: wp_get_current_user()->user_email): home_url('/auth/login');

wp_safe_redirect($default_redirect_to);
exit;