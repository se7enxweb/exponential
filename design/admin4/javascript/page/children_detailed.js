/*
 * The sub-items table of the node view (children_detailed.tpl): reads the settings and texts from the data
 * attributes of #subitems-config and starts Exponential UI's exp::datatable (ezajaxsubitems_expdatatable.js).
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/eng-us/features/6.0/content-security-policy.md).
 */
(function () {
    'use strict';

    var config = document.getElementById('subitems-config');
    if (!config) { return; }

    function json(name, fallback) {
        try {
            var value = JSON.parse(config.getAttribute(name) || 'null');
            return value === null ? fallback : value;
        } catch (e) {
            return fallback;
        }
    }

    var confObj = json('data-conf', {});
    var labelsObj = json('data-labels', {});
    var createGroups = json('data-create-groups', []);
    var createOptions = json('data-create-options', []);

    // The columns of each navigation part, as the setting lists them (checkbox;crank;name;...)
    var vcols = {};
    var columns = confObj.defaultShownColumns || {};
    for (var part in columns) {
        if (Object.prototype.hasOwnProperty.call(columns, part)) { vcols[part] = String(columns[part]).split(';'); }
    }
    confObj.defaultShownColumns = vcols;

    // A filter that is not set is an empty one (it goes into the request URL)
    if (typeof confObj.nameFilter !== 'string') {
        confObj.nameFilter = confObj.nameFilter === null || confObj.nameFilter === undefined || confObj.nameFilter === false ? '' : String(confObj.nameFilter);
    }

    // The classes that may be created here: JSON, or '' when none may (node.classes_js_array)
    var classes = confObj.classesString;
    if (typeof classes !== 'string' || classes === '' || classes === "''") {
        confObj.classesString = '';
    } else {
        try { confObj.classesString = JSON.parse(classes); } catch (e) { confObj.classesString = ''; }
    }

    // Where "Edit selected" sends the selection (ezajaxsubitems_expdatatable.js reads it from window)
    var multiEditURL = config.getAttribute('data-multiedit-url');
    if (multiEditURL) { window.eZExpMultiEditURL = multiEditURL; }

    // The sub-items table: Exponential UI's exp::datatable (ezajaxsubitems_expdatatable.js)
    if (window.Exp && Exp.$ && Exp.$.fn.expDataTable && window.eZAjaxSubitemsExpDataTable) {
        Exp.ready(function () {
            eZAjaxSubitemsExpDataTable.init(confObj, labelsObj, createGroups, createOptions);
        });
    } else {
        // Without Exponential UI the table cannot be drawn: say so instead of leaving its place empty. The place comes
        // after this script, so the note waits for the page. The text is data-no-table, inserted as text.
        var noTableText = config.getAttribute('data-no-table') || '';
        var showNoTable = function () {
            var list = document.getElementById('content-sub-items-list');
            if (!list || !noTableText || list.querySelector('.exp-no-subitems-table')) { return; }
            var note = document.createElement('div');
            note.className = 'a4-notice is-warn exp-no-subitems-table';
            note.setAttribute('role', 'status');
            note.textContent = noTableText;
            list.appendChild(note);
        };
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', showNoTable);
        } else {
            showNoTable();
        }
    }
})();
