// Runs the asynchronous publishing page's script (design/standard/javascript/ezasynchronouspublishing.js) against a
// small stand-in for the DOM and Exp.io that answers ezpublishingqueue::status with one sample, and prints one JSON
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

    // as much of the DOM as the script uses: elements by id, the placeholders by class, children by id and class
    Object.keys(elements).forEach((id) => {
        const el = elements[id];
        el.getAttribute = (key) => (key in el.attrs ? el.attrs[key] : null);
        el.setAttribute = (key, value) => { el.attrs[key] = String(value); };
        el.removeAttribute = (key) => { delete el.attrs[key]; };
        el.querySelector = (selector) => (selector.charAt(0) === '#' && (children[id] || []).includes(selector.slice(1)) ? elements[selector.slice(1)] : null);
        el.querySelectorAll = () => [];
        el.appendChild = (child) => child;
        Object.defineProperty(el, 'innerHTML', { get: () => el.html, set: (value) => { el.html = value; } });
    });
    const document = {
        getElementById: (id) => elements[id] || null,
        querySelectorAll: (selector) => (selector === '.ezap-placeholder' ? placeholders.map((id) => elements[id]) : []),
        createElement: () => ({ style: {}, className: '', innerHTML: '' }),
    };
    const ready = [];
    const io = { call() { return { then(ok) { ok(answer); } }; } };
    const util = { extend() { return Object.assign.apply(null, arguments); } };
    const window = { Exp: { io, util, ready(fn) { ready.push(fn); } }, setTimeout() {}, console };
    const sandbox = { window, document, console };
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
