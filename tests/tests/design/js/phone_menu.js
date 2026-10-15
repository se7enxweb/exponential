// Runs navbarToggle() of design/admin4/javascript/ezadmin_menubar.js against a small stand-in for the browser (jQuery's
// few calls, the document's keydown listeners, the menu, its button, a link and the search scope popup in the menu, a
// link outside it) and prints one JSON line per scenario.
// Usage: node phone_menu.js <path to ezadmin_menubar.js>
'use strict';
const fs = require('fs');
const vm = require('vm');
const source = fs.readFileSync(process.argv[2], 'utf8');
const start = source.indexOf('function navbarToggle()');
const end = source.indexOf('/**\n     * Left sidebar menu resize functionality');

function run(steps) {
    const classes = { menu: new Set(), body: new Set() };
    const listeners = [];
    const log = [];
    let sandbox = null;
    function element(name, extra) {
        return Object.assign({
            name,
            attrs: {},
            focus() { sandbox.document.activeElement = this; log.push('focus ' + name); },
            setAttribute(k, v) { this.attrs[k] = String(v); },
            getAttribute(k) { return k in this.attrs ? this.attrs[k] : null; },
            closest() { return null; },
        }, extra || {});
    }
    const pane = { hidden: true };
    const icon = element('icon');
    const inside = element('menu link');
    const outside = element('page link');
    const menu = element('menu', {
        classList: { contains: (c) => classes.menu.has(c) },
        contains: (el) => el === inside,
        querySelector: (sel) => (sel === '.searchscope-pane:not([hidden])' && !pane.hidden ? pane : null),
    });
    const iconHandlers = {};
    function $(target) {
        const name = typeof target === 'string' ? target : 'element';
        return {
            on(type, fn) { if (name === '.navbar-icon') { iconHandlers[type] = fn; } return this; },
            toggleClass(c, on) {
                const set = name === '.navbar-menu' ? classes.menu : classes.body;
                const want = on === undefined ? !set.has(c) : !!on;
                if (want) { set.add(c); } else { set.delete(c); }
                return this;
            },
            hasClass(c) { return (name === '.navbar-menu' ? classes.menu : classes.body).has(c); },
        };
    }
    const body = element('body');
    sandbox = {
        $,
        window: { getComputedStyle: () => ({ display: 'none', visibility: 'visible' }) },
        document: {
            body,
            activeElement: body,
            addEventListener(type, fn, capture) { listeners.push({ type, fn, capture: !!capture }); },
            querySelector(sel) {
                if (sel === '.navbar-icon') { return icon; }
                if (sel === '.navbar-menu') { return menu; }
                return null;
            },
            querySelectorAll() { return []; },
        },
    };
    vm.createContext(sandbox);
    vm.runInContext(source.slice(start, end) + '\nnavbarToggle();', sandbox);
    function key(k, prevented) {
        const e = { key: k, defaultPrevented: !!prevented, preventDefault() { this.defaultPrevented = true; } };
        listeners.filter((l) => l.type === 'keydown').forEach((l) => l.fn(e));
    }
    steps.forEach((step) => {
        if (step === 'click') { iconHandlers.click.call(icon); }
        else if (step === 'enter') { iconHandlers.keydown.call(icon, { key: 'Enter', preventDefault() {} }); }
        else if (step === 'escape') { key('Escape'); }
        else if (step === 'escape prevented') { key('Escape', true); }
        else if (step === 'focus inside') { sandbox.document.activeElement = inside; }
        else if (step === 'focus outside') { sandbox.document.activeElement = outside; }
        else if (step === 'open scope') { pane.hidden = false; }
        else if (step === 'close scope') { pane.hidden = true; }
    });
    return {
        open: classes.menu.has('show'),
        bodyOpen: classes.body.has('navbar-open'),
        expanded: icon.getAttribute('aria-expanded'),
        focus: sandbox.document.activeElement.name,
        captured: listeners.some((l) => l.type === 'keydown' && l.capture),
        log,
    };
}

const scenarios = {
    'click opens': ['click'],
    'escape on the button closes': ['click', 'escape'],
    'escape in the menu closes and focuses the button': ['enter', 'focus inside', 'escape'],
    'escape with the scope popup open keeps the menu': ['click', 'focus inside', 'open scope', 'escape'],
    'escape after the popup closed closes': ['click', 'focus inside', 'open scope', 'escape', 'close scope', 'escape'],
    'escape with the focus on the page keeps the menu': ['click', 'focus outside', 'escape'],
    'escape taken by another script keeps the menu': ['click', 'escape prevented'],
    'escape while closed does nothing': ['escape'],
};
Object.keys(scenarios).forEach((name) => {
    process.stdout.write(JSON.stringify(Object.assign({ name }, run(scenarios[name]))) + '\n');
});
