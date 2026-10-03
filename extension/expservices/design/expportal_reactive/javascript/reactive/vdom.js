/**
 * The React-like model, part 1: elements and rendering.
 *
 *   ExpPortal.h( tag, props, ...children )  describes DOM (a plain object, nothing is created yet)
 *   ExpPortal.mount( container, view )      view() returns the description; call mount.update() after state changed
 *
 * A component is a function: props/state in, description out. It never touches the DOM and never keeps state of its own.
 * The renderer compares the new description with the previous one and changes only what differs (keyed lists move
 * nodes instead of recreating them, so focus, typed text and scroll position of unchanged nodes survive).
 *
 * props: class, id, ... (attributes); value / checked / disabled / hidden / selected (properties); on: { click: fn }
 * (one delegating listener per event type, so a handler is replaced, never stacked); key (identity inside a list);
 * ref: fn(domNode) called after the node was created or updated (the place for focus(), showModal() and the like).
 * Text is always set as text; there is no way to inject HTML.
 */
(function (window, $) {
    'use strict';
    var Exp = window.ExpPortal = window.ExpPortal || {};

    function norm(c, out) {
        if (c === null || c === undefined || c === false || c === true) { return out; }
        if (Array.isArray(c)) { for (var i = 0; i < c.length; i++) { norm(c[i], out); } return out; }
        out.push(typeof c === 'object' ? c : { t: '#text', s: String(c), p: {}, c: [] });
        return out;
    }
    var PROPS = { value: 1, checked: 1, disabled: 1, hidden: 1, selected: 1 };

    Exp.h = function (tag, props) {
        props = props || {};
        return { t: tag, p: props, c: norm(Array.prototype.slice.call(arguments, 2), []), k: props.key === undefined ? null : props.key };
    };

    function setProps(el, oldP, newP) {
        var name;
        for (name in oldP) { if (!(name in newP) && name !== 'key' && name !== 'ref') { unset(el, name, oldP[name]); } }
        for (name in newP) {
            if (name === 'key' || name === 'ref') { continue; }
            if (name === 'on') { handlers(el, newP.on); continue; }
            if (oldP[name] === newP[name] && !PROPS[name]) { continue; }
            var v = newP[name];
            if (PROPS[name]) {
                if (name === 'value') { v = v === null || v === undefined ? '' : String(v); if (el.value !== v) { el.value = v; } }
                else { el[name] = !!v; }
            }
            else if (v === false || v === null || v === undefined) { el.removeAttribute(name); }
            else { el.setAttribute(name, v === true ? '' : v); }
        }
    }
    function unset(el, name) { if (name === 'on') { handlers(el, {}); } else if (PROPS[name]) { el[name] = name === 'value' ? '' : false; } else { el.removeAttribute(name); } }
    function handlers(el, map) {
        var h = el._h = el._h || {}, ev;
        for (ev in h) { if (!(ev in map)) { h[ev] = null; } }
        for (ev in map) {
            if (!(ev in h)) { (function (type) { el.addEventListener(type, function (e) { var f = el._h[type]; if (f) { f.call(el, e); } }); }(ev)); }
            h[ev] = map[ev];
        }
    }

    function create(v) {
        var el = v.t === '#text' ? document.createTextNode(v.s) : document.createElement(v.t);
        v._d = el;
        if (v.t !== '#text') { setProps(el, {}, v.p); patchKids(el, [], v.c); if (v.p.ref) { v.p.ref(el); } }
        return el;
    }
    function update(o, n) {
        var el = n._d = o._d;
        if (n.t === '#text') { if (o.s !== n.s) { el.nodeValue = n.s; } return; }
        setProps(el, o.p, n.p); patchKids(el, o.c, n.c); if (n.p.ref) { n.p.ref(el); }
    }
    function same(o, n) { return o.t === n.t && o.k === n.k; }

    /** Keyed children: a new child takes the old one with the same key (or, unkeyed, the same position and tag). */
    function patchKids(parent, oldC, newC) {
        var byKey = {}, used = [], i, j, dom = [];
        for (i = 0; i < oldC.length; i++) { if (oldC[i].k !== null) { byKey[oldC[i].k] = oldC[i]; } }
        for (i = 0; i < newC.length; i++) {
            var n = newC[i], o = null;
            if (n.k !== null) { o = byKey[n.k] || null; }
            else if (oldC[i] && oldC[i].k === null) { o = oldC[i]; }
            if (o && o._used) { o = null; }
            if (o && same(o, n)) { o._used = true; used.push(o); update(o, n); }
            else { create(n); }
            dom.push(n._d);
        }
        for (j = 0; j < oldC.length; j++) { if (!oldC[j]._used && oldC[j]._d.parentNode === parent) { parent.removeChild(oldC[j]._d); } }
        for (j = 0; j < used.length; j++) { used[j]._used = false; }
        for (i = 0; i < dom.length; i++) { if (parent.childNodes[i] !== dom[i]) { parent.insertBefore(dom[i], parent.childNodes[i] || null); } }
    }

    /** mount( container, view ) returns { update, destroy }. Rendering is batched: many update() calls, one render. */
    Exp.mount = function (container, view) {
        container.textContent = ''; // the container's server-rendered placeholder ("Loading") is replaced, not kept
        var prev = [], queued = false, api = {
            render: function () { queued = false; var next = norm(view(), []); patchKids(container, prev, next); prev = next; Exp.mount.renders++; },
            update: function () { if (!queued) { queued = true; Promise.resolve().then(api.render); } },
            destroy: function () { patchKids(container, prev, []); prev = []; }
        };
        api.render();
        return api;
    };
    Exp.mount.renders = 0;
}(window, jQuery));
