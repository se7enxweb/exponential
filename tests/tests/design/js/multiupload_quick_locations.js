// Runs the quick location buttons of ezmultiupload's upload page (ezmultiupload/quick_locations.tpl) against the
// mini DOM (mini_dom.js): the new page of admin4, admin4l and adminui (expmultiupload.js, upload_body.tpl) and the
// classic page (ezmultiupload_upload.js, design/standard ezmultiupload/upload.tpl, on a small jQuery-like stand-in
// for Exp.ready). The service's check answers with the location asked about. Prints one line per check, "PASS ..."
// or "FAIL ...", and a last line "PASS" or "FAIL".
// Usage: node multiupload_quick_locations.js [path to expmultiupload.js] [path to ezmultiupload_upload.js]
// (by default the files of extension/ezmultiupload in the root this test lies in)
'use strict';
const fs = require('fs');
const path = require('path');
const vm = require('vm');
const { createDocument } = require('./mini_dom.js');

const root = path.resolve(__dirname, '../../../..');
const newScript = process.argv[2] || path.join(root, 'extension/ezmultiupload/design/standard/javascript/expmultiupload.js');
const classicScript = process.argv[3] || path.join(path.dirname(newScript), 'ezmultiupload_upload.js');

let failed = 0;
function check(name, ok, detail) {
    if (!ok) { failed++; }
    process.stdout.write((ok ? 'PASS ' : 'FAIL ') + name + (ok || detail === undefined ? '' : ' (got ' + JSON.stringify(detail) + ')') + '\n');
}
const wait = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

// the locations of the buttons, as expMultiuploadService::locationInfo() gives them
const LOC = {
    2: { id: 2, name: 'Websites', pathText: 'Websites', url: '/content/view/full/2', allowed: true, reason: '', pathIds: [2] },
    43: { id: 43, name: 'Media', pathText: 'Media', url: '/content/view/full/43', allowed: true, reason: '', pathIds: [43] },
    51: { id: 51, name: 'Images', pathText: 'Media / Images', url: '/content/view/full/51', allowed: true, reason: '', pathIds: [43, 51] },
    52: { id: 52, name: 'Files', pathText: 'Media / Files', url: '/content/view/full/52', allowed: true, reason: '', pathIds: [43, 52] },
    89: { id: 89, name: 'Site A', pathText: 'Websites / Site A', url: '/content/view/full/89', allowed: true, reason: '', pathIds: [2, 89] },
};
const WITHIN = '(contains the target location)';

function attr(value) { return String(value).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }
function button(id, current) {
    const l = LOC[id];
    return '<button type="button" class="a4-btn a4-btn-small" data-exp-mu-quick-id="' + id + '" data-exp-mu-quick-location="'
        + attr(JSON.stringify(l)) + '" aria-pressed="' + (current ? 'true' : 'false') + '" title="' + attr(l.pathText) + '">'
        + l.name + '<span class="a4-sr-only" data-exp-mu-within></span></button>';
}
// quick_locations.tpl with Media (43) as the target
function quickMarkup(more) {
    return '<div class="a4-toolbar a4-quickpick" id="exp-mu-quick" data-exp-mu-within-text="' + WITHIN + '">'
        + '<div class="a4-row" role="group" aria-labelledby="exp-mu-quick-presets"><span class="a4-quickpick-label" id="exp-mu-quick-presets">Quick locations</span>'
        + button(2) + button(43, true) + button(51) + button(52) + '</div>'
        + '<div class="a4-row" role="group" aria-labelledby="exp-mu-quick-row-2"><span class="a4-quickpick-label" id="exp-mu-quick-row-2">Websites</span>'
        + button(89) + (more ? '<button type="button" class="a4-btn a4-btn-small a4-btn-ghost" data-exp-mu-quick-more="2" aria-label="Choose another location in Websites">More...</button>' : '')
        + '</div></div>';
}

// a few DOM members mini_dom.js does not have
function patch(doc) {
    const proto = Object.getPrototypeOf(doc.createElement('div'));
    if (!('tBodies' in proto)) { Object.defineProperty(proto, 'tBodies', { get() { return this.querySelectorAll('tbody'); } }); }
    doc.createTextNode = (text) => { const s = doc.createElement('span'); s.textContent = text; return s.firstChild; };
    doc.documentElement.lang = 'en';
}

