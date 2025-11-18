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
                                Supercharge your futures trading with UP to $750k
                            </h1>
                        </header>

                        <ul class="hero-bs__benefits list-unstyled">
                            <?php
                            $features = [
                                    'Start a challenge or get instant funding',
                                    'Lightning fast payouts in just one hour',
                                    'Journal to track and improve your trades',
                            ]
                            ?>
                            <?php foreach ($features as $key => $feature): ?>
                                <li class="hero-bs__benefit d-flex align-items-start">
                                    <img src="<?= get_template_directory_uri() . '/assets/img/landing-page/flash.svg'; ?>">
                                    <span class="hero-bs__text"><?= $feature ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <div class="d-flex">
                            <a href="#get-funded" class="btn mega-btn-md mega-btn-primary-md hero-bs__btn">
                                <img src="<?= get_template_directory_uri() . '/assets/img/landing-page/flash-icon.svg'; ?>"
                                     width="44" height="43">
                                Get funded now
                            </a>
                        </div>
                    </div>

                    <div class="hero-bs__media">
                        <div class="hero-bs__media-wrapper">
                            <div class="mt-card hero-bs__media-card">
                            </div>
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
                        'description' => 'Scale your futures trading with your simulated funds'
                ],
                [
                        'title' => 'Get paid',
                        'description' => 'Request your payout. We’ll pay in ~1 hour.'
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

        <?php get_template_part('template-parts/landing-page/sections/pricing_table', null, ['classes' => 'container']); ?>

        <section id="competition" class="competition container">
            <div class="competition__wrapper">
                <div class="competition__content">
                    <h2 class="competition__title">
                        Join the World’s Biggest Futures Trading Competition
                    </h2>

                    <div class="competition__rewards">
                        <div class="competition__reward-card">
                            <div class="competition__reward-header">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <mask id="mask0_17091_79275" style="mask-type:alpha" maskUnits="userSpaceOnUse"
                                          x="0" y="0" width="24" height="24">
                                        <rect width="24" height="24" fill="#D9D9D9"/>
                                    </mask>
                                    <g mask="url(#mask0_17091_79275)">
                                        <path d="M6 20C4.9 20 3.95833 19.6083 3.175 18.825C2.39167 18.0417 2 17.1 2 16V8C2 6.9 2.39167 5.95833 3.175 5.175C3.95833 4.39167 4.9 4 6 4H18C19.1 4 20.0417 4.39167 20.825 5.175C21.6083 5.95833 22 6.9 22 8V16C22 17.1 21.6083 18.0417 20.825 18.825C20.0417 19.6083 19.1 20 18 20H6ZM6 8H18C18.3667 8 18.7167 8.04167 19.05 8.125C19.3833 8.20833 19.7 8.34167 20 8.525V8C20 7.45 19.8042 6.97917 19.4125 6.5875C19.0208 6.19583 18.55 6 18 6H6C5.45 6 4.97917 6.19583 4.5875 6.5875C4.19583 6.97917 4 7.45 4 8V8.525C4.3 8.34167 4.61667 8.20833 4.95 8.125C5.28333 8.04167 5.63333 8 6 8ZM4.15 11.25L15.275 13.95C15.425 13.9833 15.575 13.9833 15.725 13.95C15.875 13.9167 16.0167 13.85 16.15 13.75L19.625 10.85C19.4417 10.6 19.2083 10.3958 18.925 10.2375C18.6417 10.0792 18.3333 10 18 10H6C5.56667 10 5.1875 10.1125 4.8625 10.3375C4.5375 10.5625 4.3 10.8667 4.15 11.25Z"
                                              fill="#FFB34A"/>
                                    </g>
                                </svg>

                                <span class="competition__reward-amount">$1,000,000</span>
                            </div>
                            <p class="competition__reward-label">Reward Pool</p>
                        </div>

                        <div class="competition__reward-card">
                            <div class="competition__reward-header">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <mask id="mask0_17091_79275" style="mask-type:alpha" maskUnits="userSpaceOnUse"
                                          x="0" y="0" width="24" height="24">
                                        <rect width="24" height="24" fill="#D9D9D9"/>
                                    </mask>
                                    <g mask="url(#mask0_17091_79275)">
                                        <path d="M6 20C4.9 20 3.95833 19.6083 3.175 18.825C2.39167 18.0417 2 17.1 2 16V8C2 6.9 2.39167 5.95833 3.175 5.175C3.95833 4.39167 4.9 4 6 4H18C19.1 4 20.0417 4.39167 20.825 5.175C21.6083 5.95833 22 6.9 22 8V16C22 17.1 21.6083 18.0417 20.825 18.825C20.0417 19.6083 19.1 20 18 20H6ZM6 8H18C18.3667 8 18.7167 8.04167 19.05 8.125C19.3833 8.20833 19.7 8.34167 20 8.525V8C20 7.45 19.8042 6.97917 19.4125 6.5875C19.0208 6.19583 18.55 6 18 6H6C5.45 6 4.97917 6.19583 4.5875 6.5875C4.19583 6.97917 4 7.45 4 8V8.525C4.3 8.34167 4.61667 8.20833 4.95 8.125C5.28333 8.04167 5.63333 8 6 8ZM4.15 11.25L15.275 13.95C15.425 13.9833 15.575 13.9833 15.725 13.95C15.875 13.9167 16.0167 13.85 16.15 13.75L19.625 10.85C19.4417 10.6 19.2083 10.3958 18.925 10.2375C18.6417 10.0792 18.3333 10 18 10H6C5.56667 10 5.1875 10.1125 4.8625 10.3375C4.5375 10.5625 4.3 10.8667 4.15 11.25Z"
                                              fill="#FFB34A"/>
                                    </g>
                                </svg>

                                <span class="competition__reward-amount">$200,000</span>
                            </div>
                            <p class="competition__reward-label">Grand Reward</p>
                        </div>
                    </div>

                    <p class="competition__entry-note">
                        FREE entry with a Tradeify account. $35 otherwise.
                    </p>

                    <div class="competition__perks">
                        <div class="competition__perk">
                            <img class="payouts-and-comparison__checked"
                                 src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/checked-circle.svg'); ?>"
                                 alt="checked circle">

                            <span class="competition__perk-text">
          Top 400 traders earn Cash Rewards
        </span>
                        </div>
                        <div class="competition__perk">
                            <img class="payouts-and-comparison__checked"
                                 src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/checked-circle.svg'); ?>"
                                 alt="checked circle">

                            <span class="competition__perk-text">
          Top 200 also get a Live Account
        </span>
                        </div>
                    </div>

                    <div class="competition__cta">
                        <a href="#get-funded" class="btn mega-btn-md mega-btn-primary-md">
                            Get Funded Now
                        </a>
                    </div>
                </div>

                <div class="competition__image">
                    <div class="competition__image-placeholder"></div>
                </div>
            </div>

        </section>

        <section class="payouts-and-comparison">
            <div class="payouts-and-comparison__container container">
                <header class="payouts-and-comparison__header text-center">
                    <h2 class="payouts-and-comparison__title">
                        Over $70 million verified payouts
                    </h2>
                    <p class="payouts-and-comparison__subtitle">
                        Every certificate represents a trader reaching their next level. Explore the success stories and
                        see what’s possible with MegaTrader.
                    </p>
                </header>

                <div class="payouts-and-comparison__cards">
                    <div class="payouts-and-comparison__card">
                        <div class="payouts-and-comparison__badge">
                            <div class="mt-badge mt-badge-rounded mt-badge-primary d-inline">POPULAR</div>
                        </div>

                        <img class="payouts-and-comparison__image"
                             src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/window-popular.png'); ?>"
                             alt="flash" width="24" height="24">

                        <div class="payouts-and-comparison__content">
                            <svg width="420" height="28" viewBox="0 0 420 28" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_17091_79575)">
                                    <path d="M14.7886 11.4754C14.1562 11.1416 13.4404 10.9747 12.6415 10.9747C11.8426 10.9747 11.1185 11.15 10.4694 11.4921C9.95341 11.7675 9.51234 12.1348 9.15448 12.5938C8.82159 12.1264 8.4138 11.7509 7.90614 11.4754C7.29862 11.1416 6.63284 10.9747 5.89216 10.9747C5.10986 10.9747 4.41079 11.1416 3.78663 11.4754C3.45374 11.6591 3.16246 11.876 2.90447 12.1348V11.2H0V21.9243H2.89614V15.6233C2.89614 15.1976 2.98769 14.8304 3.17078 14.5383C3.35387 14.2462 3.59521 14.0209 3.90314 13.8623C4.20274 13.7038 4.55227 13.6286 4.9351 13.6286C5.50933 13.6286 5.99202 13.8039 6.37485 14.1461C6.75767 14.4883 6.94908 14.9807 6.94908 15.5982V21.9243H9.87019V15.6233C9.87019 15.1976 9.96174 14.8304 10.1448 14.5383C10.3279 14.2462 10.5693 14.0209 10.8772 13.8623C11.1768 13.7038 11.5346 13.6286 11.9258 13.6286C12.4834 13.6286 12.9577 13.8039 13.3406 14.1461C13.7234 14.4883 13.9148 14.9807 13.9148 15.5982V21.9243H16.8609V15.1559C16.8609 14.2796 16.6695 13.5368 16.295 12.9109C15.9205 12.285 15.4128 11.8093 14.7803 11.4754H14.7886ZM51.5314 11.2V12.2516C51.2318 11.9345 50.8989 11.6507 50.4911 11.4421C49.8836 11.1333 49.2095 10.9747 48.4522 10.9747C47.4618 10.9747 46.5797 11.2167 45.8057 11.7091C45.0317 12.2015 44.4242 12.8608 43.9831 13.7038C43.542 14.5467 43.3173 15.4981 43.3173 16.5664C43.3173 17.6346 43.542 18.561 43.9831 19.4039C44.4242 20.2468 45.0317 20.9145 45.8057 21.3985C46.5797 21.8909 47.4535 22.133 48.4272 22.133C49.1928 22.133 49.8836 21.9744 50.4994 21.6656C50.9072 21.457 51.2318 21.1816 51.5314 20.8728V21.916H54.4525V11.1917H51.5314V11.2ZM50.9572 18.6361C50.4578 19.1869 49.8004 19.454 48.9848 19.454C48.4688 19.454 48.0028 19.3288 47.5866 19.0784C47.1705 18.8281 46.8543 18.4859 46.6213 18.0602C46.3882 17.6346 46.2801 17.1255 46.2801 16.5497C46.2801 15.9738 46.3966 15.4898 46.6213 15.0641C46.846 14.6385 47.1705 14.2963 47.57 14.0459C47.9778 13.7956 48.4438 13.6704 48.9765 13.6704C49.5091 13.6704 49.9918 13.7956 50.3912 14.0376C50.7907 14.2796 51.107 14.6301 51.34 15.0725C51.573 15.5148 51.6895 16.0155 51.6895 16.583C51.6895 17.4093 51.4398 18.102 50.9405 18.6444L50.9572 18.6361ZM26.8309 11.6424C26.032 11.1833 25.1332 10.958 24.1345 10.958C23.0693 10.958 22.1122 11.2 21.255 11.6924C20.3978 12.1848 19.7237 12.8441 19.2327 13.6871C18.7417 14.53 18.492 15.4814 18.492 16.5497C18.492 17.6179 18.7417 18.5944 19.2411 19.4373C19.7404 20.2802 20.4311 20.9479 21.3133 21.4319C22.1871 21.916 23.1941 22.1664 24.3093 22.1664C25.1831 22.1664 25.9821 22.0078 26.7228 21.699C27.4634 21.3902 28.1042 20.9228 28.6452 20.2969L26.9142 18.5693C26.5896 18.9532 26.2068 19.237 25.7657 19.4123C25.3246 19.5875 24.8253 19.6793 24.2843 19.6793C23.6768 19.6793 23.1442 19.5541 22.6948 19.3038C22.2371 19.0534 21.8875 18.6862 21.6462 18.2021C21.5546 18.0102 21.4881 17.8015 21.4298 17.5845L29.2278 17.5678C29.286 17.3342 29.3276 17.1172 29.3359 16.9169C29.3526 16.7166 29.3609 16.5246 29.3609 16.3327C29.3609 15.2978 29.1362 14.3714 28.6951 13.5619C28.254 12.7523 27.6299 12.1097 26.8393 11.6507L26.8309 11.6424ZM21.4298 15.4146C21.4797 15.2227 21.538 15.0224 21.6212 14.8555C21.8459 14.3881 22.1788 14.0292 22.6032 13.7789C23.0277 13.5285 23.5353 13.4033 24.1096 13.4033C24.6588 13.4033 25.1166 13.5118 25.4827 13.7371C25.8489 13.9625 26.1402 14.288 26.3483 14.7136C26.4481 14.9222 26.523 15.1559 26.573 15.4063L21.4298 15.423V15.4146ZM38.5653 12.1097C38.2907 11.8427 37.9828 11.6173 37.6166 11.4337C36.9924 11.1333 36.2933 10.9747 35.5111 10.9747C34.554 10.9747 33.6885 11.2084 32.9228 11.6757C32.1572 12.1431 31.558 12.7774 31.1169 13.5702C30.6841 14.3714 30.4594 15.2728 30.4594 16.2743C30.4594 17.2757 30.6758 18.1938 31.1169 19.0033C31.5497 19.8212 32.1572 20.4555 32.9228 20.9228C33.6885 21.3902 34.554 21.6239 35.5111 21.6239C36.2933 21.6239 36.9924 21.4653 37.5999 21.1565C37.9578 20.9729 38.274 20.7476 38.5404 20.4805V21.4152C38.5404 22.1997 38.274 22.8257 37.7414 23.293C37.2088 23.7604 36.5014 23.9941 35.6192 23.9941C34.8952 23.9941 34.2794 23.8605 33.7717 23.6018C33.264 23.3431 32.798 22.9675 32.3902 22.4835L30.5343 24.3279C31.067 25.0707 31.766 25.6382 32.6232 26.0471C33.4887 26.4561 34.4957 26.6564 35.6442 26.6564C36.7927 26.6564 37.783 26.4394 38.6569 26.0054C39.5307 25.5714 40.2131 24.9538 40.7041 24.1526C41.1952 23.3515 41.4448 22.4251 41.4448 21.3819V11.1917H38.5653V12.1014V12.1097ZM38.3739 17.693C38.1575 18.102 37.8579 18.4108 37.4668 18.6361C37.0756 18.8614 36.6096 18.9699 36.0603 18.9699C35.561 18.9699 35.1033 18.8531 34.6955 18.6278C34.2877 18.4024 33.9798 18.0769 33.7551 17.6763C33.5304 17.2674 33.4222 16.8084 33.4222 16.2909C33.4222 15.7735 33.5387 15.3312 33.7634 14.9222C33.9881 14.5133 34.3043 14.1962 34.6955 13.9708C35.0866 13.7455 35.5443 13.6286 36.077 13.6286C36.6096 13.6286 37.0507 13.7455 37.4501 13.9708C37.8496 14.1962 38.1575 14.5216 38.3656 14.9222C38.582 15.3312 38.6902 15.7902 38.6902 16.3076C38.6902 16.8251 38.582 17.2841 38.3656 17.693H38.3739Z"
                                          fill="white"/>
                                    <path d="M64.3312 0.26709H131.217C134.271 0.26709 136.751 2.75412 136.751 5.81702V22.4501C136.751 25.513 134.271 28 131.217 28H64.3312C61.2769 28 58.7969 25.513 58.7969 22.4501V5.81702C58.7969 2.75412 61.2769 0.26709 64.3312 0.26709Z"
                                          fill="#0062FF"/>
                                    <path d="M163.482 0H146.613C143.561 0 141.087 2.48105 141.087 5.54158V22.4584C141.087 25.519 143.561 28 146.613 28H163.482C166.534 28 169.008 25.519 169.008 22.4584V5.54158C169.008 2.48105 166.534 0 163.482 0Z"
                                          fill="#0062FF"/>
                                    <path d="M153.338 9.35558L151.149 6.52637H145.889L150.716 12.7356L153.338 9.35558ZM155.893 19.3955L158.164 22.3165H163.358L158.506 16.0405L155.893 19.3955ZM158.098 6.53471L145.889 22.3165H151.082L163.358 6.53471H158.098Z"
                                          fill="white"/>
                                    <path d="M71.488 6.74332H68.5919V11.2083H66.0952V13.7621H68.5919V21.9326H71.488V13.7621H73.9847V11.2083H71.488V6.74332ZM81.2917 10.983C80.0932 10.983 79.1778 11.3669 78.5453 12.1263C78.537 12.1347 78.5287 12.1514 78.5203 12.1597V11.2083H75.6242V21.9326H78.5203V16.0321C78.5203 15.2226 78.7201 14.6217 79.1112 14.2378C79.5024 13.8539 80.01 13.662 80.6259 13.662C80.9171 13.662 81.1835 13.7037 81.4082 13.7871C81.6412 13.8706 81.8326 14.0041 81.999 14.1961L83.8133 12.0846C83.4721 11.7007 83.0976 11.4253 82.6981 11.25C82.2903 11.0831 81.8243 10.9913 81.2917 10.9913V10.983ZM129.577 11.2417C129.17 11.0748 128.704 10.983 128.171 10.983C126.972 10.983 126.057 11.3669 125.425 12.1263C125.416 12.1347 125.408 12.1514 125.4 12.1597V11.2083H122.503V21.9326H125.4V16.0321C125.4 15.2226 125.599 14.6217 125.99 14.2378C126.382 13.8539 126.889 13.662 127.505 13.662C127.796 13.662 128.063 13.7037 128.287 13.7871C128.52 13.8706 128.712 14.0041 128.878 14.1961L130.693 12.0846C130.351 11.7007 129.977 11.4253 129.577 11.25V11.2417ZM118.168 11.6506C117.369 11.1916 116.47 10.9663 115.471 10.9663C114.406 10.9663 113.449 11.2083 112.592 11.7007C111.734 12.1931 111.06 12.8524 110.569 13.6953C110.078 14.5383 109.829 15.4897 109.829 16.5579C109.829 17.6262 110.078 18.6026 110.578 19.4456C111.077 20.2885 111.768 20.9561 112.65 21.4402C113.524 21.9242 114.531 22.1746 115.646 22.1746C116.52 22.1746 117.319 22.0161 118.059 21.7073C118.8 21.3985 119.441 20.9311 119.982 20.3052L118.251 18.5776C117.926 18.9615 117.543 19.2453 117.102 19.4205C116.661 19.5958 116.162 19.6876 115.621 19.6876C115.013 19.6876 114.481 19.5624 114.031 19.312C113.574 19.0617 113.224 18.6944 112.983 18.2104C112.891 18.0184 112.825 17.8098 112.766 17.5928L120.564 17.5761C120.623 17.3424 120.664 17.1254 120.673 16.9251C120.689 16.7248 120.698 16.5329 120.698 16.3409C120.698 15.3061 120.473 14.3797 120.032 13.5701C119.591 12.7606 118.966 12.118 118.176 11.659L118.168 11.6506ZM112.766 15.4229C112.816 15.231 112.875 15.0307 112.958 14.8637C113.183 14.3964 113.515 14.0375 113.94 13.7871C114.364 13.5368 114.872 13.4116 115.446 13.4116C115.995 13.4116 116.453 13.5201 116.819 13.7454C117.186 13.9707 117.477 14.2962 117.685 14.7219C117.785 14.9305 117.86 15.1642 117.91 15.4146L112.766 15.4313V15.4229ZM92.0939 12.2599C91.7943 11.9427 91.4614 11.659 91.0537 11.4503C90.4461 11.1415 89.772 10.983 89.0147 10.983C88.0244 10.983 87.1422 11.225 86.3682 11.7174C85.5943 12.2098 84.9867 12.8691 84.5457 13.712C84.1046 14.5549 83.8799 15.5064 83.8799 16.5746C83.8799 17.6429 84.1046 18.5693 84.5457 19.4122C84.9867 20.2551 85.5943 20.9228 86.3682 21.4068C87.1422 21.8992 88.016 22.1412 88.9897 22.1412C89.7554 22.1412 90.4461 21.9827 91.062 21.6739C91.4698 21.4652 91.7943 21.1898 92.0939 20.881V21.9242H95.015V11.2H92.0939V12.2515V12.2599ZM91.5197 18.6444C91.0204 19.1952 90.3629 19.4622 89.5473 19.4622C89.0313 19.4622 88.5653 19.3371 88.1492 19.0867C87.7331 18.8363 87.4168 18.4941 87.1838 18.0685C86.9508 17.6429 86.8426 17.1338 86.8426 16.5579C86.8426 15.9821 86.9591 15.498 87.1838 15.0724C87.4085 14.6468 87.7331 14.3046 88.1325 14.0542C88.5403 13.8038 89.0064 13.6786 89.539 13.6786C90.0716 13.6786 90.5543 13.8038 90.9538 14.0459C91.3533 14.2879 91.6695 14.6384 91.9025 15.0807C92.1355 15.5231 92.2521 16.0238 92.2521 16.5913C92.2521 17.4175 92.0024 18.1102 91.5031 18.6527L91.5197 18.6444ZM105.068 12.1514C104.785 11.8843 104.477 11.6506 104.12 11.4587C103.504 11.1415 102.805 10.983 102.039 10.983C101.049 10.983 100.167 11.225 99.3926 11.7174C98.6186 12.2098 98.0027 12.8691 97.545 13.712C97.0873 14.5549 96.8543 15.5064 96.8543 16.5746C96.8543 17.6429 97.0873 18.5693 97.545 19.4122C98.0027 20.2551 98.6186 20.9228 99.3926 21.4068C100.167 21.8992 101.049 22.1412 102.039 22.1412C102.821 22.1412 103.52 21.9827 104.145 21.6655C104.511 21.4819 104.819 21.2316 105.093 20.9645V21.9242H108.014V5.87537H105.068V12.1514ZM104.927 18.0685C104.702 18.4941 104.378 18.8363 103.961 19.0867C103.545 19.3371 103.079 19.4622 102.547 19.4622C102.014 19.4622 101.565 19.3371 101.149 19.095C100.732 18.853 100.416 18.5108 100.183 18.0769C99.9501 17.6429 99.842 17.1338 99.842 16.5579C99.842 15.9821 99.9585 15.498 100.183 15.0557C100.408 14.6217 100.732 14.2795 101.132 14.0375C101.54 13.7955 102.014 13.6703 102.563 13.6703C103.113 13.6703 103.562 13.7955 103.97 14.0459C104.378 14.2962 104.694 14.6384 104.919 15.064C105.152 15.4897 105.26 15.9988 105.26 16.5746C105.26 17.1505 105.143 17.6345 104.919 18.0602L104.927 18.0685Z"
                                          fill="white"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_17091_79575">
                                        <rect width="169" height="28" fill="white"/>
                                    </clipPath>
                                </defs>
                            </svg>

                            <hr class="payouts-and-comparison__separator">

                            <?php
                            $payouts_and_comparison__list = [
                                    'Modern, with TradingView built in',
                                    'Real-time risk controls, smart guardrails',
                                    'Trade on mobile, desktop or the web',
                                    'Popular at Tradeify'
                            ];
                            ?>

                            <ul class="payouts-and-comparison__list list-unstyled">
                                <?php foreach ($payouts_and_comparison__list as $payouts_and_comparison__value) : ?>
                                    <li>
                                        <img class="payouts-and-comparison__checked"
                                             src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/checked-circle.svg'); ?>"
                                             alt="checked circle">

                                        <p>
                                            <?= $payouts_and_comparison__value; ?>
                                        </p>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <a href="#get-funded" class="btn mega-btn-md mega-btn-primary-md">
                                Trade on Projectx
                            </a>
                        </div>
                    </div>
                    <div class="payouts-and-comparison__card">
                        <div class="payouts-and-comparison__badge">
                            <div class="mt-badge mt-badge-rounded mt-badge-light d-inline">COMING SOON</div>
                        </div>

                        <img class="payouts-and-comparison__image"
                             src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/window-coming-soon.png'); ?>"
                             alt="flash">

                        <div class="payouts-and-comparison__content">
                            <div class="payouts-and-comparison__platforms">
                                <svg width="110" height="30" viewBox="0 0 110 30" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 15C0 6.71573 6.71573 0 15 0C23.2843 0 30 6.71573 30 15C30 23.2843 23.2843 30 15 30C6.71573 30 0 23.2843 0 15Z"
                                          fill="#257FFF"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                          d="M18.7299 21.2464V24.2106L12.0679 21.2464L18.7299 18.2823V21.2464ZM11.7964 17.9917V20.9558L18.4583 17.9917L11.7964 15.0275V17.9917ZM11.4002 11.4821V14.4462L4.73682 11.4821L11.3988 8.51797V11.4821H11.4002ZM18.7313 14.7369V17.701L12.0693 14.7369L18.7313 11.7728V14.7369ZM11.7978 11.4821V14.4462L18.4597 11.4821L11.7978 8.51797V11.4821ZM18.7313 8.22729V11.1914L12.0693 8.22729L18.7299 5.26318V8.22729H18.7313ZM19.1275 8.22729V11.1914L25.7894 8.22729L19.1261 5.26318V8.22729H19.1275Z"
                                          fill="white"/>
                                    <path d="M43.8359 8.625V20H41.8906V8.625H43.8359ZM47.4062 8.625V10.1875H38.3516V8.625H47.4062ZM50.0469 13.1562V20H48.1641V11.5469H49.9609L50.0469 13.1562ZM52.6328 11.4922L52.6172 13.2422C52.5026 13.2214 52.3776 13.2057 52.2422 13.1953C52.112 13.1849 51.9818 13.1797 51.8516 13.1797C51.5286 13.1797 51.2448 13.2266 51 13.3203C50.7552 13.4089 50.5495 13.5391 50.3828 13.7109C50.2214 13.8776 50.0964 14.0807 50.0078 14.3203C49.9193 14.5599 49.8672 14.8281 49.8516 15.125L49.4219 15.1562C49.4219 14.625 49.474 14.1328 49.5781 13.6797C49.6823 13.2266 49.8385 12.8281 50.0469 12.4844C50.2604 12.1406 50.526 11.8724 50.8438 11.6797C51.1667 11.487 51.5391 11.3906 51.9609 11.3906C52.0755 11.3906 52.1979 11.401 52.3281 11.4219C52.4635 11.4427 52.5651 11.4661 52.6328 11.4922ZM58.2969 18.3047V14.2734C58.2969 13.9714 58.2422 13.7109 58.1328 13.4922C58.0234 13.2734 57.8568 13.1042 57.6328 12.9844C57.4141 12.8646 57.138 12.8047 56.8047 12.8047C56.4974 12.8047 56.2318 12.8568 56.0078 12.9609C55.7839 13.0651 55.6094 13.2057 55.4844 13.3828C55.3594 13.5599 55.2969 13.7604 55.2969 13.9844H53.4219C53.4219 13.651 53.5026 13.3281 53.6641 13.0156C53.8255 12.7031 54.0599 12.4245 54.3672 12.1797C54.6745 11.9349 55.0417 11.7422 55.4688 11.6016C55.8958 11.4609 56.375 11.3906 56.9062 11.3906C57.5417 11.3906 58.1042 11.4974 58.5938 11.7109C59.0885 11.9245 59.4766 12.2474 59.7578 12.6797C60.0443 13.1068 60.1875 13.6432 60.1875 14.2891V18.0469C60.1875 18.4323 60.2135 18.7786 60.2656 19.0859C60.3229 19.388 60.4036 19.651 60.5078 19.875V20H58.5781C58.4896 19.7969 58.4193 19.5391 58.3672 19.2266C58.3203 18.9089 58.2969 18.6016 58.2969 18.3047ZM58.5703 14.8594L58.5859 16.0234H57.2344C56.8854 16.0234 56.5781 16.0573 56.3125 16.125C56.0469 16.1875 55.8255 16.2812 55.6484 16.4062C55.4714 16.5312 55.3385 16.6823 55.25 16.8594C55.1615 17.0365 55.1172 17.237 55.1172 17.4609C55.1172 17.6849 55.1693 17.8906 55.2734 18.0781C55.3776 18.2604 55.5286 18.4036 55.7266 18.5078C55.9297 18.612 56.1745 18.6641 56.4609 18.6641C56.8464 18.6641 57.1823 18.5859 57.4688 18.4297C57.7604 18.2682 57.9896 18.0729 58.1562 17.8438C58.3229 17.6094 58.4115 17.388 58.4219 17.1797L59.0312 18.0156C58.9688 18.2292 58.862 18.4583 58.7109 18.7031C58.5599 18.9479 58.362 19.1823 58.1172 19.4062C57.8776 19.625 57.5885 19.8047 57.25 19.9453C56.9167 20.0859 56.5312 20.1562 56.0938 20.1562C55.5417 20.1562 55.0495 20.0469 54.6172 19.8281C54.1849 19.6042 53.8464 19.3047 53.6016 18.9297C53.3568 18.5495 53.2344 18.1198 53.2344 17.6406C53.2344 17.1927 53.3177 16.7969 53.4844 16.4531C53.6562 16.1042 53.9062 15.8125 54.2344 15.5781C54.5677 15.3438 54.974 15.1667 55.4531 15.0469C55.9323 14.9219 56.4792 14.8594 57.0938 14.8594H58.5703ZM67.3125 18.25V8H69.2031V20H67.4922L67.3125 18.25ZM61.8125 15.8672V15.7031C61.8125 15.0625 61.888 14.4792 62.0391 13.9531C62.1901 13.4219 62.4089 12.9661 62.6953 12.5859C62.9818 12.2005 63.3307 11.9062 63.7422 11.7031C64.1536 11.4948 64.6172 11.3906 65.1328 11.3906C65.6432 11.3906 66.0911 11.4896 66.4766 11.6875C66.862 11.8854 67.1901 12.1693 67.4609 12.5391C67.7318 12.9036 67.9479 13.3411 68.1094 13.8516C68.2708 14.3568 68.3854 14.9193 68.4531 15.5391V16.0625C68.3854 16.6667 68.2708 17.2188 68.1094 17.7188C67.9479 18.2188 67.7318 18.651 67.4609 19.0156C67.1901 19.3802 66.8594 19.6615 66.4688 19.8594C66.0833 20.0573 65.6328 20.1562 65.1172 20.1562C64.6068 20.1562 64.1458 20.0495 63.7344 19.8359C63.3281 19.6224 62.9818 19.3229 62.6953 18.9375C62.4089 18.5521 62.1901 18.099 62.0391 17.5781C61.888 17.0521 61.8125 16.4818 61.8125 15.8672ZM63.6953 15.7031V15.8672C63.6953 16.2526 63.7292 16.612 63.7969 16.9453C63.8698 17.2786 63.9818 17.5729 64.1328 17.8281C64.2839 18.0781 64.4792 18.276 64.7188 18.4219C64.9635 18.5625 65.2552 18.6328 65.5938 18.6328C66.0208 18.6328 66.3724 18.5391 66.6484 18.3516C66.9245 18.1641 67.1406 17.9115 67.2969 17.5938C67.4583 17.2708 67.5677 16.9115 67.625 16.5156V15.1016C67.5938 14.7943 67.5286 14.5078 67.4297 14.2422C67.3359 13.9766 67.2083 13.7448 67.0469 13.5469C66.8854 13.3438 66.6849 13.1875 66.4453 13.0781C66.2109 12.9635 65.9323 12.9062 65.6094 12.9062C65.2656 12.9062 64.974 12.9792 64.7344 13.125C64.4948 13.2708 64.2969 13.4714 64.1406 13.7266C63.9896 13.9818 63.8776 14.2786 63.8047 14.6172C63.7318 14.9557 63.6953 15.3177 63.6953 15.7031ZM70.8125 15.8672V15.6875C70.8125 15.0781 70.901 14.513 71.0781 13.9922C71.2552 13.4661 71.5104 13.0104 71.8438 12.625C72.1823 12.2344 72.5938 11.9323 73.0781 11.7188C73.5677 11.5 74.1198 11.3906 74.7344 11.3906C75.3542 11.3906 75.9062 11.5 76.3906 11.7188C76.8802 11.9323 77.2943 12.2344 77.6328 12.625C77.9714 13.0104 78.2292 13.4661 78.4062 13.9922C78.5833 14.513 78.6719 15.0781 78.6719 15.6875V15.8672C78.6719 16.4766 78.5833 17.0417 78.4062 17.5625C78.2292 18.0833 77.9714 18.5391 77.6328 18.9297C77.2943 19.3151 76.8828 19.6172 76.3984 19.8359C75.9141 20.0495 75.3646 20.1562 74.75 20.1562C74.1302 20.1562 73.5755 20.0495 73.0859 19.8359C72.6016 19.6172 72.1901 19.3151 71.8516 18.9297C71.513 18.5391 71.2552 18.0833 71.0781 17.5625C70.901 17.0417 70.8125 16.4766 70.8125 15.8672ZM72.6953 15.6875V15.8672C72.6953 16.2474 72.7344 16.6068 72.8125 16.9453C72.8906 17.2839 73.013 17.5807 73.1797 17.8359C73.3464 18.0911 73.5599 18.2917 73.8203 18.4375C74.0807 18.5833 74.3906 18.6562 74.75 18.6562C75.099 18.6562 75.401 18.5833 75.6562 18.4375C75.9167 18.2917 76.1302 18.0911 76.2969 17.8359C76.4635 17.5807 76.5859 17.2839 76.6641 16.9453C76.7474 16.6068 76.7891 16.2474 76.7891 15.8672V15.6875C76.7891 15.3125 76.7474 14.9583 76.6641 14.625C76.5859 14.2865 76.4609 13.987 76.2891 13.7266C76.1224 13.4661 75.9089 13.263 75.6484 13.1172C75.3932 12.9661 75.0885 12.8906 74.7344 12.8906C74.3802 12.8906 74.0729 12.9661 73.8125 13.1172C73.5573 13.263 73.3464 13.4661 73.1797 13.7266C73.013 13.987 72.8906 14.2865 72.8125 14.625C72.7344 14.9583 72.6953 15.3125 72.6953 15.6875ZM82.875 18.5078L84.9453 11.5469H86.8984L83.9609 20H82.7422L82.875 18.5078ZM81.2891 11.5469L83.3984 18.5391L83.5 20H82.2812L79.3281 11.5469H81.2891ZM92.7031 18.3047V14.2734C92.7031 13.9714 92.6484 13.7109 92.5391 13.4922C92.4297 13.2734 92.263 13.1042 92.0391 12.9844C91.8203 12.8646 91.5443 12.8047 91.2109 12.8047C90.9036 12.8047 90.638 12.8568 90.4141 12.9609C90.1901 13.0651 90.0156 13.2057 89.8906 13.3828C89.7656 13.5599 89.7031 13.7604 89.7031 13.9844H87.8281C87.8281 13.651 87.9089 13.3281 88.0703 13.0156C88.2318 12.7031 88.4661 12.4245 88.7734 12.1797C89.0807 11.9349 89.4479 11.7422 89.875 11.6016C90.3021 11.4609 90.7812 11.3906 91.3125 11.3906C91.9479 11.3906 92.5104 11.4974 93 11.7109C93.4948 11.9245 93.8828 12.2474 94.1641 12.6797C94.4505 13.1068 94.5938 13.6432 94.5938 14.2891V18.0469C94.5938 18.4323 94.6198 18.7786 94.6719 19.0859C94.7292 19.388 94.8099 19.651 94.9141 19.875V20H92.9844C92.8958 19.7969 92.8255 19.5391 92.7734 19.2266C92.7266 18.9089 92.7031 18.6016 92.7031 18.3047ZM92.9766 14.8594L92.9922 16.0234H91.6406C91.2917 16.0234 90.9844 16.0573 90.7188 16.125C90.4531 16.1875 90.2318 16.2812 90.0547 16.4062C89.8776 16.5312 89.7448 16.6823 89.6562 16.8594C89.5677 17.0365 89.5234 17.237 89.5234 17.4609C89.5234 17.6849 89.5755 17.8906 89.6797 18.0781C89.7839 18.2604 89.9349 18.4036 90.1328 18.5078C90.3359 18.612 90.5807 18.6641 90.8672 18.6641C91.2526 18.6641 91.5885 18.5859 91.875 18.4297C92.1667 18.2682 92.3958 18.0729 92.5625 17.8438C92.7292 17.6094 92.8177 17.388 92.8281 17.1797L93.4375 18.0156C93.375 18.2292 93.2682 18.4583 93.1172 18.7031C92.9661 18.9479 92.7682 19.1823 92.5234 19.4062C92.2839 19.625 91.9948 19.8047 91.6562 19.9453C91.3229 20.0859 90.9375 20.1562 90.5 20.1562C89.9479 20.1562 89.4557 20.0469 89.0234 19.8281C88.5911 19.6042 88.2526 19.3047 88.0078 18.9297C87.763 18.5495 87.6406 18.1198 87.6406 17.6406C87.6406 17.1927 87.724 16.7969 87.8906 16.4531C88.0625 16.1042 88.3125 15.8125 88.6406 15.5781C88.974 15.3438 89.3802 15.1667 89.8594 15.0469C90.3385 14.9219 90.8854 14.8594 91.5 14.8594H92.9766ZM100.438 11.5469V12.9219H95.6719V11.5469H100.438ZM97.0469 9.47656H98.9297V17.6641C98.9297 17.9245 98.9661 18.125 99.0391 18.2656C99.1172 18.401 99.224 18.4922 99.3594 18.5391C99.4948 18.5859 99.6536 18.6094 99.8359 18.6094C99.9661 18.6094 100.091 18.6016 100.211 18.5859C100.331 18.5703 100.427 18.5547 100.5 18.5391L100.508 19.9766C100.352 20.0234 100.169 20.0651 99.9609 20.1016C99.7578 20.138 99.5234 20.1562 99.2578 20.1562C98.8255 20.1562 98.4427 20.0807 98.1094 19.9297C97.776 19.7734 97.5156 19.5208 97.3281 19.1719C97.1406 18.8229 97.0469 18.3594 97.0469 17.7812V9.47656ZM105.617 20.1562C104.992 20.1562 104.427 20.0547 103.922 19.8516C103.422 19.6432 102.995 19.3542 102.641 18.9844C102.292 18.6146 102.023 18.1797 101.836 17.6797C101.648 17.1797 101.555 16.6406 101.555 16.0625V15.75C101.555 15.0885 101.651 14.4896 101.844 13.9531C102.036 13.4167 102.305 12.9583 102.648 12.5781C102.992 12.1927 103.398 11.8984 103.867 11.6953C104.336 11.4922 104.844 11.3906 105.391 11.3906C105.995 11.3906 106.523 11.4922 106.977 11.6953C107.43 11.8984 107.805 12.1849 108.102 12.5547C108.404 12.9193 108.628 13.3542 108.773 13.8594C108.924 14.3646 109 14.9219 109 15.5312V16.3359H102.469V14.9844H107.141V14.8359C107.13 14.4974 107.062 14.1797 106.938 13.8828C106.818 13.5859 106.633 13.3464 106.383 13.1641C106.133 12.9818 105.799 12.8906 105.383 12.8906C105.07 12.8906 104.792 12.9583 104.547 13.0938C104.307 13.224 104.107 13.4141 103.945 13.6641C103.784 13.9141 103.659 14.2161 103.57 14.5703C103.487 14.9193 103.445 15.3125 103.445 15.75V16.0625C103.445 16.4323 103.495 16.776 103.594 17.0938C103.698 17.4062 103.849 17.6797 104.047 17.9141C104.245 18.1484 104.484 18.3333 104.766 18.4688C105.047 18.599 105.367 18.6641 105.727 18.6641C106.18 18.6641 106.583 18.5729 106.938 18.3906C107.292 18.2083 107.599 17.9505 107.859 17.6172L108.852 18.5781C108.669 18.8438 108.432 19.099 108.141 19.3438C107.849 19.5833 107.492 19.7786 107.07 19.9297C106.654 20.0807 106.169 20.1562 105.617 20.1562Z"
                                          fill="white"/>
                                </svg>

                                <svg width="118" height="30" viewBox="0 0 118 30" fill="none"
                                     xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                    <path d="M0 15C0 6.71573 6.71573 0 15 0C23.2843 0 30 6.71573 30 15C30 23.2843 23.2843 30 15 30C6.71573 30 0 23.2843 0 15Z"
                                          fill="#131210"/>
                                    <path d="M0 15C0 6.71573 6.71573 0 15 0C23.2843 0 30 6.71573 30 15C30 23.2843 23.2843 30 15 30C6.71573 30 0 23.2843 0 15Z"
                                          fill="url(#pattern0_17091_79544)"/>
                                    <path d="M48.1797 8.625V20H46.2188L41.1172 11.8516V20H39.1562V8.625H41.1172L46.2344 16.7891V8.625H48.1797ZM52.3438 11.5469V20H50.4531V11.5469H52.3438ZM50.3281 9.32812C50.3281 9.04167 50.4219 8.80469 50.6094 8.61719C50.8021 8.42448 51.0677 8.32812 51.4062 8.32812C51.7396 8.32812 52.0026 8.42448 52.1953 8.61719C52.388 8.80469 52.4844 9.04167 52.4844 9.32812C52.4844 9.60938 52.388 9.84375 52.1953 10.0312C52.0026 10.2188 51.7396 10.3125 51.4062 10.3125C51.0677 10.3125 50.8021 10.2188 50.6094 10.0312C50.4219 9.84375 50.3281 9.60938 50.3281 9.32812ZM56.2734 13.3516V20H54.3906V11.5469H56.1641L56.2734 13.3516ZM55.9375 15.4609L55.3281 15.4531C55.3333 14.8542 55.4167 14.3047 55.5781 13.8047C55.7448 13.3047 55.974 12.875 56.2656 12.5156C56.5625 12.1562 56.9167 11.8802 57.3281 11.6875C57.7396 11.4896 58.1979 11.3906 58.7031 11.3906C59.1094 11.3906 59.4766 11.4479 59.8047 11.5625C60.138 11.6719 60.4219 11.8516 60.6562 12.1016C60.8958 12.3516 61.0781 12.6771 61.2031 13.0781C61.3281 13.474 61.3906 13.9609 61.3906 14.5391V20H59.5V14.5312C59.5 14.125 59.4401 13.8047 59.3203 13.5703C59.2057 13.3307 59.0365 13.1615 58.8125 13.0625C58.5938 12.9583 58.3203 12.9062 57.9922 12.9062C57.6693 12.9062 57.3802 12.974 57.125 13.1094C56.8698 13.2448 56.6536 13.4297 56.4766 13.6641C56.3047 13.8984 56.1719 14.1693 56.0781 14.4766C55.9844 14.7839 55.9375 15.112 55.9375 15.4609ZM63.4062 11.5469H65.2969V20.7344C65.2969 21.3125 65.1979 21.7995 65 22.1953C64.8021 22.5964 64.5104 22.8984 64.125 23.1016C63.7396 23.3099 63.2682 23.4141 62.7109 23.4141C62.5443 23.4141 62.3724 23.401 62.1953 23.375C62.013 23.3542 61.8411 23.3229 61.6797 23.2812L61.6875 21.8125C61.7969 21.8333 61.9141 21.849 62.0391 21.8594C62.1589 21.875 62.2734 21.8828 62.3828 21.8828C62.6068 21.8828 62.7943 21.8438 62.9453 21.7656C63.0964 21.6875 63.2109 21.5651 63.2891 21.3984C63.3672 21.2318 63.4062 21.0104 63.4062 20.7344V11.5469ZM63.2422 9.32812C63.2422 9.04167 63.3385 8.80469 63.5312 8.61719C63.724 8.42448 63.987 8.32812 64.3203 8.32812C64.6589 8.32812 64.9219 8.42448 65.1094 8.61719C65.3021 8.80469 65.3984 9.04167 65.3984 9.32812C65.3984 9.60938 65.3021 9.84375 65.1094 10.0312C64.9219 10.2188 64.6589 10.3125 64.3203 10.3125C63.987 10.3125 63.724 10.2188 63.5312 10.0312C63.3385 9.84375 63.2422 9.60938 63.2422 9.32812ZM72.0938 18.3047V14.2734C72.0938 13.9714 72.0391 13.7109 71.9297 13.4922C71.8203 13.2734 71.6536 13.1042 71.4297 12.9844C71.2109 12.8646 70.9349 12.8047 70.6016 12.8047C70.2943 12.8047 70.0286 12.8568 69.8047 12.9609C69.5807 13.0651 69.4062 13.2057 69.2812 13.3828C69.1562 13.5599 69.0938 13.7604 69.0938 13.9844H67.2188C67.2188 13.651 67.2995 13.3281 67.4609 13.0156C67.6224 12.7031 67.8568 12.4245 68.1641 12.1797C68.4714 11.9349 68.8385 11.7422 69.2656 11.6016C69.6927 11.4609 70.1719 11.3906 70.7031 11.3906C71.3385 11.3906 71.901 11.4974 72.3906 11.7109C72.8854 11.9245 73.2734 12.2474 73.5547 12.6797C73.8411 13.1068 73.9844 13.6432 73.9844 14.2891V18.0469C73.9844 18.4323 74.0104 18.7786 74.0625 19.0859C74.1198 19.388 74.2005 19.651 74.3047 19.875V20H72.375C72.2865 19.7969 72.2161 19.5391 72.1641 19.2266C72.1172 18.9089 72.0938 18.6016 72.0938 18.3047ZM72.3672 14.8594L72.3828 16.0234H71.0312C70.6823 16.0234 70.375 16.0573 70.1094 16.125C69.8438 16.1875 69.6224 16.2812 69.4453 16.4062C69.2682 16.5312 69.1354 16.6823 69.0469 16.8594C68.9583 17.0365 68.9141 17.237 68.9141 17.4609C68.9141 17.6849 68.9661 17.8906 69.0703 18.0781C69.1745 18.2604 69.3255 18.4036 69.5234 18.5078C69.7266 18.612 69.9714 18.6641 70.2578 18.6641C70.6432 18.6641 70.9792 18.5859 71.2656 18.4297C71.5573 18.2682 71.7865 18.0729 71.9531 17.8438C72.1198 17.6094 72.2083 17.388 72.2188 17.1797L72.8281 18.0156C72.7656 18.2292 72.6589 18.4583 72.5078 18.7031C72.3568 18.9479 72.1589 19.1823 71.9141 19.4062C71.6745 19.625 71.3854 19.8047 71.0469 19.9453C70.7135 20.0859 70.3281 20.1562 69.8906 20.1562C69.3385 20.1562 68.8464 20.0469 68.4141 19.8281C67.9818 19.6042 67.6432 19.3047 67.3984 18.9297C67.1536 18.5495 67.0312 18.1198 67.0312 17.6406C67.0312 17.1927 67.1146 16.7969 67.2812 16.4531C67.4531 16.1042 67.7031 15.8125 68.0312 15.5781C68.3646 15.3438 68.7708 15.1667 69.25 15.0469C69.7292 14.9219 70.276 14.8594 70.8906 14.8594H72.3672ZM79.8281 11.5469V12.9219H75.0625V11.5469H79.8281ZM76.4375 9.47656H78.3203V17.6641C78.3203 17.9245 78.3568 18.125 78.4297 18.2656C78.5078 18.401 78.6146 18.4922 78.75 18.5391C78.8854 18.5859 79.0443 18.6094 79.2266 18.6094C79.3568 18.6094 79.4818 18.6016 79.6016 18.5859C79.7214 18.5703 79.8177 18.5547 79.8906 18.5391L79.8984 19.9766C79.7422 20.0234 79.5599 20.0651 79.3516 20.1016C79.1484 20.138 78.9141 20.1562 78.6484 20.1562C78.2161 20.1562 77.8333 20.0807 77.5 19.9297C77.1667 19.7734 76.9062 19.5208 76.7188 19.1719C76.5312 18.8229 76.4375 18.3594 76.4375 17.7812V9.47656ZM83.1719 13.1562V20H81.2891V11.5469H83.0859L83.1719 13.1562ZM85.7578 11.4922L85.7422 13.2422C85.6276 13.2214 85.5026 13.2057 85.3672 13.1953C85.237 13.1849 85.1068 13.1797 84.9766 13.1797C84.6536 13.1797 84.3698 13.2266 84.125 13.3203C83.8802 13.4089 83.6745 13.5391 83.5078 13.7109C83.3464 13.8776 83.2214 14.0807 83.1328 14.3203C83.0443 14.5599 82.9922 14.8281 82.9766 15.125L82.5469 15.1562C82.5469 14.625 82.599 14.1328 82.7031 13.6797C82.8073 13.2266 82.9635 12.8281 83.1719 12.4844C83.3854 12.1406 83.651 11.8724 83.9688 11.6797C84.2917 11.487 84.6641 11.3906 85.0859 11.3906C85.2005 11.3906 85.3229 11.401 85.4531 11.4219C85.5885 11.4427 85.6901 11.4661 85.7578 11.4922ZM91.4219 18.3047V14.2734C91.4219 13.9714 91.3672 13.7109 91.2578 13.4922C91.1484 13.2734 90.9818 13.1042 90.7578 12.9844C90.5391 12.8646 90.263 12.8047 89.9297 12.8047C89.6224 12.8047 89.3568 12.8568 89.1328 12.9609C88.9089 13.0651 88.7344 13.2057 88.6094 13.3828C88.4844 13.5599 88.4219 13.7604 88.4219 13.9844H86.5469C86.5469 13.651 86.6276 13.3281 86.7891 13.0156C86.9505 12.7031 87.1849 12.4245 87.4922 12.1797C87.7995 11.9349 88.1667 11.7422 88.5938 11.6016C89.0208 11.4609 89.5 11.3906 90.0312 11.3906C90.6667 11.3906 91.2292 11.4974 91.7188 11.7109C92.2135 11.9245 92.6016 12.2474 92.8828 12.6797C93.1693 13.1068 93.3125 13.6432 93.3125 14.2891V18.0469C93.3125 18.4323 93.3385 18.7786 93.3906 19.0859C93.4479 19.388 93.5286 19.651 93.6328 19.875V20H91.7031C91.6146 19.7969 91.5443 19.5391 91.4922 19.2266C91.4453 18.9089 91.4219 18.6016 91.4219 18.3047ZM91.6953 14.8594L91.7109 16.0234H90.3594C90.0104 16.0234 89.7031 16.0573 89.4375 16.125C89.1719 16.1875 88.9505 16.2812 88.7734 16.4062C88.5964 16.5312 88.4635 16.6823 88.375 16.8594C88.2865 17.0365 88.2422 17.237 88.2422 17.4609C88.2422 17.6849 88.2943 17.8906 88.3984 18.0781C88.5026 18.2604 88.6536 18.4036 88.8516 18.5078C89.0547 18.612 89.2995 18.6641 89.5859 18.6641C89.9714 18.6641 90.3073 18.5859 90.5938 18.4297C90.8854 18.2682 91.1146 18.0729 91.2812 17.8438C91.4479 17.6094 91.5365 17.388 91.5469 17.1797L92.1562 18.0156C92.0938 18.2292 91.987 18.4583 91.8359 18.7031C91.6849 18.9479 91.487 19.1823 91.2422 19.4062C91.0026 19.625 90.7135 19.8047 90.375 19.9453C90.0417 20.0859 89.6562 20.1562 89.2188 20.1562C88.6667 20.1562 88.1745 20.0469 87.7422 19.8281C87.3099 19.6042 86.9714 19.3047 86.7266 18.9297C86.4818 18.5495 86.3594 18.1198 86.3594 17.6406C86.3594 17.1927 86.4427 16.7969 86.6094 16.4531C86.7812 16.1042 87.0312 15.8125 87.3594 15.5781C87.6927 15.3438 88.099 15.1667 88.5781 15.0469C89.0573 14.9219 89.6042 14.8594 90.2188 14.8594H91.6953ZM100.438 18.25V8H102.328V20H100.617L100.438 18.25ZM94.9375 15.8672V15.7031C94.9375 15.0625 95.013 14.4792 95.1641 13.9531C95.3151 13.4219 95.5339 12.9661 95.8203 12.5859C96.1068 12.2005 96.4557 11.9062 96.8672 11.7031C97.2786 11.4948 97.7422 11.3906 98.2578 11.3906C98.7682 11.3906 99.2161 11.4896 99.6016 11.6875C99.987 11.8854 100.315 12.1693 100.586 12.5391C100.857 12.9036 101.073 13.3411 101.234 13.8516C101.396 14.3568 101.51 14.9193 101.578 15.5391V16.0625C101.51 16.6667 101.396 17.2188 101.234 17.7188C101.073 18.2188 100.857 18.651 100.586 19.0156C100.315 19.3802 99.9844 19.6615 99.5938 19.8594C99.2083 20.0573 98.7578 20.1562 98.2422 20.1562C97.7318 20.1562 97.2708 20.0495 96.8594 19.8359C96.4531 19.6224 96.1068 19.3229 95.8203 18.9375C95.5339 18.5521 95.3151 18.099 95.1641 17.5781C95.013 17.0521 94.9375 16.4818 94.9375 15.8672ZM96.8203 15.7031V15.8672C96.8203 16.2526 96.8542 16.612 96.9219 16.9453C96.9948 17.2786 97.1068 17.5729 97.2578 17.8281C97.4089 18.0781 97.6042 18.276 97.8438 18.4219C98.0885 18.5625 98.3802 18.6328 98.7188 18.6328C99.1458 18.6328 99.4974 18.5391 99.7734 18.3516C100.049 18.1641 100.266 17.9115 100.422 17.5938C100.583 17.2708 100.693 16.9115 100.75 16.5156V15.1016C100.719 14.7943 100.654 14.5078 100.555 14.2422C100.461 13.9766 100.333 13.7448 100.172 13.5469C100.01 13.3438 99.8099 13.1875 99.5703 13.0781C99.3359 12.9635 99.0573 12.9062 98.7344 12.9062C98.3906 12.9062 98.099 12.9792 97.8594 13.125C97.6198 13.2708 97.4219 13.4714 97.2656 13.7266C97.1146 13.9818 97.0026 14.2786 96.9297 14.6172C96.8568 14.9557 96.8203 15.3177 96.8203 15.7031ZM108.023 20.1562C107.398 20.1562 106.833 20.0547 106.328 19.8516C105.828 19.6432 105.401 19.3542 105.047 18.9844C104.698 18.6146 104.43 18.1797 104.242 17.6797C104.055 17.1797 103.961 16.6406 103.961 16.0625V15.75C103.961 15.0885 104.057 14.4896 104.25 13.9531C104.443 13.4167 104.711 12.9583 105.055 12.5781C105.398 12.1927 105.805 11.8984 106.273 11.6953C106.742 11.4922 107.25 11.3906 107.797 11.3906C108.401 11.3906 108.93 11.4922 109.383 11.6953C109.836 11.8984 110.211 12.1849 110.508 12.5547C110.81 12.9193 111.034 13.3542 111.18 13.8594C111.331 14.3646 111.406 14.9219 111.406 15.5312V16.3359H104.875V14.9844H109.547V14.8359C109.536 14.4974 109.469 14.1797 109.344 13.8828C109.224 13.5859 109.039 13.3464 108.789 13.1641C108.539 12.9818 108.206 12.8906 107.789 12.8906C107.477 12.8906 107.198 12.9583 106.953 13.0938C106.714 13.224 106.513 13.4141 106.352 13.6641C106.19 13.9141 106.065 14.2161 105.977 14.5703C105.893 14.9193 105.852 15.3125 105.852 15.75V16.0625C105.852 16.4323 105.901 16.776 106 17.0938C106.104 17.4062 106.255 17.6797 106.453 17.9141C106.651 18.1484 106.891 18.3333 107.172 18.4688C107.453 18.599 107.773 18.6641 108.133 18.6641C108.586 18.6641 108.99 18.5729 109.344 18.3906C109.698 18.2083 110.005 17.9505 110.266 17.6172L111.258 18.5781C111.076 18.8438 110.839 19.099 110.547 19.3438C110.255 19.5833 109.898 19.7786 109.477 19.9297C109.06 20.0807 108.576 20.1562 108.023 20.1562ZM114.766 13.1562V20H112.883V11.5469H114.68L114.766 13.1562ZM117.352 11.4922L117.336 13.2422C117.221 13.2214 117.096 13.2057 116.961 13.1953C116.831 13.1849 116.701 13.1797 116.57 13.1797C116.247 13.1797 115.964 13.2266 115.719 13.3203C115.474 13.4089 115.268 13.5391 115.102 13.7109C114.94 13.8776 114.815 14.0807 114.727 14.3203C114.638 14.5599 114.586 14.8281 114.57 15.125L114.141 15.1562C114.141 14.625 114.193 14.1328 114.297 13.6797C114.401 13.2266 114.557 12.8281 114.766 12.4844C114.979 12.1406 115.245 11.8724 115.562 11.6797C115.885 11.487 116.258 11.3906 116.68 11.3906C116.794 11.3906 116.917 11.401 117.047 11.4219C117.182 11.4427 117.284 11.4661 117.352 11.4922Z"
                                          fill="white"/>
                                    <defs>
                                        <pattern id="pattern0_17091_79544" patternContentUnits="objectBoundingBox"
                                                 width="1" height="1">
                                            <use xlink:href="#image0_17091_79544" transform="scale(0.0025)"/>
                                        </pattern>
                                        <image id="image0_17091_79544" width="400" height="400"
                                               preserveAspectRatio="none"
                                               xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAZAAAAGQCAYAAACAvzbMAAAACXBIWXMAAAsTAAALEwEAmpwYAAAHCGlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgOS4wLWMwMDAgNzkuMTcxYzI3ZmFiLCAyMDIyLzA4LzE2LTIyOjM1OjQxICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIgeG1sbnM6cGhvdG9zaG9wPSJodHRwOi8vbnMuYWRvYmUuY29tL3Bob3Rvc2hvcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgMjQuMSAoTWFjaW50b3NoKSIgeG1wOkNyZWF0ZURhdGU9IjIwMjQtMTItMThUMTk6MDI6MzArMDE6MDAiIHhtcDpNb2RpZnlEYXRlPSIyMDI0LTEyLTE4VDE5OjA0OjQ3KzAxOjAwIiB4bXA6TWV0YWRhdGFEYXRlPSIyMDI0LTEyLTE4VDE5OjA0OjQ3KzAxOjAwIiBkYzpmb3JtYXQ9ImltYWdlL3BuZyIgcGhvdG9zaG9wOkNvbG9yTW9kZT0iMyIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo3ZmYzMTU2Yy1hMzFlLTRjYjQtYTUyYy0xYzQ3ZTQxYzQ4MzgiIHhtcE1NOkRvY3VtZW50SUQ9ImFkb2JlOmRvY2lkOnBob3Rvc2hvcDoyNDU1N2RmMy00YzNiLTc1NGYtYWQ0Zi1mMzBlNjhlMjE3M2EiIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDoxMmRhYWQ5NS0xZDA5LTRhZWEtYjgyNS1iODI2MDY2ZTVkZDgiPiA8eG1wTU06SGlzdG9yeT4gPHJkZjpTZXE+IDxyZGY6bGkgc3RFdnQ6YWN0aW9uPSJjcmVhdGVkIiBzdEV2dDppbnN0YW5jZUlEPSJ4bXAuaWlkOjEyZGFhZDk1LTFkMDktNGFlYS1iODI1LWI4MjYwNjZlNWRkOCIgc3RFdnQ6d2hlbj0iMjAyNC0xMi0xOFQxOTowMjozMCswMTowMCIgc3RFdnQ6c29mdHdhcmVBZ2VudD0iQWRvYmUgUGhvdG9zaG9wIDI0LjEgKE1hY2ludG9zaCkiLz4gPHJkZjpsaSBzdEV2dDphY3Rpb249ImNvbnZlcnRlZCIgc3RFdnQ6cGFyYW1ldGVycz0iZnJvbSBhcHBsaWNhdGlvbi92bmQuYWRvYmUucGhvdG9zaG9wIHRvIGltYWdlL3BuZyIvPiA8cmRmOmxpIHN0RXZ0OmFjdGlvbj0ic2F2ZWQiIHN0RXZ0Omluc3RhbmNlSUQ9InhtcC5paWQ6MWNiZTQwY2EtYWNhMi00MjdhLTkwODMtMjJjODA3NGYyNjRmIiBzdEV2dDp3aGVuPSIyMDI0LTEyLTE4VDE5OjAzOjM5KzAxOjAwIiBzdEV2dDpzb2Z0d2FyZUFnZW50PSJBZG9iZSBQaG90b3Nob3AgMjQuMSAoTWFjaW50b3NoKSIgc3RFdnQ6Y2hhbmdlZD0iLyIvPiA8cmRmOmxpIHN0RXZ0OmFjdGlvbj0ic2F2ZWQiIHN0RXZ0Omluc3RhbmNlSUQ9InhtcC5paWQ6N2ZmMzE1NmMtYTMxZS00Y2I0LWE1MmMtMWM0N2U0MWM0ODM4IiBzdEV2dDp3aGVuPSIyMDI0LTEyLTE4VDE5OjA0OjQ3KzAxOjAwIiBzdEV2dDpzb2Z0d2FyZUFnZW50PSJBZG9iZSBQaG90b3Nob3AgMjQuMSAoTWFjaW50b3NoKSIgc3RFdnQ6Y2hhbmdlZD0iLyIvPiA8L3JkZjpTZXE+IDwveG1wTU06SGlzdG9yeT4g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"/>
                                    </defs>
                                </svg>
                            </div>

                            <hr class="payouts-and-comparison__separator">

                            <?php
                            $payouts_and_comparison__list = [
                                    'Smooth and reliable, easy-to-use',
                                    'Customizable charts, hotkeys, layouts',
                                    'Trade on mobile, desktop or the web',
                                    'Optimized for Tradeify'
                            ];
                            ?>

                            <ul class="payouts-and-comparison__list list-unstyled">
                                <?php foreach ($payouts_and_comparison__list as $payouts_and_comparison__value) : ?>
                                    <li>
                                        <img class="payouts-and-comparison__checked"
                                             src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/checked-circle.svg'); ?>"
                                             alt="checked circle">

                                        <p>
                                            <?= $payouts_and_comparison__value; ?>
                                        </p>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <a href="#get-funded" class="btn mega-btn-md mega-btn-primary-md">
                                Trade on Tradovate
                            </a>
                        </div>
                    </div>
                </div>

                <div class="comparison">
                    <h2 class="comparison__header">No Hidden Rules or Extra Fees</h2>
                    <div class="comparison__table">
                        <div class="comparison__column comparison__column--others">
                            <div>

                            </div>
                            <?php
                            $comparison__list = [
                                    []
                            ]

                            ?>

                        </div>
                        <div class="comparison__column comparison__column--megat">...</div>
                    </div>
                    <div class="comparison__stats">
                        <div class="comparison__stat">1,000+ happy traders</div>
                        <div class="comparison__stat">75% of traders buy again</div>
                        <div class="comparison__stat">$70 MILLION+ paid out</div>
                    </div>
                </div>
            </div>
        </section>
    </main>

<?php get_footer('landing-page-bs'); ?>