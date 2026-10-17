// A small stand-in for the browser's DOM, enough to run the admin4 scripts that act on data-* attributes through
// listeners on the document (a4_actions.js, ezpopupmenu.js, contentstructuremenu_dynamic.js): elements with
// attributes, a tree, a simple HTML parser (tags with quoted attributes, text, entities &amp; &lt; &gt; &quot;),
// selectors (tag, #id, .class, [attr], [attr="v"], descendant combinator, lists with ","), events that bubble to the
// document, forms with elements and submit(). Not a browser: no layout, no styles beyond element.style.
'use strict';

const VOID = new Set(['br', 'hr', 'img', 'input', 'meta', 'link']);

function decode(text) {
    return text.replace(/&quot;/g, '"').replace(/&#039;|&#39;/g, "'").replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&amp;/g, '&');
}
function encode(text, attr) {
    let s = String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    return attr ? s.replace(/"/g, '&quot;') : s;
}

class Node {
    constructor(doc) { this.ownerDocument = doc; this.parentNode = null; this.childNodes = []; }
    get firstChild() { return this.childNodes[0] || null; }
    get nextSibling() {
        if (!this.parentNode) return null;
        const list = this.parentNode.childNodes;
        return list[list.indexOf(this) + 1] || null;
    }
    appendChild(child) {
        if (child.nodeType === 11) { child.childNodes.slice().forEach((c) => this.appendChild(c)); return child; }
        if (child.parentNode) child.parentNode.removeChild(child);
        child.parentNode = this; this.childNodes.push(child); return child;
    }
    removeChild(child) {
        const i = this.childNodes.indexOf(child);
        if (i >= 0) this.childNodes.splice(i, 1);
        child.parentNode = null; return child;
    }
    get textContent() { return this.childNodes.map((c) => c.textContent).join(''); }
    set textContent(value) { this.childNodes = []; this.appendChild(new Text(this.ownerDocument, String(value))); }
}

class Text extends Node {
    constructor(doc, value) { super(doc); this.nodeType = 3; this.nodeValue = value; }
    get textContent() { return this.nodeValue; }
    set textContent(value) { this.nodeValue = String(value); }
    get outerHTML() { return encode(this.nodeValue); }
}

class Element extends Node {
    constructor(doc, tag) {
        super(doc);
        this.nodeType = 1; this.tagName = tag.toUpperCase(); this.attrs = new Map(); this.style = {}; this.listeners = {};
        // a template's content: its nodes, searched like a DocumentFragment
        if (this.tagName === 'TEMPLATE') {
            this.content = {
                childNodes: [],
                querySelectorAll(selector) {
                    const out = [];
                    const walk = (n) => { if (n.nodeType !== 1) return; if (n.matches(selector)) out.push(n); n.children.forEach(walk); };
                    this.childNodes.forEach(walk);
                    return out;
                },
                querySelector(selector) { return this.querySelectorAll(selector)[0] || null; },
            };
        }
    }
    getAttribute(name) { return this.attrs.has(name) ? this.attrs.get(name) : null; }
    setAttribute(name, value) { this.attrs.set(name, String(value)); }
    hasAttribute(name) { return this.attrs.has(name); }
    removeAttribute(name) { this.attrs.delete(name); }
    get attributes() { return Array.from(this.attrs, ([name, value]) => ({ name, value })); }
    get id() { return this.getAttribute('id') || ''; }
    set id(v) { this.setAttribute('id', v); }
    get className() { return this.getAttribute('class') || ''; }
    set className(v) { this.setAttribute('class', v); }
    get classList() {
        const el = this;
        const list = () => el.className.split(/\s+/).filter(Boolean);
        return {
            contains: (c) => list().includes(c),
            add: (c) => { if (!list().includes(c)) el.className = list().concat(c).join(' '); },
            remove: (c) => { el.className = list().filter((x) => x !== c).join(' '); },
            toggle: (c, force) => {
                const on = force === undefined ? !list().includes(c) : !!force;
                el.className = on ? (list().includes(c) ? el.className : list().concat(c).join(' ')) : list().filter((x) => x !== c).join(' ');
                return on;
            },
        };
    }
    get hidden() { return this.hasAttribute('hidden'); }
    set hidden(v) { if (v) this.setAttribute('hidden', ''); else this.removeAttribute('hidden'); }
    get href() { return this.getAttribute('href') || ''; }
    set href(v) { this.setAttribute('href', v); }
    get name() { return this.getAttribute('name') || ''; }
    get type() { return (this.getAttribute('type') || (this.tagName === 'BUTTON' ? 'submit' : '')).toLowerCase(); }
    get disabled() { return this.hasAttribute('disabled'); }
    set disabled(v) { if (v) this.setAttribute('disabled', ''); else this.removeAttribute('disabled'); }
    get checked() { return this._checked === undefined ? this.hasAttribute('checked') : this._checked; }
    set checked(v) { this._checked = !!v; }
    get value() { return this._value === undefined ? (this.getAttribute('value') || '') : this._value; }
    set value(v) { this._value = String(v); }
    get children() { return this.childNodes.filter((c) => c.nodeType === 1); }
    get form() { return this.closest('form'); }
    get elements() { return this.querySelectorAll('input, select, textarea, button'); }
    get offsetParent() { return this.style.display === 'none' ? null : this.parentNode; }
    getClientRects() { return this.style.display === 'none' ? [] : [{}]; }
    getBoundingClientRect() { return { top: 0, bottom: 20, left: 0, right: 100, width: 100, height: 20 }; }
    get offsetWidth() { return 100; }
    get offsetHeight() { return 100; }
    focus() { this.ownerDocument.activeElement = this; }
    blur() {}
    click() { return this.ownerDocument.dispatch('click', this, { detail: 0 }); }
    submit() { this.ownerDocument.submitted.push(this.getAttribute('id') || this.getAttribute('name') || this.tagName); }
    addEventListener(type, fn) { (this.listeners[type] = this.listeners[type] || []).push(fn); }
    contains(other) { for (let n = other; n; n = n.parentNode) if (n === this) return true; return false; }
    matches(selector) { return selector.split(',').some((s) => matchesComplex(this, s.trim())); }
    closest(selector) { for (let n = this; n && n.nodeType === 1; n = n.parentNode) if (n.matches(selector)) return n; return null; }
    querySelectorAll(selector) {
        const out = [];
        const walk = (n) => n.children.forEach((c) => { if (c.matches(selector)) out.push(c); walk(c); });
        walk(this);
        return out;
    }
    querySelector(selector) { return this.querySelectorAll(selector)[0] || null; }
    getElementsByTagName(tag) { return this.querySelectorAll(tag.toLowerCase()); }
    get innerHTML() {
        const nodes = this.tagName === 'TEMPLATE' ? this.content.childNodes : this.childNodes;
        return nodes.map((c) => c.outerHTML).join('');
    }
    set innerHTML(html) {
        const nodes = parse(this.ownerDocument, String(html));
        if (this.tagName === 'TEMPLATE') { this.content.childNodes = nodes; return; }
        this.childNodes.forEach((c) => { c.parentNode = null; });
        this.childNodes = [];
        nodes.forEach((n) => this.appendChild(n));
    }
    get outerHTML() {
        const tag = this.tagName.toLowerCase();
        const attrs = this.attributes.map((a) => ' ' + a.name + '="' + encode(a.value, true) + '"').join('');
        return VOID.has(tag) ? '<' + tag + attrs + ' />' : '<' + tag + attrs + '>' + this.innerHTML + '</' + tag + '>';
    }
}

function matchesCompound(el, compound) {
    const re = /([a-z0-9]+)|#([\w-]+)|\.([\w-]+)|\[([\w-]+)(?:="([^"]*)")?\]|(:not\(\[type\]\))/gi;
    let m, matched = '';
    while ((m = re.exec(compound))) {
        matched += m[0];
        if (m[1] && el.tagName.toLowerCase() !== m[1].toLowerCase()) return false;
        if (m[2] && el.id !== m[2]) return false;
        if (m[3] && !el.classList.contains(m[3])) return false;
        if (m[4] && !el.hasAttribute(m[4])) return false;
        if (m[4] && m[5] !== undefined && el.getAttribute(m[4]) !== m[5]) return false;
        if (m[6] && el.hasAttribute('type')) return false;
    }
    if (matched !== compound) throw new Error('mini_dom: selector not supported: ' + compound);
    return true;
}
function matchesComplex(el, selector) {
    const parts = selector.split(/\s+/);
    if (!matchesCompound(el, parts[parts.length - 1])) return false;
    let n = el.parentNode;
    for (let i = parts.length - 2; i >= 0; i--) {
        while (n && n.nodeType === 1 && !matchesCompound(n, parts[i])) n = n.parentNode;
        if (!n || n.nodeType !== 1) return false;
        n = n.parentNode;
    }
    return true;
}

function parse(doc, html) {
    const root = new Element(doc, 'div');
    const stack = [root];
    const re = /<!--[\s\S]*?-->|<\/([a-z0-9]+)\s*>|<([a-z0-9]+)((?:\s+[\w:-]+(?:\s*=\s*(?:"[^"]*"|'[^']*'|[^\s>]+))?)*)\s*(\/?)>|([^<]+)/gi;
    let m;
    while ((m = re.exec(html))) {
        const top = stack[stack.length - 1];
        if (m[1]) { if (stack.length > 1) stack.pop(); continue; }
        if (m[2]) {
            const el = new Element(doc, m[2]);
            const attrRe = /([\w:-]+)(?:\s*=\s*(?:"([^"]*)"|'([^']*)'|([^\s>]+)))?/g;
            let a;
            while ((a = attrRe.exec(m[3]))) el.setAttribute(a[1], decode(a[2] !== undefined ? a[2] : a[3] !== undefined ? a[3] : a[4] !== undefined ? a[4] : ''));
            (top.tagName === 'TEMPLATE' ? top.content.childNodes : top.childNodes).push(el);
            el.parentNode = top.tagName === 'TEMPLATE' ? null : top;
            if (!m[4] && !VOID.has(m[2].toLowerCase())) stack.push(el);
            continue;
        }
        if (m[5] !== undefined && m[0][0] !== '<') {
            const t = new Text(doc, decode(m[5]));
            if (top.tagName === 'TEMPLATE') top.content.childNodes.push(t); else top.appendChild(t);
        }
    }
    return root.childNodes.slice();
}

function createDocument(html) {
    const doc = {
        listeners: {}, submitted: [], activeElement: null, readyState: 'complete', cookie: '',
        addEventListener(type, fn) { (this.listeners[type] = this.listeners[type] || []).push(fn); },
        createElement(tag) { return new Element(doc, tag); },
        getElementById(id) { return doc.body.querySelector('#' + id) || null; },
        querySelectorAll(s) { return doc.body.querySelectorAll(s); },
        querySelector(s) { return doc.body.querySelector(s); },
        getElementsByTagName(t) { return doc.body.getElementsByTagName(t); },
        get forms() {
            const list = doc.body.querySelectorAll('form');
            list.forEach((f) => { if (f.name) list[f.name] = f; });
            return list;
        },
        // An event on target: its own listeners and those of its parents, then the document's
        dispatch(type, target, props) {
            const event = Object.assign({ type, target, bubbles: true, defaultPrevented: false, stopped: false, detail: 1,
                clientX: 10, clientY: 10, pageX: 10, pageY: 10, key: undefined, shiftKey: false, altKey: false, ctrlKey: false, metaKey: false },
                props || {});
            event.preventDefault = () => { event.defaultPrevented = true; };
            event.stopPropagation = () => { event.stopped = true; };
            event.stopImmediatePropagation = () => { event.stopped = true; event.immediate = true; };
            for (let n = target; n && n.nodeType === 1 && !event.stopped; n = n.parentNode) {
                event.currentTarget = n;
                (n.listeners[type] || []).forEach((fn) => fn.call(n, event));
            }
            event.currentTarget = doc;
            for (const fn of (doc.listeners[type] || [])) {
                if (event.immediate) break;
                fn.call(doc, event);
            }
            // a submit button's default action: its form's submit event
            if (type === 'click' && !event.defaultPrevented && target.tagName && /^(INPUT|BUTTON)$/.test(target.tagName) && target.type === 'submit' && target.form) {
                const submit = doc.dispatch('submit', target.form, {});
                if (!submit.defaultPrevented) target.form.submit();
            }
            return event;
        },
    };
    doc.documentElement = new Element(doc, 'html');
    doc.body = new Element(doc, 'body');
    doc.documentElement.appendChild(doc.body);
    doc.body.innerHTML = html || '';
    return doc;
}

module.exports = { createDocument, parse };
