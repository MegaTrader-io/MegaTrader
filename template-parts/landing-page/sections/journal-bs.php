<?php

$journal_list = [
        [
                'btnText' => 'METRICS & PERFORMANCE',
                'title' => 'Check your account performance live.',
                'videoUrl' => site_url('/wp-content/uploads/2026/01/Account_Overall_Performance.mp4'),
        ],
        [
                'btnText' => 'TRADING JOURNAL',
                'title' => 'Track your trading activity every day.',
                'videoUrl' => site_url('/wp-content/uploads/2026/01/Trading_Journal.mp4'),
        ],
        [
                'btnText' => 'ACCOUNT SETTINGS',
                'title' => 'Control account settings from one place.',
                'videoUrl' => site_url('/wp-content/uploads/2026/01/Account_Settings.mp4'),
        ]
]

?>

<section class="journal-bs text-white">
    <div class="landing-bs-container">
        <header class="journal-bs__header">
            <div class="badge-duo">
                <div class="badge-duo__primary">
                    <div class="badge-duo__text">MEGATRADER EXCLUSIVE</div>
                </div>
                <div class="badge-duo__secondary">
                    <div class="badge-duo__text">Included In All Plans.</div>
                </div>
            </div>

            <h2 class="journal-bs__title">
                EVERYTHING TO TRACK AND MANAGE ACCOUNTS
            </h2>

            <div class="badge-group journal-bs__badge-group" data-glide-el="controls[nav]">
                <?php foreach ($journal_list as $key => $journal) : ?>
                    <a href="javascript:void(0);"
                       class="badge-group__item <?= $key == 0 ? 'is-active' : '' ?>"
                       data-glide-dir="=<?= $key ?>">
                        <div class="badge-group__text"><?= $journal['btnText'] ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        </header>

        <div class="verified-bs__wrapper">
            <div id="verified-bs-id" class="verified-bs__glide slider glide"
                 style="--verified-bs-slide-width: 0px; --verified-bs-slide-left: 0px;">
                <div class="slider__track glide__track" data-glide-el="track">
                    <ul class="slider__slides glide__slides">
                        <?php foreach ($journal_list as $journal) : ?>
                            <li class="slider__frame glide__slide">
                                <div class="mt-card journal-bs__card">
                                    <div class="mt-card__title">
                                        <?= $journal['title'] ?>
                                    </div>
                                    <div class="mt-card__body"
                                         style="position: relative; overflow: hidden;display: flex">
                                        <video src="<?= $journal['videoUrl'] ?>"
                                               autoplay="" muted="" loop="" playsinline="" preload="metadata"
                                               style="border-radius: 8px;">
                                            Tu navegador no soporta la reproducción de video.
                                        </video>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div data-glide-el="controls" class="glide__arrows">
                    <button class="glide__arrow glide__arrow--prev" data-glide-dir="<">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/arrow-left.svg'); ?>"
                             alt="control left">
                    </button>
                    <button class="glide__arrow glide__arrow--next" data-glide-dir=">">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-page/arrow-right.svg'); ?>"
                             alt="control right">
                    </button>
                </div>

                <div class="slider__bullets glide__bullets" data-glide-el="controls[nav]">
                    <?php foreach ($journal_list as $key => $journal) : ?>
                        <button class="slider__bullet glide__bullet" data-glide-dir="=<?= $key ?>"></button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const verifiedBsCarouselRoot = document.getElementById('verified-bs-id');

        const verifiedBsCarousel = new Glide(verifiedBsCarouselRoot, {
            type: 'slider',
            gap: 16,
            perView: 1,
            autoplay: false,
            hoverpause: false,
            rewind: false,
            animationDuration: 800,
            swipeThreshold: 80,
            dragThreshold: 120
        });

        verifiedBsCarousel.mount({
            Sizes: function CustomSizes(Glide, Components, Events) {

                const Sizes = {

                    setupSlides() {
                        const width = this.slideWidth + 'px';
                        const slides = Components.Html.slides;

                        for (let i = 0; i < slides.length; i++) {
                            slides[i].style.width = width;
                        }

                        // 🔹 Ejemplo: cambiar autoplay dinámicamente
                        if (document.documentElement.clientWidth <= 768 && !Glide.settings.autoplay) {
                            // Glide.update({type: 'slider'});
                        } else if (document.documentElement.clientWidth > 768 && Glide.settings.autoplay) {
                            // Glide.update({autoplay: false});
                        }
                    },

                    setupWrapper() {
                        Components.Html.wrapper.style.width = `${this.wrapperSize}px`;
                    },

                    remove() {
                        const slides = Components.Html.slides;

                        for (let i = 0; i < slides.length; i++) {
                            slides[i].style.width = '';
                        }

                        Components.Html.wrapper.style.width = '';
                    }
                };

                // ----------------------------
                // GETTERS OBLIGATORIOS
                // ----------------------------

                Object.defineProperty(Sizes, 'length', {
                    get() {
                        return Components.Html.slides.length;
                    }
                });

                Object.defineProperty(Sizes, 'width', {
                    get() {
                        return Components.Html.track.offsetWidth;
                    }
                });

                Object.defineProperty(Sizes, 'wrapperSize', {
                    get() {
                        return (this.slideWidth * this.length + Components.Gaps.grow + Components.Clones.grow);
                    }
                });

                Object.defineProperty(Sizes, 'slideWidth', {
                    get() {
                        // 🔹 Lógica adaptativa + límite máximo
                        const maxWidth = 715; // el máximo que tú desees
                        const horizontalPadding = 32; // margen lateral en mobile

                        let width = document.documentElement.clientWidth <= 768 ? document.documentElement.clientWidth - horizontalPadding : Math.min(document.documentElement.clientWidth * 0.85, maxWidth);

                        // 🔹 Variables CSS opcionales para efectos visuales
                        verifiedBsCarouselRoot.style.setProperty('--verified-bs-slide-width', width + 'px');

                        const points = document.querySelector('.verified-bs__glide .slider__bullets');
                        verifiedBsCarouselRoot.style.setProperty('--verified-bs-slide-left', (points?.getBoundingClientRect().x || 0) + 'px');

                        return width;
                    }
                });

                // ----------------------------
                // EVENTOS COMO EN LA LIBRERÍA
                // ----------------------------

                Events.on(['build.before', 'resize', 'update'], () => {
                    Sizes.setupSlides();
                    Sizes.setupWrapper();
                });

                Events.on('destroy', () => {
                    Sizes.remove();
                });

                Events.on('run.after', () => {
                    try {
                        const currentIndex = verifiedBsCarousel.index;
                        const badgeItems = document.querySelectorAll('.badge-group__item');

                        badgeItems.forEach((item, i) => {
                            if (i === currentIndex) {
                                item.classList.add('is-active');
                            } else {
                                item.classList.remove('is-active');
                            }
                        });
                    } catch (error) {
                        console.error('Error al actualizar badge activo:', error);
                    }
                });

                return Sizes;
            }
        });

        const badgeItems = document.querySelectorAll('.badge-group__item');

        badgeItems.forEach((element, index) => {
            element.addEventListener('click', (btn) => {
                badgeItems.forEach(link => link.classList.remove('is-active'));
                btn.currentTarget.classList.add('is-active');

                const glide = verifiedBsCarousel;

                if (glide) {
                    try {
                        const originalDuration = glide.settings.animationDuration;

                        glide.settings.animationDuration = 80;

                        glide.go(`=${index}`);

                        setTimeout(() => {
                            glide.settings.animationDuration = originalDuration;
                        }, 50);
                    } catch (error) {
                        console.error('Error al mover el carrusel:', error);
                    }
                } else {
                    console.warn('La instancia de Glide aún no está disponible.');
                }
            });
        });
    });
</script>
