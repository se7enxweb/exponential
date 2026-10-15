// Runs two parts of design/admin4/javascript/admin4.js against a small stand-in for the browser and prints one JSON
// object: "keys", the arrow keys of the segmented switches (.a4-tabs.is-segmented), and "preview", the device
// switch of the version preview (window.a4Pref, the iframe's size and scale).
// Usage: node segmented_switch.js <path to admin4.js>
'use strict';
const fs = require('fs');
const vm = require('vm');
const source = fs.readFileSync(process.argv[2], 'utf8');

// one part of the file: from the comment that starts with `head` to the next top-level comment (or the end)
function part(head) {
    const start = source.indexOf('/*\n * ' + head);
    if (start < 0) throw new Error('part not found: ' + head);
    const end = source.indexOf('\n/*\n', start + 4);
    return source.slice(start, end < 0 ? source.length : end);
}

function el(tag, attrs, parent) {
    const node = {
        tagName: tag, attrs: Object.assign({}, attrs || {}), children: [], parentNode: parent || null, disabled: false,
        style: {}, listeners: {}, textContent: '', hidden: false, clientWidth: 0, classes: [],
        hasAttribute(n) { return n in this.attrs; },
        getAttribute(n) { return n in this.attrs ? String(this.attrs[n]) : null; },
        setAttribute(n, v) { this.attrs[n] = String(v); },
        addEventListener(type, fn) { (this.listeners[type] = this.listeners[type] || []).push(fn); },
        click() { (this.listeners.click || []).forEach((fn) => fn.call(this, {})); },
        focus() { focused = this; },
        matches(sel) { return sel.split(',').some((s) => s.trim().split('.').filter(Boolean).every((c) => node.classes.indexOf(c) >= 0)); },
        classList: { add(c) { node.classes.push(c); } },
    };
    if (parent) parent.children.push(node);
    return node;
}
let focused = null;

// --- keys
function keys() {
    const listeners = [];
    const document = { addEventListener(type, fn) { if (type === 'keydown') listeners.push(fn); } };
    vm.runInNewContext(part('The segmented switches answer the arrow keys'), { document, window: {} });
    const group = el('DIV');
    group.classes = ['a4-tabs', 'is-segmented'];
    const b = ['small', 'medium', 'large', 'off'].map((name) => el('BUTTON', { name }, group));
    b[3].disabled = true;
    const other = el('DIV');
    const outside = el('BUTTON', { name: 'outside' }, other);
    function press(target, key, mods) {
        focused = target;
        let prevented = false;
        const event = Object.assign({ key, target, preventDefault() { prevented = true; } }, mods || {});
        listeners.forEach((fn) => fn(event));
        return { to: focused.attrs.name, prevented };
    }
    return {
        right: press(b[0], 'ArrowRight'),
        rightFromLastSkipsDisabled: press(b[2], 'ArrowRight'),
        left: press(b[0], 'ArrowLeft'),
        home: press(b[2], 'Home'),
        end: press(b[0], 'End'),
        down: press(b[0], 'ArrowDown'),
        shift: press(b[0], 'ArrowRight', { shiftKey: true }),
        outside: press(outside, 'ArrowRight'),
    };
}

// --- preview
function preview(signedIn, stored, steps) {
    const saved = [];
    const prefs = Object.assign({}, stored);
    const window = {
        a4Pref: {
            signedIn,
            get(name, legacy) { return signedIn ? (prefs[name] || null) : (prefs[legacy] || null); },
            set(name, value, legacy) { saved.push((signedIn ? name : legacy) + '=' + value); prefs[signedIn ? name : legacy] = value; },
        },
        getComputedStyle() { return { paddingLeft: '12', paddingRight: '12' }; },
        addEventListener() {},
    };
    const bar = el('DIV', { 'data-a4-size': '%width × %height px, shown at %scale %' });
    const pill = el('DIV', {}, bar);
    const devices = [['phone', 390, 844], ['tablet', 820, 1180], ['desktop', 1280, 800], ['large', 1920, 1080]]
        .map(([id, w, h]) => el('BUTTON', { 'data-a4-device': id, 'data-width': w, 'data-height': h, 'aria-pressed': 'false' }, pill));
    const turn = el('BUTTON', { 'data-a4-landscape': '', 'aria-pressed': 'false' }, bar);
    const size = el('P', {}, bar);
    bar.hidden = true;
    const stage = el('DIV');
    stage.clientWidth = 944;
    const device = el('DIV', {}, stage);
    const frame = el('IFRAME', {}, device);
    bar.querySelectorAll = (sel) => (sel === 'button[data-a4-device]' ? devices : []);
    bar.querySelector = (sel) => {
        if (sel === 'button[data-a4-landscape]') return turn;
        if (sel === '.a4-content-preview-size') return size;
        const m = /data-a4-device="(\w+)"/.exec(sel);
        return m ? devices.find((d) => d.attrs['data-a4-device'] === m[1]) : null;
    };
    stage.querySelector = (sel) => (sel === 'iframe' ? frame : device);
    const document = {
        readyState: 'complete',
        getElementById(id) { return id === 'a4-content-preview-bar' ? bar : id === 'a4-content-preview-stage' ? stage : null; },
    };
    vm.runInNewContext(part('The version preview in the width of a device'), { document, window, parseInt, parseFloat, Math, Array, String });
    function state() {
        const pressed = devices.filter((d) => d.attrs['aria-pressed'] === 'true').map((d) => d.attrs['data-a4-device']);
        return {
            pressed: pressed.join(','), landscape: turn.attrs['aria-pressed'], turnDisabled: turn.disabled, barHidden: bar.hidden,
            frame: frame.style.width + 'x' + frame.style.height, transform: frame.style.transform, box: device.style.width + 'x' + device.style.height,
            size: size.textContent,
        };
    }
    const out = { start: state() };
    (steps || []).forEach((step, i) => {
        if (step === 'turn') turn.click();
        else devices.find((d) => d.attrs['data-a4-device'] === step).click();
        out['step' + (i + 1) + ':' + step] = state();
    });
    out.saved = saved;
    return out;
}

process.stdout.write(JSON.stringify({
    keys: keys(),
    previewDefault: (function () { try { return preview(true, {}, ['phone', 'turn', 'large', 'turn']); } catch (e) { return { error: String(e) }; } })(),
    previewStored: (function () { try { return preview(true, { admin_preview_device: 'tablet', admin_preview_landscape: '1' }, []); } catch (e) { return { error: String(e) }; } })(),
    previewAnonymous: (function () { try { return preview(false, { 'exp-admin4-preview-device': 'phone' }, ['desktop']); } catch (e) { return { error: String(e) }; } })(),
}) + '\n');
