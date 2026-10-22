{* The datatable/conflicts view: the open conflicts of the two-way synchronisations.
   Variables: conflicts (expDataSyncConflict), count, offset, limit, sync_names (id => name), sync_id, messages, page_uri. *}
<div class="a4-page a4-datatable" id="exp-datatable-conflicts">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <p class="a4-pagehead-pre"><a href={'datatable/syncs'|ezurl}>{'Synchronisations'|i18n( 'design/admin/datatable' )}</a></p>
        <h1 class="a4-pagehead-title context-title">{'Conflicts'|i18n( 'design/admin/datatable' )}</h1>
        <p class="a4-pagehead-sub">{'Both the content and the row changed since the last run of a two-way synchronisation that asks. Take one side; the other is written over.'|i18n( 'design/admin/datatable' )}</p>
    </div>
</div>

{foreach $messages as $m}
<div class="a4-notice is-{if eq( $m.kind, 'ok' )}ok{else}bad{/if}" role="{if eq( $m.kind, 'ok' )}status{else}alert{/if}"><p>{$m.text|wash}</p></div>
{/foreach}

{if $conflicts|count}
<form method="post" action={$page_uri|ezurl} class="a4-stack">
{foreach $conflicts as $c}
<section class="a4-card is-warn" aria-labelledby="conflict-{$c.id}">
    <div class="a4-card-head">
        <h2 class="a4-h3 a4-card-title" id="conflict-{$c.id}">{if is_set( $sync_names[$c.sync_id] )}{$sync_names[$c.sync_id]|wash}{else}#{$c.sync_id}{/if}: <code class="a4-code">{$c.row_key|wash}</code></h2>
        <p class="a4-meta">{'Found %time'|i18n( 'design/admin/datatable',, hash( '%time', $c.detected|l10n( 'shortdatetime' ) ) )|wash} &middot; {'object %id'|i18n( 'design/admin/datatable',, hash( '%id', $c.object_id ) )|wash}</p>
    </div>
    <div class="a4-table-wrap" role="region" aria-labelledby="conflict-{$c.id}" tabindex="0">
    <table class="a4-table is-compact">
        <thead><tr><th scope="col">{'Column'|i18n( 'design/admin/datatable' )}</th><th scope="col">{'Content'|i18n( 'design/admin/datatable' )}</th><th scope="col">{'Table'|i18n( 'design/admin/datatable' )}</th></tr></thead>
        <tbody>{foreach $c.differences as $d}<tr><td><code class="a4-code">{$d.column|wash}</code></td><td>{$d.content|wash}</td><td>{$d.row|wash}</td></tr>{/foreach}</tbody>
    </table>
    </div>
    <div class="a4-card-foot a4-actions">
        <button type="submit" class="a4-btn" name="TakeContentButton[{$c.id}]" value="1">{'Take the content'|i18n( 'design/admin/datatable' )}</button>
        <button type="submit" class="a4-btn" name="TakeTableButton[{$c.id}]" value="1">{'Take the table'|i18n( 'design/admin/datatable' )}</button>
    </div>
</section>
{/foreach}
</form>
{include name=navigator uri='design:navigator/google.tpl' page_uri=$page_uri item_count=$count view_parameters=hash( 'offset', $offset ) item_limit=$limit}
{else}
<p class="a4-empty">{'No open conflict.'|i18n( 'design/admin/datatable' )}</p>
{/if}

</div>
