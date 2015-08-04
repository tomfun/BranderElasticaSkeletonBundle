define([
    'jquery',
    'lodash',
    'backbone',
    'util/basemodel',
], function ($, _, Backbone, BaseModel) {
    'use strict';

    return function (Filter, types, disableCategoryFilters) {
        var ModelByFilter = BaseModel.extend({
                type:         'abstract',
                fetchWhether: function () {
                    var filter = this.get('filter'),
                        need   = true;
                    if (_.isFunction(filter.needFetchByType)) {
                        need = filter.needFetchByType(this.type);
                    }
                    if (need || need === undefined) {
                        return this.fetch();
                    }
                    return 'no reload';
                },
                url:          function () {
                    return this.get('filter').getRouteByType(this.type);
                }
            }),
            ModelTypes    = {};
        _.each(types, function (type) {
            ModelTypes[type] = (ModelByFilter.extend({type: type}));
        });
        if (!disableCategoryFilters) {
            ModelTypes.filters = ModelByFilter.extend({
                defaults: {
                    filters:    [],
                    attributes: [],
                },
                type:     'filters'
            });
        }
        var Model = BaseModel.extend({
            defaults:     {
                filters: null,
            },
            fetchId:      0,
            initialize:   function () {
                BaseModel.prototype.initialize.apply(this, arguments);

                var that  = this,
                    fetch = function () {
                        that.fetch();
                    };
                this.fetchDelayed = _.debounce(fetch, 40);
                if (!this.get('filter')) {
                    this.set('filter', new Filter());
                }
                var filter = this.get('filter');
                _.each(ModelTypes, function (ModelType, type) {
                    that.set(type, new ModelType({filter: filter}));
                });
                this.get('filter').on('change', this.fetchDelayed, this);

                if (!disableCategoryFilters) {
                    var filters       = this.get('filters'),
                        updateFilters = function () {
                            filters.fetch();
                        };
                    this.get('filter').on('update-filters', updateFilters);//custom event
                }

            },
            fetchDelayed: undefined,
            fetch:        function () {
                this.fetchId++;
                this.trigger('prefetch', this.fetchId);
                var that   = this,
                    deferred,
                    models = [],
                    xhrs   = [];
                _.each(ModelTypes, function (ModelType, type) {
                    if (type !== 'filters') {
                        models.push(that.get(type));
                    }
                });
                _.each(models, function (model) {
                    xhrs.push(model.fetchWhether());
                });
                deferred = $.when.apply(this, xhrs);
                deferred.done(function (resp) {
                    that.trigger('sync', that, resp, that);
                });
                return deferred;
            },
            relations:    [
                {
                    relatedModel: Filter,
                    type:         Backbone.HasOne,
                    key:          'filter',
                }
            ],

            getAttributesMain: function (isMain) {
                return _.filter(this.get('filters').get('filterableAttributes'), function (attr) {
                    return attr.isMain === isMain;
                });
            },
        });

        return Model;
    };
});