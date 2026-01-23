<section class="path-to-payout-bs">
    <div class="path-to-payout-bs__container landing-bs-container">
        <header class="section-header text-center">
            <h2 class="section-header__title">
                A CLEAR PATH TO YOUR NEXT <span>PAYOUT</span>
            </h2>

            <p class="section-header__subtitle">
                Transparent rules, real-time metrics, and clear milestones that show exactly what’s needed to reach your
                next payout.
            </p>
        </header>

        <div class="path-to-payout-bs__steps" style="--margin-bottom-path-to-payout-item__step: 32px">
            <div class="path-to-payout-bs__mobile-card path-to-payout-bs__item path-to-payout-bs__item--first-card">
                <div class="path-to-payout-item__step">START TODAY</div>
                <div class="path-to-payout-bs__desktop-card">
                    <h2 class="path-to-payout-item__title">Start trading and earn payouts</h2>
                    <p class="path-to-payout-item__subtitle">Activate a Challenge or Funded Account to begin</p>
                </div>
            </div>
            <div class="path-to-payout-bs__mobile-card path-to-payout-bs__item path-to-payout-bs__item--middle-card">
                <div class="path-to-payout-item__step">FIRST PAYOUT</div>
                <div class="path-to-payout-bs__desktop-card">
                    <h2 class="path-to-payout-item__title">Reach your first milestone</h2>
                    <p class="path-to-payout-item__subtitle">Withdraw profits and unlock consistent cycles.</p>
                </div>
            </div>

            <div class="path-to-payout-bs__item--last-card--wrapper" style="--path-to-payout-last-line-px: 0">
                <div class="path-to-payout-bs__mobile-card path-to-payout-bs__item path-to-payout-bs__item--last-card">
                    <div class="path-to-payout-item__step">LIVE ACCOUNTS</div>
                    <div class="path-to-payout-bs__desktop-card">
                        <h2 class="path-to-payout-item__title">Access to real funding capital</h2>
                        <p class="path-to-payout-item__subtitle">Coming soon to MegaTrader.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="trader-benefits-bs">
            <div class="trader-benefits-bs__content">
                <div class="trader-benefits-bs__title">
                    WHAT YOU GET AS A TRADER
                </div>

                <div class="trader-benefits-bs__list">
                    <div class="trader-benefits-bs__item">
                        <img
                                class="trader-benefits-bs__icon"
                                src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/checked-circle-warning.svg'); ?>"
                        />
                        <div class="trader-benefits-bs__text">
                            <div class="trader-benefits-bs__subtitle">
                                Real Capital Simulation
                            </div>
                            <div class="trader-benefits-bs__description">
                                Trade with real market data and professional-grade performance tracking.
                            </div>
                        </div>
                    </div>

                    <div class="trader-benefits-bs__item">
                        <img
                                class="trader-benefits-bs__icon"
                                src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/checked-circle-warning.svg'); ?>"
                        />
                        <div class="trader-benefits-bs__text">
                            <div class="trader-benefits-bs__subtitle">
                                Fast, Reliable Payouts
                            </div>
                            <div class="trader-benefits-bs__description">
                                Request payouts with 90% profit share and quick processing times.
                            </div>
                        </div>
                    </div>

                    <div class="trader-benefits-bs__item">
                        <img
                                class="trader-benefits-bs__icon"
                                src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/checked-circle-warning.svg'); ?>"
                        />
                        <div class="trader-benefits-bs__text">
                            <div class="trader-benefits-bs__subtitle">
                                Full Control and Flexibility
                            </div>
                            <div class="trader-benefits-bs__description">
                                Use add-ons like Anytime Payouts or Drawdown Buffer for more freedom.
                            </div>
                        </div>
                    </div>
                </div>

                <a data-menu="pricing" href="<?= home_url('#pricing') ?>"
                   class="btn-get-funded-now btn mega-btn-md mega-btn-primary-md">
                    GET FUNDED NOW
                </a>

                <div class="trader-benefits-bs__link">
                    Learn more about funded trading
                </div>
            </div>

            <div class="trader-benefits-bs__image">
                <div class="trader-benefits-bs__image-placeholder">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/landing-page/perks.jpg" alt="WHAT YOU GET AS A TRADER"/>
                </div>
            </div>
        </div>

    </div>
</section>

<script>
    (function initStepWidthObserver() {
        const wrapper = document.querySelector('.path-to-payout-bs__item--last-card--wrapper');
        const step = wrapper?.querySelector('.path-to-payout-item__step');

        if (!wrapper || !step) {
            console.warn('Step width observer: elements not found');
            return;
        }

        let rafId = null;

        const updateStepWidth = () => {
            cancelAnimationFrame(rafId);

            rafId = requestAnimationFrame(() => {
                try {
                    const wrapperRect = wrapper.getBoundingClientRect();
                    const stepRect = step.getBoundingClientRect();

                    const width = parseInt(Math.max(0, stepRect.x - wrapperRect.x)) + 1;

                    wrapper.style.setProperty('--path-to-payout-last-line-px', `${width}px`);
                } catch (error) {
                    console.error('Error calculating step width:', error);
                }
            });
        };

        // 1️⃣ Observa cambios de tamaño
        const resizeObserver = new ResizeObserver(updateStepWidth);
        resizeObserver.observe(wrapper);
        resizeObserver.observe(step);

        // 2️⃣ Observa cambios en el DOM (clases, nodos, etc.)
        const mutationObserver = new MutationObserver(updateStepWidth);
        mutationObserver.observe(wrapper, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['class', 'style']
        });

        // 3️⃣ Recalcula al cargar
        updateStepWidth();

        // 4️⃣ Fallback para resize global
        window.addEventListener('resize', updateStepWidth);
    })();
</script>