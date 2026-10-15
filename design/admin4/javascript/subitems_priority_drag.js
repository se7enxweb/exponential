/**
 * admin4: the rows of the sub items table reordered by drag and drop, saved as the children's priorities.
 *
 * Only where the parent sorts its children by priority and the user may change the priorities: the same condition
 * as the classic "Update priorities" (children.tpl: sorted by priority and $node.can_edit), which
 * children_detailed.tpl hands over as confObj.priorityDrag. ezajaxsubitems_expdatatable.js then adds a column of
 * grips (window.a4SubitemsPriorityDrag.create(confObj, labelsObj.DRAG)) and attaches it to the table. The grips
 * show while the table is sorted as the parent sorts (by priority, in the parent's direction), with no name
 * filter, and with more than one row; else the column is hidden.
 *
 *  - Pointer (mouse, touch, pen): the grip is dragged; the row itself is the placeholder, a ghost with the name
 *    follows the pointer, the page scrolls when the pointer comes near the top or bottom of the window. Escape or a
 *    cancelled pointer puts the row back.
 *  - Keyboard: the grip is a button. Space or Enter picks the row up, the arrow keys move it (Home and End to the
 *    first and last place), Space or Enter drops it, Escape (or leaving the grip) cancels. Alt+Up and Alt+Down move
 *    it by one place at once. Each step is announced ("Position 3 of 12").
 *
 * Saving (ezjscnode::updatepriority, the server function of the inline priority editor, with the page's form
 * token): right after the drop. The priorities on the page are given out again to the rows in their new order -
 * the same values, sorted in the parent's direction - so no row moves to another page (assign()). When the page
 * has equal priorities, or shares a priority with the last row of the page before or the first row of the page
 * after, the rows get distinct values between those two neighbours (in steps of 10 where there is room); when there
 * is no room between them, the server numbers all children again in steps of 10 (Renumber). The priorities the
 * table showed, and the two neighbours', go along (ExpectedPriorityList): when one of them changed in the meantime,
 * nothing is written, the table loads again and says so. A toast says "Order saved" with an Undo button that posts
 * the previous priorities back (checked the same way). When saving fails, the rows go back and the error is shown.
 *
 * The priority cells stay editable inline. A drag does not start while a priority is being edited with a changed
 * value (the editor keeps the focus and a toast asks to save or cancel it first) or while such a change is being
 * saved; an editor with an unchanged value is closed. While the order is being saved, no cell can be edited.
 *
 * The pure parts (isActive, assign, keyAction, save, undo) are exported for tests/tests/design/js/priority_drag.js.
 */
