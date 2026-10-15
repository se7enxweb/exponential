// Runs design/admin4/javascript/a4_mega_menu.js against a small stand-in for the browser (the tab's list item with the
// tab link, the button and the panel, with { second: true } a second tab's (users) as well, the document's listeners, matchMedia, timers that only move when told, fetch)
// and prints one JSON line per scenario.
// Usage: node mega_menu.js <path to a4_mega_menu.js>
'use strict';
const fs = require('fs');
const vm = require('vm');
const source = fs.readFileSync(process.argv[2], 'utf8');

function listeners() {
    const map = {};
    return {
        addEventListener(type, fn) { (map[type] = map[type] || []).push(fn); },
        fire(type, e) { (map[type] || []).forEach((fn) => fn(e)); },
    };
}

async function run(steps, options) {
    const opts = Object.assign({ wide: true, fine: true, fetchOk: true, fragment: true, other: false, inMore: false, second: false }, options || {});
    const log = [];
    let fetches = 0;
    let now = 0;
    let timers = [];
    let sandbox = null;

    function element(name, extra) {
        const l = listeners();
        const el = Object.assign({
            name,
            attrs: {},
            children: [],
            parent: null,
            hidden: false,
            setAttribute(k, v) { this.attrs[k] = String(v); },
            getAttribute(k) { return k in this.attrs ? this.attrs[k] : null; },
            removeAttribute(k) { delete this.attrs[k]; },
            focus() { sandbox.document.activeElement = this; log.push('focus ' + name); },
            addEventListener: l.addEventListener,
            fire: l.fire,
            contains(other) {
                for (let e = other; e; e = e.parent) { if (e === this) { return true; } }
                return false;
            },
            appendChild(child) { child.parent = this; this.children.push(child); return child; },
            closest() { return null; },
            querySelector(sel) { return this.querySelectorAll(sel)[0] || null; },
            querySelectorAll(sel) {
                const out = [];
                const walk = (e) => e.children.forEach((c) => { if (c.matches(sel)) { out.push(c); } walk(c); });
                walk(this);
                return out;
            },
            matches(sel) { return (this.selectors || []).indexOf(sel) !== -1; },
        }, extra || {});
        Object.defineProperty(el, 'innerHTML', {
            set() { this.children.forEach((c) => { c.parent = null; }); this.children = []; },
            get() { return ''; },
        });
        return el;
    }

    const classes = new Set();
    const item = element('item', {
        classList: { toggle(c, on) { if (on) { classes.add(c); } else { classes.delete(c); } } },
        // the tab moved into the header's "More" list
        closest(sel) { return opts.inMore && sel === '.a4-nav-more-list' ? {} : null; },
    });
    const tab = item.appendChild(element('tab'));
    const toggle = item.appendChild(element('toggle', { selectors: ['.a4-mega-toggle'] }));
    toggle.attrs = { 'aria-expanded': 'false', 'aria-controls': 'a4-mega-setup', 'data-a4-mega-src': '/user/megamenu/setup' };
    const panel = item.appendChild(element('panel', { hidden: true }));
    panel.attrs = { 'data-a4-mega-error': 'could not be loaded' };
    const body = panel.appendChild(element('body', { selectors: ['[data-a4-mega-body]'] }));
    const all = panel.appendChild(element('all link', { selectors: ['a[href]'], pathname: '/setup/menu' }));
    // the second tab with a mega menu (users): its own item, button and panel
    const classes2 = new Set();
    const item2 = element('item2', {
        classList: { toggle(c, on) { if (on) { classes2.add(c); } else { classes2.delete(c); } } },
    });
    const toggle2 = item2.appendChild(element('toggle2', { selectors: ['.a4-mega-toggle'] }));
    toggle2.attrs = { 'aria-expanded': 'false', 'aria-controls': 'a4-mega-users', 'data-a4-mega-src': '/user/megamenu/users' };
    const panel2 = item2.appendChild(element('panel2', { hidden: true }));
    panel2.appendChild(element('body2', { selectors: ['[data-a4-mega-body]'] }));
    const outside = element('page link');
    const otherToggle = element('more');

    // the fragment: a groups element with two links
    function fragment() {
        const groups = element('groups', { selectors: ['[data-a4-mega-groups]'] });
        const first = groups.appendChild(element('first link', { selectors: ['a[href]'], pathname: '/setup/cache' }));
        groups.appendChild(element('second link', { selectors: ['a[href]'], pathname: '/class/grouplist' }));
        return { groups, first };
    }

    const doc = listeners();
    function media(query) {
        return {
            get matches() { return query.indexOf('min-width: 576px') !== -1 ? opts.wide : opts.fine; },
            addEventListener() {},
        };
    }
    const window = listeners();
    Object.assign(window, {
        matchMedia: media,
        location: { pathname: '/setup/cache' },
        setTimeout(fn, ms) { const t = { fn, at: now + ms }; timers.push(t); return t; },
        clearTimeout(t) { timers = timers.filter((x) => x !== t); },
        fetch() {
            fetches++;
            if (!opts.fetchOk) { return Promise.resolve({ ok: false, status: 500, text: () => Promise.resolve('') }); }
            return Promise.resolve({ ok: true, status: 200, text: () => Promise.resolve(opts.fragment ? 'fragment' : '<html>sign in</html>') });
        },
    });
    sandbox = {
        window,
        document: {
            readyState: 'complete',
            body: element('body'),
            activeElement: null,
            addEventListener: doc.addEventListener,
            getElementById(id) { return id === 'a4-mega-setup' ? panel : (id === 'a4-mega-users' ? panel2 : null); },
            querySelectorAll(sel) { return sel === '#header .a4-mega-item' ? (opts.second ? [item, item2] : [item]) : []; },
            querySelector(sel) { return opts.other && sel.indexOf('.a4-nav-more-toggle') !== -1 ? otherToggle : null; },
            createElement(tag) {
                if (tag === 'template') {
                    const t = { content: null };
                    Object.defineProperty(t, 'innerHTML', {
                        set(html) {
                            const f = html === 'fragment' ? fragment() : null;
                            t.content = { querySelector: () => (f ? f.groups : null) };
                        },
                    });
                    return t;
                }
                const p = element(tag);
                return p;
            },
        },
    };
    sandbox.document.activeElement = sandbox.document.body;
    vm.createContext(sandbox);
    vm.runInContext(source, sandbox);

    const flush = () => new Promise((r) => setImmediate(r));
    async function advance(ms) {
        const until = now + ms;
        for (;;) {
            const due = timers.filter((t) => t.at <= until).sort((a, b) => a.at - b.at)[0];
            if (!due) { break; }
            timers = timers.filter((t) => t !== due);
            now = due.at;
            due.fn();
            await flush();
        }
        now = until;
    }

    for (const step of steps) {
        const [what, arg] = step.split(':');
        if (what === 'click') { toggle.fire('click', {}); doc.fire('click', { target: toggle }); }
        else if (what === 'click outside') { doc.fire('click', { target: outside }); }
        else if (what === 'click inside') { doc.fire('click', { target: body.children[0] || body }); }
        else if (what === 'arrowdown') { toggle.fire('keydown', { key: 'ArrowDown', preventDefault() {} }); }
        else if (what === 'escape') { doc.fire('keydown', { key: 'Escape', defaultPrevented: false, preventDefault() {} }); }
        else if (what === 'focus toggle') { sandbox.document.activeElement = toggle; }
        else if (what === 'focus outside') { sandbox.document.activeElement = outside; }
        else if (what === 'tab out') { item.fire('focusout', { relatedTarget: outside }); sandbox.document.activeElement = outside; }
        else if (what === 'tab inside') { item.fire('focusout', { relatedTarget: all }); sandbox.document.activeElement = all; }
        else if (what === 'enter') { item.fire('pointerenter', { pointerType: arg || 'mouse' }); }
        else if (what === 'leave') { item.fire('pointerleave', { pointerType: arg || 'mouse' }); }
        else if (what === 'wait') { await advance(Number(arg)); }
        else if (what === 'narrow') { opts.wide = false; window.fire('resize', {}); }
        else if (what === 'into more') { opts.inMore = true; window.fire('resize', {}); }
        else if (what === 'focus in') { item.fire('focusin', {}); sandbox.document.activeElement = all; }
        else if (what === 'on overview') { window.location.pathname = '/setup/menu'; }
        else if (what === 'click second') { toggle2.fire('click', {}); doc.fire('click', { target: toggle2 }); }
        else if (what === 'enter second') { item2.fire('pointerenter', { pointerType: 'mouse' }); }
        else if (what === 'leave second') { item2.fire('pointerleave', { pointerType: 'mouse' }); }
        await flush();
    }
    const groups = body.children[0];
    const current = groups && groups.children ? groups.children.filter((c) => c.getAttribute('aria-current') === 'page').map((c) => c.name) : [];
    return {
        expanded: toggle.getAttribute('aria-expanded'),
        open: !panel.hidden,
        itemClass: classes.has('is-mega-open'),
        fetches,
        content: groups ? groups.name : null,
        status: groups && groups.name === 'p' ? groups.textContent : null,
        busy: panel.getAttribute('aria-busy'),
        current,
        allCurrent: all.getAttribute('aria-current'),
        focus: sandbox.document.activeElement ? sandbox.document.activeElement.name : null,
        secondExpanded: toggle2.getAttribute('aria-expanded'),
        secondOpen: !panel2.hidden,
        secondItemClass: classes2.has('is-mega-open'),
        log,
    };
}

