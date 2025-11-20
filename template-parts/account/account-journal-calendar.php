<?php
/**
 * File: template-parts/account/account-journal-calendar.php
 * Trade calendar with Month/Year navigation and TODAY button.
 *
 * Expected $args:
 *  - $args['meta']['accountId'] (string, required for live data)
 *  - $args['data']['days'] optional (manual override)
 */
defined('ABSPATH') || exit;

/* ===== Resolve accountId ===== */
$acc_id = '';
if (!empty($args['meta']['accountId'])) {
  $acc_id = (string) $args['meta']['accountId'];
} elseif (!empty($args['account']['accountId'])) {
  $acc_id = (string) $args['account']['accountId'];
} elseif (!empty($args['account']['id'])) {
  $acc_id = (string) $args['account']['id'];
} elseif (!empty($args['account_id'])) {
  $acc_id = (string) $args['account_id'];
}

/* ===== Build days from real trades (mt_trades_history_fetch) ===== */
$days_arg = [];

if ($acc_id !== '' && function_exists('mt_trades_history_fetch')) {
  $page    = 1;
  $perPage = 500;

  if (function_exists('error_log')) {
    error_log('[mt-journal-cal] accountId=' . $acc_id . ' type=CLOSED page=' . $page . ' perPage=' . $perPage);
  }

  $resp = mt_trades_history_fetch($acc_id, 'CLOSED', $page, $perPage);

  if (function_exists('error_log')) {
    error_log('[mt-journal-cal] resp=' . print_r($resp, true));
  }

  $records = (isset($resp['records']) && is_array($resp['records'])) ? $resp['records'] : [];

  if (function_exists('error_log')) {
    error_log('[mt-journal-cal] records_count=' . count($records));
  }

  $bucket = [];

  foreach ($records as $t) {
    if (!is_array($t)) {
      continue;
    }

    // Helper retorna openDate / closeDate en m/d/Y (ya con cutoff aplicado)
    $ymd = '';

    if (!empty($t['closeDate']) && is_string($t['closeDate'])) {
      $dt = DateTimeImmutable::createFromFormat('m/d/Y', $t['closeDate']);
      if ($dt instanceof DateTimeImmutable) {
        $ymd = $dt->format('Y-m-d');
      }
    }

    if ($ymd === '' && !empty($t['openDate']) && is_string($t['openDate'])) {
      $dt = DateTimeImmutable::createFromFormat('m/d/Y', $t['openDate']);
      if ($dt instanceof DateTimeImmutable) {
        $ymd = $dt->format('Y-m-d');
      }
    }

    if ($ymd === '') {
      continue;
    }

    $net = isset($t['net']) ? (float) $t['net'] : 0.0;

    if (!isset($bucket[$ymd])) {
      $bucket[$ymd] = [
        'date'   => $ymd,
        'pnl'    => 0.0,
        'trades' => 0,
      ];
    }

    $bucket[$ymd]['pnl']    += $net;
    $bucket[$ymd]['trades'] += 1;
  }

  $days_arg = array_values($bucket);

  usort(
    $days_arg,
    static function ($a, $b) {
      return strcmp((string) $a['date'], (string) $b['date']);
    }
  );
}

/* ===== Optional manual override (sin mock por defecto) ===== */
if (empty($days_arg)) {
  if (isset($args['data']['days']) && is_array($args['data']['days'])) {
    $days_arg = $args['data']['days'];
  } elseif (isset($args['data']) && is_array($args['data']) && isset($args['data'][0]['date'])) {
    $days_arg = $args['data'];
  } elseif (isset($args['days']) && is_array($args['days'])) {
    $days_arg = $args['days'];
  }
}

/* ===== Range params ===== */
$now = new DateTimeImmutable('now');
$years_back  = isset($args['data']['years_back']) ? (int) $args['data']['years_back'] : 20;
$years_back  = max(1, $years_back);
$start_year  = isset($args['data']['start_year']) ? (int) $args['data']['start_year'] : (int) $now->format('Y');
$start_month = isset($args['data']['start_month']) ? (int) $args['data']['start_month'] : ((int) $now->format('n') - 1);
$start_month = max(0, min(11, $start_month));
?>