(function (root, factory) {
    'use strict';
    var api = factory(root);
    if (typeof module === 'object' && module.exports) { module.exports = api; }
    if (root) { root.a4SubitemsPriorityDrag = api; }
}(typeof window !== 'undefined' ? window : null, function (window) {
    'use strict';

    var STEP = 10;
    var COLUMN = 'a4drag';

    function int(v) { var n = parseInt(v, 10); return isFinite(n) ? n : 0; }
    function fmt(text, params) {
        return String(text || '').replace(/%[a-z_]+/g, function (k) { return Object.prototype.hasOwnProperty.call(params, k) ? String(params[k]) : k; });
    }
    function range(start, step, n) { var out = []; for (var i = 0; i < n; i++) { out.push(start + i * step); } return out; }
    function list(map) { return Object.keys(map).map(function (id) { return id + ':' + map[id]; }).join(','); }

    /**
     * Whether the grips are on: the parent sorts by priority and the user may change it (conf.priorityDrag), the
     * table is sorted by priority in the parent's direction (conf.sortOrder: 1 ascending, 0 descending), no name
     * filter, more than one row.
     */
    function isActive(conf, state, rowCount) {
        var dir = Number(conf.sortOrder) === 1 ? 'asc' : 'desc';
        return conf.priorityDrag === true && !!state && !!state.sort && state.sort.key === 'priority' && state.sort.dir === dir &&
               !conf.nameFilter && !state.filter && rowCount > 1;
    }

    /**
     * n distinct whole numbers strictly between lo and hi (either may be infinite), ascending: in steps of 10 where
     * there is room, else spread evenly; null when there are fewer than n numbers between them.
     */
    function spread(n, lo, hi, first) {
        if (isFinite(lo) && isFinite(hi)) {
            if (hi - lo - 1 < n) { return null; }
            var step = Math.min(STEP, Math.floor((hi - lo) / (n + 1)));
            return range(lo + step, step, n);
        }
        // the last page: upwards from its smallest value, or from 10 above the page before when it shares that one
        if (isFinite(lo)) { return range(first > lo ? first : lo + STEP, STEP, n); }
        if (isFinite(hi)) {
            // the first page: upwards from its smallest value while that stays below the next page, else downwards
            if (hi - first >= n) { return range(first, Math.min(STEP, Math.floor((hi - first) / n)), n); }
            return range(hi - n * STEP, STEP, n);
        }
        return range(first, STEP, n);
    }

    /**
     * The new priorities of a page after a move.
     *   before: [{id, priority}] the rows as the table showed them; after: [id] the rows in their new order;
     *   dir: 'asc' | 'desc' (the parent's); bounds: {prev, next} the priority of the row before the page and the
     *   row after it (null when there is none).
     * Returns {mode: 'permute', values} (the page's own values given out again), {mode: 'spread', values} (distinct
     * values between the neighbours) or {mode: 'renumber'} (no room: the server numbers all children again).
     * values: {id: priority}.
     */
    function assign(before, after, dir, bounds) {
        var s = dir === 'desc' ? -1 : 1;
        // in "rank" space (the priority, negated for a descending parent) the rows run from small to large
        var ks = before.map(function (r) { return s * int(r.priority); }).sort(function (a, b) { return a - b; });
        var lo = bounds && bounds.prev !== null && bounds.prev !== undefined ? s * int(bounds.prev) : -Infinity;
        var hi = bounds && bounds.next !== null && bounds.next !== undefined ? s * int(bounds.next) : Infinity;
        var n = ks.length, mode = 'permute', out = ks;
        var distinct = ks.every(function (k, i) { return i === 0 || k > ks[i - 1]; });
        if (!distinct || (n && (ks[0] <= lo || ks[n - 1] >= hi))) {
            out = spread(n, lo, hi, ks[0]);
            if (!out) { return { mode: 'renumber' }; }
            mode = 'spread';
        }
        var values = {};
        after.forEach(function (id, i) { values[id] = s * out[i] + 0; });   // + 0: no -0
        return { mode: mode, values: values };
    }

    /**
     * What a key on a grip does: when not picked up, 'pickup' (Space, Enter), 'stepUp' / 'stepDown' (Alt+Up,
     * Alt+Down); when picked up, 'up', 'down', 'first' (Home), 'last' (End), 'drop' (Space, Enter), 'cancel'
     * (Escape). null for any other key.
     */
    function keyAction(grabbed, e) {
        var k = e.key, plain = !e.ctrlKey && !e.metaKey && !e.altKey && !e.shiftKey;
        if (!grabbed) {
            if (e.altKey && !e.ctrlKey && !e.metaKey && (k === 'ArrowUp' || k === 'ArrowDown')) { return k === 'ArrowUp' ? 'stepUp' : 'stepDown'; }
            return plain && (k === ' ' || k === 'Spacebar' || k === 'Enter') ? 'pickup' : null;
        }
        if (k === 'Escape') { return 'cancel'; }
        if (!plain) { return null; }
        return { ArrowUp: 'up', ArrowDown: 'down', Home: 'first', End: 'last', ' ': 'drop', Spacebar: 'drop', Enter: 'drop' }[k] || null;
    }

    /**
     * Saves a new order of the page.
     *   ctx: {dir, offset, total, nodeID, objectID, call(data) -> Promise(content), neighbour(offset) -> Promise({id, priority} | null)}
     *   before: [{id, priority}] as shown; after: [id] the new order.
     * Resolves {status: 'unchanged'}, {status: 'saved', mode, values: {id: priority of each row of the page},
     * changed: {id: [old, new]}}, {status: 'conflict', nodes} or {status: 'error', error}; it never rejects.
     */
    function save(ctx, before, after) {
        if (before.length === after.length && before.every(function (r, i) { return String(r.id) === String(after[i]); })) {
            return Promise.resolve({ status: 'unchanged' });
        }
        var shown = before.length;
        return Promise.all([
            ctx.offset > 0 ? ctx.neighbour(ctx.offset - 1) : null,
            ctx.offset + shown < ctx.total ? ctx.neighbour(ctx.offset + shown) : null
        ]).then(function (n) {
            var prev = n[0], next = n[1], plan = assign(before, after, ctx.dir, { prev: prev ? prev.priority : null, next: next ? next.priority : null });
            var expected = {}, old = {};
            before.forEach(function (r) { expected[r.id] = int(r.priority); old[r.id] = int(r.priority); });
            [prev, next].forEach(function (r) { if (r) { expected[r.id] = int(r.priority); } });
            var data = { ContentNodeID: ctx.nodeID, ContentObjectID: ctx.objectID, ExpectedPriorityList: list(expected) };
            if (plan.mode === 'renumber') {
                data.Renumber = '1';
                data.PageOrder = after.join(',');
                data.PageOffset = ctx.offset;
            } else {
                var set = {};
                Object.keys(plan.values).forEach(function (id) { if (plan.values[id] !== old[id]) { set[id] = plan.values[id]; } });
                data.PriorityList = list(set);
            }
            return ctx.call(data).then(function (content) {
                content = content || {};
                if (content.conflict === true) { return { status: 'conflict', nodes: content.nodes || [] }; }
                // only the answer of a server that checked and wrote counts as saved (an older server answers [])
                if (content.conflict !== false || !content.changed || typeof content.changed !== 'object') {
                    return { status: 'error', error: new Error(ctx.unexpected || 'unexpected answer') };
                }
                var changed = content.changed, values = {};
                // a row the server did not change kept its priority
                after.forEach(function (id) { values[id] = changed[id] ? int(changed[id][1]) : old[id]; });
                return { status: 'saved', mode: plan.mode, values: values, changed: changed };
            });
        }).catch(function (error) { return { status: 'error', error: error }; });
    }

    /**
     * Puts the priorities a save changed back (changed: {id: [old, new]}), only where they still are the new ones.
     * Resolves {status: 'saved' | 'conflict' | 'error' | 'unchanged'}; it never rejects.
     */
    function undo(ctx, changed) {
        var set = {}, expected = {};
        Object.keys(changed || {}).forEach(function (id) { set[id] = int(changed[id][0]); expected[id] = int(changed[id][1]); });
        if (!Object.keys(set).length) { return Promise.resolve({ status: 'unchanged' }); }
        return ctx.call({ ContentNodeID: ctx.nodeID, ContentObjectID: ctx.objectID, PriorityList: list(set), ExpectedPriorityList: list(expected) })
            .then(function (content) {
                if (content && content.conflict === true) { return { status: 'conflict', nodes: content.nodes || [] }; }
                return content && content.conflict === false ? { status: 'saved' } : { status: 'error', error: new Error(ctx.unexpected || 'unexpected answer') };
            }).catch(function (error) { return { status: 'error', error: error }; });
    }

    // ---- the page --------------------------------------------------------------------------------------------

    /** The column and its behaviour for one table; attach(table) once the table is built. */
    function create(conf, labels) {
        var $ = window.Exp.$;
        labels = labels || {};
        var L = function (key, fallback, params) { return fmt(labels[key] || fallback, params || {}); };
        var dir = Number(conf.sortOrder) === 1 ? 'asc' : 'desc';
        var table = null, busy = false, pendingEdit = false, grab = null, drag = null, undoToast = null;
        var hintId = 'a4-subitems-drag-hint-' + (conf.nodeID || 0);

        var htmlText = function (html) {
            var t = document.createElement('textarea');
            t.innerHTML = html === undefined || html === null ? '' : String(html);
            return t.value;
        };
        var active = function () { return !!table && isActive(conf, table.state, table.rowsData.length); };

        var column = {
            key: COLUMN, label: '', hidden: true, className: 'a4-content-drag',
            render: function (row) {
                if (!active()) { return ''; }
                return $('<button type="button" class="a4-grip a4-content-drag-grip"></button>')
                    .attr({ 'aria-label': L('grip', 'Move %name', { '%name': htmlText(row.name) }), 'aria-describedby': hintId,
                            title: L('grip_title', 'Drag to change the order') })
                    .append('<span class="a4-icon a4-bi-grip-vertical" aria-hidden="true"></span>');
            }
        };

        // ---- helpers on the rows shown -------------------------------------------------------------------
        var rowsEls = function () { return table.$body.children('tr').get(); };
        var keyOf = function (tr) { return tr.getAttribute('data-row-key'); };
        var rowOf = function (el) { return $(el).closest('tr')[0]; };
        var gripOf = function (id) { return table.$body.find('tr[data-row-key="' + String(id).replace(/[^0-9a-z_-]/gi, '') + '"] .a4-content-drag-grip')[0]; };
        var position = function (tr) { return table.state.offset + rowsEls().indexOf(tr) + 1; };
        var nameOf = function (tr) { var r = $.data(tr, 'expRow'); return r ? htmlText(r.name) : ''; };
        var say = function (text) { table.announce(text); };
        var toast = function (text, kind, opener, delay) {
            var t = window.a4Toast ? window.a4Toast(text, kind, opener, delay) : null;
            if (!t) { say(text); }
            return t;
        };
        // Moves a row to the index j by moving the rows between instead: the row with the focused grip stays in
        // the document, so the grip keeps the focus.
        var moveTo = function (tr, j) {
            var rows = rowsEls(), i = rows.indexOf(tr), body = tr.parentNode, k;
            j = Math.max(0, Math.min(rows.length - 1, j));
            if (i === -1 || i === j) { return false; }
            if (j < i) { for (k = i - 1; k >= j; k--) { body.insertBefore(rows[k], tr.nextSibling); } }
            else { for (k = i + 1; k <= j; k++) { body.insertBefore(rows[k], tr); } }
            return true;
        };
        var focusGrip = function (id) { var g = gripOf(id); if (g) { g.focus(); } };
        var sync = function () {
            var on = active();
            table.setColumnHidden(COLUMN, !on);
            // the column's header has a name for screen readers (the datatable draws an empty one for a column
            // without a label, and a label would put the column in Table options)
            var th = table.$head.find('th[data-key="' + COLUMN + '"]');
            if (!th.find('.a4-sr-only').length) { th.append($('<span class="a4-sr-only"></span>').text(L('column', 'Order'))); }
            table.$root.toggleClass('a4-content-drag-on', on);
        };
        var setBusy = function (on) {
            busy = on;
            table.$root.toggleClass('a4-content-drag-saving', on);
            if (on) { table.$table.attr('aria-busy', 'true'); } else { table.$table.removeAttr('aria-busy'); }
            // no other page, sorting or filter while the order is being saved
            $().add(table.$pagers).add(table.$compacts).add(table.$head).add(table.$filter).prop('inert', on);
        };

        /** A move may start: nothing being saved, and no priority being edited with a changed value. */
        var canStart = function (grip) {
            if (busy) { say(L('busy', 'Please wait until the order is saved.')); return false; }
            if (pendingEdit) { say(L('wait_edit', 'Please wait until the priority is saved.')); return false; }
            var ed = table.editing;
            if (ed) {
                var shownValue = ed.old === null || ed.old === undefined ? '' : String(ed.old);
                if (String(ed.$in.val()) !== shownValue) {
                    toast(L('unsaved_edit', 'Save the priority you are editing (Enter) or cancel it (Escape) before you move a row.'), 'warn', grip);
                    ed.$in[0].focus();
                    return false;
                }
                table.cancelEdit();
            }
            return true;
        };

        // ---- saving -------------------------------------------------------------------------------------
        var ctx = function () {
            return {
                dir: dir, offset: table.state.offset, total: table.total, nodeID: conf.nodeID, objectID: conf.parentObjectID,
                unexpected: L('error_unknown', 'unknown error'),
                call: function (data) { return window.Exp.io.call('ezjscnode::updatepriority', [], { method: 'POST', data: data }); },
                // the rows next to the page, asked for fresh (the table's cache is emptied after every save)
                neighbour: function (offset) {
                    return table.fetch($.extend(table.stateCopy(), { offset: offset, limit: 1 })).then(function (res) {
                        var r = res && res.rows && res.rows[0];
                        return r ? { id: r.node_id, priority: r.priority } : null;
                    });
                }
            };
        };
        var errorText = function (e) { return (e && e.message) || L('error_unknown', 'unknown error'); };

        /** Saves the order the rows now have in the table; tr is the moved row, from its index before. */
        var commit = function (tr, from) {
            // a row the table has drawn again in the meantime (a reload) is not saved
            if (!document.body.contains(tr)) { return; }
            var id = keyOf(tr), rows = table.rowsData.slice(), count = table.total, seq = table.seq;
            var before = rows.map(function (r) { return { id: String(r.node_id), priority: r.priority }; });
            var after = rowsEls().map(keyOf);
            var to = after.indexOf(id);
            if (to === from) { return; }
            say(L('moved', 'Moved to position %position of %count', { '%position': table.state.offset + to + 1, '%count': count }));
            setBusy(true);
            table.flushCache();
            save(ctx(), before, after).then(function (res) {
                setBusy(false);
                // the neighbours were read during the save: the cache holds them with the old priorities
                table.flushCache();
                if (table.seq !== seq) {
                    // the table loaded other rows meanwhile: they are not overwritten with this page's
                    if (res.status === 'saved') { saved(res.changed, id); }
                    else if (res.status !== 'unchanged') { toast(res.status === 'conflict' ? L('conflict', 'Someone else changed the order in the meantime. Nothing was saved; the list shows the current order now.') : L('error', 'The order was not saved: %error', { '%error': errorText(res.error) }), res.status === 'conflict' ? 'warn' : 'bad'); }
                    table.load();
                    return;
                }
                if (res.status === 'saved') {
                    var byId = {};
                    rows.forEach(function (r) { byId[String(r.node_id)] = r; });
                    table.rowsData = after.map(function (k) { var r = byId[k]; r.priority = res.values[k]; return r; });
                    table.render();
                    sync();
                    focusGrip(id);
                    saved(res.changed, id);
                } else if (res.status === 'conflict') {
                    table.render();
                    toast(L('conflict', 'Someone else changed the order in the meantime. Nothing was saved; the list shows the current order now.'), 'warn');
                    table.load().then(function () { focusGrip(id); });
                } else if (res.status === 'error') {
                    table.render();
                    sync();
                    focusGrip(id);
                    toast(L('error', 'The order was not saved: %error', { '%error': errorText(res.error) }), 'bad');
                }
            });
        };

        /** The toast after a save, with Undo when something changed. */
        var saved = function (changed, id) {
            if (undoToast && undoToast.parentNode) { undoToast.parentNode.removeChild(undoToast); }
            undoToast = null;
            // with Undo it stays 10 s, so it can be reached from the keyboard
            var t = toast(L('saved', 'Order saved'), 'ok', gripOf(id), Object.keys(changed || {}).length ? 10000 : 0);
            if (!t || !Object.keys(changed || {}).length) { return; }
            var b = document.createElement('button');
            b.type = 'button';
            b.className = 'a4-toast-action';
            b.textContent = L('undo', 'Undo');
            t.insertBefore(b, t.querySelector('.a4-toast-close'));
            b.addEventListener('click', function () {
                if (busy) { say(L('busy', 'Please wait until the order is saved.')); return; }
                if (t.parentNode) { t.parentNode.removeChild(t); }
                if (undoToast === t) { undoToast = null; }
                restore(changed, id);
            });
            undoToast = t;
        };

        var restore = function (changed, id) {
            if (busy) { say(L('busy', 'Please wait until the order is saved.')); return; }
            setBusy(true);
            undo(ctx(), changed).then(function (res) {
                setBusy(false);
                table.flushCache();
                if (res.status === 'saved') { toast(L('restored', 'Previous order restored'), 'ok'); }
                else if (res.status === 'conflict') { toast(L('conflict', 'Someone else changed the order in the meantime. Nothing was saved; the list shows the current order now.'), 'warn'); }
                else if (res.status === 'error') { toast(L('undo_error', 'The previous order was not restored: %error', { '%error': errorText(res.error) }), 'bad'); }
                table.load().then(function () { focusGrip(id); });
            });
        };

        // ---- keyboard ----------------------------------------------------------------------------------
        var endGrab = function (cancel) {
            var g = grab;
            if (!g) { return null; }
            grab = null;
            $(g.tr).removeClass('a4-content-drag-placeholder');
            $(g.grip).attr('aria-pressed', null);
            table.$root.removeClass('a4-content-drag-moving');
            if (cancel) { moveTo(g.tr, g.from); }
            return g;
        };
        var onKey = function (e) {
            var grip = this, tr = rowOf(grip), action = keyAction(!!grab && grab.tr === tr, e.originalEvent || e);
            if (!action || drag) { return; }
            e.preventDefault();
            e.stopPropagation();
            var rows = rowsEls(), i = rows.indexOf(tr), count = table.total;
            if (action === 'pickup') {
                if (grab) { endGrab(true); }
                if (!canStart(grip)) { return; }
                grab = { tr: tr, grip: grip, from: i };
                $(tr).addClass('a4-content-drag-placeholder');
                $(grip).attr('aria-pressed', 'true');
                table.$root.addClass('a4-content-drag-moving');
                say(L('picked', 'Picked up %name. Position %position of %count.', { '%name': nameOf(tr), '%position': position(tr), '%count': count }));
            } else if (action === 'stepUp' || action === 'stepDown') {
                if (!canStart(grip)) { return; }
                if (moveTo(tr, i + (action === 'stepUp' ? -1 : 1))) { grip.focus(); commit(tr, i); }
            } else if (action === 'drop') {
                var g = endGrab(false);
                if (rowsEls().indexOf(tr) === g.from) { say(L('dropped_same', 'Dropped. The order is unchanged.')); return; }
                commit(tr, g.from);
            } else if (action === 'cancel') {
                endGrab(true);
                grip.focus();
                say(L('cancelled', 'Move cancelled. %name is back at position %position of %count.', { '%name': nameOf(tr), '%position': position(tr), '%count': count }));
            } else {
                var j = { up: i - 1, down: i + 1, first: 0, last: rows.length - 1 }[action];
                if (moveTo(tr, j)) { grip.focus(); }
                say(L('position', 'Position %position of %count', { '%position': position(tr), '%count': count }));
            }
        };
        // leaving a picked-up grip (Tab, a click elsewhere) cancels the move
        var onBlur = function (e) {
            if (!grab || grab.grip !== this) { return; }
            var to = e.relatedTarget || (e.originalEvent && e.originalEvent.relatedTarget);
            if (to === this) { return; }
            var tr = grab.tr;
            endGrab(true);
            say(L('cancelled', 'Move cancelled. %name is back at position %position of %count.', { '%name': nameOf(tr), '%position': position(tr), '%count': table.total }));
        };

        // ---- pointer ------------------------------------------------------------------------------------
        var scroller = function (el) {
            for (var n = el.parentElement; n && n !== document.body && n !== document.documentElement; n = n.parentElement) {
                var oy = window.getComputedStyle(n).overflowY;
                if ((oy === 'auto' || oy === 'scroll') && n.scrollHeight > n.clientHeight) { return n; }
            }
            return null;
        };
        var reducedMotion = function () { return !!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches); };
        var place = function () {
            var d = drag, rows = rowsEls(), j = rows.length - 1;
            for (var k = 0; k < rows.length; k++) {
                if (rows[k] === d.tr) { continue; }
                var r = rows[k].getBoundingClientRect();
                if (d.y < r.top + r.height / 2) { j = k > rows.indexOf(d.tr) ? k - 1 : k; break; }
            }
            moveTo(d.tr, j);
            d.ghost.style.setProperty('--a4-content-drag-y', Math.round(d.y - d.grabY) + 'px');
        };
        var autoScroll = function () {
            var d = drag;
            if (!d) { return; }
            var edge = 48, top = 0, bottom = window.innerHeight, el = d.scroller;
            if (el) { var r = el.getBoundingClientRect(); top = Math.max(top, r.top); bottom = Math.min(bottom, r.bottom); }
            var dy = 0;
            if (d.y < top + edge) { dy = -Math.ceil((top + edge - d.y) / edge * 16); }
            else if (d.y > bottom - edge) { dy = Math.ceil((d.y - bottom + edge) / edge * 16); }
            if (dy) {
                if (reducedMotion()) { dy = dy > 0 ? 8 : -8; }
                if (el) { el.scrollTop += dy; } else { window.scrollBy(0, dy); }
                place();
            }
            d.frame = window.requestAnimationFrame(autoScroll);
        };
        var startDrag = function () {
            var d = drag, rect = d.tr.getBoundingClientRect(), tableRect = table.$table[0].getBoundingClientRect();
            d.started = true;
            d.grabY = d.y - rect.top;
            d.scroller = scroller(table.$table[0]);
            var ghost = document.createElement('div');
            ghost.className = 'a4-content-drag-ghost';
            ghost.setAttribute('aria-hidden', 'true');
            ghost.style.setProperty('--a4-content-drag-x', Math.round(tableRect.left) + 'px');
            ghost.style.setProperty('--a4-content-drag-w', Math.round(Math.min(tableRect.width, 560)) + 'px');
            var icon = document.createElement('span');
            icon.className = 'a4-icon a4-bi-grip-vertical';
            var name = document.createElement('span');
            name.className = 'a4-content-drag-ghost-name';
            name.textContent = nameOf(d.tr);
            ghost.appendChild(icon);
            ghost.appendChild(name);
            document.body.appendChild(ghost);
            d.ghost = ghost;
            $(d.tr).addClass('a4-content-drag-placeholder');
            table.$root.addClass('a4-content-drag-moving');
            place();
            d.frame = window.requestAnimationFrame(autoScroll);
        };
        var stopDrag = function (cancel) {
            var d = drag;
            if (!d) { return null; }
            drag = null;
            window.removeEventListener('pointermove', onMove);
            window.removeEventListener('pointerup', onUp);
            window.removeEventListener('pointercancel', onCancel);
            document.removeEventListener('keydown', onEscape, true);
            if (d.frame) { window.cancelAnimationFrame(d.frame); }
            if (d.ghost && d.ghost.parentNode) { d.ghost.parentNode.removeChild(d.ghost); }
            $(d.tr).removeClass('a4-content-drag-placeholder');
            table.$root.removeClass('a4-content-drag-moving');
            if (cancel) { moveTo(d.tr, d.from); }
            return d;
        };
        var onMove = function (e) {
            var d = drag;
            if (!d || e.pointerId !== d.pointerId) { return; }
            // the mouse button was let go outside the window: the drag ends there
            if (e.pointerType === 'mouse' && e.buttons === 0) { onUp(e); return; }
            d.y = e.clientY;
            if (!d.started) {
                if (Math.abs(d.y - d.startY) < 4) { return; }
                startDrag();
            }
            e.preventDefault();
            place();
        };
        var onUp = function (e) {
            if (!drag || e.pointerId !== drag.pointerId) { return; }
            var d = stopDrag(false);
            d.grip.focus();
            if (d.started) { commit(d.tr, d.from); }
        };
        var onCancel = function (e) {
            if (!drag || e.pointerId !== drag.pointerId) { return; }
            var d = stopDrag(true);
            if (d.started) { say(L('cancelled', 'Move cancelled. %name is back at position %position of %count.', { '%name': nameOf(d.tr), '%position': position(d.tr), '%count': table.total })); }
        };
        var onEscape = function (e) {
            if (e.key !== 'Escape' || !drag) { return; }
            e.preventDefault();
            e.stopPropagation();
            var d = stopDrag(true);
            d.grip.focus();
            say(L('cancelled', 'Move cancelled. %name is back at position %position of %count.', { '%name': nameOf(d.tr), '%position': position(d.tr), '%count': table.total }));
        };
        var onPointerDown = function (e) {
            var ev = e.originalEvent || e, grip = this;
            if (drag || grab || !ev.isPrimary || (ev.pointerType === 'mouse' && ev.button !== 0)) { return; }
            // no text selection, no focus change before the check (an open priority editor keeps the focus)
            e.preventDefault();
            if (!canStart(grip)) { return; }
            grip.focus();
            var tr = rowOf(grip);
            drag = { tr: tr, grip: grip, from: rowsEls().indexOf(tr), pointerId: ev.pointerId, startY: ev.clientY, y: ev.clientY, started: false };
            window.addEventListener('pointermove', onMove, { passive: false });
            window.addEventListener('pointerup', onUp);
            window.addEventListener('pointercancel', onCancel);
            document.addEventListener('keydown', onEscape, true);
        };

        return {
            column: column,
            busy: function () { return busy || !!drag || !!grab; },
            attach: function (t) {
                if (!t) { return; }
                table = t;
                table.$root.append($('<p class="a4-sr-only"></p>').attr('id', hintId)
                    .text(L('hint', 'Drag the grip to change the order. With the keyboard: Space picks the row up, the arrow keys move it, Space drops it, Escape cancels. Alt+Up and Alt+Down move it by one place at once.')));
                table.$el.on('exp:datatable:load', function () { pendingEdit = false; if (grab) { endGrab(false); } if (drag) { stopDrag(false); } sync(); });
                table.$el.on('exp:datatable:columns', sync);
                table.$el.on('exp:datatable:edit', function () { pendingEdit = true; });
                table.$el.on('exp:datatable:error', function () { pendingEdit = false; });
                table.$body.on('pointerdown', '.a4-content-drag-grip', onPointerDown);
                table.$body.on('keydown', '.a4-content-drag-grip', onKey);
                table.$body.on('focusout', '.a4-content-drag-grip', onBlur);
                // a drag that started on the grip shows the grip as pressed; a plain click does nothing
                table.$body.on('click', '.a4-content-drag-grip', function (e) { e.preventDefault(); });
                sync();
            }
        };
    }

    return { create: create, isActive: isActive, assign: assign, keyAction: keyAction, save: save, undo: undo, STEP: STEP };
}));
