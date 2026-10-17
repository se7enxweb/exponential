/**
 * The selection of the result lists of the content checks (doc/features/6.0/content-checks.md): a master checkbox
 * [data-a4-select-all] in a table [data-a4-select] checks or clears every row box [data-a4-select-item] of it, shows
 * the mixed state when some are checked, and keeps the count [data-a4-select-count] up to date from its
 * data-a4-count-text ("%selected of %total selected"). Without JavaScript the master box is hidden and the links
 * "Select all on this page" / "Select none" do the same on the server.
 */
(function () {
    'use strict';
    function setup(table) {
        var master = table.querySelector('[data-a4-select-all]');
        var items = Array.prototype.slice.call(table.querySelectorAll('[data-a4-select-item]'));
        var form = table.closest('form') || document;
        var count = form.querySelector('[data-a4-select-count]');
        if (!master || !items.length) {
            return;
        }
        master.hidden = false;
        function update() {
            var checked = items.filter(function (box) { return box.checked; }).length;
            master.checked = checked === items.length;
            master.indeterminate = checked > 0 && checked < items.length;
            if (count && count.getAttribute('data-a4-count-text')) {
                count.textContent = count.getAttribute('data-a4-count-text')
                    .replace('%selected', String(checked)).replace('%total', String(items.length));
            }
        }
        master.addEventListener('change', function () {
            items.forEach(function (box) { box.checked = master.checked; });
            update();
        });
        items.forEach(function (box) { box.addEventListener('change', update); });
        update();
    }
    function start() {
        Array.prototype.forEach.call(document.querySelectorAll('[data-a4-select]'), setup);
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
}());
