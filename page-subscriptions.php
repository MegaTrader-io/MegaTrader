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
                        <section class="product-section">
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
                                    $checked = '';
                                    if (!$selected && $item['count'] > 0) {
                                        $checked = 'checked';
                                        $selected = true;
                                    }

                                    $slug = esc_attr($item['slug']);
                                    $name = esc_html($item['name']);
                                    $doesNotHaveItems = $item['count'] == 0;
                                    $description = esc_html($item['description']);
                                    $thumbnail = esc_url($item['thumbnail_url']);
                                    ?>
                                    <input type="radio"
                                           name="market-type" <?= $doesNotHaveItems ? 'disabled="disabled"' : '' ?>
                                           value="<?= $slug ?>"
                                           id="<?= $input_id ?>" <?= $checked ?> class="mt-circle-radio">
                                    <div class="radio__label__wrapper">
                                        <label class="mt-card mt-card-dark mt-card-radio" for="<?= $input_id ?>">
                                            <div class="mt-card__header">
                                                <i class="mt-card__radio"></i>
                                            </div>
                                            <div class="mt-card__title">
                                                <span class="mt-card__title__text"><?= $name ?></span>
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
                        <section id="market-type-section" class="product-section">
                            <h2 class="product-section__header mb-3">
                                <span class="product-section__title">2. Account Type</span>
                            </h2>
                            <div class="product-section__list product-section__list_grid">
                                <?php foreach ($accountTypes as $key => $item): ?>
                                    <?php
                                    $input_id = 'account-type-' . $item['slug'];
                                    $checked = $key == 0 ? 'checked' : '';
                                    $slug = esc_attr($item['slug']);
                                    $name = esc_html($item['name']);
                                    $doesNotHaveItems = $item['count'] == 0;
                                    $description = esc_html($item['description']);
                                    $thumbnail = esc_url($item['thumbnail_url']);

                                    $parsed = parse_attribute_meta($item['attribute_meta'] ?? []);
                                    $config = isset($parsed['config']) ? $parsed['config'] : [];
                                    $badge = isset($parsed['config']['badge']) ? $parsed['config']['badge'] : [];
                                    ?>
                                    <input type="radio"
                                           name="account-type" <?= $doesNotHaveItems ? 'disabled="disabled"' : '' ?>
                                           value="<?= $slug ?>"
                                           id="<?= $input_id ?>" <?= $checked ?> class="mt-circle-radio">
                                    <div class="radio__label__wrapper">
                                        <label class="mt-card mt-card-dark mt-card-radio" for="<?= $input_id ?>">
                                            <div class="mt-card__header">
                                                <i class="mt-card__radio"></i>
                                            </div>
                                            <div class="mt-card__title">
                                                <span class="mt-card__title__text"><?= $name ?></span>
                                                <?php if ($badge):
                                                    $badge_style_class = isset($badge['style']) ? 'mt-badge-' . $badge['style'] : 'mt-badge-light';
                                                    $badge_text = $badge['text'] ?? '';
                                                    ?>
                                                    <div class="mt-dropdown__badge mt-card__badge mt-badge mt-badge-rounded-sm <?= $badge_style_class ?>"><?= $badge_text ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </section>
                        <section id="account-type-section" class="product-section">
                            <h2 class="product-section__header mb-3">
                                <span class="product-section__title">3. Account Size</span>
                            </h2>
                            <div class="product-section__list product-section__list_grid">
                                <?php foreach ($accountSizes as $key => $item): ?>
                                    <?php
                                    $input_id = 'account-size-' . $item['slug'];
                                    $checked = $key == 0 ? 'checked' : '';
                                    $slug = esc_attr($item['slug']);
                                    $name = '$' . esc_html($item['slug']);
                                    $doesNotHaveItems = $item['count'] == 0;
                                    $description = esc_html($item['description']);
                                    $thumbnail = esc_url($item['thumbnail_url']);

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
                                           name="account-size" <?= $doesNotHaveItems ? 'disabled="disabled"' : '' ?>
                                           value="<?= $slug ?>"
                                           id="<?= $input_id ?>" <?= $checked ?> class="mt-circle-radio">
                                    <div class="radio__label__wrapper">
                                        <label class="mt-card mt-card-dark mt-card-radio" for="<?= $input_id ?>">
                                            <div class="mt-card__header">
                                                <i class="mt-card__radio"></i>
                                            </div>
                                            <div class="mt-card__title">
                                                <span class="mt-card__title__text text-uppercase"><?= $name ?></span>
                                                <div class="mt-dropdown__badge mt-card__badge mt-badge mt-badge-rounded mt-badge-gray"><?= mt_price_plain($price) . '/' . $frequency ?></div>
                                            </div>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </section>
                        <section id="account-size-section" class="product-section">
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
                                    $checked = $key == 0 ? 'checked' : '';
                                    $slug = esc_attr($item['slug']);
                                    $name = esc_html($item['slug']);
                                    $doesNotHaveItems = $item['count'] == 0;
                                    $description = esc_html($item['description']);
                                    $thumbnail = esc_url($item['thumbnail_url']);
                                    ?>
                                    <input type="radio"
                                           name="platform" <?= $doesNotHaveItems ? 'disabled="disabled"' : '' ?>
                                           value="<?= $slug ?>"
                                           id="<?= $input_id ?>" <?= $checked ?> class="mt-circle-radio">
                                    <div class="radio__label__wrapper">
                                        <label class="mt-card mt-card-dark mt-card-radio" for="<?= $input_id ?>">
                                            <div class="mt-card__header">
                                                <i class="mt-card__radio"></i>
                                            </div>
                                            <div class="mt-card__title">
                                                <span class="mt-card__title__text"><?= ucfirst($name) ?></span>
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
                                        <div class="plan-card__title">Growth Plan 25K</div>
                                        <div class="plan-card__subtitle">on MegaTraderX</div>
                                    </div>
                                    <div class="plan-card__pricing-group">
                                        <div class="mt-card__badge mt-badge mt-badge-rounded mt-badge-secondary">
                                            SAVE $42 WITH CODE DEC
                                        </div>
                                        <div class="plan-card__pricing">
                                            <div class="plan-card__old-price">$139</div>
                                            <div class="plan-card__new-price">$139</div>
                                            <div class="plan-card__period">per month</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-card__body plan-card__body">
                                    <div class="plan-card__rules">
                                        <div class="plan-card__rules-header">Funded Rules</div>
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
        const PAGE_KEY = '<?= $page_slug ?>-storage';
        window[PAGE_KEY] = {
            screenLoaded: false
        };

        const REMEMBER_PREVIOUS_SELECTION = true;
        const CHECKOUT_URL = '<?= home_url('/checkout/?add-to-cart=PRODUCT_ID') ?>';
        const MG_GLOBAL = {
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

        function getFormValues(formEl) {
            const data = {};
            const formData = new FormData(formEl);

            for (const [key, value] of formData.entries()) {
                data[key] = value;
            }
            return data;
        }

        function getProduct({accountType, marketType}) {
            let productSelected = MG_GLOBAL.products.find(product => product.tree_map['account-types'] === accountType && product.tree_map['market-type'] === marketType);
            if (!productSelected) {
                productSelected = MG_GLOBAL.products.find(product => product.tree_map['market-type'] === marketType);
            }

            let productPlatformDetail = productSelected[productSelected.slug];
            return {productSelected, productPlatformDetail};
        }

        async function handleFormChanges() {
            let values = getFormValues(form);
            const marketType = values['market-type'];
            const accountType = values['account-type'];

            const {productSelected, productPlatformDetail} = getProduct({accountType, marketType});
        }

        function renderOptions({productSelected, productPlatformDetail}) {
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
                }
                // Plain data entry
                else {
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
<div class="mt-dropdown__badge mt-card__badge mt-badge mt-badge-rounded-sm ${styleClass}">
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
        <div class="mt-card__header">
            <i class="mt-card__radio"></i>
        </div>
        <div class="mt-card__title">
            <span class="mt-card__title__text">${title ?? ''}</span>
            ${rightElementHTML || ''}
        </div>
    </label>
</div>
`.trim();
        }

        function renderAccountTypes({marketType}) {
            const marketTypeData = MG_GLOBAL.pricingData.marketTypes.find(mt => mt.slug == marketType);

            const container = document.querySelector('#market-type-section .product-section__list');

            let fragmentHTML = '';
            marketTypeData.accountTypes
                .forEach(({name, slug: slugAccountType}, index) => {
                    const data = MG_GLOBAL.accountTypes.find(at => at.slug == slugAccountType) || {slug: '', name: ''};
                    if (!data.slug) {
                        return;
                    }

                    const checked = index == 0;
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

            for (const priceSize in productPlatformDetail) {
                console.info(productSelected)
            }

        }

        function renderBrokers() {

        }

        function renderPlanDetail() {

        }

        form.addEventListener("change", handleFormChanges);

        form.querySelectorAll('[name="market-type"]').forEach(element => {
            element.addEventListener("change", function (e) {
                const marketType = e.currentTarget.value;
                const {productSelected, productPlatformDetail} = getProduct({marketType});

                renderAccountTypes({marketType});
                renderAccountSizes({productSelected, productPlatformDetail});
            });
        })

        document.addEventListener("DOMContentLoaded", () => {
            setTimeout(() => {
                handleFormChanges();
            }, 0);
        });
    </script>
<?php
get_footer();