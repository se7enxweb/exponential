/**
 * The admin's sub-items table on Exponential UI's $.fn.expDataTable (exp::datatable), without YUI.
 *
 * Started by children_detailed.tpl when Exponential UI is there (window.Exp && Exp.$.fn.expDataTable), with the
 * same four objects the YUI version (ezajaxsubitems_datatable.js, kept as the fallback) is given:
 *
 *   eZAjaxSubitemsExpDataTable.init(confObj, labelsObj, createGroups, createOptions)
 *
 * It keeps everything the YUI table did, the same way: the ezjscnode::subtree request (GET, same arguments), the
 * cache of 20 pages, sorting, the pagers in #bpg and #tpg, rows per page saved as admin_list_limit (and a custom
 * number that is not saved), the shown columns in the eZSubitemColumns cookie (one sub-value per navigation part),
 * inline priority editing through ezjscnode::updatepriority, the Select, Create new, Create multiple new, More
 * actions and Table options controls (same ids, same form fields posted) and the context menu of each row.
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

    function init(confObj, labelsObj, createGroups, createOptions) {
        var $ = window.Exp.$;
        var shownColumns = getCookieSubMultiValue(confObj.cookieName, confObj.navigationPart);
        if (shownColumns === null) { shownColumns = confObj.defaultShownColumns[confObj.navigationPart]; }
        var L = labelsObj.DATA_TABLE_COLS, A = labelsObj.ACTION_BUTTONS, O = labelsObj.TABLE_OPTIONS;
        var form = function () { return $('form[name=children]').first(); };
        var selectedCount = function () { return $('form[name=children] input.ezsubitems_delete_checkbox:checked').length; };

        // ---- cell formatters (the YUI version's, same HTML) ----------------------------------------------------
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

        // the server's rows, as the YUI DataSource's responseSchema parsed them
        var parseRow = function (r) {
            var row = $.extend({}, r);
            row.creator = r.creator && r.creator.name ? r.creator.name : '?';
            row.section = r.section && r.section.name ? r.section.name : '?';
            row.translations = r.translations ? r.translations.language_list : undefined;
            return row;
        };

        var $list = $('#content-sub-items-list');
        $list.expDataTable({
            columns: columns,
            rowKey: 'node_id',
            source: {
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
            sort: { key: confObj.sortKey, dir: confObj.sortOrder === 1 ? 'asc' : 'desc' },
            paging: {
                limit: confObj.rowsPrPage,
                containers: ['#bpg'],
                compact: ['#tpg'],
                labels: { first: "<span data-icon='&#xe065;'></span>", last: "<span data-icon='&#xe068;'></span>",
                          prev: "<span data-icon='&#xe01e;'></span>", next: "<span data-icon='&#xe01c;'></span>" }
            },
            select: { key: 'checkbox', name: 'DeleteIDArray[]', className: 'ezsubitems_delete_checkbox',
                      value: function (row) { return row.node_id; }, label: function (row) { return row.name; },
                      header: false, ranges: false },
            columnToggle: {
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
                          { id: 'ezopt-menu-multiedit', label: A.more_actions_me, value: 5 }
                      ];
                  },
                  onSelect: function (item) {
                      if (selectedCount() === 0) { return; }
                      var f = form(), v = item.value;
                      if (v === 0) { f.append($('<input type="hidden" name="RemoveButton" value="1" />')).trigger('submit'); }
                      else if (v === 2) { f.append($('<input type="hidden" name="CopyButton" value="1" />')).trigger('submit'); }
                      else if (v === 3) { f.append($('<input type="hidden" name="HideButton" value="1" />')).trigger('submit'); }
                      else if (v === 4) { f.append($('<input type="hidden" name="UnhideButton" value="1" />')).trigger('submit'); }
                      else if (v === 5) {
                          // Edit the selection in one form: the DeleteIDArray checkboxes carry node ids, which
                          // content/multiedit resolves; the return uri brings the reader back to this list
                          f.attr('action', multiEditURLFor(f))
                           .append($('<input type="hidden" name="MultiEditReturnURI" />').val(window.location.pathname))
                           .trigger('submit');
                      } else { f.append($('<input type="hidden" name="MoveButton" value="1" />')).trigger('submit'); }
                  } }
            ],
            tableOptions: {
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
            }
        });
        return $list.data('expDataTable');
    }

    return { init: init, getCookieSubMultiValue: getCookieSubMultiValue, setCookieSubMultiValue: setCookieSubMultiValue };
}());
