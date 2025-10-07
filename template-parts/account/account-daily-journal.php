<?php
/**
 * Template Part: Account Daily Journal
 * Ruta: template-parts/account/account-daily-journal.php
 *
 * Requisitos:
 * - mt_get_daily_feedback($user_id, $account_id, $date_yyyy_mm_dd)
 * - Modal/Popover #mt-feedback-modal en account-overview.php
 * - JS mt-account-overview.js (abre popover y guarda por AJAX)
 */
if (!defined('ABSPATH')) exit;

$root_id  = 'mt-daily-journal';
$pager_id = 'mt-daily-journal-pager';

// Payload inyectado desde overview
$in       = (isset($args['data']) && is_array($args['data'])) ? $args['data'] : [];
$rows     = (isset($in['rows']) && is_array($in['rows'])) ? $in['rows'] : [];
$per_page = isset($in['per_page']) ? (int)$in['per_page'] : 7;

/** ===== Contexto de cuenta (lee desde $args['meta']['accountId']) ===== */
$user_id = get_current_user_id();
$acc_id  = '';
if (isset($args) && is_array($args)) {
  if (isset($args['meta']['accountId'])) $acc_id = (string)$args['meta']['accountId'];
  elseif (isset($args['accountId']))     $acc_id = (string)$args['accountId'];
}
if ($acc_id === '' && isset($current_account_id)) $acc_id = (string)$current_account_id;
if ($acc_id === '')                                $acc_id = (string)get_user_meta($user_id, 'mt_current_account_id', true);

// ====== Formatters que respetan '-' ======
$fmt_money = function ($v) {
  if ($v === '-' || $v === null || $v === '') return '-';
  if (!is_numeric($v)) return '-';
  $n    = (float)$v;
  $sign = $n < 0 ? '-' : '';
  $abs  = abs($n);
  return $sign . '$' . number_format($abs, 2, '.', ',');
};
$fmt_int = function ($v) {
  return ($v === '-' || $v === null || $v === '') ? '-' : number_format((int)$v);
};
$fmt_pct = function ($v) {
  return ($v === '-' ? '-' : mt_format_percent_compact($v));
};
$fmt_maxwl = function ($v) {
  $s = trim((string)$v);
  if ($s === '' || $s === '-') return '-';
  if (strpos($s, '/') !== false) {
    [$w, $l] = array_map('trim', explode('/', $s, 2));
    $w = ($w === '' || $w === '-') ? '0' : $w;
    $l = ($l === '' || $l === '-') ? '0' : $l;
    return $w . '/' . $l;
  }
  return $s;
};
$fmt_durwl = function ($v) {
  $s = trim((string)$v);
  if ($s === '' || $s === '-') return '-';
  // admite "00:06:59 -", "00:06:59/00:00:00" o "00:06:59 00:00:00"
  $parts = preg_split('/\s*\/\s*|\s+/', $s);
  $w = $parts[0] ?? '';
  $l = $parts[1] ?? '';
  $w = ($w === '' || $w === '-') ? '00:00:00' : $w;
  $l = ($l === '' || $l === '-') ? '00:00:00' : $l;
  return $w . ' / ' . $l;
};
?>
<div
  id="<?php echo esc_attr($root_id); ?>"
  data-per-page="<?php echo (int)$per_page; ?>"
  data-account-id="<?php echo esc_attr($acc_id); ?>"
  data-nonce="<?php echo esc_attr( wp_create_nonce('mt-acc-nonce') ); ?>"
  class="mt-card"
