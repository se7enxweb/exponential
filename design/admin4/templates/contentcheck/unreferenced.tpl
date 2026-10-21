{* The contentcheck/unreferenced view (doc/eng-us/features/6.0/content-checks.md, "Unreferenced objects").

   Variables: result (node, count, rows, error), params (node_id, class_ids, sort, order, containers, offset,
   limit), scope (the start location, see scope.tpl), class_options, sorts, page_uri, page_uri_suffix, view_parameters, limit, limit_choices,
   limit_preference, feedback (the outcome of a move to the trash, or null), select_all ((selectall)/1: every box of the page ticked, the
   way without JavaScript; contentcheck_select.js adds the master box), can_trash, batch_limit, form_token,
   token_field. Only what the user may read is in result.rows; a row can be ticked only when the user may move it
   to the trash. The filters are a posted form, answered with a 303 to the view parameters; the ticked rows are posted to contentcheck/trash, which asks for a
   confirmation first. The same file is in design/admin and design/admin4. *}
{def $sort_names = hash( 'name', 'Name'|i18n( 'kernel/contentcheck' ),
                         'class', 'Class'|i18n( 'kernel/contentcheck' ),
                         'modified', 'Last modified'|i18n( 'kernel/contentcheck' ),
                         'published', 'Published'|i18n( 'kernel/contentcheck' ),
                         'id', 'Object id'|i18n( 'kernel/contentcheck' ) )
     $node = $result.node
     $rows = $result.rows
     $tickable = false()
     $tickable_count = 0}
{foreach $rows as $row}{if $row.can_trash}{set $tickable = true()}{set $tickable_count = $tickable_count|inc}{/if}{/foreach}

<div class="context-block a4-page a4-contentcheck">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Unreferenced objects (%count)'|i18n( 'kernel/contentcheck',, hash( '%count', $result.count ) )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="a4-intro">{'Images, files and media below “%node” that nothing uses. An asset counts as used when another object relates to it, links to it or embeds it (by id or by path), when a layout block, collection or rule, a setting, a template, a role, a bookmark, a notification, the shop or the newsletter names it, when a dynamic collection lists it, or when it is stored below content that shows it (a gallery, an article). Only assets in plain folders can be listed. Pages, folders, users and other content are reached through the tree and are never listed. Templates that compute an id cannot be checked: see the documentation.'|i18n( 'kernel/contentcheck',, hash( '%node', $node.name ) )|wash}</p>

{if $feedback}
    {if $feedback.done}
<div class="a4-notice is-ok" role="status">{'%count objects were moved to the trash. They can be restored from the trash.'|i18n( 'kernel/contentcheck',, hash( '%count', $feedback.moved ) )}{if $feedback.failed|gt( 0 )} {'%count could not be moved.'|i18n( 'kernel/contentcheck',, hash( '%count', $feedback.failed ) )}{/if}</div>
    {elseif eq( $feedback.reason, 'changed' )}
<div class="a4-notice is-warn" role="alert">{'Nothing was moved: the objects or your access changed after the confirmation. Tick them again.'|i18n( 'kernel/contentcheck' )}</div>
    {else}
<div class="a4-notice is-warn" role="alert">{'Nothing was moved to the trash.'|i18n( 'kernel/contentcheck' )}</div>
    {/if}
{/if}

