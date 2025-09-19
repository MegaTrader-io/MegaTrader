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
$per_page = 7;

/** ===== Helpers locales (seguros) ===== */
if (!function_exists('mt_money_fmt')) {
  function mt_money_fmt($n) {
    $s = ($n < 0) ? '-' : '';
    return $s . '$' . number_format(abs((float)$n), 2);
  }
}

/**
 * Acepta openTime ISO-8601 (p.ej. 2025-09-12T17:58:28.759Z)
 * Devuelve ['iso' => 'YYYY-MM-DD', 'label' => 'MM/DD/YYYY']
 */
if (!function_exists('mt_parse_open_time')) {
  function mt_parse_open_time($openTime) {
    try {
      $dt = !empty($openTime)
        ? new DateTime($openTime, new DateTimeZone('UTC'))
        : new DateTime('now', new DateTimeZone('UTC'));
    } catch (Exception $e) {
      $dt = new DateTime('now', new DateTimeZone('UTC'));
    }
    return [
      'iso'   => $dt->format('Y-m-d'),
      'label' => $dt->format('m/d/Y'),
    ];
  }
}

/** ===== Datos mock mientras conectas API =====
 * Cuando conectes la API, mapea tu respuesta a este esquema:
 * [
 *   'openTime' => '2025-09-12T17:58:28.759Z',
 *   'net'      => (float),
 *   'hi'       => (float),
 *   'lo'       => (float),
 *   'ct'       => (int),
 *   'fees'     => (float),
 *   'trades'   => (int),
 *   'awin'     => (float),
 *   'aloss'    => (float),
 *   'win'      => (float),  // porcentaje
 *   'max'      => (string), // "W/L" consecutivos
 *   'dur'      => (string), // "avgW avgL"
 * ]
 */
$rows = [
  ['openTime' => '2025-08-11T10:00:00.000Z', 'net' => -73.40, 'hi' => -1.00,  'lo' => -73.40,  'ct' => 70,  'fees' => 25.90,  'trades' => 4,  'awin' => 2.60,  'aloss' => 39.90, 'win' => 50.00, 'max' => '2/1',  'dur' => '00:05:05 00:01:17'],
  ['openTime' => '2025-08-12T10:00:00.000Z', 'net' => 45.10,  'hi' => 88.20,  'lo' => -5.25,   'ct' => 22,  'fees' => 12.20,  'trades' => 9,  'awin' => 18.00, 'aloss' => 9.50,  'win' => 62.50, 'max' => '3/1',  'dur' => '00:08:10 00:03:25'],
  ['openTime' => '2025-08-13T10:00:00.000Z', 'net' => -234.28,'hi' => 6.90,   'lo' => -240.78, 'ct' => 158, 'fees' => 139.28, 'trades' => 22, 'awin' => 28.07, 'aloss' => 22.04, 'win' => 22.73, 'max' => '2/12', 'dur' => '00:06:09 00:04:42'],
  ['openTime' => '2025-08-10T10:00:00.000Z', 'net' => 120.50, 'hi' => 160.10, 'lo' => -8.40,   'ct' => 11,  'fees' => 6.80,   'trades' => 5,  'awin' => 42.30, 'aloss' => 15.60, 'win' => 66.67, 'max' => '4/1',  'dur' => '00:04:20 00:02:10'],
  ['openTime' => '2025-08-13T10:00:00.000Z', 'net' => -15.75, 'hi' => 20.00,  'lo' => -30.40,  'ct' => 33,  'fees' => 9.99,   'trades' => 7,  'awin' => 10.15, 'aloss' => 11.30, 'win' => 42.86, 'max' => '2/2',  'dur' => '00:03:50 00:02:40'],
  ['openTime' => '2025-08-12T10:00:00.000Z', 'net' => 90.00,  'hi' => 120.00, 'lo' => -2.10,   'ct' => 40,  'fees' => 19.10,  'trades' => 8,  'awin' => 25.50, 'aloss' => 10.40, 'win' => 62.50, 'max' => '3/1',  'dur' => '00:07:33 00:03:01'],
  ['openTime' => '2025-08-12T10:00:00.000Z', 'net' => 90.00,  'hi' => 120.00, 'lo' => -2.10,   'ct' => 40,  'fees' => 19.10,  'trades' => 8,  'awin' => 25.50, 'aloss' => 10.40, 'win' => 62.50, 'max' => '3/1',  'dur' => '00:07:33 00:03:01'],
  ['openTime' => '2025-08-12T10:00:00.000Z', 'net' => 90.00,  'hi' => 120.00, 'lo' => -2.10,   'ct' => 40,  'fees' => 19.10,  'trades' => 8,  'awin' => 25.50, 'aloss' => 10.40, 'win' => 62.50, 'max' => '3/1',  'dur' => '00:07:33 00:03:01'],
  ['openTime' => '2025-08-14T10:00:00.000Z', 'net' => 120.50, 'hi' => 160.10, 'lo' => -8.40,   'ct' => 11,  'fees' => 6.80,   'trades' => 5,  'awin' => 42.30, 'aloss' => 15.60, 'win' => 66.67, 'max' => '4/1',  'dur' => '00:04:20 00:02:10'],
  ['openTime' => '2025-08-13T10:00:00.000Z', 'net' => -15.75, 'hi' => 20.00,  'lo' => -30.40,  'ct' => 33,  'fees' => 9.99,   'trades' => 7,  'awin' => 10.15, 'aloss' => 11.30, 'win' => 42.86, 'max' => '2/2',  'dur' => '00:03:50 00:02:40'],
  ['openTime' => '2025-08-12T10:00:00.000Z', 'net' => 90.00,  'hi' => 120.00, 'lo' => -2.10,   'ct' => 40,  'fees' => 19.10,  'trades' => 8,  'awin' => 25.50, 'aloss' => 10.40, 'win' => 62.50, 'max' => '3/1',  'dur' => '00:07:33 00:03:01'],
  ['openTime' => '2025-08-12T10:00:00.000Z', 'net' => 90.00,  'hi' => 120.00, 'lo' => -2.10,   'ct' => 40,  'fees' => 19.10,  'trades' => 8,  'awin' => 25.50, 'aloss' => 10.40, 'win' => 62.50, 'max' => '3/1',  'dur' => '00:07:33 00:03:01'],
  ['openTime' => '2025-08-12T10:00:00.000Z', 'net' => 90.00,  'hi' => 120.00, 'lo' => -2.10,   'ct' => 40,  'fees' => 19.10,  'trades' => 8,  'awin' => 25.50, 'aloss' => 10.40, 'win' => 62.50, 'max' => '3/1',  'dur' => '00:07:33 00:03:01'],
];

