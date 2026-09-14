{* The cronjobs console.

   Lives in the admin design rather than admin3 so every administration design
   resolves it - admin3 lists admin2 and admin as its fallbacks, so it is found
   there too - but it is written to admin3's idiom: no nested box-tl/box-tc
   scaffolding, no spacer images, one grid of cards, and a console that fills in
   as the job runs. The styling is scoped to .exp-cronjobs and carries its own
   values so it looks the same under admin, admin2 and admin3. *}

{literal}
<style>
.exp-cronjobs {
    --exp-bg: #ffffff;
    --exp-line: #e2e2e5;
    --exp-muted: #6b6b70;
    --exp-ink: #1f1f23;
    --exp-accent: #ff5500;
    --exp-ok: #2e7d32;
    --exp-warn: #a4741a;
    --exp-bad: #b3261e;
    --exp-radius: 10px;
    color: var(--exp-ink);
    font-size: 0.9rem;
}
.exp-cronjobs * { box-sizing: border-box; }
.exp-cronjobs h1.context-title { margin: 0; }

.exp-head {
    display: flex; flex-wrap: wrap; gap: 1rem;
    align-items: center; justify-content: space-between;
    padding: 0 0 1rem 0;
}
.exp-head p { margin: .25rem 0 0 0; color: var(--exp-muted); max-width: 60ch; }

