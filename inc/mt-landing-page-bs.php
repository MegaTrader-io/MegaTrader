<?php
defined('ABSPATH') || exit;

/**
 * Encola los assets del template landing-page-bs.php
 */
add_action('wp_enqueue_scripts', function () {
    if (
        !is_front_page()
    ) {
        return;
    }

  $theme_dir = get_stylesheet_directory();
  $theme_uri = get_stylesheet_directory_uri();

  $css_path = get_template_directory() . '/assets/css/';
  $js_path = get_template_directory() . '/assets/js/';
  $css_uri = get_template_directory_uri() . '/assets/css/';
  $js_uri = get_template_directory_uri() . '/assets/js/';

  $css_version = file_exists($css_path . 'swiper-bundle.min.css') ? filemtime($css_path . 'swiper-bundle.min.css') : null;
  $js_version = file_exists($js_path . 'swiper-bundle.min.js') ? filemtime($js_path . 'swiper-bundle.min.js') : null;

  wp_enqueue_style('swiper-bundle', $css_uri . 'swiper-bundle.min.css', [], $css_version);
  wp_enqueue_script('swiper-bundle-style', $js_uri . 'swiper-bundle.min.js', [], $js_version, true);

  $css_version = file_exists($css_path . 'glide.core.min.css') ? filemtime($css_path . 'glide.core.min.css') : null;
  $js_version = file_exists($js_path . 'glide.js') ? filemtime($js_path . 'glide.js') : null;

  wp_enqueue_style('glide-style', $css_uri . 'glide.core.min.css', [], $css_version);
  wp_enqueue_script('glide-js', $js_uri . 'glide.js', [], $js_version, true);

  $css_version = file_exists($css_path . 'splide.min.css') ? filemtime($css_path . 'splide.min.css') : null;
  $js_version = file_exists($js_path . 'splide.min.js') ? filemtime($js_path . 'splide.min.js') : null;

  wp_enqueue_style('splide-style', $css_uri . 'splide.min.css', [], $css_version);
  wp_enqueue_script('splide-js', $js_uri . 'splide.min.js', [], $js_version, true);

  $js_version = file_exists($js_path . 'splide-extension-auto-scroll.min.js') ? filemtime($js_path . 'splide-extension-auto-scroll.min.js') : null;
  wp_enqueue_script('splide-extension-js', $js_uri . 'splide-extension-auto-scroll.min.js', [], $js_version, true);


  $css_version = file_exists($css_path . 'glide.theme.min.css') ? filemtime($css_path . 'glide.theme.min.css') : null;
  wp_enqueue_style('glide-theme-style', $css_uri . 'glide.theme.min.css', [], $css_version);


  $css_file = $theme_dir . '/assets/css/landing-page-bs.css';
  $js_file = $theme_dir . '/assets/js/landing-page-bs.js';

  // Versionado seguro y reproducible
  $css_ver = file_exists($css_file) ? @filemtime($css_file) : '1.0.0';
  $js_ver = file_exists($js_file) ? @filemtime($js_file) : '1.0.0';

  $clipboard_js_version = file_exists($js_path . 'clipboard.min.js') ? filemtime($js_path . 'clipboard.min.js') : null;
  wp_enqueue_script('clipboard', $js_uri . 'clipboard.min.js', [], $clipboard_js_version, true);

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

  $products_data = get_products_with_attributes();
  $products_with_best_coupons = [];

  // Pasar datos al JS (opcional)
  wp_localize_script('mt-landing-page-bs', 'MG_GLOBAL', [
    'adminAjaxApi' => admin_url('admin-ajax.php'),
    'baseApi' => esc_url_raw(rest_url('megatrader/v1')),
    'nonce' => wp_create_nonce('wp_rest'),
    'subscriptionNonce' => wp_create_nonce('subscription_action'),
    'products' => $products_data['products'] ?? [],
    'productsWithBestCoupons' => $products_with_best_coupons ?? [],
    'productMetaLabel' => Label::PRODUCT_META,
    'isUserLoggedIn' => is_user_logged_in(),
    'CHECKOUT_URL' => home_url('/checkout/?add-to-cart=PRODUCT_ID'),
    'GO_TO_URL' => home_url('/auth/register/?redirect_to='),
    'bestProducts' => mt_most_popular_products()
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
