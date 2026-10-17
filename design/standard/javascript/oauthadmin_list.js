/*
 * The REST application list (oauthadmin/list): shows the "select all" box and ticks or clears every application's
 * box with it. A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/features/6.0/content-security-policy.md).
 */
(function () {
    var all = document.getElementById('oauth-select-all');
    if (!all) { return; }
    all.parentNode.hidden = false;
    all.addEventListener('change', function () {
        var boxes = document.querySelectorAll('.exp-oauth input[name="DeleteIDArray[]"]');
        for (var i = 0; i < boxes.length; i++) { boxes[i].checked = all.checked; }
    });
}());
