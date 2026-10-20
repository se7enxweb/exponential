{* exphelp/viewer (doc/en/us/features/6.0/help-system.md): one help page. Wide windows show three columns: the tree of
   every help page on the left, the page in the middle (title, meta, the rendered document, previous/next) and its
   table of contents on the right, which stays in view. Narrower windows drop the side contents for a folded one above
   the page, and on phones the tree moves below the page (it comes after the page in the source, so the page is read
   first). The tree lists every page the help serves (expHelpNavigation, cached per index signature), the folders on
   the way to this page open; its filter box (exphelp.js) narrows it as you type. Works without JavaScript; exphelp.js
   (w612/F hooks) adds the instant search, the scrollspy of the contents, the copy buttons of code blocks, collapsible
   sections and the keys / j k [ ].
   The article carries the language of the page (help_page_lang, help_page_dir: en-US and ltr for the English
   original, also on a right-to-left admin). A page that is not translated into the reader's language yet is shown in
   the original, with a note that says so (help_fallback).

   Variables (kernel/private/classes/views/exphelp/viewer.php): help_page (document: id, path, title, summary, type,
   category, audience[], changed, size, language, fallback ...), help_html (rendered by expHelpMarkdownRenderer and
   safe: NOT washed), help_toc (level, text, anchor), help_nav_html (the tree: safe HTML, NOT washed), help_prev,
   help_next (documents or null), help_edit_url (GitHub, may be empty), help_category (id, name, icon, or not set),
   help_i18n_json, help_page_lang, help_page_dir, help_fallback, help_reader_language, help_page_language; help_reading
   (the "Reading in" control, parts/reading_language.tpl), help_original_url, help_original_language, help_original_view
   (the English original of a page for a reader of another language: ?lang=en-us).

   The same file is in design/admin and design/admin4. *}
{def $help_title = cond( and( is_set( $help_page.title ), $help_page.title|ne( '' ) ), $help_page.title, $help_page.id )
     $help_audience_list = cond( is_set( $help_page.audience ), $help_page.audience, array() )
     $help_toc_items = array()}
{foreach $help_toc as $help_t}{if and( $help_t.level|ge( 2 ), $help_t.level|le( 3 ) )}{set $help_toc_items = $help_toc_items|append( $help_t )}{/if}{/foreach}
<div class="a4-page a4-help-frame a4-help-viewer{if $help_toc_items|count|lt( 2 )} is-no-toc{/if}" data-exphelp data-exphelp-i18n="{$help_i18n_json|wash}">

    <div class="a4-help-viewer-bar">
        <a class="a4-btn a4-btn-ghost a4-btn-small" href={'exphelp/dashboard'|ezurl}><span class="a4-icon a4-bi-signpost-split" aria-hidden="true"></span> {'Help start'|i18n( 'kernel/exphelp' )}</a>
        <a class="a4-btn a4-btn-ghost a4-btn-small a4-help-viewer-tonav" href="#exphelp-nav"><span class="a4-icon a4-bi-list-ul" aria-hidden="true"></span> {'All help pages'|i18n( 'kernel/exphelp' )}</a>
        <form class="a4-help-viewer-search a4-help-search-box" data-exphelp-search action={'exphelp/search'|ezurl} method="get" role="search" data-exphelp-endpoint={'exphelp/search'|ezurl}>
            <label class="a4-sr-only" for="exphelp-viewer-q">{'Search the help'|i18n( 'kernel/exphelp' )}</label>
            <input class="a4-input" type="search" id="exphelp-viewer-q" name="q" value="" autocomplete="off" placeholder="{'Search the help'|i18n( 'kernel/exphelp' )|wash}" aria-controls="exphelp-viewer-results" aria-keyshortcuts="/" data-exphelp-search-input />
            <button type="submit" class="a4-btn a4-btn-small"><span class="a4-icon a4-bi-search" aria-hidden="true"></span> {'Search'|i18n( 'kernel/exphelp' )}</button>
            <div id="exphelp-viewer-results" data-exphelp-results aria-live="polite" hidden></div>
        </form>
        {if and( is_set( $help_reading ), $help_reading )}{include uri='design:exphelp/parts/reading_language.tpl' reading=$help_reading prefix='exphelp-viewer-reading'}{/if}
    </div>

    <div class="a4-help-viewer-grid">

        <div class="a4-help-main">
            <header class="a4-help-pagehead">
                <h1 class="a4-help-title" dir="auto">{$help_title|wash}</h1>
                {if and( is_set( $help_page.summary ), $help_page.summary|ne( '' ) )}
                <p class="a4-help-summary" dir="auto">{$help_page.summary|wash}</p>
                {/if}
                {include uri='design:exphelp/parts/viewer_meta.tpl' meta_page=$help_page meta_audiences=$help_audience_list meta_edit_url=$help_edit_url meta_category=cond( is_set( $help_category ), $help_category, false() )}
                {include uri='design:exphelp/parts/viewer_actions.tpl' actions_page=$help_page actions_pdf=cond( is_set( $help_pdf_enabled ), $help_pdf_enabled, false() )}
            </header>

            {if $help_toc_items|count|ge( 2 )}
            <details class="a4-help-toc-fold">
                <summary class="a4-help-toc-head">{'On this page'|i18n( 'kernel/exphelp' )}</summary>
                {include uri='design:exphelp/parts/viewer_toc.tpl' toc_items=$help_toc_items toc_id='exphelp-toc-inline' toc_spy=false()}
            </details>
            {/if}

            {if and( is_set( $help_fallback ), $help_fallback )}
            <p class="a4-notice is-info a4-help-fallback" role="note"><span class="a4-icon a4-bi-translate" aria-hidden="true"></span> {'This page is not translated into %reader yet. It is shown in %original.'|i18n( 'kernel/exphelp', '', hash( '%reader', $help_reader_language, '%original', $help_page_language ) )|wash}{if and( is_set( $help_original_url ), $help_original_url|ne( '' ) )} <a href={$help_original_url|ezurl} hreflang="en">{'Open the %language original'|i18n( 'kernel/exphelp', '', hash( '%language', $help_original_language ) )|wash}</a>{/if}</p>
            {elseif and( is_set( $help_original_view ), $help_original_view )}
            <p class="a4-notice is-info a4-help-fallback" role="note"><span class="a4-icon a4-bi-translate" aria-hidden="true"></span> {'You are reading the %language original of this page.'|i18n( 'kernel/exphelp', '', hash( '%language', $help_original_view.name ) )|wash} <a href={$help_original_view.back_url|ezurl}>{'Back to the page in %language'|i18n( 'kernel/exphelp', '', hash( '%language', $help_original_view.reader_name ) )|wash}</a></p>
            {elseif and( is_set( $help_original_url ), $help_original_url|ne( '' ) )}
            <p class="a4-help-original a4-meta"><span class="a4-icon a4-bi-translate" aria-hidden="true"></span> {'This page is a translation.'|i18n( 'kernel/exphelp' )} <a href={$help_original_url|ezurl} hreflang="en">{'Read the %language original'|i18n( 'kernel/exphelp', '', hash( '%language', $help_original_language ) )|wash}</a></p>
            {/if}
            <article class="a4-help-article" data-exphelp-content data-exphelp-collapsible="h2" aria-label="{$help_title|wash}" lang="{cond( is_set( $help_page_lang ), $help_page_lang, 'en' )|wash}" dir="{cond( is_set( $help_page_dir ), $help_page_dir, 'ltr' )|wash}">
{$help_html}
            </article>

            {if or( $help_prev, $help_next )}
            <nav class="a4-help-pager" aria-label="{'Previous and next page'|i18n( 'kernel/exphelp' )|wash}">
                {if $help_prev}
                <a class="a4-help-pager-link is-prev" rel="prev" data-exphelp-prev aria-keyshortcuts="[" href={concat( 'exphelp/viewer/', $help_prev.id )|ezurl}>
                    <span class="a4-help-pager-dir"><span class="a4-icon a4-bi-chevron-right" aria-hidden="true"></span> {'Previous'|i18n( 'kernel/exphelp' )}</span>
                    <span class="a4-help-pager-title" dir="auto">{cond( and( is_set( $help_prev.title ), $help_prev.title|ne( '' ) ), $help_prev.title, $help_prev.id )|wash}</span>
                </a>
                {/if}
                {if $help_next}
                <a class="a4-help-pager-link is-next" rel="next" data-exphelp-next aria-keyshortcuts="]" href={concat( 'exphelp/viewer/', $help_next.id )|ezurl}>
                    <span class="a4-help-pager-dir">{'Next'|i18n( 'kernel/exphelp' )} <span class="a4-icon a4-bi-chevron-right" aria-hidden="true"></span></span>
                    <span class="a4-help-pager-title" dir="auto">{cond( and( is_set( $help_next.title ), $help_next.title|ne( '' ) ), $help_next.title, $help_next.id )|wash}</span>
                </a>
                {/if}
            </nav>
            {/if}

            <p class="a4-help-foot a4-meta"><a href={'exphelp/report'|ezurl}><span class="a4-icon a4-bi-exclamation-diamond" aria-hidden="true"></span> {'Something wrong on this page? Report an error'|i18n( 'kernel/exphelp' )}</a></p>

            {include uri='design:exphelp/parts/license_notice.tpl' license_page_id=$help_page.id}
        </div>

        <nav class="a4-help-nav" id="exphelp-nav" aria-labelledby="exphelp-nav-title" tabindex="-1" data-exphelp-nav>
            <p class="a4-help-nav-head" id="exphelp-nav-title"><span class="a4-icon a4-bi-list-ul" aria-hidden="true"></span> {'All help pages'|i18n( 'kernel/exphelp' )}</p>
            <div class="a4-help-nav-tools">
                <label class="a4-sr-only" for="exphelp-nav-filter">{'Filter the help pages'|i18n( 'kernel/exphelp' )}</label>
                <input class="a4-input a4-help-nav-filter exp-js-only" type="search" id="exphelp-nav-filter" autocomplete="off" placeholder="{'Filter pages'|i18n( 'kernel/exphelp' )|wash}" data-exphelp-navfilter hidden />
                <button type="button" class="a4-btn a4-btn-ghost a4-btn-small a4-help-nav-clear" aria-controls="exphelp-nav-filter" data-exphelp-navfilter-clear hidden><span class="a4-icon a4-bi-x-lg" aria-hidden="true"></span> {'Clear filter'|i18n( 'kernel/exphelp' )}</button>
                <a class="a4-help-nav-all" href={'exphelp/contents'|ezurl}>{'Browse all help'|i18n( 'kernel/exphelp' )}</a>
            </div>
            <p class="a4-sr-only" data-exphelp-navfilter-status aria-live="polite"></p>
            {$help_nav_html}
        </nav>

        {if $help_toc_items|count|ge( 2 )}
        <div class="a4-help-toc-side">
            <p class="a4-help-toc-head" aria-hidden="true">{'On this page'|i18n( 'kernel/exphelp' )}</p>
            {include uri='design:exphelp/parts/viewer_toc.tpl' toc_items=$help_toc_items toc_id='exphelp-toc' toc_spy=true()}
        </div>
        {/if}

    </div>
</div>
{undef $help_title $help_audience_list $help_toc_items}
{ezscript_require( array( 'exphelp.js' ) )}
