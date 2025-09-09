<?php
$products_data = get_products_with_attributes();
$attributes = $products_data['attributes'] ?? [];

$account_sizes = [];
$account_types = [];

foreach ($attributes as $attr) {
    switch ($attr['taxonomy']) {
        case 'pa_account-size':
            $account_sizes[] = $attr['slug'];
            break;
        case 'pa_account-types':
            $account_types[] = $attr;
            break;
    }
}

$defaultPlatform = 'megatraderx';
$defaultMarketType = 'futures';
$defaultSlug = $account_types[0]['slug'];

$get_plan_url = is_user_logged_in() ? wc_get_account_endpoint_url('') : home_url('auth/register');

$filtered = array_filter($products_data['products'], function ($product) use ($defaultSlug) {
    return $product['slug'] === $defaultSlug;
});

$product = reset($filtered) ?: null;

$planList = [];
$defaultMetaInfo = [];
$has_coupon_global = null;
foreach ($account_sizes as $size) {
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

    $coupon = mt_get_best_coupon_for_variation($id);
    if ($coupon['valid'] && !$has_coupon_global) {
        $has_coupon_global = true;
    }

    $planList[] = [
            'id' => $id,
            'parent_id' => $parent_id,
            'price' => $price,
            'size' => $size,
            'metaInfoList' => $metaInfoList
    ];
}

function render_template_meta_info($value = '', $label = '', $classes = '')
{
    return <<<HTML
<div class="mega-info-row tw-w-full tw-py-2.5 first:tw-border-t first:tw-border-neutral-700 tw-inline-flex tw-justify-start tw-items-center tw-gap-2 {$classes}">
    <div class="tw-flex-1 tw-justify-start tw-text-base tw-font-medium">
        <div class="tw-grid tw-grid-cols-[1fr_auto] tw-gap-2">
            <div class="mega-info-row__label tw-col-span-1 tw-leading-6 tw-text-stone-400">
                {$label}
            </div>
            <div class="mega-info-row__value tw-col-auto tw-no-wrap tw-content-center tw-text-right tw-text-white tw-leading-6 tw-w-full">{$value}</div>
        </div>
    </div>
</div>
HTML;
}

$best_products = mt_most_popular_products();
?>

