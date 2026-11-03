/*
 * The suggestions of the header search (page_search.tpl, parts/search_suggest.tpl): while the user types, each search
 * field with a data-suggest-url asks content/suggest/(text)/<text> (expSearchSuggest) for content, document pages,
 * users, classes and admin pages, and shows them in groups under the field, the last entry "View all results" (the
 * field's form, content/search). One file for every admin design (design/standard), and for the sites: a site's form
 * with the include parts/front_search_suggest.tpl asks content/sitesuggest for content only, and its list takes the
 * site's own class names (data-suggest-prefix exps-suggest, stylesheets/front_search.css).
 *
 * The field is an ARIA combobox: the list is its listbox, the entry under the arrow keys its aria-activedescendant.
 * Up and Down move through the entries, Enter opens the one chosen (or searches when none is), Escape and a click
 * outside close the list. Ctrl+/ or Cmd+/ focuses the field from anywhere, also from inside another field ("/" alone
 * is admin4.js's, outside fields). Asked for after 250 ms without typing and only from data-suggest-min characters
 * on; a request still running when the text changes is cancelled. The server's texts are put in as text, the typed
 * words marked with <mark> through the DOM, never as HTML. A file of its own, so the page runs under a Content Security
 * Policy without 'unsafe-inline'; no jQuery.
 */
