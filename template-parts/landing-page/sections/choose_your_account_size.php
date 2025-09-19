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
        <div class="tw-flex tw-flex-col tw-h-full tw-space-y-8 lg:tw-gap-y-6 lg:tw-space-y-12">
            <div>
                <div class="tw-justify-start tw-text-white tw-text-xl tw-font-bold tw-leading-8">Account Type</div>
                <div class="tw-justify-start tw-text-stone-400 tw-text-base tw-font-medium tw-leading-normal">Pick the plan that aligns
                    with your trading goals. Each type includes its own drawdown model, payout rules, and evaluation
                    structure to support your growth.
                </div>
            </div>
            <div class="tw-space-y-2 tw-flex-1 md:tw-space-y-0 md:tw-flex tw-gap-2">
                <?php foreach ($account_types as $index => $account_type) : ?>
                    <button data-value="<?= $account_type['slug'] ?>"
                            class="btn-account-type tw-group tw-relative tw-w-full tw-rounded-2xl tw-p-6 tw-text-left account-type <?= $index === 0 ? 'account-active' : '' ?>">
                        <div class="tw-inline-flex tw-justify-start tw-items-start tw-gap-4">
                            <div class="tw-text-primary group-[.account-active]:tw-text-black tw-mt-1 group-[.account-active]:filter group-[.account-active]:tw-brightness-[5] group-[.account-active]:tw-invert">
                                <img src="<?= $account_type['thumbnail_url'] ?>" class="tw-w-9 tw-h-9" alt="Icon">
                            </div>
                            <div class="tw-flex-1 tw-inline-flex tw-flex-col tw-justify-center tw-items-start tw-gap-2">
                                <div class="tw-self-stretch tw-inline-flex tw-justify-start tw-items-start tw-gap-1">
                                    <div class="tw-justify-start tw-text-white group-[.account-active]:tw-text-black tw-text-xl tw-font-bold tw-leading-loose">
                                        <?= $account_type['name'] ?>
                                    </div>
                                </div>
                            </div>
                            <div class="tw-w-[30px] tw-h-[30px] tw-right-[8px] tw-top-[8px] tw-absolute group-[.account-active]:tw-block">
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
                    </button>
                <?php endforeach; ?>
            </div>
            <div class="tw-space-y-2 lg:tw-mb-12 tw-h-full tw-relative">
                <div class="tw-justify-start tw-text-white tw-text-xl tw-font-bold tw-leading-8">Account Size</div>
                <div class="tw-justify-start tw-text-stone-400 tw-text-base tw-font-medium tw-leading-6 !tw-mb-3">Select the account
                    balance you want to trade. This determines your profit target, drawdown limits, and maximum position
                    size based on your trading style.
                </div>
                <div id="slider-mgt" data-sizes="<?= join(',', $account_sizes) ?>"></div>
            </div>
            <div class="tw-space-y-4 !tw-pt-8 lg:tw-pt-0 lg:tw-flex-1 lg:tw-justify-end">
                <div class="tw-justify-start tw-text-white tw-text-xl tw-font-bold tw-leading-8">Addons</div>

                <?php foreach ($addons as $option_data) : ?>
                    <div class="addons-item <?php echo esc_attr($option_data['field']); ?>">
                        <input type="checkbox" name="<?php echo esc_attr($option_data['field']); ?>"
                               id="option_<?php echo esc_attr($option_data['field']); ?>"
                               class="tw-hidden tw-peer addon-option"/>
                        <label for="option_<?php echo esc_attr($option_data['field']); ?>"
                               class="tw-block hover:tw-cursor-pointer mgt-checkbox tw-bg-mgt-dark tw-w-full tw-rounded-lg tw-p-4 tw-space-y-2 tw-outline tw-outline-1 tw-outline-offset-[-1px] tw-outline-neutral-700 peer-checked:tw-outline-primary tw-transition-colors tw-duration-300">

                            <div class="tw-grid tw-grid-cols-[auto_1fr_auto] tw-items-center tw-gap-4 tw-justify-between">
                                <div class="box-checked tw-w-[18px] tw-h-[18px] tw-border-2 tw-rounded tw-justify-center tw-flex tw-border-primary"></div>

                                <div class="tw-justify-start tw-text-left tw-text-white tw-text-base tw-font-medium tw-leading-normal">
                                    <?= $option_data['label'] ?>
                                </div>
                                <div class="tw-p-1 tw-bg-neutral-200 tw-rounded tw-inline-flex tw-justify-center tw-items-center tw-gap-2.5">
                                    <div class="tw-justify-start tw-text-[#131210] tw-text-sm tw-font-bold tw-uppercase tw-leading-none">
                                        <?= $option_data['adjustment'] . $option_data['adjustment_symbol'] ?>
                                    </div>
                                </div>
                            </div>
                            <div class="tw-justify-start tw-text-stone-400 tw-text-sm tw-font-bold tw-leading-5">
                                <?= $option_data['description'] ?>
                            </div>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
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