<section id="pricing" class="tw-px-4">
    <div class="tw-pb-4 tw-text-center tw-text-white tw-text-[40px] tw-font-light tw-uppercase tw-leading-[48px]">
        Choose your account size
    </div>

    <div class="tw-mx-auto tw-pb-8 tw-max-w-[760px] tw-text-center tw-text-xl tw-leading-8 tw-font-medium tw-text-stone-400 md:tw-max-w-[ 860px]">
        Choose from flexible account sizes and plans tailored to your trading style—whether you're growing your skills
        or ready to trade real capital with confidence
    </div>

    <div class="tw-space-y-2 tw-flex-1 md:tw-space-y-0 md:tw-flex tw-gap-2 tw-mb-4">
        <?php foreach ($account_types as $index => $account_type) : ?>
            <div data-value="<?= $account_type['slug'] ?>"
                 data-default-platform="<?= $defaultPlatform ?>"
                 data-default-market-type="<?= $defaultMarketType ?>"
                 class="btn-account-type tw-group tw-relative tw-w-full tw-rounded-2xl tw-p-6 tw-text-left account-type <?= $index === 0 ? 'account-active' : '' ?>">
                <div class="tw-inline-flex tw-justify-start tw-items-start tw-gap-4">
                    <div class="tw-mt-1 group-[.account-active]:tw-filter group-[.account-active]:tw-brightness-[5] group-[.account-active]:tw-invert">
                        <img src="<?= $account_type['thumbnail_url'] ?>" class="tw-w-9 tw-h-9" alt="Icon">
                    </div>
                    <div class="tw-flex-1 tw-inline-flex tw-flex-col tw-justify-center tw-items-start tw-gap-2">
                        <div class="tw-inline-flex tw-justify-start tw-items-start tw-gap-1">
                            <div class="tw-justify-start tw-text-white group-[.account-active]:tw-text-black tw-text-xl tw-font-bold tw-leading-loose">
                                <?= $account_type['name'] ?>
                            </div>
                        </div>
                        <div class="tw-justify-start group-[.account-active]:tw-opacity-60 group-[.account-active]:tw-text-black tw-text-stone-400 tw-text-base tw-font-bold tw-leading-normal group-[.plan-selected]:tw-opacity-60 group-[.plan-selected]:tw-text-black">
                            <?= $account_type['description'] ?>
                        </div>
                    </div>
                    <div class="tw-w-[30px] twh-[30px] tw-right-[8px] tw-top-[8px] tw-absolute group-[.account-active]:tw-block">
                        <svg width="31" height="30" viewBox="0 0 31 30" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <mask id="mask0_11266_797" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0"
                                  y="0"
                                  width="31" height="30">
                                <rect x="0.5" width="30" height="30" fill="#D9D9D9"></rect>
                            </mask>
                            <g mask="url(#mask0_11266_797)">
                                <path d="M13.75 20.75L22.5625 11.9375L20.8125 10.1875L13.75 17.25L10.1875 13.6875L8.4375 15.4375L13.75 20.75ZM15.5 27.5C13.7708 27.5 12.1458 27.1719 10.625 26.5156C9.10417 25.8594 7.78125 24.9688 6.65625 23.8438C5.53125 22.7188 4.64063 21.3958 3.98438 19.875C3.32812 18.3542 3 16.7292 3 15C3 13.2708 3.32812 11.6458 3.98438 10.125C4.64063 8.60417 5.53125 7.28125 6.65625 6.15625C7.78125 5.03125 9.10417 4.14063 10.625 3.48438C12.1458 2.82812 13.7708 2.5 15.5 2.5C17.2292 2.5 18.8542 2.82812 20.375 3.48438C21.8958 4.14063 23.2188 5.03125 24.3438 6.15625C25.4688 7.28125 26.3594 8.60417 27.0156 10.125C27.6719 11.6458 28 13.2708 28 15C28 16.7292 27.6719 18.3542 27.0156 19.875C26.3594 21.3958 25.4688 22.7188 24.3438 23.8438C23.2188 24.9688 21.8958 25.8594 20.375 26.5156C18.8542 27.1719 17.2292 27.5 15.5 27.5Z"
                                      fill="#131210"></path>
                            </g>
                        </svg>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="price-table">
        <?php foreach ($planList as $index => $plan) : ?>
            <?php
            $id = $plan['id'];
            $size = $plan['size'];
            $parent_id = $plan['parent_id'];
            $price = $plan['price'];
            $metaInfoList = $plan['metaInfoList'];

            $coupon = mt_get_best_coupon_for_variation($id);
            $has_coupon = $coupon['valid'];
            $price_plan = $has_coupon ? $coupon['final_total'] : $price;

            $scan_product = $best_products[$parent_id];
            $is_most_popular = $scan_product && $scan_product['variation_id'] == $id;
            ?>
            <div class="price-table__plan <?= $is_most_popular
                    ? 'price-table__plan--most-popular'
                    : 'price-table__plan--regular-plan'
            ?> tw-group" data-price="<?= $size ?>">
                <div class="price-table__size">
                    <div class="price-table__most-popular-badge">
                        <span class="mt-badge mt-badge-sm mt-badge-primary  !tw-inline-flex !tw-justify-start">
                            Most popular
                        </span>
                    </div>
                    <div class="tw-justify-start tw-text-white tw-text-2xl tw-font-medium tw-uppercase tw-leading-7"><?= $size ?>
                        Account
                    </div>
                </div>
                <div data-price="<?= $size ?>"
                     class="price-information tw-px-4 tw-flex <?= $index !== array_key_last($planList) ? 'tw-border-r-2' : '' ?> tw-border-stone-800 <?= $has_coupon_global && !$has_coupon ? 'tw-min-h-[140px] tw-items-center' : '' ?>">
                    <div class="tw-space-y-2 tw-my-3 tw-w-full">
                        <div style="display: <?= $has_coupon ? 'block' : 'none' ?>"
                             data-price="<?= $size ?>"
                             class="badge-coupon">
                            <div class="mt-badge mt-badge-sm mt-badge-secondary !tw-inline-flex !tw-justify-start">
                                Save <span
                                        class="badge-coupon__discount_total"><?= $has_coupon ? mt_price_plain($coupon['discount_total']) : 0 ?></span>
                                with code
                                <svg width="1" height="16" viewBox="0 0 1 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <line x1="0.5" y1="2.18557e-08" x2="0.499999" y2="24" stroke="#404040"/>
                                </svg>
                                <div class="badge-coupon__code tw-uppercase tw-justify-start">
                                    <?= $has_coupon ? strtoupper($coupon['coupon']) : '' ?>
                                </div>
                            </div>
                        </div>
                        <div class="tw-text-white tw-font-medium">
                            <span class="tw-text-4xl tw-leading-[48px] price-plan"
                                  data-price="<?= $size ?>"><?= mt_price_plain($price_plan) ?></span>
                            <span class="tw-text-xl frequency-plan"
                                  data-price="<?= $size ?>"> <?= $defaultSlug !== 'funded-plan' ? 'per month' : 'one time fee' ?></span>
                            <div style="display: <?= $has_coupon ? 'block' : 'none' ?>"
                                 data-price="<?= $size ?>"
                                 class="coupon-before-price tw-text-red-500 tw-text-base tw-font-medium tw-line-through tw-uppercase tw-leading-7">
                                before <span><?= $has_coupon ? mt_price_plain($coupon['original_total']) : '0' ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?= render_template_meta_info(classes: 'tw-hidden template-metaInfo') ?>
                <div class="tw-px-4 <?= $index !== array_key_last($planList) ? 'tw-border-r-2' : '' ?> tw-border-stone-800 metaInfo"
                     data-price="<?= $size ?>">
                    <?php foreach ($defaultMetaInfo as $field => $value): ?>
                        <?php
                        $label = Label::PRODUCT_META[$field];
                        $value = $metaInfoList[$field];
                        ?>
                        <?= render_template_meta_info(value: $value, label: $label) ?>
                    <?php endforeach; ?>
                </div>
                <div class="tw-px-6 tw-py-6 <?= $index !== array_key_last($planList) ? 'tw-border-r-2' : '' ?> tw-border-stone-800">
                    <a href="<?= $get_plan_url ?>"
                       class="mega-btn-md <?= $is_most_popular ? 'mega-btn-primary-md' : 'mega-btn-default-md' ?> w-100 tw-no-underline">
                        GET FUNDED WITH $<?= $size ?>
                    </a>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</section>