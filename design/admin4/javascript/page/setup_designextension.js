/*
 * admin4 setup/designextension: shows what the extension name becomes as a directory while it is typed. Folding the
 * files is page/setup_wizard_files.js; "Tick all" / "Tick none" are data-a4-check-all actions. A file of its own, so
 * the page runs under a Content Security Policy without 'unsafe-inline' (doc/en/us/features/6.0/content-security-policy.md).
 */
( function () {
    // The name drives the directory, so show what it will become as it is typed.
    var name = document.getElementById( 'dewName' );
    if ( name )
    {
        name.addEventListener( 'keyup', function () {
            var clean = name.value.toLowerCase().replace( /[^a-z0-9_]+/g, '_' ).replace( /^_+|_+$/g, '' ),
                hint = name.parentNode.getElementsByTagName( 'span' )[0];
            if ( hint ) {
                if ( !hint.getAttribute( 'data-empty' ) )
                    hint.setAttribute( 'data-empty', hint.textContent );
                hint.textContent = clean === ''
                    ? hint.getAttribute( 'data-empty' )
                    : hint.getAttribute( 'data-named' ).split( '%name' ).join( clean );
            }
        } );
    }
} )();
