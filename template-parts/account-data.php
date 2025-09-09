<?php
/**
 * Partial: template-parts/account-data.php
 * Purpose: Fetch accounts by email (and optional account by ID) and render minimal tables.
 */
defined('ABSPATH') || exit;

// ---------- helpers ----------
function mega_ad_safe_get($arr, $path, $default = '-') {
    $cur = $arr;
    foreach (explode('.', $path) as $seg) {
        if (is_array($cur) && array_key_exists($seg, $cur)) {
            $cur = $cur[$seg];
        } else {
            return $default;
        }
    }
    return ($cur === '' || $cur === null) ? $default : $cur;
}
function mega_ad_decode_shortcode_json($shortcode) {
    $raw = do_shortcode($shortcode);
    $data = json_decode($raw, true);
    if (!is_array($data)) return ['error' => 'Malformed JSON'];
    if (isset($data['error'])) return $data;
    if (isset($data['items']) && is_array($data['items'])) return $data['items'];
    if (isset($data['data'])  && is_array($data['data']))  return $data['data'];
    return $data; // already an array of objects
}

// ---------- inputs ----------
$current_user = wp_get_current_user();
$email = isset($_GET['email']) && $_GET['email'] !== ''
    ? sanitize_email($_GET['email'])
    : ($current_user && $current_user->exists() ? $current_user->user_email : '');

$page    = isset($_GET['page'])    ? max(1, intval($_GET['page']))    : 1;
$perPage = isset($_GET['perPage']) ? max(1, intval($_GET['perPage'])) : 10;
$account_id = isset($_GET['account_id']) ? sanitize_text_field($_GET['account_id']) : '';

// ---------- fetch: accounts by email ----------
$accounts = [];
$accounts_error = '';
if ($email) {
    $sc = sprintf('[mega_accounts_data email="%s" page="%d" perpage="%d" output="json"]',
        esc_attr($email), $page, $perPage
    );
    $accounts = mega_ad_decode_shortcode_json($sc);
    if (isset($accounts['error'])) { $accounts_error = $accounts['error']; $accounts = []; }
}

// ---------- fetch: account by ID (optional) ----------
$account_detail = null;
$detail_error = '';
if ($account_id) {
    $scd = sprintf('[mega_account_data id="%s" page="1" perpage="10" output="json"]', esc_attr($account_id));
    $account_detail = mega_ad_decode_shortcode_json($scd);
    if (isset($account_detail['error'])) { $detail_error = $account_detail['error']; $account_detail = null; }
    // If wrapper array with one item, unwrap
    if (is_array($account_detail) && isset($account_detail[0]) && is_array($account_detail[0])) {
        $account_detail = $account_detail[0];
    }
}
?>

<div class="mega-accounts-data">
  <h3>Accounts</h3>

  <?php if (!$email): ?>
    <p><em>No email provided (login or add <code>?email=</code> in URL).</em></p>
  <?php elseif ($accounts_error): ?>
    <p><strong>Error:</strong> <?php echo esc_html($accounts_error); ?></p>
  <?php elseif (empty($accounts)): ?>
    <p><em>No accounts found.</em></p>
  <?php else: ?>
    <table class="wp-list-table widefat striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Status</th>
          <th>Program</th>
          <th>Current Balance</th>
          <th>Current Equity</th>
          <th>Current Profit</th>
          <th>Created At</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($accounts as $acc): ?>
          <tr>
            <td><?php echo esc_html(mega_ad_safe_get($acc, 'id')); ?></td>
            <td><?php echo esc_html(mega_ad_safe_get($acc, 'status')); ?></td>
            <td><?php echo esc_html(mega_ad_safe_get($acc, 'program.label', mega_ad_safe_get($acc, 'program.description'))); ?></td>
            <td><?php echo esc_html(mega_ad_safe_get($acc, 'metrics.currentBalance')); ?></td>
            <td><?php echo esc_html(mega_ad_safe_get($acc, 'metrics.currentEquity')); ?></td>
            <td><?php echo esc_html(mega_ad_safe_get($acc, 'metrics.currentProfit')); ?></td>
            <td><?php echo esc_html(mega_ad_safe_get($acc, 'createdAt')); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php if ($account_id): ?>
  <div class="mega-account-detail" style="margin-top:20px;">
    <h3>Account by ID: <?php echo esc_html($account_id); ?></h3>
    <?php if ($detail_error): ?>
      <p><strong>Error:</strong> <?php echo esc_html($detail_error); ?></p>
    <?php elseif (is_array($account_detail)): ?>
      <table class="wp-list-table widefat striped">
        <thead>
          <tr>
            <th>ID</th><th>Status</th><th>Program</th>
            <th>Balance</th><th>Equity</th><th>Profit</th>
            <th>WinRate</th><th>LossRate</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><?php echo esc_html(mega_ad_safe_get($account_detail, 'id')); ?></td>
            <td><?php echo esc_html(mega_ad_safe_get($account_detail, 'status')); ?></td>
            <td><?php echo esc_html(mega_ad_safe_get($account_detail, 'program.label', mega_ad_safe_get($account_detail, 'program.description'))); ?></td>
            <td><?php echo esc_html(mega_ad_safe_get($account_detail, 'metrics.currentBalance')); ?></td>
            <td><?php echo esc_html(mega_ad_safe_get($account_detail, 'metrics.currentEquity')); ?></td>
            <td><?php echo esc_html(mega_ad_safe_get($account_detail, 'metrics.currentProfit')); ?></td>
            <td><?php echo esc_html(mega_ad_safe_get($account_detail, 'metrics.winRate')); ?></td>
            <td><?php echo esc_html(mega_ad_safe_get($account_detail, 'metrics.lossRate')); ?></td>
          </tr>
        </tbody>
      </table>
    <?php else: ?>
      <p><em>No detail found.</em></p>
    <?php endif; ?>
  </div>
<?php endif; ?>
