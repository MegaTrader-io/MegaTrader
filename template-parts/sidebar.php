<?php

defined('ABSPATH') || exit;

$mt_current_account_section = static function (): string {
    $req_path = (string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    $req_path = rtrim($req_path ?: '/', '/');

    $account_base_url = wc_get_page_permalink('myaccount');           // e.g. https://.../my-account/
    $account_base     = (string) parse_url($account_base_url, PHP_URL_PATH);
    $account_base     = rtrim($account_base ?: '/my-account', '/');

    if (strpos($req_path, $account_base) !== 0) {
        return '';
    }

    // resto del path después de /my-account
    $rest  = ltrim(substr($req_path, strlen($account_base)), '/');    // '' | 'overview/...' | 'profile/...'
    $first = $rest === '' ? '' : strtolower(strtok($rest, '/'));

    // La raíz (/my-account/) o 'dashboard' cuentan como 'overview'
    if ($first === '' || $first === 'dashboard') {
        $first = 'overview';
    }
    return $first;
};

$mt_is_active = static function (string $slug, string $class = 'active') use ($mt_current_account_section): string {
    return $mt_current_account_section() === strtolower($slug) ? $class : '';
};

$account_base_url = trailingslashit( wc_get_page_permalink('myaccount') );

$menu_links = [
    [
        'text' => 'ACCOUNT OVERVIEW',
        'icon' => '',
        'href' => ''
    ]
];

$v2 = isset($_GET['v2']);

?>
<aside class="mt-sidebar">
    <div class="mt-sidebar__wrapper mt-card mt-card_border">

    <?php if($v2): ?>
        <?php get_template_part('template-parts/main-menu'); ?>
    <?php else: ?>
        <div class="mt-sidebar__container d-flex flex-column overflow-hidden">
            <div class="mt-sidebar__logo d-flex gap-2 align-items-center justify-content-between">
                <a href="<?php echo esc_url(home_url()); ?>" class="mt-sidebar__logo-link d-flex gap-3 align-items-center">
                    <img class="mt-sidebar__logo-icon" src="<?php echo get_template_directory_uri(); ?>/assets/img/megatrader-mobile-original.svg" alt="MegaTrader"width="60" height="60" loading="eager">
                    <div class="mt-sidebar__logo-text">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/megatrader-text-original.svg" alt="MegaTrader" class="mt-sidebar__logo-wordmark" width="200" loading="eager"/>
                    </div>
                </a>
                <a id="mt-sidebar-toggle" class="p-2" href="javascript:void(0);" onclick="this.dispatchEvent(new CustomEvent('MT_SIDEBAR_TOGGLE', { bubbles:true }));">
                    <i class="mt-icon mt-icon-white mt-icon_caret-left-solid"></i>
                </a>

            </div>

            <div class="mt-card mt-card-dark h-auto">
                <?php get_template_part('template-parts/my-profile'); ?>
            </div>
            <!--
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
            -->

            <div class="mt-sidebar__menu flex-fill overflow-y-auto d-flex flex-column gap-32"">
                <div class="mt-sidebar__menu__group">
                    <div class="mt-sidebar__menu__group__title mb-2">DASHBOARD</div>
                    <div class="mt-sidebar__menu__group__options">
                        <!-- Desktop -->
                        <div class="mt-sidebar__menu__links d-flex flex-column gap-1 align-items-start">
                            <a class="mt-sidebar__menu__link <?php echo esc_attr($mt_is_active('overview')); ?>" href="/my-account/overview/">
                                <i class="mt-icon mt-icon-sm mt-icon_bar-chart"></i>
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
                            <a class="mt-sidebar__menu__link <?= esc_attr($mt_is_active('profile')); ?>" href="/my-account/profile/">
                                <i class="mt-icon mt-icon-sm mt-icon_settings"></i>
                                <span>ACCOUNT SETTINGS<span>
                            </a>
                            <a class="mt-sidebar__menu__link" href="/my-account/orders">
                                <i class="mt-icon mt-icon-sm mt-icon_dollar-solid"></i>
                                <span class="text-uppercase">Subscriptions & Billing<span>
                            </a>
                            <a class="mt-sidebar__menu__link" href="https://help.megatrader.io/en/" target="_blank">
                                <i class="mt-icon mt-icon-sm mt-icon_help"></i>
                                <span>HELP CENTER<span>
                            </a>
                            <a class="mt-sidebar__menu__link btn-logout" href="<?php echo esc_url(
                                    add_query_arg(
                                            'time',
                                            time(),
                                            wp_logout_url()
                                    )
                            ); ?>">
                                <i class="mt-icon mt-icon-sm mt-icon_logout"></i>
                                <span>LOGOUT<span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-card mt-card_bg-layer mt-card_radius-small gap-3 h-auto">
                <div class="text-white text-size-20 fw-medium text-uppercase">
                    <?php echo esc_html(Label::SIDEBAR_META['plan_title']); ?>
                </div>
                <div class="text-16px fw-medium text-a8a29e text-wrap">
                    <?php echo esc_html(Label::SIDEBAR_META['plan_description']); ?>
                </div>
                <div class="btn-challenge">
                    <a href="<?php echo esc_url(home_url('/subscriptions')); ?>" class="mega-btn-md mega-btn-default-md w-100">
                        <?php echo esc_html(Label::SIDEBAR_META['plan_button']); ?>
                        <i class="mt-icon mt-icon_caret-right"></i>
                    </a>
                </div>
                <script>
                    document.querySelector('.btn-challenge a').addEventListener('click', function(){
                        localStorage.removeItem('content-crypto-storage');
                        localStorage.removeItem('content-forex-storage');
                        localStorage.removeItem('content-futures-storage');
                    });
                </script>
            </div>
        </div>

        <!-- COLLAPSED -->
        <div class="mt-sidebar__container__collapsed d-flex flex-column overflow-hidden align-items-center">
            <a id="mt-sidebar-toggle" class="p-2" href="javascript:void(0);" onclick="this.dispatchEvent(new CustomEvent('MT_SIDEBAR_TOGGLE', { bubbles:true }));">
                <i class="mt-icon mt-icon-white mt-icon_caret-right-solid"></i>
            </a>

            <div class="mt-sidebar__logo_collapsed">
                <a href="<?php echo esc_url(home_url()); ?>" class="mt-sidebar__logo-link d-flex gap-3 align-items-center">
                    <img class="mt-sidebar__logo-icon" src="<?php echo get_template_directory_uri(); ?>/assets/img/megatrader-mobile-original.svg" alt="MegaTrader"width="50" height="50" loading="eager">
                </a>
            </div>
            <div class="mt-sidebar__profile_collapsed">
                <div class="mt-card mt-card-dark mt-card-xs">
                    <?php get_template_part('template-parts/my-profile', null, [
                        'avatar_size' => 44,
                        'collapsed' => true,
                    ]); ?>
                </div>
            </div>

            <div class="mt-sidebar__menu__group__options">
                <div class="mt-sidebar__menu__links d-flex flex-column gap-1 align-items-start">
                    <span class="mt-tooltip" data-placement="right">
                        <a class="mt-sidebar__menu__link <?= $overview_active_class ?>" href="/my-account/overview/">
                            <i class="mt-icon mt-icon-sm mt-icon_bar-chart"></i>
                        </a>
                        <span class="mt-tooltip__panel" role="tooltip">
                            <div class="mt-tooltip__body">ACCOUNT OVERVIEW</div>
                        </span>
                    </span>
                    <span class="mt-tooltip" data-placement="right">
                        <a class="mt-sidebar__menu__link" href="/my-account/overview/">
                            <i class="mt-icon mt-icon-sm mt-icon_people-plus"></i>
                        </a>
                        <span class="mt-tooltip__panel" role="tooltip">
                            <div class="mt-tooltip__body">REFERRALS</div>
                        </span>
                    </span>
                    <span class="mt-tooltip" data-placement="right">
                        <a class="mt-sidebar__menu__link" href="/my-account/overview/">
                            <i class="mt-icon mt-icon-sm mt-icon_wallet"></i>
                        </a>
                        <span class="mt-tooltip__panel" role="tooltip">
                            <div class="mt-tooltip__body">PAYOUTS</div>
                        </span>
                    </span>
                    <span class="mt-tooltip" data-placement="right">
                        <a class="mt-sidebar__menu__link <?= esc_attr($mt_is_active('profile')); ?>" href="/my-account/profile/">
                            <i class="mt-icon mt-icon-sm mt-icon_settings"></i>
                        </a>
                        <span class="mt-tooltip__panel" role="tooltip">
                            <div class="mt-tooltip__body">ACCOUNT SETTINGS</div>
                        </span>
                    </span>
                    <span class="mt-tooltip" data-placement="right">
                        <a class="mt-sidebar__menu__link " href="/my-account/orders">
                            <i class="mt-icon mt-icon-sm mt-icon_dollar-solid"></i>
                        </a>
                        <span class="mt-tooltip__panel" role="tooltip">
                            <div class="mt-tooltip__body text-uppercase">Subscriptions & Billing</div>
                        </span>
                    </span>
                    <span class="mt-tooltip" data-placement="right">
                        <a class="mt-sidebar__menu__link" href="/my-account/overview/" target="_blank">
                            <i class="mt-icon mt-icon-sm mt-icon_help"></i>
                        </a>
                        <span class="mt-tooltip__panel" role="tooltip">
                            <div class="mt-tooltip__body">HELP CENTER</div>
                        </span>
                    </span>
                    <span class="mt-tooltip" data-placement="right">
                        <a class="mt-sidebar__menu__link" href="/my-account/overview/">
                            <i class="mt-icon mt-icon-sm mt-icon_logout"></i>
                        </a>
                        <span class="mt-tooltip__panel" role="tooltip">
                            <div class="mt-tooltip__body">LOGOUT</div>
                        </span>
                    </span>
                </div>
            </div>

            <span class="mt-tooltip" data-placement="right">
                <a href="<?php echo esc_url(home_url('/subscriptions')); ?>" class="mega-btn-md mega-btn-default-md w-100">
                    <i class="mt-icon mt-icon_dollar-solid"></i>
                </a>
                <span class="mt-tooltip__panel" role="tooltip">
                    <div class="mt-tooltip__body text-uppercase"><?= esc_html(Label::SIDEBAR_META['plan_button']); ?></div>
                </span>
            </span>

        </div>
    <?php endif; ?>
    </div>
</aside>

<?php get_template_part('template-parts/main-menu', null, [
    'is_overlay' => true
]); ?>

<script>
    let expanded = true;

    function sidebarUpdateToggle(expanded){
        const toggleIcon = document.querySelector('#mt-sidebar-toggle > .mt-icon');
        if(toggleIcon){
            toggleIcon.classList.remove('mt-icon_caret-left-solid', 'mt-icon_caret-right-solid');
            toggleIcon.classList.add( expanded ? 'mt-icon_caret-left-solid' : 'mt-icon_caret-right-solid');
        }
    }

    function updateDesktopContentVisibility(){
        document.querySelector('.mt-sidebar')?.classList[expanded ? 'remove' : 'add']('mt-sidebar_collapsed');
    }

    function sidebarInit(){

        updateDesktopContentVisibility(expanded);


        document.addEventListener('MT_SIDEBAR_TOGGLE', function(e) {
            expanded = !expanded;

            updateDesktopContentVisibility(expanded);
        });
    }

    document.addEventListener('DOMContentLoaded', sidebarInit);


</script>