// Runs design/admin4/javascript/node_tabs.js against a small stand-in for jQuery and the browser: the document's
// cookie, every selector the script asks for (recorded), one tab list with a selected tab, window.a4Pref recording
// what is saved. Prints one JSON object, one entry per scenario.
// Usage: node node_tabs.js <path to node_tabs.js>
'use strict';
const fs = require('fs');
const vm = require('vm');
const source = fs.readFileSync(process.argv[2], 'utf8');

function run(cookie, steps, opts) {
    opts = opts || {};
    const selectors = [];
    const written = [];
    const saved = [];
    let jar = cookie || '';
    const handlers = {};
    function q(sel) {
        const self = {
            sel, length: /#node-tab-/.test(String(sel)) ? 1 : 0,
            on(type, fn) { handlers[sel + ' ' + type] = fn; return self; },
            each() { return self; }, find() { return q(sel + ' find'); }, removeClass() { return self; }, addClass() { return self; },
            hasClass() { return false; }, toggleClass() { return self; }, attr() { return 'node-tab-view'; },
            removeAttr() { return self; }, children() { return Object.assign(q(sel + ' children'), { length: 0 }); }, toArray() { return []; },
            closest(s) { selectors.push('closest ' + s); return q(s); },
        };
        self[0] = { parentNode: { parentNode: { id: 'window-controls', parentNode: {} } } };
        return self;
    }
    function $(arg) {
        if (typeof arg === 'function') { arg($); return null; }
        if (typeof arg === 'string') {
            selectors.push(arg);
            // jQuery 4 throws on a broken selector
            if (/\]/.test(arg)) { throw new Error('Syntax error, unrecognized expression: ' + arg); }
        }
        return q(arg);
    }
    $.ez = { url: '/ezjscore/' };
    $.post = (url, data) => saved.push('post ' + url + ' ' + data);
    const document = { getElementById: (id) => (opts.token !== undefined && id === 'ezxform_token_js' ? { getAttribute: () => opts.token } : null) };
    Object.defineProperty(document, 'cookie', { get: () => jar, set: (v) => { written.push(v); } });
    const sandbox = { jQuery: $, document, setTimeout: (fn) => fn(), clearTimeout() {}, location: { protocol: 'https:' } };
    sandbox.window = sandbox;
    if (!opts.noPref) sandbox.a4Pref = { set: (n, v) => saved.push(n + '=' + v) };
    vm.createContext(sandbox);
    let error = null;
    try {
        vm.runInContext(source, sandbox);
        for (const step of steps || []) {
            if (step === 'toggle') { handlers['a.show-hide-tabs click'].call({ setAttribute() {}, getAttribute() { return null; } }); }
            if (step === 'open') { handlers['div.tab-block ul.tabs li a click'].call({}); }
        }
    } catch (e) {
        error = e.message;
    }
    return { error, opened: selectors.filter((s) => /#node-tab-/.test(s)), block: selectors.filter((s) => /^closest div\.tab-block$/.test(s)).length, written, saved };
}

process.stdout.write(JSON.stringify({
    'a tab name opens its tab': run('a=1; adminNavigationTab=details'),
    'a broken value is not a selector': run('adminNavigationTab=x]'),
    'a cookie of another name is not read': run('xadminNavigationTab=details'),
    'opening a tab writes the cookie': run('', ['open']),
    'the switch saves through a4Pref': run('', ['toggle']),
    'the token is URL-encoded in the legacy post': run('', ['toggle'], { noPref: true, token: 'a+b&c=d%e f' }),
}));
