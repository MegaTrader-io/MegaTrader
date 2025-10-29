document.addEventListener('DOMContentLoaded', function () {
    console.info('[landing_page_bootstrap] landing page bootstrap');

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

    async function loadPricingTable(fn = function () {
    }) {
        /**
         * Initialize tab component
         */
        MT_Tabs.init();

        document.querySelectorAll(`${MT_Tabs.selector} a`).forEach(itemTab => {
            const panelId = itemTab.getAttribute("aria-controls");
            const panel = document.getElementById(panelId);
            if (!panel) {
                return;
            }

            form.addEventListener("product:selected", (e) => {
                // try {
                void fn(e.detail);
                // } catch (err) {
                //     console.error("unable to listen product:selected:", err);
                // }
            });
        })

        async function loadChooseYourAccountSize(fn) {
            const defaultAccountType = document.querySelector('.btn-account-type.account-active').dataset.value;

            let internalOptions = {
                accountType: defaultAccountType,
            };

            /** Handler Account Type **/
            const buttons = [];
            document.querySelectorAll('.btn-account-type').forEach(btn => {
                buttons.push(btn);
                btn.addEventListener('click', function (e) {
                    buttons.forEach(btn => {
                        btn.classList.remove('account-active');
                    })

                    e.currentTarget.classList.add('account-active');
                    internalOptions.accountType = e.currentTarget.dataset.value;
                    internalOptions.defaultPlatform = e.currentTarget.dataset.defaultPlatform;
                    internalOptions.defaultMarketType = e.currentTarget.dataset.defaultMarketType;

                    fn(internalOptions);
                })
            })
        }


        /**
         * **********************************************************
         * **********************************************************
         *
         * Initialize the first selection to set the link GET PLAN
         */

        const activeTab = document.querySelector('.mt-tabs__item[aria-selected=true]')
        if (!activeTab) {
            return;
        }

        const panelId = activeTab.getAttribute("aria-controls");
        const panel = document.getElementById(panelId);

        const activeFormTab = panel.querySelector('form');

        if (!activeFormTab) {
            return;
        }

        const changeEvent = new Event("change", {
            bubbles: true,
            cancelable: true
        });

        activeFormTab.dispatchEvent(changeEvent);

        /**
         * **********************************************************
         * **********************************************************
         */
    }

    const couponCache = {};
    void loadPricingTable(
        async (params) => {
            const metaInfoElement = document.querySelector('.metaInfo');
            metaInfoElement.innerHTML = '';

            const {product: productionSelected, values} = params;

            function formatNumber(value) {
                return '$' + parseInt(value.toString().replace('$', ''));
            }

            function formatNumberToString(value) {
                try {
                    if (value === null || value === undefined || value === '') {
                        throw new Error('Invalid input: value is null, undefined or empty');
                    }

                    const num = Number(value);

                    if (isNaN(num)) {
                        throw new Error(`Invalid input: "${value}" is not a number`);
                    }

                    // Verificar si tiene parte decimal
                    if (Number.isInteger(num)) {
                        return num.toString(); // sin .00
                    }

                    return num.toFixed(2); // con dos decimales
                } catch (err) {
                    console.error('formatNumberToString error:', err.message);
                    return '';
                }
            }

            function currencyFormat(value) {
                return '$' + formatNumberToString(value);
            }

            if (!productionSelected) {
                console.info('values', values);
                return;
            }

            const defaultMetaInfo = {}
            const metaInfo = productionSelected['meta-info'] || [];
            for (const metaInfoKey in metaInfo) {
                if (metaInfo[metaInfoKey]) {
                    defaultMetaInfo[metaInfoKey] = true;
                }
            }

            const validMetaInfo = Object.keys(defaultMetaInfo);
            let metaInfoList = [];
            Object.keys(MG_GLOBAL.productMetaLabel).forEach(key => {
                if (validMetaInfo.includes(key)) {
                    metaInfoList.push({key, label: MG_GLOBAL.productMetaLabel[key]})
                }
            })

            const template = document.querySelector(`.template-metaInfo`);


            metaInfoList.forEach(metaInfo => {
                const row = template.cloneNode(true);
                row.classList.remove('template-metaInfo', 'd-none');
                const labelHTML = row.querySelector('.mega-info-row__label');
                labelHTML.dataset.key = metaInfo.key;
                labelHTML.innerText = metaInfo.label;

                row.querySelector('.mega-info-row__value').innerText = productionSelected['meta-info'][metaInfo.key];
                metaInfoElement.appendChild(row)
            });

            const priceCard = document.querySelector(`.mt-pricing-card`);
            const couponBeforePrice = document.querySelector(`.coupon-before-price`);

            let price = Number(productionSelected['price-monthly'].replace('$', ''));
            const totalPlan = document.querySelector(`.total-plan`);
            const frequencyPanel = document.querySelector(`.frequency-plan`);
            const planTypeInputRadio = document.querySelector(`[name="account-type"][value="${values['account-type']}"]`);

            if (frequencyPanel) {
                frequencyPanel.innerText = `${values['account-type'] !== 'funded-plan' ? 'per month' : 'one time fee'}`;
            }

            if (planTypeInputRadio) {
                const img = planTypeInputRadio.nextElementSibling.querySelector('.mt-card__title__icon');
                if (img) {
                    document.querySelector('.plan-summary__plan-icon').src = img.src;
                }
            }

            const platformInputRadio = document.querySelector(`[name="platform"][value="${values['platform']}"]`);
            if (platformInputRadio) {
                const img = platformInputRadio.nextElementSibling.querySelector('.mt-card__title__image');
                if (img) {
                    document.querySelector('.plan-summary__platform-icon').src = img.src;
                }
            }

            document.querySelector('.plan-summary__name').innerText = values['account-size'].toUpperCase() + ' ' + values['account-type'].replace('-', ' ');

            const couponURL = `/wp-json/custom/v1/best-coupon?id=${productionSelected.id}`;
            if (!couponCache[couponURL]) {
                const responseCoupons = await fetch(couponURL);
                couponCache[couponURL] = await responseCoupons.json();
            }

            const {data: coupon} = couponCache[couponURL]

            console.info('dataCoupons', coupon);

            if (coupon && coupon.valid) {
                priceCard.querySelector('.mt-pricing-card__header').style.display = 'flex';
                if (couponBeforePrice) {
                    couponBeforePrice.style.display = 'block';
                    couponBeforePrice.querySelector('span').innerText = formatNumber(price);
                }

                totalPlan.innerText = currencyFormat(price - coupon.discount_total);

                priceCard.querySelector('.mt-pricing-card--discount_total span').innerText = formatNumber(coupon.discount_total);

                const couponText = priceCard.querySelector('.mt-pricing-card__code');
                const btnCopyCoupon = priceCard.querySelector('.mt-pricing-card__copy-btn');

                if (couponText && coupon.coupon) {
                    couponText.innerText = coupon.coupon.toUpperCase();
                }

                if (btnCopyCoupon && coupon.coupon) {
                    btnCopyCoupon.dataset.copyText = coupon.coupon.toUpperCase();
                }
            } else {
                priceCard.querySelector('.mt-pricing-card__header').style.display = 'none';
                if (couponBeforePrice) {
                    couponBeforePrice.style.display = 'none';
                }

                totalPlan.innerText = currencyFormat(price);
            }
        }
    );

    initializeSwiper();
    loadCopyElements();

    void loadChooseYourAccountSize(
        (params) => {
            const {defaultPlatform, defaultMarketType} = params;
            const productionSelected = MG_GLOBAL.products.find(product => product.slug === params.accountType);
            const productPlatformDetail = productionSelected[params.accountType];

            function formatNumber(value) {
                return '$' + parseInt(value.toString().replace('$', ''));
            }

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

            for (const priceSize in productPlatformDetail) {
                const attributes = productPlatformDetail[priceSize][params.accountType][defaultPlatform][defaultMarketType];
                const priceObject = attributes.find(item => item['price-monthly'])['price-monthly'] || '$0.00';
                const productId = attributes.find(item => item['id'])['id'];
                const product = MG_GLOBAL?.productsWithBestCoupons?.find(p => p.id === Number(productId));
                const coupon = product?.coupon;
                const is_most_popular = !!Object.values(MG_GLOBAL.bestProducts).find(item => item && item.variation_id === Number(productId))

                if (is_most_popular) {
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
                    const badgeCoupon = document.querySelector(`.badge-coupon[data-price="${priceSize}"]`);
                    const couponBeforePrice = document.querySelector(`.coupon-before-price[data-price="${priceSize}"]`);

                    if (coupon && coupon.valid) {
                        badgeCoupon.style.display = 'block';
                        couponBeforePrice.style.display = 'block';
                        priceInformation.classList.add('has-coupon');

                        couponBeforePrice.querySelector('span').innerText = price;
                        pricePanel.innerText = formatNumber(coupon.final_total);
                        badgeCoupon.querySelector('.badge-coupon__discount_total').innerText = formatNumber(coupon.discount_total);
                        badgeCoupon.querySelector('.badge-coupon__code').innerText = coupon.coupon;
                    } else {
                        badgeCoupon.style.display = 'none';
                        couponBeforePrice.style.display = 'none';
                        priceInformation.classList.remove('has-coupon', 'tw-min-h-[140px]', 'tw-items-center');

                        pricePanel.innerText = price;
                    }
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
                        row.classList.remove('template-metaInfo', 'tw-hidden');
                        const labelHTML = row.querySelector('.mega-info-row__label');
                        labelHTML.dataset.key = metaInfo.key;
                        labelHTML.innerText = metaInfo.label;
                        row.querySelector('.mega-info-row__value').innerText = metaInfoContext[metaInfo.key];
                        metaInfoElement.appendChild(row)
                    })
                }
            }

            if (document.querySelector('.price-information.has-coupon')) {
                document.querySelectorAll('.price-information:not(.has-coupon)').forEach(element => {
                    element.classList.add('tw-min-h-[140px]', 'tw-items-center');
                })
            }

        }
    );
});