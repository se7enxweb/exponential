// Runs the asynchronous publishing page's script (design/standard/javascript/ezasynchronouspublishing.js) against a
// small stand-in for jQuery and Exp.io that answers ezpublishingqueue::status with one sample, and prints one JSON
// line per sample: the placeholder shown and the href and display of the link to the published node.
// Usage: node asynchronous_publishing.js <path to ezasynchronouspublishing.js>
'use strict';
const fs = require('fs');
const vm = require('vm');

const source = fs.readFileSync(process.argv[2], 'utf8');

function run(name, answer) {
    // the elements of queued.tpl the script touches
    const elements = {};
    ['ezap-error', 'ezap-message-publishing', 'ezap-message-finished', 'ezap-message-deferred',
     'ezap-contentview-uri', 'ezap-versionview-uri'].forEach((id) => {
        elements[id] = { id, attrs: { href: '#' }, style: { display: '' }, html: '' };
    });
    const placeholders = ['ezap-message-publishing', 'ezap-message-finished', 'ezap-message-deferred'];
    const children = { 'ezap-message-finished': ['ezap-contentview-uri'], 'ezap-message-deferred': ['ezap-versionview-uri'] };

    function wrap(list) {
        return {
            length: list.length,
            css(prop, value) { list.forEach((el) => { el.style[prop] = value; }); return this; },
            // as jQuery: attr(key, undefined) reads, it does not write
            attr(key, value) {
                if (value === undefined) return list.length ? list[0].attrs[key] : undefined;
                list.forEach((el) => { el.attrs[key] = String(value); });
                return this;
            },
            html(value) { list.forEach((el) => { el.html = value; }); return this; },
            find(selector) {
                const id = selector.charAt(0) === '#' ? selector.slice(1) : null;
                return wrap(list.reduce((found, el) => found.concat((children[el.id] || []).filter((c) => c === id).map((c) => elements[c])), []));
            },
            appendTo() { return this; },
        };
    }
    const ready = [];
    function $(target) {
        if (typeof target === 'function') { ready.push(target); return undefined; }
        if (target === '.ezap-placeholder') return wrap(placeholders.map((id) => elements[id]));
        if (target.charAt(0) === '#') return wrap(elements[target.slice(1)] ? [elements[target.slice(1)]] : []);
        return wrap([]);
    }
    $.extend = function () { return Object.assign.apply(null, arguments); };
    const io = { call() { return { then(ok) { ok(answer); } }; } };
    const window = { Exp: { $, io }, setTimeout() {}, console };
    const sandbox = { window, console };
    vm.createContext(sandbox);
    vm.runInContext(source + '\nthis.app = eZAsynchronousPublishingApp;', sandbox);
    sandbox.app.cfg = { contentobject_id: 7, version: 1, failure_message: 'failed', last_checked_message: '' };
    sandbox.app.init();
    ready.forEach((fn) => fn());
    const shown = placeholders.filter((id) => elements[id].style.display === 'block');
    const link = elements['ezap-contentview-uri'];
    process.stdout.write(JSON.stringify({ name, shown, href: link.attrs.href, display: link.style.display }) + '\n');
}

run('finished with the node address', { status: 'finished', node_uri: '/index.php/news/item' });
run('finished without the node address', { status: 'finished' });
