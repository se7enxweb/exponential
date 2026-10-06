{* The RSS list (rss/list): the feeds this site publishes (exports) and the feeds of other sites it reads (imports).

   What the two are, an overview, a search and a filter, then one card per export with its feed address, format,
   sources, number of items and when the feed was last written, and one card per import with its source, where it
   puts its items, how many it has brought in and the cronjob that reads it. Each list pages and sorts on its own;
   the page size is shared and remembered. Remove selected leads to a confirmation (design:rss/confirmremove.tpl)
   that says what removing means for the feed's readers or the imported content.

   The same file is in design/admin and design/admin4. The extra figures come from the view (rssexport_info,
   rssimport_info, rss_summary, rss_import_cronjob, rss_feed_cache_time, rss_feedback); without them (an older view
   class) the cards show what the rows hold and nothing is lost. Every variable the page had before is still set:
   rssexport_list, rssimport_list, rssexport_count, rssimport_count, rssexport_pager, rssimport_pager, page_limit,
   page_limit_links, rssexport_sort, rssimport_sort. Everything works without javascript; the script adds the
   search, the filter and the selection count. Guide: doc/guides/rss-feeds.md *}
{include uri='design:rss/exp_style.tpl'}

{def $export_info = first_set( $rssexport_info, hash() )
     $import_info = first_set( $rssimport_info, hash() )
     $summary = first_set( $rss_summary, false() )
     $cronjob = first_set( $rss_import_cronjob, false() )
     $feedback = first_set( $rss_feedback, false() )
     $cache_time = first_set( $rss_feed_cache_time, -1 )}

<div class="context-block exp-lists exp-rss">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'RSS feeds'|i18n( 'design/admin/rss/list' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'An RSS export publishes content of this site as a feed that feed readers and other sites subscribe to. An RSS import reads the feed of another site and creates an object in the content tree for every new item; the rssimport cronjob runs the active imports.'|i18n( 'design/admin/rss/list' )}</p>

{if $feedback}
    {if eq( $feedback.type, 'removed' )}
<div class="exp-feedback is-ok" role="status">{'Removed: %names.'|i18n( 'design/admin/rss/list',, hash( '%names', $feedback.names|implode( ', ' )|wash ) )}</div>
    {elseif eq( $feedback.type, 'none_selected' )}
<div class="exp-feedback is-warn" role="alert">{if eq( $feedback.kind, 'import' )}{'No import was selected. Tick the imports to remove first.'|i18n( 'design/admin/rss/list' )}{else}{'No export was selected. Tick the exports to remove first.'|i18n( 'design/admin/rss/list' )}{/if}</div>
    {elseif eq( $feedback.type, 'gone' )}
<div class="exp-feedback is-warn" role="alert">{'The selected feeds no longer exist; somebody may have removed them already.'|i18n( 'design/admin/rss/list' )}</div>
    {/if}
{/if}

{if $summary}
<section aria-labelledby="rss-overview-title">
<h2 class="exp-sr" id="rss-overview-title">{'Overview'|i18n( 'design/admin/rss/list' )}</h2>
<ul class="exp-figures">
    <li class="exp-figure"><strong>{$summary.exports}</strong><span>{'Exports'|i18n( 'design/admin/rss/list' )}</span></li>
    <li class="exp-figure"><strong>{$summary.active_exports}</strong><span>{'Active exports'|i18n( 'design/admin/rss/list' )}</span></li>
    <li class="exp-figure"><strong>{$summary.imports}</strong><span>{'Imports'|i18n( 'design/admin/rss/list' )}</span></li>
    <li class="exp-figure"><strong>{$summary.active_imports}</strong><span>{'Active imports'|i18n( 'design/admin/rss/list' )}</span></li>
    <li class="exp-figure"><strong>{$summary.imported}</strong><span>{'Imported objects'|i18n( 'design/admin/rss/list' )}</span></li>
</ul>
</section>
{/if}

