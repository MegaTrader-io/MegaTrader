<div class="mt-navbar__account">
<!--
    <button class="mt-navbar__auth-link mega-btn-md mega-btn-secondary-md w-100" type="button" aria-label="Notifications">
        <div class="mt-icon mt-icon-white mt-icon_notifications"></div>
    </button>
    <button class="mt-navbar__auth-link mega-btn-md mega-btn-secondary-md w-100" type="button" aria-label="Profile">
        <div class="mt-icon mt-icon-white mt-icon_account"></div>
    </button>
    <button id="mega-burger" class="d-none d-md-block d-lg-none mt-navbar__auth-link mega-btn-md mega-btn-secondary-md w-100 burger" type="button" aria-label="Open menu"
            aria-controls="mega-mobile-menu" aria-expanded="false"
            onclick="this.dispatchEvent(new CustomEvent('MT_SIDEBAR_TOGGLE', { bubbles:true }));">
        <div class="mt-icon mt-icon-white mt-icon_menu"></div>
    </button>
-->
    <a id="mt-sidebar-toggle" class="p-2" href="javascript:void(0);" onclick="this.dispatchEvent(new CustomEvent('MT_MENU_TOGGLE', { bubbles:true }));">
        <i class="mt-icon mt-icon-white mt-icon_menu"></i>
    </a>
</div>