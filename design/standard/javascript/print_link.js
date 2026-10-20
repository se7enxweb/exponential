/*
 * The print <link> of link.tpl: added here, with the page's query string, to be cache safe (the query string is not
 * in the pagelayout's cache-block key by default). The href and title come from this script element's data
 * attributes (data-print-href, data-print-title). A file of its own, so the page runs under a Content Security
 * Policy without 'unsafe-inline' (doc/eng-us/features/6.0/content-security-policy.md).
 */
(function() {

    var script = document.currentScript;
    if ( !script )
        return;
    var head = document.getElementsByTagName('head')[0];
    var printNode = document.createElement('link');
    printNode.rel = 'Alternate';
    printNode.href = script.getAttribute( 'data-print-href' ) + document.location.search;
    printNode.media = 'print';
    printNode.title = script.getAttribute( 'data-print-title' );
    head.appendChild(printNode);

})();
