// Generated by BUCKLESCRIPT, PLEASE EDIT WITH CARE
'use strict';

var React = require("react");
var Caml_option = require("bs-platform/lib/js/caml_option.js");

function getComponent(placeholderOpt, childrenOpt, valueOpt, param) {
  var placeholder = placeholderOpt !== undefined ? Caml_option.valFromOption(placeholderOpt) : undefined;
  var children = childrenOpt !== undefined ? Caml_option.valFromOption(childrenOpt) : undefined;
  var value = valueOpt !== undefined ? Caml_option.valFromOption(valueOpt) : undefined;
  if (placeholder !== undefined) {
    return Caml_option.valFromOption(placeholder);
  } else if (children !== undefined) {
    return Caml_option.valFromOption(children);
  } else if (value !== undefined) {
    return Caml_option.valFromOption(value);
  } else {
    return null;
  }
}

function getClass(display) {
  return "is-loadable-placeholder" + (
          "" === display ? "" : " is-" + display
        );
}

function Loadable(Props) {
  var isLoading = Props.isLoading;
  var display = Props.display;
  var placeholder = Props.placeholder;
  var value = Props.value;
  var children = Props.children;
  if (isLoading) {
    return React.createElement("span", {
                "aria-busy": true,
                className: getClass(display)
              }, getComponent(Caml_option.some(placeholder), Caml_option.some(children), Caml_option.some(value), undefined));
  } else {
    return getComponent(undefined, Caml_option.some(children), Caml_option.some(value), undefined);
  }
}

var make = Loadable;

var $$default = Loadable;

exports.getComponent = getComponent;
exports.getClass = getClass;
exports.make = make;
exports.$$default = $$default;
exports.default = $$default;
exports.__esModule = true;
/* react Not a pure module */