function quick(doc, id) { return doc.querySelector('[data-exp-mu-quick-id="' + id + '"]'); }
function pressed(doc) { return doc.querySelectorAll('[data-exp-mu-quick-location]').filter((b) => b.getAttribute('aria-pressed') === 'true').map((b) => b.getAttribute('data-exp-mu-quick-id')); }
function within(doc) { return doc.querySelectorAll('[data-exp-mu-quick-location]').filter((b) => b.classList.contains('is-within')).map((b) => b.getAttribute('data-exp-mu-quick-id')); }
function sr(doc, id) { return quick(doc, id).querySelector('[data-exp-mu-within]').textContent; }

// ---- the new page (expmultiupload.js) --------------------------------------------------------------------------

async function newPage() {
    const doc = createDocument(
        '<div class="exp-mu" id="exp-mu" data-exp-mu>'
        + '<script type="application/json" id="exp-mu-config"></script>'
        + '<section class="a4-card"><div class="a4-card-body"><p>'
        + '<a id="exp-mu-location-link" href="/content/view/full/43">Media</a>'
        + '<span class="a4-badge is-ok" id="exp-mu-location-state">Uploads allowed</span></p>'
        + '<p class="a4-notice is-bad" id="exp-mu-location-reason" role="alert" hidden></p>'
        + '<input type="hidden" id="exp-mu-location-id" name="ParentNodeID" value="43" />'
        + quickMarkup(true)
        + '</div><div class="a4-card-foot"><button type="button" class="button" id="exp-mu-change-location">Change location...</button>'
        + '<button type="button" class="button" id="exp-mu-last-location" hidden></button></div></section>'
        + '<div class="exp-mu-drop" id="exp-mu-drop"><button type="button" class="button" id="exp-mu-pick-files">Choose files...</button>'
        + '<button type="button" class="button" id="exp-mu-pick-folder" hidden>Choose a folder...</button>'
        + '<input type="file" id="exp-mu-files" multiple hidden /><input type="file" id="exp-mu-folder" multiple hidden /></div>'
        + '<label class="a4-check"><input type="checkbox" id="exp-mu-draft" /></label>'
        + '<label class="a4-check"><input type="checkbox" id="exp-mu-zip-import" /></label>'
        + '<fieldset id="exp-mu-zip-mode"><input type="radio" name="ExpMuZipMode" value="keep" /><input type="radio" name="ExpMuZipMode" value="flat" /></fieldset>'
        + '<p id="exp-mu-queue-count">0</p><p id="exp-mu-queue-empty"></p>'
        + '<div><table id="exp-mu-queue"><tbody></tbody></table></div>'
        + '<div id="exp-mu-overall" hidden><div id="exp-mu-overall-meter"></div></div>'
        + '<input type="button" id="exp-mu-start" data-exp-mu-primary="1" /><input type="button" id="exp-mu-cancel-all" />'
        + '<input type="button" id="exp-mu-retry-all" /><input type="button" id="exp-mu-clear" />'
        + '<h2 id="exp-mu-results-title">Uploaded</h2><p id="exp-mu-results-count">0</p><p id="exp-mu-results-empty"></p>'
        + '<div><table id="exp-mu-results"><tbody></tbody></table></div>'
        + '<input type="checkbox" id="exp-mu-select-all" /><span id="exp-mu-selected"></span>'
        + '<input type="button" id="exp-mu-move" /><input type="button" id="exp-mu-place" /><input type="button" id="exp-mu-remove" />'
        + '<p class="a4-sr-only" id="exp-mu-status" role="status"></p>'
        + '</div>');
    patch(doc);
    doc.getElementById('exp-mu-config').textContent = JSON.stringify({
        location: LOC[43], parallel: 3, serviceUrl: '/ezmultiupload/service', uploadUrl: '/ezmultiupload/upload',
        preferenceUrl: '/user/preferences/set_and_exit', preference: 'ezmultiupload_location', zipEnabled: true,
        zipImport: false, zipMode: 'keep', zipPreference: 'ezmultiupload_zip_mode', zipImportPreference: 'ezmultiupload_zip_import',
        remembered: null, suggested: null,
        texts: { locationChanged: 'The location is now %location.', useLast: 'Use the last location: %location', blocked: 'No uploads' },
    });

    const checks = [], prefs = [];
    const fetch = (url, opts) => {
        const body = opts && opts.body;
        const action = body && body.get ? body.get('Action') : '';
        const id = body && body.get ? Number(body.get('Location')) : 0;
        checks.push({ url, action, id });
        const answer = action === 'check' ? { ok: true, location: LOC[id], files: [] } : { ok: true };
        return Promise.resolve({ ok: true, status: 200, url, text: () => Promise.resolve(JSON.stringify(answer)) });
    };
    const sandbox = {
        document: doc, fetch, URLSearchParams, Promise, Intl, setTimeout, clearTimeout, console,
        XMLHttpRequest: function () { throw new Error('no upload expected'); }, FormData: function () {},
        a4Pref: { signedIn: true, set(name, value) { prefs.push([name, value]); } },
        Exp: { token: () => 'token' },
    };
    sandbox.window = sandbox;
    vm.createContext(sandbox);
    try {
        vm.runInContext(fs.readFileSync(newScript, 'utf8'), sandbox, { filename: newScript });
    } catch (e) {
        check('new page: the script runs', false, String(e && e.stack));
        return;
    }
    await wait(250);
    const page = doc.getElementById('exp-mu');
    check('new page: the script starts', page.getAttribute('data-exp-mu-state') === 'ready', page.getAttribute('data-exp-mu-state'));
    check('new page: the target is checked on the server', checks.some((c) => c.action === 'check' && c.id === 43), checks);

    // the initial state: Media is the target
    check('new page: initially only Media is pressed', JSON.stringify(pressed(doc)) === '["43"]', pressed(doc));
    check('new page: initially no button is within', within(doc).length === 0, within(doc));
    check('new page: initially the hidden field holds 43', doc.getElementById('exp-mu-location-id').value === '43', doc.getElementById('exp-mu-location-id').value);

    // the buttons are real buttons with an accessible text
    const buttons = doc.querySelectorAll('[data-exp-mu-quick-location]');
    check('new page: five quick location buttons', buttons.length === 5, buttons.length);
    check('keyboard: every quick location is a <button type="button">', buttons.every((b) => b.tagName === 'BUTTON' && b.getAttribute('type') === 'button'),
          buttons.map((b) => b.tagName + ':' + b.getAttribute('type')));
    check('keyboard: every quick location has an accessible text', buttons.every((b) => b.textContent.trim() !== '' || (b.getAttribute('aria-label') || '').trim() !== ''),
          buttons.map((b) => b.textContent));
    check('keyboard: no quick location is disabled', buttons.every((b) => !b.disabled), buttons.map((b) => b.disabled));

    // Images
    quick(doc, 51).click();
    await wait(250);
    check('new page: after Images only Images is pressed', JSON.stringify(pressed(doc)) === '["51"]', pressed(doc));
    check('new page: after Images Media is within', JSON.stringify(within(doc)) === '["43"]', within(doc));
    check('new page: after Images the screen reader text of Media', sr(doc, 43) === ' ' + WITHIN, sr(doc, 43));
    check('new page: after Images no other screen reader text', [2, 51, 52, 89].every((id) => sr(doc, id) === ''), [2, 51, 52, 89].map((id) => sr(doc, id)));
    check('new page: after Images the hidden field holds 51', doc.getElementById('exp-mu-location-id').value === '51', doc.getElementById('exp-mu-location-id').value);
    check('new page: after Images the link shows "Media / Images"', doc.getElementById('exp-mu-location-link').textContent === 'Media / Images',
          doc.getElementById('exp-mu-location-link').textContent);
    check('new page: after Images the link goes to Images', doc.getElementById('exp-mu-location-link').getAttribute('href') === '/content/view/full/51',
          doc.getElementById('exp-mu-location-link').getAttribute('href'));
    check('new page: after Images the change is announced', doc.getElementById('exp-mu-status').textContent === 'The location is now Media / Images.',
          doc.getElementById('exp-mu-status').textContent);
    check('new page: after Images the location is remembered', prefs.some((p) => p[0] === 'ezmultiupload_location' && p[1] === '51'), prefs);
    check('new page: after Images Media is offered as the last location', !doc.getElementById('exp-mu-last-location').hidden
          && doc.getElementById('exp-mu-last-location').textContent === 'Use the last location: Media', doc.getElementById('exp-mu-last-location').textContent);
    check('new page: after Images the new target is checked on the server', checks.some((c) => c.action === 'check' && c.id === 51), checks);

    // the current one again: nothing changes
    const before = checks.length;
    quick(doc, 51).click();
    await wait(250);
    check('new page: Images again keeps Images pressed', JSON.stringify(pressed(doc)) === '["51"]', pressed(doc));
    check('new page: Images again asks the server nothing', checks.length === before, checks.slice(before));

    // a site
    quick(doc, 89).click();
    await wait(250);
    check('new page: after a site only the site is pressed', JSON.stringify(pressed(doc)) === '["89"]', pressed(doc));
    check('new page: after a site Websites is within', JSON.stringify(within(doc)) === '["2"]', within(doc));
    check('new page: after a site Media is no longer within', sr(doc, 43) === '' && !quick(doc, 43).classList.contains('is-within'), sr(doc, 43));
    check('new page: after a site the screen reader text of Websites', sr(doc, 2) === ' ' + WITHIN, sr(doc, 2));
    check('new page: after a site the hidden field holds 89', doc.getElementById('exp-mu-location-id').value === '89', doc.getElementById('exp-mu-location-id').value);
    check('new page: after a site the link shows "Websites / Site A"', doc.getElementById('exp-mu-location-link').textContent === 'Websites / Site A',
          doc.getElementById('exp-mu-location-link').textContent);
}

