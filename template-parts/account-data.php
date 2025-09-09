<?php
/**
 * Partial: template-parts/account-data.php
 * UI: Button + Modal (Bootstrap) to pick active account.
 * Data source: [mega_accounts_data email="..." output="json"]
 *
 * EXPECTED USAGE:
 *   get_template_part('template-parts/account-data', null, ['email' => $email, 'page'=>1, 'perPage'=>50]);
 */
defined('ABSPATH') || exit;

/* ===================== Helpers ===================== */
if (!function_exists('mt_safe_get')) {
  function mt_safe_get($arr, $path, $default = '-') {
    $cur = $arr;
    foreach (explode('.', $path) as $seg) {
      if (is_array($cur) && array_key_exists($seg, $cur)) { $cur = $cur[$seg]; }
      else { return $default; }
    }
    return ($cur === '' || $cur === null) ? $default : $cur;
  }
}
if (!function_exists('mt_decode_sc_json')) {
  function mt_decode_sc_json($shortcode) {
    $raw = do_shortcode($shortcode);
    $data = json_decode($raw, true);
    if (!is_array($data)) return ['error' => 'Malformed JSON'];
    if (isset($data['error'])) return $data;
    if (isset($data['items']) && is_array($data['items'])) return $data['items'];
    if (isset($data['data'])  && is_array($data['data']))  return $data['data'];
    return $data;
  }
}
if (!function_exists('mt_is_active_status')) {
  function mt_is_active_status($status) {
    $s = strtolower(trim((string)$status));
    return in_array($s, ['active','approved','open','enabled','running','live','activated'], true);
  }
}
if (!function_exists('mt_badge_class_from_status')) {
  function mt_badge_class_from_status($status) {
    $s = strtolower(trim((string)$status));
    if (in_array($s, ['active','approved','open','enabled','running','live','activated'], true)) return 'badge-mega-active';
    if (in_array($s, ['pending','pending-cancel','paused','submitted'], true)) return 'badge-mega-pending';
    if (in_array($s, ['rejected','closed','disabled'], true)) return 'badge-mega-danger';
    return 'badge-mega-default';
  }
}
if (!function_exists('mt_dot_class_from_status')) {
  function mt_dot_class_from_status($status) {
    $s = strtolower(trim((string)$status));
    if (in_array($s, ['active','approved','open','enabled','running','live','activated'], true)) return 'dot-status-active';
    if (in_array($s, ['pending','pending-cancel','paused','submitted','on-hold','refunded'], true)) return 'dot-status-pending';
    if (in_array($s, ['rejected','closed','disabled','cancelled','failed','expired'], true)) return 'dot-status-danger';
    return 'dot-status-default';
  }
}
/** Keep only label before first "|" and split "50k ..." -> size/name */
if (!function_exists('mt_parse_program_label')) {
  function mt_parse_program_label($label, $startingBalance = null) {
    $label = (string)$label;
    $head = $label; $pos = strpos($label, '|'); if ($pos !== false) $head = substr($label, 0, $pos);
    $head = trim($head);
    $size_slug = ''; $product_name = $head;
    if (preg_match('/^\s*([0-9]+k)\b/i', $head, $m)) {
      $size_slug = strtoupper($m[1]);
      $product_name = trim(substr($head, strlen($m[1])));
    } elseif (is_numeric($startingBalance)) {
      $k = round(((int)$startingBalance)/1000); if ($k > 0) $size_slug = strtoupper($k.'k');
    }
    if ($product_name === '') $product_name = 'Account';
    return [$size_slug, $product_name];
  }
}
/** Platform: prefer program.platform; fallback = 2nd label segment */
if (!function_exists('mt_extract_platform_from_account')) {
  function mt_extract_platform_from_account(array $acc) {
    $p = trim((string) mt_safe_get($acc, 'program.platform', ''));
    if ($p !== '') return $p;
    $label = (string) mt_safe_get($acc, 'program.label', mt_safe_get($acc, 'program.description', ''));
    $parts = array_map('trim', explode('|', $label));
    return $parts[1] ?? '';
  }
}
if (!function_exists('mt_norm')) {
  function mt_norm($s) { return preg_replace('/[^a-z0-9]+/','', strtolower((string)$s)); }
}

/* ===================== Inputs (email viene del parent) ===================== */
$mt_args = (isset($args) && is_array($args)) ? $args : [];
$email   = isset($mt_args['email'])   ? sanitize_email($mt_args['email'])   : '';
$page    = isset($mt_args['page'])    ? max(1, (int)$mt_args['page'])       : 1;
$perPage = isset($mt_args['perPage']) ? max(1, (int)$mt_args['perPage'])    : 50;

/* ===================== Early exit if no email ===================== */
if (!$email) {
  echo '<p><em>Account data: no email provided by parent template.</em></p>';
  return;
}

