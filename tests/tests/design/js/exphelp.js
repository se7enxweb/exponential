// Runs design/admin4/javascript/exphelp.js (the help pages) against mini_dom.js: the instant search (request, stale
// answers, filters, results built as text, unsafe links dropped, errors), its keys (/, arrows, Enter, Esc), copy buttons
// on code, collapsible sections, the table of contents following the reading position, j/k and [ ]. Each case gets a
// fresh document and script. Prints one JSON line per case.
// Usage: node exphelp.js <path to exphelp.js>
'use strict';
const fs = require('fs');
const vm = require('vm');
const { createDocument } = require('./mini_dom.js');

const source = fs.readFileSync(process.argv[2], 'utf8');
const attr = (value) => JSON.stringify(value).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;');

const SEARCH = '<div class="a4-help" data-exphelp data-exphelp-i18n="' + attr({ resultsCount: '%count Treffer', copy: 'Kopieren', copied: 'Kopiert' }) + '">'
    + '<form data-exphelp-search action="/admin/exphelp/search" method="get" data-exphelp-endpoint="/admin/exphelp/search">'
    + '<input type="search" name="q" id="q" data-exphelp-search-input aria-controls="res" />'
    + '<label data-exphelp-facet="audience:editor"><input type="radio" name="audience" value="" data-exphelp-filter checked="checked" />All</label>'
    + '<label data-exphelp-facet="audience:editor"><input type="radio" name="audience" value="editor" id="aud-editor" data-exphelp-filter />Editors <span data-exphelp-count>?</span></label>'
    + '<select name="category" data-exphelp-filter><option>x</option></select>'
    + '<button type="submit">Search</button></form>'
    + '<div id="res" data-exphelp-results hidden></div></div>';

const VIEWER = '<div class="a4-help" data-exphelp><form data-exphelp-search data-exphelp-endpoint="/admin/exphelp/search?x=1">'
    + '<input type="search" name="q" id="q" data-exphelp-search-input /></form>'
    + '<nav data-exphelp-toc><a href="#one" id="t1">One</a><a href="#two" id="t2">Two</a><a href="#deep" id="t3">Deep</a></nav>'
    + '<article class="a4-help-body a4-help-prose" data-exphelp-content data-exphelp-collapsible="h2"><div class="a4-help-body">'
    + '<h1 id="top">Title</h1><p>intro</p>'
    + '<h2 id="one" class="a4-help-h">One <a class="a4-help-anchor" href="#one" aria-hidden="true" tabindex="-1">#</a></h2><p>a</p><pre><code>echo 1;</code></pre>'
    + '<h2 id="two" class="a4-help-h">Two <a class="a4-help-anchor" href="#two" aria-hidden="true" tabindex="-1">#</a></h2><h3 id="deep">Deep</h3><div class="a4-help-code" data-lang="php" data-label="PHP"><pre class="a4-help-pre" tabindex="0"><code class="language-php">$a = 1 &lt; 2;</code></pre></div>'
    + '<details class="a4-help-details" id="det"><summary>More</summary><p id="inner">hidden</p></details>'
    + '</div></article>'
    + '<a href="/admin/exphelp/viewer/(path)/doc/a.md" data-exphelp-prev rel="prev">Prev</a>'
    + '<a href="javascript:alert(1)" data-exphelp-next rel="next">Next</a></div>';