<div class="exp-toolbar exp-js-only" hidden>
    <div class="exp-field">
        <label for="rss-search">{'Find a feed'|i18n( 'design/admin/rss/list' )}</label>
        <input type="search" id="rss-search" autocomplete="off" spellcheck="false" aria-controls="rss-export-list rss-import-list" aria-describedby="rss-filter-count rss-search-help" />
        <span class="exp-help" id="rss-search-help">{'Name, address, format, source, destination or ID, on this page of each list.'|i18n( 'design/admin/rss/list' )}</span>
    </div>
    <fieldset class="exp-field">
        <legend>{'Show'|i18n( 'design/admin/rss/list' )}</legend>
        <div class="exp-chips">
            <label class="exp-chip"><input type="radio" name="RSSFilter" data-filter="1" value="" checked="checked" /><span>{'All'|i18n( 'design/admin/rss/list' )}</span></label>
            <label class="exp-chip"><input type="radio" name="RSSFilter" data-filter="1" value="active" /><span>{'Active'|i18n( 'design/admin/rss/list' )}</span></label>
            <label class="exp-chip"><input type="radio" name="RSSFilter" data-filter="1" value="!active" /><span>{'Inactive'|i18n( 'design/admin/rss/list' )}</span></label>
            <label class="exp-chip"><input type="radio" name="RSSFilter" data-filter="1" value="attention" /><span>{'Need attention'|i18n( 'design/admin/rss/list' )}</span></label>
        </div>
    </fieldset>
    <p class="exp-filter-count" id="rss-filter-count" aria-live="polite"></p>
</div>

{* How many rows a page holds, for both lists. Changing it starts both lists from the top and keeps their order. *}
<div class="exp-listfoot" style="margin: 0 0 18px;">
    <p class="exp-sizes">
        <span>{'Per page'|i18n( 'design/admin/rss/list' )}:</span>
    {foreach $page_limit_links as $rss_limit}
        {if $rss_limit.current}<span class="current" aria-current="true">{$rss_limit.limit}</span>
        {else}<a href={concat( '/rss/list', $rss_limit.suffix )|ezurl} title="{'Show %count items per page.'|i18n( 'design/admin/rss/list',, hash( '%count', $rss_limit.limit ) )}">{$rss_limit.limit}</a>{/if}
    {/foreach}
    </p>
    {if ge( $cache_time, 0 )}
    <p class="exp-meta">{if eq( $cache_time, 0 )}{'Feeds are written anew for every request (site.ini [RSSSettings] CacheTime is 0).'|i18n( 'design/admin/rss/list' )}{else}{'A feed is written at most every %minutes minutes and served from a cached copy in between (site.ini [RSSSettings] CacheTime).'|i18n( 'design/admin/rss/list',, hash( '%minutes', div( $cache_time, 60 )|round ) )}{/if}</p>
    {/if}
</div>

{* ---- Exports ---- *}
<form name="rssexportslist" method="post" action={'rss/list'|ezurl}>
<section class="exp-part" aria-labelledby="rss-export-title">
<div class="exp-subhead">
    <h2 class="exp-h2" id="rss-export-title">{'RSS exports (%exports_count)'|i18n( 'design/admin/rss/list',, hash( '%exports_count', $rssexport_count ) )}</h2>
    <p class="exp-sortby">
        <span>{'Sort by'|i18n( 'design/admin/rss/list' )}:</span>
        {foreach hash( 'title', 'Name'|i18n( 'design/admin/rss/list' ),
                       'modified', 'Modified'|i18n( 'design/admin/rss/list' ),
                       'active', 'Status'|i18n( 'design/admin/rss/list' ),
                       'rss_version', 'Format'|i18n( 'design/admin/rss/list' ),
                       'access_url', 'Address'|i18n( 'design/admin/rss/list' ),
                       'id', 'ID'|i18n( 'design/admin/rss/list' ) ) as $sort_key => $sort_label}
            {if eq( $rssexport_sort.field, $sort_key )}
        <a class="current" href={concat( '/rss/list/(sort)/', $sort_key, '/(dir)/', $rssexport_sort.opposite, $rssexport_sort.suffix )|ezurl} aria-current="true" title="{'Sort by %column'|i18n( 'design/admin/parts/sortheader',, hash( '%column', $sort_label ) )|wash}">{$sort_label|wash} {if eq( $rssexport_sort.direction, 'asc' )}&#9650;{else}&#9660;{/if}</a>
            {else}
        <a href={concat( '/rss/list/(sort)/', $sort_key, '/(dir)/asc', $rssexport_sort.suffix )|ezurl} title="{'Sort by %column'|i18n( 'design/admin/parts/sortheader',, hash( '%column', $sort_label ) )|wash}">{$sort_label|wash}</a>
            {/if}
        {/foreach}
    </p>
    <p>{'Each export is a feed at its own address. It lists the newest objects below its sources, written as RSS, Atom, OPML or a podcast feed.'|i18n( 'design/admin/rss/list' )}</p>
