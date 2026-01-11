<?php
$payouts_items = [
        [
                'title' => 'Sign up',
                'description' => 'Go Funded, or pick and pass a Challenge.'
        ],
        [
                'title' => 'Trade 7 days',
                'description' => 'Scale your trading with your simulated funds.'
        ],
        [
                'title' => 'Get paid',
                'description' => 'Request your payout. We’ll pay in ~1 hour.'
        ],
];

?>

<section id="how-it-works" class="payout-bs text-white">
    <div class="landing-bs-container">
        <div class="payout-bs__row">
            <div class="payout-bs__content">
                <header class="payout-bs__header text-start">
                    <h2 class="payout-bs__title">
                        Earn your first payout in 7 days
                    </h2>
                </header>

                <ol class="payout-bs__list list-unstyled">
                    <?php foreach ($payouts_items as $key => $item): ?>
                        <li class="payout-bs__item">
                            <div class="payout-bs__item-title">
                                <?= ($key + 1) ?>. <?= $item['title'] ?>
                            </div>
                            <div class="payout-bs__wrapper_content">
                                <p>
                                    <?= $item['description'] ?>
                                </p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ol>

                <div class="d-flex">
                    <a data-menu="pricing" href="<?= home_url('#pricing') ?>"
                       class="btn-get-funded-now btn mega-btn-md mega-btn-primary-md payout-bs__btn">
                        Get Funded Now
                    </a>
                </div>
            </div>
            <div class="payout-bs__media">
                <div class="mt-card payout-bs__media-wrapper">
                    <div style="padding:56.25% 0 0 0;position:relative;">
                        <iframe src="https://player.vimeo.com/video/1153328572?badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479"
                                frameborder="0"
                                allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media; web-share"
                                referrerpolicy="strict-origin-when-cross-origin"
                                style="position:absolute;top:0;left:0;width:100%;height:100%;"
                                title="Welcome MegaTrader"></iframe>
                    </div>
                    <script src="https://player.vimeo.com/api/player.js"></script>
                </div>
            </div>
        </div>
    </div>
</section>
