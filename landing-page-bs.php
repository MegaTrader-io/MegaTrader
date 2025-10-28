<?php
/**
 * Template Name: Landing Page Bootstrap
 */

get_header('landing-page-bs');
?>

    <main class="landing-bs">
        <!-- Hero Section -->
        <section class="hero-bs text-white">
            <div class="container hero-bs__container">
                <div class="hero-bs__row">
                    <div class="hero-bs__content">
                        <header class="hero-bs__header">
                            <h1 class="hero-bs__title">
                                Supercharge your futures trading with $750k in funding
                            </h1>
                        </header>

                        <ul class="hero-bs__benefits list-unstyled">
                            <li class="hero-bs__benefit d-flex align-items-start">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <mask id="mask0_17269_3092" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0"
                                          y="0" width="24" height="24">
                                        <rect width="24" height="24" fill="#D9D9D9"/>
                                    </mask>
                                    <g mask="url(#mask0_17269_3092)">
                                        <path d="M8 22L9 15H4L13 2H15L14 10H20L10 22H8Z" fill="#FFB34A"/>
                                    </g>
                                </svg>
                                <span class="hero-bs__text">Take a Challenge or get Instant Funding</span>
                            </li>
                            <li class="hero-bs__benefit d-flex align-items-start">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <mask id="mask0_17269_3092" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0"
                                          y="0" width="24" height="24">
                                        <rect width="24" height="24" fill="#D9D9D9"/>
                                    </mask>
                                    <g mask="url(#mask0_17269_3092)">
                                        <path d="M8 22L9 15H4L13 2H15L14 10H20L10 22H8Z" fill="#FFB34A"/>
                                    </g>
                                </svg>
                                <span class="hero-bs__text">Lightning fast 1 hour payouts</span>
                            </li>
                            <li class="hero-bs__benefit d-flex align-items-start">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <mask id="mask0_17269_3092" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0"
                                          y="0" width="24" height="24">
                                        <rect width="24" height="24" fill="#D9D9D9"/>
                                    </mask>
                                    <g mask="url(#mask0_17269_3092)">
                                        <path d="M8 22L9 15H4L13 2H15L14 10H20L10 22H8Z" fill="#FFB34A"/>
                                    </g>
                                </svg>
                                <span class="hero-bs__text">Journal to track, analyse and improve your trades</span>
                            </li>
                        </ul>

                        <div class="d-flex">
                            <a href="#get-funded" class="btn mega-btn-md mega-btn-primary-md hero-bs__btn">
                                <img src="<?= get_template_directory_uri() . '/assets/img/landing-page/flash-icon.svg'; ?>"
                                     width="44" height="43">
                                Get Funded Now

                            </a>
                        </div>
                    </div>

                    <div class="hero-bs__media">
                        <div class="hero-bs__media-wrapper">
                            <img src="<?= get_template_directory_uri() . '/assets/img/landing-page/pork-bills.png' ?>"
                                 alt="pork bills" width="532" height="522">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <?php
        $verified_payouts_base_url = get_template_directory_uri() . '/assets/img/landing-page/verified-payouts';
        $certifies = [
                ['img' => $verified_payouts_base_url . '/Certificate-passed-1.png', 'title' => 'Mega Certified Trader'],
                ['img' => $verified_payouts_base_url . '/Certificate-passed-2.png', 'title' => 'Mega Certified Trader'],
                ['img' => $verified_payouts_base_url . '/Certificate-widthdrawal-3.png', 'title' => 'Mega Certified Trader'],
                ['img' => $verified_payouts_base_url . '/Certificate-widthdrawal-4.png', 'title' => 'Mega Certified Trader'],
        ]

        ?>

        <section class="verified-bs text-white">
            <div class="container verified-bs__container">
                <header class="verified-bs__header text-center">
                    <h2 class="verified-bs__title">
                        Verified <span>ACHIEVEMENTS</span> from Real Traders
                    </h2>
                    <p class="verified-bs__subtitle">
                        Every certificate represents a trader moving forward — from passing evaluations to receiving
                        payouts. This is just the beginning of what’s possible.
                    </p>
                </header>
            </div>

            <div id="verified-bs-carousel" class="splide">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php foreach ($certifies as $key => $certify) : ?>
                            <li class="splide__slide">
                                <img src="<?php echo esc_url($certify['img']); ?>"
                                     alt="Mega Certified Trader Badge"
                                     class="verified-bs__image"
                                     loading="lazy">
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </section>

        <?php
        $payouts_items = [
                [
                        'title' => 'Sign up',
                        'description' => 'Go Lightning Funded, or pick and pass a Challenge'
                ],
                [
                        'title' => 'Trade 5 days',
                        'description' => 'Scale your futures trading with your<br> simulated funds'
                ],
                [
                        'title' => 'Get paid',
                        'description' => 'Request your payout. We’ll pay in ~4 hours.'
                ],
        ];

        ?>
        <section class="payout-bs text-white">
            <div class="container payout-bs__container">
                <div class="payout-bs__row">
                    <div class="payout-bs__content">
                        <header class="payout-bs__header text-start">
                            <h2 class="payout-bs__title">
                                Earn your first payout in 5 days
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
                            <a href="#get-funded" class="btn mega-btn-md mega-btn-primary-md payout-bs__btn">
                                Get Funded Now
                            </a>
                        </div>
                    </div>
                    <div class="payout-bs__media">
                        <div class="mt-card payout-bs__media-wrapper">
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <?php
        $platform_base_url = get_template_directory_uri() . '/assets/img/landing-page/platforms';
        ?>

        <section class="platforms-bs">
            <div class="container">
                <div class="platforms-bs__list">
                    <div class="platforms-bs__item platforms-bs__item--available">
                        <img src="<?= $platform_base_url . '/megatraderx.svg'; ?>" alt="MegaTraderX Sponsor Logo">
                    </div>
                    <div class="platforms-bs__item">
                        <img src="<?= $platform_base_url . '/tradovate.svg'; ?>" alt="MegaTraderX Sponsor Logo">
                        <div class="mt-badge mt-badge-rounded-sm mt-badge-light">COMING SOON</div>
                    </div>
                    <div class="platforms-bs__item">
                        <img src="<?= $platform_base_url . '/ninjatrader.svg'; ?>" alt="MegaTraderX Sponsor Logo">
                        <div class="mt-badge mt-badge-rounded-sm mt-badge-light">COMING SOON</div>
                    </div>
                    <div class="platforms-bs__item">
                        <img src="<?= $platform_base_url . '/quantower.svg'; ?>" alt="MegaTraderX Sponsor Logo">
                        <div class="mt-badge mt-badge-rounded-sm mt-badge-light">COMING SOON</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="journal-bs text-white">
            <div class="container journal-bs__container">
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

                    <div class="badge-group">
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
            </div>
            <div class="journal-bs__content">
                <div id="verified-bs-id" class="verified-bs__glide slider glide">
                    <div class="slider__track glide__track" data-glide-el="track">
                        <ul class="slider__slides glide__slides">

                            <li class="slider__frame glide__slide">
                                <div class="mt-card journal-bs__card"></div>
                            </li>

                            <li class="slider__frame glide__slide">
                                <div class="mt-card journal-bs__card"></div>
                            </li>

                            <li class="slider__frame glide__slide">
                                <div class="mt-card journal-bs__card"></div>
                            </li>

                        </ul>
                    </div>

                    <div class="slider__bullets glide__bullets" data-glide-el="controls[nav]">
                        <button class="slider__bullet glide__bullet" data-glide-dir="=0"></button>
                        <button class="slider__bullet glide__bullet" data-glide-dir="=1"></button>
                        <button class="slider__bullet glide__bullet" data-glide-dir="=2"></button>
                    </div>
                </div>
            </div>
        </section>

        <?php require 'template-parts/landing-page/sections/price_table.php'; ?>

    </main>

<?php get_footer('landing-page-bs'); ?>