/** ===== Contexto de cuenta (lee desde $args['meta']['accountId']) ===== */
$user_id = get_current_user_id();
$acc_id  = 0;
if (isset($args) && is_array($args)) {
  if (isset($args['meta']['accountId'])) $acc_id = (int) $args['meta']['accountId'];
  elseif (isset($args['accountId']))     $acc_id = (int) $args['accountId'];
}
if (!$acc_id && isset($current_account_id)) $acc_id = (int) $current_account_id;
if (!$acc_id)                                $acc_id = (int) get_user_meta($user_id, 'mt_current_account_id', true);

?>
<div
  id="<?php echo esc_attr($root_id); ?>"
  data-per-page="<?php echo (int)$per_page; ?>"
  data-account-id="<?php echo (int)$acc_id; ?>"
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
          $page   = (int) floor($i / $per_page) + 1;
          $hidden = ($page === 1) ? '' : 'style="display:none"';

          $dateParts = mt_parse_open_time($r['openTime'] ?? '');
          $day_iso   = $dateParts['iso'];   // YYYY-MM-DD
          $day_label = $dateParts['label']; // MM/DD/YYYY

          $fb     = ($acc_id && function_exists('mt_get_daily_feedback'))
                    ? mt_get_daily_feedback($user_id, $acc_id, $day_iso)
                    : null;
          $has_fb = !empty($fb);

          // Datos para precarga en tooltip
          $mood     = $has_fb ? (int)($fb['mood'] ?? 0) : 0;
          $followed = $has_fb ? ( (int)($fb['followed_plan'] ?? 0) ? 1 : 0 ) : 0;
          $note     = $has_fb ? (string)($fb['note'] ?? '') : '';

          $net_class = isset($r['net'])
            ? ($r['net'] > 0 ? 'text-success' : ($r['net'] < 0 ? 'text-danger' : ''))
            : '';
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
            <div class="dj-cell is-right <?php echo esc_attr($net_class); ?>"><?php echo esc_html(mt_money_fmt($r['net']   ?? 0)); ?></div>
            <div class="dj-cell is-right"><?php echo esc_html(mt_money_fmt($r['hi']    ?? 0)); ?></div>
            <div class="dj-cell is-right"><?php echo esc_html(mt_money_fmt($r['lo']    ?? 0)); ?></div>
            <div class="dj-cell is-right"><?php echo esc_html(number_format((int)($r['ct']     ?? 0))); ?></div>
            <div class="dj-cell is-right"><?php echo esc_html(mt_money_fmt($r['fees']  ?? 0)); ?></div>
            <div class="dj-cell is-right"><?php echo esc_html(number_format((int)($r['trades'] ?? 0))); ?></div>
            <div class="dj-cell is-right"><?php echo esc_html(mt_money_fmt($r['awin']  ?? 0)); ?></div>
            <div class="dj-cell is-right"><?php echo esc_html(mt_money_fmt($r['aloss'] ?? 0)); ?></div>
            <div class="dj-cell is-right"><?php echo esc_html(number_format((float)($r['win'] ?? 0), 2)); ?>%</div>
            <div class="dj-cell is-right"><?php echo esc_html($r['max'] ?? ''); ?></div>
            <div class="dj-cell is-right"><?php echo esc_html($r['dur'] ?? ''); ?></div>
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
