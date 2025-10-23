<?php
defined('ABSPATH') || exit;

/**
 * Encola los assets del template landing-page-bs.php
 */
add_action('wp_enqueue_scripts', function () {
  // ✅ Asegura que solo se ejecuta en ese template
  global $post;

  if (empty($post) || get_page_template_slug($post->ID) !== 'landing-page-bs.php') {
    return;
  }

  $theme_dir = get_stylesheet_directory();
  $theme_uri = get_stylesheet_directory_uri();

  $css_file = $theme_dir . '/assets/css/landing-page-bs.css';
  $js_file = $theme_dir . '/assets/js/landing-page-bs.js';

  // Versionado seguro y reproducible
  $css_ver = file_exists($css_file) ? @filemtime($css_file) : '1.0.0';
  $js_ver = file_exists($js_file) ? @filemtime($js_file) : '1.0.0';

  wp_enqueue_style(
    'mt-landing-page-bs',
    $theme_uri . '/assets/css/landing-page-bs.css',
    [],
    $css_ver
  );

  wp_enqueue_script(
    'mt-landing-page-bs',
    $theme_uri . '/assets/js/landing-page-bs.js',
    ['jquery'],
    $js_ver,
    true
  );

  // Pasar datos al JS (opcional)
  wp_localize_script('mt-landing-page-bs', 'MT_LANDING_PAGE_BS', [
    'ajaxUrl' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('mt_landing_nonce'),
    'isLogged' => is_user_logged_in(),
  ]);
}, 20);


/**
 * (Opcional) Evita que el template se cachee
 */
add_action('send_headers', function () {
  if (is_page_template('landing-page-bs.php')) {
    nocache_headers();
  }
});
