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
$in = isset($args['data']) && is_array($args['data']) ? $args['data'] : [];
$rows = isset($in['rows']) && is_array($in['rows']) ? $in['rows'] : [];
$per_page = isset($in['per_page']) ? (int)$in['per_page'] : 7;

// Contexto de cuenta (como ya lo tenías)
$user_id = get_current_user_id();
$acc_id  = 0;
if (isset($args) && is_array($args)) {
  if (isset($args['meta']['accountId'])) $acc_id = (int) $args['meta']['accountId'];
  elseif (isset($args['accountId']))     $acc_id = (int) $args['accountId'];
}
if (!$acc_id && isset($current_account_id)) $acc_id = (int) $current_account_id;
if (!$acc_id)                                $acc_id = (int) get_user_meta($user_id, 'mt_current_account_id', true);


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
