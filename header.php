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

          <ul class="nav-tabs">
            <li><a class="nav-tab is-active" href="/account-overview/">Account overview</a></li>
            <li><button class="nav-tab" type="button" title="Coming soon">Referrals</button></li>
            <li><button class="nav-tab" type="button" title="Coming soon">Payouts</button></li>
            <li><a class="nav-tab" href="https://help.megatrader.io/en/" target="_blank" rel="noopener">Help center</a></li>
          </ul>

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

            <button id="mega-burger" class="icon-btn burger" type="button" aria-label="Open menu"
                    aria-controls="mega-mobile-menu" aria-expanded="false">
              <div class="mt-icon mt-icon-white mt-icon_menu"></div>
            </button>
          </div>

          <div id="mega-mobile-menu" class="mega-mobile-menu">
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

    const SPEED = 520;
    const EASING = 'cubic-bezier(.22,.85,.36,1)';
    const prefersReduce = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;

    drawer.style.transitionProperty = 'height, opacity';
    drawer.style.transitionDuration = `${SPEED}ms, ${SPEED}ms`;
    drawer.style.transitionTimingFunction = `${EASING}, linear`;

    setClosedStyles();

    let animating = false;

    function setClosedStyles() {
      drawer.classList.remove('is-open');
      drawer.style.height = '0px';
      drawer.style.opacity = '0';
      drawer.style.pointerEvents = 'none';
    }
    function setOpenInteractive() {
      drawer.style.pointerEvents = 'auto';
    }

    function onHeightEnd(cb) {
      let done = false;
      const end = (e) => {
        if (e.target === drawer && e.propertyName === 'height') {
          cleanup();
        }
      };
      const cleanup = () => {
        if (done) return;
        done = true;
        drawer.removeEventListener('transitionend', end);
        animating = false;
        cb && cb();
      };
      drawer.addEventListener('transitionend', end);
      setTimeout(cleanup, SPEED + 100); 
    }

    function openDrawer() {
      if (animating || drawer.classList.contains('is-open')) return;
      animating = true;
      burger.setAttribute('aria-expanded', 'true');
      drawer.classList.add('is-open');

      if (prefersReduce) {
        drawer.style.opacity = '1';
        drawer.style.height = 'auto';
        setOpenInteractive();
        animating = false;
        return;
      }

      drawer.style.opacity = '0';
      drawer.style.height = '0px';
      drawer.offsetHeight; 

      const target = drawer.scrollHeight;
      requestAnimationFrame(() => {
        drawer.style.height = target + 'px';
        drawer.style.opacity = '1';
        setOpenInteractive();
      });

      onHeightEnd(() => {
        drawer.style.height = 'auto'; 
      });
    }

    function closeDrawer() {
      if (animating || !drawer.classList.contains('is-open')) return;
      animating = true;
      burger.setAttribute('aria-expanded', 'false');

      if (prefersReduce) {
        setClosedStyles();
        return;
      }

      const start = drawer.getBoundingClientRect().height || drawer.scrollHeight;
      drawer.style.height = start + 'px';
      drawer.style.opacity = '1';
      drawer.offsetHeight; 

      requestAnimationFrame(() => {
        requestAnimationFrame(() => {
          drawer.style.height = '0px';
          drawer.style.opacity = '0';
          drawer.style.pointerEvents = 'none';
        });
      });

      onHeightEnd(() => {
        drawer.style.height = '0px';
        drawer.style.opacity = '0';
        drawer.style.pointerEvents = 'none';
        drawer.classList.remove('is-open');
      });
    }

    function toggleDrawer(e) {
      if (e) e.preventDefault();
      if (animating) return;
      drawer.classList.contains('is-open') ? closeDrawer() : openDrawer();
    }

    burger.addEventListener('click', toggleDrawer);
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeDrawer(); });

    drawer.addEventListener('click', (e) => {
      const el = e.target.closest('a.nav-tab, button.nav-tab, .mm-link');
      if (el) closeDrawer();
    });

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

