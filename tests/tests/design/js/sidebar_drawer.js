// Runs sidebarToggle() of design/admin4/javascript/ezadmin_menubar.js against a small stand-in for the browser
// (matchMedia, jQuery's few calls, the keydown listener) and prints one JSON line per scenario.
// Usage: node sidebar_drawer.js <path to ezadmin_menubar.js>
'use strict';
const fs = require('fs');
const vm = require('vm');
const source = fs.readFileSync(process.argv[2], 'utf8');
const start = source.indexOf('function sidebarToggle()');
const end = source.indexOf('// Navbar menu toggling feature');

function run(width, prefsIn, steps) {
    const queries = [];
    const state = { left: { show: false, expanded: null }, right: { show: false, expanded: null } };
    const handlers = { left: null, right: null };
    const saved = [];
    let keydown = null;
    let focused = null;
    const controls = { left: { side: 'left', focus() { focused = 'left'; } }, right: { side: 'right', focus() { focused = 'right'; } } };
    function side(sel) { return sel.indexOf('left') >= 0 ? 'left' : 'right'; }
    function $(sel) {
        const s = side(sel);
        const api = {
            toggleClass(cls, on) { if (sel.indexOf('.sidebar.') === 0 && cls === 'show') state[s].show = on; return api; },
            attr(name, value) { if (name === 'aria-expanded') state[s].expanded = value; return api; },
            removeAttr() { return api; },
            on(evt, fn) { handlers[s] = fn; return api; },
        };
        return api;
    }
    const sandbox = {
        $,
        window: {
            matchMedia(q) {
                const max = parseFloat(q.match(/max-width: ([\d.]+)px/)[1]);
                const mq = { query: q, max, matches: width <= max, listeners: [], addEventListener(t, f) { this.listeners.push(f); } };
                queries.push(mq);
                return mq;
            },
            a4Pref: { get: (n) => (n in prefsIn ? prefsIn[n] : null), set: (n, v) => saved.push(n + '=' + v) },
            localStorage: { removeItem() {} },
            getComputedStyle: () => ({ display: 'none', visibility: 'visible' }),
        },
        document: {
            getElementById: () => ({}),
            querySelector: (sel) => (sel.indexOf('.sidebar-control.') === 0 ? controls[side(sel)] : { contains: () => false }),
            querySelectorAll: () => [],
            addEventListener: (t, f) => { if (t === 'keydown') keydown = f; },
            activeElement: null,
        },
    };
    vm.createContext(sandbox);
    vm.runInContext(source.slice(start, end) + '\nsidebarToggle();', sandbox);
    const out = [];
    const snap = (label) => out.push(label + ':' + (state.left.show ? 'L' : '-') + (state.right.show ? 'R' : '-'));
    snap('start');
    for (const step of steps) {
        if (step === 'escape') {
            sandbox.document.activeElement = state.left.show && queries[0].matches ? controls.left : controls.right;
            keydown({ key: 'Escape', defaultPrevented: false });
            snap('escape');
        } else if (step.indexOf('resize:') === 0) {
            width = parseInt(step.slice(7), 10);
            queries.forEach((q) => { q.matches = width <= q.max; q.listeners.forEach((f) => f()); });
            snap(step);
        } else {
            handlers[step]();
            snap('click-' + step);
        }
    }
    return { width, states: out, saved, focused, breakpoints: queries.map((q) => q.max) };
}

const scenarios = {
    wide1440: run(1440, {}, ['right', 'right']),
    mid1180: run(1180, {}, ['right', 'escape', 'left', 'right', 'left']),
    narrow800: run(800, {}, ['left', 'right', 'escape']),
    midStoredClosed: run(1180, { admin_left_sidebar: '0' }, ['left']),
    resize: run(1440, {}, ['resize:1180', 'right', 'resize:1440']),
};
process.stdout.write(JSON.stringify(scenarios));
