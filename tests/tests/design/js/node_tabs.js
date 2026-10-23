// Runs design/admin4/javascript/node_tabs.js against mini_dom.js: the node view's tab block (the show/hide switch,
// a tab list with a selected tab, the panels), the document's cookie, window.a4Pref recording what is saved and
// fetch recording the legacy POST. Prints one JSON object, one entry per scenario.
// Usage: node node_tabs.js <path to a4_dom.js> <path to node_tabs.js> (the files run in the order given)
'use strict';
const fs = require('fs');
const vm = require('vm');
const { createDocument } = require('./mini_dom.js');
const sources = process.argv.slice(2).map((file) => fs.readFileSync(file, 'utf8'));

const html = '<div id="window-controls" class="tab-block">'
    + '<div class="button-left"><a id="maincontent-hide" class="show-hide-tabs" href="/x" role="button" aria-pressed="true" data-title-show="Show" data-title-hide="Hide">&nbsp;</a></div>'
    + '<ul class="tabs tabs-by-cookie">'
    + '<li id="node-tab-view" class="first selected"><a href="/v">View</a></li>'
    + '<li id="node-tab-details" class="middle"><a href="/d">Details</a></li>'
    + '<li id="node-tab-ordering" class="last"><a href="/o">Ordering</a></li></ul>'
    + '<div class="tabs-content"><div id="node-tab-view-content" class="tab-content selected">v</div>'
    + '<div id="node-tab-details-content" class="tab-content hide">d</div>'
    + '<div id="node-tab-ordering-content" class="tab-content hide">o</div></div></div>';

function run(cookie, steps, opts) {
    opts = opts || {};
    const written = [];
    const saved = [];
    const jar = cookie || '';
    const document = createDocument(html + (opts.token !== undefined ? '<span id="ezxform_token_js" title="' + opts.token.replace(/&/g, '&amp;') + '"></span>' : ''));
    Object.defineProperty(document, 'cookie', { get: () => jar, set: (v) => { written.push(v); } });
    const sandbox = { document, setTimeout: (fn) => fn(), clearTimeout() {}, location: { protocol: 'https:' }, URLSearchParams,
                      fetch: (url, init) => { saved.push('post ' + url + ' ' + init.body); return Promise.resolve({}); } };
    sandbox.window = sandbox;
    if (!opts.noPref) sandbox.a4Pref = { set: (n, v) => saved.push(n + '=' + v) };
    vm.createContext(sandbox);
    let error = null;
    try {
        sources.forEach((source) => vm.runInContext(source, sandbox));
        for (const step of steps || []) {
            if (step === 'toggle') { document.querySelector('a.show-hide-tabs').click(); }
            if (step === 'open') { document.querySelector('#node-tab-details a').click(); }
        }
    } catch (e) {
        error = e.message;
    }
    const selected = document.querySelector('ul.tabs li.selected');
    return { error, selected: selected ? selected.id : null, panel: (document.querySelector('div.tab-content.selected') || {}).id || null,
             tablist: document.querySelector('ul.tabs').getAttribute('role'), written, saved };
}

process.stdout.write(JSON.stringify({
    'a tab name opens its tab': run('a=1; adminNavigationTab=details'),
    'a broken value is not a selector': run('adminNavigationTab=x]'),
    'a cookie of another name is not read': run('xadminNavigationTab=details'),
    'opening a tab writes the cookie': run('', ['open']),
    'the switch saves through a4Pref': run('', ['toggle']),
    'the token is URL-encoded in the legacy post': run('', ['toggle'], { noPref: true, token: 'a+b&c=d%e f' }),
}));
