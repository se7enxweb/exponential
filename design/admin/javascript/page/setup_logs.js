/*
 * setup/logs (doc/features/6.0/log-viewer.md): the log viewer's page. It follows the open file through
 * setup/logsfeed with short requests (never a stream that holds a server process): the interval doubles while
 * nothing arrives, up to the configured maximum, comes back to the minimum when lines arrive, waits as long as a
 * 429 answer says, stops for an archive and while the tab is hidden. Only the lines in view are drawn (a fixed row
 * height; with wrapped lines every kept line is drawn instead), at most max_lines are kept. Space pauses and resumes,
 * End jumps to the newest line, Up/Down move, Enter opens a line with the lines of its request, its debug output and
 * the audit of that moment; n/N and the find box find within the kept lines. New lines are announced politely, at
 * most every few seconds. Every text from the log is put in with textContent, never as markup.
 *
 * The address is a permalink (setup/logs/view/<key>/(name)/value, expLogPermalink): the script keeps it in step with
 * the view (history.replaceState): (follow)/0 while paused, (wrap)/1 with wrapped lines, (offset)/N for the focused
 * entry; it honors them on load. A value is written as the server writes it: every character other than A-Z a-z 0-9
 * _ . , - as ~ and the two hex digits of each of its UTF-8 bytes. "Copy permalink" copies the link without the
 * unmasked choice.
 *
 * The values from the template are the data-* attributes of #log-script, the data it starts from the JSON of
 * #log-state. A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'.
 * The same file is in design/admin and design/admin4.
 */
