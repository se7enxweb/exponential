{* The PDF exports (pdf/list): what an export is, an overview, a search, a filter, an order and pages, then one card
   per export with its source node and whether it still exists, how it is produced, its stored file (size and date),
   Download, Regenerate and Edit, and what needs attention. Remove selected leads to a confirmation
   (design:pdf/confirmremove.tpl) that says what goes.

   The same file is in design/admin and design/admin4. The cards come from the view (pdfexport_info,
   pdfexport_summary, pdfexport_feedback, pdfexport_sort, pdfexport_filter_links, pdfexport_limit_links,
   pdfexport_search ...; worked out by expPDFExportInfo); without them (an older view class) each card shows what
   the export row holds. The variables the page always had are still set: pdfexport_list, pdfexport_count, limit,
   view_parameters. The form fields and buttons are the same: DeleteIDArray[], RemoveExportButton, NewPDFExport.
   Everything works without javascript; the script only adds "select all" and the selection count.
   Guide: doc/guides/pdf-exports.md *}
{include uri='design:pdf/exp_style.tpl'}

{def $infos = first_set( $pdfexport_info, hash() )
     $summary = first_set( $pdfexport_summary, false() )
     $feedback = first_set( $pdfexport_feedback, false() )
     $search = first_set( $pdfexport_search, '' )
     $filter = first_set( $pdfexport_filter, '' )
     $sort = first_set( $pdfexport_sort, false() )}

<div class="context-block exp-lists exp-pdf">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'PDF exports (%export_count)'|i18n( 'design/admin/pdf/list',, hash( '%export_count', cond( $summary, $summary.total, $pdfexport_count ) ) )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'A PDF export turns a node of the content tree, and if wanted the nodes below it, into one PDF document with an optional front page and a table of contents. Generated once, the file is stored and downloaded from here until it is regenerated; generated on the fly, it is made anew for every download.'|i18n( 'design/admin/pdf/list' )}</p>

{if $feedback}
    {switch match=$feedback.type}
    {case match='removed'}
<div class="exp-feedback is-ok" role="status"><p>{'Removed: %names.'|i18n( 'design/admin/pdf/list',, hash( '%names', $feedback.names|implode( ', ' ) ) )|wash}{if $feedback.files|count} {'Stored files removed: %files.'|i18n( 'design/admin/pdf/list',, hash( '%files', $feedback.files|implode( ', ' ) ) )|wash}{/if}</p></div>
    {/case}
    {case match='stored'}
<div class="exp-feedback {if and( is_set( $feedback.generated ), $feedback.generated|not )}is-warn{else}is-ok{/if}" role="status"><p>
    {if $feedback.is_new}{'The PDF export %name was created.'|i18n( 'design/admin/pdf/list',, hash( '%name', $feedback.name ) )|wash}{else}{'The PDF export %name was saved.'|i18n( 'design/admin/pdf/list',, hash( '%name', $feedback.name ) )|wash}{/if}
    {if is_set( $feedback.generated )}{if $feedback.generated}{'Its file was generated (%size).'|i18n( 'design/admin/pdf/list',, hash( '%size', $feedback.size|si( byte, auto ) ) )|wash}{else}{include uri='design:pdf/exp_problem.tpl' problem=$feedback.problem}{/if}{else}{'It is generated on the fly for every download.'|i18n( 'design/admin/pdf/list' )}{/if}
</p></div>
    {/case}
    {case match='regenerated'}
<div class="exp-feedback is-ok" role="status"><p>{'The file of %name was generated anew (%size).'|i18n( 'design/admin/pdf/list',, hash( '%name', $feedback.name, '%size', $feedback.size|si( byte, auto ) ) )|wash}</p></div>
    {/case}
    {case match='generate_failed'}
<div class="exp-feedback is-bad" role="alert"><p><strong>{'%name could not be generated.'|i18n( 'design/admin/pdf/list',, hash( '%name', $feedback.name ) )|wash}</strong> {include uri='design:pdf/exp_problem.tpl' problem=$feedback.problem}</p></div>
    {/case}
    {case match='none_selected'}
