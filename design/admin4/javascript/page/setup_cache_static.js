/*
 * admin4 setup/cache, static cache: "Create new" runs the crawl in the page and shows its lines as they come (an
 * EventSource on the stream url), "Stop" closes the stream. The url and the texts are data-* attributes of
 * #staticcache-console. A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/eng-us/features/6.0/content-security-policy.md).
 */
(function () {
    var consoleEl = document.getElementById( 'staticcache-console' );
    if ( !consoleEl ) return;
    var streamUrl = consoleEl.getAttribute( 'data-stream-url' );
    var say       = function ( name ) { return consoleEl.getAttribute( 'data-text-' + name ) || ''; };
    var startBtn  = document.getElementById( 'staticcache-start' );
    var stopBtn   = document.getElementById( 'staticcache-stop' );
    var statusEl  = document.getElementById( 'staticcache-status' );
    var saEl      = document.getElementById( 'staticcache-siteaccess' );
    var source    = null;
    var lines     = 0;

    if ( !startBtn || typeof window.EventSource === 'undefined' )
        return; // No EventSource: the button stays an ordinary form submit.

    // The kinds of line the stream sends; each has its colour in modules/setup.css (.a4-setup-console-<kind>)
    var kinds = { phase: 1, 'phase-item': 1, ok: 1, warn: 1, error: 1, info: 1, done: 1 };

    function write( type, text )
    {
        var atBottom = consoleEl.scrollTop + consoleEl.clientHeight >= consoleEl.scrollHeight - 8;
        var line = document.createElement( 'div' );
        line.className = 'a4-setup-console-' + ( kinds[type] ? type : 'text' );
        line.appendChild( document.createTextNode( text ) );
        consoleEl.appendChild( line );
        lines++;
        // Only follow the tail while the operator is already at the bottom.
        if ( atBottom )
            consoleEl.scrollTop = consoleEl.scrollHeight;
    }

    function finish( message )
    {
        if ( source ) { source.close(); source = null; }
        startBtn.disabled = false;
        stopBtn.disabled  = true;
        statusEl.innerHTML = '';
        statusEl.appendChild( document.createTextNode( message ) );
    }

    // Run it in the page instead of posting the form, so every page appears as
    // it is written rather than after one long silent request.
    startBtn.onclick = function ( event )
    {
        if ( event && event.preventDefault ) event.preventDefault();
        if ( source ) return false;

        consoleEl.hidden = false;
        consoleEl.innerHTML = '';
        lines = 0;

        var pages = parseInt( document.getElementById( 'staticcache-max-pages' ).value, 10 );
        var depth = parseInt( document.getElementById( 'staticcache-max-depth' ).value, 10 );
        if ( isNaN( pages ) || pages < 1 ) pages = 2500;
        if ( isNaN( depth ) || depth < 0 ) depth = 12;

        // the options as view parameters, never a query string
        var url = streamUrl + ( saEl && saEl.value ? '/(siteaccess)/' + ( window.a4ViewParameter ? window.a4ViewParameter.encode( saEl.value ) : encodeURIComponent( saEl.value ) ) : '' )
                + '/(maxpages)/' + pages + '/(maxdepth)/' + depth;

        startBtn.disabled = true;
        stopBtn.disabled  = false;
        statusEl.innerHTML = '';
        statusEl.appendChild( document.createTextNode( say( 'running' ) ) );

        source = new EventSource( url );

        source.onmessage = function ( messageEvent )
        {
            var payload;
            try { payload = JSON.parse( messageEvent.data ); }
            catch ( e ) { return; }
            write( payload.type, payload.message );
            if ( payload.type === 'done' )
                finish( say( 'finished' ) );
        };

        source.addEventListener( 'end', function () { finish( say( 'finished' ) ); } );

        source.onerror = function ()
        {
            // EventSource reconnects on its own, which would start the run
            // again from the beginning; closing here keeps one run to a press.
            if ( lines === 0 )
                write( 'error', say( 'no-stream' ) );
            else
                write( 'warn', say( 'closed' ) );
            finish( say( 'stopped' ) );
        };

        return false;
    };

    // Closing the stream is all the browser can do: the run is already under
    // way on the server and the pages it has written stay written.
    stopBtn.onclick = function () {
        write( 'warn', say( 'stopped-by-operator' ) );
        finish( say( 'stopped' ) );
        return false;
    };
})();
