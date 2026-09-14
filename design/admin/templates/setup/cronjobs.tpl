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

{if $cronjob_php_binary|eq('')}
<div class="exp-feedback is-bad">
    {'No php command line binary could be found, so nothing can be launched from here. Set cronjob.ini [AdminSettings] PhpCliPath to its full path.'|i18n( 'design/admin/setup/cronjobs' )}
</div>
{/if}

<div class="exp-status">
    <span class="exp-pill{if $cronjob_status.running} is-running{/if}">
        <span class="exp-dot"></span>
        {if $cronjob_status.running}
            {'Running'|i18n( 'design/admin/setup/cronjobs' )}: {$cronjob_status.part|wash}
        {else}
            {'Idle'|i18n( 'design/admin/setup/cronjobs' )}
        {/if}
    </span>
    {if $cronjob_status.running}
    <span class="exp-meta">{'Site'|i18n( 'design/admin/setup/cronjobs' )}: {$cronjob_status.siteaccess|wash}</span>
    <span class="exp-meta">{'Process'|i18n( 'design/admin/setup/cronjobs' )}: {$cronjob_status.pid}</span>
    <span class="exp-meta">{'Elapsed'|i18n( 'design/admin/setup/cronjobs' )}: {$cronjob_status.elapsed}s</span>
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
    <button type="submit" class="exp-btn" name="StopCronjobButton" value="1"{if $cronjob_status.running|not} disabled="disabled"{/if}>{'Stop running job'|i18n( 'design/admin/setup/cronjobs' )}</button>
    <button type="submit" class="exp-btn" name="ClearCronjobLogButton" value="1">{'Clear logs'|i18n( 'design/admin/setup/cronjobs' )}</button>
</div>

<div class="exp-grid">
{foreach $cronjob_parts as $cronjob_part}
    {def $cronjob_runnable = and( $cronjob_part.forbidden|not,
                                  $cronjob_part.missing|lt( $cronjob_part.scripts|count ),
                                  $cronjob_status.running|not,
                                  $cronjob_php_binary|ne('') )}
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
            <button type="submit" class="exp-btn exp-btn-primary" name="LaunchCronjobButton"
                    value="{$cronjob_part.name|wash}"{if $cronjob_runnable|not} disabled="disabled"{/if}>{'Run now'|i18n( 'design/admin/setup/cronjobs' )}</button>
            {if $cronjob_part.forbidden}
                <span class="exp-note">{'Blocked by cronjob.ini ForbiddenParts.'|i18n( 'design/admin/setup/cronjobs' )}</span>
            {elseif $cronjob_part.missing|gt(0)}
                <span class="exp-note">{$cronjob_part.missing} {'of its scripts are missing.'|i18n( 'design/admin/setup/cronjobs' )}</span>
            {elseif $cronjob_status.running}
                <span class="exp-note">{'Another job is running.'|i18n( 'design/admin/setup/cronjobs' )}</span>
            {/if}
        </div>
    </div>
    {undef $cronjob_runnable}
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
var expCronjobStreamUrl = {$cronjob_stream_url|ezurl()};
var expCronjobRunning   = {if $cronjob_status.running}true{else}false{/if};
var expCronjobOffset    = {$cronjob_log_offset};
{literal}
(function () {
    var consoleEl = document.getElementById( 'cronjob-console' );
    var statusEl  = document.getElementById( 'cronjob-stream-status' );
    if ( !consoleEl || typeof window.EventSource === 'undefined' )
        return;

    var colours = {
        phase: '#7fd1ff', ok: '#d8d8d8', warn: '#e8c765',
        error: '#ff8a80', info: '#9a9a9a', done: '#7fd1ff'
    };
    var source = null;

    function write( type, text ) {
        var atBottom = consoleEl.scrollTop + consoleEl.clientHeight >= consoleEl.scrollHeight - 8;
        var line = document.createElement( 'div' );
        line.style.color = colours[type] || '#d8d8d8';
        if ( type === 'phase' || type === 'done' ) line.style.fontWeight = 'bold';
        line.appendChild( document.createTextNode( text ) );
        consoleEl.appendChild( line );
        if ( atBottom ) consoleEl.scrollTop = consoleEl.scrollHeight;
    }

    function say( text ) {
        statusEl.innerHTML = '';
        statusEl.appendChild( document.createTextNode( text ) );
    }

    function stop( message ) {
        if ( source ) { source.close(); source = null; }
        say( message );
    }

    // Only follow when something is actually running. The page already printed
    // what the log holds, so with nothing running there is nothing to add, and
    // opening a stream would hold a php worker open for no reason.
    if ( !expCronjobRunning ) {
        consoleEl.scrollTop = consoleEl.scrollHeight;
        return;
    }

    say( 'following…' );
    // Offset is where the page's own copy of the log ends, so the stream picks
    // up from there instead of reprinting it.
    source = new EventSource( expCronjobStreamUrl + '?Offset=' + expCronjobOffset );

    source.onmessage = function ( event ) {
        var payload;
        try { payload = JSON.parse( event.data ); }
        catch ( e ) { return; }
        write( payload.type, payload.message );
        if ( payload.type === 'done' ) {
            stop( 'finished' );
            // The controls reflect a job that is no longer running, so the page
            // is reloaded once rather than left showing a stale Stop button.
            window.setTimeout( function () { window.location.reload(); }, 1200 );
        }
    };

    source.addEventListener( 'end', function () { stop( 'finished' ); } );

    source.onerror = function () {
        // EventSource reconnects by itself, which would start the tail again
        // from the offset the page was rendered with and reprint everything.
        stop( 'stream closed' );
    };

    consoleEl.scrollTop = consoleEl.scrollHeight;
})();
{/literal}
</script>

</div></div></div>
</div>
