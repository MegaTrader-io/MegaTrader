<?php

$mt_in_numbers = [
        [
                'title' => '+5,000',
                'subtitle' => 'Active Users',
                'detail' => 'A thriving trader community<br/>growing every week.',
        ],
        [
                'title' => '72%',
                'subtitle' => 'Conversion Rate',
                'detail' => 'Most challenge users<br/>eventually get funded.',
        ],
        [
                'title' => '+800',
                'subtitle' => 'Payouts Per Week',
                'detail' => 'Real traders paid regularly<br/>proven success.',
        ],
        [
                'title' => '90%',
                'subtitle' => 'Profit Split',
                'detail' => 'Keep most profits with<br/>top-tier splits.',
        ],
        [
                'title' => '4.8',
                'subtitle' => 'User Rating',
                'detail' => 'Highly rated by real users<br/>on trusted reviews.',
        ],
];
?>

<section id="megatrader-numbers" class="tw-order-2 tw-space-y-4 tw-px-4 tw-pb-12">
    <div class="tw-hidden md:tw-block">
        <h2 class="tw-text-center tw-mb-0 tw-text-white tw-text-[40px] tw-font-light tw-uppercase tw-leading-[48px]">
            Megatrader in numbers
        </h2>

        <p class="tw-mx-auto tw-text-center tw-mb-0 tw-max-w-[760px] tw-text-xl tw-leading-8 tw-font-medium tw-text-stone-400 md:tw-w-[760px]">
            See how our commitment to excellence delivers real payouts, consistent performance, and trader success.
        </p>
    </div>
    <div class="tw-hidden md:tw-block tw-bg-mgt-dark tw-rounded-2xl md:tw-grid md:tw-grid-cols-12 lg:tw-grid lg:tw-grid-cols-5 !tw-mt-12 lg:tw-items-start lg:tw-justify-between tw-gap-3 tw-px-4 tw-py-8">
        <?php foreach ($mt_in_numbers as $index => $item): ?>
            <?php
            $colClasses = 'tw-flex-col tw-justify-start tw-items-center tw-gap-2 lg:tw-col-auto';
            if ($index <= 2) {
                $colClasses .= ' md:tw-col-span-4';
            } else {
                $colClasses .= ' md:tw-col-span-6';
            }
            if ($index === 3) {
                $colClasses .= ' md:tw-col-start-4 md:tw-col-end-7';
            }
            if ($index === 4) {
                $colClasses .= ' md:tw-col-start-8 md:tw-col-end-11';
            }

            $detailLines = explode('<br/>', $item['detail']);
            ?>

            <div class="<?= esc_attr($colClasses) ?>">
                <h3 class="tw-text-center tw-justify-start tw-text-[#ffb34a] tw-text-6xl tw-font-light tw-uppercase tw-leading-[72px]">
                    <?= esc_html($item['title']) ?>
                </h3>

                <div class="tw-text-center tw-justify-start tw-text-white tw-text-xl tw-font-medium tw-leading-loose">
                    <?= esc_html($item['subtitle']) ?>
                </div>

                <div class="tw-text-center tw-justify-start tw-text-stone-400 tw-text-base tw-font-medium tw-leading-6">
                    <?php foreach ($detailLines as $line): ?>
                        <p class="tw-mb-0"><?= esc_html(trim($line)) ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="tw-block md:tw-hidden">
        <h2 class="tw-flex tw-mb-0 tw-flex-col tw-text-[40px] tw-text-center tw-text-white">
            <span class="tw-font-light tw-leading-[48px] tw-uppercase">MEGATRADER IN NUMBERS</span>
        </h2>

        <p class="tw-m-auto tw-pt-5 tw-mb-0 tw-pb-[31px] tw-max-w-[325px] tw-text-center tw-justify-start text-stone-400 tw-text-lg tw-font-normal tw-leading-6 tw-tracking-tight">
            See how our commitment to excellence delivers real payouts and consistent performance for traders at global
            scale.
        </p>
    </div>

    <div id="megatrader-in-numbers" class="glide tw-mt-10 md:tw-hidden">
        <div class="slider__track glide__track tw-relative" data-glide-el="track">
            <ul class="slider__slides glide__slides">
                <?php foreach ($mt_in_numbers as $key => $item) : ?>
                    <li>
                        <div class="mt-card !tw-pt-10 !tw-pb-[83px] !tw-gap-2.5">
                            <h3 class="tw-text-center tw-mb-0 tw-justify-start tw-text-[#ffb34a] tw-text-[73.33px] tw-font-light tw-uppercase tw-leading-[72px]">
                                <?= esc_html($item['title']) ?>
                            </h3>

                            <div class="tw-text-center tw-justify-start tw-text-white tw-text-2xl tw-font-medium tw-leading-loose">
                                <?= esc_html($item['subtitle']) ?>
                            </div>

                            <p class="tw-self-stretch tw-mb-0 tw-text-center tw-justify-start text-stone-400 tw-text-xl tw-font-medium font-['Roboto'] leading-[29.33px]">
                                <?= $item['detail'] ?>
                            </p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="tw-absolute tw-bottom-[20px] tw-w-full tw-m-auto">
                <div class="tw-flex tw-justify-center tw-px-5 tw-py-2.5 tw-full" data-glide-el="controls[nav]">
                    <?php foreach ($mt_in_numbers as $key => $mt_number) : ?>
                        <button class="slider__bullet glide__bullet !tw-w-[15px] !tw-h-[15px]" data-glide-dir="=<?= $key ?>"></button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