<div class="exp-feedback is-warn" role="alert"><p>{'No export was selected. Tick the exports to remove first.'|i18n( 'design/admin/pdf/list' )}</p></div>
    {/case}
    {case match='gone'}
<div class="exp-feedback is-warn" role="alert"><p>{'The selected exports no longer exist; somebody may have removed them already.'|i18n( 'design/admin/pdf/list' )}</p></div>
    {/case}
    {case}{/case}
    {/switch}
{/if}

{if $summary}
<section aria-labelledby="pdf-overview-title">
<h2 class="exp-sr" id="pdf-overview-title">{'Overview'|i18n( 'design/admin/pdf/list' )}</h2>
<ul class="exp-figures">
    <li class="exp-figure"><strong>{$summary.total}</strong><span>{'Exports'|i18n( 'design/admin/pdf/list' )}</span></li>
    <li class="exp-figure"><strong>{$summary.stored}</strong><span>{'Generated once'|i18n( 'design/admin/pdf/list' )}</span></li>
    <li class="exp-figure"><strong>{$summary.onthefly}</strong><span>{'Generated on the fly'|i18n( 'design/admin/pdf/list' )}</span></li>
    <li class="exp-figure"><strong>{$summary.generated}</strong><span>{'Stored files'|i18n( 'design/admin/pdf/list' )}</span></li>
    <li class="exp-figure"><strong>{$summary.size|si( byte, auto )|wash}</strong><span>{'Stored in total'|i18n( 'design/admin/pdf/list' )}</span></li>
    <li class="exp-figure{if $summary.attention|gt( 0 )} is-attention{/if}"><strong>{$summary.attention}</strong><span>{'Need attention'|i18n( 'design/admin/pdf/list' )}</span></li>
</ul>
</section>
{if $summary.unfinished|gt( 0 )}
<div class="exp-statusbar is-info" role="status"><p>{'%count new exports were started and never saved. Their drafts are removed once the draft timeout has passed (content.ini [PDFExportSettings] DraftTimeout) and a new export is started.'|i18n( 'design/admin/pdf/list',, hash( '%count', $summary.unfinished ) )}</p></div>
{/if}
{/if}

{if $sort}
<div class="exp-controls">
    <form class="exp-search" method="get" action={$pdfexport_search_action|ezurl} role="search">
        <div class="exp-field">
            <label for="pdf-search">{'Find an export'|i18n( 'design/admin/pdf/list' )}</label>
            <input type="search" id="pdf-search" name="search" value="{$search|wash}" maxlength="100" autocomplete="off" spellcheck="false" aria-describedby="pdf-search-help" />
            <span class="exp-help" id="pdf-search-help">{'Title, file name, ID, source node or class. Every word must match.'|i18n( 'design/admin/pdf/list' )}</span>
        </div>
        <div class="exp-actions">
            <button type="submit" class="exp-btn">{'Search'|i18n( 'design/admin/pdf/list' )}</button>
            {if $search|ne( '' )}<a class="exp-btn" href={$pdfexport_search_action|ezurl}>{'Clear'|i18n( 'design/admin/pdf/list' )}</a>{/if}
        </div>
    </form>
    <div class="exp-row">
        <p class="exp-filters" aria-label="{'Show'|i18n( 'design/admin/pdf/list' )}">
            <span>{'Show'|i18n( 'design/admin/pdf/list' )}:</span>
            {foreach hash( 'all', 'All'|i18n( 'design/admin/pdf/list' ),
                           'stored', 'Generated once'|i18n( 'design/admin/pdf/list' ),
                           'onthefly', 'On the fly'|i18n( 'design/admin/pdf/list' ),
                           'generated', 'With a stored file'|i18n( 'design/admin/pdf/list' ),
                           'attention', 'Need attention'|i18n( 'design/admin/pdf/list' ) ) as $filter_key => $filter_label}
            {def $link = $pdfexport_filter_links[$filter_key]}
            <a href={$link.uri|ezurl}{if $link.current} class="current" aria-current="true"{/if}>{$filter_label|wash} <span class="exp-count">{$link.count}</span></a>
            {undef $link}
            {/foreach}
        </p>
        <p class="exp-sortby">
            <span>{'Sort by'|i18n( 'design/admin/pdf/list' )}:</span>
            {foreach hash( 'title', 'Title'|i18n( 'design/admin/pdf/list' ),
                           'modified', 'Modified'|i18n( 'design/admin/pdf/list' ),
                           'generated', 'Generated'|i18n( 'design/admin/pdf/list' ),
                           'size', 'File size'|i18n( 'design/admin/pdf/list' ),
                           'id', 'ID'|i18n( 'design/admin/pdf/list' ) ) as $sort_key => $sort_label}
            {def $link = $sort.links[$sort_key]}
            <a href={$link.uri|ezurl}{if $link.current} class="current" aria-current="true"{/if} title="{'Sort by %column'|i18n( 'design/admin/parts/sortheader',, hash( '%column', $sort_label ) )|wash}">{$sort_label|wash}{if $link.current} {if eq( $sort.direction, 'asc' )}&#9650;{else}&#9660;{/if}<span class="exp-sr">{if eq( $sort.direction, 'asc' )}{'ascending'|i18n( 'design/admin/pdf/list' )}{else}{'descending'|i18n( 'design/admin/pdf/list' )}{/if}</span>{/if}</a>
            {undef $link}
            {/foreach}
        </p>
    </div>
