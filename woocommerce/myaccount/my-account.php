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
            <div class="mt-page__main-header">
                <div class="mt-page__title text-white text-size-20 fw-medium text-uppercase">
                    <?php echo Label::META_ACCOUNT_OVERVIEW['page_title_overview']; ?>
                </div>
                <span class="mt-page__subtitle text-a8a29e text-14px-line-20px fw-medium">
                    <?php echo Label::META_ACCOUNT_OVERVIEW['page_subtitle_overview']; ?>
                </span>
            </div>
            <div class="mb-3">
                <?php account_navigation_render(); ?>
            </div>

            <div class="woocommerce-MyAccount-content">
                <?php do_action('woocommerce_account_content'); ?>
            </div>
        </div>
    </div>
</div>