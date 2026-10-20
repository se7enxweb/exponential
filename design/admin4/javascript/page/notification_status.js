/*
 * admin4 notification/status: runs the notifications (or a dry run) in the background and shows its lines.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/en/us/features/6.0/content-security-policy.md). The address of the job and the texts come from data attributes
 * on #nf-console.
 */
(function () {
    var runBtn = document.getElementById( 'nf-run-now' ), dryBtn = document.getElementById( 'nf-run-dry' );
    var box = document.getElementById( 'nf-console' ), state = document.getElementById( 'nf-run-state' );
    if ( !runBtn || !dryBtn || !box ) return;
    var jobUrl = box.getAttribute( 'data-job-url' );
    var T = {
        starting: box.getAttribute( 'data-text-starting' ),
        finished: box.getAttribute( 'data-text-finished' ),
        failed: box.getAttribute( 'data-text-failed' ),
        confirm: box.getAttribute( 'data-text-confirm' )
    };
    var csrf = document.querySelector( 'meta[name="csrf-token"]' );
    var timer = null, jobId = null, offset = 0;

    function tokenField() {
        var f = document.querySelector( 'input[name="ezxform_token"]' );
        return f ? f.value : '';
    }
    function post( data, done ) {
        var r = new XMLHttpRequest();
        r.open( 'POST', jobUrl, true );
        r.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
        r.setRequestHeader( 'X-Requested-With', 'XMLHttpRequest' );
        if ( csrf ) r.setRequestHeader( 'X-CSRF-Token', csrf.getAttribute( 'content' ) );
        var t = tokenField();
        if ( t ) data.ezxform_token = t;
        r.onload = function () {
            var a = null; try { a = JSON.parse( r.responseText ); } catch ( e ) {}
            done( r.status, a );
        };
        r.onerror = function () { done( 0, null ); };
        var p = []; for ( var k in data ) p.push( encodeURIComponent( k ) + '=' + encodeURIComponent( data[k] ) );
        r.send( p.join( '&' ) );
    }
    function line( text ) {
        box.appendChild( document.createTextNode( text + "\n" ) );
        box.scrollTop = box.scrollHeight;
    }
    function busy( on ) { runBtn.disabled = on; dryBtn.disabled = on; }
    function poll() {
        if ( !jobId ) return;
        var r = new XMLHttpRequest();
        r.open( 'GET', jobUrl + '/' + jobId + '/' + offset, true );
        r.setRequestHeader( 'X-Requested-With', 'XMLHttpRequest' );
        r.onload = function () {
            var a = null; try { a = JSON.parse( r.responseText ); } catch ( e ) {}
            if ( !a || !a.events ) { state.textContent = T.failed; busy( false ); jobId = null; return; }
            var failed = false;
            for ( var i = 0; i < a.events.length; i++ ) {
                var e = a.events[i];
                if ( e.type === 'end' ) { failed = e.result !== 'ok'; }
                else line( e.message );
            }
            offset = a.offset;
            if ( a.done ) { state.textContent = failed ? T.failed : T.finished; busy( false ); jobId = null; }
            else timer = window.setTimeout( poll, 1000 );
        };
        r.onerror = function () { timer = window.setTimeout( poll, 3000 ); };
        r.send();
    }
    function start( dry ) {
        if ( jobId ) return;
        if ( !dry && !window.confirm( T.confirm ) ) return;
        box.hidden = false; box.textContent = ''; offset = 0; busy( true ); state.textContent = T.starting;
        post( dry ? { Action: 'start', DryRun: '1' } : { Action: 'start' }, function ( status, a ) {
            if ( !a || !a.id ) { line( a && a.error ? a.error : 'HTTP ' + status ); state.textContent = T.failed; busy( false ); return; }
            jobId = a.id; state.textContent = '';
            poll();
        } );
    }
    runBtn.onclick = function () { start( false ); };
    dryBtn.onclick = function () { start( true ); };
})();
