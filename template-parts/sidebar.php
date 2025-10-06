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

?>
<aside class="mt-sidebar">
    <div class="mt-sidebar__wrapper mt-card mt-card_border">
        <?php get_template_part('template-parts/main-menu'); ?>
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