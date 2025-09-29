<?php
defined('ABSPATH') || exit;

$prepared = isset($args['prepared']) && is_array($args['prepared']) ? $args['prepared'] : null;
$current = $prepared['current'] ?? null;
$accounts = $prepared['accounts'] ?? [];
$resetId = (string) ($current['resetProductId'] ?? '');
$hasReset = $resetId !== '';
$checkout = function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : '/checkout';
$reset_url = $hasReset ? ($checkout . '?add-to-cart=' . urlencode($resetId)) : '';




if (!$prepared || empty($accounts)) {
  echo '<p class="text-a8a29e"><em>No accounts found for this user.</em></p>';
  return;
}

if (!$current && !empty($accounts)) {
  $current = $accounts[0];
}


$currentId = (string) ($current['id'] ?? '');
$currentStat = (string) ($current['status'] ?? 'unknown');
$sizeSlug = (string) ($current['size'] ?? '');
$productName = (string) ($current['name'] ?? 'Account');
$status_key = strtolower(trim($currentStat));
$status_key = preg_replace('/[^a-z0-9]+/', '-', $status_key);
$badgeBase = class_exists('MT_Accounts') ? MT_Accounts::badge_class($currentStat) : 'badge-mega-default';
$badgeClass = trim($badgeBase . ' badge-mega-' . ($status_key ?: 'default'));




?>

<div
  class="account-selection-wrapper d-flex gap-2 align-items-center gap-3 p-3 rounded-2xl bg-1e1e1e justify-content-between">
  <button type="button" class="mega-btn-md mega-btn-secondary-md flex-shrink-0 flex-grow-1" data-bs-toggle="modal"
    data-bs-target="#changeSubcriptionModal">
    <div class="d-flex align-items-center gap-2 justify-content-between w-100">
      <div class="align-items-center d-flex flex-wrap column-gap-2 column-gap-sm-3 row-gap-2">
        <div class="badge-mega badge-mega-sm <?php echo esc_attr($badgeClass); ?>" id="mt-badge">
          <?php
          $st = strtolower($currentStat);
          echo ($st === 'pending-cancel')
            ? esc_html__('Pending Cancellation', 'woocommerce')
            : esc_html(ucwords(str_replace('-', ' ', $st)));
          ?>
        </div>
        <div class="d-flex gap-2 align-items-center flex-fill">
          <div class="mt-icon mt-icon-primary mt-icon_diamond d-none d-md-block d-lg-block"></div>
          <div class="fw-medium plan-name text-uppercase text-white text-truncate mobile-max-width-100">
            <span id="mt-size"><?php echo esc_html($sizeSlug); ?></span>
            <span id="mt-name"><?php echo esc_html($productName); ?></span>
          </div>
        </div>
      </div>
      <span class="svg-button mt-icon mt-icon_caret-down mt-icon-white"></span>
    </div>
  </button>
  <?php $attrs = $reset_url ? '' : 'onclick="alert(\'Reset product not found.\'); return false;" aria-disabled="true"'; ?>

  <a class="custom-reset-btn mega-btn-md mega-btn-primary-md d-none d-sm-block"
    href="<?php echo esc_url($hasReset ? $reset_url : '#'); ?>" <?php echo $hasReset ? '' : 'hidden aria-disabled="true"'; ?>>
    Reset Challenge
  </a>
  <a class="custom-reset-btn mega-btn-md mega-btn-primary-md d-sm-none"
    href="<?php echo esc_url($hasReset ? $reset_url : '#'); ?>" aria-label="Reset Challenge" <?php echo $hasReset ? '' : 'hidden aria-disabled="true"'; ?>>
    ...
  </a>



</div>


