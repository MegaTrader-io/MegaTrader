<?php
$platform_base_url = get_template_directory_uri() . '/assets/img/landing-page/platforms';
?>

<section class="platforms-bs">
  <div class="landing-bs-container">
    <div class="platforms-bs__list">
      <div class="platforms-bs__first-row">
        <div class="platforms-bs__item platforms-bs__item--available">
          <img src="<?= $platform_base_url . '/megatraderx.svg'; ?>" alt="MegaTraderX Sponsor Logo">
        </div>
        <div class="platforms-bs__item">
          <img src="<?= $platform_base_url . '/tradovate.svg'; ?>" alt="MegaTraderX Sponsor Logo">
          <div class="mt-badge mt-badge-rounded-sm mt-badge-light">COMING SOON</div>
        </div>
      </div>
      <div class="platforms-bs__second-row">
        <div class="platforms-bs__item">
          <img src="<?= $platform_base_url . '/ninjatrader.svg'; ?>" alt="MegaTraderX Sponsor Logo">
          <div class="mt-badge mt-badge-rounded-sm mt-badge-light">COMING SOON</div>
        </div>
        <div class="platforms-bs__item">
          <img src="<?= $platform_base_url . '/quantower.svg'; ?>" alt="MegaTraderX Sponsor Logo">
          <div class="mt-badge mt-badge-rounded-sm mt-badge-light">COMING SOON</div>
        </div>
      </div>
    </div>
  </div>
</section>