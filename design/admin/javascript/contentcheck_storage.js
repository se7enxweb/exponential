/**
 * The scan of the storage files (contentcheck/storageaction, doc/eng-us/features/6.0/content-checks.md): presses the
 * button marked data-a4-autocontinue after a short pause, so a long scan goes on step by step by itself. Pressing
 * the button marked data-a4-stop, or any key, stops it; without JavaScript the button is pressed by hand.
 */
(function () {
    'use strict';
    function start() {
        var button = document.querySelector('[data-a4-autocontinue]');
        if (!button) {
            return;
        }
        var stopped = false;
        var stop = function () { stopped = true; };
        var stopButton = document.querySelector('[data-a4-stop]');
        if (stopButton) {
            stopButton.addEventListener('click', stop);
        }
        document.addEventListener('keydown', stop);
        window.setTimeout(function () {
            if (!stopped && button.form) {
                button.click();
            }
        }, 600);
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
}());