>
  <div class="dj-viewport">
    <div class="dj-clip">
      <div class="dj-scroll">

        <!-- Encabezados -->
        <div class="dj-grid dj-headrow">
          <div class="dj-head is-left">Daily<br>Journal</div>
          <div class="dj-head is-right">Date</div>
          <div class="dj-head is-right">Net P&L</div>
          <div class="dj-head is-right">P&L High</div>
          <div class="dj-head is-right">P&L Low</div>
          <div class="dj-head is-right">Total<br>Contracts</div>
          <div class="dj-head is-right">Total Fees<br>+Comm</div>
          <div class="dj-head is-right">Total Trades</div>
          <div class="dj-head is-right">Avg. Win<br>Trades</div>
          <div class="dj-head is-right">Avg. Loss<br>Trades</div>
          <div class="dj-head is-right">Winning<br>Trade %</div>
          <div class="dj-head is-right">Max. Consec.<br>W/L Trades</div>
          <div class="dj-head is-right">Avg. W/L<br>Duration</div>
        </div>

        <?php foreach ($rows as $i => $r):
          $page     = (int) floor($i / max(1, $per_page)) + 1;
          $hidden   = ($page === 1) ? '' : 'style="display:none"';

          $dateParts = function_exists('mt_parse_open_time')
            ? mt_parse_open_time($r['openTime'] ?? '')
            : ['iso' => substr((string)($r['openTime'] ?? ''), 0, 10), 'label' => substr((string)($r['openTime'] ?? ''), 0, 10)];
          $day_iso   = $dateParts['iso'];   // YYYY-MM-DD
          $day_label = $dateParts['label']; // MM/DD/YYYY

          $fb     = ($acc_id !== '' && function_exists('mt_get_daily_feedback'))
                    ? mt_get_daily_feedback($user_id, $acc_id, $day_iso)
                    : null;
          $has_fb = !empty($fb);

          // Datos para precarga en tooltip
          $mood     = $has_fb ? (int)($fb['mood'] ?? 0) : 0;
          $followed = $has_fb ? ( (int)($fb['followed_plan'] ?? 0) ? 1 : 0 ) : 0;
          $note     = $has_fb ? (string)($fb['note'] ?? '') : '';

          $net_val   = $r['net'] ?? '-';
          $net_class = (is_numeric($net_val) ? ($net_val > 0 ? 'text-success' : ($net_val < 0 ? 'text-danger' : '')) : '');
          ?>
          <div
            class="dj-grid dj-row"
            id="dj-row-<?php echo esc_attr($day_iso); ?>"
            data-page="<?php echo esc_attr($page); ?>"
            <?php echo $hidden; ?>
            data-trade-date="<?php echo esc_attr($day_iso); ?>"
            data-has-fb="<?php echo $has_fb ? '1' : '0'; ?>"
            data-mood="<?php echo $has_fb ? (int)$mood : ''; ?>"
            data-followed="<?php echo $has_fb ? (int)$followed : ''; ?>"
            data-note="<?php echo $has_fb ? esc_attr($note) : ''; ?>"
          >
            <div class="dj-cell is-left">
              <span class="mt-dj-visibility" role="button" tabindex="0" aria-label="Add daily feedback" title="Daily feedback">
                <i class="mt-icon mt-icon-white <?php echo $has_fb ? 'mt-icon_visibility' : 'mt-icon_pencil'; ?>" aria-hidden="true"></i>
              </span>
            </div>

            <div class="dj-cell is-right"><?php echo esc_html($day_label); ?></div>
            <div class="dj-cell is-right <?php echo esc_attr($net_class); ?>"><?php echo esc_html($fmt_money($net_val)); ?></div>
            <div class="dj-cell is-right"><?php echo esc_html($fmt_money($r['hi']    ?? '-')); ?></div>
            <div class="dj-cell is-right"><?php echo esc_html($fmt_money($r['lo']    ?? '-')); ?></div>
            <div class="dj-cell is-right"><?php echo esc_html($fmt_int  ($r['ct']    ?? '-')); ?></div>
            <div class="dj-cell is-right"><?php echo esc_html($fmt_money($r['fees']  ?? '-')); ?></div>
            <div class="dj-cell is-right"><?php echo esc_html($fmt_int  ($r['trades']?? '-')); ?></div>
            <div class="dj-cell is-right"><?php echo esc_html($fmt_money($r['awin']  ?? '-')); ?></div>
            <div class="dj-cell is-right"><?php echo esc_html($fmt_money($r['aloss'] ?? '-')); ?></div>
            <div class="dj-cell is-right"><?php echo esc_html($fmt_pct  ($r['win']   ?? '-')); ?></div>
            <div class="dj-cell is-right"><?php echo esc_html($fmt_maxwl($r['max']   ?? '-')); ?></div>
            <div class="dj-cell is-right"><?php echo esc_html($fmt_durwl($r['dur']   ?? '-')); ?></div>
          </div>
        <?php endforeach; ?>

      </div>
    </div>
  </div>

  <!-- Paginación -->
  <nav id="<?php echo esc_attr($pager_id); ?>" class="dj-pager" aria-label="Daily Journal pagination">
    <button class="dj-btn mt-dj-prev" type="button" disabled>
      <span class="mt-icon mt-icon-white mt-icon_chevron-left"></span>
    </button>
    <span class="dj-pages d-flex gap-1"></span>
    <button class="dj-btn mt-dj-next" type="button">
      <span class="mt-icon mt-icon-white mt-icon_chevron-right"></span>
    </button>
  </nav>
</div>
