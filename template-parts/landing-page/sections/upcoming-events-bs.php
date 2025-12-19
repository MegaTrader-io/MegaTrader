<?php
$events_bs = [
        [
                'image' => get_template_directory_uri() . '/assets/img/landing-page/upcoming-events/london-trader-1.png',
                'title' => 'London Trader Meetup',
                'date' => 'Friday • 26 September • 7pm',
                'location' => 'London, UK',
        ],
        [
                'image' => get_template_directory_uri() . '/assets/img/landing-page/upcoming-events/london-trader-2.png',
                'title' => 'London Trader Meetup',
                'date' => 'Friday • 26 September • 7pm',
                'location' => 'London, UK',
        ],
        [
                'image' => get_template_directory_uri() . '/assets/img/landing-page/upcoming-events/london-trader-3.png',
                'title' => 'London Trader Meetup',
                'date' => 'Friday • 26 September • 7pm',
                'location' => 'London, UK',
        ]
];

?>

<section class="upcoming-bs text-white">
    <div class="upcoming-bs__container landing-bs-container">
        <header class="section-header text-center">
            <h2 class="section-header__title">
                UPCOMING <span>EVENTS</span>
            </h2>
        </header>

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
    </div>
</section>