{if eq( $result.error, 'database' )}
<div class="a4-notice is-warn" role="alert">{'This check is not available on this database.'|i18n( 'kernel/contentcheck' )}</div>
{elseif eq( $result.error, 'no_index' )}
<div class="a4-notice is-warn" role="alert">{'The reference index has not been built yet. Run bin/php/contentcheck.php --build-index or the cronjob part contentcheck.'|i18n( 'kernel/contentcheck' )}</div>
{elseif eq( $result.error, 'incomplete' )}
<div class="a4-notice is-warn" role="alert">{'Some places that can use content could not be read, so nothing is listed. The debug output names them.'|i18n( 'kernel/contentcheck' )}</div>
{elseif eq( $result.error, 'too_many' )}
<div class="a4-notice is-warn" role="alert">{'There are too many assets below this node to check at once. Choose a smaller subtree or fewer classes.'|i18n( 'kernel/contentcheck' )}</div>
{/if}
{if is_set( $result.index.stale )}{if $result.index.stale}
<div class="a4-notice is-warn" role="status">{'The reference index is from %time and may be out of date. Nothing can be moved to the trash until it is built again.'|i18n( 'kernel/contentcheck',, hash( '%time', $result.index.built|l10n( 'shortdatetime' ) ) )}</div>
{/if}{/if}
{if $result.without_location|gt( 0 )}
<p class="a4-meta">{'%count published objects have no location at all. They are not listed here; nothing can reach them.'|i18n( 'kernel/contentcheck',, hash( '%count', $result.without_location ) )}</p>
{/if}

<section aria-labelledby="cc-filter-title">
<h2 class="a4-sr-only" id="cc-filter-title">{'Filter'|i18n( 'kernel/contentcheck' )}</h2>
<form class="a4-filterbar" method="post" action={$page_uri|ezurl}>
    {include uri='design:contentcheck/scope.tpl' scope=$scope}
    <div class="a4-field">
        <label for="cc-class">{'Classes'|i18n( 'kernel/contentcheck' )}</label>
        <select id="cc-class" name="ClassID[]" multiple="multiple" size="6" aria-describedby="cc-class-help">
        {foreach $class_options as $option}
            <option value="{$option.id}"{if $params.class_ids|contains( $option.id )} selected="selected"{/if}>{$option.name|wash}</option>
        {/foreach}
        </select>
        <span class="a4-help" id="cc-class-help">{'None chosen: every asset class. Hold Ctrl or Cmd to choose several, for example Image and File.'|i18n( 'kernel/contentcheck' )}</span>
    </div>
    <div class="a4-field">
        <label for="cc-sort">{'Order by'|i18n( 'kernel/contentcheck' )}</label>
        <select id="cc-sort" name="Sort">
        {foreach $sorts as $sort}
            <option value="{$sort|wash}"{if eq( $sort, $params.sort )} selected="selected"{/if}>{$sort_names[$sort]|wash}</option>
        {/foreach}
        </select>
    </div>
    <div class="a4-field">
        <label for="cc-order">{'Direction'|i18n( 'kernel/contentcheck' )}</label>
        <select id="cc-order" name="Order">
            <option value="asc"{if eq( $params.order, 'asc' )} selected="selected"{/if}>{'Ascending'|i18n( 'kernel/contentcheck' )}</option>
            <option value="desc"{if eq( $params.order, 'desc' )} selected="selected"{/if}>{'Descending'|i18n( 'kernel/contentcheck' )}</option>
        </select>
    </div>
    <div class="a4-field">
        <label class="a4-check" for="cc-containers"><input type="checkbox" id="cc-containers" name="Containers" value="1"{if $params.containers} checked="checked"{/if} /> {'Also objects with sub-items'|i18n( 'kernel/contentcheck' )}</label>
    </div>
    <div class="a4-field">
        <button type="submit" class="a4-btn a4-btn-primary">{'Show'|i18n( 'kernel/contentcheck' )}</button>
    </div>
    {include uri='design:contentcheck/filter_choice.tpl' group='Class' label='Classes'|i18n( 'kernel/contentcheck' )}
</form>
</section>

<form name="contentcheckunreferenced" method="post" action={'/contentcheck/trash'|ezurl}>
<input type="hidden" name="{$token_field|wash}" value="{$form_token|wash}" />
<input type="hidden" name="NodeID" value="{$params.node_id}" />

<section class="a4-section" aria-labelledby="cc-list-title">
{include uri='design:contentcheck/results_head.tpl' title='Objects'|i18n( 'kernel/contentcheck' ) title_id='cc-list-title'
         offset=$params.offset limit=$limit count=$result.count}

