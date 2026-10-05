{* The notification status (admin4): for administrators. Variables from notification/status: status (expNotificationService::status()),
   problems, notice, missing_rules, recent_subscriptions, recent_total, events, job_available, job_error. *}
{include uri='design:notification/parts/style.tpl'}

<div class="nf" id="exp-notify-status">

    <div class="nf-head">
        <div>
            <p class="nf-crumb"><a href={'notification/settings'|ezurl}>{'My notification settings'|i18n( 'design/admin/notification/status' )}</a></p>
            <h1>{'Notification status'|i18n( 'design/admin/notification/status' )}</h1>
            <p>{'Events wait here until the notification cronjob handles them: it sends the messages, or keeps them for a digest. This page shows what waits, what was sent and what looks wrong.'|i18n( 'design/admin/notification/status' )}</p>
        </div>
    </div>

    {include uri='design:notification/parts/notice.tpl' notice=first_set( $notice, false() )}

{if $problems|count|gt( 0 )}
    <ul class="nf-problems">
    {foreach $problems as $problem}
        <li class="{$problem.level|wash}"><b>{if $problem.level|eq( 'error' )}{'Problem'|i18n( 'design/admin/notification/status' )}{elseif $problem.level|eq( 'warning' )}{'Attention'|i18n( 'design/admin/notification/status' )}{else}{'Note'|i18n( 'design/admin/notification/status' )}{/if}:</b> {$problem.text|wash}</li>
    {/foreach}
    </ul>
{else}
    <div class="nf-notice nf-notice-success" role="status"><p>{'Nothing looks wrong.'|i18n( 'design/admin/notification/status' )}</p></div>
{/if}

    <ul class="nf-stats">
        <li><div class="nf-stat{if $status.pending_total|gt( 0 )} hot{/if}"><strong>{$status.pending_total}</strong><span>{'Events waiting to be handled'|i18n( 'design/admin/notification/status' )}</span></div></li>
        <li><div class="nf-stat"><strong>{$status.items_digest}</strong><span>{'Messages kept for a digest'|i18n( 'design/admin/notification/status' )}{if $status.items_due|gt( 0 )} ({$status.items_due} {'due'|i18n( 'design/admin/notification/status' )}){/if}</span></div></li>
        <li><div class="nf-stat"><strong>{$status.subscriptions}</strong><span>{'Subscriptions by %users users'|i18n( 'design/admin/notification/status',, hash( '%users', $status.subscribers ) )}</span></div></li>
        <li><div class="nf-stat"><strong>{$status.sent_24h.mails}</strong><span>{'Messages to %count recipients, last 24 hours'|i18n( 'design/admin/notification/status',, hash( '%count', $status.sent_24h.recipients ) )}</span></div></li>
        <li><div class="nf-stat{if and( $status.last_run, $status.last_run.result|ne( 'ok' ) )} bad{/if}"><strong>{if $status.last_run}{$status.last_run.time|l10n( 'shortdatetime' )}{else}&mdash;{/if}</strong><span>{'Last run'|i18n( 'design/admin/notification/status' )}{if $status.last_run} ({$status.last_run.source|wash}){/if}</span></div></li>
    </ul>

    <section class="nf-card" id="nf-run">
        <h2>{'Run the notifications'|i18n( 'design/admin/notification/status' )}</h2>
        <p class="nf-lead">{'The notification cronjob does this on a schedule (part frequent, or php runcronjobs.php notification). Run now starts the same pass in the background; Preview lists what would be sent and changes nothing.'|i18n( 'design/admin/notification/status' )}</p>
{if $status.running}
        <div class="nf-notice nf-notice-info" role="status"><p>{'A run is in progress (process %pid, since %time).'|i18n( 'design/admin/notification/status',, hash( '%pid', $status.running.pid, '%time', $status.running.since|l10n( 'shortdatetime' ) ) )}</p></div>
{/if}
{if $job_available|not}
        <div class="nf-notice nf-notice-warning" role="status"><p>{'Run now needs a background process: %reason'|i18n( 'design/admin/notification/status',, hash( '%reason', $job_error ) )|wash} {'Use the console: ./console exp:notification:run'|i18n( 'design/admin/notification/status' )}</p></div>
{/if}
        <div class="nf-actions">
            <button type="button" class="nf-btn primary" id="nf-run-now"{if $job_available|not} disabled="disabled"{/if}>{'Run now'|i18n( 'design/admin/notification/status' )}</button>
            <button type="button" class="nf-btn" id="nf-run-dry"{if $job_available|not} disabled="disabled"{/if}>{'Preview (dry run)'|i18n( 'design/admin/notification/status' )}</button>
        </div>
        <p class="nf-hint" id="nf-run-state" role="status" aria-live="polite"></p>
        <pre class="nf-console" id="nf-console" hidden="hidden"></pre>
        <form method="post" action={'notification/runfilter'|ezurl} class="nf-hint">
            {'Without JavaScript:'|i18n( 'design/admin/notification/status' )}
            <input class="nf-btn" type="submit" name="RunFilterButton" value="{'Run notification filter'|i18n( 'design/admin/notification/runfilter' )}" />
        </form>
    </section>

    <div class="nf-layout two">

        <section class="nf-card">
            <h2>{'Recent runs'|i18n( 'design/admin/notification/status' )}</h2>
{if $status.runs|count|gt( 0 )}
            <div class="nf-scroll"><table class="nf-table">
                <thead><tr><th>{'When'|i18n( 'design/admin/notification/status' )}</th><th>{'Started by'|i18n( 'design/admin/notification/status' )}</th><th>{'Events'|i18n( 'design/admin/notification/status' )}</th><th>{'Messages'|i18n( 'design/admin/notification/status' )}</th><th>{'Result'|i18n( 'design/admin/notification/status' )}</th></tr></thead>
                <tbody>
    {foreach $status.runs as $run}
                    <tr><td>{$run.time|l10n( 'shortdatetime' )}</td><td>{$run.source|wash}</td><td>{$run.events}</td><td>{$run.mails} / {$run.recipients}</td>
                        <td>{if $run.result|eq( 'ok' )}<span class="nf-badge ok">{if $run.failed|gt( 0 )}{$run.failed} {'failed'|i18n( 'design/admin/notification/status' )}{else}OK{/if}</span>{else}<span class="nf-badge bad">{$run.result|wash}</span>{/if} <small>{$run.ms} ms</small></td></tr>
    {/foreach}
                </tbody>
            </table></div>
            <p class="nf-hint">{'Messages: sent / recipients.'|i18n( 'design/admin/notification/status' )}</p>
{else}
            <div class="nf-empty"><h3>{'No run is recorded yet'|i18n( 'design/admin/notification/status' )}</h3><p>{'The notification cronjob records each of its runs here. Add the part frequent to the crontab, or run exp:notification:run.'|i18n( 'design/admin/notification/status' )}</p></div>
{/if}
        </section>

        <section class="nf-card">
            <h2>{'Events'|i18n( 'design/admin/notification/status' )} <span class="nf-tag">{$status.pending_total} {'waiting'|i18n( 'design/admin/notification/status' )}</span></h2>
{if $events|count|gt( 0 )}
            <div class="nf-scroll"><table class="nf-table">
                <thead><tr><th>#</th><th>{'Type'|i18n( 'design/admin/notification/status' )}</th><th>{'Status'|i18n( 'design/admin/notification/status' )}</th><th>{'Made'|i18n( 'design/admin/notification/status' )}</th></tr></thead>
                <tbody>
    {foreach $events as $event}
                    <tr><td>{$event.id}</td><td>{$event.type|wash}</td><td>{if $event.status|eq( 'pending' )}{'Waiting'|i18n( 'design/admin/notification/status' )}{else}{'Handled'|i18n( 'design/admin/notification/status' )} ({$event.items}){/if}</td><td>{if $event.created}{$event.created|l10n( 'shortdatetime' )}{else}&mdash;{/if}</td></tr>
    {/foreach}
                </tbody>
            </table></div>
{else}
            <p class="nf-hint">{'No events.'|i18n( 'design/admin/notification/status' )}</p>
{/if}
            <form method="post" action={'notification/status'|ezurl}>
                <div class="nf-actions">
                    <input class="nf-btn" type="submit" name="CleanupHandled" value="{'Remove handled events with nothing left to send'|i18n( 'design/admin/notification/status' )}"{if $status.handled_orphans|eq( 0 )} disabled="disabled"{/if} />
                </div>
            </form>
            <form method="post" action={'notification/status'|ezurl} onsubmit="return confirm( this.getAttribute( 'data-confirm' ) );" data-confirm="{'Remove all events older than the chosen age, with their waiting messages? This cannot be undone.'|i18n( 'design/admin/notification/status' )|wash}">
                <div class="nf-actions nf-field">
                    <label for="nf-age">{'Remove events older than'|i18n( 'design/admin/notification/status' )}</label>
                    <select id="nf-age" name="OlderThan">
                        <option value="30d">{'30 days'|i18n( 'design/admin/notification/status' )}</option>
                        <option value="90d">{'90 days'|i18n( 'design/admin/notification/status' )}</option>
                        <option value="180d">{'180 days'|i18n( 'design/admin/notification/status' )}</option>
                        <option value="365d">{'1 year'|i18n( 'design/admin/notification/status' )}</option>
                    </select>
                    <input class="nf-btn danger" type="submit" name="CleanupOld" value="{'Remove'|i18n( 'design/admin/notification/status' )}" />
                </div>
            </form>
        </section>

        <section class="nf-card">
            <h2>{'Subscriptions'|i18n( 'design/admin/notification/status' )} <span class="nf-tag">{$recent_total}</span></h2>
{if $recent_subscriptions|count|gt( 0 )}
            <div class="nf-scroll"><table class="nf-table">
                <thead><tr><th>{'User'|i18n( 'design/admin/notification/status' )}</th><th>{'Item'|i18n( 'design/admin/notification/status' )}</th></tr></thead>
                <tbody>
    {foreach $recent_subscriptions as $row}
                    <tr><td>{if $row.login|ne( '' )}{$row.login|wash}{else}#{$row.user_id}{/if}</td><td>{if $row.missing}<span class="nf-badge warn">{'Content no longer exists'|i18n( 'design/admin/notification/status' )}</span> #{$row.node_id}{else}{$row.name|wash} <small>({$row.class_name|wash})</small>{/if}</td></tr>
    {/foreach}
                </tbody>
            </table></div>
            <p class="nf-hint">{'The list per user and item: ./console exp:notification:subscriptions list'|i18n( 'design/admin/notification/status' )}</p>
{else}
            <p class="nf-hint">{'No one follows any item yet.'|i18n( 'design/admin/notification/status' )}</p>
{/if}
{if $missing_rules|gt( 0 )}
            <form method="post" action={'notification/status'|ezurl}>
                <div class="nf-actions"><input class="nf-btn danger" type="submit" name="RemoveMissingRules" value="{'Remove %count subscriptions whose content is gone'|i18n( 'design/admin/notification/status',, hash( '%count', $missing_rules ) )}" /></div>
            </form>
{/if}
        </section>

        <section class="nf-card">
            <h2>{'Mail and handlers'|i18n( 'design/admin/notification/status' )}</h2>
            <dl class="nf-facts">
                <div><dt>{'Mail transport'|i18n( 'design/admin/notification/status' )}</dt><dd>{$status.transport|wash}</dd></div>
                <div><dt>{'Sender'|i18n( 'design/admin/notification/status' )}</dt><dd>{$status.sender|wash}</dd></div>
                <div><dt>{'Handlers'|i18n( 'design/admin/notification/status' )}</dt><dd>{foreach $status.handlers as $h}{$h|wash}{delimiter}, {/delimiter}{/foreach}</dd></div>
                <div><dt>{'Digests chosen'|i18n( 'design/admin/notification/status' )}</dt><dd>{'daily'|i18n( 'design/admin/notification/status' )} {$status.digest.daily}, {'weekly'|i18n( 'design/admin/notification/status' )} {$status.digest.weekly}, {'monthly'|i18n( 'design/admin/notification/status' )} {$status.digest.monthly}</dd></div>
                <div><dt>{'Collaboration rules'|i18n( 'design/admin/notification/status' )}</dt><dd>{$status.collab_rules}</dd></div>
                <div><dt>{'Messages waiting now'|i18n( 'design/admin/notification/status' )}</dt><dd>{$status.items_now}</dd></div>
            </dl>
            <p class="nf-hint">{'Console:'|i18n( 'design/admin/notification/status' )} <code>exp:notification:status</code> <code>exp:notification:run --dry-run</code> <code>exp:notification:events</code> <code>exp:notification:subscriptions</code></p>
        </section>

    </div>
