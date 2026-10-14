{* admin4: admin3's stylesheets, then admin4.css -- the admin4 look -- and the shared components and module stylesheets (design.ini AdminComponentCSSFileList), before the extensions' own stylesheets, so an extension keeps the last word on its own markup. *}
{* Light or dark mode, before anything is drawn: html[data-a4-theme] from this browser's choice (localStorage
   "exp-admin4-theme"), light by default; then the header's #a4-theme-toggle switches and stores it. Storage that
   is blocked (private windows, policies) only means the choice is not remembered.
   This script is the one piece of the page's look that cannot wait for the stylesheets (a page drawn light and then
   switched would flash). It also sets the left column's hand-dragged width (data-a4-left-width on <html>, from the
   admin_left_menu_size preference) as --left-sidebar-width; the preset sizes are in pagelayout.css. The second
   script does the same for the density (html[data-a4-density], #a4-density-toggle). No <style>. *}
{literal}<script>
(function () {
    var KEY = 'exp-admin4-theme', root = document.documentElement;
    var w = root.getAttribute('data-a4-left-width');
    if (w && /^\d{2,4}(\.\d+)?px$/.test(w)) root.style.setProperty('--left-sidebar-width', w);
    function stored() { try { return window.localStorage.getItem(KEY); } catch (e) { return null; } }
    function apply(theme) {
        root.setAttribute('data-a4-theme', theme === 'dark' ? 'dark' : 'light');
        var b = document.getElementById('a4-theme-toggle');
        if (!b) return;
        var dark = root.getAttribute('data-a4-theme') === 'dark', label = b.querySelector('.a4-theme-label');
        b.setAttribute('aria-pressed', dark ? 'true' : 'false');
        if (label) label.textContent = b.getAttribute(dark ? 'data-label-dark' : 'data-label-light') || '';
    }
    apply(stored());
    document.addEventListener('DOMContentLoaded', function () {
        apply(root.getAttribute('data-a4-theme'));
        var b = document.getElementById('a4-theme-toggle');
        if (!b) return;
        b.addEventListener('click', function () {
            var next = root.getAttribute('data-a4-theme') === 'dark' ? 'light' : 'dark';
            try { window.localStorage.setItem(KEY, next); } catch (e) {}
            apply(next);
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
