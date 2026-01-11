<?php
$testimonials_list = [
        [
                'text' => 'Education is the passport to the future, for tomorrow belongs to those who prepare for it today.',
                'author' => [
                        'name' => 'Angela Kim',
                        'country' => 'US',
                        'initials' => 'AK',
                        'color' => '#FFB34A',
                ],
        ],
        [
                'text' => 'Excellent Support and very fast with the payouts I have another funded account with another prop-firms but MEGA TRADER let me totally impressed with every little thing…!!',
                'author' => [
                        'name' => 'Denskyn',
                        'country' => 'US',
                        'initials' => 'DE',
                        'color' => '#FFB34A',
                ],
        ],
        [
                'text' => 'The best propfirm really loving it currently on evals and their platform is being upgraded. CS is the best as well and the ceo is always there to answer any questions I have. I highly recommend this prop firm',
                'author' => [
                        'name' => 'Joe',
                        'country' => 'US',
                        'initials' => 'JO',
                        'color' => '#FFB34A',
                ],
        ],
        [
                'text' => 'The only thing that holds you back is the story you tell yourself about why you can\'t.',
                'author' => [
                        'name' => 'Robert Fox',
                        'country' => 'US',
                        'initials' => 'RF',
                        'color' => '#FFB34A',
                ],
        ],
        [
                'text' => 'The only thing that will redeem mankind is cooperation.',
                'author' => [
                        'name' => 'Cody Fisher',
                        'country' => 'US',
                        'initials' => 'CF',
                        'color' => '#FFB34A'
                ],
        ],
        [
                'text' => 'Great firm. Agents willing to help and CEO always making sure you are satisfy with the services. FAST payouts onces is approved. I will continue using this firm for my trading career.',
                'author' => [
                        'name' => 'Aldo Ramírez',
                        'country' => 'US',
                        'image' => esc_url(get_template_directory_uri() . '/assets/img/landing-page/aldo-ramirez.png'),
                ],
        ],
        [
                'text' => 'As a new company I was skeptical but yes I received a payout and I’m happy overall. The rules are not too complicated. They have a few more rules than other firms but also offer EOD drawdown and the lowest price on a funded account I’ve ever seen that’s not a scam. If you are a newer trader or just looking to try something new, yes I would definitely recommend them.',
                'author' => [
                        'name' => 'TG',
                        'country' => 'US',
                        'initials' => 'TG',
                        'color' => '#FFB34A',
                ],
        ],
        [
                'text' => 'We are not human beings having a spiritual experience; we are spiritual beings having a human experience.',
                'author' => [
                        'name' => 'Jerome Bell',
                        'country' => 'US',
                        'initials' => 'JB',
                        'color' => '#FFB34A',
                ],
        ],
        [
                'text' => 'Great pricing compared to other prop firms. Clear rules, fair structure, and no unnecessary complexity.',
                'author' => [
                        'name' => 'Leo',
                        'country' => 'US',
                        'initials' => 'LE',
                        'color' => '#FFB34A',
                ],
        ],
        [
                'text' => 'MegaTrader stands out for its organization. From account setup to daily trading, everything was clearly laid out, which allowed me to focus on trading instead of worrying about unclear rules.',
                'author' => [
                        'name' => 'Grayson Wilson',
                        'country' => 'US',
                        'initials' => 'GW',
                        'color' => '#FFB34A',
                ],
        ],
        [
                'text' => 'The evaluation process felt fair and well designed. Performance metrics were easy to follow, and the platform tools helped me manage risk properly throughout my trading sessions.',
                'author' => [
                        'name' => 'Alex Watkins',
                        'country' => 'US',
                        'initials' => 'AW',
                        'color' => '#FFB34A',
                ],
        ],
        [
                'text' => 'What I appreciated most about MegaTrader was the clarity around expectations. Nothing felt hidden or confusing, and the documentation answered most of my questions before I even needed to contact support.',
                'author' => [
                        'name' => 'Daniel Wrigh',
                        'country' => 'US',
                        'initials' => 'DW',
                        'color' => '#FFB34A',
                ],
        ],
];

$testimonials_columns = array_chunk($testimonials_list, ceil(count($testimonials_list) / 2));
?>

<section class="testimonials-bs text-white">
    <div class="testimonials-bs__container landing-bs-container">
        <div class="testimonials-bs__layout">
            <div class="testimonials-bs__intro">
                <div class="testimonials-bs__trustpilot">
                    <?php get_template_part('template-parts/landing-page/sections/trustpilot-dummy-bs'); ?>
                </div>

                <h2 class="testimonials-bs__headline">5,000+ happy traders</h2>

                <p class="testimonials-bs__subheadline">
                    And over 3,000 decided to buy again, because of :
                </p>

                <ul class="testimonials-bs__features">
                    <li class="testimonials-bs__feature">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/checked-circle.svg'); ?>"
                             alt="" class="testimonials-bs__feature-icon"/>
                        <span class="testimonials-bs__feature-text">Instant simulated funding</span>
                    </li>
                    <li class="testimonials-bs__feature">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/checked-circle.svg'); ?>"
                             alt="" class="testimonials-bs__feature-icon"/>
                        <span class="testimonials-bs__feature-text">Fastest customer service</span>
                    </li>
                    <li class="testimonials-bs__feature">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/checked-circle.svg'); ?>"
                             alt="" class="testimonials-bs__feature-icon"/>
                        <span class="testimonials-bs__feature-text">Clear and straightforward</span>
                    </li>
                </ul>

                <a data-menu="pricing" href="<?= home_url('#pricing') ?>"
                   class="btn-get-funded-now testimonials-bs__cta btn mega-btn-md mega-btn-primary-md">
                    GET FUNDED NOW
                </a>
            </div>

            <div class="testimonials-bs__grid">
                <?php foreach ($testimonials_columns as $column): ?>
                    <div class="testimonials-bs__col">
                        <?php foreach ($column as $testimonial): ?>
                            <div class="testimonials-bs__card">
                                <div class="testimonials-bs__stars">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/start.svg'); ?>"/>
                                    <?php endfor; ?>
                                </div>

                                <p class="testimonials-bs__text">
                                    "<?php echo esc_html($testimonial['text']); ?>"
                                </p>

                                <div class="testimonials-bs__author">
                                    <?php if (!empty($testimonial['author']['image'])): ?>
                                        <img src="<?php echo esc_url($testimonial['author']['image']); ?>"
                                             alt="<?php echo esc_attr($testimonial['author']['name']); ?>"
                                             class="testimonials-bs__author-img"/>
                                    <?php elseif (!empty($testimonial['author']['initials'])): ?>
                                        <div class="testimonials-bs__author-initials"
                                             style="background: <?php echo esc_attr($testimonial['author']['color'] ?? '#FFB34A'); ?>;">
                                            <?php echo esc_html($testimonial['author']['initials']); ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="testimonials-bs__author-info">
                                        <span class="testimonials-bs__author-name"><?php echo esc_html($testimonial['author']['name']); ?></span>
                                        <span class="testimonials-bs__author-country"><?php echo esc_html($testimonial['author']['country']); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
