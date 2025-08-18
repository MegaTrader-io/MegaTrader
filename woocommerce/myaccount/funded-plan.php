<?php
defined( 'ABSPATH' ) || exit;
?>

<div class="container p-0">
    <?php
    // Obtenemos las órdenes y filtramos
    $user_id = get_current_user_id();
    $orders  = wc_get_orders( [
        'customer_id' => $user_id,
        'status'      => [ 'processing', 'completed' ],
        'limit'       => -1,
    ] );

    $slug_prefix = 'funded-plan';
    $found       = false;

    foreach ( $orders as $order ) {
        foreach ( $order->get_items() as $item ) {
            $product = $item->get_product();
            if ( $product && 0 === strpos( $product->get_slug(), $slug_prefix ) ) {
                $found = true;
                break 2;
            }
        }
    }

    if ( $found ) :
    ?>
        <div class="order-table-wrap">
            <ul class="funded-plan-list list-unstyled">
                <?php
                foreach ( $orders as $order ) {
                    foreach ( $order->get_items() as $item ) {
                        $product = $item->get_product();
                        if ( $product && 0 === strpos( $product->get_slug(), $slug_prefix ) ) {
                            $order_id   = $order->get_id();
                            $item_name  = $item->get_name();
                            $order_date = $order->get_date_created()->date_i18n( wc_date_format() );
                            $view_url = wc_get_endpoint_url( 'view-funded-plan', $order_id, wc_get_page_permalink( 'myaccount' ) );
                            ?>
                            <li class="funded-plan-item mb-3">
                                <a href="<?php echo esc_url( $view_url ); ?>" class="funded-plan-link d-block mb-1">
                                    <?php printf( esc_html__( '#%1$s – %2$s', 'woocommerce' ), $order_id, $item_name ); ?>
                                </a>
                                <span class="funded-plan-date text-muted">(<?php echo esc_html( $order_date ); ?>)</span>
                            </li>
                            <?php
                        }
                    }
                }
                ?>
            </ul>
        </div>

    <?php else: ?>
         <div class="container p-0">
        <div class="no-order-wrapper d-flex flex-column gap-32">

            <div class="align-items-center bg-1e1e1e d-flex gap-3 overflow-hidden p-3 rounded-16px">
                <div class="d-flex flex-column flex-grow-1 flex-shrink-1 justify-content-center">
                    <div class="fw-medium text-size-20 text-uppercase text-white">
                        <?php esc_html_e('No active membership', 'woocommerce'); ?>
                    </div>
                    <div class="fw-medium text-14px text-a8a29e">
                        <?php esc_html_e('You don’t have any active plans or memberships at the moment.', 'woocommerce'); ?>
                    </div>
                </div>
                <div class="get-started">
                    <?php
                    $shop_url = home_url('/');
                    ?>
                    <a id="get_started_btn" href="<?php echo esc_url($shop_url); ?>"
                        class="btn w-100 mega-btn-md mega-btn-primary-md">
                        <?php esc_html_e('Get Started', 'woocommerce'); ?>
                    </a>

                </div>
            </div>

            <div class="align-items-center bg-1e1e1e d-flex flex-column gap-32 overflow-hidden p-3 rounded-3 rounded-16px">
                <div class="text-white text-size-20 fw-medium text-uppercase">
                    <?php esc_html_e('Get started in 3 steps', 'woocommerce'); ?>
                </div>
                <div class="d-flex align-items-center flex-column">
                    <div class="no-order stepper position-relative w-100">
                        <div class="stepper-line"></div>
                        <div class="stepper-numbers-wrapper">
                            <div class="stepper-numbers-circle">
                                <div class="stepper-numbers-text">
                                    <?php esc_html_e('1', 'woocommerce'); ?>
                                </div>
                            </div>
                            <div class="stepper-numbers-circle">
                                <div class="stepper-numbers-text">
                                    <?php esc_html_e('2', 'woocommerce'); ?>
                                </div>

                            </div>
                            <div class="stepper-numbers-circle">
                                <div class="stepper-numbers-text">
                                    <?php esc_html_e('3', 'woocommerce'); ?>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="d-flex gap-32">
                        <div class="d-flex flex-column gap-1 flex-grow-1 flex-shrink-1 w-max-224px">
                            <div class="text-primary text-center text-base fw-medium">
                                <?php esc_html_e('Choose your plan', 'woocommerce'); ?>
                            </div>
                            <div class="text-center fw-medium text-14px text-a8a29e">
                                <?php esc_html_e('Select from our range of trading challenges', 'woocommerce'); ?>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-1 flex-grow-1 flex-shrink-1 w-max-224px">
                            <div class="text-primary text-center text-base fw-medium">
                                <?php esc_html_e('Complete purchase', 'woocommerce'); ?>
                            </div>
                            <div class="text-center fw-medium text-14px text-a8a29e">
                                <?php esc_html_e('Secure payment and instant activation', 'woocommerce'); ?>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-1 flex-grow-1 flex-shrink-1 w-max-224px">
                            <div class="text-primary text-center text-base fw-medium">
                                <?php esc_html_e('Start trading', 'woocommerce'); ?>
                            </div>
                            <div class="text-center fw-medium text-14px text-a8a29e">
                                <?php esc_html_e('Access your platform and begin your challenge', 'woocommerce'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column flex-md-row gap-3 pb-35">
                <div class="bg-1e1e1e d-flex flex-column gap-3 overflow-hidden p-3 rounded-16px w-100">
                    <div class="text-white fw-medium text-size-20 text-uppercase">
                        <?php esc_html_e('Why join us?', 'woocommerce'); ?>
                    </div>
                    <div class="d-flex flex-column">
                        <div class="py-3 d-flex gap-2 align-items-center border-bottom-dark">
                            <div class="svg-check">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none">
                                    <mask id="mask0_12164_24055" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0"
                                        y="0" width="24" height="24">
                                        <rect width="24" height="24" fill="#D9D9D9" />
                                    </mask>
                                    <g mask="url(#mask0_12164_24055)">
                                        <path
                                            d="M9.54961 17.9996L3.84961 12.2996L5.27461 10.8746L9.54961 15.1496L18.7246 5.97461L20.1496 7.39961L9.54961 17.9996Z"
                                            fill="#A8A29E" />
                                    </g>
                                </svg>
                            </div>
                            <div class="text-a8a29e text-base fw-medium">
                                <?php esc_html_e('Professional trading environment', 'woocommerce'); ?>
                            </div>
                        </div>
                        <div class="py-3 d-flex gap-2 align-items-center border-bottom-dark">
                            <div class="svg-check">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none">
                                    <mask id="mask0_12164_24055" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0"
                                        y="0" width="24" height="24">
                                        <rect width="24" height="24" fill="#D9D9D9" />
                                    </mask>
                                    <g mask="url(#mask0_12164_24055)">
                                        <path
                                            d="M9.54961 17.9996L3.84961 12.2996L5.27461 10.8746L9.54961 15.1496L18.7246 5.97461L20.1496 7.39961L9.54961 17.9996Z"
                                            fill="#A8A29E" />
                                    </g>
                                </svg>
                            </div>
                            <div class="text-a8a29e text-base fw-medium">
                                <?php esc_html_e('Real-time performance tracking', 'woocommerce'); ?>
                            </div>
                        </div>
                        <div class="py-3 d-flex gap-2 align-items-center border-bottom-dark">
                            <div class="svg-check">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none">
                                    <mask id="mask0_12164_24055" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0"
                                        y="0" width="24" height="24">
                                        <rect width="24" height="24" fill="#D9D9D9" />
                                    </mask>
                                    <g mask="url(#mask0_12164_24055)">
                                        <path
                                            d="M9.54961 17.9996L3.84961 12.2996L5.27461 10.8746L9.54961 15.1496L18.7246 5.97461L20.1496 7.39961L9.54961 17.9996Z"
                                            fill="#A8A29E" />
                                    </g>
                                </svg>
                            </div>
                            <div class="text-a8a29e text-base fw-medium">
                                <?php esc_html_e('Expert support and guidance', 'woocommerce'); ?>
                            </div>
                        </div>
                        <div class="py-3 d-flex gap-2 align-items-center">
                            <div class="svg-check">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none">
                                    <mask id="mask0_12164_24055" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0"
                                        y="0" width="24" height="24">
                                        <rect width="24" height="24" fill="#D9D9D9" />
                                    </mask>
                                    <g mask="url(#mask0_12164_24055)">
                                        <path
                                            d="M9.54961 17.9996L3.84961 12.2996L5.27461 10.8746L9.54961 15.1496L18.7246 5.97461L20.1496 7.39961L9.54961 17.9996Z"
                                            fill="#A8A29E" />
                                    </g>
                                </svg>
                            </div>
                            <div class="text-a8a29e text-base fw-medium">
                                <?php esc_html_e('Flexible challenge options', 'woocommerce'); ?>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="bg-1e1e1e d-flex flex-column gap-3 overflow-hidden p-3 rounded-16px w-100">
                    <div class="text-white fw-medium text-size-20 text-uppercase">
                        <?php esc_html_e('Frequently Asked Question', 'woocommerce'); ?>
                    </div>
                    <div class="d-flex flex-column">
                        <div class="py-3 border-bottom-dark d-flex flex-column gap-1">
                            <div class="text-white text-base fw-medium">
                                <?php esc_html_e('How do I get started?', 'woocommerce'); ?>
                            </div>
                            <div class="text-14px text-a8a29e fw-medium">
                                <?php esc_html_e('Simply choose a plan that fits your trading experience and goals, complete the purchase, and
                                you’ll get instant access to your trading challenge.', 'woocommerce'); ?>
                            </div>
                        </div>
                        <div class="py-3 border-bottom-dark d-flex flex-column gap-1">
                            <div class="text-white text-base fw-medium">
                                <?php esc_html_e('What’s included in each plan?', 'woocommerce'); ?>
                            </div>
                            <div class="text-14px text-a8a29e fw-medium">
                                <?php esc_html_e('Each plan includes access to our trading platform, real-time performance tracking, support resources and specific challenge objectives based on your chosen tier.', 'woocommerce'); ?>
                            </div>
                        </div>
                        <div class="py-3 d-flex flex-column gap-1">
                            <div class="text-white text-base fw-medium">
                                <?php esc_html_e('Can I upgrade my plan later?', 'woocommerce'); ?>
                            </div>
                            <div class="text-14px text-a8a29e fw-medium">
                                <?php esc_html_e('Yes, you can upgrade to a higher tier plan at any time. Contact our support team for assistance with plan changes.', 'woocommerce'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
