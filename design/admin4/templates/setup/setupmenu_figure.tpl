{* A tile's figure on the Setup overview (setup/setupmenu.tpl, parts/ini_menu_tiles.tpl). Variable: figure_key, the
   link key of the tile. Only figures that one cheap fetch or a setting gives, and only for a tile the user may open
   (this template is included for a shown tile only): the number of classes, sections, content languages, active
   extensions and signed-in users. Every other tile has none. *}
{switch match=$figure_key}
{case match='classes'}
<span class="a4-setup-tile-figure">{'%count defined'|i18n( 'design/admin/setup/setupmenu',, hash( '%count', fetch( 'class', 'list' )|count ) )}</span>
{/case}
{case match='sections'}
<span class="a4-setup-tile-figure">{'%count defined'|i18n( 'design/admin/setup/setupmenu',, hash( '%count', fetch( 'section', 'list' )|count ) )}</span>
{/case}
{case match='languages'}
<span class="a4-setup-tile-figure">{'%count in use'|i18n( 'design/admin/setup/setupmenu',, hash( '%count', fetch( 'content', 'translation_list' )|count ) )}</span>
{/case}
{case match='extensions'}
<span class="a4-setup-tile-figure">{'%count active'|i18n( 'design/admin/setup/setupmenu',, hash( '%count', ezini( 'ExtensionSettings', 'ActiveExtensions' )|merge( ezini( 'ExtensionSettings', 'ActiveAccessExtensions' ) )|unique|count ) )}</span>
{/case}
{case match='sessions'}
<span class="a4-setup-tile-figure">{'%count signed in'|i18n( 'design/admin/setup/setupmenu',, hash( '%count', fetch( 'user', 'logged_in_count' ) ) )}</span>
{/case}
{case}{/case}
{/switch}
