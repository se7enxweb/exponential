// Runs the rich text editor block of design/admin4/javascript/admin4.js (the one that starts "The content of the rich
// text editors follows the page's mode") against a stand-in for the browser and TinyMCE 8: the page's mode on <html>,
// a MutationObserver, the oxide stylesheet links of the page and of an editor's document, and an editor manager whose
// editors register their options like TinyMCE 8 (an option set before it is registered is refused, registering it
// again keeps the value). Prints one JSON line per scenario.
// Usage: node editor_skin.js <path to admin4.js>
'use strict';
const fs = require('fs');
const vm = require('vm');
const source = fs.readFileSync(process.argv[2], 'utf8');
const marker = source.indexOf(" * The content of the rich text editors follows the page's mode.");
const start = source.lastIndexOf('/*', marker);
const end = source.indexOf('})();', marker) + '})();'.length;

function options(raw) {
    const specs = {};
    const values = {};
    return {
        register(name, spec) {
            specs[name] = spec;
            const value = name in values ? values[name] : raw[name];
            if (value !== undefined && spec.processor(value)) { values[name] = value; }
        },
        isRegistered: (name) => name in specs,
        get: (name) => (name in values ? values[name] : (specs[name] || {}).default),
        set(name, value) {
            if (!(name in specs)) { return false; }
            values[name] = value;
            return true;
        },
    };
}

function link(href) {
    return { href, getAttribute() { return this.href; }, setAttribute(k, v) { this.href = v; } };
}

function run(mode, raw, steps) {
    let theme = mode;
    const observers = [];
    const loadHandlers = [];
    const pageLinks = [link('/t8/skins/ui/oxide' + (mode === 'dark' ? '-dark' : '') + '/skin.min.css')];
    const frameLinks = [link('/t8/skins/ui/oxide' + (mode === 'dark' ? '-dark' : '') + '/content.min.css'), link('/t8/skins/content/default/content.min.css')];
    const frameDoc = { querySelectorAll: () => frameLinks, getElementById: () => null, body: null };
    const editors = [];
    const managerHandlers = {};
    const tinymce = {
        on(type, fn) { managerHandlers[type] = fn; },
        get: () => editors.slice(),
    };
    const html = {
        getAttribute: (k) => (k === 'data-a4-theme' ? theme : null),
        setAttribute(k, v) { if (k === 'data-a4-theme') { theme = v; } },
    };
    const sandbox = {
        window: { tinymce, addEventListener(type, fn) { if (type === 'load') { loadHandlers.push(fn); } }, MutationObserver: null },
        document: {
            readyState: 'complete',
            documentElement: html,
            querySelector: () => null,
            querySelectorAll(sel) {
                if (sel === 'iframe') { return [{ contentDocument: frameDoc }]; }
                if (sel.indexOf('link[rel="stylesheet"]') === 0) {
                    const part = sel.match(/href\*="([^"]+)"/)[1];
                    return pageLinks.filter((l) => l.href.indexOf(part) >= 0);
                }
                return [];
            },
            addEventListener() {},
        },
    };
    frameDoc.querySelectorAll = (sel) => {
        const m = sel.match(/href\*="([^"]+)"/);
        return m ? frameLinks.filter((l) => l.href.indexOf(m[1]) >= 0) : [];
    };
    sandbox.window.MutationObserver = function (fn) { this.observe = () => observers.push(fn); };
    sandbox.MutationObserver = sandbox.window.MutationObserver;
    vm.createContext(sandbox);
    vm.runInContext(source.slice(start, end), sandbox);
    const skins = [];
    steps.forEach((step) => {
        if (step === 'add editor') {
            const editor = { initialized: false, options: options(raw) };
            editors.push(editor);
            managerHandlers.AddEditor({ editor });
            // the silver theme registers its option when it loads, after AddEditor
            editor.options.register('skin', { processor: (v) => typeof v === 'string' || v === false, default: 'oxide' });
            skins.push(editor.options.get('skin'));
        } else if (step === 'light' || step === 'dark') {
            theme = step;
            observers.forEach((fn) => fn());
        } else if (step === 'load') {
            loadHandlers.forEach((fn) => fn());
        }
    });
    return { skins, page: pageLinks.map((l) => l.href), frame: frameLinks.map((l) => l.href), hooked: !!managerHandlers.AddEditor };
}

const scenarios = {
    'dark page': ['dark', {}, ['add editor']],
    'light page': ['light', {}, ['add editor']],
    'configured skin': ['dark', { skin: 'myskin' }, ['add editor']],
    'dark to light': ['dark', {}, ['add editor', 'light']],
    'light to dark': ['light', {}, ['add editor', 'dark']],
};
Object.keys(scenarios).forEach((name) => {
    const [mode, raw, steps] = scenarios[name];
    process.stdout.write(JSON.stringify(Object.assign({ name }, run(mode, raw, steps))) + '\n');
});
