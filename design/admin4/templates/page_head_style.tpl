{* admin4: admin3's stylesheets, then admin4.css -- the admin4 look -- and the shared components and module stylesheets (design.ini AdminComponentCSSFileList), before the extensions' own stylesheets, so an extension keeps the last word on its own markup. *}
{* Light, dark or system mode, before anything is drawn: html[data-a4-theme] (light|dark, what the stylesheets read)
   and html[data-a4-theme-choice] (light|dark|system, what the switch shows) from this browser's choice
   (window.a4Pref: the signed-in user's eZPreference admin_color_mode; localStorage "exp-admin4-theme" on an
   anonymous page), light by default; "system" follows prefers-color-scheme, also when the system
   changes while the page is open. The header's #a4-theme-toggle steps light -> dark -> system -> light and stores
   the step; its name says the mode in use ("Color mode: …", data-label-*), its title the next step
   (data-title-*), and #a4-theme-status announces a press. A switch without data-label-system (an older copy of
   the markup) keeps stepping between light and dark. Other open admin tabs follow through the storage event.
   Storage that is blocked (private windows, policies) only means the choice is not remembered.
   This script is the one piece of the page's look that cannot wait for the stylesheets (a page drawn light and then
   switched would flash). It also sets the left column's hand-dragged width (data-a4-left-width on <html>, from the
   admin_left_menu_size preference) as --left-sidebar-width; the preset sizes are in pagelayout.css. No <style>. *}
{literal}<script>
/* window.a4Pref: the one place for the frame's remembered choices (color mode, side columns, left menu
   groups). For a signed-in user each is an eZPreference: parts/a4_preference_attributes.tpl writes it on <html>
   (data-a4-pref-<name>) and set() saves it through user/preferences/set_and_exit (data-a4-pref-url), so it
   follows the user from browser to browser. An anonymous page has no data-a4-pref-url and keeps the choice in this
   browser's localStorage only. get( name, legacyKey ): a signed-in user without the preference yet takes the
   browser's old choice under legacyKey along once. */
(function () {
    var root = document.documentElement, url = root.getAttribute('data-a4-pref-url');
    function attr(name) { return 'data-a4-pref-' + name.replace(/_/g, '-'); }
    function local(key, value) {
        try {
            if (value === undefined) return window.localStorage.getItem(key);
            window.localStorage.setItem(key, value);
        } catch (e) { /* blocked storage: the choice lasts for this page */ }
        return null;
    }
    function send(name, value) {
        var go = function () {
            var t = document.getElementById('ezxform_token_js'), token = t ? t.getAttribute('title') : '';
            if (!window.fetch) return;
            window.fetch(url + '/' + encodeURIComponent(name) + '/' + encodeURIComponent(value === '' ? '-' : value), {
                method: 'POST', credentials: 'same-origin',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'ezxform_token=' + encodeURIComponent(token)
            }).catch(function () {});
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
            if (old) window.a4Pref.set(name, old);
            return old;
        },
        set: function (name, value, legacyKey) {
            value = String(value);
            if (!url) { local(legacyKey || 'exp-a4-' + name, value); return; }
            root.setAttribute(attr(name), value === '' ? '-' : value);
            send(name, value);
        }
    };
})();
(function () {
    var KEY = 'exp-admin4-theme', root = document.documentElement;
    var mq = window.matchMedia ? window.matchMedia( '(prefers-color-scheme: dark)' ) : null;
    var w = root.getAttribute('data-a4-left-width');
    if (w && /^\d{2,4}(\.\d+)?px$/.test(w)) root.style.setProperty('--left-sidebar-width', w);
    // the eZPreference admin_color_mode (window.a4Pref); localStorage for an anonymous page
    function stored() { return window.a4Pref.get('admin_color_mode', KEY); }
    function save(next) { window.a4Pref.set('admin_color_mode', next, KEY); }
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
</script>{/literal}

{if is_unset( $load_css_file_list )}
  {def $load_css_file_list = true()}
{/if}

{if is_unset( $load_main_css )}
  {def $load_main_css = true()}
{/if}

{if is_unset( $admin_theme )}
  {def $admin_theme = ''}
{/if}

{if $admin_theme}
  {def $admin_theme_css = concat( 'theme/', $admin_theme, '.css' )}
{else}
  {def $admin_theme_css = 'theme/rounded.css'}
{/if}

{if $load_main_css}
  {* load main css files and required css files *}
  {if $load_css_file_list}
    {ezcss_load( array( 'core.css',
                        'debug.css',
                        'pagelayout.css',
                        'content.css',
                        $admin_theme_css,
                        'theme/admin_datatable.css',
                        'theme/modalwindow.css',
                        'admin4.css',
                        ezini( 'StylesheetSettings', 'AdminComponentCSSFileList', 'design.ini' ),
                        ezini( 'StylesheetSettings', 'BackendCSSFileList', 'design.ini' ) ) )}
  {else}
    {ezcss_load( array( 'core.css',
                        'debug.css',
                        'pagelayout.css',
                        'content.css',
                        $admin_theme_css,
                        'theme/admin_datatable.css',
                        'admin4.css',
                        ezini( 'StylesheetSettings', 'AdminComponentCSSFileList', 'design.ini' ) ) )}
  {/if}

  {include uri='design:page_head_style_inline.tpl'}
{else}
  {* load required css files *}
  {ezcss_load()}
{/if}
