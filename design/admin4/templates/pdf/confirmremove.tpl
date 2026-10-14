{* The confirmation of Remove selected on the PDF export list (pdf/list).

   Shown instead of the list when Remove selected is pressed: which exports would go, with their stored files and
   what that means, before anything happens. Remove posts the same ids again with ConfirmRemoveButton; Cancel is a
   link back to the list and changes nothing.

   Variables: pdf_remove_items (array of hash( 'object', the eZPDFExport, 'info', what the list's cards show )).
   The same file is in design/admin and design/admin4. Guide: doc/guides/pdf-exports.md *}
{def $files = 0
     $bytes = 0}
{foreach $pdf_remove_items as $item}{if $item.info.file_exists}{set $files = inc( $files )}{set $bytes = sum( $bytes, $item.info.file_size )}{/if}{/foreach}

<div class="context-block a4-page exp-pdf">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Remove PDF exports?'|i18n( 'design/admin/pdf/list' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<form method="post" action={'pdf/list'|ezurl}>
<section class="a4-card is-bad a4-stack" aria-labelledby="pdf-confirm-title">
    <h2 class="a4-h2" id="pdf-confirm-title">{'What happens'|i18n( 'design/admin/pdf/list' )}</h2>
    <ul class="a4-pdf-consequences">
        <li>{'The exports below are removed with their settings, and a draft somebody has open goes with them.'|i18n( 'design/admin/pdf/list' )}</li>
        {if $files|gt( 0 )}
        <li>{'Their %count stored files (%size) are deleted. Their public addresses, wherever they were shared, stop working.'|i18n( 'design/admin/pdf/list',, hash( '%count', $files, '%size', $bytes|si( byte, auto ) ) )|wash}</li>
        {else}
        <li>{'None of them has a stored file, so no file is deleted.'|i18n( 'design/admin/pdf/list' )}</li>
        {/if}
        <li>{'The content they were made from is not touched.'|i18n( 'design/admin/pdf/list' )}</li>
    </ul>

    <ul class="a4-cards">
    {foreach $pdf_remove_items as $item}
        <li class="a4-card">
            <input type="hidden" name="DeleteIDArray[]" value="{$item.object.id}" />
            <div class="a4-card-title">
                <h3 class="a4-h3">{$item.object.title|wash}</h3>
                <span class="a4-meta">{'ID %id'|i18n( 'design/admin/pdf/list',, hash( '%id', $item.object.id ) )}</span>
                <ul class="a4-badges">
                    {if $item.info.is_stored}<li class="a4-badge is-info">{'Generated once'|i18n( 'design/admin/pdf/list' )}</li>{else}<li class="a4-badge is-info">{'On the fly'|i18n( 'design/admin/pdf/list' )}</li>{/if}
                    {if $item.info.draft}<li class="a4-badge is-warn">{if $item.info.draft_by|ne( '' )}{'Open in the editor by %name'|i18n( 'design/admin/pdf/list',, hash( '%name', $item.info.draft_by ) )|wash}{else}{'Open in the editor'|i18n( 'design/admin/pdf/list' )}{/if}</li>{/if}
                </ul>
            </div>
            <dl class="a4-facts">
                <div><dt>{'Source node'|i18n( 'design/admin/pdf/list' )}</dt>
                    <dd>{if $item.info.source_exists}{$item.info.source_name|wash}{elseif $item.info.source_node_id|gt( 0 )}<span class="a4-muted">{'Node %id (no longer exists)'|i18n( 'design/admin/pdf/list',, hash( '%id', $item.info.source_node_id ) )}</span>{else}<span class="a4-muted">{'not chosen'|i18n( 'design/admin/pdf/list' )}</span>{/if}</dd></div>
                <div><dt>{'Deleted with it'|i18n( 'design/admin/pdf/list' )}</dt>
                    <dd>{if $item.info.file_exists}<span class="a4-pdf-file"><code>{$item.info.file_name|wash}</code></span><span class="a4-meta">{'%size, generated %date'|i18n( 'design/admin/pdf/list',, hash( '%size', $item.info.file_size|si( byte, auto ), '%date', $item.info.file_mtime|l10n( shortdatetime ) ) )|wash}</span>{else}<span class="a4-muted">{'no stored file'|i18n( 'design/admin/pdf/list' )}</span>{/if}</dd></div>
                {if $item.info.public_url|ne( '' )}
                <div class="a4-field-wide"><dt>{'Address that stops working'|i18n( 'design/admin/pdf/list' )}</dt>
                    <dd class="a4-pdf-url"><code>{concat( ezsys( 'serverurl' ), $item.info.public_url|ezroot( 'no' ) )|wash}</code></dd></div>
                {/if}
            </dl>
        </li>
    {/foreach}
    </ul>
</section>

<div class="a4-toolbar is-bottom">
    <div class="a4-actions">
        <input type="hidden" name="RemoveExportButton" value="1" />
        <button type="submit" class="a4-btn a4-btn-danger" name="ConfirmRemoveButton" value="1">{if $pdf_remove_items|count|eq( 1 )}{'Remove the export'|i18n( 'design/admin/pdf/list' )}{else}{'Remove %count exports'|i18n( 'design/admin/pdf/list',, hash( '%count', $pdf_remove_items|count ) )}{/if}</button>
        <a class="a4-btn exp-cancel" href={'pdf/list'|ezurl}>{'Cancel'|i18n( 'design/admin/pdf/list' )}</a>
    </div>
    <p class="a4-meta">{'Nothing has been removed yet.'|i18n( 'design/admin/pdf/list' )}</p>
</div>
</form>

</div></div></div>
</div>
{undef $files $bytes}
