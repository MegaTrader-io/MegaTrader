<?php
/**
 * Template Name: Home Page
 * 
 * The template for displaying front page
 * The Home template file
 *
 *
 * @package megatrader
 */

get_header();

$fflag = isset($_GET['v2']);

if($fflag): ?>

    <div class="main-container pt-32 pb-32">
        <div class="container">
            <?php include get_stylesheet_directory() . '/woocommerce/checkout/choose-subscription.php'; ?>
        </div>
    </div>

<?php else:

    $products_data = get_products_with_attributes();
    $attributes = $products_data['attributes'] ?? [];

    $account_sizes = [];
    $account_types = [];
    $platforms = [];

    foreach ($attributes as $attr) {
        switch ($attr['taxonomy']) {
            case 'pa_account-size':
                $account_sizes[] = $attr;
                break;
            case 'pa_account-types':
                $account_types[] = $attr;
                break;
            case 'pa_platform':
                $platforms[] = $attr;
                break;
        }
    }
    
    function render_account_types($account_types) {
        if (empty($account_types)) return;

        foreach ($account_types as $index => $type) {
            $slug = esc_attr($type['slug']);
            $name = esc_html($type['name']);
            $description = esc_html($type['description']);
            $thumbnail = esc_url($type['thumbnail_url']);
            $is_active = $index === 0 ? ' active' : '';
            ?>
            <div data-value="<?= $slug ?>" id="<?= $slug ?>" class="button <?= $slug . $is_active ?>">
                <img src="<?= $thumbnail ?>" alt="Icon">
                <span class="content">
                    <span class="title"><?= $name ?></span>
                    <span class="text"><?= $description ?></span>
                </span>
            </div>
            <?php
        }
    }
    
    function render_platforms($platforms) {
        if (empty($platforms)) return;

        foreach ($platforms as $index => $platform) {
            $slug = esc_attr($platform['slug']);
            $name = esc_html($platform['name']);
            $description = esc_html($platform['description']);
            $thumbnail = esc_url($platform['thumbnail_url']);

            $comingSoon = '';
            if (!empty($platform['attribute_meta']) && is_array($platform['attribute_meta'])) {
                foreach ($platform['attribute_meta'] as $meta) {
                    $normalized = strtolower(str_replace(' ', '', trim($meta)));
                    if ($normalized === 'comingsoon') {
                        $comingSoon = ' coming-soon';
                        break;
                    }
                }
            }

            $is_active = $index === 0 ? ' active' : '';

            ?>
            <div data-value="<?= $slug ?>" id="<?= $slug ?>" class="button <?= $slug . $is_active . $comingSoon?>">
                <img src="<?= $thumbnail ?>" alt="Icon" width="57" height="57">
                <span class="content">
                    <?php if ($comingSoon): ?> <span class="mbadge">Coming Soon</span> <?php endif; ?>
                    <span class="title"> <?= $name ?> </span>
                    <span class="text"><?= $description ?></span>
                </span>
            </div>
            <?php
        }
    }

    function render_account_sizes($account_sizes) {
        if (empty($account_sizes)) return;

        foreach ($account_sizes as $index => $size) {
            $slug = esc_attr($size['slug']); // e.g. 25k
            $name = esc_html($size['name']); // e.g. $25.000
            $description = esc_html($size['description']);
            $thumbnail = esc_url($size['thumbnail_url']);
            $price = ''; // this price is updated in the frontend based on selection [type][platform][size]
            $is_active = $index === 0 ? ' active' : '';

            ?>
            <div data-value="<?= $slug ?>" id="<?= $slug ?>" class="button<?= $is_active ?>">
                <img src="<?= $thumbnail ?>" alt="Icon" width="57" height="57">
                <span class="content">
                    <span class="title">
                        <span class="label"><?= $name ?></span>
                        <span class="mbadge d-none">On sale</span> 
                        <b><span class="a-price"><?= $price ?></span> / Monthly</b>
                    </span>
                    <span class="text"><?= $description ?></span>
                </span>
            </div>
            <?php
        }
    }
