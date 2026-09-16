<form name="objects" method="post" action={'/infocollector/overview/'|ezurl}>

{let number_of_items=min( ezpreference( 'admin_infocollector_list_limit' ), 3)|choose( 10, 10, 25, 50 )}

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">

<h1 class="context-title">{'Objects that have collected information (%object_count)'|i18n( 'design/admin/infocollector/overview',, hash( '%object_count', $object_count ) )}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{section show=$object_array}
{* Items per page selector. *}
<div class="context-toolbar">
<div class="button-left">
<p class="table-preferences">
{* The sizes come from admininterface.ini [PaginationSettings]; the preference
   stores the position in that list. *}
{foreach $limit_choices as $limit_index => $limit_option}
    {if eq( $limit_index|inc, $limit_choice )}
        <span class="current">{$limit_option}</span>
    {else}
        <a href={concat( '/user/preferences/set/admin_infocollector_list_limit/', $limit_index|inc )|ezurl} title="{'Show %count items per page.'|i18n( 'design/admin/infocollector/overview',, hash( '%count', $limit_option ) )}">{$limit_option}</a>
    {/if}
{/foreach}
</p>
</div>
<div class="float-break"></div>
</div>

{* Object table. *}
<table class="list" cellspacing="0">
<tr>
    <th class="tight"><img src={'toggle-button-16x16.gif'|ezimage} width="16" height="16" alt="{'Invert selection.'|i18n( 'design/admin/infocollector/overview' )}" title="{'Invert selection.'|i18n( 'design/admin/infocollector/overview' )}" onclick="ezjs_toggleCheckboxes( document.objects, 'ObjectIDArray[]' ); return false;" /></th>
    <th>{'Name'|i18n( 'design/admin/infocollector/overview' )}</th>
    <th>{'Type'|i18n( 'design/admin/infocollector/overview' )}</th>
    <th>{'First collection'|i18n( 'design/admin/infocollector/overview' )}</th>
    <th>{'Last collection'|i18n( 'design/admin/infocollector/overview' )}</th>
    <th class="tight">{'Collections'|i18n( 'design/admin/infocollector/overview' )}</th>
</tr>
{section var=Objects loop=$object_array sequence=array( bglight, bgdark )}
<tr class="{$Objects.sequence}">
    <td><input type="checkbox" name="ObjectIDArray[]" value="{$Objects.item.contentobject_id}" title="{'Select collections for removal.'|i18n( 'design/admin/infocollector/overview' )}" /></td>
    <td>{$Objects.item.class_identifier|icon( 'small', 'section'|i18n( 'design/admin/infocollector/overview' ) )}&nbsp;<a href={concat( '/content/view/full/', $Objects.item.main_node_id )|ezurl}>{$Objects.item.name|wash}</a></td>
    <td>{$Objects.item.class_name|wash}</td>
    <td>{$Objects.item.first_collection|l10n( shortdatetime )}</td>
    <td>{$Objects.item.last_collection|l10n( shortdatetime )}</td>
    <td class="number" align="right"><a href={concat( '/infocollector/collectionlist/', $Objects.item.contentobject_id )|ezurl}>{$Objects.item.collections}</a></td>
</tr>
{/section}
</table>
{section-else}
<div class="block">
<p>{'There are no objects that have collected any information.'|i18n( 'design/admin/infocollector/overview' )}</p>
</div>

{/section}

{* Navigator. *}
<div class="context-toolbar">
{include name=navigator
         uri='design:navigator/google.tpl'
         page_uri='/infocollector/overview'
         item_count=$object_count
         view_parameters=$view_parameters
         item_limit=$limit}
</div>

{* DESIGN: Content END *}</div></div></div>

{* Buttons. *}
<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<div class="block">
{if $object_array}
<input class="button" type="submit" name="RemoveObjectCollectionButton" value="{'Remove selected'|i18n( 'design/admin/infocollector/overview' )}" title="{'Remove all information that was collected by the selected objects.'|i18n( 'design/admin/infocollector/overview' )}" />
{else}
<input class="button-disabled" type="submit" name="RemoveObjectCollectionButton" value="{'Remove selected'|i18n( 'design/admin/infocollector/overview' )}" disabled="disabled" />
{/if}
</div>
{* DESIGN: Control bar END *}</div></div>
</div>

</div>

{/let}

</form>
