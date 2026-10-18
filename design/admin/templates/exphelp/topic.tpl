{* exphelp/topic/<help_id> (doc/features/6.0/help-content.md): one help topic maintained as content, in the layout of
   exphelp/viewer.tpl: the topic in the middle (title, short description, its type, audiences and validity, the
   rendered fields, figures, related topics), the list of help topics on the left, its contents on the right.
   A topic that is not Released is shown only to those who may edit it, marked as a preview.

   Variables (kernel/private/classes/views/exphelp/topic.php): help_page (document: id, title, summary, topic_type,
   audience[], audience_names[], platform[], state, preview, version_from, version_to, mtime), help_topic, help_html
   (safe: NOT washed), help_toc, help_related (documents), help_figures (image_url, alt_text, caption), help_topics
   (documents), help_manuals (title, remote_id, preview), help_edit_url ('' or content/edit/<id>), help_json_url,
   help_i18n_json.

   The same file is in design/admin and design/admin4. *}
{def $help_toc_items = array()}
{foreach $help_toc as $help_t}{if and( $help_t.level|ge( 2 ), $help_t.level|le( 3 ) )}{set $help_toc_items = $help_toc_items|append( $help_t )}{/if}{/foreach}
<div class="a4-page a4-help a4-help-viewer a4-help-topic{if $help_toc_items|count|lt( 2 )} is-no-toc{/if}" data-exphelp data-exphelp-i18n="{$help_i18n_json|wash}">

    <div class="a4-help-viewer-bar">
        <a class="a4-btn a4-btn-ghost a4-btn-small" href={'exphelp/dashboard'|ezurl}><span class="a4-icon a4-bi-signpost-split" aria-hidden="true"></span> {'Help start'|i18n( 'kernel/exphelp' )}</a>
        <a class="a4-btn a4-btn-ghost a4-btn-small" href={'exphelp/manual'|ezurl}><span class="a4-icon a4-bi-journal-text" aria-hidden="true"></span> {'Manuals'|i18n( 'kernel/exphelp' )}</a>
        <form class="a4-help-viewer-search a4-help-search-box" data-exphelp-search action={'exphelp/search'|ezurl} method="get" role="search" data-exphelp-endpoint={'exphelp/search'|ezurl}>
            <label class="a4-sr-only" for="exphelp-topic-q">{'Search the help'|i18n( 'kernel/exphelp' )}</label>
            <input class="a4-input" type="search" id="exphelp-topic-q" name="q" value="" autocomplete="off" placeholder="{'Search the help'|i18n( 'kernel/exphelp' )|wash}" aria-controls="exphelp-topic-results" aria-keyshortcuts="/" data-exphelp-search-input />
            <button type="submit" class="a4-btn a4-btn-small"><span class="a4-icon a4-bi-search" aria-hidden="true"></span> {'Search'|i18n( 'kernel/exphelp' )}</button>
            <div id="exphelp-topic-results" data-exphelp-results aria-live="polite" hidden></div>
        </form>
    </div>

    <div class="a4-help-viewer-grid">

        <div class="a4-help-main">
            <header class="a4-help-pagehead">
                <h1 class="a4-help-title" dir="auto">{$help_page.title|wash}</h1>
                {if $help_page.summary|ne( '' )}
                <p class="a4-help-summary" dir="auto">{$help_page.summary|wash}</p>
                {/if}
                <ul class="a4-help-meta" role="list">
                    {if $help_page.preview}
                    <li class="a4-help-meta-item"><span class="a4-tag is-warn"><span class="a4-icon a4-bi-eye" aria-hidden="true"></span> {'Preview: %state, not delivered to readers'|i18n( 'kernel/exphelp',, hash( '%state', $help_page.state ) )|wash}</span></li>
                    {/if}
                    <li class="a4-help-meta-item"><span class="a4-tag is-accent"><span class="a4-sr-only">{'Topic type:'|i18n( 'kernel/exphelp' )} </span>{switch match=$help_page.topic_type}{case match='concept'}{'Concept'|i18n( 'kernel/exphelp' )}{/case}{case match='task'}{'Task'|i18n( 'kernel/exphelp' )}{/case}{case match='reference'}{'Reference'|i18n( 'kernel/exphelp' )}{/case}{case}{'Troubleshooting'|i18n( 'kernel/exphelp' )}{/case}{/switch}</span></li>
                    {if $help_page.audience_names|count}
                    <li class="a4-help-meta-item is-audience"><span class="a4-sr-only">{'For:'|i18n( 'kernel/exphelp' )}</span>
                        {foreach $help_page.audience_names as $help_a}<span class="a4-tag a4-help-pill">{$help_a|wash}</span> {/foreach}
                    </li>
                    {/if}
                    {if $help_page.platform|count}
                    <li class="a4-help-meta-item"><span class="a4-icon a4-bi-display" aria-hidden="true"></span><span class="a4-sr-only">{'Platform:'|i18n( 'kernel/exphelp' )} </span>{$help_page.platform|implode( ', ' )|wash}</li>
                    {/if}
                    {if $help_page.version_from|ne( '' )}
                    <li class="a4-help-meta-item"><span class="a4-icon a4-bi-tag" aria-hidden="true"></span>{if $help_page.version_to|ne( '' )}{'Versions %from to %to'|i18n( 'kernel/exphelp',, hash( '%from', $help_page.version_from, '%to', $help_page.version_to ) )|wash}{else}{'From version %from'|i18n( 'kernel/exphelp',, hash( '%from', $help_page.version_from ) )|wash}{/if}</li>
                    {/if}
                    <li class="a4-help-meta-item"><code class="a4-help-meta-path" dir="ltr">{$help_topic.help_id|wash}</code></li>
                    {if $help_page.mtime|gt( 0 )}
                    <li class="a4-help-meta-item"><span class="a4-icon a4-bi-clock-history" aria-hidden="true"></span>{'Last changed'|i18n( 'kernel/exphelp' )} <time datetime="{$help_page.mtime|datetime( 'custom', '%Y-%m-%d' )}">{$help_page.mtime|l10n( 'shortdate' )}</time></li>
                    {/if}
                    {if $help_edit_url|ne( '' )}
                    <li class="a4-help-meta-item is-edit"><a class="a4-btn a4-btn-small a4-btn-ghost" href={$help_edit_url|ezurl}><span class="a4-icon a4-bi-pencil" aria-hidden="true"></span> {'Edit this topic'|i18n( 'kernel/exphelp' )}</a></li>
                    {/if}
                    <li class="a4-help-meta-item"><a class="a4-btn a4-btn-small a4-btn-ghost" href={$help_json_url|ezurl}><span class="a4-icon a4-bi-file-earmark-code" aria-hidden="true"></span> JSON</a></li>
                </ul>
            </header>

            {if $help_toc_items|count|ge( 2 )}
            <details class="a4-help-toc-fold">
                <summary class="a4-help-toc-head">{'On this page'|i18n( 'kernel/exphelp' )}</summary>
                {include uri='design:exphelp/parts/viewer_toc.tpl' toc_items=$help_toc_items toc_id='exphelp-toc-inline' toc_spy=false()}
            </details>
            {/if}

            <article class="a4-help-article" data-exphelp-content aria-label="{$help_page.title|wash}">
{$help_html}
            {foreach $help_figures as $help_f}
                <figure class="a4-help-figure">
                    <img class="a4-help-img" src={$help_f.image_url|ezroot} alt="{$help_f.alt_text|wash}" loading="lazy" decoding="async" />
                    {if $help_f.caption|ne( '' )}<figcaption>{$help_f.caption|wash}</figcaption>{/if}
                </figure>
            {/foreach}
            </article>

            {if $help_related|count}
            <section class="a4-help-related" aria-labelledby="exphelp-related-title">
                <h2 class="a4-help-section-head" id="exphelp-related-title">{'Related topics'|i18n( 'kernel/exphelp' )}</h2>
                <ul class="a4-help-featured">
                {foreach $help_related as $help_r}
                    <li class="a4-help-feature">
                        <a href={$help_r.url|ezurl}>
                            <span class="a4-help-feature-text">
                                <span class="a4-help-feature-title">{$help_r.title|wash}</span>
                                {if $help_r.summary|ne( '' )}<span class="a4-help-feature-summary">{$help_r.summary|shorten( 140 )|wash}</span>{/if}
                            </span>
                        </a>
                    </li>
                {/foreach}
                </ul>
            </section>
            {/if}

            <p class="a4-help-foot a4-meta"><a href={'exphelp/report'|ezurl}><span class="a4-icon a4-bi-exclamation-diamond" aria-hidden="true"></span> {'Something wrong on this page? Report an error'|i18n( 'kernel/exphelp' )}</a></p>
        </div>

        <nav class="a4-help-nav" id="exphelp-nav" aria-labelledby="exphelp-nav-title" tabindex="-1">
            <p class="a4-help-nav-head" id="exphelp-nav-title"><span class="a4-icon a4-bi-list-ul" aria-hidden="true"></span> {'Help topics'|i18n( 'kernel/exphelp' )}</p>
            <ul class="a4-help-nav-list" role="list">
            {foreach $help_topics as $help_n}
                <li class="a4-help-nav-page"><a class="a4-help-nav-link"{if eq( $help_n.id, $help_page.id )} aria-current="page"{/if} href={$help_n.url|ezurl}><span class="a4-help-nav-text" dir="auto">{$help_n.title|wash}</span></a></li>
            {/foreach}
            </ul>
            {if $help_manuals|count}
            <p class="a4-help-nav-head"><span class="a4-icon a4-bi-journal-text" aria-hidden="true"></span> {'Manuals'|i18n( 'kernel/exphelp' )}</p>
            <ul class="a4-help-nav-list" role="list">
            {foreach $help_manuals as $help_m}
                <li class="a4-help-nav-page"><a class="a4-help-nav-link" href={concat( 'exphelp/manual/', $help_m.remote_id )|ezurl}><span class="a4-help-nav-text" dir="auto">{$help_m.title|wash}</span></a></li>
            {/foreach}
            </ul>
            {/if}
        </nav>

        {if $help_toc_items|count|ge( 2 )}
        <div class="a4-help-toc-side">
            <p class="a4-help-toc-head" aria-hidden="true">{'On this page'|i18n( 'kernel/exphelp' )}</p>
            {include uri='design:exphelp/parts/viewer_toc.tpl' toc_items=$help_toc_items toc_id='exphelp-toc' toc_spy=true()}
        </div>
        {/if}

    </div>
</div>
{undef $help_toc_items}
{ezscript_require( array( 'exphelp.js' ) )}
