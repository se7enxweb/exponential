/*
 * admin4 (prototype): the mega menu of a top tab (menu.ini [TopAdminMenu] MegaMenuTabs[], each tab whose
 * [Topmenu_<tab>] names a MegaMenuTemplate; page_topmenuitem.tpl). Loaded only while that setting names a tab
 * (page_head_script.tpl). Each tab has its own button and panel, and one panel is open at a time: opening one, by
 * a click, a key or the hover preview, closes the other; a panel opened by a click or a key is not replaced by the
 * hover preview of another tab.
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
 * - From 576px (the one-line main menu; also 960px at 200% zoom): below (the phone menu), or while the tab sits in the "More" list, admin4.css hides the button
 *   and the script keeps the panel closed; the tab is then a plain link, and the phone menu is unchanged.
 * - The panel's groups and links are loaded from the button's data-a4-mega-src (user/megamenu/<tab>) the first time it
 *   opens: the page itself carries only the button, a status line and the link to the tab's page. The load:
 *   - is one request per tab and page: an opening while it runs waits for the same request, and closing the panel
 *     does not cancel it; once loaded, the links stay for the life of the page;
 *   - is the menu only when the answer holds the fragment's marker (data-a4-mega-fragment, set by
 *     user/megamenu.tpl; data-a4-mega-groups for a design's own fragment without it);
 *   - is tried once more, after a short pause, when it failed for a passing reason: no connection, a server error
 *     (5xx, 408, 429), no answer after 10 s, or an answer that is not the menu;
 *   - with a 401 or 403, or a sign-in page in place of the menu, shows that the user is signed out, with a link to
 *     sign in (data-a4-mega-signed-out, data-a4-mega-login) instead of the error;
 *   - when it has failed for good, says so (data-a4-mega-error) with a "Try again" button (data-a4-mega-retry); the
 *     link to the tab's page stays below it, and the next opening tries again too;
 *   - stopped by the browser (another page being opened: an AbortError, or Firefox's network error) is no failure:
 *     nothing is shown, and the next
 *     opening loads again.
 *   A link to the page shown (the tab's link included) is marked aria-current="page".
 * - Bound once: a second copy of the script, or a second run of it, leaves a tab already bound (data-a4-mega-bound)
 *   as it is.
 * Plain JavaScript, no library; window.matchMedia, setTimeout and fetch are read from window, so the behaviour can
 * be run outside a browser (tests/tests/design/js/mega_menu.js, tests/tests/design/js/mega_menu_load.js).
 */
