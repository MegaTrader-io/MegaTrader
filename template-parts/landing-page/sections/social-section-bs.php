<?php

$social_list = [
        [
                'slug' => 'discord',
                'link' => 'https://discord.com/invite/megatrader'
        ],
        [
                'slug' => 'instagram',
                'link' => 'https://www.instagram.com/megatrader.io/'
        ],
        [
                'slug' => 'facebook',
                'link' => '#'
        ],
        [
                'slug' => 'x',
                'link' => 'https://x.com/MegaTrader_io'
        ],
];

?>
<section class="social-section-bs">
    <div class="social-section-bs__container">
        <header class="section-header text-center">
            <h2 class="section-header__title">
                GET THE LATEST <span>DISCOUNTS</span> ON SOCIALS
            </h2>
            <p class="section-header__subtitle">
                Follow our social channels to stay updated on exclusive discounts, promotions, and limited-time offers.
            </p>
        </header>

        <div class="social-section-bs__grid">
            <?php foreach ($social_list as $social_list_item): ?>
                <?php
                $svg_url = esc_url(get_template_directory_uri() . '/assets/img/landing-page/social-media/' . $social_list_item['slug'] . '.svg');
                $link = $social_list_item['link'];
                $text = ucfirst($social_list_item['slug']);
                if ($social_list_item['slug'] == 'x') {
                    $text = 'X (Twitter)';
                }

                $target_attr = $social_list_item['slug'] === 'facebook' ? '' : ' target="_blank" rel="noopener noreferrer"';

                ?>

                <a href="<?= esc_url($link) ?>"<?= $target_attr ?> class="social-section-bs__card">
                    <div class="social-section-bs__icon-container">
                        <div class="social-section-bs__icon-bg">
                            <img src="<?= esc_url($svg_url) ?>" alt="<?= esc_attr($text) ?> icon"/>
                        </div>
                    </div>
                    <span class="social-section-bs__card-label"><?= esc_html($text) ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="social-section-bs__banner">
            <div class="social-section-bs__banner-content">
                <div class="social-section-bs__rating">
                    <?php get_template_part('template-parts/landing-page/sections/trustpilot-dummy-bs', null, array('showTrustpilot' => false)); ?>
                </div>

                <h3 class="social-section-bs__banner-title">
                    10x your trading with up to<br> $750k simulated funds
                </h3>
            </div>

            <div class="social-section-bs__features">

                <span class="social-section-bs__feature-item social-section-bs__feature-item--order-1">Free built-in journal</span>
                <img src="<?php echo get_template_directory_uri() . '/assets/img/landing-page/quick-flash.svg'; ?>"
                     alt="flash"
                     width="24" height="24" class="social-section-bs__feature-item--order-2">

                <span class="social-section-bs__feature-item social-section-bs__feature-item--order-3">Get Instant Funding or take a Challenge.</span>
                <img src="<?php echo get_template_directory_uri() . '/assets/img/landing-page/quick-flash.svg'; ?>"
                     alt="flash"
                     width="24" height="24" class="social-section-bs__feature-item--order-4">

                <span class="social-section-bs__feature-item social-section-bs__feature-item--order-5">Automated payouts</span>
                <img src="<?php echo get_template_directory_uri() . '/assets/img/landing-page/quick-flash.svg'; ?>"
                     alt="flash"
                     width="24" height="24"
                     class="social-section-bs__feature-item--order-6 social-section-bs__feature-item--last-img">
            </div>

            <a data-menu="pricing" href="<?= home_url('#pricing') ?>"
               class="btn-get-funded-now btn mega-btn-md mega-btn-primary-md">
                GET FUNDED NOW
            </a>
        </div>

    </div>
</section>