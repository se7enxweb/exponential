/* admin4: the head script of every page (page_head_style.tpl loads it before the stylesheets): window.a4Pref and
   the color mode, see the comment in page_head_style.tpl. A file of its own, not an inline script, so it runs under a
   Content Security Policy without 'unsafe-inline' (doc/features/6.0/content-security-policy.md). */
/* window.a4Pref: the one place for the frame's remembered choices (color mode, side columns, left menu
   groups). For a signed-in user each is an eZPreference: parts/a4_preference_attributes.tpl writes it on <html>
   (data-a4-pref-<name>) and set() saves it through user/preferences/set_and_exit (data-a4-pref-url), so it
   follows the user from browser to browser. An anonymous page has no data-a4-pref-url and keeps the choice in this
   browser's localStorage only. get( name, legacyKey ): a signed-in user without the preference yet takes the
   browser's old choice under legacyKey along once. A preference that cannot be saved (no fetch, a network error,
   an error status, or a redirect such as the login page of an expired session) is kept in localStorage instead,
   which get() reads while the preference is empty (that copy belongs to the browser, not to the user: the next user
   without the preference takes it along). set( name, '' ) forgets a choice: the preference is set empty ("-") and
   the browser's copy removed. A value equal to the one already on <html> is not sent again (a page that only
   restores a stored state writes nothing). set( name, value, legacyKey, share ): with share, a saved value is also
   written to the browser's copy, so other open tabs hear it through the storage event. */
(function () {
    var root = document.documentElement, url = root.getAttribute('data-a4-pref-url');
    // html.a4-js: the frame's scripts run. Set before the first paint, so the stylesheets draw a part the scripts
    // change once the document is parsed (the disclosures of the right column) in its final state from the start.
    if (root.classList) root.classList.add('a4-js');
    function attr(name) { return 'data-a4-pref-' + name.replace(/_/g, '-'); }
    function local(key, value) {
        try {
            if (value === undefined) return window.localStorage.getItem(key);
            if (value === '') window.localStorage.removeItem(key);
            else window.localStorage.setItem(key, value);
        } catch (e) { /* blocked storage: the choice lasts for this page */ }
        return null;
    }
    function send(name, value, failed, saved) {
        var fail = function () { if (failed) failed(); };
        var go = function () {
            var t = document.getElementById('ezxform_token_js'), token = t ? t.getAttribute('title') : '';
            if (!window.fetch) { fail(); return; }
            window.fetch(url + '/' + encodeURIComponent(name) + '/' + encodeURIComponent(value === '' ? '-' : value), {
                method: 'POST', credentials: 'same-origin', keepalive: true,
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'ezxform_token=' + encodeURIComponent(token)
            }).then(function (r) { if (!r.ok || r.redirected) fail(); else if (saved) saved(); }, fail);
        };
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', go); else go();
    }
    window.a4Pref = {
        signedIn: !!url,
        get: function (name, legacyKey) {
            if (!url) return local(legacyKey || 'exp-a4-' + name);
            var value = root.getAttribute(attr(name));
            if (value) return value === '-' ? '' : value;
            var old = legacyKey ? local(legacyKey) : null;
            if (old) window.a4Pref.set(name, old, legacyKey);
            return old;
        },
        set: function (name, value, legacyKey, share) {
            value = String(value);
            var key = legacyKey || 'exp-a4-' + name;
            if (!url) { if (local(key) !== value) local(key, value); return; }
            var shown = value === '' ? '-' : value;
            // unchanged: nothing to save (a stored state put back on load is not a choice)
            if (root.getAttribute(attr(name)) === shown) return;
            root.setAttribute(attr(name), shown);
            if (value === '') local(key, '');
            // not saved: this browser keeps it, unless a later choice has replaced it meanwhile; saved and shared:
            // the browser's copy follows, which tells the other open tabs
            send(name, value, function () { if (root.getAttribute(attr(name)) === shown) local(key, value); },
                 share ? function () { if (root.getAttribute(attr(name)) === shown) local(key, value); } : null);
        }
    };
})();
(function () {
    var KEY = 'exp-admin4-theme', root = document.documentElement;
    var mq = window.matchMedia ? window.matchMedia( '(prefers-color-scheme: dark)' ) : null;
    var w = root.getAttribute('data-a4-left-width');
    // a width the server did not get (window.a4Pref kept it in this browser), unless a preset size is chosen
    if (!w && !root.hasAttribute('data-a4-left-size') && window.a4Pref.signedIn) {
        try { w = window.localStorage.getItem('exp-a4-admin_left_menu_size'); } catch (e) { w = null; }
    }
    if (w && /^\d{2,4}(\.\d+)?px$/.test(w)) root.style.setProperty('--left-sidebar-width', w);
    // the eZPreference admin_color_mode (window.a4Pref); localStorage for an anonymous page
    function stored() { return window.a4Pref.get('admin_color_mode', KEY); }
    function save(next) { window.a4Pref.set('admin_color_mode', next, KEY, true); }
    function choiceOf(value) { return value === 'dark' || value === 'system' ? value : 'light'; }
    function apply(value) {
        var choice = choiceOf(value);
        var dark = choice === 'dark' || ( choice === 'system' && !!mq && mq.matches );
        root.setAttribute('data-a4-theme', dark ? 'dark' : 'light');
        root.setAttribute('data-a4-theme-choice', choice);
        var b = document.getElementById('a4-theme-toggle');
        if (!b) return;
        var label = b.querySelector('.a4-theme-label');
        if (label) label.textContent = b.getAttribute('data-label-' + choice) || b.getAttribute(dark ? 'data-label-dark' : 'data-label-light') || '';
        var title = b.getAttribute('data-title-' + choice);
        if (title) b.setAttribute('title', title);
    }
    // a press is announced once, politely, by the status region beside the switch (not on load)
    function announce(b) {
        var status = document.getElementById('a4-theme-status'), label = b.querySelector('.a4-theme-label');
        if (status && label) status.textContent = label.textContent;
    }
    apply(stored());
    if (mq) {
        var follow = function () { if (root.getAttribute('data-a4-theme-choice') === 'system') apply('system'); };
        if (mq.addEventListener) mq.addEventListener('change', follow); else if (mq.addListener) mq.addListener(follow);
    }
    document.addEventListener('DOMContentLoaded', function () {
        apply(root.getAttribute('data-a4-theme-choice'));
        var b = document.getElementById('a4-theme-toggle');
        if (!b) return;
        b.addEventListener('click', function () {
            var choice = root.getAttribute('data-a4-theme-choice'), next;
            if (choice === 'light') next = 'dark';
            else if (choice === 'dark' && b.hasAttribute('data-label-system')) next = 'system';
            else next = 'light';
            save(next);
            apply(next);
            announce(b);
        });
    });
    // another tab changed it: follow
    window.addEventListener('storage', function (e) { if (e.key === KEY) apply(e.newValue); });
    // paper is light: print with the light tokens, then go back to the chosen mode
    window.addEventListener('beforeprint', function () { root.setAttribute('data-a4-theme', 'light'); });
    window.addEventListener('afterprint', function () { apply(root.getAttribute('data-a4-theme-choice')); });
})();
/* admin4 has one density: the compact view and its switch are gone. A value an earlier version kept in this browser
   is dropped; a signed-in user's eZPreference admin_density is no longer read and does nothing. */
try { window.localStorage.removeItem('exp-admin4-density'); } catch (e) { /* blocked storage: nothing was kept */ }
