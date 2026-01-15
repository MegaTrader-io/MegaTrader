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
    <div class="landing-bs-container verified-bs__container">
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const carouselSelector = '#verified-bs-carousel';
        const carouselEl = document.querySelector(carouselSelector);
        if (carouselEl) {
            function calculatePerPage() {
                const width = carouselEl.clientWidth;
                const slideWidth = document.documentElement.clientWidth <= 767 ? 276 : 378;
                return Math.max(1, Math.floor(width / slideWidth));
            }

            let splide = new Splide(carouselSelector, {
                type: 'loop',
                drag: 'free',
                pagination: false,
                arrows: false,
                focus: 'center',
                gap: '16px',
                perPage: calculatePerPage(),
                autoScroll: {
                    speed: 0.2, pauseOnHover: true, pauseOnFocus: true,
                },
            });

            splide.mount(window.splide.Extensions);

            window.addEventListener('resize', () => {
                const newPerPage = calculatePerPage();
                if (splide.options.perPage !== newPerPage) {
                    splide.options = {...splide.options, perPage: newPerPage};
                    splide.refresh();
                }
            });
        }
    });
</script>