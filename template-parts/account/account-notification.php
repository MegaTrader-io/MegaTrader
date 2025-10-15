<?php
defined('ABSPATH') || exit;

$user_email = $args['user_email'] ?? '';
$notifications = [];

if (!function_exists('mt_notif_palette')) {
  function mt_notif_palette(string $type): array {
    $t = strtolower($type);
    switch ($t) {
      case 'success': return ['bg' => 'bg-success-100', 'dot' => 'bg-success-600', 'right' => 'text-success-600'];
      case 'error':   return ['bg' => 'bg-error-100',   'dot' => 'bg-error-600',   'right' => 'text-error-600'];
      default:        return ['bg' => 'bg-warning-100', 'dot' => 'bg-warning-600', 'right' => 'text-warning-600'];
    }
  }
}

if ($user_email && function_exists('mt_notifications_payload_for_email')) {
  $notifications = mt_notifications_payload_for_email($user_email, 1, 10);
}

if (empty($notifications)) {
  $notifications = [[
    'id' => '—',
    'type' => 'success',
    'title' => 'No notifications',
    'message' => 'You are up to date.',
    'read' => true,
    'right_label' => '',
  ]];
}
?>
<!-- Panel: oculto por defecto. El JS lo abre/cierra -->
<div class="mt-account-notifications" role="dialog" aria-modal="true" tabindex="-1" hidden>
  <!-- Flecha (solo desktop) -->
  <div class="mt-notifications__arrow d-none d-md-block" aria-hidden="true"></div>

  <!-- Header mobile -->
  <div class="mt-notifications__header d-flex align-items-center gap-2 d-md-none">
    <h2 class="m-0 text-uppercase fw-light">Notifications</h2>
    <button type="button" class="btn btn-link ms-auto p-0" data-mt-notif-close aria-label="Close">
      <i class="mt-icon mt-icon_close"></i>
    </button>
  </div>

  <!-- Lista -->
  <div class="mt-notifications__body">
    <?php foreach ($notifications as $n): $pal = mt_notif_palette($n['type'] ?? 'warning'); ?>
      <article
        class="mt-notification-account card border-0 p-3 mb-1 rounded-2 <?php echo !empty($n['read']) ? 'bg-gray-100' : ''; ?>"
        data-mt-notif-item
        data-type="<?php echo esc_attr($n['type'] ?? 'warning'); ?>"
      >
        <div class="d-inline-flex align-items-start gap-3 w-100">
          <!-- Bubble -->
          <div
            class="position-relative rounded-circle flex-shrink-0 mt-notification__bubble <?php echo esc_attr($pal['bg']); ?>"
            style="width:32px;height:32px;"
          >
            <span
              class="position-absolute start-50 top-50 translate-middle rounded-circle d-block <?php echo esc_attr($pal['dot']); ?>"
              style="width:20px;height:20px;"
            ></span>
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

            <div class="fw-bold small mt-1 <?php echo esc_attr($pal['right']); ?>">
              <?php echo esc_html($n['title'] ?? ''); ?>
            </div>
            <div class="small text-body mt-1"><?php echo esc_html($n['message'] ?? ''); ?></div>

            <button
              class="btn btn-light btn-sm mt-2 d-inline-flex align-items-center gap-2 text-uppercase"
              data-mt-mark-read
              data-id="<?php echo esc_attr($n['id'] ?? ''); ?>"
            >
              <i class="mt-icon mt-icon_check"></i>
              <span>Mark read</span>
            </button>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</div>


