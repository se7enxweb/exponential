/*
 * admin4 (prototype): the mega menu of a top tab (menu.ini [TopAdminMenu] MegaMenuTabs[], only setup so far;
 * page_topmenuitem.tpl). Loaded only while that setting names a tab (page_head_script.tpl).
 *
 * The WAI-ARIA disclosure navigation pattern, not a menu: the tab stays a link, the button beside it
 * (.a4-mega-toggle, aria-expanded, aria-controls) opens and closes a panel of plain links under the header.
 * - A click, Enter or Space on the button opens or closes the panel; ArrowDown opens it and goes to its first link.
 *   Tab goes through the panel's links in their order; the focus leaving the tab, the button and the panel closes
 *   it, as does a click outside them. Escape closes it and gives the focus back to the button when the focus was in
 *   the tab, the button or the panel.
 * - A mouse resting on the tab or the button for 300 ms opens the panel as a preview; leaving the tab, the button
 *   and the panel for 400 ms closes it again (the delay is the bridge across the gap between the tab and the panel).
 *   A click on the button, or the focus moving into it, while the preview is shown keeps it open. Never on touch (only a pointer of type mouse,
 *   and only while the device reports (hover: hover) and (pointer: fine)); never while the "More" list or the user
 *   menu of the header is open.
 * - From 1280px only (one header row): below, or while the tab sits in the "More" list, admin4.css hides the button
 *   and the script keeps the panel closed; the tab is then a plain link, and the phone menu is unchanged.
 * - The panel's groups and links are loaded once, from the button's data-a4-mega-src (setup/megamenu), the first
 *   time it opens: the page itself carries only the button, a status line and the link to the overview. A failed
 *   load says so in the panel (data-a4-mega-error) and is tried again on the next opening. A link to the page
 *   shown (the overview link included) is marked aria-current="page".
 * Plain JavaScript, no library; window.matchMedia, setTimeout and fetch are read from window, so the behaviour can
 * be run outside a browser (tests/tests/design/js/mega_menu.js).
 */
(function () {
    'use strict';

    var ENTER_DELAY = 300, LEAVE_DELAY = 400;

    function megaMenu(item) {
        var toggle = item.querySelector('.a4-mega-toggle');
        var panel = toggle ? document.getElementById(toggle.getAttribute('aria-controls')) : null;
        var body = panel ? panel.querySelector('[data-a4-mega-body]') : null;
        if (!panel || !body) return;
        var wide = window.matchMedia('(min-width: 1280px)');
        var fine = window.matchMedia('(hover: hover) and (pointer: fine)');
        var mode = null;     // null (closed), 'preview' (opened by the mouse resting on it) or 'open'
        var timer = null;    // the pending preview or close
        var loading = null;  // the load of the links, once it has started and not failed

        // the button is shown: one header row, and the tab not in the "More" list
        function usable() {
            return wide.matches && !(item.closest && item.closest('.a4-nav-more-list'));
        }

        // the "More" list or the user menu of the header is open
        function otherOpen() {
            return !!document.querySelector('.a4-nav-more-toggle[aria-expanded="true"], #a4-user-toggle[aria-expanded="true"]');
        }

        function wait(fn, ms) {
            stop();
            timer = window.setTimeout(function () { timer = null; fn(); }, ms);
        }

        function stop() {
            if (timer !== null) {
                window.clearTimeout(timer);
                timer = null;
            }
        }

        function links() {
            return Array.prototype.slice.call(panel.querySelectorAll('a[href]'));
        }

        function status(text) {
            body.innerHTML = '';
            var p = document.createElement('p');
            p.className = 'a4-mega-status';
            p.setAttribute('role', 'status');
            p.textContent = text;
            body.appendChild(p);
        }

        // the links to the page shown, the overview link included
        function markCurrent(root) {
            var here = window.location ? window.location.pathname : null;
            if (!here) return;
            Array.prototype.forEach.call(root.querySelectorAll('a[href]'), function (a) {
                if (a.pathname === here) a.setAttribute('aria-current', 'page');
            });
        }

        function load() {
            if (loading) return loading;
            panel.setAttribute('aria-busy', 'true');
            loading = window.fetch(toggle.getAttribute('data-a4-mega-src'), {
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(function (response) {
                if (!response.ok) throw new Error('HTTP ' + response.status);
                return response.text();
            }).then(function (html) {
                // only the fragment's element: a sign-in or error page in its place is not the menu
                var template = document.createElement('template');
                template.innerHTML = html;
                var groups = template.content.querySelector('[data-a4-mega-groups]');
                if (!groups) throw new Error('not the menu');
                body.innerHTML = '';
                body.appendChild(groups);
                markCurrent(panel);
                panel.removeAttribute('aria-busy');
            }).catch(function () {
                loading = null;
                panel.removeAttribute('aria-busy');
                status(panel.getAttribute('data-a4-mega-error') || '');
            });
            return loading;
        }

        function set(next, focusToggle) {
            stop();
            if (next && !usable()) next = null;
            mode = next;
            toggle.setAttribute('aria-expanded', next ? 'true' : 'false');
            panel.hidden = !next;
            item.classList.toggle('is-mega-open', !!next);
            if (next) load();
            else if (focusToggle) toggle.focus();
        }

        toggle.addEventListener('click', function () {
            // a preview the button is clicked on stays open
            set(mode === 'open' ? null : 'open', false);
        });
        toggle.addEventListener('keydown', function (e) {
            if (e.key !== 'ArrowDown' || !usable()) return;
            e.preventDefault();
            set('open', false);
            load().then(function () {
                var first = links()[0];
                if (mode && first) first.focus();
            });
        });

        // Escape: closes it; the focus goes back to the button when it was in the tab, the button or the panel
        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape' || !mode || e.defaultPrevented) return;
            var active = document.activeElement;
            e.preventDefault();
            set(null, !!(active && item.contains(active)));
        });
        // a click outside the tab, the button and the panel
        document.addEventListener('click', function (e) {
            if (mode && !item.contains(e.target)) set(null, false);
        });
        // the focus coming into a preview keeps it open: the mouse leaving no longer closes it under the focus
        item.addEventListener('focusin', function () {
            if (mode === 'preview') set('open', false);
        });
        // the focus leaving them (Tab past the panel's last link, Shift+Tab before the tab)
        item.addEventListener('focusout', function (e) {
            if (mode && e.relatedTarget && !item.contains(e.relatedTarget)) set(null, false);
        });

        // the mouse: a delayed preview, never on touch
        function mouse(e) {
            return e.pointerType === 'mouse' && fine.matches;
        }
        item.addEventListener('pointerenter', function (e) {
            if (!mouse(e)) return;
            if (mode) {
                stop();   // back in time: the preview stays
                return;
            }
            wait(function () {
                if (!mode && usable() && !otherOpen()) set('preview', false);
            }, ENTER_DELAY);
        });
        item.addEventListener('pointerleave', function (e) {
            if (!mouse(e)) return;
            if (mode === 'preview') wait(function () { set(null, false); }, LEAVE_DELAY);
            else if (!mode) stop();
        });

        // narrower than one header row, or the tab moved into "More": closed
        function check() {
            if (mode && !usable()) set(null, false);
        }
        if (wide.addEventListener) wide.addEventListener('change', check);
        else if (wide.addListener) wide.addListener(check);
        window.addEventListener('resize', check);
    }

    function init() {
        Array.prototype.forEach.call(document.querySelectorAll('#header .a4-mega-item'), megaMenu);
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();