const scenarios = {
    'click opens and loads': [['click']],
    'click again closes': [['click', 'click']],
    'second opening loads nothing more': [['click', 'click', 'click']],
    'arrowdown opens and focuses the first link': [['focus toggle', 'arrowdown']],
    'escape in the panel closes and focuses the button': [['focus toggle', 'arrowdown', 'escape']],
    'escape with the focus elsewhere closes and keeps it': [['click', 'focus outside', 'escape']],
    'escape while closed does nothing': [['focus toggle', 'escape']],
    'click outside closes': [['click', 'click outside']],
    'click inside keeps it': [['click', 'click inside']],
    'tab out of it closes': [['focus toggle', 'arrowdown', 'tab out']],
    'tab inside keeps it': [['focus toggle', 'arrowdown', 'tab inside']],
    'hover 299ms opens nothing': [['enter', 'wait:299']],
    'hover 300ms opens the preview': [['enter', 'wait:300']],
    'leaving for 399ms keeps the preview': [['enter', 'wait:300', 'leave', 'wait:399']],
    'leaving for 400ms closes the preview': [['enter', 'wait:300', 'leave', 'wait:400']],
    'back within the delay keeps the preview': [['enter', 'wait:300', 'leave', 'wait:200', 'enter', 'wait:1000']],
    'leaving before the delay opens nothing': [['enter', 'wait:200', 'leave', 'wait:1000']],
    'a click keeps the preview open': [['enter', 'wait:300', 'click', 'leave', 'wait:1000']],
    'touch never previews': [['enter:touch', 'wait:1000']],
    'a coarse pointer never previews': [['enter', 'wait:1000'], { fine: false }],
    'no preview while another menu is open': [['enter', 'wait:1000'], { other: true }],
    'below 576px a click opens nothing': [['click'], { wide: false }],
    'getting narrower closes it': [['click', 'narrow']],
    'focus coming into a preview keeps it open': [['enter', 'wait:300', 'focus in', 'leave', 'wait:1000']],
    'in the More list a click opens nothing': [['click'], { inMore: true }],
    'moving into the More list closes it': [['click', 'into more']],
    'the overview link is marked on the overview': [['on overview', 'click']],
    'a failed load says so': [['click'], { fetchOk: false }],
    'a failed load is tried again': [['click', 'click', 'click'], { fetchOk: false }],
    'a page in place of the fragment is no menu': [['click'], { fragment: false }],
    'arrowdown after a failed load focuses the overview link': [['focus toggle', 'arrowdown'], { fetchOk: false }],
    'two tabs: opening the second closes the first': [['click', 'click second'], { second: true }],
    'two tabs: opening the first again closes the second': [['click', 'click second', 'click'], { second: true }],
    'two tabs: a preview of the second closes the first': [['enter', 'wait:300', 'leave', 'enter second', 'wait:300'], { second: true }],
    'two tabs: each opens on its own': [['click second'], { second: true }],
    'two tabs: hovering the second keeps the first opened by a click': [['click', 'enter second', 'wait:1000'], { second: true }],
};
(async () => {
    for (const name of Object.keys(scenarios)) {
        const [steps, options] = scenarios[name];
        process.stdout.write(JSON.stringify(Object.assign({ name }, await run(steps, options))) + '\n');
    }
})();
