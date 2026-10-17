/*
 * admin4 infocollector/overview: shows the "select all" checkbox and keeps it in step with the rows.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/features/6.0/content-security-policy.md).
 */
(function () {
    var list = document.getElementById('info-list'), all = document.getElementById('info-select-all');
    if (!list || !all) return;
    var boxes = function () { return Array.prototype.slice.call(list.querySelectorAll('input[name="ObjectIDArray[]"]')); };
    all.parentNode.hidden = false;
    all.addEventListener('change', function () { boxes().forEach(function (b) { b.checked = all.checked; }); });
    list.addEventListener('change', function () { var b = boxes(); all.checked = b.length > 0 && b.every(function (x) { return x.checked; }); });
})();
