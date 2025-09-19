<?php

defined('ABSPATH') || exit;

/**
 * @var array<int, array{
 *     href: string,
 *     icon?: string,
 *     text: string
 * }> $menu_items
 */

$links = $args['menu_items'] ?? [];

?>

<aside class="mt-sidebar w-100 d-flex gap-32 flex-column">
    <?php get_template_part('template-parts/my-profile'); ?>
    <div class="traders-area">
        <a href="https://app.megatrader.io/" class="mega-btn-md mega-btn-secondary-md w-100">
            <svg xmlns="http://www.w3.org/2000/svg" width="31" height="30" viewBox="0 0 31 30" fill="none">
                <circle cx="15.5" cy="15" r="15" fill="#0062FF" />
                <path
                    d="M14.2126 11.0172L12.4628 8.79297H8.25781L12.1168 13.6745L14.2126 11.0172ZM16.2552 18.9103L18.0716 21.2068H22.2233L18.3444 16.2727L16.2552 18.9103ZM18.0184 8.79953L8.25781 21.2068H12.4095L22.2233 8.79953H18.0184Z"
                    fill="white" />
            </svg>

            <?php echo esc_html(Label::SIDEBAR_META['launch_button']); ?>
        </a>

    </div>
    <div class="mt-card gap-3">
        <div class="text-white text-size-20 fw-medium text-uppercase">
            <?php echo esc_html(Label::SIDEBAR_META['plan_title']); ?>
        </div>
        <div class="text-16px fw-medium text-a8a29e text-wrap">
            <?php echo esc_html(Label::SIDEBAR_META['plan_description']); ?>
        </div>
        <div class="btn-challenge">
            <a href="<?php echo esc_url(home_url('/subscriptions')); ?>" class="mega-btn-md mega-btn-default-md w-100">
                <?php echo esc_html(Label::SIDEBAR_META['plan_button']); ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="24" viewBox="0 0 25 24" fill="none">
                    <mask id="mask0_12909_529" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24"
                        height="24">
                        <rect width="24" height="24" fill="#D9D9D9" />
                    </mask>
                    <g mask="url(#mask0_12909_529)">
                        <path d="M12.6 12L8 7.4L9.4 6L15.4 12L9.4 18L8 16.6L12.6 12Z" fill="black" />
                    </g>
                </svg>
            </a>
        </div>
    </div>

<style>
    .mt-sidebar__menu__link {
        padding: 12px 0;
        display: flex;
        gap: 8px;
        align-items: center;
        color: white;
    }
    .mt-sidebar__menu__link:hover {
        color: var(--Text-Action-Primary-Text, #FFB34A);
    }
    .mt-sidebar__menu__link.active {
        color: var(--Text-Action-Primary-Text, #FFB34A);
    }
    @media (max-width: 1199.98px) {
        .mt-sidebar__menu {
            display: none;
        }
    }
</style>
    <div class="mt-sidebar__menu">
        <div class="mt-sidebar__menu__group">
        <div class="mt-sidebar__menu__group__title">DASHBOARD</div>
        <div class="mt-sidebar__menu__links d-flex flex-column gap-2 align-items-start">
            <a class="mt-sidebar__menu__link active" href="https://subscriptions.megatrader.io/my-account/overview/">
                <i class="mt-icon mt-icon-sm mt-icon_account"></i>
                <span>ACCOUNT OVERVIEW<span>
            </a>
            <a class="mt-sidebar__menu__link" href="https://subscriptions.megatrader.io/my-account/overview/">
                <i class="mt-icon mt-icon-sm mt-icon_checkmark-solid"></i>
                <span>REFERRALS<span>
            </a>
            <a class="mt-sidebar__menu__link" href="https://subscriptions.megatrader.io/my-account/overview/">
                <i class="mt-icon mt-icon-sm mt-icon_wallet"></i>
                <span>PAYOUTS<span>
            </a>
            <a class="mt-sidebar__menu__link" href="https://subscriptions.megatrader.io/my-account/overview/">
                <i class="mt-icon mt-icon-sm mt-icon_help"></i>
                <span>HELP CENTER<span>
            </a>
        </div>
    </div>
</aside>