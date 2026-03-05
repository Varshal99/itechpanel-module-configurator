/**
 * Copyright © ItechPanel. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/form/element/multiselect',
    'underscore'
], function (Multiselect, _) {
    'use strict';

    return Multiselect.extend({
        defaults: {
            imports: {
                sectionIds: '${ $.provider }:data.product.section_ids',
                allOptions: '${ $.provider }:data.subsection_options'
            },
            listens: {
                sectionIds: 'filterOptions'
            }
        },

        /**
         * Initialize component
         */
        initialize: function () {
            this._super();
            this.allSubsectionOptions = this.options();
            return this;
        },

        /**
         * Filter subsection options based on selected sections
         *
         * @param {Array} selectedSectionIds
         */
        filterOptions: function (selectedSectionIds) {
            if (!selectedSectionIds || selectedSectionIds.length === 0) {
                this.setOptions([]);
                return;
            }

            // Normalize to integers for consistent comparison
            var normalizedSectionIds = _.map(selectedSectionIds, function (id) {
                return parseInt(id, 10);
            });

            var filteredOptions = _.filter(this.allSubsectionOptions, function (option) {
                if (!option.section_id) {
                    return false;
                }
                return _.contains(normalizedSectionIds, parseInt(option.section_id, 10));
            });

            this.setOptions(filteredOptions);

            // Clear selected values that are no longer valid
            var currentValue = this.value();
            if (currentValue && currentValue.length > 0) {
                var validSubsectionIds = _.pluck(filteredOptions, 'value');
                var newValue = _.filter(currentValue, function (val) {
                    return _.contains(validSubsectionIds, val);
                });

                if (newValue.length !== currentValue.length) {
                    this.value(newValue);
                }
            }
        }
    });
});
