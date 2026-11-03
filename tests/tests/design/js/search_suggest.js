// Runs javascript/page/search_suggest.js (with a4_url.js before it, as the page head loads it) against mini_dom.js:
// the header search field as page_search.tpl draws it with parts/search_suggest.tpl, fetch answering from a planned
// list, and timers that only move when told. Prints one JSON object with a state per scenario.
// Usage: node search_suggest.js <path to search_suggest.js> <path to a4_url.js>
'use strict';
const fs = require('fs');
const vm = require('vm');
const { createDocument } = require('./mini_dom.js');

const source = fs.readFileSync(process.argv[2], 'utf8');
const urlSource = fs.readFileSync(process.argv[3], 'utf8');

const PAGE = '<div class="searchblock" data-searchblock="1"><form id="searchform" action="/admin/content/search/" method="get">'
    + '<div class="searchtextwrapper"><input id="searchtext" class="searchtext" name="SearchText" type="search" value=""'
    + ' role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="searchsuggest-list" autocomplete="off"'
    + ' data-suggest-url="/admin/content/suggest" data-suggest-min="3" /></div>'
    + '<div class="a4-suggest" id="searchsuggest" hidden="hidden" data-text-all="ALL" data-text-count="%count RESULTS"'
    + ' data-text-found="%count FOUND" data-text-none="NONE" data-text-failed="FAILED">'
    + '<ul class="a4-suggest-list" id="searchsuggest-list" role="listbox" aria-label="Suggestions"></ul></div>'
    + '<p class="a4-suggest-status" id="searchsuggest-status" role="status"></p>'
    + '</form></div><a id="outside" href="/admin/content/dashboard">elsewhere</a><input id="other" type="text" />';

const ANSWER = {
    query: 'admi', total: 9, min_length: 3,
    groups: [
        { id: 'content', title: 'Content', count: 9, items: [
            { type: 'content', title: 'Administrator <b>users</b>', url: '/admin/Users/Administrator-users', meta: 'User group',
              icon: '/share/icons/folder.svg', path: ['Users'] },
            { type: 'content', title: 'Evil', url: 'javascript:alert(1)', meta: 'x' },
            { type: 'content', title: 'Elsewhere', url: '//evil.example/admin', meta: 'x' },
        ] },
        { id: 'pages', title: 'Document pages', count: 1, items: [
            { type: 'page', title: 'Manual.pdf', url: '/admin/pdfdeepsearch/viewer/abc/4', meta: 'Page 4', excerpt: 'the admin page' },
        ] },
        { id: 'admin', title: 'Administration', count: 1, items: [
            { type: 'admin', title: 'Setup › Solr-Admin', url: '/admin/setup/solr' },
        ] },
    ],
};

function run() {
    const document = createDocument(PAGE);
    // what mini_dom leaves out and the script needs
    document.createTextNode = (text) => { const s = document.createElement('span'); s.textContent = text; return s.firstChild; };
    let now = 0, timers = [];
    const requests = [], answers = [], clicked = [];
    const setTimeout = (fn, ms) => { const t = { fn, at: now + (ms || 0) }; timers.push(t); return t; };
    const clearTimeout = (t) => { timers = timers.filter((x) => x !== t); };
    function AbortController() {
        const signal = { aborted: false, listeners: [] };
        this.signal = signal;
        this.abort = () => { signal.aborted = true; signal.listeners.forEach((fn) => fn()); };
    }
    function fetch(url, init) {
        requests.push({ url, init });
        const next = answers.length ? answers.shift() : 'hang';
        return new Promise((resolve, reject) => {
            if (init && init.signal) init.signal.listeners.push(() => reject(Object.assign(new Error('aborted'), { name: 'AbortError' })));
            if (next === 'hang') return;
            resolve({
                ok: next.status >= 200 && next.status < 300, status: next.status,
                headers: { get: (name) => (name.toLowerCase() === 'content-type' ? (next.type || 'application/json; charset=utf-8') : null) },
                json: () => Promise.resolve(next.body),
            });
        });
    }
    const window = { document, location: { href: 'https://example.test/admin/content/dashboard' }, innerWidth: 1440, console: { error() {} } };
    const sandbox = { window, document, console, Promise, Error, URL, setTimeout, clearTimeout, AbortController, fetch, encodeURIComponent, decodeURIComponent };
    vm.createContext(sandbox);
    vm.runInContext(urlSource, sandbox);
    vm.runInContext(source, sandbox);
    document.addEventListener('click', (e) => { if (e.target.tagName === 'A') clicked.push(e.target.getAttribute('href')); });

    const flush = async () => { for (let i = 0; i < 10; i++) await new Promise((r) => setImmediate(r)); };
    async function advance(ms) {
        const until = now + ms;
        for (;;) {
            await flush();
            const due = timers.filter((t) => t.at <= until).sort((a, b) => a.at - b.at)[0];
            if (!due) break;
            timers = timers.filter((t) => t !== due);
            now = due.at;
            due.fn();
        }
        now = until;
        await flush();
    }
    const input = document.getElementById('searchtext');
    const box = document.getElementById('searchsuggest');
    return {
        document, requests, answers, clicked, advance,
        type: async (text) => { input.focus(); input.value = text; document.dispatch('input', input); await flush(); },
        key: async (key, props) => { const e = document.dispatch('keydown', props && props.target ? props.target : input, Object.assign({ key }, props || {})); await flush(); return e; },
        state() {
            const options = box.querySelectorAll('[role="option"]');
            const active = input.getAttribute('aria-activedescendant');
            const selected = options.filter((o) => o.getAttribute('aria-selected') === 'true').map((o) => o.id);
            return {
                open: !box.hidden,
                expanded: input.getAttribute('aria-expanded'),
                active: active,
                activeIsOption: !!(active && document.getElementById(active) && document.getElementById(active).getAttribute('role') === 'option'),
                selected: selected,
                options: options.length,
                hrefs: box.querySelectorAll('a').map((a) => a.getAttribute('href')),
                headings: box.querySelectorAll('.a4-suggest-heading').map((h) => h.textContent),
                groups: box.querySelectorAll('[role="group"]').map((g) => g.getAttribute('aria-labelledby') === g.querySelector('.a4-suggest-heading').id),
                titles: box.querySelectorAll('.a4-suggest-title').map((t) => t.textContent),
                marks: box.querySelectorAll('mark').map((m) => m.textContent),
                bold: box.querySelectorAll('b').length,
                metas: box.querySelectorAll('.a4-suggest-meta').map((m) => m.textContent),
                paths: box.querySelectorAll('.a4-suggest-path').map((m) => m.textContent),
                icons: box.querySelectorAll('img').map((i) => i.src),
                none: box.querySelectorAll('.a4-suggest-none').map((n) => n.textContent),
                status: document.getElementById('searchsuggest-status').textContent,
                submitted: document.submitted.slice(),
                focus: document.activeElement ? document.activeElement.id : null,
            };
        },
    };
}

