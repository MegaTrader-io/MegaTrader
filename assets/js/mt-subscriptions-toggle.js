jQuery(function ($) {

    $('.wcs-auto-renew-toggle').each(function () {

        const $container = $(this);
        const subId = $container.data('sub-id');
        const params = WCSListSubscriptions[subId];
        const $toggle = $container.find('.subscription-auto-renew-toggle');
        const $icon = $toggle.find('i');

        // bail if param missing
        if (!params) return;

        // apply initial color, etc.
        function updateColor() {
            const on = $toggle.hasClass('subscription-auto-renew-toggle--on');
            if (on) {
                $icon.css({
                    backgroundColor: getComputedStyle($icon[0]).color,
                    borderColor: getComputedStyle($icon[0]).color
                });
            } else {
                $icon.css({ backgroundColor: '', borderColor: '' });
            }
        }

        function block() { $container.block({ message: null, overlayCSS: { opacity: 0.0 } }); }
        function unblock() { $container.unblock(); }

        $toggle.on('click', function (e) {
            e.preventDefault();

            if ($toggle.hasClass('subscription-auto-renew-toggle--disabled')) return;

            const enable = $toggle.hasClass('subscription-auto-renew-toggle--off');
            const action = enable ? 'wcs_enable_auto_renew' : 'wcs_disable_auto_renew';

            if (enable && !params.has_payment_gateway) {
                if (window.confirm(params.add_payment_method_msg)) {
                    window.location.href = params.add_payment_method_url;
                }
                return;
            }

            block();

            $.ajax({
                url: params.ajax_url,
                type: 'POST',
                data: {
                    subscription_id: subId,
                    action: action,
                    security: params.auto_renew_nonce
                },
                success: function () {
                    if (enable) {
                        $icon.removeClass('fa-toggle-off').addClass('fa-toggle-on');
                        $toggle.removeClass('subscription-auto-renew-toggle--off')
                            .addClass('subscription-auto-renew-toggle--on');
                    } else {
                        $icon.removeClass('fa-toggle-on').addClass('fa-toggle-off');
                        $toggle.removeClass('subscription-auto-renew-toggle--on')
                            .addClass('subscription-auto-renew-toggle--off');
                    }
                    updateColor();
                },
                complete: unblock
            });
        });

        updateColor();
    });

});
