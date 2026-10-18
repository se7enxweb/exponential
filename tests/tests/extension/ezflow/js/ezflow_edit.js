// Runs ezflow's design/standard/javascript/ezflow_edit.js against tests/tests/design/js/mini_dom.js: the block remove
// button (data-ezflow-confirm) and the "Discard draft" button (data-ezflow-discard), clicked the way a browser clicks
// them. Prints one JSON line per case.
// Usage: node ezflow_edit.js <path to ezflow_edit.js>
'use strict';
const fs = require('fs');
const path = require('path');
const vm = require('vm');
const { createDocument } = require(path.join(__dirname, '..', '..', '..', 'design', 'js', 'mini_dom.js'));

const source = fs.readFileSync(process.argv[2], 'utf8');

function run(html) {
    const document = createDocument(html);
    const log = { confirms: [] };
    let answer = true;
    const window = {
        confirm: (q) => { log.confirms.push(q); return answer; },
        addEventListener: () => {},
    };
    window.document = document;
    const sandbox = { window, document, console };
    vm.createContext(sandbox);
    vm.runInContext(source, sandbox);
    return { document, log, sandbox, answer: (v) => { answer = v; } };
}
function out(name, value) { console.log(JSON.stringify(Object.assign({ name }, value))); }

// The frontpage edit form of the admin design: DiscardConfirm starts at 1
const form = (button) => '<form id="editform" name="editform" method="post">'
    + '<input type="hidden" name="DiscardConfirm" value="1" />' + button + '</form>';
const remove = '<input id="block-remove-1" class="block-control" type="image" name="CustomActionButton[12_remove_block-z-1]" '
    + 'data-ezflow-confirm="Are you sure you want to remove this block?" />';
const discard = '<input class="button" type="submit" id="discard" name="DiscardButton" data-ezflow-discard="Are you sure you want to discard the draft?" />';
const field = (t) => t.document.querySelector('[name="DiscardConfirm"]').value;

for (const answer of [true, false]) {
    const t = run(form(remove));
    t.answer(answer);
    const ev = t.document.dispatch('click', t.document.getElementById('block-remove-1'));
    out('remove ' + (answer ? 'confirmed' : 'cancelled'), { asked: t.log.confirms, prevented: ev.defaultPrevented, discardConfirm: field(t) });
}
for (const answer of [true, false]) {
    const t = run(form(discard));
    t.answer(answer);
    const ev = t.document.dispatch('click', t.document.getElementById('discard'));
    out('discard ' + (answer ? 'confirmed' : 'cancelled'), { asked: t.log.confirms, prevented: ev.defaultPrevented, discardConfirm: field(t), submitted: t.document.submitted.length });
}
// A remove button in a form without DiscardConfirm (the zone content loaded later, a plain form) still asks
{
    const t = run('<form id="f">' + remove + '</form>');
    t.answer(true);
    const ev = t.document.dispatch('click', t.document.getElementById('block-remove-1'));
    out('remove without field', { asked: t.log.confirms.length, prevented: ev.defaultPrevented });
}
// The script sets itself up once, however often it is printed: one question per click
{
    const t = run(form(remove));
    vm.runInContext(source, t.sandbox);
    t.document.dispatch('click', t.document.getElementById('block-remove-1'));
    out('once', { asked: t.log.confirms.length });
}
