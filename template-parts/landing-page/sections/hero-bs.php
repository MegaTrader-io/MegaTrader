<!-- Hero Section -->
<section id="hero-section" class="hero-bs text-white">
    <div class="landing-bs-container">
        <div class="hero-bs__row">
            <div class="hero-bs__content">
                <div>
                    <div class="promo-badge">
                        <div class="promo-badge__tag">
                            <span class="promo-badge__tag-text">TRADE BIG</span>
                        </div>
                        <span class="promo-badge__text">Reach Your Next Level.</span>
                    </div>
                </div>

                <header class="hero-bs__header">
                    <h1 class="hero-bs__title">
                        Supercharge your trading with up to $750K in funding
                    </h1>
                </header>

                <ul class="hero-bs__benefits list-unstyled">
                    <?php
                    $features = [
                            'Start a challenge and get instant funding',
                            'Lightning fast payouts in just a few hours',
                            'Journal to track and improve your trading',
                    ]
                    ?>
                    <?php foreach ($features as $key => $feature): ?>
                        <li class="hero-bs__benefit d-flex">
                            <img src="<?= get_template_directory_uri() . '/assets/img/landing-page/flash.svg'; ?>">
                            <span class="hero-bs__text"><?= $feature ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="hero-bs__get-funded-now d-flex">
                    <a data-menu="pricing" href="<?= home_url('#pricing') ?>"
                       class="btn-get-funded-now mega-btn-md mega-btn-primary-md hero-bs__btn">
                        <img src="<?= get_template_directory_uri() . '/assets/img/landing-page/flash-icon.svg'; ?>"
                             width="44" height="43">
                        Get funded now
                    </a>
                </div>

                <div>
                    <?php get_template_part('template-parts/landing-page/sections/trustpilot-dummy-bs'); ?>
                </div>
            </div>

            <div class="hero-bs__media">
                <div class="hero-bs__media-wrapper">
                    <div class="hero-bs__media-card">
                        <?php
                        $skews_images = [
                                get_template_directory_uri() . '/assets/img/landing-page/skew-01-11.png',
                                get_template_directory_uri() . '/assets/img/landing-page/skew-02-22.png'
                        ];
                        ?>

                        <div id="hero-bs-carousel" class="hero-bs__glide slider glide"
                             style="--hero-bs-slide-width: 0px; --hero-bs-slide-left: 0px;">
                            <div class="slider__track glide__track" data-glide-el="track">
                                <ul class="slider__slides glide__slides">
                                    <?php foreach ($skews_images as $key => $skew_image) : ?>
                                        <li class="slider__frame glide__slide">
                                            <img src="<?php echo esc_url($skew_image); ?>"
                                                 alt="control left">
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                            <div data-glide-el="controls" class="glide__arrows control-arrows-bs">
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const heroBsCarouselRoot = document.getElementById('hero-bs-carousel');
        if (heroBsCarouselRoot) {
            const heroGlideInstance = new Glide(heroBsCarouselRoot, {
                type: 'carousel', focusAt: 'center', gap: 16, perView: 1, autoplay: 3000,
            });

            heroGlideInstance.mount();
        }
    });
</script>