</div>
{/if}

<form name="pdfexportlist" method="post" action={'pdf/list'|ezurl}>

{if $pdfexport_list|count|eq( 0 )}
    {if or( $search|ne( '' ), $filter|ne( '' ) )}
<p class="exp-empty">{'No export matches. Clear the search or show all exports.'|i18n( 'design/admin/pdf/list' )}</p>
    {else}
<p class="exp-empty">{'There are no PDF exports yet. Create one with New PDF export: give it a title, choose the node it starts from and whether its file is stored or made for every download.'|i18n( 'design/admin/pdf/list' )}</p>
    {/if}
{else}
<label class="exp-meta exp-js-only" hidden><input type="checkbox" id="pdf-select-all" data-select-all="DeleteIDArray[]" /> {'Select all on this page'|i18n( 'design/admin/pdf/list' )}</label>
<ul class="exp-secs" id="pdf-export-list" data-list="1">
{foreach $pdfexport_list as $export}
    {def $info = first_set( $infos[$export.id], false() )
         $card_id = concat( 'pdf-export-', $export.id )}
<li class="exp-sec{if and( $info, $info.attention )} is-attention{/if}" id="{$card_id}" data-search="{$export.title|downcase|wash}">
    <div class="exp-sec-head">
        <div class="exp-sec-title">
            <label class="exp-select" title="{'Select PDF export for removal.'|i18n( 'design/admin/pdf/list' )}">
                <input type="checkbox" name="DeleteIDArray[]" value="{$export.id}" aria-label="{'Select %name for removal'|i18n( 'design/admin/pdf/list',, hash( '%name', $export.title ) )|wash}" />
            </label>
            <h3 id="{$card_id}-title"><a href={concat( 'pdf/edit/', $export.id )|ezurl}>{$export.title|wash}</a></h3>
            <span class="exp-meta">{'ID %id'|i18n( 'design/admin/pdf/list',, hash( '%id', $export.id ) )}</span>
            <ul class="exp-badges">
                {if $export.status|eq( 2 )}<li class="exp-badge is-info">{'On the fly'|i18n( 'design/admin/pdf/list' )}</li>{else}<li class="exp-badge is-info">{'Generated once'|i18n( 'design/admin/pdf/list' )}</li>{/if}
                {if $info}
                    {if $info.is_stored}{if $info.file_exists}<li class="exp-badge is-ok">{'File ready'|i18n( 'design/admin/pdf/list' )}</li>{else}<li class="exp-badge is-warn">{'No file'|i18n( 'design/admin/pdf/list' )}</li>{/if}{/if}
                    {if $info.source_exists|not}<li class="exp-badge is-bad">{'Source missing'|i18n( 'design/admin/pdf/list' )}</li>{/if}
                    {if $info.draft}<li class="exp-badge is-muted">{if $info.draft_by|ne( '' )}{'Open in the editor by %name'|i18n( 'design/admin/pdf/list',, hash( '%name', $info.draft_by ) )|wash}{else}{'Open in the editor'|i18n( 'design/admin/pdf/list' )}{/if}</li>{/if}
                {/if}
            </ul>
        </div>
        <div class="exp-actions">
            {if or( $info|not, $info.can_download )}
            <a class="exp-btn exp-btn-small" href={concat( 'pdf/edit/', $export.id, '/generate' )|ezurl} aria-describedby="{$card_id}-title" title="{if $export.status|eq( 2 )}{'Generate the PDF now and download it.'|i18n( 'design/admin/pdf/list' )}{else}{'Download the stored file.'|i18n( 'design/admin/pdf/list' )}{/if}"><svg width="14" height="14" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M7 1h2v7.6l2.3-2.3 1.4 1.4L8 12.4 3.3 7.7l1.4-1.4L7 8.6zM2 13h12v2H2z"/></svg>{'Download'|i18n( 'design/admin/pdf/list' )}</a>
            {/if}
            {if and( $info, $info.is_stored )}
            <button type="submit" class="exp-btn exp-btn-small" name="RegenerateButton" value="{$export.id}" aria-describedby="{$card_id}-title{if $info.can_regenerate|not} {$card_id}-noregen{/if}"{if $info.can_regenerate|not} disabled="disabled"{/if} title="{'Generate the stored file anew from the current content.'|i18n( 'design/admin/pdf/list' )}">{'Regenerate'|i18n( 'design/admin/pdf/list' )}</button>
            {/if}
            <a class="exp-btn exp-btn-small" href={concat( 'pdf/edit/', $export.id )|ezurl} aria-describedby="{$card_id}-title" title="{'Edit the <%pdf_export_name> PDF export.'|i18n( 'design/admin/pdf/list',, hash( '%pdf_export_name', $export.title ) )|wash}">{'Edit'|i18n( 'design/admin/pdf/list' )}</a>
        </div>
    </div>
    {if $info}
    <dl class="exp-facts">
        <div>
            <dt>{'Source node'|i18n( 'design/admin/pdf/list' )}</dt>
            <dd>{if $info.source_exists}<a href={$info.source_url|ezurl}>{$info.source_name|wash}</a><span class="exp-meta">{$info.source_class|wash}, {'node %id'|i18n( 'design/admin/pdf/list',, hash( '%id', $info.source_node_id ) )}</span>{elseif $info.source_node_id|gt( 0 )}<span class="exp-muted">{'Node %id (no longer exists)'|i18n( 'design/admin/pdf/list',, hash( '%id', $info.source_node_id ) )}</span>{else}<span class="exp-muted">{'not chosen'|i18n( 'design/admin/pdf/list' )}</span>{/if}</dd>
        </div>
        <div>
            <dt>{'Contains'|i18n( 'design/admin/pdf/list' )}</dt>
            <dd>{if $info.is_tree}{'The source node and the nodes below it of these classes:'|i18n( 'design/admin/pdf/list' )}
                <span class="exp-meta">{if $info.classes|count}{foreach $info.classes as $class}{delimiter}, {/delimiter}{$class.name|wash}{/foreach}{else}{'none'|i18n( 'design/admin/pdf/list' )}{/if}</span>
                {else}{'The source node only'|i18n( 'design/admin/pdf/list' )}{/if}</dd>
        </div>
        <div>
            <dt>{if $info.is_stored}{'Stored file'|i18n( 'design/admin/pdf/list' )}{else}{'Download name'|i18n( 'design/admin/pdf/list' )}{/if}</dt>
            <dd>{if $info.is_stored}
                    <span class="exp-file"><code>{$info.file_name|wash}</code></span>
                    {if $info.file_exists}<span class="exp-meta">{'%size, generated %date'|i18n( 'design/admin/pdf/list',, hash( '%size', $info.file_size|si( byte, auto ), '%date', $info.file_mtime|l10n( shortdatetime ) ) )|wash}</span>
                    {else}<span class="exp-meta">{'not generated'|i18n( 'design/admin/pdf/list' )}</span>{/if}
                {else}<span class="exp-file"><code>{$info.download_name|wash}</code></span><span class="exp-meta">{'made anew for every download'|i18n( 'design/admin/pdf/list' )}</span>{/if}</dd>
        </div>
        <div>
            <dt>{'Front page'|i18n( 'design/admin/pdf/list' )}</dt>
            <dd>{if $info.show_frontpage}{'Yes'|i18n( 'design/admin/pdf/list' )}{else}{'No'|i18n( 'design/admin/pdf/list' )}{/if}</dd>
        </div>
        <div>
            <dt>{'Modified'|i18n( 'design/admin/pdf/list' )}</dt>
            <dd>{$export.modified|l10n( shortdatetime )}{if $info.modifier_name|ne( '' )}<span class="exp-meta">{'by %name'|i18n( 'design/admin/pdf/list',, hash( '%name', $info.modifier_name ) )|wash}</span>{/if}</dd>
        </div>
    </dl>
    {if $info.warnings|count}
    <div class="exp-warnings" id="{$card_id}-noregen"><ul>{foreach $info.warnings as $warning}<li>{$warning|wash}</li>{/foreach}</ul></div>
    {/if}
    {else}
    <dl class="exp-facts">
        <div><dt>{'Modified'|i18n( 'design/admin/pdf/list' )}</dt><dd>{$export.modified|l10n( shortdatetime )}</dd></div>
    </dl>
    {/if}
