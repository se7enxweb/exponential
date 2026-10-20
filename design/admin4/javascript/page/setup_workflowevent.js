/*
 * admin4 setup/workflowevent: shows the class name the event name leads to while it is typed. Folding the files is
 * page/setup_wizard_files.js; "Tick none" is a data-a4-check-all action. A file of its own, so the page runs under a
 * Content Security Policy without 'unsafe-inline' (doc/en/us/features/6.0/content-security-policy.md).
 */
( function () {
    // The event name drives the class name; show it as it is typed.
    var event = document.getElementById( 'wfeEvent' );
    if ( event )
    {
        event.onkeyup = function () {
            var clean = event.value.toLowerCase().replace( /[^a-z0-9]+/g, '' ),
                hint = event.parentNode.getElementsByTagName( 'span' )[0];
            if ( hint )
                hint.textContent = clean === ''
                    ? hint.getAttribute( 'data-empty' )
                    : hint.getAttribute( 'data-named' ).split( '%class' ).join( clean + 'Type' );
        };
    }
} )();
