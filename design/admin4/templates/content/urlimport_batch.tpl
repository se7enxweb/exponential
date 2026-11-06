{* One import of a URL map (content/urlimporthistory/<id>[/(offset)/<n>]): its options and counts, Undo (asks "Verify
   it's you"; the entries changed after the import are listed and kept unless "also undo" is ticked, the console's
   --force), and the entries it wrote, a page at a time.

   Variables (kernel/private/classes/views/content/urlimporthistory.php): batch, batch_items, batch_item_count,
   batch_item_limit, batch_changed, batch_notice, batch_error, view_parameters. The same file is in design/admin and
   design/admin4. *}

{def $b = $batch}

<div class="context-block a4-page a4-url a4-content-urlmap">

<div class="box-header"><div class="box-ml">
<div class="a4-title-row">
<h1 class="context-title">{'Import %id'|i18n( 'design/admin/content/urlimport',, hash( '%id', $b.id ) )|wash}</h1>
{include uri='design:content/urlimport_batch_state.tpl' state=$b.state}
</div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

{include uri='design:content/urlmap_toolbar.tpl' current='history' all=true()}

{switch match=$batch_notice}
{case match='undone'}<div class="a4-notice is-ok" role="status"><p>{'The import was undone.'|i18n( 'design/admin/content/urlimport' )}</p></div>{/case}
{case match='partly'}<div class="a4-notice is-warn" role="status"><p>{'The import was undone, except for the entries changed after it, which were kept.'|i18n( 'design/admin/content/urlimport' )}</p></div>{/case}
{case}{/case}
{/switch}
{if $batch_error|ne( '' )}<div class="a4-notice is-bad" role="alert"><p>{'The import could not be undone: %error'|i18n( 'design/admin/content/urlimport',, hash( '%error', $batch_error ) )|wash}</p></div>{/if}

<dl class="a4-facts">
    <div><dt>{'Date'|i18n( 'design/admin/content/urlimport' )}</dt><dd>{$b.created|l10n( shortdatetime )}</dd></div>
    <div><dt>{'By'|i18n( 'design/admin/content/urlimport' )}</dt><dd>{$b.user_name|wash}</dd></div>
    <div><dt>{'File'|i18n( 'design/admin/content/urlimport' )}</dt><dd>{$b.source_name|wash}{if $b.format|ne( '' )} ({$b.format|wash}){/if}</dd></div>
    <div><dt>{'Siteaccess of bare paths'|i18n( 'design/admin/content/urlimport' )}</dt><dd>{$b.siteaccess|wash}</dd></div>
    <div><dt>{'Language'|i18n( 'design/admin/content/urlimport' )}</dt><dd>{if $b.language|ne( '' )}{$b.language|wash}{else}{'The language of the siteaccess'|i18n( 'design/admin/content/urlimport' )}{/if}</dd></div>
    <div><dt>{'When an old address has an entry already'|i18n( 'design/admin/content/urlimport' )}</dt><dd>{if eq( $b.on_conflict, 'overwrite' )}{'Replace the entry'|i18n( 'design/admin/content/urlimport' )}{else}{'Keep the entry (skip the row)'|i18n( 'design/admin/content/urlimport' )}{/if}</dd></div>
    <div><dt>{'Rows'|i18n( 'design/admin/content/urlimport' )}</dt><dd>{'%created created, %updated replaced, %skipped skipped, %failed failed.'|i18n( 'design/admin/content/urlimport',, hash( '%created', $b.created_count, '%updated', $b.updated_count, '%skipped', $b.skipped_count, '%failed', $b.failed_count ) )|wash}</dd></div>
    {if $b.undone|gt( 0 )}<div><dt>{'Undone'|i18n( 'design/admin/content/urlimport' )}</dt><dd>{$b.undone|l10n( shortdatetime )}{if $b.undone_by_name|ne( '' )}, {$b.undone_by_name|wash}{/if}</dd></div>{/if}
</dl>

{if or( $b.undoable, eq( $b.state, 'partly-undone' ) )}
<form method="post" action={concat( 'content/urlimporthistory/', $b.id )|ezurl}>
<section class="a4-card a4-section" aria-labelledby="urlimport-undo-title">
<div class="a4-card-head">
    <h2 class="a4-h2" id="urlimport-undo-title">{'Undo this import'|i18n( 'design/admin/content/urlimport' )}</h2>
    <p>{'Every entry the import created is removed and every entry it replaced is put back as it was, the last first. You confirm who you are first.'|i18n( 'design/admin/content/urlimport' )}</p>
