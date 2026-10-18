/**
 * The help pages (module exphelp, doc/features/6.0/help-system.md): instant search, keyboard shortcuts, the table of
 * contents that follows the reading position, copy buttons on code and collapsible sections. Every page works without
 * this file: the search form sends a GET to exphelp/search, the contents are plain #anchors, code is a plain <pre>.
 *
 * Hooks (data-* attributes, no inline script or handler, so it runs under the admin's Content Security Policy):
 * - [data-exphelp] the page root; data-exphelp-i18n holds the translated strings as JSON.
 * - form[data-exphelp-search] with data-exphelp-endpoint (the JSON search view), an input [data-exphelp-search-input],
 *   filters [data-exphelp-filter] (select, checkbox or radio named audience, category or type) and a results
 *   container [data-exphelp-results] (the input's aria-controls, or the first one in the form or the page).
 * - [data-exphelp-content] the rendered page; data-exphelp-collapsible="h2" adds a show/hide button to each h2.
 * - nav[data-exphelp-toc] links to #ids of the headings; the one being read gets aria-current="location".
 * - a[data-exphelp-prev], a[data-exphelp-next] the previous and next page.
 * Keys: / focuses the search, ArrowDown/ArrowUp move through the results, Enter opens one, Esc closes the results;
 * j/k go to the next/previous heading, [ and ] to the previous/next page.
 */
