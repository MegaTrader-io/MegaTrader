<?php
/**
 * Template Part: Account Trades
 * Path: template-parts/account/account-trades.php
 *
 * Requirements:
 * - Design tokens/classes already present (mt-*, colors, etc.)
 * - Tooltip markup available (mt-tooltip)
 * - JS en mt-account-overview.js (render y fetch)
 */

$root_id  = 'mt-trades';
$pager_id = 'mt-trades-pager';

$user_id = get_current_user_id();
$acc_id  = '';
if (isset($args['meta']['accountId']))      $acc_id = (string)$args['meta']['accountId'];
elseif (isset($args['accountId']))          $acc_id = (string)$args['accountId'];
elseif (isset($current_account_id))         $acc_id = (string)$current_account_id;
if ($acc_id === '')                         $acc_id = (string)get_user_meta($user_id, 'mt_current_account_id', true);

$per_page = isset($args['data']['per_page']) ? (int)$args['data']['per_page'] : 10;
?>

<div id="<?php echo esc_attr($root_id); ?>"
     class="mt-card"
     data-account-id="<?php echo esc_attr($acc_id); ?>"
     data-per-page="<?php echo (int)$per_page; ?>">

  <!-- Header -->
  <div class="tr-headbar">
    <div class="d-flex align-items-center gap-2">
      <div class="tr-title">Trades</div>
      <span class="mt-tooltip">
        <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0" aria-label="Trades info"></i>
        <span class="mt-tooltip__panel" role="tooltip">
          <div class="mt-tooltip__title">About trades</div>
          <div class="mt-tooltip__body">Switch between Closed and Open positions.</div>
        </span>
      </span>
    </div>
    <div class="tr-seg w-308px" role="group" aria-label="Trades filter">
      <button type="button" class="tr-seg__btn mt-btn mt-btn--xs w-100" data-type="CLOSED" aria-pressed="true">Close</button>
      <button type="button" class="tr-seg__btn mt-btn mt-btn--xs w-100" data-type="OPEN" aria-pressed="false">Open</button>
    </div>
  </div>

  <!-- Grid -->
  <div class="dj-viewport" aria-busy="true">
    <div class="dj-clip">
      <div class="dj-scroll">
        <!-- Header columns -->
        <div class="dj-grid dj-headrow" style="--cols:10;">
         <div class="dj-head is-left">Side</div>
          <div class="dj-head is-left">Symbol</div>         
          <div class="dj-head is-right">Close Date</div>
          <div class="dj-head is-right">Net P&amp;L</div>
          <div class="dj-head is-right">Net ROI</div>
          <div class="dj-head is-right">Duration</div>
          <div class="dj-head is-right">Avg. Entry</div>
          <div class="dj-head is-right">Avg. Exit</div>
          <div class="dj-head is-right">Open Date</div>
          <div class="dj-head is-right">Status</div>
        </div>
        <!-- Rows container -->
        <div class="dj-rows"></div>
      </div>
    </div>
  </div>

  <!-- Pager -->
  <nav id="<?php echo esc_attr($pager_id); ?>" class="dj-pager" aria-label="Trades pagination" hidden>
    <span class="dj-pages">Showing <span class="js-showing">0</span>/<span class="js-total">0</span></span>
    <div class="d-flex gap-1">
      <button class="dj-btn js-prev" type="button" disabled>
        <span class="mt-icon mt-icon-white mt-icon_chevron-left" aria-hidden="true"></span>
      </button>
      <button class="dj-btn js-next" type="button" disabled>
        <span class="mt-icon mt-icon-white mt-icon_chevron-right" aria-hidden="true"></span>
      </button>
    </div>
  </nav>
</div>
