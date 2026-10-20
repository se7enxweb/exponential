/*
 * admin4 rss/edit_import: the focus when the page opens - the error box after a failed save, the name otherwise. The
 * template names the field in data-focus on #rss-import-script. A file of its own, so the page runs under a Content
 * Security Policy without 'unsafe-inline' (doc/eng-us/features/6.0/content-security-policy.md).
 */
var expRssImportScript = document.getElementById( 'rss-import-script' );
var expRssImportFocus = expRssImportScript ? expRssImportScript.getAttribute( 'data-focus' ) : '';
( function () {
    var focus = document.getElementById( expRssImportFocus );
    if ( focus ) { focus.focus(); if ( focus.select ) focus.select(); }
} )();
