/*
 * admin4 setup/session: "Select all" over the session checkboxes.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/features/6.0/content-security-policy.md).
 */
(function () {
    var list = document.getElementById('session-list'), all = document.getElementById('session-select-all');
    if (!list || !all) return;
    var boxes = function () { return Array.prototype.filter.call(list.querySelectorAll('input[type=checkbox]'), function (b) { return !b.disabled; }); };
    all.parentNode.hidden = false;
    all.addEventListener('change', function () { boxes().forEach(function (b) { b.checked = all.checked; }); });
    list.addEventListener('change', function () { var b = boxes(); all.checked = b.length > 0 && b.every(function (x) { return x.checked; }); });
})();
