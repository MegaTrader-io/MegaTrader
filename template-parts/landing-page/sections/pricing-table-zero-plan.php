<?php
$page_slug = pathinfo(__FILE__, PATHINFO_FILENAME);
$remember_previous_selection = true;

/**
 * 1. Cargar data base de WooCommerce
 */
$mt_products_data = get_products_with_attributes();
$mt_attributes = $mt_products_data['attributes'] ?? [];
$mt_products_raw = $mt_products_data['products'] ?? [];

/**
 * 2. Clasificar atributos por taxonomía
 */
$mt_account_sizes = [];
$mt_account_types = [];
$mt_platforms = [];
$mt_market_types = [];
$mt_billing_types = [];

foreach ($mt_attributes as $attr) {
    switch ($attr['taxonomy']) {
        case 'pa_market-type':
            $mt_market_types[] = $attr;
            break;
        case 'pa_account-size':
            $mt_account_sizes[] = $attr;
            break;
        case 'pa_account-types':
            $mt_account_types[] = $attr;
            break;
        case 'pa_billing-type':
            $mt_billing_types[] = $attr;
            break;
        case 'pa_platform':
            $mt_platforms[] = $attr;
            break;
    }
}

require_once get_template_directory() . '/mt_prices_manager.php';

$instance = new MT_PRICESManager(
        products: $mt_products_raw, marketTypes: $mt_market_types, accountTypes: $mt_account_types, sizes: $mt_account_sizes, platforms: $mt_platforms,
);

$marketTypes = $instance->marketTypes()->filtered(function ($marketType) {
    return $marketType['count'] > 0;
});

$marketType = $marketTypes[0] ?? null;
$marketTypeSlug = $marketType['slug'] ?? null;
$accountTypes = $instance->accountTypesByMarketType($marketType['slug']);
$accountTypeZeroPlan = array_values(array_filter($accountTypes, function ($accountType) {
    return $accountType['slug'] == AccountType::ZERO_PLAN->value;
}));

$accountType = $accountTypeZeroPlan[0] ?? null;
$accountTypeSlug = $accountType['slug'] ?? '';

$mt_product = $instance->productByMarketTypeAndAccountType($marketTypeSlug, $accountTypeSlug);

$accountSizes = $instance->accountSizesByMarketTypeAndAccountType($marketTypeSlug, $accountTypeSlug);
$platforms = $instance->platformsByMarketTypeAccountTypeAndSizes($marketTypeSlug, $accountTypeSlug);

/**
 * 3. Definir valores iniciales
 */
$mt_default_market_type = $marketTypes[0] ?? null;
$mt_default_platform = $platforms[0] ?? null;
$mt_platform_thumbnail_url = $mt_default_platform['thumbnail_url'] ?? '';
$mt_default_market_type_slug = $mt_default_market_type ?? [];
$market_types_allowed = [];

$mt_default_account_type = $accountTypeZeroPlan[0] ?? [];
$mt_default_slug = $mt_default_account_type['slug'] ?? '';
$mt_account_thumbnail_url = $mt_default_account_type['thumbnail_url'] ?? '';
$mt_size = $accountSizes[0] ?? '';

$mt_plan_list = [];
$mt_default_meta_info = [];

if ($mt_product) {
    $mt_slug = $mt_product['slug'] ?? null;
    $parent_id = $mt_product['id'];

    $indexAttribute = array_search('platform', array_keys($mt_product['tree_map']), true);

    foreach ($accountSizes as $size) {
        $properties = [];
        $structured_data = $mt_product[$mt_slug][$size['slug']];

        foreach (array_keys($mt_product['tree_map']) as $key => $attr) {
            if ($key === $indexAttribute) {
                $hasValue = array_keys($properties)[0];
                $properties = !$hasValue ? array_values($properties)[0] : $properties[$mt_default_platform['slug']];
            } else {
                $properties = count($properties) === 0 ? array_values($structured_data) : array_values($properties)[0];
            }
        }

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

        foreach (Label::PRODUCT_META as $meta_key => $meta_label) {
            if (!empty($meta_info[$meta_key])) {
                $mt_default_meta_info[$meta_key] = true;
            }
        }

        $mt_plan_list[] = [
                'id' => $variation_id,
                'parent_id' => $parent_id,
                'price' => $price,
                'size' => $size,
                'meta_info_list' => $meta_info
        ];
    }
}

$mt_best_products = mt_most_popular_products();

/**
 * 11. Helper de renderizado de meta info
 */
function mt_render_template_meta_info($value = '', $label = '', $classes = '')
{
    return <<<HTML
<div class="mega-info-row {$classes}">
    <div class="mega-info-row__label">{$label}</div>
    <div class="mega-info-row__value text-truncate">{$value}</div>
</div>
HTML;
}

?>

