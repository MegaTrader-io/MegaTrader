document.addEventListener('DOMContentLoaded', function () {
    function showLoading(show) {
        const loadingOverlay = document.querySelector('.loading-overlay');

        if (show) {
            loadingOverlay.classList.add('flex');
            loadingOverlay.classList.remove('hidden');

            return;
        }

        loadingOverlay.classList.remove('flex');
        loadingOverlay.classList.add('hidden');
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
        carousel.classList.add('carousel', 'flex', 'gap-4', 'animate-carousel');

        const changeValue = (value) => {
            const symbol = value > 0 ? "+" : "-";
            return `${symbol} $ ${Math.abs(value).toFixed(2)}`;
        };

        marketList.forEach((instrument) => {
            const card = document.createElement('div');
            card.classList.add('p-3', 'rounded-lg', 'inline-table', 'bg-mgt-dark');

            const grid = document.createElement('div');
            grid.classList.add('grid', 'grid-cols-[1fr_auto]', 'gap-4');

            // Instrument name and price
            const left = document.createElement('div');

            const h3 = document.createElement('h3');
            h3.classList.add('text-white', 'text-base', 'font-bold', 'leading-normal', 'text-nowrap');
            h3.textContent = instrument.name;

            const price = document.createElement('p');
            price.classList.add('text-stone-400', 'text-base', 'font-medium', 'leading-normal');
            price.textContent = instrument.price.toLocaleString();

            left.appendChild(h3);
            left.appendChild(price);

            // Change value and arrow
            const right = document.createElement('div');
            right.classList.add('flex', 'justify-center', 'items-center', 'text-nowrap');

            const change = document.createElement('p');
            change.classList.add('flex', 'gap-2', 'text-base', 'font-bold', 'leading-normal');
            change.classList.add(instrument.change > 0 ? 'text-teal-400' : 'text-rose-500');
            change.textContent = changeValue(instrument.change);

            const svg = instrument.change > 0 ? ArrowUp(true, 'w-5 h-5') : ArrowDown(true, 'w-5 h-5');
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
                fn(internalOptions);
            })
        })
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
                    element.classList.add('hidden');
                })

                const categorySelected = ev.currentTarget.value;
                faqsByCategoryPanel.querySelector(`[data-category="${categorySelected}"]`).classList.remove('hidden');
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

                    element.querySelector('.mgt-copy-tooltip').classList.remove('hidden');
                    setTimeout(() => {
                        element.querySelector('.mgt-copy-tooltip').classList.add('hidden');
                    }, 800);
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
                        element.classList.remove('hidden');
                        return;
                    }

                    element.classList.add('hidden');
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
                emailError.classList.remove('hidden')
            } else {
                input.classList.remove('mgt-input-error');
                input.classList.add('mgt-input-ok');
                submitBtn.disabled = !emailConsent.checked;
                emailError.classList.add('hidden');
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
            alertSuccess.classList.add('hidden');

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

                alertSuccess.classList.remove('hidden');

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
    void loadChooseYourAccountSize(
        (params) => {
            console.info('params', params);
            const defaultPlatform = 'megatraderx';
            const productionSelected = MG_GLOBAL.products.find(product => product.slug === params.accountType);
            console.info(
                'productionSelected', productionSelected
            )
            const productPlatformDetail = productionSelected[params.accountType];

            for (const priceSize in productPlatformDetail) {
                const attributes = productPlatformDetail[priceSize][params.accountType][defaultPlatform];
                const priceObject = attributes.find(item => item['price-monthly'])['price-monthly'] || '$0.00';
                const price = '$' + parseInt(priceObject.replace('$', ''));
                const pricePanel = document.querySelector(`.price-plan[data-price="${priceSize}"]`);
                const frequencyPanel = document.querySelector(`.frequency-plan[data-price="${priceSize}"]`);
                if (pricePanel) {
                    pricePanel.innerText = price;
                }

                if (frequencyPanel) {
                    frequencyPanel.innerText = `/ ${params.accountType !== 'funded-plan' ? 'Month' : 'One-Time Fee'}`;
                }

                const metaInfoObject = attributes.find(item => item['meta-info']);
                if (metaInfoObject) {
                    const metaInfoList = metaInfoObject['meta-info'];
                    const metaInfoElement = document.querySelector(`.metaInfo[data-price="${priceSize}"]`);

                    for (const metainfo in metaInfoList) {
                        const element = metaInfoElement.querySelector(`.${metainfo}`);
                        const value = metaInfoList[metainfo];
                        if (element && value) {
                            element.querySelector('.metaValue').innerHTML = metaInfoList[metainfo] || 'None';
                        }
                    }
                }
            }
        }
    );

    loadYourPathToProfitableTabs();
    loadFaqs();
    loadCopyElements();
    loadPlatformSelection();
    loadSubscriptionForm();
    loadFooterModals();
});

