<?php
/**
 * My Account page
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @version 3.5.0
 */

defined('ABSPATH') || exit;

?>

<div class="pt-32">
    <div class="overflow-hidden">
        <div class="container">
            <div class="two-columns my-account flex-column flex-lg-row m-auto">
                <div class="two-columns__col">
                    <?php render_sidebar(); ?>
                </div>
                <div class="two-columns__col">
                    <div class="mega-navigation mb-3">
                        <?php do_action('woocommerce_account_navigation'); ?>
                    </div>

                    <div class="woocommerce-MyAccount-content">
                        <?php do_action('woocommerce_account_content'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>