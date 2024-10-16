/*!
 * Telex 2.0.7
 * (c) 2024 sjaakpriester.nl
 */
var Telex = function (e) {
    "use strict";
    var t = "undefined" != typeof globalThis ? globalThis : "undefined" != typeof window ? window : "undefined" != typeof global ? global : "undefined" != typeof self ? self : {},
        n = /^\s+|\s+$/g,
        i = /^[-+]0x[0-9a-f]+$/i,
        s = /^0b[01]+$/i,
        a = /^0o[0-7]+$/i,
        o = parseInt,
        r = "object" == typeof t && t && t.Object === Object && t,
        l = "object" == typeof self && self && self.Object === Object && self,
        u = r || l || Function("return this")(),
        d = Object.prototype.toString,
        c = Math.max,
        f = Math.min,
        m = function () {
            return u.Date.now()
        };

    function h(e) {
        var t = typeof e;
        return !!e && ("object" == t || "function" == t)
    }

    function p(e) {
        if ("number" == typeof e) return e;
        if (function (e) {
                return "symbol" == typeof e || function (e) {
                    return !!e && "object" == typeof e
                }(e) && "[object Symbol]" == d.call(e)
            }(e)) return NaN;
        if (h(e)) {
            var t = "function" == typeof e.valueOf ? e.valueOf() : e;
            e = h(t) ? t + "" : t
        }
        if ("string" != typeof e) return 0 === e ? e : +e;
        e = e.replace(n, "");
        var r = s.test(e);
        return r || a.test(e) ? o(e.slice(2), r ? 2 : 8) : i.test(e) ? NaN : +e
    }
    var g = function (e, t, n) {
        var i, s, a, o, r, l, u = 0,
            d = !1,
            g = !1,
            v = !0;
        if ("function" != typeof e) throw new TypeError("Expected a function");

        function y(t) {
            var n = i,
                a = s;
            return i = s = void 0, u = t, o = e.apply(a, n)
        }

        function x(e) {
            return u = e, r = setTimeout(_, t), d ? y(e) : o
        }

        function b(e) {
            var n = e - l;
            return void 0 === l || n >= t || n < 0 || g && e - u >= a
        }

        function _() {
            var e = m();
            if (b(e)) return w(e);
            r = setTimeout(_, function (e) {
                var n = t - (e - l);
                return g ? f(n, a - (e - u)) : n
            }(e))
        }

        function w(e) {
            return r = void 0, v && i ? y(e) : (i = s = void 0, o)
        }

        function E() {
            var e = m(),
                n = b(e);
            if (i = arguments, s = this, l = e, n) {
                if (void 0 === r) return x(l);
                if (g) return r = setTimeout(_, t), y(l)
            }
            return void 0 === r && (r = setTimeout(_, t)), o
        }
        return t = p(t) || 0, h(n) && (d = !!n.leading, a = (g = "maxWait" in n) ? c(p(n.maxWait) || 0, t) : a, v = "trailing" in n ? !!n.trailing : v), E.cancel = function () {
            void 0 !== r && clearTimeout(r), u = 0, i = l = s = r = void 0
        }, E.flush = function () {
            return void 0 === r ? o : w(m())
        }, E
    };
    return function (e) {
        if (e && "undefined" != typeof window) {
            var t = document.createElement("style");
            t.setAttribute("type", "text/css"), t.innerHTML = e, document.head.appendChild(t)
        }
    }("@keyframes telex{from{margin-left:0}}.telex{display:flex;white-space:nowrap;overflow-x:hidden;line-height:1.7;min-height:1.7em}.telex > div{padding:0 1.5em}"), e.Widget = function (e, t, n) {
        var i = this;
        Object.setPrototypeOf(this, {
            defaults: {
                speed: 50,
                direction: "normal",
                timing: "linear",
                pauseOnHover: 1,
                onCycle: function (e) {}
            },
            animStart: function (e) {
                if (e) {
                    var t = this._elementWidth(e),
                        n = 1e3 * t / this.speed;
                    e.classList.contains("telex-head") && this.onCycle(this), Object.assign(e.style, {
                        marginLeft: "-" + t + "px",
                        animationName: "telex",
                        animationDirection: this.direction,
                        animationDuration: n + "ms",
                        animationTimingFunction: this.timing
                    })
                }
            },
            animStop: function (e) {
                e && Object.assign(e.style, {
                    marginLeft: null,
                    animationName: "none"
                })
            },
            discardMsg: function (e) {
                e && e.classList.add("telex-discard")
            },
            populate: function () {
                var e = this,
                    t = this.element.children.length;
                this.element.childNodes.forEach((function (t) {
                    e.discardMsg(t)
                }));
                var n = this._elementWidth(this.element),
                    i = {
                        total: 0,
                        max: 0
                    };
                do {
                    i = this._messages.reduce((function (t, n, i) {
                        "string" == typeof n && (n = {
                            content: n
                        });
                        var s = document.createElement("div");
                        s.innerHTML = n.content, n.class && s.classList.add(n.class), 0 === i && s.classList.add("telex-head"), e.element.append(s);
                        var a = e._elementWidth(s);
                        return {
                            total: t.total + a,
                            max: a > t.max ? a : t.max
                        }
                    }), i)
                } while (i.total > 0 && i.total < n + i.max);
                t || this.animStart(this.element.firstElementChild)
            },
            _setAnimationState: function (e) {
                var t = this.element.firstElementChild;
                t && (t.style.animationPlayState = e)
            },
            _elementWidth: function (e) {
                return e.getBoundingClientRect().width
            },
            _isVisible: function (e) {
                return e.getBoundingClientRect().right > this.element.getBoundingClientRect().left
            },
            _removeIfDiscarded: function (e) {
                // return console.log(e.getBoundingClientRect()), !(!e || !e.classList.contains("telex-discard")) && (e.remove(), !0)
            },
            set messages(e) {
                this._messages = e, this.populate()
            },
            get messages() {
                return this._messages
            },
            add: function (e) {
                this._messages.unshift(e), this.populate()
            },
            remove: function (e) {
                var t = this._messages.findIndex((function (t) {
                    return t.id === e
                }));
                t >= 0 && this._messages.splice(t, 1), this.populate()
            },
            update: function (e, t) {
                var n = this._messages.findIndex((function (t) {
                    return t.id === e
                }));
                n >= 0 && this._messages.splice(n, 1, t), this.populate()
            },
            pause: function () {
                this._setAnimationState("paused")
            },
            resume: function () {
                this._setAnimationState("running")
            }
        }), this.element = document.getElementById(e), this.element.classList.add("telex"), this.element.addEventListener("animationend", (function (e) {
            if (i.animStop(e.target), i.direction === e.target.style.animationDirection)
                if ("normal" === i.direction) {
                    for (; i._removeIfDiscarded(i.element.firstElementChild););
                    var t = i.element.firstElementChild;
                    t && i.element.append(t)
                } else {
                    for (; i._removeIfDiscarded(i.element.lastElementChild););
                    var n = i.element.lastElementChild;
                    n && i.element.prepend(n)
                } i.animStart(i.element.firstElementChild)
        })), this.element.addEventListener("mouseenter", (function (e) {
            i.pauseOnHover && i._setAnimationState("paused")
        })), this.element.addEventListener("mouseleave", (function (e) {
            i.pauseOnHover && !i._paused && i._setAnimationState("running")
        })), window.addEventListener("resize", g((function (e) {
            i.populate()
        }), 300)), Object.assign(this, this.defaults, t), this._messages = n, this.populate()
    }, e.widget = function (e, t, n) {
        return new this.Widget(e, t, n)
    }, Object.defineProperty(e, "__esModule", {
        value: !0
    }), e.version = "2.0.7", e
}({});
//# sourceMappingURL=telex.js.map