/* ===================== Fetch accounts ===================== */
$accounts = [];
$err = '';

$sc   = sprintf('[mega_accounts_data email="%s" page="%d" perpage="%d" output="json"]',
                esc_attr($email), $page, $perPage);
$accs = mt_decode_sc_json($sc);
if (isset($accs['error'])) {
  $err = $accs['error'];
} else {
  $accounts = is_array($accs) ? $accs : [];
}

/* ===================== Split & pick current ===================== */
$active=[]; $inactive=[];
foreach ($accounts as $a) {
  $st = mt_safe_get($a, 'status', '');
  if (mt_is_active_status($st)) $active[]=$a; else $inactive[]=$a;
}
usort($active, function($a,$b){
  $ta = strtotime((string)mt_safe_get($a,'createdAt','')) ?: 0;
  $tb = strtotime((string)mt_safe_get($b,'createdAt','')) ?: 0;
  return $tb <=> $ta;
});
$current = $active[0] ?? ($accounts[0] ?? null);

/* ===================== Button data ===================== */
$badge_class = 'badge-mega-default';
$status = 'unknown';
$size_slug = '';
$product_name = 'Account';

if ($current) {
  $status = (string)mt_safe_get($current, 'status', 'unknown');
  $badge_class = mt_badge_class_from_status($status);
  $program_label = (string)mt_safe_get($current, 'program.label', mt_safe_get($current, 'program.description', ''));
  $startingBalance = mt_safe_get($current, 'program.startingBalance', null);
  [$size_slug, $product_name] = mt_parse_program_label($program_label, $startingBalance);
}

/* ===================== Logos (always show one) ===================== */
$DEFAULT_LOGO = 'https://subscriptions.megatrader.io/wp-content/uploads/2025/07/Stylecolor-Sizelg.svg'; // MegaTrader
$PLATFORM_LOGOS = [
  mt_norm('MegaTrader')  => $DEFAULT_LOGO,
  mt_norm('NinjaTrader') => 'https://subscriptions.megatrader.io/wp-content/uploads/2025/02/icon_ninjatrader.svg',
  mt_norm('Tradovate')   => 'https://subscriptions.megatrader.io/wp-content/uploads/2025/02/icon_tradovate.svg',
  mt_norm('Quantower')   => 'https://subscriptions.megatrader.io/wp-content/uploads/2025/02/icon_quantower.svg',
];

/* ===================== Dataset for JS (actives only) ===================== */
$jsAccounts = [];
foreach ($active as $a) {
  $program_label = (string)mt_safe_get($a, 'program.label', mt_safe_get($a, 'program.description', 'Account'));
  $platform_val  = mt_extract_platform_from_account($a);
  $sb            = mt_safe_get($a,'program.startingBalance', null);
  [$size, $name] = mt_parse_program_label($program_label, $sb);

  $logo = $DEFAULT_LOGO;
  if ($platform_val !== '') {
    $norm = mt_norm($platform_val);
    if (isset($PLATFORM_LOGOS[$norm])) $logo = $PLATFORM_LOGOS[$norm];
  }

  $jsAccounts[] = [
    'id'        => (string)mt_safe_get($a,'id',''),
    'status'    => (string)mt_safe_get($a,'status',''),
    'badgeClass'=> mt_badge_class_from_status(mt_safe_get($a,'status','')),
    'dotClass'  => mt_dot_class_from_status(mt_safe_get($a,'status','')),
    'size'      => $size,
    'name'      => $name,
    'platform'  => $platform_val,
    'logo'      => $logo,
    'createdAt' => (string)mt_safe_get($a,'createdAt',''),
  ];
}
$currentId = $current ? (string)mt_safe_get($current,'id','') : '';

/* ===================== Error / empty handling ===================== */
if ($err)    { echo '<p><strong>Error:</strong> '.esc_html($err).'</p>'; return; }
if (!$current){ echo '<p><em>No accounts found.</em></p>'; return; }
?>

<!-- =============== BUTTON =============== -->
<button type="button" class="w-100 p-0 border-0 bg-131210 text-start btn-reset" data-bs-toggle="modal"
  data-bs-target="#changeSubcriptionModal">
  <div class="border-gray d-flex flex-wrap align-items-center gap-2 mb-3 p-3 rounded-2xl">
    <div class="d-flex gap-3 flex-grow-1 flex-shirk-0 align-items-center">
      <div class="badge-mega badge-mega-sm <?php echo esc_attr($badge_class); ?>" id="mt-badge">
        <?php
          $st = strtolower($status);
          echo ($st === 'pending-cancel')
            ? esc_html__('Pending Cancellation', 'woocommerce')
            : esc_html(ucwords(str_replace('-', ' ', $st)));
        ?>
      </div>
      <div class="d-flex gap-2 align-items-center">
        <div class="mt-icon mt-icon-md mt-icon-primary mt-icon_diamond"></div>
        <div class="fw-medium plan-name text-size-24 text-uppercase text-white">
          <span id="mt-size"><?php echo esc_html($size_slug); ?></span>
          <span id="mt-name"><?php echo esc_html($product_name); ?></span>
        </div>
      </div>
    </div>
    <span class="svg-button mt-icon mt-icon_caret-down mt-icon-white"></span>
  </div>
