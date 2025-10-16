<?php
defined('ABSPATH') || exit;

$user_email   = $args['user_email'] ?? '';
$notifications = [];

/**
 * Paleta visual por nivel (success|error|warning)
 */
if (!function_exists('mt_notif_palette')) {
  function mt_notif_palette(string $type): array
  {
    $t = strtolower($type);
    switch ($t) {
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
 * Lee meta (title/message/icon/level) desde el mapa en constants.php (Label)
 * Admite distintos nombres de constante por si varían.
 * Retorna array ['level','title','message','icon'] (cualquiera puede venir vacío).
 */
if (!function_exists('mt_notif_meta_from_type')) {
  function mt_notif_meta_from_type(?string $apiType): array
  {
    $apiType = is_string($apiType) ? trim($apiType) : '';
    if ($apiType === '' || !class_exists('Label')) return [];

    // Intenta varias constantes por compatibilidad
    $map = [];
    if (defined('Label::NOTIFICATION_MAP')) {
      $map = Label::NOTIFICATION_MAP;
    } elseif (defined('Label::NOTIFICATIONS')) {
      $map = Label::NOTIFICATIONS;
    } elseif (defined('Label::NOTIFICATION_TYPE_MAP')) {
      $map = Label::NOTIFICATION_TYPE_MAP;
    }

    if (!is_array($map) || empty($map)) return [];

    $key = strtolower($apiType);
    foreach ($map as $k => $v) {
      if (strtolower((string) $k) === $key && is_array($v)) {
        return [
          'level'   => isset($v['level']) ? strtolower((string)$v['level']) : '',
          'title'   => (string)($v['title'] ?? ''),
          'message' => (string)($v['message'] ?? ''),
          'icon'    => (string)($v['icon'] ?? ''), // ej. 'mt-icon_checkmark-solid'
        ];
      }
    }
    return [];
  }
}

/**
 * Carga de notificaciones desde helper (orden ya viene desc por fecha)
 */
if ($user_email && function_exists('mt_notifications_payload_for_email')) {
  $notifications = mt_notifications_payload_for_email($user_email, 1, 10);
}

/**
 * Fallback visual cuando no hay notificaciones
 */
if (empty($notifications)) {
  $notifications = [[
    'id'          => '—',
    'type'        => 'success',
    'message'     => 'You are up to date.',
    'read'        => true,
    'right_label' => '',
    'meta'        => ['apiType' => ''],
  ]];
}
?>

<!-- Panel: oculto por defecto. El JS lo abre/cierra -->
<div class="mt-account-notifications" role="dialog" aria-modal="true" tabindex="-1" hidden>
  <!-- Flecha (solo desktop) -->
  <div class="mt-notifications__arrow d-none d-md-block" aria-hidden="true"></div>

  <!-- Header mobile -->
  <div class="mt-notifications__header d-flex align-items-center gap-2 d-md-none mb-3">
    <span class="m-0 text-2xl text-uppercase text-131210 fw-light">Notifications</span>
    <button type="button" class="btn btn-link ms-auto p-0" data-mt-notif-close aria-label="Close">
      <i class="mt-icon mt-icon_close_solid mt-icon-dark"></i>
    </button>
  </div>

  <!-- Lista -->
  <div class="mt-notifications__body subscription-scroll-area">
    <?php foreach ($notifications as $n):
      // Colores por nivel ya normalizado en el helper (success|error|warning)
      $pal  = mt_notif_palette($n['type'] ?? 'warning');

      // Meta desde mapa por tipo de la API
      $apiType = $n['meta']['apiType'] ?? ($n['type'] ?? '');
      $meta    = mt_notif_meta_from_type($apiType);

      // Si el mapa trae level y quieres forzar la paleta, descomenta:
      // if (!empty($meta['level'])) $pal = mt_notif_palette($meta['level']);

      $title   = (string)($meta['title'] ?? '');
      $msg     = (string)($meta['message'] ?? '');
      $icon    = (string)($meta['icon'] ?? '');

      // Mensaje: prioridad al del mapa; si no, el que vino en la notificación
      if ($msg === '') $msg = (string)($n['message'] ?? '');
    ?>
      <article
        class="mt-notification-account card border-0 p-12 <?php echo !empty($n['read']) ? 'bg-gray-100' : ''; ?>"
        data-mt-notif-item
        data-type="<?php echo esc_attr($n['type'] ?? 'warning'); ?>">

        <div class="d-inline-flex align-items-start gap-3 w-100">
          <!-- Bubble -->
          <div class="position-relative rounded-circle flex-shrink-0 <?php echo esc_attr($pal['bg']); ?>"
               style="width:32px;height:32px;">
            <?php if ($icon !== ''): ?>
              <i class="mt-icon <?php echo esc_attr($icon . ' ' . $pal['right']); ?>"
                 style="position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);font-size:16px;"></i>
            <?php else: ?>
              <span class="position-absolute start-50 top-50 translate-middle rounded-circle d-block <?php echo esc_attr($pal['dot']); ?>"
                    style="width:20px;height:20px;"></span>
            <?php endif; ?>
          </div>

          <!-- Texto -->
          <div class="flex-grow-1">
            <div class="d-flex align-items-start gap-2">
              <span class="mt-chip mt-chip--light text-uppercase fw-bold px-1 py-1 rounded-1 bg-gray-200 small">
                <?php echo esc_html($n['id'] ?? ''); ?>
              </span>

              <?php if (!empty($n['right_label'])): ?>
                <span class="ms-auto fw-bold small <?php echo esc_attr($pal['right']); ?>">
                  <?php echo esc_html($n['right_label']); ?>
                </span>
              <?php endif; ?>
            </div>

            <?php if ($title !== ''): ?>
              <div class="fw-bold small mt-1 <?php echo esc_attr($pal['right']); ?>">
                <?php echo esc_html($title); ?>
              </div>
            <?php endif; ?>

            <?php if ($msg !== ''): ?>
              <div class="small text-body mt-1"><?php echo esc_html($msg); ?></div>
            <?php endif; ?>

            <button class="btn btn-light btn-sm mt-2 d-inline-flex align-items-center gap-2 text-uppercase"
                    data-mt-mark-read
                    data-id="<?php echo esc_attr($n['id'] ?? ''); ?>">
              <i class="mt-icon mt-icon_check"></i>
              <span>Mark read</span>
            </button>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</div>
