/* The bookmark tree (design:content/bookmark_tree.tpl): open/close folders (remembered), search, and on the
   bookmark page create, rename, delete and move with a dialog, drag and drop, and move up / down buttons.
   Every action is a plain form post to content/bookmark (with the form token), so a reload shows the result. */
(function () {
    'use strict';
    if (window.expBookmarks) return;
    window.expBookmarks = true;

    var KEY = 'expbm.closed';
    var root = document.documentElement;
    root.classList.add('exp-bm-has-js');

    function load() {
        try { return JSON.parse(window.localStorage.getItem(KEY) || '[]') || []; } catch (e) { return []; }
    }
    function save(list) {
        try { window.localStorage.setItem(KEY, JSON.stringify(list)); } catch (e) { /* private mode: the state is just not remembered */ }
    }
    var closed = load().map(String);

    function rowsOf(tree) { return Array.prototype.slice.call(tree.querySelectorAll('.exp-bm-row')); }
    function parentOf(tree, row) {
        var p = row.getAttribute('data-parent');
        return p && p !== '0' ? tree.querySelector('.exp-bm-folder[data-id="' + p + '"]') : null;
    }
    function ancestors(tree, row) {
        var list = [], p = parentOf(tree, row), guard = 0;
        while (p && guard++ < 100) { list.push(p); p = parentOf(tree, p); }
        return list;
    }

    /* show/hide by the closed set and the search text */
    function apply(tree, query) {
        var q = (query || '').trim().toLowerCase();
        var rows = rowsOf(tree), matches = 0;
        var show = {};
        rows.forEach(function (row) { show[row.getAttribute('data-type') + row.getAttribute('data-id')] = false; });
        rows.forEach(function (row) {
            var hit = !q || (row.getAttribute('data-name') || '').toLowerCase().indexOf(q) !== -1;
            row.classList.toggle('exp-bm-match', !!q && hit);
            if (q) {
                if (hit) {
                    matches++;
                    show[row.getAttribute('data-type') + row.getAttribute('data-id')] = true;
                    ancestors(tree, row).forEach(function (a) { show['folder' + a.getAttribute('data-id')] = true; });
                }
            } else {
                var hidden = ancestors(tree, row).some(function (a) { return closed.indexOf(a.getAttribute('data-id')) !== -1; });
                show[row.getAttribute('data-type') + row.getAttribute('data-id')] = !hidden;
            }
        });
        rows.forEach(function (row) {
            row.hidden = !show[row.getAttribute('data-type') + row.getAttribute('data-id')];
            if (row.classList.contains('exp-bm-folder')) {
                var open = q ? true : closed.indexOf(row.getAttribute('data-id')) === -1;
                row.setAttribute('aria-expanded', open ? 'true' : 'false');
            }
        });
        return matches;
    }

    function toggle(tree, folder) {
        var id = folder.getAttribute('data-id'), i = closed.indexOf(id);
        if (i === -1) closed.push(id); else closed.splice(i, 1);
        save(closed);
        trees().forEach(function (t) { apply(t); });
    }
    function trees() { return Array.prototype.slice.call(document.querySelectorAll('[data-exp-bm]')); }

    document.addEventListener('click', function (e) {
        var t = e.target;
        var btn = t.closest ? t.closest('.exp-bm-toggle') : null;
        if (btn) {
            var folder = btn.closest('.exp-bm-folder'), tree = btn.closest('[data-exp-bm]');
            toggle(tree, folder);
            return;
        }
        var name = t.closest ? t.closest('.exp-bm-folder-name') : null;
        if (name) { toggle(name.closest('[data-exp-bm]'), name.closest('.exp-bm-folder')); }
    });

    /* ---- the bookmark page ---- */
    function page() {
        var scope = document.querySelector('[data-exp-bm-page]');
        if (!scope) return;
        var tree = scope.querySelector('[data-exp-bm="page"]') || document.querySelector('[data-exp-bm="page"]');
        if (!tree) return;
        var dialog = document.getElementById('exp-bm-dialog'), form = document.getElementById('exp-bm-action');
        var texts = document.getElementById('exp-bm-t');
        if (!dialog || !form || !texts) return;
        var search = document.getElementById('exp-bm-search'), nomatch = document.querySelector('.exp-bm-nomatch');

        if (search) {
            search.addEventListener('input', function () {
                var m = apply(tree, search.value);
                if (nomatch) nomatch.hidden = !(search.value.trim() && m === 0);
            });
            search.addEventListener('keydown', function (e) { if (e.key === 'Enter') e.preventDefault(); });
        }
        Array.prototype.forEach.call(document.querySelectorAll('[data-exp-bm-all]'), function (b) {
            b.addEventListener('click', function () {
                closed = b.getAttribute('data-exp-bm-all') === 'open' ? [] :
                    rowsOf(tree).filter(function (r) { return r.classList.contains('exp-bm-folder'); }).map(function (r) { return r.getAttribute('data-id'); });
                save(closed);
                trees().forEach(function (t) { apply(t); });
            });
        });

        function field(name) { return form.elements[name]; }
        function set(values) {
            ['BookmarkFolderAction', 'FolderID', 'BookmarkID', 'Type', 'ID', 'BeforeID', 'FolderName', 'ParentFolderID'].forEach(function (n) {
                if (field(n)) field(n).value = values[n] !== undefined ? values[n] : (n === 'ParentFolderID' ? '0' : '');
            });
            if (field('DeleteBookmarks')) field('DeleteBookmarks').checked = false;
        }
        function show(cls, yes) { Array.prototype.forEach.call(form.querySelectorAll('.' + cls), function (el) { el.hidden = !yes; }); }
        function layout(o) {
            document.getElementById('exp-bm-dialog-title').textContent = o.title;
            show('exp-bm-d-text', !!o.text); form.querySelector('.exp-bm-d-text').textContent = o.text || '';
            show('exp-bm-d-name', !!o.name); show('exp-bm-d-parent', !!o.parent); show('exp-bm-d-withbookmarks', !!o.withBookmarks);
            var ok = form.querySelector('[data-exp-bm-ok]');
            ok.textContent = o.ok;
            ok.classList.toggle('exp-bm-danger-ok', !!o.danger);
            field('FolderName').required = !!o.name;
        }
        function disableTargets(ids) {
            Array.prototype.forEach.call(field('ParentFolderID').options, function (o) { o.disabled = ids.indexOf(o.value) !== -1; });
        }
        function open() {
            if (dialog.showModal) dialog.showModal(); else dialog.setAttribute('open', 'open');
            var focus = form.querySelector('input[type=text]:not([hidden]), select:not([hidden])');
            var first = Array.prototype.find.call(form.querySelectorAll('.exp-bm-d:not([hidden]) input[type=text], .exp-bm-d:not([hidden]) select'), function () { return true; });
            (first || form.querySelector('[data-exp-bm-ok]')).focus();
        }
        // a form sent from inside the drop event would leave the page before the drag is over: send it right after
        function afterDrop(values) { window.setTimeout(function () { submitNow(values); }, 30); }
        function submitNow(values) {
            set(values);
            if (form.requestSubmit) form.requestSubmit(); else form.submit();
        }
        function descendants(folder) {
            var ids = [folder.getAttribute('data-id')], changed = true;
            while (changed) {
                changed = false;
                rowsOf(tree).forEach(function (r) {
                    if (r.classList.contains('exp-bm-folder') && ids.indexOf(r.getAttribute('data-parent')) !== -1 && ids.indexOf(r.getAttribute('data-id')) === -1) { ids.push(r.getAttribute('data-id')); changed = true; }
                });
            }
            return ids;
        }
        function siblings(row) {
            return rowsOf(tree).filter(function (r) {
                return r.getAttribute('data-type') === row.getAttribute('data-type') && r.getAttribute('data-parent') === row.getAttribute('data-parent');
            });
        }

        // the buttons of the rows
        tree.addEventListener('click', function (e) {
            var b = e.target.closest ? e.target.closest('button.exp-bm-act') : null;
            if (!b) return;
            var row = b.closest('.exp-bm-row'), type = row.getAttribute('data-type'), id = row.getAttribute('data-id'), name = row.getAttribute('data-name') || '';
            var action = b.getAttribute('data-action');
            if (action === 'up' || action === 'down') {
                var sib = siblings(row), i = sib.indexOf(row);
                var before = action === 'up' ? (i > 0 ? sib[i - 1] : null) : (i < sib.length - 2 ? sib[i + 2] : null);
                if ((action === 'up' && i === 0) || (action === 'down' && i === sib.length - 1)) return;
                submitNow({ BookmarkFolderAction: 'place', Type: type, ID: id, ParentFolderID: row.getAttribute('data-parent'), BeforeID: before ? before.getAttribute('data-id') : '0' });
            } else if (action === 'rename') {
                set({ BookmarkFolderAction: 'rename', FolderID: id, FolderName: name });
                layout({ title: texts.getAttribute('data-rename'), name: true, ok: texts.getAttribute('data-save') });
                open();
            } else if (action === 'delete') {
                set({ BookmarkFolderAction: 'delete', FolderID: id });
                layout({ title: texts.getAttribute('data-delete'), text: texts.getAttribute('data-delete-text').replace('%name', name), withBookmarks: true, ok: texts.getAttribute('data-confirm-delete'), danger: true });
                open();
            } else if (action === 'newsub') {
                set({ BookmarkFolderAction: 'create', ParentFolderID: id });
                layout({ title: texts.getAttribute('data-new'), name: true, parent: true, ok: texts.getAttribute('data-create') });
                disableTargets([]);
                open();
            } else if (action === 'move') {
                set({ BookmarkFolderAction: 'place', Type: type, ID: id, ParentFolderID: type === 'folder' ? row.getAttribute('data-parent') : row.getAttribute('data-parent') });
                layout({ title: texts.getAttribute(type === 'folder' ? 'data-move-folder' : 'data-move-bookmark'), parent: true, ok: texts.getAttribute('data-move') });
                disableTargets(type === 'folder' ? descendants(row) : []);
                open();
            }
        });
        Array.prototype.forEach.call(document.querySelectorAll('[data-exp-bm-new]'), function (b) {
            b.addEventListener('click', function () {
                set({ BookmarkFolderAction: 'create', ParentFolderID: b.getAttribute('data-exp-bm-new') });
                layout({ title: texts.getAttribute('data-new'), name: true, parent: true, ok: texts.getAttribute('data-create') });
                disableTargets([]);
                open();
            });
        });
        form.querySelector('[data-exp-bm-cancel]').addEventListener('click', function () {
            if (dialog.close) dialog.close(); else dialog.removeAttribute('open');
        });

        // drag and drop: onto a folder (into it, last), onto an entry (in front of it, in its folder), onto "Top level"
        var dragged = null;
        tree.addEventListener('dragstart', function (e) {
            var row = e.target.closest ? e.target.closest('.exp-bm-row') : null;
            if (!row) return;
            dragged = row;
            row.classList.add('exp-bm-dragged');
            scope.classList.add('exp-bm-dragging');
            try { e.dataTransfer.effectAllowed = 'move'; e.dataTransfer.setData('text/plain', row.getAttribute('data-type') + ':' + row.getAttribute('data-id')); } catch (x) { /* old browsers */ }
        });
        function clear() {
            Array.prototype.forEach.call(document.querySelectorAll('.exp-bm-over, .exp-bm-before, .exp-bm-dragged'), function (el) { el.classList.remove('exp-bm-over', 'exp-bm-before', 'exp-bm-dragged'); });
            scope.classList.remove('exp-bm-dragging');
        }
        tree.addEventListener('dragend', function () { dragged = null; clear(); });
        function target(e) {
            var row = e.target.closest ? e.target.closest('.exp-bm-row') : null;
            if (!row || !dragged || row === dragged) return null;
            if (dragged.classList.contains('exp-bm-folder') && descendants(dragged).indexOf(row.getAttribute('data-id')) !== -1 && row.classList.contains('exp-bm-folder')) return null;
            if (dragged.classList.contains('exp-bm-folder') && ancestors(tree, row).indexOf(dragged) !== -1) return null;
            var rect = row.getBoundingClientRect(), into = row.classList.contains('exp-bm-folder') && e.clientY > rect.top + rect.height * 0.25 && e.clientY < rect.bottom - rect.height * 0.25;
            return { row: row, into: into };
        }
        tree.addEventListener('dragover', function (e) {
            var t = target(e);
            Array.prototype.forEach.call(tree.querySelectorAll('.exp-bm-over, .exp-bm-before'), function (el) { el.classList.remove('exp-bm-over', 'exp-bm-before'); });
            if (!t) return;
            e.preventDefault();
            t.row.classList.add(t.into ? 'exp-bm-over' : 'exp-bm-before');
        });
        tree.addEventListener('drop', function (e) {
            var t = target(e);
            if (!t) return;
            e.preventDefault();
            var d = dragged;
            clear();
            var type = d.getAttribute('data-type');
            if (t.into) {
                afterDrop({ BookmarkFolderAction: 'place', Type: type, ID: d.getAttribute('data-id'), ParentFolderID: t.row.getAttribute('data-id'), BeforeID: '0' });
            } else {
                var sameKind = t.row.getAttribute('data-type') === type;
                var before = sameKind ? t.row.getAttribute('data-id') : '0';
                afterDrop({ BookmarkFolderAction: 'place', Type: type, ID: d.getAttribute('data-id'), ParentFolderID: t.row.getAttribute('data-parent'), BeforeID: before });
            }
        });
        var rootDrop = document.querySelector('[data-exp-bm-root]');
        if (rootDrop) {
            rootDrop.addEventListener('dragover', function (e) { if (dragged) { e.preventDefault(); rootDrop.classList.add('exp-bm-over'); } });
            rootDrop.addEventListener('dragleave', function () { rootDrop.classList.remove('exp-bm-over'); });
            rootDrop.addEventListener('drop', function (e) {
                if (!dragged) return;
                e.preventDefault();
                var d = dragged;
                clear();
                afterDrop({ BookmarkFolderAction: 'place', Type: d.getAttribute('data-type'), ID: d.getAttribute('data-id'), ParentFolderID: '0', BeforeID: '0' });
            });
        }
    }

    function init() {
        trees().forEach(function (t) { apply(t); });
        page();
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
    window.addEventListener('pageshow', function (e) { if (e.persisted) trees().forEach(function (t) { apply(t); }); });
})();
