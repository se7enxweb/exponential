{if and( ne( $ui_context, 'edit' ), ne( $ui_context, 'browse' ))}
<div id="currentuserpreferences">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">

<h4>{'User preferences'|i18n( 'design/admin/pagelayout' )}</h4>

{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">

{* Each preference: its name and its on/off choice on one row, drawn as the design's tabs (ul.tabs, the current
   one in li.selected); the current state is a span.current with aria-current="true", the other one the link that
   changes it. A group around the list (not role="group" on the ul: its items would lose their list) carries the
   preference's name. *}
<div class="settings">
<ul>
    <li class="nobullet"><span class="a4-pref-label" id="a4-pref-locations">{'Locations'|i18n( 'design/admin/parts/my/menu')}</span>
    <div class="a4-pref-tabs" role="group" aria-labelledby="a4-pref-locations"><ul class="tabs">
    {if ezpreference( 'admin_edit_show_locations' )}
        <li class="selected"><span class="current" aria-current="true">{'on'|i18n( 'design/admin/parts/my/menu' )}</span></li><li><a href={'/user/preferences/set/admin_edit_show_locations/0'|ezurl} title="{'Disable location window when editing content.'|i18n( 'design/admin/parts/my/menu' )}">{'off'|i18n( 'design/admin/parts/my/menu' )}</a></li>
    {else}
        <li><a href={'/user/preferences/set/admin_edit_show_locations/1'|ezurl} title="{'Enable location window when editing content.'|i18n( 'design/admin/parts/my/menu' )}">{'on'|i18n( 'design/admin/parts/my/menu' )}</a></li><li class="selected"><span class="current" aria-current="true">{'off'|i18n( 'design/admin/parts/my/menu' )}</span></li>
    {/if}
    </ul></div>
    </li>
    <li class="nobullet"><span class="a4-pref-label" id="a4-pref-re-edit">{'Re-edit'|i18n( 'design/admin/parts/my/menu')}</span>
    <div class="a4-pref-tabs" role="group" aria-labelledby="a4-pref-re-edit"><ul class="tabs">
    {if ezpreference( 'admin_edit_show_re_edit' )}
        <li class="selected"><span class="current" aria-current="true">{'on'|i18n( 'design/admin/parts/my/menu' )}</span></li><li><a href={'/user/preferences/set/admin_edit_show_re_edit/0'|ezurl} title="{'Disable &quot;Back to edit&quot; checkbox when editing content.'|i18n( 'design/admin/parts/my/menu' )}">{'off'|i18n( 'design/admin/parts/my/menu' )}</a></li>
    {else}
        <li><a href={'/user/preferences/set/admin_edit_show_re_edit/1'|ezurl} title="{'Enable &quot;Back to edit&quot; checkbox when editing content.'|i18n( 'design/admin/parts/my/menu' )}">{'on'|i18n( 'design/admin/parts/my/menu' )}</a></li><li class="selected"><span class="current" aria-current="true">{'off'|i18n( 'design/admin/parts/my/menu' )}</span></li>
    {/if}
    </ul></div>
    </li>
</ul>
</div>

{* DESIGN: Content END *}</div></div></div>

</div>
{/if}