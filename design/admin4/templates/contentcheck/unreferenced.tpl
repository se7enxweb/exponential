{* The contentcheck/unreferenced view (doc/features/6.0/content-checks.md, "Unreferenced objects").

   Variables: result (node, count, rows, error), params (node_id, class_ids, sort, order, containers, offset,
   limit), class_options, sorts, page_uri, page_uri_suffix, view_parameters, limit, limit_choices,
   limit_preference, feedback (the outcome of a move to the trash, or null), can_trash, batch_limit, form_token,
   token_field. Only what the user may read is in result.rows; a row can be ticked only when the user may move it
   to the trash. The filters are a GET form; the ticked rows are posted to contentcheck/trash, which asks for a
   confirmation first. The same file is in design/admin and design/admin4. *}
{def $sort_names = hash( 'name', 'Name'|i18n( 'kernel/contentcheck' ),
                         'class', 'Class'|i18n( 'kernel/contentcheck' ),
                         'modified', 'Last modified'|i18n( 'kernel/contentcheck' ),
                         'published', 'Published'|i18n( 'kernel/contentcheck' ),
                         'id', 'Object id'|i18n( 'kernel/contentcheck' ) )
     $node = $result.node
     $rows = $result.rows
     $tickable = false()}
{foreach $rows as $row}{if $row.can_trash}{set $tickable = true()}{/if}{/foreach}

<div class="context-block a4-page a4-contentcheck">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Unreferenced objects (%count)'|i18n( 'kernel/contentcheck',, hash( '%count', $result.count ) )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="a4-intro">{'Published objects below “%node” that no other object refers to: no object relation, no relation field and no link or embed in rich text, from a published version or a current draft. References from objects in the trash and from old versions do not count. An object is listed where its main location is.'|i18n( 'kernel/contentcheck',, hash( '%node', $node.name ) )|wash}</p>

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
{/if}

<section aria-labelledby="cc-filter-title">
<h2 class="a4-sr-only" id="cc-filter-title">{'Filter'|i18n( 'kernel/contentcheck' )}</h2>
<form class="a4-filterbar" method="get" action={$page_uri|ezurl}>
    <div class="a4-field">
        <label for="cc-class">{'Classes'|i18n( 'kernel/contentcheck' )}</label>
        <select id="cc-class" name="ClassID[]" multiple="multiple" size="6" aria-describedby="cc-class-help">
        {foreach $class_options as $option}
            <option value="{$option.id}"{if $params.class_ids|contains( $option.id )} selected="selected"{/if}>{$option.name|wash}</option>
        {/foreach}
        </select>
        <span class="a4-help" id="cc-class-help">{'None chosen: every class. Hold Ctrl or Cmd to choose several, for example Image and File.'|i18n( 'kernel/contentcheck' )}</span>
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
</form>
</section>

<form name="contentcheckunreferenced" method="post" action={'/contentcheck/trash'|ezurl}>
<input type="hidden" name="{$token_field|wash}" value="{$form_token|wash}" />
<input type="hidden" name="NodeID" value="{$params.node_id}" />

<section class="a4-section" aria-labelledby="cc-list-title">
<div class="a4-section-head">
    <h2 class="a4-h2" id="cc-list-title">{'Objects'|i18n( 'kernel/contentcheck' )}</h2>
    {if $result.count|gt( 0 )}
    <span class="a4-meta">{'%from to %to of %count'|i18n( 'kernel/contentcheck',, hash( '%from', sum( $params.offset, 1 ), '%to', min( sum( $params.offset, $limit ), $result.count ), '%count', $result.count ) )}</span>
    {/if}
</div>

{if $rows|count|eq( 0 )}
<p class="a4-empty">{'No unreferenced object below this node matches the filter.'|i18n( 'kernel/contentcheck' )}</p>
{else}
<div class="a4-table-wrap">
<table class="a4-table">
<thead>
<tr>
    {if and( $can_trash, $tickable )}<th scope="col" class="tight"><span class="a4-sr-only">{'Select'|i18n( 'kernel/contentcheck' )}</span></th>{/if}
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
    {if and( $can_trash, $tickable )}<td>{if $row.can_trash}<input type="checkbox" name="ObjectIDList[]" value="{$row.object_id}" aria-label="{$row.name|wash}" />{/if}</td>{/if}
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

<div class="a4-listfoot">
    <p class="a4-sizes">
        <span>{'Per page'|i18n( 'kernel/contentcheck' )}:</span>
    {foreach $limit_choices as $limit_index => $limit_option}
        {if eq( $limit_option, $limit )}
        <span class="current" aria-current="true">{$limit_option}</span>
        {else}
        <a href={concat( '/user/preferences/set/', $limit_preference, '/', $limit_index|inc )|ezurl} title="{'Show %count items per page.'|i18n( 'kernel/contentcheck',, hash( '%count', $limit_option ) )}">{$limit_option}</a>
        {/if}
    {/foreach}
    </p>
    <div class="a4-pager">
    {include name=navigator
             uri='design:navigator/google.tpl'
             page_uri=$page_uri
             page_uri_suffix=$page_uri_suffix
             item_count=$result.count
             view_parameters=$view_parameters
             item_limit=$limit}
    </div>
</div>
</section>

{if and( $can_trash, $tickable )}
<div class="a4-toolbar is-bottom">
    <div class="a4-actions">
        <button type="submit" class="a4-btn a4-btn-outline-danger" name="TrashButton" value="1" aria-describedby="cc-trash-help">{'Move ticked objects to the trash'|i18n( 'kernel/contentcheck' )}</button>
    </div>
    <p class="a4-meta" id="cc-trash-help">{'The next page shows what will happen and asks you to confirm. At most %count objects at a time; they can be restored from the trash.'|i18n( 'kernel/contentcheck',, hash( '%count', $batch_limit ) )}</p>
</div>
{/if}
</form>

</div></div></div>
</div>
{undef $sort_names $node $rows $tickable}
