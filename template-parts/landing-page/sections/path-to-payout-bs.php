<section class="path-to-payout-bs">
    <div class="path-to-payout-bs__container landing-bs-container">
        <header class="section-header text-center">
            <h2 class="section-header__title">
                A CLEAR PATH TO YOUR NEXT <span>PAYOUT</span>
            </h2>
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