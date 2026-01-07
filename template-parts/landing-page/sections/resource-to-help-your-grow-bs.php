<?php
$resources_items = [
        [
                'image' => get_template_directory_uri() . '/assets/img/landing-page/upcoming-events/london-trader-3.png',
                'title' => 'Learn how to qualify faster for payouts.',
                'button_text' => 'View all LIVE trader perks',
        ],
        [
                'image' => get_template_directory_uri() . '/assets/img/landing-page/upcoming-events/london-trader-1.png',
                'title' => 'Learn the fix to why most traders blow their account',
                'button_text' => 'Get FREE eBook',
        ],
        [
                'image' => get_template_directory_uri() . '/assets/img/landing-page/upcoming-events/london-trader-2.png',
                'title' => 'Join 30,000+ active traders on Discord',
                'button_text' => 'Join Discord',
        ]
];

?>

<section class="resources-bs">
    <header class="section-header text-center">
        <h2 class="section-header__title">
            ALL THE <SPAN>RESOURCES</SPAN> TO HELP YOU GROW
        </h2>
        <p class="section-header__subtitle">
            Access powerful tools, insights, and resources designed to support smarter decisions and consistent growth
            at every stage.
        </p>
    </header>

    <div class="landing-bs-container">
        <div class="resources-bs__intro">
            <div class="resources-bs__intro-content">
                <div class="trading-status">
                    <div class="trading-status__badge">
                        <div class="trading-status__label">
                            <div class="trading-status__dot"></div>
                            <div class="trading-status__label-text">
                                LIVE DATA
                            </div>
                        </div>
                        <div class="trading-status__description text-truncate">
                            Trading Stats Update In Real Time.
                        </div>
                    </div>
                </div>

                <div class="resources-bs__intro-description">
                    <div class="resources-bs__intro-title">
                        TRACK YOUR PERFORMANCE LIVE
                    </div>
                    <div class="resources-bs__intro-paragraph">
                        Monitor your trading stats, consistency, and drawdown in one place. Gain insights to improve
                        your results and stay payout-ready.
                    </div>
                </div>
                <a data-menu="pricing" href="<?= home_url('#pricing') ?>"
                   class="btn-get-funded-now btn mega-btn-md mega-btn-primary-md">
                    GET FUNDED NOW
                </a>
            </div>
            <div class="resources-bs__intro-image">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/track-performance.jpg'); ?>"/>
            </div>
        </div>

        <div class="resources-bs__cards">
            <?php foreach ($resources_items as $item) : ?>
                <div class="resources-bs__card">
                    <img class="resources-bs__card-image" src="<?= $item['image'] ?>">
                    <div class="resources-bs__card-body">
                        <div class="resources-bs__card-title">
                            <?= $item['title'] ?>
                        </div>
                        <a href="javascript:void(0);" class="mega-btn-md mega-btn-default-md w-100">
                            <?= $item['button_text'] ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>