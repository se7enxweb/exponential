// Runs design/admin4/javascript/ezajaxrelations_jquery.js (the search of an object relation (list) attribute) against a
// small stand-in for jQuery: every selection records what is done to it, $.ez answers with what a scenario gives, the
// relation table's last row is filled (list mode) so adding clones it. Prints one JSON object, one entry per scenario.
// Usage: node relations.js <path to ezajaxrelations_jquery.js>
'use strict';
const fs = require('fs');
const vm = require('vm');
const source = fs.readFileSync(process.argv[2], 'utf8');

function run(scenario) {
    const log = [];
    const toasts = [];
    const alerts = [];
    const created = [];
    let ezCall = null;
    let failedText = scenario.failedText || null;

    // a selection: what it is (sel), its elements, the handlers bound to it
    function q(sel, items) {
        const self = {
            sel, handlers: {}, length: items ? items.length : 1,
            on(type, data, fn) {
                if (typeof data === 'function') { fn = data; data = undefined; }
                (self.handlers[type] = self.handlers[type] || []).push({ fn, data });
                return self;
            },
            trigger(type, event) {
                (self.handlers[type] || []).forEach((h) => h.fn.call(self.el, Object.assign({ data: h.data, preventDefault() { event && (event.prevented = true); } }, event || {})));
            },
            removeClass() { return self; }, addClass() { return self; }, prop() { return self; }, show() { return self; },
            empty() { log.push('empty ' + sel); return self; },
            append(x) { if (x && x.sel) { log.push('append ' + x.sel); } return self; },
            attr(name, value) { if (value === undefined) { return name === 'id' ? 'box1' : null; } if (self.el) { self.el.attrs[name] = value; } return self; },
            text(v) { if (v === undefined) { return sel === '#ezobjectrelation-search-failed-text' ? failedText : 'text'; } return self; },
            html(v) { if (v === undefined) { return '--search-string--'; } log.push('html ' + sel + ' ' + v); return self; },
            val(v) { if (v === undefined) { return sel.indexOf('search-text') !== -1 ? 'query' : '1'; } log.push('val ' + sel + ' ' + v); return self; },
            find(s) {
                if (s === 'td') { return q('td', [{ innerHTML: 'filled' }, { innerHTML: 'filled' }, {}, {}, {}, {}]); }
                if (s === 'input.ezobject-relation-search-text') { return q(s); }
                return q(sel + ' ' + s, s.indexOf('_for_object') !== -1 ? [] : undefined);
            },
            clone() { log.push('clone'); return q('row clone'); },
            insertAfter() { return self; },
            slice() { return q('tds'); },
            eq() { return q('td'); },
            first() { return self; },
        };
        (items || [{ className: 'bgdark' }]).forEach((it, i) => { self[i] = it; });
        return self;
    }

    function $(arg) {
        if (typeof arg === 'function') { arg($); return null; }
        if (typeof arg === 'string' && arg.charAt(0) === '<') {
            // a new element: a stand-in that knows its attributes and its click
            const el = { attrs: {}, className: '', getAttribute(n) { return n in this.attrs ? this.attrs[n] : null; }, setAttribute(n, v) { this.attrs[n] = String(v); } };
            const sel = q(arg);
            sel.el = el;
            el.click = () => sel.trigger('click', {});
            created.push({ markup: arg, sel, el });
            return sel;
        }
        if (arg === '#ezobjectrelation-search-failed-text') { return q(arg, failedText === null ? [] : [{}]); }
        return q(String(arg));
    }
    $.ez = function (call, params, callback) {
        ezCall = { call, params, callback, failFn: null };
        return { fail(fn) { ezCall.failFn = fn; return this; } };
    };


    // the search button and field keep their handlers, so the search can be pressed
    const handlers = {};
    const $page = function (arg) {
        if (typeof arg === 'function') { arg($page); return null; }
        if (typeof arg === 'string' && arg.indexOf('input.ezobject-relation-search') === 0) { return (handlers[arg] = q(arg)); }
        return $(arg);
    };
    $page.ez = $.ez;
    // the sandbox is the window, as in a browser (the script sets and calls a global)
    const sandbox = { jQuery: $page, alert: (m) => alerts.push(m) };
    if (!scenario.noToast) { sandbox.a4Toast = (message, kind) => { toasts.push(kind + ': ' + message); }; }
    sandbox.window = sandbox;
    vm.createContext(sandbox);
    vm.runInContext(source, sandbox);

    // a press of the search button (its box is two levels up)
    const result = {};
    const button = { parentNode: { parentNode: {} } };
    handlers['input.ezobject-relation-search-btn'].handlers.click[0].fn.call(button, { preventDefault() {} });
    result.searched = !!ezCall;
    if (scenario.answer && ezCall) { ezCall.callback(scenario.answer); }
    if (scenario.httpFail && ezCall && ezCall.failFn) { ezCall.failFn({ status: scenario.httpFail }); }

    const links = created.filter((c) => c.markup.indexOf('<a') === 0);
    result.resultMarkup = links.map((c) => c.markup);
    if (scenario.press && links.length) {
        const link = links[0];
        for (const how of scenario.press) {
            if (how === 'click') { link.el.click(); }
            else if (how === 'space') {
                const e = { key: ' ', prevented: false };
                (link.sel.handlers.keydown || []).forEach((h) => h.fn.call(link.el, Object.assign(e, { preventDefault() { e.prevented = true; } })));
                result.spacePrevented = e.prevented;
            }
        }
        result.disabled = link.el.getAttribute('aria-disabled');
    }
    result.clones = log.filter((l) => l === 'clone').length;
    result.idsSet = log.filter((l) => /^val .*td:first-child input/.test(l)).length;
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
const out = {
    'a result is a link Tab reaches': run({ answer }),
    'a second click adds nothing': run({ answer, press: ['click', 'click'] }),
    'space presses a result': run({ answer, press: ['space'] }),
    'a server error text is a toast': run({ answer: { content: '', error_text: 'Search is off' } }),
    'without the toast an alert': run({ answer: { content: '', error_text: 'Search is off' }, noToast: true }),
    'a failed request says so': run({ httpFail: 500 }),
    'a failed request with the page text': run({ httpFail: 403, failedText: 'The search failed.' }),
};
process.stdout.write(JSON.stringify(out));
