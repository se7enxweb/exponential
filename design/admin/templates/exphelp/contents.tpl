{* exphelp/contents (doc/features/6.0/help-system.md): every page of the help as one tree. Each folder is a native
   <details>, closed, with its title and number of pages; it opens without JavaScript, and an address with its
   #exphelp-folder-... anchor opens it. exphelp.js adds a filter box that narrows the tree as you type (the folders with
   a match open) and the Expand all / Collapse all control, fixed at the side of the window near the bottom, above the
   debug bar or its minimised tab (--exp-debug-clearance, expdebugbar.css). Without JavaScript the control stays hidden.
   The frame is the viewer's: the bar (help start, search), the page head, then the tree in a card; one column, the top
   folders side by side on wide pages.
   Variables (kernel/private/classes/views/exphelp/contents.php): help_nav_html (expHelpNavigation: safe HTML, NOT
   washed), help_nav_count, help_i18n_json.
   The same file is in design/admin and design/admin4. *}
<div class="a4-page a4-help a4-help-contents" data-exphelp data-exphelp-i18n="{$help_i18n_json|wash}">

    <div class="a4-help-viewer-bar">
        <a class="a4-btn a4-btn-ghost a4-btn-small" href={'exphelp/dashboard'|ezurl}><span class="a4-icon a4-bi-signpost-split" aria-hidden="true"></span> {'Help start'|i18n( 'kernel/exphelp' )}</a>
        <form class="a4-help-viewer-search a4-help-search-box" data-exphelp-search action={'exphelp/search'|ezurl} method="get" role="search" data-exphelp-endpoint={'exphelp/search'|ezurl}>
            <label class="a4-sr-only" for="exphelp-contents-q">{'Search the help'|i18n( 'kernel/exphelp' )}</label>
            <input class="a4-input" type="search" id="exphelp-contents-q" name="q" value="" autocomplete="off" placeholder="{'Search the help'|i18n( 'kernel/exphelp' )|wash}" aria-controls="exphelp-contents-results" aria-keyshortcuts="/" data-exphelp-search-input />
            <button type="submit" class="a4-btn a4-btn-small"><span class="a4-icon a4-bi-search" aria-hidden="true"></span> {'Search'|i18n( 'kernel/exphelp' )}</button>
            <div id="exphelp-contents-results" data-exphelp-results aria-live="polite" hidden></div>
        </form>
    </div>

    <header class="a4-help-pagehead">
        <h1 class="a4-help-title" id="exphelp-contents-title">{'All help pages'|i18n( 'kernel/exphelp' )}</h1>
        <p class="a4-help-summary">{'%count pages, in the folders of the documentation. Open a folder, or filter by a word of the title.'|i18n( 'kernel/exphelp',, hash( '%count', $help_nav_count ) )|wash}</p>
    </header>

    <nav class="a4-card a4-help-nav is-contents" id="exphelp-contents-tree" aria-labelledby="exphelp-contents-title" data-exphelp-nav>
        <div class="a4-help-nav-tools">
            <label class="a4-sr-only" for="exphelp-nav-filter">{'Filter the help pages'|i18n( 'kernel/exphelp' )}</label>
            <input class="a4-input a4-help-nav-filter exp-js-only" type="search" id="exphelp-nav-filter" autocomplete="off" placeholder="{'Filter pages'|i18n( 'kernel/exphelp' )|wash}" data-exphelp-navfilter hidden />
        </div>
        <p class="a4-sr-only" data-exphelp-navfilter-status aria-live="polite"></p>
        {$help_nav_html}
    </nav>

    <div class="a4-help-contents-controls" role="group" aria-label="{'Open or close every folder'|i18n( 'kernel/exphelp' )|wash}" data-exphelp-contents-controls hidden>
        <button type="button" class="a4-btn a4-btn-small" aria-controls="exphelp-contents-tree" data-exphelp-expand-all><span class="a4-icon a4-bi-arrows-expand-vertical" aria-hidden="true"></span> {'Expand all'|i18n( 'kernel/exphelp' )}</button>
        <button type="button" class="a4-btn a4-btn-small" aria-controls="exphelp-contents-tree" data-exphelp-collapse-all><span class="a4-icon a4-bi-arrows-collapse-vertical" aria-hidden="true"></span> {'Collapse all'|i18n( 'kernel/exphelp' )}</button>
    </div>
</div>
{ezscript_require( array( 'exphelp.js' ) )}
