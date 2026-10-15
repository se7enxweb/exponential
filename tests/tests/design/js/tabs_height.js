// Runs the height control of the node view's tab box (design/admin4/javascript/admin4.js) together with window.a4Pref
// (design/admin4/templates/page_head_style.tpl) against a small stand-in for the browser: <html> with its attributes
// and style, the box whose drawn height follows admin4.css (the default 20em of 16px, held between 10em and 60em and at
// most 85% of the window), the grip and the reset button, localStorage, fetch, a clock for the timers. Prints one JSON
// line per scenario.
// Usage: node tabs_height.js <path to admin4.js> <path to page_head_style.tpl>
'use strict';
const fs = require('fs');
const vm = require('vm');

const js = fs.readFileSync(process.argv[2], 'utf8');
const head = fs.readFileSync(process.argv[3], 'utf8');
function slice(text, from, to) {
    const a = text.indexOf(from);
    const b = text.indexOf(to, a + from.length);
    if (a < 0 || b < 0) throw new Error('not found: ' + from);
    return text.slice(a, b);
}
const pref = slice(head, '(function () {\n    var root = document.documentElement, url', '\n(function () {\n    var KEY = ');
const block = slice(js, "/*\n * admin4: the height of the node view's tab box", "\n/*\n * The content of the rich text editors");

function events() {
    const list = {};
    return {
        addEventListener(type, fn) { (list[type] = list[type] || []).push(fn); },
        fire(type, event) { (list[type] || []).forEach((fn) => fn(event)); },
    };
}
function classes() {
    const set = new Set();
    return { add: (c) => set.add(c), remove: (c) => set.delete(c), contains: (c) => set.has(c) };
}

function setup(opt) {
    const attrs = Object.assign({}, opt.attrs || {});
    const vars = {};
    const store = Object.assign({}, opt.local || {});
    const fetches = [];
    const timers = [];
    let now = 0;
    let focused = null;
    const root = {
        getAttribute: (n) => (n in attrs ? attrs[n] : null),
        setAttribute: (n, v) => { attrs[n] = String(v); },
        removeAttribute: (n) => { delete attrs[n]; },
        hasAttribute: (n) => n in attrs,
        style: {
            setProperty: (n, v) => { vars[n] = v; },
            removeProperty: (n) => { delete vars[n]; },
            getPropertyValue: (n) => vars[n] || '',
        },
    };
    const innerHeight = opt.innerHeight || 1000;
    // the drawn height, as admin4.css draws it from 576px (pagelayout.css: 20em, 22em with the layouts tab)
    function drawn() {
        if (!('data-a4-node-tabs-height' in attrs) || !vars['--a4-node-tabs-height']) return box.classList.contains('layouts-active') ? 352 : 320;
        const v = parseFloat(vars['--a4-node-tabs-height']);
        return Math.max(160, Math.min(v, Math.min(960, innerHeight * 0.85)));
    }
    function element(name, extra) {
        const e = events();
        const el = Object.assign({
            name, hidden: true, attrs: {}, classList: classes(),
            setAttribute(n, v) { this.attrs[n] = String(v); },
            getAttribute(n) { return n in this.attrs ? this.attrs[n] : null; },
            addEventListener: e.addEventListener,
            fire: e.fire,
            focus() { focused = name; },
        }, extra || {});
        return el;
    }
    const grip = element('grip', { setPointerCapture() {} });
    let observer = null;
    const reset = element('reset');
    const bar = element('bar', { querySelector: (s) => (s === '.a4-tabs-height-grip' ? grip : s === '.a4-tabs-height-reset' ? reset : null) });
    const box = element('box', {
        querySelector: (s) => (s === '.a4-tabs-height' ? bar : null),
        layouts(on) { if (on) this.classList.add('layouts-active'); else this.classList.remove('layouts-active'); if (observer) observer(); },
        getBoundingClientRect: () => ({ height: drawn() }),
    });
    const win = events();
    const window = {
        innerHeight,
        addEventListener: win.addEventListener,
        fire: win.fire,
        MutationObserver: function (fn) { this.observe = () => { observer = fn; }; },
        getComputedStyle: () => ({ fontSize: '16px' }),
        setTimeout(fn, ms) { const t = { fn, at: now + ms, id: timers.length + 1 }; timers.push(t); return t.id; },
        clearTimeout(id) { const t = timers.find((x) => x.id === id); if (t) t.fn = null; },
        localStorage: {
            getItem: (k) => { if (opt.blocked) throw new Error('blocked'); return k in store ? store[k] : null; },
            setItem: (k, v) => { if (opt.blocked) throw new Error('blocked'); store[k] = String(v); },
            removeItem: (k) => { if (opt.blocked) throw new Error('blocked'); delete store[k]; },
        },
        fetch(url, init) {
            fetches.push({ url, method: init.method });
            if (opt.fetch === 'reject') return Promise.reject(new Error('offline'));
            return Promise.resolve({ ok: opt.fetch !== 'error', redirected: opt.fetch === 'redirect' });
        },
    };
    const document = {
        readyState: 'complete',
        documentElement: root,
        body: { classList: classes() },
        getElementById: (id) => (id === 'window-controls' ? box : null),
        addEventListener() {},
    };
    window.document = document;
    const sandbox = { window, document, Promise, parseInt, parseFloat, Math, String, encodeURIComponent };
    vm.createContext(sandbox);
    vm.runInContext(pref, sandbox);
    vm.runInContext(block, sandbox);
    return {
        grip, reset, bar, box, window, fetches, store, attrs, vars,
        focused: () => focused,
        height: () => drawn(),
        advance(ms) {
            now += ms;
            timers.filter((t) => t.fn && t.at <= now).forEach((t) => { const fn = t.fn; t.fn = null; fn(); });
        },
        key(k, shift) { grip.fire('keydown', { key: k, shiftKey: !!shift, preventDefault() {} }); },
        drag(from, to, steps) {
            grip.fire('pointerdown', { pointerId: 1, button: 0, clientY: from, preventDefault() {} });
            for (let i = 1; i <= steps; i++) grip.fire('pointermove', { pointerId: 1, clientY: from + (to - from) * i / steps });
            grip.fire('pointerup', { pointerId: 1, clientY: to });
        },
    };
}

