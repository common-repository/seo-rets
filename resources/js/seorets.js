(function () {
    function g(a) {
        var b = typeof a;
        if ("object" == b)if (a) {
            if (a instanceof Array)return "array";
            if (a instanceof Object)return b;
            var c = Object.prototype.toString.call(a);
            if ("[object Window]" == c)return "object";
            if ("[object Array]" == c || "number" == typeof a.length && "undefined" != typeof a.splice && "undefined" != typeof a.propertyIsEnumerable && !a.propertyIsEnumerable("splice"))return "array";
            if ("[object Function]" == c || "undefined" != typeof a.call && "undefined" != typeof a.propertyIsEnumerable && !a.propertyIsEnumerable("call"))return "function"
        } else return "null"; else if ("function" == b && "undefined" == typeof a.call)return "object";
        return b
    };
    function h(a) {
        a = "" + a;
        if (/^\s*$/.test(a) ? 0 : /^[\],:{}\s\u2028\u2029]*$/.test(a.replace(/\\["\\\/bfnrtu]/g, "@").replace(/"[^"\\\n\r\u2028\u2029\x00-\x08\x10-\x1f\x80-\x9f]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, "]").replace(/(?:^|:|,)(?:[\s\u2028\u2029]*\[)+/g, "")))try {
            return eval("(" + a + ")")
        } catch (b) {
        }
        throw Error("Invalid JSON string: " + a);
    }

    function i(a, b) {
        var c = [];
        j(new k(b), a, c);
        return c.join("")
    }

    function k(a) {
        this.a = a
    }

    function j(a, b, c) {
        switch (typeof b) {
            case "string":
                l(b, c);
                break;
            case "number":
                c.push(isFinite(b) && !isNaN(b) ? b : "null");
                break;
            case "boolean":
                c.push(b);
                break;
            case "undefined":
                c.push("null");
                break;
            case "object":
                if (null == b) {
                    c.push("null");
                    break
                }
                if ("array" == g(b)) {
                    var f = b.length;
                    c.push("[");
                    for (var d = "", e = 0; e < f; e++)c.push(d), d = b[e], j(a, a.a ? a.a.call(b, "" + e, d) : d, c), d = ",";
                    c.push("]");
                    break
                }
                c.push("{");
                f = "";
                for (e in b)Object.prototype.hasOwnProperty.call(b, e) && (d = b[e], "function" != typeof d && (c.push(f), l(e, c), c.push(":"), j(a, a.a ? a.a.call(b, e, d) : d, c), f = ","));
                c.push("}");
                break;
            case "function":
                break;
            default:
                throw Error("Unknown type: " + typeof b);
        }
    }

    var m = {
        '"': '\\"',
        "\\": "\\\\",
        "/": "\\/",
        "\u0008": "\\b",
        "\u000c": "\\f",
        "\n": "\\n",
        "\r": "\\r",
        "\t": "\\t",
        "\x0B": "\\u000b"
    }, n = /\uffff/.test("\uffff") ? /[\\\x22\x00-\x1f\x7f-\uffff]/g : /[\\\"\x00-\x1f\x7f-\xff]/g;

    function l(a, b) {
        b.push('"', a.replace(n, function (a) {
            if (a in m)return m[a];
            var b = a.charCodeAt(0), d = "\\u";
            16 > b ? d += "000" : 256 > b ? d += "00" : 4096 > b && (d += "0");
            return m[a] = d + b.toString(16)
        }), '"')
    };
    window.JSON || (window.JSON = {});
    "function" !== typeof window.JSON.stringify && (window.JSON.stringify = i);
    "function" !== typeof window.JSON.parse && (window.JSON.parse = h)
})();


if (window.seorets === undefined) {
    //This prototype is provided by the Mozilla foundation and
    //is distributed under the MIT license.
    //http://www.ibiblio.org/pub/Linux/LICENSES/mit.license
    if (!Array.prototype.map) {
        Array.prototype.map = function (fun /*, thisp*/) {
            var len = this.length;
            if (typeof fun != "function") {
                throw new TypeError();
            }

            var res = new Array(len);
            var thisp = arguments[1];
            for (var i = 0; i < len; i++) {
                if (i in this) res[i] = fun.call(thisp, this[i], i, this);
            }

            return res;
        };
    }

    if (!String.prototype.reverse) {
        String.prototype.reverse = function () {
            return this.split("").reverse().join("");
        }
    }

    if (!Array.prototype.clean) {
        Array.prototype.clean = function (deleteValue) {
            for (var i = 0; i < this.length; i++) {
                if (this[i] == deleteValue) {
                    this.splice(i, 1);
                    i--;
                }
            }
            return this;
        };
    }


    var explode = function (s) {
        return s.reverse().split(/,(?!(\\\\)*\\(?!\\))/).reverse().clean(null).map(function (x) {
            return x.reverse().replace(/\\(.)/g, "$1");
        });
    }

    var r = new RegExp('[^\\d\\.]+', 'g');
    var rtrim = function (s) {
        var r = s.length - 1;
        while (r > 0 && s[r] == '=') {
            r -= 1;
        }
        return s.substring(0, r + 1);
    }
    var z = function getConditionFromElement() {
        var obj = jQuery(this);
        var type = obj.attr('type');
        if ((type == "checkbox" || type == "radio") && obj.attr('checked') == undefined)return;
        var value = obj.val();
        if (typeof value == "object" && value != null) {
            value = value.pop();
        }
        if (value == "" || value == null)return;
        if (obj.attr('srtype') !== undefined && obj.attr('srtype').toLowerCase() == 'numeric') {
            value = seorets.parseNum(value);
        }
        var field = obj.attr('srfield'), operator = obj.attr('sroperator'), loose = obj.attr('srloose') !== undefined;
        if (obj.is('select')) {
            var options = obj.find('option:selected');
            if (options.length > 1) {
                var result = {b: 0, c: []};
                options.each(function () {
                    var me = jQuery(this);
                    var f = me.attr('srfield');
                    var o = me.attr('sroperator');
                    var l = me.attr('srloose');
                    var condition = {f: field, o: operator, v: me.val()};
                    if (l !== undefined || loose) {
                        condition.l = 1;
                    }
                    if (f !== undefined) {
                        condition.f = f;
                    }
                    if (o !== undefined) {
                        condition.o = o;
                    }
                    if (condition.o == "in") {
                        condition.v = explode(condition.v);
                    }
                    result.c.push(condition);
                });
                return result;
            }
            var f = options.attr('srfield');
            var o = options.attr('sroperator');
            var l = options.attr('srloose');
            if (f !== undefined)
                field = f;
            if (o !== undefined)
                operator = o;
            if (l !== undefined)
                loose = true;
        }
        if (operator == "in") {
            value = explode(value);
        }
        var result = {f: field, o: operator, v: value};
        if (loose) {
            result.l = 1;
        }
        return result;
    };
    var x = function (b) {
        var a = function () {
            return 0 == jQuery(this).parentsUntil(b, ".sr-formsection").length
        };
        var c = b.find(".sr-formelement").filter(a).map(z).get();
        var a = b.find(".sr-formsection").filter(a).map(function () {
            return x(jQuery(this))
        }).get();
        var d = b.attr("sroperator");
        if (d === undefined)d = "and"; else d = d.toLowerCase();
        if (jQuery.inArray(d, ["or", "and"]) == -1)console.log("Error: invalid sroperator, using \"AND\"");
        return {b: "or" == d ? 0 : 1, c: c.concat(a)}
    };
    var getOrder = function (root) {
        var result = [];
        root.find('.sr-order').each(function () {
            var node = jQuery(this);
            var field = node.attr('srfield'), direction = node.attr('srdirection');
            if (node.is("select")) {
                var testNode = node.find("option:selected");
                testField = testNode.attr("srfield");
                testDirection = testNode.attr("srdirection");
                if (testField !== undefined) {
                    field = testField;
                }
                if (testDirection !== undefined) {
                    direction = testDirection;
                }
            }
            if (field === undefined) {
                console.log("Error: no field in order statement");
                return;
            }
            var d = 0;
            if (direction === undefined) {
                console.log("Error: no direction in order statement");
                return;
            } else {
                direction = direction.toLowerCase();
                if (jQuery.inArray(direction, ["asc", "desc"]) == -1) {
                    console.log("Error: invalid direction, using \"DESC\"");
                }
                d = direction == "asc" ? 1 : 0;
            }
            result.push({f: field, o: d});
        });
        return result;
    };
    var getLimit = function (root) {
        var limit = root.find('.sr-limit');

        if (limit.length == 0) {
            return 10;
        } else if (limit.length > 1) {
            console.log("Error: too many limits specified, using first");
            limit = limit.first();
        }

        var num = parseInt(limit.val());
        if (isNaN(num)) {
            console.log("Error: limit is NaN");
            return 10;
        }
        if (num < 1 || num > 300) {
            console.log("Error: limit out of bounds");
            return 10;
        }
        return num;
    };
    window.seorets = {
        parseNum: function (v) {
            return parseFloat(v.replace(r, ''));
        },
        base64encode: function (b) {
            var s = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
            for (var g = "", a, c, d, i, h, e, f = 0; f < b.length;)a = b.charCodeAt(f++), c = b.charCodeAt(f++), d = b.charCodeAt(f++), i = a >> 2, a = (a & 3) << 4 | c >> 4, h = (c & 15) << 2 | d >> 6, e = d & 63, isNaN(c) ? h = e = 64 : isNaN(d) && (e = 64), g = g + s.charAt(i) + s.charAt(a) + s.charAt(h) + s.charAt(e);
            return g
        },
        getFormRequest: function (b) {
            var a = b.attr("srtype");
            if (a === undefined) {
                console.log("Error: no type specified");
                return {};
            }
            var result = {q: x(b), t: a, p: getLimit(b)};
            var order = getOrder(b);
            if (order.length > 0) {
                result.o = order;
            }

            return result;
        },
        startForm: function (a, b) {
            jQuery(function () {
                if (void 0 === a)var a = jQuery(".sr-formsection");
                else"string" == typeof a && (a = jQuery(a));
                var d = [function (r) {
                    r.find(".sr-submit").click(function () {
                        if (jQuery('body').hasClass('sr-search-oinw')) {
                            window.open(
                                seorets.options.blogurl + "/sr-search?" + encodeURIComponent(Base64.encode(JSON.stringify(seorets.getFormRequest(r)))),
                                '_blank' // <- This is what makes it open in a new window.
                            );
                        } else {

                            window.location = seorets.options.blogurl + "/sr-search?" + encodeURIComponent(Base64.encode(JSON.stringify(seorets.getFormRequest(r))));
                        }
                    })
                }];
                var c = typeof b;
                if (c == "function")
                    d.push(b)
                else if (c == "array")
                    d.concat(b)
                for (var i = 0; i < d.length; i++)
                    if (typeof d[i] == "function")
                        d[i](a);
            });
        }
    };
}