(function () {
    'use strict';

    var ENTER_DELAY = 300, LEAVE_DELAY = 400, LOAD_TIMEOUT = 10000, RETRY_DELAY = 600, RETRIES = 1;
    var menus = [];      // per tab with a mega menu: { close, isOpen }; one panel is open at a time
    var leaving = false; // the page is being left: a request the browser stops then is no failure

    // why a load did not give the menu: 'transient' (tried again), 'signed-out', 'failed' or 'aborted' (no failure)
    function Failure(kind, detail) {
        this.kind = kind;
        this.detail = detail;
    }

    function megaMenu(item) {
        if (item.getAttribute('data-a4-mega-bound') !== null) return;
        var toggle = item.querySelector('.a4-mega-toggle');
        var panel = toggle ? document.getElementById(toggle.getAttribute('aria-controls')) : null;
        var body = panel ? panel.querySelector('[data-a4-mega-body]') : null;
        if (!panel || !body) return;
        item.setAttribute('data-a4-mega-bound', '');
        var wide = window.matchMedia('(min-width: 576px)');
        var fine = window.matchMedia('(hover: hover) and (pointer: fine)');
        var mode = null;     // null (closed), 'preview' (opened by the mouse resting on it) or 'open'
        var timer = null;    // the pending preview or close
        var loading = null;  // the load of the links: running, or done (then kept); null before it and after a failure
        var loadingText = (body.textContent || '').replace(/\s+/g, ' ').trim();

        // the button is shown: one header row, and the tab not in the "More" list
        function usable() {
            return wide.matches && !(item.closest && item.closest('.a4-nav-more-list'));
        }

        // the "More" list, the user menu, the site menu or the Help menu of the header is open
        function otherOpen() {
            return !!document.querySelector('.a4-nav-more-toggle[aria-expanded="true"], #a4-user-toggle[aria-expanded="true"], #a4-site-toggle[aria-expanded="true"], #a4-help-toggle[aria-expanded="true"]');
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

        // the panel's status line, with an action (a link or a button) under it when given
        function status(text, action) {
            body.innerHTML = '';
            var p = document.createElement('p');
            p.className = 'a4-mega-status';
            p.setAttribute('role', 'status');
            p.textContent = text;
            body.appendChild(p);
            if (action) {
                var row = document.createElement('p');
                row.className = 'a4-mega-actions';
                row.appendChild(action);
                body.appendChild(row);
            }
        }

        function showFailed() {
            var retry = document.createElement('button');
            retry.setAttribute('type', 'button');
            retry.className = 'a4-btn';
            retry.setAttribute('data-a4-mega-retry-button', '');
            retry.textContent = panel.getAttribute('data-a4-mega-retry') || '';
            retry.addEventListener('click', function () {
                // kept in the panel until the answer: a click on a removed button would read as one outside
                retry.disabled = true;
                load(true);
            });
            status(panel.getAttribute('data-a4-mega-error') || '', retry);
        }

        function showSignedOut() {
            var login = panel.getAttribute('data-a4-mega-login');
            var a = null;
            if (login) {
                a = document.createElement('a');
                a.setAttribute('href', login);
                a.className = 'a4-btn';
                a.textContent = panel.getAttribute('data-a4-mega-signin') || '';
            }
            status(panel.getAttribute('data-a4-mega-signed-out') || panel.getAttribute('data-a4-mega-error') || '', a);
        }

        // the links to the page shown, the tab's link included
        function markCurrent(root) {
            var here = window.location ? window.location.pathname : null;
            if (!here) return;
            Array.prototype.forEach.call(root.querySelectorAll('a[href]'), function (a) {
                if (a.pathname === here) a.setAttribute('aria-current', 'page');
            });
        }

        // one request: given up (and aborted where the browser can) when no answer has come after LOAD_TIMEOUT, so a
        // server that never answers does not leave the panel busy and the load blocked for good; the answer's text,
        // or a Failure
        function attempt() {
            var controller = typeof window.AbortController === 'function' ? new window.AbortController() : null;
            var init = {
                method: 'GET',
                credentials: 'same-origin',
                cache: 'no-store',
                redirect: 'follow',
                headers: { 'Accept': 'text/html', 'X-Requested-With': 'XMLHttpRequest' }
            };
            if (controller) init.signal = controller.signal;
            return new Promise(function (resolve, reject) {
                var settled = false, timedOut = false;
                function settle(fn, value) {
                    if (settled) return;
                    settled = true;
                    window.clearTimeout(limit);
                    fn(value);
                }
                var limit = window.setTimeout(function () {
                    timedOut = true;
                    if (controller) controller.abort();
                    settle(reject, new Failure('transient', 'timeout'));
                }, LOAD_TIMEOUT);
                var pending;
                try {
                    pending = window.fetch(toggle.getAttribute('data-a4-mega-src'), init);
                } catch (e) {
                    settle(reject, new Failure('transient', 'network'));
                    return;
                }
                pending.then(function (response) {
                    var code = response.status;
                    // the view names its verdict (X-Exp-Mega): a signed-in user refused the tab is not signed out
                    var verdict = response.headers && response.headers.get ? response.headers.get('X-Exp-Mega') : null;
                    if (verdict === 'denied' || verdict === 'unknown') throw new Failure('failed', verdict);
                    if (code === 401 || code === 403) throw new Failure('signed-out', code);
                    if (response.redirected && /\/user\/login(?:[/?#]|$)/.test(response.url || '')) throw new Failure('signed-out', 'login');
                    if (code >= 500 || code === 408 || code === 429) throw new Failure('transient', code);
                    if (!response.ok) throw new Failure('failed', code);
                    return response.text();
                }).then(function (html) {
                    settle(resolve, html);
                }, function (error) {
                    if (error instanceof Failure) settle(reject, error);
                    else if (error && error.name === 'AbortError') settle(reject, new Failure(timedOut ? 'transient' : 'aborted', 'abort'));
                    else settle(reject, new Failure('transient', 'network'));
                });
            });
        }

        // the menu in the answer: the fragment's marked element, or a Failure (a sign-in page is the signed-out state)
        function fragment(html) {
            var template = document.createElement('template');
            template.innerHTML = html;
            var content = template.content;
            var groups = content.querySelector('[data-a4-mega-fragment]') || content.querySelector('[data-a4-mega-groups]');
            if (groups) return groups;
            if (content.querySelector('input[name="Login"]')) throw new Failure('signed-out', 'login form');
            throw new Failure('transient', 'not the menu');
        }

        // the request and its parsing, tried again once after a passing failure or a request the browser stopped
        function fetchMenu(tries) {
            return attempt().then(fragment).catch(function (failure) {
                if (!(failure instanceof Failure)) failure = new Failure('failed', 'parse');
                if ((failure.kind !== 'transient' && failure.kind !== 'aborted') || tries >= RETRIES || leaving) throw failure;
                return new Promise(function (resolve) { window.setTimeout(resolve, RETRY_DELAY); }).then(function () {
                    return fetchMenu(tries + 1);
                });
            });
        }

        // keep: the panel's content stays until the answer (the "Try again" button clicked, which must not leave the
        // panel under the click); otherwise a failure shown before gives way to the loading line again
        function load(keep) {
            if (loading) return loading;
            panel.setAttribute('aria-busy', 'true');
            if (!keep && body.querySelector('.a4-mega-actions')) status(loadingText);
            loading = fetchMenu(0).then(function (groups) {
                body.innerHTML = '';
                body.appendChild(groups);
                markCurrent(panel);
                panel.removeAttribute('aria-busy');
            }, function (failure) {
                loading = null;
                panel.removeAttribute('aria-busy');
                // stopped by the browser, or the page being left: no failure; the next opening loads again
                if (leaving || failure.kind === 'aborted') {
                    status(loadingText);
                    return;
                }
                if (failure.kind === 'signed-out') showSignedOut();
                else showFailed();
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
            if (next) {
                menus.forEach(function (m) { if (m !== self) m.close(); });
                load();
            }
            else if (focusToggle) toggle.focus();
        }

        // this tab's panel to the others: closed when another opens; opened on purpose (not a preview), it keeps
        // the hover preview of another tab from opening
        var self = {
            close: function () {
                if (mode) set(null, false);
                else stop();
            },
            isOpen: function () { return mode === 'open'; }
        };
        menus.push(self);

        // another tab's panel opened by a click or a key
        function otherMegaOpen() {
            return menus.some(function (m) { return m !== self && m.isOpen(); });
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
                if (!mode && usable() && !otherOpen() && !otherMegaOpen()) set('preview', false);
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

    // a request the browser stops while the page is left (any link followed, the panel's or another) is no failure:
    // browsers reject a fetch still running when the next page starts loading (Firefox with a network error, not an
    // AbortError), and the error would show for the seconds the next page takes. The page is in use again when it
    // comes back from the back-forward cache, or when the user acts on it after a navigation that did not happen (a
    // page's own "leave this page?" prompt answered with stay).
    function left() { leaving = true; }
    function inUse() { leaving = false; }
    window.addEventListener('pagehide', left);
    window.addEventListener('beforeunload', left);
    window.addEventListener('pageshow', inUse);
    document.addEventListener('pointerdown', inUse, true);
    document.addEventListener('keydown', inUse, true);

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();
