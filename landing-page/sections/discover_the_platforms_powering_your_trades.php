<?php

$products_data = get_products_with_attributes();
$attributes = $products_data['attributes'] ?? [];
$platforms = [];

foreach ($attributes as $attr) {
    switch ($attr['taxonomy']) {
        case 'pa_platform':
            $attribute_meta = $attr['attribute_meta'] ?? [];
            $parsed = parse_attribute_meta($attribute_meta);
            $attr['order'] = $parsed['config']['order']['value'] ?? 5;
            $platforms[] = $attr;
            break;
    }
}

$features = [
        [
                'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    <mask id="mask0_4890_4470" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
        <rect width="24" height="24" fill="#D9D9D9"/>
    </mask>
    <g mask="url(#mask0_4890_4470)">
        <path d="M2 22V4C2 3.45 2.19583 2.97917 2.5875 2.5875C2.97917 2.19583 3.45 2 4 2H20C20.55 2 21.0208 2.19583 21.4125 2.5875C21.8042 2.97917 22 3.45 22 4V16C22 16.55 21.8042 17.0208 21.4125 17.4125C21.0208 17.8042 20.55 18 20 18H6L2 22ZM6 14H14V12H6V14ZM6 11H18V9H6V11ZM6 8H18V6H6V8Z" fill="#2DD4BF"/>
    </g>
</svg>',
                'title' => 'Email and web support'
        ],
        [
                'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    <mask id="mask0_4890_4515" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
        <rect width="24" height="24" fill="#D9D9D9"/>
    </mask>
    <g mask="url(#mask0_4890_4515)">
        <path d="M5 22C4.45 22 3.97917 21.8042 3.5875 21.4125C3.19583 21.0208 3 20.55 3 20V6C3 5.45 3.19583 4.97917 3.5875 4.5875C3.97917 4.19583 4.45 4 5 4H6V2H8V4H16V2H18V4H19C19.55 4 20.0208 4.19583 20.4125 4.5875C20.8042 4.97917 21 5.45 21 6V20C21 20.55 20.8042 21.0208 20.4125 21.4125C20.0208 21.8042 19.55 22 19 22H5ZM5 20H19V10H5V20Z" fill="#2DD4BF"/>
    </g>
</svg>',
                'title' => 'Create plans based on your needs'
        ],
        [
                'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    <mask id="mask0_4890_4520" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
        <rect width="24" height="24" fill="#D9D9D9"/>
    </mask>
    <g mask="url(#mask0_4890_4520)">
        <path d="M6 22C5.45 22 4.97917 21.8042 4.5875 21.4125C4.19583 21.0208 4 20.55 4 20V4C4 3.45 4.19583 2.97917 4.5875 2.5875C4.97917 2.19583 5.45 2 6 2H14L20 8V20C20 20.55 19.8042 21.0208 19.4125 21.4125C19.0208 21.8042 18.55 22 18 22H6ZM13 9H18L13 4V9Z" fill="#2DD4BF"/>
    </g>
</svg>',
                'title' => 'Your data in detailed summary'
        ],
        [
                'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    <mask id="mask0_4890_4525" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
        <rect width="24" height="24" fill="#D9D9D9"/>
    </mask>
    <g mask="url(#mask0_4890_4525)">
        <path d="M6 22C5.45 22 4.97917 21.8042 4.5875 21.4125C4.19583 21.0208 4 20.55 4 20V10C4 9.45 4.19583 8.97917 4.5875 8.5875C4.97917 8.19583 5.45 8 6 8H7V6C7 4.61667 7.4875 3.4375 8.4625 2.4625C9.4375 1.4875 10.6167 1 12 1C13.3833 1 14.5625 1.4875 15.5375 2.4625C16.5125 3.4375 17 4.61667 17 6V8H18C18.55 8 19.0208 8.19583 19.4125 8.5875C19.8042 8.97917 20 9.45 20 10V20C20 20.55 19.8042 21.0208 19.4125 21.4125C19.0208 21.8042 18.55 22 18 22H6ZM12 17C12.55 17 13.0208 16.8042 13.4125 16.4125C13.8042 16.0208 14 15.55 14 15C14 14.45 13.8042 13.9792 13.4125 13.5875C13.0208 13.1958 12.55 13 12 13C11.45 13 10.9792 13.1958 10.5875 13.5875C10.1958 13.9792 10 14.45 10 15C10 15.55 10.1958 16.0208 10.5875 16.4125C10.9792 16.8042 11.45 17 12 17ZM9 8H15V6C15 5.16667 14.7083 4.45833 14.125 3.875C13.5417 3.29167 12.8333 3 12 3C11.1667 3 10.4583 3.29167 9.875 3.875C9.29167 4.45833 9 5.16667 9 6V8Z" fill="#2DD4BF"/>
    </g>
</svg>',
                'title' => 'Secure and fast'
        ],
        [
                'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    <mask id="mask0_4890_5001" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
        <rect width="24" height="24" fill="#D9D9D9"/>
    </mask>
    <g mask="url(#mask0_4890_5001)">
        <path d="M4 19V17H6V10C6 8.61667 6.41667 7.3875 7.25 6.3125C8.08333 5.2375 9.16667 4.53333 10.5 4.2V3.5C10.5 3.08333 10.6458 2.72917 10.9375 2.4375C11.2292 2.14583 11.5833 2 12 2C12.4167 2 12.7708 2.14583 13.0625 2.4375C13.3542 2.72917 13.5 3.08333 13.5 3.5V4.2C14.8333 4.53333 15.9167 5.2375 16.75 6.3125C17.5833 7.3875 18 8.61667 18 10V17H20V19H4ZM12 22C11.45 22 10.9792 21.8042 10.5875 21.4125C10.1958 21.0208 10 20.55 10 20H14C14 20.55 13.8042 21.0208 13.4125 21.4125C13.0208 21.8042 12.55 22 12 22Z" fill="#2DD4BF"/>
    </g>
</svg>',
                'title' => 'Create custom alerts'
        ],
];

