// Runs window.a4Pref and the color mode script of design/admin4/templates/page_head_style.tpl, and two parts of
// design/admin4/javascript/admin4.js (the left menu's groups, the sign-in page), against a small stand-in for the
// browser: <html> with its attributes and style, localStorage, fetch (answers as a scenario says), the toggle event
// of <details>, which a browser queues as a task (here: after the current script, like setImmediate). Prints one JSON
// object, one entry per scenario.
// Usage: node preferences.js <path to admin4.js> <path to page_head_style.tpl>
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
const theme = slice(head, '(function () {\n    var KEY = ', '\n/* admin4 has one density');
const groupsPart = slice(js, '/*\n * admin4: the groups of a long left menu', '\n/*\n * admin4: the height of the node view');
const loginPart = slice(js, '/*\n * admin4 sign-in page', '\n/*\n * Reduced motion');
const tick = () => new Promise((r) => setImmediate(r));

function events() {
    const list = {};
    return {
        addEventListener(type, fn) { (list[type] = list[type] || []).push(fn); },
        fire(type, event) { (list[type] || []).forEach((fn) => fn(event || {})); },
    };
}

function setup(opt) {
    opt = opt || {};
    const attrs = Object.assign({}, opt.attrs || {});
    const vars = {};
    const store = Object.assign({}, opt.local || {});
    const fetches = [];
    const root = {
        getAttribute: (n) => (n in attrs ? attrs[n] : null),
        setAttribute: (n, v) => { attrs[n] = String(v); },
        removeAttribute: (n) => { delete attrs[n]; },
        hasAttribute: (n) => n in attrs,
        style: { setProperty: (n, v) => { vars[n] = v; } },
    };
    const win = events();
    const doc = events();
    const byId = {};
    const window = {
        addEventListener: win.addEventListener,
        fire: win.fire,
        matchMedia: () => ({ matches: false, addEventListener() {} }),
        localStorage: {
            getItem: (k) => (k in store ? store[k] : null),
            setItem: (k, v) => { store[k] = String(v); },
            removeItem: (k) => { delete store[k]; },
        },
        fetch(url) {
            fetches.push(decodeURIComponent(url.split('/').slice(-2).join('=')));
            if (opt.fetch === 'error') return Promise.resolve({ ok: false, redirected: false });
            return Promise.resolve({ ok: true, redirected: false });
        },
    };
    const document = {
        readyState: 'complete',
        documentElement: root,
        addEventListener: doc.addEventListener,
        fire: doc.fire,
        getElementById: (id) => byId[id] || null,
        querySelector: (sel) => (opt.querySelector ? opt.querySelector(sel) : null),
        querySelectorAll: (sel) => (opt.querySelectorAll ? opt.querySelectorAll(sel) : []),
    };
    window.document = document;
    const sandbox = { window, document, Promise, String, encodeURIComponent };
    vm.createContext(sandbox);
    vm.runInContext(pref, sandbox);
    return { sandbox, window, document, byId, attrs, vars, store, fetches, run: (code) => vm.runInContext(code, sandbox) };
}
const SIGNED = { 'data-a4-pref-url': '/user/preferences/set_and_exit' };

// a <details> group of the left menu: setting open queues its toggle event, as a browser does
function group(key, open, current) {
    const ev = events();
    let isOpen = open;
    const g = {
        attrs: { 'data-a4-navgroup': key },
        getAttribute(n) { return n in this.attrs ? this.attrs[n] : null; },
        hasAttribute(n) { return n === 'data-a4-current' ? !!current : n in this.attrs; },
        addEventListener: ev.addEventListener,
        get open() { return isOpen; },
        set open(v) { if (v !== isOpen) { isOpen = v; setImmediate(() => ev.fire('toggle')); } },
    };
    return g;
}

