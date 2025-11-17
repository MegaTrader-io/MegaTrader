document.addEventListener('DOMContentLoaded', function () {
    function showLoading(show) {
        const loadingOverlay = document.querySelector('.loading-overlay');

        if (show) {
            loadingOverlay.classList.add('tw-flex');
            loadingOverlay.classList.remove('tw-hidden');

            return;
        }

        loadingOverlay.classList.remove('tw-flex');
        loadingOverlay.classList.add('tw-hidden');
    }

    const ArrowUpWithCircle = () => {
        return `
<svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                               xmlns="http://www.w3.org/2000/svg">
    <mask id="mask0_3531_727" maskUnits="userSpaceOnUse" x="0" y="0" width="24"
          height="24">
        <rect width="24" height="24" fill="#D9D9D9"/>
    </mask>
    <g mask="url(#mask0_3531_727)">
        <path
            d="M11 16H13V11.8L14.6 13.4L16 12L12 8L8 12L9.4 13.4L11 11.8V16ZM12 22C10.6167 22 9.31667 21.7375 8.1 21.2125C6.88333 20.6875 5.825 19.975 4.925 19.075C4.025 18.175 3.3125 17.1167 2.7875 15.9C2.2625 14.6833 2 13.3833 2 12C2 10.6167 2.2625 9.31667 2.7875 8.1C3.3125 6.88333 4.025 5.825 4.925 4.925C5.825 4.025 6.88333 3.3125 8.1 2.7875C9.31667 2.2625 10.6167 2 12 2C13.3833 2 14.6833 2.2625 15.9 2.7875C17.1167 3.3125 18.175 4.025 19.075 4.925C19.975 5.825 20.6875 6.88333 21.2125 8.1C21.7375 9.31667 22 10.6167 22 12C22 13.3833 21.7375 14.6833 21.2125 15.9C20.6875 17.1167 19.975 18.175 19.075 19.075C18.175 19.975 17.1167 20.6875 15.9 21.2125C14.6833 21.7375 13.3833 22 12 22Z"
            fill="#14B8A6"/>
    </g>
</svg>
        `;
    }

    const ArrowUpWithoutCircle = (className = '') => {
        return `
<svg class=${className} width="24" height="24" viewBox="0 0 24 24" fill="none"
     xmlns="http://www.w3.org/2000/svg">
    <mask id="mask0_2604_1333" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0" width="24"
          height="24">
        <rect width="24" height="24" fill="#D9D9D9"/>
    </mask>
    <g mask="url(#mask0_2604_1333)">
        <path d="M11 18V8.8L7.4 12.4L6 11L12 5L18 11L16.6 12.4L13 8.8V18H11Z" fill="currentColor"/>
    </g>
</svg>
        `;
    }

    function ArrowUp(circle = true, className = '') {
        return circle ? ArrowUpWithCircle() : ArrowUpWithoutCircle(className)
    }

    function ArrowDown(circle = true, className = '') {
        if (circle) {
            return `
<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    <mask id="mask0_3531_831" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0" width="24"
          height="24">
        <rect x="24" y="24" width="24" height="24" transform="rotate(180 24 24)" fill="#D9D9D9"/>
    </mask>
    <g mask="url(#mask0_3531_831)">
        <path
            d="M13 8L11 8L11 12.2L9.4 10.6L8 12L12 16L16 12L14.6 10.6L13 12.2L13 8ZM12 2C13.3833 2 14.6833 2.2625 15.9 2.7875C17.1167 3.3125 18.175 4.025 19.075 4.925C19.975 5.825 20.6875 6.88333 21.2125 8.1C21.7375 9.31667 22 10.6167 22 12C22 13.3833 21.7375 14.6833 21.2125 15.9C20.6875 17.1167 19.975 18.175 19.075 19.075C18.175 19.975 17.1167 20.6875 15.9 21.2125C14.6833 21.7375 13.3833 22 12 22C10.6167 22 9.31667 21.7375 8.1 21.2125C6.88333 20.6875 5.825 19.975 4.925 19.075C4.025 18.175 3.3125 17.1167 2.7875 15.9C2.2625 14.6833 2 13.3833 2 12C2 10.6167 2.2625 9.31667 2.7875 8.1C3.3125 6.88333 4.025 5.825 4.925 4.925C5.825 4.025 6.88333 3.3125 8.1 2.7875C9.31666 2.2625 10.6167 2 12 2Z"
            fill="#F43F5E"/>
    </g>
</svg>            
            `;
        }

        return `
<svg class=${className} width="24" height="24" viewBox="0 0 24 24" fill="none"
            xmlns="http://www.w3.org/2000/svg">
    <mask id="mask0_2604_1323" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0" width="24"
          height="24">
        <rect width="24" height="24" fill="currentColor"/>
    </mask>
    <g mask="url(#mask0_2604_1323)">
        <path d="M12 18L6 12L7.4 10.6L11 14.2V5H13V14.2L16.6 10.6L18 12L12 18Z" fill="currentColor"/>
    </g>
</svg>        
        `;
    }

    async function loadMarkerCarousel() {
        async function getMarketData() {
            try {
                const response = await fetch(window.MG_GLOBAL.baseApi + '/markets', {
                    headers: {'Content-Type': 'application/json'},
                });

                if (!response.ok) throw new Error(`Status ${response.status}`);
                return await response.json();
            } catch (error) {
                console.error('Error al cargar mercados:', error.message);
                return [];
            }
        }

        const marketList = await getMarketData();
        const marketWrapper = document.querySelector('.market-wrapper');
        if (!marketWrapper) return;

        const carousel = document.createElement('div');
        carousel.classList.add('carousel', 'tw-flex', 'tw-gap-4', 'animate-carousel');

        const changeValue = (value) => {
            const symbol = value > 0 ? "+" : "-";
            return `${symbol} $ ${Math.abs(value).toFixed(2)}`;
        };

        marketList.forEach((instrument) => {
            const card = document.createElement('div');
            card.classList.add('tw-p-3', 'tw-rounded-lg', 'tw-inline-table', 'tw-bg-mgt-dark');

            const grid = document.createElement('div');
            grid.classList.add('tw-grid', 'tw-grid-cols-[1fr_auto]', 'tw-gap-4');

            // Instrument name and price
            const left = document.createElement('div');

            const h3 = document.createElement('h3');
            h3.classList.add('tw-text-white', 'mb-0', 'tw-text-base', 'tw-font-bold', 'tw-leading-normal', 'tw-text-nowrap');
            h3.textContent = instrument.name;

            const price = document.createElement('p');
            price.classList.add('tw-text-stone-400', 'mb-0', 'tw-text-base', 'tw-font-medium', 'tw-leading-normal');
            price.textContent = instrument.price.toLocaleString();

            left.appendChild(h3);
            left.appendChild(price);

            // Change value and arrow
            const right = document.createElement('div');
            right.classList.add('tw-flex', 'tw-justify-center', 'tw-items-center', 'tw-text-nowrap');

            const change = document.createElement('p');
            change.classList.add('tw-flex', 'mb-0', 'tw-gap-2', 'tw-text-base', 'tw-font-bold', 'tw-leading-normal');
            change.classList.add(instrument.change > 0 ? 'tw-text-teal-400' : 'tw-text-rose-500');
            change.textContent = changeValue(instrument.change);

            const svg = instrument.change > 0 ? ArrowUp(true, 'tw-w-5 tw-h-5') : ArrowDown(true, 'tw-w-5 tw-h-5');
            change.insertAdjacentHTML('beforeend', svg);

            right.appendChild(change);

            grid.appendChild(left);
            grid.appendChild(right);
            card.appendChild(grid);
            carousel.appendChild(card);
        });

        marketWrapper.innerHTML = '';
        marketWrapper.appendChild(carousel);

        if (carousel.children.length === 0) {
            document.getElementById('market-data').remove();
            return
        }

        const itemWidth = carousel.children[0].clientWidth;
        const totalWidth = itemWidth * marketList.length;

        carousel.style.setProperty('--item-width', `${itemWidth}px`);
        carousel.style.setProperty('--total-width', `${totalWidth}px`);

        const clonedItemsBefore = Array.from(carousel.children).map(child => child.cloneNode(true));
        const clonedItemsAfter = Array.from(carousel.children).map(child => child.cloneNode(true));
        clonedItemsBefore.forEach(item => carousel.insertBefore(item, carousel.firstChild));
        clonedItemsAfter.forEach(item => carousel.appendChild(item));

        carousel.scrollLeft = totalWidth;

        const handleScroll = () => {
            if (carousel.scrollLeft <= totalWidth) {
                carousel.scrollLeft = 2 * totalWidth;
            } else if (carousel.scrollLeft >= 3 * totalWidth) {
                carousel.scrollLeft = 2 * totalWidth;
            }
        };

        carousel.addEventListener('scroll', handleScroll);
    }

    async function loadChooseYourAccountSize(fn = function () {
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

    function loadYourPathToProfitableTabs() {
        const buttons = [];
        document.querySelectorAll('.feature-tab').forEach(btn => {
            buttons.push(btn);
            btn.addEventListener('click', function (e) {
                buttons.forEach(btn => {
                    btn.classList.remove('btn-primary-filled');
                    btn.classList.add('btn-primary-text');
                    document.querySelector(`[data-panel=${btn.dataset.option}]`).style.display = 'none';
                })

                e.currentTarget.classList.remove('btn-primary-text');
                e.currentTarget.classList.add('btn-primary-filled');

                document.querySelector(`[data-panel=${e.currentTarget.dataset.option}]`).style.display = 'block';
            })
        })
    }


    function loadFaqs() {
        const faqsByCategoryPanel = document.querySelector('.faqs-by-category');

        document.querySelectorAll('#faq [name="category_option"]').forEach(btn => {
            btn.addEventListener('click', function (ev) {
                faqsByCategoryPanel.querySelectorAll('[data-category]').forEach(element => {
                    element.querySelectorAll('[type=radio]').forEach((checkboxElement, index) => {
                        checkboxElement.checked = index === 0;
                    })
                    element.classList.add('tw-hidden');
                })

                const categorySelected = ev.currentTarget.value;
                faqsByCategoryPanel.querySelector(`[data-category="${categorySelected}"]`).classList.remove('tw-hidden');
            })
        })
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

    function loadPlatformSelection() {
        const featureDiscoverPlatform = document.getElementById('feature-discover-the-platforms');
        const selectPlatform = featureDiscoverPlatform.querySelector('[name="changePlan"]');

        function displayDescription(platformSelected) {
            featureDiscoverPlatform.querySelectorAll('[data-platform-description]')
                .forEach(element => {
                    if (element.dataset.platformDescription === platformSelected) {
                        element.classList.remove('tw-hidden');
                        return;
                    }

                    element.classList.add('tw-hidden');
                })
        }

        featureDiscoverPlatform
            .querySelectorAll('[name="platform_option"]')
            .forEach(element => {
                element.addEventListener('change', function (ev) {
                    console.info('radio', ev.currentTarget);
                    const platform = ev.currentTarget.value;
                    selectPlatform.value = platform;
                    displayDescription(platform);
                })
            })

        selectPlatform.addEventListener('change', function (ev) {
            const platform = ev.currentTarget.value;
            document.querySelector(`[name="platform_option"][value=${platform}]`).checked = true;
            displayDescription(platform);
        })
    }

    function loadSubscriptionForm() {
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
                input.classList.add('mgt-input-error');
                input.classList.remove('mgt-input-ok');
                emailError.classList.remove('tw-hidden')
            } else {
                input.classList.remove('mgt-input-error');
                input.classList.add('mgt-input-ok');
                submitBtn.disabled = !emailConsent.checked;
                emailError.classList.add('tw-hidden');
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

            const alertSuccess = document.querySelector('.alert-success');
            alertSuccess.classList.add('tw-hidden');

            try {
                showLoading(true);
                lockForm(true);

                const response = await fetch(MG_GLOBAL.adminAjaxApi, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
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

                alertSuccess.classList.remove('tw-hidden');

                input.value = '';
                emailConsent.checked = false;

                submitBtn.disabled = true;

                setTimeout(() => {
                    input.focus();
                }, 0);
            } catch (e) {
            } finally {
                showLoading(false);
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
    }

    function loadFooterModals() {
        document.querySelectorAll('.btn-dialog').forEach(btn => {
            btn.addEventListener('click', (ev) => {
                ev.preventDefault();
                const dialogId = ev.currentTarget?.dataset?.dialogId || '';
                console.info('dialogId', dialogId);
                showModal(dialogId);
            });
        })
    }

    void loadMarkerCarousel();

    const couponCache = {};
    void loadChooseYourAccountSize(
        async (params) => {
            const metaInfoElement = document.querySelector('.metaInfo');
            const planDetailSelection = document.querySelector('.plan-detail-selection');
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


            const features = planDetailSelection.querySelector('.plan-detail-selection__features-list');
            features.innerHTML = '';
            metaInfoList.forEach(metaInfo => {
                const row = template.cloneNode(true);
                row.classList.remove('template-metaInfo', 'd-none');
                const labelHTML = row.querySelector('.mega-info-row__label');
                labelHTML.dataset.key = metaInfo.key;
                labelHTML.innerText = metaInfo.label;
                const value = productionSelected['meta-info'][metaInfo.key];

                row.querySelector('.mega-info-row__value').innerText = value;
                metaInfoElement.appendChild(row)

                const li = document.createElement('li');
                li.classList.add('plan-detail-selection__features-item');

                const img = document.createElement('img');
                img.src = `/wp-content/themes/megatrader-addons/assets/img/landing-page/check.svg`;

                li.appendChild(img);

                const div = document.createElement('div');
                div.classList.add('mega-info-row__label');
                div.innerText = metaInfo.label + ': ' + value;

                li.appendChild(div);

                features.appendChild(li);
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

            const planTitle = values['account-size'].toUpperCase() + ' ' + values['account-type'].replace('-', ' ');
            document.querySelector('.plan-summary__name').innerText = planTitle;
            planDetailSelection.querySelector('.plan-detail-selection__name').innerText = planTitle.toUpperCase();

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

    loadYourPathToProfitableTabs();
    loadFaqs();
    loadCopyElements();
    loadPlatformSelection();
    loadSubscriptionForm();
    loadFooterModals();

    (new Glide('#certifications-slider', {
        type: 'carousel',
        perView: 1,
        gap: 16,
        autoplay: 3000,
    })).mount();

    (new Glide('#megatrader-in-numbers', {
        type: 'carousel',
        perView: 1,
        gap: 16,
        autoplay: 3000,
    })).mount();

    function pricingTable() {
        const futuresForm = document.getElementById("futures-form");
        const glideRoot = document.querySelector("#account-type-glide");
        if (!glideRoot) return;

        const slidesContainer = glideRoot.querySelector(".product-section__list");
        if (!slidesContainer) return;

        let glideInstance = null;
        let isMobile = false;
        const originalSlides = Array.from(slidesContainer.children);

        const initGlide = () => {
            if (glideInstance) return;
            try {
                // Revertir orden
                const reversed = [...slidesContainer.children].reverse();
                slidesContainer.innerHTML = "";
                reversed.forEach(slide => slidesContainer.appendChild(slide));

                glideInstance = new Glide("#account-type-glide", {
                    type: "slider",
                    perView: 1,
                    gap: 16,
                    peek: {before: 0, after: 60},
                    autoplay: false,
                    hoverpause: true,
                    animationDuration: 600,
                    rewind: false,
                    bound: true,
                });

                glideInstance.mount();

                // 👇 Click para centrar el slide
                const topLevelSlides = slidesContainer.querySelectorAll(":scope > li.glide__slide");
                topLevelSlides.forEach((slide, index) => {
                    slide.addEventListener("click", (e) => {
                        if (!glideInstance) return;
                        const currentIndex = glideInstance.index;
                        if (index !== currentIndex) {
                            e.preventDefault();
                            e.stopPropagation();
                            glideInstance.go(`=${index}`);

                            document.querySelectorAll('[name="account-type"]')
                                .forEach((accountType, indexAccountType) => {
                                    if (indexAccountType === index) {
                                        accountType.checked = true;
                                        if (futuresForm) futuresForm.dispatchEvent(new Event("change"));
                                    }
                                })

                            console.log(`🎯 Slide ${index} centrado por click`);
                        } else {
                            console.log(`✅ Slide ${index} ya está centrado`);
                        }
                    });
                });

                console.log("✅ Glide inicializado (modo móvil)");
            } catch (err) {
                console.error("❌ Error al inicializar Glide:", err);
            }
        };

        const destroyGlide = () => {
            if (!glideInstance) return;
            try {
                glideInstance.destroy();
                glideInstance = null;

                slidesContainer.innerHTML = "";
                originalSlides.forEach(slide => slidesContainer.appendChild(slide));
                console.log("🧹 Glide destruido (modo escritorio)");
            } catch (err) {
                console.error("❌ Error al destruir Glide:", err);
            }
        };

        let pointsNavRoot;
        const buildPointsControlsNav = () => {
            const mtAccountTypePoints = document.createElement("div");
            mtAccountTypePoints.classList.add("mt-account-type-points");
            mtAccountTypePoints.dataset.glideEl = "controls[nav]";

            for (let i = 0; i < 3; i++) {
                const button = document.createElement("button");
                button.classList.add("mt-account-type-points__pointer", "glide__bullet", "slider__bullet");
                button.type = "button";
                button.dataset.glideDir = `=${i}`;
                mtAccountTypePoints.appendChild(button);
            }

            slidesContainer.parentNode.insertBefore(mtAccountTypePoints, slidesContainer.nextSibling);
            return mtAccountTypePoints;
        };

        const applyMode = (mobile) => {
            const btnFundedPlan = document.querySelector('[name="account-type"][value="funded-plan"]');

            if (mobile && !isMobile) {
                glideRoot.classList.add("glide--slider");

                const track = glideRoot.querySelector('[data-glide-el="track"]');
                if (track) track.classList.add("glide__track");

                slidesContainer.classList.add("glide__slides");

                // ✅ solo los <li> de primer nivel
                slidesContainer.querySelectorAll(":scope > li").forEach(li => li.classList.add("glide__slide"));

                isMobile = true;

                pointsNavRoot = buildPointsControlsNav();

                const doesExistsPreviousPriceTableSelection = !!localStorage.getItem('content-futures-storage');

                setTimeout(() => {
                    if (doesExistsPreviousPriceTableSelection) {
                        const radios = Array.from(document.querySelectorAll('[name="account-type"]'));
                        const checked = document.querySelector('[name="account-type"]:checked');

                        if (checked) {
                            const index = radios.indexOf(checked);
                            console.log("🔢 Índice del radio seleccionado:", index);
                            glideInstance.go(`=${index}`);
                        } else {
                            console.log("⚠️ Ningún radio seleccionado.");
                        }

                        return;
                    }

                    if (btnFundedPlan) {
                        btnFundedPlan.checked = true;
                        if (futuresForm) futuresForm.dispatchEvent(new Event("change"));
                    }
                }, 0);

                initGlide();
            } else if (!mobile && isMobile) {
                glideRoot.classList.remove("glide--slider");

                if (pointsNavRoot) {
                    pointsNavRoot.remove();
                    pointsNavRoot = null;
                }

                const track = glideRoot.querySelector('[data-glide-el="track"]');
                if (track) track.classList.remove("glide__track");

                slidesContainer.classList.remove("glide__slides");

                // ✅ solo remover del primer nivel
                slidesContainer.querySelectorAll(":scope > li").forEach(li => li.classList.remove("glide__slide", "glide__slide--active"));

                isMobile = false;
                destroyGlide();
            }
        };

        const media = window.matchMedia("(max-width: 767px)");
        const checkMode = () => applyMode(media.matches);

        media.addEventListener("change", checkMode);
        window.addEventListener("orientationchange", () => setTimeout(checkMode, 250));

        checkMode();
    }

    pricingTable();
});

window.addEventListener("pageshow", function (event) {
    if (event.persisted) {
        console.info('reload landing page', new Date());
        window.location.reload();
    }
});

