define([
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/totals'
], function (Component, totals) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Naman_HandlingFee/summary/handling-fee',
            title: 'Handling Fee'
        },

        isDisplayed: function () {
            return this.isFullMode() && this.getPureValue() > 0;
        },

        getPureValue: function () {
            var segment = totals.getSegment('handling_fee');

            if (!segment) {
                return 0;
            }

            return segment.value;
        },

        getValue: function () {
            return this.getFormattedPrice(this.getPureValue());
        }
    });
});
