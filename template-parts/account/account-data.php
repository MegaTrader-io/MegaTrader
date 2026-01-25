<?php
/**
 * Template part: Account Data
 */
if (!defined('ABSPATH')) exit;

// accountId (string)
$account_id = isset($args['meta']['accountId']) ? sanitize_text_field($args['meta']['accountId']) : '';

$current_user = wp_get_current_user();
$user_email = ($current_user && $current_user->exists()) ? (string) $current_user->user_email : '';

$data = isset($args['data']) && is_array($args['data']) ? $args['data'] : [];

$agreement_missing = ($args['meta']['agreementShow'] ?? '') === '1';

$login  = $agreement_missing ? 'sample@megatrader.io' : ($data['login'] ?? null);
$server = ($data['server'] ?? null);
$pwd    = $agreement_missing ? null : ($data['password'] ?? null);

// =============================
// Platform dynamic variables
// =============================
$platform_key = trim((string)($data['platform'] ?? ''));

$platform_term = null;
$platform_icon_url = '';
$platform_links = [];

// 1) Resolver term por API platform key
if ($platform_key !== '' && function_exists('mt_platform_term_by_api_key')) {
  $platform_term = mt_platform_term_by_api_key($platform_key);
}

// 2) Icon URL desde term (esto ES el logo final)
if ($platform_term && !empty($platform_term->term_id) && function_exists('mt_platform_icon_url_from_term')) {
  $platform_icon_url = (string) mt_platform_icon_url_from_term($platform_term);
}

// 3) Links: vienen dentro de term meta "custom_repeater_field" como string que inicia con "@links"
if ($platform_term && !empty($platform_term->term_id)) {
  $rep = get_term_meta((int)$platform_term->term_id, 'custom_repeater_field', true);

  if (is_array($rep)) {
    foreach ($rep as $row) {
      if (!is_string($row)) continue;

      $row_trim = trim($row);

      // buscamos la línea que empieza con @links
      if (stripos($row_trim, '@links') !== 0) continue;

      // quitamos "@links" y nos quedamos con el JSON-like
      $json_like = trim(substr($row_trim, 6)); // 6 = strlen("@links")

      // Normaliza comillas curvas “ ” a comillas normales "
      $json_like = str_replace(["\u{201C}", "\u{201D}", "“", "”"], '"', $json_like);

      // Intentamos parsear JSON
      $decoded = json_decode($json_like, true);

      // Si falló, intenta rescatar desde el primer '[' hasta el último ']'
      if (json_last_error() !== JSON_ERROR_NONE) {
        $lb = strpos($json_like, '[');
        $rb = strrpos($json_like, ']');
        if ($lb !== false && $rb !== false && $rb > $lb) {
          $slice = substr($json_like, $lb, $rb - $lb + 1);
          $decoded = json_decode($slice, true);
        }
      }

      if (is_array($decoded)) {
        foreach ($decoded as $btn) {
          if (!is_array($btn)) continue;

          // icon viene ya como clase completa (ej: "mt-icon_download")
          $icon = trim((string)($btn['icon'] ?? ''));
          $text = (string)($btn['text'] ?? '');
          $url  = (string)($btn['url'] ?? '');

          // tu formato: URL como key
          if ($url === '') {
            foreach ($btn as $k => $v) {
              if (is_string($k) && preg_match('#^https?://#i', $k)) { $url = $k; break; }
            }
          }

          if ($text !== '' && $url !== '') {
            $platform_links[] = [
              'icon' => $icon,
              'text' => $text,
              'url'  => $url,
            ];
          }
        }
      }

      // solo usamos el primer @links encontrado
      break;
    }
  }
}

// =============================
// Skeleton flags (solo logo + links)
// =============================
$has_logo  = ($platform_icon_url !== '');
$has_links = !empty($platform_links);

// =============================
// Agreement URL
// =============================
$agreement_url = '';
$root = $args['meta']['agreementShow'] ?? null;
if ($root !== null) {
  $agreement_url = (string) ($args['meta']['agreementUrl'] ?? '');
}
?>

