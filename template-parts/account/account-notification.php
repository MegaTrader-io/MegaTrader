<?php
defined('ABSPATH') || exit;

// 1) Usuario/email y lote (BD si existe sync; si no, helper original)
$user = wp_get_current_user();
$user_email = $args['user_email'] ?? ($user->user_email ?? '');
$notifications = [];

/**
 * Paleta por severidad (success|error|warning)
 */
if (!function_exists('mt_notif_palette')) {
  function mt_notif_palette(string $severity): array
  {
    $s = strtolower($severity);
    switch ($s) {
      case 'success':
        return ['bg' => 'bg-success-100', 'dot' => 'bg-success-600', 'right' => 'text-success-600'];
      case 'error':
        return ['bg' => 'bg-error-100', 'dot' => 'bg-error-600', 'right' => 'text-error-600'];
      default:
        return ['bg' => 'bg-warning-100', 'dot' => 'bg-warning-600', 'right' => 'text-warning-600'];
    }
  }
}

/**
 * Meta desde Label (mapa de tipos → {severity,title,message,icon})
 */
if (!function_exists('mt_notif_meta_from_type')) {
  function mt_notif_meta_from_type(?string $apiType): array
  {
    $apiType = is_string($apiType) ? trim($apiType) : '';
    if ($apiType === '')
      return [];

    $keyL = strtolower($apiType);

    // 1) Primero: mapa guardado en WP (admin)
    $opt = function_exists('mtun_get_map')
      ? mtun_get_map()
      : get_option(defined('MTUN_OPTION_MAP') ? MTUN_OPTION_MAP : 'mtun_notification_map_v1', []);
    if (is_array($opt) && !empty($opt)) {
      foreach ($opt as $k => $v) {
        if (strtolower((string) $k) === $keyL && is_array($v)) {
          $sev = isset($v['severity']) ? (string) $v['severity'] : 'warning';
          return [
            'severity' => strtolower($sev ?: 'warning'),
            'title' => (string) ($v['title'] ?? ''),
            'message' => (string) ($v['message'] ?? ''),
            'icon' => (string) ($v['icon'] ?? ''),
          ];
        }
      }
    }

    // 2) Fallback: clase Label (si existe)
    if (class_exists('Label')) {
      $map = [];
      if (defined('Label::NOTIFICATION_MAP'))
        $map = Label::NOTIFICATION_MAP;
      elseif (defined('Label::NOTIFICATIONS'))
        $map = Label::NOTIFICATIONS;
      elseif (defined('Label::NOTIFICATION_TYPE_MAP'))
        $map = Label::NOTIFICATION_TYPE_MAP;

      if (is_array($map) && !empty($map)) {
        foreach ($map as $k => $v) {
          if (strtolower((string) $k) === $keyL && is_array($v)) {
            $sev = isset($v['severity']) ? (string) $v['severity'] : (string) ($v['level'] ?? 'warning');
            return [
              'severity' => strtolower($sev ?: 'warning'),
              'title' => (string) ($v['title'] ?? ''),
              'message' => (string) ($v['message'] ?? ''),
              'icon' => (string) ($v['icon'] ?? ''),
            ];
          }
        }
      }
    }

    return [];
  }
}


// 2) Traer lote: primero desde BD (sync), si no existe fallback al helper antiguo
if ($user_email) {
  if (function_exists('mt_user_notifs_sync_for_user')) {
    $notifications = mt_user_notifs_sync_for_user((int) $user->ID, (string) $user_email, 100);
  } elseif (function_exists('mt_notifications_payload_for_email')) {
    $notifications = mt_notifications_payload_for_email($user_email, 1, 100);
  }
}

