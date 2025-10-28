document.addEventListener('DOMContentLoaded', function () {
    console.info('[landing_page_bootstrap] landing page bootstrap');

    function initializeSwiper() {
        


        (new Glide('#verified-bs-id', {
            type: 'carousel',
            focusAt: 'center',
            perView: 2.280599,
            gap: 16,
            peek: {before: 100, after: 100},
            autoplay: 3000,
        })).mount()
    }

    initializeSwiper();
});