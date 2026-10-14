{* admin4: admin3's stylesheets, then admin4.css -- the admin4 look -- and the shared components and module stylesheets (design.ini AdminComponentCSSFileList), before the extensions' own stylesheets, so an extension keeps the last word on its own markup. *}
{* Light, dark or system mode, before anything is drawn: html[data-a4-theme] (light|dark, what the stylesheets read)
   and html[data-a4-theme-choice] (light|dark|system, what the switch shows) from this browser's choice
   (the signed-in user's eZPreference admin_color_mode, written on <html> as data-a4-theme-pref; localStorage
   "exp-admin4-theme" for anonymous pages and before the first choice), light by default; "system" follows prefers-color-scheme, also when the system
   changes while the page is open. The header's #a4-theme-toggle steps light -> dark -> system -> light and stores
   the step; its name says the mode in use ("Colour mode: …", data-label-*), its title the next step
   (data-title-*), and #a4-theme-status announces a press. A switch without data-label-system (an older copy of
   the markup) keeps stepping between light and dark. Other open admin tabs follow through the storage event.
   Storage that is blocked (private windows, policies) only means the choice is not remembered.
   This script is the one piece of the page's look that cannot wait for the stylesheets (a page drawn light and then
   switched would flash). It also sets the left column's hand-dragged width (data-a4-left-width on <html>, from the
   admin_left_menu_size preference) as --left-sidebar-width; the preset sizes are in pagelayout.css. The second
   script does the same for the density (html[data-a4-density], #a4-density-toggle). No <style>. *}
{literal}<script>
(function () {
    var KEY = 'exp-admin4-theme', root = document.documentElement;
    var mq = window.matchMedia ? window.matchMedia( '(prefers-color-scheme: dark)' ) : null;
    var w = root.getAttribute('data-a4-left-width');
    if (w && /^\d{2,4}(\.\d+)?px$/.test(w)) root.style.setProperty('--left-sidebar-width', w);
    // a signed-in user's choice is the eZPreference admin_color_mode (data-a4-theme-pref on <html>, empty before the
    // first choice); the browser's storage is the fallback for anonymous pages and the first visit
    function stored() {
        var pref = root.getAttribute('data-a4-theme-pref');
        if (pref) return pref;
        try { return window.localStorage.getItem(KEY); } catch (e) { return null; }
    }
    function save(next) {
        try { window.localStorage.setItem(KEY, next); } catch (e) {}
        if (!root.hasAttribute('data-a4-theme-pref')) return;
        root.setAttribute('data-a4-theme-pref', next);
        var t = document.getElementById('ezxform_token_js'), token = t ? t.getAttribute('title') : '';
        if (window.jQuery && jQuery.ez && token)
            jQuery.post(jQuery.ez.url.replace('ezjscore/', 'user/preferences/') + 'set_and_exit/admin_color_mode/' + next, { ezxform_token: token });
    }
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
})();
/* Normal or compact density: html[data-a4-density] from this browser's choice (localStorage "exp-admin4-density"),
   normal by default; #a4-density-toggle switches it (pressed = compact). Same pattern as the mode above. */
(function () {
    var KEY = 'exp-admin4-density', root = document.documentElement;
    function stored() { try { return window.localStorage.getItem(KEY); } catch (e) { return null; } }
    function apply(density) {
        var compact = density === 'compact';
        root.setAttribute('data-a4-density', compact ? 'compact' : 'normal');
        var b = document.getElementById('a4-density-toggle');
        if (b) b.setAttribute('aria-pressed', compact ? 'true' : 'false');
    }
    apply(stored());
    document.addEventListener('DOMContentLoaded', function () {
        apply(root.getAttribute('data-a4-density'));
        var b = document.getElementById('a4-density-toggle');
        if (!b) return;
        b.addEventListener('click', function () {
            var next = root.getAttribute('data-a4-density') === 'compact' ? 'normal' : 'compact';
            try { window.localStorage.setItem(KEY, next); } catch (e) {}
            apply(next);
        });
    });
    window.addEventListener('storage', function (e) { if (e.key === KEY) apply(e.newValue); });
})();
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
                        'responsive.css',
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
