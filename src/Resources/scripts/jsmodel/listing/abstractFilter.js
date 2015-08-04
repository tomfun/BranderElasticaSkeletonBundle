define([
    'jquery',
    'lodash',
    'backbone',
    'util/basemodel',
    'routing',
    'iwin-app/jstorage',
], function ($, _, Backbone, BaseModel, Routing) {
    'use strict';

    /* global console */

    /**
     routes      = {
            filtered: '', // route name for get set of filters. получаем набор фильтров
            filteredResult: '',
        };
     */
    return function (routes) {
        _.each([
            'filtered',
            'filteredResult',
        ], function (v, i) {
            if (routes[v] === undefined || routes[v] === undefined) {
                console.warn('Specify routes for filter');
                throw 'Not all required routes given';
            }
        });
        return BaseModel.extend({
            defaults:             {
                page:  1,
                view:  'tiles',
                order: 'createdAt desc',
            },
            ignorePageAttributes: ['page', 'view', 'order'],
            initialize:           function () {
                BaseModel.prototype.initialize.apply(this, arguments);
                this.on('change', this.resetPageHandler);
            },

            resetPageHandler: function () {
                var changed = _.keys(this.changedAttributes()),
                    list    = _.intersection(this.ignorePageAttributes, changed);
                if (list.length <= 0 && changed.length > 0) {
                    this.set({page: this.defaults.page}, {silent: true});
                }
            },

            toHash: function () {
                return JSON.stringify(this.toJSON());
            },

            getRoutingName: function (isResult) {
                return isResult ? routes.filteredResult : routes.filtered;
            },

            getRoute: function (isResult, filters) {
                return Routing.generate(this.getRoutingName(isResult), {
                    filters: filters === undefined ? this.toHash() : filters,
                });
            },

            getRouteByType: function (type, filters) {
                if (!type || type === 'abstract' || !routes[type]) {
                    throw 'Wrong filter type for routing';
                }
                if (filters === undefined) {
                    filters = this.toHash();
                }
                return Routing.generate(routes[type], {filters: filters,});
            },

            clearFilters: function () {
                _.each(
                    _.without(this.keys(), 'view', 'order'),
                    function (attr) {
                        this.unset(attr, {silent: true});
                    }.bind(this)
                );
                this.set({
                    page: this.defaults.page,
                    view: this.defaults.view,
                });
            },
        });
    };
});