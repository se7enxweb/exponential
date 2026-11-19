/**
 * Swaps placeholder images for the real ones (image aliases made on demand, settings/image.ini [AliasSettings]).
 *
 * While an image alias is being made, content/image and index_image.php answer a neutral placeholder marked with
 * "Server-Timing: exp-image-placeholder" (SwapScript=enabled). This script finds those answers in the browser's
 * resource timing, asks again, and puts the real image in place of every <img> that showed the placeholder. Browsers
 * without server timing check the content/image images once loaded. No library, nothing to configure; it does nothing
 * on a page without placeholders.
 *
 * The server makes the aliases it could not make at once in the order they were asked for ([AliasSettings] Backlog),
 * which is the order of the page. So each round asks for the waiting images in page order, a few at a time, and stops
 * at the first one that is still a placeholder: the ones after it are not made yet either. A page of hundreds of new
 * images costs a request per image made plus one per round, not one per image and round. The pause between rounds
 * grows while nothing comes; after a minute without any image made the script gives up (the placeholders stay until
 * the page is loaded again).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 */
(function () {
    'use strict';

    var MARK = 'exp-image-placeholder';
    // Requests at once in a round
    var AT_ONCE = 4;
    // Pauses between rounds (ms): the first ones short, longer while nothing is made
    var PAUSES = [500, 700, 1000, 1500, 2000, 3000];
    // Give up after this long without any image made (ms)
    var GIVE_UP = 60000;
    // url => state: 'waiting' | 'done' | 'refused'
    var seen = {};
    var waiting = [];
    // real url => the src of the <img> that shows its page placeholder ([AliasSettings] PagePlaceholders)
    var shownAs = {};
    var timer = null;
    var running = false;
    var idleRounds = 0;
    var lastProgress = 0;

    if (!window.fetch || !window.Promise || !document.images) {
        return;
    }
    try {
        // A page of hundreds of images would fill the default buffer of 250 entries
        if (window.performance && performance.setResourceTimingBufferSize) {
            performance.setResourceTimingBufferSize(2000);
        }
    } catch (e) { /* not supported */ }

    function now() {
        return new Date().getTime();
    }

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

    // url without this page's origin: how a srcset written by a template names it (img.srcset is not resolved)
    function pathOf(url) {
        var origin = window.location.origin;
        return origin && url.indexOf(origin + '/') === 0 ? url.substring(origin.length) : url;
    }

    // The form of url a srcset uses, or null when it names it in neither form
    function inSrcset(img, url) {
        if (!img.srcset) {
            return null;
        }
        if (img.srcset.indexOf(url) !== -1) {
            return url;
        }
        var path = pathOf(url);
        return path !== url && img.srcset.indexOf(path) !== -1 ? path : null;
    }

    // The <img> elements that show url, through src or srcset
    function imagesShowing(url) {
        var found = [];
        var alt = shownAs[url];
        for (var i = 0; i < document.images.length; i++) {
            var img = document.images[i];
            if (img.currentSrc === url || img.src === url || inSrcset(img, url) !== null
                || (alt && (img.src === alt || img.currentSrc === alt))) {
                found.push(img);
            }
        }
        return found;
    }

    // Where each image first shows on the page (the order the server makes the images in)
    function positions() {
        var found = {};
        for (var i = document.images.length - 1; i >= 0; i--) {
            var img = document.images[i];
            found[img.currentSrc || img.src] = i;
            if (img.src) {
                found[img.src] = i;
            }
        }
        return found;
    }

    function swap(url, replacement) {
        imagesShowing(url).forEach(function (img) {
            var named = inSrcset(img, url);
            if (named !== null) {
                img.srcset = img.srcset.split(named).join(replacement);
            }
            if (img.src === url || img.currentSrc === url || (shownAs[url] && img.src === shownAs[url])) {
                img.src = replacement;
            }
            img.removeAttribute('data-exp-placeholder');
        });
    }

    // Asks for url once: resolves true when the real image is there (swapped), false while it is still a
    // placeholder; a refused or unknown image keeps its placeholder and is not asked for again
    function ask(url) {
        return window.fetch(url, { cache: 'reload', credentials: 'same-origin' }).then(function (response) {
            var pending = response.headers.get('X-Image-Placeholder') || isPlaceholderTiming(response.headers.get('Server-Timing'));
            if (pending && response.status === 200) {
                return false;
            }
            seen[url] = (!response.ok || pending) ? 'refused' : 'done';
            if (seen[url] === 'refused') {
                return true;
            }
            if (response.redirected && response.url) {
                swap(url, response.url);
                return true;
            }
            return response.blob().then(function (blob) {
                swap(url, window.URL.createObjectURL(blob));
                return true;
            });
        })['catch'](function () {
            return false;
        });
    }

    function schedule() {
        if (timer !== null || running) {
            return;
        }
        if (lastProgress && now() - lastProgress > GIVE_UP) {
            return;
        }
        timer = window.setTimeout(round, PAUSES[Math.min(idleRounds, PAUSES.length - 1)]);
    }

    // One round: the waiting images in page order, AT_ONCE at a time, until one is still a placeholder
    function round() {
        timer = null;
        waiting = waiting.filter(function (url) { return seen[url] === 'waiting'; });
        if (!waiting.length) {
            return;
        }
        running = true;
        var at = positions();
        var last = document.images.length;
        function place(url) {
            return url in at ? at[url] : (shownAs[url] && shownAs[url] in at ? at[shownAs[url]] : last);
        }
        waiting.sort(function (a, b) {
            return place(a) - place(b);
        });
        var queue = waiting.slice();
        var made = 0;
        function next() {
            var batch = queue.splice(0, AT_ONCE);
            if (!batch.length) {
                return Promise.resolve(true);
            }
            return Promise.all(batch.map(ask)).then(function (results) {
                var all = true;
                results.forEach(function (ok) {
                    if (ok) {
                        made++;
                    } else {
                        all = false;
                    }
                });
                return all ? next() : false;
            });
        }
        next().then(function () {
            running = false;
            if (made) {
                idleRounds = 0;
                lastProgress = now();
            } else {
                idleRounds++;
            }
            schedule();
        });
    }

    function add(url) {
        if (seen[url]) {
            return;
        }
        seen[url] = 'waiting';
        waiting.push(url);
        // The first placeholder, or one found after the script gave up on the others: it gets its own GIVE_UP
        if (!lastProgress || now() - lastProgress > GIVE_UP) {
            lastProgress = now();
            idleRounds = 0;
        }
        schedule();
    }

    function scan(entries) {
        for (var i = 0; i < entries.length; i++) {
            if (hasMark(entries[i])) {
                add(entries[i].name);
            }
        }
    }

    // A page placeholder (a page with many images not made yet): a static file whose fragment names the real image,
    // #exp-src=<address>&exp-depth=<path segments of the placeholder below the installation's root>
    function placeholderTarget(src) {
        var at = src.indexOf('#exp-src=');
        if (at === -1 || !window.URL) {
            return null;
        }
        var real = null;
        var depth = 0;
        src.slice(at + 1).split('&').forEach(function (part) {
            var eq = part.indexOf('=');
            var key = eq === -1 ? part : part.slice(0, eq);
            var value = eq === -1 ? '' : part.slice(eq + 1);
            if (key === 'exp-src') {
                try { real = decodeURIComponent(value); } catch (e) { real = null; }
            } else if (key === 'exp-depth') {
                depth = parseInt(value, 10) || 0;
            }
        });
        if (!real) {
            return null;
        }
        try {
            var file = new URL(src);
            var parts = file.pathname.split('/');
            var root = parts.slice(0, Math.max(1, parts.length - depth)).join('/') + '/';
            var target = new URL(real, file.origin + root);
            return target.origin === file.origin ? target.href : null;
        } catch (e) {
            return null;
        }
    }

    function scanPlaceholders() {
        for (var i = 0; i < document.images.length; i++) {
            var src = document.images[i].src;
            if (!src || src.indexOf('#exp-src=') === -1) {
                continue;
            }
            var real = placeholderTarget(src);
            if (real && !shownAs[real]) {
                shownAs[real] = src;
                add(real);
            }
        }
    }
    if (document.readyState !== 'loading') {
        scanPlaceholders();
    } else {
        document.addEventListener('DOMContentLoaded', scanPlaceholders);
    }
    window.addEventListener('load', scanPlaceholders);

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
            if (src && src.indexOf('/content/image/') !== -1) {
                add(src);
            }
        }
    });
})();
