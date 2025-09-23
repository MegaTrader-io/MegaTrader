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

<aside class="mt-sidebar">
    <div class="mt-sidebar__wrapper mt-card d-flex gap-32 flex-column">
        <div class="mt-sidebar__logo">
            <a href="<?php echo esc_url(home_url()); ?>" class="mt-sidebar__logo-link d-flex gap-3 align-items-center">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/megatrader-mobile-original.svg"
                        alt="MegaTrader" class="mt-sidebar__logo-icon" width="60" height="60" loading="eager">
                <div class="mt-sidebar__logo-text">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/megatrader-text-original.svg"
                            alt="MegaTrader" class="mt-sidebar__logo-wordmark" width="200" loading="eager"/>
                </div>
            </a>
        </div>

        <div class="mt-card mt-card-dark">
            <?php get_template_part('template-parts/my-profile'); ?>
        </div>

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

        <div class="mt-sidebar__menu flex-fill overflow-y-auto">
            <div class="mt-sidebar__wrapper d-flex flex-column gap-32">
                <div class="mt-sidebar__menu__group">
                    <div class="mt-sidebar__menu__group__title mb-2">DASHBOARD</div>
                    <div class="mt-sidebar__menu__group__options mt-page_md-d-none">
                        <!-- Desktop -->
                        <div class="mt-sidebar__menu__links d-flex flex-column gap-1 align-items-start">
                            <a class="mt-sidebar__menu__link active" href="/my-account/overview/">
                                <i class="mt-icon mt-icon-sm mt-icon_account"></i>
                                <span>ACCOUNT OVERVIEW<span>
                            </a>
                            <a class="mt-sidebar__menu__link" href="/my-account/referrals/">
                                <i class="mt-icon mt-icon-sm mt-icon_people-plus"></i>
                                <span>REFERRALS<span>
                            </a>
                            <a class="mt-sidebar__menu__link" href="/my-account/payouts/">
                                <i class="mt-icon mt-icon-sm mt-icon_wallet"></i>
                                <span>PAYOUTS<span>
                            </a>
                            <a class="mt-sidebar__menu__link" href="<?= wp_logout_url(); ?>">
                                <i class="mt-icon mt-icon-sm mt-icon_settings"></i>
                                <span>ACCOUNT SETTINGS<span>
                            </a>
                            <a class="mt-sidebar__menu__link" href="https://help.megatrader.io/en/">
                                <i class="mt-icon mt-icon-sm mt-icon_help"></i>
                                <span>HELP CENTER<span>
                            </a>
                        </div>
                    </div>

                    <div class="d-none mt-page_md-d-block">
                        <!-- Tablet/Mobile -->
                        <select id="account-nav-select" class="mt-sidebar__select"
                        onchange="if (this.value) window.location.href=this.value;">
                            <i class="mt-icon mt-icon-sm mt-icon_account"></i>
                            <optgroup label="DASHBOARD">
                                <option value="/my-account/overview/" <?php selected($_SERVER['REQUEST_URI'], '/my-account/overview/'); ?> checked>
                                    ACCOUNT OVERVIEW
                                </option>
                                <option value="/my-account/referrals/" <?php selected($_SERVER['REQUEST_URI'], '/my-account/referrals/'); ?>>
                                    REFERRALS
                                </option>
                                <option value="/my-account/payouts/" <?php selected($_SERVER['REQUEST_URI'], '/my-account/payouts/'); ?>>
                                    PAYOUTS
                                </option>
                                <option value="https://help.megatrader.io/en/" <?php selected($_SERVER['REQUEST_URI'], '/help/'); ?>>
                                    HELP CENTER
                                </option>
                            </optgroup>
                        </select>
                    </div>
                </div>

                <div class="mt-sidebar__menu__group">
                    <div class="mt-sidebar__menu__group__options">
                        <div class="mt-sidebar__menu__links d-flex flex-column gap-2 align-items-start">
                            <a class="mt-sidebar__menu__link" href="<?= wp_logout_url(); ?>">
                                <i class="mt-icon mt-icon-sm mt-icon_logout"></i>
                                <span>LOGOUT<span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-card mt-card_bg-layer mt-card_radius-small gap-3">
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


    </div>
</aside>