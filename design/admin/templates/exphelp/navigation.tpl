{* exphelp/navigation/<folder> (doc/eng-us/features/6.0/help-system.md): one folder of the help tree, as a page. It is
   the link a closed folder of the viewer's tree carries for a browser without JavaScript (with it, exphelp.js loads
   the folder's list into the tree instead). The folder's overview, its folders (closed, each with the link to its
   own listing) and its pages, in the frame of the contents page.
   Variables (kernel/private/classes/views/exphelp/navigation.php): help_folder (path, title, count, index_id),
   help_nav_html (expHelpNavigation: safe HTML, NOT washed), help_i18n_json.
   The same file is in design/admin and design/admin4. *}
<div class="a4-page a4-help-frame a4-help-contents" data-exphelp data-exphelp-i18n="{$help_i18n_json|wash}">

    <div class="a4-help-viewer-bar">
        <div class="a4-help-viewer-line" data-exphelp-line>
            <a class="a4-btn a4-btn-ghost a4-btn-small a4-help-viewer-start" href={'exphelp/dashboard'|ezurl}><span class="a4-icon a4-bi-signpost-split" aria-hidden="true"></span> {'Help Start'|i18n( 'kernel/exphelp' )}</a>
        </div>
        <a class="a4-btn a4-btn-ghost a4-btn-small" href={'exphelp/contents'|ezurl}><span class="a4-icon a4-bi-list-ul" aria-hidden="true"></span> {'All help pages'|i18n( 'kernel/exphelp' )}</a>
        <form class="a4-help-viewer-search a4-help-search-box" data-exphelp-search action={'exphelp/search'|ezurl} method="post" role="search" data-exphelp-endpoint={'exphelp/search'|ezurl}>
            <label class="a4-sr-only" for="exphelp-folder-q">{'Search the help'|i18n( 'kernel/exphelp' )}</label>
            <input class="a4-input" type="search" id="exphelp-folder-q" name="q" value="" autocomplete="off" placeholder="{'Search the help'|i18n( 'kernel/exphelp' )|wash}" aria-controls="exphelp-folder-results" aria-keyshortcuts="/" data-exphelp-search-input />
            <button type="submit" class="a4-btn a4-btn-small"><span class="a4-icon a4-bi-search" aria-hidden="true"></span> {'Search'|i18n( 'kernel/exphelp' )}</button>
            <div id="exphelp-folder-results" data-exphelp-results aria-live="polite" hidden></div>
        </form>
    </div>

    <header class="a4-help-pagehead">
        <h1 class="a4-help-title" id="exphelp-folder-title" dir="auto">{cond( $help_folder.title|ne( '' ), $help_folder.title, 'All help pages'|i18n( 'kernel/exphelp' ) )|wash}</h1>
        <p class="a4-help-summary">{'%count pages'|i18n( 'kernel/exphelp',, hash( '%count', $help_folder.count ) )|wash}</p>
    </header>

    <nav class="a4-card a4-help-nav is-contents" id="exphelp-folder-tree" aria-labelledby="exphelp-folder-title" data-exphelp-nav>
        <p class="a4-sr-only" data-exphelp-navfilter-status aria-live="polite"></p>
        {$help_nav_html}
    </nav>
</div>
{ezscript_require( array( 'exphelp.js' ) )}
