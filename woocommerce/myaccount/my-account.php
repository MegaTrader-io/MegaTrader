<?php
/**
 * My Account page
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @version 3.5.0
 */

defined('ABSPATH') || exit;

?>
<div class="container">
    <div class="mt-page">
        <div class="mt-page__sidebar">
            <?php render_sidebar(); ?>
        </div>
        <div class="mt-page__main">
            <div class="mega-navigation mb-3">
                <?php do_action('woocommerce_account_navigation'); ?>
            </div>

            <div class="woocommerce-MyAccount-content">
                <?php do_action('woocommerce_account_content'); ?>
            </div>
        </div>
    </div>
</div>