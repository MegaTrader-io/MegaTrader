<!-- Hero Section -->
<section id="hero-section" class="hero-zero-plan text-white">
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
                    <h1 class="hero-bs__zero-title">
                        START TRADING TODAY WITH OUR ZERO PLAN FOR JUST $20
                    </h1>
                </header>

                <ul class="hero-bs__benefits list-unstyled">
                    <?php
                    $features = [
                            'Start for just $20 and only pay after you pass',
                            'Just a 2% profit target makes passing easier',
                            'Fast payouts once funded with quick approval',
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
                        <div class="hero-bs__media-card-circle"></div>
                        <img class="hero-bs__media-icon-zero-plan"
                             src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/zero-icon.png'); ?>"
                             alt="control left">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>