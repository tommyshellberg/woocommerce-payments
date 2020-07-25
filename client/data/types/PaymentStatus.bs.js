// Generated by BUCKLESCRIPT, PLEASE EDIT WITH CARE
'use strict';

var Belt_Option = require("bs-platform/lib/js/belt_Option.js");

function fromCharge(charge) {
  var outcomeType = Belt_Option.map(charge.outcome, (function (o) {
          return o.type;
        }));
  var disputeStatus = charge.disputed ? Belt_Option.map(charge.dispute, (function (d) {
            return d.status;
          })) : undefined;
  var fullyRefunded = charge.refunded;
  var partiallyRefunded = charge.amount_refunded > 0 && !fullyRefunded;
  var match = charge.status;
  if (match >= 2 && outcomeType !== undefined) {
    if (outcomeType === 3) {
      return /* Blocked */1;
    }
    if (outcomeType >= 2) {
      return /* Failed */0;
    }
    
  }
  if (disputeStatus !== undefined) {
    return /* Disputed */{
            _0: disputeStatus
          };
  } else if (partiallyRefunded) {
    return /* PartiallyRefunded */2;
  } else if (fullyRefunded) {
    return /* FullyRefunded */3;
  } else if (charge.captured) {
    return /* Paid */4;
  } else {
    return /* Authorized */5;
  }
}

exports.fromCharge = fromCharge;
/* No side effect */