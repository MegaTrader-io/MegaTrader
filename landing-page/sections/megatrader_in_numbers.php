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

<section id="megatrader-numbers" class="space-y-4 px-4 pb-12">
    <div class="self-stretch text-center text-white text-[40px] font-light uppercase leading-[48px]">
        Megatrader in numbers
    </div>

    <div class="mx-auto max-w-[760px] text-center text-xl leading-8 font-medium text-stone-400 md:w-[760px]">
        See how our commitment to excellence delivers real payouts, consistent performance, and trader success.
    </div>

    <div class="bg-mgt-dark rounded-2xl md:grid md:grid-cols-12 lg:grid lg:grid-cols-5 !mt-12 lg:items-start lg:justify-between gap-3 px-4 py-8">
        <?php foreach ($items as $index => $item): ?>
            <?php
            $colClasses = 'flex-col justify-start items-center gap-2 lg:col-auto';
            if ($index <= 2) {
                $colClasses .= ' md:col-span-4';
            } else {
                $colClasses .= ' md:col-span-6';
            }
            if ($index === 3) {
                $colClasses .= ' md:col-start-4 md:col-end-7';
            }
            if ($index === 4) {
                $colClasses .= ' md:col-start-8 md:col-end-11';
            }

            $detailLines = explode('<br/>', $item['detail']);
            ?>

            <div class="<?= esc_attr($colClasses) ?>">
                <h3 class="text-center justify-start text-[#ffb34a] text-6xl font-light uppercase leading-[72px]">
                    <?= esc_html($item['title']) ?>
                </h3>

                <div class="text-center justify-start text-white text-xl font-medium leading-loose">
                    <?= esc_html($item['subtitle']) ?>
                </div>

                <div class="text-center justify-start text-stone-400 text-base font-medium leading-6">
                    <?php foreach ($detailLines as $line): ?>
                        <p><?= esc_html(trim($line)) ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
