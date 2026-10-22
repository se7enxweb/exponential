{* The node view's "Data tables" tab: the rows of data tables that point at this node or its object (fetch
   datatable/pointing_at), the first ten per table, with a link to the table. Empty for a user without datatable/read. *}
{def $datatable_tab_module = 'datatable'
     $datatable_tab_found = fetch( $datatable_tab_module, 'pointing_at', hash( 'object_id', $node.contentobject_id, 'node_id', $node.node_id ) )}
<div class="a4-datatable a4-stack">
{if $datatable_tab_found|count}
{foreach $datatable_tab_found as $t}
<section class="a4-card is-flush" aria-labelledby="datatable-tab-{$t.class|wash}-{$t.column|wash}">
    <div class="a4-card-head">
        <h3 class="a4-h3 a4-card-title" id="datatable-tab-{$t.class|wash}-{$t.column|wash}"><a href={concat( 'datatable/rows/', $t.class )|ezurl}>{$t.class|wash}</a></h3>
        <p class="a4-meta">{'%count rows point at this item through %column (%kind)'|i18n( 'design/admin/datatable',, hash( '%count', $t.count, '%column', $t.column, '%kind', $t.kind ) )|wash}</p>
    </div>
    <div class="a4-table-wrap" role="region" aria-labelledby="datatable-tab-{$t.class|wash}-{$t.column|wash}" tabindex="0">
    <table class="a4-table is-compact">
        <thead><tr>{foreach $t.fields as $f max 6}<th scope="col">{$f|wash}</th>{/foreach}</tr></thead>
        <tbody>
        {foreach $t.rows as $row}<tr>{foreach $t.fields as $f max 6}<td>{if is_set( $row[$f] )}{$row[$f]|shorten( 60 )|wash}{/if}</td>{/foreach}</tr>{/foreach}
        </tbody>
    </table>
    </div>
    {if $t.more}<div class="a4-card-foot"><a href={concat( 'datatable/rows/', $t.class, '/(', $t.column, ')/', cond( eq( $t.kind, 'node' ), $node.node_id, $node.contentobject_id ) )|ezurl}>{'All %count rows'|i18n( 'design/admin/datatable',, hash( '%count', $t.count ) )|wash}</a></div>{/if}
</section>
{/foreach}
{else}
<p class="a4-empty">{'No row of a data table points at this item.'|i18n( 'design/admin/datatable' )}</p>
{/if}
</div>
{undef $datatable_tab_module $datatable_tab_found}