</div>

{if $rssexport_list|count|eq( 0 )}
<p class="exp-empty">{'There are no RSS exports yet. Create one with New export: give it a name and an address, then choose the folders whose content it lists.'|i18n( 'design/admin/rss/list' )}</p>
{else}
<label class="exp-meta exp-js-only" hidden><input type="checkbox" id="rss-export-select-all" data-select-all="DeleteIDArray[]" /> {'Select all on this page'|i18n( 'design/admin/rss/list' )}</label>
<ul class="exp-secs" id="rss-export-list" data-list="1">
{foreach $rssexport_list as $export}
    {def $info = first_set( $export_info[$export.id], false() )
         $card_id = concat( 'rss-export-', $export.id )}
<li class="exp-sec{if and( $info, $info.attention )} is-attention{/if}" id="{$card_id}" data-active="{if $export.active|eq( 1 )}1{else}0{/if}" data-attention="{if and( $info, $info.attention )}1{else}0{/if}"
    data-search="{if $info}{$info.search|wash}{else}{concat( $export.title, ' ', $export.access_url, ' ', $export.id )|downcase|wash}{/if}">
    <div class="exp-sec-head">
        <div class="exp-sec-title">
            <label class="exp-select" title="{'Select RSS export for removal.'|i18n( 'design/admin/rss/list' )}">
                <input type="checkbox" name="DeleteIDArray[]" value="{$export.id}" aria-label="{'Select %name for removal'|i18n( 'design/admin/rss/list',, hash( '%name', $export.title ) )|wash}" />
            </label>
            <h3 id="{$card_id}-title"><a href={concat( 'rss/edit_export/', $export.id )|ezurl}>{$export.title|wash}</a></h3>
            <span class="exp-meta">{'ID %id'|i18n( 'design/admin/rss/list',, hash( '%id', $export.id ) )}</span>
            <ul class="exp-badges">
                {if $export.active|eq( 1 )}<li class="exp-badge is-ok">{'Active'|i18n( 'design/admin/rss/list' )}</li>{else}<li class="exp-badge is-muted">{'Inactive'|i18n( 'design/admin/rss/list' )}</li>{/if}
                <li class="exp-badge is-info">{if $info}{$info.format|wash}{else}{$export.rss_version|wash}{/if}</li>
                {if and( $info, $info.attention )}<li class="exp-badge is-warn">{'Needs attention'|i18n( 'design/admin/rss/list' )}</li>{/if}
            </ul>
        </div>
        <div class="exp-actions">
            {if and( $export.active|eq( 1 ), $export.access_url|ne( '' ) )}
            <a class="exp-btn exp-btn-small" href="{if and( $info, $info.feed_url|ne( '' ) )}{$info.feed_url|wash}{else}{concat( 'rss/feed/', $export.access_url )|ezurl( 'no' )}{/if}" target="_blank" rel="noopener" aria-describedby="{$card_id}-title">{'Open feed'|i18n( 'design/admin/rss/list' )}</a>
            {/if}
            <a class="exp-btn exp-btn-small" href={concat( 'rss/edit_export/', $export.id )|ezurl} aria-describedby="{$card_id}-title" title="{'Edit the <%name> RSS export.'|i18n( 'design/admin/rss/list',, hash( '%name', $export.title ) )|wash}">{'Edit'|i18n( 'design/admin/rss/list' )}</a>
        </div>
    </div>
    <dl class="exp-facts">
        <div class="exp-field-wide" style="grid-column: 1 / -1;">
            <dt>{'Feed address'|i18n( 'design/admin/rss/list' )}</dt>
            <dd class="exp-url">{if $export.access_url|ne( '' )}<code>{if and( $info, $info.feed_url|ne( '' ) )}{$info.feed_url|wash}{else}rss/feed/{$export.access_url|wash}{/if}</code>{else}<span class="exp-muted">{'not set'|i18n( 'design/admin/rss/list' )}</span>{/if}</dd>
        </div>
        {if $info}
        <div>
            <dt>{if $info.is_opml}{'Feeds listed'|i18n( 'design/admin/rss/list' )}{else}{'Sources'|i18n( 'design/admin/rss/list' )}{/if}</dt>
            <dd>{if $info.source_count|eq( 0 )}{'None'|i18n( 'design/admin/rss/list' )}{elseif $info.is_opml}{$info.source_count}{else}
                <ul class="exp-sources">
                {foreach $info.sources as $source}
                    <li>{if $source.found}<a href={$source.url|ezurl}>{$source.name|wash}</a>{else}<span class="exp-muted">{'Location %id (missing)'|i18n( 'design/admin/rss/list',, hash( '%id', $source.node_id ) )}</span>{/if}{if $source.class_name|ne( '' )} <span class="exp-meta">({$source.class_name|wash}{if $source.subnodes}, {'with subitems'|i18n( 'design/admin/rss/list' )}{/if})</span>{/if}</li>
                {/foreach}
                {if $info.more_sources|gt( 0 )}<li class="exp-meta">{'and %count more'|i18n( 'design/admin/rss/list',, hash( '%count', $info.more_sources ) )}</li>{/if}
                </ul>{/if}</dd>
        </div>
        {if $info.is_opml|not}
        <div>
            <dt>{'Items in the feed'|i18n( 'design/admin/rss/list' )}</dt>
            <dd>{'up to %count'|i18n( 'design/admin/rss/list',, hash( '%count', $info.number_of_objects ) )}{if $info.main_node_only}<span class="exp-meta">{'main locations only'|i18n( 'design/admin/rss/list' )}</span>{/if}</dd>
        </div>
        {/if}
        <div>
            <dt>{'Last written'|i18n( 'design/admin/rss/list' )}</dt>
            <dd>{if $export.active|ne( 1 )}<span class="exp-muted">{'Not served while inactive'|i18n( 'design/admin/rss/list' )}</span>{elseif eq( $cache_time, 0 )}{'On every request'|i18n( 'design/admin/rss/list' )}{elseif $info.last_generated|gt( 0 )}{$info.last_generated|l10n( shortdatetime )}{else}<span class="exp-muted">{'Not requested since the cache was cleared'|i18n( 'design/admin/rss/list' )}</span>{/if}</dd>
        </div>
        {if $info.site_access|ne( '' )}
        <div>
            <dt>{'Links point to'|i18n( 'design/admin/rss/list' )}</dt>
            <dd><code>{$info.site_access|wash}</code></dd>
        </div>
        {/if}
        {/if}
        <div>
            <dt>{'Modified'|i18n( 'design/admin/rss/list' )}</dt>
            <dd>{$export.modified|l10n( shortdatetime )}{if $export.modifier.contentobject}<span class="exp-meta">{'by %name'|i18n( 'design/admin/rss/list',, hash( '%name', $export.modifier.contentobject.name|wash ) )}</span>{/if}</dd>
        </div>
    </dl>
    {if and( $info, $info.warnings|count )}
    <div class="exp-warnings"><ul>{foreach $info.warnings as $warning}<li>{$warning|wash}</li>{/foreach}</ul></div>
    {/if}