</div>
<div class="a4-card-body">
{if $batch_changed|count|gt( 0 )}
<div class="a4-notice is-warn">
    <p class="a4-notice-title"><strong>{'%count entries changed after the import'|i18n( 'design/admin/content/urlimport',, hash( '%count', $batch_changed|count ) )|wash}</strong></p>
    <ul class="a4-content-urlmap-notes">{foreach $batch_changed as $index => $changed max 20}<li><code>{$changed.old_url|wash}</code></li>{/foreach}</ul>
</div>
<label class="a4-check"><input type="checkbox" name="UndoForce" value="1" /> <span><strong>{'Also undo the entries changed after the import'|i18n( 'design/admin/content/urlimport' )}</strong><span class="a4-help">{'Without it they are kept and the import becomes partly undone (the console\'s --force).'|i18n( 'design/admin/content/urlimport' )}</span></span></label>
{/if}
<div class="a4-actions">
    <button type="submit" class="a4-btn a4-btn-danger" name="UndoButton" value="1" data-a4-confirm="{'Undo this import?'|i18n( 'design/admin/content/urlimport' )|wash}">{'Undo import'|i18n( 'design/admin/content/urlimport' )}</button>
</div>
</div>
</section>
</form>
{/if}

<section class="a4-section" aria-labelledby="urlimport-items-title">
<div class="a4-section-head">
    <h2 class="a4-h2" id="urlimport-items-title">{'Entries written'|i18n( 'design/admin/content/urlimport' )}</h2>
    {if $batch_item_count|gt( 0 )}<span class="a4-meta">{'%from to %to of %count'|i18n( 'design/admin/content/urlimport',, hash( '%from', sum( $view_parameters.offset, 1 ), '%to', min( sum( $view_parameters.offset, $batch_item_limit ), $batch_item_count ), '%count', $batch_item_count ) )|wash}</span>{/if}
</div>
{if $batch_items|count|eq( 0 )}
<p class="a4-empty">{'The import wrote no entry.'|i18n( 'design/admin/content/urlimport' )}</p>
{else}
<div class="a4-table-wrap">
<table class="a4-table a4-content-urlmap-table">
<caption class="a4-sr-only">{'The entries the import wrote'|i18n( 'design/admin/content/urlimport' )}</caption>
<thead><tr>
    <th scope="col" class="a4-num">{'Line'|i18n( 'design/admin/content/urlimport' )}</th>
    <th scope="col">{'Old address'|i18n( 'design/admin/content/urlimport' )}</th>
    <th scope="col">{'Entry'|i18n( 'design/admin/content/urlimport' )}</th>
    <th scope="col">{'Leads to'|i18n( 'design/admin/content/urlimport' )}</th>
    <th scope="col">{'Change'|i18n( 'design/admin/content/urlimport' )}</th>
    <th scope="col">{'State'|i18n( 'design/admin/content/urlimport' )}</th>
</tr></thead>
<tbody>
{foreach $batch_items as $item}
<tr>
    <td class="a4-num">{$item.line}</td>
    <td class="a4-content-urlmap-addr"><code>{$item.old_url|wash}</code></td>
    <td>{switch match=$item.target_table}{case match='ezurlalias_ml'}{'URL alias'|i18n( 'design/admin/content/urlimport' )}{/case}{case match='ezurlwildcard'}{'Wildcard'|i18n( 'design/admin/content/urlimport' )}{/case}{case}{'Redirect'|i18n( 'design/admin/content/urlimport' )}{/case}{/switch}</td>
    <td class="a4-content-urlmap-addr"><code>{$item.target|wash}</code></td>
    <td>{if eq( $item.action, 'created' )}{'Created'|i18n( 'design/admin/content/urlimport' )}{else}{'Replaced'|i18n( 'design/admin/content/urlimport' )}{/if}</td>
    <td>{switch match=$item.state}{case match='applied'}<span class="a4-tag is-ok">{'Applied'|i18n( 'design/admin/content/urlimport' )}</span>{/case}{case match='undone'}<span class="a4-tag">{'Undone'|i18n( 'design/admin/content/urlimport' )}</span>{/case}{case}<span class="a4-tag is-warn">{'Kept'|i18n( 'design/admin/content/urlimport' )}</span>{/case}{/switch}</td>
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
             page_uri=concat( 'content/urlimporthistory/', $b.id )
             item_count=$batch_item_count
             view_parameters=$view_parameters
             item_limit=$batch_item_limit}
    </div>
</div>
</section>

</div></div></div>
</div>
{undef $b}
