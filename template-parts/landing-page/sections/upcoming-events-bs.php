<?php
$events_bs = [
//        [
//                'image' => get_template_directory_uri() . '/assets/img/landing-page/upcoming-events/london-trader-1.png',
//                'title' => 'London Trader Meetup',
//                'date' => 'Friday • 26 September • 7pm',
//                'location' => 'London, UK',
//        ],
//        [
//                'image' => get_template_directory_uri() . '/assets/img/landing-page/upcoming-events/london-trader-2.png',
//                'title' => 'London Trader Meetup',
//                'date' => 'Friday • 26 September • 7pm',
//                'location' => 'London, UK',
//        ],
//        [
//                'image' => get_template_directory_uri() . '/assets/img/landing-page/upcoming-events/london-trader-3.png',
//                'title' => 'London Trader Meetup',
//                'date' => 'Friday • 26 September • 7pm',
//                'location' => 'London, UK',
//        ]
];

?>

<section class="upcoming-bs text-white">
    <div class="upcoming-bs__container landing-bs-container">
        <header class="section-header text-center">
            <h2 class="section-header__title pb-0">
                UPCOMING <span>EVENTS</span>
            </h2>
            <p class="section-header__subtitle">
                Stay informed with upcoming platform updates, trading events, and important announcements you won’t want to miss.
            </p>

            <p class="section-header__subtitle section-header__subtitle--only-mobile">
                Stay informed with upcoming platform updates, events, and important announcements.
            </p>
        </header>

        <?php if (count($events_bs) === 0): ?>
            <div class="upcoming-bs__no_events">
                <div role="alert" tabindex="-1" class="woocommerce-info mb-0">
                    There are currently no events available.
                </div>
            </div>
        <?php else: ?>
            <div class="events-bs">
                <div class="events-bs__list">
                    <?php foreach ($events_bs as $event) : ?>
                        <article class="events-bs__card">
                            <img class="events-bs__image"
                                 src="<?= $event['image'] ?>"
                                 alt="Event image"
                            />

                            <div class="events-bs__content">
                                <div class="events-bs__info">
                                    <h3 class="events-bs__title"><?= $event['title'] ?></h3>
                                    <div class="events-bs__details">
                                        <span class="events-bs__date"><?= $event['date'] ?></span>
                                        <span class="events-bs__location"><?= $event['location'] ?></span>
                                    </div>
                                </div>
                                <a href="javascript:void(0);" class="events-bs__button">
                                    <span class="events-bs__button-text">View details</span>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>