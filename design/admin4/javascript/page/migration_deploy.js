/*
 * migration/deployment: follows a deploy to remotes in the background while its page is open (Setup > Migrations >
 * Deploys). The view migration/stream sends Server-Sent Events: progress (the state of the deploy: the phase, how many
 * phases have ended, and hosts: every remote with its state and the phase it is in) and done. The bar, the line under
 * it and the row of every remote are updated as they come; when the deploy has ended the page is loaded again and
 * shows its report. A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/eng-us/features/6.0/content-security-policy.md); the same file in design/admin and design/admin4.
 */
(function () {
    'use strict';
    var root = document.querySelector( '[data-migration-deploy-stream]' );
    if ( !root || !window.EventSource || !window.JSON ) return;
    var text = root.dataset;
    var url = root.getAttribute( 'data-migration-deploy-stream' );
    var bar = document.getElementById( 'exp-deploy-bar' );
    var phase = document.getElementById( 'exp-deploy-phase' );
    var hosts = document.getElementById( 'exp-deploy-hosts' );
    var words = {};
    var ended = false, failures = 0, source = null;

    Array.prototype.forEach.call( document.querySelectorAll( '#exp-deploy-state-words li' ), function ( li ) {
        words[li.getAttribute( 'data-state' )] = { text: li.textContent.trim(), kind: li.getAttribute( 'data-kind' ) };
    } );

    function phaseText( name ) {
        name = String( name || '' ).replace( /^stage\d+-/, '' );
        switch ( name ) {
            case 'prepare': case 'prepare-rollback': return text.textPrepare;
            case 'preflight': return text.textPreflight;
            case 'transfer': return text.textTransfer;
            case 'verify': return text.textVerify;
            case 'switch': return text.textSwitch;
            case 'rollback': return text.textRollback;
        }
        return '';
    }
    function badge( state ) {
        var word = words[state] || { text: state, kind: 'info' };
        var span = document.createElement( 'span' );
        span.className = 'a4-badge is-' + word.kind;
        span.setAttribute( 'data-deploy-state', state );
        span.textContent = word.text;
        return span;
    }
    function showHost( id, host ) {
        if ( !hosts ) return;
        var row = hosts.querySelector( 'tr[data-deploy-host="' + id.replace( /"/g, '' ) + '"]' );
        if ( !row ) {
            row = document.createElement( 'tr' );
            row.setAttribute( 'data-deploy-host', id );
            var name = document.createElement( 'td' );
            var code = document.createElement( 'code' );
            code.className = 'a4-code';
            code.textContent = id;
            name.appendChild( code );
            row.appendChild( name );
            row.appendChild( document.createElement( 'td' ) );
            var cell = document.createElement( 'td' );
            cell.className = 'a4-meta';
            row.appendChild( cell );
            hosts.appendChild( row );
        }
        var cells = row.children;
        var current = cells[1].querySelector( '[data-deploy-state]' );
        if ( !current || current.getAttribute( 'data-deploy-state' ) !== host.state ) {
            cells[1].textContent = '';
            cells[1].appendChild( badge( String( host.state || '' ) ) );
        }
        cells[2].textContent = phaseText( host.phase );
    }
    function showState( state, meter ) {
        if ( bar ) bar.className = 'a4-pct-' + Math.max( 0, Math.min( 100, parseInt( meter, 10 ) || 0 ) );
        if ( phase ) phase.textContent = state.ended ? '' : phaseText( state.phase );
        var list = state.hosts || {};
        for ( var id in list ) if ( Object.prototype.hasOwnProperty.call( list, id ) ) showHost( id, list[id] );
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
    connect();
}());