// Render
?>
<div class="mt-account-notifications" role="dialog" aria-modal="true" tabindex="-1" hidden
  data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>"
  data-nonce="<?php echo esc_attr(wp_create_nonce('mt_user_notifs')); ?>">
  <div class="mt-notifications__arrow d-none d-md-block" aria-hidden="true"></div>

  <div class="mt-notifications__header d-flex align-items-center gap-2 d-md-none mb-3">
    <span class="m-0 text-2xl text-uppercase text-131210 fw-light">Notifications</span>
    <button type="button" class="btn btn-link ms-auto p-0" data-mt-notif-close aria-label="Close">
      <i class="mt-icon mt-icon_close_solid mt-icon-dark"></i>
    </button>
  </div>

  <div class="mt-notifications__body subscription-scroll-area">
    <?php foreach ($notifications as $n):
      // === Fuente visual prioritaria (BD) ===
      $saved = is_array($n['__mt_map'] ?? null) ? $n['__mt_map'] : [];

      // === Fallback por tipo (Label) ===
      $apiType = (string) ($n['meta']['apiType'] ?? '');
      $fallback = mt_notif_meta_from_type($apiType);

      // === Severidad / título / icono (prioridad: BD → Label) ===
      $severity = (string) ($saved['severity'] ?? $fallback['severity'] ?? 'warning');
      $title = (string) ($saved['title'] ?? $fallback['title'] ?? '');
      $icon = (string) ($saved['icons'] ?? $fallback['icon'] ?? '');

      // === Mensaje: prioriza el del payload (puede venir personalizado), si no el del mapa
      $msg = (string) ($n['message'] ?? $fallback['message'] ?? '');

      // === IDs y UID ===
      $accIdRaw = (string) ($n['meta']['accountId_raw'] ?? '');
      $displayId = (string) ($n['id'] ?? ''); // MT-XXXX
      $createdAt = (string) ($n['meta']['createdAt'] ?? '');
      $notifUid = (string) ($n['uuid'] ?? ''); // al sync desde BD lo trae aquí
    
      if ($notifUid === '') {
        $typeKey = $apiType;
        $notifUid = substr(md5($typeKey . '|' . $createdAt . '|' . $accIdRaw), 0, 16);
      }

      $pal = mt_notif_palette($severity);
      $hideCtrlId = 'mt-notif-hide-' . preg_replace('/[^a-zA-Z0-9_\-]/', '', ($displayId !== '' ? $displayId : $notifUid));
      ?>
      <article class="mt-notification-account card border-0 p-12 <?php echo !empty($n['read']) ? 'bg-gray-100' : ''; ?>"
        data-mt-notif-item data-type="<?php echo esc_attr(strtolower($apiType)); ?>">

        <div class="d-inline-flex align-items-start gap-3 w-100">
          <!-- Bubble -->
          <div class="position-relative rounded-circle flex-shrink-0 <?php echo esc_attr($pal['bg']); ?>"
            style="width:32px;height:32px;">
            <?php if ($icon !== ''): ?>
              <i class="mt-icon <?php echo esc_attr($icon); ?>"
                style="position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);font-size:16px;"></i>
            <?php else: ?>
              <span
                class="position-absolute start-50 top-50 translate-middle rounded-circle d-block <?php echo esc_attr($pal['dot']); ?>"
                style="width:20px;height:20px;"></span>
            <?php endif; ?>
          </div>

          <!-- Texto + control ocultar -->
          <div class="flex-grow-1 d-flex flex-column gap-1">
            <div class="d-flex align-items-start gap-2">
              <span
                class="mt-chip mt-chip--light text-uppercase fw-bold px-1 py-1 rounded-1 bg-gray-200 small js-mt-goto-account"
                data-mt-account-id="<?php echo esc_attr($accIdRaw); ?>"
                data-mt-account-disp="<?php echo esc_attr($displayId); ?>" role="button" tabindex="0"
                aria-label="Switch to account <?php echo esc_attr($displayId); ?>">
                <?php echo esc_html($displayId); ?>
              </span>

              <!-- Ojo (ocultar): exponemos data-id y data-uid para máxima compat JS -->
              <span id="<?php echo esc_attr($hideCtrlId); ?>" class="ms-auto mt-notif-hide-ctrl" data-mt-notif-hide
                data-id="<?php echo esc_attr($notifUid); ?>" data-uid="<?php echo esc_attr($notifUid); ?>" role="button"
                aria-label="Hide this notification" tabindex="0">
                <i class="mt-icon mt-icon_visibility-off mt-icon-dark"></i>
              </span>
            </div>

            <?php if ($title !== ''): ?>
              <div class="fw-bold text-14px-line-20px <?php echo esc_attr($pal['right']); ?>">
                <?php echo esc_html($title); ?>
              </div>
            <?php endif; ?>

            <?php if ($msg !== ''): ?>
              <div class="text-black fw-500 text-14px-line-20px"><?php echo esc_html($msg); ?></div>
            <?php endif; ?>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</div>