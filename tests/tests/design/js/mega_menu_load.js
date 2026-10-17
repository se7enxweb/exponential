// Runs design/admin4/javascript/a4_mega_menu.js against mini_dom.js for the load of the panel's links: the header's
// tab with its button and panel as page_topmenuitem.tpl draws them, fetch answering from a list of planned answers
// (an HTTP status with a body, a network error, an abort, no answer at all), and timers that only move when told.
// Prints one JSON line per scenario.
// Usage: node mega_menu_load.js <path to a4_mega_menu.js>
'use strict';
const fs = require('fs');
const vm = require('vm');
const { createDocument } = require('./mini_dom.js');

const source = fs.readFileSync(process.argv[2], 'utf8');

const HEADER = '<div id="header"><ul class="navbar-bar"><li class="a4-mega-item">'
    + '<a href="/admin/setup/menu">Setup</a>'
    + '<button type="button" class="a4-mega-toggle" aria-expanded="false" aria-controls="a4-mega-setup" data-a4-mega-src="/admin/user/megamenu/setup"></button>'
    + '<div id="a4-mega-setup" class="a4-mega-panel" hidden data-a4-mega-error="ERROR" data-a4-mega-retry="TRY AGAIN"'
    + ' data-a4-mega-signed-out="SIGNED OUT" data-a4-mega-signin="SIGN IN" data-a4-mega-login="/admin/user/login">'
    + '<div class="a4-mega-body" data-a4-mega-body><p class="a4-mega-status" role="status">LOADING</p></div>'
    + '<p class="a4-mega-all"><a href="/admin/setup/menu">Go to Setup</a></p>'
    + '</div></li></ul></div><a id="outside" href="/admin/content/dashboard">elsewhere</a>';

const FRAGMENT = '<div class="a4-mega-groups" data-a4-mega-groups data-a4-mega-fragment="setup">'
    + '<section class="a4-mega-group"><ul class="a4-mega-links"><li class="a4-mega-link"><a href="/admin/setup/cache">Caches</a></li></ul></section></div>';
const LOGIN_PAGE = '<html><body><form method="post" action="/admin/user/login"><input type="text" name="Login" /></form></body></html>';