function setup(html, opts) {
    opts = opts || {};
    const document = createDocument(html);
    // the capture phase, which mini_dom leaves out: the document's capture listeners run before every other one
    const capture = [];
    const add = document.addEventListener;
    document.addEventListener = function (type, fn, useCapture) {
        if (useCapture === true || (useCapture && useCapture.capture)) { capture.push({ type, fn }); return; }
        add.call(document, type, fn);
    };
    const dispatch = document.dispatch;
    document.dispatch = function (type, target, props) {
        const pre = Object.assign({ type, target, defaultPrevented: false, ctrlKey: false, metaKey: false, altKey: false }, props || {});
        pre.preventDefault = () => { pre.defaultPrevented = true; };
        pre.stopPropagation = () => {};
        capture.filter((c) => c.type === type).forEach((c) => c.fn.call(document, pre));
        return dispatch.call(document, type, target, Object.assign({}, props || {}, { defaultPrevented: pre.defaultPrevented }));
    };
    // admin4.js: its own / shortcut to the header search, registered before exphelp.js, skips a prevented event
    if (opts.admin4) {
        add.call(document, 'keydown', (e) => {
            if (e.key !== '/' || e.defaultPrevented) return;
            e.preventDefault();
            document.getElementById('searchtext').focus();
        });
    }
    // what mini_dom leaves out and the script uses
    const proto = Object.getPrototypeOf(Object.getPrototypeOf(document.body));
    proto.insertBefore = function (child, ref) {
        if (!ref) return this.appendChild(child);
        if (child.parentNode) child.parentNode.removeChild(child);
        child.parentNode = this; this.childNodes.splice(this.childNodes.indexOf(ref), 0, child); return child;
    };
    const Element = Object.getPrototypeOf(document.body);
    Element.blur = function () { if (document.activeElement === this) document.activeElement = null; };
    Element.select = function () { this.selected = true; };
    const scrolled = [];
    Element.scrollIntoView = function () { scrolled.push(this.id); };
    const tops = opts.tops || {};
    Element.getBoundingClientRect = function () {
        const top = tops[this.id] === undefined ? 1000 : tops[this.id];
        return { top, bottom: top + 20, left: 0, right: 100, width: 100, height: 20 };
    };

    const timers = [];
    const requests = [];
    const copied = [];
    const assigned = [];
    const replaced = [];
    const winListeners = {};
    const sandbox = {
        document, console, JSON, Array, Object, String, Math, Error, Promise, encodeURIComponent, decodeURIComponent,
        setTimeout: (fn, ms) => { timers.push({ fn, ms }); return timers.length; },
        clearTimeout: (id) => { if (timers[id - 1]) timers[id - 1].fn = null; },
        requestAnimationFrame: (fn) => fn(),
        fetch: (url, init) => new Promise((resolve, reject) => { requests.push({ url, init, resolve, reject }); }),
        navigator: { clipboard: opts.noClipboard ? undefined : { writeText: (t) => { copied.push(t); return Promise.resolve(); } } },
        location: { hash: opts.hash || '', pathname: opts.pathname || '/admin/exphelp/dashboard', assign: (u) => assigned.push(u) },
        history: { replaceState: (a, b, h) => { replaced.push(h); if (String(h).charAt(0) === '#') sandbox.location.hash = h; } },
        addEventListener: (type, fn) => { (winListeners[type] = winListeners[type] || []).push(fn); },
    };
    if (opts.noFetch) delete sandbox.fetch;
    sandbox.window = sandbox;
    vm.createContext(sandbox);
    vm.runInContext(source, sandbox);
    const runTimers = (maxMs) => {
        let ran = 0;
        for (const t of timers.splice(0)) {
            if (!t.fn) continue;
            if (maxMs !== undefined && t.ms > maxMs) { timers.push(t); continue; }
            t.fn(); ran++;
        }
        return ran;
    };
    const flush = () => new Promise((r) => setImmediate(r));
    const type = async (value) => {
        const input = document.getElementById('q');
        input.value = value;
        document.dispatch('input', input, { bubbles: true });
        runTimers(500);
        await flush();
    };
    const key = (target, k) => document.dispatch('keydown', target, { key: k });
    const answer = async (i, data, ok) => {
        requests[i].resolve({ ok: ok !== false, status: ok === false ? 500 : 200, json: () => Promise.resolve(data) });
        await flush(); await flush(); await flush();
    };
    return { document, sandbox, requests, copied, assigned, replaced, scrolled, timers, runTimers, flush, type, key, answer, winListeners };
}

