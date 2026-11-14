/*
 * admin4 setup/aliases (Setup > Image aliases): follows a job's progress and the run history, and sends the filter and
 * lookup forms to their /(name)/value address. The feed is setup/aliasesfeed (JSON, short GET requests); every text is
 * set with textContent. The texts are data-text-* attributes of #ia-script. A file of its own, so the page runs under
 * a Content Security Policy without 'unsafe-inline'. Without it the page works as plain forms.
 */
( function () {
    var data = document.getElementById( 'ia-script' );
    if ( !data ) return;
    var text = {
        images: data.getAttribute( 'data-text-images' ),
        following: data.getAttribute( 'data-text-following' ),
        paused: data.getAttribute( 'data-text-paused' ),
        failed: data.getAttribute( 'data-text-failed' ),
        ended: data.getAttribute( 'data-text-ended' )
    };
    function tr( s, values ) { var k; for ( k in values ) s = s.split( '%' + k ).join( String( values[k] ) ); return s; }
    function encode( value ) {
        return window.a4ViewParameter ? window.a4ViewParameter.encode( value ) : String( value ).replace( /[~\/()%+\\?#]/g, function ( c ) {
            return '~' + ( '0' + c.charCodeAt( 0 ).toString( 16 ).toUpperCase() ).slice( -2 );
        } );
    }
    function setText( id, value ) { var el = document.getElementById( id ); if ( el ) el.textContent = value; }
    function getJSON( url, done, failed ) {
        var request = new XMLHttpRequest();
        request.open( 'GET', url, true );
        request.setRequestHeader( 'Accept', 'application/json' );
        request.onload = function () {
            var answer = null;
            try { answer = JSON.parse( request.responseText ); } catch ( e ) { answer = null; }
            if ( request.status === 200 && answer ) done( answer ); else failed();
        };
        request.onerror = failed;
        request.send();
    }

    document.querySelectorAll( '.a4-setup-alias .a4-setup-js-only' ).forEach( function ( el ) { el.hidden = false; } );

    // The filter and lookup forms: straight to the address the view would answer the POST with
    function addressForm( form, fields ) {
        if ( !form ) return;
        form.addEventListener( 'submit', function ( event ) {
            var base = form.getAttribute( 'data-base' ), suffix = '';
            if ( !base ) return;
            fields().forEach( function ( pair ) {
                if ( pair[1] !== '' ) suffix += '/(' + pair[0] + ')/' + encode( pair[1] );
            } );
            event.preventDefault();
            window.location.href = base + suffix;
        } );
    }
    var historyForm = document.getElementById( 'ia-history-form' );
    addressForm( historyForm, function () {
        var out = [];
        historyForm.querySelectorAll( '[data-name]' ).forEach( function ( el ) { out.push( [ el.getAttribute( 'data-name' ), el.value.trim() ] ); } );
        return out;
    } );
    var testForm = document.getElementById( 'ia-test-form' );
    addressForm( testForm, function () {
        var id = document.getElementById( 'ia-test-id' ).value.trim();
        if ( !/^\d{1,10}$/.test( id ) ) return [];
        return [ [ document.getElementById( 'ia-test-kind' ).value === 'object' ? 'object' : 'node', id ] ];
    } );

    // A job's progress
    var job = document.getElementById( 'ia-job' );
    if ( job && job.getAttribute( 'data-final' ) !== '1' ) {
        var bar = document.getElementById( 'ia-job-bar' );
        var jobTimer = null;
        var pollJob = function () {
            getJSON( job.getAttribute( 'data-feed' ), function ( answer ) {
                var j = answer.job, percent = Math.max( 0, Math.min( 100, parseInt( j.percent, 10 ) || 0 ) ), names = [], k;
                bar.className = bar.className.replace( /\ba4-pct-\d+\b/, '' ).trim() + ' a4-pct-' + percent;
                setText( 'ia-job-count', tr( text.images, { done: j.done, total: j.total } ) );
                setText( 'ia-job-message', ( answer.paused && !j.final ? text.paused + ' ' : '' ) + ( j.message || '' ) );
                setText( 'ia-job-state', j.state );
                for ( k in j.counts ) if ( j.counts[k] > 0 ) names.push( k + '=' + j.counts[k] );
                setText( 'ia-job-counts', names.join( ' ' ) );
                document.getElementById( 'ia-job-counts-row' ).hidden = names.length === 0;
                setText( 'ia-job-error', j.error || '' );
                document.getElementById( 'ia-job-error-row' ).hidden = !j.error;
                if ( j.final ) {
                    var cancel = document.getElementById( 'ia-job-cancel' );
                    if ( cancel ) cancel.hidden = true;
                    setText( 'ia-job-message', text.ended );
                    return;
                }
                jobTimer = window.setTimeout( pollJob, 2000 );
            }, function () {
                setText( 'ia-job-message', text.failed );
                jobTimer = window.setTimeout( pollJob, 5000 );
            } );
        };
        jobTimer = window.setTimeout( pollJob, 1000 );
    }

    // The run history, near real time
    var log = document.getElementById( 'ia-log' );
    var follow = document.getElementById( 'ia-follow' );
    if ( log && follow ) {
        var rows = document.getElementById( 'ia-log-rows' );
        var empty = document.getElementById( 'ia-log-empty' );
        var runBase = historyForm ? historyForm.getAttribute( 'data-base' ) + '/(run)/' : '';
        var badge = { fail: ' is-bad', defer: ' is-info', placeholder: ' is-warn', generate: ' is-ok', stage: ' is-info' };
        var logTimer = null;
        var cell = function ( row, className ) { var td = document.createElement( 'td' ); if ( className ) td.className = className; row.appendChild( td ); return td; };
        var code = function ( parent, value, className ) { var c = document.createElement( 'code' ); if ( className ) c.className = className; c.textContent = value; parent.appendChild( c ); return c; };
        var draw = function ( entries ) {
            var fragment = document.createDocumentFragment();
            entries.forEach( function ( e ) {
                var tr = document.createElement( 'tr' ), td, span, a;
                cell( tr, 'a4-nowrap' ).textContent = new Date( e.time * 1000 ).toLocaleString();
                span = document.createElement( 'span' );
                span.className = 'a4-badge' + ( badge[e.event] || '' );
                span.textContent = e.event;
                cell( tr ).appendChild( span );
                cell( tr ).textContent = e.result;
                code( cell( tr ), e.alias );
                cell( tr, 'a4-num' ).textContent = e.ms === null ? '' : String( e.ms );
                td = cell( tr );
                if ( e.run ) {
                    a = document.createElement( 'a' );
                    a.href = runBase + encode( e.run );
                    code( a, e.run );
                    td.appendChild( a );
                }
                td = cell( tr );
                code( td, e.path, 'a4-setup-alias-path' );
                if ( e.extra ) {
                    td.appendChild( document.createElement( 'br' ) );
                    span = document.createElement( 'span' );
                    span.className = 'a4-meta a4-setup-alias-path';
                    span.textContent = e.extra;
                    td.appendChild( span );
                }
                fragment.appendChild( tr );
            } );
            rows.textContent = '';
            rows.appendChild( fragment );
            if ( empty ) empty.hidden = entries.length > 0;
        };
        var pollLog = function () {
            if ( !follow.checked || document.hidden ) { logTimer = window.setTimeout( pollLog, 3000 ); return; }
            getJSON( log.getAttribute( 'data-feed' ), function ( answer ) {
                draw( answer.log || [] );
                setText( 'ia-follow-state', tr( text.following, { time: new Date().toLocaleTimeString() } ) );
                logTimer = window.setTimeout( pollLog, 3000 );
            }, function () {
                setText( 'ia-follow-state', text.failed );
                logTimer = window.setTimeout( pollLog, 6000 );
            } );
        };
        follow.addEventListener( 'change', function () {
            setText( 'ia-follow-state', follow.checked ? '' : text.paused );
        } );
        logTimer = window.setTimeout( pollLog, 3000 );
    }
} )();
