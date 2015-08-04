define([
    'backbone',
    'templating',
], function (Backbone, templating) {
    'use strict';

    var View = Backbone.View.extend({
        templateName: '@IwinQual/List/doersFilteredResult.twig',
        globalVars:   {
            app: {
                user: {id: window.$userId ? window.$userId : undefined}
            }
        },

        initialize: function (options) {
            Backbone.View.prototype.initialize.apply(this, arguments);
            this.template = templating.get(this.templateName, this.globalVars);
        },
        render:     function () {
            var renderContext = {
                top:       this.model.get('top'),
                nonTop:    this.model.get('nonTop'),
                view:      this.model.get('filter').get('view'),
                filterTop: this.model.get('filter').get('isTop') === true,
            };
            this.$el.html(this.template(renderContext));
            this.delegateEvents();
            return this;
        }
    });

    return View;
});