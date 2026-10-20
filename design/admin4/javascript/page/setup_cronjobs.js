/*
 * admin4 setup/cronjobs: the filters, the copy buttons and the site in the commands, the console that follows a run
 * (an EventSource on the stream), and the run, stop and clear buttons posting in the background. The values from the
 * template are data-* attributes of #cronjob-script. A file of its own, so the page runs under a Content Security
 * Policy without 'unsafe-inline' (doc/eng-us/features/6.0/content-security-policy.md).
 */
var expCronjobScript     = document.getElementById( 'cronjob-script' );
var expCronjobActionUrl  = expCronjobScript.getAttribute( 'data-action-url' );
var expCronjobTokenField = expCronjobScript.getAttribute( 'data-token-field' );
var expCronjobToken      = expCronjobScript.getAttribute( 'data-token' );
var expCronjobStreamUrl  = expCronjobScript.getAttribute( 'data-stream-url' );
var expCronjobRunning    = expCronjobScript.getAttribute( 'data-running' ) === '1';
var expCronjobOffset     = parseInt( expCronjobScript.getAttribute( 'data-offset' ), 10 ) || 0;
// What the console writes itself; %name is replaced in the script.
var expCronjobText = {
    idle: expCronjobScript.getAttribute( 'data-text-idle' ),
    running: expCronjobScript.getAttribute( 'data-text-running' ),
    site: expCronjobScript.getAttribute( 'data-text-site' ),
    process: expCronjobScript.getAttribute( 'data-text-process' ),
    elapsed: expCronjobScript.getAttribute( 'data-text-elapsed' ),
    following: expCronjobScript.getAttribute( 'data-text-following' ),
    finished: expCronjobScript.getAttribute( 'data-text-finished' ),
    streamClosed: expCronjobScript.getAttribute( 'data-text-stream-closed' ),
    noConnection: expCronjobScript.getAttribute( 'data-text-no-connection' ),
    answered: expCronjobScript.getAttribute( 'data-text-answered' ),
    answeredNothing: expCronjobScript.getAttribute( 'data-text-answered-nothing' ),
    failed: expCronjobScript.getAttribute( 'data-text-failed' ),
    timeout: expCronjobScript.getAttribute( 'data-text-timeout' ),
    notSent: expCronjobScript.getAttribute( 'data-text-not-sent' ),
    next: expCronjobScript.getAttribute( 'data-text-next' ),
    runsEvery: expCronjobScript.getAttribute( 'data-text-runs-every' ),
    runsOne: expCronjobScript.getAttribute( 'data-text-runs-one' ),
    nothingToRun: expCronjobScript.getAttribute( 'data-text-nothing-to-run' ),
    runningList: expCronjobScript.getAttribute( 'data-text-running-list' ),
    shown: expCronjobScript.getAttribute( 'data-text-shown' ),
    allShown: expCronjobScript.getAttribute( 'data-text-all-shown' ),
    copy: expCronjobScript.getAttribute( 'data-text-copy' ),
    copied: expCronjobScript.getAttribute( 'data-text-copied' ),
    copiedLong: expCronjobScript.getAttribute( 'data-text-copied-long' ),
    copyFailed: expCronjobScript.getAttribute( 'data-text-copy-failed' )
};
(function () {
    var consoleEl = document.getElementById( 'cronjob-console' );
    var outputEl  = document.getElementById( 'cronjob-output' );
    var statusEl  = document.getElementById( 'cronjob-stream-status' );
    var stopBtn   = document.getElementById( 'cronjob-stop' );
    var clearBtn  = document.getElementById( 'cronjob-clear' );
    var saEl      = document.getElementById( 'cronjob-siteaccess' );
    var feedEl    = document.getElementById( 'cronjob-feedback' );
    var runSelectedEl = document.getElementById( 'cronjob-run-selected' );

    function each( selector, fn ) {
        var nodes = document.querySelectorAll( selector ), i;
        for ( i = 0; i < nodes.length; i++ ) fn( nodes[i], i );
    }

    // A translated console string with its %name placeholders filled in.
    function tr( name, values ) {
        var s = expCronjobText[name], key;
        for ( key in values || {} ) s = s.split( '%' + key ).join( values[key] );
        return s;
    }

    function text( el, value ) { if ( el ) el.textContent = value; }

    // ---- What works with javascript alone: the filters, the copy buttons, the site in the commands ----------

    each( '.a4-setup-cron .a4-setup-js-only', function ( el ) { el.hidden = false; } );

    var searchEl = document.getElementById( 'cronjob-search' );
    var filterEl = document.getElementById( 'cronjob-part-filter' );
    var runHintEl = document.getElementById( 'cronjob-run-hint' );
    var countEl = document.getElementById( 'cronjob-filter-count' );
    var noMatchEl = document.getElementById( 'cronjob-no-match' );

    function chosenState() {
        var picked = document.querySelector( 'input[name="CronjobFilterState"]:checked' );
        return picked ? picked.value : '';
    }

    // The search, the state and the part together decide which cards are shown.
    function applyFilter() {
        var words = searchEl ? searchEl.value.toLowerCase().split( /\s+/ ).filter( Boolean ) : [];
        var state = chosenState();
        var part = filterEl ? filterEl.value : '';
        var shown = 0, total = 0;
        each( '#cronjob-list > .a4-card, #cronjob-table .a4-setup-part-row', function ( card ) {
            total++;
            var haystack = card.getAttribute( 'data-search' ) || '';
            var ok = ( part === '' || card.getAttribute( 'data-part' ) === part );
            if ( ok && state === 'attention' ) ok = card.getAttribute( 'data-attention' ) === '1';
            else if ( ok && state !== '' ) ok = card.getAttribute( 'data-state' ) === state;
            for ( var i = 0; ok && i < words.length; i++ ) ok = haystack.indexOf( words[i] ) !== -1;
            card.hidden = !ok;
            if ( ok ) shown++;
            // The scripts row of a compact row goes with it.
            var detail = card.nextElementSibling;
            if ( detail && detail.classList.contains( 'a4-setup-compact-detail' ) ) {
                var folded = card.querySelector( '.a4-setup-compact-scripts' );
                detail.hidden = !ok || !( folded && folded.open );
            }
            // A search may match a script rather than the part, so the scripts of a match are opened while it
            // lasts; the ones opened that way close again when the search is cleared.
            var scripts = card.querySelector( '.a4-setup-scripts, .a4-setup-compact-scripts' );
            if ( scripts ) {
                if ( ok && words.length ) {
                    if ( !scripts.open ) { scripts.open = true; scripts.setAttribute( 'data-auto-open', '1' ); }
                } else if ( scripts.getAttribute( 'data-auto-open' ) ) {
                    scripts.open = false;
                    scripts.removeAttribute( 'data-auto-open' );
                }
            }
        } );
        if ( noMatchEl ) noMatchEl.hidden = !( total > 0 && shown === 0 );
        if ( countEl ) text( countEl, shown === total ? tr( 'allShown', { count: total } ) : tr( 'shown', { shown: shown, count: total } ) );
        if ( runHintEl && filterEl )
            text( runHintEl, part === '' ? tr( 'runsEvery' ) : tr( 'runsOne', { part: filterEl.options[filterEl.selectedIndex].text } ) );
    }
    if ( searchEl ) searchEl.addEventListener( 'input', applyFilter );
    if ( filterEl ) filterEl.addEventListener( 'change', applyFilter );
    each( 'input[name="CronjobFilterState"]', function ( radio ) { radio.addEventListener( 'change', applyFilter ); } );
    applyFilter();

    // The commands name the site chosen above.
    function siteaccess() { return saEl ? saEl.value : ''; }
    if ( saEl ) saEl.addEventListener( 'change', function () {
        each( '.a4-setup-cmd-sa', function ( el ) { text( el, siteaccess() ); } );
    } );

    var copyStatusEl = document.getElementById( 'cronjob-copy-status' );
    function copyText( value, button ) {
        function done( ok ) {
            text( copyStatusEl, ok ? tr( 'copiedLong' ) : tr( 'copyFailed' ) );
            if ( !ok ) return;
            var label = button.textContent;
            text( button, tr( 'copied' ) );
            window.setTimeout( function () { text( button, label ); }, 1600 );
        }
        if ( navigator.clipboard && window.isSecureContext ) {
            navigator.clipboard.writeText( value ).then( function () { done( true ); }, function () { done( fallback() ); } );
            return;
        }
        done( fallback() );
        function fallback() {
            var area = document.createElement( 'textarea' );
            area.value = value; area.setAttribute( 'readonly', '' );
            area.style.position = 'fixed'; area.style.opacity = '0';
            document.body.appendChild( area ); area.select();
            var ok = false;
            try { ok = document.execCommand( 'copy' ); } catch ( e ) {}
            document.body.removeChild( area );
            button.focus();
            return ok;
        }
    }
    each( '.a4-setup-copy', function ( button ) {
        button.addEventListener( 'click', function () {
            var from = button.getAttribute( 'data-copy-from' );
            var value = from ? ( document.getElementById( from ) || {} ).textContent || ''
                             : ( button.getAttribute( 'data-cmd-head' ) || '' ) + siteaccess() + ( button.getAttribute( 'data-cmd-tail' ) || '' );
            copyText( value, button );
        } );
    } );

    // ---- Running in place and following the output; without EventSource the form posts on its own ------------

    if ( !consoleEl || typeof window.EventSource === 'undefined' ) {
        if ( runSelectedEl ) { runSelectedEl.hidden = true; if ( runHintEl ) runHintEl.hidden = true; }
        return;
    }

    var source = null;
    var elapsedTimer = null;
    var offset = expCronjobOffset;

    function write( type, line ) {
        var atBottom = consoleEl.scrollTop + consoleEl.clientHeight >= consoleEl.scrollHeight - 8;
        var el = document.createElement( 'div' );
        // each kind of line has its colour in modules/setup.css; an ordinary line of output is plain text
        el.className = 'a4-setup-console-' + ( /^(phase|warn|error|info|done)$/.test( type ) ? type : 'text' );
        el.appendChild( document.createTextNode( line ) );
        consoleEl.appendChild( el );
        if ( atBottom ) consoleEl.scrollTop = consoleEl.scrollHeight;
    }

    function say( value ) { text( statusEl, value ); }

    function feedback( ok, message ) {
        if ( !feedEl ) return;
        feedEl.className = 'a4-notice ' + ( ok ? 'is-ok' : 'is-bad' );
        feedEl.setAttribute( 'role', ok ? 'status' : 'alert' );
        feedEl.hidden = false;
        text( feedEl, message );
    }

    function pillText( value, running ) {
        var pill = document.getElementById( 'cronjob-pill' );
        if ( !pill ) return;
        pill.className = 'a4-status' + ( running ? ' is-running' : '' );
        text( pill.querySelector( '.a4-setup-pill-text' ), value );
    }

    // The page is never fetched again, in either direction. Everything an action or a finished job changes is on
    // this page already, so it is changed here. A reload repeats the request that produced the page, so the form
    // post that started a job was offered for resending, and confirming launched it again - and again.
    function markIdle() {
        pillText( tr( 'idle' ), false );
        each( '.a4-setup-running-meta', function ( el ) { el.parentNode.removeChild( el ); } );
        each( '.a4-setup-part-row.is-current', function ( el ) { el.classList.remove( 'is-current' ); } );
        if ( stopBtn ) stopBtn.disabled = true;
        if ( runSelectedEl && runSelectedEl.getAttribute( 'data-blocked' ) !== '1' ) runSelectedEl.disabled = false;
        // Only the ones that were waiting on this job. A part that is forbidden, missing its scripts, or has no php
        // to run with stays disabled.
        each( '.a4-setup-run', function ( el ) {
            if ( el.getAttribute( 'data-blocked' ) !== '1' ) el.disabled = false;
        } );
        if ( elapsedTimer ) { window.clearInterval( elapsedTimer ); elapsedTimer = null; }
    }

    function markRunning( part, siteaccessName, pid ) {
        pillText( tr( 'running', { part: part } ), true );

        var strip = document.getElementById( 'cronjob-status' );
        if ( strip ) {
            each( '.a4-setup-running-meta', function ( el ) { el.parentNode.removeChild( el ); } );
            var meta = [ [ tr( 'site', { siteaccess: siteaccessName } ), null ],
                         [ tr( 'process', { pid: pid } ), null ],
                         [ tr( 'elapsed', { elapsed: '0s' } ), 'cronjob-elapsed' ] ];
            for ( var i = 0; i < meta.length; i++ ) {
                var span = document.createElement( 'span' );
                span.className = 'a4-meta a4-setup-running-meta';
                if ( meta[i][1] ) span.id = meta[i][1];
                span.appendChild( document.createTextNode( meta[i][0] ) );
                strip.appendChild( span );
            }
        }

        each( '.a4-setup-run', function ( el ) { el.disabled = true; } );
        if ( runSelectedEl ) runSelectedEl.disabled = true;
        if ( stopBtn ) stopBtn.disabled = false;
        each( '.a4-setup-part-row', function ( el ) {
            if ( el.getAttribute( 'data-part' ) === part ) el.classList.add( 'is-current' );
        } );

        startElapsed( 0 );
        follow();
    }

    function startElapsed( from ) {
        if ( elapsedTimer ) window.clearInterval( elapsedTimer );
        var el = document.getElementById( 'cronjob-elapsed' );
        if ( !el ) return;
        var openedAt = new Date().getTime();
        elapsedTimer = window.setInterval( function () {
            text( el, tr( 'elapsed', { elapsed: ( from + Math.round( ( new Date().getTime() - openedAt ) / 1000 ) ) + 's' } ) );
        }, 1000 );
    }

    function follow() {
        if ( source ) return;
        say( tr( 'following' ) );
        source = new EventSource( expCronjobStreamUrl + '?Offset=' + offset );

        source.onmessage = function ( event ) {
            var payload;
            try { payload = JSON.parse( event.data ); }
            catch ( e ) { return; }
            if ( typeof payload.offset === 'number' ) offset = payload.offset;
            write( payload.type, payload.message );
            if ( payload.type === 'done' ) { close( tr( 'finished' ) ); afterFinished(); }
        };

        source.addEventListener( 'end', function () { close( tr( 'finished' ) ); afterFinished(); } );

        // EventSource reconnects by itself, which would start the tail over from the offset this page was rendered
        // with and reprint it all.
        source.onerror = function () { close( tr( 'streamClosed' ) ); };
    }

    function close( message ) {
        if ( source ) { source.close(); source = null; }
        say( message );
    }

    // Reads an answer out of whatever came back. Anything appended to the json - a debug block, a warning - would
    // otherwise make the reply unparseable, so the first balanced object in the text is taken if parsing the lot
    // fails.
    function readAnswer( body ) {
        if ( !body ) return null;
        try { return JSON.parse( body ); } catch ( e ) {}
        var start = body.indexOf( '{' );
        while ( start !== -1 ) {
            var depth = 0, i;
            for ( i = start; i < body.length; i++ ) {
                if ( body.charAt( i ) === '{' ) depth++;
                else if ( body.charAt( i ) === '}' ) {
                    depth--;
                    if ( depth === 0 ) {
                        try { return JSON.parse( body.substring( start, i + 1 ) ); } catch ( e ) {}
                        break;
                    }
                }
            }
            start = body.indexOf( '{', start + 1 );
        }
        return null;
    }

    // When there is no json at all the page that came back is shown, trimmed of markup: it usually says exactly
    // what went wrong.
    function describeFailure( request ) {
        if ( request.status === 0 ) return tr( 'noConnection' );
        var body = ( request.responseText || '' )
                       .replace( /<script[\s\S]*?<\/script>/gi, ' ' )
                       .replace( /<(?:style)[\s\S]*?<\/style>/gi, ' ' )
                       .replace( /<[^>]*>/g, ' ' )
                       .replace( /\s+/g, ' ' ).trim();
        if ( body.length > 240 ) body = body.substring( 0, 240 ) + '…';
        return body ? tr( 'answered', { status: request.status, text: body } )
                    : tr( 'answeredNothing', { status: request.status } );
    }

    // An action asks for its answer rather than a new page. The same view does the same work either way.
    function act( fields, onDone ) {
        var body = 'Ajax=1', key;
        for ( key in fields )
            body += '&' + encodeURIComponent( key ) + '=' + encodeURIComponent( fields[key] );

        // ezformtoken refuses a post from a logged in user that does not carry its token, so it is sent both
        // ways it accepts: in the field it looks for, and in the header it offers for requests that are not forms.
        if ( expCronjobToken && expCronjobTokenField )
            body += '&' + encodeURIComponent( expCronjobTokenField ) + '=' + encodeURIComponent( expCronjobToken );

        var request = new XMLHttpRequest();
        request.open( 'POST', expCronjobActionUrl, true );
        request.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
        request.setRequestHeader( 'X-Requested-With', 'XMLHttpRequest' );
        if ( expCronjobToken ) request.setRequestHeader( 'X-CSRF-Token', expCronjobToken );

        var finished = false;
        function settle( answer ) {
            if ( finished ) return;
            finished = true;
            feedback( answer.ok, answer.message );
            try { onDone( answer ); } catch ( e ) {}
            // Whatever happened, the controls must not be left disabled with nothing running.
            if ( !answer.ok && !answer.running ) markIdle();
        }

        request.onreadystatechange = function () {
            if ( request.readyState !== 4 ) return;
            var answer = readAnswer( request.responseText );
            if ( answer && typeof answer.ok !== 'undefined' ) { settle( answer ); return; }
            settle( { ok: false, running: false, message: describeFailure( request ) } );
        };
        request.onerror = function () { settle( { ok: false, running: false, message: tr( 'failed' ) } ); };
        request.ontimeout = function () { settle( { ok: false, running: false, message: tr( 'timeout' ) } ); };
        request.timeout = 120000;

        try { request.send( body ); }
        catch ( e ) { settle( { ok: false, running: false, message: tr( 'notSent', { error: e.message } ) } ); }
    }

    // With no token there is nothing to prove the request with, and posting in the background would only be
    // refused. The buttons are then left to submit the form, which carries a token of its own.
    var canPostInBackground = !!expCronjobToken;
    if ( !canPostInBackground && runSelectedEl ) { runSelectedEl.hidden = true; if ( runHintEl ) runHintEl.hidden = true; }

    // What is still to be run, when a whole list of parts was asked for.
    var queue = [];

    function showOutput() {
        if ( !outputEl ) return;
        outputEl.hidden = false;
        outputEl.open = true;
    }

    // Starts one thing: a part on its own, or one script of a part.
    function launch( fields, part ) {
        showOutput();
        consoleEl.scrollTop = consoleEl.scrollHeight;
        if ( saEl ) fields.CronjobSiteAccess = saEl.value;
        act( fields, function ( answer ) {
            if ( !answer.ok ) { queue = []; return; }
            if ( typeof answer.offset === 'number' ) offset = answer.offset;
            markRunning( answer.part || part, answer.siteaccess, answer.pid );
        } );
    }

    // Called when a job ends. If a list was asked for, the next one starts.
    function afterFinished() {
        markIdle();
        if ( !queue.length ) return;
        var next = queue.shift();
        write( 'phase', tr( 'next', { part: next } ) );
        window.setTimeout( function () { launch( { LaunchCronjobButton: next }, next ); }, 400 );
    }

    each( '.a4-setup-run', function ( button ) {
        button.addEventListener( 'click', function ( event ) {
            if ( !canPostInBackground ) return;
            event.preventDefault();
            queue = [];
            var value = button.value, fields = {}, part = value;
            if ( button.name === 'LaunchCronjobScriptButton' ) {
                fields.LaunchCronjobScriptButton = value;
                part = value.split( '|' )[0];
            } else {
                fields.LaunchCronjobButton = value;
            }
            launch( fields, part );
        } );
    } );

    if ( runSelectedEl ) {
        runSelectedEl.addEventListener( 'click', function ( event ) {
            event.preventDefault();
            if ( !canPostInBackground ) return;
            var wanted = filterEl ? filterEl.value : '';
            queue = [];
            if ( wanted !== '' ) { launch( { LaunchCronjobButton: wanted }, wanted ); return; }

            // Every part it is allowed to run - on every page, not only this one - one after another, so only one
            // cronjob is ever going at a time.
            each( '#cronjob-part-filter option', function ( option ) {
                if ( option.value !== '' && option.getAttribute( 'data-blocked' ) !== '1' ) queue.push( option.value );
            } );
            if ( !queue.length ) { feedback( false, tr( 'nothingToRun' ) ); return; }
            showOutput();
            write( 'phase', tr( 'runningList', { count: queue.length } ) );
            var first = queue.shift();
            launch( { LaunchCronjobButton: first }, first );
        } );
    }

    if ( stopBtn ) {
        stopBtn.addEventListener( 'click', function ( event ) {
            if ( !canPostInBackground ) return;
            event.preventDefault();
            queue = [];
            act( { StopCronjobButton: '1' }, function () {} );   // The stream sees the job end and settles the rest.
        } );
    }

    if ( clearBtn ) {
        clearBtn.addEventListener( 'click', function ( event ) {
            if ( !canPostInBackground ) return;
            event.preventDefault();
            act( { ClearCronjobLogButton: '1' }, function () { consoleEl.textContent = ''; offset = 0; } );
        } );
    }

    consoleEl.scrollTop = consoleEl.scrollHeight;
    // The compact view opens a part's scripts in the row below it, across the table, so several can be open.
    var compactTable = document.getElementById( 'cronjob-table' );
    if ( compactTable ) {
        compactTable.classList.add( 'is-scripted' );
        each( '#cronjob-table .a4-setup-compact-scripts', function ( folded ) {
            folded.addEventListener( 'toggle', function () {
                var row = folded.closest( 'tr' );
                var detail = row ? row.nextElementSibling : null;
                if ( detail && detail.classList.contains( 'a4-setup-compact-detail' ) ) detail.hidden = !folded.open || row.hidden;
            } );
        } );
    }

    if ( outputEl ) outputEl.addEventListener( 'toggle', function () {
        if ( outputEl.open ) consoleEl.scrollTop = consoleEl.scrollHeight;
    } );

    // Only follow when something is actually running. With nothing running the page already shows what the log
    // holds, and opening a stream would hold a php worker for no reason.
    if ( expCronjobRunning ) {
        showOutput();
        var el = document.getElementById( 'cronjob-elapsed' );
        var from = 0;
        if ( el ) {
            from = parseInt( ( el.textContent || '' ).replace( /[^0-9]/g, '' ), 10 );
            if ( isNaN( from ) ) from = 0;
        }
        startElapsed( from );
        follow();
    }
})();
