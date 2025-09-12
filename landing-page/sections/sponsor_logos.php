<?php
$products_data = get_products_with_attributes();
$attributes = $products_data['attributes'] ?? [];
$platforms = [];
$has_badge = false;

foreach ($attributes as $attr) {
    switch ($attr['taxonomy']) {
        case 'pa_platform':
            $attribute_meta = $attr['attribute_meta'] ?? [];
            $parsed = parse_attribute_meta($attribute_meta);
            $badge = $parsed['config']['badge'] ?? [];

            if (count($badge)) {
                $has_badge = true;
            }

            $platforms[] = $attr;
            break;
    }
}
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
        <?php foreach ($platforms as $index => $platform): ?>
            <?php
            $image = "{$platform['slug']}.svg";
            $name = $platform['name'];

            $attribute_meta = $platform['attribute_meta'] ?? [];
            $parsed = parse_attribute_meta($attribute_meta);
            $badge = $parsed['config']['badge'] ?? [];

            $classes = [
                    0 => 'tw-contents md:tw-flex md:tw-justify-self-end',
                    1 => 'tw-contents md:tw-flex md:tw-justify-self-start',
                    2 => 'tw-contents md:tw-flex md:tw-justify-self-end',
                    3 => 'tw-contents md:tw-flex md:tw-justify-self-start',
            ];
            ?>

            <div class="<?= $classes[$index] ?> <?= count($badge) == 0 && $has_badge ? 'tw-mb-10' : '' ?>">
                <div>
                    <img
                            src="<?= assets_landing_page(resource: "platforms/{$image}") ?>"
                            alt="<?= $name ?> Sponsor Logo"
                    />
                    <?php if (count($badge)):
                        $badge_style_class = isset($badge['style']) ? 'mt-badge-' . $badge['style'] : 'mt-badge-light';
                        $badge_text = $badge['text'] ?? '';
                        ?>
                        <div class="tw-flex tw-justify-center tw-mt-4">
                            <div class="mt-badge <?= $badge_style_class ?>"><?= $badge_text ?></div>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        <?php endforeach; ?>
    </div>
</section>