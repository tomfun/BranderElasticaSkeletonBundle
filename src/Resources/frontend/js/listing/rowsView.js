define([
    'backbone',
    'templating',
    'underscore'
], function (Backbone, templating, _) {
    'use strict';

    var BaseProto = Backbone.View.prototype,
        View = Backbone.View.extend({
            templateName: undefined, //'@BranderEAV/Widgets/one.model.twig',{'template': '@BranderEAV/Widgets/one.model.twig'}
            template:     undefined,

            globalVars:   {
                app: {
                    user: {id: window.$userId ? window.$userId : undefined}
                }
            },

            initialize: function (options) {
                Backbone.View.prototype.initialize.apply(this, arguments);
                BaseProto.initialize.apply(this, arguments);
                this.template = templating.get((options && options.templateName)
                    || (_.isObject(this.templateName) ? this.templateName.template : this.templateName),
                    this.globalVars
                );
                if (_.isObject(this.templateName)) {
                    _.each(this.templateName, function (templateName, name) {
                        if (name !== 'template') {
                            this[name] = templating.get(templateName, this.globalVars);
                        }
                    }, this);
                }
                this.template = templating.get(this.templateName, this.globalVars);
            },

            serializeData: function () {
                var renderContext = {};
                _.each(this.model.getAvailableTypes(), function(type) {
                    renderContext[type] = this.model.get(type);
                }, this);
                return renderContext;
            },

            renderBefore: function () {
            },

            render: function () {
                _.partial(this.trigger, 'render:before').apply(this, arguments);
                this.renderBefore.apply(this, arguments);
                this.$el.html(this.template(this.serializeData()));
                this.renderAfter.apply(this, arguments);
                _.partial(this.trigger, 'render').apply(this, arguments);
                this.delegateEvents();
                return this;
            },

            renderAfter: function () {
            },
        });

    return View;
});