(function () {
    'use strict';

    var script = document.getElementById( 'log-script' );
    var stateNode = document.getElementById( 'log-state' );
    var view = document.getElementById( 'log-view' );
    if ( !script || !stateNode || !view || !window.fetch )
        return;

    var state;
    try { state = JSON.parse( stateNode.textContent || '{}' ); } catch ( e ) { return; }
    if ( !state || !state.file )
        return;

    var data = function ( name ) { return script.getAttribute( 'data-' + name ) || ''; };
    // a user preference, stored as every admin preference is (user/preferences/set_and_exit, a POST with the form
    // token), so it holds for every log, every view and the next visit
    var savePreference = function ( name, value ) {
        var url = data( 'pref-url' );
        if ( !url || !window.fetch )
            return;
        var body = encodeURIComponent( data( 'token-field' ) || 'ezxform_token' ) + '=' + encodeURIComponent( data( 'token' ) );
        window.fetch( url + '/' + encodeURIComponent( name ) + '/' + encodeURIComponent( value ), {
            method: 'POST', credentials: 'same-origin', keepalive: true,
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: body
        } ).catch( function () {} );
    };
    var text = function ( name ) { return data( 'text-' + name ); };
    var fill = function ( template, values ) {
        return String( template ).replace( /%(\w+)/g, function ( all, key ) { return values.hasOwnProperty( key ) ? String( values[key] ) : all; } );
    };
    var slash = function ( url ) { return url && url.charAt( url.length - 1 ) !== '/' ? url + '/' : url; };
    var urls = {
        feed: data( 'feed-url' ), page: data( 'page-url' ), node: slash( data( 'node-url' ) ), klass: slash( data( 'class-url' ) ),
        finder: data( 'finder-url' ), template: slash( data( 'template-url' ) ), audit: slash( data( 'audit-url' ) ),
        permalink: data( 'permalink-url' ), base: data( 'base-url' )
    };
    // a value of a permalink, as expLogPermalink::encode() writes it
    var encodeValue = function ( value ) {
        var out = '';
        var bytes = unescape( encodeURIComponent( String( value ) ) );
        for ( var i = 0; i < bytes.length; i++ )
        {
            var c = bytes.charAt( i );
            if ( /[A-Za-z0-9_.,\-]/.test( c ) )
                out += c;
            else
                out += '~' + ( '0' + bytes.charCodeAt( i ).toString( 16 ).toUpperCase() ).slice( -2 );
        }
        return out;
    };
    // the permalink of the view without the parameters the script writes itself
    var VIEW_PARAMS = /\/\((follow|wrap|offset|unmask)\)\/[^\/]*/g;
    var permalinkBase = ( urls.permalink || urls.base || '' ).replace( VIEW_PARAMS, '' );
    var searchQuery = data( 'search-query' );
    var canUnmask = data( 'can-unmask' ) === '1';
    var client = state.client || {};
    var minInterval = Math.max( 250, client.poll_interval || 1000 );
    var maxInterval = Math.max( minInterval, client.max_poll_interval || 15000 );
    var maxLines = Math.max( 100, client.max_lines || 5000 );
    var WRAP_DOM_CAP = 1500;
    var BUFFER_ROWS = 20;
    var ANNOUNCE_EVERY = 5000;

    // ---- the viewer's choice to see the lines unmasked, remembered for this tab ----
    var store = {
        get: function ( k ) { try { return window.sessionStorage.getItem( k ); } catch ( e ) { return null; } },
        set: function ( k, v ) { try { window.sessionStorage.setItem( k, v ); } catch ( e ) { /* not kept */ } }
    };
    var unmaskLink = document.getElementById( 'log-unmask' );
    if ( unmaskLink )
    {
        unmaskLink.addEventListener( 'click', function () {
            store.set( 'expLogViewerUnmask', unmaskLink.getAttribute( 'data-unmasked' ) === '1' ? '0' : '1' );
        } );
    }
    if ( canUnmask && !state.unmasked && store.get( 'expLogViewerUnmask' ) === '1' && permalinkBase && !/\/\(unmask\)\//.test( window.location.pathname ) )
    {
        window.location.replace( permalinkBase + '/(unmask)/1' );
        return;
    }

    // ---- the elements ----
    var el = function ( id ) { return document.getElementById( id ); };
    var pauseButton = el( 'log-pause' ), pauseLabel = el( 'log-pause-label' ), newestButton = el( 'log-newest' );
    var followBox = el( 'log-follow' ), wrapBox = el( 'log-wrap' );
    var findBox = el( 'log-find' ), findPrev = el( 'log-find-prev' ), findNext = el( 'log-find-next' ), findCount = el( 'log-find-count' );
    var statusBox = el( 'log-status' ), statusText = el( 'log-status-text' ), sizeBox = el( 'log-size' );
    var live = el( 'log-live' );
    var detail = el( 'log-detail' ), detailTitle = el( 'log-detail-title' ), detailFacts = el( 'log-detail-facts' );
    var detailText = el( 'log-detail-text' ), detailActions = el( 'log-detail-actions' ), detailRelated = el( 'log-detail-related' );
    var detailClose = el( 'log-detail-close' ), moreLink = el( 'log-more' );

    var searching = !!state.searching;
    var file = state.file;
    var source = searching ? state.search : state.initial;
    var entries = [];
    var serial = 0;
    var next = source && typeof source.next === 'number' ? source.next : 0;
    var inode = source && source.ino ? source.ino : ( file.ino || 0 );
    var liveFile = !searching && !file.archive && !( source && source.live === false );
    var paused = false, follow = true, wrap = false;
    var interval = minInterval, timer = null, inFlight = false, failures = 0;
    var active = -1;              // index in entries of the active row
    var matches = [], matchAt = -1, findText = '';
    var pendingNew = 0, lastAnnounce = 0, announceTimer = null;
    var rowHeight = 0;
    var cursor = searching && state.search ? ( state.search.cursor || '' ) : '';

    // ---- entries ----
    var plain = function ( entry ) {
        if ( entry._text === undefined )
        {
            var parts = [];
            for ( var i = 0; i < ( entry.p || [] ).length; i++ )
                parts.push( entry.p[i][0] );
            entry._text = ( entry.ts || '' ) + ' ' + ( entry.src || '' ) + ' ' + ( entry.sa || '' ) + ' ' + ( entry.ip || '' ) + ' ' + ( entry.u || '' ) + ' ' + parts.join( '' );
            entry._lower = entry._text.toLowerCase();
        }
        return entry._text;
    };
    var add = function ( list, isSearch ) {
        var added = 0;
        for ( var i = 0; i < list.length; i++ )
        {
            var e = list[i];
            var last = entries.length ? entries[entries.length - 1] : null;
            if ( e.c && !isSearch && last && !last.sep )
            {
                // the rest of the entry the last poll ended in
                last.p = ( last.p || [] ).concat( [[ '\n' ]], e.p || [] );
                last.e = e.e;
                last._text = undefined;
                continue;
            }
            e._n = ++serial;
            entries.push( e );
            added++;
        }
        var dropped = entries.length - maxLines;
        if ( dropped > 0 )
        {
            entries.splice( 0, dropped );
            active = active >= dropped ? active - dropped : -1;
            if ( !follow && rowHeight && !wrap )
                view.scrollTop = Math.max( 0, view.scrollTop - dropped * rowHeight );
        }
        return added;
    };
    var hash = function ( s ) {
        var h = 0;
        for ( var i = 0; i < s.length; i++ )
            h = ( h * 31 + s.charCodeAt( i ) ) | 0;
        return Math.abs( h ) % 8 + 1;
    };

    // ---- links of the parts ----
    var linkOf = function ( type, value ) {
        var m;
        switch ( type )
        {
            case 'reference':
                // setup/logs?key=... answers 303 with the permalink of the search
                return urls.page + '?key=' + encodeURIComponent( file.key || '' ) + ( file.rotation ? '&archive=' + encodeURIComponent( file.rotation ) : '' ) +
                       '&reference=' + encodeURIComponent( value ) + '&scope=kernel&rotations=1' + ( state.unmasked ? '&unmask=1' : '' );
            case 'url':
                try
                {
                    var u = new URL( value, window.location.href );
                    return u.origin === window.location.origin ? u.href : '';
                }
                catch ( e ) { return ''; }
            case 'node':
                return urls.node ? urls.node + encodeURIComponent( value ) : '';
            case 'object':
            case 'user':
                return urls.finder ? urls.finder + '?SearchText=' + encodeURIComponent( value ) + '&SearchType=object' : '';
            case 'class':
                m = /^(\d+)\|/.exec( value );
                return m && urls.klass ? urls.klass + m[1] : '';
            case 'file':
                m = /\/templates\/(.+\.tpl)$/.exec( value );
                return m && urls.template ? urls.template + m[1].split( '/' ).map( encodeURIComponent ).join( '/' ) : '';
        }
        return '';
    };
    var renderParts = function ( target, parts, oneLine ) {
        for ( var i = 0; i < parts.length; i++ )
        {
            var part = parts[i];
            var str = oneLine ? String( part[0] ).replace( /\s*\n\s*/g, ' ↵ ' ) : String( part[0] );
            var href = part.length > 2 ? linkOf( part[1], part[2] ) : '';
            if ( href )
            {
                var a = document.createElement( 'a' );
                a.href = href;
                a.className = 'a4-setup-log-link is-' + part[1];
                a.textContent = str;
                a.tabIndex = -1;
                target.appendChild( a );
            }
            else
                target.appendChild( document.createTextNode( str ) );
        }
    };
    var span = function ( cls, str ) {
        var s = document.createElement( 'span' );
        s.className = cls;
        s.textContent = str;
        return s;
    };
    var levelName = function ( lv ) { return text( 'level-' + lv ) || lv; };

    var buildRow = function ( entry, index ) {
        var li = document.createElement( 'li' );
        li.id = 'log-row-' + entry._n;
        li.setAttribute( 'data-index', index );
        var cls = 'a4-setup-log-row is-' + ( entry.lv || 'notice' );
        if ( entry.sep )
            cls += ' is-separator';
        if ( entry.rk )
            cls += ' a4-setup-log-rk-' + hash( entry.rk );
        if ( index === active )
            cls += ' is-active';
        if ( matches.length && matchAt >= 0 && matches[matchAt] === index )
            cls += ' is-current-match';
        else if ( findText && entry._lower !== undefined && entry._lower.indexOf( findText ) !== -1 )
            cls += ' is-match';
        li.className = cls;
        if ( entry.sep )
        {
            li.appendChild( span( 'a4-setup-log-msg', text( 'rotated' ) ) );
            return li;
        }
        li.appendChild( span( 'a4-setup-log-time', entry.ts || '' ) );
        li.appendChild( span( 'a4-setup-log-level is-' + entry.lv, levelName( entry.lv ) ) );
        if ( entry.f )
            li.appendChild( span( 'a4-setup-log-file', entry.f ) );
        if ( entry.src )
            li.appendChild( span( 'a4-setup-log-src a4-setup-log-src-' + hash( entry.src ), entry.src ) );
        var req = [ entry.sa, entry.ip, entry.u ].filter( function ( v ) { return v; } ).join( ' ' );
        if ( req )
            li.appendChild( span( 'a4-setup-log-req', req ) );
        var msg = span( 'a4-setup-log-msg', '' );
        renderParts( msg, entry.p || [], !wrap );
        if ( entry.x )
            msg.appendChild( document.createTextNode( ' ' + text( 'cut' ) ) );
        li.appendChild( msg );
        return li;
    };

    // ---- the view: a spacer as tall as every kept row, and the rows in view drawn inside it ----
    var oldHeading = document.getElementById( 'log-view-title' );
    var headingText = oldHeading ? oldHeading.textContent : '';
    var emptyOld = document.getElementById( 'log-empty' );
    var emptyText = emptyOld ? emptyOld.textContent : '';
    view.textContent = '';
    view.classList.add( 'is-scripted' );
    view.setAttribute( 'role', 'group' );
    var heading = document.createElement( 'h2' );
    heading.className = 'a4-sr-only';
    heading.id = 'log-view-title';
    heading.textContent = headingText;
    var empty = document.createElement( 'p' );
    empty.className = 'a4-empty';
    empty.id = 'log-empty';
    empty.textContent = emptyText;
    var spacer = document.createElement( 'div' );
    spacer.className = 'a4-setup-log-spacer';
    var list = document.createElement( 'ol' );
    list.className = 'a4-setup-log-rows';
    list.setAttribute( 'role', 'list' );
    spacer.appendChild( list );
    view.appendChild( heading );
    view.appendChild( empty );
    view.appendChild( spacer );

    var measure = function () {
        if ( wrap )
            return;
        var probe = buildRow( { _n: 0, ts: 'x', lv: 'notice', p: [[ 'x' ]] }, -1 );
        probe.id = '';
        list.textContent = '';
        list.appendChild( probe );
        rowHeight = probe.getBoundingClientRect().height || 20;
        list.textContent = '';
    };

    var scheduled = false;
    var render = function () {
        scheduled = false;
        list.textContent = '';
        empty.hidden = entries.length > 0 || emptyText === '';
        if ( !entries.length )
        {
            spacer.style.height = '';
            list.style.transform = '';
            return;
        }
        var from, to;
        if ( wrap )
        {
            from = Math.max( 0, entries.length - WRAP_DOM_CAP );
            to = entries.length;
            spacer.style.height = '';
            list.style.transform = '';
        }
        else
        {
            if ( !rowHeight )
                measure();
            spacer.style.height = ( entries.length * rowHeight ) + 'px';
            from = Math.max( 0, Math.floor( view.scrollTop / rowHeight ) - BUFFER_ROWS );
            to = Math.min( entries.length, Math.ceil( ( view.scrollTop + view.clientHeight ) / rowHeight ) + BUFFER_ROWS );
            list.style.transform = 'translateY(' + ( from * rowHeight ) + 'px)';
        }
        var frag = document.createDocumentFragment();
        for ( var i = from; i < to; i++ )
        {
            plain( entries[i] );
            frag.appendChild( buildRow( entries[i], i ) );
        }
        list.appendChild( frag );
        var activeEntry = active >= 0 ? entries[active] : null;
        if ( activeEntry && active >= from && active < to ) view.setAttribute( 'aria-activedescendant', 'log-row-' + activeEntry._n ); else view.removeAttribute( 'aria-activedescendant' );
        syncLater();
    };
    var schedule = function () {
        if ( !scheduled )
        {
            scheduled = true;
            window.requestAnimationFrame( render );
        }
    };

    // ---- the address: the permalink of what is shown ----
    var permalinkField = document.getElementById( 'log-permalink' ), copyButton = document.getElementById( 'log-copy-link' );
    var syncTimer = null;
    var viewPath = function () {
        var path = permalinkBase;
        if ( !path )
            return '';
        var focused = active >= 0 && entries[active] && !follow ? entries[active] : null;
        if ( focused && typeof focused.o === 'number' )
            path += '/(offset)/' + encodeValue( focused.o );
        if ( paused && liveFile && !searching )
            path += '/(follow)/0';
        // wrapping is the viewer's preference (admin_logviewer_wrap), not part of a view's address
        return path;
    };
    var syncUrl = function () {
        syncTimer = null;
        var path = viewPath();
        if ( !path )
            return;
        if ( permalinkField )
            permalinkField.value = window.location.origin + path;
        var shown = path + ( state.unmasked ? '/(unmask)/1' : '' );
        if ( window.history && window.history.replaceState && window.location.pathname !== shown )
        {
            try { window.history.replaceState( window.history.state, '', shown ); } catch ( e ) { /* the address stays */ }
        }
    };
    var syncLater = function () {
        if ( syncTimer === null )
            syncTimer = window.setTimeout( syncUrl, 400 );
    };
    var atBottom = function () {
        return view.scrollTop + view.clientHeight >= view.scrollHeight - Math.max( 4, rowHeight );
    };
    var toBottom = function () {
        render();
        view.scrollTop = view.scrollHeight;
        schedule();
    };
    var scrollToIndex = function ( index ) {
        if ( index < 0 || index >= entries.length )
            return;
        if ( wrap )
        {
            render();
            var row = document.getElementById( 'log-row-' + entries[index]._n );
            if ( row && row.scrollIntoView )
                row.scrollIntoView( { block: 'nearest' } );
            return;
        }
        var top = index * rowHeight;
        if ( top < view.scrollTop )
            view.scrollTop = top;
        else if ( top + rowHeight > view.scrollTop + view.clientHeight )
            view.scrollTop = top + rowHeight - view.clientHeight;
        render();
    };
    var setFollow = function ( on ) {
        follow = on;
        if ( followBox )
            followBox.checked = on;
        if ( on )
            toBottom();
    };

    view.addEventListener( 'scroll', function () {
        if ( follow && !atBottom() && !searching )
            setFollow( false );
        schedule();
    } );
    window.addEventListener( 'resize', function () { rowHeight = 0; schedule(); } );

    // ---- announcements: politely, at most one every few seconds ----
    var say = function ( message ) {
        if ( live )
        {
            live.textContent = '';
            window.setTimeout( function () { live.textContent = message; }, 50 );
        }
        lastAnnounce = Date.now();
    };
    var announceNew = function ( count ) {
        pendingNew += count;
        if ( announceTimer )
            return;
        var wait = Math.max( 0, ANNOUNCE_EVERY - ( Date.now() - lastAnnounce ) );
        announceTimer = window.setTimeout( function () {
            announceTimer = null;
            if ( pendingNew > 0 )
                say( pendingNew === 1 ? text( 'new-line' ) : fill( text( 'new-lines' ), { count: pendingNew } ) );
            pendingNew = 0;
        }, wait );
    };

    // ---- status ----
    var formatSize = function ( bytes ) {
        var units = [ 'B', 'KB', 'MB', 'GB' ], n = bytes, u = 0;
        while ( n >= 1024 && u < units.length - 1 )
        {
            n /= 1024;
            u++;
        }
        return ( u ? n.toLocaleString( undefined, { maximumFractionDigits: 1 } ) : String( n ) ) + ' ' + units[u];
    };
    var setStatus = function ( message, kind ) {
        if ( statusText )
            statusText.textContent = message;
        if ( statusBox )
        {
            statusBox.classList.remove( 'is-running', 'is-warn', 'is-bad' );
            if ( kind )
                statusBox.classList.add( kind );
        }
    };

    // ---- following the file ----
    var feedUrl = function ( params ) {
        var q = [];
        for ( var k in params )
            if ( params.hasOwnProperty( k ) )
                q.push( encodeURIComponent( k ) + '=' + encodeURIComponent( params[k] ) );
        if ( state.unmasked )
            q.push( 'unmask=1' );
        return urls.feed + ( urls.feed.indexOf( '?' ) === -1 ? '?' : '&' ) + q.join( '&' );
    };
    var getJSON = function ( url ) {
        return window.fetch( url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } } ).then( function ( response ) {
            return response.text().then( function ( body ) {
                var json = null;
                try { json = JSON.parse( body ); } catch ( e ) { json = null; }
                return { status: response.status, json: json, retry: parseInt( response.headers.get( 'Retry-After' ) || '0', 10 ) || 0 };
            } );
        } );
    };
    var plan = function ( wait ) {
        window.clearTimeout( timer );
        timer = null;
        if ( paused || !liveFile || document.hidden )
            return;
        timer = window.setTimeout( poll, wait );
    };
    var poll = function () {
        timer = null;
        if ( paused || !liveFile || document.hidden || inFlight )
            return;
        inFlight = true;
        getJSON( feedUrl( { action: 'tail', file: file.id, offset: next, ino: inode } ) ).then( function ( r ) {
            inFlight = false;
            if ( r.status === 429 )
            {
                var wait = Math.max( 1, ( r.json && r.json.retry_after ) || r.retry || 5 );
                setStatus( fill( text( 'waiting' ), { seconds: wait } ), 'is-warn' );
                interval = maxInterval;
                return plan( wait * 1000 );
            }
            if ( r.status === 404 || ( r.json && r.json.error ) )
            {
                setStatus( text( 'gone' ), 'is-bad' );
                liveFile = false;
                return;
            }
            if ( r.status !== 200 || !r.json )
                throw new Error( 'status ' + r.status );
            failures = 0;
            var d = r.json;
            var wasBottom = follow || atBottom();
            if ( d.reset && entries.length )
            {
                entries.push( { sep: true, _n: ++serial, lv: 'notice', p: [] } );
                say( text( 'rotated' ) );
            }
            var added = add( d.entries || [], false );
            next = d.next;
            inode = d.ino;
            if ( sizeBox && typeof d.size === 'number' )
                sizeBox.textContent = formatSize( d.size );
            setStatus( text( 'following' ), 'is-running' );
            if ( added )
            {
                interval = minInterval;
                announceNew( added );
                refreshMatches();
                if ( wasBottom && follow )
                    toBottom();
                else
                    schedule();
            }
            else
                interval = Math.min( maxInterval, interval * 2 );
            plan( d.more ? 0 : interval );
        } ).catch( function () {
            inFlight = false;
            failures++;
            setStatus( text( 'offline' ), 'is-warn' );
            interval = Math.min( maxInterval, Math.max( minInterval, interval * 2 ) );
            plan( interval * Math.min( 4, failures ) );
        } );
    };
    var setPaused = function ( on ) {
        paused = on;
        if ( pauseButton )
        {
            pauseButton.setAttribute( 'aria-pressed', on ? 'true' : 'false' );
            var icon = pauseButton.querySelector( '.a4-icon' );
            if ( icon )
            {
                icon.classList.toggle( 'a4-bi-pause-fill', !on );
                icon.classList.toggle( 'a4-bi-play-fill', on );
            }
        }
        if ( pauseLabel )
            pauseLabel.textContent = on ? text( 'resume' ) : text( 'pause' );
        setStatus( on ? text( 'paused' ) : text( 'following' ), on ? 'is-warn' : 'is-running' );
        say( on ? text( 'paused' ) : text( 'following' ) );
        syncLater();
        if ( on )
            plan( 0 );
        else
        {
            interval = minInterval;
            plan( 0 );
        }
    };
    document.addEventListener( 'visibilitychange', function () {
        if ( !document.hidden )
        {
            interval = minInterval;
            plan( 0 );
        }
    } );

    // ---- find within the kept lines ----
    var refreshMatches = function () {
        matches = [];
        if ( findText )
            for ( var i = 0; i < entries.length; i++ )
            {
                plain( entries[i] );
                if ( !entries[i].sep && entries[i]._lower.indexOf( findText ) !== -1 )
                    matches.push( i );
            }
        if ( matchAt >= matches.length )
            matchAt = matches.length - 1;
        if ( findCount )
            findCount.textContent = !findText ? '' : ( matches.length ? fill( text( 'match' ), { index: matchAt + 1, count: matches.length } ) : text( 'no-match' ) );
    };
    var findStep = function ( step ) {
        if ( !findText || !matches.length )
        {
            refreshMatches();
            if ( findText && !matches.length )
                say( text( 'no-match' ) );
            return;
        }
        // from the active row onwards
        var at = matchAt;
        if ( at < 0 )
        {
            at = step > 0 ? -1 : matches.length;
            for ( var i = 0; i < matches.length; i++ )
                if ( active >= 0 && matches[i] >= active ) { at = step > 0 ? i - 1 : i; break; }
        }
        matchAt = ( at + step + matches.length ) % matches.length;
        active = matches[matchAt];
        if ( follow )
            setFollow( false );
        scrollToIndex( active );
        refreshMatches();
        render();
        say( fill( text( 'match' ), { index: matchAt + 1, count: matches.length } ) );
    };
    if ( findBox )
    {
        findBox.addEventListener( 'input', function () {
            findText = findBox.value.trim().toLowerCase();
            matchAt = -1;
            refreshMatches();
            schedule();
        } );
        findBox.addEventListener( 'keydown', function ( ev ) {
            if ( ev.key === 'Enter' )
            {
                ev.preventDefault();
                findStep( ev.shiftKey ? -1 : 1 );
            }
            else if ( ev.key === 'Escape' )
                view.focus();
        } );
    }
    if ( findPrev )
        findPrev.addEventListener( 'click', function () { findStep( -1 ); } );
    if ( findNext )
        findNext.addEventListener( 'click', function () { findStep( 1 ); } );

    // ---- one entry opened ----
    var lastFocus = null;
    var fact = function ( label, value, href ) {
        if ( !value )
            return;
        var dt = document.createElement( 'dt' );
        dt.textContent = label;
        var dd = document.createElement( 'dd' );
        if ( href )
        {
            var a = document.createElement( 'a' );
            a.href = href;
            a.textContent = value;
            dd.appendChild( a );
        }
        else
            dd.textContent = value;
        detailFacts.appendChild( dt );
        detailFacts.appendChild( dd );
    };
    var relatedList = function ( title, list, none ) {
        var section = document.createElement( 'div' );
        section.className = 'a4-setup-log-related-part';
        var h = document.createElement( 'h3' );
        h.className = 'a4-h3';
        h.textContent = title;
        section.appendChild( h );
        if ( !list.length )
        {
            section.appendChild( span( 'a4-meta', none ) );
            return section;
        }
        var ol = document.createElement( 'ol' );
        ol.className = 'a4-setup-log-plain';
        for ( var i = 0; i < list.length; i++ )
        {
            var e = list[i];
            plain( e );
            var li = buildRow( e, -1 );
            li.id = '';
            li.className = li.className.replace( / is-(active|match|current-match)\b/g, '' );
            ol.appendChild( li );
        }
        section.appendChild( ol );
        return section;
    };
    var openEntry = function ( index ) {
        var entry = entries[index];
        if ( !entry || entry.sep || !detail )
            return;
        lastFocus = document.activeElement;
        detailFacts.textContent = '';
        detailText.textContent = '';
        detailActions.textContent = '';
        detailRelated.textContent = '';
        detailTitle.textContent = fill( text( 'entry' ), { time: entry.ts || '' } );
        fact( text( 'time' ), entry.ts );
        fact( text( 'level' ), levelName( entry.lv ) );
        fact( text( 'source' ), entry.src );
        fact( text( 'siteaccess' ), entry.sa );
        fact( text( 'client' ), entry.ip );
        fact( text( 'request' ), entry.u, linkOf( 'url', entry.u ) );
        fact( text( 'file' ), entry.f || file.id );
        fact( text( 'offset' ), String( entry.o ) );
        renderParts( detailText, entry.p || [], false );
        var anchors = detailText.querySelectorAll( 'a' );
        for ( var i = 0; i < anchors.length; i++ )
            anchors[i].removeAttribute( 'tabindex' );
        ( entry.refs || [] ).forEach( function ( ref ) {
            var a = document.createElement( 'a' );
            a.className = 'a4-btn a4-btn-small';
            a.href = linkOf( 'reference', ref );
            a.textContent = fill( text( 'reference' ), { reference: ref } );
            detailActions.appendChild( a );
        } );
        detail.hidden = false;
        detail.focus();
        if ( entry.t === null || entry.t === undefined )
            return;
        detailRelated.appendChild( span( 'a4-meta', text( 'related-loading' ) ) );
        getJSON( feedUrl( { action: 'related', file: entry.f || file.id, offset: entry.o } ) ).then( function ( r ) {
            detailRelated.textContent = '';
            if ( r.status !== 200 || !r.json )
            {
                detailRelated.appendChild( span( 'a4-meta', fill( text( 'failed' ), { error: r.status } ) ) );
                return;
            }
            var all = r.json.entries || [];
            var debug = all.filter( function ( e ) { return /(^|\/)(debug|notice)\.log(\.\d+)?$/.test( e.f || '' ); } );
            detailRelated.appendChild( relatedList( text( 'related' ), all, text( 'related-none' ) ) );
            detailRelated.appendChild( relatedList( text( 'debug' ), debug, text( 'debug-none' ) ) );
            if ( urls.audit && r.json.audit && r.json.audit.from )
            {
                var a = document.createElement( 'a' );
                a.className = 'a4-btn a4-btn-small';
                a.href = urls.audit + '(from)/' + encodeURIComponent( r.json.audit.from ) + '/(to)/' + encodeURIComponent( r.json.audit.to );
                a.textContent = text( 'audit' );
                detailActions.appendChild( a );
            }
        } ).catch( function ( err ) {
            detailRelated.textContent = '';
            detailRelated.appendChild( span( 'a4-meta', fill( text( 'failed' ), { error: err && err.message ? err.message : '' } ) ) );
        } );
    };
    var closeEntry = function () {
        if ( !detail || detail.hidden )
            return;
        detail.hidden = true;
        ( lastFocus && lastFocus.focus ? lastFocus : view ).focus();
    };
    if ( detailClose )
        detailClose.addEventListener( 'click', closeEntry );
    if ( detail )
        detail.addEventListener( 'keydown', function ( ev ) {
            if ( ev.key === 'Escape' )
            {
                ev.preventDefault();
                closeEntry();
            }
        } );

    // ---- mouse and keyboard on the lines ----
    list.addEventListener( 'click', function ( ev ) {
        if ( ev.target.closest && ev.target.closest( 'a' ) )
            return;
        var row = ev.target.closest ? ev.target.closest( 'li[data-index]' ) : null;
        if ( !row )
            return;
        active = parseInt( row.getAttribute( 'data-index' ), 10 );
        render();
        openEntry( active );
    } );
    var move = function ( to ) {
        if ( !entries.length )
            return;
        active = Math.max( 0, Math.min( entries.length - 1, to ) );
        if ( follow && active < entries.length - 1 )
            setFollow( false );
        scrollToIndex( active );
        render();
    };
    view.addEventListener( 'keydown', function ( ev ) {
        if ( ev.target !== view || ev.altKey || ev.ctrlKey || ev.metaKey )
            return;
        var page = Math.max( 1, Math.floor( view.clientHeight / ( rowHeight || 20 ) ) - 1 );
        switch ( ev.key )
        {
            case ' ':
            case 'Spacebar':
                if ( liveFile || paused )
                {
                    ev.preventDefault();
                    setPaused( !paused );
                }
                break;
            case 'End':
                ev.preventDefault();
                active = entries.length - 1;
                setFollow( !searching );
                if ( searching )
                    move( active );
                break;
            case 'Home':
                ev.preventDefault();
                move( 0 );
                break;
            case 'ArrowDown':
                ev.preventDefault();
                move( active < 0 ? Math.floor( view.scrollTop / ( rowHeight || 20 ) ) : active + 1 );
                break;
            case 'ArrowUp':
                ev.preventDefault();
                move( active < 0 ? entries.length - 1 : active - 1 );
                break;
            case 'PageDown':
                ev.preventDefault();
                move( ( active < 0 ? 0 : active ) + page );
                break;
            case 'PageUp':
                ev.preventDefault();
                move( ( active < 0 ? entries.length - 1 : active ) - page );
                break;
            case 'Enter':
                if ( active >= 0 )
                {
                    ev.preventDefault();
                    openEntry( active );
                }
                break;
            case 'n':
            case 'N':
                if ( findText )
                {
                    ev.preventDefault();
                    findStep( ev.key === 'N' ? -1 : 1 );
                }
                else if ( findBox )
                {
                    ev.preventDefault();
                    findBox.focus();
                }
                break;
            case '/':
                if ( findBox )
                {
                    ev.preventDefault();
                    findBox.focus();
                }
                break;
        }
    } );

    // ---- the controls ----
    var enable = function ( node ) { if ( node ) node.disabled = false; };
    [ pauseButton, newestButton, followBox, wrapBox, findBox, findPrev, findNext ].forEach( enable );
    if ( pauseButton )
    {
        pauseButton.hidden = !liveFile;
        pauseButton.addEventListener( 'click', function () { setPaused( !paused ); } );
    }
    if ( newestButton )
        newestButton.addEventListener( 'click', function () {
            active = entries.length - 1;
            setFollow( true );
            view.focus();
        } );
    if ( followBox )
    {
        followBox.checked = !searching;
        follow = !searching;
        followBox.addEventListener( 'change', function () { setFollow( followBox.checked ); } );
        if ( searching )
            followBox.disabled = true;
    }
    if ( wrapBox )
        wrapBox.addEventListener( 'change', function () {
            wrap = wrapBox.checked;
            savePreference( 'admin_logviewer_wrap', wrap ? '1' : '0' );
            view.classList.toggle( 'is-wrapped', wrap );
            rowHeight = 0;
            if ( follow )
                toBottom();
            else
            {
                render();
                scrollToIndex( active );
            }
        } );

    // ---- more search results ----
    if ( moreLink && searching )
        moreLink.addEventListener( 'click', function ( ev ) {
            if ( !cursor )
                return;
            ev.preventDefault();
            moreLink.setAttribute( 'aria-disabled', 'true' );
            say( text( 'more-loading' ) );
            var url = urls.feed + ( urls.feed.indexOf( '?' ) === -1 ? '?' : '&' ) + 'action=search&' + searchQuery + '&cursor=' + encodeURIComponent( cursor );
            getJSON( url ).then( function ( r ) {
                moreLink.removeAttribute( 'aria-disabled' );
                if ( r.status !== 200 || !r.json )
                {
                    say( fill( text( 'failed' ), { error: r.status } ) );
                    return;
                }
                var added = add( r.json.entries || [], true );
                cursor = r.json.cursor || '';
                refreshMatches();
                schedule();
                say( fill( text( 'new-lines' ), { count: added } ) );
                if ( !cursor )
                {
                    moreLink.hidden = true;
                    say( text( 'more-done' ) );
                }
            } ).catch( function () {
                moreLink.removeAttribute( 'aria-disabled' );
                say( fill( text( 'failed' ), { error: '' } ) );
            } );
        } );

    // ---- the file chooser opens the option's permalink ----
    var picker = el( 'log-picker' ), pickerForm = el( 'log-picker-form' );
    var openPicked = function ( ev ) {
        var option = picker && picker.options[picker.selectedIndex];
        var href = option ? option.getAttribute( 'data-href' ) : '';
        if ( !href )
            return;
        if ( ev )
            ev.preventDefault();
        window.location.href = href;
    };
    if ( picker )
        picker.addEventListener( 'change', function () { openPicked( null ); } );
    if ( pickerForm )
        pickerForm.addEventListener( 'submit', openPicked );

    // ---- copy the permalink ----
    if ( copyButton && permalinkField )
    {
        copyButton.disabled = false;
        var copied = function () { say( text( 'copied' ) ); };
        var fallback = function () {
            permalinkField.focus();
            permalinkField.select();
            var ok = false;
            try { ok = document.execCommand( 'copy' ); } catch ( e ) { ok = false; }
            say( ok ? text( 'copied' ) : text( 'copy-failed' ) );
        };
        copyButton.addEventListener( 'click', function () {
            syncUrl();
            if ( navigator.clipboard && navigator.clipboard.writeText )
                navigator.clipboard.writeText( permalinkField.value ).then( copied, fallback );
            else
                fallback();
        } );
    }

    // ---- copy messages: the lines shown, as plain text (page/setup_logs_copy.js) ----
    // Following: every line kept on the page. Paused (or not following): the lines in view, as a selection of the
    // rows on screen would give them.
    var copyMessages = el( 'log-copy-messages' );
    var shownEntries = function () {
        if ( !paused && follow )
            return entries.slice();
        var box = view.getBoundingClientRect(), out = [];
        var rows = list.children;
        for ( var i = 0; i < rows.length; i++ )
        {
            var r = rows[i].getBoundingClientRect();
            var index = parseInt( rows[i].getAttribute( 'data-index' ), 10 );
            if ( r.bottom > box.top && r.top < box.bottom && entries[index] )
                out.push( entries[index] );
        }
        return out;
    };
    var copyText = function ( value, done ) {
        var fallback = function () {
            var area = document.createElement( 'textarea' );
            area.value = value;
            area.setAttribute( 'readonly', 'readonly' );
            area.className = 'a4-sr-only';
            document.body.appendChild( area );
            area.select();
            var ok = false;
            try { ok = document.execCommand( 'copy' ); } catch ( e ) { ok = false; }
            document.body.removeChild( area );
            if ( copyMessages )
                copyMessages.focus();
            done( ok );
        };
        if ( navigator.clipboard && navigator.clipboard.writeText && window.isSecureContext !== false )
            navigator.clipboard.writeText( value ).then( function () { done( true ); }, fallback );
        else
            fallback();
    };
    if ( copyMessages && window.ExpLogCopy )
    {
        copyMessages.disabled = false;
        copyMessages.addEventListener( 'click', function () {
            var shown = shownEntries();
            var value = window.ExpLogCopy.textOf( shown, { wrap: wrap, levelName: levelName, rotated: text( 'rotated' ), cut: text( 'cut' ) } );
            copyText( value, function ( ok ) {
                var message = ok ? ( shown.length === 1 ? text( 'lines-copied-one' ) : text( 'lines-copied' ).replace( '%count', String( shown.length ) ) )
                                 : text( 'lines-copy-failed' );
                // the toast's region announces it; without toasts (the classic design) the page's own polite region
                if ( window.a4Toast && document.getElementById( 'a4-toasts' ) )
                    window.a4Toast( message, ok ? 'ok' : 'bad', copyMessages );
                else
                    say( message );
            } );
        } );
    }

    // ---- start: as the address asks (paused, wrapped, a focused entry) ----
    var asked = state.view || {};
    add( ( source && source.entries ) || [], searching );
    setStatus( searching ? text( 'results' ) : ( liveFile ? text( 'following' ) : text( 'archive' ) ), liveFile ? 'is-running' : '' );
    // wrapping as the server drew it on first paint: the preference admin_logviewer_wrap, on by default
    if ( wrapBox )
    {
        wrap = !!state.wrap;
        wrapBox.checked = wrap;
        view.classList.toggle( 'is-wrapped', wrap );
    }
    var focusAt = -1;
    if ( typeof asked.offset === 'number' && asked.offset >= 0 )
    {
        for ( var k = 0; k < entries.length; k++ )
            if ( entries[k].o === asked.offset )
                focusAt = k;
    }
    if ( focusAt >= 0 )
    {
        follow = false;
        if ( followBox )
            followBox.checked = false;
        active = focusAt;
        render();
        scrollToIndex( focusAt );
    }
    else if ( searching )
    {
        follow = false;
        render();
    }
    else
        toBottom();
    if ( asked.follow === false && liveFile && !searching )
        setPaused( true );
    plan( interval );
    syncLater();
}() );
