{* A tile's figure on the Setup overview, named by menu.ini [Leftmenu_setup] LinkFigureTemplates[<key>] and included
   by parts/ini_menu_tiles.tpl with figure_key, the link key, for a tile the user may open only. Only figures that
   one cheap fetch gives: classes and sections defined, content languages, active extensions (those that exist),
   signed-in users. Any other key draws nothing. One and many are two strings each. *}
{def $figure_count = false()}
{switch match=$figure_key}
{case match='classes'}
    {set $figure_count = fetch( 'class', 'list' )|count}
<span class="a4-setup-tile-figure">{if eq( $figure_count, 1 )}{'1 class'|i18n( 'design/admin/setup/setupmenu' )}{else}{'%count classes'|i18n( 'design/admin/setup/setupmenu',, hash( '%count', $figure_count ) )}{/if}</span>
{/case}
{case match='sections'}
    {set $figure_count = fetch( 'section', 'list' )|count}
<span class="a4-setup-tile-figure">{if eq( $figure_count, 1 )}{'1 section'|i18n( 'design/admin/setup/setupmenu' )}{else}{'%count sections'|i18n( 'design/admin/setup/setupmenu',, hash( '%count', $figure_count ) )}{/if}</span>
{/case}
{case match='languages'}
    {set $figure_count = fetch( 'content', 'translation_list' )|count}
<span class="a4-setup-tile-figure">{if eq( $figure_count, 1 )}{'1 language'|i18n( 'design/admin/setup/setupmenu' )}{else}{'%count languages'|i18n( 'design/admin/setup/setupmenu',, hash( '%count', $figure_count ) )}{/if}</span>
{/case}
{case match='extensions'}
    {* expinfo( 'active' ): the active extensions that exist, ActiveExtensions[] and ActiveAccessExtensions[] together *}
    {set $figure_count = expinfo( 'active' )|count}
<span class="a4-setup-tile-figure">{if eq( $figure_count, 1 )}{'1 active'|i18n( 'design/admin/setup/setupmenu' )}{else}{'%count active'|i18n( 'design/admin/setup/setupmenu',, hash( '%count', $figure_count ) )}{/if}</span>
{/case}
{case match='sessions'}
    {set $figure_count = fetch( 'user', 'logged_in_count' )}
<span class="a4-setup-tile-figure">{if eq( $figure_count, 1 )}{'1 user signed in'|i18n( 'design/admin/setup/setupmenu' )}{else}{'%count users signed in'|i18n( 'design/admin/setup/setupmenu',, hash( '%count', $figure_count ) )}{/if}</span>
{/case}
{case}{/case}
{/switch}
{undef $figure_count}
