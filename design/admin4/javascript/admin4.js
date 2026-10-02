/*
 * admin4: the top menu on narrow windows (576-1023px, see admin4.css), where it is one row that scrolls sideways
 * without a scrollbar:
 *  - a vertical mouse wheel scrolls it sideways (trackpads and touch already do);
 *  - .a4-more-left / .a4-more-right on the row while items are hidden on that side (admin4.css fades that edge);
 *  - the current item (a.selected) is scrolled into view.
 * Plain JavaScript, no library; it only acts while the row actually overflows.
 */
(function () {
    'use strict';
    var NARROW = window.matchMedia('(min-width: 576px) and (max-width: 1023px)');

    function edges(bar) {
        var max = bar.scrollWidth - bar.clientWidth;
        bar.classList.toggle('a4-more-left', max > 1 && bar.scrollLeft > 1);
        bar.classList.toggle('a4-more-right', max > 1 && bar.scrollLeft < max - 1);
    }

    function showCurrent(bar) {
        var current = bar.querySelector('a.selected');
        if (!current || bar.scrollWidth <= bar.clientWidth) return;
        var li = current.parentNode, left = li.offsetLeft - bar.offsetLeft;
        if (left < bar.scrollLeft || left + li.offsetWidth > bar.scrollLeft + bar.clientWidth) {
            bar.scrollLeft = Math.max(0, left - (bar.clientWidth - li.offsetWidth) / 2);
        }
    }

    function init() {
        var bar = document.querySelector('.navbar-menu .navbar-bar');
        if (!bar) return;
        bar.addEventListener('wheel', function (e) {
            if (!NARROW.matches || bar.scrollWidth <= bar.clientWidth) return;
            if (Math.abs(e.deltaY) > Math.abs(e.deltaX)) {
                bar.scrollLeft += e.deltaY;
                e.preventDefault();
            }
        }, { passive: false });
        bar.addEventListener('scroll', function () { edges(bar); }, { passive: true });
        var refresh = function () {
            if (NARROW.matches) { showCurrent(bar); }
            edges(bar);
        };
        window.addEventListener('resize', refresh);
        if (NARROW.addEventListener) NARROW.addEventListener('change', refresh);
        refresh();
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();
