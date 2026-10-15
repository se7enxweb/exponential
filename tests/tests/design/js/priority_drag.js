// Runs the pure parts of admin4's drag and drop reordering of the sub items (design/admin4/javascript/
// subitems_priority_drag.js): when the grips are on, how the priorities of a page are given out again (direction,
// equal priorities, the neighbours on the pages before and after), the keys of a grip, and saving, undo and a
// conflict against a stand-in for ezjscnode::updatepriority. Prints one JSON line per scenario.
// Usage: node priority_drag.js <path to subitems_priority_drag.js>
'use strict';
const api = require(process.argv[2]);

const out = (name, value) => process.stdout.write(JSON.stringify({ name, value }) + '\n');
const rows = (pairs) => pairs.map(([id, priority]) => ({ id, priority }));

// ---- when the grips are on ----------------------------------------------------------------------------------
const conf = { priorityDrag: true, sortOrder: 1, nameFilter: '' };
const asc = { sort: { key: 'priority', dir: 'asc' }, filter: '' };
out('active: priority, may edit', api.isActive(conf, asc, 3));
out('active: may not edit', api.isActive(Object.assign({}, conf, { priorityDrag: false }), asc, 3));
out('active: not sorted by priority', api.isActive(Object.assign({}, conf, { priorityDrag: false }), { sort: { key: 'name', dir: 'asc' } }, 3));
out('active: table sorted by name', api.isActive(conf, { sort: { key: 'name', dir: 'asc' } }, 3));
out('active: table in the other direction', api.isActive(conf, { sort: { key: 'priority', dir: 'desc' } }, 3));
out('active: descending parent', api.isActive(Object.assign({}, conf, { sortOrder: 0 }), { sort: { key: 'priority', dir: 'desc' } }, 3));
out('active: name filter', api.isActive(Object.assign({}, conf, { nameFilter: 'a' }), asc, 3));
out('active: table filter', api.isActive(conf, { sort: asc.sort, filter: 'x' }, 3));
out('active: one row', api.isActive(conf, asc, 1));
out('active: priorityDrag must be true', api.isActive(Object.assign({}, conf, { priorityDrag: 'true' }), asc, 3));

// ---- giving out the priorities ------------------------------------------------------------------------------
out('permute ascending', api.assign(rows([['a', 10], ['b', 20], ['c', 30]]), ['c', 'a', 'b'], 'asc', {}));
out('permute descending', api.assign(rows([['a', 30], ['b', 20], ['c', 10]]), ['c', 'a', 'b'], 'desc', {}));
out('permute between neighbours', api.assign(rows([['a', 10], ['b', 20], ['c', 30]]), ['b', 'c', 'a'], 'asc', { prev: 5, next: 40 }));
out('equal, no neighbours', api.assign(rows([['a', 0], ['b', 0], ['c', 0]]), ['b', 'a', 'c'], 'asc', {}));
out('equal, narrow room', api.assign(rows([['a', 6], ['b', 6], ['c', 6]]), ['c', 'b', 'a'], 'asc', { prev: 5, next: 9 }));
out('equal, no room', api.assign(rows([['a', 0], ['b', 0]]), ['b', 'a'], 'asc', { prev: 0, next: 0 }));
out('tie with the page before', api.assign(rows([['a', 5], ['b', 6], ['c', 7]]), ['c', 'a', 'b'], 'asc', { prev: 5, next: 20 }));
out('equal, descending', api.assign(rows([['a', 30], ['b', 30], ['c', 30]]), ['c', 'a', 'b'], 'desc', { prev: 50, next: 10 }));
out('equal, first page', api.assign(rows([['a', 0], ['b', 0], ['c', 0]]), ['a', 'c', 'b'], 'asc', { prev: null, next: 0 }));
out('equal, last page', api.assign(rows([['a', 5], ['b', 5]]), ['b', 'a'], 'asc', { prev: 5, next: null }));
out('equal, first page, descending', api.assign(rows([['a', 0], ['b', 0]]), ['b', 'a'], 'desc', { prev: null, next: 0 }));

// ---- keys -----------------------------------------------------------------------------------------------------
const k = (grabbed, key, mods) => api.keyAction(grabbed, Object.assign({ key, altKey: false, ctrlKey: false, metaKey: false, shiftKey: false }, mods || {}));
out('keys', {
    space: k(false, ' '), enter: k(false, 'Enter'), arrow: k(false, 'ArrowDown'), altUp: k(false, 'ArrowUp', { altKey: true }),
    altDown: k(false, 'ArrowDown', { altKey: true }), ctrlSpace: k(false, ' ', { ctrlKey: true }),
    grabbedUp: k(true, 'ArrowUp'), grabbedDown: k(true, 'ArrowDown'), grabbedHome: k(true, 'Home'), grabbedEnd: k(true, 'End'),
    grabbedSpace: k(true, ' '), grabbedEnter: k(true, 'Enter'), grabbedEscape: k(true, 'Escape'), grabbedTab: k(true, 'Tab'),
    grabbedAltUp: k(true, 'ArrowUp', { altKey: true }), escape: k(false, 'Escape')
});

