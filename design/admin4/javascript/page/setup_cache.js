/*
 * admin4 setup/cache: the cache table - filter, selection, and "Clear selected" naming what goes before the form is
 * sent. A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/en/us/features/6.0/content-security-policy.md).
 */
(function () {
    var page = document.getElementById('exp-cachepage');
    if (!page) return;
    var rows = Array.prototype.slice.call(page.querySelectorAll('tr.a4-setup-cache-row'));
    var groups = Array.prototype.slice.call(page.querySelectorAll('section.a4-setup-group'));
    var toolbar = document.getElementById('exp-cache-toolbar');
    var search = document.getElementById('exp-cache-search');
    var count = document.getElementById('exp-cache-count');
    var selection = document.getElementById('exp-cache-selection');
    var selectAll = document.getElementById('exp-cache-select-all');
    var clearButton = document.getElementById('exp-cache-clear-selected');
    var confirmBox = document.getElementById('exp-cache-confirm');
    var confirmList = document.getElementById('exp-cache-confirm-list');
    var texts = page.dataset;
    toolbar.hidden = false;
    document.getElementById('exp-cache-select-all-label').hidden = false;

    function boxes(onlyShown) {
        return rows.filter(function (r) { return !onlyShown || !r.hidden; })
                   .map(function (r) { return r.querySelector('input[name="CacheList[]"]'); })
                   .filter(function (b) { return b && !b.disabled; });
    }
    function chosen() { return boxes(false).filter(function (b) { return b.checked; }); }
    function updateSelection() {
        var n = chosen().length;
        selection.textContent = n ? n + ' ' + texts.selected : texts.none;
    }
    function filter() {
        var q = (search.value || '').trim().toLowerCase();
        var g = (page.querySelector('input[name="exp-cache-group-filter"]:checked') || {}).value || '';
        var shown = 0;
        rows.forEach(function (r) {
            var inGroup = !g || r.closest('section.a4-setup-group').dataset.group === g;
            var hit = !q || r.dataset.search.indexOf(q) !== -1;
            r.hidden = !(inGroup && hit);
            if (!r.hidden) shown++;
        });
        groups.forEach(function (s) {
            var visible = !g || s.dataset.group === g;
            if (q && visible) visible = s.querySelectorAll('tr.a4-setup-cache-row:not([hidden])').length > 0 || s.dataset.group === 'velocity' && !q;
            s.hidden = !visible;
        });
        count.textContent = shown + ' / ' + rows.length + ' ' + texts.shown;
    }
    search.addEventListener('input', filter);
    page.querySelectorAll('input[name="exp-cache-group-filter"]').forEach(function (r) { r.addEventListener('change', filter); });
    page.addEventListener('change', function (e) { if (e.target.name === 'CacheList[]') updateSelection(); });
    selectAll.addEventListener('change', function () {
        boxes(true).forEach(function (b) { b.checked = selectAll.checked; });
        updateSelection();
    });
    // Clear selected: name what goes, in place, before the form is sent
    var confirmed = false;
    clearButton.addEventListener('click', function (e) {
        if (confirmed) return;
        e.preventDefault();
        var list = chosen();
        confirmList.innerHTML = '';
        if (!list.length) { selection.textContent = texts.none; return; }
        list.forEach(function (b) { var li = document.createElement('li'); li.textContent = b.dataset.name || b.value; confirmList.appendChild(li); });
        confirmBox.hidden = false;
        document.getElementById('exp-cache-confirm-yes').focus();
    });
    document.getElementById('exp-cache-confirm-yes').addEventListener('click', function () {
        confirmed = true;
        clearButton.click();
    });
    document.getElementById('exp-cache-confirm-no').addEventListener('click', function () {
        confirmBox.hidden = true;
        clearButton.focus();
    });
    filter();
    updateSelection();
})();
