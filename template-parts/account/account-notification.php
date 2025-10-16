<?php
defined('ABSPATH') || exit;

$user_email = $args['user_email'] ?? '';
$notifications = [];


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


if (!function_exists('mt_notif_meta_from_type')) {
  function mt_notif_meta_from_type(?string $apiType): array
  {
    $apiType = is_string($apiType) ? trim($apiType) : '';
    if ($apiType === '' || !class_exists('Label'))
      return [];

    $map = [];
    if (defined('Label::NOTIFICATION_MAP')) {
      $map = Label::NOTIFICATION_MAP;
    } elseif (defined('Label::NOTIFICATIONS')) {
      $map = Label::NOTIFICATIONS;
    } elseif (defined('Label::NOTIFICATION_TYPE_MAP')) {
      $map = Label::NOTIFICATION_TYPE_MAP;
    }
    if (!is_array($map) || empty($map))
      return [];

    $key = strtolower($apiType);
    foreach ($map as $k => $v) {
      if (strtolower((string) $k) === $key && is_array($v)) {
        $severity = isset($v['severity']) ? (string) $v['severity'] : (string) ($v['level'] ?? 'warning');
        return [
          'severity' => strtolower($severity ?: 'warning'),
          'title' => (string) ($v['title'] ?? ''),
          'message' => (string) ($v['message'] ?? ''),
          'icon' => (string) ($v['icon'] ?? ''), 
        ];
      }
    }
    return [];
  }
}


if ($user_email && function_exists('mt_notifications_payload_for_email')) {
  $notifications = mt_notifications_payload_for_email($user_email, 1, 100);
}


?>
<div class="mt-account-notifications" role="dialog" aria-modal="true" tabindex="-1" hidden>
  <div class="mt-notifications__arrow d-none d-md-block" aria-hidden="true"></div>

  <div class="mt-notifications__header d-flex align-items-center gap-2 d-md-none mb-3">
    <span class="m-0 text-2xl text-uppercase text-131210 fw-light">Notifications</span>
    <button type="button" class="btn btn-link ms-auto p-0" data-mt-notif-close aria-label="Close">
      <i class="mt-icon mt-icon_close_solid mt-icon-dark"></i>
    </button>
  </div>

  <div class="mt-notifications__body subscription-scroll-area">
    <?php foreach ($notifications as $n):
      $apiType = (string) ($n['meta']['apiType'] ?? '');
      $meta = mt_notif_meta_from_type($apiType);
      $severity = (string) ($meta['severity'] ?? 'warning');
      $pal = mt_notif_palette($severity);

      $title = (string) ($meta['title'] ?? '');
      $icon = (string) ($meta['icon'] ?? '');
      $msg = (string) ($meta['message'] ?? '');
      if ($msg === '')
        $msg = (string) ($n['message'] ?? '');

      $accIdRaw = (string) ($n['meta']['accountId_raw'] ?? '');
      $typeKey = (string) ($n['meta']['apiType'] ?? '');
      $createdAt = (string) ($n['createdAt'] ?? ($n['meta']['createdAt'] ?? ''));
      $notifUid = (string) ($n['meta']['eventId'] ?? ($n['uuid'] ?? ''));
      if ($notifUid === '') {
        $notifUid = substr(md5($typeKey . '|' . $createdAt . '|' . $accIdRaw), 0, 16);
      }

      $displayId = (string) ($n['id'] ?? '');
      $hideCtrlId = 'mt-notif-hide-' . preg_replace('/[^a-zA-Z0-9_\-]/', '', $displayId);
      ?>
      <article class="mt-notification-account card border-0 p-12 <?php echo !empty($n['read']) ? 'bg-gray-100' : ''; ?>"
        data-mt-notif-item data-type="<?php echo esc_attr(strtolower($apiType)); ?>">

        <div class="d-inline-flex align-items-start gap-3 w-100">
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

          <div class="flex-grow-1 d-flex flex-column gap-1">
            <div class="d-flex align-items-start gap-2">
              <span
                class="mt-chip mt-chip--light text-uppercase fw-bold px-1 py-1 rounded-1 bg-gray-200 small js-mt-goto-account"
                data-mt-account-id="<?php echo esc_attr($n['meta']['accountId_raw'] ?? ''); ?>"
                data-mt-account-disp="<?php echo esc_attr($n['id'] ?? ''); ?>" role="button" tabindex="0"
                aria-label="Switch to account <?php echo esc_attr($n['id'] ?? ''); ?>">
                <?php echo esc_html($n['id'] ?? ''); ?>
              </span>

              <span id="<?php echo esc_attr($hideCtrlId); ?>" class="ms-auto mt-notif-hide-ctrl" data-mt-notif-hide
                data-id="<?php echo esc_attr($notifUid); ?>" role="button" aria-label="Hide this notification"
                tabindex="0">
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
