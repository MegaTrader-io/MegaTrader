<?php
/*
Template Name: Account Overview
Template Post Type: page
*/
defined('ABSPATH') || exit;

if (!is_user_logged_in()) {
  wp_safe_redirect(wc_get_page_permalink('myaccount'));
  exit;
}

get_header('shop'); 
?>

<div class="container">
  <div class="mt-page">
    <div class="mt-page__sidebar">
      <?php render_sidebar() ?>
    </div>
    <div class="mt-page__main">
      <div class="no-order-wrapper d-flex flex-column gap-32">
        <div class="mt-card mt-card_row">
          <div class="d-flex flex-column flex-grow-1 flex-shrink-1 justify-content-center">
            <div class="fw-medium text-size-20 text-uppercase text-white">
              <?php esc_html_e('No active membership', 'woocommerce'); ?>
            </div>
            <div class="fw-medium text-14px text-a8a29e">
              <?php esc_html_e('You don’t have any active plans or memberships at the moment.', 'woocommerce'); ?>
            </div>
          </div>
          <div class="get-started">
            <?php
            $shop_url = home_url('/');
            ?>
            <a id="get_started_btn" href="<?php echo esc_url($shop_url); ?>"
              class="btn w-100 mega-btn-md mega-btn-primary-md">
              <?php esc_html_e('Get Started', 'woocommerce'); ?>
            </a>

          </div>
        </div>

        <div class="mt-card">
          <div class="text-white text-size-20 fw-medium text-uppercase align-self-start pb-32">
            <?php esc_html_e('Get started in 3 steps', 'woocommerce'); ?>
          </div>
          <div class="d-flex align-items-center flex-column w-100 mt-2">
            <div class="no-order stepper position-relative w-100">
              <div class="stepper-line"></div>
              <div class="stepper-numbers-wrapper">
                <div class="stepper-numbers-circle">
                  <div class="stepper-numbers-text">
                    <?php esc_html_e('1', 'woocommerce'); ?>
                  </div>
                </div>
                <div class="stepper-numbers-circle">
                  <div class="stepper-numbers-text">
                    <?php esc_html_e('2', 'woocommerce'); ?>
                  </div>

                </div>
                <div class="stepper-numbers-circle">
                  <div class="stepper-numbers-text">
                    <?php esc_html_e('3', 'woocommerce'); ?>
                  </div>
                </div>
              </div>

            </div>
            <div class="d-flex gap-32 justify-content-between w-100">
              <div class="d-flex flex-column gap-1 flex-grow-1 flex-shrink-1 w-max-224px">
                <div class="text-primary text-center text-base fw-medium">
                  <?php esc_html_e('Choose your plan', 'woocommerce'); ?>
                </div>
                <div class="text-center fw-medium text-14px text-a8a29e">
                  <?php esc_html_e('Select from our range of trading challenges', 'woocommerce'); ?>
                </div>
              </div>
              <div class="d-flex flex-column gap-1 flex-grow-1 flex-shrink-1 w-max-224px">
                <div class="text-primary text-center text-base fw-medium">
                  <?php esc_html_e('Complete purchase', 'woocommerce'); ?>
                </div>
                <div class="text-center fw-medium text-14px text-a8a29e">
                  <?php esc_html_e('Secure payment and instant activation', 'woocommerce'); ?>
                </div>
              </div>
              <div class="d-flex flex-column gap-1 flex-grow-1 flex-shrink-1 w-max-224px">
                <div class="text-primary text-center text-base fw-medium">
                  <?php esc_html_e('Start trading', 'woocommerce'); ?>
                </div>
                <div class="text-center fw-medium text-14px text-a8a29e">
                  <?php esc_html_e('Access your platform and begin your challenge', 'woocommerce'); ?>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="d-flex flex-column flex-md-row gap-3 pb-35 align-items-start">
          <div class="mt-card">
            <div class="text-white fw-medium text-size-20 text-uppercase">
              <?php esc_html_e('Why join us?', 'woocommerce'); ?>
            </div>
            <div class="d-flex flex-column">
              <div class="py-3 d-flex gap-2 align-items-center border-bottom-dark">
                <div class="mt-icon mt-icon_checkmark">
                </div>
                <div class="text-a8a29e text-base fw-medium">
                  <?php esc_html_e('Professional trading environment', 'woocommerce'); ?>
                </div>
              </div>
              <div class="py-3 d-flex gap-2 align-items-center border-bottom-dark">
              <div class="mt-icon mt-icon_checkmark">
                </div>
                <div class="text-a8a29e text-base fw-medium">
                  <?php esc_html_e('Real-time performance tracking', 'woocommerce'); ?>
                </div>
              </div>
              <div class="py-3 d-flex gap-2 align-items-center border-bottom-dark">
               <div class="mt-icon mt-icon_checkmark">
                </div>
                <div class="text-a8a29e text-base fw-medium">
                  <?php esc_html_e('Expert support and guidance', 'woocommerce'); ?>
                </div>
              </div>
              <div class="py-3 d-flex gap-2 align-items-center">
               <div class="mt-icon mt-icon_checkmark">
                </div>
                <div class="text-a8a29e text-base fw-medium">
                  <?php esc_html_e('Flexible challenge options', 'woocommerce'); ?>
                </div>
              </div>

            </div>
          </div>
          <div class="mt-card">
            <div class="text-white fw-medium text-size-20 text-uppercase">
              <?php esc_html_e('Frequently Asked Question', 'woocommerce'); ?>
            </div>
            <div class="d-flex flex-column">
              <div class="py-3 border-bottom-dark d-flex flex-column gap-1">
                <div class="text-white text-base fw-medium">
                  <?php esc_html_e('How do I get started?', 'woocommerce'); ?>
                </div>
                <div class="text-14px text-a8a29e fw-medium">
                  <?php esc_html_e('Simply choose a plan that fits your trading experience and goals, complete the purchase, and
                                you’ll get instant access to your trading challenge.', 'woocommerce'); ?>
                </div>
              </div>
              <div class="py-3 border-bottom-dark d-flex flex-column gap-1">
                <div class="text-white text-base fw-medium">
                  <?php esc_html_e('What’s included in each plan?', 'woocommerce'); ?>
                </div>
                <div class="text-14px text-a8a29e fw-medium">
                  <?php esc_html_e('Each plan includes access to our trading platform, real-time performance tracking, support resources and specific challenge objectives based on your chosen tier.', 'woocommerce'); ?>
                </div>
              </div>
              <div class="py-3 d-flex flex-column gap-1">
                <div class="text-white text-base fw-medium">
                  <?php esc_html_e('Can I upgrade my plan later?', 'woocommerce'); ?>
                </div>
                <div class="text-14px text-a8a29e fw-medium">
                  <?php esc_html_e('Yes, you can upgrade to a higher tier plan at any time. Contact our support team for assistance with plan changes.', 'woocommerce'); ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php get_footer('shop'); 