</li>
    {undef $info $card_id}
{/foreach}
</ul>
<p class="exp-empty exp-no-match" hidden>{'No export on this page matches. Clear the search or choose All.'|i18n( 'design/admin/rss/list' )}</p>
{/if}

<div class="exp-listfoot exp-pager">
{include uri='design:rss/pagination.tpl' pager=$rssexport_pager page_uri='/rss/list'}
</div>

<div class="exp-bottombar">
    <div class="exp-actions">
        <button type="submit" class="exp-btn exp-btn-outline-danger" name="RemoveExportButton" value="1" aria-describedby="rss-export-remove-help" title="{'Remove selected RSS exports.'|i18n( 'design/admin/rss/list' )}"{if $rssexport_list|count|eq( 0 )} disabled="disabled"{/if}>{'Remove selected'|i18n( 'design/admin/rss/list' )}</button>
        <button type="submit" class="exp-btn exp-btn-primary" name="NewExportButton" value="1" title="{'Create a new RSS export.'|i18n( 'design/admin/rss/list' )}"><svg width="14" height="14" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M7 2h2v5h5v2H9v5H7V9H2V7h5z"/></svg>{'New export'|i18n( 'design/admin/rss/list' )}</button>
    </div>
    <p class="exp-meta" id="rss-export-remove-help">{'Remove selected asks for confirmation first and says which feed addresses stop working.'|i18n( 'design/admin/rss/list' )} <span class="exp-selected-count" data-for="DeleteIDArray[]" aria-live="polite"></span></p>
