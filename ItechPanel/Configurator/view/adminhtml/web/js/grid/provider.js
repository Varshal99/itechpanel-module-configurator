/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/grid/provider'
], function (Provider) {
    'use strict';

    return Provider.extend({
        /**
         * Reload data
         */
        reload: function (options) {
            this._super(options);
            this.loadSelectedProducts();

            return this;
        },

        /**
         * Load data from server
         */
        load: function (params) {
            return this._super(params).done(function () {
                this.loadSelectedProducts();
            }.bind(this));
        },

        /**
         * Load selected products and set them in the selections column
         */
        loadSelectedProducts: function () {
            var sectionId = this.params.section_id;

            if (!sectionId || !this.client) {
                return;
            }

            if (this.data && this.data.selectedProducts) {
                var selected = this.data.selectedProducts;

                // Notify the selections column about pre-selected items
                if (typeof this.client.selections === 'function') {
                    this.client.selections(selected);
                }
            }
        }
    });
});