// ---- the classic page (ezmultiupload_upload.js) -----------------------------------------------------------------

// as much of jQuery as the classic script uses
function jq(doc) {
    const data = new WeakMap();
    function Q(list) { this.length = list.length; list.forEach((e, i) => { this[i] = e; }); this.list = list; }
    function $(sel) {
        if (sel instanceof Q) { return sel; }
        if (typeof sel === 'string' && sel.trim()[0] === '<') { const d = doc.createElement('div'); d.innerHTML = sel; return new Q(d.children); }
        if (typeof sel === 'string') { return new Q(doc.querySelectorAll(sel)); }
        return new Q(sel ? [sel] : []);
    }
    Q.prototype = {
        each(fn) { this.list.forEach((e, i) => fn.call(e, i, e)); return this; },
        attr(name, value) { if (value === undefined) { return this.list[0] ? this.list[0].getAttribute(name) : undefined; } this.list.forEach((e) => e.setAttribute(name, value)); return this; },
        text(value) { if (value === undefined) { return this.list.map((e) => e.textContent).join(''); } this.list.forEach((e) => { e.textContent = value; }); return this; },
        html(value) { if (value === undefined) { return this.list[0] ? this.list[0].innerHTML : ''; } this.list.forEach((e) => { e.innerHTML = value; }); return this; },
        find(sel) { return new Q([].concat.apply([], this.list.map((e) => e.querySelectorAll(sel)))); },
        toggleClass(c, on) { this.list.forEach((e) => e.classList.toggle(c, on)); return this; },
        prop(name, value) { if (value === undefined) { return this.list[0] ? this.list[0][name] : undefined; } this.list.forEach((e) => { e[name] = value; }); return this; },
        css(name, value) { if (typeof name === 'string' && value === undefined) { return '1'; } return this; },
        animate() { return this; },
        stop() { return this; },
        append(q) { const first = this.list[0]; $(q).list.forEach((e) => first.appendChild(e)); return this; },
        data(key) { return this.list[0] ? (data.get(this.list[0]) || {})[key] : undefined; },
        on(type, sel, fn) {
            if (typeof sel === 'function') { fn = sel; sel = null; }
            this.list.forEach((el) => el.addEventListener(type, (e) => {
                if (!sel) { fn.call(el, e); return; }
                const m = e.target.closest ? e.target.closest(sel) : null;
                if (m && el.contains(m)) { fn.call(m, e); }
            }));
            return this;
        },
        expUpload(options) {
            this.list.forEach((el) => {
                const up = { options: Object.assign({}, options), enabled: true, enable() { this.enabled = true; }, disable() { this.enabled = false; },
                             cancel() {}, progress() { return { percent: 0 }; } };
                data.set(el, Object.assign(data.get(el) || {}, { expUpload: up }));
            });
            return this;
        },
    };
    $.fn = Q.prototype;
    return $;
}

