// Runs design/admin4/javascript/page/content_job.js (the progress page of a content job) against mini_dom.js with a
// stub fetch: each case answers the status requests in its own way (a running job, 404, 403, the login page after a
// redirect, an HTML page, a network error, a hidden tab) and records the requests, the timers the script set and the
// message it shows. Prints one JSON line per case.
// Usage: node content_job.js <path to content_job.js>
'use strict';
const fs = require('fs');
const vm = require('vm');
const { createDocument } = require('./mini_dom.js');

const source = fs.readFileSync(process.argv[2], 'utf8');
const BOX = '<div id="exp-contentjob" data-status-url="/content/job/abc?json=1" data-state="running"'
    + ' data-text-counts="%done of %total" data-text-elapsed="%seconds s" data-text-opening="Opening in %seconds s"'
    + ' data-text-stopping="Stopping" data-text-waiting="Waiting" data-text-gone="Gone" data-text-signed-out="Signed out"'
    + ' data-states="Waiting|Running|Done|Failed|Cancelled">'
    + '<span class="a4-badge a4-content-job-state running">Running</span><span class="a4-content-job-counts"></span>'
    + '<span class="a4-content-job-elapsed"></span><div class="a4-meter a4-content-job-bar running"><span class="a4-pct-0"></span></div>'
    + '<p class="a4-content-job-message">start</p><div class="a4-content-job-done" hidden=""><a class="a4-content-job-open" href="/x">open</a>'
    + '<span class="a4-content-job-auto"></span></div><pre class="a4-content-job-log"></pre></div>';
const RUNNING = { ok: true, state: 'running', done: 1, total: 4, percent: 25, message: 'Working', batch: 1, started: 10, now: 12, log: '' };

function response(status, body, opts) {
    const o = opts || {};
    return {
        status, ok: status >= 200 && status < 300, redirected: !!o.redirected,
        headers: { get: (n) => (n.toLowerCase() === 'content-type' ? (o.type || 'application/json; charset=utf-8') : null) },
        json: () => (typeof body === 'string' ? Promise.reject(new SyntaxError('Unexpected token <')) : Promise.resolve(body)),
    };
}

function run(answers) {
    const document = createDocument(BOX);
    document.hidden = false;
    const log = { requests: 0, timers: [], reloads: 0 };
    const sandbox = {
        document, console,
        fetch: () => {
            const a = answers[Math.min(log.requests, answers.length - 1)];
            log.requests++;
            return a === 'network' ? Promise.reject(new TypeError('Failed to fetch')) : Promise.resolve(a);
        },
        setTimeout: (fn, ms) => { log.timers.push({ fn, ms }); return log.timers.length; },
        clearTimeout: () => {},
        location: { reload: () => { log.reloads++; }, set href(v) { log.href = v; } },
    };
    sandbox.window = sandbox;
    vm.createContext(sandbox);
    vm.runInContext(source, sandbox);
    const settle = async () => { for (let i = 0; i < 10; i++) await new Promise((r) => setImmediate(r)); };
    return {
        document, log, settle,
        message: () => document.querySelector('.a4-content-job-message').textContent,
        // fires the timers set so far that would start another request (not the done page's countdown)
        next: async () => { const t = log.timers.splice(0); t.forEach((x) => x.fn()); await settle(); },
        delays: () => log.timers.map((x) => x.ms),
    };
}
function out(name, value) { console.log(JSON.stringify(Object.assign({ name }, value))); }

(async () => {
    // a running job: the next request in two seconds
    {
        const t = run([response(200, RUNNING)]);
        await t.settle();
        out('running', { requests: t.log.requests, delays: t.delays(), message: t.message() });
    }
    // the job is gone (404) or not the user's (403): the message says so, and no further request
    for (const status of [404, 403]) {
        const t = run([response(status, { ok: false, error: status === 404 ? 'not found' : 'access denied' })]);
        await t.settle();
        await t.next();
        out('status ' + status, { requests: t.log.requests, delays: t.delays(), message: t.message(), stopped: t.document.getElementById('exp-contentjob').getAttribute('data-polling') });
    }
    // signed out: the request ends on the login page (a redirect), or an HTML page comes back in place of the JSON
    {
        const t = run([response(200, '<html>', { redirected: true, type: 'text/html; charset=utf-8' })]);
        await t.settle();
        await t.next();
        out('redirected', { requests: t.log.requests, delays: t.delays(), message: t.message() });
    }
    {
        const t = run([response(200, '<html>', { type: 'text/html; charset=utf-8' })]);
        await t.settle();
        await t.next();
        out('html', { requests: t.log.requests, delays: t.delays(), message: t.message() });
    }
    // a network error: waits longer each time (up to a minute), and after an answer polls every two seconds again
    {
        const t = run(['network', 'network', 'network', 'network', 'network', 'network', 'network', response(200, RUNNING)]);
        await t.settle();
        const delays = [];
        for (let i = 0; i < 7; i++) { delays.push(t.delays()); await t.next(); }
        out('network', { requests: t.log.requests, delays, after: t.delays(), message: t.message() });
    }
    // a server error (an HTML error page with status 500) is passing: the script tries again later
    {
        const t = run([response(500, '<html>', { type: 'text/html' }), response(200, RUNNING)]);
        await t.settle();
        const first = t.delays();
        await t.next();
        out('server error', { requests: t.log.requests, first, after: t.delays() });
    }
    // a hidden tab: no request while hidden, one as soon as it is shown again
    {
        const t = run([response(200, RUNNING)]);
        await t.settle();
        t.document.hidden = true;
        await t.next();
        const hidden = t.log.requests;
        t.document.hidden = false;
        t.document.dispatch('visibilitychange', t.document);
        await t.settle();
        const shown = t.log.requests;
        t.document.dispatch('visibilitychange', t.document);
        await t.settle();
        out('hidden', { hidden, shown, again: t.log.requests, delays: t.delays() });
    }
})();