<div class="modal modal-subcription fade" id="changeSubcriptionModal" tabindex="-1"
  aria-labelledby="changeSubcriptionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-fullscreen-md-down">
    <div class="modal-content gap-4">
      <div class="modal-header w-100 border-0 justify-content-between align-items-start p-0">
        <h5 class="modal-title text-white heading-sm-medium" id="changeSubcriptionModalLabel">Select account</h5>
        <button type="button" class="p-0 border-0 bg-transparent shadow-none" data-bs-dismiss="modal"
          aria-label="Close">
          <span aria-hidden="true">
            <img src="/wp-content/uploads/2025/05/cancel-circle-1.png" alt="Close" style="width: 24px; height: 24px;" />
          </span>
        </button>
      </div>

      <div class="modal-body">
        <div class="subscription-scroll-area px-lg-3 p-0">
          <div class="subscription-grid" id="mt-accounts-grid">
            <?php foreach ($accounts as $a): ?>
              <?php
              $aid = (string) ($a['id'] ?? '');
              $isCur = ($aid === $currentId);
              $card_classes = 'subscription-card position-relative d-flex flex-column gap-2';
              if ($isCur)
                $card_classes .= ' active';
              $status_raw = (string) ($a['status'] ?? '');
              $status_key = strtolower(trim($status_raw));
              $status_key = preg_replace('/[^a-z0-9]+/', '-', $status_key);
              $dot_class = 'dot-status-' . $status_key;


              ?>
              <div class="<?php echo esc_attr($card_classes); ?>" role="button"
                data-account-id="<?php echo esc_attr($aid); ?>" data-status="<?php echo esc_attr($a['status'] ?? ''); ?>"
                data-size="<?php echo esc_attr($a['size'] ?? ''); ?>"
                data-reset-id="<?php echo esc_attr($a['resetProductId'] ?? ''); ?>"
                data-name="<?php echo esc_attr($a['name'] ?? 'Account'); ?>">

                <div class="checkmark-icon position-absolute"
                  style="top: 10px; right: 10px; <?php echo $isCur ? '' : 'display:none;'; ?>">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" fill="#FFB34A" />
                    <path d="M10.6 16.6L17.65 9.55L16.25 8.15L10.6 13.8L7.75 10.95L6.35 12.35L10.6 16.6Z" fill="black" />
                  </svg>
                </div>

                <div
                  class="subscription-card__header text-center position-relative d-flex flex-column align-items-center">
                  <div class="logo-container position-relative d-inline-block">
                    <img src="<?php echo esc_url($a['logo'] ?? ''); ?>"
                      alt="<?php echo esc_attr(($a['platform'] ?? '') ?: 'platform'); ?> logo" style="max-height:40px;">
                    <div class="dot-indicator <?php echo esc_attr($dot_class); ?>"
                      title="<?php echo esc_attr($a['status'] ?? ''); ?>"
                      style="position:absolute; right:-2px; bottom:-2px;">
                      <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                        <circle cx="6" cy="6" r="6" fill="white" />
                        <circle cx="6" cy="6" r="4" fill="currentColor" />
                      </svg>
                    </div>
                  </div>
                </div>

                <div class="subscription-card__body text-center">
                  <div class="subscription-card__name fw-medium text-16px text-white">
                    <?php echo esc_html(($a['size'] ?? '') . ' ' . ($a['name'] ?? 'Account')); ?>
                  </div>
                  <div class="subscription-card__id text-14px text-a8a29e text-uppercase text-truncate">
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
$handle = 'mt-account-picker';
$js_path = get_stylesheet_directory() . '/assets/js/mt-account-picker.js';
$js_url = get_stylesheet_directory_uri() . '/assets/js/mt-account-picker.js';
wp_enqueue_script($handle, $js_url, [], (file_exists($js_path) ? filemtime($js_path) : null), true);

$payload = [
  'currentId' => $currentId,
  'accounts' => $accounts,
  'selectionClass' => 'active',
  'selectors' => [
    'grid' => '#mt-accounts-grid',
    'select' => '#select-subscription-btn',
    'badge' => '#mt-badge',
    'size' => '#mt-size',
    'name' => '#mt-name',
    'modal' => '#changeSubcriptionModal',
    'card' => '.subscription-card',
    'check' => '.checkmark-icon',
    'performance' => '.mt-account-performance',
  ],
  // 👇 datos para la llamada AJAX
  'ajax' => [
    'url' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('mt-acc-nonce'),
    'action' => 'mt_accounts_performance',
  ],
  'checkoutBase' => function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : '/checkout',
  'debug' => false,
];
wp_add_inline_script($handle, 'window.MT_DATA = ' . wp_json_encode($payload) . ';', 'before');


