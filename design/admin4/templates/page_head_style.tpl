{* admin4: its own base stylesheet base.css (element defaults, frame, content classes, tables, modal window), the kernel's debug.css, then admin4.css -- the admin4 look -- and the shared components and module stylesheets (design.ini AdminComponentCSSFileList), before the extensions' own stylesheets, so an extension keeps the last word on its own markup. *}
{* Light, dark or system mode, before anything is drawn: html[data-a4-theme] (light|dark, what the stylesheets read)
   and html[data-a4-theme-choice] (light|dark|system, what the switch shows) from this browser's choice
   (window.a4Pref: the signed-in user's eZPreference admin_color_mode; localStorage "exp-admin4-theme" on an
   anonymous page), light by default; "system" follows prefers-color-scheme, also when the system
   changes while the page is open. The header's #a4-theme-toggle steps light -> dark -> system -> light and stores
   the step; its name says the mode in use ("Color mode: …", data-label-*), its title the next step
   (data-title-*), and #a4-theme-status announces a press. A switch without data-label-system (an older copy of
   the markup) keeps stepping between light and dark. Other open admin tabs follow through the storage event: a
   signed-in user's choice is written to localStorage too once it is saved (window.a4Pref.set's share).
   Storage that is blocked (private windows, policies) only means the choice is not remembered.
   This script is the one piece of the page's look that cannot wait for the stylesheets (a page drawn light and then
   switched would flash). It also sets the left column's hand-dragged width (data-a4-left-width on <html>, from the
   admin_left_menu_size preference; without a preference and without a preset size, the browser's copy of a width
   that could not be saved) as --left-sidebar-width; the preset sizes are in base.css. The code is a4_head.js. *}
{ezscript( array( 'a4_head.js' ) )}

{if is_unset( $load_css_file_list )}
  {def $load_css_file_list = true()}
{/if}

{if is_unset( $load_main_css )}
  {def $load_main_css = true()}
{/if}

{* admin4 ships one theme stylesheet: an admin_theme preference left from an older admin design names a file admin4
   does not have, which the packer skipped (only an eZDebug warning) and the frame lost its theme; so the name is
   fixed here *}

{if $load_main_css}
  {* load main css files and required css files *}
  {if $load_css_file_list}
    {ezcss_load( array( 'base.css',
                        'debug.css',
                        'admin4.css',
                        ezini( 'StylesheetSettings', 'AdminComponentCSSFileList', 'design.ini' ),
                        ezini( 'StylesheetSettings', 'BackendCSSFileList', 'design.ini' ) ) )}
  {else}
    {ezcss_load( array( 'base.css',
                        'debug.css',
                        'admin4.css',
                        ezini( 'StylesheetSettings', 'AdminComponentCSSFileList', 'design.ini' ) ) )}
  {/if}

  {include uri='design:page_head_style_inline.tpl'}
{else}
  {* load required css files *}
  {ezcss_load()}
{/if}
