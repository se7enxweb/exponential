// Runs design/admin4/javascript/page/children_detailed.js against mini_dom.js without Exponential UI (no window.Exp):
// the place of the sub-items table gets the note of data-no-table once, as text, with role status. With Exponential UI
// the table is set up instead. Prints one JSON line per case.
// Usage: node subitems_no_table.js <path to children_detailed.js>
'use strict';
const fs = require('fs');
const vm = require('vm');
const { createDocument } = require('./mini_dom.js');

const source = fs.readFileSync(process.argv[2], 'utf8');
const TEXT = 'No table <b>here</b> & "now"';

function run(withExp) {
    const document = createDocument('<div id="subitems-config" data-conf="{}" data-no-table="' +
        TEXT.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;') +
        '"></div><div id="content-sub-items-list"></div>');
    const inits = [];
    const window = {};
    if (withExp) {
        window.Exp = { datatable: { create: function () {} }, ready: (fn) => fn() };
        window.eZAjaxSubitemsExpDataTable = { init: () => inits.push(1) };
    }
    window.document = document;
    const sandbox = Object.assign({ window, document, console, JSON, Object, String }, window);
    vm.createContext(sandbox);
    vm.runInContext(source, sandbox);
    vm.runInContext(source, sandbox); // a second run adds no second note
    const notes = document.querySelectorAll('#content-sub-items-list .exp-no-subitems-table');
    return {
        notes: notes.length,
        text: notes.length ? notes[0].textContent : null,
        markup: notes.length ? notes[0].childNodes.filter((c) => c.nodeType === 1).length : 0,
        role: notes.length ? notes[0].getAttribute('role') : null,
        cls: notes.length ? notes[0].getAttribute('class') : null,
        inits: inits.length,
    };
}
console.log(JSON.stringify(Object.assign({ name: 'without' }, run(false))));
console.log(JSON.stringify(Object.assign({ name: 'with' }, run(true))));
