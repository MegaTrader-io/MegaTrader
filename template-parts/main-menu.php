<?php

defined('ABSPATH') || exit;

/* === Helpers === */
if (file_exists(get_stylesheet_directory() . '/inc/mt-accounts-helpers.php')) {
    require_once get_stylesheet_directory() . '/inc/mt-accounts-helpers.php';
}


/* Check account Elegible for payout? */
if (!function_exists('mt_user_has_payout_accounts')) {
  function mt_user_has_payout_accounts(string $email, bool $requireEligible = true): bool {
    $email = sanitize_email($email);
    if (!$email || !function_exists('mt_prepare_ui_payout')) return false;

    $out = mt_prepare_ui_payout($email);
    if (!is_array($out) || empty($out['items'])) return false;

    if (!$requireEligible) return true; 
    foreach ($out['items'] as $it) {
      $eligible = !empty($it['eligibleForPayout']);
      $maxUI = isset($it['meta']['maxWithdrawalUI']) ? floatval($it['meta']['maxWithdrawalUI']) : 0.0;
      if ($eligible && $maxUI > 0) return true;
    }
    return false;
  }
}

/* get email from the use login */
$current_user = wp_get_current_user();
$user_email = ($current_user && !empty($current_user->user_email)) ? sanitize_email($current_user->user_email) : '';
$can_request_payout = $user_email ? mt_user_has_payout_accounts($user_email, true) : false;


