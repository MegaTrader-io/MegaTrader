<?php if (!defined('ABSPATH'))
  exit;

$current_user = wp_get_current_user();
$user_id = get_current_user_id();
$nonce = wp_create_nonce('mt_profile_nonce');
$mt_billing_nonce = wp_create_nonce('mt_save_billing');

$billing = [
  'first_name' => get_user_meta($user_id, 'first_name', true),
  'last_name' => get_user_meta($user_id, 'last_name', true),
  'email' => $current_user ? $current_user->user_email : '',
  'billing_address_1' => get_user_meta($user_id, 'billing_address_1', true),
  'billing_city' => get_user_meta($user_id, 'billing_city', true),
  'billing_state' => get_user_meta($user_id, 'billing_state', true),
  'billing_postcode' => get_user_meta($user_id, 'billing_postcode', true),
  'billing_country' => get_user_meta($user_id, 'billing_country', true) ?: 'US',
  'billing_phone' => get_user_meta($user_id, 'billing_phone', true),
]; ?>

<div id="mt-profile-modal" class="modal modal-profile fade" tabindex="-1" aria-labelledby="mt-profile-title"
  aria-hidden="true" data-nonce="<?php echo esc_attr($nonce); ?>">
  <div class="modal-dialog modal-dialog-centered modal-fullscreen-md-down">
    <div class="modal-content" style="background:#131210; border:1px solid #404040; border-radius:16px;">

      <!-- HEADER -->
      <div class="modal-header w-100 border-0 justify-content-between align-items-center p-4 pt-3">
        <span id="mt-profile-title" class="modal-title text-white heading-sm-medium text-uppercase">My Profile</span>
        <button type="button" class="p-0 border-0 bg-transparent shadow-none mt-modal__close js-close-profile-modal"
          aria-label="Close">
          <span aria-hidden="true">
            <img src="https://subscriptions.megatrader.io/wp-content/uploads/2025/05/cancel-circle-1.png" alt="Close"
              style="width:24px;height:24px;">
          </span>
        </button>
      </div>

      <!-- BODY -->
      <div class="modal-body d-flex align-items-start gap-4 px-4 pb-4 pt-0">

        <!-- TABS (LEFT) -->
        <aside class="d-flex flex-column" style="width:300px;gap:32px">
          <div class="d-flex flex-column w-100" style="gap:8px">
            <button type="button" class="btn w-100 text-start mt-tab active" data-tab="pi"
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

        <!-- DIVIDER -->
        <div style="width:1px;background:#404040;opacity:.9"></div>

        <!-- CONTENT (RIGHT) -->
        <section class="flex-grow-1 d-flex flex-column" style="gap:16px">

          <!-- User header -->
          <div class="d-flex align-items-start" style="gap:16px">
            <div class="position-relative d-inline-flex" style="width:96px;height:96px">
              <div
                style="width:96px;height:96px;background:#FFB34A;border-radius:64px;display:flex;align-items:center;justify-content:center">
                <div
                  style="color:#000;font-size:40px;font-weight:300;line-height:48px;text-transform:uppercase;text-align:center">
                  <?php
                  $fn = strtoupper(substr((string) ($billing['first_name'] ?: ''), 0, 1));
                  $ln = strtoupper(substr((string) ($billing['last_name'] ?: ''), 0, 1));
                  echo esc_html(($fn ?: 'U') . ($ln ?: 'N'));
                  ?>
                </div>
              </div>
              <button type="button" class="btn btn-light p-1 position-absolute"
                style="left:68px;top:68px;border-radius:16px" disabled title="Change avatar (soon)">
                <span class="visually-hidden">Change avatar</span>⬆
              </button>
            </div>

            <div class="flex-grow-1 d-flex flex-column" style="gap:8px">
              <div class="d-inline-flex align-items-center" style="gap:4px">
                <span class="text-secondary" style="font-weight:500;">Member since:
                  <?php echo esc_html($current_user && $current_user->user_registered ? date_i18n('d-m-Y', strtotime($current_user->user_registered)) : '—'); ?>
                </span>
              </div>
              <div class="d-inline-flex align-items-center" style="gap:4px">
                <span class="fw-medium" style="color:#F43F5E;">UNVERIFIED</span>
              </div>
            </div>
          </div>

          <!-- Panel: Personal information (DEFAULT) -->
          <form id="mt-profile-form" data-panel="pi" class="checkout" novalidate>
            <div class="billing-details pt-3">
              <?php
              $user_id = get_current_user_id();
              $current_state = $user_id ? get_user_meta($user_id, 'billing_state', true) : '';
              ?>

              <?php
              do_action('woocommerce_before_checkout_form');
              do_action('woocommerce_checkout_before_customer_details');
              ?>
              <div id="customer_details">
                <input type="hidden" id="billing_state_current" value="<?php echo esc_attr($current_state); ?>">
                <?php do_action('woocommerce_checkout_billing'); ?>
                <?php do_action('woocommerce_checkout_shipping'); ?>
              </div>
              <?php
              do_action('woocommerce_checkout_after_customer_details');
              do_action('woocommerce_after_checkout_form');
              ?>

              <input type="hidden" id="mt_save_billing_nonce" value="<?php echo esc_attr($mt_billing_nonce); ?>">
              <button type="button" id="mt-save-billing" class="ot-btn bg-mgt-primary text-black fw-medium mt-3 w-100">
                Save changes
              </button>
            </div>
          </form>

          <!-- Panel: Verification -->
          <div data-panel="verif" hidden>
            <div
              style="width: 100%; height: 100%; position: relative; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 16px; display: inline-flex">
              <div
                style="width: 320px; height: 0px; left: 19px; top: 306px; position: absolute; transform: rotate(90deg); transform-origin: top left; outline: 2px var(--Colors-Gray-700, #404040) solid; outline-offset: -1px">
              </div>
              <div
                style="align-self: stretch; border-radius: 16px; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 32px; display: flex">
                <div
                  style="flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 16px; display: flex">
                  <div
                    style="width: 596px; padding: 16px; background: var(--Surface-Page, #1E1E1E); border-radius: 16px; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 16px; display: flex">
                    <div
                      style="align-self: stretch; justify-content: flex-start; align-items: flex-start; gap: 16px; display: inline-flex">
                      <div
                        style="flex: 1 1 0; border-radius: 16px; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 16px; display: inline-flex">
                        <div
                          style="align-self: stretch; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 16px; display: flex">
                          <div
                            style="align-self: stretch; justify-content: flex-start; align-items: center; gap: 4px; display: inline-flex">
                            <div style="width: 24px; height: 24px; position: relative">
                              <div
                                style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9">
                              </div>
                              <div
                                style="width: 16px; height: 20px; left: 4px; top: 2px; position: absolute; background: var(--Icon-Primary, #FFB34A)">
                              </div>
                            </div>
                            <div
                              style="flex: 1 1 0; color: var(--Text-Headings, white); font-size: 16px; font-family: Roboto; font-weight: 700; line-height: 24px; word-wrap: break-word">
                              100% Safe</div>
                          </div>
                          <div
                            style="align-self: stretch; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">
                            Your data is secured by AES-grade encryption.</div>
                        </div>
                      </div>
                      <div
                        style="flex: 1 1 0; border-radius: 16px; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 16px; display: inline-flex">
                        <div
                          style="align-self: stretch; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 16px; display: flex">
                          <div
                            style="align-self: stretch; justify-content: flex-start; align-items: flex-start; gap: 4px; display: inline-flex">
                            <div style="width: 24px; height: 24px; position: relative">
                              <div
                                style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9">
                              </div>
                              <div
                                style="width: 16px; height: 20px; left: 4px; top: 2px; position: absolute; background: var(--Icon-Primary, #FFB34A)">
                              </div>
                            </div>
                            <div
                              style="flex: 1 1 0; color: var(--Text-Headings, white); font-size: 16px; font-family: Roboto; font-weight: 700; line-height: 24px; word-wrap: break-word">
                              Fast Process</div>
                          </div>
                          <div
                            style="align-self: stretch; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">
                            Identity check takes only a couple of minutes.</div>
                        </div>
                      </div>
                      <div
                        style="flex: 1 1 0; border-radius: 16px; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 16px; display: inline-flex">
                        <div
                          style="align-self: stretch; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 16px; display: flex">
                          <div
                            style="align-self: stretch; justify-content: flex-start; align-items: flex-start; gap: 4px; display: inline-flex">
                            <div style="width: 24px; height: 24px; position: relative">
                              <div
                                style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9">
                              </div>
                              <div
                                style="width: 20px; height: 16px; left: 2px; top: 4px; position: absolute; background: var(--Icon-Primary, #FFB34A)">
                              </div>
                            </div>
                            <div
                              style="flex: 1 1 0; color: var(--Text-Headings, white); font-size: 16px; font-family: Roboto; font-weight: 700; line-height: 24px; word-wrap: break-word">
                              Contract Ready</div>
                          </div>
                          <div
                            style="align-self: stretch; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">
                            Skip KYC steps. Get your contract right away</div>
                        </div>
                      </div>
                    </div>
                    <div
                      style="align-self: stretch; justify-content: space-between; align-items: center; display: inline-flex">
                      <div data-icon-alignment="Default" data-size="md" data-status="Default" data-type="Dark"
                        data-variant="Filled"
                        style="padding-left: 16px; padding-right: 16px; padding-top: 12px; padding-bottom: 12px; background: var(--Surface-Dark, #292524); border-radius: 12px; outline: 1px var(--Colors-Gray-700, #404040) solid; outline-offset: -1px; justify-content: center; align-items: center; gap: 8px; display: flex">
                        <div
                          style="color: var(--Text-Action-Dark-Solid, #FAFAFA); font-size: 16px; font-family: Roboto; font-weight: 500; text-transform: uppercase; line-height: 24px; word-wrap: break-word">
                          Get verified now</div>
                      </div>
                      <img style="width: 118px; height: 33px" src="https://placehold.co/118x33" />
                      <div style="width: 92px; height: 33px; background: var(--Text-Body, #A8A29E)"></div>
                      <div
                        style="width: 38px; height: 33px; transform: rotate(180deg); transform-origin: top left; background: var(--Text-Body, #A8A29E)">
                      </div>
                    </div>
                  </div>
                  <div
                    style="width: 596px; padding-top: 16px; padding-bottom: 16px; border-radius: 16px; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 24px; display: flex">
                    <div
                      style="align-self: stretch; color: var(--Text-Headings, white); font-size: 20px; font-family: Roboto; font-weight: 300; text-transform: uppercase; line-height: 24px; word-wrap: break-word">
                      Verification process</div>
                    <div
                      style="align-self: stretch; justify-content: flex-start; align-items: flex-start; gap: 16px; display: inline-flex">
                      <div
                        style="padding: 8px; background: var(--Surface-Page, #1E1E1E); border-radius: 20px; outline: 4px var(--Colors-Gray-700, #404040) solid; justify-content: flex-start; align-items: center; display: flex">
                        <div style="width: 24px; height: 24px; position: relative; border-radius: 64px">
                          <div
                            style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9">
                          </div>
                          <div
                            style="width: 22.20px; height: 16px; left: 1px; top: 4px; position: absolute; background: var(--Primary-400, #FFB34A)">
                          </div>
                        </div>
                      </div>
                      <div
                        style="flex: 1 1 0; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 8px; display: inline-flex">
                        <div
                          style="align-self: stretch; color: var(--Text-Headings, white); font-size: 20px; font-family: Roboto; font-weight: 500; text-transform: uppercase; line-height: 24px; word-wrap: break-word">
                          Check your account information</div>
                        <div
                          style="align-self: stretch; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">
                          Please check that your account information matches with your government-issued ID to avoid any
                          inconveniences during verification. You will not be able to change this information afterward.
                        </div>
                      </div>
                    </div>
                    <div
                      style="align-self: stretch; justify-content: flex-start; align-items: flex-start; gap: 16px; display: inline-flex">
                      <div
                        style="padding: 8px; background: var(--Surface-Page, #1E1E1E); border-radius: 20px; outline: 4px var(--Colors-Gray-700, #404040) solid; justify-content: flex-start; align-items: center; display: flex">
                        <div style="width: 24px; height: 24px; position: relative; border-radius: 64px">
                          <div
                            style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9">
                          </div>
                          <div
                            style="width: 20px; height: 16px; left: 2px; top: 4px; position: absolute; background: var(--Primary-400, #FFB34A)">
                          </div>
                        </div>
                      </div>
                      <div
                        style="flex: 1 1 0; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 8px; display: inline-flex">
                        <div
                          style="align-self: stretch; color: var(--Text-Headings, white); font-size: 20px; font-family: Roboto; font-weight: 500; text-transform: uppercase; line-height: 24px; word-wrap: break-word">
                          Prepare your physical ID cards</div>
                        <div
                          style="align-self: stretch; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">
                          You will be asked to take a photo of either your ID card, driving license, or other
                          government-issued cards. Make sure you take a photo of your physical ID. Copies, screenshots,
                          or other forms will be declined.</div>
                      </div>
                    </div>
                    <div
                      style="align-self: stretch; justify-content: flex-start; align-items: flex-start; gap: 16px; display: inline-flex">
                      <div
                        style="padding: 8px; background: var(--Surface-Page, #1E1E1E); border-radius: 20px; outline: 4px var(--Colors-Gray-700, #404040) solid; justify-content: flex-start; align-items: center; display: flex">
                        <div style="width: 24px; height: 24px; position: relative; border-radius: 64px">
                          <div
                            style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9">
                          </div>
                          <div
                            style="width: 20px; height: 18px; left: 2px; top: 3px; position: absolute; background: var(--Primary-400, #FFB34A)">
                          </div>
                        </div>
                      </div>
                      <div
                        style="flex: 1 1 0; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 8px; display: inline-flex">
                        <div
                          style="align-self: stretch; color: var(--Text-Headings, white); font-size: 20px; font-family: Roboto; font-weight: 500; text-transform: uppercase; line-height: 24px; word-wrap: break-word">
                          Begin the verification process</div>
                        <div
                          style="align-self: stretch; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">
                          Click the button bellow to start the verification process. You will be asked to take a photo
                          of your ID and yourself.</div>
                      </div>
                    </div>
                    <div
                      style="align-self: stretch; justify-content: flex-start; align-items: flex-start; gap: 16px; display: inline-flex">
                      <div
                        style="padding: 8px; background: var(--Surface-Page, #1E1E1E); box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25); border-radius: 20px; outline: 4px var(--Colors-Gray-700, #404040) solid; justify-content: flex-start; align-items: center; display: flex">
                        <div style="width: 24px; height: 24px; position: relative; border-radius: 64px">
                          <div
                            style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9">
                          </div>
                          <div
                            style="width: 16px; height: 20px; left: 4px; top: 2px; position: absolute; background: var(--Primary-400, #FFB34A)">
                          </div>
                        </div>
                      </div>
                      <div
                        style="flex: 1 1 0; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 8px; display: inline-flex">
                        <div
                          style="align-self: stretch; color: var(--Text-Headings, white); font-size: 20px; font-family: Roboto; font-weight: 500; text-transform: uppercase; line-height: 24px; word-wrap: break-word">
                          Wait for confirmation</div>
                        <div
                          style="align-self: stretch; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">
                          You will be notified that your account has been verified. Usually it takes under a minute. All
                          accounts passwords waiting for verification will be released immediately.</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>


          <!-- Panel: Password -->
          <form id="mt-profile-password-form" data-panel="pwd" novalidate hidden>
            <div
              style="width: 100%; height: 100%; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 16px; display: inline-flex">
              <div
                style="align-self: stretch; border-radius: 16px; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 32px; display: flex">
                <div
                  style="align-self: stretch; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 16px; display: flex">
                  <div
                    style="align-self: stretch; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 4px; display: flex">
                    <div
                      style="align-self: stretch; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 700; line-height: 24px; word-wrap: break-word">
                      Current password</div>
                    <div data-show-helpertext="false" data-showbutton="false" data-showicon="true"
                      data-showiconhelp="false" data-status="filled"
                      style="align-self: stretch; height: 48px; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 8px; display: flex">
                      <div
                        style="align-self: stretch; padding-left: 16px; padding-right: 16px; padding-top: 12px; padding-bottom: 12px; background: rgba(30, 30, 30, 0.70); border-radius: 12px; outline: 1px var(--Colors-Gray-700, #404040) solid; outline-offset: -1px; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                        <div
                          style="flex: 1 1 0; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">
                          ••••••••••</div>
                        <div style="width: 24px; height: 24px; position: relative">
                          <div
                            style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9">
                          </div>
                          <div
                            style="width: 22px; height: 19.80px; left: 1px; top: 2.80px; position: absolute; background: var(--Icon-Body, #A8A29E)">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div
                    style="align-self: stretch; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 4px; display: flex">
                    <div
                      style="align-self: stretch; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 700; line-height: 24px; word-wrap: break-word">
                      New password</div>
                    <div data-show-helpertext="false" data-showbutton="false" data-showicon="true"
                      data-showiconhelp="false" data-status="filled"
                      style="align-self: stretch; height: 48px; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 8px; display: flex">
                      <div
                        style="align-self: stretch; padding-left: 16px; padding-right: 16px; padding-top: 12px; padding-bottom: 12px; background: rgba(30, 30, 30, 0.70); border-radius: 12px; outline: 1px var(--Colors-Gray-700, #404040) solid; outline-offset: -1px; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                        <div
                          style="flex: 1 1 0; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">
                          ••••••••••</div>
                        <div style="width: 24px; height: 24px; position: relative">
                          <div
                            style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9">
                          </div>
                          <div
                            style="width: 22px; height: 19.80px; left: 1px; top: 2.80px; position: absolute; background: var(--Icon-Body, #A8A29E)">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div
                    style="align-self: stretch; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 4px; display: flex">
                    <div
                      style="align-self: stretch; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 700; line-height: 24px; word-wrap: break-word">
                      Confirm password</div>
                    <div data-show-helpertext="false" data-showbutton="false" data-showicon="true"
                      data-showiconhelp="false" data-status="filled"
                      style="align-self: stretch; height: 48px; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 8px; display: flex">
                      <div
                        style="align-self: stretch; padding-left: 16px; padding-right: 16px; padding-top: 12px; padding-bottom: 12px; background: rgba(30, 30, 30, 0.70); border-radius: 12px; outline: 1px var(--Colors-Gray-700, #404040) solid; outline-offset: -1px; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                        <div
                          style="flex: 1 1 0; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">
                          ••••••••••</div>
                        <div style="width: 24px; height: 24px; position: relative">
                          <div
                            style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9">
                          </div>
                          <div
                            style="width: 22px; height: 19.80px; left: 1px; top: 2.80px; position: absolute; background: var(--Icon-Body, #A8A29E)">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div data-icon-alignment="Default" data-size="md" data-status="Default" data-type="Primary"
                    data-variant="Filled"
                    style="padding-left: 16px; padding-right: 16px; padding-top: 12px; padding-bottom: 12px; background: var(--Surface-Primary, #FFB34A); border-radius: 12px; outline: 2px var(--Surface-Primary, #FFB34A) solid; outline-offset: -2px; justify-content: center; align-items: center; gap: 8px; display: inline-flex">
                    <div
                      style="color: var(--Text-Action-Primary-Solid, #020617); font-size: 16px; font-family: Roboto; font-weight: 500; text-transform: uppercase; line-height: 24px; word-wrap: break-word">
                      Save changes</div>
                  </div>
                </div>
              </div>
            </div>
          </form>


          <!-- Panel: 2FA -->
          <form id="mt-profile-2fa-form" data-panel="2fa" novalidate hidden>
            <div
              style="width: 100%; height: 100%; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 16px; display: inline-flex">
              <div
                style="align-self: stretch; border-radius: 16px; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 32px; display: flex">
                <div
                  style="align-self: stretch; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 16px; display: flex">
                  <div
                    style="align-self: stretch; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                    <div data-shape="Pill" data-size="md" data-type="light"
                      style="padding-left: 12px; padding-right: 12px; padding-top: 2px; padding-bottom: 2px; background: var(--Colors-Gray-200, #E5E5E5); border-radius: 16px; justify-content: center; align-items: center; gap: 10px; display: flex">
                      <div
                        style="color: var(--Surface-Body, #131210); font-size: 14px; font-family: Roboto; font-weight: 700; text-transform: uppercase; line-height: 24px; word-wrap: break-word">
                        Step 1</div>
                    </div>
                    <div
                      style="color: var(--Text-Headings, white); font-size: 20px; font-family: Roboto; font-weight: 500; line-height: 32px; word-wrap: break-word">
                      Scan QR Code</div>
                  </div>
                  <div
                    style="align-self: stretch; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">
                    Scan the QR code below or manually enter the secret key into your authentication app.</div>
                  <div
                    style="align-self: stretch; padding: 8px; background: var(--Surface-Page, #1E1E1E); border-radius: 8px; flex-direction: column; justify-content: flex-start; align-items: center; gap: 16px; display: flex">
                    <div
                      style="align-self: stretch; justify-content: flex-start; align-items: center; gap: 16px; display: inline-flex">
                      <div
                        style="width: 120px; height: 120px; position: relative; background: white; overflow: hidden; border-radius: 8px">
                        <!-- Placeholder QR pixels de Figma (tal cual) -->
                        <div
                          style="width: 3.12px; height: 7.81px; left: 69.38px; top: 45.94px; position: absolute; background: black">
                        </div>
                        <div
                          style="width: 3.12px; height: 7.81px; left: 75.62px; top: 45.94px; position: absolute; background: black">
                        </div>
                        <div
                          style="width: 3.12px; height: 3.12px; left: 69.38px; top: 56.88px; position: absolute; background: black">
                        </div>
                        <div
                          style="width: 3.12px; height: 3.12px; left: 75.62px; top: 56.88px; position: absolute; background: black">
                        </div>
                        <!-- … resto del “pixel art” … -->
                      </div>
                      <div
                        style="flex: 1 1 0; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 16px; display: inline-flex">
                        <div
                          style="align-self: stretch; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 4px; display: flex">
                          <div
                            style="align-self: stretch; color: var(--Text-Headings, white); font-size: 20px; font-family: Roboto; font-weight: 500; line-height: 32px; word-wrap: break-word">
                            Can’t scan QA code?</div>
                          <div
                            style="align-self: stretch; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">
                            Enter this secret instead:</div>
                        </div>
                        <div data-icon-alignment="Right" data-size="sm" data-status="Default" data-type="Light"
                          data-variant="Filled"
                          style="padding-top: 4px; padding-bottom: 4px; padding-left: 12px; padding-right: 8px; background: var(--Surface-Light, white); border-radius: 4px; outline: 1px var(--Surface-Light, white) solid; outline-offset: -1px; justify-content: center; align-items: center; gap: 8px; display: inline-flex">
                          <div
                            style="color: var(--Text-Action-Light-Solid, #292524); font-size: 14px; font-family: Roboto; font-weight: 500; text-transform: uppercase; line-height: 20px; word-wrap: break-word">
                            PHDNUETGDHNYRASBDF</div>
                          <div style="width: 20px; height: 20px; position: relative">
                            <div
                              style="width: 20px; height: 20px; left: 0px; top: 0px; position: absolute; background: #D9D9D9">
                            </div>
                            <div
                              style="width: 14.17px; height: 16.67px; left: 2.50px; top: 1.67px; position: absolute; background: var(--Black, black)">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div
                    style="align-self: stretch; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                    <div data-shape="Pill" data-size="md" data-type="light"
                      style="padding-left: 12px; padding-right: 12px; padding-top: 2px; padding-bottom: 2px; background: var(--Colors-Gray-200, #E5E5E5); border-radius: 16px; justify-content: center; align-items: center; gap: 10px; display: flex">
                      <div
                        style="color: var(--Surface-Body, #131210); font-size: 14px; font-family: Roboto; font-weight: 700; text-transform: uppercase; line-height: 24px; word-wrap: break-word">
                        Step 2</div>
                    </div>
                    <div
                      style="color: var(--Text-Headings, white); font-size: 20px; font-family: Roboto; font-weight: 500; line-height: 32px; word-wrap: break-word">
                      Get verification Code</div>
                  </div>
                  <div
                    style="align-self: stretch; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">
                    Enter the 6-digit code you see in your authentication app.</div>
                  <div data-show-helpertext="false" data-showbutton="false" data-showicon="false"
                    data-showiconhelp="false" data-status="normal"
                    style="align-self: stretch; height: 48px; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 8px; display: flex">
                    <div
                      style="align-self: stretch; padding-left: 16px; padding-right: 16px; padding-top: 12px; padding-bottom: 12px; background: rgba(30, 30, 30, 0.70); border-radius: 12px; outline: 1px var(--Colors-Gray-700, #404040) solid; outline-offset: -1px; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                      <div
                        style="flex: 1 1 0; color: var(--Colors-Gray-700, #404040); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">
                        XXX XXX</div>
                    </div>
                  </div>
                  <div
                    style="align-self: stretch; justify-content: flex-start; align-items: flex-start; gap: 16px; display: inline-flex">
                    <div data-icon-alignment="Default" data-size="md" data-status="Default" data-type="Primary"
                      data-variant="Filled"
                      style="flex: 1 1 0; height: 48px; padding-left: 16px; padding-right: 16px; padding-top: 12px; padding-bottom: 12px; background: var(--Surface-Primary, #FFB34A); border-radius: 12px; outline: 2px var(--Surface-Primary, #FFB34A) solid; outline-offset: -2px; justify-content: center; align-items: center; gap: 8px; display: flex">
                      <div
                        style="color: var(--Text-Action-Primary-Solid, #020617); font-size: 16px; font-family: Roboto; font-weight: 500; text-transform: uppercase; line-height: 24px; word-wrap: break-word">
                        setup 2FA</div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </form>


        </section>
      </div>
    </div>
  </div>
</div>