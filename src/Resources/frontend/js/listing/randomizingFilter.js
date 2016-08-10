define([
    'lodash',
    'simpleStorage',
    './abstractFilter'
], function (_, jStorage, abstractFilter) {
    'use strict';

    /** jStorageKey = 'iwin-advert/listing/filter/defaults.random',
     routes      = {
            filtered: '',
            filteredResult: '',
            nonTop: '',
            top: '',
            banner: '',
        };
     */
    return function (jStorageKey, routes) {
        _.each([
            'filtered',
            'filteredResult',
            'nonTop',
            'top',
            'banner',
        ], function (v) {
            if (routes[v] === undefined || routes[v] === undefined) {
                /* global console */
                console.warn('Specify routes for filter');
                throw 'Not all required routes given';
            }
        });

        var Model = abstractFilter(routes);
        return Model.extend({
            defaults:             _.extend(Model.prototype.defaults, {
                isTop:         null,
                isRandomSort:  true,
                randomPattern: jStorage.get(jStorageKey, false),
            }),
            ignorePageAttributes: _.union(Model.prototype.ignorePageAttributes, [
                'randomPattern',
                'isRandomSort'
            ]),
            initialize:           function () {
                Model.prototype.initialize.apply(this, arguments);
                this.on('change:isTop', this.onChangeIsTop, this);
                this.on('change:randomPattern', this.onChangeRandomPattern, this);
                this.on('change', this.onChange, this);
            },
            needFetchByType:      function (type) {
                return !(type === 'nonTop' && this.get('isTop') === true);
            },
            getRouteByType:       function (type) {
                var filters;
                var isTop = this.get('isTop');
                switch (type) {
                    case 'filters':
                        filters = this.toHash(true, true);
                        return Model.prototype.getRouteByType.apply(this, [type, filters]);
                    case 'nonTop':
                        this.set('isTop', false, {silent: true});
                        filters = this.toHash(true, true);
                        this.set('isTop', isTop, {silent: true});
                        return Model.prototype.getRouteByType.apply(this, [type, filters]);
                    case 'top':
                        var page = this.get('page');
                        var temporaryData;
                        if (isTop !== true) {
                            temporaryData = {
                                isTop: true,
                                page:  1
                            };
                            this.set(temporaryData, {silent: true});
                        }
                        filters = this.toHash(!this.get('isRandomSort'), true);
                        if (isTop !== true) {
                            this.set({
                                isTop: isTop,
                                page:  page
                            }, {
                                silent: true
                            });
                        }
                        return Model.prototype.getRouteByType.apply(this, [type, filters]);
                }
                return Model.prototype.getRouteByType.apply(this, arguments);
            },
            toHash:               function (withoutPattern, withoutView) {
                var data = this.toJSON();
                if (withoutPattern === true) {
                    delete data.randomPattern;
                    delete data.isRandomSort;
                }
                if (withoutView === true) {
                    delete data.view;
                }
                return JSON.stringify(data);
            },

            clearFilters: function () {
                var randomPattern = this.getIncreasedPattern();
                _.each(
                    _.without(this.keys(), 'view', 'order'),
                    function (attr) {
                        this.unset(attr, {silent: true});
                    }.bind(this)
                );
                this.set(_.defaults({
                    randomPattern: randomPattern
                }, this.defaults));
                this.onChangeRandomPattern();
            },

            onChangeIsTop: function () {
                this.set({
                    isRandomSort: this.get('isTop') === null,
                    page:         this.defaults.page
                });
                return this;
            },


            getRoute: function (isResult) {
                return Model.prototype.getRoute.apply(this, [
                    isResult,
                    this.toHash(!this.get('isRandomSort'), isResult)
                ]);
            },

            onChangeRandomPattern: function () {
                jStorage.set(jStorageKey, this.get('randomPattern'));
                return this;
            },

            getIncreasedPattern: function () {
                var randomPattern = this.get('randomPattern');
                randomPattern = _.isNumber(randomPattern) ? ++randomPattern : 0;
                randomPattern = randomPattern % 100;
                return randomPattern;
            },

            onChange: function () {
                var hash = this.toHash(true);
                if (this.lastHash !== hash) {
                    var randomPattern = this.getIncreasedPattern();
                    this.set('randomPattern', randomPattern, {
                        silent: true
                    });
                    this.onChangeRandomPattern();
                    this.lastHash = hash;
                }
                return this;
            }
        });
    };
});