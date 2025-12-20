<form id="form-subscription" novalidate class="footer-bs__column footer-bs__column--subscribe">
    <div class="footer-bs__alert d-none">
        <div role="alert" tabindex="-1" class="woocommerce-message">
            Congratulations, you have successfully subscribed.
        </div>
    </div>

    <div class="footer-bs__form">
        <div class="footer-bs__input-group">
            <label for="email" class="w-100 mb-0">
                <input type="email" id="email" name="email" autocomplete="off" placeholder="Enter your email" class="form-control">
            </label>
            <div id="email-error" class="email_text invalid-text d-none"></div>
        </div>

        <button type="submit" disabled class="btn mega-btn-md mega-btn-primary-md">
            SUBSCRIBE
        </button>
    </div>

    <div class="footer-bs__consent">
        <div class="checkbox-container">
            <input class="checkbox-container__input" type="checkbox"
                   id="email_consent"
                   name="email_consent">

            <label for="email_consent" class="checkbox-container__label m-0">
                <span class="footer-bs__consent-text">
                    I consent to the use of my email address to receive news, updates, and important notifications.
                </span>
            </label>
        </div>
    </div>
</form>