?>

<section id="feature-discover-the-platforms" class="lg:tw-flex lg:tw-gap-12 tw-space-y-12 lg:tw-space-y-0 tw-px-4">
    <div class="tw-w-full tw-space-y-12">
        <div
                class="tw-justify-start tw-text-white tw-text-[40px] tw-font-light tw-uppercase tw-leading-[48px]">
            Discover the Platforms Powering Your Trades
        </div>
        <div
                class="tw-justify-start tw-text-stone-400 tw-text-xl tw-font-medium tw-leading-8">Explore
            the industry's leading trading platforms designed to empower your strategies and enhance your
            trading experience.
        </div>

        <div class="tw-space-y-2">
            <?php foreach ($features as $feature) : ?>
                <div class="tw-px-3 tw-py-2 tw-bg-mgt-dark tw-rounded-[64px] tw-inline-flex tw-justify-start tw-items-center tw-gap-2 tw-mr-2">
                    <div class="tw-w-6 tw-h-6 tw-relative">
                        <?= $feature['icon'] ?>
                    </div>
                    <div class="tw-justify-start tw-text-teal-400 tw-text-base tw-font-medium tw-leading-normal">
                        <?= $feature['title'] ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="mgt-card tw-w-full tw-py-8">
        <div class="tw-hidden md:tw-flex">
            <?php foreach (mt_sort_list_by_order($platforms) as $index => $platform): ?>
                <?php
                $id = 'id_' . $platform['slug'];
                $attribute_meta = $platform['attribute_meta'] ?? [];
                $parsed = parse_attribute_meta($attribute_meta);
                $badge_text = $parsed['config']['badge']['text'] ?? '';
                $is_coming_soon = mt_is_coming_soon($badge_text);
                ?>

                <label
                        for="<?= $id ?>"
                        class="peer/platform tw-gap-2 tw-flex md:tw-flex-col tw-w-full md:tw-items-center tw-p-4 tw-space-y-2 tw-cursor-pointer">
                    <input type="radio" class="tw-peer/platform tw-hidden" id="<?= $id ?>"
                           name="platform_option" value="<?= $platform['slug'] ?>" <?= $index == 0 ? 'checked' : '' ?>>
                    <img
                            src="<?php echo get_template_directory_uri(); ?>/assets/img/landing-page/discover-platform-icons/<?= $platform['slug'] ?>-off.png"
                            width="40"
                            class="tw-block peer-checked/platform:tw-hidden"
                            height="40" alt="<?= $platform['name'] ?>"/>
                    <img
                            src="<?php echo get_template_directory_uri(); ?>/assets/img/landing-page/discover-platform-icons/<?= $platform['slug'] ?>-on.png"
                            width="40"
                            class="tw-hidden peer-checked/platform:tw-block"
                            height="40" alt="<?= $platform['name'] ?>"/>

                    <div class="tw-text-center tw-justify-start tw-text-base tw-text-white tw-font-bold tw-leading-6">
                        <?= $platform['name'] ?>
                    </div>

                    <?php if ($is_coming_soon) : ?>
                        <div class='badge-secondary-sm tw-bg-white tw-tracking-tight tw-hidden tw-text-nowrap lg:tw-flex'>
                            <?= $badge_text ?>
                        </div>
                    <?php endif ?>
                </label>
            <?php endforeach; ?>
        </div>
        <div class="tw-block md:tw-hidden tw-relative tw-w-full">
            <select name="changePlan"
                    class="tw-w-full tw-text-white tw-py-3 tw-px-4 tw-pr-10 tw-font-medium tw-rounded-xl tw-border tw-border-neutral-700 bg-mgt-dark/70
            tw-appearance-none focus:tw-outline-none">
                <?php foreach ($platforms as $index => $platform): ?>
                    <option value="<?= $platform['slug'] ?>"> <?= $platform['name'] ?></option>
                <?php endforeach; ?>
            </select>
            <div
                    class="tw-absolute tw-inset-y-0 tw-right-3 tw-flex tw-items-center tw-pointer-events-none">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <mask id="mask0_5269_2288"
                          maskUnits="userSpaceOnUse" x="0" y="0"
                          width="24" height="24">
                        <rect width="24" height="24" fill="#D9D9D9"/>
                    </mask>
                    <g mask="url(#mask0_5269_2288)">
                        <path d="M12 15L7 10H17L12 15Z" fill="white"/>
                    </g>
                </svg>
            </div>
        </div>

        <?php foreach ($platforms as $index => $platform): ?>
            <div class="tw-px-4 tw-space-y-4 tw-mt-6 md:tw-mt-8 <?= ($index > 0 ? 'tw-hidden' : '') ?>"
                 data-platform-description="<?= $platform['slug'] ?>">
                <div class="tw-justify-start tw-text-white tw-text-xl tw-font-light tw-uppercase tw-leading-6">
                    <?= $platform['name'] ?>
                </div>
                <div
                        class="tw-justify-start tw-text-stone-400 tw-text-base tw-font-medium tw-leading-6">
                    <?= $platform['description'] ?>
                </div>
            </div>
        <?php endforeach; ?>
</section>