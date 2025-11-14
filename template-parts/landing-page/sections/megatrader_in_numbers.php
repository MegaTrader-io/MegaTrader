<?php

$items = [
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
    <div class="tw-text-center tw-text-white tw-text-[40px] tw-font-light tw-uppercase tw-leading-[48px]">
        Megatrader in numbers
    </div>

    <div class="tw-mx-auto tw-max-w-[760px] tw-text-center tw-text-xl tw-leading-8 tw-font-medium tw-text-stone-400 md:tw-w-[760px]">
        See how our commitment to excellence delivers real payouts, consistent performance, and trader success.
    </div>

    <div class="tw-bg-mgt-dark tw-rounded-2xl md:tw-grid md:tw-grid-cols-12 lg:tw-grid lg:tw-grid-cols-5 !tw-mt-12 lg:tw-items-start lg:tw-justify-between tw-gap-3 tw-px-4 tw-py-8">
        <?php foreach ($items as $index => $item): ?>
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
</section>
