<?php
/**
 * Template part: Account Data
 * Espera $args['meta']['accountId'] (string 24-hex)
 */
if (!defined('ABSPATH')) exit;

// 1) Tomar accountId como STRING (no usar absint en ids alfanuméricos)
$account_id = '';
if (isset($args['meta']['accountId'])) {
  $account_id = sanitize_text_field($args['meta']['accountId']);
}

// 2) Validar formato de ObjectId (24 hex). Si no, empty state.
if (!preg_match('/^[a-f0-9]{24}$/i', $account_id)) : ?>
  <div class="mt-account-data card background-page radius-16 outline outline-gray-700 p-24">
    <div class="text-body-16"><?php esc_html_e('Select an account to view credentials.', 'megatrader'); ?></div>
  </div>
  <?php return;
endif;

// 3) Obtener credenciales desde helper central (API via shortcode)
if (!function_exists('mt_accounts_get_credentials')) {
  echo '<div class="mt-account-data p-24">Helper mt_accounts_get_credentials() missing.</div>';
  return;
}
$creds    = mt_accounts_get_credentials($account_id);
$login    = sanitize_text_field($creds['login'] ?? '');
$server   = sanitize_text_field($creds['server'] ?? '');
$pwd      = (string)($creds['password'] ?? '');
$hasPwd   = ($pwd !== '');
$links    = $creds['links'] ?? ['web'=>'','appstore'=>'','playstore'=>''];
$platform = $creds['platform'] ?? ['name'=>'Trading Platform','icon_class'=>''];

?>
<div class="mt-card">
  <div class="d-flex align-items-center gap-16 flex-wrap">
    <div class="flex-grow-1 d-flex flex-column gap-4">
      <h3 class="h5 text-uppercase m-0"><?php esc_html_e('Trading Account', 'megatrader'); ?></h3>
      <div class="text-16 fw-500"><?php esc_html_e('Access the platform', 'megatrader'); ?></div>
      <div class="d-flex gap-8 pt-16 flex-wrap">
        <?php if (!empty($links['web'])): ?>
          <a class="btn btn-sm btn-dark" href="<?php echo esc_url($links['web']); ?>" target="_blank" rel="noopener"><?php esc_html_e('Web app','megatrader'); ?></a>
        <?php endif; ?>
        <?php if (!empty($links['appstore'])): ?>
          <a class="btn btn-sm btn-dark" href="<?php echo esc_url($links['appstore']); ?>" target="_blank" rel="noopener"><?php esc_html_e('App Store','megatrader'); ?></a>
        <?php endif; ?>
        <?php if (!empty($links['playstore'])): ?>
          <a class="btn btn-sm btn-dark" href="<?php echo esc_url($links['playstore']); ?>" target="_blank" rel="noopener"><?php esc_html_e('Google Play','megatrader'); ?></a>
        <?php endif; ?>
      </div>
    </div>

    <div class="vr d-none d-md-block"></div>

    <div class="d-flex align-items-center gap-12">
      <div class="mt-platform-avatar">
        <span class="<?php echo esc_attr($platform['icon_class'] ?? ''); ?>" title="<?php echo esc_attr($platform['name'] ?? 'Trading Platform'); ?>"></span>
      </div>

      <div class="d-flex flex-column gap-8">
        <div class="d-flex align-items-center gap-16">
          <div class="label-16"><?php esc_html_e('Login','megatrader'); ?></div>
          <div class="text-body-16 flex-grow-1"><?php echo $login ? esc_html($login) : '--'; ?></div>
          <?php if ($login): ?>
            <button type="button" class="btn btn-sm btn-outline-light" data-copy="<?php echo esc_attr($login); ?>" aria-label="<?php esc_attr_e('Copy login','megatrader'); ?>">Copy</button>
          <?php endif; ?>
        </div>

        <div class="d-flex align-items-center gap-16">
          <div class="label-16"><?php esc_html_e('Password','megatrader'); ?></div>
          <div class="text-body-16 js-pwd-mask"><?php echo $hasPwd ? '••••••••••••' : '--'; ?></div>
          <?php if ($hasPwd): ?>
            <div class="d-flex align-items-center gap-8">
              <button type="button" class="btn btn-sm btn-outline-light js-pwd-toggle" data-pwd="<?php echo esc_attr($pwd); ?>" aria-expanded="false"><?php esc_html_e('Show','megatrader'); ?></button>
              <button type="button" class="btn btn-sm btn-outline-light" data-copy="<?php echo esc_attr($pwd); ?>"><?php esc_html_e('Copy','megatrader'); ?></button>
            </div>
          <?php endif; ?>
        </div>

        <div class="d-flex align-items-center gap-16">
          <div class="label-16"><?php esc_html_e('Server','megatrader'); ?></div>
          <div class="text-body-16"><?php echo $server ? esc_html($server) : '--'; ?></div>
        </div>
      </div>
    </div>
  </div>
</div>
