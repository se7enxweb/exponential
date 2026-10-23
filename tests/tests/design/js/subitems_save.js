// Runs design/admin4/javascript/ezajaxsubitems_expdatatable.js (the sub-items table on the column registry) against a
// small stand-in for Exponential UI: Exp.io answers expsubitems::columns with two columns and expsubitems::savepreference
// as a scenario says (saved, or refused), Exp.prefs.set the same, Exp.datatable.create() only keeps the options it is given,
// window.a4Toast records the toasts. Then it presses what the table options offer: save a preset, delete one, choose
// the rows per page, change the shown columns (saved 700 ms later). Prints one JSON object, one entry per scenario.
// Usage: node subitems_save.js <path to ezajaxsubitems_expdatatable.js>
'use strict';
const fs = require('fs');
const vm = require('vm');
const source = fs.readFileSync(process.argv[2], 'utf8');

async function run(scenario) {
    const toasts = [];
    const saves = [];
    let options = null;
    let timers = [];
    const flush = () => new Promise((r) => setImmediate(r));

    const listElement = { querySelectorAll: () => [] };

    const refuse = (what) => Promise.reject({ message: 'You are no longer signed in. (' + what + ')' });
    const Exp = {
        util: { extend() { return Object.assign.apply(null, Array.prototype.slice.call(arguments).filter((a) => a && typeof a === 'object')); } },
        datatable: { create(el, o) { options = o; return { el: { addEventListener() {} } }; } },
        io: {
            call(fn) {
                if (fn === 'expsubitems::columns') {
                    return Promise.resolve({
                        columns: [{ key: 'name', name: 'Name' }, { key: 'modified', name: 'Modified' }],
                        preference: { saved: true, visible: ['name'], preset: null },
                        presets: [{ id: 'mine', name: 'Mine', columns: ['name'], source: 'user' }],
                    });
                }
                saves.push(fn);
                return scenario.saveOk ? Promise.resolve({}) : refuse(fn);
            },
        },
        prefs: { set(name) { saves.push('pref ' + name); return scenario.saveOk ? Promise.resolve() : refuse(name); } },
        config: { call: '/ezjscore/call' },
    };
    const sandbox = {
        Exp,
        document: { cookie: '', createElement: () => ({}), getElementById: () => listElement, querySelector: () => null, querySelectorAll: () => [] },
        console: { warn() {} },
        setTimeout: (fn, ms) => { const t = { fn, ms }; timers.push(t); return t; },
        clearTimeout: (t) => { timers = timers.filter((x) => x !== t); },
        addEventListener() {},
        navigator: {},
        location: { pathname: '/content/view/full/2' },
        Date,
    };
    if (scenario.toast !== false) { sandbox.a4Toast = (message, kind) => { toasts.push(kind + ': ' + message); }; }
    sandbox.window = sandbox;
    vm.createContext(sandbox);
    vm.runInContext(source, sandbox);

    const labels = {
        DATA_TABLE_COLS: {}, ACTION_BUTTONS: {}, DATA_TABLE: {}, DRAG: {},
        TABLE_OPTIONS: scenario.label ? { save_failed: 'Not saved: %error' } : {},
    };
    const conf = {
        subitemsServer: true, nodeID: 2, cookieName: 'eZSubitemColumns', navigationPart: 'ezcontentnavigationpart',
        defaultShownColumns: { ezcontentnavigationpart: ['name'] }, sortKey: 'name', sortOrder: 1, rowsPrPage: 10,
        classesString: '', languages: {},
    };
    await sandbox.eZAjaxSubitemsExpDataTable.init(conf, labels, [], []);
    const presets = options.tableOptions.presets;
    const table = { presetsFieldset: null, renderPresets() {}, setShown() {} };
    const ids = () => presets.items.call(table, table).map((p) => p.id);
    const out = {};
    let resolved = null;
    if (scenario.step === 'save preset') {
        resolved = await presets.onSave.call(table, 'New one', ['name', 'modified']).then(() => 'resolved', () => 'rejected');
    } else if (scenario.step === 'delete preset') {
        resolved = await presets.onDelete.call(table, { id: 'mine', own: true }).then(() => 'resolved', () => 'rejected');
    } else if (scenario.step === 'rows per page') {
        options.tableOptions.limits.onSelect({ id: 3, count: 50 });
    } else if (scenario.step === 'columns') {
        options.columnToggle.save.call(table, ['name', 'modified']);
        out.savedBeforeDelay = saves.length;
        timers.splice(0).forEach((t) => t.fn());
    }
    await flush();
    await flush();
    out.resolved = resolved;
    out.presets = ids();
    out.current = presets.current.call(table, table);
    out.saves = saves;
    out.toasts = toasts;
    return out;
}

(async () => {
    const out = {};
    const scenarios = {
        'a saved preset is kept': { saveOk: true, step: 'save preset' },
        'a preset not saved is taken back and said': { saveOk: false, step: 'save preset' },
        'a preset not saved, with the template text': { saveOk: false, step: 'save preset', label: true },
        'a preset not deleted stays and is said': { saveOk: false, step: 'delete preset' },
        'a deleted preset goes': { saveOk: true, step: 'delete preset' },
        'rows per page not saved is said': { saveOk: false, step: 'rows per page' },
        'columns not saved are said': { saveOk: false, step: 'columns' },
        'columns saved say nothing': { saveOk: true, step: 'columns' },
    };
    for (const name of Object.keys(scenarios)) {
        out[name] = await run(scenarios[name]);
    }
    process.stdout.write(JSON.stringify(out));
})();
