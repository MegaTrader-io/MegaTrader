<section class="journal-bs text-white">
    <div class="landing-bs-container">
        <header class="journal-bs__header">
            <div class="badge-duo">
                <div class="badge-duo__primary">
                    <div class="badge-duo__text">TRADEIFY EXCLUSIVE</div>
                </div>
                <div class="badge-duo__secondary">
                    <div class="badge-duo__text">Included in all plans</div>
                </div>
            </div>

            <h2 class="journal-bs__title">
                Journal to find your winning strategies
            </h2>

            <div class="badge-group journal-bs__badge-group">
                <div class="badge-group__item is-active">
                    <div class="badge-group__text">P&amp;L Calendar</div>
                </div>
                <div class="badge-group__item">
                    <div class="badge-group__text">Trade tagging</div>
                </div>
                <div class="badge-group__item">
                    <div class="badge-group__text">Personalized reports</div>
                </div>
            </div>
        </header>

        <div class="verified-bs__wrapper">
            <div id="verified-bs-id" class="verified-bs__glide slider glide"
                 style="--verified-bs-slide-width: 0px; --verified-bs-slide-left: 0px;">
                <div class="slider__track glide__track" data-glide-el="track">
                    <ul class="slider__slides glide__slides">
                      <?php for ($i = 1; $i <= 3; $i++) : ?>
                          <li class="slider__frame glide__slide">
                              <div class="mt-card journal-bs__card">
                              </div>
                          </li>
                      <?php endfor; ?>
                    </ul>
                </div>

                <div data-glide-el="controls" class="glide__arrows">
                    <button class="glide__arrow glide__arrow--prev" data-glide-dir="<">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/arrow-left.svg'); ?>"
                             alt="control left">
                    </button>
                    <button class="glide__arrow glide__arrow--next" data-glide-dir=">">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/arrow-right.svg'); ?>"
                             alt="control right">
                    </button>
                </div>

                <div class="slider__bullets glide__bullets" data-glide-el="controls[nav]">
                    <button class="slider__bullet glide__bullet" data-glide-dir="=0"></button>
                    <button class="slider__bullet glide__bullet" data-glide-dir="=1"></button>
                    <button class="slider__bullet glide__bullet" data-glide-dir="=2"></button>
                </div>
            </div>
        </div>
    </div>
</section>