$mt_current_account_section = static function (): string {
    $req_path = (string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    $req_path = rtrim($req_path ?: '/', '/');

    $account_base_url = wc_get_page_permalink('myaccount');           
    $account_base = (string) parse_url($account_base_url, PHP_URL_PATH);
    $account_base = rtrim($account_base ?: '/my-account', '/');

    if (strpos($req_path, $account_base) !== 0) {
        return '';
    }

    $rest = ltrim(substr($req_path, strlen($account_base)), '/');    // '' | 'overview/...' | 'profile/...'
    $first = $rest === '' ? '' : strtolower(strtok($rest, '/'));

    if ($first === '' || $first === 'dashboard') {
        $first = 'overview';
    }
    return $first;
};

$mt_is_active = static function (string $slug, string $class = 'active') use ($mt_current_account_section): string {
    return $mt_current_account_section() === strtolower($slug) ? $class : '';
};

$account_base_url = trailingslashit(wc_get_page_permalink('myaccount'));

$menu_links = [
    [
        'class' => $mt_is_active('overview', 'active'),
        'text' => 'ACCOUNT OVERVIEW',
        'icon' => 'mt-icon_bar-chart',
        'href' => '/my-account/overview/',
    ],
    [
        'class' => $mt_is_active('referrals', 'active'),
        'text' => 'REFERRALS',
        'icon' => 'mt-icon_people-plus',
        'is_disabled' => true,
        'badge' => [
            'text' => 'COMING SOON',
            'style' => 'light'
        ]
    ],
    [
        'class' => $mt_is_active('payout', 'active'),
        'id' => 'mt-request-payout',
        'text' => 'REQUEST PAYOUT',
        'icon' => 'mt-icon_wallet',
        'href' => '#',
        'modal_target' => '#mt-request-payout-modal',
        /*'is_disabled' => !$can_request_payout,*/
        'is_disabled' => true,
         'badge' => [
            'text' => 'COMING SOON',
            'style' => 'light'
        ]
    ],
    [
        'class' => $mt_is_active('profile', 'active'),
        'text' => 'ACCOUNT SETTINGS',
        'icon' => 'mt-icon_settings',
        'href' => '/my-account/profile/',
    ],
    [
        'text' => 'HELP CENTER',
        'icon' => 'mt-icon_help',
        'href' => 'https://help.megatrader.io/en/',
        'target' => '_blank'
    ],
    [
        'class' => 'btn-logout ',
        'text' => 'LOGOUT',
        'icon' => 'mt-icon_logout',
        'href' => add_query_arg('time', time(), wp_logout_url()),
    ],
];

if (!function_exists('render_menu_link')) {
    function render_menu_link(array $item)
    {
        $class = esc_attr($item['class'] ?? '');
        $is_disabled_class = esc_attr(($item['is_disabled'] ?? false) ? 'disabled' : '');
        $href = esc_url($item['href'] ?? 'javascript:void(0)');
        $target = esc_attr($item['target'] ?? '');
        $text = esc_html($item['text'] ?? '');
        $icon = esc_attr($item['icon'] ?? '');
        $badge = $item['badge'] ?? null;
        $id = esc_attr($item['id'] ?? '');

        $modal_attrs = '';
    if (!empty($item['modal_target']) && !$is_disabled) { 
            $href = '#';
            $modal_attrs = ' data-bs-toggle="modal" data-bs-target="' . esc_attr($item['modal_target']) . '" role="button"';
        }
        ?>
        <a <?= $id ? 'id="' . $id . '"' : '' ?> class="mt-sidebar__menu__link <?= $class ?> <?= $is_disabled_class ?>"
            href="<?= $href ?>" target="<?= $target ?>" <?= $modal_attrs ?>>
            <span class="mt-sidebar__menu__link__content">
                <?php if ($icon): ?>
                    <i class="mt-sidebar__menu__link__icon mt-icon mt-icon-sm <?= $icon ?>"></i>
                <?php endif; ?>
                <span class="mt-sidebar__menu__link__text"><?= $text ?></span>
            </span>
            <?php if ($badge): ?>
                <div class="mt-badge mt-badge-<?= esc_attr($badge['style'] ?? 'light') ?>"><?= esc_html($badge['text'] ?? '') ?>
                </div>
            <?php endif; ?>
        </a>
        <?php
    }


}

if (!function_exists('render_menu_link_collapsed')) {
    function render_menu_link_collapsed(array $item)
    {
        $class = esc_attr($item['class'] ?? '');
        $is_disabled_class = esc_attr(($item['is_disabled'] ?? false) ? 'disabled' : '');
        $href = esc_url($item['href'] ?? 'javascript:void(0)');
        $target = esc_attr($item['target'] ?? '');
        $text = esc_html($item['text'] ?? '');
        $icon = esc_attr($item['icon'] ?? '');
        $badge = $item['badge'] ?? null;

        $modal_attrs = '';
    if (!empty($item['modal_target']) && !$is_disabled) { 
            $href = '#';
            $modal_attrs = ' data-bs-toggle="modal" data-bs-target="' . esc_attr($item['modal_target']) . '" role="button"';
        }
        ?>
        <span class="mt-tooltip" data-placement="right">
            <a class="mt-sidebar__menu__link <?= $class ?> <?= $is_disabled_class ?>" href="<?= $href ?>"
                target="<?= $target ?>" <?= $modal_attrs ?>>
                <span class="mt-sidebar__menu__link__content">
                    <i class="mt-icon mt-icon-sm <?= $icon ?>"></i>
                </span>
            </a>
            <span class="mt-tooltip__panel" role="tooltip">
                <div class="mt-tooltip__body d-flex align-items-center gap-3">
                    <span><?= $text ?></span>
                    <?php if ($badge): ?>
                        <div class="mt-badge mt-badge-<?= esc_attr($badge['style'] ?? 'light') ?>">
                            <?= esc_html($badge['text'] ?? '') ?></div>
                    <?php endif; ?>
                </div>
            </span>
        </span>
        <?php
    }


}

$is_overlay = $args['is_overlay'] ?? false;

?>

<?php if ($is_overlay): ?>
    <!-- DRAWER -->
    <div class="mt-sidebar-overlay">
        <div class="mt-sidebar-overlay__dialog">
            <div class="mt-sidebar-overlay__content d-flex flex-column gap-32 overflow-hidden">
                <div class="mt-sidebar__logo d-flex gap-2 align-items-center justify-content-between">
                    <a href="<?php echo esc_url(home_url()); ?>"
                        class="mt-sidebar__logo-link d-flex gap-3 align-items-center">
                        <img class="mt-sidebar__logo-icon"
                            src="<?php echo get_template_directory_uri(); ?>/assets/img/megatrader-mobile-original.svg"
                            alt="MegaTrader" width="60" height="60" loading="eager">
                        <div class="mt-sidebar__logo-text">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/megatrader-text-original.svg"
                                alt="MegaTrader" class="mt-sidebar__logo-wordmark" width="200" loading="eager" />
                        </div>
                    </a>
                    <a id="mt-sidebar-overlay__close-btn" class="p-2" href="javascript:void(0);" title="Close Menu"
                        onclick="this.dispatchEvent(new CustomEvent('MT_MENU_TOGGLE', { bubbles:true }));">
                        <i class="mt-icon mt-icon-white mt-icon_close"></i>
                    </a>

                </div>

                <div class="mt-card mt-card-dark h-auto">
                    <?php get_template_part('template-parts/my-profile'); ?>
                </div>

                <div class="mt-sidebar__menu flex-fill overflow-y-auto d-flex flex-column gap-32"">
                    <div class=" mt-sidebar__menu__group">
                    <div class="mt-sidebar__menu__group__title mb-2">DASHBOARD</div>
                    <div class="mt-sidebar__menu__group__options">
                        <!-- Desktop -->
                        <div class="mt-sidebar__menu__links d-flex flex-column gap-1 align-items-start">
                            <?php foreach ($menu_links as $item) {
                                echo render_menu_link($item);
                            }
                            ?>
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
                    <a href="<?php echo esc_url(home_url('/subscriptions')); ?>"
                        class="mega-btn-md mega-btn-default-md w-100">
                        <?php echo esc_html(Label::SIDEBAR_META['plan_button']); ?>
                        <i class="mt-icon mt-icon_caret-right"></i>
                    </a>
                </div>
                <script>
                    document.querySelector('.btn-challenge a').addEventListener('click', function () {
                        localStorage.removeItem('content-crypto-storage');
                        localStorage.removeItem('content-forex-storage');
                        localStorage.removeItem('content-futures-storage');
                    });
                </script>
            </div>
        </div>
    </div>
    <div class="mt-sidebar-overlay__backdrop" title="Close Menu"
        onclick="this.dispatchEvent(new CustomEvent('MT_MENU_TOGGLE', { bubbles:true }));">
    </div>
    </div>

<?php else: ?>
    <!-- SIDEBAR -->

    <!-- EXPANDED -->
    <div class="mt-sidebar__container d-flex flex-column overflow-hidden">
        <div class="mt-sidebar__logo d-flex gap-2 align-items-center justify-content-between">
            <a href="<?php echo esc_url(home_url()); ?>" class="mt-sidebar__logo-link d-flex gap-3 align-items-center">
                <img class="mt-sidebar__logo-icon"
                    src="<?php echo get_template_directory_uri(); ?>/assets/img/megatrader-mobile-original.svg"
                    alt="MegaTrader" width="60" height="60" loading="eager">
                <div class="mt-sidebar__logo-text">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/megatrader-text-original.svg"
                        alt="MegaTrader" class="mt-sidebar__logo-wordmark" width="200" loading="eager" />
                </div>
            </a>
            <a id="mt-sidebar-toggle" class="p-2" href="javascript:void(0);"
                onclick="this.dispatchEvent(new CustomEvent('MT_SIDEBAR_TOGGLE', { bubbles:true }));">
                <i class="mt-icon mt-icon-white mt-icon_caret-left-solid"></i>
            </a>

        </div>

        <div class="mt-card mt-card-dark h-auto">
            <?php get_template_part('template-parts/my-profile'); ?>
        </div>

        <div class="mt-sidebar__menu flex-fill overflow-y-auto d-flex flex-column gap-32"">
            <div class=" mt-sidebar__menu__group">
            <div class="mt-sidebar__menu__group__title mb-2">DASHBOARD</div>
            <div class="mt-sidebar__menu__group__options">
                <!-- Desktop -->
                <div class="mt-sidebar__menu__links d-flex flex-column gap-1 align-items-start">
                    <?php foreach ($menu_links as $item) {
                        echo render_menu_link($item);
                    }
                    ?>
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
            document.querySelector('.btn-challenge a').addEventListener('click', function () {
                localStorage.removeItem('content-crypto-storage');
                localStorage.removeItem('content-forex-storage');
                localStorage.removeItem('content-futures-storage');
            });
        </script>
    </div>
    </div>

    <!-- COLLAPSED -->
    <div class="mt-sidebar__container__collapsed d-flex flex-column overflow-hidden align-items-center">
        <a id="mt-sidebar-toggle" class="p-2" href="javascript:void(0);"
            onclick="this.dispatchEvent(new CustomEvent('MT_SIDEBAR_TOGGLE', { bubbles:true }));">
            <i class="mt-icon mt-icon-white mt-icon_caret-right-solid"></i>
        </a>

        <div class="mt-sidebar__logo_collapsed">
            <a href="<?php echo esc_url(home_url()); ?>" class="mt-sidebar__logo-link d-flex gap-3 align-items-center">
                <img class="mt-sidebar__logo-icon"
                    src="<?php echo get_template_directory_uri(); ?>/assets/img/megatrader-mobile-original.svg"
                    alt="MegaTrader" width="50" height="50" loading="eager">
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
                <?php foreach ($menu_links as $item) {
                    echo render_menu_link_collapsed($item);
                }
                ?>
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