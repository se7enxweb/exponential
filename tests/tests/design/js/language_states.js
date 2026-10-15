// Runs the Translations column's formatter of design/admin4/javascript/ezajaxsubitems_expdatatable.js (attr,
// languageState, translationView) with a small stand-in for jQuery's $.each and prints one JSON line per scenario:
// the HTML it draws for a row.
// Usage: node language_states.js <path to ezajaxsubitems_expdatatable.js>
'use strict';
const fs = require('fs');
const vm = require('vm');
const source = fs.readFileSync(process.argv[2], 'utf8');
const start = source.indexOf('        var attr = function (v) {');
const end = source.indexOf('        var text = function (key) {');
if (start === -1 || end === -1 || end < start) {
    console.log(JSON.stringify({ error: 'formatter not found' }));
    process.exit(1);
}
const code = source.slice(start, end) + '\nresult = translationView(row);';

function run(name, confObj, row) {
    const sandbox = {
        confObj,
        row,
        result: null,
        $: { each: (list, fn) => { list.forEach((v, i) => fn(i, v)); } },
    };
    vm.runInNewContext(code, sandbox);
    console.log(JSON.stringify({ name, html: sandbox.result }));
}

const base = {
    languages: { 'ger-DE': 'Deutsch', 'eng-GB': 'English', 'fre-FR': 'Français', 'esl-ES': 'Español' },
    languageBadges: { 'ger-DE': 'DE', 'eng-GB': 'EN', 'fre-FR': 'FR', 'esl-ES': 'ES' },
    languageStates: { main: '%language – main language', present: '%language – translation exists', missing: '%language – missing' },
    editInLanguage: 'Edit in <%language_name>.',
    editPrefixURL: '/content/edit',
};
const row = { contentobject_id: 42, translations: ['eng-GB', 'ger-DE'], initial_language: 'ger-DE', can_edit: false };

run('few languages, read only', Object.assign({}, base, { showMissingLanguages: true }), row);
run('many languages', Object.assign({}, base, { showMissingLanguages: false }), row);
run('editable', Object.assign({}, base, { showMissingLanguages: true }), Object.assign({}, row, { can_edit: true }));
run('escaped', Object.assign({}, base, { showMissingLanguages: false, languages: { 'ger-DE': '<b>"x"</b>' } }),
    { contentobject_id: 1, translations: ['ger-DE'], initial_language: 'ger-DE', can_edit: false });
run('a language outside the site list', Object.assign({}, base, { showMissingLanguages: true }),
    { contentobject_id: 2, translations: ['ger-DE', 'nor-NO'], initial_language: 'nor-NO', can_edit: false });