<div class="mt-card" data-account-id="<?php echo esc_attr($account_id); ?>">
  <div class="mt-card-wrapper d-flex align-items-center gap-3 w-100 justify-content-between flex-column flex-lg-row flex-md-row">

    <div class="mt-card-content d-flex align-items-center gap-3 w-100 justify-content-between flex-column flex-lg-row flex-md-row">

      <div class="d-flex flex-column gap-1 flex-wrap flex-shrink-0">
        <div class="text-white fw-500 text-2xl text-uppercase">
          <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['account_data_title']); ?>
        </div>
        <div class="text-16 fw-500">
          <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['account_platform_description']); ?>
        </div>

        <!-- LINKS: si no vienen (SSR), skeleton; si vienen, render real -->
        <div class="d-flex gap-2 pt-3 flex-wrap">
          <?php if ($has_links): ?>
            <?php foreach ($platform_links as $btn):
              $btn_icon = trim((string)($btn['icon'] ?? ''));
              $btn_text = (string)($btn['text'] ?? '');
              $btn_url  = (string)($btn['url'] ?? '');
              if ($btn_url === '' || $btn_text === '') continue;
            ?>
              <a href="<?php echo esc_url($btn_url); ?>" target="_blank" class="text-decoration-none" rel="noopener">
                <div class="mt-badge mt-badge-apps">
                  <?php if ($btn_icon !== ''): ?>
                    <i class="mt-icon mt-icon-sm <?php echo esc_attr($btn_icon); ?>"></i>
                  <?php endif; ?>
                  <span class="mt-card__links__text"><?php echo esc_html($btn_text); ?></span>
                </div>
              </a>
            <?php endforeach; ?>
          <?php else: ?>
            <!-- skeleton only for links -->
            <span class="mt-skeleton-pulse d-inline-block rounded-pill" style="width:92px;height:28px;"></span>
            <span class="mt-skeleton-pulse d-inline-block rounded-pill" style="width:92px;height:28px;"></span>
            <span class="mt-skeleton-pulse d-inline-block rounded-pill" style="width:92px;height:28px;"></span>
          <?php endif; ?>
        </div>
      </div>

      <div class="vr d-none d-md-block"></div>
      <div class="d-block d-md-none w-100 h-1px bg-404040"></div>

      <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0 mt-account-data-credentials">
        <div class="mt-platform-avatar flex-shrink-0">
          <?php if ($has_logo): ?>
            <img
              src="<?php echo esc_url($platform_icon_url); ?>"
              alt="Platform logo"
              width="64"
              height="64"
              style="width:64px;height:64px;border-radius:9999px;object-fit:cover;"
            />
          <?php else: ?>
            <!-- skeleton only for logo -->
            <span class="mt-skeleton-pulse d-inline-block" style="width:64px;height:64px;border-radius:9999px;"></span>
          <?php endif; ?>
        </div>

        <div class="d-flex flex-column gap-2 flex-grow-1 min-w-0">
          <div class="d-flex align-items-center flex-nowrap gap-2 min-w-0">
            <div class="text-white flex-shrink-0">
              <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['account_login']); ?>
            </div>

            <div class="flex-grow-1 text-truncate pe-2 min-w-0">
              <span class="text-base text-a8a29e fw-medium" title="<?php echo esc_attr($login); ?>">
                <?php echo $login ? esc_html($login) : '--'; ?>
              </span>
            </div>

            <?php if ($login): ?>
              <span class="d-inline-flex align-items-center justify-content-center flex-shrink-0"
                data-copy="<?php echo esc_attr($login); ?>" role="button" tabindex="0"
                aria-label="<?php esc_attr_e('Copy login', 'megatrader'); ?>"
                title="<?php esc_attr_e('Copy login', 'megatrader'); ?>">
                <i class="mt-icon mt-icon-white mt-icon_content-copy" aria-hidden="true"></i>
              </span>
            <?php endif; ?>
          </div>

          <div class="d-flex align-items-center flex-nowrap min-w-0 gap-3" data-pwd-row>
            <div class="text-white flex-shrink-0">
              <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['account_password']); ?>
            </div>

            <div class="flex-grow-1 text-truncate pe-2 min-w-0">
              <span class="text-base text-a8a29e fw-medium js-pwd-mask"
                title="<?php esc_attr_e('Hidden password', 'megatrader'); ?>">
                ••••••••••••
              </span>
            </div>

            <div class="d-flex align-items-center gap-2 ms-1 flex-shrink-0">
              <span class="d-inline-flex align-items-center justify-content-center js-pwd-toggle"
                data-pwd="<?php echo esc_attr($pwd); ?>" role="button" tabindex="0" aria-expanded="false"
                aria-label="<?php esc_attr_e('Show/Hide password', 'megatrader'); ?>"
                title="<?php esc_attr_e('Show/Hide password', 'megatrader'); ?>">
                <i class="mt-icon mt-icon-white mt-icon_visibility" aria-hidden="true"></i>
              </span>

              <span class="d-inline-flex align-items-center justify-content-center"
                data-copy="<?php echo esc_attr($pwd); ?>" role="button" tabindex="0"
                aria-label="<?php esc_attr_e('Copy password', 'megatrader'); ?>"
                title="<?php esc_attr_e('Copy password', 'megatrader'); ?>">
                <i class="mt-icon mt-icon-white mt-icon_content-copy" aria-hidden="true"></i>
              </span>
            </div>
          </div>

          <div class="d-flex align-items-center flex-nowrap min-w-0 gap-3">
            <div class="text-white flex-shrink-0">
              <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['account_server']); ?>
            </div>
            <div class="text-base text-a8a29e fw-medium text-truncate min-w-0">
              <?php echo esc_html($server); ?>
            </div>
          </div>
        </div>
      </div>

    </div>

    <div class="mt-agreement-overlay" hidden aria-hidden="true">
      <div class="mt-agreement-overlay__inner text-center">
        <div class="mt-agreement-overlay__text">
          <?php echo Label::META_ACCOUNT_OVERVIEW['agreement_overlay_description']; ?>
        </div>

        <a href="<?php echo esc_url($agreement_url ?: '#'); ?>" target="_blank" rel="noopener"
          class="mt-btn mt-btn--md mt-btn--primary mt-agreement-button">
          <?php echo Label::META_ACCOUNT_OVERVIEW['agreement_overlay_button']; ?>
        </a>
      </div>
    </div>

  </div>
</div>
