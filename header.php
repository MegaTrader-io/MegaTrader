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
    Mt_Navbar::render_navbar(
            section: 'account',
            classes_navbar: 'mt-navbar--my-account'
    );
    ?>
<?php endif; ?>