<?php
$verified_payouts_base_url = get_template_directory_uri() . '/assets/img/landing-page/verified-payouts';
$certifies = [
        ['img' => $verified_payouts_base_url . '/Certificate-passed-1.png', 'title' => 'Mega Certified Trader'],
        ['img' => $verified_payouts_base_url . '/Certificate-passed-2.png', 'title' => 'Mega Certified Trader'],
        ['img' => $verified_payouts_base_url . '/Certificate-widthdrawal-3.png', 'title' => 'Mega Certified Trader'],
        ['img' => $verified_payouts_base_url . '/Certificate-widthdrawal-4.png', 'title' => 'Mega Certified Trader'],
]

?>

<section class="md:tw-hidden tw-my-6">
    <h2 class="tw-flex tw-mb-0 tw-flex-col tw-text-[40px] tw-text-center tw-text-white">
        <span class="tw-font-light tw-leading-[48px]">ACHIEVEMENTS</span>
        <span class="tw-font-light tw-uppercase tw-leading-[48px] tw-tracking-tighter">FROM REAL TRADERS</span>
    </h2>

    <div class="tw-m-auto tw-py-5 tw-w-[362px] tw-text-center tw-justify-start text-stone-400 tw-text-lg tw-font-normal tw-leading-6 tw-tracking-tight">
        Every certificate represents a trader reaching their next level. Explore the success stories and see what’s
        possible with MegaTrader.
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
