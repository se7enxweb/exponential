/*
 * admin4 visual/menuconfig: the "Copy" buttons of the template examples.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/en/us/features/6.0/content-security-policy.md).
 */
(function () {
    var buttons = document.querySelectorAll('.a4-visual .a4-visual-copy');
    Array.prototype.forEach.call(buttons, function (button) {
        var target = document.getElementById(button.getAttribute('data-copy'));
        if (!target) { return; }
        button.hidden = false;
        var label = button.textContent;
        button.addEventListener('click', function () {
            var text = target.textContent;
            var done = function () {
                button.textContent = label + ' ✓'; setTimeout(function () { button.textContent = label; }, 1500);
                // the tick is only seen; the toast also tells a screen reader
                if (window.a4Toast) window.a4Toast(button.getAttribute('data-copied'), 'ok', button);
            };
            // a copy that failed says so instead of doing nothing
            var failed = function () { if (window.a4Toast) window.a4Toast(button.getAttribute('data-copy-failed'), 'bad', button); };
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(done, failed);
            } else {
                var range = document.createRange(); range.selectNodeContents(target);
                var selection = window.getSelection(); selection.removeAllRanges(); selection.addRange(range);
                try { if (document.execCommand('copy')) { done(); } else { failed(); } } catch (e) { failed(); }
            }
        });
    });
})();