</div>

<script type="text/javascript">
(function () {ldelim}
    var jobUrl = {'notification/job'|ezurl()};
    var runBtn = document.getElementById( 'nf-run-now' ), dryBtn = document.getElementById( 'nf-run-dry' );
    var box = document.getElementById( 'nf-console' ), state = document.getElementById( 'nf-run-state' );
    if ( !runBtn || !dryBtn ) return;
    var csrf = document.querySelector( 'meta[name="csrf-token"]' );
    var T = {ldelim}
        starting: "{'Starting in the background...'|i18n( 'design/admin/notification/status' )|wash( 'javascript' )}",
        finished: "{'Finished. Reload the page to see the new numbers.'|i18n( 'design/admin/notification/status' )|wash( 'javascript' )}",
        failed: "{'The run failed or stopped. See the lines above.'|i18n( 'design/admin/notification/status' )|wash( 'javascript' )}",
        confirm: "{'Run the notifications now? Messages that are due are sent.'|i18n( 'design/admin/notification/status' )|wash( 'javascript' )}"
    {rdelim};
    var timer = null, jobId = null, offset = 0;

    function tokenField() {ldelim}
        var f = document.querySelector( 'input[name="ezxform_token"]' );
        return f ? f.value : '';
    {rdelim}
    function post( data, done ) {ldelim}
        var r = new XMLHttpRequest();
        r.open( 'POST', jobUrl, true );
        r.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
        r.setRequestHeader( 'X-Requested-With', 'XMLHttpRequest' );
        if ( csrf ) r.setRequestHeader( 'X-CSRF-Token', csrf.getAttribute( 'content' ) );
        var t = tokenField();
        if ( t ) data.ezxform_token = t;
        r.onload = function () {ldelim}
            var a = null; try {ldelim} a = JSON.parse( r.responseText ); {rdelim} catch ( e ) {ldelim}{rdelim}
            done( r.status, a );
        {rdelim};
        r.onerror = function () {ldelim} done( 0, null ); {rdelim};
        var p = []; for ( var k in data ) p.push( encodeURIComponent( k ) + '=' + encodeURIComponent( data[k] ) );
        r.send( p.join( '&' ) );
    {rdelim}
    function line( text ) {ldelim}
        box.appendChild( document.createTextNode( text + "\n" ) );
        box.scrollTop = box.scrollHeight;
    {rdelim}
    function busy( on ) {ldelim} runBtn.disabled = on; dryBtn.disabled = on; {rdelim}
    function poll() {ldelim}
        if ( !jobId ) return;
        var r = new XMLHttpRequest();
        r.open( 'GET', jobUrl + '/' + jobId + '/' + offset, true );
        r.setRequestHeader( 'X-Requested-With', 'XMLHttpRequest' );
        r.onload = function () {ldelim}
            var a = null; try {ldelim} a = JSON.parse( r.responseText ); {rdelim} catch ( e ) {ldelim}{rdelim}
            if ( !a || !a.events ) {ldelim} state.textContent = T.failed; busy( false ); jobId = null; return; {rdelim}
            var failed = false;
            for ( var i = 0; i < a.events.length; i++ ) {ldelim}
                var e = a.events[i];
                if ( e.type === 'end' ) {ldelim} failed = e.result !== 'ok'; {rdelim}
                else line( e.message );
            {rdelim}
            offset = a.offset;
            if ( a.done ) {ldelim} state.textContent = failed ? T.failed : T.finished; busy( false ); jobId = null; {rdelim}
            else timer = window.setTimeout( poll, 1000 );
        {rdelim};
        r.onerror = function () {ldelim} timer = window.setTimeout( poll, 3000 ); {rdelim};
        r.send();
    {rdelim}
    function start( dry ) {ldelim}
        if ( jobId ) return;
        if ( !dry && !window.confirm( T.confirm ) ) return;
        box.hidden = false; box.textContent = ''; offset = 0; busy( true ); state.textContent = T.starting;
        post( dry ? {ldelim} Action: 'start', DryRun: '1' {rdelim} : {ldelim} Action: 'start' {rdelim}, function ( status, a ) {ldelim}
            if ( !a || !a.id ) {ldelim} line( a && a.error ? a.error : 'HTTP ' + status ); state.textContent = T.failed; busy( false ); return; {rdelim}
            jobId = a.id; state.textContent = '';
            poll();
        {rdelim} );
    {rdelim}
    runBtn.onclick = function () {ldelim} start( false ); {rdelim};
    dryBtn.onclick = function () {ldelim} start( true ); {rdelim};
{rdelim})();
</script>