</li>
    {undef $info $card_id}
{/foreach}
</ul>
{/if}

{* The pager. The size is admininterface.ini [PaginationSettings] ItemsPerPage[pdf/list]; the links below pick
   another one, which is remembered. *}
<div class="exp-listfoot">
    {if is_set( $pdfexport_limit_links )}
    <p class="exp-sizes">
        <span>{'Per page'|i18n( 'design/admin/pdf/list' )}:</span>
    {foreach $pdfexport_limit_links as $size_link}
        {if $size_link.current}<span class="current" aria-current="true">{$size_link.limit}</span>
        {else}<a href={$size_link.uri|ezurl} title="{'Show %count exports per page.'|i18n( 'design/admin/pdf/list',, hash( '%count', $size_link.limit ) )}">{$size_link.limit}</a>{/if}
    {/foreach}
    </p>
    {/if}
    {if $pdfexport_count|gt( $limit )}
    <div class="exp-pager">
    {include name=PDFNavigator
             uri='design:navigator/google.tpl'
             page_uri='/pdf/list'
             page_uri_suffix=first_set( $pdfexport_pager_suffix, false() )
             item_count=$pdfexport_count
             view_parameters=first_set( $pdfexport_pager_parameters, $view_parameters )
             item_limit=$limit}
    </div>
    {/if}
