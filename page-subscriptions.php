<?php
/**
 * Template Name: Subscription Template
 *
 * The template for displaying front page
 * The Home template file
 *
 *
 * @package megatrader
 */
nocache_headers();
header("Cache-Control: private, must-revalidate");

get_header();
$page_slug = pathinfo(__FILE__, PATHINFO_FILENAME);

$products_data = get_products_with_attributes();
$attributes = $products_data['attributes'] ?? [];
$mt_products_raw = $products_data['products'] ?? [];

$mtAccountTypes = $mtMarketTypes = $mtAccountSizes = $mtPlatforms = [];

foreach ($attributes as $attr) {
    switch ($attr['taxonomy']) {
        case 'pa_market-type':
            $mtMarketTypes[] = $attr;
            break;
        case 'pa_account-types':
            $mtAccountTypes[] = $attr;
            break;
        case 'pa_account-size':
            $mtAccountSizes[] = $attr;
            break;
        case 'pa_platform':
            $mtPlatforms[] = $attr;
            break;
    }
}

require_once get_template_directory() . '/mt_prices_manager.php';

$instance = new MT_PRICESManager(
        products: $mt_products_raw, marketTypes: $mtMarketTypes, accountTypes: $mtAccountTypes, sizes: $mtAccountSizes, platforms: $mtPlatforms,
);

$marketTypes = $instance->marketTypes()->filtered(function ($marketType) {
    return $marketType['count'] > 0;
});

$marketType = $marketTypes[0] ?? null;
$marketTypeSlug = $marketType['slug'] ?? null;
$accountTypes = $instance->accountTypesByMarketType($marketType['slug']);

$accountType = $accountTypes[0] ?? null;
$accountTypeSlug = $accountType['slug'] ?? '';

$mt_product = $instance->productByMarketTypeAndAccountType($marketTypeSlug, $accountTypeSlug);

$accountSizes = $instance->accountSizesByMarketTypeAndAccountType($marketTypeSlug, $accountTypeSlug);
$platforms = $instance->platformsByMarketTypeAccountTypeAndSizes($marketTypeSlug, $accountTypeSlug);

while (count($platforms) < 4) {
    $platforms [] = ['id' => 'empty'];
}

$metaInfo = [
        [
                'key' => 'trailing_drawdown',
                'label' => 'Trailing Drawdown',
                'value' => '$6,000',
        ],
        [
                'key' => 'daily_loss_limit',
                'label' => 'Daily Loss Limit',
                'value' => '$3,750',
        ],
        [
                'key' => 'consistency',
                'label' => 'Consistency',
                'value' => '20%',
        ],
        [
                'key' => 'payout_frequency',
                'label' => 'Payout Frequency',
                'value' => '5 days',
        ],
        [
                'key' => 'max_accounts',
                'label' => 'Max Accounts',
                'value' => '5',
        ],
        [
                'key' => 'max_contracts',
                'label' => 'Max Contracts',
                'value' => '12 minis / 12 micros',
        ],
];

