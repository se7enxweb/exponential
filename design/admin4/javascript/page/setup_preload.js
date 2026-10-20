/*
 * admin4 setup/preload: follows a running cache preload (the job, its log and its progress) while the page is open.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/en/us/features/6.0/content-security-policy.md).
 */
(function () {
    'use strict';
    var root = document.getElementById( 'exp-preload' );
    if ( !root || !window.XMLHttpRequest || !window.JSON ) return;
    var text = document.getElementById( 'exp-preload-text' ).dataset;
    var jobUrl = root.getAttribute( 'data-job-url' );
    var live = document.getElementById( 'exp-preload-live' );
    var consoleEl = document.getElementById( 'exp-preload-console' );
    var brokenEl = document.getElementById( 'exp-preload-broken' );
    var figuresEl = document.getElementById( 'exp-preload-figures' );
    var stateEl = document.getElementById( 'exp-preload-state' );
    var barEl = document.getElementById( 'exp-preload-bar' );
    var countEl = document.getElementById( 'exp-preload-count' );
    var currentEl = document.getElementById( 'exp-preload-current' );
    var startBtn = document.getElementById( 'preload-start' );
    var noteEl = document.getElementById( 'preload-start-note' );
    var jobId = root.getAttribute( 'data-running-id' ) || '';
    var offset = 0, timer = null, failuresInRow = 0;

    function tr( s, values ) {
        for ( var key in values || {} ) s = s.split( '%' + key ).join( values[key] );
        return s;
    }
    function el( tag, cls, txt ) {
        var e = document.createElement( tag );
        if ( cls ) e.className = cls;
        if ( txt !== undefined && txt !== null ) e.appendChild( document.createTextNode( String( txt ) ) );
        return e;
    }
    // Only an http address of the crawl becomes a link; anything else is text.
    function link( url ) {
        if ( !/^https?:\/\//i.test( url ) ) return document.createTextNode( url );
        var a = el( 'a', '', url );
        a.href = url; a.target = '_blank'; a.rel = 'noopener noreferrer';
        return a;
    }
    function write( type, message ) {
        var atBottom = consoleEl.scrollTop + consoleEl.clientHeight >= consoleEl.scrollHeight - 8;
        consoleEl.appendChild( el( 'div', 'a4-setup-console-' + String( type ).replace( /[^a-z-]/g, '' ), message ) );
        // follow the tail only while the reader is at the bottom
        if ( atBottom ) consoleEl.scrollTop = consoleEl.scrollHeight;
    }
    function renderBroken( broken ) {
        brokenEl.textContent = '';
        if ( !broken || !broken.length ) { var none = el( 'p', 'a4-meta a4-setup-gap', text.noBroken ); brokenEl.appendChild( none ); return; }
        brokenEl.appendChild( el( 'h3', '', text.brokenHead ) );
        var wrap = el( 'div', 'a4-table-wrap a4-setup-gap' ), table = el( 'table', 'a4-table' ), head = el( 'tr' );
        [ text.colLink, text.colStatus, text.colFrom ].forEach( function ( label ) { head.appendChild( el( 'th', '', label ) ); } );
        var thead = el( 'thead' ); thead.appendChild( head ); table.appendChild( thead );
        var tbody = el( 'tbody' );
        broken.forEach( function ( entry ) {
            var row = el( 'tr' ), cell = el( 'td', 'a4-setup-url' );
            cell.appendChild( link( entry.url ) ); row.appendChild( cell );
            row.appendChild( el( 'td', 'a4-num', entry.status ? entry.status : text.noResponse ) );
            var from = el( 'td', 'a4-setup-url' ), list = entry.referrers || [];
            if ( !list.length ) from.appendChild( el( 'span', 'a4-meta', text.startPage ) );
            list.forEach( function ( url, i ) { if ( i ) from.appendChild( el( 'br' ) ); from.appendChild( link( url ) ); } );
            if ( entry.more > 0 ) { from.appendChild( el( 'br' ) ); from.appendChild( el( 'span', 'a4-meta', tr( text.more, { count: entry.more } ) ) ); }
            row.appendChild( from ); tbody.appendChild( row );
        } );
        table.appendChild( tbody ); wrap.appendChild( table ); brokenEl.appendChild( wrap );
    }
    function figure( value, label, attention ) {
        var li = el( 'li', 'a4-figure' + ( attention ? ' is-attention' : '' ) );
        li.appendChild( el( 'strong', '', value ) ); li.appendChild( el( 'span', '', label ) );
        figuresEl.appendChild( li );
    }
    function showStatus( status ) {
        if ( !status ) return;
        var c = status.counts || {};
        var max = status.max_pages || 0;
        if ( barEl && max ) barEl.className = 'a4-pct-' + Math.min( 100, Math.round( ( c.fetched || 0 ) * 100 / max ) );
        if ( countEl ) countEl.textContent = tr( text.count, { pages: c.fetched || 0, max: max, broken: c.broken || 0 } );
        if ( currentEl ) currentEl.textContent = status.current || "";
        if ( status.running ) return;
        figuresEl.textContent = '';
        figure( c.fetched || 0, text.pages );
        figure( status.aliases === null ? text.notCounted : status.aliases, text.aliases );
        figure( ( c.broken || 0 ) + ( c.images_broken || 0 ), text.broken, ( c.broken || 0 ) + ( c.images_broken || 0 ) > 0 );
        if ( status.images ) figure( c.images || 0, text.images );
        if ( status.seconds !== null ) figure( status.seconds, text.duration );
        figuresEl.hidden = false;
    }
    function finish( status ) {
        timer = null; jobId = '';
        var state = status ? status.state : 'failed';
        stateEl.textContent = state === 'finished' ? text.finished : ( state === 'stopped' ? text.stopped : text.failed );
        showStatus( status );
        var bar = document.getElementById( 'exp-preload-status' );
        if ( bar ) {
            var pill = bar.querySelector( '.a4-status' );
            if ( pill ) { pill.classList.remove( 'is-running' ); pill.lastChild.textContent = stateEl.textContent; }
            var stop = document.getElementById( 'exp-preload-stop' );
            if ( stop ) stop.hidden = true;
            var refresh = document.getElementById( 'exp-preload-refresh' );
            if ( refresh ) { refresh.textContent = text.reload; refresh.hidden = false; }
        }
        if ( startBtn ) startBtn.disabled = false;
        if ( noteEl ) noteEl.textContent = text.idleNote;
    }
    function poll() {
        if ( !jobId ) return;
        var request = new XMLHttpRequest();
        request.open( 'GET', jobUrl + '/' + jobId + '/' + offset, true );
        request.setRequestHeader( 'X-Requested-With', 'XMLHttpRequest' );
        request.onload = function () {
            var answer = null;
            try { answer = JSON.parse( request.responseText ); } catch ( e ) {}
            if ( !answer || !answer.events ) {
                if ( ++failuresInRow > 5 ) { write( 'error', text.lost ); timer = null; return; }
                timer = window.setTimeout( poll, 3000 ); return;
            }
            failuresInRow = 0;
            answer.events.forEach( function ( event ) {
                try {
                    if ( event.type === 'report' ) renderBroken( event.broken );
                    write( event.type, event.message );
                } catch ( e ) { write( 'error', String( e ) ); }
            } );
            offset = answer.offset;
            showStatus( answer.status );
            if ( answer.done ) finish( answer.status );
            else timer = window.setTimeout( poll, 1000 );
        };
        request.onerror = function () { timer = window.setTimeout( poll, 3000 ); };
        request.send();
    }
    // Start, Dry run and Stop post the form; the page the server draws next shows the run, and this follows it.
    if ( jobId ) {
        live.hidden = false;
        stateEl.textContent = text.running;
        var refresh = document.getElementById( 'exp-preload-refresh' );
        if ( refresh ) refresh.hidden = true;
        poll();
    }
})();