// ---- saving against a stand-in server -------------------------------------------------------------------------
function server(priorities, opts) {
    opts = opts || {};
    const calls = [], fetched = [];
    const parse = (text) => (text ? text.split(',') : []).reduce((m, p) => { const [id, v] = p.split(':'); m[id] = Number(v); return m; }, {});
    const order = () => Object.keys(priorities).sort((a, b) => priorities[a] - priorities[b] || Number(a) - Number(b));
    return {
        calls, fetched, priorities,
        ctx(offset, total, dir) {
            return {
                dir: dir || 'asc', offset, total, nodeID: 2, objectID: 1,
                neighbour: (o) => { fetched.push(o); const id = order()[o]; return Promise.resolve(id ? { id, priority: priorities[id] } : null); },
                call: (data) => {
                    calls.push(data);
                    if (opts.fail) { return Promise.reject(new Error('HTTP 500')); }
                    if (opts.before) { opts.before(priorities); }
                    const expected = parse(data.ExpectedPriorityList);
                    const nodes = Object.keys(expected).filter((id) => priorities[id] !== expected[id]);
                    if (nodes.length) { return Promise.resolve({ conflict: true, nodes: nodes.map(Number) }); }
                    let set = parse(data.PriorityList);
                    if (data.Renumber === '1') {
                        const page = data.PageOrder.split(','), rest = order().filter((id) => page.indexOf(id) === -1);
                        const all = rest.slice(0, data.PageOffset).concat(page, rest.slice(data.PageOffset));
                        set = {};
                        all.forEach((id, i) => { set[id] = (i + 1) * 10; });
                    }
                    const changed = {};
                    Object.keys(set).forEach((id) => { if (priorities[id] !== set[id]) { changed[id] = [priorities[id], set[id]]; priorities[id] = set[id]; } });
                    return Promise.resolve({ conflict: false, changed });
                }
            };
        }
    };
}

(async () => {
    // a page in the middle: rows 3 to 5 of 7
    let s = server({ 1: 10, 2: 20, 3: 30, 4: 40, 5: 50, 6: 60, 7: 70 });
    let res = await api.save(s.ctx(2, 7), rows([['3', 30], ['4', 40], ['5', 50]]), ['5', '3', '4']);
    out('save: permute', { res, call: s.calls[0], fetched: s.fetched, priorities: s.priorities });
    // undo puts the old values back
    const undone = await api.undo(s.ctx(2, 7), res.changed);
    out('undo', { res: undone, call: s.calls[1], priorities: s.priorities });

    // the order did not change: no call
    s = server({ 1: 10, 2: 20 });
    res = await api.save(s.ctx(0, 2), rows([['1', 10], ['2', 20]]), ['1', '2']);
    out('save: unchanged', { res, calls: s.calls.length, fetched: s.fetched });

    // first page: no row before it is asked for; equal priorities get distinct ones
    s = server({ 1: 0, 2: 0, 3: 0, 4: 100 });
    res = await api.save(s.ctx(0, 4), rows([['1', 0], ['2', 0], ['3', 0]]), ['3', '1', '2']);
    out('save: first page, equal priorities', { res, call: s.calls[0], fetched: s.fetched });

    // no room between the neighbours: the server numbers all again
    s = server({ 1: 0, 2: 0, 3: 0, 4: 0, 5: 0 });
    res = await api.save(s.ctx(2, 5), rows([['3', 0], ['4', 0]]), ['4', '3']);
    out('save: renumber', { res, call: s.calls[0], priorities: s.priorities });

    // someone else changed a row of the page in the meantime: nothing is written
    s = server({ 1: 10, 2: 20, 3: 30 }, { before: (p) => { if (p[2] === 20) { p[2] = 25; } } });
    res = await api.save(s.ctx(0, 3), rows([['1', 10], ['2', 20], ['3', 30]]), ['3', '1', '2']);
    out('save: conflict', { res, priorities: s.priorities });

    // a neighbour changed: also a conflict
    s = server({ 1: 10, 2: 20, 3: 30, 4: 40 }, { before: (p) => { if (p[1] === 10) { p[1] = 35; } } });
    res = await api.save(s.ctx(1, 4), rows([['2', 20], ['3', 30]]), ['3', '2']);
    out('save: neighbour conflict', { res, priorities: s.priorities });

    // the server fails: an error, never a rejection
    s = server({ 1: 10, 2: 20 }, { fail: true });
    res = await api.save(s.ctx(0, 2), rows([['1', 10], ['2', 20]]), ['2', '1']);
    out('save: error', { status: res.status, message: res.error && res.error.message });

    // a server without the check answers [] (nothing written): an error, not "saved"
    const older = { dir: 'asc', offset: 0, total: 2, nodeID: 2, objectID: 1, neighbour: () => Promise.resolve(null), call: () => Promise.resolve([]) };
    res = await api.save(older, rows([['1', 10], ['2', 20]]), ['2', '1']);
    out('save: older server', { status: res.status });
    out('undo: older server', { status: (await api.undo(older, { 1: [10, 20] })).status });

    // undo after someone else moved a row again: conflict
    s = server({ 1: 10, 2: 20, 3: 30 });
    res = await api.save(s.ctx(0, 3), rows([['1', 10], ['2', 20], ['3', 30]]), ['2', '1', '3']);
    s.priorities[1] = 99;
    out('undo: conflict', { res: await api.undo(s.ctx(0, 3), res.changed), priorities: s.priorities });
    out('undo: nothing changed', await api.undo(s.ctx(0, 3), {}));
})();
