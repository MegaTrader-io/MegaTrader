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

    function loadChooseYourAccountSize(fn) {
        document.querySelectorAll(`[name="account-type"]`).forEach(btn => {
            btn.addEventListener('click', function (e) {
                const accountType = e.currentTarget.value;

                fn({
                    accountType
                });
            })
        })
    }

    initializeSwiper();
    loadCopyElements();

    loadChooseYourAccountSize((params) => {
        console.info('[landing_page_bootstrap] choose your account size', params);
    })
});