<?php

$people = [
        ['image' => 'user-1.png', 'name' => 'John Doe', 'role' => 'Founder and CEO',],
        ['image' => 'user-2.png', 'name' => 'John Doe', 'role' => 'CTO',],
        ['image' => 'user-3.png', 'name' => 'John Doe', 'role' => 'CMO',],
        ['image' => 'user-4.png', 'name' => 'John Doe', 'role' => 'CFO',],
        ['image' => 'user-5.png', 'name' => 'John Doe', 'role' => 'PR Lead']
]

?>

<section id="our_team" class="tw-space-y-4 tw-px-8">
    <div class="tw-self-stretch tw-text-center tw-text-white tw-text-[40px] tw-font-light tw-uppercase tw-leading-[48px]">
        Our Leadership & Team
    </div>

    <div class="tw-space-y-12">
        <div
                class="tw-mx-auto lg:tw-max-w-[860px] tw-text-center tw-text-xl tw-leading-8 tw-font-medium tw-text-stone-400">
            Meet the dedicated team behind MegaTrader, combining years of industry expertise with fresh innovation to
            deliver a platform built on fairness, transparency, and trader success worldwide.
        </div>

        <div class="tw-flex tw-justify-center tw-gap-4">
            <?php foreach ($people as $index => $person) : ?>
                <?php
                $url = get_template_directory_uri() . "/assets/img/landing-page/team/{$person['image']}";
                ?>
                <div class="tw-flex <?= $index % 2 == 0 ? 'tw-flex-col' : ' tw-flex-col-reverse' ?>">
                    <img src="<?= $url ?>" alt="<?= $person['name'] ?>" class="tw-w-[236px] tw-h-[360px]">
                    <div class="tw-p-2">
                        <div class="tw-justify-start tw-text-teal-400 tw-text-base tw-font-medium tw-leading-normal"><?= $person['name'] ?></div>
                        <div class="tw-justify-start tw-text-stone-400 tw-text-base tw-font-medium tw-leading-normal">
                            Founder and CEO
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>
    </div>


</section>