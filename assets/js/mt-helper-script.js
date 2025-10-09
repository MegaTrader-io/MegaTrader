((w, d) => {
    /**
     * <div class="woocommerce-message woocommerce-error" role="alert">
     *        Thank you. Your order has been received.
     *    </div>
     */

    w.MTHelpers = {
        showMessage: function ({message, type = 'success'}, renderTo = d.body, timeout = 5000) {
            const messageContainer = d.createElement('div');
            // messageContainer.classList.add('woocommerce-message');
            if (type === 'error') {
                // messageContainer.classList.add('woocommerce-error');
            }

            messageContainer.setAttribute('role', 'alert');

            messageContainer.innerHTML = message;

            return messageContainer;
        }
    };
})(window, document);