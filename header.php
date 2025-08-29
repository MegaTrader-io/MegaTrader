<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package megatrader
 */
global $product;

?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet">

  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <?php wp_body_open(); ?>

  <div class="preloader">
    <div class="preloader-inner">
      <span class="loader"></span>
    </div>
  </div>

  <div class="ot-header header-layout1">
    <div class="sticky-wrapper">
      <?php do_action('mega_sticky_promo_render_banner'); ?>
      <div class="menu-area">
        <div class="container">
          <nav class="mega-navbar" role="navigation" aria-label="Primary">
            <!-- Left cluster -->
            <div class="nav-left">
              <a class="brand-tile" href="https://megatrader.io/">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/megatrader-mobile-original.svg"
                  alt="MegaTrader" width="60" height="60" loading="eager" />
              </a>

              <!-- Wordmark / logotipo ancho (usa el que ya tienes) -->
              <a class="brand-wordmark" href="https://megatrader.io/">
                <img class="logo" src="<?php echo get_template_directory_uri(); ?>/assets/img/megatrader-original.svg"
                  alt="MegaTrader" height="28" loading="eager" />
              </a>
            </div>

            <!-- Center tabs (oculto en mobile) -->
            <ul class="nav-tabs">
              <li>
                <a class="nav-tab is-active" href="/account-overview/">
                  Account overview
                </a>
              </li>
              <li>
                <button class="nav-tab" type="button" title="Coming soon">
                  Referrals
                </button>
              </li>
              <li>
                <button class="nav-tab" type="button" title="Coming soon">
                  Payouts
                </button>
              </li>
              <li>
                <a class="nav-tab" href="https://help.megatrader.io/en/" target="_blank" rel="noopener">
                  Help center
                </a>
              </li>
            </ul>

            <!-- Right cluster -->
            <div class="nav-right">
              <button class="icon-btn" type="button" aria-label="Notifications">
                <div class="mt-icon mt-icon-success mt-icon_notifications"></div>
              </button>
              <button class="icon-btn" type="button" aria-label="Profile">
                <div class="mt-icon mt-icon-white mt-icon_account"></div>
              </button>
              <button class="icon-btn" type="button" aria-label="Logout" onclick="handleLogout(event)">
                <div class="mt-icon mt-icon-white mt-icon_logout"></div>
              </button>

              <!-- Burger (solo visible en ≤lg) -->
              <button id="mega-burger" class="icon-btn burger" type="button" aria-label="Open menu"
                aria-controls="mega-mobile-menu" aria-expanded="false">
                <div class="mt-icon mt-icon-white mt-icon_menu"></div>
              </button>
            </div>
          </nav>
        </div>

        <!-- Mobile drawer -->
        <div id="mega-mobile-menu" class="mega-mobile-menu" hidden>
          <div class="mm-inner">
            <ul class="mm-links" role="menu">
              <li role="none"><a role="menuitem" href="/account-overview/" class="mm-link is-active">Account
                  overview</a></li>
              <li role="none"><button role="menuitem" class="mm-link" type="button" disabled>Referrals</button></li>
              <li role="none"><button role="menuitem" class="mm-link" type="button" disabled>Payouts</button></li>
              <li role="none"><a role="menuitem" href="https://help.megatrader.io/en/" target="_blank" rel="noopener"
                  class="mm-link">Help center</a></li>
            </ul>
          </div>
        </div>
      </div>

    </div>
  </div>


  <script>
    function handleLogout(e) {
      fetch('<?php echo wp_logout_url(); ?>', {
        method: 'GET',
        credentials: 'include'
      }).then(() => {
        window.location.href = 'https://megatrader.io/';
      });
    }
    (function () {
      // ===== Burger toggle =====
      const burger = document.getElementById('mega-burger');
      const drawer = document.getElementById('mega-mobile-menu');

      function openDrawer() {
        if (!drawer) return;
        drawer.hidden = false;
        document.documentElement.classList.add('mm-open');
        burger?.setAttribute('aria-expanded', 'true');
      }
      function closeDrawer() {
        if (!drawer) return;
        drawer.hidden = true;
        document.documentElement.classList.remove('mm-open');
        burger?.setAttribute('aria-expanded', 'false');
      }
      function toggleDrawer() {
        if (drawer?.hidden) openDrawer(); else closeDrawer();
      }

      burger?.addEventListener('click', toggleDrawer);
      drawer?.addEventListener('click', (e) => {
        // cierra si clic fuera de enlaces
        if (e.target === drawer) closeDrawer();
      });
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeDrawer();
      });

      // ===== Setear variable con altura real del header para el padding-top del body =====
      const headerEl = document.querySelector('.mega-navbar');
      const promo = document.querySelector('[data-mega-sticky-promo], .mega-sticky-promo'); // por si tu banner usa un data-attr/clase

      function setHeaderHeightVar() {
        // si hay banner arriba, cuenta su alto también
        const promoH = promo ? promo.getBoundingClientRect().height : 0;
        const h = (headerEl ? headerEl.getBoundingClientRect().height : 0) + promoH;
        document.documentElement.style.setProperty('--mt-header-height', h + 'px');
      }
      window.addEventListener('load', setHeaderHeightVar);
      window.addEventListener('resize', setHeaderHeightVar);
      const ro = (window.ResizeObserver) ? new ResizeObserver(setHeaderHeightVar) : null;
      headerEl && ro?.observe(headerEl);
      promo && ro?.observe(promo);

      // ===== Marcar pestaña activa (simple heurística) =====
      try {
        const path = location.pathname.toLowerCase();
        const markActive = (selector, cond) => {
          const el = document.querySelector(selector);
          if (el && cond) el.classList.add('is-active');
        };
        markActive('.nav-tabs a[href="/account-overview/"]', path.includes('/account-overview'));
        markActive('.mega-mobile-menu a[href="/account-overview/"]', path.includes('/account-overview'));
      } catch (_) { }
    })();
  </script>