(async () => {
    const out = {};

    // too short: nothing asked
    {
        const t = run();
        await t.type('ad');
        await t.advance(1000);
        out.short = { requests: t.requests.length, state: t.state() };
    }
    // typing on within the delay asks once, for the last text, as a view parameter; the answer is drawn
    {
        const t = run();
        t.answers.push({ status: 200, body: ANSWER });
        await t.type('adm');
        await t.advance(100);
        await t.type('admi');
        await t.advance(249);
        const before = t.requests.length;
        await t.advance(1);
        const init = t.requests[0] ? t.requests[0].init : {};
        out.render = { before, requests: t.requests.length, url: t.requests[0] && t.requests[0].url, credentials: init.credentials,
                       signal: !!init.signal, state: t.state() };
        // the arrow keys move through the entries; Up from the first goes to "View all results"
        await t.key('ArrowDown');
        out.down1 = t.state();
        await t.key('ArrowDown');
        out.down2 = t.state();
        await t.key('ArrowUp');
        await t.key('ArrowUp');
        out.wrapUp = t.state();
        // Enter on "View all results" searches
        const enter = await t.key('Enter');
        out.enterAll = Object.assign(t.state(), { prevented: enter.defaultPrevented });
    }
    // Enter on an entry follows its link; Escape and a click outside close
    {
        const t = run();
        t.answers.push({ status: 200, body: ANSWER });
        await t.type('admi');
        await t.advance(300);
        await t.key('ArrowDown');
        await t.key('Enter');
        out.enterItem = Object.assign(t.state(), { clicked: t.clicked.slice() });

        await t.type('admin');
        t.answers.push({ status: 200, body: ANSWER });
        await t.advance(300);
        await t.key('ArrowDown');
        const esc = await t.key('Escape');
        out.escape = Object.assign(t.state(), { prevented: esc.defaultPrevented });
        // the field focused again with the same text: the list is back
        t.document.dispatch('focus', t.document.getElementById('searchtext'));
        out.refocus = t.state();
        t.document.dispatch('click', t.document.getElementById('outside'));
        out.outside = t.state();
    }
    // a change while a request runs cancels it; a failed answer says so; an empty one says "none"
    {
        const t = run();
        await t.type('abc');
        await t.advance(300);
        const first = t.requests[0].init.signal;
        await t.type('abcd');
        out.abort = { aborted: first.aborted, state: t.state() };
        t.answers.push({ status: 500, body: {} });
        await t.advance(300);
        out.failed = t.state();
        t.answers.push({ status: 200, body: { query: 'abcde', total: 0, groups: [] } });
        await t.type('abcde');
        await t.advance(300);
        out.empty = t.state();
        // a sign-in page instead of JSON is a failure, not a crash
        t.answers.push({ status: 200, type: 'text/html', body: null });
        await t.type('abcdef');
        await t.advance(300);
        out.html = t.state();
    }
    // Ctrl+/ and Cmd+/ focus the field from anywhere, also from another field; "/" alone is left to admin4.js
    {
        const t = run();
        const other = t.document.getElementById('other');
        other.focus();
        const plain = await t.key('/', { target: other });
        out.slash = { focus: t.state().focus, prevented: plain.defaultPrevented };
        const ctrl = await t.key('/', { target: other, ctrlKey: true });
        out.ctrl = { focus: t.state().focus, prevented: ctrl.defaultPrevented };
        other.focus();
        const meta = await t.key('/', { target: other, metaKey: true });
        out.meta = { focus: t.state().focus, prevented: meta.defaultPrevented };
    }
    process.stdout.write(JSON.stringify(out));
})().catch((e) => { process.stdout.write(JSON.stringify({ error: String(e && e.stack || e) })); });
