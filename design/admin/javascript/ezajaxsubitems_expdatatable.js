/**
 * The admin's sub-items table on Exponential UI's $.fn.expDataTable (exp::datatable).
 *
 * Started by children_detailed.tpl when Exponential UI is there (window.Exp && Exp.$.fn.expDataTable), with the
 * four objects:
 *
 *   eZAjaxSubitemsExpDataTable.init(confObj, labelsObj, createGroups, createOptions)
 *
 * It does: the ezjscnode::subtree request (GET, same arguments), the
 * cache of 20 pages, sorting, the pagers in #bpg and #tpg, rows per page saved as admin_list_limit (and a custom
 * number that is not saved), the shown columns in the eZSubitemColumns cookie (one sub-value per navigation part),
 * inline priority editing through ezjscnode::updatepriority, the Select, Create new, Create multiple new, More
 * actions and Table options controls (same ids, same form fields posted) and the context menu of each row.
 *
 * With the subitems column registry (confObj.subitemsServer, settings/subitemscolumns.ini) the columns come from
 * expsubitems::columns (only the ones this user may see), the rows from expsubitems::rows with the shown registry
 * columns (computed by the server for the rows of the page only), and the choice - shown columns in their order,
 * rows per page, the preset and the user's own presets - is saved with expsubitems::savepreference. The first time,
 * the columns from the eZSubitemColumns cookie are taken over. Table options then also has a column filter, the
 * columns by group, their order (drag, or the up and down buttons), presets and Export CSV (content/subitemsexport).
 * When expsubitems::columns cannot be called, the table is the classic one above.
 *
 * design/admin and design/admin4 each keep this file, byte for byte the same: admin4 is a complete design that
 * must work with no other admin design present, so it cannot rely on design/admin's copy, and one copy in
 * design/standard would be shadowed by any admin design that has its own. Change both together.
 */
