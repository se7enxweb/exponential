// Runs AjaxUploader.prototype.call of design/admin4/javascript/expajaxuploader.js against a stand-in for Exponential UI
// (Exp.io.call answers what a scenario gives) and an open dialog that records its content. Prints one JSON object: per
// answer, what the dialog shows and whether anything was thrown.
// Usage: node uploader.js <path to expajaxuploader.js>
'use strict';
const fs = require('fs');
const vm = require('vm');
const source = fs.readFileSync(process.argv[2], 'utf8');

async function run(answer) {
    const shown = [];
    let thrown = null;
    const $ = function () { return {}; };
    $.fn = { expUpload() {} };
    $.extend = function () { return Object.assign.apply(null, Array.prototype.slice.call(arguments).filter((a) => a && typeof a === 'object')); };
    const Exp = { $, dialog() {}, io: { call: () => Promise.resolve(answer) }, config: {} };
    const sandbox = { Exp, document: {}, console: { error() {} } };
    sandbox.window = sandbox;
    vm.createContext(sandbox);
    vm.runInContext(source, sandbox);
    const Uploader = $.fn.expAjaxUploader.AjaxUploader;
    const up = Object.create(Uploader.prototype);
    up.conf = { parseJSONErrorText: 'Unable to parse the JSON response.' };
    up.dialog = { isOpen: true, busy() {} };
    up.setContent = (html) => shown.push('content: ' + html);
    up.displayError = (text) => shown.push('error: ' + text);
    try {
        await up.call('ezajaxuploader::uploadform::ezobjectrelation', {});
    } catch (e) {
        thrown = String(e && e.message);
    }
    return { shown, thrown, meta: up.lastMetaData };
}

(async () => {
    process.stdout.write(JSON.stringify({
        'a step': await run({ meta_data: { id: 1 }, html: '<form></form>' }),
        'an empty answer': await run(null),
        'an answer without html': await run({ meta_data: false }),
    }));
})();
