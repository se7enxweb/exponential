{* The datatable/rows view: the rows of one data table, page by page.
   Variables: entry, fields, rows (list of cells: value, cut, url), count, offset, limit, filter (field, value) or
   null, error, page_uri. *}
<div class="a4-page a4-datatable" id="exp-datatable-rows">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <p class="a4-pagehead-pre"><a href={concat( 'datatable/table/', $entry.class )|ezurl}>{$entry.class|wash}</a></p>
        <h1 class="a4-pagehead-title context-title">{'Rows of %table'|i18n( 'design/admin/datatable',, hash( '%table', $entry.table ) )|wash}</h1>
        <p class="a4-pagehead-sub">{if $filter}{'%count rows where %field is %value.'|i18n( 'design/admin/datatable',, hash( '%count', $count, '%field', $filter.field, '%value', $filter.value ) )|wash} <a href={concat( 'datatable/rows/', $entry.class )|ezurl}>{'All rows'|i18n( 'design/admin/datatable' )}</a>{else}{'%count rows.'|i18n( 'design/admin/datatable',, hash( '%count', $count ) )|wash}{/if}</p>
    </div>
</div>

{if $error}<div class="a4-notice is-bad" role="alert"><p>{$error|wash}</p></div>{/if}

{if $rows|count}
<div class="a4-card is-flush a4-section">
<div class="a4-table-wrap" role="region" aria-label="{'Rows of %table'|i18n( 'design/admin/datatable',, hash( '%table', $entry.table ) )|wash}" tabindex="0">
<table class="a4-table is-compact">
    <thead><tr>{foreach $fields as $f}<th scope="col">{$f|wash}</th>{/foreach}</tr></thead>
    <tbody>
    {foreach $rows as $cells}
    <tr>{foreach $cells as $c}<td>{if $c.url}<a href={$c.url|ezurl}>{$c.value|wash}</a>{else}{$c.value|wash}{/if}{if $c.cut}&hellip;{/if}</td>{/foreach}</tr>
    {/foreach}
    </tbody>
</table>
</div>
</div>
{include name=navigator uri='design:navigator/google.tpl' page_uri=$page_uri item_count=$count view_parameters=hash( 'offset', $offset ) item_limit=$limit}
{else}
<p class="a4-empty">{'No rows.'|i18n( 'design/admin/datatable' )}</p>
{/if}

</div>
