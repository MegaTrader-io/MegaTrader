<?php
$products_data = get_products_with_attributes();
$attributes = $products_data['attributes'] ?? [];

$account_sizes = [];
$account_types = [];
$platforms = [];
$market_type = [];

foreach ($attributes as $attr) {
    switch ($attr['taxonomy']) {
        case 'pa_account-size':
            $account_sizes[] = $attr['slug'];
            break;
        case 'pa_account-types':
            $account_types[] = $attr;
            break;
        case 'pa_market-type':
            $market_type[] = $attr;
            break;
        case 'pa_platform':
            $platforms[] = $attr;
            break;
    }
}

$defaultAccountType = $account_types[0];
$accountThumbnailUrl = $defaultAccountType['thumbnail_url'];
$platformThumbnailUrl = $platforms[0]['thumbnail_url'];

$size = $account_sizes[0];
$defaultSlug = $defaultAccountType['slug'];
$defaultPlanName = $size . ' ' . $defaultAccountType['name'];

$get_plan_url = is_user_logged_in() ? wc_get_account_endpoint_url('') : home_url('auth/register');

$filtered = array_filter($products_data['products'], function ($product) use ($defaultSlug) {
    return $product['slug'] === $defaultSlug;
});

$product = reset($filtered) ?: null;

$productLevel = $product[$defaultSlug][$size][$defaultSlug];
$defaultPlatform = array_key_first($productLevel);
$defaultMarketType = array_key_first($productLevel[$defaultPlatform]);

$planList = [];
$defaultMetaInfo = [];
$metaInfoList = [];
$firstProduct = null;

foreach ($account_sizes as $index => $size) {
    $parent_id = $product['id'];
    $properties = array_values($product[$defaultSlug][$size][$defaultSlug][$defaultPlatform])[0];
    $id = -1;
    $price = '0.00';
    $metaInfoList = [];
    foreach ($properties as $property) {
        foreach ($property as $key => $arrayProperties) {
            switch ($key) {
                case 'id':
                    $id = $arrayProperties;
                    break;
                case 'price-monthly':
                    $price = intval(str_replace('$', '', $arrayProperties));
                    break;
                case 'meta-info':
                    $metaInfoList = $arrayProperties;
                    break;
            }
        }
    }

    foreach (Label::PRODUCT_META as $key => $value) {
        if ($metaInfoList[$key]) {
            $defaultMetaInfo[$key] = true;
        }
    }

    if ($index == 0) {
        $firstProduct = [
                'id' => $id,
                'parent_id' => $parent_id,
                'price' => $price,
                'size' => $size,
                'metaInfoList' => $metaInfoList
        ];
    }
}

$has_coupon = false;
$coupon = ['discount_total' => null, 'coupon' => null, 'original_total' => null];

if ($firstProduct) {
    $metaInfoList = $firstProduct['metaInfoList'];
    $coupon = mt_get_best_coupon_for_variation($firstProduct['id']);
    $has_coupon = $coupon['valid'];
}

function render_template_meta_info($value = '', $label = '', $classes = '')
{
    $checkIconUrl = get_template_directory_uri() . '/assets/img/landing-page/check.svg';

    return <<<HTML
<div class="tw-w-full tw-py-3 tw-inline-flex tw-justify-start tw-items-center tw-gap-2 {$classes}">
    <div class="tw-w-6 tw-h-6 tw-relative tw-text-[#A8A29E] group-[.mark]:tw-text-[#131210]">
        <img src="{$checkIconUrl}"
             width="24" height="24">
    </div>
    <div class="tw-flex-1 tw-justify-start text-stone-400 group-[.mark]:text-[#131210] tw-text-base tw-font-medium tw-leading-normal">
        <span class="mega-info-row__label">{$label}</span>: <span class="mega-info-row__value">{$value}</span>
    </div>
</div>
HTML;
}

$tabs = array_map(function ($item) {
    $parsed = parse_attribute_meta($item['attribute_meta'] ?? []);

    $is_disabled = isset($parsed['config']['status']) && $parsed['config']['status'] === 'disabled';

    return [
            'id' => $item['slug'] . $item['id'] . '_tab',
            'panel_id' => $item['slug'] . $item['id'] . '_panel',
            'icon' => $item['thumbnail_url'],
            'title' => $item['name'],
            'subtitle' => $item['description'],
            'disabled' => $is_disabled,
            'content' => !$is_disabled ? load_tab_content(
                    template_path: 'template-parts/tabs/content-' . $item['slug'],
                    layoutType: LayoutType::LandingPage
            ) : null,
    ];
}, $market_type);

?>

