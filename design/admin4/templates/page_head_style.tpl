{* admin4: admin3's stylesheets, then admin4.css -- the admin4 look -- before the extensions' own stylesheets, so an extension keeps the last word on its own markup. *}
{* Light or dark mode, before anything is drawn: html[data-a4-theme] from this browser's choice (localStorage
   "exp-admin4-theme"), light by default; then the header's #a4-theme-toggle switches and stores it. Storage that
   is blocked (private windows, policies) only means the choice is not remembered. *}
{literal}<script>
(function () {
    var KEY = 'exp-admin4-theme', root = document.documentElement;
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
                        ezini( 'StylesheetSettings', 'BackendCSSFileList', 'design.ini' ) ) )}
  {else}
    {ezcss_load( array( 'core.css',
                        'debug.css',
                        'pagelayout.css',
                        'content.css',
                        'responsive.css',
                        $admin_theme_css,
                        'theme/admin_datatable.css',
                        'admin4.css' ) )}
  {/if}

  {include uri='design:page_head_style_inline.tpl'}
{else}
  {* load required css files *}
  {ezcss_load()}
{/if}
