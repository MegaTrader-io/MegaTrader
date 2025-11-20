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

$sidebar_default_expanded = false;

?>


<aside
  class="mt-sidebar<?php echo $sidebar_default_expanded ? '' : ' mt-sidebar_collapsed'; ?>"
  data-sidebar-default="<?php echo $sidebar_default_expanded ? 'expanded' : 'collapsed'; ?>"
>
  <div class="mt-sidebar__wrapper mt-card mt-card_border">
    <?php get_template_part('template-parts/main-menu'); ?>
  </div>
</aside>


<script>
  let expanded = false;

  function sidebarUpdateToggle(isExpanded){
    const toggleIcon = document.querySelector('#mt-sidebar-toggle > .mt-icon');
    if (!toggleIcon) return;
    toggleIcon.classList.remove('mt-icon_caret-left-solid', 'mt-icon_caret-right-solid');
    toggleIcon.classList.add(isExpanded ? 'mt-icon_caret-left-solid' : 'mt-icon_caret-right-solid');
  }

  function toggleMainMenuDrawer(){
    document.querySelector('.mt-sidebar-overlay')?.classList.toggle('show');
  }

  function closeMainMenuDrawer(){
    document.querySelector('.mt-sidebar-overlay')?.classList.remove('show');
  }

  function updateDesktopContentVisibility(){
    const sidebar = document.querySelector('.mt-sidebar');
    if (!sidebar) return;
    sidebar.classList[expanded ? 'remove' : 'add']('mt-sidebar_collapsed');
  }

  function onViewportWide(callback) {
    if (typeof callback !== 'function') return;

    const mediaQuery = window.matchMedia('(min-width: 1200px)');

    if (mediaQuery.matches) {
      callback(mediaQuery);
    }

    const handler = (event) => {
      if (event.matches) {
        callback(event);
      }
    };

    mediaQuery.addEventListener('change', handler);
    return () => mediaQuery.removeEventListener('change', handler);
  }

  function sidebarInit(){
    const sidebar = document.querySelector('.mt-sidebar');
    if (sidebar) {
      const def = sidebar.dataset.sidebarDefault || 'collapsed';
      expanded = (def === 'expanded');
    }

    updateDesktopContentVisibility();
    sidebarUpdateToggle(expanded);

    document.addEventListener('MT_SIDEBAR_TOGGLE', function() {
      expanded = !expanded;
      updateDesktopContentVisibility();
      sidebarUpdateToggle(expanded);
    });

    document.addEventListener('MT_MENU_TOGGLE', function() {
      toggleMainMenuDrawer();
    });

    onViewportWide(closeMainMenuDrawer);
  }

  document.addEventListener('DOMContentLoaded', sidebarInit);
</script>

