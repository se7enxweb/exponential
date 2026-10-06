{* The confirmation of removing class groups (class/removegroup).

   For each group: the classes that are in no other group and are removed with it, each with its number of objects;
   classes that are also in another group stay there and are not listed. OK (ConfirmButton) removes, Cancel
   (CancelButton) goes back to the class groups; both post to the view as before. Every template variable is
   unchanged (groups_info, DeleteResult, module). Class names are escaped (they were printed as they were). The same
   file is in design/admin and design/admin4. Guide: doc/guides/class-groups.md *}
{include uri='design:class/exp_style.tpl'}
{def $objects = 0
     $classes = 0}
{foreach $groups_info as $info}{foreach $info.class_list as $c}{set $objects = sum( $objects, $c.object_count )
     $classes = inc( $classes )}{/foreach}{/foreach}

<div class="context-block exp-lists exp-classgroups">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Confirm class group removal'|i18n( 'design/admin/class/removegroup' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<form action={concat( $module.functions.removegroup.uri )|ezurl} method="post" name="GroupRemove">
<section class="exp-confirm" aria-labelledby="classgroup-remove-title">
    <h2 class="exp-h2" id="classgroup-remove-title">{if $groups_info|count|eq( 1 )}{'Are you sure you want to remove the class group?'|i18n( 'design/admin/class/removegroup' )}{else}{'Are you sure you want to remove the class groups?'|i18n( 'design/admin/class/removegroup' )}{/if}</h2>
    <ul class="exp-consequences">
        {if $classes|gt( 0 )}
        <li><strong>{'%classes classes that are in no other group are removed, and with them %objects objects and their sub items. This cannot be undone.'|i18n( 'design/admin/class/removegroup',, hash( '%classes', $classes, '%objects', $objects ) )}</strong></li>
        {else}
        <li>{'No class is removed: these groups hold no class that is in no other group.'|i18n( 'design/admin/class/removegroup' )}</li>
        {/if}
        <li>{'Classes that are also in another group stay there.'|i18n( 'design/admin/class/removegroup' )}</li>
    </ul>
    <ul class="exp-secs">
    {foreach $groups_info as $info}
        <li class="exp-sec">
            <div class="exp-sec-title"><h3>{$info.group_name|wash}</h3></div>
            {if $info.class_list|count}
            <p class="exp-meta" style="margin-top: 6px;">{'The following classes will be removed from the <%group_name> class group'|i18n( 'design/admin/class/removegroup',, hash( '%group_name', $info.group_name ) )|wash}:</p>
            <ul class="exp-plain" style="margin-top: 6px;">
            {foreach $info.class_list as $c}
                <li><strong>{$c.class_name|wash}</strong> <span class="exp-badge{if $c.object_count|gt( 0 )} is-bad{/if}">{'%objects objects will be removed'|i18n( 'design/admin/class/removegroup',, hash( '%objects', $c.object_count ) )|wash}</span></li>
            {/foreach}
            </ul>
            {else}
            <p class="exp-meta" style="margin-top: 6px;">{'Only the group is removed.'|i18n( 'design/admin/class/removegroup' )}</p>
            {/if}
        </li>
    {/foreach}
    </ul>
</section>

<div class="exp-bottombar">
    <div class="exp-actions">
        <button class="exp-btn exp-btn-danger" type="submit" name="ConfirmButton" value="1">{'OK'|i18n( 'design/admin/class/removegroup' )}</button>
        <button class="exp-btn" type="submit" name="CancelButton" value="1">{'Cancel'|i18n( 'design/admin/class/removegroup' )}</button>
    </div>
    <p class="exp-meta">{'Nothing has been removed yet.'|i18n( 'design/admin/class/removegroup' )}</p>
</div>
</form>

</div></div></div>
</div>
{undef $objects $classes}
