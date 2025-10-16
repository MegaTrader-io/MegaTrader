<?php

if (!isset($content) || !is_string($content)) $content = '<!-- empty content -->';
if (!isset($classes) || !is_string($classes)) $classes = '';

?>

<div class="auth <?= $classes; ?>">
    <div class="auth-form">
        <div class="auth-form__container">
            <div class="auth-form__content">
                <?php echo $content; ?>
            </div>
        </div>
    </div>
    <div class="auth-preview">
        <div class="auth-preview__container">
            <div class="auth-preview__content">
                <div class="swiper">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="slider-content">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/auth/sd1.png"
                                     class="slider-content__image-web"
                                     style="object-fit: fill"
                                     alt="Logo MegaTraderX"/>

                                <div class="slider-content__info">
                                    <h3 class="slider-content__title mb-0">
                                        Power up your trading with full control.
                                    </h3>
                                    <p class="slider-content__subtitle ">
                                        Log in to manage your accounts, track performance, and unlock the full
                                        potential of your MegaTrader journey.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="slider-content">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/auth/sd2.png"
                                     class="slider-content__image-web"
                                     style="object-fit: fill"
                                     alt="Logo MegaTraderX"/>

                                <div class="slider-content__info">
                                    <h3 class="slider-content__title mb-0">
                                        Track, Grow, and Earn with Confidence
                                    </h3>
                                    <p class="slider-content__subtitle ">
                                        Monitor your referrals, commissions, and performance using powerful
                                        tools designed to maximize your affiliate success.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="slider-content">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/auth/sd3.png"
                                     class="slider-content__image-web"
                                     style="object-fit: fill"
                                     alt="Logo MegaTraderX"/>

                                <div class="slider-content__info">
                                    <h3 class="slider-content__title mb-0">
                                        Stay Fully in Control of Every Payout
                                    </h3>
                                    <p class="slider-content__subtitle">
                                        View your earnings, check payment status, and stay in control of your
                                        withdrawals with clear, real-time updates.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </div>
    </div>
</div>