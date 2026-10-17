/*
 * The "Create New" button of the object relation list field (content/datatype/edit/ezobjectrelationlist.tpl): a
 * click shows the hidden name field (data-a4-reveal, the id of its box), focuses its input (data-a4-reveal-focus)
 * and hides the button. One listener on the document handles every field of the page. A file of its own, so the
 * page runs under a Content Security Policy without 'unsafe-inline' (doc/features/6.0/content-security-policy.md).
 */
(function () {
    'use strict';
    if ( window.a4ContentObjectRelationList ) return;  // loaded once per page
    window.a4ContentObjectRelationList = true;

    document.addEventListener( 'click', function ( e ) {
        var el = e.target;
        while ( el && el.nodeType !== 1 ) el = el.parentNode;
        var button = el && el.closest ? el.closest( '[data-a4-reveal]' ) : null;
        if ( !button ) return;
        e.preventDefault();
        var divfield = document.getElementById( button.getAttribute( 'data-a4-reveal' ) );
        if ( divfield ) divfield.hidden = false;
        var editfield = document.getElementById( button.getAttribute( 'data-a4-reveal-focus' ) );
        if ( editfield ) editfield.focus();
        button.style.display = 'none';
    } );
})();
