// Runs ContentStructureMenu of design/admin4/javascript/contentstructuremenu_dynamic.js against a small stand-in for
// the browser and jQuery: generateEntry for a node with sub items and for one on the path without, the document's
// click and keydown listeners pressed on the open/close button, and the cookie functions where localStorage is
// blocked. Prints one JSON object.
// Usage: node tree_toggle.js <path to contentstructuremenu_dynamic.js>
'use strict';
const fs = require('fs');
const vm = require('vm');
const source = fs.readFileSync(process.argv[2], 'utf8');

function element(tag) {
    const el = { tag, attrs: {}, children: [], textContent: '', value: '' };
    el.setAttribute = (k, v) => { el.attrs[k] = String(v); };
    el.appendChild = (child) => { el.children.push(child); };
    Object.defineProperty(el, 'innerHTML', {
        set(html) { el.value = String(html).replace(/&amp;/g, '&'); },
        get() { return el.children.map((c) => '<' + c.tag + '>' + c.textContent + '</' + c.tag + '>').join(''); },
    });
    return el;
}

function run(storage, cookie) {
    const listeners = {};
    const cookies = [];
    const document = {
        createElement: element,
        addEventListener(type, fn) { (listeners[type] = listeners[type] || []).push(fn); },
        getElementById: () => null,
    };
    let jar = cookie || '';
    Object.defineProperty(document, 'cookie', { get: () => jar, set: (v) => { cookies.push(v); jar = v.split(';')[0]; } });
    const jQuery = {
        inArray: (v, list) => (list || []).indexOf(v),
        map: (list, fn) => (list || []).map((v, i) => fn(v, i)),
        makeArray: (a) => Array.prototype.slice.call(a || []),
        ajax: () => ({}),
    };
    const localStorage = storage ? { getItem: () => null, setItem() {}, removeItem() {} } : { setItem() { throw new Error('blocked'); } };
    const sandbox = { document, jQuery, localStorage, location: { protocol: 'https:' } };
    sandbox.window = sandbox;
    vm.createContext(sandbox);
    vm.runInContext(source, sandbox);
    const params = {
        classes: { _1: { identifier: 'folder', name: 'Folder' } }, iconsList: { __default__: '/i.png' }, languages: {},
        path: ['1', '2', '42'], useCookie: true, context: 'browse', createHereMenu: 'disabled',
    };
    const menu = new sandbox.ContentStructureMenu(params, { subitems: 'Sub items of %name', actions: '%name' });
    const loads = [];
    menu.load = (a, nodeID, stamp) => { loads.push([a.id, nodeID, stamp]); return false; };
    const node = (id, children) => ({ node_id: id, object_id: id, name: 'N' + id, has_children: children, class_id: 1, languages: [], class_list: [], modified_subnode: 1700000000, url: '/n' + id });
    const out = {};
    out.withChildren = menu.generateEntry(node(5, true), false, false, 1);
    // node 2 is on the path (above the current node 42) and has no sub items in the tree
    out.onPathWithout = /currentnode/.test(menu.generateEntry(node(2, false), false, false, 1));
    out.offPathWithout = /currentnode/.test(menu.generateEntry(node(3, false), false, false, 1));
    const toggle = { id: 'a5', getAttribute: (k) => (k === 'data-modified-subnode' ? '1700000000' : null) };
    toggle.closest = (sel) => (sel === 'a[data-modified-subnode]' ? toggle : null);
    toggle.click = () => (listeners.click || []).forEach((fn) => fn({ target: toggle, preventDefault() {} }));
    const foreign = { id: 'a99', getAttribute: () => '1', closest: () => foreign };
    let prevented = false;
    (listeners.click || []).forEach((fn) => fn({ target: toggle, preventDefault() { prevented = true; } }));
    out.clickPrevented = prevented;
    (listeners.keydown || []).forEach((fn) => fn({ key: ' ', target: toggle, preventDefault() {} }));
    (listeners.click || []).forEach((fn) => fn({ target: foreign, preventDefault() {} }));
    out.loads = loads;
    if (!storage) {
        menu.setOpen(7);
        menu.setOpen(8);
        out.cookieWritten = cookies[cookies.length - 1];
        out.openFromCookie = menu.open;
    }
    return out;
}

process.stdout.write(JSON.stringify({
    storage: run(true),
    cookie: run(false),
    // a cookie written by an older version (escape(): "/" left as it is)
    oldCookie: run(false, 'other=1; contentStructureMenu=2/5/43'),
}));
