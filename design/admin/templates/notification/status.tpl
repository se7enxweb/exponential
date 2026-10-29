{* The notification status (admin): for administrators. Variables from notification/status: status (expNotificationService::status()),
   problems, notice, missing_rules, recent_subscriptions, recent_total, events, job_available, job_error.
   The admin4 page (design/admin4/templates/notification/status.tpl) runs the notifications in the background with
   javascript; this one has no javascript: it runs the notification filter by a form (notification/runfilter) and
   names the console commands. *}

{if and( is_set( $notice ), $notice )}
<div class="{if eq( $notice.type, 'error' )}message-error{elseif eq( $notice.type, 'warning' )}message-warning{else}message-feedback{/if}" role="status">
<h2>{$notice.text|wash}</h2>
</div>
{/if}

{if $problems|count|gt( 0 )}
{foreach $problems as $problem}
<div class="{if eq( $problem.level, 'error' )}message-error{elseif eq( $problem.level, 'warning' )}message-warning{else}message-feedback{/if}">
<p><b>{if $problem.level|eq( 'error' )}{'Problem'|i18n( 'design/admin/notification/status' )}{elseif $problem.level|eq( 'warning' )}{'Attention'|i18n( 'design/admin/notification/status' )}{else}{'Note'|i18n( 'design/admin/notification/status' )}{/if}:</b> {$problem.text|wash}</p>
</div>
{/foreach}
{/if}

