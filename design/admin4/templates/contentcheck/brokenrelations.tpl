{* The contentcheck/brokenrelations view (doc/features/6.0/content-checks.md, "Broken relations"). Read only.

   Variables: result (node, excluded, findings, checked, last, done, error), params (node_id, exclude, kinds,
   relations, after, limit), kinds, relations, page_uri, filter_suffix, limit, limit_choices, limit_preference.
   The sources are walked by object id: a page shows the findings of the objects checked for it, and "Next page"
   goes on after the last of them (After). A target the user may not read is "unavailable",
   without id or state. The same file is in design/admin and design/admin4. *}
{def $kind_names = hash( 'deleted', 'Deleted'|i18n( 'kernel/contentcheck' ),
                         'trash', 'In the trash'|i18n( 'kernel/contentcheck' ),
                         'hidden', 'Hidden'|i18n( 'kernel/contentcheck' ),
                         'unpublished', 'Never published'|i18n( 'kernel/contentcheck' ),
                         'no_location', 'Without location'|i18n( 'kernel/contentcheck' ),
                         'unavailable', 'Not available to you'|i18n( 'kernel/contentcheck' ) )
     $kind_classes = hash( 'deleted', 'is-bad', 'trash', 'is-bad', 'hidden', 'is-warn', 'unpublished', 'is-warn', 'no_location', 'is-warn', 'unavailable', 'is-info' )
     $relation_names = hash( 'xml', 'Links and embeds in rich text'|i18n( 'kernel/contentcheck' ),
                             'object', 'Object relations and relation fields'|i18n( 'kernel/contentcheck' ) )
     $via_names = hash( 'link', 'Link'|i18n( 'kernel/contentcheck' ),
                        'embed', 'Embed'|i18n( 'kernel/contentcheck' ),
                        'embed-inline', 'Inline embed'|i18n( 'kernel/contentcheck' ),
                        'relation', 'Object relation'|i18n( 'kernel/contentcheck' ),
                        'attribute', 'Relation field'|i18n( 'kernel/contentcheck' ) )
     $node = $result.node
     $findings = $result.findings
     $separator = cond( $filter_suffix|eq( '?' ), '', '&' )}

<div class="context-block a4-page a4-contentcheck">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Broken relations'|i18n( 'kernel/contentcheck' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="a4-intro">{'Visible, published content that refers to something a visitor cannot reach: an object or node that was deleted, is in the trash, is hidden or was never published. Checked are the object relations and relation fields of the published version and the links and embeds in its rich text. Nothing is changed here; open a source to edit it.'|i18n( 'kernel/contentcheck' )}</p>

{if eq( $result.error, 'database' )}
<div class="a4-notice is-warn" role="alert">{'This check is not available on this database.'|i18n( 'kernel/contentcheck' )}</div>
{/if}

<section aria-labelledby="cc-filter-title">
<h2 class="a4-sr-only" id="cc-filter-title">{'Filter'|i18n( 'kernel/contentcheck' )}</h2>
<form class="a4-filterbar" method="get" action={$page_uri|ezurl}>
    <div class="a4-field">
        <label for="cc-node">{'Below node'|i18n( 'kernel/contentcheck' )}</label>
        <input type="text" id="cc-node" name="NodeID" value="{$params.node_id}" inputmode="numeric" pattern="[0-9]*" maxlength="10" aria-describedby="cc-node-help" />
        <span class="a4-help" id="cc-node-help">{'Now: %name. 1 checks the whole tree.'|i18n( 'kernel/contentcheck',, hash( '%name', cond( $node.node_id|eq( 1 ), 'Whole tree'|i18n( 'kernel/contentcheck' ), $node.name ) ) )|wash}</span>
    </div>
    <div class="a4-field">
        <label for="cc-exclude">{'Leave out the nodes below'|i18n( 'kernel/contentcheck' )}</label>
        <input type="text" id="cc-exclude" name="Exclude" value="{$params.exclude|implode( ', ' )|wash}" maxlength="250" spellcheck="false" aria-describedby="cc-exclude-help" />
        <span class="a4-help" id="cc-exclude-help">{'Node ids, separated by commas.'|i18n( 'kernel/contentcheck' )}{if $result.excluded|count|gt( 0 )} {'Left out: %count subtrees.'|i18n( 'kernel/contentcheck',, hash( '%count', $result.excluded|count ) )}{/if}</span>
    </div>
    <fieldset class="a4-field">
        <legend>{'Targets'|i18n( 'kernel/contentcheck' )}</legend>
        <div class="a4-chips">
        {foreach $kinds as $kind}
            <label class="a4-chip"><input type="checkbox" name="Kind[]" value="{$kind|wash}"{if $params.kinds|contains( $kind )} checked="checked"{/if} /><span>{$kind_names[$kind]|wash}</span></label>
        {/foreach}
        </div>
    </fieldset>
    <fieldset class="a4-field">
        <legend>{'References'|i18n( 'kernel/contentcheck' )}</legend>
        <div class="a4-chips">
        {foreach $relations as $relation}
            <label class="a4-chip"><input type="checkbox" name="Relation[]" value="{$relation|wash}"{if $params.relations|contains( $relation )} checked="checked"{/if} /><span>{$relation_names[$relation]|wash}</span></label>
        {/foreach}
        </div>
    </fieldset>
    <div class="a4-field">
        <button type="submit" class="a4-btn a4-btn-primary">{'Check'|i18n( 'kernel/contentcheck' )}</button>
    </div>
