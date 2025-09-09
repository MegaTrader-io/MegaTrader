<?php

defined('ABSPATH') || exit;

?>

<aside class="mt-sidebar w-100 d-flex gap-32 flex-column">
    <?php get_template_part('template-parts/my-profile'); ?>
    <div class="traders-area">
        <a href="https://app.megatrader.io/"
            class="mega-btn-md mega-btn-secondary-md w-100">
            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="24" viewBox="0 0 25 24"
                fill="none" style="margin-right:8px;">
                <mask id="mask0_12909_1841" mask-type="alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                    width="25" height="24">
                    <rect x="0.5" width="24" height="24" fill="#D9D9D9" />
                </mask>
                <g mask="url(#mask0_12909_1841)">
                    <path d="M16.5 20V13H20.5V20H16.5ZM10.5 20V4H14.5V20H10.5ZM4.5 20V9H8.5V20H4.5Z"
                        fill="white" />
                </g>
            </svg>
            Traders area
        </a>

    </div>
    <div class="mt-card gap-3">
        <div class="text-white text-size-20 fw-medium text-uppercase">
            Explore the plans</div>
        <div class="text-16px fw-medium text-a8a29e text-wrap">
            Find the perfect plan to enhance your experience.</div>
        <div class="btn-challenge">
            <a href="<?php echo esc_url(home_url('/subscriptions')); ?>"
                class="mega-btn-md mega-btn-default-md w-100">
                Buy a new chanllenge
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="24" viewBox="0 0 25 24"
                    fill="none">
                    <mask id="mask0_12909_529" style="mask-type:alpha" maskUnits="userSpaceOnUse"
                        x="0" y="0" width="24" height="24">
                        <rect width="24" height="24" fill="#D9D9D9" />
                    </mask>
                    <g mask="url(#mask0_12909_529)">
                        <path d="M12.6 12L8 7.4L9.4 6L15.4 12L9.4 18L8 16.6L12.6 12Z"
                            fill="black" />
                    </g>
                </svg>
            </a>
        </div>
    </div>
</aside>