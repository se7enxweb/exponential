/*
 * admin4: keeps the space the page leaves for the fixed header (--header-height on .dashboard-flex) equal to the
 * header's real height, at all times.
 *
 * From 576px up the top menu wraps onto as many lines as it needs (admin4.css), so the header is as tall as its
 * lines and changes height when the window is resized, when the fonts arrive, when the light/dark toggle changes
 * its label. ezadmin_menubar.js measured the header once, at load: after a resize that added a line the header
 * covered the top of the page. A ResizeObserver on the header follows every change; where there is none, resize
 * and load events do. Plain JavaScript, no library.
 *
 * It also keeps --a4-sticky-edge on <html>: the header's height, and on edit pages the bottom of the sticky button
 * bar (#controlbar-top), so a field scrolled into view and the rich text editor's toolbar stay below both.
 */
(function () {
    'use strict';

    function sync() {
        var header = document.getElementById('header');
        var flex = document.querySelector('.dashboard-flex');
        if (!header || !flex) return;
        // Pixels, not rem: a rem is the browser's root font size (user text size, mobile minimum size), which is not
        // always 16px, and a value divided by 16 then left less room than the header really takes.
        var px = header.getBoundingClientRect().height;
        if (flex.style.getPropertyValue('--header-height') !== px + 'px') {
            flex.style.setProperty('--header-height', px + 'px');
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
 * admin4 toasts: window.a4Toast( message, kind, opener ) shows a short feedback of an action done in the page in
 * the regions #a4-toasts (polite) or, for kind 'bad', #a4-toasts-alert (assertive) of the frame. kind is 'ok',
 * 'bad', 'warn' or 'info'; opener is the element the action came from (optional). The toast does not take the
 * focus, closes after 5 s (an error stays until it is dismissed; Escape closes a focused toast), waits while the pointer or the focus is on it, and has a close button named by the
 * region's data-close-label. Closed with that button, it gives the focus to the opener when it is still in the page;
 * without one the focus goes to the page, as with any removed element. A toast with the same text replaces the one
 * shown; at most three stay, the oldest goes first. Without the regions it does nothing.
 */
(function () {
    'use strict';
    var DELAY = 5000, MAX = 3;
    window.a4Toast = function (message, kind, opener) {
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
        function arm() { if (timer) clearTimeout(timer); if (!held && kind !== 'bad') timer = setTimeout(remove, DELAY); }
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
