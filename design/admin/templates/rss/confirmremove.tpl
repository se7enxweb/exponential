{* The confirmation of Remove selected on the RSS list (rss/list).

   Shown instead of the list when Remove selected is pressed: which exports or imports would go and what that
   means, before anything happens. Remove posts the same ids again with ConfirmRemoveButton; Cancel is a link back
   to the list and changes nothing.

   Variables: rss_remove_kind ('export' or 'import'), rss_remove_items (array of hash( 'object', the eZRSSExport or
   eZRSSImport, 'info', what the list's cards show )), rss_remove_button (RemoveExportButton or RemoveImportButton)
   and rss_remove_field (DeleteIDArray or DeleteIDArrayImport). Guide: doc/guides/rss-feeds.md *}
{include uri='design:rss/exp_style.tpl'}
{def $is_export = eq( $rss_remove_kind, 'export' )
     $imported = 0}
{if $is_export|not}{foreach $rss_remove_items as $item}{set $imported = sum( $imported, $item.info.imported_count )}{/foreach}{/if}

<div class="context-block exp-lists exp-rss">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{if $is_export}{'Remove RSS exports?'|i18n( 'design/admin/rss/list' )}{else}{'Remove RSS imports?'|i18n( 'design/admin/rss/list' )}{/if}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<form method="post" action={'rss/list'|ezurl}>
<section class="exp-confirm" aria-labelledby="rss-confirm-title">
    <h2 class="exp-h2" id="rss-confirm-title">{'What happens'|i18n( 'design/admin/rss/list' )}</h2>
    <ul class="exp-consequences">
    {if $is_export}
        <li>{'The feed addresses below stop answering. Feed readers and sites subscribed to them get an error from then on.'|i18n( 'design/admin/rss/list' )}</li>
        <li>{'The sources of each export go with it. The content the feeds listed is not touched.'|i18n( 'design/admin/rss/list' )}</li>
        <li>{'An export can be made inactive instead: its settings stay and it can be switched on again.'|i18n( 'design/admin/rss/list' )}</li>
    {else}
        <li>{'The rssimport cronjob stops reading these feeds.'|i18n( 'design/admin/rss/list' )}</li>
        <li>{'The %count objects they created stay where they are. Remove them in the content tree if they are no longer wanted.'|i18n( 'design/admin/rss/list',, hash( '%count', $imported ) )}</li>
        <li>{'An import can be made inactive instead: its settings stay and it can be switched on again.'|i18n( 'design/admin/rss/list' )}</li>
    {/if}
    </ul>

    <ul class="exp-secs">
    {foreach $rss_remove_items as $item}
        <li class="exp-sec">
            <input type="hidden" name="{$rss_remove_field|wash}[]" value="{$item.object.id}" />
            <div class="exp-sec-title">
                <h3>{if $is_export}{$item.object.title|wash}{else}{$item.object.name|wash}{/if}</h3>
                <span class="exp-meta">{'ID %id'|i18n( 'design/admin/rss/list',, hash( '%id', $item.object.id ) )}</span>
                <ul class="exp-badges">{if $item.object.active|eq( 1 )}<li class="exp-badge is-ok">{'Active'|i18n( 'design/admin/rss/list' )}</li>{else}<li class="exp-badge is-muted">{'Inactive'|i18n( 'design/admin/rss/list' )}</li>{/if}</ul>
            </div>
            <dl class="exp-facts">
            {if $is_export}
                <div style="grid-column: 1 / -1;"><dt>{'Feed address'|i18n( 'design/admin/rss/list' )}</dt>
                    <dd class="exp-url">{if $item.info.feed_url|ne( '' )}<code>{$item.info.feed_url|wash}</code>{else}<span class="exp-muted">{'not set'|i18n( 'design/admin/rss/list' )}</span>{/if}</dd></div>
                <div><dt>{'Format'|i18n( 'design/admin/rss/list' )}</dt><dd>{$item.info.format|wash}</dd></div>
                <div><dt>{if $item.info.is_opml}{'Feeds listed'|i18n( 'design/admin/rss/list' )}{else}{'Sources'|i18n( 'design/admin/rss/list' )}{/if}</dt><dd>{$item.info.source_count}</dd></div>
            {else}
                <div style="grid-column: 1 / -1;"><dt>{'Source URL'|i18n( 'design/admin/rss/list' )}</dt>
                    <dd class="exp-url">{if $item.object.url|ne( '' )}<code>{$item.object.url|wash}</code>{else}<span class="exp-muted">{'not set'|i18n( 'design/admin/rss/list' )}</span>{/if}</dd></div>
                <div><dt>{'Destination'|i18n( 'design/admin/rss/list' )}</dt><dd>{if $item.info.destination_name|ne( '' )}{$item.info.destination_name|wash}{else}<span class="exp-muted">{'not set'|i18n( 'design/admin/rss/list' )}</span>{/if}</dd></div>
                <div><dt>{'Imported'|i18n( 'design/admin/rss/list' )}</dt><dd>{'%count objects, which stay'|i18n( 'design/admin/rss/list',, hash( '%count', $item.info.imported_count ) )}</dd></div>
            {/if}
            </dl>
        </li>
    {/foreach}
    </ul>
</section>

<div class="exp-bottombar">
    <div class="exp-actions">
        <input type="hidden" name="{$rss_remove_button|wash}" value="1" />
        <button type="submit" class="exp-btn exp-btn-danger" name="ConfirmRemoveButton" value="1">{if $is_export}{'Remove %count exports'|i18n( 'design/admin/rss/list',, hash( '%count', $rss_remove_items|count ) )}{else}{'Remove %count imports'|i18n( 'design/admin/rss/list',, hash( '%count', $rss_remove_items|count ) )}{/if}</button>
        <a class="exp-btn exp-cancel" href={'rss/list'|ezurl}>{'Cancel'|i18n( 'design/admin/rss/list' )}</a>
    </div>
    <p class="exp-meta">{'Nothing has been removed yet.'|i18n( 'design/admin/rss/list' )}</p>
</div>
</form>

</div></div></div>
</div>
{undef $is_export $imported}
