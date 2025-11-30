<?php

defined('ABSPATH') || exit;

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
  let sidebarIsExpanded = <?= json_encode($sidebar_default_expanded) ?>;

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
    sidebar.classList[sidebarIsExpanded ? 'remove' : 'add']('mt-sidebar_collapsed');
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
      sidebarIsExpanded = (def === 'expanded');
    }

    updateDesktopContentVisibility();
    sidebarUpdateToggle(sidebarIsExpanded);

    document.addEventListener('MT_SIDEBAR_TOGGLE', function() {
      sidebarIsExpanded = !sidebarIsExpanded;
      updateDesktopContentVisibility();
      sidebarUpdateToggle(sidebarIsExpanded);
    });

    document.addEventListener('MT_MENU_TOGGLE', function() {
      toggleMainMenuDrawer();
    });

    onViewportWide(closeMainMenuDrawer);
  }

  document.addEventListener('DOMContentLoaded', sidebarInit);
</script>

