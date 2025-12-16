<section class="testimonials-bs text-white">
    <div class="testimonials-bs__container landing-bs-container">
        <div class="testimonials-bs__layout">
            <!-- Columna izquierda -->
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
                        <img
                                src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/checked-circle.svg'); ?>"
                                alt=""
                                class="testimonials-bs__feature-icon"
                        />
                        <span class="testimonials-bs__feature-text">Instant simulated funding</span>
                    </li>
                    <li class="testimonials-bs__feature">
                        <img
                                src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/checked-circle.svg'); ?>"
                                alt=""
                                class="testimonials-bs__feature-icon"
                        />
                        <span class="testimonials-bs__feature-text">Fastest customer service</span>
                    </li>
                    <li class="testimonials-bs__feature">
                        <img
                                src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/checked-circle.svg'); ?>"
                                alt=""
                                class="testimonials-bs__feature-icon"
                        />
                        <span class="testimonials-bs__feature-text">Clear and straightforward</span>
                    </li>
                </ul>

                <a href="#get-funded" class="testimonials-bs__cta btn mega-btn-md mega-btn-primary-md">
                    GET FUNDED NOW
                </a>
            </div>

            <!-- Columna derecha -->
            <div class="testimonials-bs__grid">
                <!-- Columna izquierda de tarjetas -->
                <div class="testimonials-bs__col">
                    <!-- Card 1 -->
                    <div class="testimonials-bs__card">
                        <div class="testimonials-bs__stars">
                          <?php for ($i = 0; $i < 5; $i++): ?>
                              <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/start.svg'); ?>"/>
                          <?php endfor; ?>
                        </div>
                        <p class="testimonials-bs__text">
                            "Education is the passport to the future, for tomorrow belongs to those who prepare for it
                            today."
                        </p>
                        <div class="testimonials-bs__author">
                            <img src="https://placehold.co/64x64" alt="Angela Kim" class="testimonials-bs__author-img"/>
                            <div class="testimonials-bs__author-info">
                                <span class="testimonials-bs__author-name">Angela Kim</span>
                                <span class="testimonials-bs__author-country">United States</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="testimonials-bs__card">
                        <div class="testimonials-bs__stars">
                          <?php for ($i = 0; $i < 5; $i++): ?>
                              <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/start.svg'); ?>"/>
                          <?php endfor; ?>
                        </div>
                        <p class="testimonials-bs__text">
                            Excellent Support and very fast with the payouts I have another funded account with another
                            prop-firms but
                            MEGA TRADER let me totally impressed with every little thing…!!
                        </p>
                        <div class="testimonials-bs__author">
                            <div class="testimonials-bs__author-initials" style="background: #FFB34A;">DE</div>
                            <div class="testimonials-bs__author-info">
                                <span class="testimonials-bs__author-name">Denskyn</span>
                                <span class="testimonials-bs__author-country">US</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="testimonials-bs__card">
                        <div class="testimonials-bs__stars">
                          <?php for ($i = 0; $i < 5; $i++): ?>
                              <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/start.svg'); ?>"/>
                          <?php endfor; ?>
                        </div>
                        <p class="testimonials-bs__text">
                            The best propfirm really loving it currently on evals and their platform is being upgraded.
                            CS is the best
                            as well and the ceo is always there to answer any questions I have. I highly recommend this
                            prop firm
                        </p>
                        <div class="testimonials-bs__author">
                            <div class="testimonials-bs__author-initials" style="background: #FFB34A;">JO</div>
                            <div class="testimonials-bs__author-info">
                                <span class="testimonials-bs__author-name">Joe</span>
                                <span class="testimonials-bs__author-country">US</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4 -->
                    <div class="testimonials-bs__card">
                        <div class="testimonials-bs__stars">
                          <?php for ($i = 0; $i < 5; $i++): ?>
                              <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/start.svg'); ?>"/>
                          <?php endfor; ?>
                        </div>
                        <p class="testimonials-bs__text">
                            "The only thing that holds you back is the story you tell yourself about why you can't."
                        </p>
                        <div class="testimonials-bs__author">
                            <img src="https://placehold.co/64x64" alt="Robert Fox" class="testimonials-bs__author-img"/>
                            <div class="testimonials-bs__author-info">
                                <span class="testimonials-bs__author-name">Robert Fox</span>
                                <span class="testimonials-bs__author-country">United States</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna derecha de tarjetas -->
                <div class="testimonials-bs__col">
                    <!-- Card 5 -->
                    <div class="testimonials-bs__card">
                        <div class="testimonials-bs__stars">
                          <?php for ($i = 0; $i < 5; $i++): ?>
                              <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/start.svg'); ?>"/>
                          <?php endfor; ?>
                        </div>
                        <p class="testimonials-bs__text">"The only thing that will redeem mankind is cooperation."</p>
                        <div class="testimonials-bs__author">
                            <img src="https://placehold.co/64x64" alt="Cody Fisher"
                                 class="testimonials-bs__author-img"/>
                            <div class="testimonials-bs__author-info">
                                <span class="testimonials-bs__author-name">Cody Fisher</span>
                                <span class="testimonials-bs__author-country">United States</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 6 -->
                    <div class="testimonials-bs__card">
                        <div class="testimonials-bs__stars">
                          <?php for ($i = 0; $i < 5; $i++): ?>
                              <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/start.svg'); ?>"/>
                          <?php endfor; ?>
                        </div>
                        <p class="testimonials-bs__text">
                            Great firm. Agents willing to help and CEO always making sure you are satisfy with the
                            services. FAST
                            payouts onces is approved. I will continue using this firm for my trading career.
                        </p>
                        <div class="testimonials-bs__author">
                            <img
                                    src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/aldo-ramirez.png'); ?>"
                                    alt="Aldo Ramírez"
                                    class="testimonials-bs__author-img"
                            />
                            <div class="testimonials-bs__author-info">
                                <span class="testimonials-bs__author-name">Aldo Ramírez</span>
                                <span class="testimonials-bs__author-country">US</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 7 -->
                    <div class="testimonials-bs__card">
                        <div class="testimonials-bs__stars">
                          <?php for ($i = 0; $i < 5; $i++): ?>
                              <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/start.svg'); ?>"/>
                          <?php endfor; ?>
                        </div>
                        <p class="testimonials-bs__text">
                            As a new company I was skeptical but yes I received a payout and I’m happy overall. The
                            rules are not too
                            complicated. They have a few more rules than other firms but also offer EOD drawdown and the
                            lowest price
                            on a funded account I’ve ever seen that’s not a scam. If you are a newer trader or just
                            looking to try
                            something new, yes I would definitely recommend them.
                        </p>
                        <div class="testimonials-bs__author">
                            <div class="testimonials-bs__author-initials" style="background: #FFB34A;">TG</div>
                            <div class="testimonials-bs__author-info">
                                <span class="testimonials-bs__author-name">TG</span>
                                <span class="testimonials-bs__author-country">US</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 8 -->
                    <div class="testimonials-bs__card">
                        <div class="testimonials-bs__stars">
                          <?php for ($i = 0; $i < 5; $i++): ?>
                              <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/start.svg'); ?>"/>
                          <?php endfor; ?>
                        </div>
                        <p class="testimonials-bs__text">
                            "We are not human beings having a spiritual experience; we are spiritual beings having a
                            human experience."
                        </p>
                        <div class="testimonials-bs__author">
                            <img src="https://placehold.co/64x64" alt="Jerome Bell"
                                 class="testimonials-bs__author-img"/>
                            <div class="testimonials-bs__author-info">
                                <span class="testimonials-bs__author-name">Jerome Bell</span>
                                <span class="testimonials-bs__author-country">United States</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