</form>
</section>

<section class="a4-section" aria-labelledby="cc-list-title">
<div class="a4-section-head">
    <h2 class="a4-h2" id="cc-list-title">{'Findings'|i18n( 'kernel/contentcheck' )}</h2>
    <span class="a4-meta">{if $params.after|gt( 0 )}{'%count objects checked after object %after.'|i18n( 'kernel/contentcheck',, hash( '%count', $result.checked, '%after', $params.after ) )}{else}{'%count objects checked.'|i18n( 'kernel/contentcheck',, hash( '%count', $result.checked ) )}{/if}</span>
</div>

{if $findings|count|eq( 0 )}
<p class="a4-empty">{if $result.done}{'No broken relation found.'|i18n( 'kernel/contentcheck' )}{else}{'No broken relation found among these objects. Go on with the next page.'|i18n( 'kernel/contentcheck' )}{/if}</p>
{else}
<div class="a4-table-wrap">
<table class="a4-table">
<thead>
<tr>
    <th scope="col">{'Source'|i18n( 'kernel/contentcheck' )}</th>
    <th scope="col">{'Field'|i18n( 'kernel/contentcheck' )}</th>
    <th scope="col">{'Target'|i18n( 'kernel/contentcheck' )}</th>
    <th scope="col">{'State'|i18n( 'kernel/contentcheck' )}</th>
    <th scope="col"><span class="a4-sr-only">{'Actions'|i18n( 'kernel/contentcheck' )}</span></th>
</tr>
</thead>
<tbody>
{foreach $findings as $finding}
<tr>
    <td><a href={concat( '/content/view/full/', $finding.source_node_id )|ezurl}>{$finding.source_name|wash}</a>
        <div class="a4-meta">{$finding.class_name|wash} &middot; {'Object %id'|i18n( 'kernel/contentcheck',, hash( '%id', $finding.source_id ) )}</div></td>
    <td>{if $finding.field_name|ne( '' )}{$finding.field_name|wash}{else}&ndash;{/if}{if $finding.language|ne( '' )} <span class="a4-tag is-mono">{$finding.language|wash}</span>{/if}
        <div class="a4-meta">{$via_names[$finding.via]|wash}</div></td>
    <td>{if eq( $finding.kind, 'unavailable' )}{'Target not available'|i18n( 'kernel/contentcheck' )}{elseif eq( $finding.target_type, 'node' )}{'Node %id'|i18n( 'kernel/contentcheck',, hash( '%id', $finding.target_id ) )}{else}{'Object %id'|i18n( 'kernel/contentcheck',, hash( '%id', $finding.target_id ) )}{/if}
        {if $finding.target_name|ne( '' )}<div class="a4-meta">{$finding.target_name|wash}</div>{/if}</td>
    <td><span class="a4-tag {$kind_classes[$finding.kind]}">{$kind_names[$finding.kind]|wash}</span></td>
    <td>{if $finding.can_edit}<a class="a4-btn a4-btn-small" href={concat( '/content/edit/', $finding.source_id )|ezurl}>{'Edit'|i18n( 'kernel/contentcheck' )}</a>{/if}</td>
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
        <a href={concat( '/user/preferences/set/', $limit_preference, '/', $limit_index|inc )|ezurl} title="{'Show %count findings per page.'|i18n( 'kernel/contentcheck',, hash( '%count', $limit_option ) )}">{$limit_option}</a>
        {/if}
    {/foreach}
    </p>
    <div class="a4-pager">
        {if $params.after|gt( 0 )}<a class="a4-btn" href={concat( $page_uri, $filter_suffix )|ezurl}>{'From the start'|i18n( 'kernel/contentcheck' )}</a>{/if}
        {if $result.done|not}<a class="a4-btn" href={concat( $page_uri, $filter_suffix, $separator, 'After=', $result.last )|ezurl}>{'Next page'|i18n( 'kernel/contentcheck' )}</a>{/if}
    </div>
</div>
</section>

</div></div></div>
</div>
{undef $kind_names $kind_classes $relation_names $via_names $node $findings $separator}
