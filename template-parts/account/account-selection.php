<?php
/**
 * Partial: template-parts/account-data.php
 * - Recibe $args['prepared'] con:
 *   [
 *     'current'  => ['id','status','badgeClass','size','name'],
 *     'accounts' => [
 *        ['id','status','dotClass','size','name','platform','logo','createdAt'], ...
 *     ]
 *   ]
 * - NO consulta la API. Solo pinta UI y encola JS.
 */
defined('ABSPATH') || exit;

/* ===== Inputs ===== */
$prepared = isset($args['prepared']) && is_array($args['prepared']) ? $args['prepared'] : null;
$current  = $prepared['current']  ?? null;
$accounts = $prepared['accounts'] ?? [];

/* ===== Guardas ===== */
if (!$prepared || !$current || empty($accounts)) {
  echo '<p class="text-a8a29e"><em>No active accounts available.</em></p>';
  return;
}

/* ===== Vars de botón ===== */
$currentId   = (string)($current['id'] ?? '');
$badgeClass  = (string)($current['badgeClass'] ?? 'badge-mega-default');
$currentStat = (string)($current['status'] ?? 'unknown');
$sizeSlug    = (string)($current['size'] ?? '');
$productName = (string)($current['name'] ?? 'Account');

?>
<!-- =============== BUTTON (usa datos de $current) =============== -->
<button type="button" class="w-100 p-0 border-0 bg-131210 text-start btn-reset" data-bs-toggle="modal"
  data-bs-target="#changeSubcriptionModal">
  <div class="border-gray d-flex flex-wrap align-items-center gap-2 mb-3 p-3 rounded-2xl">
    <div class="d-flex gap-3 flex-grow-1 flex-shirk-0 align-items-center">
      <div class="badge-mega badge-mega-sm <?php echo esc_attr($badgeClass); ?>" id="mt-badge">
        <?php
          $st = strtolower($currentStat);
          echo ($st === 'pending-cancel')
            ? esc_html__('Pending Cancellation', 'woocommerce')
            : esc_html(ucwords(str_replace('-', ' ', $st)));
        ?>
      </div>
      <div class="d-flex gap-2 align-items-center">
        <div class="mt-icon mt-icon-md mt-icon-primary mt-icon_diamond"></div>
        <div class="fw-medium plan-name text-size-24 text-uppercase text-white">
          <span id="mt-size"><?php echo esc_html($sizeSlug); ?></span>
          <span id="mt-name"><?php echo esc_html($productName); ?></span>
        </div>
      </div>
    </div>
    <span class="svg-button mt-icon mt-icon_caret-down mt-icon-white"></span>
  </div>
</button>

<!-- =============== MODAL (usa $accounts tal cual) =============== -->
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
            <?php foreach ($accounts as $a): ?>
              <?php
                $aid   = (string)($a['id'] ?? '');
                $isCur = ($aid === $currentId);
                $card_classes = 'subscription-card position-relative d-flex flex-column gap-2';
                if ($isCur) $card_classes .= ' active';
              ?>
              <div class="<?php echo esc_attr($card_classes); ?>"
                   role="button"
                   data-account-id="<?php echo esc_attr($aid); ?>">
                <div class="checkmark-icon position-absolute" style="top: 10px; right: 10px; <?php echo $isCur ? '' : 'display:none;'; ?>">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" fill="#FFB34A"/>
                    <path d="M10.6 16.6L17.65 9.55L16.25 8.15L10.6 13.8L7.75 10.95L6.35 12.35L10.6 16.6Z" fill="black"/>
                  </svg>
                </div>

                <div class="subscription-card__header text-center position-relative d-flex flex-column align-items-center">
                  <div class="logo-container position-relative d-inline-block">
                    <img src="<?php echo esc_url($a['logo'] ?? ''); ?>"
                         alt="<?php echo esc_attr(($a['platform'] ?? '') ?: 'platform'); ?> logo"
                         style="max-height:40px;">
                    <div class="dot-indicator <?php echo esc_attr($a['dotClass'] ?? 'dot-status-default'); ?>"
                         title="<?php echo esc_attr($a['status'] ?? ''); ?>"
                         style="position:absolute; right:-2px; bottom:-2px;">
                      <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                        <circle cx="6" cy="6" r="6" fill="white"/>
                        <circle cx="6" cy="6" r="4" fill="currentColor"/>
                      </svg>
                    </div>
                  </div>
                </div>

                <div class="subscription-card__body text-center">
                  <div class="subscription-card__name fw-medium text-16px text-white">
                    <?php echo esc_html(($a['size'] ?? '').' '.($a['name'] ?? 'Account')); ?>
                  </div>
                  <div class="subscription-card__id text-14px text-a8a29e text-uppercase">
                    #<?php echo esc_html($aid); ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
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
/* ============== Enqueue JS + payload (usa lo ya preparado) ============== */
$handle  = 'mt-account-picker';
$js_path = get_stylesheet_directory() . '/assets/js/mt-account-picker.js';
$js_url  = get_stylesheet_directory_uri() . '/assets/js/mt-account-picker.js';
wp_enqueue_script($handle, $js_url, [], (file_exists($js_path) ? filemtime($js_path) : null), true);

$payload = [
  'currentId'      => $currentId,
  'accounts'       => $accounts,     // ← ya vienen con logo/dotClass/etc.
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
