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
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/auth/sd11.png"
                                     class="slider-content__image-web"
                                     style="object-fit: fill;min-height: 685px"
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
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/auth/sd12.png"
                                     class="slider-content__image-web"
                                     style="object-fit: fill;min-height: 685px"
                                     alt="Logo MegaTraderX"/>

                                <div class="slider-content__info">
                                    <h3 class="slider-content__title mb-0">
                                        Execute every trade with precision and speed
                                    </h3>
                                    <p class="slider-content__subtitle ">
                                        Access advanced order tools, real-time data, and a seamless interface built for
                                        serious futures traders.
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