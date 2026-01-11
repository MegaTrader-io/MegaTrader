<footer class="footer-bs">
    <div class="footer-bs__content">
        <div class="footer-bs__top">
            <div class="footer-bs__column footer-bs__column--info">
                <div class="footer-bs__logo-group">
                    <div class="footer-bs__logo-box">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/landing-page/logo-mt.svg"
                             width="60" height="60" alt="Logo MegaTraderX"/>
                    </div>
                    <div class="footer-bs__logo-text">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/landing-page/megatrader-bs.svg"
                             alt="Logo"
                             width="200"
                             height="45">
                    </div>
                </div>

                <p class="footer-bs__description">
                    From evaluation to funding, we’re redefining the trader journey with performance-driven solutions
                    and transparency.
                </p>

                <?php get_template_part('template-parts/landing-page/sections/social_media-bs'); ?>
            </div>

            <?php get_template_part('template-parts/landing-page/sections/subscribe_form-bs'); ?>
        </div>

        <div class="footer-bs-divider">
            <div class="footer-bs-divider__line"></div>
        </div>

        <div class="footer-bs__bottom">
            <div class="footer-bs__copy">© <?= date('Y') ?> MegaTraderX</div>
            <div class="footer-bs__links">
                <a href="#" class="footer-bs__link" data-bs-toggle="modal" data-bs-target="#disclaimerModal">Disclaimer</a>
                <a href="#" class="footer-bs__link" data-bs-toggle="modal" data-bs-target="#privacyModal">Privacy Policy</a>
                <a href="#" class="footer-bs__link" data-bs-toggle="modal" data-bs-target="#termsModal">Terms of Service</a>
                <a href="#" class="footer-bs__link" data-bs-toggle="modal" data-bs-target="#cookiesModal">Cookies
                    Settings</a>
            </div>
        </div>
    </div>
</footer>