var eZAjaxSubitemsExpDataTable = (function () {
    'use strict';

    // ---- the eZSubitemColumns cookie, as YAHOO.util.Cookie.getSub / setSub wrote it ----------------------------
    // name=sub1=value1&sub2=value2, each sub name and value encodeURIComponent()ed; the value is escape()d first

    function readSubs(name) {
        var all = document.cookie ? document.cookie.split(/;\s/g) : [], subs = {};
        for (var i = 0; i < all.length; i++) {
            var eq = all[i].indexOf('=');
            if (eq === -1 || decodeURIComponent(all[i].substring(0, eq)) !== name) { continue; }
            var hash = all[i].substring(eq + 1);
            if (!hash) { break; }
            hash.split('&').forEach(function (pair) {
                var k = pair.indexOf('=');
                if (k === -1) { return; }
                try { subs[decodeURIComponent(pair.substring(0, k))] = decodeURIComponent(pair.substring(k + 1)); } catch (e) { /* not ours */ }
            });
            break;
        }
        return subs;
    }

    function getCookieSubMultiValue(name, sub) {
        var subs = readSubs(name);
        if (!Object.prototype.hasOwnProperty.call(subs, sub)) { return null; }
        return window.unescape(subs[sub]).split('|');
    }

    function setCookieSubMultiValue(name, sub, values, secure) {
        var subs = readSubs(name), expires = new Date(), parts = [];
        subs[sub] = window.escape(values.join('|'));
        expires.setFullYear(expires.getFullYear() + 10);
        Object.keys(subs).forEach(function (k) { parts.push(encodeURIComponent(k) + '=' + encodeURIComponent(subs[k])); });
        document.cookie = encodeURIComponent(name) + '=' + parts.join('&') + '; expires=' + expires.toUTCString() +
                          '; path=/' + (secure ? '; secure' : '');
    }

    // Where Edit selected and Create multiple new send the list form: the URL the template gives, else the form's
    // own action with its last part changed (it carries this siteaccess and port; a bare /content/multiedit went
    // to the public siteaccess).
    function multiEditURLFor(form) {
        if (typeof window.eZExpMultiEditURL === 'string' && window.eZExpMultiEditURL) { return window.eZExpMultiEditURL; }
        var action = form.attr('action') || '';
        return /content\/action(\?.*)?$/.test(action) ? action.replace(/content\/action(\?.*)?$/, 'content/multiedit') : '/content/multiedit';
    }

    // ---- the subitems column registry (expsubitems::columns, ::rows, ::savepreference) ------------------------

    // The 15 columns the table draws itself from the node JSON: the registry's key -> the row field (and the sort
    // name ezjscnode::subtree knows, and the key the eZSubitemColumns cookie and admininterface.ini used).
    var BUILTIN = {
        thumbnail: 'thumbnail', name: 'name', visibility: 'hidden_status_string', type: 'class_name', modifier: 'creator',
        modified: 'modified_date', published: 'published_date', translations: 'translations', section: 'section',
        nodeid: 'node_id', noderemoteid: 'node_remote_id', objectid: 'contentobject_id', objectremoteid: 'contentobject_remote_id',
        objectstate: 'contentobject_state', priority: 'priority'
    };
    var FIELD_TO_KEY = {};
    Object.keys(BUILTIN).forEach(function (k) { FIELD_TO_KEY[BUILTIN[k]] = k; });

    function sameList(a, b) { return a.length === b.length && a.every(function (k, i) { return k === b[i]; }); }

    // A registry column's cell of a row from expsubitems::rows: { v: value, h: html }, or null.
    function registryCell(row, key) { return (row.columns && row.columns[key]) || null; }

    // The text a click on a cell copies: '' for nothing, a list joined with ', ', a map as JSON.
    function copyText(v) {
        if (v === undefined || v === null) { return ''; }
        return typeof v === 'object' ? (Array.isArray(v) ? v.join(', ') : JSON.stringify(v)) : v;
    }

    /**
     * Starts the table. With the subitems server functions (confObj.subitemsServer) it asks expsubitems::columns
     * for the columns this user may see, then loads rows with expsubitems::rows; when that call fails (an
     * installation without them) the table is the classic one on ezjscnode::subtree.
     */
    function init(confObj, labelsObj, createGroups, createOptions) {
        if (!confObj.subitemsServer || !window.Exp.io) { return build(confObj, labelsObj, createGroups, createOptions, null); }
        return window.Exp.io.call('expsubitems::columns', [confObj.nodeID]).then(function (meta) {
            if (!meta || !Array.isArray(meta.columns) || !meta.columns.length) { throw new Error('no columns'); }
            return build(confObj, labelsObj, createGroups, createOptions, meta);
        }).catch(function (e) {
            if (window.console) { window.console.warn('sub items: the column list was not loaded, the classic table is used', e && e.message); }
            return build(confObj, labelsObj, createGroups, createOptions, null);
        });
    }

    function build(confObj, labelsObj, createGroups, createOptions, meta) {
        var $ = window.Exp.$;
        var shownColumns = getCookieSubMultiValue(confObj.cookieName, confObj.navigationPart);
        var cookieColumns = shownColumns;
        if (shownColumns === null) { shownColumns = confObj.defaultShownColumns[confObj.navigationPart]; }
        var L = labelsObj.DATA_TABLE_COLS, A = labelsObj.ACTION_BUTTONS, O = labelsObj.TABLE_OPTIONS;
        var form = function () { return $('form[name=children]').first(); };
        var selectedCount = function () { return $('form[name=children] input.ezsubitems_delete_checkbox:checked').length; };

        // ---- cell formatters ----------------------------------------------------
        var formatName = function (row) {
            return '<a href="' + row.url + '" title="' + row.name + '">' + row.class_icon + '</a>' + '&nbsp;' +
                   '<a href="' + row.url + '" title="' + row.name + '">' + row.name + '</a>';
        };
        var customMenu = function (row) {
            var createhereMenu = (confObj.classesString != '') ? -1 : "'child-menu-create-here'";
            var translationArray = [];
            $.each(row.translations || [], function (i, e) { translationArray.push({ 'locale': e, 'name': confObj.languages[e] }); });
            var a = $('<a></a>').attr({ href: '#', role: 'button', 'aria-label': row.name, 'aria-haspopup': 'menu' })
                .append('<div class="crankfield"></div>');
            a.on('click', function (e) {
                e.preventDefault();
                var ev = e.originalEvent || e;
                if (!ev.pageX && !ev.pageY) {   // opened with the keyboard: under the link
                    var r = this.getBoundingClientRect();
                    ev = { pageX: r.left + window.pageXOffset, pageY: r.bottom + window.pageYOffset };
                }
                window.ezpopmenu_showTopLevel(ev, 'SubitemsContextMenu', {
                    '%nodeID%': row.node_id, '%objectID%': row.contentobject_id, '%version%': row.version,
                    '%languages%': translationArray, '%classList%': confObj.classesString
                }, row.name, row.node_id, createhereMenu);
            });
            return a;
        };
        var thumbView = function (row) {
            var url = encodeURI(row.thumbnail_url);
            if (url) {
                var thBack = 'background: url(\'' + url.replace(/'/g, "\\'") + '\') no-repeat;';
                var thWidth = ' width: ' + row.thumbnail_width + 'px;';
                var thHeight = ' height: ' + row.thumbnail_height + 'px;';
                return '<div class="thumbview"><div id="thumbfield" class="thumbfield"></div><span><div style="' + thBack + thWidth + thHeight + '"></div></span></div>';
            }
            return '';
        };
        var translationView = function (row) {
            var html = '';
            $.each(row.translations || [], function (i, e) {
                if (row.can_edit === true) { html += '<a href="' + confObj.editPrefixURL + '/' + row.contentobject_id + '/f/' + e + '">'; }
                html += '<img src="' + confObj.flagIcons[e] + '" width="18" height="12" style="margin-right: 4px;" alt="' + e + '" title="' + e + '"/>';
                if (row.can_edit === true) { html += '</a>'; }
            });
            return html;
        };
        var text = function (key) {
            return function (row) { var v = row[key]; return v === null || v === undefined ? '' : String(v).replace(/&/g, '&#38;').replace(/</g, '&#60;').replace(/>/g, '&#62;'); };
        };

        var columns = [
            { key: 'checkbox', label: '' },
            { key: 'crank', label: '', render: customMenu },
            { key: 'thumbnail', label: L.thumbnail, labelHidden: true, render: thumbView },
            { key: 'name', label: L.name, sortable: true, render: formatName },
            { key: 'hidden_status_string', label: L.visibility, sortable: true, render: text('hidden_status_string') },
            { key: 'class_name', label: L.type, sortable: true, render: text('class_name') },
            { key: 'creator', label: L.modifier, render: text('creator') },
            { key: 'modified_date', label: L.modified, sortable: true, render: text('modified_date') },
            { key: 'published_date', label: L.published, sortable: true, render: text('published_date') },
            { key: 'translations', label: L.translations, render: translationView },
            { key: 'section', label: L.section, sortable: true, render: text('section') },
            { key: 'node_id', label: L.nodeid, sortable: true, render: text('node_id') },
            { key: 'node_remote_id', label: L.noderemoteid, render: text('node_remote_id') },
            { key: 'contentobject_id', label: L.objectid, sortable: true, render: text('contentobject_id') },
            { key: 'contentobject_remote_id', label: L.objectremoteid, render: text('contentobject_remote_id') },
            { key: 'contentobject_state', label: L.objectstate, render: text('contentobject_state') },
            { key: 'priority', label: L.priority, sortable: true, editable: 'number', render: text('priority') }
        ];
        // Hidden columns from the cookie, with the ini setting as fallback; neither: all columns are shown
        if (shownColumns && shownColumns.length !== 0) {
            columns.forEach(function (c) { if (shownColumns.indexOf(c.key) === -1 && c.label !== '') { c.hidden = true; } });
        }

        // the server's rows
        var parseRow = function (r) {
            var row = $.extend({}, r);
            row.creator = r.creator && r.creator.name ? r.creator.name : '?';
            row.section = r.section && r.section.name ? r.section.name : '?';
            row.translations = r.translations ? r.translations.language_list : undefined;
            return row;
        };

        var $list = $('#content-sub-items-list');
        var sortOptions = { key: confObj.sortKey, dir: confObj.sortOrder === 1 ? 'asc' : 'desc' };
        var rowsPerPage = confObj.rowsPrPage;
        var server = meta ? serverMode(meta) : null;

        /** The table on the column registry: its columns, rows source, saved choice, presets and CSV export. */
        function serverMode(meta) {
            var O2 = labelsObj.TABLE_OPTIONS || {}, byField = {};
            columns.forEach(function (c) { byField[c.key] = c; });
            var available = {}, list = [];
            meta.columns.forEach(function (m) {
                if (!m || !m.key || available[m.key]) { return; }
                available[m.key] = m;
                var field = BUILTIN[m.key], c;
                var title = m.description || '';
                if (field && byField[field]) {
                    // a built-in: drawn as before from the node JSON, under the registry's key
                    var old = byField[field];
                    c = $.extend({}, old, { key: m.key, field: field, label: old.label || m.name, group: m.group || '', title: title,
                                            align: m.align || old.align, hidden: false });
                    if (m.copy) { c.copy = (function (f) { return function (row) { var v = row[f]; return v === null || v === undefined ? '' : v; }; }(field)); }
                } else if (!field) {
                    // a registry column: the server's (escaped) HTML, the raw value for copying
                    c = { key: m.key, label: m.name || m.key, group: m.group || '', title: title, align: m.align, remote: true,
                          sortable: !!m.sortable, className: 'exp-subitems-col exp-subitems-type-' + String(m.type || 'text').replace(/[^a-z0-9_-]/gi, ''),
                          render: (function (k) { return function (row) { var x = registryCell(row, k); return x && x.h !== undefined && x.h !== null ? String(x.h) : ''; }; }(m.key)) };
                    if (m.copy) {
                        c.copy = (function (k) { return function (row) { var x = registryCell(row, k); return x ? copyText(x.v) : ''; }; }(m.key));
                    }
                }
                if (c) { list.push(c); }
            });
            // the inline priority editor writes row.priority; the editable column has the key 'priority' in both modes
            var fixed = columns.filter(function (c) { return c.key === 'checkbox' || c.key === 'crank'; });
            columns = fixed.concat(list);

            // the visible columns: the saved choice, else the eZSubitemColumns cookie once (migrated), else the defaults
            var known = function (keys) { return (keys || []).filter(function (k) { return available[k] && k !== 'checkbox' && k !== 'crank'; }); };
            var pref = meta.preference || {};
            var saved = pref.saved === true || (pref.saved === undefined && Array.isArray(pref.visible) && pref.visible.length > 0);
            var state = { visible: [], preset: pref.preset || null, pageSize: Number(pref.page_size) || 0 };
            var migrate = false;
            if (saved && known(pref.visible).length) {
                state.visible = known(pref.visible);
            } else if (cookieColumns && cookieColumns.length) {
                state.visible = known(cookieColumns.map(function (f) { return FIELD_TO_KEY[f] || f; }));
                migrate = state.visible.length > 0;
            }
            if (!state.visible.length) { state.visible = known(meta.defaults); }
            if (!state.visible.length) { state.visible = known(list.map(function (c) { return c.key; })); }
            columns.forEach(function (c) { if (c.label && state.visible.indexOf(c.key) === -1) { c.hidden = true; } });

            var presets = (meta.presets || []).filter(function (p) { return p && p.id && Array.isArray(p.columns); }).map(function (p) {
                return { id: String(p.id), name: p.name || String(p.id), columns: p.columns, source: p.source === 'user' ? 'user' : 'ini' };
            });
            var userPresets = function () {
                var out = {};
                presets.forEach(function (p) { if (p.source === 'user') { out[p.id] = { name: p.name, columns: p.columns }; } });
                return out;
            };
            var payload = function () {
                return JSON.stringify({ visible: state.visible, preset: state.preset, page_size: state.pageSize || null, presets: userPresets() });
            };
            var timer = null;
            var flush = function () {
                if (timer) { window.clearTimeout(timer); timer = null; }
                return window.Exp.io.call('expsubitems::savepreference', [confObj.nodeID], { data: { preference: payload() } }).catch(function (e) {
                    if (window.console) { window.console.warn('sub items: the table options were not saved', e && e.message); }
                });
            };
            var queueSave = function () {
                if (timer) { window.clearTimeout(timer); }
                timer = window.setTimeout(flush, 700);
            };
            // leaving the page within the delay: the last change still reaches the server
            window.addEventListener('pagehide', function () {
                if (!timer || !window.navigator.sendBeacon) { return; }
                window.clearTimeout(timer); timer = null;
                var body = new window.URLSearchParams();
                body.append('ezjscServer_function_arguments', 'expsubitems::savepreference::' + confObj.nodeID);
                body.append('ezxform_token', window.Exp.token ? window.Exp.token() : '');
                body.append('preference', payload());
                window.navigator.sendBeacon(window.Exp.config.call, body);
            });
            if (migrate) { flush(); }
            if (state.pageSize > 0) { rowsPerPage = state.pageSize; }

            // sorting: built-ins by the names ezjscnode::subtree knows, registry columns by their key
            var sortName = function (key) { return BUILTIN[key] || key; };
            var initial = FIELD_TO_KEY[confObj.sortKey] || confObj.sortKey;
            sortOptions = { key: initial, dir: confObj.sortOrder === 1 ? 'asc' : 'desc' };
            var remoteShown = function (table) {
                return table.visibleColumns().filter(function (k) { return available[k] && !BUILTIN[k]; });
            };
            var presetOf = function (id) { for (var i = 0; i < presets.length; i++) { if (presets[i].id === id) { return presets[i]; } } return null; };
            var exportURL = function (table) {
                var s = table.state.sort || sortOptions;
                return confObj.exportURL + '?columns=' + encodeURIComponent(table.shownColumns().join(',')) +
                       '&sort=' + encodeURIComponent(sortName(s.key)) + '&order=' + (s.dir === 'asc' ? '1' : '0');
            };

            return {
                source: {
                    fn: 'expsubitems::rows',
                    args: function (s) {
                        var a = [confObj.nodeID, s.limit, s.offset, sortName(s.sort.key), s.sort.dir === 'asc' ? '1' : '0'];
                        if (confObj.nameFilter) { a.push(confObj.nameFilter); }
                        return a;
                    },
                    data: function () { return { columns: remoteShown(this).join(',') }; },
                    parse: function (c) {
                        c = c || {};
                        return { rows: (c.list || []).map(parseRow), total: Number(c.total_count) || 0 };
                    },
                    cache: 20
                },
                columnToggle: {
                    shown: state.visible,
                    ordered: true,
                    save: function (keys) {
                        state.visible = keys.slice();
                        var p = state.preset ? presetOf(state.preset) : null;
                        if (p && !sameList(known(p.columns), keys)) { state.preset = null; if (this.$presets) { this.renderPresets(); } }
                        queueSave();
                    }
                },
                limitSaved: function (limit) { limit = Number(limit) || 0; state.pageSize = limit > 0 && limit <= 500 ? limit : 0; queueSave(); },
                copy: { title: O2.copy_title, done: O2.copied, failed: O2.copy_failed },
                tableOptions: {
                    columns: {
                        legend: O2.header_vtc,
                        filter: { label: O2.filter_label, placeholder: O2.filter_placeholder, none: O2.filter_none },
                        otherGroup: O2.group_other,
                        order: { legend: O2.order_legend, hint: O2.order_hint, up: O2.order_up, down: O2.order_down }
                    },
                    presets: {
                        legend: O2.presets_legend, none: O2.presets_none, choose: O2.presets_choose, name: O2.presets_name,
                        saveAs: O2.presets_save_as, remove: O2.presets_delete,
                        items: function () { return presets.map(function (p) { return { id: p.id, name: p.name, own: p.source === 'user', columns: p.columns }; }); },
                        current: function () { return state.preset; },
                        onApply: function (item) {
                            var keys = known(item.columns);
                            if (!keys.length) { return; }
                            state.preset = item.id;
                            this.setShown(keys);
                        },
                        onSave: function (name, keys) {
                            var id = 'u' + Date.now().toString(36);
                            presets.push({ id: id, name: name, columns: keys.slice(), source: 'user' });
                            state.preset = id;
                            return flush();
                        },
                        onDelete: function (item) {
                            presets = presets.filter(function (p) { return !(p.id === item.id && p.source === 'user'); });
                            if (state.preset === item.id) { state.preset = null; }
                            return flush();
                        }
                    },
                    buttons: confObj.exportURL && meta.csv_export !== false ? [{
                        id: 'ezbtn-subitems-export', label: O2.export_csv, title: O2.export_csv_title,
                        onClick: function (table) { window.location.assign(exportURL(table)); }
                    }] : []
                }
            };
        }

        $list.expDataTable({
            columns: columns,
            rowKey: 'node_id',
            copy: server ? server.copy : null,
            source: server ? server.source : {
                // GET <ezjscore/call/ezjscnode::subtree::node>::limit::offset::sort::order::filter?ContentType=json
                url: function (s) {
                    return confObj.dataSourceURL + '::' + s.limit + '::' + s.offset + '::' + s.sort.key + '::' +
                           (s.sort.dir === 'asc' ? '1' : '0') + '::' + confObj.nameFilter + '?ContentType=json';
                },
                parse: function (json) {
                    var c = (json && json.content) || {};
                    return { rows: (c.list || []).map(parseRow), total: Number(c.total_count) || 0 };
                },
                cache: 20
            },
            sort: sortOptions,
            paging: {
                limit: rowsPerPage,
                containers: ['#bpg'],
                compact: ['#tpg'],
                labels: { first: "<span data-icon='&#xe065;'></span>", last: "<span data-icon='&#xe068;'></span>",
                          prev: "<span data-icon='&#xe01e;'></span>", next: "<span data-icon='&#xe01c;'></span>" }
            },
            select: { key: 'checkbox', name: 'DeleteIDArray[]', className: 'ezsubitems_delete_checkbox',
                      value: function (row) { return row.node_id; }, label: function (row) { return row.name; },
                      header: false, ranges: false },
            columnToggle: server ? server.columnToggle : {
                shown: shownColumns,
                save: function (keys) {
                    setCookieSubMultiValue(confObj.cookieName, confObj.navigationPart, keys, confObj.cookieSecure);
                    shownColumns = keys;
                }
            },
            inlineEdit: {
                canEdit: function (row) { return row.can_edit === true; },
                fn: 'ezjscnode::updatepriority',
                data: function (row, key, value) {
                    return { ContentNodeID: row.parent_node_id, ContentObjectID: row.contentobject_id,
                             PriorityID: [row.node_id], Priority: [value] };
                }
            },
            loading: labelsObj.DATA_TABLE.msg_loading,
            caption: labelsObj.DATA_TABLE.caption || '',
            actionsContainer: '#action-controls',
            actions: [
                { id: 'ezbtn-items', label: A.select, menu: [
                    { id: 'ezopt-menu-check', label: A.select_sav, onSelect: function () { $list.find('input[type=checkbox]').prop('checked', true); this.syncAll(); this.emitSelect(); } },
                    { id: 'ezopt-menu-uncheck', label: A.select_sn, onSelect: function () { $list.find('input[type=checkbox]').prop('checked', false); this.syncAll(); this.emitSelect(); } },
                    { id: 'ezopt-menu-toggle', label: A.select_inv, onSelect: function () { $list.find('input[type=checkbox]').each(function () { this.checked = !this.checked; }); this.syncAll(); this.emitSelect(); } }
                ] },
                { id: 'ezbtn-new', label: A.create_new, disabled: createGroups.length === 0,
                  menu: createGroups.map(function (g, i) {
                      return { group: g, items: (createOptions[i] || []).map(function (o) { return { label: o.text, value: o.value, html: true }; }) };
                  }),
                  onSelect: function (item) {
                      form().append($('<input type="hidden" name="ClassID" />').val(item.value)).append($('<input type="hidden" name="NewButton" />')).trigger('submit');
                  } },
                { id: 'ezbtn-new-multi', label: A.create_multiple, disabled: createGroups.length === 0,
                  onClick: function () {
                      // the parent is this list's own node; the sub items form carries it as ContentNodeID
                      var f = form(), parent = f.find('input[name=ContentNodeID]').val();
                      f.attr('action', multiEditURLFor(f))
                       .append($('<input type="hidden" name="MultiEditCreateParent" />').val(parent))
                       .append($('<input type="hidden" name="MultiEditReturnURI" />').val(window.location.pathname))
                       .trigger('submit');
                  } },
                { id: 'ezbtn-more', label: A.more_actions,
                  menu: function () {
                      if (selectedCount() === 0) { return [{ label: A.more_actions_no, disabled: true }]; }
                      return [
                          { id: 'ezopt-menu-remove', label: A.more_actions_rs, value: 0 },
                          { id: 'ezopt-menu-move', label: A.more_actions_ms, value: 1 },
                          { id: 'ezopt-menu-copy', label: A.more_actions_cp, value: 2 },
                          { id: 'ezopt-menu-hide', label: A.more_actions_hs, value: 3 },
                          { id: 'ezopt-menu-unhide', label: A.more_actions_us, value: 4 },
                          { id: 'ezopt-menu-multiedit', label: A.more_actions_me, value: 5 },
                          { id: 'ezopt-menu-addlocation', label: A.more_actions_al || 'Add a location for selected', value: 6 }
                      ];
                  },
                  onSelect: function (item) {
                      if (selectedCount() === 0) { return; }
                      var f = form(), v = item.value;
                      if (v === 0) { f.append($('<input type="hidden" name="RemoveButton" value="1" />')).trigger('submit'); }
                      else if (v === 2) { f.append($('<input type="hidden" name="CopyButton" value="1" />')).trigger('submit'); }
                      else if (v === 3) { f.append($('<input type="hidden" name="HideButton" value="1" />')).trigger('submit'); }
                      else if (v === 4) { f.append($('<input type="hidden" name="UnhideButton" value="1" />')).trigger('submit'); }
                      else if (v === 6) { f.append($('<input type="hidden" name="AddLocationsButton" value="1" />')).trigger('submit'); }
                      else if (v === 5) {
                          // Edit the selection in one form: the DeleteIDArray checkboxes carry node ids, which
                          // content/multiedit resolves; the return uri brings the reader back to this list
                          f.attr('action', multiEditURLFor(f))
                           .append($('<input type="hidden" name="MultiEditReturnURI" />').val(window.location.pathname))
                           .trigger('submit');
                      } else { f.append($('<input type="hidden" name="MoveButton" value="1" />')).trigger('submit'); }
                  } }
            ],
            tableOptions: $.extend({
                button: { id: 'ezbtn-options', label: A.table_options },
                container: '#to-dialog-container',
                title: O.header,
                close: O.button_close,
                limits: {
                    legend: O.header_noipp,
                    items: [{ id: 1, count: 10 }, { id: 2, count: 25 }, { id: 3, count: 50 }, { id: 4, count: 100 }, { id: 5, count: 200 }, { id: 6, count: 500 }],
                    onSelect: function (item) {
                        window.Exp.prefs.set('admin_list_limit', item.id).catch(function (e) {
                            if (window.console) { window.console.warn('sub items: admin_list_limit was not saved', e && e.message); }
                        });
                    },
                    custom: { label: O.custom_label, placeholder: O.custom_placeholder || 'Enter number', max: 10000,
                              invalid: O.custom_invalid || 'Please enter a valid number between 1 and 10000' }
                },
                columns: { legend: O.header_vtc }
            }, server ? server.tableOptions : {})
        });
        if (server) {
            // rows per page (a listed number or a custom one) is part of the saved choice
            $list.on('exp:datatable:page', function (e, d) {
                if (d && d.limit && Number(d.limit) !== rowsPerPage) { rowsPerPage = Number(d.limit); server.limitSaved(rowsPerPage); }
            });
        }
        return $list.data('expDataTable');
    }

    return { init: init, getCookieSubMultiValue: getCookieSubMultiValue, setCookieSubMultiValue: setCookieSubMultiValue };
}());
