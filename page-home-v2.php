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


?>

    <div class="overflow-hidden pt-30 pb-30">
        <div class="container">
            <div class="top-menu">
                <span class="active">1</span>
                <a class="continue-to-pay-link" href="/checkout/?add-to-cart=67">2</a>
                <span>3</span>
            </div>
            <div class="row home-page-row">
                <div class="col-lg-7">
                    <div class="variation-list">
                        <div class="account-items">
                            <h6 class="box-title">Select your TRADING platform</h6>
                            <div class="pricing-buttons platform" id="platform">
                                <button value="megax" id="megax" type="button" class="active">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon_megax.svg" alt="Icon" width="57" height="57">
                                    <span class="content">
                                        <span class="title">MegaX</span>
                                        <span class="text">A cutting-edge trading platform designed for serious traders, offering advanced tools and seamless performance.</span>
                                    </span>
                                </button>
                                <button value="ninjatrader" id="ninjatrader" type="button" class="">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon_ninjatrader.svg" alt="Icon" width="57" height="57">
                                    <span class="content">
                                        <span class="title">NinjaTrader</span>
                                        <span class="text">NinjaTrader empowers traders with intuitive tools, robust analytics, and seamless execution to elevate their trading experience.</span>
                                    </span>
                                </button>
                                <button value="tradovate" id="tradovate" type="button" class="">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon_tradovate.svg" alt="Icon" width="57" height="57">
                                    <span class="content">
                                        <span class="title">Tradovate</span>
                                        <span class="text">Tradovate simplifies trading with a modern, cloud-based platform offering innovative tools and commission-free futures trading.</span>
                                    </span>
                                </button>
                                <button value="quantower" id="quantower" type="button" class="">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon_quantower.svg" alt="Icon" width="57" height="57">
                                    <span class="content">
                                        <span class="title">Quantower</span>
                                        <span class="text">Quantower offers a versatile, feature-rich trading platform with advanced tools and seamless multi-asset support.</span>
                                    </span>
                                </button>
                            </div>
                        </div>
                        <div class="account-items">
                            <h6 class="box-title">Select Your Funding Plan</h6>
                            <div class="pricing-buttons trading-capital" id="trading-capital">
                                <button value="50k" id="50k" type="button" class="">$50K BASIC</button>
                                <button value="100k" id="100k" type="button" class="active">$100K PREMIUM</button>
                                <button value="150k" id="150k" type="button" class="">$150K UNLIMITED</button>
                            </div>
                        </div>
                        
                    </div>
                    <div class="single-checkout-widget info-box">
                        <p class="text">Profit Target <span class="profitTarget">$6,000</span></p>
                        <p class="text">Maximum Loss Limit <span class="mLossLimit">$3,000</span></p>
                        <p class="text">Maximum Position Size <span class="Scontracts">10 Contracts</span></p>
                        <h4 class="challege-price">
                            Total
                            <span class="original-price"> $235</span><span class="duration"> Monthly</span>
                        </h4>
                    </div>
                    <div class="pt-10 text-end">
                        <a href="#" class="ot-btn start-trading-btn">Continue</a>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="home-right-side">
                        <h3 class="sec-title">Unlock the Power of MegaTrader</h3>
                        <h3 class="mb-20">+ + +</h3>
                        <p class="sec-text">MegaTrader is transforming how traders engage with futures markets. Designed for all experience levels, our platform combines cutting-edge technology with user-friendly tools to ensure seamless, high-performance trading.</p>
                        <div class="profit-cards">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/cards.png" alt="Cards">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="brand-sec">
        <div class="container">
            <div class="brand-wrap">
                <div class="brand-logo">
                    <img src="<?php echo get_template_directory_uri(). '/assets/img/brand_1.svg';?>" alt="Brand 1">
                </div>
                <div class="brand-logo">
                    <img src="<?php echo get_template_directory_uri(). '/assets/img/brand_2.svg';?>" alt="Brand 1">
                </div>
                <div class="brand-logo">
                    <img src="<?php echo get_template_directory_uri(). '/assets/img/brand_3.svg';?>" alt="Brand 1">
                </div>
                <div class="brand-logo">
                    <img src="<?php echo get_template_directory_uri(). '/assets/img/brand_4.svg';?>" alt="Brand 1">
                </div>
            </div>
        </div>
    </div>


<?php
get_footer();