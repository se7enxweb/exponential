// Runs design/admin4/javascript/page/content_job_mode_choice.js (the "run now or in the background" choice) against
// mini_dom.js: a change of the choice is saved through window.a4Pref (stubbed here, a4_head.js has its own test), not
// by a request of its own (a GET of user/preferences/set, which redirects and carries no form token). Prints one
// JSON line per case.
// Usage: node content_job_mode_choice.js <path to content_job_mode_choice.js>
'use strict';
const fs = require('fs');
const vm = require('vm');
const { createDocument } = require('./mini_dom.js');

const source = fs.readFileSync(process.argv[2], 'utf8');
const SET = '<fieldset class="a4-content-job-mode" data-prefs="/user/preferences/set" data-op="remove" data-form="browse">'
    + '<input type="radio" name="ContentJobMode" id="job" value="job" checked="checked" />'
    + '<input type="radio" name="ContentJobMode" id="now" value="now" /></fieldset>'
    + '<form name="browse" id="browse"></form>';

function run(withPref) {
    const document = createDocument(SET);
    const log = { fetches: [], prefs: [] };
    const sandbox = { document, console, fetch: (u, o) => { log.fetches.push([u, o]); return Promise.resolve({ ok: true }); } };
    sandbox.window = sandbox;
    if (withPref) sandbox.a4Pref = { set: (name, value) => { log.prefs.push([name, value]); } };
    vm.createContext(sandbox);
    vm.runInContext(source, sandbox);
    vm.runInContext(source, sandbox); // loaded twice: still one listener
    return { document, log };
}
function out(name, value) { console.log(JSON.stringify(Object.assign({ name }, value))); }

{
    const t = run(true);
    const now = t.document.getElementById('now');
    t.document.getElementById('job').checked = false;
    now.checked = true;
    t.document.dispatch('change', now);
    out('change', { prefs: t.log.prefs, fetches: t.log.fetches.length });
}
{
    const t = run(false);
    const now = t.document.getElementById('now');
    t.document.dispatch('change', now);
    out('no a4Pref', { fetches: t.log.fetches.length });
}