(function () {
    'use strict';
    if (window.expSearchSuggest) return;

    var DELAY = 250, counter = 0;

    // a value of a view parameter as expViewParameters::encode() writes it (a4_url.js, loaded in the page head)
    function encode(value) {
        if (window.a4ViewParameter && window.a4ViewParameter.encode) return window.a4ViewParameter.encode(value);
        return encodeURIComponent(String(value).replace(/[~\/()%+\\?#\x00-\x1F\x7F]/g, function (c) {
            return '~' + ('0' + c.charCodeAt(0).toString(16).toUpperCase()).slice(-2);
        }));
    }

    // an address of this site only (a4SafeURL in the page head; a path of the site where it is missing)
    function safe(url) {
        if (typeof url !== 'string' || url === '') return false;
        if (window.a4SafeURL) return window.a4SafeURL(url, 'search_suggest');
        return /^\/(?![\/\\])/.test(url);
    }

    // text into el, each typed word marked: text nodes and <mark>, nothing parsed as HTML
    function mark(el, text, query) {
        text = String(text === null || text === undefined ? '' : text);
        var lower = text.toLowerCase(), ranges = [];
        if (lower.length === text.length) {
            String(query).toLowerCase().split(/\s+/).forEach(function (word) {
                word = word.replace(/[*"]/g, '');
                if (!word) return;
                for (var at = lower.indexOf(word); at !== -1; at = lower.indexOf(word, at + word.length))
                    ranges.push([at, at + word.length]);
            });
        }
        ranges.sort(function (a, b) { return a[0] - b[0]; });
        var pos = 0;
        ranges.forEach(function (r) {
            if (r[1] <= pos) return;
            var start = Math.max(r[0], pos);
            if (start > pos) el.appendChild(document.createTextNode(text.slice(pos, start)));
            var m = document.createElement('mark');
            m.textContent = text.slice(start, r[1]);
            el.appendChild(m);
            pos = r[1];
        });
        if (pos < text.length) el.appendChild(document.createTextNode(text.slice(pos)));
    }

    function span(className, text) {
        var s = document.createElement('span');
        s.className = className;
        if (text !== undefined) s.textContent = text;
        return s;
    }

    function setup(input) {
        if (input.getAttribute('data-suggest-ready')) return;
        input.setAttribute('data-suggest-ready', '1');
        var list = document.getElementById(input.getAttribute('aria-controls') || '');
        var box = list && list.closest ? list.closest('.a4-suggest, [data-suggest-box]') : null;
        if (!list || !box) return;
        // the class names' prefix: a4-suggest in the administration, the site's own (exps-suggest) on a site
        var P = box.getAttribute('data-suggest-prefix') || 'a4-suggest';
        var status = document.getElementById(box.id + '-status');
        var form = input.form;
        var min = parseInt(input.getAttribute('data-suggest-min'), 10) || 3;
        var base = input.getAttribute('data-suggest-url');
        var timer = null, controller = null, options = [], active = -1, shown = '', id = 'searchsuggest' + (++counter);

        function text(name) { return box.getAttribute('data-text-' + name) || ''; }

        function say(message) { if (status) status.textContent = message; }

        function isOpen() { return !box.hidden; }

        function open() {
            if (!options.length) return;
            show();
        }

        // shown under the field; aligned to the field's end instead when it would leave the window there
        function show() {
            box.hidden = false;
            input.setAttribute('aria-expanded', 'true');
            box.classList.remove(P + '-end');
            var rect = box.getBoundingClientRect ? box.getBoundingClientRect() : null;
            var width = document.documentElement.clientWidth || window.innerWidth;
            if (rect && (rect.right > width || rect.left < 0)) box.classList.add(P + '-end');
        }

        function close() {
            box.hidden = true;
            input.setAttribute('aria-expanded', 'false');
            select(-1);
        }

        function select(index) {
            if (active >= 0 && options[active]) options[active].setAttribute('aria-selected', 'false');
            active = index;
            if (active >= 0 && options[active]) {
                options[active].setAttribute('aria-selected', 'true');
                input.setAttribute('aria-activedescendant', options[active].id);
                if (options[active].scrollIntoView) options[active].scrollIntoView({ block: 'nearest' });
            } else {
                input.removeAttribute('aria-activedescendant');
            }
        }

        function submit() {
            close();
            if (!form) return;
            if (form.requestSubmit) form.requestSubmit(); else form.submit();
        }

        function choose(option) {
            if (!option) return;
            if (option.getAttribute('data-action') === 'all') return submit();
            var link = option.querySelector('a[href]');
            if (link) { close(); link.click(); }
        }

        function option(item, query) {
            var li = document.createElement('li');
            li.className = P + '-item ' + P + '-' + String(item.type || 'item').replace(/[^a-z]/g, '');
            li.setAttribute('role', 'option');
            li.setAttribute('aria-selected', 'false');
            li.id = id + '-' + options.length;
            var a = document.createElement('a');
            a.href = item.url;
            a.tabIndex = -1;
            if (typeof item.icon === 'string' && item.icon !== '' && safe(item.icon)) {
                var img = document.createElement('img');
                img.className = P + '-icon';
                img.src = item.icon;
                img.alt = '';
                a.appendChild(img);
            } else {
                a.appendChild(span(P + '-icon'));
            }
            var body = span(P + '-body');
            var title = span(P + '-title');
            mark(title, item.title, query);
            body.appendChild(title);
            if (item.meta) body.appendChild(span(P + '-meta', item.meta));
            if (item.path && item.path.length) body.appendChild(span(P + '-path', item.path.join(' › ')));
            if (item.excerpt) {
                var excerpt = span(P + '-excerpt');
                mark(excerpt, item.excerpt, query);
                body.appendChild(excerpt);
            }
            a.appendChild(body);
            li.appendChild(a);
            options.push(li);
            return li;
        }

        function render(data, query) {
            list.textContent = '';
            options = [];
            active = -1;
            var groups = data && data.groups && data.groups.length ? data.groups : [], count = 0;
            groups.forEach(function (group, g) {
                var items = (group.items || []).filter(function (item) { return item && safe(item.url); });
                if (!items.length) return;
                var li = document.createElement('li');
                li.className = P + '-group';
                li.setAttribute('role', 'group');
                var heading = span(P + '-heading', group.title || '');
                heading.id = id + '-g' + g;
                heading.setAttribute('role', 'presentation');
                if (group.count > items.length) heading.appendChild(span(P + '-count', ' ' + group.count));
                li.setAttribute('aria-labelledby', heading.id);
                li.appendChild(heading);
                var ul = document.createElement('ul');
                ul.setAttribute('role', 'none');
                items.forEach(function (item) { ul.appendChild(option(item, query)); count++; });
                li.appendChild(ul);
                list.appendChild(li);
            });
            if (!count) {
                var none = document.createElement('li');
                none.className = P + '-none';
                none.setAttribute('role', 'none');
                none.textContent = text('none');
                list.appendChild(none);
            }
            var all = document.createElement('li');
            all.className = P + '-all';
            all.setAttribute('role', 'option');
            all.setAttribute('aria-selected', 'false');
            all.setAttribute('data-action', 'all');
            all.id = id + '-all';
            all.appendChild(span(P + '-title', text('all')));
            if (data && data.total > 0) {
                all.appendChild(span(P + '-meta', data.total === 1 && text('count-one') ? text('count-one') : text('count').replace('%count', data.total)));
            }
            list.appendChild(all);
            options.push(all);
            shown = query;
            say(count ? text('found').replace('%count', count) : text('none'));
            open();
        }

        function failed(query) {
            list.textContent = '';
            options = [];
            var li = document.createElement('li');
            li.className = P + '-none';
            li.setAttribute('role', 'none');
            li.textContent = text('failed');
            list.appendChild(li);
            shown = query;
            say(text('failed'));
            show();
        }

        function ask(query) {
            if (controller) controller.abort();
            controller = typeof AbortController === 'function' ? new AbortController() : null;
            var own = controller;
            fetch(base + '/(text)/' + encode(query), {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' },
                signal: own ? own.signal : undefined
            }).then(function (response) {
                var type = response.headers.get('Content-Type') || '';
                if (!response.ok || type.indexOf('json') === -1) throw new Error('HTTP ' + response.status);
                return response.json();
            }).then(function (data) {
                if (own !== controller || input.value.trim() !== query) return;
                render(data, query);
            }, function (error) {
                if (error && error.name === 'AbortError') return;
                if (own !== controller || input.value.trim() !== query) return;
                failed(query);
            });
        }

        input.addEventListener('input', function () {
            clearTimeout(timer);
            if (controller) { controller.abort(); controller = null; }
            var query = input.value.trim();
            if (query.length < min) { close(); list.textContent = ''; options = []; shown = ''; say(''); return; }
            timer = setTimeout(function () { ask(query); }, DELAY);
        });

        input.addEventListener('focus', function () {
            if (shown !== '' && shown === input.value.trim()) open();
        });

        input.addEventListener('keydown', function (e) {
            if (e.isComposing) return;
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                if (!options.length) return;
                e.preventDefault();
                if (!isOpen()) { open(); select(e.key === 'ArrowDown' ? 0 : options.length - 1); return; }
                var next = e.key === 'ArrowDown' ? active + 1 : active - 1;
                if (next >= options.length) next = 0;
                if (next < 0) next = options.length - 1;
                select(next);
            } else if (e.key === 'Enter') {
                if (isOpen() && active >= 0) { e.preventDefault(); choose(options[active]); }
                else close();
            } else if (e.key === 'Escape') {
                if (isOpen()) { e.preventDefault(); e.stopPropagation(); close(); }
            } else if (e.key === 'Tab') {
                close();
            }
        });

        // a press on an entry keeps the focus in the field; the click opens it
        box.addEventListener('mousedown', function (e) { e.preventDefault(); });
        box.addEventListener('click', function (e) {
            var target = e.target && e.target.closest ? e.target.closest('[role="option"]') : null;
            if (!target) return;
            if (target.getAttribute('data-action') === 'all') { e.preventDefault(); submit(); return; }
            close();
        });
        box.addEventListener('mousemove', function (e) {
            var target = e.target && e.target.closest ? e.target.closest('[role="option"]') : null;
            var index = target ? options.indexOf(target) : -1;
            if (index >= 0 && index !== active) select(index);
        });

        document.addEventListener('click', function (e) {
            if (!isOpen() || e.target === input || box.contains(e.target)) return;
            close();
        });
    }

    // a site's search form (parts/front_search_suggest.tpl): the box names the address and the fewest characters, the
    // form's SearchText field becomes the combobox here, so a design only includes the box in its form
    function wire(box) {
        var form = box.closest ? box.closest('form') : null;
        var input = form ? form.querySelector('input[name="SearchText"]') : null;
        var list = box.querySelector('[role="listbox"]');
        if (!input || !list || input.getAttribute('data-suggest-url')) return;
        input.setAttribute('role', 'combobox');
        input.setAttribute('aria-autocomplete', 'list');
        input.setAttribute('aria-expanded', 'false');
        input.setAttribute('aria-controls', list.id);
        input.setAttribute('autocomplete', 'off');
        input.setAttribute('data-suggest-url', box.getAttribute('data-suggest-url'));
        input.setAttribute('data-suggest-min', box.getAttribute('data-suggest-min') || '3');
    }

    function setupAll() {
        document.querySelectorAll('[data-suggest-box][data-suggest-url]').forEach(wire);
        document.querySelectorAll('input[data-suggest-url]').forEach(setup);
    }

    // Ctrl+/ or Cmd+/: the visible header search field, from anywhere
    document.addEventListener('keydown', function (e) {
        if (e.key !== '/' || !(e.ctrlKey || e.metaKey) || e.altKey || e.isComposing || e.defaultPrevented) return;
        var fields = document.querySelectorAll('.searchblock input[name="SearchText"], input[data-suggest-url]');
        for (var i = 0; i < fields.length; i++) {
            var field = fields[i];
            if (field.disabled || field.offsetParent === null) continue;
            e.preventDefault();
            field.focus();
            if (field.select) field.select();
            return;
        }
    });

    window.expSearchSuggest = { setup: setup, mark: mark, encode: encode };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', setupAll);
    else setupAll();
})();