<div class="context-block" id="exp-notify-status">
{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Notification status'|i18n( 'design/admin/notification/status' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-attributes">
<p>{'Events wait here until the notification cronjob handles them: it sends the messages, or keeps them for a digest. This page shows what waits, what was sent and what looks wrong.'|i18n( 'design/admin/notification/status' )}
<a href={'notification/settings'|ezurl}>{'My notification settings'|i18n( 'design/admin/notification/status' )}</a></p>
{if $problems|count|eq( 0 )}
<p>{'Nothing looks wrong.'|i18n( 'design/admin/notification/status' )}</p>
{/if}

<table class="list" cellspacing="0">
<tr><th scope="row">{'Events waiting to be handled'|i18n( 'design/admin/notification/status' )}</th><td>{$status.pending_total}</td></tr>
<tr><th scope="row">{'Messages kept for a digest'|i18n( 'design/admin/notification/status' )}</th><td>{$status.items_digest}{if $status.items_due|gt( 0 )} ({$status.items_due} {'due'|i18n( 'design/admin/notification/status' )}){/if}</td></tr>
<tr><th scope="row">{'Subscriptions by %users users'|i18n( 'design/admin/notification/status',, hash( '%users', $status.subscribers ) )}</th><td>{$status.subscriptions}</td></tr>
<tr><th scope="row">{'Messages to %count recipients, last 24 hours'|i18n( 'design/admin/notification/status',, hash( '%count', $status.sent_24h.recipients ) )}</th><td>{$status.sent_24h.mails}</td></tr>
<tr><th scope="row">{'Last run'|i18n( 'design/admin/notification/status' )}</th><td>{if $status.last_run}{$status.last_run.time|l10n( 'shortdatetime' )} ({$status.last_run.source|wash}){if $status.last_run.result|ne( 'ok' )} &ndash; {$status.last_run.result|wash}{/if}{else}&mdash;{/if}</td></tr>
</table>
</div>

{* DESIGN: Content END *}</div></div></div>
</div>

<div class="context-block">
<div class="box-header"><div class="box-ml"><h2 class="context-title">{'Run the notifications'|i18n( 'design/admin/notification/status' )}</h2><div class="header-subline"></div></div></div>
<div class="box-ml"><div class="box-mr"><div class="box-content"><div class="context-attributes">
{if $status.running}
<p>{'A run is in progress (process %pid, since %time).'|i18n( 'design/admin/notification/status',, hash( '%pid', $status.running.pid, '%time', $status.running.since|l10n( 'shortdatetime' ) ) )}</p>
{/if}
<p>{'Use the console: ./console exp:notification:run --apply'|i18n( 'design/admin/notification/status' )}</p>
</div></div></div></div>
<div class="controlbar"><div class="block">
<form method="post" action={'notification/runfilter'|ezurl}>
    <input class="button" type="submit" name="RunFilterButton" value="{'Run notification filter'|i18n( 'design/admin/notification/runfilter' )}" />
</form>
</div></div>
</div>

<div class="context-block">
<div class="box-header"><div class="box-ml"><h2 class="context-title">{'Recent runs'|i18n( 'design/admin/notification/status' )}</h2><div class="header-subline"></div></div></div>
<div class="box-ml"><div class="box-mr"><div class="box-content">
{if $status.runs|count|gt( 0 )}
<table class="list" cellspacing="0">
<tr><th scope="col">{'When'|i18n( 'design/admin/notification/status' )}</th><th scope="col">{'Started by'|i18n( 'design/admin/notification/status' )}</th><th scope="col">{'Events'|i18n( 'design/admin/notification/status' )}</th><th scope="col">{'Messages'|i18n( 'design/admin/notification/status' )}</th><th scope="col">{'Result'|i18n( 'design/admin/notification/status' )}</th></tr>
{foreach $status.runs as $run sequence array( 'bglight', 'bgdark' ) as $style}
<tr class="{$style}"><td>{$run.time|l10n( 'shortdatetime' )}</td><td>{$run.source|wash}</td><td>{$run.events}</td><td>{$run.mails} / {$run.recipients}</td>
    <td>{if $run.result|eq( 'ok' )}{if $run.failed|gt( 0 )}{$run.failed} {'failed'|i18n( 'design/admin/notification/status' )}{else}OK{/if}{if first_set( $run.send_failed, 0 )|gt( 0 )}, {first_set( $run.send_failed, 0 )} {'not sent'|i18n( 'design/admin/notification/status' )}{/if}{if first_set( $run.dropped, 0 )|gt( 0 )}, {first_set( $run.dropped, 0 )} {'given up'|i18n( 'design/admin/notification/status' )}{/if}{else}{$run.result|wash}{/if} ({$run.ms} ms)</td></tr>
{/foreach}
</table>
<p>{'Messages: sent / recipients.'|i18n( 'design/admin/notification/status' )}</p>
{else}
<div class="context-attributes">
<p><b>{'No run is recorded yet'|i18n( 'design/admin/notification/status' )}</b></p>
<p>{'The notification cronjob records each of its runs here. Add the part frequent to the crontab, or run exp:notification:run --apply.'|i18n( 'design/admin/notification/status' )}</p>
</div>
{/if}
</div></div></div>
</div>

<div class="context-block">
<div class="box-header"><div class="box-ml"><h2 class="context-title">{'Events'|i18n( 'design/admin/notification/status' )} ({$status.pending_total} {'waiting'|i18n( 'design/admin/notification/status' )})</h2><div class="header-subline"></div></div></div>
<div class="box-ml"><div class="box-mr"><div class="box-content">
{if $events|count|gt( 0 )}
<table class="list" cellspacing="0">
<tr><th scope="col">#</th><th scope="col">{'Type'|i18n( 'design/admin/notification/status' )}</th><th scope="col">{'Status'|i18n( 'design/admin/notification/status' )}</th><th scope="col">{'Made'|i18n( 'design/admin/notification/status' )}</th></tr>
{foreach $events as $event sequence array( 'bglight', 'bgdark' ) as $style}
<tr class="{$style}"><td>{$event.id}</td><td>{$event.type|wash}</td><td>{if $event.status|eq( 'pending' )}{'Waiting'|i18n( 'design/admin/notification/status' )}{else}{'Handled'|i18n( 'design/admin/notification/status' )} ({$event.items}){/if}</td><td>{if $event.created}{$event.created|l10n( 'shortdatetime' )}{else}&mdash;{/if}</td></tr>
{/foreach}
</table>
{else}
<div class="context-attributes"><p>{'No events.'|i18n( 'design/admin/notification/status' )}</p></div>
{/if}
</div></div></div>
<div class="controlbar"><div class="block">
<form method="post" action={'notification/status'|ezurl}>
    <input class="{if $status.handled_orphans|eq( 0 )}button-disabled{else}button{/if}" type="submit" name="CleanupHandled" value="{'Remove handled events with nothing left to send'|i18n( 'design/admin/notification/status' )}"{if $status.handled_orphans|eq( 0 )} disabled="disabled"{/if} />
</form>
{* no javascript: the browser asks for the confirmation, a checkbox the form requires (the view does not check it,
   as it does not check the confirm() of admin4) *}
<form method="post" action={'notification/status'|ezurl}>
    <label for="nf-age">{'Remove events older than'|i18n( 'design/admin/notification/status' )}</label>
    <select id="nf-age" name="OlderThan">
        <option value="30d">{'30 days'|i18n( 'design/admin/notification/status' )}</option>
        <option value="90d">{'90 days'|i18n( 'design/admin/notification/status' )}</option>
        <option value="180d">{'180 days'|i18n( 'design/admin/notification/status' )}</option>
        <option value="365d">{'1 year'|i18n( 'design/admin/notification/status' )}</option>
    </select>
    <label><input type="checkbox" name="CleanupOldConfirm" value="1" required="required" /> {'Remove all events older than the chosen age, with their waiting messages? This cannot be undone.'|i18n( 'design/admin/notification/status' )}</label>
    <input class="button" type="submit" name="CleanupOld" value="{'Remove'|i18n( 'design/admin/notification/status' )}" />
</form>
</div></div>
</div>

<div class="context-block">
<div class="box-header"><div class="box-ml"><h2 class="context-title">{'Subscriptions'|i18n( 'design/admin/notification/status' )} ({$recent_total})</h2><div class="header-subline"></div></div></div>
<div class="box-ml"><div class="box-mr"><div class="box-content">
{if $recent_subscriptions|count|gt( 0 )}
<table class="list" cellspacing="0">
<tr><th scope="col">{'User'|i18n( 'design/admin/notification/status' )}</th><th scope="col">{'Item'|i18n( 'design/admin/notification/status' )}</th></tr>
{foreach $recent_subscriptions as $row sequence array( 'bglight', 'bgdark' ) as $style}
<tr class="{$style}"><td>{if $row.login|ne( '' )}{$row.login|wash}{else}#{$row.user_id}{/if}</td><td>{if $row.missing}{'Content no longer exists'|i18n( 'design/admin/notification/status' )} #{$row.node_id}{else}{$row.name|wash} ({$row.class_name|wash}){/if}</td></tr>
{/foreach}
</table>
<p>{'The list per user and item: ./console exp:notification:subscriptions list'|i18n( 'design/admin/notification/status' )}</p>
{else}
<div class="context-attributes"><p>{'No one follows any item yet.'|i18n( 'design/admin/notification/status' )}</p></div>
{/if}
</div></div></div>
{if $missing_rules|gt( 0 )}
<div class="controlbar"><div class="block">
<form method="post" action={'notification/status'|ezurl}>
    <input class="button" type="submit" name="RemoveMissingRules" value="{'Remove %count subscriptions whose content is gone'|i18n( 'design/admin/notification/status',, hash( '%count', $missing_rules ) )}" />
</form>
</div></div>
{/if}
</div>

<div class="context-block">
<div class="box-header"><div class="box-ml"><h2 class="context-title">{'Mail and handlers'|i18n( 'design/admin/notification/status' )}</h2><div class="header-subline"></div></div></div>
<div class="box-ml"><div class="box-mr"><div class="box-content">
<table class="list" cellspacing="0">
<tr><th scope="row">{'Mail transport'|i18n( 'design/admin/notification/status' )}</th><td>{$status.transport|wash}</td></tr>
<tr><th scope="row">{'Sender'|i18n( 'design/admin/notification/status' )}</th><td>{$status.sender|wash}</td></tr>
<tr><th scope="row">{'Handlers'|i18n( 'design/admin/notification/status' )}</th><td>{foreach $status.handlers as $h}{$h|wash}{delimiter}, {/delimiter}{/foreach}</td></tr>
<tr><th scope="row">{'Digests chosen'|i18n( 'design/admin/notification/status' )}</th><td>{'daily'|i18n( 'design/admin/notification/status' )} {$status.digest.daily}, {'weekly'|i18n( 'design/admin/notification/status' )} {$status.digest.weekly}, {'monthly'|i18n( 'design/admin/notification/status' )} {$status.digest.monthly}</td></tr>
<tr><th scope="row">{'Collaboration rules'|i18n( 'design/admin/notification/status' )}</th><td>{$status.collab_rules}</td></tr>
<tr><th scope="row">{'Messages waiting now'|i18n( 'design/admin/notification/status' )}</th><td>{$status.items_now}</td></tr>
</table>
<p>{'Console:'|i18n( 'design/admin/notification/status' )} <code>exp:notification:status</code> <code>exp:notification:run --dry-run</code> <code>exp:notification:events</code> <code>exp:notification:subscriptions</code></p>
</div></div></div>
</div>
