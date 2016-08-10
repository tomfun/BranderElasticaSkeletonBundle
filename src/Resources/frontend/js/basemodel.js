(function (global, factory) {
  if (typeof define === "function" && define.amd) {
    define(['exports', 'backbone', 'backbone-relational'], factory);
  } else if (typeof exports !== "undefined") {
    factory(exports, require('backbone'), require('backbone-relational'));
  } else {
    var mod = {
      exports: {}
    };
    factory(mod.exports, global.backbone, global.backboneRelational);
    global.basemodel = mod.exports;
  }
})(this, function (exports, _backbone) {
  'use strict';

  Object.defineProperty(exports, "__esModule", {
    value: true
  });

  var _backbone2 = _interopRequireDefault(_backbone);

  function _interopRequireDefault(obj) {
    return obj && obj.__esModule ? obj : {
      default: obj
    };
  }

  exports.default = _backbone2.default.RelationalModel;
});
//# sourceMappingURL=basemodel.js.map
