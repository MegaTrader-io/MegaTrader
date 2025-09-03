<?php
$products_data = get_products_with_attributes();
$attributes = $products_data['attributes'] ?? [];
$platforms = [];

foreach ($attributes as $attr) {
    switch ($attr['taxonomy']) {
        case 'pa_platform':
            $attribute_meta = $attr['attribute_meta'] ?? [];

            $parsed = parse_attribute_meta($attribute_meta);
            $badge_text = $parsed['config']['badge']['text'] ?? '';

            $platforms[] = [
                    'name' => $attr['name'],
                    'slug' => $attr['slug'],
                    'order' => $parsed['config']['order']['value'] ?? 5,
                    'is_coming_soon' => mt_is_coming_soon($badge_text)
            ];

            break;
    }
}

$platforms_chunk_list = mt_sort_list_by_order($platforms);

?>

<section id="sponsor" class="tw-space-y-4 tw-px-8">
    <div class="tw-self-stretch tw-text-center tw-text-white tw-text-[40px] tw-font-light tw-uppercase tw-leading-[48px]">
        Trusted Platforms
    </div>

    <div
            class="tw-mx-auto lg:tw-max-w-[760px] tw-text-center tw-text-xl tw-leading-8 tw-font-medium tw-text-stone-400">
        Trade with confidence on industry-leading platforms trusted by
        professionals for their speed, reliability, and advanced trading
        capabilities.
    </div>

    <div
            class="tw-my-8 tw-flex tw-flex-col tw-items-center tw-gap-16 tw-py-12 md:tw-gap-12 lg:tw-my-auto lg:tw-flex lg:tw-flex-row lg:tw-gap-8 lg:tw-py-12 xl:tw-gap-16  <?= count($platforms) == 4 ? 'md:tw-grid md:tw-grid-cols-2 lg:tw-justify-between' : 'lg:tw-justify-center' ?>">
        <?php foreach (mt_sort_list_by_order($platforms) as $index => $platform): ?>
            <?php
            $is_coming_prefix = $platform['is_coming_soon'] ? '-coming-soon' : '';
            $image = $platform['slug'] ? "{$platform['slug']}$is_coming_prefix.svg" : '';
            $name = $platform['name'];

            $classes = [
                    0 => 'tw-contents md:tw-flex md:tw-justify-self-end',
                    1 => 'tw-contents md:tw-flex md:tw-justify-self-start',
                    2 => 'tw-contents md:tw-flex md:tw-justify-self-end',
                    3 => 'tw-contents md:tw-flex md:tw-justify-self-start',
            ];
            ?>

            <div class="<?= $classes[$index] ?>">
                <?php if ($image) : ?>
                    <img
                            src="<?php echo get_template_directory_uri(); ?>/assets/img/landing-page/platforms/<?= $image ?>"
                            alt="<?= $name ?> Sponsor Logo"
                    />
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>