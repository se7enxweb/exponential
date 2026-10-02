<style type="text/css">
{literal}
table.list td { max-width: 500px; word-wrap: break-word; }
table.list td.width-280 { max-width: 280px; }
.mt-20 { margin-top: 20px; }
.content-trash .trash-summary { margin: 0 0 .6em 0; }
.content-trash .trash-filter { display: flex; flex-wrap: wrap; gap: .4em 1em; align-items: flex-end; margin: 0 0 .8em 0; }
.content-trash .trash-filter label { display: block; font-weight: bold; font-size: .9em; }
.content-trash .trash-filter select, .content-trash .trash-filter input.trash-date { max-width: 14em; }
.content-trash .trash-meta { font-size: .85em; opacity: .75; margin-top: .2em; }
.content-trash .trash-state { font-size: .85em; margin-top: .2em; }
.content-trash .trash-state-exists { color: #2d6a2d; }
.content-trash .trash-state-trash, .content-trash .trash-state-moved, .content-trash .trash-state-gone { color: #8a4b00; }
.content-trash .trash-path-trash, .content-trash .trash-path-gone { font-style: italic; }
.content-trash details.trash-details { font-size: .85em; margin-top: .2em; }
.content-trash details.trash-details summary { cursor: pointer; }
.content-trash td.trash-name { min-width: 14em; }
.content-trash details.trash-details dl { display: grid; grid-template-columns: auto 1fr; gap: .1em .6em; margin: .3em 0 0 0; }
.content-trash details.trash-details dt { font-weight: bold; margin: 0; }
.content-trash details.trash-details dd { margin: 0; }
.content-trash a.trash-restore { display: inline-block; padding: 2px 8px; border: 1px solid #c9ced6; border-radius: 4px; background-color: #fff; text-decoration: none; white-space: nowrap; }
.content-trash a.trash-restore:hover { border-color: #9aa1ad; }
{/literal}
</style>
{let item_type = ezpreference( 'admin_list_limit' )
     number_of_items = min( $item_type, 3)|choose( 10, 10, 25, 50 )
     trash_sort_field = first_set(  $view_parameters.sort_field, 'trashed' )
     trash_sort_order = first_set(  $view_parameters.sort_order, '0' ) }

{def
    $sort_method = 1
    $sort_order  = ''
    $col_class   = ''
    $link_order  = 1
    $filter_uri  = ''
    $items       = array()
    $list_count  = 0
    $details     = false()
    $sort_columns = array( hash( 'field', 'name',       'label', 'Name'|i18n( 'design/admin/content/trash' ) ),
                           hash( 'field', 'class_name', 'label', 'Type'|i18n( 'design/admin/content/trash' ) ),
                           hash( 'field', 'section',    'label', 'Section'|i18n( 'design/admin/content/trash' ) ) )
}

{* The view describes each item (who trashed it, where it was, ...). A view from before that only gives the
   view parameters: then the list is fetched here, as it always was, without the details. *}
{if is_set( $trash_items )}
    {set $items      = $trash_items
         $list_count = $trash_count
         $filter_uri = $trash_filter_uri
         $details    = true()}
{else}
    {set $list_count = fetch( 'content', 'trash_count', hash( 'objectname_filter', $view_parameters.namefilter ) )}
    {foreach fetch( 'content', 'trash_object_list', hash( 'limit',  $number_of_items,
                                                          'offset', $view_parameters.offset,
                                                          'sort_by', array( $trash_sort_field, $trash_sort_order ),
                                                          'objectname_filter', $view_parameters.namefilter ) ) as $trash_node}
        {set $items = $items|append( hash( 'node', $trash_node, 'object', $trash_node.object, 'object_id', $trash_node.contentobject_id ) )}
    {/foreach}
{/if}

{if eq($trash_sort_order, '0')}
    {set $sort_order = 'Descending'|i18n( 'design/admin/node/view/full' )}
{elseif eq($trash_sort_order, '1')}
    {set $sort_order = 'Ascending'|i18n( 'design/admin/node/view/full' )}
{/if}

<form name="trashform" action={concat( 'content/trash', $filter_uri, '/(sort_field)/', $trash_sort_field, '/(sort_order)/', $trash_sort_order )|ezurl} method="post" >

<div class="context-block content-trash">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">

<h1 class="context-title">{'Trash (%list_count)'|i18n( 'design/admin/content/trash',, hash( '%list_count', $list_count ) )}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{if $details}
{* Summary of the whole trash, then the filters *}
<div class="context-toolbar">
<div class="block">
{if $trash_summary.items}
    <p class="trash-summary">{'%items items in the trash: %top removed directly, %below below them. Oldest: %oldest.'|i18n( 'design/admin/content/trash',, hash( '%items', $trash_summary.items, '%top', $trash_summary.top, '%below', $trash_summary.below, '%oldest', $trash_summary.oldest|l10n( 'shortdatetime' ) ) )}
    {if lt( $trash_summary.recorded, $trash_summary.items )}{'Who trashed an item is recorded from this version on: known for %recorded.'|i18n( 'design/admin/content/trash',, hash( '%recorded', $trash_summary.recorded ) )}{/if}</p>
{/if}
{if or( $trash_summary.items, $trash_filtered )}
    <div class="trash-filter">
        <div>
            <label for="trash-filter-by">{'Trashed by'|i18n( 'design/admin/content/trash' )}</label>
            <select id="trash-filter-by" name="FilterTrashedBy">
                <option value="">{'Anyone'|i18n( 'design/admin/content/trash' )}</option>
                {foreach $trash_user_options as $option}
                <option value="{$option.id}"{if eq( $trash_filters.trashed_by, $option.id )} selected="selected"{/if}>{$option.name|wash} ({$option.count})</option>
                {/foreach}
                <option value="unknown"{if eq( $trash_filters.trashed_by, 'unknown' )} selected="selected"{/if}>{'Unknown'|i18n( 'design/admin/content/trash' )}</option>
            </select>
        </div>
        <div>
            <label for="trash-filter-class">{'Type'|i18n( 'design/admin/content/trash' )}</label>
            <select id="trash-filter-class" name="FilterClassID">
                <option value="">{'Any'|i18n( 'design/admin/content/trash' )}</option>
                {foreach $trash_class_options as $option}
                <option value="{$option.id}"{if eq( $trash_filters.class, $option.id )} selected="selected"{/if}>{$option.name|wash}</option>
                {/foreach}
            </select>
        </div>
        <div>
            <label for="trash-filter-from">{'Trashed from'|i18n( 'design/admin/content/trash' )}</label>
            <input class="trash-date" id="trash-filter-from" type="date" name="FilterTrashedFrom" value="{$trash_filters.from|wash}" />
        </div>
        <div>
            <label for="trash-filter-to">{'to'|i18n( 'design/admin/content/trash' )}</label>
            <input class="trash-date" id="trash-filter-to" type="date" name="FilterTrashedTo" value="{$trash_filters.to|wash}" />
        </div>
        <div>
            <input class="button" type="submit" name="FilterButton" value="{'Filter'|i18n( 'design/admin/content/trash' )}" title="{'Show only the items that match.'|i18n( 'design/admin/content/trash' )}" />
            {if $trash_filtered}<input class="button" type="submit" name="ResetFilterButton" value="{'Show all'|i18n( 'design/admin/content/trash' )}" />{/if}
        </div>
    </div>
{/if}
</div>
</div>
{/if}

{if $list_count}
{* Items per page selector. *}
<div class="context-toolbar">
<div class="button-left">
    <p class="table-preferences">
    {switch match=$number_of_items}
    {case match=25}
        <a href={'/user/preferences/set/admin_list_limit/1'|ezurl}>10</a>
        <span class="current">25</span>
        <a href={'/user/preferences/set/admin_list_limit/3'|ezurl}>50</a>

        {/case}

        {case match=50}
        <a href={'/user/preferences/set/admin_list_limit/1'|ezurl}>10</a>
        <a href={'/user/preferences/set/admin_list_limit/2'|ezurl}>25</a>
        <span class="current">50</span>
        {/case}

        {case}
        <span class="current">10</span>
        <a href={'/user/preferences/set/admin_list_limit/2'|ezurl}>25</a>
        <a href={'/user/preferences/set/admin_list_limit/3'|ezurl}>50</a>
        {/case}

        {/switch}
    </p>
</div>
<div class="float-break"></div>
</div>

<div class="content-navigation-childlist admin-dt">
    <table class="list" cellspacing="0">
    <tr>
        <th class="tight"><img src={'toggle-button-16x16.gif'|ezimage} width="16" height="16" alt="{'Invert selection.'|i18n( 'design/admin/content/trash' )}" onclick="ezjs_toggleCheckboxes( document.trashform, 'DeleteIDArray[]' ); return false;" title="{'Invert selection.'|i18n( 'design/admin/content/trash' )}" /></th>
        {* Name, Type and Section: sort link & asc/desc icon *}
        {foreach $sort_columns as $column}
        {set
            $col_class  = ''
            $link_order = 1
        }
        {if eq( $column.field, $trash_sort_field )}
            {if eq($trash_sort_order, '0')}
                {set $col_class = ' admin-dt-desc'}
            {elseif eq($trash_sort_order, '1')}
                {set
                    $col_class  = ' admin-dt-asc'
                    $link_order = 0
                }
            {/if}
        {/if}
        <th class="admin-dt-col-name admin-dt-sortable{$col_class}">
            <div class="admin-dt-liner">
                <span class="admin-dt-label">
                    <a href="{concat( 'content/trash', $filter_uri, '/(sort_field)/', $column.field, '/(sort_order)/', $link_order )|ezurl( 'no' )}" title="{'Click to sort %sort_order'|i18n( 'design/admin/content/trash',, hash( '%sort_order', $sort_order ) )}" class="admin-dt-sortable">{$column.label|wash}</a>
                </span>
            </div>
        </th>
        {/foreach}
        <th class="admin-dt-col-name admin-dt-sortable">{'Original Placement'|i18n( 'design/admin/content/trash')}</th>
        {* set Trashed column link & asc/desc icon *}
        {set
            $col_class  = ''
            $link_order = 1
        }
        {if eq( 'trashed', $trash_sort_field )}
            {if eq($trash_sort_order, '0')}
                {set $col_class = ' admin-dt-desc'}
            {elseif eq($trash_sort_order, '1')}
                {set
                    $col_class  = ' admin-dt-asc'
                    $link_order = 0
                }
            {/if}
        {/if}
        <th class="admin-dt-col-name admin-dt-sortable{$col_class}">
            <div class="admin-dt-liner">
                <span class="admin-dt-label">
                    <a href="{concat( 'content/trash', $filter_uri, '/(sort_field)/trashed/(sort_order)/', $link_order )|ezurl( 'no' )}" title="{'Click to sort %sort_order'|i18n( 'design/admin/content/trash',, hash( '%sort_order', $sort_order ) )}" class="admin-dt-sortable">{'Date trashed'|i18n( 'design/admin/content/trash')}</a>
                </span>
            </div>
        </th>
        <th class="tight admin-dt-col-name">&nbsp;</th>
    </tr>

    {foreach $items as $item sequence array( bglight, bgdark ) as $row_class}
    {def $trash_node = $item.node
         $cur_c_object = $item.object}

    <tr class="{$row_class}">
        <td>
        <input type="checkbox" name="DeleteIDArray[]" value="{$item.object_id}" title="{'Use these checkboxes to mark items for removal. Click the "Remove selected" button to remove the selected items.'|i18n( 'design/admin/content/trash' )|wash()}" />
        </td>
        <td class="trash-name">
        {$trash_node.class_identifier|class_icon( small, $trash_node.class_name|wash )}&nbsp;<a href={concat( '/content/versionview/', $item.object_id, '/', $cur_c_object.current_version, '/' )|ezurl}>{$trash_node.name|wash}</a>
        {if $details}
        <div class="trash-meta">{'ID %id'|i18n( 'design/admin/content/trash',, hash( '%id', $item.object_id ) )}{if $item.subtree_count} &middot; {'%count below it'|i18n( 'design/admin/content/trash',, hash( '%count', $item.subtree_count ) )}{/if}{if $item.languages} &middot; {foreach $item.languages as $language}<span title="{$language.name|wash}">{$language.locale|wash}</span>{delimiter}, {/delimiter}{/foreach}{/if}</div>
        {if $item.other_locations}
        <div class="trash-state trash-state-exists">{'Still in the tree at'|i18n( 'design/admin/content/trash' )}: {foreach $item.other_locations as $location}<a href={$location.url|ezurl}>{$location.name|wash}</a>{delimiter}, {/delimiter}{/foreach}</div>
        {/if}
        <details class="trash-details">
            <summary>{'Details'|i18n( 'design/admin/content/trash' )}</summary>
            <dl>
                <dt>{'Owner'|i18n( 'design/admin/content/trash' )}</dt>
                <dd>{if $item.owner_name}<a href={concat( '/content/view/full/', fetch( 'content', 'object', hash( 'object_id', $item.owner_id ) ).main_node_id )|ezurl}>{$item.owner_name|wash}</a>{else}{'Unknown'|i18n( 'design/admin/content/trash' )}{/if}</dd>
                <dt>{'Last modified by'|i18n( 'design/admin/content/trash' )}</dt>
                <dd>{if $item.modifier_name}{$item.modifier_name|wash}{else}{'Unknown'|i18n( 'design/admin/content/trash' )}{/if}</dd>
                <dt>{'Published'|i18n( 'design/admin/content/trash' )}</dt>
                <dd>{if $item.published}{$item.published|l10n( 'shortdatetime' )}{else}{'Never'|i18n( 'design/admin/content/trash' )}{/if}</dd>
                <dt>{'Modified'|i18n( 'design/admin/content/trash' )}</dt>
                <dd>{if $item.modified}{$item.modified|l10n( 'shortdatetime' )}{else}-{/if}</dd>
                <dt>{'Languages'|i18n( 'design/admin/content/trash' )}</dt>
                <dd>{foreach $item.languages as $language}{$language.name|wash}{delimiter}, {/delimiter}{/foreach}</dd>
                <dt>{'Other locations'|i18n( 'design/admin/content/trash' )}</dt>
                <dd>{if $item.other_locations}{foreach $item.other_locations as $location}<a href={$location.url|ezurl}>/{$location.path|wash}</a>{delimiter}, {/delimiter}{/foreach}{else}{'None'|i18n( 'design/admin/content/trash' )}{/if}</dd>
            </dl>
        </details>
        {/if}
        </td>
        <td>
        {$trash_node.class_name|wash}
        </td>
        <td>
        {if $details}
            {if $item.section_name}{$item.section_name|wash}{else}<i>{'Unknown'|i18n( 'design/admin/content/trash' )}</i>{/if}
        {else}
            {let section_object=fetch( section, object, hash( section_id, $cur_c_object.section_id ) )}{section show=$section_object}{$section_object.name|wash}{section-else}<i>{'Unknown'|i18n( 'design/admin/content/trash' )}</i>{/section}{/let}
        {/if}
        </td>
        <td class="width-280">
        {if $details}
            {if $item.path}{foreach $item.path as $step}{if $step.url}<a href={$step.url|ezurl}>{$step.name|wash}</a>{else}<span class="trash-path-{$step.state}">{$step.name|wash}</span>{/if}{delimiter} / {/delimiter}{/foreach}{else}/{/if}
            {switch match=$item.parent_state}
            {case match='exists'}<div class="trash-state trash-state-exists">{'Parent exists: restores to its original place.'|i18n( 'design/admin/content/trash' )}</div>{/case}
            {case match='trash'}<div class="trash-state trash-state-trash">{'Parent is in the trash too: restore it first.'|i18n( 'design/admin/content/trash' )}</div>{/case}
            {case match='moved'}<div class="trash-state trash-state-moved">{'Parent has moved: choose a place when restoring.'|i18n( 'design/admin/content/trash' )}</div>{/case}
            {case}<div class="trash-state trash-state-gone">{'Parent no longer exists: choose a place when restoring.'|i18n( 'design/admin/content/trash' )}</div>{/case}
            {/switch}
        {else}
            {def $original_parent = $trash_node.original_parent}
            {if $original_parent}<a href={concat( '/', $original_parent.path_identification_string )|ezurl}>{/if}/{$trash_node.original_parent_path_id_string|wash}{if $original_parent}</a>{/if}
            {undef $original_parent}
        {/if}
        </td>
        <td>
        {$trash_node.trashed|l10n( 'shortdatetime' )}
        {if $details}
            {if $item.trashed_by}
            <div class="trash-meta">{'by %name'|i18n( 'design/admin/content/trash',, hash( '%name', $item.trashed_by.name|wash ) )}{if $item.trashed_by.via} <span title="{'Where the removal came from'|i18n( 'design/admin/content/trash' )}">({$item.trashed_by.via|wash})</span>{/if}</div>
            {else}
            <div class="trash-meta">{'by unknown'|i18n( 'design/admin/content/trash' )}{if $item.modifier_name}<br />{'last modified by %name'|i18n( 'design/admin/content/trash',, hash( '%name', $item.modifier_name|wash ) )}{/if}</div>
            {/if}
        {/if}
        </td>
        <td>
        <a class="trash-restore" href={concat( '/content/restore/', $item.object_id, '/' )|ezurl} title="{'Restore this item: to its original place, or choose another one.'|i18n( 'design/admin/content/trash' )}">{'Restore'|i18n( 'design/admin/content/trash' )}</a>
        </td>
    </tr>

    {undef $trash_node $cur_c_object}
    {/foreach}
    </table>
</div>

{else}

<div class="block">
    {if and( $details, $trash_filtered )}
    <p>{'No item in the trash matches the filter.'|i18n( 'design/admin/content/trash' )}</p>
    {else}
    <p>{'There are no items in the trash'|i18n( 'design/admin/content/trash' )}.</p>
    {/if}
</div>

{/if}

<div class="context-toolbar">
{include name=navigator
         uri='design:navigator/alphabetical.tpl'
         page_uri='/content/trash'
         item_count=$list_count
         view_parameters=$view_parameters
         item_limit=$number_of_items
         show_google_navigator=true()}
</div>


{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<div class="button-left mt-20">
    {if $list_count}
        <input class="button" type="submit" name="RemoveButton" value="{'Remove selected'|i18n( 'design/admin/content/trash' )}"  title="{'Permanently remove the selected items.'|i18n( 'design/admin/content/trash' )}" />
        <input class="button" type="submit" name="EmptyButton"  value="{'Empty trash'|i18n( 'design/admin/content/trash' )}" title="{'Permanently remove all items from the trash.'|i18n( 'design/admin/content/trash' )}" />
    {else}
        <input class="button-disabled" type="submit" name="RemoveButton" value="{'Remove selected'|i18n( 'design/admin/content/trash' )}" disabled="disabled" />
        <input class="button-disabled" type="submit" name="EmptyButton"  value="{'Empty trash'|i18n( 'design/admin/content/trash' )}" disabled="disabled" />
    {/if}
</div>

</div>

<div class="float-break"></div>
{* DESIGN: Control bar END *}</div></div>
</div>
</div>
</form>
{undef}
{/let}
