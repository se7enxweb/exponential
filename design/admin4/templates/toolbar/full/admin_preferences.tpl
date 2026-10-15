{if and( ne( $ui_context, 'edit' ), ne( $ui_context, 'browse' ))}
<div id="currentuserpreferences">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">

<h4>{'User preferences'|i18n( 'design/admin/pagelayout' )}</h4>

{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">

{* Each preference: its name and its on/off choice on one row, drawn as the design's tabs (ul.tabs, the current one
   in li.selected). The choice is a POST form like the left column's width switch (parts/left_width_control.tpl):
   two buttons, on and off, the current state the pressed one (aria-pressed="true"), so the preference is written
   from this site only and a screen reader says "Locations, on, toggle button, pressed"; ezformtoken adds its token
   and user/preferences/set returns to this page. A group around the list (not role="group" on the ul: its items
   would lose their list) carries the preference's name. *}
{def $a4_prefs = array( hash( 'key', 'admin_edit_show_locations', 'id', 'a4-pref-locations',
                              'name', 'Locations'|i18n( 'design/admin/parts/my/menu' ),
                              'title_on', 'Enable location window when editing content.'|i18n( 'design/admin/parts/my/menu' ),
                              'title_off', 'Disable location window when editing content.'|i18n( 'design/admin/parts/my/menu' ) ),
                        hash( 'key', 'admin_edit_show_re_edit', 'id', 'a4-pref-re-edit',
                              'name', 'Re-edit'|i18n( 'design/admin/parts/my/menu' ),
                              'title_on', 'Enable &quot;Back to edit&quot; checkbox when editing content.'|i18n( 'design/admin/parts/my/menu' ),
                              'title_off', 'Disable &quot;Back to edit&quot; checkbox when editing content.'|i18n( 'design/admin/parts/my/menu' ) ) )
     $a4_pref_on = false()}
<div class="settings">
<ul>
{foreach $a4_prefs as $a4_pref}
    {set $a4_pref_on = ezpreference( $a4_pref.key )|ne( '' )|and( ezpreference( $a4_pref.key )|ne( '0' ) )}
    <li class="nobullet"><span class="a4-pref-label" id="{$a4_pref.id}">{$a4_pref.name}</span>
    <form class="a4-pref-form" method="post" action={'/user/preferences/set'|ezurl}>
    <input type="hidden" name="Key" value="{$a4_pref.key}" />
    {if and( is_set( $requested_uri_string ), $requested_uri_string|trim( '/' )|ne( '' ) )}<input type="hidden" name="RedirectURIAfterSet" value="{$requested_uri_string|trim( '/' )|wash}" />{/if}
    <div class="a4-pref-tabs" role="group" aria-labelledby="{$a4_pref.id}"><ul class="tabs">
        <li{if $a4_pref_on} class="selected"{/if}><button type="submit" name="Value" value="1" title="{$a4_pref.title_on}" aria-pressed="{if $a4_pref_on}true{else}false{/if}">{'on'|i18n( 'design/admin/parts/my/menu' )}</button></li><li{if $a4_pref_on|not} class="selected"{/if}><button type="submit" name="Value" value="0" title="{$a4_pref.title_off}" aria-pressed="{if $a4_pref_on}false{else}true{/if}">{'off'|i18n( 'design/admin/parts/my/menu' )}</button></li>
    </ul></div>
    </form>
    </li>
{/foreach}
</ul>
</div>
{undef $a4_prefs $a4_pref_on}

{* DESIGN: Content END *}</div></div></div>

</div>
{/if}