</div>
</section>
</form>

{* ---- Imports ---- *}
<form name="rssimportslist" method="post" action={'rss/list'|ezurl}>
<section class="exp-part" aria-labelledby="rss-import-title">
<div class="exp-subhead">
    <h2 class="exp-h2" id="rss-import-title">{'RSS imports (%imports_count)'|i18n( 'design/admin/rss/list',, hash( '%imports_count', $rssimport_count ) )}</h2>
    <p class="exp-sortby">
        <span>{'Sort by'|i18n( 'design/admin/rss/list' )}:</span>
        {foreach hash( 'name', 'Name'|i18n( 'design/admin/rss/list' ),
                       'modified', 'Modified'|i18n( 'design/admin/rss/list' ),
                       'active', 'Status'|i18n( 'design/admin/rss/list' ),
                       'url', 'Source URL'|i18n( 'design/admin/rss/list' ),
                       'id', 'ID'|i18n( 'design/admin/rss/list' ) ) as $sort_key => $sort_label}
            {if eq( $rssimport_sort.field, $sort_key )}
        <a class="current" href={concat( '/rss/list/(importsort)/', $sort_key, '/(importdir)/', $rssimport_sort.opposite, $rssimport_sort.suffix )|ezurl} aria-current="true" title="{'Sort by %column'|i18n( 'design/admin/parts/sortheader',, hash( '%column', $sort_label ) )|wash}">{$sort_label|wash} {if eq( $rssimport_sort.direction, 'asc' )}&#9650;{else}&#9660;{/if}</a>
            {else}
        <a href={concat( '/rss/list/(importsort)/', $sort_key, '/(importdir)/asc', $rssimport_sort.suffix )|ezurl} title="{'Sort by %column'|i18n( 'design/admin/parts/sortheader',, hash( '%column', $sort_label ) )|wash}">{$sort_label|wash}</a>
            {/if}
        {/foreach}
    </p>
    <p>{'Each import reads one feed of another site and creates an object below its destination for every item it has not seen before. Removing an import keeps the objects it created.'|i18n( 'design/admin/rss/list' )}</p>
</div>

{if $cronjob}
    {if $cronjob.found|not}
<div class="exp-statusbar is-bad" role="status"><p>{'No cronjob part runs rssimport.php, so no import is ever read. Add Scripts[]=rssimport.php to a part in cronjob.ini.'|i18n( 'design/admin/rss/list' )}</p>
    <div class="exp-actions"><a class="exp-btn exp-btn-small" href={'setup/cronjobs'|ezurl}>{'Go to the cronjobs page'|i18n( 'design/admin/rss/list' )}</a></div></div>
    {else}
<div class="exp-statusbar {if $cronjob.scheduled}is-ok{else}is-warn{/if}" role="status"><p>
    {'Active imports are read by the rssimport cronjob (%script) in the %part part:'|i18n( 'design/admin/rss/list',, hash( '%script', 'rssimport.php', '%part', $cronjob.label|wash ) )}
    {if $cronjob.scheduled}<strong>{if $cronjob.schedule_text|ne( '' )}{$cronjob.schedule_text|wash}{else}{'scheduled'|i18n( 'design/admin/rss/list' )}{/if}</strong>{if $cronjob.next_run|gt( 0 )}, {'next run %time'|i18n( 'design/admin/rss/list',, hash( '%time', $cronjob.next_run|l10n( shortdatetime ) ) )}{/if}.
    {else}<strong>{'not scheduled in the crontab'|i18n( 'design/admin/rss/list' )}</strong>. {'Imports are read only when that part is started by hand.'|i18n( 'design/admin/rss/list' )}{/if}</p>
    <div class="exp-actions"><a class="exp-btn exp-btn-small" href="{'setup/cronjobs'|ezurl( 'no' )}#{$cronjob.anchor|wash}">{'Open the cronjob'|i18n( 'design/admin/rss/list' )}</a></div></div>
    {/if}
{/if}

