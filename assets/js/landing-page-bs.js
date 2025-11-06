document.addEventListener('DOMContentLoaded', function () {
    console.info('[landing_page_bootstrap] landing page bootstrap');

    function formatNumber(value) {
        return '$' + parseInt(value.toString().replace('$', ''));
    }

    async function fetchCouponInBatch(productIds) {
        const couponURL = `/wp-json/custom/v1/best-coupon-in-batch?ids=${productIds}`;
        if (!couponsCache[couponURL]) {
            const responseCoupons = await fetch(couponURL);
            couponsCache[couponURL] = await responseCoupons.json();
        }

        return couponsCache[couponURL];
    }

    function buildProductUrl(productId) {
        const CHECKOUT_URL = MG_GLOBAL.CHECKOUT_URL;
        const GO_TO_URL = MG_GLOBAL.GO_TO_URL;
        const isUserLoggedIn = Number(MG_GLOBAL.isUserLoggedIn);
        const checkoutUrl = CHECKOUT_URL.replace('PRODUCT_ID', productId);

        if (!isUserLoggedIn) {
            return GO_TO_URL + encodeURIComponent(checkoutUrl);
        }

        return checkoutUrl;
    }

    function loadCopyElements() {
        new ClipboardJS('.btn-copy');
        document.querySelectorAll('.btn-copy').forEach(element => {
            element.addEventListener('click', async function (ev) {
                const element = ev.currentTarget;
                const value = element.dataset?.copyText || '';

                try {
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        await navigator.clipboard.writeText(value);
                    } else {
                        const tempBtn = document.createElement("button");
                        document.body.appendChild(tempBtn);

                        const clipboard = new ClipboardJS(tempBtn, {
                            text: () => value,
                        });

                        tempBtn.click();
                        clipboard.destroy();
                        tempBtn.remove();
                    }

                    const copyTooltip = element.querySelector('.mgt-copy-tooltip');
                    if (copyTooltip) {
                        copyTooltip.classList.remove('d-none');
                        setTimeout(() => {
                            copyTooltip.classList.add('d-none');
                        }, 800);
                    }
                } catch (err) {
                    console.error("Error copying to clipboard:", err);
                }
            });
        })
    }

    function initializeSwiper() {

        try {
            const carouselSelector = '#verified-bs-carousel';
            const carouselEl = document.querySelector(carouselSelector);
            if (!carouselEl) throw new Error('Carousel element not found.');

            function calculatePerPage() {
                const width = carouselEl.clientWidth;
                const slideWidth = 378;
                return Math.max(1, Math.floor(width / slideWidth));
            }

            let splide = new Splide(carouselSelector, {
                type: 'loop',
                drag: 'free',
                pagination: false,
                arrows: false,
                focus: 'center',
                gap: '16px',
                perPage: calculatePerPage(),
                autoScroll: {
                    speed: 0.2,
                    pauseOnHover: true,
                    pauseOnFocus: true,
                },
            });

            splide.mount(window.splide.Extensions);

            window.addEventListener('resize', () => {
                const newPerPage = calculatePerPage();
                if (splide.options.perPage !== newPerPage) {
                    splide.options = {...splide.options, perPage: newPerPage};
                    splide.refresh();
                }
            });
        } catch (error) {
            console.error('Error initializing Splide carousel:', error);
        }


        (new Glide('#verified-bs-id', {
            type: 'carousel',
            focusAt: 'center',
            perView: 2.280599,
            gap: 16,
            peek: {before: 100, after: 100},
            autoplay: 3000,
        })).mount()
    }

    function loadChooseYourAccountSize(fn) {
        function handlerSelection(target) {
            const input = target.currentTarget || target;
            const accountType = input.value;

            void fn({
                accountType,
                defaultPlatform: input.dataset.defaultPlatform,
                defaultMarketType: input.dataset.defaultMarketType,
            });
        }

        document.querySelectorAll(`[name="account-type"]`).forEach(btn => {
            btn.addEventListener('click', handlerSelection);
        })

        const targetSelection = document.querySelector('[name="account-type"]:checked');
        handlerSelection(targetSelection)
    }

    initializeSwiper();
    loadCopyElements();

    window.couponsCache = {};
    loadChooseYourAccountSize(async (params) => {
        const {defaultPlatform, defaultMarketType} = params;
        const productSelected = MG_GLOBAL.products.find(product => product.slug === params.accountType);
        const productPlatformDetail = productSelected[params.accountType];

        const defaultMetaInfo = {}
        for (const priceSize in productPlatformDetail) {
            const attributes = productPlatformDetail[priceSize][params.accountType][defaultPlatform][defaultMarketType];
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
        })

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
        for (const priceSize in productPlatformDetail) {
            const attributes = productPlatformDetail[priceSize][params.accountType][defaultPlatform][defaultMarketType];
            const priceObject = attributes.find(item => item['price-monthly'])['price-monthly'] || '$0.00';
            const productId = attributes.find(item => item['id'])['id'];

            products.push({productId, priceSize, priceObject});

            const isMostPopular = !!Object.values(MG_GLOBAL.bestProducts).find(item => item && item.variation_id === Number(productId))

            if (isMostPopular) {
                const mostPopularElement = document.querySelector(`.price-table__plan[data-price="${priceSize}"]`)
                if (mostPopularElement) {
                    mostPopularElement.classList.add('price-table__plan--most-popular');
                    mostPopularElement.classList.remove('price-table__plan--regular-plan');

                    const btnGetPlan = mostPopularElement.querySelector('.mega-btn-md');
                    if (btnGetPlan) {
                        btnGetPlan.classList.add('mega-btn-primary-md');
                        btnGetPlan.classList.remove('mega-btn-default-md');
                    }
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
                frequencyPanel.innerText = ` ${params.accountType !== 'funded-plan' ? 'per month' : 'one time fee'}`;
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
            console.info('url', url);
            const link = document.querySelector(`.price-table__footer[data-price="${priceSize}"] a`);
            link.href = url;
        }

        const productIds = products.map(product => Number(product.productId)).join(',')

        const {data: coupons} = await fetchCouponInBatch(productIds);

        console.info('dataCoupons', coupons);

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

                if (isValidCoupon) {
                    priceInformation.classList.add('has-coupon');

                    couponBeforePrice.querySelector('span').innerText = price;
                    pricePanel.innerText = formatNumber(coupon.final_total);
                    badgeCoupon.querySelector('.badge-coupon__discount_total').innerText = formatNumber(coupon.discount_total);
                    badgeCoupon.querySelector('.badge-coupon__code').innerText = coupon.coupon.toUpperCase();
                } else {
                    priceInformation.classList.remove('has-coupon', 'price-information--min-h-112', 'align-items-center');
                    pricePanel.innerText = price;
                }
            }
        });

        if (document.querySelector('.price-information.has-coupon')) {
            document.querySelectorAll('.price-information:not(.has-coupon)').forEach(element => {
                element.classList.add('price-information--min-h-112', 'align-items-center');
            })
        }
    })
});