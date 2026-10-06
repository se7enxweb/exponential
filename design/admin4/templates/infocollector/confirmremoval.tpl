{* The confirmation before collected information is removed: all collections of the ticked objects
   (infocollector/overview, remove_type 'objects') or the ticked collections of one object
   (infocollector/collectionlist, remove_type 'collections'). Says what goes and what stays, then posts
   ConfirmRemoveButton; Cancel posts CancelButton, which removes nothing. Field and button names are unchanged.

   The same file is in design/admin and design/admin4. Guide: doc/guides/collected-information.md *}
{include uri='design:infocollector/exp_style.tpl'}

{def $objects = first_set( $remove_objects, array() )}

<div class="context-block exp-info">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Confirm information collection removal'|i18n( 'design/admin/infocollector/confirmremoval' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

{if $remove_type|eq( 'objects' )}
<form action={$module.functions.overview.uri|ezurl} method="post" name="CollectionRemove">
{else}
<form action={concat( $module.functions.collectionlist.uri, '/', $object_id )|ezurl} method="post" name="CollectionRemove">
{/if}

<div class="exp-feedback is-warn" role="alert">
    <h2 class="exp-h2">{'Are you sure you want to remove the collected information?'|i18n( 'design/admin/infocollector/confirmremoval' )}</h2>
    {if $collections|lt( 2 )}
    <p>{'%collections collection will be removed.'|i18n( 'design/admin/infocollector/confirmremoval',, hash( '%collections', $collections ) )}</p>
    {else}
    <p>{'%collections collections will be removed.'|i18n( 'design/admin/infocollector/confirmremoval',, hash( '%collections', $collections ) )}</p>
    {/if}
    {if $objects}
    <ul>
    {foreach $objects as $o}
        <li>{$o.name|wash} &ndash; {if $o.collections|eq( 1 )}{'1 collection'|i18n( 'design/admin/infocollector/overview' )}{else}{'%count collections'|i18n( 'design/admin/infocollector/overview',, hash( '%count', $o.collections ) )}{/if}</li>
    {/foreach}
    </ul>
    {/if}
    <p>{'What the visitors sent is deleted and cannot be brought back; export it as CSV first if it is still needed. The objects and their forms stay and go on collecting.'|i18n( 'design/admin/infocollector/confirmremoval' )}</p>
</div>

<div class="exp-bottombar">
    <div class="exp-actions">
        <button class="exp-btn exp-btn-danger" type="submit" name="ConfirmRemoveButton" value="{'OK'|i18n( 'design/admin/infocollector/confirmremoval' )}">{'Remove'|i18n( 'design/admin/infocollector/confirmremoval' )}</button>
        <button class="exp-btn" type="submit" name="CancelButton" value="{'Cancel'|i18n( 'design/admin/infocollector/confirmremoval' )}">{'Cancel'|i18n( 'design/admin/infocollector/confirmremoval' )}</button>
    </div>
</div>

</form>

</div></div></div>
</div>
{undef $objects}