{if $rssimport_list|count|eq( 0 )}
<p class="exp-empty">{'There are no RSS imports. Create one with New import: give it the address of a feed, choose where its items go and which class they become.'|i18n( 'design/admin/rss/list' )}</p>
{else}
<label class="exp-meta exp-js-only" hidden><input type="checkbox" id="rss-import-select-all" data-select-all="DeleteIDArrayImport[]" /> {'Select all on this page'|i18n( 'design/admin/rss/list' )}</label>
<ul class="exp-secs" id="rss-import-list" data-list="1">
{foreach $rssimport_list as $import}
    {def $info = first_set( $import_info[$import.id], false() )
         $card_id = concat( 'rss-import-', $import.id )}
<li class="exp-sec{if and( $info, $info.attention )} is-attention{/if}" id="{$card_id}" data-active="{if $import.active|eq( 1 )}1{else}0{/if}" data-attention="{if and( $info, $info.attention )}1{else}0{/if}"
    data-search="{if $info}{$info.search|wash}{else}{concat( $import.name, ' ', $import.url, ' ', $import.id )|downcase|wash}{/if}">
    <div class="exp-sec-head">
        <div class="exp-sec-title">
            <label class="exp-select" title="{'Select RSS import for removal.'|i18n( 'design/admin/rss/list' )}">
                <input type="checkbox" name="DeleteIDArrayImport[]" value="{$import.id}" aria-label="{'Select %name for removal'|i18n( 'design/admin/rss/list',, hash( '%name', $import.name ) )|wash}" />
            </label>
            <h3 id="{$card_id}-title"><a href={concat( 'rss/edit_import/', $import.id )|ezurl}>{$import.name|wash}</a></h3>
            <span class="exp-meta">{'ID %id'|i18n( 'design/admin/rss/list',, hash( '%id', $import.id ) )}</span>
            <ul class="exp-badges">
                {if $import.active|eq( 1 )}<li class="exp-badge is-ok">{'Active'|i18n( 'design/admin/rss/list' )}</li>{else}<li class="exp-badge is-muted">{'Inactive'|i18n( 'design/admin/rss/list' )}</li>{/if}
                {if and( $info, $info.attention )}<li class="exp-badge is-warn">{'Needs attention'|i18n( 'design/admin/rss/list' )}</li>{/if}
            </ul>
        </div>
        <div class="exp-actions">
            {if $info}{if $info.destination_url|ne( '' )}<a class="exp-btn exp-btn-small" href={$info.destination_url|ezurl} aria-describedby="{$card_id}-title">{'View destination'|i18n( 'design/admin/rss/list' )}</a>{/if}{/if}
            <a class="exp-btn exp-btn-small" href={concat( 'rss/edit_import/', $import.id )|ezurl} aria-describedby="{$card_id}-title" title="{'Edit the <%name> RSS import.'|i18n( 'design/admin/rss/list',, hash( '%name', $import.name ) )|wash}">{'Edit'|i18n( 'design/admin/rss/list' )}</a>
        </div>
    </div>
    <dl class="exp-facts">
        <div style="grid-column: 1 / -1;">
            <dt>{'Source URL'|i18n( 'design/admin/rss/list' )}</dt>
            <dd class="exp-url">{if $import.url|ne( '' )}<code>{$import.url|wash}</code>{else}<span class="exp-muted">{'not set'|i18n( 'design/admin/rss/list' )}</span>{/if}</dd>
        </div>
        {if $info}
        <div>
            <dt>{'Destination'|i18n( 'design/admin/rss/list' )}</dt>
            <dd>{if $info.destination_url|ne( '' )}<a href={$info.destination_url|ezurl}>{$info.destination_name|wash}</a>{elseif $info.destination_node_id|gt( 0 )}<span class="exp-muted">{'Location %id (missing)'|i18n( 'design/admin/rss/list',, hash( '%id', $info.destination_node_id ) )}</span>{else}<span class="exp-muted">{'not set'|i18n( 'design/admin/rss/list' )}</span>{/if}</dd>
        </div>
        <div>
            <dt>{'Creates'|i18n( 'design/admin/rss/list' )}</dt>
            <dd>{if $info.class_name|ne( '' )}<a href={concat( 'class/view/', $info.class_id )|ezurl}>{$info.class_name|wash}</a>{else}<span class="exp-muted">{'not set'|i18n( 'design/admin/rss/list' )}</span>{/if}{if $info.owner_name|ne( '' )}<span class="exp-meta">{'owned by %name'|i18n( 'design/admin/rss/list',, hash( '%name', $info.owner_name|wash ) )}</span>{/if}</dd>
        </div>
        <div>
            <dt>{'Imported'|i18n( 'design/admin/rss/list' )}</dt>
            <dd>{if $info.imported_count|eq( 0 )}{'Nothing yet'|i18n( 'design/admin/rss/list' )}{else}{'%count objects'|i18n( 'design/admin/rss/list',, hash( '%count', $info.imported_count ) )}{/if}</dd>
        </div>
        <div>
            <dt>{'Newest item'|i18n( 'design/admin/rss/list' )}</dt>
            <dd>{if $info.newest_import|gt( 0 )}{$info.newest_import|l10n( shortdatetime )}{else}<span class="exp-muted">{'none'|i18n( 'design/admin/rss/list' )}</span>{/if}</dd>
        </div>
        {/if}
        <div>
            <dt>{'Modified'|i18n( 'design/admin/rss/list' )}</dt>
            <dd>{$import.modified|l10n( shortdatetime )}{if $import.modifier.contentobject}<span class="exp-meta">{'by %name'|i18n( 'design/admin/rss/list',, hash( '%name', $import.modifier.contentobject.name|wash ) )}</span>{/if}</dd>
        </div>
    </dl>
    {if and( $info, $info.warnings|count )}
    <div class="exp-warnings"><ul>{foreach $info.warnings as $warning}<li>{$warning|wash}</li>{/foreach}</ul></div>
    {/if}
