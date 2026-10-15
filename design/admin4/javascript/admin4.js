/*
 * admin4: keeps the space the page leaves for the fixed header (--header-height on the root element) equal to the
 * header's real height, at all times.
 *
 * admin4.css gives the header one row of 56px from 1280px, two rows (48 + 40px) from 576px and one row of 56px on a
 * phone, and sets these heights as the default of --header-height. The header still changes height with the user's
 * text size, when the fonts arrive or when a long name wraps: a ResizeObserver on the header follows every change;
 * where there is none, resize and load events do. The value is set on <html>, so the side columns, the sticky edit bar
 * and the scroll margins (modules/content.css) all read the same. Plain JavaScript, no library.
 *
 * It also keeps --a4-sticky-edge on <html>: the header's height, and on edit pages the bottom of the sticky button
 * bar (#controlbar-top), so a field scrolled into view and the rich text editor's toolbar stay below both.
 */
(function () {
    'use strict';

    function sync() {
        var header = document.getElementById('header');
        var root = document.documentElement;
        if (!header) return;
        // Pixels, not rem: a rem is the browser's root font size (user text size, mobile minimum size), which is not
        // always 16px, and a value divided by 16 then left less room than the header really takes.
        var px = header.getBoundingClientRect().height;
        if (root.style.getPropertyValue('--header-height') !== px + 'px') {
            root.style.setProperty('--header-height', px + 'px');
        }
        // --a4-sticky-edge on <html>: where the visible page starts under the header and, on edit pages, the sticky
        // button bar (#controlbar-top, stuck at its CSS top). The scroll padding and the editor toolbars use it.
        var edge = px, bar = document.getElementById('controlbar-top');
        if (bar && window.getComputedStyle && window.getComputedStyle(bar).position === 'sticky') {
            var top = parseFloat(window.getComputedStyle(bar).top);
            edge = Math.max(edge, (isNaN(top) ? px : top) + bar.getBoundingClientRect().height + 4);
        }
        edge = Math.ceil(edge) + 'px';
        if (document.documentElement.style.getPropertyValue('--a4-sticky-edge') !== edge) {
            document.documentElement.style.setProperty('--a4-sticky-edge', edge);
            // for scripts that cannot read it from CSS, such as a rich text editor's sticky toolbar offset
            document.dispatchEvent(new CustomEvent('a4:sticky-edge', { detail: { edge: parseFloat(edge) } }));
        }
    }

    function init() {
        var header = document.getElementById('header');
        if (!header) return;
        sync();
        if (window.ResizeObserver) {
            var observer = new ResizeObserver(sync), bar = document.getElementById('controlbar-top');
            observer.observe(header);
            if (bar) observer.observe(bar);
        } else {
            window.addEventListener('resize', sync);
        }
        window.addEventListener('load', sync);
        if (document.fonts && document.fonts.ready) document.fonts.ready.then(sync);
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();

/*
 * admin4 header menus: the main menu's "More" list, the user menu, the site menu and the "/" key.
 *
 * From 576px the main menu is one line (admin4.css). The tabs that do not fit move, in their order, into the list of
 * the "More" item at the row's end (page_topmenu.tpl: li.a4-nav-more, hidden until needed); they move back when there
 * is room again. A ResizeObserver on the menu's panel refits the row (the panel's width does not depend on the tabs:
 * flex-basis 0 from 1280px, the full width below); without one, the window's resize event does. Below 576px every tab
 * stays in the phone menu's grid. "More" is marked like the selected tab while the current page's tab is in it.
 * The panel gets .a4-nav-fits once the script runs: the row no longer clips or scrolls (the CSS fallback without the
 * script), so the list can open below it.
 *
 * "More", the user menu (parts/a4_header_user.tpl) and the site menu (parts/a4_header_site.tpl) are disclosures: a button with aria-expanded and aria-controls
 * and a list of plain links. A click or Enter/Space on the button opens and closes it, ArrowDown opens it and goes to
 * its first link, ArrowDown/ArrowUp/Home/End move between the links, Escape closes it and gives the focus back to the
 * button; a click outside or the focus leaving it closes it. At most one is open.
 *
 * "/" (outside a field, without modifier keys) puts the focus in the header search when it is shown (the input
 * carries aria-keyshortcuts="/").
 */
(function () {
    'use strict';

    var open = null; // the open disclosure: { button, panel }

    function setOpen(button, panel, state, focusButton) {
        button.setAttribute('aria-expanded', state ? 'true' : 'false');
        panel.hidden = !state;
        if (state) {
            if (open && open.button !== button) setOpen(open.button, open.panel, false, false);
            open = { button: button, panel: panel };
        } else {
            if (open && open.button === button) open = null;
            if (focusButton) button.focus();
        }
    }

    function links(panel) {
        return Array.prototype.filter.call(panel.querySelectorAll('a[href]'), function (a) {
            return a.offsetParent !== null;
        });
    }

    function disclosure(button, panel) {
        if (!button || !panel) return;
        button.addEventListener('click', function () {
            setOpen(button, panel, button.getAttribute('aria-expanded') !== 'true', false);
        });
        button.addEventListener('keydown', function (e) {
            if (e.key !== 'ArrowDown') return;
            e.preventDefault();
            setOpen(button, panel, true, false);
            var first = links(panel)[0];
            if (first) first.focus();
        });
        panel.addEventListener('keydown', function (e) {
            var list = links(panel), i = list.indexOf(document.activeElement), next = null;
            if (e.key === 'ArrowDown') next = list[Math.min(list.length - 1, i + 1)];
            else if (e.key === 'ArrowUp') next = i <= 0 ? null : list[i - 1];
            else if (e.key === 'Home') next = list[0];
            else if (e.key === 'End') next = list[list.length - 1];
            else return;
            e.preventDefault();
            if (next) next.focus(); else if (e.key === 'ArrowUp') button.focus();
        });
        // the focus leaving the button and its list closes it (Tab past the last link, a click elsewhere)
        var wrap = button.parentNode;
        wrap.addEventListener('focusout', function (e) {
            if (button.getAttribute('aria-expanded') === 'true' && e.relatedTarget && !wrap.contains(e.relatedTarget)) {
                setOpen(button, panel, false, false);
            }
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && open && !e.defaultPrevented) {
            // also after a mouse click, which leaves the focus on the page in Safari and Firefox on macOS; the focus
            // goes back to the button only when it was in the disclosure
            var active = document.activeElement;
            e.preventDefault();
            setOpen(open.button, open.panel, false, active === open.button || open.panel.contains(active));
            return;
        }
        if (e.key !== '/' || e.ctrlKey || e.metaKey || e.altKey || e.isComposing || e.defaultPrevented) return;
        var t = e.target;
        if (t && (t.isContentEditable || /^(INPUT|TEXTAREA|SELECT)$/.test(t.tagName))) return;
        // not out of a widget that takes text or out of a dialog
        if (t && t.closest && t.closest('[role="textbox"], [role="combobox"], [role="dialog"], [aria-modal="true"], dialog')) return;
        var field = document.getElementById('searchtext');
        if (!field || field.disabled || field.offsetParent === null) return;
        e.preventDefault();
        field.focus();
        if (field.select) field.select();
    });
    document.addEventListener('click', function (e) {
        if (open && !open.button.parentNode.contains(e.target)) setOpen(open.button, open.panel, false, false);
    });

    function overflowMenu() {
        // found from the "More" item: admin4l's tab menu block draws its row inside the layout's own menu panel
        var more = document.querySelector('#header .a4-nav-more');
        var bar = more && more.parentNode;
        var nav = bar && bar.closest ? bar.closest('.navbar-menu') : null;
        if (!nav) return;
        var toggle = more.querySelector('.a4-nav-more-toggle');
        var list = more.querySelector('.a4-nav-more-list');
        if (!toggle || !list) return;
        var wide = window.matchMedia('(min-width: 576px)');
        // the tabs, in their order: every item of the row but "More" and the phone menu's own items (its search, the
        // user's actions and the close button, page_topmenu.tpl)
        var tabs = Array.prototype.filter.call(bar.children, function (li) {
            return li !== more && !li.classList.contains('header-search-mobile') && !li.classList.contains('a4-phone-only');
        });

        function fits() {
            var last = more.hidden ? tabs.filter(function (li) { return li.parentNode === bar; }).pop() : more;
            if (!last) return true;
            var style = window.getComputedStyle(bar);
            var end = bar.getBoundingClientRect().right - parseFloat(style.paddingRight || '0');
            return last.getBoundingClientRect().right <= end + 0.5;
        }

        function fit() {
            nav.classList.toggle('a4-nav-fits', wide.matches);
            // the phone menu's grid holds every tab: nothing to do while none is in the list
            if (!wide.matches && !list.firstChild) return;
            // all tabs back in the row, in their order, before "More". Only the ones that are elsewhere move: a moved
            // element loses the focus, and this runs on every resize. The focus is given back if it was in a tab.
            var active = document.activeElement;
            tabs.forEach(function (li, i) {
                var next = i + 1 < tabs.length ? tabs[i + 1] : more;
                if (li.parentNode !== bar || li.nextSibling !== next) bar.insertBefore(li, next.parentNode === bar ? next : more);
            });
            more.hidden = true;
            if (wide.matches && !fits()) {
                more.hidden = false;
                for (var i = tabs.length - 1; i >= 0 && !fits(); i--) {
                    list.insertBefore(tabs[i], list.firstChild);
                }
            }
            if (more.hidden && toggle.getAttribute('aria-expanded') === 'true') setOpen(toggle, list, false, false);
            toggle.classList.toggle('has-current', !!list.querySelector('[aria-current]'));
            if (active && active !== document.activeElement && document.contains(active) && active.offsetParent !== null) active.focus();
        }

        // on edit pages the tabs lead nowhere (page_topmenu.tpl: .navbar-menu.disabled), and neither does "More"
        if (nav.classList.contains('disabled')) toggle.disabled = true;
        disclosure(toggle, list);
        fit();
        if (window.ResizeObserver) new ResizeObserver(fit).observe(nav);
        else window.addEventListener('resize', fit);
        if (wide.addEventListener) wide.addEventListener('change', fit);
        if (document.fonts && document.fonts.ready) document.fonts.ready.then(fit);
    }

    // the site menu (parts/a4_header_site.tpl): its button takes the place of the site link, which stays for a browser
    // without the script
    function siteMenu() {
        var wrap = document.getElementById('a4-site-menu-wrap');
        var button = document.getElementById('a4-site-toggle');
        var panel = document.getElementById('a4-site-menu');
        if (!wrap || !button || !panel) return;
        var link = wrap.parentNode.querySelector('.site-preview');
        wrap.hidden = false;
        if (link) link.hidden = true;
        disclosure(button, panel);
    }

    function init() {
        overflowMenu();
        disclosure(document.getElementById('a4-user-toggle'), document.getElementById('a4-user-menu'));
        siteMenu();
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();

/*
 * admin4 toasts: window.a4Toast( message, kind, opener, delay ) shows a short feedback of an action done in the page in
 * the regions #a4-toasts (polite) or, for kind 'bad', #a4-toasts-alert (assertive) of the frame. kind is 'ok',
 * 'bad', 'warn' or 'info'; opener is the element the action came from (optional); delay the time in ms it stays
 * (optional, for a toast with an action such as Undo). The toast does not take the
 * focus, closes after 5 s (an error stays until it is dismissed; Escape closes a focused toast), waits while the pointer or the focus is on it, and has a close button named by the
 * region's data-close-label. Closed with that button, it gives the focus to the opener when it is still in the page;
 * without one the focus goes to the page, as with any removed element. A toast with the same text replaces the one
 * shown; at most three stay, the oldest goes first. Without the regions it does nothing.
 */
(function () {
    'use strict';
    var DELAY = 5000, MAX = 3;
    window.a4Toast = function (message, kind, opener, delay) {
        var region = document.getElementById(kind === 'bad' ? 'a4-toasts-alert' : 'a4-toasts');
        if (!region || !message) return null;
        // the same text again replaces the toast shown; more than MAX: the oldest goes
        Array.prototype.forEach.call(region.querySelectorAll('.a4-toast'), function (old) {
            var t = old.querySelector('.a4-toast-text');
            if (t && t.textContent === String(message) && old.parentNode) old.parentNode.removeChild(old);
        });
        while (region.querySelectorAll('.a4-toast').length >= MAX) region.removeChild(region.querySelector('.a4-toast'));
        var labels = document.getElementById('a4-toasts');
        var toast = document.createElement('div');
        toast.className = 'a4-toast' + (/^(ok|bad|warn|info)$/.test(kind || '') ? ' is-' + kind : '');
        var text = document.createElement('span');
        text.className = 'a4-toast-text';
        text.textContent = String(message);
        var close = document.createElement('button');
        close.type = 'button';
        close.className = 'a4-toast-close';
        close.setAttribute('aria-label', (labels && labels.getAttribute('data-close-label')) || 'Dismiss');
        close.textContent = '\u00d7';
        toast.appendChild(text);
        toast.appendChild(close);
        var timer = null, held = false;
        function remove() { if (timer) clearTimeout(timer); if (toast.parentNode) toast.parentNode.removeChild(toast); }
        // an error stays until it is dismissed (WCAG 2.2.1): the user may have to act on it
        function arm() { if (timer) clearTimeout(timer); if (!held && kind !== 'bad') timer = setTimeout(remove, delay > 0 ? delay : DELAY); }
        function hold(on) { held = on; if (on) { if (timer) clearTimeout(timer); timer = null; } else arm(); }
        close.addEventListener('click', function () {
            remove();
            if (opener && opener.focus && document.body.contains(opener)) opener.focus();
        });
        // Escape on a focused toast closes it like its button
        toast.addEventListener('keydown', function (e) { if (e.key === 'Escape') { e.preventDefault(); close.click(); } });
        toast.addEventListener('mouseenter', function () { hold(true); });
        toast.addEventListener('mouseleave', function () { hold(toast.contains(document.activeElement)); });
        toast.addEventListener('focusin', function () { hold(true); });
        toast.addEventListener('focusout', function (e) { if (!toast.contains(e.relatedTarget)) hold(toast.matches(':hover')); });
        region.appendChild(toast);
        arm();
        return toast;
    };
})();

/*
 * admin4: chips with data-a4-copy (the node view's node, object and remote ids) copy their value to the
 * clipboard when clicked, and show a tick for a moment.
 */
(function () {
    'use strict';
    function copy(text) {
        if (navigator.clipboard && window.isSecureContext) return navigator.clipboard.writeText(text);
        return new Promise(function (resolve, reject) {
            // the textarea takes the focus to be selected; give it back to where it was (the chip)
            var before = document.activeElement;
            var t = document.createElement('textarea');
            t.value = text; t.setAttribute('readonly', ''); t.style.position = 'fixed'; t.style.opacity = '0';
            document.body.appendChild(t); t.select();
            try { document.execCommand('copy') ? resolve() : reject(); } catch (e) { reject(e); }
            document.body.removeChild(t);
            if (before && before.focus) before.focus();
        });
    }
    document.addEventListener('click', function (e) {
        var chip = e.target.closest ? e.target.closest('[data-a4-copy]') : null;
        if (!chip) return;
        e.preventDefault();
        copy(chip.getAttribute('data-a4-copy')).then(function () {
            chip.classList.add('a4-copied');
            setTimeout(function () { chip.classList.remove('a4-copied'); }, 1400);
            // the tick is only seen; the toast also tells a screen reader
            if (window.a4Toast && chip.getAttribute('data-a4-copied'))
                window.a4Toast(chip.getAttribute('data-a4-copied') + ': ' + chip.getAttribute('data-a4-copy'), 'ok', chip);
        }, function () {
            // a copy that failed says so instead of doing nothing
            if (window.a4Toast && chip.getAttribute('data-a4-copy-failed'))
                window.a4Toast(chip.getAttribute('data-a4-copy-failed'), 'bad', chip);
        });
    });
})();

/*
 * admin4 sign-in page (user/login.tpl):
 *  - the eye button shows and hides the password;
 *  - the form is sent once: a second press (or Enter) while it is on its way does nothing. The button is NOT
 *    disabled -- a disabled button is left out of the form, and the kernel signs in only when LoginButton is
 *    posted; it is marked busy instead, with its "Signing in" label.
 */
(function () {
    'use strict';
    function init() {
        var form = document.querySelector('form.a4-login-form');
        if (!form) return;
        var reveal = form.querySelector('.a4-reveal'), pw = document.getElementById('passwordtext');
        if (reveal && pw) {
            reveal.addEventListener('click', function () {
                var show = pw.type === 'password';
                pw.type = show ? 'text' : 'password';
                reveal.setAttribute('aria-pressed', show ? 'true' : 'false');
                var label = reveal.getAttribute(show ? 'data-label-hide' : 'data-label-show') || '';
                reveal.setAttribute('aria-label', label); reveal.title = label;
                pw.focus();
            });
        }
        var sent = false, button = form.querySelector('.a4-login-submit');
        form.addEventListener('submit', function (e) {
            if (sent) { e.preventDefault(); return; }
            sent = true;
            if (pw && pw.type === 'text') pw.type = 'password';
            if (button) {
                button.classList.add('a4-busy');
                button.setAttribute('aria-busy', 'true');
                if (button.getAttribute('data-label-busy')) button.textContent = button.getAttribute('data-label-busy');
            }
        });
        // back/forward cache: a page shown again is ready to send again
        window.addEventListener('pageshow', function (e) {
            if (e.persisted && button) { sent = false; button.classList.remove('a4-busy'); button.removeAttribute('aria-busy'); }
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();

/*
 * Reduced motion reaches jQuery's effects too (slideDown and slideUp of the edit page's attribute groups, fades):
 * the CSS rule in admin4.css cannot stop them, jQuery.fx.off ends each one at once.
 */
(function () {
    'use strict';
    if (window.jQuery && window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        window.jQuery.fx.off = true;
    }
}());

/*
 * window.a4CardBusy( card, on, message ): the busy state of an .a4-card.is-busy, complete. On: aria-busy="true",
 * inert (the keyboard cannot act on what is being saved) and, when given, a short message ("Saving...") in a polite
 * live region outside the card, so a screen reader hears why the focus left the card. Off: all of it removed, and a
 * second message (the result) can be given the same way. The region is created once, hidden, at the end of the body.
 */
(function () {
    'use strict';
    function region() {
        var r = document.getElementById('a4-busy-status');
        if (!r) {
            r = document.createElement('div');
            r.id = 'a4-busy-status';
            r.className = 'a4-sr-only';
            r.setAttribute('role', 'status');
            document.body.appendChild(r);
        }
        return r;
    }
    window.a4CardBusy = function (card, on, message) {
        if (!card) return;
        if (on) {
            card.setAttribute('aria-busy', 'true');
            card.setAttribute('inert', '');
        } else {
            card.removeAttribute('aria-busy');
            card.removeAttribute('inert');
        }
        if (message) region().textContent = String(message);
    };
})();

/*
 * admin4: the groups of a long left menu (details.a4-navgroup, parts/ini_menu.tpl) keep their open or closed state
 * through window.a4Pref: for a signed-in user the eZPreference admin_navgroups (the closed groups' ids, comma
 * separated), for an anonymous page the browser. A group that holds the current page (data-a4-current) opens
 * whatever was stored.
 */
(function () {
    'use strict';

    var NAME = 'admin_navgroups';

    function closed() {
        var value = window.a4Pref ? window.a4Pref.get(NAME) : null;
        return value ? value.split(',').filter(function (id) { return id !== '' && id !== '-'; }) : [];
    }

    function save(list) {
        if (window.a4Pref) window.a4Pref.set(NAME, list.join(','));
    }

    function init() {
        var groups = document.querySelectorAll('details.a4-navgroup[data-a4-navgroup]');
        var list = closed();
        Array.prototype.forEach.call(groups, function (group) {
            var key = group.getAttribute('data-a4-navgroup');
            if (!group.hasAttribute('data-a4-current') && list.indexOf(key) !== -1) group.open = false;
            group.addEventListener('toggle', function () {
                var now = closed().filter(function (id) { return id !== key; });
                if (!group.open) now.push(key);
                save(now);
            });
        });
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();

/*
 * admin4: the height of the node view's tab box (#window-controls: View, Details, ...), one height of the user's own
 * for every node. The grip (.a4-tabs-height-grip, parts/a4_tabs_height.tpl) is a focusable separator: it is dragged
 * with the mouse, a finger or a pen, or moved with the arrow keys (16px; Shift, Page Up and Page Down 64px; Home and
 * End the smallest and the largest height), the same steps as the grips of the left column and the debug panel. The
 * button beside it (shown only while the height is not the default) and a double-click on the grip go back to the
 * default and forget the stored height instead of storing the default, so a later change of the default reaches the
 * user.
 * Stored as a number of pixels: for a signed-in user the eZPreference admin_node_preview_height through window.a4Pref
 * (written on <html> outside every cache-block, parts/a4_preference_attributes.tpl); window.a4Pref keeps it in this
 * browser on an anonymous page and when the preference could not be saved. On load the preference wins over the
 * browser's copy, which wins over the default. A height is saved once: at the end of a drag, 400ms after the last key
 * (or when the page is left before that), at once by the reset button, and only when it changed. It is applied
 * before the page is drawn as --a4-node-tabs-height on <html>; admin4.css uses it from 576px only (a phone keeps the
 * default) and holds it between 10em and 60em, and at most 85% of the window.
 */
(function () {
    'use strict';

    var NAME = 'admin_node_preview_height', KEY = 'exp-a4-node-preview-height', DELAY = 400, STEP = 16, BIG = 64;
    var root = document.documentElement;

    function parse(value) {
        return /^\d{2,4}$/.test(String(value || '')) ? parseInt(value, 10) : null;
    }

    // the preference (signed in; "" once reset), else this browser's copy (window.a4Pref)
    function stored() {
        return window.a4Pref ? parse(window.a4Pref.get(NAME, KEY)) : null;
    }

    function show(height) {
        if (height === null) {
            root.removeAttribute('data-a4-node-tabs-height');
            root.style.removeProperty('--a4-node-tabs-height');
        } else {
            root.setAttribute('data-a4-node-tabs-height', String(height));
            root.style.setProperty('--a4-node-tabs-height', height + 'px');
        }
    }

    var saved = stored();
    show(saved);

    // null: back to the default; the stored height is forgotten (the preference set empty, the browser's copy removed)
    function save(height) {
        if (height === saved) return;
        saved = height;
        if (window.a4Pref) window.a4Pref.set(NAME, height === null ? '' : String(height), KEY);
    }

    function init() {
        var box = document.getElementById('window-controls');
        var bar = box ? box.querySelector('.a4-tabs-height') : null;
        var grip = bar ? bar.querySelector('.a4-tabs-height-grip') : null;
        var reset = bar ? bar.querySelector('.a4-tabs-height-reset') : null;
        if (!grip || !reset) return;

        var current = saved, timer = null, drag = null;

        // the box's em: the default is 20em (pagelayout.css; the layouts tab draws 22em), the range 10em to 60em and
        // at most 85% of the window, as in admin4.css. A height equal to the 20em default is no height of the user's
        // own on any tab; shown is what the box draws without one on the open tab.
        function limits() {
            var em = parseFloat(window.getComputedStyle(box).fontSize) || 16;
            var min = Math.round(em * 10);
            return {
                min: min,
                max: Math.max(min, Math.round(Math.min(em * 60, window.innerHeight * 0.85))),
                def: Math.round(em * 20),
                shown: Math.round(em * (box.classList.contains('layouts-active') ? 22 : 20))
            };
        }

        // the height drawn now, worked out as admin4.css does it: the box's own height can lag behind while a
        // transition runs, and the keys would then step from an old value
        function drawn(l) {
            return current === null ? l.shown : Math.min(l.max, Math.max(l.min, current));
        }

        function update() {
            var l = limits();
            grip.setAttribute('aria-valuemin', String(l.min));
            grip.setAttribute('aria-valuemax', String(l.max));
            grip.setAttribute('aria-valuenow', String(drawn(l)));
            reset.hidden = current === null;
        }

        // a height within the limits; the default itself is no height of the user's own
        function apply(height) {
            var l = limits();
            if (height !== null) {
                height = Math.min(l.max, Math.max(l.min, Math.round(height)));
                if (height === l.def) height = null;
            }
            current = height;
            show(height);
            update();
        }

        function flush() {
            if (timer !== null) { window.clearTimeout(timer); timer = null; }
            save(current);
        }

        function later() {
            if (timer !== null) window.clearTimeout(timer);
            timer = window.setTimeout(flush, DELAY);
        }

        function stop(event) {
            if (!drag || (event && event.pointerId !== drag.id)) return;
            drag = null;
            grip.classList.remove('is-dragging');
            document.body.classList.remove('a4-tabs-resizing');
            flush();
        }

        grip.addEventListener('pointerdown', function (event) {
            if (event.button !== undefined && event.button !== 0) return;
            event.preventDefault();
            drag = { id: event.pointerId, y: event.clientY, height: drawn(limits()) };
            if (grip.setPointerCapture) grip.setPointerCapture(event.pointerId);
            grip.classList.add('is-dragging');
            document.body.classList.add('a4-tabs-resizing');
            grip.focus();
        });
        grip.addEventListener('pointermove', function (event) {
            if (drag && event.pointerId === drag.id) apply(drag.height + event.clientY - drag.y);
        });
        grip.addEventListener('pointerup', stop);
        grip.addEventListener('pointercancel', stop);
        grip.addEventListener('lostpointercapture', stop);

        grip.addEventListener('keydown', function (event) {
            var l = limits(), height = drawn(l), next = null;
            var step = event.shiftKey ? BIG : STEP;
            if (event.key === 'ArrowDown') next = height + step;
            else if (event.key === 'ArrowUp') next = height - step;
            else if (event.key === 'PageDown') next = height + BIG;
            else if (event.key === 'PageUp') next = height - BIG;
            else if (event.key === 'Home') next = l.min;
            else if (event.key === 'End') next = l.max;
            if (next === null) return;
            event.preventDefault();
            apply(next);
            later();
        });

        function toDefault() {
            grip.focus();   // the button hides itself: the focus stays on the control
            apply(null);
            flush();
        }
        reset.addEventListener('click', toDefault);
        // as on the grips of the debug panel and of the details panel: a double-click gives the default back
        grip.addEventListener('dblclick', function (event) {
            if (event && event.preventDefault) event.preventDefault();
            toDefault();
        });

        // a change still waiting when the page is left is saved
        window.addEventListener('pagehide', function () { if (timer !== null) flush(); });
        window.addEventListener('resize', update);
        // node_tabs.js switches the layouts tab's class: its default height differs
        if (window.MutationObserver) new window.MutationObserver(update).observe(box, { attributes: true, attributeFilter: ['class'] });

        box.classList.add('a4-tabs-sizable');
        bar.hidden = false;
        update();
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();

/*
 * The content of the rich text editors follows the page's mode. TinyMCE 3 and 8 (the online editor) draw a field's
 * text in an iframe with ezoe's own light content stylesheet. The edit form names two stylesheets in
 * data-a4-editor-css (components.css for the tokens, editor_content.css for the rules); each editor's document gets
 * them once, and html[data-a4-theme] of the page is copied to it when the editor is ready and whenever the mode
 * changes. Editors are found by their body class (mceContentBody in TinyMCE 3, mce-content-body in 8), through the
 * editor API where it exists and on the page's load as a fallback. Plain JavaScript, no library.
 * TinyMCE 8's own interface (toolbar, menus, dialogs, status bar) follows the mode too: an editor made on a dark page
 * starts with the skin oxide-dark instead of oxide (the skin option is set before the theme loads it; an editor
 * configured with another skin keeps it), and a change of the mode swaps the loaded oxide and oxide-dark
 * stylesheets of the page and of the editors' documents. TinyMCE 3 keeps its skin.
 */
(function () {
    'use strict';

    function sheets() {
        var form = document.querySelector('form[data-a4-editor-css]');
        return form ? form.getAttribute('data-a4-editor-css').split(/\s+/).filter(Boolean) : [];
    }

    function dress(doc) {
        if (!doc || !doc.body || !/\b(mceContentBody|mce-content-body)\b/.test(doc.body.className)) return;
        doc.documentElement.setAttribute('data-a4-theme', document.documentElement.getAttribute('data-a4-theme') || 'light');
        if (doc.getElementById('a4-editor-css-0')) return;
        sheets().forEach(function (href, i) {
            var link = doc.createElement('link');
            link.id = 'a4-editor-css-' + i;
            link.rel = 'stylesheet';
            link.type = 'text/css';
            link.href = href;
            (doc.head || doc.getElementsByTagName('head')[0] || doc.documentElement).appendChild(link);
        });
    }

    function all() {
        Array.prototype.forEach.call(document.querySelectorAll('iframe'), function (frame) {
            try { dress(frame.contentDocument); } catch (e) { /* a frame of another origin is no editor */ }
        });
    }

    function dark() {
        return document.documentElement.getAttribute('data-a4-theme') === 'dark';
    }

    // TinyMCE 8: the skin of an editor that is not rendered yet. The silver theme registers the option only when it
    // loads, and registering it again keeps a value set before, so it is registered here with the same rule.
    function skin(editor) {
        var options = editor.options;
        if (!options || !options.register || editor.initialized) return;
        if (!options.isRegistered('skin')) {
            options.register('skin', { processor: function (value) { return typeof value === 'string' || value === false; }, default: 'oxide' });
        }
        if (options.get('skin') === 'oxide' && dark()) options.set('skin', 'oxide-dark');
        else if (options.get('skin') === 'oxide-dark' && !dark()) options.set('skin', 'oxide');
    }

    // TinyMCE 8: the oxide stylesheets already loaded (skin.min.css on the page, content.min.css in each editor's
    // document) are swapped for the mode's
    function swapSkins() {
        var from = dark() ? '/skins/ui/oxide/' : '/skins/ui/oxide-dark/';
        var to = dark() ? '/skins/ui/oxide-dark/' : '/skins/ui/oxide/';
        var docs = [document];
        Array.prototype.forEach.call(document.querySelectorAll('iframe'), function (frame) {
            try { if (frame.contentDocument) docs.push(frame.contentDocument); } catch (e) { /* another origin */ }
        });
        docs.forEach(function (doc) {
            Array.prototype.forEach.call(doc.querySelectorAll('link[rel="stylesheet"][href*="' + from + '"]'), function (link) {
                link.setAttribute('href', link.getAttribute('href').replace(from, to));
            });
        });
    }

    function hook() {
        var mce = window.tinymce;
        if (!mce) return;
        var v3 = !!(mce.onAddEditor && mce.onAddEditor.add);
        if (!v3 && mce.on) {
            // editors made before this script ran are rendered already: swapSkins() dresses them
            Array.prototype.forEach.call(mce.get ? mce.get() : [], skin);
            mce.on('AddEditor', function (e) { skin(e.editor); });
        }
        if (!sheets().length) return;
        // PreInit: the editor's document exists but is not shown yet, so a dark page shows no light flash; init again
        // for an editor that sets its body class only then
        function watch(editor) {
            if (editor.initialized) dress(editor.getDoc());
            else if (v3) {
                if (editor.onPreInit) editor.onPreInit.add(function (ed) { dress(ed.getDoc()); });
                editor.onInit.add(function (ed) { dress(ed.getDoc()); });
            } else {
                editor.on('PreInit init', function () { dress(editor.getDoc()); });
            }
        }
        // editors made before this script ran, then the ones that come
        var existing = v3 ? (mce.editors || []) : (mce.get ? mce.get() : []);
        Array.prototype.forEach.call(existing, watch);
        if (v3) mce.onAddEditor.add(function (manager, editor) { watch(editor); });
        else if (mce.on) mce.on('AddEditor', function (e) { watch(e.editor); });
    }

    function init() {
        hook();
        window.addEventListener('load', swapSkins);
        if (window.MutationObserver) {
            new MutationObserver(swapSkins).observe(document.documentElement, { attributes: true, attributeFilter: ['data-a4-theme'] });
        }
        if (!sheets().length) return;
        window.addEventListener('load', all);
        all();
        if (window.MutationObserver) {
            new MutationObserver(all).observe(document.documentElement, { attributes: true, attributeFilter: ['data-a4-theme'] });
        }
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();

/*
 * The segmented switches answer the arrow keys like a toolbar of toggle buttons (the left menu's width switch, the
 * version preview's devices, ezoe's engine switch: .a4-tabs.is-segmented). Left and Right
 * move the focus to the previous and next choice (round), Home and End to the first and last; disabled choices are
 * skipped. Every choice keeps its Tab stop and is pressed with Enter or Space as before, and Up and Down keep
 * scrolling the page. One listener on the document, so a switch drawn later answers too.
 */
(function () {
    'use strict';

    var GROUP = '.a4-tabs.is-segmented';

    function choices(group) {
        return Array.prototype.filter.call(group.children, function (el) {
            return (el.tagName === 'BUTTON' || (el.tagName === 'A' && el.hasAttribute('href'))) && !el.disabled;
        });
    }

    document.addEventListener('keydown', function (event) {
        var key = event.key, target = event.target;
        if (event.altKey || event.ctrlKey || event.metaKey || event.shiftKey) return;
        if (key !== 'ArrowLeft' && key !== 'ArrowRight' && key !== 'Home' && key !== 'End') return;
        var group = target && target.parentNode;
        if (!group || !group.matches || !group.matches(GROUP)) return;
        var list = choices(group), at = list.indexOf(target), next;
        if (at < 0 || list.length < 2) return;
        if (key === 'Home') next = list[0];
        else if (key === 'End') next = list[list.length - 1];
        else next = list[(at + (key === 'ArrowRight' ? 1 : -1) + list.length) % list.length];
        event.preventDefault();
        next.focus();
    });
})();

/*
 * The version preview in the width of a device (content/view/versionview.tpl, #a4-content-preview-bar and
 * #a4-content-preview-stage; styles in modules/content.css .a4-content-preview-*). The device switch (iPhone |
 * iPad | Desktop | Large Desktop) and the landscape switch are aria-pressed buttons; the iframe is given the
 * device's size in CSS pixels (data-width, data-height of the button) and scaled down when the column is narrower,
 * the line beside them says the size and the scale. The choice is kept through window.a4Pref: for a signed-in user
 * the eZPreferences admin_preview_device (phone | tablet | desktop | large; empty: desktop) and
 * admin_preview_landscape (1 | 0), for an anonymous page the browser. Without the script the bar stays hidden and
 * the iframe fills the column as before. The stage is redrawn only when its width changes or a device is chosen, so
 * a scrollbar that comes and goes with the new height cannot start a loop, and a hidden stage is left alone.
 */
(function () {
    'use strict';

    var DEVICE = 'admin_preview_device', TURN = 'admin_preview_landscape';

    function pref(name, legacyKey) { return window.a4Pref ? window.a4Pref.get(name, legacyKey) : null; }
    function keep(name, value, legacyKey) { if (window.a4Pref) window.a4Pref.set(name, value, legacyKey); }

    function init() {
        var bar = document.getElementById('a4-content-preview-bar'),
            stage = document.getElementById('a4-content-preview-stage');
        if (!bar || !stage) return;
        var device = stage.querySelector('.a4-content-preview-device'),
            frame = stage.querySelector('iframe'),
            buttons = Array.prototype.slice.call(bar.querySelectorAll('button[data-a4-device]')),
            turn = bar.querySelector('button[data-a4-landscape]'),
            size = bar.querySelector('.a4-content-preview-size'),
            sizeText = bar.getAttribute('data-a4-size') || '',
            current = null, landscape = false, lastWidth = -1;
        if (!device || !frame || !buttons.length || !turn || !size) return;

        function turnable() {
            var id = current && current.getAttribute('data-a4-device');
            return id === 'phone' || id === 'tablet';
        }
        // force: a new device or orientation; otherwise only a changed column width redraws
        function draw(force) {
            var width = stage.clientWidth;
            if (!current || !width || (force !== true && width === lastWidth)) return;
            lastWidth = width;
            var w = parseInt(current.getAttribute('data-width'), 10), h = parseInt(current.getAttribute('data-height'), 10), t;
            if (landscape && turnable()) { t = w; w = h; h = t; }
            var style = window.getComputedStyle(stage),
                room = width - parseFloat(style.paddingLeft) - parseFloat(style.paddingRight) - 2,   // the frame's edges
                scale = Math.min(1, Math.max(room, 1) / w);
            frame.style.width = w + 'px';
            frame.style.height = h + 'px';
            frame.style.transform = scale < 1 ? 'scale(' + scale + ')' : '';
            device.style.width = Math.floor(w * scale) + 'px';
            device.style.height = Math.floor(h * scale) + 'px';
            size.textContent = sizeText.replace('%width', w).replace('%height', h).replace('%scale', Math.round(scale * 100));
        }
        function choose(button) {
            current = button;
            buttons.forEach(function (b) { b.setAttribute('aria-pressed', b === button ? 'true' : 'false'); });
            turn.disabled = !turnable();
            turn.setAttribute('aria-pressed', landscape && turnable() ? 'true' : 'false');
            draw(true);
        }

        var stored = pref(DEVICE, 'exp-admin4-preview-device'), initial = null;
        buttons.forEach(function (b) {
            if (b.getAttribute('data-a4-device') === stored) initial = b;
            b.addEventListener('click', function () {
                if (current === b) return;
                keep(DEVICE, b.getAttribute('data-a4-device'), 'exp-admin4-preview-device');
                choose(b);
            });
        });
        if (!initial) initial = bar.querySelector('button[data-a4-device="desktop"]') || buttons[0];
        landscape = pref(TURN, 'exp-admin4-preview-landscape') === '1';
        turn.addEventListener('click', function () {
            if (turn.disabled) return;
            landscape = !landscape;
            keep(TURN, landscape ? '1' : '0', 'exp-admin4-preview-landscape');
            choose(current);
        });
        bar.hidden = false;
        stage.classList.add('is-device');
        choose(initial);
        if (window.ResizeObserver) new ResizeObserver(draw).observe(stage);
        else window.addEventListener('resize', draw);
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();

/*
 * admin4: Escape dismisses the environment badge shown on hover (parts/a4_environment_badge.tpl, BadgeDisplay=hover).
 *
 * With a mouse or a trackpad the badge shows while the pointer rests on the logo or on the badge, or the logo has the
 * keyboard focus (admin4.css). Content that appears on hover or focus must be dismissible without moving the pointer
 * or the focus (WCAG 1.4.13): Escape adds .a4-env-dismissed, which hides it, and it shows again once the pointer
 * comes back to the logo or the logo gets the focus again. Without the script the badge still goes when the pointer
 * or the focus leaves. The Escape of an open menu or dialog comes first (defaultPrevented).
 */
(function () {
    'use strict';

    function init() {
        var badge = document.getElementById('a4-env-badge');
        if (!badge || !badge.classList.contains('a4-env-hover')) return;
        var logo = badge.previousElementSibling;
        if (!logo || logo.tagName !== 'A') return;

        function shown() {
            return logo.matches(':hover') || logo.matches(':focus-visible') || badge.matches(':hover');
        }
        function again() { badge.classList.remove('a4-env-dismissed'); }

        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape' || e.defaultPrevented || badge.classList.contains('a4-env-dismissed') || !shown()) return;
            badge.classList.add('a4-env-dismissed');
        });
        logo.addEventListener('pointerenter', again);
        logo.addEventListener('focus', again);
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();
