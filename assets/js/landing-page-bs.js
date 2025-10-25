document.addEventListener('DOMContentLoaded', function () {
    console.info('[landing_page_bootstrap] landing page bootstrap');

    function initializeSwiper() {
        new Swiper('.verified-bs__swiper', {
            direction: 'horizontal',
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            slidesPerView: 1.2,
            spaceBetween: 16,
            centeredSlides: false,
            loop: true,
            pagination: {
                el: '.verified-bs__swiper .swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                576: {slidesPerView: 2},
                768: {slidesPerView: 3},
                1200: {slidesPerView: 4},
            },
            scrollbar: {
                el: '.verified-bs__swiper .swiper-scrollbar',
            },
        });

        var glide = new Glide('#verified-bs-id', {
            perView: 3,
            type: 'carousel',
            gap: 16,
            startAt: 0,
            focusAt: 'center',
        })

        glide.mount()
    }

    initializeSwiper();
});