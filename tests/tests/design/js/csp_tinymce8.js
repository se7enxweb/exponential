// Runs extension/ezoe/design/standard/javascript/tinymce8_ez/ezoe8_start.js against mini_dom.js: the TinyMCE 8 editor
// starts from the JSON data blocks and the data-ezoe8-* attributes the edit template writes, with a stand-in for
// tinymce.init that keeps the settings. Prints one JSON line per case.
// Usage: node csp_tinymce8.js <path to ezoe8_start.js>
'use strict';
const fs = require('fs');
const vm = require('vm');
const { createDocument } = require('./mini_dom.js');

const source = fs.readFileSync(process.argv[2], 'utf8');

const SETTINGS = '<script type="application/json" id="ezoe8-settings">{"license_key": "gpl", "directionality": "ltr", '
    + '"ez_settings": {"root_url": "/", "contentobject_id": 7}}</script>';
const MAP = '<script type="application/json" id="ezoe8-button-map">{"bold": "bold", "italic": "italic", "image": "ezembed", '
    + '"object": "ezembed", "|": "|", "disable": "ezdisable"}</script>';
function area(id, attribute, extra) {
    return '<textarea id="' + id + '" data-ezoe8-attribute="' + attribute + '" data-ezoe8-buttons="[&quot;bold&quot;,&quot;|&quot;,'
        + '&quot;image&quot;,&quot;object&quot;,&quot;unknown&quot;,&quot;|&quot;,&quot;italic&quot;,&quot;|&quot;,&quot;disable&quot;]" '
        + 'data-ezoe8-path-location="' + (extra && extra.path || 'bottom') + '" data-ezoe8-rows="20"' + (extra && extra.attrs || '') + '></textarea>'
        + '<input type="submit" name="CustomActionButton[' + attribute + '_disable_editor]" />';
}

function run(html, readyState, withoutTinymce) {
    const document = createDocument(html);
    document.readyState = readyState || 'complete';
    document.documentElement.getPropertyValue = () => '';
    const inits = [], errors = [];
    const window = {
        tinymce: withoutTinymce ? undefined : { init: (settings) => { inits.push(settings); } },
        getComputedStyle: () => ({ getPropertyValue: (name) => (name === '--a4-sticky-edge' ? '64px' : '') }),
        console: { error: (message) => { errors.push(message); } },
    };
    window.document = document;
    const sandbox = { window, document, JSON, Object, Math, parseInt, parseFloat, isNaN };
    vm.createContext(sandbox);
    vm.runInContext(source, sandbox);
    return { document, window, inits, errors };
}
function out(name, value) { console.log(JSON.stringify(Object.assign({ name }, value))); }

// a fake editor for the setup function: what it registers and listens to
function editor(document) {
    const e = { registered: [], buttons: {}, events: {}, options: {}, initialized: false, saved: 0 };
    return Object.assign(e, {
        options: {
            register: (name) => { e.registered.push(name); },
            set: (name, value) => { e.options[name] = value; },
            get: (name) => e.options[name],
        },
        on: (event, fn) => { (e.events[event] = e.events[event] || []).push(fn); },
        ui: { registry: { addButton: (name, spec) => { e.buttons[name] = spec; } } },
        getBody: () => { e.body = e.body || document.createElement('body'); return e.body; },
        save: () => { e.saved++; },
    });
}

// two editors on a page: each starts once, with the shared settings and its own toolbar
{
    const t = run(SETTINGS + MAP + area('ContentObjectAttribute_data_text_5', '5') + area('ContentObjectAttribute_data_text_6', '6', { path: 'none' }));
    const first = t.inits[0] || {};
    const fake = editor(t.document);
    if (first.setup) first.setup(fake);
    const inits = t.inits.length;
    t.window.eZOe8Start();
    out('start', {
        inits,
        again: t.inits.length,
        selectors: t.inits.map((s) => s.selector),
        license: first.license_key,
        root: first.ez_settings && first.ez_settings.root_url,
        toolbar: first.toolbar,
        statusbar: t.inits.map((s) => s.statusbar),
        height: first.height,
        offset: first.toolbar_sticky_offset,
        registered: fake.registered,
        disable: typeof (fake.buttons.ezdisable || {}).onAction,
        started: t.document.getElementById('ContentObjectAttribute_data_text_5').getAttribute('data-ezoe8-started'),
        globals: [typeof t.window.eZOe8Init, typeof t.window.eZOe8Toolbar, typeof t.window.eZOe8StickyOffset, typeof t.window.eZOe8Start],
        errors: t.errors.length,
    });
}
// a text in a right-to-left content language: directionality rtl and the language on the editor's body
{
    const t = run(SETTINGS + MAP + area('a', '9', { attrs: ' dir="rtl" lang="ar"' }));
    const s = t.inits[0] || {};
    const fake = editor(t.document);
    if (s.setup) s.setup(fake);
    (fake.events.init || []).forEach((fn) => fn());
    out('rtl', { directionality: s.directionality, lang: fake.body ? fake.body.getAttribute('lang') : null });
}
// the page is still being parsed: the editors start on DOMContentLoaded
{
    const t = run(SETTINGS + MAP + area('a', '1'), 'loading');
    const before = t.inits.length;
    (t.document.listeners.DOMContentLoaded || []).forEach((fn) => fn());
    out('deferred', { before, after: t.inits.length });
}
// broken data: no editor and an error in the console instead of an exception
{
    const t = run('<script type="application/json" id="ezoe8-settings">{broken</script>' + MAP + area('a', '1'));
    out('broken', { inits: t.inits.length, errors: t.errors.length });
}
// TinyMCE itself is not loaded: no editor, the console says why
{
    const t = run(SETTINGS + MAP + area('a', '1'), 'complete', true);
    out('no tinymce', { inits: t.inits.length, errors: t.errors.length });
}
