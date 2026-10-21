// Runs design/admin4/javascript/popupmenu/ezpopupmenu.js against a small stand-in for the browser: two menus
// ('Menu' with an item disabled for the id 'locked', 'Other' without such an item), their links, a form with hidden
// fields, location. Prints one JSON object: what the menu substitutes, which item a menu opened without a disable list
// disables, and whether a click on a disabled item or a submenu opener follows its "#".
// Usage: node popup_substitute.js <path to ezpopupmenu.js>
'use strict';
const fs = require('fs');
const vm = require('vm');
const path = require('path');
const source = fs.readFileSync(process.argv[2], 'utf8');

const elements = {};
const listeners = {};
function element(id, tag, extra) {
    const el = Object.assign({
        id, tagName: tag || 'A', nodeType: 1, className: '', attrs: {}, style: {}, children: [], parentNode: null,
        offsetWidth: 100, offsetHeight: 100, offsetParent: {},
        getAttribute(n) { return n in this.attrs ? this.attrs[n] : null; },
        setAttribute(n, v) { this.attrs[n] = String(v); },
        removeAttribute(n) { delete this.attrs[n]; },
        hasAttribute(n) { return n in this.attrs; },
        getElementsByTagName(t) { return this.children.filter((c) => c.tagName === t.toUpperCase()); },
        getClientRects() { return [1]; },
        getBoundingClientRect() { return { left: 0, top: 0, right: 100, bottom: 20, width: 100, height: 20 }; },
        closest(sel) {
            for (let e = this; e; e = e.parentNode) {
                if (e.matches && e.matches(sel)) { return e; }
            }
            return null;
        },
        matches(sel) {
            return sel.split(',').some((one) => {
                one = one.trim();
                if (one === 'div.popupmenu') { return this.tagName === 'DIV' && /popupmenu/.test(this.className); }
                if (one === 'a[aria-disabled="true"]') { return this.tagName === 'A' && this.attrs['aria-disabled'] === 'true'; }
                if (one === 'a.more[href="#"]') { return this.tagName === 'A' && /(^|\s)more(\s|$)/.test(this.className) && this.attrs.href === '#'; }
                return false;
            });
        },
        appendChild(c) { c.parentNode = this; this.children.push(c); return c; },
        focus() {},
    }, extra || {});
    elements[id] = el;
    return el;
}
const menu = element('Menu', 'DIV', { className: 'popupmenu' });
menu.appendChild(element('menu-view'));
menu.appendChild(element('menu-edit'));
menu.appendChild(element('menu-more', 'A', { className: 'more' })).attrs.href = '#';
const other = element('Other', 'DIV', { className: 'popupmenu' });
other.appendChild(element('other-view'));
const page = element('page-link');
const form = element('menu-form', 'FORM', {
    submitted: [],
    submit() { this.submitted.push(this.childNodes.map((c) => c.name + '=' + c.value).join('&') + ' -> ' + this.attrs.action); },
});
form.attrs.action = '/content/action';
form.childNodes = [
    { type: 'hidden', name: 'NodeID', value: '%nodeID%' },
    { type: 'hidden', name: 'Pair', value: '%nodeID%-%nodeID%' },
    { type: 'hidden', name: 'Custom', value: '%extra%' },
];

const location = { href: '' };
const document = {
    documentElement: { scrollLeft: 0, scrollTop: 0, clientWidth: 1200, clientHeight: 800 },
    body: element('body', 'BODY'),
    activeElement: null,
    getElementById: (id) => elements[id] || null,
    querySelector: () => null,
    addEventListener(type, fn) { (listeners[type] = listeners[type] || []).push(fn); },
};
const sandbox = { document, location, navigator: { userAgent: '' }, innerWidth: 1200, innerHeight: 800, pageXOffset: 0, pageYOffset: 0 };
sandbox.window = sandbox;
sandbox.getComputedStyle = () => ({ position: 'static', display: 'block' });
sandbox.menuArray = {
    Menu: { depth: 0, elements: {
        'menu-view': { url: '/content/view/full/%nodeID%/%nodeID%/$&' },
        'menu-edit': { url: '/content/edit/%objectID%', disabled_class: 'menu-item-disabled', disabled_for: { locked: 'yes' } },
    } },
    Other: { depth: 0, elements: {
        'other-view': { url: '/x/%nodeID%', disabled_class: 'menu-item-disabled', disabled_for: { locked: 'yes' } },
    } },
};
vm.createContext(sandbox);
// the shared address check of the page head (a4_url.js), loaded before the menu script
vm.runInContext(fs.readFileSync(path.join(path.dirname(process.argv[2]), '..', 'a4_url.js'), 'utf8'), sandbox);
vm.runInContext(source, sandbox);

const out = {};
const mouse = { type: 'click', detail: 1, pageX: 10, pageY: 10, clientX: 10, clientY: 10, target: page, currentTarget: page };
sandbox.ezpopmenu_showTopLevel(mouse, 'Menu', { '%nodeID%': 7, '%objectID%': 9 }, 'Name', 'locked', -1);
out.href = elements['menu-view'].attrs.href;
out.editDisabled = elements['menu-edit'].className;
// a new menu without a disable list: the list of the menu before is not its own
sandbox.ezpopmenu_showTopLevel(mouse, 'Other', { '%nodeID%': 8 }, 'Name');
out.otherDisabled = elements['other-view'].className;
// the redirect and the form
sandbox.ezpopup_SubstituteAndRedirect('/a/%nodeID%/b/%nodeID%');
out.redirect = location.href;
sandbox.ezpopmenu_submitForm('menu-form', ['extra', 'x$&y']);
out.form = form.submitted[0];
out.formRestored = form.childNodes.map((c) => c.value);
// clicks: a disabled item, a submenu opener, an item, a link outside the menus
sandbox.ezpopmenu_showTopLevel(mouse, 'Menu', { '%nodeID%': 7, '%objectID%': 9 }, 'Name', 'locked', -1);
const click = (el) => { const e = { target: el, prevented: false, preventDefault() { this.prevented = true; } }; (listeners.click || []).forEach((fn) => fn(e)); return e.prevented; };
out.clickDisabled = click(elements['menu-edit']);
out.clickMore = click(elements['menu-more']);
out.clickItem = click(elements['menu-view']);
out.clickOutside = click(page);
process.stdout.write(JSON.stringify(out));