?>

    <div class="main-container pt-32 pb-32">
        <div class="container">
            <div class="top-menu">
                <span class="active">1</span>
                <a class="continue-to-pay-link" href="/checkout/?add-to-cart=67">2</a>
                <span>3</span>
            </div>
            <div class="account-type-wrap">
                <div class="pricing-buttons account-type" id="account-type">
                    <?php render_account_types($account_types); ?>
                </div>
            </div>
            <div class="row home-page-row">
                <div class="col-lg-7">
                    <div class="variation-list">
                        <div class="account-items">
                            <h6 class="fw-light text-size-20 text-uppercase text-white">Select your TRADING platformx</h6>
                            <div class="pricing-buttons platform" id="platform">
                                <?php render_platforms($platforms); ?>
                            </div>
                        </div>
                        <div class="account-items">
                            <h6 class="fw-light text-size-20 text-uppercase text-white">Select Your Funding Plan</h6>
                            <div class="pricing-buttons trading-capital" id="trading-capital">
                                <?php render_account_sizes($account_sizes); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="variation-list right-box">
                        <h4 class="box-title">
                            <span class="platformIcon">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/diamond.svg" alt="Icon" width="30" height="30">
                            </span>
                            <span class="capitalSize"></span>
                            <span class="accountType"></span>
                        </h4>
                        <div class="info-boxs">
                            <div class="info-boxs__block">
                                <h5 class="title"><span class="platformName"></span> Platform</h5>
                                <div class="checklist">
                                    <ul class="cat-meta-list">
                                    <!-- <li>No platform cost for the basic version.</li>
                                        <li>Ultra-customizable workspaces</li>
                                        <li>Access to dozens of third-party add-ons</li>
                                        <li>Compatible with NinjaTrader versions 7 & 8</li> -->
                                    </ul>
                                </div>
                            </div>
                            <div class="info-boxs__block">
                                <h5 class="title">Objectives and Rules</h5>
                                <div class="checklist">
                                    <ul class="metaInfo">
                                        <li class="profit_target">Profit Target: <span>$3,000</span></li>
                                        <li class="max_contracts">Max Contracts: <span>5 Minis (50 Micros)</span></li>
                                        <li class="daily_loss_limit">Daily Loss Limit: <span>None</span></li>
                                        <li class="daily_loss_limit_soft_breach">Daily Loss Limit (Soft Breach): <span>None</span></li>
                                        <li class="trailing_max_drawdown">Trailing Max Drawdown: <span>None</span></li>
                                        <li class="drawdown_mode">Drawdown Mode: <span>None</span></li>
                                        <li class="min_trading_days">Min Trading Days to Pass: <span>None</span></li>
                                        <li class="min_trading_days_to_payout">Min Trading Days to Payout: <span>None</span></li>
                                        <li class="reset_fee">Reset Fee: <span>None</span></li>
                                        <li class="activation_fee">Activation Fee: <span>None</span></li>
                                        <li class="consistency">Consistency: <span>None</span></li>
                                        <li class="max_accounts">Max Accounts: <span>None</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <a href="#" class="ot-btn start-trading-btn">Continue</a>
                        </div>
                        <p class="info-text">
                            <svg width="16" height="21" viewBox="0 0 16 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2 21C1.45 21 0.979167 20.8042 0.5875 20.4125C0.195833 20.0208 0 19.55 0 19V9C0 8.45 0.195833 7.97917 0.5875 7.5875C0.979167 7.19583 1.45 7 2 7H3V5C3 3.61667 3.4875 2.4375 4.4625 1.4625C5.4375 0.4875 6.61667 0 8 0C9.38333 0 10.5625 0.4875 11.5375 1.4625C12.5125 2.4375 13 3.61667 13 5V7H14C14.55 7 15.0208 7.19583 15.4125 7.5875C15.8042 7.97917 16 8.45 16 9V19C16 19.55 15.8042 20.0208 15.4125 20.4125C15.0208 20.8042 14.55 21 14 21H2ZM8 16C8.55 16 9.02083 15.8042 9.4125 15.4125C9.80417 15.0208 10 14.55 10 14C10 13.45 9.80417 12.9792 9.4125 12.5875C9.02083 12.1958 8.55 12 8 12C7.45 12 6.97917 12.1958 6.5875 12.5875C6.19583 12.9792 6 13.45 6 14C6 14.55 6.19583 15.0208 6.5875 15.4125C6.97917 15.8042 7.45 16 8 16ZM5 7H11V5C11 4.16667 10.7083 3.45833 10.125 2.875C9.54167 2.29167 8.83333 2 8 2C7.16667 2 6.45833 2.29167 5.875 2.875C5.29167 3.45833 5 4.16667 5 5V7Z" fill="var(--body-color)"/>
                            </svg>
                            <span>All payments are secured and encrypted.</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const preloader = document.querySelector('.preloader');
  const pricingTexts = document.querySelectorAll('.trading-capital .button .title b');
  const originalTexts = Array.from(pricingTexts).map(el => el.innerHTML);

  // Function to update pricing text based on selected account type
  function updatePricingText() {
    const fundedPlanActive = document.querySelector('.pricing-buttons.account-type .button.funded-plan.active');
    
    pricingTexts.forEach((text, index) => {
      if (fundedPlanActive) {
        // Replace "Monthly" with "One Time" for Funded Plan
        text.innerHTML = originalTexts[index].replace('/ Monthly', '/ One Time');
      } else {
        // Restore original text for other plans
        text.innerHTML = originalTexts[index];
      }
    });
  }

  // Función reutilizable para agregar lógica a cualquier grupo de botones
  function setupPreloaderOnClick(buttonSelector) {
    const buttons = document.querySelectorAll(buttonSelector);

    buttons.forEach(button => {
      button.addEventListener('click', function () {
        if (this.classList.contains('active')) return;

        // Mostrar preloader
        if (preloader) preloader.style.display = 'block';

        // Quitar clase active a todos y ponerla en el clicado
        buttons.forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');

        // Update pricing text when account type changes
        if (buttonSelector === '.pricing-buttons.account-type .button') {
          updatePricingText();
        }

        // Ocultar preloader luego de 500ms
        setTimeout(() => {
          if (preloader) preloader.style.display = 'none';
        }, 500);
      });
    });
  }

  setupPreloaderOnClick('.pricing-buttons.platform .button');
  setupPreloaderOnClick('.pricing-buttons.account-type .button');
  setupPreloaderOnClick('.pricing-buttons.trading-capital .button');

  // Run once on page load to set initial state
  updatePricingText();
});
</script>

<?php endif ?>

<?php
get_footer();