{* exphelp/manual[/<remote_id>] (doc/eng-us/features/6.0/help-content.md): the manuals of help maintained as content, or one
   manual with its table of contents (chapters and topics the user may see; not Released ones only for those who may
   edit them, marked as a preview).

   Variables (kernel/private/classes/views/exphelp/manual.php): help_manual (false for the list; else title, subtitle,
   shortdesc, edition, license, authors[], preview), help_manual_toc (nested: kind chapter|topic, title, shortdesc,
   url, preview, children), help_manuals (title, subtitle, shortdesc, remote_id, edition, preview), help_ebook_formats (epub, pdf), help_dita (the DITA download), help_ebooks_stored (format, name, url, size, modified,
   version, node_url: the e-books stored as file objects), help_json_url,
   help_i18n_json.

   The same file is in design/admin and design/admin4. *}
<div class="a4-page a4-help-frame a4-help-manual" data-exphelp data-exphelp-i18n="{$help_i18n_json|wash}">

    <div class="a4-help-viewer-bar">
        <div class="a4-help-viewer-line" data-exphelp-line>
            <a class="a4-btn a4-btn-ghost a4-btn-small a4-help-viewer-start" href={'exphelp/dashboard'|ezurl}><span class="a4-icon a4-bi-signpost-split" aria-hidden="true"></span> {'Help Start'|i18n( 'kernel/exphelp' )}</a>
            {if and( is_set( $help_reading ), $help_reading )}{include uri='design:exphelp/parts/reading_language.tpl' reading=$help_reading prefix='exphelp-manual-reading'}{/if}
        </div>
        {if $help_manual}
        <a class="a4-btn a4-btn-ghost a4-btn-small" href={'exphelp/manual'|ezurl}><span class="a4-icon a4-bi-journal-text" aria-hidden="true"></span> {'Manuals'|i18n( 'kernel/exphelp' )}</a>
        {/if}
    </div>

    {if $help_manual}
    <header class="a4-help-pagehead">
        <h1 class="a4-help-title" dir="auto">{$help_manual.title|wash}</h1>
        {if $help_manual.subtitle|ne( '' )}<p class="a4-help-summary" dir="auto">{$help_manual.subtitle|wash}</p>{/if}
        <ul class="a4-help-meta" role="list">
            {if $help_manual.preview}
            <li class="a4-help-meta-item"><span class="a4-tag is-warn"><span class="a4-icon a4-bi-eye" aria-hidden="true"></span> {'Preview: %state, not delivered to readers'|i18n( 'kernel/exphelp',, hash( '%state', $help_manual.state ) )|wash}</span></li>
            {/if}
            {if $help_manual.audience|count}
            <li class="a4-help-meta-item is-audience"><span class="a4-sr-only">{'For:'|i18n( 'kernel/exphelp' )}</span>{foreach $help_manual.audience as $help_a}<span class="a4-tag a4-help-pill">{$help_a|wash}</span> {/foreach}</li>
            {/if}
            {if $help_manual.edition|ne( '' )}<li class="a4-help-meta-item"><span class="a4-icon a4-bi-tag" aria-hidden="true"></span>{'Edition %edition'|i18n( 'kernel/exphelp',, hash( '%edition', $help_manual.edition ) )|wash}</li>{/if}
            {if $help_manual.license|ne( '' )}<li class="a4-help-meta-item">{$help_manual.license|wash}</li>{/if}
            {foreach $help_ebook_formats as $help_fmt}
            <li class="a4-help-meta-item"><a class="a4-btn a4-btn-small" href={concat( 'exphelp/manual/', $help_manual.remote_id, '?format=', $help_fmt )|ezurl} download><span class="a4-icon a4-bi-download" aria-hidden="true"></span> {cond( eq( $help_fmt, 'pdf' ), 'Download PDF'|i18n( 'kernel/exphelp' ), 'Download EPUB'|i18n( 'kernel/exphelp' ) )}</a></li>
            {/foreach}
            {if $help_dita}
            <li class="a4-help-meta-item"><a class="a4-btn a4-btn-small a4-btn-ghost" href={concat( 'exphelp/manual/', $help_manual.remote_id, '?format=dita' )|ezurl} download><span class="a4-icon a4-bi-file-earmark-code" aria-hidden="true"></span> {'Download DITA'|i18n( 'kernel/exphelp' )}</a></li>
            {/if}
            <li class="a4-help-meta-item"><a class="a4-btn a4-btn-small a4-btn-ghost" href={$help_json_url|ezurl}><span class="a4-icon a4-bi-file-earmark-code" aria-hidden="true"></span> JSON</a></li>
        </ul>
        {if $help_manual.shortdesc|ne( '' )}<p dir="auto">{$help_manual.shortdesc|wash}</p>{/if}
    </header>

    {if $help_ebooks_stored|count}
    <section class="a4-card" aria-labelledby="exphelp-manual-ebooks">
        <h2 class="a4-help-section-head" id="exphelp-manual-ebooks">{'E-books'|i18n( 'kernel/exphelp' )}</h2>
        <p class="a4-meta">{'Built from the released topics each time one of them is released, and kept with their earlier versions.'|i18n( 'kernel/exphelp' )}</p>
        <div class="a4-table-wrap" role="region" aria-labelledby="exphelp-manual-ebooks" tabindex="0">
        <table class="a4-table">
            <thead><tr><th scope="col">{'File'|i18n( 'kernel/exphelp' )}</th><th scope="col">{'Version'|i18n( 'kernel/exphelp' )}</th><th scope="col">{'Size'|i18n( 'kernel/exphelp' )}</th><th scope="col">{'Updated'|i18n( 'kernel/exphelp' )}</th></tr></thead>
            <tbody>
            {foreach $help_ebooks_stored as $help_book}
            <tr>
                <td><a href={$help_book.url|ezurl}><span class="a4-icon {cond( eq( $help_book.format, 'pdf' ), 'a4-bi-file-earmark-pdf', 'a4-bi-file-earmark' )}" aria-hidden="true"></span> {$help_book.name|wash}</a></td>
                <td>{if $help_book.node_url|ne( '' )}<a href={$help_book.node_url|ezurl}>{$help_book.version|wash}</a>{else}{$help_book.version|wash}{/if}</td>
                <td>{$help_book.size|si( byte )}</td>
                <td>{$help_book.modified|l10n( shortdatetime )}</td>
            </tr>
            {/foreach}
            </tbody>
        </table>
        </div>
    </section>
    {/if}

    <section class="a4-card" aria-labelledby="exphelp-manual-toc">
        <h2 class="a4-help-section-head" id="exphelp-manual-toc">{'Contents'|i18n( 'kernel/exphelp' )}</h2>
        {if $help_manual_toc|count}
        <ol class="a4-help-manual-toc">
        {foreach $help_manual_toc as $toc_e}
            <li>
            {if eq( $toc_e.kind, 'chapter' )}
                <strong dir="auto">{$toc_e.title|wash}</strong>{if $toc_e.preview} <span class="a4-tag is-warn">{'Preview'|i18n( 'kernel/exphelp' )}</span>{/if}
                {if $toc_e.shortdesc|ne( '' )}<br /><span class="a4-meta">{$toc_e.shortdesc|wash}</span>{/if}
                {if $toc_e.children|count}
                <ol>
                {foreach $toc_e.children as $toc_c}
                    <li>{if eq( $toc_c.kind, 'chapter' )}<strong dir="auto">{$toc_c.title|wash}</strong>{else}<a href={$toc_c.url|ezurl} dir="auto">{$toc_c.title|wash}</a>{/if}{if $toc_c.preview} <span class="a4-tag is-warn">{'Preview'|i18n( 'kernel/exphelp' )}</span>{/if}
                        {if $toc_c.shortdesc|ne( '' )}<br /><span class="a4-meta">{$toc_c.shortdesc|wash}</span>{/if}</li>
                {/foreach}
                </ol>
                {/if}
            {else}
                <a href={$toc_e.url|ezurl} dir="auto">{$toc_e.title|wash}</a>{if $toc_e.preview} <span class="a4-tag is-warn">{'Preview'|i18n( 'kernel/exphelp' )}</span>{/if}
                {if $toc_e.shortdesc|ne( '' )}<br /><span class="a4-meta">{$toc_e.shortdesc|wash}</span>{/if}
            {/if}
            </li>
        {/foreach}
        </ol>
        {else}
        <p class="a4-meta">{'This manual has no topics you can read yet.'|i18n( 'kernel/exphelp' )}</p>
        {/if}
    </section>
    {else}
    <header class="a4-help-pagehead">
        <h1 class="a4-help-title">{'Manuals'|i18n( 'kernel/exphelp' )}</h1>
        {if and( $help_dita, $help_manuals|count )}
        <ul class="a4-help-meta" role="list">
            <li class="a4-help-meta-item"><a class="a4-btn a4-btn-small a4-btn-ghost" href={'exphelp/manual?format=dita'|ezurl} download><span class="a4-icon a4-bi-file-earmark-code" aria-hidden="true"></span> {'Download all as DITA'|i18n( 'kernel/exphelp' )}</a></li>
        </ul>
        {/if}
    </header>
    {if $help_manuals|count}
    <ul class="a4-help-featured">
    {foreach $help_manuals as $help_m}
        <li class="a4-help-feature">
            <a href={concat( 'exphelp/manual/', $help_m.remote_id )|ezurl}>
                <span class="a4-help-tile is-small" aria-hidden="true"><span class="a4-icon a4-bi-journal-text"></span></span>
                <span class="a4-help-feature-text">
                    <span class="a4-help-feature-title">{$help_m.title|wash}</span>
                    {if $help_m.shortdesc|ne( '' )}<span class="a4-help-feature-summary">{$help_m.shortdesc|shorten( 160 )|wash}</span>{/if}
                    {if $help_m.preview}<span class="a4-help-feature-meta"><span class="a4-tag is-warn">{'Preview'|i18n( 'kernel/exphelp' )}</span></span>{/if}
                </span>
            </a>
        </li>
    {/foreach}
    </ul>
    {else}
    <p class="a4-meta">{'There are no manuals you can read yet.'|i18n( 'kernel/exphelp' )}</p>
    {/if}
    {/if}
</div>
