// Runs sidebarToggle() of design/admin4/javascript/ezadmin_menubar.js against a small stand-in for the browser
// (matchMedia, jQuery's few calls, the keydown listeners, two focusable links in each column) and prints one JSON
// line per scenario.
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
    const keydowns = [];
    const focusLog = [];
    let focused = null;
    let sandbox = null;
    function element(name) {
        return {
            name,
            focus() { focused = name; focusLog.push(name); sandbox.document.activeElement = this; },
            getClientRects() { return [1]; },
        };
    }
    const controls = { left: element('left'), right: element('right') };
    const links = { left: [element('left1'), element('left2')], right: [element('right1'), element('right2')] };
    const columns = {};
    ['left', 'right'].forEach((s) => {
        columns[s] = {
            contains: (el) => links[s].indexOf(el) >= 0,
            // a closed column is hidden: nothing in it can take the focus
            querySelectorAll: () => (state[s].show ? links[s] : []),
        };
    });
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
    sandbox = {
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
            querySelector: (sel) => (sel.indexOf('.sidebar-control.') === 0 ? controls[side(sel)] : columns[side(sel)]),
            querySelectorAll: () => [],
            addEventListener: (t, f) => { if (t === 'keydown') keydowns.push(f); },
            activeElement: null,
        },
    };
    vm.createContext(sandbox);
    vm.runInContext(source.slice(start, end) + '\nsidebarToggle();', sandbox);
    const out = [];
    const snap = (label) => out.push(label + ':' + (state.left.show ? 'L' : '-') + (state.right.show ? 'R' : '-'));
    const key = (event) => {
        event.defaultPrevented = false;
        event.preventDefault = () => { event.defaultPrevented = true; };
        keydowns.forEach((f) => f(event));
        return event;
    };
    snap('start');
    for (const step of steps) {
        if (step === 'escape') {
            const active = sandbox.document.activeElement;
            const inOpen = active && ((state.left.show && (active === controls.left || columns.left.contains(active)))
                || (state.right.show && (active === controls.right || columns.right.contains(active))));
            if (!inOpen) {
                sandbox.document.activeElement = state.left.show && queries[0].matches ? controls.left : controls.right;
            }
            key({ key: 'Escape' });
            snap('escape');
        } else if (step === 'tab' || step === 'shift-tab') {
            const e = key({ key: 'Tab', shiftKey: step === 'shift-tab' });
            snap(step + '>' + (e.defaultPrevented ? focused : 'page'));
        } else if (step.indexOf('focus:') === 0) {
            const name = step.slice(6);
            const all = [controls.left, controls.right].concat(links.left, links.right);
            all.filter((el) => el.name === name)[0].focus();
            snap(step);
        } else if (step.indexOf('resize:') === 0) {
            width = parseInt(step.slice(7), 10);
            queries.forEach((q) => { q.matches = width <= q.max; q.listeners.forEach((f) => f()); });
            snap(step);
        } else {
            handlers[step]();
            snap('click-' + step);
        }
    }
    return { width, states: out, saved, focused, focusLog, breakpoints: queries.map((q) => q.max) };
}

const scenarios = {
    wide1440: run(1440, {}, ['right', 'right']),
    mid1180: run(1180, {}, ['right', 'escape', 'left', 'right', 'left']),
    narrow800: run(800, {}, ['left', 'right', 'escape']),
    midStoredClosed: run(1180, { admin_left_sidebar: '0' }, ['left']),
    resize: run(1440, {}, ['resize:1180', 'right', 'resize:1440']),
    // the right drawer saved open: open again on a tablet-sized window (and at 960px, the 200% zoom), not on a phone
    // and not on a wide window
    drawerSaved1180: run(1180, { admin_right_drawer: '1' }, []),
    drawerSaved960: run(960, { admin_right_drawer: '1' }, ['right']),
    drawerSavedPhone: run(390, { admin_right_drawer: '1' }, ['right', 'right']),
    drawerSaved1440: run(1440, { admin_right_drawer: '1' }, []),
    // the focus trap: opening moves the focus into the drawer, Tab cycles drawer -> tab -> drawer, Shift+Tab back
    trap: run(960, {}, ['right', 'tab', 'tab', 'shift-tab', 'shift-tab', 'escape']),
    // focus outside the drawer (a click on the page) is left alone
    trapOutside: run(960, {}, ['right', 'focus:left', 'tab']),
};
process.stdout.write(JSON.stringify(scenarios));
