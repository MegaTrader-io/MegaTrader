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
header("Cache-Control: private, must-revalidate");

get_header('landing-page');

require 'template-parts/landing-page/main.php';

get_footer('landing-page');
