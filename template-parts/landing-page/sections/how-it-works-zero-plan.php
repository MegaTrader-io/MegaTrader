<?php
$zero_plan_rules = [
        [
                'value' => '2%',
                'title' => 'Profit Target',
                'text' => 'Simple two percent goal to pass the challenge.'
        ],
        [
                'value' => '1 day',
                'title' => 'Min Trading Days',
                'text' => 'Qualify in as little as one trading day.'
        ],
        [
                'value' => 'None',
                'title' => 'Daily Loss Limit',
                'text' => 'No daily loss limits restrict your trading activity.'
        ],
        [
                'value' => 'EoD',
                'title' => 'Drawdown Mode',
                'text' => 'Risk is calculated at the end of each day.'
        ],
        [
                'value' => '90%',
                'title' => 'Profit Split',
                'text' => 'Keep ninety percent of profits once funded.'
        ]
];
?>

<section id="how-it-works" class="how-it-works-zero-plan">
    <div class="landing-bs-container">
        <header class="section-header text-center">
            <h2 class="section-header__title">
                <span>ZERO PLAN</span> PROGRAM RULES
            </h2>
            <p class="section-header__subtitle mb-0">
                Designed to remove barriers while keeping trading rules simple and disciplined. Start and earn funding by meeting clear, achievable goals.
            </p>
        </header>

        <div id="zero-plan-grid" class="zero-plan-grid slider glide">
            <div class="slider__track glide__track" data-glide-el="track">
                <ul class="slider__slides glide__slides">
                    <?php foreach ($zero_plan_rules as $rule): ?>
                        <?php
                        $value = isset($rule['value']) ? esc_html($rule['value']) : '';
                        $title = isset($rule['title']) ? esc_html($rule['title']) : '';
                        $text = isset($rule['text']) ? esc_html($rule['text']) : '';
                        ?>
                        <li class="slider__frame glide__slide">
                            <div class="zero-plan-grid__item">
                                <?php if ($value): ?>
                                    <div class="zero-plan-grid__value"><?= $value; ?></div>
                                <?php endif; ?>

                                <?php if ($title): ?>
                                    <div class="zero-plan-grid__title"><?= $title; ?></div>
                                <?php endif; ?>

                                <?php if ($text): ?>
                                    <div class="zero-plan-grid__text"><?= $text; ?></div>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="slider__bullets glide__bullets" data-glide-el="controls[nav]">
                <?php foreach ($zero_plan_rules as $key => $rule) : ?>
                    <button class="slider__bullet glide__bullet" data-glide-dir="=<?= $key ?>"></button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const carouselSelector = '#zero-plan-grid';
        let glideInstance = null;
        let isGlideMounted = false;

        const carouselEl = document.querySelector(carouselSelector);
        if (!carouselEl) {
            return;
        }

        function initGlide() {
            if (glideInstance) return;

            glideInstance = new Glide(carouselEl, {
                type: 'carousel',
                perView: 1,
                focusAt: 'center',
            });

            glideInstance.mount()
            isGlideMounted = true;
        }

        function destroyGlide() {
            if (glideInstance && isGlideMounted) {
                try {
                    glideInstance.destroy();
                    glideInstance = null;
                    window.tableSliderInstance = null;
                    isGlideMounted = false;

                    console.info('[Glide] destroyed');
                } catch (err) {
                    console.error('Error destroying Glide:', err);
                }
            }
        }

        function handleResize() {
            const viewportWidth = window.innerWidth;
            if (viewportWidth > 1024) {
                destroyGlide();
            } else {
                initGlide();
            }
        }

        if (window.innerWidth <= 1024) initGlide();

        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(handleResize, 250);
        });

        handleResize();
    });
</script>
