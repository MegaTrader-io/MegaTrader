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
error_log('[LV] before get_header(landing-page)');
get_header('landing-page');
error_log('[LV] after get_header(landing-page)');


error_log('[LV] before template-parts/landing-page/main.php');
require 'template-parts/landing-page/main.php';
error_log('[LV] after template-parts/landing-page/main.php');


error_log('[LV] before get_footer(landing-page)');
get_footer('landing-page');
error_log('[LV] after get_footer(landing-page)');