(async () => {
    const out = {};
    let b;

    // window.a4Pref.set: a value already on <html> is not sent; a new one once
    b = setup({ attrs: Object.assign({ 'data-a4-pref-admin-x': '1' }, SIGNED) });
    b.run("window.a4Pref.set('admin_x', '1'); window.a4Pref.set('admin_x', '2'); window.a4Pref.set('admin_x', '2');");
    await tick();
    out['unchanged is not sent'] = { fetches: b.fetches };

    // share: a saved value is written to the browser's copy too (other tabs hear the storage event); without share not
    b = setup({ attrs: SIGNED });
    b.run("window.a4Pref.set('admin_x', 'a', 'key-x', true); window.a4Pref.set('admin_y', 'b', 'key-y');");
    await tick(); await tick();
    out['shared when saved'] = { fetches: b.fetches, store: b.store };

    // the color mode: a press saves it and writes the key the other tabs listen to; another tab follows
    b = setup({ attrs: Object.assign({ 'data-a4-pref-admin-color-mode': 'light' }, SIGNED) });
    const toggle = Object.assign(events(), { attrs: { 'data-label-system': 'System' }, hasAttribute(n) { return n in this.attrs; }, getAttribute(n) { return n in this.attrs ? this.attrs[n] : null; }, setAttribute() {}, querySelector: () => null });
    b.byId['a4-theme-toggle'] = toggle;
    b.run(theme);
    b.document.fire('DOMContentLoaded');
    toggle.fire('click');
    await tick(); await tick();
    const other = setup({ attrs: Object.assign({ 'data-a4-pref-admin-color-mode': 'light' }, SIGNED) });
    other.run(theme);
    other.window.fire('storage', { key: 'exp-admin4-theme', newValue: b.store['exp-admin4-theme'] });
    out['color mode reaches the other tabs'] = { fetches: b.fetches, store: b.store, otherTheme: other.attrs['data-a4-theme'] };

    // the left column's width that the server did not get: from the browser's copy, unless a preset is chosen
    b = setup({ attrs: SIGNED, local: { 'exp-a4-admin_left_menu_size': '300px' } });
    b.run(theme);
    out['left width from the browser'] = b.vars['--left-sidebar-width'] || null;
    b = setup({ attrs: Object.assign({ 'data-a4-left-size': 'small' }, SIGNED), local: { 'exp-a4-admin_left_menu_size': '300px' } });
    b.run(theme);
    out['left width: a preset wins'] = b.vars['--left-sidebar-width'] || null;
    b = setup({ attrs: Object.assign({ 'data-a4-left-width': '280px' }, SIGNED), local: { 'exp-a4-admin_left_menu_size': '300px' } });
    b.run(theme);
    out['left width: the preference wins'] = b.vars['--left-sidebar-width'] || null;

    // the left menu's groups: two stored closed are closed on load without a save; a change by the user saves once
    const groups = [group('a', true), group('b', true), group('c', true)];
    b = setup({ attrs: Object.assign({ 'data-a4-pref-admin-navgroups': 'a,b' }, SIGNED), querySelectorAll: () => groups });
    b.run(groupsPart);
    await tick(); await tick();
    const onLoad = b.fetches.slice();
    const closedOnLoad = groups.map((g) => g.open);
    groups[2].open = false;
    await tick(); await tick();
    const afterClose = b.fetches.slice();
    groups[0].open = true;
    await tick(); await tick();
    out['groups'] = { onLoad, closedOnLoad, afterClose, afterOpen: b.fetches };
    // a group holding the current page opens whatever was stored; it is not saved for that either
    const current = [group('a', true, true)];
    b = setup({ attrs: Object.assign({ 'data-a4-pref-admin-navgroups': 'a' }, SIGNED), querySelectorAll: () => current });
    b.run(groupsPart);
    await tick(); await tick();
    out['current group'] = { open: current[0].open, fetches: b.fetches };

    // the sign-in button: busy while it is sent, its own label again after the back/forward cache
    const form = events();
    const button = { textContent: 'Sign in', attrs: { 'data-label-busy': 'Signing in…' }, classList: { add() {}, remove() {} },
                     getAttribute(n) { return n in this.attrs ? this.attrs[n] : null; }, setAttribute(n, v) { this.attrs[n] = v; }, removeAttribute(n) { delete this.attrs[n]; } };
    form.querySelector = (sel) => (sel === '.a4-login-submit' ? button : null);
    b = setup({ querySelector: (sel) => (sel === 'form.a4-login-form' ? form : null) });
    b.run(loginPart);
    form.fire('submit', { preventDefault() {} });
    const busy = button.textContent;
    b.window.fire('pageshow', { persisted: true });
    out['sign-in label'] = { busy, back: button.textContent, ariaBusy: button.attrs['aria-busy'] || null };

    process.stdout.write(JSON.stringify(out));
})();