const RESULTS = {
    query: 'cache', terms: ['cache'], total: 3, relaxed: false,
    results: [
        { path: 'doc/a.md', url: '/admin/exphelp/viewer/(path)/doc/a.md#clear', title: 'Clearing <b>caches</b>', section: 'Clear', anchor: 'clear',
          snippet_parts: [['Run the ', false], ['cache', true], [' <img src=x onerror=alert(1)> tool', false]], audience: ['admin'], category: 'operations' },
        { path: 'doc/b.md', url: 'javascript:alert(1)', title: 'Evil' },
        { path: 'doc/c.md', url: '/admin/exphelp/viewer/(path)/doc/c.md', title: 'Fallback', snippet: 'a &lt;b&gt; <mark>cache</mark> &amp; more', audience: 'editor' },
    ],
    facets: { audience: { editor: 7, admin: 2 } },
};

const cases = [];
const out = (name, data) => cases.push(Object.assign({ name }, data));

(async () => {
    {
        const h = setup(SEARCH);
        const box = h.document.getElementById('res');
        h.document.getElementById('aud-editor').checked = true;
        await h.type('  cache ');
        const req = h.requests[0];
        await h.answer(0, RESULTS);
        const links = box.querySelectorAll('a');
        out('search', {
            requests: h.requests.length, url: req.url, accept: req.init.headers.Accept, hidden: box.hidden,
            links: links.map((a) => a.getAttribute('href')), titles: box.querySelectorAll('.a4-help-result__title').map((n) => n.textContent),
            marks: box.querySelectorAll('mark').map((n) => n.textContent), imgs: box.querySelectorAll('img').length,
            snippet: box.querySelectorAll('.a4-help-result__snippet').map((n) => n.textContent),
            tags: box.querySelectorAll('.a4-tag').map((n) => n.textContent),
            status: box.querySelector('[data-exphelp-status]').textContent,
            count: h.document.querySelector('[data-exphelp-count]').textContent,
            expanded: h.document.getElementById('q').getAttribute('aria-expanded'),
        });
    }
    {
        const h = setup(SEARCH);
        await h.type('cac');
        await h.type('cache');
        await h.answer(1, { total: 1, results: [{ url: '/new', title: 'New' }] });
        await h.answer(0, { total: 1, results: [{ url: '/old', title: 'Old' }] });
        const box = h.document.getElementById('res');
        out('stale answer ignored', { requests: h.requests.length, links: box.querySelectorAll('a').map((a) => a.getAttribute('href')), status: box.querySelector('[data-exphelp-status]').textContent });
    }
    {
        const h = setup(SEARCH);
        h.document.getElementById('q').value = 'x';
        await h.type('cache');
        await h.answer(0, RESULTS);
        h.key(h.document.body, '/');
        const focusInput = h.document.activeElement && h.document.activeElement.id;
        const input = h.document.getElementById('q');
        h.key(input, 'ArrowDown');
        const first = h.document.activeElement.getAttribute('href');
        h.key(h.document.activeElement, 'ArrowDown');
        const second = h.document.activeElement.getAttribute('href');
        h.key(h.document.activeElement, 'ArrowDown');
        const stays = h.document.activeElement.getAttribute('href');
        h.key(h.document.activeElement, 'ArrowUp');
        h.key(h.document.activeElement, 'ArrowUp');
        const back = h.document.activeElement.id;
        const enter = h.key(input, 'Enter');
        const esc1 = h.key(input, 'Escape');
        const box = h.document.getElementById('res');
        const afterEsc = { hidden: box.hidden, links: box.querySelectorAll('a').length, value: input.value };
        h.key(input, 'Escape');
        const value2 = input.value;
        out('keys', { focusInput, first, second, stays, back, assigned: h.assigned, enterPrevented: enter.defaultPrevented,
            escPrevented: esc1.defaultPrevented, afterEsc, value2,
            slashInInput: h.key(input, '/').defaultPrevented });
    }
    {
        const h = setup(SEARCH);
        await h.type('cache');
        // Enter before the answer: the form goes to the search page
        const early = h.key(h.document.getElementById('q'), 'Enter').defaultPrevented;
        await h.answer(0, {}, false);
        const box = h.document.getElementById('res');
        out('error', { early, hidden: box.hidden, status: box.querySelector('[data-exphelp-status]').textContent, links: box.querySelectorAll('a').length });
    }
    {
        const h = setup(SEARCH);
        await h.type('   ');
        out('empty query', { requests: h.requests.length, hidden: h.document.getElementById('res').hidden });
    }
    {
        const h = setup(SEARCH, { noFetch: true });
        await h.type('cache');
        out('no fetch', { status: h.document.querySelector('[data-exphelp-status]').textContent });
    }
    {
        const h = setup(SEARCH);
        await h.type('cache');
        h.document.querySelector('select').value = 'x';
        h.document.dispatch('change', h.document.querySelector('select'), {});
        await h.flush();
        out('filter change', { requests: h.requests.length, url: h.requests[1] && h.requests[1].url });
    }
    {
        const h = setup(VIEWER, { tops: { top: -400, one: -200, two: 50, deep: 300 } });
        const d = h.document;
        const article = d.querySelector('[data-exphelp-content]');
        const wraps = article.querySelectorAll('.a4-help-code');
        const buttons = article.querySelectorAll('[data-exphelp-copy]');
        d.dispatch('click', buttons[1], {});
        await h.flush();
        const label = buttons[1].textContent;
        h.runTimers();
        out('copy', { wraps: wraps.length, buttons: buttons.length, types: buttons.map((b) => b.getAttribute('type')),
            perWrap: wraps.map((w) => w.querySelectorAll('[data-exphelp-copy]').length),
            copied: h.copied, label, after: buttons[1].textContent, announced: d.querySelector('[data-exphelp-announce]').textContent });
    }
    {
        const h = setup(VIEWER, { noClipboard: true });
        const b = h.document.querySelector('[data-exphelp-copy]');
        h.document.dispatch('click', b, {});
        out('copy without clipboard', { label: b.textContent });
    }
    {
        const h = setup(VIEWER, { tops: { top: -400, one: -200, two: 50, deep: 300 } });
        const d = h.document;
        const toggles = d.querySelectorAll('[data-exphelp-collapse]');
        const sections = d.querySelectorAll('.a4-help-section');
        const before = toggles.map((b) => b.getAttribute('aria-expanded'));
        const sectionOneText = sections[0].textContent;
        d.dispatch('click', toggles[1], {});
        const closed = { expanded: toggles[1].getAttribute('aria-expanded'), hidden: sections[1].hidden, label: toggles[1].getAttribute('aria-label') };
        h.sandbox.location.hash = '#deep';
        (h.winListeners.hashchange || []).forEach((fn) => fn());
        h.sandbox.location.hash = '#inner';
        (h.winListeners.hashchange || []).forEach((fn) => fn());
        out('collapse', { toggles: toggles.length, before, sectionOneText, closed,
            reopened: { expanded: toggles[1].getAttribute('aria-expanded'), hidden: sections[1].hidden },
            detailsOpen: d.getElementById('det').hasAttribute('open'),
            controls: toggles.map((b) => b.getAttribute('aria-controls')) });
    }
    {
        const h = setup(VIEWER, { tops: { one: -200, two: 50, deep: 300 } });
        const d = h.document;
        const current = () => d.querySelectorAll('[data-exphelp-toc] a').filter((a) => a.getAttribute('aria-current') === 'location').map((a) => a.id);
        const first = current();
        const tops = { one: -500, two: -300, deep: 40 };
        Object.getPrototypeOf(d.body).getBoundingClientRect = function () { const t = tops[this.id] === undefined ? 1000 : tops[this.id]; return { top: t, bottom: t + 20 }; };
        (h.winListeners.scroll || []).forEach((fn) => fn());
        const second = current();
        out('scrollspy', { first, second, cls: d.getElementById('t3').classList.contains('is-current'), old: d.getElementById('t1').classList.contains('is-current') });
    }
    {
        const h = setup(VIEWER, { tops: { one: 40, two: 300, deep: 600 } });
        const d = h.document;
        h.key(d.body, 'j');
        h.key(d.body, 'k');
        const typingJ = h.key(d.getElementById('q'), 'j').defaultPrevented;
        const prev = h.key(d.body, '[').defaultPrevented;
        const next = h.key(d.body, ']').defaultPrevented;
        const ctrl = d.dispatch('keydown', d.body, { key: '/', ctrlKey: true }).defaultPrevented;
        out('page keys', { scrolled: h.scrolled, hash: h.sandbox.location.hash, typingJ, prev, next, assigned: h.assigned, ctrl });
    }
    {
        // the canonical address: the form sent with JavaScript, the search page kept in step, browse results
        const CANON = SEARCH.replace('data-exphelp-endpoint="/admin/exphelp/search"',
            'data-exphelp-endpoint="/admin/exphelp/search" data-exphelp-canonical-base="/admin/exphelp/search/"');
        const h = setup(CANON);
        const d = h.document;
        d.getElementById('q').value = ' a/b (c)  d+e ';
        d.getElementById('aud-editor').checked = true;
        d.querySelector('select').value = 'guides';
        const submit = d.dispatch('submit', d.querySelector('form'), {});
        const plain = setup(SEARCH);
        const plainSubmit = plain.document.dispatch('submit', plain.document.querySelector('form'), {});
        const empty = setup(CANON);
        empty.document.dispatch('submit', empty.document.querySelector('form'), {});

        // the dashboard: no address change; an empty field with a filter browses
        const dash = setup(CANON);
        dash.document.getElementById('aud-editor').checked = true;
        dash.document.dispatch('change', dash.document.getElementById('aud-editor'), {});
        await dash.flush();
        await dash.answer(0, { mode: 'browse', total: 2, results: [{ url: '/x', href: '/admin/exphelp/viewer/x.md', title: 'X', snippet_parts: [['Summary', false]] }],
            canonical_href: '/admin/exphelp/search/(audience)/editor' });
        const dashBox = dash.document.getElementById('res');

        // the search page itself: the address follows the search
        const page = setup(CANON, { pathname: '/admin/exphelp/search/(q)/old' });
        await page.type('cache');
        await page.answer(0, { total: 1, results: [{ href: '/y', title: 'Y' }], canonical_href: '/admin/exphelp/search/(q)/cache' });
        await page.type('cach');
        await page.answer(1, { total: 1, results: [{ href: '/y', title: 'Y' }], canonical_href: 'javascript:alert(1)' });
        await page.type('');
        out('canonical address', {
            submitPrevented: submit.defaultPrevented, assigned: h.assigned, plainPrevented: plainSubmit.defaultPrevented, emptyAssigned: empty.assigned,
            browseUrl: dash.requests[0] && dash.requests[0].url, browseLinks: dashBox.querySelectorAll('a').map((a) => a.getAttribute('href')),
            browseStatus: dashBox.querySelector('[data-exphelp-status]').textContent, dashReplaced: dash.replaced,
            pageReplaced: page.replaced,
        });
    }
    {
        // the admin header's search and its / shortcut (admin4.js) on the same page
        const page = '<form id="header"><input type="text" id="searchtext" name="SearchText" /></form>' + SEARCH;
        const h = setup(page, { admin4: true });
        const slash = h.key(h.document.body, '/');
        const help = h.document.activeElement && h.document.activeElement.id;
        const hidden = setup(page.replace('data-exphelp-search-input', 'data-exphelp-search-input disabled'), { admin4: true });
        hidden.key(hidden.document.body, '/');
        const dialog = setup('<div role="dialog"><button id="d">x</button></div>' + page, { admin4: true });
        dialog.key(dialog.document.getElementById('d'), '/');
        out('slash beats the header search', { prevented: slash.defaultPrevented, focused: help,
            disabledGoesToHeader: hidden.document.activeElement && hidden.document.activeElement.id,
            fromDialog: dialog.document.activeElement && dialog.document.activeElement.id });
    }
    {
        const h = setup('<div><p>no help here</p></div>');
        out('other pages untouched', { listeners: Object.keys(h.document.listeners).length, api: typeof h.sandbox.expHelp.init });
    }
    for (const c of cases) process.stdout.write(JSON.stringify(c) + '\n');
})().catch((e) => { process.stdout.write(JSON.stringify({ name: 'crash', error: String(e && e.stack || e) }) + '\n'); });
