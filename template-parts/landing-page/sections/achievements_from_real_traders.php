<?php
$verified_payouts_base_url = get_template_directory_uri() . '/assets/img/landing-page/certificates';
$certificates = [
        ['img' => $verified_payouts_base_url . '/Certificate-1.png', 'title' => 'Mega Certified Trader'],
        ['img' => $verified_payouts_base_url . '/Certificate-2.png', 'title' => 'Mega Certified Trader'],
        ['img' => $verified_payouts_base_url . '/Certificate-3.png', 'title' => 'Mega Certified Trader'],
        ['img' => $verified_payouts_base_url . '/Certificate-4.png', 'title' => 'Mega Certified Trader'],
        ['img' => $verified_payouts_base_url . '/Certificate-5.png', 'title' => 'Mega Certified Trader'],
        ['img' => $verified_payouts_base_url . '/Certificate-6.png', 'title' => 'Mega Certified Trader'],
        ['img' => $verified_payouts_base_url . '/Certificate-7.png', 'title' => 'Mega Certified Trader'],
        ['img' => $verified_payouts_base_url . '/Certificate-8.png', 'title' => 'Mega Certified Trader'],
        ['img' => $verified_payouts_base_url . '/Certificate-9.png', 'title' => 'Mega Certified Trader'],
        ['img' => $verified_payouts_base_url . '/Certificate-10.png', 'title' => 'Mega Certified Trader'],
]

?>

<section class="md:tw-hidden tw-my-6 tw-mb-5">
    <h2 class="tw-flex tw-mb-0 tw-flex-col tw-text-[40px] tw-text-center tw-text-white">
        <span class="tw-font-light tw-leading-[48px]">ACHIEVEMENTS</span>
        <span class="tw-font-light tw-uppercase tw-leading-[48px] tw-tracking-tighter">FROM REAL TRADERS</span>
    </h2>

    <div class="tw-m-auto tw-py-5 tw-w-[362px] tw-text-center tw-justify-start text-stone-400 tw-text-lg tw-font-normal tw-leading-6 tw-tracking-tight">
        Every certificate represents a trader reaching their next level. Explore the success stories and see what’s
        possible with MegaTrader.
    </div>

    <div id="certifications" class="glide tw-mt-10">
        <div class="slider__track glide__track tw-mx-4" data-glide-el="track">
            <ul class="slider__slides glide__slides">
                <?php foreach ($certificates as $key => $certificate) : ?>
                    <li class="md:tw-w-[calc(100vw-64px)]] tw-flex">
                        <img class="tw-w-full"
                             src="<?php echo esc_url($certificate['img']); ?>"/>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="tw-flex tw-justify-center tw-px-5 tw-mt-5 tw-py-2.5" data-glide-el="controls[nav]">
            <?php foreach ($certificates as $key => $certify) : ?>
                <button class="slider__bullet glide__bullet" data-glide-dir="=<?= $key ?>"></button>
            <?php endforeach; ?>
        </div>
    </div>
</section>