</li>
    {undef $info $card_id}
{/foreach}
</ul>
<p class="exp-empty exp-no-match" hidden>{'No import on this page matches. Clear the search or choose All.'|i18n( 'design/admin/rss/list' )}</p>
{/if}

<div class="exp-listfoot exp-pager">
{include uri='design:rss/pagination.tpl' pager=$rssimport_pager page_uri='/rss/list'}
</div>

<div class="exp-bottombar">
    <div class="exp-actions">
        <button type="submit" class="exp-btn exp-btn-outline-danger" name="RemoveImportButton" value="1" aria-describedby="rss-import-remove-help" title="{'Remove selected RSS imports.'|i18n( 'design/admin/rss/list' )}"{if $rssimport_list|count|eq( 0 )} disabled="disabled"{/if}>{'Remove selected'|i18n( 'design/admin/rss/list' )}</button>
        <button type="submit" class="exp-btn exp-btn-primary" name="NewImportButton" value="1" title="{'Create a new RSS import.'|i18n( 'design/admin/rss/list' )}"><svg width="14" height="14" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M7 2h2v5h5v2H9v5H7V9H2V7h5z"/></svg>{'New import'|i18n( 'design/admin/rss/list' )}</button>
    </div>
    <p class="exp-meta" id="rss-import-remove-help">{'Remove selected asks for confirmation first. The objects an import created stay in the content tree.'|i18n( 'design/admin/rss/list' )} <span class="exp-selected-count" data-for="DeleteIDArrayImport[]" aria-live="polite"></span></p>
</div>
</section>
</form>

</div></div></div>
</div>

{undef $export_info $import_info $summary $cronjob $feedback $cache_time}
{include uri='design:rss/exp_list_script.tpl' text_shown='%shown of %count feeds on this page shown'|i18n( 'design/admin/rss/list' ) text_all='Feeds on this page: %count'|i18n( 'design/admin/rss/list' ) text_selected='%count selected.'|i18n( 'design/admin/rss/list' )}
