/*
 * admin4 rss/edit_export: the focus when the page opens (the error box after a failed save, the name otherwise; the
 * template names the field in data-focus on #rss-export-script), the parts of the page that follow the format and the
 * podcast category, and Enter in the feed browser's search box. A file of its own, so the page runs under a Content
 * Security Policy without 'unsafe-inline' (doc/eng-us/features/6.0/content-security-policy.md).
 */
var expRssExportScript = document.getElementById( 'rss-export-script' );
var expRssExportFocus = expRssExportScript ? expRssExportScript.getAttribute( 'data-focus' ) : '';
( function () {
    var focus = document.getElementById( expRssExportFocus );
    if ( focus ) { focus.focus(); if ( focus.select ) focus.select(); }

    var version = document.getElementById( 'rssExportVersion' );
    if ( !version ) return;

    // Which half of the page applies follows the format. The server decides the same thing after every submit;
    // this only saves waiting for one.
    function show()
    {
        var opml    = version.value === 'OPML',
            podcast = version.value === 'ITUNES',
            sources = document.getElementById( 'rssExportSources' ),
            panel   = document.getElementById( 'rssExportOPML' ),
            pod     = document.getElementById( 'rssExportPodcast' ),
            add     = document.getElementById( 'rssExportAddSource' ),
            limit   = document.getElementById( 'rssExportLimitBlock' );

        // A podcast keeps its sources: the episodes come from content. Only OPML replaces them with other feeds.
        if ( sources ) sources.hidden = opml;
        if ( panel )   panel.hidden   = !opml;
        if ( pod )     pod.hidden     = !podcast;
        if ( add )     add.hidden     = opml;
        if ( limit )   limit.hidden   = opml;

        subcategories();
    }

    // Only the subcategories of the chosen category are worth offering; Apple rejects a pairing that is not its own.
    function subcategories()
    {
        var category = document.getElementById( 'podcastCategory' ),
            sub      = document.getElementById( 'podcastSubcategory' );
        if ( !category || !sub ) return;
        var chosen = category.value, cleared = false;
        for ( var i = 0; i < sub.options.length; i++ )
        {
            var option = sub.options[i], parent = option.getAttribute( 'data-parent' );
            if ( !parent ) continue;
            var applies = parent === chosen;
            option.hidden = !applies;
            option.disabled = !applies;
            if ( !applies && option.selected ) { option.selected = false; cleared = true; }
        }
        if ( cleared ) sub.selectedIndex = 0;
    }

    version.onchange = show;
    var podcastCategory = document.getElementById( 'podcastCategory' );
    if ( podcastCategory ) podcastCategory.onchange = subcategories;
    show();

    // Enter in the feed browser's search box searches instead of pressing the first button of the form.
    var search = document.getElementById( 'feedBrowserSearch' ),
        apply  = document.getElementById( 'feedBrowserApply' );
    if ( search && apply )
    {
        search.onkeydown = function ( event ) {
            // event.key (keyCode is deprecated); not while an input method composes a word
            if ( event.key !== 'Enter' || event.isComposing ) return true;
            apply.click();
            return false;
        };
    }
} )();