<section id="pricing" class="pricing-table-container landing-bs-container">
    <header class="pricing-table-container__header-wrapper text-center">
        <h2 class="pricing-table-container__title">
            Choose <span class="pricing-table-container__title--hidden-md text-white">your</span> <span>account</span>
            type
        </h2>
        <p class="pricing-table-container__subtitle">
            Choose from flexible account sizes and plans tailored to your trading style—whether you're growing your
            skills or ready to trade real capital with confidence.
        </p>
    </header>

    <div class="market-type-bs">
        <div class="market-type-bs__group">
            <?php foreach ($marketTypes as $index => $mt_market_type): ?>
                <?php
                $slug = esc_attr($mt_market_type['slug']);
                $name = esc_html($mt_market_type['name']);
                $radio_id = esc_attr('market-type-' . $slug);
                $checked_attr = $index === 0 ? 'checked="true"' : '';
                ?>

                <input type="radio" name="market-type" value="<?= $slug ?>"
                       id="<?= $radio_id ?>" <?= $checked_attr ?>/>
                <div>
                    <label for="<?= $radio_id ?>" data-slug="<?= $mt_market_type['slug'] ?>"
                           class="market-type-bs__item">
                        <span class="market-type-bs__label"><?= $mt_market_type['name'] ?></span>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="pricing-table-fragment-wrapper">
        <?php

        get_template_part(
                'template-parts/landing-page/sections/pricing-table-fragment-bs',
                null,
                [
                        'layout_type' => LayoutType::ZeroPlan->value,
                    // principales de control
                        'mt_account_types' => $accountTypeZeroPlan,
                        'mt_default_platform' => $mt_default_platform,
                        'mt_default_market_type' => $mt_default_market_type,
                        'mt_default_slug' => $mt_default_slug,

                    // planes
                        'mt_plan_list' => $mt_plan_list,
                        'mt_default_meta_info' => $mt_default_meta_info,
                        'mt_best_products' => $mt_best_products,

                    // funciones helpers
                        'mt_render_template_meta_info' => 'mt_render_template_meta_info',

                    // datos visuales
                        'mt_size' => $mt_size,
                        'mt_product' => $mt_product,
                        'mt_account_sizes' => $accountSizes,

                    // utilidades opcionales usadas en el loop
                        'mt_product_data' => $mt_products_data,
                ]
        );
        ?>
    </div>
</section>

