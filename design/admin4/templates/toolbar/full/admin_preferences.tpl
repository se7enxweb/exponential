{if and( ne( $ui_context, 'edit' ), ne( $ui_context, 'browse' ))}
<div id="currentuserpreferences">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">

<h4>{'User preferences'|i18n( 'design/admin/pagelayout' )}</h4>

{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">

{* Each preference: its name and an on/off switch (.a4-tabs.is-segmented) on one row; the current state is the
   marked part (aria-current), the other one the link that changes it. *}
<div class="settings">
<ul>
    <li class="nobullet"><span class="a4-pref-label" id="a4-pref-locations">{'Locations'|i18n( 'design/admin/parts/my/menu')}</span>
    <span class="a4-tabs is-segmented" role="group" aria-labelledby="a4-pref-locations">
    {if ezpreference( 'admin_edit_show_locations' )}
        <span class="current" aria-current="true">{'on'|i18n( 'design/admin/parts/my/menu' )}</span><a href={'/user/preferences/set/admin_edit_show_locations/0'|ezurl} title="{'Disable location window when editing content.'|i18n( 'design/admin/parts/my/menu' )}">{'off'|i18n( 'design/admin/parts/my/menu' )}</a>
    {else}
        <a href={'/user/preferences/set/admin_edit_show_locations/1'|ezurl} title="{'Enable location window when editing content.'|i18n( 'design/admin/parts/my/menu' )}">{'on'|i18n( 'design/admin/parts/my/menu' )}</a><span class="current" aria-current="true">{'off'|i18n( 'design/admin/parts/my/menu' )}</span>
    {/if}
    </span>
    </li>
    <li class="nobullet"><span class="a4-pref-label" id="a4-pref-re-edit">{'Re-edit'|i18n( 'design/admin/parts/my/menu')}</span>
    <span class="a4-tabs is-segmented" role="group" aria-labelledby="a4-pref-re-edit">
    {if ezpreference( 'admin_edit_show_re_edit' )}
        <span class="current" aria-current="true">{'on'|i18n( 'design/admin/parts/my/menu' )}</span><a href={'/user/preferences/set/admin_edit_show_re_edit/0'|ezurl} title="{'Disable &quot;Back to edit&quot; checkbox when editing content.'|i18n( 'design/admin/parts/my/menu' )}">{'off'|i18n( 'design/admin/parts/my/menu' )}</a>
    {else}
        <a href={'/user/preferences/set/admin_edit_show_re_edit/1'|ezurl} title="{'Enable &quot;Back to edit&quot; checkbox when editing content.'|i18n( 'design/admin/parts/my/menu' )}">{'on'|i18n( 'design/admin/parts/my/menu' )}</a><span class="current" aria-current="true">{'off'|i18n( 'design/admin/parts/my/menu' )}</span>
    {/if}
    </span>
    </li>
</ul>
</div>

{* DESIGN: Content END *}</div></div></div>

</div>
{/if}