wp_add_inline_script($handle, <<<JS
(function(){
  var CFG = window.MT_DATA || {};
  var grid = document.querySelector(CFG.selectors.grid);
  var btn  = document.querySelector(CFG.selectors.select);
  var perf = document.querySelector(CFG.selectors.performance) || document.querySelector('.mt-account-performance');
  if (!grid || !btn || !perf) return;

  var selectedId = CFG.currentId || null;

  function setBtnEnabled(on){
    btn.disabled = !on;
    btn.classList.toggle('disabled', !on);
  }

  function selectCard(card){
    grid.querySelectorAll(CFG.selectors.card+'.'+CFG.selectionClass).forEach(function(el){
      el.classList.remove(CFG.selectionClass);
      var c = el.querySelector(CFG.selectors.check);
      if (c) c.style.display = 'none';
    });
    card.classList.add(CFG.selectionClass);
    var ch = card.querySelector(CFG.selectors.check);
    if (ch) ch.style.display = '';
    selectedId = card.getAttribute('data-account-id');
    setBtnEnabled(true);
  }
    function updateResetButtons(){
    var active = grid.querySelector(CFG.selectors.card+'.'+CFG.selectionClass);
    var resetId = active ? (active.getAttribute('data-reset-id') || '') : '';
    var base = CFG.checkoutBase || (window.MT_DATA && window.MT_DATA.checkoutBase) || '/checkout';
    var btns = document.querySelectorAll('.custom-reset-btn');

    btns.forEach(function(a){
      if (!a) return;
      if (resetId) {
        a.href = base + '?add-to-cart=' + encodeURIComponent(resetId);
        a.classList.remove('d-none');
        a.removeAttribute('aria-disabled');
      } else {
        a.href = '#';
        a.classList.add('d-none');
        a.setAttribute('aria-disabled', 'true');
      }
    });
  }


  grid.addEventListener('click', function(e){
    var card = e.target.closest(CFG.selectors.card);
    if (!card) return;
    selectCard(card);
  });

  if (selectedId){
    var cur = grid.querySelector(CFG.selectors.card + '[data-account-id="'+CSS.escape(selectedId)+'"]');
    if (cur) selectCard(cur);
  } else {
    setBtnEnabled(false);
  }
    updateResetButtons();

  btn.addEventListener('click', function(){
    if (!selectedId) return;

    var fd = new FormData();
    fd.append('action', CFG.ajax.action);
    fd.append('nonce',  CFG.ajax.nonce);
    fd.append('accountId', selectedId);

    setBtnEnabled(false);

    fetch(CFG.ajax.url, { method:'POST', body: fd, credentials: 'same-origin' })
      .then(function(r){ return r.json(); })
      .then(function(res){
        if (!res || !res.success) throw new Error(res && res.data && res.data.message || 'AJAX failed');

        perf.innerHTML = res.data.html || '';

        var modalEl = document.querySelector(CFG.selectors.modal);
        if (modalEl && window.bootstrap) {
          var inst = window.bootstrap.Modal.getInstance(modalEl) || new window.bootstrap.Modal(modalEl);
          inst.hide();
        }

        var active = grid.querySelector(CFG.selectors.card+'.'+CFG.selectionClass);
        if (active) {
          var sizeVal = active.getAttribute('data-size') || '';
          var nameVal = active.getAttribute('data-name') || 'Account';
          var badgeEl = document.querySelector(CFG.selectors.badge);
          var sizeEl  = document.querySelector(CFG.selectors.size);
          var nameEl  = document.querySelector(CFG.selectors.name);
          if (sizeEl) sizeEl.textContent = sizeVal;
          if (nameEl) nameEl.textContent = nameVal;
          if (badgeEl) {
            var status = (active.getAttribute('data-status') || '').toLowerCase();
            badgeEl.textContent = status ? (status.replace(/-/g,' ').replace(/\\b\\w/g, function(m){ return m.toUpperCase(); })) : 'Active';
          }
        }
          updateResetButtons();
      })
      .catch(function(err){
        console.error('[MT] AJAX error:', err);
        alert('Could not load account performance.');
      })
      .finally(function(){
        setBtnEnabled(true);
      });
  });
})();
JS);