<div
  class="mt-card mt-account-journal-cal"
  data-account-id="<?php echo esc_attr($acc_id); ?>"
>
  <div class="mt-card__wrapper mt-account-journal-cal__wrapper">
    <!-- Header -->
    <div class="mt-card__header mt-account-journal-cal__header">
      <div class="mt-account-journal-cal__title-row">
        <div class="mt-card__title mt-account-journal-cal__title">
          <div class="mt-card__title__journal"><?php echo esc_html(Label::META_ACCOUNT_OVERVIEW_TOOLTIP['tradeCalendar']); ?></div>
          <span class="mt-tooltip">
            <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0" aria-label="Calendar info"></i>
            <span class="mt-tooltip__panel" role="tooltip">
              <div class="mt-tooltip__title">
                <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW_TOOLTIP['tradeCalendar']); ?>
              </div>
              <div class="mt-tooltip__body">
                <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW_TOOLTIP['tradeCalendarBody']); ?>
              </div>
            </span>
          </span>
        </div>
      </div>

      <!-- Month / Year navigation row -->
      <div class="mt-account-journal-cal__nav-row w-sm-100">
        <!-- Prev -->
        <button
          type="button"
          class="mt-btn mt-btn--sm mt-btn--secondary mt-btn--outline mt-account-journal-cal__nav-btn"
          data-cal-prev
          aria-label="Previous month">
          <span class="mt-icon mt-icon_caret-left"></span>
        </button>

        <!-- Month dropdown -->
        <div
          class="mt-dd"
          data-cal-month-dd
          data-type="month"
          role="combobox"
          aria-expanded="false"
          aria-haspopup="listbox"
          aria-label="Month"
          tabindex="0">
          <button type="button" class="mt-dd__button" data-dd-btn>
            <span data-dd-label>—</span>
            <i class="mt-icon mt-icon_caret-down"></i>
          </button>
          <div class="mt-dd__panel" role="listbox" tabindex="-1" hidden></div>
        </div>

        <!-- Year dropdown -->
        <div
          class="mt-dd"
          data-cal-year-dd
          data-type="year"
          role="combobox"
          aria-expanded="false"
          aria-haspopup="listbox"
          aria-label="Year"
          tabindex="0">
          <button type="button" class="mt-dd__button" data-dd-btn>
            <span data-dd-label>—</span>
            <i class="mt-icon mt-icon_caret-down"></i>
          </button>
          <div class="mt-dd__panel" role="listbox" tabindex="-1" hidden></div>
        </div>

        <!-- Next -->
        <button
          type="button"
          class="mt-btn mt-btn--sm mt-btn--secondary mt-btn--outline mt-account-journal-cal__nav-btn"
          data-cal-next
          aria-label="Next month">
          <span class="mt-icon mt-icon_caret-right"></span>
        </button>
      </div>

      <!-- TODAY -->
      <button
        type="button"
        class="mt-btn mt-btn--sm mt-btn--secondary mt-account-journal-cal__today"
        data-cal-today>
        TODAY
      </button>
    </div>

    <!-- Body -->
    <div class="mt-card__body mt-account-journal-cal__body">
      <!-- Head (DOW) -->
      <div class="mt-cal-head">
        <?php foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $dow): ?>
          <div class="mt-cal-dow"><?php echo esc_html($dow); ?></div>
        <?php endforeach; ?>
      </div>

      <!-- Grid -->
      <div
        class="mt-trade-calendar"
        data-days='<?php echo esc_attr(wp_json_encode($days_arg, JSON_UNESCAPED_SLASHES)); ?>'
        data-years-back="<?php echo (int) $years_back; ?>"
        data-start-year="<?php echo (int) $start_year; ?>"
        data-start-month="<?php echo (int) $start_month; ?>">
      </div>

      <!-- Floating tooltip for mobile -->
      <div class="mt-cal-tooltip" data-cal-tooltip hidden>
        <div class="mt-cal-tooltip__panel" role="tooltip">
          <div class="mt-cal-tooltip__pnl" data-tooltip-pnl></div>
          <div class="mt-cal-tooltip__trades" data-tooltip-trades></div>
        </div>
      </div>
    </div>
  </div>
</div>

