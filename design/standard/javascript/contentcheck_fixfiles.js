/**
 * The batches of contentcheck/fixfiles (doc/features/6.0/content-checks.md, "Resolving"): the progress page posts
 * the rest on by itself after a moment, as if "Go on with the next batch" was pressed. "Stop here" (or any click
 * on the page, or a key) stops it; without JavaScript the button does the same.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 */
(function () {
    'use strict';
    function start() {
        var form = document.querySelector('form[data-cc-continue="1"]');
        if (!form) {
            return;
        }
        var go = form.querySelector('button[name="ContinueButton"]');
        if (!go) {
            return;
        }
        var timer = window.setTimeout(function () {
            if (typeof form.requestSubmit === 'function') {
                form.requestSubmit(go);
            } else {
                go.click();
            }
        }, 800);
        var stop = function () {
            window.clearTimeout(timer);
        };
        document.addEventListener('keydown', stop, { once: true });
        document.addEventListener('pointerdown', stop, { once: true });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
})();