{if $rows|count|eq( 0 )}
<p class="a4-empty">{'No unused asset below this node matches the filter.'|i18n( 'kernel/contentcheck' )}</p>
{else}
<div class="a4-table-wrap">
<table class="a4-table" data-a4-select="1">
<thead>
<tr>
    {if and( $can_trash, $tickable )}<th scope="col" class="tight">{include uri='design:contentcheck/select_all.tpl' select_all=$select_all}</th>{/if}
    <th scope="col">{'Name'|i18n( 'kernel/contentcheck' )}</th>
    <th scope="col">{'Class'|i18n( 'kernel/contentcheck' )}</th>
    <th scope="col" class="a4-num">{'Size'|i18n( 'kernel/contentcheck' )}</th>
    <th scope="col">{'Last modified'|i18n( 'kernel/contentcheck' )}</th>
    <th scope="col">{'Location'|i18n( 'kernel/contentcheck' )}</th>
</tr>
</thead>
<tbody>
{foreach $rows as $row}
<tr>
    {if and( $can_trash, $tickable )}<td>{if $row.can_trash}<input type="checkbox" name="ObjectIDList[]" value="{$row.object_id}" aria-label="{$row.name|wash}" data-a4-select-item="1"{if $select_all} checked="checked"{/if} />{/if}</td>{/if}
    <td><a href={concat( '/content/view/full/', $row.node_id )|ezurl}>{$row.name|wash}</a>{if $row.hidden} <span class="a4-tag is-warn">{'Hidden'|i18n( 'kernel/contentcheck' )}</span>{/if}
        <div class="a4-meta">{'Object %id'|i18n( 'kernel/contentcheck',, hash( '%id', $row.object_id ) )}{if $row.children|gt( 0 )} &middot; {'%count sub-items'|i18n( 'kernel/contentcheck',, hash( '%count', $row.children ) )}{/if}</div></td>
    <td>{$row.class_name|wash}</td>
    <td class="a4-num">{if $row.files|gt( 0 )}{$row.size|si( byte )}{else}&ndash;{/if}</td>
    <td>{$row.modified|l10n( 'shortdatetime' )}</td>
    <td>{$row.path|wash}{if $row.locations|gt( 1 )} <span class="a4-meta">{'(%count locations)'|i18n( 'kernel/contentcheck',, hash( '%count', $row.locations ) )}</span>{/if}</td>
</tr>
{/foreach}
</tbody>
</table>
</div>
{/if}

{if and( $can_trash, $tickable )}
{set-block variable=cc_actions}
        <button type="submit" class="a4-btn a4-btn-outline-danger" name="TrashButton" value="1" aria-describedby="cc-trash-help">{'Move ticked objects to the trash'|i18n( 'kernel/contentcheck' )}</button>
        {if $result.count|gt( $rows|count )}
        {foreach $params.class_ids as $class_id}<input type="hidden" name="ClassID[]" value="{$class_id}" />{/foreach}
        {if $params.containers}<input type="hidden" name="Containers" value="1" />{/if}
        <button type="submit" class="a4-btn a4-btn-outline-danger" name="AllResultsButton" value="1" aria-describedby="cc-trash-help">{'Move all %count results to the trash'|i18n( 'kernel/contentcheck',, hash( '%count', $result.count ) )}</button>
        {/if}
{/set-block}
{include uri='design:contentcheck/bulkbar.tpl' select_all=$select_all total=$tickable_count
         page_url=concat( $page_uri, '/(offset)/', $params.offset, $page_uri_suffix ) actions=$cc_actions help_id='cc-trash-help'
         help='The next page shows what will happen and asks you to confirm. At most %count objects at a time; they can be restored from the trash.'|i18n( 'kernel/contentcheck',, hash( '%count', $batch_limit ) )}
{/if}

{include uri='design:contentcheck/pager.tpl' page_uri=$page_uri page_uri_suffix=$page_uri_suffix item_count=$result.count
         view_parameters=$view_parameters limit=$limit limit_choices=$limit_choices limit_preference=$limit_preference}
</section>
</form>

</div></div></div>
</div>
{undef $sort_names $node $rows $tickable $tickable_count}
