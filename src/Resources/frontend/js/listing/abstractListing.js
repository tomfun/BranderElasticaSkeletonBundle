define([
    'jquery',
    'lodash',
    'backbone',
    'brander-elastica-skeleton/basemodel',
], function ($, _, Backbone, BaseModelEs6) {
    'use strict';

    var BaseModel = BaseModelEs6.default;

    /**
     * Filter - Class, produced by abstractFilter
     * types - Array of result types. example: ['top', 'nonTop'],
     *   not this: ['filter', 'top', 'nonTop'],
     *   the 'filter' type regulated by disableCategoryFilters, and almost always must be.
     *   Used to create fields in result Model with helper models with filter field itself
     * ResultModel - Model for result from server. By default is BaseModel
     * disableCategoryFilters - boolean flag. If true, 'filter' helper model will not be created.
     *
     * @return factory to create meta Model with helper models (like filter, result, top, nonTop)
     *  this Model also make synchronise fetches of this models
     *
     *  На простом понятном. Эта рекваер жс хуян возвращает функцию в которую ты загоняешь модель фильтров
     *  на основании которой грузяться разнообразные данные основанные на !1-ом! фильтре. Также делает синхронизацию и
     *  события.
     *  Пример: нужно сделать так, чтобы в зависимости от состояния фильтров, загрузились доступные фильтры, результат для утюгов и для чайников
     *  и ещё ебучий банер, который блядь обязан подбираться на основании запроса:
     *  define([
     *    'lodash',
     *     'brander-elastica-skeleton/listing/abstractFilter',
     *     'brander-elastica-skeleton/listing/abstractListing',
     *  ], function (_, filterFactory, listingFactory) {
     *      var routes      = {
                    filteredResult: 'advert_list_filtered_result',//result route
                    teapot: 'advert_list_filtered_result',//result route. may be same
                    banner: 'advert_list_filtered_result',//result route
                    filtered: 'advert_list_filtered',// static page
                    filters: 'advert_list_category_filters',// return available filters (for  example for current category)
                },
                FilterModel = filterFactory(routes),
                // ...
                // logic for rewriting 'getRouteByType' function (support teapot, banner...)
                // ...
                listingModel = listingFactory(FilterModel, ['teapot', 'iron', 'banner']));
     */
    return function (Filter, types, ResultModel, disableCategoryFilters) {
        if (!ResultModel) {
            ResultModel = BaseModel;
        }
        if (!_.isFunction(ResultModel.extend)) {
            throw "wrong model";
        }
        var ModelByFilter = ResultModel.extend({
                type:         'abstract',
                fetchWhether: function () {
                    var filter = this.get('filter'),
                        need   = true;
                    if (_.isFunction(filter.needFetchByType)) {
                        need = filter.needFetchByType(this.type);
                    }
                    if (need || need === undefined) {
                        return this.fetch.apply(this, arguments);
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
                    attributes: {},
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
                        that.fetch.apply(that, arguments);
                    };
                this.fetchDelayed = _.debounce(fetch, 40);
                if (!this.get('filter')) {
                    this.set('filter', new Filter());
                }
                var filter = this.get('filter');
                _.each(ModelTypes, function (ModelType, type) {
                    that.set(type, new ModelType({filter: filter}));
                });
                this.get('filter').on('change', this.fetchOnChange, this);

                if (!disableCategoryFilters) {
                    var filters       = this.get('filters'),
                        updateFilters = function () {
                            filters.fetch();
                        };
                    this.get('filter').on('update-filters', updateFilters);//custom event
                }

            },
            fetchDelayed: undefined,
            fetchOnChange: function () {
                this.fetchDelayed({fetchOnChange: true});
            },
            fetch:        function () {
                this.fetchId++;
                this.trigger('prefetch', this.fetchId);
                var that   = this,
                    deferred,
                    models = [],
                    xhrs   = [],
                    args = arguments;
                _.each(ModelTypes, function (ModelType, type) {
                    if (type !== 'filters') {
                        models.push(that.get(type));
                    }
                });
                _.each(models, function (model) {
                    xhrs.push(model.fetchWhether.apply(model, args));
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

            getAvailableTypes: function () {
                return _.keys(ModelTypes);
            }
        });

        return Model;
    };
});