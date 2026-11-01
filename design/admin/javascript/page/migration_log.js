/*
 * migration/log: follows a migration run in the background while its page is open (Setup > Migrations). The view
 * migration/stream sends Server-Sent Events: progress (the state of the run: phase, the migration running now, how
 * many of how many), log (a line of the run log) and done. The bar, the line under it and the run log are updated as
 * they come; when the run has ended the page is loaded again and shows its report. A file of its own, so the page runs
 * under a Content Security Policy without 'unsafe-inline' (doc/eng-us/features/6.0/content-security-policy.md);
 * the same file in design/admin and design/admin4.
 */
(function () {
    'use strict';
    var root = document.querySelector( '[data-migration-stream]' );
    if ( !root || !window.EventSource || !window.JSON ) return;
    var text = root.dataset;
    var url = root.getAttribute( 'data-migration-stream' );
    var bar = document.getElementById( 'exp-migration-bar' );
    var phase = document.getElementById( 'exp-migration-phase' );
    var runlog = document.getElementById( 'exp-migration-runlog' );
    var lastState = null, ended = false, failures = 0, source = null;

    function tr( s, values ) {
        s = String( s || '' );
        for ( var key in values || {} ) s = s.split( '%' + key ).join( String( values[key] ) );
        return s;
    }
    function describe( state ) {
        if ( state.ended ) return state.total ? tr( text.textBetween, { done: state.done, total: state.total } ) : '';
        if ( state.current ) return tr( text.textMigrations, { id: state.current, index: state.done + 1, total: state.total } );
        switch ( state.phase ) {
            case 'caption-pre': return text.textCaptionPre;
            case 'after-migrations': return text.textAfterMigrations;
            case 'caption-post': return text.textCaptionPost;
            case 'restore': return text.textRestore;
        }
        return state.total ? tr( text.textBetween, { done: state.done, total: state.total } ) : '';
    }
    function showState( state, meter ) {
        lastState = state;
        if ( bar ) bar.className = 'a4-pct-' + Math.max( 0, Math.min( 100, parseInt( meter, 10 ) || 0 ) );
        if ( phase ) phase.textContent = describe( state );
    }
    function appendLog( line ) {
        if ( !runlog ) return;
        var atBottom = runlog.scrollTop + runlog.clientHeight >= runlog.scrollHeight - 8;
        runlog.appendChild( document.createTextNode( ( runlog.textContent && !/\n$/.test( runlog.textContent ) ? '\n' : '' ) + line + '\n' ) );
        // follow the end only while the reader is at the bottom
        if ( atBottom ) runlog.scrollTop = runlog.scrollHeight;
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
            } else if ( data.type === 'log' ) {
                appendLog( String( data.text || '' ) );
                url = url.replace( /\/\(log\)\/\d+/, '/(log)/' + ( parseInt( data.offset, 10 ) || 0 ) );
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
    if ( lastState === null ) connect();
}());
