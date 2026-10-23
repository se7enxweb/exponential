// Runs design/admin4/javascript/contentstructuremenu_dynamic.js against mini_dom.js: the tree drawn from the data
// attributes of div.content-tree-menu (no inline script), its open/close buttons without handler attributes (a click
// or Space loads the sub items through one listener on the document), and a node remembered as open opened again
// after the load. Prints one JSON line per case.
// Usage: node csp_tree.js <path to contentstructuremenu_dynamic.js>
'use strict';
const fs = require('fs');
const vm = require('vm');
const { createDocument } = require('./mini_dom.js');

const source = fs.readFileSync(process.argv[2], 'utf8');
const attr = (value) => JSON.stringify(value).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;');

const root = { node_id: 2, object_id: 1, class_id: 1, has_children: true, name: 'Home &amp; Garden', url: '/', modified_subnode: 100,
               languages: ['eng-GB'], class_list: [] };
const params = { contentTreeUrl: '/content/treemenu/', wwwDirPrefix: '/icons/', icons: { folder: 'folder.png' }, defaultIcon: 'default.png',
                 preloadIcons: false, languages: { 'eng-GB': 'English' }, classes: { _1: { name: 'Folder', identifier: 'folder' } },
                 showTips: false, createHereMenu: 'simplified', autoOpen: false,
                 i18n: { subitems: 'Items below %name', actions: 'Actions for %name', tree: 'Content tree', expand: 'Click' } };
const request = { action: false, context: 'navigation', hideNodes: ['43'], maxDepth: 0, expiry: '1', useCookie: false, path: ['2'], perm: 'abc' };

const document = createDocument('<div id="contentstructure"><div class="content-tree-menu" data-tree-root="' + attr(root)
    + '" data-tree-params="' + attr(params) + '" data-tree-request="' + attr(request) + '"><ul id="content_tree_menu"></ul></div></div>');

const requests = [];
let respond = null;
// the answer to the last request, as the server's JSON, once the script has handled it
const reply = (data) => {
    respond({ ok: true, status: 200, text: () => Promise.resolve(JSON.stringify(data)) });
    return new Promise((resolve) => setImmediate(resolve));
};
const storage = new Map();
const sandbox = {
    document, console, setTimeout: (fn) => fn(),
    localStorage: { getItem: (k) => (storage.has(k) ? storage.get(k) : null), setItem: (k, v) => storage.set(k, String(v)), removeItem: (k) => storage.delete(k) },
    fetch: (url) => { requests.push(url); return new Promise((resolve) => { respond = resolve; }); },
};
sandbox.window = sandbox;
vm.createContext(sandbox);
vm.runInContext(source, sandbox);
function out(name, value) { console.log(JSON.stringify(Object.assign({ name }, value))); }

const ul = document.getElementById('content_tree_menu');
out('drawn', { treeMenu: !!sandbox.treeMenu, role: ul.getAttribute('role'), label: ul.getAttribute('aria-label'),
               root: !!document.getElementById('n2'), handlers: /\son[a-z]+=/.test(ul.innerHTML), requests: requests.slice() });

(async () => {
    // the root's sub items arrive: a node with children (an open/close button) and a hidden one (hideNodes)
    await reply({ children_count: 2, children: [
        { node_id: 5, object_id: 6, class_id: 1, has_children: true, name: 'News', url: '/news', modified_subnode: 7, languages: [], class_list: [] },
        { node_id: 43, object_id: 44, class_id: 1, has_children: false, name: 'Hidden', url: '/h', modified_subnode: 0, languages: [], class_list: [] },
    ] });
    const toggle = document.getElementById('a5');
    out('sub items', { toggle: !!toggle, load: toggle && toggle.getAttribute('data-tree-load'), modified: toggle && toggle.getAttribute('data-tree-modified'),
                       handlers: /\son[a-z]+=/.test(ul.innerHTML), hiddenNodeShown: !!document.getElementById('n43') });

    // node 8 was open before: it opens again when the sub items of node 5 arrive
    storage.set('contentStructureMenu_8', '1');
    requests.length = 0;
    const click = document.dispatch('click', toggle);
    out('click', { prevented: click.defaultPrevented, requests: requests.slice(), state: toggle.className });
    requests.length = 0;
    await reply({ children_count: 1, children: [
        { node_id: 8, object_id: 9, class_id: 1, has_children: true, name: 'Archive', url: '/a', modified_subnode: 3, languages: [], class_list: [] },
    ] });
    out('loaded', { state: toggle.className, expanded: toggle.getAttribute('aria-expanded'), remembered: storage.get('contentStructureMenu_5'),
                    reopened: requests.slice() });

    const space = document.dispatch('keydown', toggle, { key: ' ' });
    out('space closes', { prevented: space.defaultPrevented, state: toggle.className, container: document.getElementById('c5').className });
})();