(function () {
    'use strict';

    var DEFAULTS = {
        copy: 'Copy', copyLabel: 'Copy code', copied: 'Copied', copyFailed: 'Press Ctrl+C to copy',
        noResults: 'No help page matches your search.', resultsCount: '%count results', resultCount: '1 result',
        searching: 'Searching...', error: 'The search is not available. Press Enter to search.',
        collapse: 'Hide section: %title', expand: 'Show section: %title',
        relaxed: 'No page contains every word; these pages contain some of them.'
    };
    var DEBOUNCE = 150;
    var LIMIT = 20;

    function arr(list) { return Array.prototype.slice.call(list || []); }
    function closestRoot(el) { return (el && el.closest && el.closest('[data-exphelp]')) || document; }

    function strings(root) {
        var out = {}, k, raw = root && root.getAttribute ? root.getAttribute('data-exphelp-i18n') : null, data = null;
        for (k in DEFAULTS) { if (Object.prototype.hasOwnProperty.call(DEFAULTS, k)) { out[k] = DEFAULTS[k]; } }
        if (raw) {
            try { data = JSON.parse(raw); } catch (e) { data = null; }
        }
        if (data && typeof data === 'object') {
            for (k in data) {
                if (Object.prototype.hasOwnProperty.call(data, k) && typeof data[k] === 'string' && data[k] !== '') { out[k] = data[k]; }
            }
        }
        return out;
    }

    function el(tag, className, text) {
        var node = document.createElement(tag);
        if (className) { node.className = className; }
        if (text !== undefined && text !== null) { node.textContent = String(text); }
        return node;
    }

    var uid = 0;
    function ensureId(node, prefix) {
        if (!node.id) { uid += 1; node.id = prefix + '-' + uid; }
        return node.id;
    }

    // "a &lt;b&gt; <mark>c</mark>" -> [["a <b> ", false], ["c", true]], for a server without snippet_parts
    function decode(text) {
        return text.replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"').replace(/&#0?39;/g, "'").replace(/&amp;/g, '&');
    }
    function snippetParts(result) {
        if (Array.isArray(result.snippet_parts)) {
            return result.snippet_parts.filter(function (p) { return Array.isArray(p) && typeof p[0] === 'string'; })
                .map(function (p) { return [p[0], !!p[1]]; });
        }
        if (typeof result.snippet !== 'string' || result.snippet === '') { return []; }
        return result.snippet.split(/<\/?mark>/).map(function (part, i) { return [decode(part.replace(/<[^>]*>/g, '')), i % 2 === 1]; })
            .filter(function (p) { return p[0] !== ''; });
    }

    // Only links into this site or https: a result's url comes from the server, but never a javascript: one
    function safeUrl(url) {
        url = String(url || '');
        if (/^\/(?!\/)/.test(url) || /^https:\/\//i.test(url) || /^#/.test(url)) { return url; }
        return '';
    }

    /* ---------------------------------------------------------------- search */

    function Search(form) {
        this.form = form;
        this.root = closestRoot(form);
        this.t = strings(this.root);
        this.input = form.querySelector('[data-exphelp-search-input]');
        this.endpoint = form.getAttribute('data-exphelp-endpoint') || '';
        this.seq = 0;
        this.timer = null;
        this.lastKey = null;
        if (!this.input || !this.endpoint) { return; }
        var controls = this.input.getAttribute('aria-controls');
        this.box = (controls && document.getElementById(controls)) || form.querySelector('[data-exphelp-results]')
            || (this.root.querySelector ? this.root.querySelector('[data-exphelp-results]') : null);
        if (!this.box) {
            this.box = el('div', 'a4-help-results');
            this.box.setAttribute('data-exphelp-results', '');
            this.box.hidden = true;
            form.appendChild(this.box);
        }
        this.input.setAttribute('aria-controls', ensureId(this.box, 'exphelp-results'));
        this.input.setAttribute('autocomplete', 'off');
        this.status = this.box.querySelector('[data-exphelp-status]');
        if (!this.status) {
            this.status = el('p', 'a4-help-results__status');
            this.status.setAttribute('data-exphelp-status', '');
            this.status.setAttribute('role', 'status');
            this.box.appendChild(this.status);
        }
        this.bind();
    }

    Search.prototype.bind = function () {
        var self = this;
        this.input.addEventListener('input', function () { self.schedule(); });
        arr(this.form.querySelectorAll('[data-exphelp-filter]')).forEach(function (f) {
            f.addEventListener('change', function () { self.run(); });
        });
        this.form.addEventListener('keydown', function (event) { self.key(event); });
        // the results may sit outside the form: their keys reach the search too
        if (!this.form.contains(this.box)) { this.box.addEventListener('keydown', function (event) { self.key(event); }); }
    };

    Search.prototype.params = function () {
        var q = String(this.input.value || '').trim();
        var groups = {}, order = [];
        arr(this.form.querySelectorAll('[data-exphelp-filter]')).forEach(function (f) {
            var name = f.getAttribute('name');
            var type = (f.getAttribute('type') || '').toLowerCase();
            if (!name || f.disabled) { return; }
            if ((type === 'checkbox' || type === 'radio') && !f.checked) { return; }
            if (!groups[name]) { groups[name] = []; order.push(name); }
            if (f.value !== '' && groups[name].indexOf(f.value) < 0) { groups[name].push(f.value); }
        });
        var parts = ['q=' + encodeURIComponent(q)];
        order.forEach(function (name) {
            if (groups[name].length) { parts.push(encodeURIComponent(name) + '=' + encodeURIComponent(groups[name].join(','))); }
        });
        parts.push('limit=' + LIMIT, 'format=json');
        return { q: q, query: parts.join('&') };
    };

    Search.prototype.schedule = function () {
        var self = this;
        if (this.timer) { clearTimeout(this.timer); }
        this.timer = setTimeout(function () { self.timer = null; self.run(); }, DEBOUNCE);
    };

    Search.prototype.run = function () {
        var self = this, p = this.params();
        if (p.q === '') { this.seq += 1; this.lastKey = null; this.close(true); return; }
        if (p.query === this.lastKey && !this.box.hidden) { return; }
        this.lastKey = p.query;
        var seq = (this.seq += 1);
        var url = this.endpoint + (this.endpoint.indexOf('?') >= 0 ? '&' : '?') + p.query;
        this.box.setAttribute('aria-busy', 'true');
        if (typeof fetch !== 'function') { this.fail(); return; }
        fetch(url, { headers: { Accept: 'application/json' }, credentials: 'same-origin' })
            .then(function (response) {
                if (!response.ok) { throw new Error('HTTP ' + response.status); }
                return response.json();
            })
            .then(function (data) { if (seq === self.seq) { self.render(data); } })
            .catch(function () { if (seq === self.seq) { self.fail(); } });
    };

    Search.prototype.fail = function () {
        this.lastKey = null;
        this.clearList();
        this.box.removeAttribute('aria-busy');
        this.status.textContent = this.t.error;
        this.box.hidden = false;
    };

    Search.prototype.clearList = function () {
        arr(this.box.querySelectorAll('[data-exphelp-generated]')).forEach(function (n) {
            if (n.parentNode) { n.parentNode.removeChild(n); }
        });
    };

    Search.prototype.render = function (data) {
        var t = this.t, results = data && Array.isArray(data.results) ? data.results : (data && Array.isArray(data.hits) ? data.hits : []);
        var total = data && typeof data.total === 'number' ? data.total : results.length;
        this.clearList();
        this.box.removeAttribute('aria-busy');
        // a server-rendered list (the search page without JavaScript) gives way to the live one
        arr(this.box.querySelectorAll('[data-exphelp-server]')).forEach(function (n) { n.hidden = true; });
        if (data && data.relaxed && results.length) {
            var note = el('p', 'a4-help-results__relaxed', t.relaxed);
            note.setAttribute('data-exphelp-generated', '');
            this.box.appendChild(note);
        }
        if (results.length) {
            var list = el('ul', 'a4-help-results__list');
            list.setAttribute('data-exphelp-generated', '');
            results.forEach(function (r) {
                var href = safeUrl(r.href || r.url);
                if (!href) { return; }
                var li = el('li', 'a4-help-result');
                li.setAttribute('data-exphelp-result', '');
                var a = el('a', 'a4-help-result__link');
                a.setAttribute('href', href);
                a.appendChild(el('span', 'a4-help-result__title', r.title || r.path || href));
                if (r.section) { a.appendChild(el('span', 'a4-help-result__section', r.section)); }
                var parts = snippetParts(r);
                if (parts.length) {
                    var snip = el('span', 'a4-help-result__snippet');
                    parts.forEach(function (p) { snip.appendChild(el(p[1] ? 'mark' : 'span', '', p[0])); });
                    a.appendChild(snip);
                }
                var tags = [].concat(Array.isArray(r.audience) ? r.audience : (r.audience ? [r.audience] : []), r.category ? [r.category] : []);
                if (tags.length) {
                    var meta = el('span', 'a4-help-result__meta');
                    tags.forEach(function (tag) { meta.appendChild(el('span', 'a4-tag', tag)); });
                    a.appendChild(meta);
                }
                li.appendChild(a);
                list.appendChild(li);
            });
            this.box.appendChild(list);
        }
        this.status.textContent = results.length ? (total === 1 ? t.resultCount : t.resultsCount.replace('%count', String(total))) : t.noResults;
        this.facets(data && data.facets);
        this.box.hidden = false;
        this.input.setAttribute('aria-expanded', 'true');
    };

    Search.prototype.facets = function (facets) {
        if (!facets || typeof facets !== 'object') { return; }
        arr(this.root.querySelectorAll('[data-exphelp-facet]')).forEach(function (node) {
            var key = String(node.getAttribute('data-exphelp-facet')).split(':');
            var group = facets[key[0]], count = node.querySelector('[data-exphelp-count]');
            if (count && group && typeof group === 'object') { count.textContent = String(group[key[1]] || 0); }
        });
    };

    Search.prototype.close = function (clearStatus) {
        this.clearList();
        if (clearStatus) { this.status.textContent = ''; }
        arr(this.box.querySelectorAll('[data-exphelp-server]')).forEach(function (n) { n.hidden = false; });
        var hasServer = this.box.querySelectorAll('[data-exphelp-server]').length > 0;
        if (!hasServer) { this.box.hidden = true; }
        this.input.setAttribute('aria-expanded', 'false');
    };

    Search.prototype.links = function () {
        return arr(this.box.querySelectorAll('[data-exphelp-result] a')).filter(function (a) {
            for (var n = a; n && n.nodeType === 1; n = n.parentNode) { if (n.hidden) { return false; } }
            return true;
        });
    };

    Search.prototype.mark = function (link) {
        this.links().forEach(function (a) { a.classList.remove('is-active'); });
        if (link) { link.classList.add('is-active'); link.focus(); }
    };

    Search.prototype.key = function (event) {
        if (event.ctrlKey || event.metaKey || event.altKey) { return; }
        var links = this.links(), target = event.target, i = links.indexOf(target);
        if (event.key === 'ArrowDown' || event.key === 'Down') {
            if (!links.length || this.box.hidden) { return; }
            event.preventDefault();
            this.mark(links[i < 0 ? 0 : Math.min(i + 1, links.length - 1)]);
        } else if (event.key === 'ArrowUp' || event.key === 'Up') {
            if (i < 0) { return; }
            event.preventDefault();
            if (i === 0) { this.mark(null); this.input.focus(); } else { this.mark(links[i - 1]); }
        } else if (event.key === 'Escape' || event.key === 'Esc') {
            event.preventDefault();
            if (i >= 0) { this.mark(null); this.input.focus(); return; }
            if (!this.box.hidden && this.box.querySelectorAll('[data-exphelp-generated]').length) { this.seq += 1; this.close(true); return; }
            if (this.input.value) { this.input.value = ''; this.seq += 1; this.lastKey = null; this.close(true); return; }
            this.input.blur();
        } else if (event.key === 'Enter' && target === this.input) {
            // the first result opens at once when the list is up; otherwise the form goes to the search page
            if (this.timer || this.box.hidden || !links.length) { return; }
            event.preventDefault();
            window.location.assign(links[0].getAttribute('href'));
        }
    };

    /* ---------------------------------------------------------------- page */

    function headings(content) {
        return arr(content.querySelectorAll('h2, h3')).filter(function (h) { return !!h.id; });
    }

    function contentOf(doc) { return doc.querySelector('[data-exphelp-content]'); }

    function inView(node) {
        for (var n = node; n && n.nodeType === 1; n = n.parentNode) {
            if (n.hidden) { return false; }
        }
        return true;
    }

    // Copy buttons on each <pre> of the page
    function setupCopy(content, t) {
        arr(content.querySelectorAll('pre')).forEach(function (pre) {
            var wrap = pre.parentNode;
            if (!wrap || !wrap.classList || !wrap.classList.contains('a4-help-code')) {
                wrap = el('div', 'a4-help-code');
                pre.parentNode.insertBefore(wrap, pre);
                wrap.appendChild(pre);
            }
            if (wrap.querySelector('[data-exphelp-copy]')) { return; }
            var button = el('button', 'a4-btn a4-help-copy', t.copy);
            button.setAttribute('type', 'button');
            button.setAttribute('data-exphelp-copy', '');
            button.setAttribute('aria-label', t.copyLabel);
            wrap.appendChild(button);
        });
    }

    function announce(root, text) {
        var live = root.querySelector('[data-exphelp-announce]');
        if (!live) {
            live = el('p', 'a4-sr-only');
            live.setAttribute('data-exphelp-announce', '');
            live.setAttribute('role', 'status');
            (root.body || root).appendChild(live);
        }
        live.textContent = '';
        live.textContent = text;
    }

    function copy(button) {
        var root = closestRoot(button), t = strings(root);
        var wrap = button.closest('.a4-help-code');
        var code = wrap && (wrap.querySelector('code') || wrap.querySelector('pre'));
        var text = code ? code.textContent : '';
        function done(ok) {
            button.textContent = ok ? t.copied : t.copyFailed;
            button.classList.toggle('is-copied', ok);
            announce(root, ok ? t.copied : t.copyFailed);
            setTimeout(function () { button.textContent = t.copy; button.classList.remove('is-copied'); }, 2000);
        }
        if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
            navigator.clipboard.writeText(text).then(function () { done(true); }, function () { done(false); });
        } else {
            done(false);
        }
    }

    // A show/hide button on each h2 of a page marked data-exphelp-collapsible; the section is everything up to the
    // next h1/h2. Sections start open; a link to a heading inside a closed one opens it.
    function setupCollapse(content, t) {
        var level = String(content.getAttribute('data-exphelp-collapsible') || '').toLowerCase();
        if (level !== 'h2') { return; }
        // the h2s sit in the renderer's div.a4-help-body or straight in the content
        var first = content.querySelector('h2');
        var box = first && first.parentNode && content.contains(first.parentNode) ? first.parentNode : content;
        arr(box.children).forEach(function (h) {
            if (h.tagName !== 'H2' || h.querySelector('[data-exphelp-collapse]')) { return; }
            var section = el('div', 'a4-help-section');
            var next = h.nextSibling;
            while (next && !(next.nodeType === 1 && /^H[12]$/.test(next.tagName))) {
                var move = next;
                next = next.nextSibling;
                section.appendChild(move);
            }
            if (next) { box.insertBefore(section, next); } else { box.appendChild(section); }
            var title = headingText(h);
            var button = el('button', 'a4-help-collapse');
            button.setAttribute('type', 'button');
            button.setAttribute('data-exphelp-collapse', '');
            button.setAttribute('aria-expanded', 'true');
            button.setAttribute('aria-controls', ensureId(section, (h.id || 'exphelp') + '-section'));
            button.setAttribute('aria-label', label(t.collapse, title));
            button.setAttribute('data-exphelp-title', title);
            h.appendChild(button);
        });
    }

    // A heading's text without its # anchor link
    function headingText(h) {
        return arr(h.childNodes).filter(function (n) {
            return !(n.nodeType === 1 && n.classList.contains('a4-help-anchor'));
        }).map(function (n) { return n.textContent; }).join('').replace(/\s+/g, ' ').trim();
    }

    // "Hide section: %title", or a plain "Collapse" followed by the heading
    function label(text, title) {
        return text.indexOf('%title') >= 0 ? text.replace('%title', title) : (title ? text + ': ' + title : text);
    }

    function toggle(button, open) {
        var t = strings(closestRoot(button));
        var section = document.getElementById(button.getAttribute('aria-controls'));
        if (!section) { return; }
        if (open === undefined) { open = button.getAttribute('aria-expanded') !== 'true'; }
        section.hidden = !open;
        button.setAttribute('aria-expanded', open ? 'true' : 'false');
        button.setAttribute('aria-label', label(open ? t.collapse : t.expand, button.getAttribute('data-exphelp-title') || ''));
    }

    // Opens whatever hides the element: a closed section, a <details>
    function reveal(node) {
        for (var n = node; n && n.nodeType === 1; n = n.parentNode) {
            if (n.tagName === 'DETAILS' && !n.hasAttribute('open')) { n.setAttribute('open', ''); }
            if (n.hidden && n.classList.contains('a4-help-section')) {
                var b = document.querySelector('[aria-controls="' + n.id + '"]');
                if (b) { toggle(b, true); }
            }
        }
    }

    function revealHash() {
        var id = String(window.location.hash || '').replace(/^#/, '');
        if (!id) { return; }
        try { id = decodeURIComponent(id); } catch (e) { return; }
        var target = document.getElementById(id);
        if (target) { reveal(target); }
    }

    // The table of contents marks the last heading that has passed the top of the window
    function Spy(content, toc) {
        this.content = content;
        this.toc = toc;
        this.links = {};
        var self = this;
        arr(toc.querySelectorAll('a')).forEach(function (a) {
            var href = a.getAttribute('href') || '';
            if (href.charAt(0) === '#' && href.length > 1) { self.links[href.slice(1)] = a; }
        });
        this.current = null;
        this.pending = false;
    }
    Spy.prototype.update = function () {
        var offset = 96, self = this;
        var list = headings(this.content).filter(function (h) { return self.links[h.id] && inView(h); });
        // the last heading above the line; the first one until any has passed it
        var passed = list.filter(function (h) { return h.getBoundingClientRect().top <= offset; });
        var found = passed.length ? passed[passed.length - 1] : (list[0] || null);
        var id = found ? found.id : null;
        if (id === this.current) { return; }
        if (this.current && this.links[this.current]) {
            this.links[this.current].removeAttribute('aria-current');
            this.links[this.current].classList.remove('is-current');
        }
        this.current = id;
        if (id && this.links[id]) {
            this.links[id].setAttribute('aria-current', 'location');
            this.links[id].classList.add('is-current');
        }
    };
    Spy.prototype.schedule = function () {
        var self = this;
        if (this.pending) { return; }
        this.pending = true;
        (window.requestAnimationFrame || function (fn) { return setTimeout(fn, 16); })(function () { self.pending = false; self.update(); });
    };

    function jump(content, step) {
        var list = headings(content).filter(inView), offset = 100, i, pick = null;
        for (i = 0; i < list.length; i++) {
            var top = list[i].getBoundingClientRect().top;
            if (step > 0 && top > offset + 1) { pick = list[i]; break; }
            if (step < 0 && top < offset - 1) { pick = list[i]; }
        }
        if (!pick) { return false; }
        if (pick.scrollIntoView) { pick.scrollIntoView({ block: 'start' }); }
        if (window.history && window.history.replaceState) { window.history.replaceState(null, '', '#' + pick.id); }
        return true;
    }

    function typing(target) {
        if (!target || target.nodeType !== 1) { return false; }
        if (/^(INPUT|TEXTAREA|SELECT)$/.test(target.tagName)) { return true; }
        return target.getAttribute('contenteditable') === 'true' || target.getAttribute('contenteditable') === '';
    }

    /* ---------------------------------------------------------------- start */

    var started = false, searches = [], spy = null;

    function init() {
        if (started || !document.querySelector('[data-exphelp]')) { return; }
        started = true;
        arr(document.querySelectorAll('form[data-exphelp-search]')).forEach(function (form) {
            var s = new Search(form);
            if (s.box) { searches.push(s); }
        });
        var content = contentOf(document);
        if (content) {
            var t = strings(closestRoot(content));
            setupCopy(content, t);
            setupCollapse(content, t);
            revealHash();
            var toc = document.querySelector('[data-exphelp-toc]');
            if (toc) {
                spy = new Spy(content, toc);
                spy.update();
                window.addEventListener('scroll', function () { spy.schedule(); }, { passive: true });
                window.addEventListener('resize', function () { spy.schedule(); }, { passive: true });
            }
        }
        window.addEventListener('hashchange', revealHash);
        // "Print this page": shown only where it can work (it stays hidden without JavaScript)
        if (typeof window.print === 'function') {
            arr(document.querySelectorAll('[data-exphelp-print]')).forEach(function (b) { b.hidden = false; });
        }

        document.addEventListener('click', function (event) {
            var target = event.target && event.target.closest ? event.target : null;
            if (!target) { return; }
            var b = target.closest('[data-exphelp-copy]');
            if (b) { event.preventDefault(); copy(b); return; }
            b = target.closest('[data-exphelp-print]');
            if (b) { event.preventDefault(); window.print(); return; }
            b = target.closest('[data-exphelp-collapse]');
            if (b) { event.preventDefault(); toggle(b); return; }
            // a click outside a search closes its live results
            searches.forEach(function (s) {
                if (!s.form.contains(target) && !s.box.contains(target) && s.box.querySelectorAll('[data-exphelp-generated]').length) { s.close(false); }
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.defaultPrevented || event.ctrlKey || event.metaKey || event.altKey || typing(event.target)) { return; }
            var key = event.key;
            if (key === '/' && searches.length) {
                event.preventDefault();
                searches[0].input.focus();
                if (searches[0].input.select) { searches[0].input.select(); }
                return;
            }
            var c = contentOf(document);
            if ((key === 'j' || key === 'k') && c) {
                if (jump(c, key === 'j' ? 1 : -1)) { event.preventDefault(); if (spy) { spy.schedule(); } }
                return;
            }
            if (key === '[' || key === ']') {
                var link = document.querySelector(key === '[' ? 'a[data-exphelp-prev]' : 'a[data-exphelp-next]');
                if (link && safeUrl(link.getAttribute('href'))) { event.preventDefault(); window.location.assign(link.getAttribute('href')); }
            }
        });
    }

    window.expHelp = { init: init, snippetParts: snippetParts, safeUrl: safeUrl };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}());