<section id="pricing" class="tw-px-4">
    <div class="tw-pb-4 tw-self-stretch tw-text-center tw-text-white tw-text-[40px] tw-font-light tw-uppercase tw-leading-[48px]">
        Choose your account size
    </div>

    <div class="tw-mx-auto tw-pb-8 tw-max-w-[760px] tw-text-center tw-text-xl tw-leading-8 tw-font-medium text-stone-400 md:tw-max-w-[860px]">
        Choose from tw-flexible account sizes and plans tailored to your trading style—whether you're growing your
        skills
        or ready to trade real capital with confidence
    </div>

    <div class="tw-space-y-8 lg:tw-space-y-0 lg:tw-flex lg:tw-gap-8">
        <div class="mt-tabs-no-border tw-flex tw-flex-col tw-h-full tw-space-y-8 lg:tw-gap-y-6 lg:tw-space-y-12 w-full">
            <?php render_tabs($tabs); ?>
        </div>
        <div class="tw-space-y-8 tw-flex tw-flex-col">
            <div class="lg:tw-sticky lg:tw-top-[calc((var(--promo-banner-height,0px))+(var(--admin-bar-height,0px))+(var(--nav-bar-height,0px)))]">
                <div class="tw-px-4 tw-h-[104px] tw-inline-flex tw-items-center tw-justify-center">
                    <div class="plan-summary tw-inline-flex tw-items-center tw-justify-start tw-gap-2 tw-leading-7">
                        <img class="plan-summary__plan-icon" src="<?= $accountThumbnailUrl; ?>" alt="Plan Icon">
                        <img class="plan-summary__platform-icon" src="<?= $platformThumbnailUrl; ?>"
                             alt="Platform Icon">
                        <div class="plan-summary__name tw-text-2xl tw-text-white tw-font-medium tw-uppercase">
                            <?= $defaultPlanName ?>
                        </div>
                    </div>
                </div>
                <?= render_template_meta_info(classes: 'tw-hidden template-metaInfo') ?>
                <div class="tw-w-full lg:tw-w-[360px] tw-bg-mgt-dark tw-rounded-lg">
                    <div class="tw-justify-start tw-text-white tw-text-xl tw-font-bold tw-leading-8 tw-px-4 tw-pt-4 tw-pb-2">
                        Plan Summary
                    </div>
                    <div class="metaInfo py-2 px-4">
                        <?php $defaultMetaInfo = [];
                        foreach ($defaultMetaInfo as $field => $value) : ?>
                            <?php
                            $label = Label::PRODUCT_META[$field];
                            $value = $metaInfoList[$field];

                            echo render_template_meta_info(value: $value, label: $label);
                            ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-pricing-card mt-pricing-card--light">
                        <div class="mt-pricing-card__header">
                            <div class="mt-pricing-card__header-text mt-pricing-card--discount_total">Save <span><?= $has_coupon ? mt_price_plain($coupon['discount_total']) : 0 ?></span> with code</div>
                            <div class="mt-pricing-card__code-container">
                                <span class="mt-pricing-card__code text-uppercase"><?= $has_coupon ? $coupon['coupon'] : 0 ?></span>
                                <button type="button" class="mt-pricing-card__copy-btn btn-copy tw-relative" aria-label="Copy Code" data-copy-text="<?= $has_coupon ? $coupon['coupon'] : 0 ?>">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <mask id="mask0_15572_33010" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
                                            <rect width="24" height="24" fill="#D9D9D9"/>
                                        </mask>
                                        <g mask="url(#mask0_15572_33010)">
                                            <path d="M9 18C8.45 18 7.97917 17.8042 7.5875 17.4125C7.19583 17.0208 7 16.55 7 16V4C7 3.45 7.19583 2.97917 7.5875 2.5875C7.97917 2.19583 8.45 2 9 2H18C18.55 2 19.0208 2.19583 19.4125 2.5875C19.8042 2.97917 20 3.45 20 4V16C20 16.55 19.8042 17.0208 19.4125 17.4125C19.0208 17.8042 18.55 18 18 18H9ZM5 22C4.45 22 3.97917 21.8042 3.5875 21.4125C3.19583 21.0208 3 20.55 3 20V6H5V20H16V22H5Z" fill="black"/>
                                        </g>
                                    </svg>
                                    <span class="tw-absolute -tw-top-[28px] -tw-translate-x-[45%] tw-text-xs !tw-text-white tw-hidden mgt-copy-tooltip">copied</span>
                                </button>
                            </div>
                        </div>
                        <div class="mt-pricing-card__content">
                            <div class="mt-pricing-card__price-group gap-2">
                                <span class="coupon-before-price mt-badge mt-badge-rounded-sm mt-badge-light" style="display: <?= $has_coupon ? 'block' : 'none' ?>">Before: <span><?= wc_price($firstProduct['price'], ['decimals' => 0]) ?></span></span>
                                <div class="d-flex align-items-end gap-1">
                                    <span class="mt-pricing-card__current-price total-plan"><?= wc_price($has_coupon ? $firstProduct['price'] - $coupon['discount_total'] : $firstProduct['price'], ['decimals' => 0]) ?></span>
                                    <span class="mt-pricing-card__fee-type frequency-plan"><?= $defaultSlug !== 'funded-plan' ? 'per month' : 'one time fee' ?></span>
                                </div>
                            </div>
                            <a href="#" id="proceed-to-checkout-btn" class="mega-btn-md mega-btn-secondary-md">PURCHASE PLAN</a>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</section>