// Runs design/admin4/javascript/a4_actions.js against mini_dom.js: each data-a4-* action, started the way a browser
// starts it (a click, a change, a submit). Prints one JSON line per case.
// Usage: node csp_actions.js <path to a4_actions.js>
'use strict';
const fs = require('fs');
const vm = require('vm');
const { createDocument } = require('./mini_dom.js');

const source = fs.readFileSync(process.argv[2], 'utf8');

function run(html, setup) {
    const document = createDocument(html);
    const log = { confirms: [], prints: 0, location: null, timers: [] };
    let answer = true;
    const window = {
        confirm: (q) => { log.confirms.push(q); return answer; },
        print: () => { log.prints++; },
        setTimeout: (fn) => { log.timers.push(fn); },
        location: { set href(v) { log.location = v; } },
    };
    window.document = document;
    const sandbox = { window, document, console };
    vm.createContext(sandbox);
    vm.runInContext(source, sandbox);
    return { document, window, log, answer: (v) => { answer = v; }, flush: () => { log.timers.splice(0).forEach((fn) => fn()); } };
}
function out(name, value) { console.log(JSON.stringify(Object.assign({ name }, value))); }

// confirm on a link: cancelled stops the link, confirmed lets it go on
{
    const t = run('<a id="a" href="/remove" data-a4-confirm="Remove?">x</a>');
    t.answer(false);
    const no = t.document.dispatch('click', t.document.getElementById('a'));
    t.answer(true);
    const yes = t.document.dispatch('click', t.document.getElementById('a'));
    out('confirm link', { asked: t.log.confirms, cancelledPrevented: no.defaultPrevented, confirmedPrevented: yes.defaultPrevented });
}
// confirm on a submit button: the form is not sent when cancelled
{
    const t = run('<form id="f"><input type="submit" id="b" name="RemoveButton" data-a4-confirm="Sure?" /></form>');
    t.answer(false);
    t.document.dispatch('click', t.document.getElementById('b'));
    const cancelled = t.document.submitted.length;
    t.answer(true);
    t.document.dispatch('click', t.document.getElementById('b'));
    out('confirm button', { cancelled, confirmed: t.document.submitted.length });
}
// confirm on a form: its submit event
{
    const t = run('<form id="f" data-a4-confirm="Send?"><input type="submit" id="b" /></form>');
    t.answer(false);
    const ev = t.document.dispatch('submit', t.document.getElementById('f'));
    t.answer(true);
    const ok = t.document.dispatch('submit', t.document.getElementById('f'));
    out('confirm form', { asked: t.log.confirms.length, cancelledPrevented: ev.defaultPrevented, confirmedPrevented: ok.defaultPrevented });
}
// discard: DiscardConfirm is set to 0 before the question
{
    const t = run('<form id="editform" name="editform"><input type="hidden" name="DiscardConfirm" value="1" /><input type="submit" id="d" name="DiscardButton" data-a4-discard-confirm="Discard?" /></form>');
    t.answer(false);
    const ev = t.document.dispatch('click', t.document.getElementById('d'));
    const field = t.document.querySelector('input[name="DiscardConfirm"]');
    out('discard', { asked: t.log.confirms, value: field.value, prevented: ev.defaultPrevented });
}
// toggle checkboxes: inverts the enabled boxes of that name in the form, the link does not navigate
{
    const t = run('<form id="f" name="trashform"><input type="checkbox" name="DeleteIDArray[]" id="c1" />'
        + '<input type="checkbox" name="DeleteIDArray[]" id="c2" checked="checked" />'
        + '<input type="checkbox" name="DeleteIDArray[]" id="c3" disabled="disabled" />'
        + '<input type="checkbox" name="Other[]" id="c4" />'
        + '<a href="#" id="t" data-a4-toggle-checkboxes="DeleteIDArray[]"><span id="inner">x</span></a></form>'
        + '<button type="button" id="outside" data-a4-toggle-checkboxes="Other[]" data-a4-form="trashform">y</button>');
    const ev = t.document.dispatch('click', t.document.getElementById('inner'));
    t.document.dispatch('click', t.document.getElementById('outside'));
    const c = (id) => t.document.getElementById(id).checked;
    out('toggle', { prevented: ev.defaultPrevented, c1: c('c1'), c2: c('c2'), c3: c('c3'), c4: c('c4') });
}
// check all by selector
{
    const t = run('<input type="checkbox" class="pick" id="p1" /><input type="checkbox" class="pick" id="p2" />'
        + '<button type="button" id="all" data-a4-check-all=".pick">all</button><button type="button" id="none" data-a4-check-all=".pick" data-a4-check-value="0">none</button>');
    t.document.dispatch('click', t.document.getElementById('all'));
    const on = [t.document.getElementById('p1').checked, t.document.getElementById('p2').checked];
    t.document.dispatch('click', t.document.getElementById('none'));
    const off = [t.document.getElementById('p1').checked, t.document.getElementById('p2').checked];
    out('check all', { on, off });
}
// dismiss: the parent, or a named element
{
    const t = run('<div id="notice"><button type="button" id="x" data-a4-dismiss="">x</button></div><p id="other">o</p><button type="button" id="y" data-a4-dismiss="#other">y</button>');
    t.document.dispatch('click', t.document.getElementById('x'));
    t.document.dispatch('click', t.document.getElementById('y'));
    out('dismiss', { parent: t.document.getElementById('notice').hidden, named: t.document.getElementById('other').hidden });
}
// submit on change, navigate on change, reset a field on change
{
    const t = run('<form id="f"><select id="s" data-a4-submit-on-change=""><option>a</option></select></form>'
        + '<select id="n" data-a4-navigate-on-change=""></select><input id="ignore" value="5" /><select id="r" data-a4-reset-field="ignore"></select>');
    t.document.dispatch('change', t.document.getElementById('s'));
    const n = t.document.getElementById('n');
    n.value = '/setup/info';
    t.document.dispatch('change', n);
    t.document.dispatch('change', t.document.getElementById('r'));
    out('change', { submitted: t.document.submitted, location: t.log.location, reset: t.document.getElementById('ignore').value });
}
// print
{
    const t = run('<a href="#" id="p" data-a4-print="">print</a>');
    const ev = t.document.dispatch('click', t.document.getElementById('p'));
    out('print', { prints: t.log.prints, prevented: ev.defaultPrevented });
}
// disable on submit: the buttons are disabled after the click, the click itself goes on
{
    const t = run('<form id="f"><input type="submit" id="b1" name="PublishButton" data-a4-disable-on-submit="" /><input type="submit" id="b2" name="CancelButton" /></form>');
    const ev = t.document.dispatch('click', t.document.getElementById('b1'));
    const before = t.document.getElementById('b2').disabled;
    t.flush();
    out('disable on submit', { prevented: ev.defaultPrevented, sent: t.document.submitted.length, before, after: [t.document.getElementById('b1').disabled, t.document.getElementById('b2').disabled] });
}
// focus: the marked field gets the focus when the script runs on a parsed page
{
    const t = run('<input type="text" id="a" /><input type="text" id="b" data-a4-focus="" />');
    out('focus', { active: t.document.activeElement ? t.document.activeElement.id : null });
}
