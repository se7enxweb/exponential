/*
 * staticpublish/build and staticpublish/log: follows a static publishing job (build, publish or rollback) in the
 * background while its page is open (Setup > Static publishing). The view staticpublish/stream sends Server-Sent
 * Events: progress (the folded state: pages written, the phase, percent), line (a line of the progress) and done.
 * The bar, the line under it and the progress text are updated as they come; when the job has ended the page is
 * loaded again and shows its report. A file of its own, so the page runs under a Content Security Policy without
 * 'unsafe-inline'; the same file in design/admin and design/admin4.
 */
(function () {
    'use strict';
    var root = document.querySelector( '[data-staticpublish-stream]' );
    if ( !root || !window.EventSource || !window.JSON ) return;
    var text = root.dataset;
    var url = root.getAttribute( 'data-staticpublish-stream' );
    var bar = document.getElementById( 'sp-job-bar' );
    var phase = document.getElementById( 'sp-job-phase' );
    var lines = document.getElementById( 'sp-job-lines' );
    var ended = false, failures = 0, source = null;

    function showState( state, meter ) {
        if ( bar ) bar.className = 'a4-pct-' + Math.max( 0, Math.min( 100, parseInt( meter, 10 ) || 0 ) );
        if ( !phase ) return;
        var parts = [];
        if ( state.pages ) parts.push( String( text.textPages || '' ).split( '%pages' ).join( String( state.pages ) ) );
        if ( state.phase ) parts.push( String( state.phase ) );
        phase.textContent = parts.join( ' · ' );
    }
    function appendLine( line ) {
        if ( !lines ) return;
        var atBottom = lines.scrollTop + lines.clientHeight >= lines.scrollHeight - 8;
        lines.appendChild( document.createTextNode( ( lines.textContent && !/\n$/.test( lines.textContent ) ? '\n' : '' ) + line + '\n' ) );
        // follow the end only while the reader is at the bottom
        if ( atBottom ) lines.scrollTop = lines.scrollHeight;
    }
    function finish() {
        if ( ended ) return;
        ended = true;
        if ( source ) source.close();
        if ( phase ) phase.textContent = text.textDone;
        window.setTimeout( function () { window.location.reload(); }, 1200 );
    }
    function connect() {
        source = new EventSource( url );
        source.onmessage = function ( event ) {
            var data;
            try { data = JSON.parse( event.data ); } catch ( e ) { return; }
            failures = 0;
            if ( data.type === 'progress' ) {
                showState( data.state || {}, data.meter );
                url = url.replace( /\/\(offset\)\/\d+/, '/(offset)/' + ( parseInt( data.offset, 10 ) || 0 ) );
            } else if ( data.type === 'line' ) {
                appendLine( String( data.text || '' ) );
            } else if ( data.type === 'done' ) {
                finish();
            } else if ( data.type === 'info' && data.text === 'reconnect' ) {
                source.close();
                window.setTimeout( connect, 500 );
            }
        };
        source.addEventListener( 'end', function () { if ( !ended ) { source.close(); window.setTimeout( connect, 1500 ); } } );
        source.onerror = function () {
            if ( ended ) return;
            source.close();
            failures++;
            if ( failures > 5 ) { if ( phase ) phase.textContent = text.textLost; return; }
            window.setTimeout( connect, 2000 * failures );
        };
    }
    if ( lines ) lines.scrollTop = lines.scrollHeight;
    connect();
}());
