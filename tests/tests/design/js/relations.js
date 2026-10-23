// Runs design/admin4/javascript/ezajaxrelations_jquery.js (the search of an object relation (list) attribute) against a
// small stand-in for the DOM and Exponential UI: Exp.util.request answers with what a scenario gives, the elements
// record what is done to them, the relation table's last row is filled (list mode) so adding clones it. Prints one
// JSON object, one entry per scenario.
// Usage: node relations.js <path to ezajaxrelations_jquery.js>
'use strict';
const fs = require('fs');
const vm = require('vm');
const source = fs.readFileSync(process.argv[2], 'utf8');

async function run(scenario) {
    const log = [];
    const toasts = [];
    const alerts = [];
    const created = [];
    let request = null;

    // an element: its attributes in order, its handlers, its children; querySelector(All) answers from `found`
    function element(tag, found) {
        const el = {
            tagName: tag.toUpperCase(), nodeName: tag.toUpperCase(), attrs: {}, handlers: {}, style: { display: '' }, isConnected: true,
            className: '', value: '', children: [], innerHTML: '', textContent: '', parentNode: null, found: found || {},
            classList: { add() {}, remove() {} },
            getAttribute(n) { return n in el.attrs ? el.attrs[n] : null; },
            setAttribute(n, v) { el.attrs[n] = String(v); },
            addEventListener(type, fn) { (el.handlers[type] = el.handlers[type] || []).push(fn); },
            dispatch(type, event) { (el.handlers[type] || []).forEach((fn) => fn.call(el, event)); },
            click() { el.dispatch('click', { type: 'click', preventDefault() {} }); },
            appendChild(child) { el.contentAdded(); el.children.push(child); child.parentNode = el; if (child.text) { el.textContent += child.text; } return child; },
            insertAdjacentHTML(where, html) { el.contentAdded(); el.textContent += html.replace(/<[^>]*>/g, ''); },
            contentAdded() { if (el.markup === undefined) { el.markup = '<' + tag + Object.keys(el.attrs).map((n) => ' ' + n + '="' + el.attrs[n] + '"').join('') + '></' + tag + '>'; } },
            insertBefore(child) { return child; },
            querySelector(s) { return (el.found[s] || [])[0] || null; },
            querySelectorAll(s) { return el.found[s] || []; },
            cloneNode() { log.push('clone'); return row(); },
        };
        return el;
    }
    // the relation list's last row: six cells, the first filled; a value set on the first cell's input is logged
    function row() {
        const idInput = element('input'), priority = element('input');
        let id = '';
        Object.defineProperty(idInput, 'value', { get: () => id, set: (v) => { id = v; log.push('id ' + v); } });
        priority.value = '1';
        const tds = [0, 1, 2, 3, 4, 5].map(() => { const td = element('td'); td.innerHTML = 'filled'; return td; });
        const tr = element('tr', { ':scope td': tds, ':scope td:last-child input': [priority], ':scope td:first-child input': [idInput] });
        tr.className = 'bgdark';
        tr.parentNode = element('tbody');
        return tr;
    }

    const browse = element('div');
    const lastRow = row();
    const button = element('input'), field = element('input');
    field.value = 'query';
    const box = element('div', { 'input.ezobject-relation-search-text': [field] });
    box.attrs.id = 'box1';
    button.parentNode = { parentNode: box };
    const ids = {
        'ezobjectrelation-search-published-text': Object.assign(element('input'), { value: 'Yes' }),
        'ezobjectrelation-search-empty-result-text': Object.assign(element('p'), { innerHTML: '--search-string--' }),
        'ezobjectrelation-search-failed-text': scenario.failedText ? Object.assign(element('p'), { textContent: scenario.failedText }) : null,
    };
    const document = {
        readyState: 'complete',
        body: element('body'),
        addEventListener() {},
        getElementById: (id) => ids[id] || null,
        createElement: (tag) => { const el = element(tag); created.push(el); return el; },
        createTextNode: (text) => ({ text: String(text) }),
        querySelectorAll(s) {
            return {
                'input.ezobject-relation-search-btn': [button],
                'input.ezobject-relation-search-text': [field],
                '#box1 div.ezobject-relation-search-browse': [browse],
                '#box1 table tbody tr:last-child': [lastRow],
            }[s] || [];
        },
    };
    const Exp = {
        ready(fn) { fn(); },
        config: { call: '/admin/ezjscore/call/' },
        util: {
            request(opts) {
                return new Promise((resolve, reject) => { request = { opts, resolve, reject }; });
            },
        },
    };
    // the sandbox is the window, as in a browser (the script sets and calls a global)
    const sandbox = { Exp, document, alert: (m) => alerts.push(m), getComputedStyle: () => ({ display: 'block' }), console };
    if (!scenario.noToast) { sandbox.a4Toast = (message, kind) => { toasts.push(kind + ': ' + message); }; }
    sandbox.window = sandbox;
    vm.createContext(sandbox);
    vm.runInContext(source, sandbox);

    // a press of the search button (its box is two levels up)
    const result = {};
    button.dispatch('click', { type: 'click', preventDefault() {}, stopPropagation() {} });
    result.searched = !!request;
    if (scenario.answer && request) { request.resolve(scenario.answer); }
    if (scenario.httpFail && request) { request.reject({ status: scenario.httpFail }); }
    await new Promise((r) => setTimeout(r, 0));

    const links = created.filter((c) => c.tagName === 'A');
    result.resultMarkup = links.map((c) => c.markup);
    if (scenario.press && links.length) {
        const link = links[0];
        for (const how of scenario.press) {
            if (how === 'click') { link.click(); }
            else if (how === 'space') {
                const e = { key: ' ', prevented: false, preventDefault() { e.prevented = true; } };
                link.dispatch('keydown', e);
                result.spacePrevented = e.prevented;
            }
        }
        result.disabled = link.getAttribute('aria-disabled');
    }
    result.clones = log.filter((l) => l === 'clone').length;
    result.idsSet = log.filter((l) => /^id /.test(l)).length;
    result.toasts = toasts;
    result.alerts = alerts;
    return result;
}

const answer = {
    content: {
        CallbackID: 'box1', SearchResultCount: 1,
        SearchResult: [{ id: 42, name: 'Tom &amp; Jerry', class_name: 'Article', section: { name: 'Standard' } }],
    },
};
(async () => {
    const out = {
        'a result is a link Tab reaches': await run({ answer }),
        'a second click adds nothing': await run({ answer, press: ['click', 'click'] }),
        'space presses a result': await run({ answer, press: ['space'] }),
        'a server error text is a toast': await run({ answer: { content: '', error_text: 'Search is off' } }),
        'without the toast an alert': await run({ answer: { content: '', error_text: 'Search is off' }, noToast: true }),
        'a failed request says so': await run({ httpFail: 500 }),
        'a failed request with the page text': await run({ httpFail: 403, failedText: 'The search failed.' }),
    };
    process.stdout.write(JSON.stringify(out));
})();
