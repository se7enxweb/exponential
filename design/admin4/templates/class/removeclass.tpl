{* The confirmation of Remove selected on a class group's class list (class/removeclass/<group id>).

   For each class: how many objects go with it (with their sub items), or why it cannot be removed; the classes
   that only left this group because they are also in another one. OK (ConfirmButton) removes, Cancel (CancelButton)
   goes back to the group; both post to the view as before. Every template variable is unchanged (DeleteResult,
   already_removed, can_remove, GroupID, module). The same file is in design/admin and design/admin4.
   Guide: doc/guides/class-groups.md *}
{include uri='design:class/exp_style.tpl'}
{def $objects = 0
     $blocked = 0}
{foreach $DeleteResult as $result}{set $objects = sum( $objects, $result.objectCount )}{if $result.is_removable|not}{set $blocked = inc( $blocked )}{/if}{/foreach}

<div class="context-block exp-lists exp-classgroups">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Confirm class removal'|i18n( 'design/admin/class/removeclass' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<form action={concat( $module.functions.removeclass.uri, '/', $GroupID )|ezurl} method="post" name="ClassRemove">
<section class="exp-confirm" aria-labelledby="class-remove-title">
    <h2 class="exp-h2" id="class-remove-title">{if $can_remove|not}{'You do not have permission to remove classes.'|i18n( 'design/admin/class/removeclass' )}{elseif $DeleteResult|count|gt( 1 )}{'Are you sure you want to remove the classes?'|i18n( 'design/admin/class/removeclass' )}{else}{'Are you sure you want to remove this class?'|i18n( 'design/admin/class/removeclass' )}{/if}</h2>
    {if $can_remove}
    <ul class="exp-consequences">
        <li>{'A removed class takes all its objects with it, and their sub items. This cannot be undone.'|i18n( 'design/admin/class/removeclass' )} {if $objects|gt( 0 )}<strong>{'%count objects in all.'|i18n( 'design/admin/class/removeclass',, hash( '%count', $objects ) )}</strong>{/if}</li>
        {if $blocked|gt( 0 )}<li>{'%count classes cannot be removed; the reasons are below. The others are removed.'|i18n( 'design/admin/class/removeclass',, hash( '%count', $blocked ) )}</li>{/if}
    </ul>
    {/if}

    {if $already_removed}
    <div class="exp-feedback is-info" style="margin-top: 12px;">
    {def $class_list = ''}{foreach $already_removed as $class}{set $class_list = concat( $class_list, $class.name|wash )}{delimiter}{set $class_list = concat( $class_list, ', ' )}{/delimiter}{/foreach}
    {if count( $already_removed )|eq( 1 )}{'The %1 class was already removed from the group but still exists in other groups.'|i18n( 'design/admin/class/removeclass',, array( $class_list ) )}{else}{'The %1 classes were already removed from the group but still exist in other groups.'|i18n( 'design/admin/class/removeclass',, array( $class_list ) )}{/if}
    {undef $class_list}
    </div>
    {/if}

    {if $DeleteResult|count}
    <ul class="exp-secs">
    {foreach $DeleteResult as $result}
        <li class="exp-sec">
            <div class="exp-sec-title">
                <h3>{$result.className|wash}</h3>
                <ul class="exp-badges">
                {if $result.is_removable|not}<li class="exp-badge is-warn">{'Cannot be removed'|i18n( 'design/admin/class/removeclass' )}</li>
                {elseif $result.objectCount|gt( 0 )}<li class="exp-badge is-bad">{'%count objects go with it'|i18n( 'design/admin/class/removeclass',, hash( '%count', $result.objectCount ) )}</li>
                {else}<li class="exp-badge is-ok">{'No objects'|i18n( 'design/admin/class/removeclass' )}</li>{/if}
                </ul>
            </div>
            {if $result.objectCount|gt( 0 )}
            <p class="exp-meta" style="margin-top: 6px;">{if $result.objectCount|eq( 1 )}{'Removing class <%1> will result in the removal of %2 object and all its sub items.'|i18n( 'design/admin/class/removeclass',, array( $result.className|wash, $result.objectCount ) )|wash}{else}{'Removing class <%1> will result in the removal of %2 objects and all their sub items.'|i18n( 'design/admin/class/removeclass',, array( $result.className|wash, $result.objectCount ) )|wash}{/if}</p>
            {/if}
            {if $result.is_removable|not}
            <div class="exp-warnings"><p>{$result.reason.text|wash}</p>
                <ul>{foreach $result.reason.list as $reason}<li>{$reason.text|wash}{if is_set( $reason.list )}<ul>{foreach $reason.list as $sub_reason}<li>{$sub_reason.text|wash}</li>{/foreach}</ul>{/if}</li>{/foreach}</ul>
            </div>
            {/if}
        </li>
    {/foreach}
    </ul>
    {/if}
</section>

<div class="exp-bottombar">
    <div class="exp-actions">
        <button class="exp-btn exp-btn-danger" type="submit" name="ConfirmButton" value="1"{if $can_remove|not} disabled="disabled"{/if}>{'OK'|i18n( 'design/admin/class/removeclass' )}</button>
        <button class="exp-btn exp-cancel-button" type="submit" name="CancelButton" value="1">{'Cancel'|i18n( 'design/admin/class/removeclass' )}</button>
    </div>
    <p class="exp-meta">{'Nothing has been removed yet.'|i18n( 'design/admin/class/removeclass' )}</p>
</div>
</form>

</div></div></div>
</div>
{undef $objects $blocked}
