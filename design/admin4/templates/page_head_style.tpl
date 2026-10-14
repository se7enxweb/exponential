{* admin4: admin3's stylesheets, then admin4.css -- the admin4 look -- and the shared components and module stylesheets (design.ini AdminComponentCSSFileList), before the extensions' own stylesheets, so an extension keeps the last word on its own markup. *}
{* Light, dark or system mode, before anything is drawn: html[data-a4-theme] (light|dark, what the stylesheets read)
   and html[data-a4-theme-choice] (light|dark|system, what the switch shows) from this browser's choice
   (localStorage "exp-admin4-theme"), light by default; "system" follows prefers-color-scheme, also when the system
   changes while the page is open. The header's #a4-theme-toggle steps light -> dark -> system -> light and stores
   the step; a switch without data-label-system (an older copy of the markup) keeps stepping between light and dark.
   Storage that is blocked (private windows, policies) only means the choice is not remembered.
   This script is the one piece of the page's look that cannot wait for the stylesheets (a page drawn light and then
   switched would flash). It also sets the left column's hand-dragged width (data-a4-left-width on <html>, from the
   admin_left_menu_size preference) as --left-sidebar-width; the preset sizes are in pagelayout.css. No <style>. *}
{literal}<script>
(function () {
    var KEY = 'exp-admin4-theme', root = document.documentElement;
    var mq = window.matchMedia ? window.matchMedia( '(prefers-color-scheme: dark)' ) : null;
    var w = root.getAttribute('data-a4-left-width');
    if (w && /^\d{2,4}(\.\d+)?px$/.test(w)) root.style.setProperty('--left-sidebar-width', w);
    function stored() { try { return window.localStorage.getItem(KEY); } catch (e) { return null; } }
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
