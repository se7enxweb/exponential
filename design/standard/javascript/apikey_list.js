/*
 * The API access page (apikey/list): shows the copy button of a new key where the clipboard can be used, and moves
 * the focus to the new key. A file of its own, so the page runs under a Content Security Policy without
 * 'unsafe-inline' (doc/eng-us/features/6.0/content-security-policy.md).
 */
(function () {
    var buttons = document.querySelectorAll('.ak .ak-copy');
    var status = document.getElementById('ak-copy-status');
    for (var i = 0; i < buttons.length; i++) {
        (function (button) {
            var source = document.querySelector(button.getAttribute('data-copy'));
            if (!source || !navigator.clipboard || !window.isSecureContext) { return; }
            button.hidden = false;
            button.addEventListener('click', function () {
                navigator.clipboard.writeText(source.textContent.trim()).then(function () {
                    if (status) { status.textContent = button.getAttribute('data-done') || button.textContent + ': OK'; }
                }, function () {
                    if (status) { status.textContent = ''; }
                });
            });
        }(buttons[i]));
    }
    var fresh = document.getElementById('ak-new');
    if (fresh && fresh.focus) { fresh.focus(); }
}());
