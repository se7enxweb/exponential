// Runs the header menus of design/admin4/javascript/admin4.js (the block from "admin4 header menus" to the toasts)
// against a small stand-in for the browser: the site link, the site menu's wrapper, button and panel with three links,
// the user menu's button and panel with one link, a link outside, the document's listeners. Prints one JSON line per
// scenario.
// Usage: node site_menu.js <path to admin4.js>
'use strict';
const fs = require('fs');
const vm = require('vm');
const source = fs.readFileSync(process.argv[2], 'utf8');
const start = source.indexOf('/*\n * admin4 header menus');
const end = source.indexOf('/*\n * admin4 toasts');
if (start === -1 || end === -1) {
    throw new Error('the header menus block was not found');
}
const block = source.slice(start, end);

function run(steps, options) {
    const opts = Object.assign({ menu: true }, options || {});
    const log = [];
    const docListeners = {};
    let sandbox = null;

    function element(name, extra) {
        const map = {};
        return Object.assign({
            name,
            attrs: {},
            hidden: false,
            parentNode: null,
            offsetParent: {},
            kids: [],
            setAttribute(k, v) { this.attrs[k] = String(v); },
            getAttribute(k) { return k in this.attrs ? this.attrs[k] : null; },
            focus() { sandbox.document.activeElement = this; log.push('focus ' + name); },
            addEventListener(type, fn) { (map[type] = map[type] || []).push(fn); },
            fire(type, e) { (map[type] || []).forEach((fn) => fn(e)); },
            add(child) { child.parentNode = this; this.kids.push(child); return child; },
            contains(other) {
                for (let e = other; e; e = e.parentNode) { if (e === this) { return true; } }
                return false;
            },
            all() { return this.kids.reduce((out, k) => out.concat([k], k.all()), []); },
            querySelectorAll(sel) { return sel === 'a[href]' ? this.all().filter((e) => e.isLink) : []; },
            querySelector(sel) {
                if (sel === '.site-preview') { return this.all().filter((e) => e.isSiteLink)[0] || null; }
                return null;
            },
        }, extra || {});
    }

    const body = element('body');
    const site = body.add(element('header-site'));
    const link = site.add(element('site link', { isSiteLink: true, isLink: true }));
    const wrap = element('wrap', { hidden: true });
    const button = element('site button');
    const panel = element('site panel', { hidden: true });
    const front = element('front', { isLink: true });
    const en = element('en', { isLink: true });
    const admin = element('site_admin', { isLink: true });
    if (opts.menu) {
        site.add(wrap);
        wrap.add(button);
        wrap.add(panel);
        [front, en, admin].forEach((a) => panel.add(a));
    }
    const userWrap = body.add(element('user wrap'));
    const userButton = userWrap.add(element('user button'));
    const userPanel = userWrap.add(element('user panel', { hidden: true }));
    userPanel.add(element('user link', { isLink: true }));
    const outside = body.add(element('outside', { isLink: true }));
    const ids = { 'a4-user-toggle': userButton, 'a4-user-menu': userPanel };
    if (opts.menu) {
        Object.assign(ids, { 'a4-site-menu-wrap': wrap, 'a4-site-toggle': button, 'a4-site-menu': panel });
    }

    sandbox = {
        window: { matchMedia: () => ({ matches: true }) },
        document: {
            readyState: 'complete',
            activeElement: body,
            getElementById: (id) => ids[id] || null,
            querySelector: () => null,
            addEventListener(type, fn) { (docListeners[type] = docListeners[type] || []).push(fn); },
            contains: () => true,
        },
    };
    vm.runInNewContext(block, sandbox);

    const key = (target, k) => {
        const e = { key: k, target, defaultPrevented: false, preventDefault() { this.defaultPrevented = true; } };
        target.fire('keydown', e);
        // bubbles to the panel and the document
        for (let p = target.parentNode; p; p = p.parentNode) { p.fire('keydown', e); }
        (docListeners.keydown || []).forEach((fn) => fn(e));
    };
    const click = (target) => {
        target.fire('click', { target });
        (docListeners.click || []).forEach((fn) => fn({ target }));
    };
    const state = () => ({
        wrapHidden: wrap.hidden,
        linkHidden: link.hidden,
        expanded: button.getAttribute('aria-expanded'),
        panelHidden: panel.hidden,
        userExpanded: userButton.getAttribute('aria-expanded'),
        active: sandbox.document.activeElement.name,
    });
    const elements = { button, panel, front, en, admin, outside, userButton, link };
    steps({ key, click, elements, state, log });
    return state();
}

const out = {};
out.init = run(() => {});
out.disabled = run(() => {}, { menu: false });
out.clickOpens = run(({ click, elements }) => { click(elements.button); });
out.clickTwiceCloses = run(({ click, elements }) => { click(elements.button); click(elements.button); });
out.arrowDownOpensAndFocusesTheSite = run(({ key, elements }) => { elements.button.focus(); key(elements.button, 'ArrowDown'); });
out.arrowsMove = run(({ key, elements }) => {
    elements.button.focus();
    key(elements.button, 'ArrowDown');
    key(elements.front, 'ArrowDown');
});
out.endAndHome = run(({ key, elements, log }) => {
    elements.button.focus();
    key(elements.button, 'ArrowDown');
    key(elements.front, 'End');
    log.length = 0;
    key(elements.admin, 'Home');
});
out.arrowUpFromTheFirstGoesBack = run(({ key, elements }) => {
    elements.button.focus();
    key(elements.button, 'ArrowDown');
    key(elements.front, 'ArrowUp');
});
out.escapeClosesAndReturnsTheFocus = run(({ key, elements }) => {
    elements.button.focus();
    key(elements.button, 'ArrowDown');
    key(elements.en, 'Escape');
});
out.clickOutsideCloses = run(({ click, elements }) => { click(elements.button); click(elements.outside); });
out.userMenuClosesTheSiteMenu = run(({ click, elements }) => { click(elements.button); click(elements.userButton); });
out.focusLeavingCloses = run(({ click, elements }) => {
    click(elements.button);
    elements.panel.parentNode.fire('focusout', { relatedTarget: elements.outside });
});
Object.keys(out).forEach((name) => console.log(JSON.stringify(Object.assign({ scenario: name }, out[name]))));
