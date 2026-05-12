define([
    'uiComponent',
    'Magento_Customer/js/customer-data'
], function (Component, customerData) {
    'use strict';

    return Component.extend({
        initialize: function () {
            this._super();
            this.cart = customerData.get('cart');
            return this;
        },

        isDisplayed: function () {
            return this.getValue() > 0;
        },

        getValue: function () {
            var cartData = this.cart();

            if (!cartData || !cartData.handling_fee_amount) {
                return 0;
            }

            return parseFloat(cartData.handling_fee_amount);
        },

        getFormattedValue: function () {
            var cartData = this.cart();

            if (!cartData || !cartData.handling_fee) {
                return '';
            }

            return cartData.handling_fee;
        }
    });
});
