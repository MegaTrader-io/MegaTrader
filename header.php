<?php
/**
 * The header for our theme
 *
 * @package megatrader
 */

$current_path = trailingslashit(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

$auth_paths = [
        '/auth/login/',
        '/auth/register/',
        '/auth/lost-password/',
];

$is_checkout_flow = is_page_template('page-subscriptions.php') ||
  function_exists('is_checkout') && is_checkout()
  || function_exists('is_order_received_page') && is_order_received_page();

?>
    <!DOCTYPE html>
<html <?php language_attributes(); ?>>

    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
              rel="stylesheet">
        <?php wp_head(); ?>
        <style>
        :root {
            --font-roboto: 'Roboto', sans-serif;
            --mgt-dark: #1e1e1e;
            --mgt-color-primary: #FFB34A;
            --mgt-color-teal: #14B8A6;
            --mgt-color-error: #FB7185;
            --mgt-color-link: #FFD78A;
            --mgt-color-link-hover: var(--mgt-color-primary);
        }
        </style>
    </head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

    <div class="preloader">
        <div class="preloader-inner">
            <span class="loader"></span>
        </div>
    </div>

<?php if (!in_array($current_path, $auth_paths, true)): ?>

  <?php
  if ($is_checkout_flow) {
    Mt_Navbar::render_navbar_bs(
      section: 'checkout',
      classes_navbar: 'mt-navbar--checkout mt-navbar__links--scrolled'
    );
  } else {
    Mt_Navbar::render_navbar_bs(
      section: 'account',
      classes_navbar: 'mt-navbar--my-account'
    );
  }
  ?>

<?php endif; ?>