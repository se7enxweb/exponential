// Runs leftMenuResize() of design/admin4/javascript/ezadmin_menubar.js against a small stand-in for the browser: the
// left column (its drawn width follows --left-sidebar-width), the grip, the document's pointer listeners and
// window.a4Pref, which records what is saved. Prints one JSON object, one entry per scenario.
// Usage: node left_resize.js <path to ezadmin_menubar.js>
'use strict';
const fs = require('fs');
const vm = require('vm');
const source = fs.readFileSync(process.argv[2], 'utf8');
const start = source.indexOf('    function leftMenuResize() {');
const end = source.indexOf('    function init() {', start);
if (start < 0 || end < 0) throw new Error('leftMenuResize() not found');

function events() {
    const list = {};
    return {
        addEventListener(type, fn) { (list[type] = list[type] || []).push(fn); },
        removeEventListener(type, fn) { list[type] = (list[type] || []).filter((f) => f !== fn); },
        fire(type, event) { (list[type] || []).slice().forEach((fn) => fn(event || {})); },
    };
}

function run(steps) {
    const saved = [];
    const vars = {};
    const doc = events();
    const handle = Object.assign(events(), { attrs: {}, setAttribute(n, v) { this.attrs[n] = v; }, setPointerCapture() {} });
    const leftMenu = { getBoundingClientRect: () => ({ width: vars['--left-sidebar-width'] ? parseFloat(vars['--left-sidebar-width']) : 300 }) };
    const document = {
        documentElement: { style: { setProperty: (n, v) => { vars[n] = v; }, getPropertyValue: (n) => vars[n] || '' } },
        body: { classList: { add() {}, remove() {} } },
        getElementById: (id) => (id === 'leftmenu' ? leftMenu : id === 'leftmenu-resize-handle' ? handle : null),
        addEventListener: doc.addEventListener,
        removeEventListener: doc.removeEventListener,
    };
    const window = Object.assign(events(), {
        innerWidth: 1400,
        a4Pref: { set: (name, value) => saved.push(name + '=' + value) },
    });
    // a left-to-right page: the grip's direction follows the computed direction of <html>
    const getComputedStyle = () => ({ direction: 'ltr' });
    const sandbox = { window, document, Math, getComputedStyle };
    vm.createContext(sandbox);
    vm.runInContext(source.slice(start, end) + '\nleftMenuResize();', sandbox);
    for (const step of steps) {
        const [what, arg] = step.split(':');
        if (what === 'down') { handle.fire('pointerdown', { pointerId: 1, clientX: Number(arg), preventDefault() {} }); }
        else if (what === 'move') { doc.fire('pointermove', { pointerId: 1, clientX: Number(arg) }); }
        else if (what === 'up') { doc.fire('pointerup', { pointerId: 1 }); handle.fire('pointerup', { pointerId: 1 }); }
        else if (what === 'key') { handle.fire('keydown', { key: arg, preventDefault() {} }); handle.fire('keyup', {}); }
    }
    return saved;
}

process.stdout.write(JSON.stringify({
    'a click on the grip saves nothing': run(['down:300', 'up']),
    'a drag saves its width': run(['down:300', 'move:350', 'up']),
    'a drag back to where it was saves nothing': run(['down:300', 'move:350', 'move:300', 'up']),
    'a key saves once per change': run(['key:ArrowRight', 'key:Shift', 'key:ArrowLeft']),
    'Home at the narrowest saves once': run(['key:Home', 'key:Home']),
}));
