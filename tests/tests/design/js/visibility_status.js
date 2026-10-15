// Draws the status marks of the sub-items table (ezajaxsubitems_expdatatable.js: the Name and Visibility cells) and
// of the dynamic content tree (contentstructuremenu_dynamic.js: generateEntry) for sample rows, against a small
// stand-in for the browser and jQuery, and prints one JSON line per sample.
// Usage: node visibility_status.js <ezajaxsubitems_expdatatable.js> <contentstructuremenu_dynamic.js>
'use strict';
const fs = require('fs');
const vm = require('vm');

function decode(html) {
    return String(html).replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"').replace(/&#39;/g, "'").replace(/&amp;/g, '&');
}
function escapeText(text) {
    return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}
// document.createElement for the two uses: a textarea decoding a name, a span turned into markup through a div
function element(tag) {
    const el = { tag, attrs: {}, children: [], textContent: '', value: '' };
    el.setAttribute = (k, v) => { el.attrs[k] = String(v); };
    el.appendChild = (child) => { el.children.push(child); };
    Object.defineProperty(el, 'innerHTML', {
        set(html) { el.value = decode(html); },
        get() {
            return el.children.map((c) => '<' + c.tag + Object.keys(c.attrs).map((k) => ' ' + k + '="' + c.attrs[k] + '"').join('') + '>' +
                escapeText(c.textContent) + '</' + c.tag + '>').join('');
        },
    });
    return el;
}
const document = { cookie: '', createElement: element, addEventListener() {} };

// ---- the sub-items table ------------------------------------------------------------------------------------
function subitems(source) {
    let options = null;
    function $(target) {
        return {
            first() { return this; }, find() { return this; }, on() { return this; }, attr() { return ''; },
            data() { return options; },
            expDataTable(o) { options = o; return this; },
            target,
        };
    }
    $.extend = function () { return Object.assign.apply(null, arguments); };
    $.each = function (list, fn) { (list || []).forEach((v, i) => fn(i, v)); };
    const window = { Exp: { $ } };
    const sandbox = { window, document, console };
    vm.createContext(sandbox);
    vm.runInContext(source + '\nthis.api = eZAjaxSubitemsExpDataTable;', sandbox);
    const labels = {
        DATA_TABLE: { msg_loading: '' }, DATA_TABLE_COLS: { name: 'Name', visibility: 'Visibility' }, TABLE_OPTIONS: {}, ACTION_BUTTONS: {},
        STATUS: { visible: 'Sichtbar', hidden: 'Versteckt', hidden_by_parent: 'Versteckt durch übergeordneten Eintrag',
                  visible_title: 'V title', hidden_title: 'H title', hidden_by_parent_title: 'P title "quoted"',
                  disabled: 'Deaktiviert', disabled_title: 'D title', locked: 'Gesperrt', locked_title: 'L title' },
    };
    const conf = { cookieName: 'x', navigationPart: 'ezcontentnavigationpart', defaultShownColumns: {}, sortKey: 'name', sortOrder: 1,
                   rowsPrPage: 10, classesString: '', languages: {}, nameFilter: '' };
    sandbox.api.init(conf, labels, [], []);
    const column = (key) => options.columns.filter((c) => c.key === key)[0];
    const base = { url: '/n', name: 'Tom &amp; Jerry', class_icon: '<img alt="" />', hidden_status_string: 'Text' };
    const samples = {
        visible: { is_hidden: false, is_invisible: false },
        hidden: { is_hidden: true, is_invisible: true },
        'hidden by parent': { is_hidden: false, is_invisible: true },
        'no flags': {},
        'disabled user': { is_hidden: false, is_invisible: false, user_status: 'disabled' },
        'locked user': { is_hidden: false, is_invisible: false, user_status: 'locked' },
        'enabled user': { is_hidden: false, is_invisible: false, user_status: 'enabled' },
    };
    Object.keys(samples).forEach((name) => {
        const row = Object.assign({}, base, samples[name]);
        console.log(JSON.stringify({ script: 'subitems', name, cell: column('name').render(row), visibility: column('hidden_status_string').render(row) }));
    });
    // without labelsObj.STATUS (an older template) the English words are used
    const fallback = Object.assign({}, labels);
    delete fallback.STATUS;
    sandbox.api.init(conf, fallback, [], []);
    console.log(JSON.stringify({ script: 'subitems', name: 'no labels', cell: column('name').render(Object.assign({}, base, samples.hidden)) }));
}

// ---- the content tree ---------------------------------------------------------------------------------------
function tree(source) {
    const sandbox = { window: {}, document, jQuery: { inArray: (v, list) => (list || []).indexOf(v) }, localStorage: null, console };
    sandbox.window = sandbox;
    vm.createContext(sandbox);
    vm.runInContext(source + '\nthis.Menu = ContentStructureMenu;', sandbox);
    const i18n = { hidden: 'Versteckt', hiddenbyparent: 'Vom übergeordneten Element versteckt', visible: 'Sichtbar',
                   account_disabled: 'Konto deaktiviert', actions: 'Actions for %name', subitems: 'Items below %name' };
    const params = { classes: { _1: { identifier: 'folder', name: 'Folder' } }, iconsList: { __default__: '/i.png' }, languages: {},
                     createHereMenu: 'disabled', context: 'browse', path: [], useCookie: false };
    const menu = new sandbox.Menu(params, i18n);
    const base = { node_id: 5, object_id: 7, class_id: 1, name: 'Node', url: '/n', languages: [], has_children: false, modified_subnode: 0 };
    const samples = {
        visible: { is_hidden: false, is_invisible: false },
        hidden: { is_hidden: true, is_invisible: true },
        'hidden by parent': { is_hidden: false, is_invisible: true },
        'disabled user': { is_hidden: false, is_invisible: false, user_status: 'disabled' },
        'locked user': { is_hidden: false, is_invisible: false, user_status: 'locked' },
    };
    Object.keys(samples).forEach((name) => {
        console.log(JSON.stringify({ script: 'tree', name, html: menu.generateEntry(Object.assign({}, base, samples[name]), false, false, 1) }));
    });
}

subitems(fs.readFileSync(process.argv[2], 'utf8'));
tree(fs.readFileSync(process.argv[3], 'utf8'));
