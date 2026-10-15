// Runs navbarToggle() of design/admin4/javascript/ezadmin_menubar.js against a small stand-in for the browser (jQuery's
// few calls, the document's keydown and pointerdown listeners, the phone width query, the header, the menu, its button, a
// link, the close button and the search scope popup in the menu, a link outside it) and prints one JSON line per
// scenario.
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
    let prevented = null;
    function element(name, extra) {
        return Object.assign({
            name,
            attrs: {},
            focus() { sandbox.document.activeElement = this; log.push('focus ' + name); },
            setAttribute(k, v) { this.attrs[k] = String(v); },
            getAttribute(k) { return k in this.attrs ? this.attrs[k] : null; },
            closest() { return null; },
            getClientRects() { return [{}]; },
        }, extra || {});
    }
    const pane = { hidden: true };
    const icon = element('icon');
    const inside = element('menu link');
    const close = element('close button');
    const outside = element('page link');
    const logo = element('logo');
    const site = element('site link');
    const menu = element('menu', {
        classList: { contains: (c) => classes.menu.has(c) },
        contains: (el) => el === inside || el === close,
        querySelector: (sel) => (sel === '.searchscope-pane:not([hidden])' && !pane.hidden ? pane : null),
    });
    // the header's controls in their order: the logo, the menu button, the panel's link and close button, a tool of the row
    const header = element('header', {
        contains: (el) => [logo, icon, menu, site].includes(el) || menu.contains(el),
        querySelectorAll: () => [logo, icon, inside, close, site],
    });
    const handlers = { icon: {}, close: {} };
    const phone = { matches: true, change: [], addEventListener(type, fn) { if (type === 'change') { this.change.push(fn); } } };
    function $(target) {
        const name = typeof target === 'string' ? target : 'element';
        return {
            on(type, fn) {
                if (name === '.navbar-icon') { handlers.icon[type] = fn; }
                if (name === '.a4-phone-close-button') { handlers.close[type] = fn; }
                return this;
            },
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
        window: {
            getComputedStyle: () => ({ display: 'none', visibility: 'visible' }),
            matchMedia: (q) => { if (q !== '(max-width: 575.98px)') { throw new Error('unexpected query ' + q); } return phone; },
        },
        document: {
            body,
            activeElement: body,
            addEventListener(type, fn, capture) { listeners.push({ type, fn, capture: !!capture }); },
            getElementById: (id) => (id === 'header' ? header : null),
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
    function key(k, extra) {
        const e = Object.assign({ key: k, defaultPrevented: false, shiftKey: false, preventDefault() { this.defaultPrevented = true; } }, extra || {});
        listeners.filter((l) => l.type === 'keydown').forEach((l) => l.fn(e));
        prevented = e.defaultPrevented;
    }
    function tap(target) {
        listeners.filter((l) => l.type === 'pointerdown').forEach((l) => l.fn({ target }));
    }
    steps.forEach((step) => {
        if (step === 'click') { handlers.icon.click.call(icon); tap(icon); }
        else if (step === 'enter') { handlers.icon.keydown.call(icon, { key: 'Enter', preventDefault() {} }); }
        else if (step === 'escape') { key('Escape'); }
        else if (step === 'escape prevented') { key('Escape', { defaultPrevented: true }); }
        else if (step === 'tab') { key('Tab'); }
        else if (step === 'shift tab') { key('Tab', { shiftKey: true }); }
        else if (step === 'focus icon') { sandbox.document.activeElement = icon; }
        else if (step === 'focus logo') { sandbox.document.activeElement = logo; }
        else if (step === 'focus site') { sandbox.document.activeElement = site; }
        else if (step === 'focus inside') { sandbox.document.activeElement = inside; }
        else if (step === 'focus close') { sandbox.document.activeElement = close; }
        else if (step === 'focus outside') { sandbox.document.activeElement = outside; }
        else if (step === 'open scope') { pane.hidden = false; }
        else if (step === 'close scope') { pane.hidden = true; }
        else if (step === 'press close') { handlers.close.click.call(close); tap(close); }
        else if (step === 'tap inside') { tap(inside); }
        else if (step === 'tap outside') { tap(outside); }
        else if (step === 'wide') { phone.matches = false; phone.change.forEach((fn) => fn({ matches: false })); }
        else if (step === 'wide quietly') { phone.matches = false; }
    });
    return {
        open: classes.menu.has('show'),
        bodyOpen: classes.body.has('navbar-open'),
        expanded: icon.getAttribute('aria-expanded'),
        focus: sandbox.document.activeElement.name,
        captured: listeners.some((l) => l.type === 'keydown' && l.capture),
        prevented,
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
    'tab from the last control goes to the first': ['click', 'focus site', 'tab'],
    'shift tab from the first control goes to the last': ['click', 'focus logo', 'shift tab'],
    'tab inside the panel is left to the browser': ['click', 'focus inside', 'tab'],
    'tab from the close button to the row is left to the browser': ['click', 'focus close', 'tab'],
    'tab from the button into the panel is left to the browser': ['click', 'focus icon', 'tab'],
    'tab while closed is left alone': ['focus logo', 'shift tab'],
    'tab on a wide window is left alone': ['click', 'wide quietly', 'focus site', 'tab'],
    'the close button closes and focuses the button': ['click', 'focus close', 'press close'],
    'a tap outside the header closes': ['click', 'focus outside', 'tap outside'],
    'a tap in the menu keeps it': ['click', 'tap inside'],
    'a wider window closes the menu': ['click', 'wide'],
};
Object.keys(scenarios).forEach((name) => {
    process.stdout.write(JSON.stringify(Object.assign({ name }, run(scenarios[name]))) + '\n');
});
