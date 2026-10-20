/*
 * admin4 setup/moduleextension: the file pick list, the kind and the source of the database, and the filter of the
 * table list. Folding the files is page/setup_wizard_files.js; "Tick none" is a data-a4-check-all action. A file of
 * its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/eng-us/features/6.0/content-security-policy.md).
 */
( function () {
    // The external details only apply to an external database.
    var source = document.getElementById( 'mewSource' ),
        external = document.getElementById( 'mewExternal' );

    // Choosing a file from the list fills the box; the box is still there for a
    // path the list does not know about.
    var pick = document.getElementById( 'mewFilePick' ),
        file = document.getElementById( 'mewFile' );
    if ( pick && file )
    {
        pick.addEventListener( 'change', function () {
            if ( pick.value !== '' ) file.value = pick.value;
        } );
    }

    // Which boxes apply depends on the kind, and the server decides the same
    // thing after every submit; this only saves waiting for one.
    var type = document.getElementById( 'mewType' );
    if ( type )
    {
        type.addEventListener( 'change', function () {
            var connect = document.getElementsByName( 'ConnectButton' );
            if ( connect.length ) connect[0].click();
        } );
    }

    if ( source && external )
    {
        source.addEventListener( 'change', function () {
            external.hidden = source.value !== 'external';
        } );
    }

    // Narrowing a long table list without going back to the server.
    var filter = document.getElementById( 'mewFilter' ),
        list = document.getElementById( 'mewTableList' );

    if ( filter && list )
    {
        filter.addEventListener( 'keyup', function () {
            var want = filter.value.toLowerCase(),
                rows = list.getElementsByTagName( 'tr' ), r;

            for ( r = 1; r < rows.length; r++ )
            {
                var text = ( rows[r].textContent || rows[r].innerText || '' ).toLowerCase();
                rows[r].hidden = !( want === '' || text.indexOf( want ) !== -1 );
            }
        } );
    }
} )();
