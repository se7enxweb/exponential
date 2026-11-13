/**
 * Swaps placeholder images for the real ones (image aliases made on demand, settings/image.ini [AliasSettings]).
 *
 * While an image alias is being made, content/image and index_image.php answer a neutral placeholder marked with
 * "Server-Timing: exp-image-placeholder" (SwapScript=enabled). This script finds those answers in the browser's
 * resource timing, asks again a few times with growing pauses, and puts the real image in place of every <img> that
 * showed the placeholder. Browsers without server timing check the content/image images once loaded. No library,
 * nothing to configure; it does nothing on a page without placeholders.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 */
(function () {
    'use strict';

    var MARK = 'exp-image-placeholder';
    var MAX_TRIES = 6;
    var tries = {};

    if (!window.fetch || !window.Promise || !document.images) {
        return;
    }
    try {
        // A page of hundreds of images would fill the default buffer of 250 entries
        if (window.performance && performance.setResourceTimingBufferSize) {
            performance.setResourceTimingBufferSize(2000);
        }
    } catch (e) { /* not supported */ }

    function isPlaceholderTiming(value) {
        return typeof value === 'string' && value.indexOf(MARK) !== -1;
    }

    function hasMark(entry) {
        var timing = entry.serverTiming;
        if (!timing) {
            return false;
        }
        for (var i = 0; i < timing.length; i++) {
            if (timing[i].name === MARK) {
                return true;
            }
        }
        return false;
    }

    // The <img> elements that show url, through src or srcset
    function imagesShowing(url) {
        var found = [];
        for (var i = 0; i < document.images.length; i++) {
            var img = document.images[i];
            if (img.currentSrc === url || img.src === url || (img.srcset && img.srcset.indexOf(url) !== -1)) {
                found.push(img);
            }
        }
        return found;
    }

    function swap(url, replacement) {
        imagesShowing(url).forEach(function (img) {
            if (img.srcset && img.srcset.indexOf(url) !== -1) {
                img.srcset = img.srcset.split(url).join(replacement);
            }
            if (img.src === url || img.currentSrc === url) {
                img.src = replacement;
            }
            img.removeAttribute('data-exp-placeholder');
        });
    }

    function retry(url) {
        var count = tries[url] = (tries[url] || 0) + 1;
        if (count > MAX_TRIES) {
            return;
        }
        var pause = Math.min(8000, 700 * Math.pow(2, count - 1));
        window.setTimeout(function () {
            window.fetch(url, { cache: 'reload', credentials: 'same-origin' }).then(function (response) {
                var pending = response.headers.get('X-Image-Placeholder') || isPlaceholderTiming(response.headers.get('Server-Timing'));
                if (pending && response.status === 200) {
                    retry(url);
                    return null;
                }
                if (!response.ok || pending) {
                    // Refused or unknown: the placeholder stays
                    return null;
                }
                if (response.redirected && response.url) {
                    swap(url, response.url);
                    return null;
                }
                return response.blob().then(function (blob) {
                    swap(url, window.URL.createObjectURL(blob));
                });
            })['catch'](function () {
                retry(url);
            });
        }, pause);
    }

    function scan(entries) {
        for (var i = 0; i < entries.length; i++) {
            if (hasMark(entries[i]) && !tries[entries[i].name]) {
                retry(entries[i].name);
            }
        }
    }

    var timingSupported = window.PerformanceResourceTiming && 'serverTiming' in window.PerformanceResourceTiming.prototype;
    if (timingSupported && window.PerformanceObserver) {
        try {
            new window.PerformanceObserver(function (list) {
                scan(list.getEntries());
            }).observe({ type: 'resource', buffered: true });
            return;
        } catch (e) { /* fall through */ }
    }
    if (timingSupported && performance.getEntriesByType) {
        window.addEventListener('load', function () {
            scan(performance.getEntriesByType('resource'));
        });
        return;
    }
    // No server timing: the images made on demand are the content/image ones
    window.addEventListener('load', function () {
        for (var i = 0; i < document.images.length; i++) {
            var src = document.images[i].currentSrc || document.images[i].src;
            if (src && src.indexOf('/content/image/') !== -1 && !tries[src]) {
                tries[src] = 0;
                retry(src);
            }
        }
    });
})();