const SIGNED = 'data-a4-pref-url';
const ATTR = 'data-a4-pref-admin-node-preview-height';
const KEY = 'exp-a4-node-preview-height';
const signed = (value) => ({ [SIGNED]: '/user/preferences/set_and_exit', [ATTR]: value });
const saved = (b) => b.fetches.map((f) => decodeURIComponent(f.url.split('/').pop()));

function state(name, b, extra) {
    return Object.assign({
        name,
        height: b.height(),
        applied: b.attrs['data-a4-node-tabs-height'] || null,
        saved: saved(b),
        local: b.store[KEY] === undefined ? null : b.store[KEY],
        resetHidden: b.reset.hidden,
        barHidden: b.bar.hidden,
        sizable: b.box.classList.contains('a4-tabs-sizable'),
        valuenow: b.grip.getAttribute('aria-valuenow'),
        valuemin: b.grip.getAttribute('aria-valuemin'),
        valuemax: b.grip.getAttribute('aria-valuemax'),
    }, extra || {});
}

const tick = () => new Promise((r) => setImmediate(r));

(async () => {
    const out = [];
    let b;

    // which height a page starts with: the preference, then the browser's copy, then the default
    b = setup({ attrs: signed('500'), local: { [KEY]: '700' } }); out.push(state('preference wins', b));
    b = setup({ attrs: signed(''), local: { [KEY]: '700' } }); await tick(); out.push(state('browser copy without a preference', b));
    b = setup({ attrs: signed('') }); out.push(state('default', b));
    b = setup({ attrs: signed('-'), local: { [KEY]: '700' } }); out.push(state('reset preference is the default', b));
    b = setup({ local: { [KEY]: '640' } }); out.push(state('anonymous browser copy', b));
    b = setup({ attrs: signed('abc') }); out.push(state('a value that is no height is ignored', b));
    b = setup({ attrs: signed(''), blocked: true }); out.push(state('blocked storage', b));

    // a drag saves once, at its end
    b = setup({ attrs: signed('') });
    b.drag(100, 300, 25); await tick(); out.push(state('drag saves once at the end', b));
    b = setup({ attrs: signed('') });
    b.drag(100, 100, 0); await tick(); out.push(state('a click on the grip saves nothing', b));
    b = setup({ attrs: signed('') });
    b.drag(100, 5000, 5); await tick(); out.push(state('drag held at the largest height', b));
    b = setup({ attrs: signed('') });
    b.drag(400, 0, 5); await tick(); out.push(state('drag held at the smallest height', b));

    // keys: one save 400ms after the last one
    b = setup({ attrs: signed('') });
    for (let i = 0; i < 5; i++) { b.key('ArrowDown'); b.advance(100); }
    const early = saved(b).length;
    b.advance(299); const before = saved(b).length;
    b.advance(1); await tick();
    out.push(state('keys save once after a rest', b, { early, before }));
    b = setup({ attrs: signed('') });
    b.key('PageDown'); b.key('ArrowDown', true); b.key('ArrowUp'); b.advance(400); await tick();
    out.push(state('page and shift steps', b));
    b = setup({ attrs: signed(''), innerHeight: 900 });
    b.key('End'); b.advance(400); const end = b.height(); b.key('Home'); b.advance(400); await tick();
    out.push(state('home and end', b, { end }));
    b = setup({ attrs: signed('400') });
    for (let i = 0; i < 5; i++) b.key('ArrowUp');
    b.advance(400); await tick(); out.push(state('back at the default forgets the height', b));
    b = setup({ attrs: signed('') });
    b.key('ArrowDown'); b.advance(400); b.key('ArrowDown'); b.key('ArrowUp'); b.advance(400); await tick();
    out.push(state('an unchanged height is not saved again', b));
    b = setup({ attrs: signed('') });
    b.key('Tab'); b.advance(400); await tick(); out.push(state('other keys do nothing', b));
    b = setup({ attrs: signed('') });
    b.key('ArrowDown'); b.window.fire('pagehide', {}); await tick(); out.push(state('a waiting change is saved when the page is left', b));

    // the layouts tab draws 22em by default: announced as drawn, and the steps start there
    b = setup({ attrs: signed('') });
    b.box.layouts(true); const layoutsNow = b.grip.getAttribute('aria-valuenow');
    b.key('ArrowDown', true); b.key('ArrowDown'); b.key('ArrowDown'); b.advance(400); b.box.layouts(false); await tick();
    out.push(state('layouts tab', b, { layoutsNow }));
    // a drag whose pointer capture is lost ends there; a second pointer does not move the box
    b = setup({ attrs: signed('') });
    b.grip.fire('pointerdown', { pointerId: 1, button: 0, clientY: 100, preventDefault() {} });
    b.grip.fire('pointermove', { pointerId: 1, clientY: 150 });
    b.grip.fire('pointermove', { pointerId: 2, clientY: 400 });
    b.grip.fire('lostpointercapture', { pointerId: 1 }); await tick();
    out.push(state('a lost drag saves where it ended, another pointer is ignored', b));

    // reset: the default, the preference empty, the browser's copy gone, the focus on the grip
    b = setup({ attrs: signed('500'), local: { [KEY]: '500' } });
    const before2 = { resetHidden: b.reset.hidden };
    b.reset.fire('click', {}); await tick();
    out.push(state('reset forgets the height', b, { resetWasHidden: before2.resetHidden, focus: b.focused(), pref: b.attrs[ATTR] }));
    b = setup({ local: { [KEY]: '500' } });
    b.reset.fire('click', {}); await tick(); out.push(state('reset on an anonymous page', b));

    // fallback: a preference that is not saved stays in this browser
    for (const how of ['error', 'reject', 'redirect']) {
        b = setup({ attrs: signed(''), fetch: how });
        b.drag(100, 260, 4); await tick(); await tick(); out.push(state('not saved (' + how + ') keeps it in the browser', b));
    }
    b = setup({ attrs: signed('') });
    b.drag(100, 260, 4); await tick(); out.push(state('saved: nothing in the browser', b));
    b = setup({});
    b.drag(100, 260, 4); await tick(); out.push(state('anonymous: the browser only', b));

    out.forEach((row) => console.log(JSON.stringify(row)));
})();
