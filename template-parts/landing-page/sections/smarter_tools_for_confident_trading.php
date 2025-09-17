<?php

$items = [
        [
                'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<mask id="mask0_4853_1038" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
<rect width="24" height="24" fill="#D9D9D9"/>
</mask>
<g mask="url(#mask0_4853_1038)">
<path d="M8 22L9 15H4L13 2H15L14 10H20L10 22H8Z" fill="#2DD4BF"/>
</g>
</svg>',
                'title' => 'Fast',
                'subtitle' => 'Trade faster with cutting-edge technology designed for success.'
        ],
        [
                'icon' => '<svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<mask id="mask0_4853_1043" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="25" height="24">
<rect x="0.333374" width="24" height="24" fill="#D9D9D9"/>
</mask>
<g mask="url(#mask0_4853_1043)">
<path d="M9.33337 15V9H15.3334V15H9.33337ZM9.33337 21V19H7.33337C6.78337 19 6.31254 18.8042 5.92087 18.4125C5.52921 18.0208 5.33337 17.55 5.33337 17V15H3.33337V13H5.33337V11H3.33337V9H5.33337V7C5.33337 6.45 5.52921 5.97917 5.92087 5.5875C6.31254 5.19583 6.78337 5 7.33337 5H9.33337V3H11.3334V5H13.3334V3H15.3334V5H17.3334C17.8834 5 18.3542 5.19583 18.7459 5.5875C19.1375 5.97917 19.3334 6.45 19.3334 7V9H21.3334V11H19.3334V13H21.3334V15H19.3334V17C19.3334 17.55 19.1375 18.0208 18.7459 18.4125C18.3542 18.8042 17.8834 19 17.3334 19H15.3334V21H13.3334V19H11.3334V21H9.33337ZM17.3334 17V7H7.33337V17H17.3334Z" fill="#131210"/>
</g>
</svg>',
                'title' => 'Powerful',
                'subtitle' => 'Leverage powerful tools to maximize your trading potential.'
        ],
        [
                'icon' => '<svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">...SVG OMITIDO POR EXTENSO...</svg>',
                'title' => 'Security',
                'subtitle' => 'Prioritize your trading with top-tier security and protection.'
        ],
        [
                'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">...SVG OMITIDO POR EXTENSO...</svg>',
                'title' => 'Customization',
                'subtitle' => 'Tailor your trading experience with seamless customization options.'
        ],
        [
                'icon' => '<svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">...SVG OMITIDO POR EXTENSO...</svg>',
                'title' => 'Control',
                'subtitle' => 'ake full control of your trades with intuitive, precise tools.'
        ],
        [
                'icon' => '<svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">...SVG OMITIDO POR EXTENSO...</svg>',
                'title' => 'User friendly',
                'subtitle' => 'Enjoy a user-friendly interface designed for effortless trading.'
        ]
];

?>
<section id="features" class="tw-px-4 tw-py-12">
    <div class="tw-pb-4 tw-text-center tw-text-white tw-text-[40px] tw-font-light tw-uppercase tw-leading-[48px]">
        Smarter Tools for Confident Trading
    </div>

    <div class="tw-mx-auto tw-pb-12 tw-max-w-[612px] tw-text-center tw-text-xl tw-leading-8 tw-font-medium tw-text-stone-400 md:tw-max-w-[780px]">
        Unlock advanced features built to boost performance, enhance control, and create a fully customized,
        secure, and seamless trading experience.
    </div>

    <div class="tw-grid md:tw-justify-center md:tw-grid-cols-2 lg:tw-grid-cols-[335px_335px_335px] tw-gap-4">
        <?php foreach ($items as $index => $item) : ?>
            <div class="tw-bg-[#1e1e1e] tw-border tw-border-transparent tw-rounded-2xl tw-w-full tw-group tw-p-8 <?= $index == 1 ? 'tw-bg-teal-400 active' : '' ?>">
                <div class="tw-flex tw-items-center tw-gap-2">
                    <?= $item['icon'] ?>
                    <div
                            class="tw-justify-start tw-text-teal-400 group-[.active]:tw-text-[#131210] tw-text-xl tw-font-bold tw-leading-loose">
                        <?= $item['title'] ?>
                    </div>
                </div>
                <div
                        class="tw-justify-start tw-text-stone-400 group-[.active]:tw-text-[#131210] tw-text-base tw-font-medium tw-leading-normal">
                    <?= $item['subtitle'] ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>