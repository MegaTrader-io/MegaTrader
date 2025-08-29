<?php
/**
 * The header for our theme
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
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
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
            <a class="brand-wordmark" href="https://megatrader.io/">
              <img class="logo" src="<?php echo get_template_directory_uri(); ?>/assets/img/megatrader-original.svg"
                   alt="MegaTrader" height="28" loading="eager" />
            </a>
          </div>

          <!-- Center tabs (oculto en mobile) -->
          <ul class="nav-tabs">
            <li><a class="nav-tab is-active" href="/account-overview/">Account overview</a></li>
            <li><button class="nav-tab" type="button" title="Coming soon">Referrals</button></li>
            <li><button class="nav-tab" type="button" title="Coming soon">Payouts</button></li>
            <li><a class="nav-tab" href="https://help.megatrader.io/en/" target="_blank" rel="noopener">Help center</a></li>
          </ul>

          <!-- Right cluster -->
          <div class="nav-right">
            <button class="icon-btn" type="button" aria-label="Notifications">
              <div class="mt-icon mt-icon-success mt-icon_notifications"></div>
            </button>
            <button class="icon-btn" type="button" aria-label="Profile">
              <div class="mt-icon mt-icon-white mt-icon_account"></div>
            </button>
            <button id="mega-logout" class="icon-btn logout" type="button" aria-label="Logout" onclick="handleLogout(event)">
              <div class="mt-icon mt-icon-white mt-icon_logout"></div>
            </button>

            <!-- Burger (solo visible en ≤lg) -->
            <button id="mega-burger" class="icon-btn burger" type="button" aria-label="Open menu"
                    aria-controls="mega-mobile-menu" aria-expanded="false">
              <div class="mt-icon mt-icon-white mt-icon_menu"></div>
            </button>
          </div>

          <!-- Mobile drawer (en el flujo) -->
          <div id="mega-mobile-menu" class="mega-mobile-menu" hidden>
            <div class="mm-inner">
              <ul class="mm-links" role="menu">
                <li><a class="nav-tab is-active" href="/account-overview/">Account overview</a></li>
                <li><button class="nav-tab" type="button" title="Coming soon">Referrals</button></li>
                <li><button class="nav-tab" type="button" title="Coming soon">Payouts</button></li>
                <li><a class="nav-tab" href="https://help.megatrader.io/en/" target="_blank" rel="noopener">Help center</a></li>
              </ul>
            </div>
          </div>
        </nav>
        <!-- Mobile drawer -->
      </div>
    </div>
  </div>
</div>

<script>
  function handleLogout(e) {
    fetch('<?php echo wp_logout_url(); ?>', { method: 'GET', credentials: 'include' })
      .then(() => { window.location.href = 'https://megatrader.io/'; });
  }

  (function () {
    const burger = document.getElementById('mega-burger');
    const drawer = document.getElementById('mega-mobile-menu');
    if (!burger || !drawer) return;

    // === Config animación (misma duración abrir/cerrar, más lento)
    const SPEED = 480; // ms
    const EASING = 'cubic-bezier(.22,.85,.36,1)';
    const prefersReduce = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;

    // Evitar el "brinco" por border-top: usamos outline que NO afecta layout
    drawer.style.borderTop = '0';
    drawer.style.outline = '1px solid rgba(64,64,64,.3)';
    drawer.style.outlineOffset = '-1px';

    // Forzar transición consistente
    drawer.style.transitionProperty = 'height, opacity';
    drawer.style.transitionDuration = `${SPEED}ms, ${SPEED}ms`;
    drawer.style.transitionTimingFunction = `${EASING}, linear`;

    let animating = false;

    function onEndHeight(cb) {
      let done = false;
      const handler = (e) => {
        if (e.target === drawer && e.propertyName === 'height') {
          drawer.removeEventListener('transitionend', handler);
          if (!done) { done = true; cb && cb(); }
        }
      };
      drawer.addEventListener('transitionend', handler);
      // Fallback si no dispara
      setTimeout(() => {
        if (!done) { try { drawer.removeEventListener('transitionend', handler); } catch {} cb && cb(); }
      }, SPEED + 80);
    }

    function openDrawer() {
      if (animating || drawer.classList.contains('is-open')) return;
      animating = true;
      burger.setAttribute('aria-expanded', 'true');

      // Mostrar sin parpadeo
      drawer.hidden = false;
      drawer.classList.add('is-open');

      if (prefersReduce) {
        drawer.style.opacity = '1';
        drawer.style.height = 'auto';
        animating = false;
        return;
      }

      // Estado inicial invisible y sin altura en el MISMO frame
      drawer.style.opacity = '0';
      drawer.style.height = '0px';

      // Siguiente frame: medir y animar a su altura real + fade-in sincronizado
      requestAnimationFrame(() => {
        const target = drawer.scrollHeight;
        drawer.style.height = target + 'px';
        drawer.style.opacity = '1';

        onEndHeight(() => {
          drawer.style.height = 'auto'; // flexible a cambios de contenido
          animating = false;
        });
      });
    }

    function closeDrawer() {
      if (animating || !drawer.classList.contains('is-open')) return;
      animating = true;
      burger.setAttribute('aria-expanded', 'false');

      if (prefersReduce) {
        drawer.classList.remove('is-open');
        drawer.hidden = true;
        drawer.style.height = '';
        drawer.style.opacity = '';
        animating = false;
        return;
      }

      // Partimos desde su altura real (si estaba en auto)
      const start = drawer.scrollHeight;
      drawer.style.height = start + 'px';

      // Siguiente frame: colapsar y hacer fade-out con la MISMA duración
      requestAnimationFrame(() => {
        drawer.style.opacity = '0';
        drawer.style.height = '0px';

        onEndHeight(() => {
          drawer.classList.remove('is-open');
          drawer.hidden = true;
          drawer.style.height = '';
          drawer.style.opacity = '';
          animating = false;
        });
      });
    }

    function toggleDrawer(e) {
      if (e) e.preventDefault();
      drawer.classList.contains('is-open') ? closeDrawer() : openDrawer();
    }

    // Eventos
    burger.addEventListener('click', toggleDrawer);
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeDrawer(); });

    // Cerrar al hacer click en un link del menú
    drawer.addEventListener('click', (e) => {
      const el = e.target.closest('a.nav-tab, button.nav-tab, .mm-link');
      if (el) closeDrawer();
    });

    // Ajustar si cambia el contenido mientras está abierto y animando
    if ('ResizeObserver' in window) {
      const ro = new ResizeObserver(() => {
        if (!drawer.classList.contains('is-open')) return;
        if (drawer.style.height && drawer.style.height !== 'auto') {
          drawer.style.height = drawer.scrollHeight + 'px';
        }
      });
      ro.observe(drawer);
    }
  })();
</script>
