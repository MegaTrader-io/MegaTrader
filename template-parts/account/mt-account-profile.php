<?php
defined('ABSPATH') || exit;

$current_user = wp_get_current_user();
$user_id      = get_current_user_id();
$nonce        = wp_create_nonce('mt_profile_nonce');

$billing = array(
  'first_name'        => get_user_meta($user_id, 'first_name', true),
  'last_name'         => get_user_meta($user_id, 'last_name', true),
  'email'             => $current_user ? $current_user->user_email : '',
  'billing_address_1' => get_user_meta($user_id, 'billing_address_1', true),
  'billing_city'      => get_user_meta($user_id, 'billing_city', true),
  'billing_state'     => get_user_meta($user_id, 'billing_state', true),
  'billing_postcode'  => get_user_meta($user_id, 'billing_postcode', true),
  'billing_country'   => get_user_meta($user_id, 'billing_country', true) ?: 'US',
  'billing_phone'     => get_user_meta($user_id, 'billing_phone', true),
);
?>
<div id="mt-profile-modal"
     class="mt-modal mt-profile-modal"
     data-module="profile-modal"
     role="dialog" aria-modal="true"
     aria-labelledby="mt-profile-title"
     hidden
     data-nonce="<?php echo esc_attr($nonce); ?>">
  <div class="mt-modal__panel mt-profile-modal__panel rounded-3 shadow-lg"
       tabindex="-1"
       style="max-width:980px;width:100%;background:#131210;outline:1px solid #404040">

    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between px-4 pt-3" style="gap:8px">
      <h2 id="mt-profile-title" class="h5 m-0 text-uppercase" style="color:#fff;line-height:28px;">My Profile</h2>
      <button type="button" class="mt-modal__close js-close-profile-modal btn btn-sm btn-outline-secondary" aria-label="Close">✕</button>
    </div>

    <!-- Body -->
    <div class="px-4 pb-4 pt-2 d-flex" style="gap:32px">
      <!-- Tabs (left) -->
      <aside class="d-flex flex-column" style="width:300px;gap:32px">
        <div class="d-flex flex-column w-100" style="gap:8px">
          <button type="button" class="btn w-100 text-start mt-tab active"
                  data-tab="pi"
                  style="background:#1E1E1E;border-radius:12px;outline:1px solid #fff;color:#FAFAFA;text-transform:uppercase;font-weight:500">
            Personal information
          </button>
          <button type="button" class="btn w-100 text-start mt-tab" data-tab="verif"
                  style="border-radius:12px;color:#FAFAFA;text-transform:uppercase;font-weight:500">
            Verification
          </button>
          <button type="button" class="btn w-100 text-start mt-tab" data-tab="pwd"
                  style="border-radius:12px;color:#FAFAFA;text-transform:uppercase;font-weight:500">
            Password
          </button>
          <button type="button" class="btn w-100 text-start mt-tab" data-tab="2fa"
                  style="border-radius:12px;color:#FAFAFA;text-transform:uppercase;font-weight:500">
            2FA (Two-factor-authentication)
          </button>
        </div>
      </aside>

      <!-- Divider -->
      <div style="width:1px;background:#404040;opacity:.9"></div>

      <!-- Content (right) -->
      <section class="flex-grow-1 d-flex flex-column" style="gap:16px">
        <!-- Header user -->
        <div class="d-flex align-items-start" style="gap:16px">
          <!-- Avatar iniciales -->
          <div class="position-relative d-inline-flex" style="width:96px;height:96px">
            <div style="width:96px;height:96px;background:#FFB34A;border-radius:64px;display:flex;align-items:center;justify-content:center">
              <div style="color:#000;font-size:40px;font-weight:300;line-height:48px;text-transform:uppercase;text-align:center">
                <?php
                $fn = strtoupper(substr((string)($billing['first_name'] ?: ''),0,1));
                $ln = strtoupper(substr((string)($billing['last_name'] ?: ''),0,1));
                echo esc_html(($fn?:'U').($ln?:'N'));
                ?>
              </div>
            </div>
            <button type="button" class="btn btn-light p-1 position-absolute"
                    style="left:68px;top:68px;border-radius:16px" disabled
                    title="Change avatar (coming soon)">
              <span class="visually-hidden">Change avatar</span>⬆
            </button>
          </div>

          <div class="flex-grow-1 d-flex flex-column" style="gap:8px">
            <div class="d-inline-flex align-items-center" style="gap:4px">
              <span class="text-secondary" style="font-weight:500;">Member since:
                <?php echo esc_html( $current_user && $current_user->user_registered ? date_i18n('d-m-Y', strtotime($current_user->user_registered)) : '—'); ?>
              </span>
            </div>
            <div class="d-inline-flex align-items-center" style="gap:4px">
              <span class="fw-medium" style="color:#F43F5E;">UNVERIFIED</span>
            </div>
          </div>
        </div>

        <!-- ============ TAB: Personal information (Billing editable) ============ -->
        <form id="mt-profile-form" data-panel="pi" novalidate class="d-flex flex-column" style="gap:16px">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label text-uppercase small text-secondary mb-1">First name</label>
              <input class="form-control" value="<?php echo esc_attr($billing['first_name']); ?>" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label text-uppercase small text-secondary mb-1">Last name</label>
              <input class="form-control" value="<?php echo esc_attr($billing['last_name']); ?>" disabled>
            </div>
            <div class="col-12">
              <label class="form-label text-uppercase small text-secondary mb-1">Email</label>
              <input class="form-control" value="<?php echo esc_attr($billing['email']); ?>" disabled>
            </div>

            <div class="col-12 mt-2">
              <div class="text-uppercase fw-bold small text-secondary">Billing</div>
            </div>

            <div class="col-12">
              <label class="form-label small">Address</label>
              <input name="billing_address_1" class="form-control" value="<?php echo esc_attr($billing['billing_address_1']); ?>" required>
              <div class="invalid-feedback">Address required.</div>
            </div>
            <div class="col-md-6">
              <label class="form-label small">City</label>
              <input name="billing_city" class="form-control" value="<?php echo esc_attr($billing['billing_city']); ?>" required>
              <div class="invalid-feedback">City required.</div>
            </div>
            <div class="col-md-3">
              <label class="form-label small">State</label>
              <input name="billing_state" class="form-control" value="<?php echo esc_attr($billing['billing_state']); ?>" required>
              <div class="invalid-feedback">State required.</div>
            </div>
            <div class="col-md-3">
              <label class="form-label small">Zip-code</label>
              <input name="billing_postcode" class="form-control" value="<?php echo esc_attr($billing['billing_postcode']); ?>" required pattern="^[0-9A-Za-z \-]{3,10}$">
              <div class="invalid-feedback">Valid ZIP required.</div>
            </div>
            <div class="col-md-6">
              <label class="form-label small">Country</label>
              <input name="billing_country" class="form-control" value="<?php echo esc_attr($billing['billing_country']); ?>" required>
              <div class="invalid-feedback">Country required.</div>
            </div>
            <div class="col-md-6">
              <label class="form-label small">Phone</label>
              <input name="billing_phone" class="form-control" value="<?php echo esc_attr($billing['billing_phone']); ?>" required pattern="^[0-9()+ \-\.]{7,20}$">
              <div class="invalid-feedback">Valid phone required.</div>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2 mt-2">
            <button type="button" class="btn btn-outline-secondary mt-modal__close js-close-profile-modal">Cancel</button>
            <button type="submit" class="btn btn-warning text-uppercase fw-medium" id="mt-profile-save">Save changes</button>
          </div>

          <input type="hidden" name="action" value="mt_save_billing_profile">
          <input type="hidden" name="nonce" value="<?php echo esc_attr($nonce); ?>">
        </form>

        <!-- ============ TAB: Verification ============ -->
        <form data-panel="verif" hidden class="d-flex flex-column" style="gap:16px">
          <div class="alert alert-warning mb-0">
            <strong>Status:</strong> Your email is <span class="text-danger">UNVERIFIED</span>.
          </div>

          <div class="row g-3">
            <div class="col-12">
              <label class="form-label small">Email</label>
              <input class="form-control" value="<?php echo esc_attr($billing['email']); ?>" disabled>
            </div>
            <div class="col-12">
              <p class="text-secondary small mb-0">
                We’ve sent a verification link to your email. Didn’t get it?
              </p>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2 mt-2">
            <button type="button" class="btn btn-outline-secondary" disabled>Change email</button>
            <button type="button" class="btn btn-warning text-uppercase fw-medium" disabled>Resend verification</button>
          </div>
        </form>

        <!-- ============ TAB: Password ============ -->
        <form data-panel="pwd" hidden novalidate class="d-flex flex-column" style="gap:16px">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label small">Current password</label>
              <input type="password" class="form-control" name="current_password" autocomplete="current-password" required>
              <div class="invalid-feedback">Current password required.</div>
            </div>
            <div class="col-md-6">
              <label class="form-label small">New password</label>
              <input type="password" class="form-control" name="new_password" autocomplete="new-password" minlength="8" required>
              <div class="invalid-feedback">Min 8 characters.</div>
            </div>
            <div class="col-md-6">
              <label class="form-label small">Confirm new password</label>
              <input type="password" class="form-control" name="confirm_password" autocomplete="new-password" minlength="8" required>
              <div class="invalid-feedback">Passwords must match.</div>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2 mt-2">
            <button type="button" class="btn btn-outline-secondary">Cancel</button>
            <button type="button" class="btn btn-warning text-uppercase fw-medium" disabled>Update password</button>
          </div>
        </form>

        <!-- ============ TAB: 2FA ============ -->
        <form data-panel="2fa" hidden class="d-flex flex-column" style="gap:16px">
          <div class="alert alert-secondary mb-0">
            <strong>Two-Factor Authentication:</strong> <span class="text-danger">Disabled</span>
          </div>

          <div class="row g-3">
            <div class="col-12">
              <label class="form-label small">Authenticator app</label>
              <div class="p-3 rounded" style="background:#1E1E1E;border:1px solid #404040;">
                <div class="small text-secondary mb-2">Scan this QR in your authenticator app (placeholder):</div>
                <div style="width:140px;height:140px;background:#2a2a2a;border-radius:8px;"></div>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label small">Enter 6-digit code</label>
              <input type="text" class="form-control" name="otp" pattern="^[0-9]{6}$" placeholder="••••••">
              <div class="invalid-feedback">Enter a valid 6-digit code.</div>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2 mt-2">
            <button type="button" class="btn btn-outline-secondary" disabled>Disable 2FA</button>
            <button type="button" class="btn btn-warning text-uppercase fw-medium" disabled>Enable 2FA</button>
          </div>
        </form>
      </section>
    </div>
  </div>
</div>
