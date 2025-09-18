document.addEventListener('DOMContentLoaded', function () {
    function headerScroll() {
        const navigationItems = [
            {href: '#hero-section', label: 'HOME', sectionId: 'hero-section', visible: true},
            {href: '#hero-section', label: '', sectionId: 'market-data', visible: false},
            {href: '#hero-section', label: '', sectionId: 'sponsor', visible: false},
            {href: '#hero-section', label: '', sectionId: 'megatrader-numbers', visible: false},
            {href: '#how-it-works', label: 'HOW IT WORKS', sectionId: 'how-it-works', visible: true},
            {href: '#how-it-works', label: 'HOW IT WORKS', sectionId: 'how-it-works-01', visible: false},
            {href: '#how-it-works', label: 'HOW IT WORKS', sectionId: 'how-it-works-02', visible: false},
            {href: '#how-it-works', label: 'HOW IT WORKS', sectionId: 'how-it-works-03', visible: false},
            {href: '#how-it-works', label: 'HOW IT WORKS', sectionId: 'how-it-works-04', visible: false},
            {href: '#how-it-works', label: 'HOW IT WORKS', sectionId: 'how-it-works-05', visible: false},
            {href: '#pricing', label: 'PRICING', sectionId: 'pricing', visible: true},
            {href: '#features', label: 'FEATURES', sectionId: 'features', visible: true},
            {href: '#features', label: '', sectionId: 'feature-smarter-tools', visible: false},
            {href: '#features', label: '', sectionId: 'feature-your-path', visible: false},
            {href: '#features', label: '', sectionId: 'feature-earn-more-throuch', visible: false},
            {href: '#features', label: '', sectionId: 'feature-discover-the-platforms', visible: false},
            {href: '#features', label: '', sectionId: 'feature-our-with-drawal-methods', visible: false},
            {href: '#features', label: '', sectionId: 'feature-trusted-by-leadres', visible: false},
            {href: '#our-team', label: 'OUR TEAM', sectionId: 'our-team', visible: true},
            {href: '#faq', label: 'FAQ', sectionId: 'faq', visible: true},
        ];

        const options = {
            root: null,
            rootMargin: '100px 0px 100px 0px',
            threshold: 0.8,
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const activeSection = entry.target.id;
                    const section = navigationItems.find(nav => nav.sectionId === activeSection);
                    const activeSectionGroup = section.href.replace('#', '');

                    document.querySelectorAll(`.mt-navbar__nav-link`).forEach(btn => {
                        btn.classList.remove('mt-navbar__nav-link--active');
                    })

                    const btnNav = document.querySelector(`[data-menu="${activeSectionGroup}"]`);
                    btnNav.classList.add('mt-navbar__nav-link--active');
                }
            });
        }, options);

        navigationItems.forEach(({sectionId}) => {
            const element = document.getElementById(sectionId);
            if (element) {
                observer.observe(element);
            }
        });

        function getCurrentHeaderHeight() {
            const headerElement = document.querySelector('header > div');
            return headerElement.offsetHeight;
        }

        // handler clicks for landing page
        document.querySelectorAll('.mt-navbar---landing-page .mt-navbar__nav-link')
            .forEach(btn => {
                btn.addEventListener('click', (ev) => {
                    ev.preventDefault();
                    ev.stopPropagation();
                    const element = document.getElementById(ev.currentTarget.dataset.menu);
                    if (!element) {
                        return;
                    }

                    const elementPosition = element.getBoundingClientRect().top + window.scrollY;
                    const offsetPosition = elementPosition - getCurrentHeaderHeight();

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth',
                    });
                })
            })

        function trackElementHeight(element, css_variable) {
            if (element) {
                const updateHeightVar = () => {
                    const fullHeight = element.getBoundingClientRect().height;
                    document.body.style.setProperty(`${css_variable}`, `${fullHeight}px`);
                };

                updateHeightVar();

                const resizeObserver = new ResizeObserver(() => {
                    updateHeightVar();
                });

                resizeObserver.observe(element);
            } else {
                document.body.style.setProperty(`${css_variable}`, `0px`);
            }
        }


        const adminbar = document.getElementById('wpadminbar');
        trackElementHeight(adminbar, '--admin-bar-height');

        const menuNavBar = document.querySelector('.mt-navbar__links');
        trackElementHeight(menuNavBar, '--nav-bar-height');

        if (menuNavBar) {
            const toggleScrolled = () => {
                if (window.scrollY > 14) {
                    menuNavBar.classList.add('mt-navbar__links--scrolled');
                } else {
                    menuNavBar.classList.remove('mt-navbar__links--scrolled');
                }
            };

            toggleScrolled();

            window.addEventListener('scroll', toggleScrolled, { passive: true });
        }
    }

    headerScroll();
})