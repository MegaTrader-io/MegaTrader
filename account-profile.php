<?php
/**
 * Template Name: Account Profile
 */

defined('ABSPATH') || exit;

get_header();

?>

<div id="mt-account-profile" class="container"> 
<div class="mt-page">
    <div class="mt-page__sidebar">
      <?php if (function_exists('render_sidebar')) {
        render_sidebar();
      } ?>
    </div>
    <div class="mt-page__main d-flex flex-column gap-3">
     </div>

</div>

 <?php get_footer(); ?>