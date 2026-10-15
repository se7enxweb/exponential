{* The audit/recent view: the latest audit events (newest first) and the state of each channel's hash chain.
   Read-only (policy audit/read). Variables: events, chains, channel, channel_names, audit_enabled, can_manage, log_dir,
   verified_today_only, limit, request_id, audit_sources (audit/sources.tpl). *}

<div class="context-block a4-audit-view a4-page" id="exp-audit-recent">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Audit: recent events'|i18n( 'design/admin/audit' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{if $audit_enabled|not}
<div class="message-warning" role="status"><h2>{'Audit is switched off ([AuditSettings] Audit=disabled): nothing new is recorded.'|i18n( 'design/admin/audit' )}</h2></div>
{/if}

<div class="block">
<p class="a4-audit-note">{'The latest %limit events of the audit channels in %dir, newest first. Times are local; each record is written in UTC.'|i18n( 'design/admin/audit',, hash( '%limit', $limit, '%dir', $log_dir ) )}</p>

<ul class="a4-audit-chains" aria-label="{'Hash chain per channel'|i18n( 'design/admin/audit' )|wash}">
{foreach $chains as $c}
    <li><span class="a4-audit-chain {$c.result|wash}" title="{if $c.first_break}{$c.first_break|wash}{/if}">{$c.channel|wash}: {if $c.result|eq( 'unverified' )}{'not verified yet'|i18n( 'design/admin/audit' )}{else}{$c.result|wash}{/if} ({if $c.verified_at}{'verified %time'|i18n( 'design/admin/audit',, hash( '%time', $c.verified_at ) )}{if $c.partial} {'(today only)'|i18n( 'design/admin/audit' )}{/if}, {/if}{'%n records'|i18n( 'design/admin/audit',, hash( '%n', $c.records ) )}{if $c.breaks}, {'%n breaks, first: %first'|i18n( 'design/admin/audit',, hash( '%n', $c.breaks, '%first', $c.first_break ) )|wash}{/if}{if $c.repairs}, {'%n repaired'|i18n( 'design/admin/audit',, hash( '%n', $c.repairs ) )}{/if})</span></li>
{/foreach}
{if $chains|count|not}<li class="a4-audit-note">{'No audit files yet.'|i18n( 'design/admin/audit' )}</li>{/if}
</ul>
<form method="post" action={concat( 'audit/recent', $channel|ne( '' )|choose( '', concat( '/(channel)/', $channel ) ) )|ezurl} class="a4-audit-verify">
    <p class="a4-audit-note">{'The chain status is the result of the last verification (daily maintenance, exp:audit verify or this button).'|i18n( 'design/admin/audit' )}
    {if $can_manage}<input type="submit" class="button" name="AuditVerifyNowButton" value="{'Verify now'|i18n( 'design/admin/audit' )}" />{/if}</p>
</form>

{if $channel_names|count}
<p class="a4-audit-scope">
    {if $channel}<a href={'audit/recent'|ezurl}>{'All channels'|i18n( 'design/admin/audit' )}</a>{else}<span class="current">{'All channels'|i18n( 'design/admin/audit' )}</span>{/if}
    {foreach $channel_names as $name} | {if eq( $channel, $name )}<span class="current">{$name|wash}</span>{else}<a href={concat( 'audit/recent/(channel)/', $name )|ezurl}>{$name|wash}</a>{/if}{/foreach}
</p>
{/if}
{include uri='design:audit/sources.tpl'}
</div>

{if $events|count}
<div class="a4-audit-table">
<table class="list" cellspacing="0">
<tr>
    <th scope="col">{'Time'|i18n( 'design/admin/audit' )}</th>
    <th scope="col">{'Event'|i18n( 'design/admin/audit' )}</th>
    <th scope="col">{'Actor'|i18n( 'design/admin/audit' )}</th>
    <th scope="col">{'Object'|i18n( 'design/admin/audit' )}</th>
    <th scope="col">{'Result'|i18n( 'design/admin/audit' )}</th>
    <th scope="col">{'Request'|i18n( 'design/admin/audit' )}</th>
</tr>
{foreach $events as $e sequence array( 'bglight', 'bgdark' ) as $style}
<tr class="{$style}">
    <td class="a4-audit-time" title="{$e.time_utc|wash}">{$e.time|wash}</td>
    <td class="a4-audit-name"><code>{$e.name|wash}</code><br /><small>{$e.channel|wash} #{$e.seq}, {$e.severity|wash}</small></td>
    <td>{$e.actor|wash}</td>
    <td>{$e.object|wash}</td>
    <td><span class="a4-audit-result {$e.result|wash}">{$e.result|wash}</span>{if $e.reason}<br /><small>{$e.reason|wash}</small>{/if}</td>
    <td><code title="{$e.id|wash}">{$e.request|wash}</code>{if $e.engine}<br /><small>{$e.engine|wash}</small>{/if}</td>
</tr>
{/foreach}
</table>
</div>
{else}
<div class="block a4-audit-empty">
    <p>{'No audit events yet.'|i18n( 'design/admin/audit' )}</p>
</div>
{/if}

<p class="a4-audit-note">{'This page is request %id; opening it is itself recorded (system.audit.read). On the command line: exp:audit tail, exp:audit show <id>, exp:audit verify.'|i18n( 'design/admin/audit',, hash( '%id', $request_id ) )|wash}</p>

{* DESIGN: Content END *}</div></div></div>

</div>
