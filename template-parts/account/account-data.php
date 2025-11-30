<?php
/**
 * Template part: Account Data
 */
if (!defined('ABSPATH'))
  exit;

// accountId (string)
$account_id = isset($args['meta']['accountId']) ? sanitize_text_field($args['meta']['accountId']) : '';

$current_user = wp_get_current_user();
$user_email = ($current_user && $current_user->exists()) ? (string) $current_user->user_email : '';

$data = isset($args['data']) && is_array($args['data']) ? $args['data'] : [];

$agreement_missing = ($args['meta']['agreementShow'] ?? '') === '1';

$login  = $agreement_missing ? 'sample@megatrader.io' : ($data['login'] ?? null);
$server = ($data['server'] ?? null);
$pwd    = $agreement_missing ? null : ($data['password'] ?? null);


$platform_img = '/wp-content/uploads/2025/07/Stylecolor-Sizelg.svg';
if (!empty($args['platform_image'])) {
  $platform_img = esc_url_raw($args['platform_image']);
}

$link_web = 'http://trade.megatrader.io';
$link_appstore = 'https://apps.apple.com/us/app/megatraderx/id6753067261';
$link_playstore = 'https://play.google.com/store/apps/details?id=com.megatraderxt.mobile';

$agreement_url = '';
$root = $args['meta']['agreementShow'] ?? null;
if ($root !== null) {
  $agreement_url = (string) ($args['meta']['agreementUrl'] ?? '');
}
?>

<div class="mt-card" data-account-id="<?php echo esc_attr($account_id); ?>">
  <div
    class="mt-card-wrapper d-flex align-items-center gap-3 w-100 justify-content-between flex-column flex-lg-row flex-md-row">

    <div
      class="mt-card-content d-flex align-items-center gap-3 w-100 justify-content-between flex-column flex-lg-row flex-md-row">

      <div class="d-flex flex-column gap-1 flex-wrap flex-shrink-0">
        <div class="text-white fw-500 text-2xl text-uppercase">
          <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['account_data_title']); ?>
        </div>
        <div class="text-16 fw-500">
          <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['account_platform_description']); ?>
        </div>

        <div class="d-flex gap-2 pt-3 flex-wrap">
          <a href="<?php echo esc_url($link_web); ?>" target="_blank" class="text-decoration-none" rel="noopener">
            <div class="mt-badge mt-badge-apps">
              <i class="mt-icon mt-icon-sm mt-icon_globe"></i>
              <span class="mt-card__links__text"><?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['web_app']); ?></span>
            </div>
          </a>

          <a href="<?php echo esc_url($link_appstore); ?>" target="_blank" class="text-decoration-none" rel="noopener">
            <div class="mt-badge mt-badge-apps">
              <i class="mt-icon mt-icon-sm mt-icon_app-store"></i>
              <span
                class="mt-card__links__text"><?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['app_store']); ?></span>
            </div>
          </a>

          <a href="<?php echo esc_url($link_playstore); ?>" target="_blank" class="text-decoration-none" rel="noopener">
            <div class="mt-badge mt-badge-apps">
              <i class="mt-icon mt-icon-sm mt-icon_google-play"></i>
              <span
                class="mt-card__links__text"><?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['play_store']); ?></span>
            </div>
          </a>
        </div>
      </div>

      <div class="vr d-none d-md-block"></div>
      <div class="d-block d-md-none w-100 h-1px bg-404040"></div>

      <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0 mt-account-data-credentials">
        <div class="mt-platform-avatar flex-shrink-0">
          <img src="<?php echo esc_url($platform_img); ?>" alt="DXXT logo" width="64" height="64"
            style="width:64px;height:64px;border-radius:9999px;object-fit:cover;" />
        </div>

        <div class="d-flex flex-column gap-2 flex-grow-1 min-w-0">
          <div class="d-flex align-items-center flex-nowrap gap-2 min-w-0">
            <div class="text-white flex-shrink-0">
              <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['account_login']); ?>
            </div>

            <div class="flex-grow-1 text-truncate pe-2 min-w-0">
              <span class="text-base text-a8a29e fw-medium" title="<?php echo esc_attr($login); ?>">
                <?php echo $login ? esc_html($login) : '--'; ?>
              </span>
            </div>

            <?php if ($login): ?>
              <span class="d-inline-flex align-items-center justify-content-center flex-shrink-0"
                data-copy="<?php echo esc_attr($login); ?>" role="button" tabindex="0"
                aria-label="<?php esc_attr_e('Copy login', 'megatrader'); ?>"
                title="<?php esc_attr_e('Copy login', 'megatrader'); ?>">
                <i class="mt-icon mt-icon-white mt-icon_content-copy" aria-hidden="true"></i>
              </span>
            <?php endif; ?>
          </div>

          <div class="d-flex align-items-center flex-nowrap min-w-0 gap-3" data-pwd-row>
            <div class="text-white flex-shrink-0">
              <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['account_password']); ?>
            </div>

            <div class="flex-grow-1 text-truncate pe-2 min-w-0">
              <span class="text-base text-a8a29e fw-medium js-pwd-mask"
                title="<?php esc_attr_e('Hidden password', 'megatrader'); ?>">
                ••••••••••••
              </span>
            </div>

            <div class="d-flex align-items-center gap-2 ms-1 flex-shrink-0">
              <span class="d-inline-flex align-items-center justify-content-center js-pwd-toggle"
                data-pwd="<?php echo esc_attr($pwd); ?>" role="button" tabindex="0" aria-expanded="false"
                aria-label="<?php esc_attr_e('Show/Hide password', 'megatrader'); ?>"
                title="<?php esc_attr_e('Show/Hide password', 'megatrader'); ?>">
                <i class="mt-icon mt-icon-white mt-icon_visibility" aria-hidden="true"></i>
              </span>

              <span class="d-inline-flex align-items-center justify-content-center"
                data-copy="<?php echo esc_attr($pwd); ?>" role="button" tabindex="0"
                aria-label="<?php esc_attr_e('Copy password', 'megatrader'); ?>"
                title="<?php esc_attr_e('Copy password', 'megatrader'); ?>">
                <i class="mt-icon mt-icon-white mt-icon_content-copy" aria-hidden="true"></i>
              </span>
            </div>
          </div>

          <div class="d-flex align-items-center flex-nowrap min-w-0 gap-3">
            <div class="text-white flex-shrink-0">
              <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['account_server']); ?>
            </div>
            <div class="text-base text-a8a29e fw-medium text-truncate min-w-0">
              <?php echo esc_html($server); ?>
            </div>
          </div>
        </div>
      </div>

    </div>

    <div class="mt-agreement-overlay" hidden aria-hidden="true">
      <div class="mt-agreement-overlay__inner text-center">
        <div class="mt-agreement-overlay__text">
          <?php echo Label::META_ACCOUNT_OVERVIEW['agreement_overlay_description']; ?>
        </div>

        <a href="<?php echo esc_url($agreement_url ?: '#'); ?>" target="_blank" rel="noopener"
          class="mt-btn mt-btn--md mt-btn--primary mt-agreement-button">
          <?php echo Label::META_ACCOUNT_OVERVIEW['agreement_overlay_button']; ?>
        </a>
      </div>
    </div>

  </div>
</div>