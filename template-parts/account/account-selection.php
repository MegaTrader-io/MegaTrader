<?php
defined('ABSPATH') || exit;

$prepared = isset($args['prepared']) && is_array($args['prepared']) ? $args['prepared'] : null;
$current  = $prepared['current'] ?? null;
$accounts = $prepared['accounts'] ?? [];

if (!$prepared || empty($accounts)) {
  echo '<p class="text-a8a29e"><em>No accounts found for this user.</em></p>';
  return;
}
if (!$current && !empty($accounts)) {
  $current = $accounts[0];
}

/* OVERRIDE desde Overview (cookie/selectedId) */
$selectedIdArg = isset($args['selectedId']) ? (string) $args['selectedId'] : '';
if ($selectedIdArg !== '') {
  foreach ($accounts as $row) {
    if ((string) ($row['id'] ?? '') === $selectedIdArg) {
      $current = $row;
      break;
    }
  }
}

$checkout = function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : '/checkout';

/* Datos del current */
$currentId              = (string) ($current['id'] ?? '');
$currentStat            = (string) ($current['status'] ?? 'unknown');
$sizeSlug               = (string) ($current['size'] ?? '');
$productName            = (string) ($current['name'] ?? 'Account');
$curProgText            = (string) ($current['programTypeText'] ?? '');
$curProgClass           = (string) ($current['programTypeClass'] ?? '');
$resetId                = (string) ($current['resetProductId'] ?? '');
$activationId           = (string) ($current['activationProductId'] ?? '');
$mainId                 = (string) ($current['mainProductId'] ?? '');
$ord                    = (string) ($current['order'] ?? '');
$subscriptionIdCurrent  = (string) ($current['subscriptionId'] ?? '');
$account_id             = $selectedIdArg !== '' ? $selectedIdArg : $currentId;

/* Logo (ya viene resuelto en MT_Accounts::prepare_ui) */
$theme_uri      = get_stylesheet_directory_uri();
$fallback_logo  = $theme_uri . '/assets/svg/icon_megatrader.svg';
$currentLogo    = (string) ($current['logo'] ?? '');
if ($currentLogo === '') $currentLogo = $fallback_logo;

/* Badge de estado */
$status_key  = preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($currentStat)));
$badgeBase   = class_exists('MT_Accounts') ? MT_Accounts::badge_class($currentStat) : 'badge-mega-default';
$badgeClass  = trim($badgeBase . ' badge-mega-' . ($status_key ?: 'default'));
?>