</div>

<div class="exp-bottombar">
    <div class="exp-actions">
        <button type="submit" class="exp-btn exp-btn-outline-danger" name="RemoveExportButton" value="1" aria-describedby="pdf-remove-help" title="{'Remove selected PDF exports.'|i18n( 'design/admin/pdf/list' )}"{if $pdfexport_list|count|eq( 0 )} disabled="disabled"{/if}>{'Remove selected'|i18n( 'design/admin/pdf/list' )}</button>
        <button type="submit" class="exp-btn exp-btn-primary" name="NewPDFExport" value="1" title="{'Create a new PDF export.'|i18n( 'design/admin/pdf/list' )}"><svg width="14" height="14" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M7 2h2v5h5v2H9v5H7V9H2V7h5z"/></svg>{'New PDF export'|i18n( 'design/admin/pdf/list' )}</button>
    </div>
    <p class="exp-meta" id="pdf-remove-help">{'Remove selected asks for confirmation first and says which stored files go with the exports.'|i18n( 'design/admin/pdf/list' )} <span class="exp-selected-count" data-for="DeleteIDArray[]" aria-live="polite"></span></p>
</div>

</form>

</div></div></div>
</div>

{undef $infos $summary $feedback $search $filter $sort}
{include uri='design:pdf/exp_list_script.tpl' text_selected='%count selected.'|i18n( 'design/admin/pdf/list' )}
