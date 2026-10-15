{*
   The width switch at the bottom of every left menu (parts/*/menu.tpl): three icon buttons, narrow, medium and
   wide, that set the admin_left_menu_size preference. A POST form (ezformtoken adds its token), so the preference
   is written from this site; user/preferences/set returns to the page it came from. The current size is the
   pressed button (aria-pressed="true"); a width dragged by hand presses none. Each button is named by its hidden
   text and shows it as its tooltip. One markup for every module: include this template, do not copy it.
*}
{def $a4_left_size = ezpreference( 'admin_left_menu_size' )
     $a4_left_sizes = array( hash( 'value', 'small',  'icon', 'arrows-collapse-vertical', 'name', 'Small'|i18n( 'design/admin/parts/content/menu' ) ),
                             hash( 'value', 'medium', 'icon', 'layout-sidebar-inset',     'name', 'Medium'|i18n( 'design/admin/parts/content/menu' ) ),
                             hash( 'value', 'large',  'icon', 'arrows-expand-vertical',   'name', 'Large'|i18n( 'design/admin/parts/content/menu' ) ) )}
{if eq( $a4_left_size, '' )}{set $a4_left_size = 'small'}{/if}
<form id="widthcontrol-links" class="widthcontrol" method="post" action={'/user/preferences/set'|ezurl}>
<input type="hidden" name="Key" value="admin_left_menu_size" />
<div class="a4-tabs is-segmented is-icons" role="group" aria-label="{'Width of the left column'|i18n( 'design/admin/pagelayout' )|wash}">
{foreach $a4_left_sizes as $a4_size}
    <button type="submit" name="Value" value="{$a4_size.value}" title="{$a4_size.name|wash}" aria-pressed="{if eq( $a4_left_size, $a4_size.value )}true{else}false{/if}"><span class="a4-icon a4-bi-{$a4_size.icon}" aria-hidden="true"></span><span class="a4-sr-only">{$a4_size.name|wash}</span></button>
{/foreach}
</div>
</form>
{undef $a4_left_size $a4_left_sizes}
