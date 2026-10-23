// Runs endPreload() of design/admin4/javascript/ezadmin_menubar.js against a stand-in for <html>'s class list and
// requestAnimationFrame, and prints one JSON line: whether html.a4-preload is still there after the call and after
// each frame, and what happens without requestAnimationFrame.
// Usage: node first_paint.js <path to ezadmin_menubar.js>
'use strict';
const fs = require('fs');
const vm = require('vm');
const source = fs.readFileSync(process.argv[2], 'utf8');
const start = source.indexOf('function endPreload()');
const end = source.indexOf('function init() {', start);
if (start < 0 || end < 0) {
    process.stdout.write(JSON.stringify({ missing: true }) + '\n');
    process.exit(0);
}

function run(withFrames) {
    const classes = new Set(['a4-preload', 'exp-js']);
    const frames = [];
    const window = {};
    if (withFrames) {
        window.requestAnimationFrame = (fn) => { frames.push(fn); return frames.length; };
    }
    const sandbox = {
        window,
        document: { documentElement: { classList: { remove: (c) => classes.delete(c), contains: (c) => classes.has(c) } } },
    };
    vm.createContext(sandbox);
    vm.runInContext(source.slice(start, end) + '\nendPreload();', sandbox);
    const log = [classes.has('a4-preload')];
    while (frames.length) {
        frames.shift()();
        log.push(classes.has('a4-preload'));
    }
    return { log, other: classes.has('exp-js') };
}

process.stdout.write(JSON.stringify({ frames: run(true), noFrames: run(false) }) + '\n');
