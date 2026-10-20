/*
 * admin4 RAD wizards (setup/contentextension, setup/datatype, setup/handlerextension, setup/kerneloverride,
 * setup/modulewizard, setup/settingsextension, setup/templateoperator, setup/workflowevent): the generated files fold
 * open and shut on a click on their head, and "Open all" / "Close all" (data-a4-wz-fold="open" / "close") fold every
 * file. A file of its own, so the pages run under a Content Security Policy without 'unsafe-inline'
 * (doc/en/us/features/6.0/content-security-policy.md). setup/designextension and setup/moduleextension load it too.
 */
( function () {
    // Folding a file open and shut. The markup is already complete; this only
    // saves scrolling past a thousand lines to reach the next heading.
    function fold( panel, open )
    {
        var collapsed = panel.className.indexOf( 'is-collapsed' ) !== -1;
        if ( open === collapsed )
        {
            panel.className = open
                ? panel.className.replace( / ?is-collapsed/, '' )
                : panel.className + ' is-collapsed';
        }
        var toggle = panel.getElementsByTagName( 'span' )[0];
        if ( toggle ) toggle.innerHTML = open ? '&minus;' : '+';
    }

    var divs = document.getElementsByTagName( 'div' ), files = [], i;
    for ( i = 0; i < divs.length; i++ )
        if ( ( ' ' + divs[i].className + ' ' ).indexOf( ' a4-setup-wz-file ' ) !== -1 )
            files.push( divs[i] );

    for ( i = 0; i < files.length; i++ )
        ( function ( panel ) {
            var head = panel.getElementsByTagName( 'div' )[0];
            if ( !head ) return;
            head.addEventListener( 'click', function ( e ) {
                e.preventDefault();
                fold( panel, panel.className.indexOf( 'is-collapsed' ) !== -1 );
            } );
        } )( files[i] );

    function every( open ) { for ( var j = 0; j < files.length; j++ ) fold( files[j], open ); }
    var openAll = document.querySelector( '[data-a4-wz-fold="open"]' ), closeAll = document.querySelector( '[data-a4-wz-fold="close"]' );
    if ( openAll )  openAll.addEventListener( 'click', function ( e ) { e.preventDefault(); every( true ); } );
    if ( closeAll ) closeAll.addEventListener( 'click', function ( e ) { e.preventDefault(); every( false ); } );
} )();