function run(plan, options) {
    const opts = Object.assign({ scripts: 1 }, options || {});
    const document = createDocument(HEADER);
    let now = 0;
    let timers = [];
    const requests = [];
    const answers = plan.slice();
    const windowListeners = {};
    const window = {
        document,
        location: { pathname: '/admin/content/dashboard' },
        matchMedia: () => ({ matches: true, addEventListener() {} }),
        setTimeout(fn, ms) { const t = { fn, at: now + (ms || 0) }; timers.push(t); return t; },
        clearTimeout(t) { timers = timers.filter((x) => x !== t); },
        addEventListener(type, fn) { (windowListeners[type] = windowListeners[type] || []).push(fn); },
        fire(type) { (windowListeners[type] || []).forEach((fn) => fn({ type })); },
        AbortController: function () {
            const signal = { aborted: false, listeners: [] };
            this.signal = signal;
            this.abort = () => { signal.aborted = true; signal.listeners.forEach((fn) => fn()); };
        },
        fetch(url, init) {
            requests.push({ url, init });
            const next = answers.length ? answers.shift() : { status: 200, body: FRAGMENT };
            const abortError = () => Object.assign(new Error('The operation was aborted.'), { name: 'AbortError' });
            return new Promise((resolve, reject) => {
                if (init && init.signal) init.signal.listeners.push(() => reject(abortError()));
                if (next === 'hang') return;
                if (next === 'network') { reject(new TypeError('Failed to fetch')); return; }
                if (next === 'abort') { reject(abortError()); return; }
                // an answer that comes only when the scenario says so
                if (next === 'later') { run.later = () => resolve(response({ status: 200, body: FRAGMENT })); return; }
                // stopped by the browser only when the scenario says so (the page being left)
                if (next === 'abort later') { run.abortLater = () => reject(abortError()); return; }
                if (next === 'network later') { run.networkLater = () => reject(new TypeError('NetworkError when attempting to fetch resource.')); return; }
                resolve(response(next));
            });
        },
    };
    function response(a) {
        return {
            status: a.status,
            ok: a.status >= 200 && a.status < 300,
            redirected: !!a.redirected,
            headers: { get: (name) => (name.toLowerCase() === 'x-exp-mega' ? (a.verdict || null) : null) },
            url: a.url || 'https://example.test/admin/user/megamenu/setup',
            text: () => Promise.resolve(a.body || ''),
        };
    }
    const sandbox = { window, document, console, Promise, TypeError, Error };
    vm.createContext(sandbox);
    for (let i = 0; i < opts.scripts; i++) vm.runInContext(source, sandbox);

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
    const $ = (sel) => document.querySelector(sel);
    return {
        document, window, requests, advance, flush,
        click: async (el) => { document.dispatch('click', el); await flush(); },
        toggle: () => $('.a4-mega-toggle'),
        state() {
            const panel = $('#a4-mega-setup');
            const body = panel.querySelector('[data-a4-mega-body]');
            const status = body.querySelector('.a4-mega-status');
            const retry = body.querySelector('[data-a4-mega-retry-button]');
            const signin = body.querySelector('.a4-mega-actions a');
            return {
                requests: requests.length,
                expanded: $('.a4-mega-toggle').getAttribute('aria-expanded'),
                open: !panel.hidden,
                busy: panel.getAttribute('aria-busy'),
                menu: !!body.querySelector('[data-a4-mega-fragment]'),
                status: status ? status.textContent : null,
                retry: retry ? retry.textContent : null,
                retryDisabled: retry ? retry.disabled : null,
                signin: signin ? { text: signin.textContent, href: signin.getAttribute('href') } : null,
                goTo: !!panel.querySelector('.a4-mega-all a[href="/admin/setup/menu"]'),
                bound: $('.a4-mega-item').getAttribute('data-a4-mega-bound') !== null,
            };
        },
    };
}

function out(name, value) { process.stdout.write(JSON.stringify(Object.assign({ name }, value)) + '\n'); }

