/*
 * admin4 setup/info: "Copy" puts the system report on the clipboard (or selects it for a copy by hand).
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/features/6.0/content-security-policy.md).
 */
(function () {
    var button = document.getElementById('exp-sysinfo-copy');
    var text = document.getElementById('exp-sysinfo-report-text');
    var status = document.getElementById('exp-sysinfo-copy-status');
    if (!button || !text) return;
    button.hidden = false;
    button.addEventListener('click', function () {
        var done = function () { status.textContent = button.getAttribute('data-done'); };
        var fallback = function () {
            var details = document.getElementById('exp-sysinfo-report');
            if (details) details.open = true;
            text.focus(); text.select();
            try { if (document.execCommand('copy')) { done(); return; } } catch (e) {}
            status.textContent = button.getAttribute('data-manual');
        };
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text.value).then(done, fallback);
        } else {
            fallback();
        }
    });
})();
