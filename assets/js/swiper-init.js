document.addEventListener('DOMContentLoaded', function () {
  new Swiper('.my-faq-slider', {
    slidesPerView: 1,
    spaceBetween: 24,
    loop: true,
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    // Añade más opciones si quieres
  });
});