function classicPage() {
    const conf = { uploadURL: '/ezmultiupload/upload/43', uploadVars: {}, locationChanged: 'The location is now %location.', texts: {} };
    const doc = createDocument(
        '<p>The files are uploaded to <a id="ezmultiupload-location" href="/Media">Media</a></p>'
        + quickMarkup(false)
        + '<div id="uploadButtonOverlay" data-ezmultiupload-conf="' + attr(JSON.stringify(conf)) + '"></div>'
        + '<button id="cancelUploadButton" type="button">Cancel</button>'
        + '<div id="multiuploadProgress"><span id="multiuploadProgressFile"></span><span id="multiuploadProgressFileName"></span>'
        + '<p id="multiuploadProgressMessage"></p><div id="multiuploadProgressBarOutline"><div id="multiuploadProgressBar"></div></div></div>'
        + '<div id="thumbnails"></div><p class="a4-sr-only" id="ezmultiupload-status" role="status" aria-live="polite"></p>');
    patch(doc);
    const $ = jq(doc);
    const sandbox = { document: doc, console, Exp: { ready(fn) { fn($); }, reducedMotion: true } };
    sandbox.window = sandbox;
    vm.createContext(sandbox);
    try {
        vm.runInContext(fs.readFileSync(classicScript, 'utf8'), sandbox, { filename: classicScript });
    } catch (e) {
        check('classic page: the script runs', false, String(e && e.stack));
        return;
    }
    const up = $('#uploadButtonOverlay').data('expUpload');
    check('classic page: the uploader is set up on the configured address', !!up && up.options.url === '/ezmultiupload/upload/43', up && up.options.url);
    if (!up) { return; }

    quick(doc, 51).click();
    check('classic page: after Images files go to /ezmultiupload/upload/51', up.options.url === '/ezmultiupload/upload/51', up.options.url);
    check('classic page: after Images the location shows "Media / Images"', doc.getElementById('ezmultiupload-location').textContent === 'Media / Images',
          doc.getElementById('ezmultiupload-location').textContent);
    check('classic page: after Images the location links to Images', doc.getElementById('ezmultiupload-location').getAttribute('href') === '/content/view/full/51',
          doc.getElementById('ezmultiupload-location').getAttribute('href'));
    check('classic page: after Images only Images is pressed', JSON.stringify(pressed(doc)) === '["51"]', pressed(doc));
    check('classic page: after Images Media is within', JSON.stringify(within(doc)) === '["43"]', within(doc));
    check('classic page: after Images the screen reader text of Media', sr(doc, 43) === ' ' + WITHIN, sr(doc, 43));
    check('classic page: after Images the change is announced', doc.getElementById('ezmultiupload-status').textContent === 'The location is now Media / Images.',
          doc.getElementById('ezmultiupload-status').textContent);

    // a click on the button's inner span counts as a click on the button (delegated)
    doc.dispatch('click', quick(doc, 89).querySelector('[data-exp-mu-within]'));
    check('classic page: after a site (click on its inner span) files go to /ezmultiupload/upload/89', up.options.url === '/ezmultiupload/upload/89', up.options.url);
    check('classic page: after a site Websites is within', JSON.stringify(within(doc)) === '["2"]', within(doc));
    check('classic page: after a site the screen reader text of Websites', sr(doc, 2) === ' ' + WITHIN && sr(doc, 43) === '', [sr(doc, 2), sr(doc, 43)]);

    // while files are being sent the target stays
    up.disable();
    quick(doc, 52).click();
    check('classic page: during an upload a click changes nothing', up.options.url === '/ezmultiupload/upload/89' && JSON.stringify(pressed(doc)) === '["89"]',
          [up.options.url, pressed(doc)]);
    up.enable();
}

(async () => {
    try {
        await newPage();
    } catch (e) {
        check('new page: no error', false, String(e && e.stack));
    }
    if (fs.existsSync(classicScript)) {
        try {
            classicPage();
        } catch (e) {
            check('classic page: no error', false, String(e && e.stack));
        }
    } else {
        process.stdout.write('SKIP classic page: ' + classicScript + ' is missing\n');
    }
    process.stdout.write(failed ? 'FAIL\n' : 'PASS\n');
    process.exitCode = failed ? 1 : 0;
})();
