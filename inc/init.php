<?php
defined('ABSPATH') || exit;

$base = trailingslashit( get_stylesheet_directory() ) . 'inc/';
// Si los helpers están en el parent, usa template_directory como fallback:
if ( ! file_exists( $base . 'mt-api.php' ) ) {
    $base = trailingslashit( get_template_directory() ) . 'inc/';
}

$api     = $base . 'mt-api.php';
$helpers = $base . 'mt-accounts-helpers.php';

if ( file_exists( $api ) ) {
    require_once $api;
    error_log('[MT] Loaded: ' . $api);
} else {
    error_log('[MT] MISSING: ' . $api);
}

if ( file_exists( $helpers ) ) {
    require_once $helpers;
    error_log('[MT] Loaded: ' . $helpers);
} else {
    error_log('[MT] MISSING: ' . $helpers);
}