</button>

<!-- =============== MODAL =============== -->
<div class="modal modal-subcription fade" id="changeSubcriptionModal" tabindex="-1"
  aria-labelledby="changeSubcriptionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-fullscreen-md-down">
    <div class="modal-content gap-4">
      <div class="modal-header w-100 border-0 justify-content-between align-items-start p-0">
        <h5 class="modal-title text-white heading-sm-medium" id="changeSubcriptionModalLabel">Select account</h5>
        <button type="button" class="p-0 border-0 bg-transparent shadow-none" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">
            <img src="https://subscriptions.megatrader.io/wp-content/uploads/2025/05/cancel-circle-1.png"
              alt="Close" style="width: 24px; height: 24px;" />
          </span>
        </button>
      </div>

      <div class="modal-body">
        <div class="subscription-scroll-area px-lg-3 p-0">
          <div class="subscription-grid" id="mt-accounts-grid">
            <?php if (empty($jsAccounts)): ?>
              <p class="text-a8a29e">No active accounts.</p>
            <?php else: ?>
              <?php foreach ($jsAccounts as $a): ?>
                <?php
                  $is_current = ($a['id'] === $currentId);
                  $card_classes = 'subscription-card position-relative d-flex flex-column gap-2';
                  if ($is_current) $card_classes .= ' active'; // clase para seleccionado
                ?>
                <div class="<?php echo esc_attr($card_classes); ?>"
                     role="button"
                     data-account-id="<?php echo esc_attr($a['id']); ?>">
                  <div class="checkmark-icon position-absolute" style="top: 10px; right: 10px; <?php echo $is_current ? '' : 'display:none;'; ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                      <circle cx="12" cy="12" r="10" fill="#FFB34A"/>
                      <path d="M10.6 16.6L17.65 9.55L16.25 8.15L10.6 13.8L7.75 10.95L6.35 12.35L10.6 16.6Z" fill="black"/>
                    </svg>
                  </div>

                  <div class="subscription-card__header text-center position-relative d-flex flex-column align-items-center">
                    <div class="logo-container position-relative d-inline-block">
                      <img src="<?php echo esc_url($a['logo']); ?>" alt="<?php echo esc_attr($a['platform'] ?: 'platform'); ?> logo" style="max-height:40px;">
                      <div class="dot-indicator <?php echo esc_attr($a['dotClass']); ?>" title="<?php echo esc_attr($a['status']); ?>" style="position:absolute; right:-2px; bottom:-2px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                          <circle cx="6" cy="6" r="6" fill="white"/>
                          <circle cx="6" cy="6" r="4" fill="currentColor"/>
                        </svg>
                      </div>
                    </div>
                  </div>

                  <div class="subscription-card__body text-center">
                    <div class="subscription-card__name fw-medium text-16px text-white">
                      <?php echo esc_html($a['size'].' '.$a['name']); ?>
                    </div>
                    <div class="subscription-card__id text-14px text-a8a29e text-uppercase">
                      #<?php echo esc_html($a['id']); ?>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <div class="align-items-center d-flex gap-2 justify-content-end">
          <div class="mega-btn mega-btn-md rounded-12 text-white" data-bs-dismiss="modal">Cancel</div>
          <button id="select-subscription-btn" class="mega-btn mega-btn-md mega-btn-secondary-md disabled" disabled>
            Select
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
/* ===================== Enqueue JS + payload ===================== */
$handle  = 'mt-account-picker';
$js_path = get_stylesheet_directory() . '/assets/js/mt-account-picker.js';
$js_url  = get_stylesheet_directory_uri() . '/assets/js/mt-account-picker.js';
wp_enqueue_script($handle, $js_url, [], (file_exists($js_path) ? filemtime($js_path) : null), true);

$payload = [
  'currentId'      => $currentId,
  'accounts'       => $jsAccounts,
  'selectionClass' => 'active',
  'selectors' => [
    'grid'   => '#mt-accounts-grid',
    'select' => '#select-subscription-btn',
    'badge'  => '#mt-badge',
    'size'   => '#mt-size',
    'name'   => '#mt-name',
    'modal'  => '#changeSubcriptionModal',
    'card'   => '.subscription-card',
    'check'  => '.checkmark-icon',
  ],
  'debug' => false,
];
wp_add_inline_script($handle, 'window.MT_DATA = ' . wp_json_encode($payload) . ';', 'before');
?>