.exp-status {
    display: flex; flex-wrap: wrap; gap: .5rem 1.5rem; align-items: center;
    border: 1px solid var(--exp-line); border-radius: var(--exp-radius);
    background: #fafafa; padding: .75rem 1rem; margin: 0 0 1rem 0;
}
.exp-pill {
    display: inline-flex; align-items: center; gap: .45rem;
    font-weight: 600; letter-spacing: .01em;
}
.exp-dot { width: .6rem; height: .6rem; border-radius: 50%; background: #bdbdbd; }
.exp-pill.is-running .exp-dot { background: var(--exp-ok); animation: exp-pulse 1.4s ease-in-out infinite; }
@keyframes exp-pulse { 0%,100% { opacity: 1; } 50% { opacity: .3; } }
.exp-meta { color: var(--exp-muted); font-size: .85em; }
.exp-meta code { background: #eee; border-radius: 4px; padding: .05rem .3rem; }

.exp-controls {
    display: flex; flex-wrap: wrap; gap: .75rem; align-items: flex-end;
    margin: 0 0 1.25rem 0;
}
.exp-field { display: flex; flex-direction: column; gap: .3rem; }
.exp-field label { font-size: .8em; color: var(--exp-muted); text-transform: uppercase; letter-spacing: .04em; }
.exp-cronjobs select {
    border: 1px solid var(--exp-line); border-radius: 6px;
    padding: .4rem .5rem; background: #fff; font: inherit; min-width: 14rem;
}

.exp-grid {
    display: grid; gap: 1rem;
    grid-template-columns: repeat( auto-fill, minmax( 19rem, 1fr ) );
    margin: 0 0 1.5rem 0;
}
.exp-card {
    border: 1px solid var(--exp-line); border-radius: var(--exp-radius);
    background: var(--exp-bg); padding: .9rem 1rem 1rem 1rem;
    display: flex; flex-direction: column; gap: .6rem;
}
.exp-card.is-forbidden { background: #fbfbfb; border-style: dashed; }
.exp-card.is-current { border-color: var(--exp-accent); box-shadow: 0 0 0 2px rgba(255,85,0,.12); }
.exp-card-head { display: flex; align-items: baseline; justify-content: space-between; gap: .5rem; }
.exp-card-head h3 { margin: 0; font-size: 1rem; }
.exp-card-head .exp-count { color: var(--exp-muted); font-size: .8em; white-space: nowrap; }
.exp-scripts { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: .2rem; }
.exp-scripts li { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: .8em; color: #35353a; }
.exp-scripts li.is-missing { color: var(--exp-bad); text-decoration: line-through; }
.exp-scripts li.is-missing::after { content: ' not found'; text-decoration: none; font-style: italic; }
.exp-card-foot { margin-top: auto; display: flex; align-items: center; gap: .6rem; }
.exp-note { color: var(--exp-muted); font-size: .8em; }

.exp-btn {
    font: inherit; font-weight: 600; cursor: pointer;
    border: 1px solid var(--exp-line); border-radius: 6px;
    background: #f4f4f5; color: var(--exp-ink); padding: .4rem .9rem;
}
.exp-btn:hover:not([disabled]) { border-color: #9a9aa0; background: #ececee; }
.exp-btn-primary { background: var(--exp-accent); border-color: var(--exp-accent); color: #fff; }
.exp-btn-primary:hover:not([disabled]) { background: #e64d00; border-color: #e64d00; }
.exp-btn[disabled] { opacity: .45; cursor: not-allowed; }

.exp-console {
    background: #16161a; color: #d8d8d8; border: 1px solid #35353a;
    border-radius: var(--exp-radius); padding: .8rem 1rem; margin: 0;
    height: 24em; overflow: auto; white-space: pre-wrap; word-break: break-word;
    font: 12px/1.55 ui-monospace, SFMono-Regular, Menlo, monospace;
}
.exp-console-head {
    display: flex; align-items: center; justify-content: space-between;
    gap: 1rem; margin: 0 0 .5rem 0;
}
.exp-console-head h2 { margin: 0; font-size: 1rem; }
.exp-feedback { border-radius: var(--exp-radius); padding: .7rem 1rem; margin: 0 0 1rem 0; border: 1px solid; }
.exp-feedback.is-ok { border-color: #bfe3c1; background: #f1f9f1; color: var(--exp-ok); }
.exp-feedback.is-bad { border-color: #f0c4c0; background: #fdf3f2; color: var(--exp-bad); }

@media ( max-width: 40rem ) {
    .exp-head, .exp-controls { flex-direction: column; align-items: stretch; }
    .exp-grid { grid-template-columns: 1fr; }
}
</style>
{/literal}

<div class="context-block exp-cronjobs">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Cronjobs'|i18n( 'design/admin/setup/cronjobs' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="exp-head">
    <div>
        <p>{'Runs a cronjob part now, without waiting for the scheduler. The job is started as a separate process and keeps running after this page is closed, so nothing is lost if the browser goes away.'|i18n( 'design/admin/setup/cronjobs' )}</p>
    </div>
</div>

{foreach $cronjob_feedback as $cronjob_message}
<div class="exp-feedback {if $cronjob_message.ok}is-ok{else}is-bad{/if}">{$cronjob_message.message|wash}</div>
{/foreach}

{* What an action says when the console runs it in place. Empty and hidden
   until there is something to put in it. *}
<div class="exp-feedback" id="cronjob-feedback" style="display:none;"></div>

{if $cronjob_php_binary|eq('')}
<div class="exp-feedback is-bad">
    {'No php command line binary could be found, so nothing can be launched from here. Set cronjob.ini [AdminSettings] PhpCliPath to its full path.'|i18n( 'design/admin/setup/cronjobs' )}
</div>
{/if}

{* Everything that changes when a job starts or ends is addressable, so the
   console can bring the page up to date without fetching it again. *}
<div class="exp-status" id="cronjob-status">
    <span class="exp-pill{if $cronjob_status.running} is-running{/if}" id="cronjob-pill">
        <span class="exp-dot"></span>
        <span class="exp-pill-text">
        {if $cronjob_status.running}
            {'Running'|i18n( 'design/admin/setup/cronjobs' )}: {$cronjob_status.part|wash}
        {else}
            {'Idle'|i18n( 'design/admin/setup/cronjobs' )}
        {/if}
        </span>
    </span>
    {if $cronjob_status.running}
    <span class="exp-meta exp-running-meta">{'Site'|i18n( 'design/admin/setup/cronjobs' )}: {$cronjob_status.siteaccess|wash}</span>
    <span class="exp-meta exp-running-meta">{'Process'|i18n( 'design/admin/setup/cronjobs' )}: {$cronjob_status.pid}</span>
    <span class="exp-meta exp-running-meta" id="cronjob-elapsed">{'Elapsed'|i18n( 'design/admin/setup/cronjobs' )}: {$cronjob_status.elapsed}s</span>
    {/if}
    <span class="exp-meta">{'Log'|i18n( 'design/admin/setup/cronjobs' )}: <code>{$cronjob_log_file|wash}</code></span>
    {if $cronjob_php_binary|ne('')}
    <span class="exp-meta">php: <code>{$cronjob_php_binary|wash}</code></span>
    {/if}
</div>

<form method="post" action={'setup/cronjobs'|ezurl}>

<div class="exp-controls">
    <div class="exp-field">
        <label for="cronjob-siteaccess">{'Run for site'|i18n( 'design/admin/setup/cronjobs' )}</label>
        {* A cronjob acts on content, and which content depends on the siteaccess
           it runs under, so this is asked rather than assumed. *}
        <select id="cronjob-siteaccess" name="CronjobSiteAccess">
        {foreach $cronjob_siteaccess_list as $cronjob_siteaccess}
            <option value="{$cronjob_siteaccess|wash}"{if eq( $cronjob_siteaccess, $cronjob_default_siteaccess )} selected="selected"{/if}>{$cronjob_siteaccess|wash}</option>
        {/foreach}
        </select>
    </div>
    <button type="submit" class="exp-btn" id="cronjob-stop" name="StopCronjobButton" value="1"{if $cronjob_status.running|not} disabled="disabled"{/if}>{'Stop running job'|i18n( 'design/admin/setup/cronjobs' )}</button>
    <button type="submit" class="exp-btn" id="cronjob-clear" name="ClearCronjobLogButton" value="1">{'Clear logs'|i18n( 'design/admin/setup/cronjobs' )}</button>
</div>

<div class="exp-grid">
{foreach $cronjob_parts as $cronjob_part}
    {* Blocked for good - forbidden, scripts missing, no php - is not the same
       as blocked because something else is running, which stops being true the
       moment the console sees the job finish. The card records which it is so
       the console knows what it may re-enable. *}
    {def $cronjob_blocked = or( $cronjob_part.forbidden,
                                $cronjob_part.missing|ge( $cronjob_part.scripts|count ),
                                $cronjob_php_binary|eq('') )
         $cronjob_runnable = and( $cronjob_blocked|not, $cronjob_status.running|not )}
    <div class="exp-card{if $cronjob_part.forbidden} is-forbidden{/if}{if and( $cronjob_status.running, eq( $cronjob_status.part, $cronjob_part.name ) )} is-current{/if}">
        <div class="exp-card-head">
            <h3>{$cronjob_part.label|wash}</h3>
            <span class="exp-count">{$cronjob_part.scripts|count} {'scripts'|i18n( 'design/admin/setup/cronjobs' )}</span>
        </div>
        <ul class="exp-scripts">
        {foreach $cronjob_part.scripts as $cronjob_script}
            <li{if $cronjob_script.path|eq(false())} class="is-missing"{/if}>{$cronjob_script.name|wash}</li>
        {/foreach}
        </ul>
        <div class="exp-card-foot">
            {* The button carries the part name as its own value, so one form
               serves every card and no javascript is needed to launch one. *}
            <button type="submit" class="exp-btn exp-btn-primary exp-run" name="LaunchCronjobButton"
                    data-blocked="{if $cronjob_blocked}1{else}0{/if}"
                    value="{$cronjob_part.name|wash}"{if $cronjob_runnable|not} disabled="disabled"{/if}>{'Run now'|i18n( 'design/admin/setup/cronjobs' )}</button>
            {if $cronjob_part.forbidden}
                <span class="exp-note">{'Blocked by cronjob.ini ForbiddenParts.'|i18n( 'design/admin/setup/cronjobs' )}</span>
            {elseif $cronjob_part.missing|gt(0)}
                <span class="exp-note">{$cronjob_part.missing} {'of its scripts are missing.'|i18n( 'design/admin/setup/cronjobs' )}</span>
            {elseif $cronjob_status.running}
                <span class="exp-note exp-note-busy">{'Another job is running.'|i18n( 'design/admin/setup/cronjobs' )}</span>
            {/if}
        </div>
    </div>
    {undef $cronjob_runnable $cronjob_blocked}
{/foreach}
</div>

</form>

<div class="exp-console-head">
    <h2>{'Output'|i18n( 'design/admin/setup/cronjobs' )}</h2>
    <span class="exp-meta" id="cronjob-stream-status"></span>
</div>
<pre class="exp-console" id="cronjob-console">{$cronjob_log|wash}{if $cronjob_errors|ne('')}
{$cronjob_errors|wash}{/if}</pre>

{* Template values are read before the literal block, because everything inside
   one is passed through untouched - which is the point, since javascript is
   full of braces the template engine would otherwise try to parse. *}
<script type="text/javascript">
var expCronjobActionUrl = {'setup/cronjobs'|ezurl()};
var expCronjobStreamUrl = {$cronjob_stream_url|ezurl()};
var expCronjobRunning   = {if $cronjob_status.running}true{else}false{/if};
var expCronjobOffset    = {$cronjob_log_offset};
{literal}
(function () {
    var consoleEl = document.getElementById( 'cronjob-console' );
    var statusEl  = document.getElementById( 'cronjob-stream-status' );
    var stopBtn   = document.getElementById( 'cronjob-stop' );
    var clearBtn  = document.getElementById( 'cronjob-clear' );
    var saEl      = document.getElementById( 'cronjob-siteaccess' );
    var feedEl    = document.getElementById( 'cronjob-feedback' );
    if ( !consoleEl || typeof window.EventSource === 'undefined' )
        return;   // The form posts on its own; nothing below is needed.

    var colours = {
        phase: '#7fd1ff', ok: '#d8d8d8', warn: '#e8c765',
        error: '#ff8a80', info: '#9a9a9a', done: '#7fd1ff'
    };
    var source = null;
    var elapsedTimer = null;
    var offset = expCronjobOffset;

    function each( selector, fn ) {
        var nodes = document.querySelectorAll( selector ), i;
        for ( i = 0; i < nodes.length; i++ ) fn( nodes[i] );
    }

    function text( el, value ) {
        el.innerHTML = '';
        el.appendChild( document.createTextNode( value ) );
    }

    function write( type, line ) {
        var atBottom = consoleEl.scrollTop + consoleEl.clientHeight >= consoleEl.scrollHeight - 8;
        var el = document.createElement( 'div' );
        el.style.color = colours[type] || '#d8d8d8';
        if ( type === 'phase' || type === 'done' ) el.style.fontWeight = 'bold';
        el.appendChild( document.createTextNode( line ) );
        consoleEl.appendChild( el );
        if ( atBottom ) consoleEl.scrollTop = consoleEl.scrollHeight;
    }

    function say( value ) { text( statusEl, value ); }

    function feedback( ok, message ) {
        if ( !feedEl ) return;
        feedEl.className = 'exp-feedback ' + ( ok ? 'is-ok' : 'is-bad' );
        feedEl.style.display = 'block';
        text( feedEl, message );
    }

    function pillText( value, running ) {
        var pill = document.getElementById( 'cronjob-pill' );
        if ( !pill ) return;
        pill.className = 'exp-pill' + ( running ? ' is-running' : '' );
        var label = pill.getElementsByClassName( 'exp-pill-text' )[0];
        if ( label ) text( label, value );
    }

    // The page is never fetched again, in either direction. Everything an
    // action or a finished job changes is on this page already, so it is
    // changed here. Reloading was not just unnecessary: a reload repeats the
    // request that produced the page, so the form post that started a job was
    // offered for resending, and confirming launched it again - and again.
    function markIdle() {
        pillText( 'Idle', false );
        each( '.exp-running-meta', function ( el ) { el.parentNode.removeChild( el ); } );
        each( '.exp-note-busy', function ( el ) { el.parentNode.removeChild( el ); } );
        each( '.exp-card.is-current', function ( el ) {
            el.className = el.className.replace( / ?is-current/, '' );
        } );
        if ( stopBtn ) stopBtn.disabled = true;
        // Only the ones that were waiting on this job. A part that is forbidden,
        // missing its scripts, or has no php to run with stays disabled.
        each( '.exp-run', function ( el ) {
            if ( el.getAttribute( 'data-blocked' ) !== '1' ) el.disabled = false;
        } );
        if ( elapsedTimer ) { window.clearInterval( elapsedTimer ); elapsedTimer = null; }
    }

    function markRunning( part, siteaccess, pid ) {
        pillText( 'Running: ' + part, true );

        var strip = document.getElementById( 'cronjob-status' );
        if ( strip ) {
            each( '.exp-running-meta', function ( el ) { el.parentNode.removeChild( el ); } );
            var meta = [ [ 'Site: ' + siteaccess, null ],
                         [ 'Process: ' + pid, null ],
                         [ 'Elapsed: 0s', 'cronjob-elapsed' ] ];
            for ( var i = 0; i < meta.length; i++ ) {
                var span = document.createElement( 'span' );
                span.className = 'exp-meta exp-running-meta';
                if ( meta[i][1] ) span.id = meta[i][1];
                span.appendChild( document.createTextNode( meta[i][0] ) );
                strip.appendChild( span );
            }
        }

        each( '.exp-run', function ( el ) { el.disabled = true; } );
        if ( stopBtn ) stopBtn.disabled = false;

        each( '.exp-card', function ( el ) {
            var button = el.getElementsByClassName( 'exp-run' )[0];
            if ( button && button.value === part && el.className.indexOf( 'is-current' ) === -1 )
                el.className += ' is-current';
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
            text( el, 'Elapsed: ' + ( from + Math.round( ( new Date().getTime() - openedAt ) / 1000 ) ) + 's' );
        }, 1000 );
    }

    function follow() {
        if ( source ) return;
        say( 'following…' );
        source = new EventSource( expCronjobStreamUrl + '?Offset=' + offset );

        source.onmessage = function ( event ) {
            var payload;
            try { payload = JSON.parse( event.data ); }
            catch ( e ) { return; }
            if ( typeof payload.offset === 'number' ) offset = payload.offset;
            write( payload.type, payload.message );
            if ( payload.type === 'done' ) { close( 'finished' ); markIdle(); }
        };

        source.addEventListener( 'end', function () { close( 'finished' ); markIdle(); } );

        source.onerror = function () {
            // EventSource reconnects by itself, which would start the tail over
            // from the offset this page was rendered with and reprint it all.
            close( 'stream closed' );
        };
    }

    function close( message ) {
        if ( source ) { source.close(); source = null; }
        say( message );
    }

    // An action asks for its answer rather than a new page. The same view does
    // the same work either way; only the reply differs.
    function act( fields, onDone ) {
        var body = 'Ajax=1', key;
        for ( key in fields )
            body += '&' + encodeURIComponent( key ) + '=' + encodeURIComponent( fields[key] );

        var request = new XMLHttpRequest();
        request.open( 'POST', expCronjobActionUrl, true );
        request.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
        request.onreadystatechange = function () {
            if ( request.readyState !== 4 ) return;
            var answer;
            try { answer = JSON.parse( request.responseText ); }
            catch ( e ) { answer = { ok: false, message: 'The server did not answer as expected.' }; }
            feedback( answer.ok, answer.message );
            onDone( answer );
        };
        request.send( body );
    }

    each( '.exp-run', function ( button ) {
        button.onclick = function ( event ) {
            if ( event && event.preventDefault ) event.preventDefault();
            var part = button.value;
            consoleEl.scrollTop = consoleEl.scrollHeight;
            act( { LaunchCronjobButton: part,
                   CronjobSiteAccess: saEl ? saEl.value : '' }, function ( answer ) {
                if ( !answer.ok ) return;
                if ( typeof answer.offset === 'number' ) offset = answer.offset;
                markRunning( answer.part || part, answer.siteaccess, answer.pid );
            } );
            return false;
        };
    } );

    if ( stopBtn ) {
        stopBtn.onclick = function ( event ) {
            if ( event && event.preventDefault ) event.preventDefault();
            act( { StopCronjobButton: '1' }, function () {} );
            return false;   // The stream sees the job end and settles the rest.
        };
    }

    if ( clearBtn ) {
        clearBtn.onclick = function ( event ) {
            if ( event && event.preventDefault ) event.preventDefault();
            act( { ClearCronjobLogButton: '1' }, function () {
                consoleEl.innerHTML = '';
                offset = 0;
            } );
            return false;
        };
    }

    consoleEl.scrollTop = consoleEl.scrollHeight;

    // Only follow when something is actually running. With nothing running the
    // page already shows what the log holds, and opening a stream would hold a
    // php worker for no reason.
    if ( expCronjobRunning ) {
        var el = document.getElementById( 'cronjob-elapsed' );
        var from = 0;
        if ( el ) {
            from = parseInt( ( el.textContent || el.innerText ).replace( /[^0-9]/g, '' ), 10 );
            if ( isNaN( from ) ) from = 0;
        }
        startElapsed( from );
        follow();
    }
})();
{/literal}
</script>

</div></div></div>
</div>
