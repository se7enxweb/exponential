/*
 * The asynchronous publishing page (content/queued.tpl): configures eZAsynchronousPublishingApp
 * (ezasynchronouspublishing.js) from the data attributes of #ezap-config and starts it. A file of its own, so the
 * page runs under a Content Security Policy without 'unsafe-inline' (doc/en/us/features/6.0/content-security-policy.md).
 */
(function () {
    var config = document.getElementById( 'ezap-config' );
    if ( !config || !window.eZAsynchronousPublishingApp )
        return;
    var redirect = config.getAttribute( 'data-redirect-uri' );
    eZAsynchronousPublishingApp.cfg = {
        contentobject_id: parseInt( config.getAttribute( 'data-contentobject-id' ), 10 ),
        version: parseInt( config.getAttribute( 'data-version' ), 10 ),
        redirect_uri: redirect === null ? false : redirect,
        failure_message: config.getAttribute( 'data-failure-message' ),
        last_checked_message: config.getAttribute( 'data-last-checked-message' )
    };
    eZAsynchronousPublishingApp.init();
}());