<script>
    const PAGE_KEY = '<?= $page_slug ?>-storage';
    window[PAGE_KEY] = {
        screenLoaded: false
    };

    const PLATFORMS = <?= wp_json_encode($instance->platforms()->getList(), JSON_PRETTY_PRINT) ?>;

    const TREE_MAP_KEYS = Object.freeze({
        ACCOUNT_SIZE: 'account-size',
        ACCOUNT_TYPES: 'account-types',
        BILLING_TYPE: 'billing-type',
        MARKET_TYPE: 'market-type',
        PLATFORM: 'platform'
    });

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
                    return {priceObject: null, productId: null, attributes: null}
                }

                const priceObject = attributes.find(item => item['price-monthly'])['price-monthly'] || '$0.00';
                const productId = attributes.find(item => item['id'])['id'];

                return {priceObject, productId, attributes}
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

    document.addEventListener("DOMContentLoaded", () => {
        const VISIBLE_COUNT = 4;
        let currentIndex = 0;

        /**
         * Genera el bloque HTML de un plan de la tabla de precios (versión fiel al markup original).
         *
         * @param {Object} data - Objeto de configuración.
         * @param {string} data.size - Ejemplo: "50k".
         * @param {boolean} data.isMostPopular - Muestra o no la etiqueta "Most popular".
         * @param {boolean} data.hasCoupon - Si tiene cupón o no.
         * @param {Object} [data.coupon] - Información del cupón (si aplica).
         * @param {string} [data.coupon.code]
         * @param {string|number} [data.coupon.discountTotal]
         * @param {string} data.price - Precio mostrado, ej: "$120".
         * @param {string} data.frequencyText - Texto de frecuencia ("per month" o "one time fee").
         * @param {Array<{key:string,label:string,value:string}>} data.metaInfo - Lista de atributos.
         * @param {string} data.checkoutUrl - URL completa del checkout.
         * @param {string} [data.assetsUrl='/wp-content/themes/megatrader-addons/assets/img/landing-page']
         * @returns {string} HTML raw del plan.
         */
        function createPriceTablePlan(data) {
            try {
                const {
                    size,
                    isMostPopular = false,
                    hasCoupon = false,
                    coupon = {},
                    price,
                    frequencyText,
                    metaInfo = [],
                    checkoutUrl,
                    assetsUrl = '/wp-content/themes/megatrader-addons/assets/img/landing-page',
                } = data || {};

                const planClass = 'price-table__plan--regular-plan';
                const btnClass = 'mega-btn-default-md';

                const mostPopularHTML = `
      <div class="price-table__most-popular-badge">
        <div class="price-table__most-popular-badge-wrapper">
          <img src="${assetsUrl}/flash.svg"
               class="price-table__most-popular-badge-icon"
               alt="flash" width="24" height="24">
          <div class="price-table__most-popular-badge-text">
            Most popular
          </div>
        </div>
      </div>
    `;

                const couponHTML = `
      <div style="${hasCoupon ? '' : 'display:none;'}" class="price-information__summary">
        <div class="coupon-before-price" data-price="${size}">
          <span></span>
        </div>
        <div class="badge-coupon w-100" data-price="${size}">
          <div class="badge-coupon__wrapper">
            <div class="badge-coupon__text text-truncate">
              Save
              <span class="badge-coupon__discount_total">
                ${hasCoupon ? coupon.discountTotal || '' : ''}
              </span>
              with code
            </div>
            <svg width="1" height="24" viewBox="0 0 1 24" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
              <line x1="0.5" y1="0" x2="0.5" y2="24" stroke="#404040"></line>
            </svg>
            <div class="badge-coupon__code tw-uppercase tw-justify-start">
              ${hasCoupon ? coupon.code || '' : ''}
            </div>
          </div>
        </div>
      </div>
    `;

                const metaRowsHTML = metaInfo
                    .map(
                        ({key, label, value}) => `
      <div class="mega-info-row">
        <div class="mega-info-row__label" data-key="${key}">${label}</div>
        <div class="mega-info-row__value text-truncate">${value || ''}</div>
      </div>`
                    )
                    .join('');

                return `
<li class="slider__frame glide__slide">
  <div class="price-table__plan ${planClass}" data-price="${size}">
    <div class="price-table__size">
      ${mostPopularHTML}
      <div class="price-table__title">${size} Account</div>
    </div>

    <div class="price-table__right-line price-information" data-price="${size}">
      <div class="w-100">
        ${couponHTML}
        <div class="price-information__price">
          <span class="price-plan" data-price="${size}">${price}</span>
          <span class="frequency-plan" data-price="${size}"> ${frequencyText}</span>
        </div>
      </div>
    </div>

    <div class="mega-info-row d-none template-metaInfo">
      <div class="mega-info-row__label"></div>
      <div class="mega-info-row__value text-truncate"></div>
    </div>

    <div class="price-table__right-line price-table-attributes metaInfo" data-price="${size}">
      ${metaRowsHTML}
    </div>

    <div class="price-table__right-line price-table__footer" data-price="${size}">
      <a href="${checkoutUrl}"
         class="proceed-to-checkout-btn mega-btn-md ${btnClass} w-100">
        <img src="${assetsUrl}/flash-teal.svg" alt="flash teal" width="24" height="24">
        GET FUNDED WITH $${size}
      </a>
    </div>
  </div>
</li>
`;
            } catch (err) {
                console.error('Error creating HTML for plan:', err);
                return '';
            }
        }

        async function fetchCouponInBatch(productIds) {
            const URL = `/wp-json/custom/v1/best-coupon-in-batch?ids=${productIds}`;
            if (!mtCache[URL]) {
                const responseCoupons = await fetch(URL);
                mtCache[URL] = await responseCoupons.json();
            }

            return mtCache[URL];
        }

        async function fetchPricingFragment(marketType) {
            const URL = `/wp-json/custom/v1/pricing-fragment?marketType=${encodeURIComponent(marketType)}&accountType=zero-plan`;

            if (!mtCache[URL]) {
                const _response = await fetch(URL, {cache: 'no-store'});
                mtCache[URL] = await _response.json();
            }

            return mtCache[URL];
        }

        function buildProductUrl(productId) {
            const CHECKOUT_URL = MG_GLOBAL.CHECKOUT_URL;
            let checkoutUrl = CHECKOUT_URL.replace('PRODUCT_ID', productId);

            const couponElement = document.querySelector('.badge-coupon__code');
            const coupon = couponElement?.innerText?.trim();

            if (coupon) {
                checkoutUrl = checkoutUrl + '&coupon=' + coupon;
            }

            return checkoutUrl;
        }

        function formatNumber(value) {
            return '$' + parseInt(value.toString().replace(',', '').replace('$', ''));
        }

        function updatePoints() {
            let bottomPoints = 0;
            getPriceTable().style.setProperty('--current-slider-height', bottomPoints + 'px');
        }

        setTimeout(() => {
            const saved = localStorage.getItem(PAGE_KEY);

            if (saved) {
                const values = JSON.parse(saved);

                const event = new CustomEvent("trigger:select-account-type", {
                    detail: {
                        accountType: values['account-type'],
                        defaultPlatform: values['platform'],
                        defaultMarketType: values['market-type'],
                        defaultAccountSize: values['account-size'],
                    }
                });

                document.getElementById('pricing').dispatchEvent(event);
                localStorage.removeItem(PAGE_KEY)
            }
        }, 0);

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


        document.addEventListener('click', function (e) {
            const target = e.target.closest('.proceed-to-checkout-btn');
            if (!target) return;

            try {
                const parent = target.parentElement;
                const accountSize = parent?.dataset?.price;
                const input = document.querySelector('input[name="account-type"]:checked');

                if (!input) {
                    console.warn('No account type selected.');
                    return;
                }

                const values = {
                    'market-type': input.dataset.defaultMarketType || null,
                    'account-size': accountSize || null,
                    'account-type': input.value || null,
                    'platform': input.dataset.defaultPlatform || null
                };

                // Validación mínima antes de guardar
                if (!values['account-size'] || !values['account-type']) {
                    console.error('Incomplete data to save in localStorage', values);
                    return;
                }

                localStorage.setItem(PAGE_KEY, JSON.stringify(values));
                console.info('Saved to localStorage:', values);
            } catch (err) {
                console.error('Error handling proceed-to-checkout click:', err);
            }
        });


        function loadChooseYourAccountSize(fn) {
            document.getElementById('pricing')
                .addEventListener("trigger:select-account-type", async (e) => {
                    const firstMarketType = document.querySelector('.market-type-bs [name="market-type"]:nth-child(1)').value;
                    const {accountType, defaultAccountSize, defaultMarketType} = e.detail

                    if (firstMarketType !== defaultMarketType) {
                        const target = document.querySelector(`.market-type-bs [name="market-type"][value="${defaultMarketType}"]`)
                        target.checked = true;
                        await handlerSelectByMarketType(target, accountType);
                    }

                    const accountTypeSelected = document.querySelector('input[name="account-type"][value=' + accountType + ']')
                    accountTypeSelected.checked = true;

                    handlerSelectByAccountType(accountTypeSelected);

                    const pricingTableGlide = document.querySelector('.price-table.price-table__glide');
                    if (pricingTableGlide) {
                        const cardPlanSize = pricingTableGlide.querySelector('.price-table__plan[data-price="' + defaultAccountSize + '"]');

                        const items = Array.from(pricingTableGlide.querySelectorAll('.slider__slides > li'));

                        const planSizeIndexSelection = items.indexOf(cardPlanSize.parentElement);

                        if (window.tableSliderInstance && planSizeIndexSelection !== -1) {
                            window.tableSliderInstance.go(`=${planSizeIndexSelection}`);
                        }
                    }
                });


            function handlerSelectByAccountType(target) {
                if (!target) {
                    return;
                }

                const input = target.currentTarget || target;
                const accountType = input.value;

                document.querySelectorAll('.mt-pricing-table-benefits').forEach(element => {
                    element.classList.add('d-none');
                });

                document.querySelector(`.mt-pricing-table-benefits[data-account-type-benefits="${accountType}"]`)?.classList.remove('d-none');

                resetIndexPaginationForPricingTable();

                rerenderPriceTable(movePricingCards('init'));
            }

            function resetIndexPaginationForPricingTable() {
                currentIndex = 0;
            }

            async function handlerSelectByMarketType(target, accountType = null) {
                const input = target.currentTarget || target;
                const marketType = input.value;

                resetIndexPaginationForPricingTable();

                console.info('[MarketType Selected]', marketType);
                try {
                    const data = await fetchPricingFragment(marketType);

                    if (!data.success) {
                        console.error('Backend error:', data.message);
                        return;
                    }

                    const htmlContainer = document.querySelector('.pricing-table-fragment-wrapper');
                    if (!htmlContainer) {
                        console.warn('No se encontró el contenedor .pricing-table-fragment-wrapper');
                        return;
                    }

                    document.dispatchEvent(new CustomEvent('mt:destroySliderPricingTable', {detail: {}}));

                    // 🔄 Inyectar nuevo fragmento
                    htmlContainer.innerHTML = data.html;

                    // 🕒 Esperar un tick para asegurar que el DOM esté actualizado
                    await new Promise(resolve => requestAnimationFrame(resolve));

                    // ✅ Buscar el primer input[name="account-type"] del nuevo fragmento
                    let firstAccountTypeInput = htmlContainer.querySelector('[name="account-type"]');
                    if (accountType) {
                        firstAccountTypeInput = htmlContainer.querySelector(`[name="account-type"][value="${accountType}"]`);
                    }

                    document.dispatchEvent(new CustomEvent('mt:refreshSliderPricingTable', {detail: {}}));

                    if (firstAccountTypeInput) {
                        console.info('[Auto-select AccountType]', firstAccountTypeInput.value);

                        const {pricesKeys} = getPricesInfoByAccountTypeAndMarketType(firstAccountTypeInput.value, firstAccountTypeInput.dataset.defaultMarketType)

                        toggleArrowsToMovePrices(pricesKeys.length > 4);

                        void fn({
                            accountType: firstAccountTypeInput.value,
                            defaultPlatform: firstAccountTypeInput.dataset.defaultPlatform,
                            defaultMarketType: firstAccountTypeInput.dataset.defaultMarketType,
                            prices: pricesKeys.splice(0, VISIBLE_COUNT)
                        });
                    } else {
                        console.warn('No se encontró ningún input[name="account-type"] en el nuevo fragmento.');
                    }

                    // 🔁 Reasignar listeners dentro del nuevo HTML renderizado
                    htmlContainer.querySelectorAll('[name="account-type"]').forEach(btn => {
                        btn.addEventListener('click', handlerSelectByAccountType);
                    });
                } catch (error) {
                    console.error('Error al cargar el fragmento de pricing:', error);
                }
            }


            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.mt-prices-left, .mt-prices-right');
                if (!btn) return;

                const direction = btn.classList.contains('mt-prices-left') ? 'left' : 'right';

                if (btn.classList.contains('mt-prices-disabled')) {
                    return;
                }

                try {
                    const prices = movePricingCards(direction);
                    rerenderPriceTable(prices);
                } catch (err) {
                    console.error(`[Navigation] Error moviendo precios hacia ${direction}:`, err);
                }
            });

            function rerenderPriceTable(prices = []) {
                document.querySelector('.price-table ul').innerHTML = buildPricesCardsHTML(prices);

                const [marketType, accountType, platform] = marketTypeAndAccountTypeSelect();

                const slides = document.querySelector('.price-table .glide__slides');
                if (slides) {
                    slides.style.gridTemplateColumns = `repeat(${prices.length}, 1fr)`;
                }

                void fn({
                    accountType,
                    defaultPlatform: platform,
                    defaultMarketType: marketType,
                    prices
                });
            }

            // 📌 Inicializar listeners principales
            document.querySelectorAll('[name="account-type"]').forEach(btn => btn.addEventListener('click', handlerSelectByAccountType));
            document.querySelectorAll('[name="market-type"]').forEach(btn => btn.addEventListener('click', handlerSelectByMarketType));

            const targetSelection = document.querySelector('[name="account-type"]:checked');
            targetSelection && handlerSelectByAccountType(targetSelection);
        }

        window.mtCache = {};
        loadChooseYourAccountSize(async (params) => {
            const priceTable = getPriceTable();
            const {prices} = params;

            const height = document.querySelector('.price-table .glide__slide--active .price-table__plan--most-popular') || document.querySelector('.price-table .glide__slide--active .price-table__plan--regular-plan') ? 0 : 24;
            priceTable.style.setProperty('--current-slider-height', height + 'px');

            const dropdownAccountTypeComponent = document.querySelector('.mt-select-ac-type');

            dropdownAccountTypeComponent.querySelector('.selected')?.classList.remove('selected');

            const dropdownAccountTypeOption = dropdownAccountTypeComponent.querySelector('.dropdown-item__wrapper[data-account-type-slug=' + params['accountType'] + ']');
            const productSelected = MG_GLOBAL.products.find(product => product.tree_map['account-types'] === params.accountType && product.tree_map['market-type'] === params.defaultMarketType);
            let productPlatformDetail = productSelected[productSelected.slug];

            if (prices) {
                const tmpPrices = {};
                prices.forEach(price => {
                    const priceFound = productPlatformDetail[price];
                    if (priceFound) {
                        tmpPrices[price] = priceFound;
                    }
                })

                productPlatformDetail = tmpPrices;
            }

            dropdownAccountTypeOption.querySelector('label').classList.add('selected');
            const accountTypeIcon = dropdownAccountTypeOption.querySelector('img');
            const accountTypeText = dropdownAccountTypeOption.querySelector('.mt-dropdown__item-label');
            const accountTypeBadge = dropdownAccountTypeOption.querySelector('.mt-card__badge');

            if (accountTypeIcon) {
                dropdownAccountTypeComponent.querySelector('.mt-dropdown__btn-icon').src = accountTypeIcon.src;
            }

            dropdownAccountTypeComponent.querySelector('.mt-dropdown__btn-label').innerText = accountTypeText.innerText;

            dropdownAccountTypeComponent
                .querySelector('.mt-dropdown__btn-inner')
                .nextElementSibling
                ?.remove();

            if (accountTypeBadge) {
                const badge = accountTypeBadge.cloneNode(true);

                dropdownAccountTypeComponent
                    .querySelector('.mt-select-ac-type__selection')
                    .appendChild(badge);
            }

            const metaInfoList = getDefaultMetaInfo(productSelected, productPlatformDetail);

            const mostPopularElement = document.querySelector('.price-table__plan.price-table__plan--most-popular');

            if (mostPopularElement) {
                mostPopularElement.classList.remove('price-table__plan--most-popular');
                mostPopularElement.classList.add('price-table__plan--regular-plan');

                const btnGetPlan = mostPopularElement.querySelector('.mega-btn-md');
                if (btnGetPlan) {
                    btnGetPlan.classList.remove('mega-btn-primary-md');
                    btnGetPlan.classList.add('mega-btn-default-md');
                }
            }

            const products = [];

            const {findValueByTreeData, getPriceObject, findValueByTreeDataAll} = prepareHelperFunctions(
                productSelected,
                productPlatformDetail
            );

            for (const priceSize in productPlatformDetail) {
                const billingType = findValueByTreeData(priceSize, TREE_MAP_KEYS.BILLING_TYPE);
                const platformsByPrice = findValueByTreeDataAll(priceSize, TREE_MAP_KEYS.PLATFORM);

                let defaultPlatform;
                if (platformsByPrice.length > 1) {
                    const _platformFound = PLATFORMS.find(p => platformsByPrice.includes(p.slug));
                    defaultPlatform = _platformFound?.slug;
                } else {
                    defaultPlatform = platformsByPrice[0]?.slug || null;
                }

                let {priceObject, attributes} = getPriceObject(priceSize, defaultPlatform);

                const productId = attributes.find(item => item['id'])['id'];

                products.push({productId, priceSize, priceObject});

                const isMostPopular = !!Object.values(MG_GLOBAL.bestProducts).find(item => item && item.variation_id === Number(productId))
                const priceCard = document.querySelector(`.price-table__plan[data-price="${priceSize}"]`);

                if (!priceCard) {
                    return;
                }

                if (isMostPopular) {
                    priceCard.classList.add('price-table__plan--most-popular');
                    priceCard.classList.remove('price-table__plan--regular-plan');

                    const btnGetPlan = priceCard.querySelector('.mega-btn-md');
                    if (btnGetPlan) {
                        btnGetPlan.classList.add('mega-btn-primary-md', 'mega-btn-primary--icon-md');
                        btnGetPlan.classList.remove('mega-btn-default-md');
                    }
                } else {
                    priceCard.classList.remove('price-table__plan--most-popular');
                    priceCard.classList.add('price-table__plan--regular-plan');

                    const btnGetPlan = priceCard.querySelector('.mega-btn-md');
                    if (btnGetPlan) {
                        btnGetPlan.classList.remove('mega-btn-primary-md', 'mega-btn-primary--icon-md');
                        btnGetPlan.classList.add('mega-btn-default-md');
                    }
                }

                let price = formatNumber(priceObject);
                const priceInformation = document.querySelector(`.price-information[data-price="${priceSize}"]`);
                const pricePanel = document.querySelector(`.price-plan[data-price="${priceSize}"]`);
                const frequencyPanel = document.querySelector(`.frequency-plan[data-price="${priceSize}"]`);
                if (pricePanel) {
                    priceInformation.querySelector('.price-information__summary').style.display = 'none';
                    priceInformation.classList.remove('has-coupon', 'tw-min-h-[140px]', 'tw-items-center');

                    pricePanel.innerText = price;
                }

                if (frequencyPanel) {
                    frequencyPanel.innerText = ` ${billingType === 'monthly' ? 'per month' : 'one time fee'}`;
                }

                const metaInfoObject = attributes.find(item => item['meta-info']);
                if (metaInfoObject) {
                    const metaInfoContext = metaInfoObject['meta-info'];
                    const metaInfoElement = document.querySelector(`.metaInfo[data-price="${priceSize}"]`);
                    const template = document.querySelector(`.template-metaInfo`);

                    metaInfoElement.innerHTML = '';

                    metaInfoList.forEach(metaInfo => {
                        const row = template.cloneNode(true);
                        row.classList.remove('template-metaInfo', 'd-none');
                        const labelHTML = row.querySelector('.mega-info-row__label');
                        labelHTML.dataset.key = metaInfo.key;
                        labelHTML.innerText = metaInfo.label;

                        row.querySelector('.mega-info-row__value').innerText = metaInfoContext[metaInfo.key];
                        metaInfoElement.appendChild(row)
                    })
                }

                const url = buildProductUrl(productId);
                const link = document.querySelector(`.price-table__footer[data-price="${priceSize}"] a`);
                link.href = url;
            }

            const productIds = products.map(product => Number(product.productId)).join(',')

            const {data: coupons} = await fetchCouponInBatch(productIds);

            products.forEach(({productId, priceSize, priceObject}) => {
                const coupon = coupons[productId];
                let price = formatNumber(priceObject);

                const priceInformation = document.querySelector(`.price-information[data-price="${priceSize}"]`);
                const pricePanel = document.querySelector(`.price-plan[data-price="${priceSize}"]`);
                if (pricePanel) {

                    const badgeCoupon = document.querySelector(`.badge-coupon[data-price="${priceSize}"]`);
                    const couponBeforePrice = document.querySelector(`.coupon-before-price[data-price="${priceSize}"]`);

                    const isValidCoupon = coupon && coupon.valid;
                    priceInformation.querySelector('.price-information__summary').style.display = isValidCoupon ? 'flex' : 'none';

                    let couponValue = '';

                    if (isValidCoupon) {
                        priceInformation.classList.add('has-coupon');

                        couponBeforePrice.querySelector('span').innerText = price;
                        pricePanel.innerText = formatNumber(coupon.final_total);
                        badgeCoupon.querySelector('.badge-coupon__discount_total').innerText = formatNumber(coupon.discount_total);
                        couponValue = coupon.coupon.toUpperCase();
                    } else {
                        priceInformation.classList.remove('has-coupon', 'price-information--min-h-112', 'align-items-center');
                        pricePanel.innerText = price;
                    }

                    badgeCoupon.querySelector('.badge-coupon__code').dataset.coupon = couponValue;
                    badgeCoupon.querySelector('.badge-coupon__code').innerText = couponValue;

                    const url = buildProductUrl(productId);
                    const link = document.querySelector(`.price-table__footer[data-price="${priceSize}"] a`);
                    link.href = url;
                }
            });

            if (document.querySelector('.price-information.has-coupon')) {
                document.querySelectorAll('.price-information:not(.has-coupon)').forEach(element => {
                    element.classList.add('price-information--min-h-112', 'align-items-center');
                })
            }

            updatePoints();
        });

        document.addEventListener('mt:refreshSliderPricingTable', function () {
            destroyGlide();
            handleResize();
        });

        document.addEventListener('mt:destroySliderPricingTable', function () {
            destroyGlide();
        });

        let glideInstance = null;
        let isGlideMounted = false;
        const parentGlideClasses = ['price-table__glide', 'slider', 'glide'];

        function getPriceTable() {
            return document.querySelector('.price-table');
        }

        function addClassToPriceTable() {
            parentGlideClasses.map(className => getPriceTable().classList.add(className));
        }

        function removeClassToPriceTable(extraClasses = ['glide--swipeable']) {
            (parentGlideClasses.concat(extraClasses)).map(className => getPriceTable().classList.remove(className));
        }

        function initGlide() {
            if (glideInstance || isGlideMounted) return;
            addClassToPriceTable();
            try {
                glideInstance = new Glide(getPriceTable(), {
                    type: 'slider', gap: 16, autoplay: false, rewind: false, animationDuration: 200
                });

                window.tableSliderInstance = glideInstance;

                glideInstance.on(['swipe.start', 'run.after'], () => {
                    updatePoints();
                });

                glideInstance
                    .mutate([function (Glide, Components) {
                        return {
                            modify(translate) {
                                const slideWidth = Components.Sizes.slideWidth;
                                const gap = Components.Gaps.value;
                                const viewportWidth = document.documentElement.clientWidth;
                                const offsetToCenter = (viewportWidth - slideWidth) / 2;
                                const slideIndex = Math.round(Math.abs(translate) / (slideWidth + gap));
                                const adjustedTranslate = -(slideIndex * (slideWidth + gap) - offsetToCenter);
                                const containerGap = viewportWidth <= 1024 ? 0 : 32;

                                return -1 * (adjustedTranslate - containerGap);
                            }
                        };
                    }])
                    .mount({
                        Sizes: function CustomSizes(Glide, Components, Events) {
                            const Sizes = {
                                setupSlides() {
                                    const width = this.slideWidth + 'px';
                                    const slides = Components.Html.slides;
                                    for (let i = 0; i < slides.length; i++) {
                                        slides[i].style.width = width;
                                    }
                                }, setupWrapper() {
                                    Components.Html.wrapper.style.width = `${this.wrapperSize}px`;
                                }, remove() {
                                    const slides = Components.Html.slides;
                                    for (let i = 0; i < slides.length; i++) {
                                        slides[i].style.width = '';
                                    }
                                    Components.Html.wrapper.style.width = '';
                                }
                            };

                            Object.defineProperty(Sizes, 'length', {
                                get() {
                                    return Components.Html.slides.length;
                                }
                            });

                            Object.defineProperty(Sizes, 'width', {
                                get() {
                                    return Components.Html.track.offsetWidth;
                                }
                            });

                            Object.defineProperty(Sizes, 'wrapperSize', {
                                get() {
                                    return (this.slideWidth * this.length + Components.Gaps.grow + Components.Clones.grow);
                                }
                            });

                            Object.defineProperty(Sizes, 'slideWidth', {
                                get() {
                                    const viewport = document.documentElement.clientWidth;
                                    let cardWidth = 346;

                                    if (viewport <= 768) {
                                        cardWidth = 320;
                                    }

                                    getPriceTable().style.setProperty('--price-table-slide-width', cardWidth + 'px');

                                    return cardWidth;
                                }
                            });

                            Events.on(['build.before', 'resize', 'update'], () => {
                                Sizes.setupSlides();
                                Sizes.setupWrapper();
                                updatePoints();
                            });

                            Events.on('destroy', () => Sizes.remove());
                            return Sizes;
                        }
                    });

                isGlideMounted = true;
                console.info('[Glide] mounted');
            } catch (err) {
                console.error('Error initializing Glide:', err);
            }
        }

        function destroyGlide() {
            if (glideInstance && isGlideMounted) {
                try {
                    glideInstance.destroy();
                    glideInstance = null;
                    window.tableSliderInstance = null;
                    isGlideMounted = false;
                    removeClassToPriceTable();

                    console.info('[Glide] destroyed');
                } catch (err) {
                    console.error('Error destroying Glide:', err);
                }
            }
        }

        function handleResize() {
            const viewportWidth = window.innerWidth;
            if (viewportWidth > 1024) {
                destroyGlide();
            } else {
                initGlide();
            }
        }

        // Inicializa solo si el viewport es menor o igual a 800
        if (window.innerWidth <= 1024) initGlide();

        // Escucha cambios de tamaño con debounce
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(handleResize, 250);
        });

        handleResize();
        updatePoints();

        function marketTypeAndAccountTypeSelect() {
            const marketType = document.querySelector('[name="market-type"]:checked').value;
            const accountTypeInput = document.querySelector('[name="account-type"]:checked');
            const accountType = accountTypeInput.value;
            const platform = accountTypeInput.dataset.defaultPlatform;

            return [marketType, accountType, platform];
        }

        function toggleArrowsToMovePrices(showArrows = true) {
            const leftBtn = document.querySelector('.mt-prices-left');
            const rightBtn = document.querySelector('.mt-prices-right');

            if (showArrows) {
                leftBtn.classList.add('mt-prices-disabled');
                rightBtn.classList.remove('mt-prices-disabled');

                leftBtn.classList.remove('d-none');
                rightBtn.classList.remove('d-none');

                return;
            }

            leftBtn.classList.add('d-none');
            rightBtn.classList.add('d-none');
        }

        function getPricesInfoByAccountTypeAndMarketType(accountType, marketType) {
            const productSelected = MG_GLOBAL.products.find(product => product.tree_map['account-types'] === accountType && product.tree_map['market-type'] === marketType);
            const productPlatformDetail = productSelected[productSelected.slug]
            const pricesKeys = Object.keys(productPlatformDetail);

            return {productSelected, productPlatformDetail, pricesKeys};
        }

        function movePricingCards(orientation = 'init') {
            const [marketType, accountType] = marketTypeAndAccountTypeSelect();
            const {pricesKeys} = getPricesInfoByAccountTypeAndMarketType(accountType, marketType);

            toggleArrowsToMovePrices(pricesKeys.length > 4);

            try {
                const totalItems = pricesKeys.length;

                if (totalItems <= VISIBLE_COUNT) {
                    console.warn('No hay suficientes elementos para desplazar.');
                    return pricesKeys.slice(0, VISIBLE_COUNT);
                }

                if (orientation === 'right') {
                    currentIndex++;
                    if (currentIndex + VISIBLE_COUNT >= totalItems) {
                        document.querySelector('.mt-prices-right').classList.add('mt-prices-disabled');
                        document.querySelector('.mt-prices-left').classList.remove('mt-prices-disabled');
                    } else {
                        document.querySelector('.mt-prices-right').classList.remove('mt-prices-disabled');
                    }
                }

                if (orientation === 'left') {
                    currentIndex--;
                    if (currentIndex === 0) {
                        document.querySelector('.mt-prices-left').classList.add('mt-prices-disabled');
                        document.querySelector('.mt-prices-right').classList.remove('mt-prices-disabled');
                    } else {
                        document.querySelector('.mt-prices-left').classList.remove('mt-prices-disabled');
                    }
                }

                return pricesKeys.slice(currentIndex, currentIndex + VISIBLE_COUNT);
            } catch (err) {
                console.error('Error en movePricingCards:', err);
                return [];
            }
        }

        function buildPricesCardsHTML(prices = []) {
            const [marketType, accountType] = marketTypeAndAccountTypeSelect();
            const productSelected = MG_GLOBAL.products.find(product => product.tree_map['account-types'] === accountType && product.tree_map['market-type'] === marketType);
            const productPlatformDetail = productSelected[productSelected.slug];

            let products = {};

            prices.forEach(price => {
                products[price] = productPlatformDetail[price];
            });

            let html = '';

            for (const priceSize in products) {
                html += createPriceTablePlan({
                    size: priceSize,
                    isMostPopular: false,
                    hasCoupon: false,
                    price: '',
                    frequencyText: '',
                    metaInfo: [],
                });
            }

            return html;
        }
    });
</script>