?>

    <div class="container">
        <div class="mt-page">
            <div class="mt-page__main">
                <div class="mt-subscriptions">
                    <?php render_step_selector(1); ?>
                    <form class="mt-checkout-first" id="checkout-form">
                        <section id="market-type-section" class="product-section">
                            <h2 class="product-section__header mb-3">
                                <span class="product-section__title">1. Market Type</span>
                            </h2>

                            <div class="product-section__list product-section__list_grid">
                                <?php
                                $selected = false;
                                $checked = '';
                                ?>
                                <?php foreach ($mtMarketTypes as $key => $item): ?>
                                    <?php
                                    $input_id = 'market-type-' . $item['slug'];
                                    $slug = esc_attr($item['slug']);
                                    $name = esc_html($item['name']);
                                    $isDisabled = $item['count'] == 0;
                                    $description = esc_html($item['description']);
                                    $thumbnail = esc_url($item['thumbnail_url']);

                                    $checked = '';
                                    if (!$selected && $item['count'] > 0) {
                                        $checked = 'checked';
                                        $selected = true;
                                    }

                                    ?>
                                    <input type="radio"
                                           name="market-type" <?= $isDisabled ? 'disabled="disabled"' : '' ?>
                                           value="<?= $slug ?>"
                                           id="<?= $input_id ?>" <?= $checked ?> class="mt-circle-radio">
                                    <div class="radio__label__wrapper">
                                        <label class="mt-card mt-card-dark mt-card-radio" for="<?= $input_id ?>">
                                            <div class="mt-card__header w-100">
                                                <div class="mt-card__title">
                                                    <i class="mt-card__radio"></i>
                                                    <span class="mt-card__title__text"><?= $name ?></span>
                                                </div>
                                                <?php if ($thumbnail): ?>
                                                    <img class="mt-card__title__image" src="<?= $thumbnail; ?>"
                                                         alt="Icon">
                                                <?php endif; ?>
                                            </div>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </section>
                        <section id="account-type-section" class="product-section">
                            <h2 class="product-section__header mb-3">
                                <span class="product-section__title">2. Account Type</span>
                            </h2>
                            <div class="product-section__list product-section__list_grid">
                                <?php foreach ($accountTypes as $key => $item): ?>
                                    <?php
                                    $input_id = 'account-type-' . $item['slug'];
                                    $slug = esc_attr($item['slug']);
                                    $name = esc_html($item['name']);
                                    $isDisabled = $item['count'] == 0;
                                    $description = esc_html($item['description']);
                                    $thumbnail = esc_url($item['thumbnail_url']);

                                    $checked = $key == 0 ? 'checked' : '';

                                    $parsed = parse_attribute_meta($item['attribute_meta'] ?? []);
                                    $config = isset($parsed['config']) ? $parsed['config'] : [];
                                    $badge = isset($parsed['config']['badge']) ? $parsed['config']['badge'] : [];

                                    ?>
                                    <input type="radio"
                                           name="account-type" <?= $isDisabled ? 'disabled="disabled"' : '' ?>
                                           value="<?= $slug ?>"
                                           id="<?= $input_id ?>" <?= $checked ?> class="mt-circle-radio">
                                    <div class="radio__label__wrapper">
                                        <label class="mt-card mt-card-dark mt-card-radio" for="<?= $input_id ?>">
                                            <div class="mt-card__header mt-card__header--two-columns">
                                                <div class="mt-card__title mt-card__title--group">
                                                    <i class="mt-card__radio"></i>
                                                    <span class="mt-card__title__text"><?= $name ?></span>
                                                </div>
                                                <?php if ($badge):
                                                    $badge_style_class = isset($badge['style']) ? 'mt-badge-' . $badge['style'] : 'mt-badge-light';
                                                    $badge_text = $badge['text'] ?? '';
                                                    ?>
                                                    <div class="mt-card__badge mt-badge mt-badge-rounded <?= $badge_style_class ?>"><?= $badge_text ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </section>
                        <section id="account-size-section" class="product-section">
                            <h2 class="product-section__header mb-3">
                                <span class="product-section__title">3. Account Size</span>
                            </h2>
                            <div class="product-section__list product-section__list_grid">
                                <?php foreach ($accountSizes as $key => $item): ?>
                                    <?php
                                    $input_id = 'account-size-' . $item['slug'];
                                    $slug = esc_attr($item['slug']);
                                    $name = '$' . esc_html($item['slug']);
                                    $isDisabled = $item['count'] == 0;
                                    $description = esc_html($item['description']);
                                    $thumbnail = esc_url($item['thumbnail_url']);

                                    $checked = $key == 0 ? 'checked' : '';

                                    $parsed = parse_attribute_meta($item['attribute_meta'] ?? []);
                                    $config = isset($parsed['config']) ? $parsed['config'] : [];
                                    $badge = isset($parsed['config']['badge']) ? $parsed['config']['badge'] : [];

                                    $properties = [];
                                    foreach (array_keys($mt_product['tree_map']) as $attr) {
                                        $properties = count($properties) === 0 ? array_values($mt_product[$mt_product['slug']])[0] : array_values($properties)[0];
                                    }

                                    $frequency = $mt_product['tree_map']['billing-type'] == 'one-time' ? 'OT' : 'MO';

                                    if (count($properties) == 0) continue;

                                    $variation_id = -1;
                                    $price = '0.00';
                                    $meta_info = [];

                                    foreach ($properties as $property) {
                                        foreach ($property as $key => $value) {
                                            match ($key) {
                                                'id' => $variation_id = $value,
                                                'price-monthly' => $price = (int)str_replace('$', '', $value),
                                                'meta-info' => $meta_info = $value,
                                                default => null
                                            };
                                        }
                                    }
                                    ?>
                                    <input type="radio"
                                           name="account-size" <?= $isDisabled ? 'disabled="disabled"' : '' ?>
                                           value="<?= $slug ?>"
                                           id="<?= $input_id ?>" <?= $checked ?> class="mt-circle-radio">
                                    <div class="radio__label__wrapper">
                                        <label class="mt-card mt-card-dark mt-card-radio" for="<?= $input_id ?>">
                                            <div class="mt-card__header mt-card__header--two-columns">
                                                <div class="mt-card__title mt-card__title--group">
                                                    <i class="mt-card__radio"></i>
                                                    <span class="mt-card__title__text"><?= $name ?></span>
                                                </div>
                                                <div class="mt-card__badge mt-badge mt-badge-rounded mt-badge-gray"><?= mt_price_plain($price) . '/' . $frequency ?></div>
                                            </div>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </section>
                        <section id="account-platform-section" class="product-section">
                            <h2 class="product-section__header mb-3">
                                <span class="product-section__title">4. Broker</span>
                            </h2>
                            <div class="product-section__list product-section__list_grid">
                                <?php foreach ($platforms as $key => $item): ?>
                                    <?php if ($item['id'] == 'empty'): ?>
                                        <div></div>
                                        <?php continue; ?>
                                    <?php endif; ?>
                                    <?php
                                    $input_id = 'platform-' . $item['slug'];
                                    $slug = esc_attr($item['slug']);
                                    $name = esc_html($item['name']);
                                    $isDisabled = $item['count'] == 0;
                                    $description = esc_html($item['description']);
                                    $thumbnail = esc_url($item['thumbnail_url']);

                                    $checked = $key == 0 ? 'checked' : '';
                                    ?>
                                    <input type="radio"
                                           name="platform" <?= $isDisabled ? 'disabled="disabled"' : '' ?>
                                           value="<?= $slug ?>"
                                           id="<?= $input_id ?>" <?= $checked ?> class="mt-circle-radio">
                                    <div class="radio__label__wrapper">
                                        <label class="mt-card mt-card-dark mt-card-radio" for="<?= $input_id ?>">
                                            <div class="mt-card__header w-100">
                                                <div class="mt-card__title">
                                                    <i class="mt-card__radio"></i>
                                                    <span class="mt-card__title__text"><?= $name ?></span>
                                                </div>
                                                <?php if ($thumbnail): ?>
                                                    <img class="mt-card__title__image" src="<?= $thumbnail; ?>"
                                                         alt="Icon">
                                                <?php endif; ?>
                                            </div>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </section>
                        <section id="plan-detail-section" class="plan-card">
                            <div class="mt-card plan-card__container">
                                <div class="mt-card__header plan-card__header">
                                    <div class="plan-card__title-group">
                                        <div class="plan-card__title"></div>
                                        <div class="plan-card__subtitle"></div>
                                    </div>
                                    <div class="plan-card__pricing-group">
                                        <div style="display: none"
                                             class="plan-card__coupon mt-card__badge mt-badge mt-badge-rounded mt-badge-secondary">
                                        </div>
                                        <div class="plan-card__pricing">
                                            <div class="plan-card__old-price"></div>
                                            <div class="plan-card__new-price"></div>
                                            <div class="plan-card__period"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-card__body plan-card__body">
                                    <div class="plan-card__rules-header">Rules and Objectives</div>
                                    <div class="plan-card__rules">
                                        <?php foreach ($metaInfo as $key => $metaInfoRow): ?>
                                            <div class="plan-card__rule">
                                                <div class="plan-card__rule-label">
                                                    <div class="plan-card__label-wrapper">
                                                        <?= $metaInfoRow['label'] ?>
                                                        <?php if ($metaInfoRow['key'] == 'daily_loss_limit' || $metaInfoRow['key'] == 'consistency') : ?>
                                                            <div class="mt-tooltip">
                                                                <i class="mt-icon mt-icon-base mt-icon_info-solid"
                                                                   tabindex="0"
                                                                   aria-label="<?= $metaInfoRow['label'] ?> information">
                                                                </i>
                                                                <div class="mt-tooltip__panel" role="tooltip">
                                                                    <div class="mt-tooltip__title">Title here</div>
                                                                    <div class="mt-tooltip__body">Text Here</div>
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="plan-card__rule-value"><?= $metaInfoRow['value'] ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="mt-card__footer plan-card__footer">
                                    <div class="plan-card__footer-content">
                                        <div class="plan-card__payment-methods">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/payment-methods/visa.png"
                                                 width="36" height="36" alt="Visa"/>
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/payment-methods/master.png"
                                                 width="36" height="36" alt="Mastercard"/>
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/payment-methods/amex.png"
                                                 width="36" height="36" alt="Amex"/>
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/payment-methods/discover.png"
                                                 width="36" height="36" alt="Discover"/>
                                        </div>
                                        <div>
                                            <a href="#"
                                               id="proceed-to-checkout-btn"
                                               class="proceed-to-checkout-btn mega-btn-md mega-btn-default-md mega-btn-next-icon-md">
                                                Continue with growth 25K
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <section class="trust-indicators">
                            <div class="trust-indicators__list">
                                <?php
                                $trustIndicators = [
                                        '1 hour payouts',
                                        '5,000+ traders',
                                        'Crypto payments',
                                ];
                                ?>
                                <?php foreach ($trustIndicators as $item): ?>
                                    <div class="trust-indicators__item">
                                        <img
                                                src="<?php echo get_template_directory_uri(); ?>/assets/img/checked-circle-success.svg"
                                                width="24"
                                                height="24"
                                                alt="checked"
                                                class="trust-indicators__icon"
                                        />
                                        <span class="trust-indicators__text"><?= esc_html($item) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.mtCache = {};

        const TREE_MAP_KEYS = Object.freeze({
            ACCOUNT_SIZE: 'account-size',
            ACCOUNT_TYPES: 'account-types',
            BILLING_TYPE: 'billing-type',
            MARKET_TYPE: 'market-type',
            PLATFORM: 'platform'
        });

        const tooltips = {
            daily_loss_limit: {
                title: '<?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_dll_tooltip_title']); ?>',
                value: '<?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_dll_tooltip_description']); ?>'
            },
            consistency: {
                title: '<?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_consistency_title']); ?>',
                value: '<?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['performance_consistency_description']); ?>'
            },
            futures_consistency: {
                title: '<?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['futures_performance_consistency_title']); ?>',
                value: '<?php echo esc_html(Label::META_ACCOUNT_OVERVIEW['futures_performance_consistency_description']); ?>'
            }
        }

        const PAGE_KEY = '<?= $page_slug ?>-storage';
        window[PAGE_KEY] = {
            screenLoaded: false
        };
        const REMEMBER_PREVIOUS_SELECTION = true;
        const MG_GLOBAL = {
            CHECKOUT_URL: '<?= home_url('/checkout/?add-to-cart=PRODUCT_ID') ?>',
            products: <?= wp_json_encode($products_data['products'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); ?>,
            accountTypes: <?= wp_json_encode($instance->accountTypes()->getList(), JSON_PRETTY_PRINT) ?>,
            accountSizes: <?= wp_json_encode($instance->accountSizes()->getList(), JSON_PRETTY_PRINT) ?>,
            platforms: <?= wp_json_encode($instance->platforms()->getList(), JSON_PRETTY_PRINT) ?>,
            pricingData: <?= wp_json_encode($instance->toJSON()); ?>,
            productMetaLabel: <?= wp_json_encode(Label::PRODUCT_META); ?>
        };

        const form = document.getElementById("checkout-form");

        function normalizeAttributes(data) {
            if (!data || !Array.isArray(data)) return {};

            return data.reduce((acc, item) => {
                return {...acc, ...item};
            }, {});
        }

        const firstCharUpper = str => str && str[0].toUpperCase() + str.slice(1);

        function getDefaultMetaInfo(productSelected, productPlatformDetail) {
            const defaultMetaInfo = {}
            for (const priceSize in productPlatformDetail) {
                let attributes = null;
                Object.keys(productSelected?.tree_map || []).forEach(_ => {
                    attributes = !attributes ? productPlatformDetail[priceSize] : Object.values(attributes).at(0);
                });

                const metaInfo = attributes.find(item => item['meta-info'])['meta-info'];
                for (const metaInfoKey in metaInfo) {
                    if (metaInfo[metaInfoKey]) {
                        defaultMetaInfo[metaInfoKey] = true;
                    }
                }
            }

            const validMetaInfo = Object.keys(defaultMetaInfo);
            let metaInfoList = [];
            Object.keys(MG_GLOBAL.productMetaLabel).forEach(key => {
                if (validMetaInfo.includes(key)) {
                    metaInfoList.push({key, label: MG_GLOBAL.productMetaLabel[key]})
                }
            });

            return metaInfoList;
        }

        function buildProductUrl(productId) {
            const CHECKOUT_URL = MG_GLOBAL.CHECKOUT_URL;
            let checkoutUrl = CHECKOUT_URL.replace('PRODUCT_ID', productId);

            const couponElement = document.querySelector('.plan-card__coupon');
            const coupon = couponElement?.dataset?.coupon || '';

            if (coupon) {
                checkoutUrl = checkoutUrl + '&coupon=' + coupon;
            }

            return checkoutUrl;
        }

        async function fetchCouponInBatch(productIds) {
            const URL = `/wp-json/custom/v1/best-coupon-in-batch?ids=${productIds}`;
            if (!mtCache[URL]) {
                const responseCoupons = await fetch(URL);
                mtCache[URL] = await responseCoupons.json();
            }

            return mtCache[URL];
        }


        function getProduct({accountType, marketType}) {
            let productSelected = MG_GLOBAL.products.find(product => product.tree_map[TREE_MAP_KEYS.ACCOUNT_TYPES] === accountType && product.tree_map[TREE_MAP_KEYS.MARKET_TYPE] === marketType);
            if (!productSelected) {
                productSelected = MG_GLOBAL.products.find(product => product.tree_map[TREE_MAP_KEYS.MARKET_TYPE] === marketType);
            }

            let productPlatformDetail = productSelected[productSelected.slug];
            return {productSelected, productPlatformDetail};
        }

        function parseAttributeMeta(input = []) {
            const result = {
                data: [],
                config: {}
            };

            if (!Array.isArray(input)) {
                console.warn('[parseAttributeMeta] input must be an array');
                return result;
            }

            input.forEach(rawItem => {
                if (typeof rawItem !== 'string') return;

                const item = rawItem.trim();

                // Config entry (starts with @)
                if (item.startsWith('@')) {
                    const match = item.match(/^@([a-zA-Z0-9_-]+)\s*(.*)$/);
                    if (!match) return;

                    const key = match[1];
                    let payload = match[2]?.trim() ?? '';

                    // Normalize smart quotes (same as PHP)
                    payload = payload.replace(/[“”]/g, '"');

                    let value = payload;

                    if (payload !== '') {
                        try {
                            value = JSON.parse(payload);
                        } catch (error) {
                            console.error(
                                `[parseAttributeMeta] JSON decode error "${error.message}" on payload:`,
                                payload
                            );
                        }
                    }

                    result.config[key] = value;
                } else {
                    result.data.push(item);
                }
            });

            return result;
        }


        function buildBadge({
                                text = '',
                                style = 'light'
                            }) {
            if (!text) return '';

            const styleClass = style
                ? `mt-badge-${style}`
                : 'mt-badge-light';

            return `
<div class="mt-card__badge mt-badge mt-badge-rounded ${styleClass}">
    ${text}
</div>
`.trim();
        }

        function buildRadioButton({
                                      isDisabled = false,
                                      id,
                                      name,
                                      value,
                                      title,
                                      checked = false,
                                      rightElementHTML = ''
                                  }) {
            if (!id || !name) {
                console.warn('buildRadioButton: id and name are required');
                return '';
            }

            const checkedAttr = checked ? 'checked="checked"' : '';
            const disabledAttr = isDisabled ? 'disabled="disabled"' : '';

            return `
<input
    type="radio"
    name="${name}"
    value="${value ?? ''}"
    id="${id}"
    ${checkedAttr}
    ${disabledAttr}
    class="mt-circle-radio"
/>
<div class="radio__label__wrapper">
     <label class="mt-card mt-card-dark mt-card-radio" for="${id}">
        <div class="mt-card__header mt-card__header--two-columns">
            <div class="mt-card__title mt-card__title--group">
                <i class="mt-card__radio"></i>
                <span class="mt-card__title__text">${title ?? ''}</span>
            </div>
            ${rightElementHTML || ''}
        </div>
    </label>
</div>
`.trim();
        }

        function formatNumber(value) {
            return '$' + parseInt(value.toString().replace(',', '').replace('$', ''));
        }

        function prepareHelperFunctions(productSelected, productPlatformDetail) {
            let {tree_map: treeMap} = productSelected || {tree_map: {}};
            const treeMapKeys = Object.keys(treeMap);

            return {
                getPriceObject: (sizeSlug, withPlatformSelection = null) => {
                    let attributes = null;
                    Object.keys(treeMap || []).forEach((attr) => {
                        if (withPlatformSelection && attr === 'platform') {
                            attributes = attributes[withPlatformSelection];
                            return;
                        }

                        attributes = !attributes ? productPlatformDetail[sizeSlug] : Object.values(attributes).at(0);
                    });


                    if (!attributes) {
                        return {priceObject: null, productId: null}
                    }

                    const priceObject = attributes.find(item => item['price-monthly'])['price-monthly'] || '$0.00';
                    const productId = attributes.find(item => item['id'])['id'];

                    return {priceObject, productId}
                },
                findValueByTreeData: (sizeSlug, key) => {
                    let level = 0;
                    let properties = null;
                    let value = '';
                    const treeData = productPlatformDetail[sizeSlug];

                    const indexAttribute = treeMapKeys.indexOf(key);

                    while (level <= indexAttribute) {
                        level++;

                        properties = !properties ? treeData : Object.values(properties)[0];

                        if (indexAttribute === level) {
                            value = Object.keys(properties)[0];
                            break;
                        }
                    }

                    return value;
                },
                findValueByTreeDataAll: (sizeSlug, key) => {
                    let level = 0;
                    let properties = null;
                    let values = [];
                    const treeData = productPlatformDetail[sizeSlug];

                    const indexAttribute = treeMapKeys.indexOf(key);

                    while (level <= indexAttribute) {
                        level++;

                        properties = !properties ? treeData : Object.values(properties)[0];

                        if (indexAttribute === level) {
                            values = Object.keys(properties);
                            break;
                        }
                    }

                    return values;
                },
            }
        }

        function renderAccountTypes() {
            const marketType = document.querySelector('[name="market-type"]:checked').value;
            const marketTypeData = MG_GLOBAL.pricingData.marketTypes.find(mt => mt.slug === marketType);

            const container = document.querySelector('#account-type-section .product-section__list');

            let fragmentHTML = '';
            marketTypeData.accountTypes
                .forEach(({name, slug: slugAccountType}, index) => {
                    const data = MG_GLOBAL.accountTypes.find(at => at.slug === slugAccountType) || {slug: '', name: ''};
                    if (!data.slug) {
                        return;
                    }

                    const checked = index === 0;
                    const parsed = parseAttributeMeta(data.attribute_meta ?? []);
                    const config = parsed?.config ?? {};
                    const badge = config?.badge ?? {};

                    let badgeHTML;

                    if (badge) {
                        badgeHTML = buildBadge({
                            text: badge?.text,
                            style: badge?.style
                        });
                    }

                    fragmentHTML += buildRadioButton({
                        id: `account-type-${data.slug}`,
                        name: 'account-type',
                        value: slugAccountType,
                        title: name,
                        checked: checked,
                        rightElementHTML: badgeHTML
                    });
                });

            container.innerHTML = fragmentHTML;
        }

        function renderAccountSizes() {
            const marketType = document.querySelector('[name="market-type"]:checked').value;
            const accountType = document.querySelector('[name="account-type"]:checked').value;

            const {productSelected, productPlatformDetail} = getProduct({marketType, accountType});

            const container = document.querySelector('#account-size-section .product-section__list');

            const {findValueByTreeData, findValueByTreeDataAll, getPriceObject} = prepareHelperFunctions(
                productSelected,
                productPlatformDetail
            );

            let fragmentHTML = '';

            let selected = false;

            MG_GLOBAL.accountSizes.forEach((accountSize) => {
                const sizeSlug = accountSize.slug;
                const treeData = productPlatformDetail[sizeSlug];

                if (!treeData) {
                    return;
                }

                let platforms = findValueByTreeDataAll(sizeSlug, TREE_MAP_KEYS.PLATFORM);

                let _previousPlatformSelected = previousPlatformSelected;
                if (!_previousPlatformSelected) {
                    const platformFound = MG_GLOBAL.platforms.find(p => platforms.includes(p.slug));
                    if (platformFound) {
                        _previousPlatformSelected = platformFound.slug;
                    }
                }

                const {priceObject} = getPriceObject(sizeSlug, _previousPlatformSelected);
                const billingType = findValueByTreeData(sizeSlug, TREE_MAP_KEYS.BILLING_TYPE);

                if (!priceObject) {
                    fragmentHTML += buildRadioButton({
                        id: `account-size-${sizeSlug}`,
                        name: 'account-size',
                        value: sizeSlug,
                        isDisabled: true,
                        title: `$${sizeSlug.toUpperCase()}`,
                        checked: false,
                        rightElementHTML: ''
                    });

                    return;
                }

                let checked = false;

                if (!selected) {
                    checked = true;
                    selected = true;
                }

                let price = formatNumber(priceObject);

                const badgeHTML = buildBadge({
                    text: `${price}/${billingType === 'monthly' ? 'MO' : 'OT'}`,
                    style: 'gray'
                });

                fragmentHTML += buildRadioButton({
                    id: `account-size-${sizeSlug}`,
                    name: 'account-size',
                    value: sizeSlug,
                    title: `$${sizeSlug.toUpperCase()}`,
                    checked: checked,
                    rightElementHTML: badgeHTML
                });
            });

            container.innerHTML = fragmentHTML;
        }

        function renderBrokers() {
            const marketType = document.querySelector('[name="market-type"]:checked').value;
            const accountType = document.querySelector('[name="account-type"]:checked').value;
            const accountSize = document.querySelector('[name="account-size"]:checked').value;

            const {productSelected, productPlatformDetail} = getProduct({marketType, accountType});

            const container = document.querySelector('#account-platform-section .product-section__list');

            const {findValueByTreeDataAll} = prepareHelperFunctions(
                productSelected,
                productPlatformDetail
            );

            let fragmentHTML = '';

            let platformsFound = [];
            let _previousPlatformSelected = previousPlatformSelected;

            for (let priceSize in productPlatformDetail) {
                let platforms = findValueByTreeDataAll(priceSize, TREE_MAP_KEYS.PLATFORM);

                platforms.forEach(p => {
                    if (!platformsFound.includes(p)) {
                        platformsFound.push(p);
                    }
                });
            }

            let platforms = [...MG_GLOBAL.platforms.filter(p => platformsFound.includes(p.slug))];

            if (platforms.length < 4) {
                Array(4 - platforms.length).fill('').forEach(() => {
                    platforms.push({slug: ''});
                })
            }

            let platformSelection;
            let _platforms = findValueByTreeDataAll(accountSize, TREE_MAP_KEYS.PLATFORM);
            if (!_previousPlatformSelected) {
                let platformFound = MG_GLOBAL.platforms.find(p => _platforms.includes(p.slug));
                if (platformFound) {
                    platformSelection = platformFound.slug;
                }
            }

            platforms.forEach((platform) => {
                if (!platform.slug) {
                    fragmentHTML += `<div class="empty-element"></div>`;
                    return;
                }

                let icon = '';

                if (platform.thumbnail_url) {
                    icon = `<img class="mt-card__title__image" src="${platform.thumbnail_url}" alt="Icon">`;
                }

                fragmentHTML += buildRadioButton({
                    id: `platform-${platform.slug}`,
                    name: 'platform',
                    value: platform.slug,
                    title: platform.name,
                    checked: platformSelection === platform.slug || _previousPlatformSelected && _previousPlatformSelected === platform.slug,
                    rightElementHTML: icon
                });

                fragmentHTML = fragmentHTML.replaceAll('mt-card__header--two-columns', 'w-100');
                fragmentHTML = fragmentHTML.replaceAll('mt-card__title--group', '');
            });

            container.innerHTML = fragmentHTML;
        }

        function addTooltip({title, body}) {
            return `
<div class="mt-tooltip">
    <i class="mt-icon mt-icon-base mt-icon_info-solid"
       tabindex="0"
       aria-label="${title} information">
    </i>
    <div class="mt-tooltip__panel" role="tooltip">
        <div class="mt-tooltip__title">${title}</div>
        <div class="mt-tooltip__body">${body}</div>
    </div>
</div>
`
        }

        function addRule({label, value, tooltipHTML = ''}) {
            return `
<div class="plan-card__rule">
    <div class="plan-card__rule-label">
        <div class="plan-card__label-wrapper">
            ${label}
            ${tooltipHTML}
        </div>
    </div>
    <div class="plan-card__rule-value">${value}</div>
</div>
            `;
        }

        async function renderPlanDetail() {
            const marketType = document.querySelector('[name="market-type"]:checked').value;
            const accountType = document.querySelector('[name="account-type"]:checked').value;
            const accountSize = document.querySelector('[name="account-size"]:checked').value;
            const platform = previousPlatformSelected ? previousPlatformSelected : document.querySelector('[name="platform"]:checked')?.value || '';

            const {productSelected, productPlatformDetail} = getProduct({accountType, marketType});

            const {getPriceObject, findValueByTreeData} = prepareHelperFunctions(
                productSelected,
                productPlatformDetail
            );

            const {priceObject, productId} = getPriceObject(accountSize, platform);
            const billingType = findValueByTreeData(accountSize, TREE_MAP_KEYS.BILLING_TYPE);

            let price = formatNumber(priceObject);

            let frequency = billingType === 'monthly' ? 'month' : 'one time fee';
            const accountTypeName = MG_GLOBAL.accountTypes.find((item) => item.slug === accountType)?.name || ''
            const platformName = MG_GLOBAL.platforms.find(p => p.slug === platform)?.name || ''

            const container = document.querySelector('#plan-detail-section .mt-card');

            const planTitle = `${accountTypeName} ${accountSize.toUpperCase()}`;

            container.querySelector('.plan-card__title').innerText = planTitle;
            container.querySelector('.plan-card__subtitle').innerText = `on ${platformName}`

            const metaInfoList = getDefaultMetaInfo(productSelected, productPlatformDetail);

            let attributes = null;
            Object.keys(productSelected?.tree_map || []).forEach(_ => {
                attributes = !attributes ? productPlatformDetail[accountSize] : Object.values(attributes).at(0);
            });

            const metaInfoObject = attributes.find(item => item['meta-info']);
            if (metaInfoObject) {
                const metaInfoContext = metaInfoObject['meta-info'];
                let html = '';

                metaInfoList.forEach(metaInfo => {
                    let tooltipData = tooltips[metaInfo.key];
                    let tooltipHTML = '';

                    if (tooltipData) {
                        if (marketType === 'futures' && metaInfo.key === 'consistency') {
                            tooltipData = tooltips['futures_consistency'];
                        }

                        let title = tooltipData.title;
                        let body = tooltipData.value;

                        tooltipHTML = addTooltip({title, body});
                    }

                    let label = metaInfo.label;
                    if (marketType === 'forex') {
                        label = {
                            max_contracts: 'Leverage',
                            min_trading_days_to_payout: 'Payout Frequency',
                        }[metaInfo.key] || metaInfo.label;
                    } else if (marketType === 'futures') {
                        label = {
                            consistency: 'PRS',
                        }[metaInfo.key] || metaInfo.label;
                    }

                    html += addRule({
                        label: label,
                        value: metaInfoContext[metaInfo.key],
                        tooltipHTML
                    })
                });

                container.querySelector('.plan-card__rules').innerHTML = html;

                window?.mtTooltips?.refresh(container);
            }

            container.querySelector('.plan-card__new-price').innerText = price;
            container.querySelector('.plan-card__period').innerHTML = `${frequency}`;

            const {data: coupons} = await fetchCouponInBatch([productId]);
            const coupon = coupons[productId] || null;
            if (coupon.valid) {
                container.querySelector('.plan-card__coupon').dataset.coupon = coupon.coupon.toUpperCase();
                container.querySelector('.plan-card__coupon').innerText = `SAVE ${formatNumber(coupon.discount_total)} WITH CODE ${coupon.coupon}`.toUpperCase();
                container.querySelector('.plan-card__coupon').style.display = 'block';
                container.querySelector('.plan-card__old-price').style.display = 'block';

                container.querySelector('.plan-card__old-price').innerText = price;
                container.querySelector('.plan-card__new-price').innerText = formatNumber(coupon.final_total);
            } else {
                container.querySelector('.plan-card__coupon').style.display = 'none';
                container.querySelector('.plan-card__old-price').style.display = 'none';
            }

            const link = container.querySelector('.proceed-to-checkout-btn');
            link.href = buildProductUrl(productId);
            link.innerText = `CONTINUE WITH ${planTitle}`;
        }

        async function initialize() {
            renderAccountTypes();
            renderAccountSizes();
            renderBrokers();
            await renderPlanDetail();

            const saved = localStorage.getItem(PAGE_KEY);

            if (saved) {
                const values = JSON.parse(saved);

                const marketType = values['market-type'];
                const accountType = values['account-type'];
                const accountSize = values['account-size'];
                const platform = values['platform'];

                document.querySelector(`[name="market-type"][value="${marketType}"]`).checked = true;
                renderAccountTypes();

                document.querySelector(`[name="account-type"][value="${accountType}"]`).checked = true;
                renderAccountSizes();

                document.querySelector(`[name="account-size"][value="${accountSize}"]`).checked = true;
                renderBrokers();

                document.querySelector(`[name="platform"][value=${platform}]`).checked = true;

                void renderPlanDetail();

                localStorage.removeItem(PAGE_KEY);
            }
        }

        form.querySelectorAll('[name="market-type"]').forEach(element => {
            element.addEventListener("change", function () {
                previousPlatformSelected = null;
                initialize();
            });
        })

        let previousPlatformSelected;
        document.addEventListener('change', function (e) {
            const type = e.target.type;
            if (type !== 'radio') {
                return;
            }

            const inputName = e.target.name;
            if (inputName === 'account-type') {
                previousPlatformSelected = null;
                renderAccountSizes();
                renderBrokers();
                renderPlanDetail();
            } else if (inputName === 'account-size') {
                renderBrokers();
                renderPlanDetail();
            } else if (inputName === 'platform') {
                if (previousPlatformSelected) {
                    previousPlatformSelected = null;
                    renderAccountSizes();
                    renderPlanDetail();
                } else {
                    previousPlatformSelected = e.target.value;
                    renderAccountSizes();
                    renderPlanDetail();
                }
            }
        });

        document.querySelector('.proceed-to-checkout-btn')
            .addEventListener('click', function () {
                const marketType = document.querySelector(`[name="market-type"]:checked`).value;
                const accountType = document.querySelector(`[name="account-type"]:checked`).value;
                const accountSize = document.querySelector(`[name="account-size"]:checked`).value;
                const platform = document.querySelector(`[name="platform"]:checked`).value;

                const values = {
                    'market-type': marketType || null,
                    'account-type': accountType || null,
                    'account-size': accountSize || null,
                    'platform': platform || null
                };

                localStorage.setItem(PAGE_KEY, JSON.stringify(values));
            });

        initialize();
    </script>
<?php
get_footer();