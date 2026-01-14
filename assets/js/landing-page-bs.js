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
        let checkoutUrl = CHECKOUT_URL.replace('PRODUCT_ID', productId);

        const couponElement = document.querySelector('.badge-coupon__code');
        const coupon = couponElement?.innerText?.trim();

        if (coupon) {
            checkoutUrl = checkoutUrl + '&coupon=' + coupon;
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

    function updatePoints() {
        let priceTable = document.querySelector('.price-table');
        let bottomPoints = 0;

        priceTable?.style.setProperty('--current-slider-height', bottomPoints + 'px');
    }

    function initializeSwiper() {
        try {
            const heroBsCarouselRoot = document.getElementById('hero-bs-carousel');
            const heroGlideInstance = new Glide(heroBsCarouselRoot, {
                type: 'carousel', focusAt: 'center', gap: 16, perView: 1, autoplay: 3000,
            });

            heroGlideInstance.mount();

            const carouselSelector = '#verified-bs-carousel';
            const carouselEl = document.querySelector(carouselSelector);
            if (!carouselEl) throw new Error('Carousel element not found.');

            function calculatePerPage() {
                const width = carouselEl.clientWidth;
                const slideWidth = document.documentElement.clientWidth <= 767 ? 276 : 378;
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
                    speed: 0.2, pauseOnHover: true, pauseOnFocus: true,
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

        (function () {
            const priceTable = document.querySelector('.price-table');
            let glideInstance = null;
            let isGlideMounted = false;
            const parentGlideClasses = ['price-table__glide', 'slider', 'glide'];

            function addClassToPriceTable() {
                parentGlideClasses.map(className => priceTable.classList.add(className));
            }

            function removeClassToPriceTable(extraClasses = ['glide--swipeable']) {
                (parentGlideClasses.concat(extraClasses)).map(className => priceTable.classList.remove(className));
            }

            function initGlide() {
                if (glideInstance || isGlideMounted) return;
                addClassToPriceTable();
                try {
                    glideInstance = new Glide(priceTable, {
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

                                        priceTable.style.setProperty('--price-table-slide-width', cardWidth + 'px');

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
        })();
    }

    function loadChooseYourAccountSize(fn) {
        document.getElementById('pricing')
            .addEventListener("trigger:select-account-type", (e) => {
                const {accountType, defaultAccountSize} = e.detail
                const accountTypeSelected = document.querySelector('input[name="account-type"][value=' + accountType + ']')

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

            void fn({
                accountType,
                defaultPlatform: input.dataset.defaultPlatform,
                defaultMarketType: input.dataset.defaultMarketType,
            });
        }

        async function handlerSelectByMarketType(target) {
            const input = target.currentTarget || target;
            const marketType = input.value;

            console.info('[MarketType Selected]', marketType);
            try {
                const endpoint = `/wp-json/custom/v1/pricing-fragment?marketType=${encodeURIComponent(marketType)}`;
                const response = await fetch(endpoint, {cache: 'no-store'});

                if (!response.ok) {
                    throw new Error(`Error ${response.status} al obtener el fragmento`);
                }

                const data = await response.json();
                if (!data.success) {
                    console.error('Backend error:', data.message);
                    return;
                }

                const htmlContainer = document.querySelector('.pricing-table-fragment-wrapper');
                if (!htmlContainer) {
                    console.warn('No se encontró el contenedor .pricing-table-fragment-wrapper');
                    return;
                }

                // 🔄 Inyectar nuevo fragmento
                htmlContainer.innerHTML = data.html;

                // 🕒 Esperar un tick para asegurar que el DOM esté actualizado
                await new Promise(resolve => requestAnimationFrame(resolve));

                // ✅ Buscar el primer input[name="account-type"] del nuevo fragmento
                const firstAccountTypeInput = htmlContainer.querySelector('[name="account-type"]');

                if (firstAccountTypeInput) {
                    console.info('[Auto-select AccountType]', firstAccountTypeInput.value);

                    void fn({
                        accountType: firstAccountTypeInput.value,
                        defaultPlatform: firstAccountTypeInput.dataset.defaultPlatform,
                        defaultMarketType: firstAccountTypeInput.dataset.defaultMarketType,
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

        // 📌 Inicializar listeners principales
        document.querySelectorAll('[name="account-type"]').forEach(btn => btn.addEventListener('click', handlerSelectByAccountType));
        document.querySelectorAll('[name="market-type"]').forEach(btn => btn.addEventListener('click', handlerSelectByMarketType));

        const targetSelection = document.querySelector('[name="account-type"]:checked');
        targetSelection && handlerSelectByAccountType(targetSelection);
    }

    initializeSwiper();
    loadCopyElements();

    window.couponsCache = {};
    loadChooseYourAccountSize(async (params) => {
        console.info('params', params);
        const priceTable = document.querySelector('.price-table');
        const height = document.querySelector('.price-table .glide__slide--active .price-table__plan--most-popular') || document.querySelector('.price-table .glide__slide--active .price-table__plan--regular-plan') ? 0 : 24;
        priceTable.style.setProperty('--current-slider-height', height + 'px');

        const dropdownAccountTypeComponent = document.querySelector('.mt-select-ac-type');

        dropdownAccountTypeComponent.querySelector('.selected')?.classList.remove('selected');

        const dropdownAccountTypeOption = dropdownAccountTypeComponent.querySelector('.dropdown-item__wrapper[data-account-type-slug=' + params['accountType'] + ']');
        const productSelected = MG_GLOBAL.products.find(product => product.slug === params.accountType);
        const productPlatformDetail = productSelected[params.accountType];

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

        const defaultMetaInfo = {}
        for (const priceSize in productPlatformDetail) {

            let attributes = null;
            Object.keys(productSelected?.tree_map || []).forEach(e => {
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
            const billingType = productSelected.tree_map['billing-type'];

            let attributes = null;
            Object.keys(productSelected?.tree_map || []).forEach(_ => {
                attributes = !attributes ? productPlatformDetail[priceSize] : Object.values(attributes).at(0);
            });

            const priceObject = attributes.find(item => item['price-monthly'])['price-monthly'] || '$0.00';
            console.info('priceObject', priceObject);
            const productId = attributes.find(item => item['id'])['id'];

            products.push({productId, priceSize, priceObject});

            const isMostPopular = !!Object.values(MG_GLOBAL.bestProducts).find(item => item && item.variation_id === Number(productId))
            const priceCard = document.querySelector(`.price-table__plan[data-price="${priceSize}"]`)

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

    (function () {
        const formSubscription = document.getElementById('form-subscription');
        if (!formSubscription) {
            return;
        }

        const input = formSubscription.querySelector('[name="email"]');
        const emailError = formSubscription.querySelector('#email-error');
        const submitBtn = formSubscription.querySelector('[type="submit"]');
        const emailConsent = formSubscription.querySelector('[name="email_consent"]');

        function setErrorMessage(message) {
            emailError.innerText = message ? message : '';

            submitBtn.disabled = !message;

            if (message) {
                submitBtn.disabled = true;
                input.classList.add('invalid_email');
                emailError.classList.remove('d-none')
            } else {
                input.classList.remove('invalid_email');
                submitBtn.disabled = !emailConsent.checked;
                emailError.classList.add('d-none');
            }
        }

        function lockForm(locked) {
            input.disabled = locked;
            submitBtn.disabled = locked;
            emailConsent.disabled = locked;
        }

        async function handlerSubmitForm(ev) {
            ev.preventDefault();

            if (!validateEmail()) {
                if (!input.value.trim()) {
                    setErrorMessage("This field is required");
                    input.focus();
                }

                return;
            }

            input?.blur();

            const alertSuccess = document.querySelector('.footer-bs__alert');
            alertSuccess.classList.add('d-none');

            try {
                $.preloader.show();
                lockForm(true);

                const response = await fetch(MG_GLOBAL.adminAjaxApi, {
                    method: 'POST', headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    }, body: new URLSearchParams({
                        action: 'subscription_form_submit',
                        nonce: MG_GLOBAL.subscriptionNonce,
                        email: input.value.trim(),
                        email_consent: emailConsent.checked
                    })
                });

                const responseData = await response.json();
                const {data} = responseData;

                if (!responseData.success) {
                    setErrorMessage(data.message);
                    return;
                }

                alertSuccess.classList.remove('d-none');

                input.value = '';
                emailConsent.checked = false;

                submitBtn.disabled = true;

                setTimeout(() => {
                    input.focus();
                }, 0);
            } catch (e) {
            } finally {
                $.preloader.hide();
                lockForm(false)
            }
        }

        function handlerEmailConsent() {
            validateEmail();

            if (emailConsent.checked) {
                submitBtn.disabled = false;
                return;
            }

            submitBtn.disabled = true;
        }

        function validateEmail() {
            const value = input.value.trim();

            if (value === '') {
                return;
            }

            if (!input.validity.valid) {
                setErrorMessage("Invalid email");
                return false;
            }

            setErrorMessage('');
            return true;
        }

        input.addEventListener('blur', validateEmail);
        emailConsent.addEventListener('click', handlerEmailConsent);
        submitBtn.addEventListener('click', handlerSubmitForm);
    })();
});