(async () => {
    // success: one request with the session's cookie, an HTML Accept and X-Requested-With, never from the HTTP cache
    {
        const t = run([{ status: 200, body: FRAGMENT }]);
        await t.click(t.toggle());
        await t.advance(0);
        const init = t.requests[0].init;
        out('success', Object.assign(t.state(), {
            url: t.requests[0].url, credentials: init.credentials, cache: init.cache, accept: init.headers.Accept,
            xrw: init.headers['X-Requested-With'], signal: !!init.signal,
        }));
    }
    // success is kept for the page: closing and opening again asks nothing more
    {
        const t = run([{ status: 200, body: FRAGMENT }]);
        await t.click(t.toggle());
        await t.advance(0);
        await t.click(t.toggle());
        await t.click(t.toggle());
        await t.advance(0);
        out('success is cached', t.state());
    }
    // a request the browser stops while the page is left (a link followed): no error, and the next opening loads again
    {
        const t = run(['abort later']);
        await t.click(t.toggle());
        t.window.fire('pagehide');
        run.abortLater();
        await t.advance(5000);
        const during = t.state();
        t.window.fire('pageshow');
        await t.click(t.toggle());
        await t.click(t.toggle());
        await t.advance(0);
        out('abort is no error', { during, after: t.state() });
    }
    // Firefox: another link followed while the panel loads; beforeunload, then the fetch fails with a network error
    // while the next page loads: no error, no second request. Back in use (a stay on a leave prompt): loads again
    {
        const t = run(['network later']);
        await t.click(t.toggle());
        t.window.fire('beforeunload');
        run.networkLater();
        await t.advance(5000);
        const during = t.state();
        t.document.dispatch('pointerdown', t.toggle());
        await t.click(t.toggle());
        await t.click(t.toggle());
        await t.advance(0);
        out('navigation network error is no error', { during, after: t.state() });
    }
    // an aborted request on a page still in use is tried once more, not shown as an error
    {
        const t = run(['abort', { status: 200, body: FRAGMENT }]);
        await t.click(t.toggle());
        await t.advance(600);
        out('abort is tried again', t.state());
    }
    // closing the panel while it loads cancels nothing: the opening after it waits for the same request
    {
        const t = run(['later']);
        await t.click(t.toggle());
        await t.click(t.toggle());
        const closed = t.state();
        await t.click(t.toggle());
        run.later();
        await t.advance(0);
        out('in-flight request is reused', { closed, after: t.state() });
    }
    // a server error, then the menu: tried once more after a short pause, no error shown
    {
        const t = run([{ status: 503, body: 'busy' }, { status: 200, body: FRAGMENT }]);
        await t.click(t.toggle());
        await t.advance(599);
        const waiting = t.state();
        await t.advance(1);
        out('retry then success', { waiting, after: t.state() });
    }
    // no connection twice: the error with "Try again", the link to the tab's page still there; "Try again" loads it
    // while the panel stays open
    {
        const t = run(['network', 'network', { status: 200, body: FRAGMENT }]);
        await t.click(t.toggle());
        await t.advance(600);
        const failed = t.state();
        t.document.dispatch('click', t.document.querySelector('[data-a4-mega-retry-button]'));
        const clicked = t.state();
        await t.advance(0);
        out('failure offers try again', { failed, clicked, after: t.state() });
    }
    // no answer: given up after 10 s, tried once more
    {
        const t = run(['hang', { status: 200, body: FRAGMENT }]);
        await t.click(t.toggle());
        await t.advance(10600);
        out('timeout then success', t.state());
    }
    // 403 (and 401): the user is signed out; no second request, a link to sign in instead of the error
    for (const code of [403, 401]) {
        const t = run([{ status: code, body: 'denied' }]);
        await t.click(t.toggle());
        await t.advance(5000);
        out('signed out ' + code, t.state());
    }
    // the view's own verdicts: 403 signed-out is the signed-out state; 403 denied (a signed-in user refused the tab) is
    // a failure, not "signed out"
    {
        const t = run([{ status: 403, verdict: 'signed-out' }]);
        await t.click(t.toggle());
        await t.advance(5000);
        out('signed out verdict', t.state());
    }
    {
        const t = run([{ status: 403, verdict: 'denied' }]);
        await t.click(t.toggle());
        await t.advance(5000);
        out('denied verdict is no sign-in', t.state());
    }
    // a redirect to the sign-in page, or a sign-in page in place of the menu: signed out
    {
        const t = run([{ status: 200, body: LOGIN_PAGE, redirected: true, url: 'https://example.test/admin/user/login' }]);
        await t.click(t.toggle());
        await t.advance(5000);
        out('signed out redirect', t.state());
    }
    {
        const t = run([{ status: 200, body: LOGIN_PAGE }]);
        await t.click(t.toggle());
        await t.advance(5000);
        out('signed out page', t.state());
    }
    // an answer without the fragment's marker is not the menu: tried once more, then the error
    {
        const t = run([{ status: 200, body: '<html><body><p>Something else</p></body></html>' }, { status: 200, body: '<p>still not</p>' }]);
        await t.click(t.toggle());
        await t.advance(600);
        out('no marker is no menu', t.state());
    }
    // 404: a failure for good, not tried again by itself
    {
        const t = run([{ status: 404, body: 'not here' }]);
        await t.click(t.toggle());
        await t.advance(5000);
        out('not found is not retried', t.state());
    }
    // a failure shown gives way to the loading line when the panel is opened again
    {
        const t = run([{ status: 404 }, 'later']);
        await t.click(t.toggle());
        await t.advance(0);
        await t.click(t.toggle());
        await t.click(t.toggle());
        out('reopening after a failure loads again', t.state());
    }
    // the script run twice on the page: bound once, one click opens (and does not close it again), one request
    {
        const t = run([{ status: 200, body: FRAGMENT }], { scripts: 2 });
        await t.click(t.toggle());
        await t.advance(0);
        out('double init', t.state());
    }
})();
