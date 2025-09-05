<?php
/**
 * Thankyou page
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.1.0
 */

defined('ABSPATH') || exit;
?>

<div class="woocommerce-order pb-30 pt-32">
    <div class="container">
        <div class="top-menu">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="actived">
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.1333 20.1334L21.5333 10.7334L19.6667 8.86669L12.1333 16.4L8.33332 12.6L6.46666 14.4667L12.1333 20.1334ZM14 27.3334C12.1555 27.3334 10.4222 26.9834 8.79999 26.2834C7.17777 25.5834 5.76666 24.6334 4.56666 23.4334C3.36666 22.2334 2.41666 20.8222 1.71666 19.2C1.01666 17.5778 0.666656 15.8445 0.666656 14C0.666656 12.1556 1.01666 10.4222 1.71666 8.80002C2.41666 7.1778 3.36666 5.76669 4.56666 4.56669C5.76666 3.36669 7.17777 2.41669 8.79999 1.71669C10.4222 1.01669 12.1555 0.666687 14 0.666687C15.8444 0.666687 17.5778 1.01669 19.2 1.71669C20.8222 2.41669 22.2333 3.36669 23.4333 4.56669C24.6333 5.76669 25.5833 7.1778 26.2833 8.80002C26.9833 10.4222 27.3333 12.1556 27.3333 14C27.3333 15.8445 26.9833 17.5778 26.2833 19.2C25.5833 20.8222 24.6333 22.2334 23.4333 23.4334C22.2333 24.6334 20.8222 25.5834 19.2 26.2834C17.5778 26.9834 15.8444 27.3334 14 27.3334Z" fill="#FFB34A"/>
                </svg>
            </a>
            <span class="actived">
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.1333 20.1334L21.5333 10.7334L19.6667 8.86669L12.1333 16.4L8.33332 12.6L6.46666 14.4667L12.1333 20.1334ZM14 27.3334C12.1555 27.3334 10.4222 26.9834 8.79999 26.2834C7.17777 25.5834 5.76666 24.6334 4.56666 23.4334C3.36666 22.2334 2.41666 20.8222 1.71666 19.2C1.01666 17.5778 0.666656 15.8445 0.666656 14C0.666656 12.1556 1.01666 10.4222 1.71666 8.80002C2.41666 7.1778 3.36666 5.76669 4.56666 4.56669C5.76666 3.36669 7.17777 2.41669 8.79999 1.71669C10.4222 1.01669 12.1555 0.666687 14 0.666687C15.8444 0.666687 17.5778 1.01669 19.2 1.71669C20.8222 2.41669 22.2333 3.36669 23.4333 4.56669C24.6333 5.76669 25.5833 7.1778 26.2833 8.80002C26.9833 10.4222 27.3333 12.1556 27.3333 14C27.3333 15.8445 26.9833 17.5778 26.2833 19.2C25.5833 20.8222 24.6333 22.2334 23.4333 23.4334C22.2333 24.6334 20.8222 25.5834 19.2 26.2834C17.5778 26.9834 15.8444 27.3334 14 27.3334Z" fill="#FFB34A"/>
                </svg>
            </span>
            <span class="active">3</span>
        </div>

        <?php if ( $order ): ?>

            <?php
            do_action('woocommerce_before_thankyou', $order->get_id());
            ?>

            <?php if ( $order->has_status('failed') ): ?>

                <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed">
                    <?php esc_html_e('Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce'); ?>
                </p>

                <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
                    <a href="<?php echo esc_url($order->get_checkout_payment_url()); ?>" class="button pay"><?php esc_html_e('Pay', 'woocommerce'); ?></a>
                    <?php if ( is_user_logged_in() ): ?>
                        <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="button pay"><?php esc_html_e('My account', 'woocommerce'); ?></a>
                    <?php endif; ?>
                </p>

            <?php else: ?>

                <div class="thankyou-info">
                    <div class="text-center mb-15">
                        <img fetchpriority="high" decoding="async" class="d-none d-sm-inline-block" src="https://subscriptions.megatrader.io/wp-content/themes/megatrader-addons/assets/img/thank-you.png" alt="thank you" width="690" height="132">
                        <img decoding="async" class="d-inline-block d-sm-none" src="https://subscriptions.megatrader.io/wp-content/themes/megatrader-addons/assets/img/thank-you2.png" alt="thank you" width="327" height="132">
                    </div>
                    <h2 class="mb-3">Amazing !</h2>
                    <h3 class="box-title">Congratulations! You've got covered</h3>
                    <p class="thanks-text">Your trading account have been created successfully</p>
                    <p class="order-number">Order Number : <?php echo esc_html( $order->get_order_number() ); ?></p>
                    <p class="thanks-text order-email"><?php echo esc_html( $order->get_billing_email() ); ?></p>
                    <!-- <a href="https://megatrader.io" class="ot-btn">Beging trading</a> -->
                </div>

                <?php
                if ( $order ) {
                    // Loop principal existente
                    foreach ( $order->get_items() as $item_id => $item ) {
                        $product = $item->get_product();
                        if ( $product ) {

                            $product_image = wp_get_attachment_image_url( $product->get_image_id(), 'medium' );
                            $product_title = $product->get_name();
                            $product_short_description = $product->get_short_description();

                            $plan_attr = $product->get_attribute('pa_account-types');
                            if ( ! empty($plan_attr) ) {
                                $plan_term = get_term_by('name', $plan_attr, 'pa_account-types');
                                if ( $plan_term && isset($plan_term->term_id) ) {
                                    $image_id = get_term_meta($plan_term->term_id, 'attribute_image_id', true);
                                    $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
                                }
                            }

                            $size_attr = $product->get_attribute('pa_account-size');
                            if ( ! empty($size_attr) ) {
                                $size_term = get_term_by('name', $size_attr, 'pa_account-size');
                                if ( $size_term && isset($size_term->term_id) ) {
                                    $size_attr_slug = $size_term->slug;
                                    $size_description = $size_term->description;
                                }
                            }

                            $platform_attr = $product->get_attribute('pa_platform');
                            if ( ! empty($platform_attr) ) {
                                $platform_term = get_term_by('name', $platform_attr, 'pa_platform');
                                if ( $platform_term && isset($platform_term->term_id) ) {
                                    $platform_image_id = get_term_meta($platform_term->term_id, 'attribute_image_id', true);
                                    $platform_image_url = $platform_image_id ? wp_get_attachment_url($platform_image_id) : '';
                                    $platform_description = $platform_term->description;
                                }
                            }

                            $terms = get_the_terms( $product->get_id(), 'product_cat' );
                            $is_activation_fee = false;
                            $is_reset_fee = false;
                            if ( $terms && ! is_wp_error($terms) ) {
                                foreach ( $terms as $term ) {
                                    if ( $term->slug === 'activation-fee' ) { $term_description = $term->description; $is_activation_fee = true; break; }
                                    if ( $term->slug === 'reset-fee' ) { $term_description = $term->description; $is_reset_fee = true; break; }
                                }
                            }

                            $tags = get_the_terms( $product->get_id(), 'product_tag' );
                            $tag = ! empty($tags) ? $tags[0] : null;
                            if ( $tag ) { $tagName = str_replace('$', '', $tag->name); }

                            if ( $is_activation_fee ) { ?>
                                <div class="order-product-wrap">
                                    <div class="order-plan">
                                        <div class="box-icon"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/diamond.svg" alt="Icon" width="36" height="36"></div>
                                        <h4 class="box-title"><?php echo esc_html($product_title . ' - Activation Fee'); ?></h4>
                                    </div>
                                    <div class="order-product">
                                        <span class="icon">
                                            <svg width="58" height="57" viewBox="0 0 58 57" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M0.5 28.5C0.5 12.7599 13.2599 0 29 0C44.7401 0 57.5 12.7599 57.5 28.5C57.5 44.2401 44.7401 57 29 57C13.2599 57 0.5 44.2401 0.5 28.5Z" fill="#F1A035" />
                                                <mask id="mask0_11289_3680" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="14" y="13" width="31" height="31"><rect x="14.5" y="13.5" width="30" height="30" fill="#D9D9D9" /></mask>
                                                <g mask="url(#mask0_11289_3680)"><path d="M29.5 41C27.7708 41 26.1458 40.6719 24.625 40.0156C23.1042 39.3594 21.7812 38.4688 20.6562 37.3438C19.5312 36.2188 18.6406 34.8958 17.9844 33.375C17.3281 31.8542 17 30.2292 17 28.5C17 26.75 17.3281 25.1198 17.9844 23.6094C18.6406 22.099 19.5312 20.7812 20.6562 19.6562L22.4063 21.4063C21.4896 22.3229 20.776 23.3854 20.2656 24.5938C19.7552 25.8021 19.5 27.1042 19.5 28.5C19.5 31.2917 20.4688 33.6562 22.4063 35.5938C24.3438 37.5312 26.7083 38.5 29.5 38.5C32.2917 38.5 34.6562 37.5312 36.5938 35.5938C38.5312 33.6562 39.5 31.2917 39.5 28.5C39.5 27.1042 39.2448 25.8021 38.7344 24.5938C38.224 23.3854 37.5104 22.3229 36.5938 21.4063L38.3438 19.6562C39.4688 20.7812 40.3594 22.099 41.0156 23.6094C41.6719 25.1198 42 26.75 42 28.5C42 30.2292 41.6719 31.8542 41.0156 33.375C40.3594 34.8958 39.4688 36.2188 38.3438 37.3438C37.2188 38.4688 35.8958 39.3594 34.375 40.0156C32.8542 40.6719 31.2292 41 29.5 41ZM28.25 29.75V16H30.75V29.75H28.25Z" fill="black"/></g>
                                            </svg>
                                        </span>
                                        <div class="box-content">
                                            <h4 class="box-title">Activation Fee
                                                <b><?php echo esc_html( wc_price( $product->get_price() ) ); ?> / One Time</b>
                                            </h4>
                                            <p class="box-text"><?php echo esc_html($term_description); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php } elseif ( $is_reset_fee ) { ?>
                                <div class="order-product-wrap">
                                    <div class="order-plan">
                                        <div class="box-icon"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/diamond.svg" alt="Icon" width="36" height="36"></div>
                                        <h4 class="box-title"><?php echo esc_html($product_title . ' - Reset Fee'); ?></h4>
                                    </div>
                                    <div class="order-product">
                                        <span class="icon">
                                            <svg width="58" height="57" viewBox="0 0 58 57" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M0.5 28.5C0.5 12.7599 13.2599 0 29 0C44.7401 0 57.5 12.7599 57.5 28.5C57.5 44.2401 44.7401 57 29 57C13.2599 57 0.5 44.2401 0.5 28.5Z" fill="#F1A035" />
                                                <mask id="mask0_11289_3949" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="14" y="13" width="31" height="31"><rect x="14.5" y="13.5" width="30" height="30" fill="#D9D9D9"/></mask>
                                                <g mask="url(#mask0_11289_3949)"><path d="M29.5 38.5C26.7083 38.5 24.3438 37.5313 22.4063 35.5938C20.4687 33.6563 19.5 31.2917 19.5 28.5C19.5 25.7083 20.4687 23.3438 22.4063 21.4063C24.3438 19.4687 26.7083 18.5 29.5 18.5C30.9375 18.5 32.3125 18.7969 33.625 19.3906C34.9375 19.9844 36.0625 20.8333 37 21.9375V18.5H39.5V27.25H30.75V24.75H36C35.3333 23.5833 34.4219 22.6667 33.2656 22C32.1094 21.3333 30.8542 21 29.5 21C27.4167 21 25.6458 21.7292 24.1875 23.1875C22.7292 24.6458 22 26.4167 22 28.5C22 30.5833 22.7292 32.3542 24.1875 33.8125C25.6458 35.2708 27.4167 36 29.5 36C31.1042 36 32.5521 35.5417 33.8438 34.625C35.1354 33.7083 36.0417 32.5 36.5625 31H39.1875C38.6042 33.2083 37.4167 35.0104 35.625 36.4062C33.8333 37.8021 31.7917 38.5 29.5 38.5Z" fill="black"/></g>
                                            </svg>
                                        </span>
                                        <div class="box-content">
                                            <h4 class="box-title">Reset Fee <b><?php echo esc_html( wc_price( $product->get_price() ) ); ?> / One Time</b></h4>
                                            <p class="box-text"><?php echo esc_html($term_description); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="order-product-wrap">
                                    <?php if ( $plan_attr ): ?>
                                        <div class="order-plan">
                                            <div class="box-icon">
                                                <?php if ( ! empty($image_url) ): ?>
                                                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($plan_attr); ?>" width="36" height="36">
                                                <?php endif; ?>
                                            </div>
                                            <h4 class="box-title"><?php echo esc_html($plan_attr); ?></h4>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ( $platform_attr ): ?>
                                        <div class="order-product">
                                            <?php if ( ! empty($platform_image_url) ): ?>
                                                <div class="box-img">
                                                    <img src="<?php echo esc_url($platform_image_url); ?>" alt="<?php echo esc_attr($platform_attr); ?>" width="57" height="57">
                                                </div>
                                            <?php endif; ?>
                                            <div class="box-content">
                                                <h4 class="box-title"><?php echo esc_html($platform_attr); ?></h4>
                                                <p class="box-text"><?php echo esc_html($platform_description); ?></p>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ( $size_attr ): ?>
                                        <div class="order-product">
                                            <span class="icon"><?php echo esc_html( $size_attr_slug ); ?></span>
                                            <div class="box-content">
                                                <h4 class="box-title">
                                                    <?php echo esc_html( $size_attr_slug . ' Account' ); ?>
                                                    <b>
                                                        <?php echo esc_html( wc_price( $product->get_price() ) ); ?>
                                                        <?php echo ( $plan_attr == 'Funded Plan' ) ? '/ One Time' : '/ Month'; ?>
                                                    </b>
                                                </h4>
                                                <p class="box-text"><?php echo esc_html( $size_description ); ?></p>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php
                                    $order_id = get_query_var('order-received');
                                    if ( ! $order_id ) {
                                        global $wp;
                                        $order_id = absint( $wp->query_vars['order-received'] ?? 0 );
                                    }
                                    $order_for_addons = wc_get_order( $order_id );
                                    $order_add_ons = array();

                                    if ( $order_for_addons ) {
                                        foreach ( $order_for_addons->get_items('fee') as $fee_id => $fee ) {
                                            if ( empty( $fee->get_meta('_wc_checkout_add_on_id') ) ) {
                                                continue;
                                            }
                                            $add_on_id    = $fee->get_meta('_wc_checkout_add_on_id');
                                            $add_on_value = $fee->get_meta('_wc_checkout_add_on_value');
                                            $add_on_label = $fee->get_meta('_wc_checkout_add_on_label');
                                            $order_add_ons[$add_on_id] = array(
                                                'name'  => $fee->get_name(),
                                                'value' => $add_on_value,
                                                'label' => $add_on_label,
                                                'total' => $fee->get_total(),
                                                'fee_id'=> $fee_id,
                                            );
                                        }
                                    }

                                    $addon_configs = array(
                                        'drawdown_buffer' => array(
                                            'name' => 'Drawdown buffer',
                                            'icon' => 'drawdown-icon.svg',
                                            'description' => 'Add $500 to your trailing drawdown and extend your cushion, giving you more flexibility while managing trades and risk.'
                                        ),
                                        'anytime_payouts' => array(
                                            'name' => 'Anytime Payouts',
                                            'icon' => 'payout-icon.svg',
                                            'description' => 'Waive the 10 days trading rule and get paid as soon as you hit your consistency and profit targets.'
                                        )
                                    );

                                    if ( ! empty($order_add_ons) ) {
                                        foreach ( $order_add_ons as $add_on_id => $add_on_data ) {
                                            $add_on_values = $add_on_data['value'];
                                            if ( is_array($add_on_values) ) { ?>
                                                <div class="order-product d-flex flex-column gap-4">
                                                    <?php
                                                    $first = true;
                                                    foreach ( $add_on_values as $value_slug ) {
                                                        $config_key = str_replace('-', '_', $value_slug);
                                                        if ( isset($addon_configs[$config_key]) ) {
                                                            $config = $addon_configs[$config_key];
                                                            $price  = wc_price( $add_on_data['total'] );
                                                            ?>
                                                            <div class="order-product-wrapper d-flex gap-3 align-items-center">
                                                                <div class="box-img">
                                                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/<?php echo esc_attr( $config['icon'] ); ?>" alt="<?php echo esc_attr( $config['name'] ); ?> Icon" width="57" height="57">
                                                                </div>
                                                                <div class="box-content">
                                                                    <h4 class="box-title">
                                                                        <?php echo esc_html( $config['name'] ); ?>
                                                                        <?php if ( $first ): ?><b><?php echo $price; ?></b><?php endif; ?>
                                                                    </h4>
                                                                    <p class="box-text"><?php echo esc_html( $config['description'] ); ?></p>
                                                                </div>
                                                            </div>
                                                            <?php
                                                            $first = false;
                                                        }
                                                    } ?>
                                                </div>
                                            <?php }
                                        }
                                    }
                                    ?>
                                </div>
                            <?php } ?>

                            <?php
                        }
                    }
                } else {
                    echo '<p>Order not found.</p>';
                }
                ?>

                <div class="order-process-wrap">
                    <h4 class="box-title">What's next</h4>
                    <div class="order-process">
                        <div class="box-content">
                            <h5 class="title">Step 1 :</h5>
                            <p class="text">You'll quickly get an email to set your MegaTrader password. Check spam if it doesn't arrive.</p>
                        </div>
                        <div class="box-content">
                            <h5 class="title">Step 2 :</h5>
                            <p class="text">After setting your password, log in to access your dashboard and view platform credentials.</p>
                        </div>
                        <div class="box-content">
                            <h5 class="title">Step 3 :</h5>
                            <p class="text">Use the credentials to access the platform and start placing trades toward your payout.</p>
                        </div>
                    </div>
                </div>

                <div class="d-none">
                    <?php do_action('woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id()); ?>
                </div>

            <?php endif; ?>
        <?php else: ?>

            <p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received">
                <?php echo apply_filters('woocommerce_thankyou_order_received_text', __('Thank you. Your order has been received.', 'woocommerce'), null); ?>
            </p>

        <?php endif; ?>
    </div>
</div>

<?php
// =====================
//  MODAL (Bootstrap)
// =====================
if ( isset($order) && $order && ! $order->has_status('failed') ):

    // --- Datos para el modal ---
    $order_number = $order->get_order_number();
    $order_email  = $order->get_billing_email();

    // Producto principal (primer ítem no fee)
    $main_item_name  = '';
    $main_item_price = 0;
    foreach ( $order->get_items() as $it ) {
        $main_item_name = $it->get_name();
        $main_item_price = (float) $it->get_total();
        break;
    }

    // Add-ons (desde fees)
    $addons_names = array();
    $addons_total = 0.0;

    $addon_configs_lookup = array(
        'drawdown_buffer' => 'Drawdown buffer',
        'anytime_payouts' => 'Anytime Payouts',
    );

    foreach ( $order->get_items('fee') as $fee ) {
        $val = $fee->get_meta('_wc_checkout_add_on_value');
        $addons_total += (float) $fee->get_total();
        if ( is_array($val) ) {
            foreach ( $val as $slug ) {
                $key = str_replace('-', '_', $slug);
                $addons_names[] = $addon_configs_lookup[$key] ?? $fee->get_name();
            }
        } else {
            $addons_names[] = $fee->get_name();
        }
    }
    $addons_names = array_values( array_unique( array_filter($addons_names) ) );

    $total_paid = wc_price( $order->get_total() );

    // Método de pago: marca + last4
    $pm_title = $order->get_payment_method_title();
    $card_brand = '';
    $card_last4 = '';

    $tokens = $order->get_payment_tokens();
    if ( ! empty($tokens) ) {
        $tok = WC_Payment_Tokens::get( $tokens[0] );
        if ( $tok && method_exists($tok, 'get_last4') ) {
            $card_last4 = $tok->get_last4();
        }
        if ( $tok && method_exists($tok, 'get_brand') ) {
            $card_brand = $tok->get_brand();
        }
    }
    if ( ! $card_last4 ) {
        $meta_keys = array('stripe_last4','_stripe_card_last4','card_last4','last4','wc_payment_token_last4');
        foreach ( $meta_keys as $mk ) {
            $v = $order->get_meta($mk, true);
            if ( $v ) { $card_last4 = $v; break; }
        }
    }
    if ( ! $card_brand ) {
        $meta_keys_b = array('stripe_brand','_stripe_card_brand','card_brand','brand');
        foreach ( $meta_keys_b as $mk ) {
            $v = $order->get_meta($mk, true);
            if ( $v ) { $card_brand = $v; break; }
        }
    }
    $pm_suffix = $card_last4 ? sprintf('Ending in %s', esc_html($card_last4)) : esc_html($pm_title);
    ?>

<!-- Modal -->
<div class="modal fade" id="orderSuccessModal" tabindex="-1" aria-labelledby="orderSuccessLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down" style="--bs-modal-width: 600px;">
    <div class="modal-content align-items-center d-flex flex-column gap-4">

      <!-- Header -->
      <div class="modal-header w-100 border-0 justify-content-between align-items-start p-0">
        <h5 class="modal-title text-white heading-sm-medium" id="orderSuccessLabel">SUCCESS</h5>
        <button type="button" class="p-0 border-0 bg-transparent shadow-none" data-bs-dismiss="modal" aria-label="Close">
          <img src="https://subscriptions.megatrader.io/wp-content/uploads/2025/05/cancel-circle-1.png" alt="Close" style="width:24px;height:24px;" />
        </button>
      </div>

      <div class="modal-body d-flex flex-column align-items-center justify-content-center gap-3 w-100">

        <!-- Imágenes -->
        <div class="text-center w-100">
          <img fetchpriority="high" decoding="async"
               class="d-none d-sm-inline-block"
               src="https://subscriptions.megatrader.io/wp-content/themes/megatrader-addons/assets/img/thank-you.png"
               alt="thank you" width="690" height="132">
          <img decoding="async"
               class="d-inline-block d-sm-none"
               src="https://subscriptions.megatrader.io/wp-content/themes/megatrader-addons/assets/img/thank-you2.png"
               alt="thank you" width="327" height="132">
        </div>

        <!-- Títulos -->
        <div class="d-flex flex-column align-items-center gap-2">
          <div class="text-white text-uppercase fw-medium" style="font-size:32px; line-height:40px;">ORDER SUCCESSFUL!</div>
          <div class="text-body text-center" style="color:#A8A29E;">Your trading challenge is ready.</div>
        </div>

        <!-- Chip de Order -->
        <div class="px-3 py-2 bg-dark rounded-2 d-inline-flex align-items-center gap-2" style="outline:1px solid #404040;">
          <div class="text-white fw-bold">Order:</div>
          <div class="text-body fw-bold" style="color:#A8A29E;"><?php echo esc_html( $order_number ); ?></div>
          <div class="mt-icon mt-icon-white mt-icon_content-copy"></div>
        </div>

        <!-- Resumen -->
        <div class="w-100" style="max-width:600px;">
          <div class="p-3 rounded-3 d-flex flex-column gap-3">
            <div class="text-white fw-bold text-base">Order Summary</div>

            <!-- Producto principal -->
            <?php if ( $main_item_name ): ?>
              <div class="d-flex align-items-center justify-content-between">
                <div class="text-white text-14px-line-20px"><?php echo esc_html( $main_item_name ); ?></div>
                <div class="fw-bold text-14px-line-20px text-primary">
                  <?php echo wp_kses_post( wc_price( $main_item_price ) ); ?>
                </div>
              </div>
            <?php endif; ?>

            <div style="height:1px; border-top:1px solid #404040;"></div>

            <!-- Add-ons -->
            <?php if ( $addons_total > 0 ): ?>
              <div class="d-flex align-items-center justify-content-between">
                <div class="text-white text-base fw-bold">Add Ons</div>
                <div class="fw-bold text-primary">
                  <?php echo wp_kses_post( wc_price( $addons_total ) ); ?>
                </div>
              </div>

              <?php foreach ( $addons_names as $an ): ?>
                <div class="d-flex align-items-center justify-content-between">
                  <div class="text-white text-14px-line-20px"><?php echo esc_html( $an ); ?></div>
                </div>
              <?php endforeach; ?>

              <div style="height:1px; border-top:1px solid #404040;"></div>
            <?php endif; ?>

            <!-- Total + método de pago -->
            <div class="d-flex align-items-center justify-content-between">
              <div class="text-white text-base fw-bold">Total Paid</div>
              <div class="fw-bold text-primary"><?php echo wp_kses_post( $total_paid ); ?></div>
            </div>

            <div class="d-flex align-items-center justify-content-between">
              <div class="text-white fw-bold">Payment Method</div>
              <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px;background:#fff;">
                  <span class="text-dark" style="font-size:10px; text-transform:uppercase;"><?php echo esc_html( $card_brand ?: 'CARD' ); ?></span>
                </div>
                <div class="text-white text-14px-line-20px"><?php echo esc_html( $pm_suffix ); ?></div>
              </div>
            </div>

            <div class="p-3 rounded-2 d-flex align-items-start gap-3 outline-dark bg-1e1e1e">
              <div class="mt-icon mt-icon-success mt-icon_checkmark-solid"></div>
              <div class="flex-grow-1" style="color:#2DD4BF;">Confirmation email sent  - Account activating now.</div>
            </div>

            <a href="<?php echo esc_url( home_url('/my-account/overview/') ); ?>"
               class="ot-btn w-100 fw-medium text-black rounded-xl p-y-12-mega p-x-16-mega bg-mgt-primary text-center text-uppercase">
               Go to my account
            </a>
          </div>
        </div>

      </div><!-- /.modal-body -->
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  try {
    if (window.bootstrap && document.getElementById('orderSuccessModal')) {
      var modal = new bootstrap.Modal(document.getElementById('orderSuccessModal'));
      modal.show();
    }
  } catch (e) {}
});
</script>


<?php endif; ?>
