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
        // Pixels, not rem: a rem is the browser's root font size (user text size, mobile minimum size), which is not
        // always 16px, and a value divided by 16 then left less room than the header really takes.
        var px = header.getBoundingClientRect().height;
        if (flex.style.getPropertyValue('--header-height') !== px + 'px') {
            flex.style.setProperty('--header-height', px + 'px');
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

/*
 * The content of the rich text editors follows the page's mode. TinyMCE 3 and 8 (the online editor) draw a field's
 * text in an iframe with ezoe's own light content stylesheet. The edit form names two stylesheets in
 * data-a4-editor-css (components.css for the tokens, editor_content.css for the rules); each editor's document gets
 * them once, and html[data-a4-theme] of the page is copied to it when the editor is ready and whenever the mode
 * changes. Editors are found by their body class (mceContentBody in TinyMCE 3, mce-content-body in 8), through the
 * editor API where it exists and on the page's load as a fallback. Plain JavaScript, no library.
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

    function hook() {
        var mce = window.tinymce;
        if (!mce || !sheets().length) return;
        var v3 = !!(mce.onAddEditor && mce.onAddEditor.add);
        function watch(editor) {
            if (editor.initialized) dress(editor.getDoc());
            else if (v3) editor.onInit.add(function (ed) { dress(ed.getDoc()); });
            else editor.on('init', function () { dress(editor.getDoc()); });
        }
        // editors made before this script ran, then the ones that come
        var existing = v3 ? (mce.editors || []) : (mce.get ? mce.get() : []);
        Array.prototype.forEach.call(existing, watch);
        if (v3) mce.onAddEditor.add(function (manager, editor) { watch(editor); });
        else if (mce.on) mce.on('AddEditor', function (e) { watch(e.editor); });
    }

    function init() {
        if (!sheets().length) return;
        hook();
        window.addEventListener('load', all);
        all();
        if (window.MutationObserver) {
            new MutationObserver(all).observe(document.documentElement, { attributes: true, attributeFilter: ['data-a4-theme'] });
        }
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();
