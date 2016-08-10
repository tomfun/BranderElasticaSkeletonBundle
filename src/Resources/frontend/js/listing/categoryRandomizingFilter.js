define([
    'lodash',
    './randomizingFilter'
], function (_, randomizingFilter) {
    'use strict';

    /** jStorageKey = 'iwin-advert/listing/filter/defaults.random',
     routes      = {
            filtered: '',
            filteredResult: '',
            filters: '',
            nonTop: '',
            top: '',
            banner: '',
        };
     */
    return function (jStorageKey, routes) {
        var Model    = randomizingFilter(jStorageKey, routes),
            defaults = Model.prototype.defaults;


        return Model.extend({
            defaults:     _.extend(defaults, {
                category:   null,
                attributes: {}
            }),
            toCategory:   function (category) {
                if (category) {
                    this.set({
                        page:       this.defaults.page,
                        attributes: this.defaults.attributes,
                        category:   {
                            id: category
                        },
                    });
                } else {
                    this.unset('category');
                    this.unset('attributes');
                    this.set({
                        page: this.defaults.page,
                    });
                }
            },
            clearFilters: function () {
                var cat = this.get('category');
                Model.prototype.clearFilters.apply(this, arguments);
                if (cat) {
                    this.toCategory(cat.id);
                }
            },
        });
    };
});