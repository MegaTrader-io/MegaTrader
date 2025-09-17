<?php
/**
 * Template part: Account Data
 * - Login = email del usuario logueado
 * - Server = hardcode temporal
 * - Password = bullets + ojito (muestra "password")
 * - Badges de acceso con href="#"
 * - Avatar de plataforma con fallback a imagen provista
 */
if (!defined('ABSPATH'))
  exit;

// accountId (string); se usa si lo necesitas luego
$account_id = isset($args['meta']['accountId']) ? sanitize_text_field($args['meta']['accountId']) : '';

// Usuario actual
$current_user = wp_get_current_user();
$user_email = ($current_user && $current_user->exists()) ? (string) $current_user->user_email : '';

// === Valores de UI solicitados ===
$login = $user_email;            // Login = email usuario
$server = 'PurpleTradingSC-02';   // Hardcode temporal
$pwd = 'password';             // Valor real a revelar en la demo

// Plataforma (imagen con fallback provisto)
$platform_img = 'https://subscriptions.megatrader.io/wp-content/uploads/2025/07/Stylecolor-Sizelg.svg';
if (!empty($args['platform_image'])) {
  $platform_img = esc_url_raw($args['platform_image']);
}

// Links de acceso (siempre visibles, por ahora '#')
$link_web = 'http://trade.megatrader.io';
$link_appstore = '#';
$link_playstore = '#';
?>

<div class="mt-card">
  <div class="d-flex align-items-center gap-3 w-100 justify-content-between flex-column flex-lg-row flex-md-row">
    <!-- IZQ: títulos + accesos (no se achica) -->
    <div class="d-flex flex-column gap-1 flex-wrap flex-shrink-0 flex-grow-1">
      <div class="text-white fw-500 text-2xl text-uppercase"><?php esc_html_e('Trading Account', 'megatrader'); ?></div>
      <div class="text-16 fw-500"><?php esc_html_e('Access the platform', 'megatrader'); ?></div>

      <!-- Badges de acceso -->
      <div class="d-flex gap-2 pt-3 flex-wrap">
        <a href="<?php echo esc_url($link_web); ?>" target="_self" class="text-decoration-none" rel="noopener">
          <div class="mt-badge mt-badge-apps">
            <i class="mt-icon mt-icon-sm mt-icon_globe"></i>
            <span class="mt-card__links__text">Web App</span>
          </div>
        </a>

        <a href="<?php echo esc_url($link_appstore); ?>" target="_self" class="text-decoration-none" rel="noopener">
          <div class="mt-badge mt-badge-apps">
            <i class="mt-icon mt-icon-sm mt-icon_app-store"></i>
            <span class="mt-card__links__text">Store</span>
          </div>
        </a>

        <a href="<?php echo esc_url($link_playstore); ?>" target="_self" class="text-decoration-none" rel="noopener">
          <div class="mt-badge mt-badge-apps">
            <i class="mt-icon mt-icon-sm mt-icon_google-play"></i>
            <span class="mt-card__links__text">Google Store</span>
          </div>
        </a>
      </div>
    </div>

    <!-- Divisor: vertical en md+ y horizontal en mobile -->
    <div class="vr d-none d-md-block"></div>
    <div class="d-block d-md-none w-100 h-1px bg-404040"></div>

    <!-- DER: credenciales (ocupa el resto y permite truncar) -->
    <div class="d-flex align-items-center gap-2 flex-grow-1 min-w-0 w-max-320px">
      <div class="mt-platform-avatar flex-shrink-0">
        <img src="<?php echo esc_url($platform_img); ?>" alt="DXXT logo" width="64" height="64"
          style="width:64px;height:64px;border-radius:9999px;object-fit:cover;" />
      </div>

      <!-- Stack de campos: debe poder encoger -->
      <div class="d-flex flex-column gap-2 flex-grow-1 min-w-0">
        <!-- Login (email) -->
        <div class="d-flex align-items-center flex-nowrap gap-2 min-w-0">
          <div class="text-white flex-shrink-0"><?php esc_html_e('Login', 'megatrader'); ?></div>

          <!-- contenedor con truncate + padding derecho -->
          <div class="flex-grow-1 text-truncate pe-2 min-w-0"">
            <span class="text-base text-a8a29e fw-medium" title="<?php echo esc_attr($login); ?>">
              <?php echo $login ? esc_html($login) : '--'; ?>
            </span>
          </div>

          <!-- icon copy (24x24, sin .btn) -->
          <?php if ($login): ?>
            <span class="d-inline-flex align-items-center justify-content-center flex-shrink-0"
              data-copy="<?php echo esc_attr($login); ?>" role="button" tabindex="0"
              aria-label="<?php esc_attr_e('Copy login', 'megatrader'); ?>"
              title="<?php esc_attr_e('Copy login', 'megatrader'); ?>">
              <i class="mt-icon mt-icon-white mt-icon_content-copy" aria-hidden="true"></i>
            </span>
          <?php endif; ?>
        </div>

        <!-- Password (oculto + ojito + copiar) -->
        <div class="d-flex align-items-center flex-nowrap min-w-0 gap-3" data-pwd-row>
          <div class="text-white flex-shrink-0"><?php esc_html_e('Password', 'megatrader'); ?></div>

          <!-- contenedor con truncate + padding derecho -->
          <div class="flex-grow-1 text-truncate pe-2 min-w-0">
            <span class="text-base text-a8a29e fw-medium js-pwd-mask"
              title="<?php esc_attr_e('Hidden password', 'megatrader'); ?>">
              ••••••••••••
            </span>
          </div>

          <div class="d-flex align-items-center gap-2 ms-1 flex-shrink-0">
            <!-- ojito (24x24, sin .btn) -->
            <span class="d-inline-flex align-items-center justify-content-center js-pwd-toggle"
              data-pwd="<?php echo esc_attr($pwd); ?>" role="button" tabindex="0" aria-expanded="false"
              aria-label="<?php esc_attr_e('Show/Hide password', 'megatrader'); ?>"
              title="<?php esc_attr_e('Show/Hide password', 'megatrader'); ?>">
              <i class="mt-icon mt-icon-white mt-icon_visibility" aria-hidden="true"></i>
            </span>

            <!-- copy (24x24, sin .btn) -->
            <span class="d-inline-flex align-items-center justify-content-center"
              data-copy="<?php echo esc_attr($pwd); ?>" role="button" tabindex="0"
              aria-label="<?php esc_attr_e('Copy password', 'megatrader'); ?>"
              title="<?php esc_attr_e('Copy password', 'megatrader'); ?>">
              <i class="mt-icon mt-icon-white mt-icon_content-copy" aria-hidden="true"></i>
            </span>
          </div>
        </div>

        <!-- Server (hardcode) -->
        <div class="d-flex align-items-center flex-nowrap min-w-0 gap-3">
          <div class="text-white flex-shrink-0"><?php esc_html_e('Server', 'megatrader'); ?></div>
          <div class="text-base text-a8a29e fw-medium text-truncate min-w-0">
            <?php echo esc_html($server); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>