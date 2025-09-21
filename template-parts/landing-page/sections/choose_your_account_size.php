<?php
$products_data = get_products_with_attributes();
$attributes = $products_data['attributes'] ?? [];

$account_sizes = [];
$account_types = [];
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

$addons = get_saved_challenge_addons();

$meta_info_list = [
        [
                'key' => 'profit_target',
                'label' => 'Profit Target',
                'value' => '$3,000',
        ],
        [
                'key' => 'max_contracts',
                'label' => 'Max Contracts',
                'value' => '5 Minis (50 Micros)',
        ],
        [
                'key' => 'daily_loss_limit_soft_breach',
                'label' => 'Daily Loss Limit (Soft Breach)',
                'value' => 'None',
        ],
        [
                'key' => 'trailing_max_drawdown',
                'label' => 'Trailing Max Drawdown',
                'value' => 'None',
        ],
        [
                'key' => 'drawdown_mode',
                'label' => 'Drawdown Mode',
                'value' => 'None',
        ],
        [
                'key' => 'min_trading_days',
                'label' => 'Min Trading Days to Pass',
                'value' => 'None',
        ],
        [
                'key' => 'reset_fee',
                'label' => 'Reset Fee',
                'value' => 'None',
        ],
        [
                'key' => 'activation_fee',
                'label' => 'Activation Fee',
                'value' => 'None',
        ]
];

$best_products = mt_most_popular_products();

$tabs = array_map(function($item) {
    $parsed = parse_attribute_meta($item['attribute_meta'] ?? []);

    $is_disabled = isset($parsed['config']['status']) && $parsed['config']['status'] === 'disabled';

    return [
            'id'        => $item['slug'] . $item['id'] . '_tab',
            'panel_id'  => $item['slug'] . $item['id'] . '_panel',
            'icon'      => $item['thumbnail_url'],
            'title'     => $item['name'],
            'subtitle'  => $item['description'],
            'disabled'  => $is_disabled,
            'content'   => ! $is_disabled ? load_tab_content('template-parts/tabs/content-' . $item['slug']) : null,
    ];
}, $market_type);

?>

<section id="pricing" class="tw-px-4">
    <div class="tw-pb-4 tw-self-stretch tw-text-center tw-text-white tw-text-[40px] tw-font-light tw-uppercase tw-leading-[48px]">
        Choose your account size
    </div>

    <div class="tw-mx-auto tw-pb-8 tw-max-w-[760px] tw-text-center tw-text-xl tw-leading-8 tw-font-medium text-stone-400 md:tw-max-w-[860px]">
        Choose from tw-flexible account sizes and plans tailored to your trading style—whether you're growing your skills
        or ready to trade real capital with confidence
    </div>

    <div class="tw-space-y-8 lg:tw-space-y-0 lg:tw-flex lg:tw-gap-8">
        <div class="tw-flex tw-flex-col tw-h-full tw-space-y-8 lg:tw-gap-y-6 lg:tw-space-y-12 w-full">
            <?php render_tabs($tabs); ?>
        </div>
        <div class="tw-space-y-8 tw-flex tw-flex-col">
            <div class="tw-flex-1">
                <div class="tw-w-full lg:tw-w-[360px] tw-bg-mgt-dark tw-rounded-lg">
                    <div class="tw-justify-start tw-text-white tw-text-xl tw-font-bold tw-leading-loose tw-px-4 tw-pt-4">Plan Summary</div>
                    <div class="metaInfo">
                        <?php foreach ($meta_info_list as $index => $meta_info) : ?>
                            <?php
                            $isLastLoop = $meta_info === end($meta_info_list)
                            ?>
                            <div class="tw-w-full tw-p-4 tw-border-b last:tw-border-b-0 tw-border-stone-800 group-[.mark]:tw-border-[#f1a035] tw-inline-flex tw-justify-start tw-items-center tw-gap-2">
                                <div class="tw-w-6 tw-h-6 tw-relative tw-text-[#A8A29E] group-[.mark]:tw-text-[#131210]">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/landing-page/check.svg"
                                         width="24" height="24">
                                </div>
                                <div class="<?= $meta_info['key'] ?> tw-flex-1 tw-justify-start text-stone-400 group-[.mark]:text-[#131210] tw-text-base tw-font-medium tw-leading-normal">
                                    <?= $meta_info['label'] ?>: <span
                                            class="metaValue"><?= $meta_info['value'] ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="tw-w-full lg:w-[360px] lg:tw-justify-end tw-bg-mgt-dark tw-rounded-lg tw-p-4 tw-flex tw-flex-col tw-gap-4">
                <div class="tw-justify-start tw-text-white tw-text-xl tw-font-bold tw-leading-8">Plan Total</div>

                <div class="tw-inline-flex tw-justify-start  tw-gap-2 tw-items-center">
                    <div class="tw-text-right tw-justify-start tw-text-[#ffb34a] tw-tw-text-xl tw-font-medium tw-leading-loose">$</div>
                    <div class="tw-text-right tw-justify-start tw-text-[#ffb34a] tw-text-[32px] tw-font-medium tw-uppercase tw-leading-10">
                        1,423
                    </div>
                    <div class="w-[76px] tw-text-right tw-justify-center tw-text-[#fff7e6] tw-text-base tw-font-medium tw-leading-normal">
                        per month
                    </div>
                </div>
                <button class="btn-yellow-link tw-rounded-xl tw-h-12 tw-px-4 tw-py-3">
                    GET PLAN
                </button>
            </div>
        </div>
    </div>
</section>