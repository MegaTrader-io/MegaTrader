<?php
$verified_payouts_base_url = get_template_directory_uri() . '/assets/img/landing-page/verified-payouts';
$certifies = [
  ['img' => $verified_payouts_base_url . '/Certificate-passed-1.png', 'title' => 'Mega Certified Trader'],
  ['img' => $verified_payouts_base_url . '/Certificate-passed-2.png', 'title' => 'Mega Certified Trader'],
  ['img' => $verified_payouts_base_url . '/Certificate-widthdrawal-3.png', 'title' => 'Mega Certified Trader'],
  ['img' => $verified_payouts_base_url . '/Certificate-widthdrawal-4.png', 'title' => 'Mega Certified Trader'],
]

?>

<section class="verified-bs text-white">
  <div class="landing-bs-container verified-bs__container">
    <header class="verified-bs__header text-center">
      <h2 class="verified-bs__title">
        Verified <span>ACHIEVEMENTS</span> from Real Traders
      </h2>
      <p class="verified-bs__subtitle">
        Every certificate represents a trader moving forward — from passing evaluations to receiving
        payouts. This is just the beginning of what’s possible.
      </p>
    </header>
  </div>

  <div id="verified-bs-carousel" class="splide">
    <div class="splide__track">
      <ul class="splide__list">
        <?php foreach ($certifies as $key => $certify) : ?>
          <li class="splide__slide">
            <img src="<?php echo esc_url($certify['img']); ?>"
                 alt="Mega Certified Trader Badge"
                 class="verified-bs__image"
                 loading="lazy">
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</section>