<div class="mt-picker-wrap position-relative">
  <div class="mega-btn-md mega-btn-dark-md w-100" role="button" tabindex="0"
    data-bs-toggle="modal"
    data-bs-target="#changeSubcriptionModal"
    data-account-id="<?php echo esc_attr($account_id); ?>"
    data-main-id="<?php echo esc_attr($mainId); ?>"
    data-reset-id="<?php echo esc_attr($resetId); ?>"
    data-order-id="<?php echo esc_attr($ord); ?>"
    data-activation-id="<?php echo esc_attr($activationId); ?>"
    data-account-type="<?php echo esc_attr($curProgText); ?>"
    data-subscription-id="<?php echo esc_attr($subscriptionIdCurrent); ?>">

    <div class="d-flex align-items-center gap-2 w-100">
      <span class="svg-button mt-icon mt-icon_caret-down mt-icon-white"></span>

      <div class="align-items-center d-flex flex-wrap column-gap-2 column-gap-sm-2 row-gap-2">
        <div id="mt-account-id-badge" class="mt-badge mt-badge-light mt-badge-rounded-sm">
          <?php echo esc_html(($current['accountId'] ?? $currentId)); ?>
        </div>

        <div id="mt-selection-icon_diamond" class="mt-icon mt-icon-primary mt-icon_diamond mt-icon-md"></div>

        <div class="d-flex gap-2 align-items-center flex-fill">
          <img
            id="mt-platform-logo"
            src="<?php echo esc_url($currentLogo); ?>"
            alt="Platform logo"
            width="30"
            height="30"
            loading="lazy"
            decoding="async"
            fetchpriority="low"
            style="width:30px;height:30px;object-fit:contain;border-radius:6px;"
            onerror="this.onerror=null;this.src='<?php echo esc_js($fallback_logo); ?>';"
          />

          <div class="fw-medium plan-name text-uppercase text-white text-2xl leading-7">
            <span id="mt-size"><?php echo esc_html($sizeSlug); ?></span>
            <span id="mt-name"><?php echo esc_html($productName); ?></span>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<div class="modal modal-subcription fade" id="changeSubcriptionModal" tabindex="-1"
  aria-labelledby="changeSubcriptionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="--bs-modal-width: 450px;">
    <div class="modal-content gap-4">

      <div class="modal-header w-100 border-0 justify-content-between align-items-start p-0">
        <span class="modal-title text-white heading-sm-medium" id="changeSubcriptionModalLabel">Select account</span>
        <button type="button" class="p-0 border-0 bg-transparent shadow-none" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">
            <img src="/wp-content/uploads/2025/05/cancel-circle-1.png" alt="Close" style="width:24px;height:24px;" />
          </span>
        </button>
      </div>

      <div class="modal-body">
        <div class="mb-4">
          <div class="dropdown w-100">
            <button id="mt-acc-filter-btn"
              class="btn btn-dark w-100 d-flex justify-content-between align-items-center rounded-12"
              type="button"
              data-bs-toggle="dropdown"
              aria-expanded="false">
              <span id="mt-acc-filter-label">Active</span>
              <span class="mt-icon mt-icon_caret-down mt-icon-white"></span>
            </button>

            <?php
            $present = ['ACTIVE' => false, 'BREACHED' => false, 'PASSED' => false];
            foreach ($accounts as $row) {
              $st = strtoupper(trim((string) ($row['status'] ?? '')));
              if ($st === 'ACTIVE' || $st === 'PENDING_ACTIVATION') $present['ACTIVE'] = true;
              if ($st === 'BREACHED' || $st === 'RESET') $present['BREACHED'] = true;
              if ($st === 'PASSED' || $st === 'UPGRADED') $present['PASSED'] = true;
            }

            $curStatus = strtoupper(trim((string) ($current['status'] ?? '')));
            switch ($curStatus) {
              case 'ACTIVE':
              case 'PENDING_ACTIVATION':
                $curFilter = 'ACTIVE';
                break;
              case 'BREACHED':
              case 'RESET':
                $curFilter = 'BREACHED';
                break;
              case 'PASSED':
              case 'UPGRADED':
                $curFilter = 'PASSED';
                break;
              default:
                $curFilter = $present['ACTIVE'] ? 'ACTIVE' : ($present['BREACHED'] ? 'BREACHED' : ($present['PASSED'] ? 'PASSED' : 'ACTIVE'));
            }

            $firstOpt = !empty($present[$curFilter]) ? $curFilter : ($present['ACTIVE'] ? 'ACTIVE' : ($present['BREACHED'] ? 'BREACHED' : ($present['PASSED'] ? 'PASSED' : 'ACTIVE')));
            ?>

            <ul class="dropdown-menu w-100 p-0 overflow-hidden rounded-12 mt-1" id="mt-acc-filter-menu">
              <?php if ($present['ACTIVE']): ?>
                <li><button type="button" class="dropdown-item py-2 mt-filter-option" data-value="ACTIVE">Active</button></li>
              <?php endif; ?>
              <?php if ($present['BREACHED']): ?>
                <li><button type="button" class="dropdown-item py-2 mt-filter-option" data-value="BREACHED">Breached</button></li>
              <?php endif; ?>
              <?php if ($present['PASSED']): ?>
                <li><button type="button" class="dropdown-item py-2 mt-filter-option" data-value="PASSED">Passed</button></li>
              <?php endif; ?>
            </ul>

            <select id="mt-acc-filter" class="d-none" aria-hidden="true">
              <?php if ($present['ACTIVE']): ?>
                <option value="ACTIVE" <?php selected($firstOpt, 'ACTIVE'); ?>>Active</option>
              <?php endif; ?>
              <?php if ($present['BREACHED']): ?>
                <option value="BREACHED" <?php selected($firstOpt, 'BREACHED'); ?>>Breached</option>
              <?php endif; ?>
              <?php if ($present['PASSED']): ?>
                <option value="PASSED" <?php selected($firstOpt, 'PASSED'); ?>>Passed</option>
              <?php endif; ?>
            </select>

          </div>
        </div>

        <div class="subscription-scroll-area px-lg-3 p-0">
          <div class="subscription-grid" id="mt-accounts-grid" data-grid-empty="1"></div>
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
$handle  = 'mt-account-picker';
$js_path = get_stylesheet_directory() . '/assets/js/mt-account-picker.js';
$js_url  = get_stylesheet_directory_uri() . '/assets/js/mt-account-picker.js';
wp_enqueue_script($handle, $js_url, [], (file_exists($js_path) ? filemtime($js_path) : null), true);

$payload = [
  'currentId' => $currentId,
  'accounts'  => $accounts, // <- ya vienen con 'logo' correcto desde prepare_ui
  'selectionClass' => 'active',
  'selectors' => [
    'grid' => '#mt-accounts-grid',
    'select' => '#select-subscription-btn',
    'badge' => '#mt-badge',
    'platformLogo' => '#mt-platform-logo',
    'size' => '#mt-size',
    'name' => '#mt-name',
    'modal' => '#changeSubcriptionModal',
    'card' => '.subscription-card',
    'check' => '.checkmark-icon',
    'performance' => '.mt-account-performance',
  ],
  'ajax' => [
    'url' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('mt-acc-nonce'),
    'action' => 'mt_accounts_performance',
  ],
  'checkoutBase' => $checkout,
  'debug' => false,
  'autoloadAccountOverview' => true
];

wp_add_inline_script($handle, 'window.MT_DATA = ' . wp_json_encode($payload) . ';', 'before');

wp_add_inline_script(
  $handle,
  <<<'JS'
(function(){
  var filterSel = document.getElementById('mt-acc-filter');
  var filterLbl = document.getElementById('mt-acc-filter-label');
  if (!filterSel || !filterLbl) return;

  var val = (filterSel.value || (filterSel.querySelector('option') && filterSel.querySelector('option').value) || 'ACTIVE');
  val = String(val || 'ACTIVE').trim().toUpperCase();
  if (val !== 'ACTIVE' && val !== 'BREACHED' && val !== 'PASSED') val = 'ACTIVE';

  var map = {ACTIVE:'Active', BREACHED:'Breached', PASSED:'Passed'};
  filterLbl.textContent = map[val] || 'Active';
})();
JS
);
