/*
 * admin4: keeps the space the page leaves for the fixed header (--header-height on .dashboard-flex) equal to the
 * header's real height, at all times.
 *
 * From 576px up the top menu wraps onto as many lines as it needs (admin4.css), so the header is as tall as its
 * lines and changes height when the window is resized, when the fonts arrive, when the light/dark toggle changes
 * its label. ezadmin_menubar.js measured the header once, at load: after a resize that added a line the header
 * covered the top of the page. A ResizeObserver on the header follows every change; where there is none, resize
 * and load events do. Plain JavaScript, no library.
 */
(function () {
    'use strict';

    function sync() {
        var header = document.getElementById('header');
        var flex = document.querySelector('.dashboard-flex');
        if (!header || !flex) return;
        var rem = header.offsetHeight / 16;
        if (flex.style.getPropertyValue('--header-height') !== rem + 'rem') {
            flex.style.setProperty('--header-height', rem + 'rem');
        }
    }

    function init() {
        var header = document.getElementById('header');
        if (!header) return;
        sync();
        if (window.ResizeObserver) {
            new ResizeObserver(sync).observe(header);
        } else {
            window.addEventListener('resize', sync);
        }
        window.addEventListener('load', sync);
        if (document.fonts && document.fonts.ready) document.fonts.ready.then(sync);
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();
