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
<style>
/* Scroll/grid alias (como Daily Journal) */
#<?php echo $root_id; ?> { --cols:11; --colw:120px; --bg:var(--Surface-Page,#1E1E1E); }
#<?php echo $root_id; ?> .dj-viewport{max-inline-size:100%}
#<?php echo $root_id; ?> .dj-clip{position:relative;overflow:hidden;border-radius:inherit;background:var(--bg)}
#<?php echo $root_id; ?> .dj-scroll{overflow-x:auto;overflow-y:hidden;background:var(--bg);scrollbar-width:thin;scrollbar-color:rgba(255,255,255,.15) transparent}
#<?php echo $root_id; ?> .dj-grid{display:grid;grid-template-columns:repeat(var(--cols),var(--colw));inline-size:max-content}
#<?php echo $root_id; ?> .dj-headrow{border-bottom:1px solid rgba(255,255,255,.12)}
#<?php echo $root_id; ?> .dj-row{border-bottom:1px solid var(--Colors-Gray-800,#292524)}
#<?php echo $root_id; ?> .dj-head{color:#fff;font-weight:700;font-size:.875rem;line-height:1.25;padding:.75rem .9rem}
#<?php echo $root_id; ?> .dj-cell{color:var(--Text-Body,#A8A29E);font-weight:500;font-size:.875rem;line-height:1.125rem;padding:.75rem .9rem;white-space:normal}
#<?php echo $root_id; ?> .is-right{text-align:right} .is-left{text-align:left}

/* Header & segmented buttons */
#<?php echo $root_id; ?> .tr-headbar{display:flex;align-items:center;gap:8px;justify-content:space-between}
#<?php echo $root_id; ?> .tr-title{color:var(--Text-Body,#A8A29E);font:500 16px/24px Roboto,system-ui,sans-serif}
#<?php echo $root_id; ?> .tr-seg{display:flex;gap:4px;padding:4px;border:1px solid var(--Colors-Gray-700,#404040);border-radius:8px;background:var(--Surface-Page,#1E1E1E)}
#<?php echo $root_id; ?> .tr-seg__btn{padding:4px 12px;border-radius:4px;font:500 14px/20px Roboto,system-ui,sans-serif;text-transform:uppercase;color:#fff;background:transparent;border:0;cursor:pointer}
#<?php echo $root_id; ?> .tr-seg__btn[aria-pressed="true"]{background:#fff;color:#292524}

/* Pills */
#<?php echo $root_id; ?> .mt-pill{display:inline-flex;align-items:center;justify-content:center;padding:4px 8px;border-radius:12px;font-weight:700;text-transform:uppercase;font-size:.75rem;line-height:16px}
#<?php echo $root_id; ?> .mt-pill--sec{background:var(--Secondary-500,#14B8A6);color:var(--Surface-Body,#131210)}
#<?php echo $root_id; ?> .mt-pill--err{background:var(--Error-500,#F43F5E);color:var(--Surface-Body,#131210)}

/* Pager */
#<?php echo $root_id; ?> .dj-pager{display:flex;align-items:center;justify-content:flex-end;gap:8px;margin-top:8px}
#<?php echo $root_id; ?> .dj-btn{display:inline-flex;align-items:center;justify-content:center;padding:4px;background:var(--Surface-Dark,#292524);border:1px solid var(--Colors-Gray-700,#404040);border-radius:4px}
#<?php echo $root_id; ?> .dj-pages{color:var(--Text-Body,#A8A29E);font:500 14px/20px Roboto,system-ui,sans-serif}
</style>

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
    <div class="tr-seg" role="group" aria-label="Trades filter">
      <button type="button" class="tr-seg__btn" data-type="CLOSED" aria-pressed="true">Close</button>
      <button type="button" class="tr-seg__btn" data-type="OPEN" aria-pressed="false">Open</button>
    </div>
  </div>

  <!-- Grid -->
  <div class="dj-viewport" aria-busy="true">
    <div class="dj-clip">
      <div class="dj-scroll">
        <!-- Header columns -->
        <div class="dj-grid dj-headrow" style="--cols:10;">
          <div class="dj-head is-left">Symbol</div>
          <div class="dj-head is-right">Close Date</div>
          <div class="dj-head is-right">Net P&amp;L</div>
          <div class="dj-head is-right">Net ROI</div>
          <div class="dj-head is-right">Duration</div>
          <div class="dj-head is-right">Avg. Entry</div>
          <div class="dj-head is-right">Avg. Exit</div>
          <div class="dj-head is-right">Open Date</div>
          <div class="dj-head is-right">Tag</div>
          <div class="dj-head is-right">Status</div>        </div>
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
