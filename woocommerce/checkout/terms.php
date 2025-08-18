<?php
/**
 * Checkout terms and conditions area.
 *
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

if ( apply_filters( 'woocommerce_checkout_show_terms', true ) && function_exists( 'wc_terms_and_conditions_checkbox_enabled' ) ) {
	do_action( 'woocommerce_checkout_before_terms_and_conditions' );

	?>
	<div class="woocommerce-terms-and-conditions-wrapper">
		<?php
		/**
		 * Terms and conditions hook used to inject content.
		 *
		 * @since 3.4.0.
		 * @hooked wc_checkout_privacy_policy_text() Shows custom privacy policy text. Priority 20.
		 * @hooked wc_terms_and_conditions_page_content() Shows t&c page content. Priority 30.
		 */
		do_action( 'woocommerce_checkout_terms_and_conditions' );
		?>

		<?php if ( wc_terms_and_conditions_checkbox_enabled() ) : ?>
			<div class="col-lg-12">  
				<div class="form-group mb-4">
					<!-- Required Terms Checkbox -->
					<div class="form-check mb-2 d-flex validate-required">
						<input class="form-check-input" type="checkbox" value="" name="terms" <?php checked( apply_filters( 'woocommerce_terms_is_checked_default', isset( $_POST['terms'] ) ), true ); // WPCS: input var ok, csrf ok. ?> id="terms" required>
						<input type="hidden" name="terms-field" value="1" />
						<label class="form-check-label woocommerce-form__label woocommerce-form__label-for-checkbox checkbox" for="terms">
							Agree to our <a href="/terms-of-service/">Terms of Use</a> and <a href="/privacy-policy/">Privacy Policy</a>
						</label>
					</div>

					<!-- Optional Updates Checkbox -->
					<div class="form-check d-flex">
						<input class="form-check-input" type="checkbox" value="" id="updates">
						<label class="form-check-label" for="updates">
							Agree to receive Megatrader emails &amp; updates (Optional)
						</label>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<?php

	do_action( 'woocommerce_checkout_after_terms_and_conditions' );
}

?>
