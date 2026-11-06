{* The imports of URL maps (content/urlimporthistory[/(offset)/<n>]), newest first: who, when, from which file, the
   counts and the state; each opens its details with Undo (urlimport_batch.tpl).

   Variables (kernel/private/classes/views/content/urlimporthistory.php): batches, batch_count, batch_limit,
   import_tables, view_parameters. The same file is in design/admin and design/admin4. *}

<div class="context-block a4-page a4-url a4-content-urlmap">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Import history (%count)'|i18n( 'design/admin/content/urlimport',, hash( '%count', $batch_count ) )|wash}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="a4-intro">{'Every import of a URL map, newest first. An import can be undone as a whole: each entry it wrote goes back to what was there before.'|i18n( 'design/admin/content/urlimport' )}</p>

{include uri='design:content/urlmap_toolbar.tpl' current='history' all=true()}

{if $import_tables|not}
<div class="a4-notice is-warn" role="alert"><p>{'The tables of URL map imports do not exist yet. Run the upgrade step 6.0.15-url-map (./console exp:upgrade --apply) before importing.'|i18n( 'design/admin/content/urlimport' )}</p></div>
{/if}

{if $batches|count|eq( 0 )}
<p class="a4-empty">{'No URL map has been imported yet.'|i18n( 'design/admin/content/urlimport' )} <a href={'content/urlimport'|ezurl}>{'Import a URL map'|i18n( 'design/admin/content/urlimport' )}</a></p>
{else}
<div class="a4-table-wrap">
<table class="a4-table">
<caption class="a4-sr-only">{'Imports of URL maps'|i18n( 'design/admin/content/urlimport' )}</caption>
<thead><tr>
    <th scope="col">{'Number'|i18n( 'design/admin/content/urlimport' )}</th>
    <th scope="col">{'Date'|i18n( 'design/admin/content/urlimport' )}</th>
    <th scope="col">{'By'|i18n( 'design/admin/content/urlimport' )}</th>
    <th scope="col">{'File'|i18n( 'design/admin/content/urlimport' )}</th>
    <th scope="col" class="a4-num">{'Rows'|i18n( 'design/admin/content/urlimport' )}</th>
    <th scope="col" class="a4-num">{'Created'|i18n( 'design/admin/content/urlimport' )}</th>
    <th scope="col" class="a4-num">{'Replaced'|i18n( 'design/admin/content/urlimport' )}</th>
    <th scope="col" class="a4-num">{'Skipped'|i18n( 'design/admin/content/urlimport' )}</th>
    <th scope="col" class="a4-num">{'Failed'|i18n( 'design/admin/content/urlimport' )}</th>
    <th scope="col">{'State'|i18n( 'design/admin/content/urlimport' )}</th>
</tr></thead>
<tbody>
{foreach $batches as $batch}
<tr>
    <td><a href={concat( 'content/urlimporthistory/', $batch.id )|ezurl}>{'Import %id'|i18n( 'design/admin/content/urlimport',, hash( '%id', $batch.id ) )|wash}</a></td>
    <td>{$batch.created|l10n( shortdatetime )}</td>
    <td>{$batch.user_name|wash}</td>
    <td class="a4-content-urlmap-addr">{$batch.source_name|wash}{if $batch.format|ne( '' )} <span class="a4-meta">{$batch.format|wash}</span>{/if}</td>
    <td class="a4-num">{$batch.rows_total}</td>
    <td class="a4-num">{$batch.created_count}</td>
    <td class="a4-num">{$batch.updated_count}</td>
    <td class="a4-num">{$batch.skipped_count}</td>
    <td class="a4-num">{$batch.failed_count}</td>
    <td>{include uri='design:content/urlimport_batch_state.tpl' state=$batch.state}</td>
</tr>
{/foreach}
</tbody>
</table>
</div>
{/if}

<div class="a4-listfoot">
    <div class="a4-pager">
    {include name=navigator
             uri='design:navigator/google.tpl'
             page_uri='content/urlimporthistory'
             item_count=$batch_count
             view_parameters=$view_parameters
             item_limit=$batch_limit}
    </div>
</div>

</div></div></div>
</div>
