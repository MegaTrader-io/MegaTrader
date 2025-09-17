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
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet">
  <?php wp_head(); ?>
</head>

<?php
function mt_active_class($current, $prefix)
{
  $prefix = strtolower(trailingslashit(parse_url($prefix, PHP_URL_PATH) ?: $prefix));
  return (strpos($current, $prefix) === 0) ? ' is-active' : '';
}
?>

<body <?php body_class(); ?>>
  <?php wp_body_open(); ?>

  <div class="preloader">
    <div class="preloader-inner">
      <span class="loader"></span>
    </div>
  </div>

  <?php Mt_Navbar::render_navbar(
          section: 'account',
          classes_navbar: 'mt-navbar--my-account'
  ); ?>

  <script>
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