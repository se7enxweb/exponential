{* The audit/recent view: the latest audit events (newest first) and the state of each channel's hash chain.
   Read-only (policy audit/read). Variables: events, chains, channel, channel_names, audit_enabled, log_dir,
   verified_today_only, limit, request_id. *}
<style type="text/css">
{literal}
#exp-audit-recent .au-chains { display: flex; flex-wrap: wrap; gap: .4em .8em; margin: 0 0 .8em; padding: 0; list-style: none; }
#exp-audit-recent .au-chain { display: inline-block; padding: .2em .6em; border-radius: var(--a4-radius-s, 9px); background: var(--a4-line, #e3e6eb); color: var(--a4-muted, #5d6573); font-size: .9em; }
#exp-audit-recent .au-chain.intact { background: #e1f1e2; color: #2e7d32; }
#exp-audit-recent .au-chain.repaired { background: var(--a4-orange-soft, #fde7d9); color: var(--a4-orange-dark, #b84a0e); }
#exp-audit-recent .au-chain.broken { background: #fbe3e6; color: #b00020; font-weight: bold; }
#exp-audit-recent .au-result { display: inline-block; padding: .1em .5em; border-radius: var(--a4-radius-s, 9px); font-size: .8em; font-weight: bold; text-transform: uppercase; white-space: nowrap; background: var(--a4-line, #e3e6eb); color: var(--a4-muted, #5d6573); }
#exp-audit-recent .au-result.success { background: #e1f1e2; color: #2e7d32; }
#exp-audit-recent .au-result.refused { background: var(--a4-orange-soft, #fde7d9); color: var(--a4-orange-dark, #b84a0e); }
#exp-audit-recent .au-result.failed { background: #fbe3e6; color: #b00020; }
#exp-audit-recent .au-table { width: 100%; overflow-x: auto; }
#exp-audit-recent td { vertical-align: top; overflow-wrap: anywhere; }
#exp-audit-recent td.au-time, #exp-audit-recent td.au-name { white-space: nowrap; }
#exp-audit-recent code { font-size: .85em; }
#exp-audit-recent .au-scope { margin: 0 0 .6em; }
#exp-audit-recent .au-scope .current { font-weight: bold; }
#exp-audit-recent .au-empty { padding: 1.2em; text-align: center; color: var(--a4-muted, #5d6573); }
#exp-audit-recent .au-note { color: var(--a4-muted, #5d6573); font-size: .9em; }
{/literal}
</style>

<div class="context-block" id="exp-audit-recent">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Audit: recent events'|i18n( 'design/admin/audit' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{if $audit_enabled|not}
<div class="message-warning"><h2>{'Audit is switched off ([AuditSettings] Audit=disabled): nothing new is recorded.'|i18n( 'design/admin/audit' )}</h2></div>
{/if}

<div class="block">
<p class="au-note">{'The latest %limit events of the audit channels in %dir, newest first. Times are local; each record is written in UTC.'|i18n( 'design/admin/audit',, hash( '%limit', $limit, '%dir', $log_dir ) )}</p>

<ul class="au-chains" aria-label="{'Hash chain per channel'|i18n( 'design/admin/audit' )|wash}">
{foreach $chains as $c}
    <li><span class="au-chain {$c.result|wash}" title="{if $c.first_break}{$c.first_break|wash}{/if}">{$c.channel|wash}: {$c.result|wash} ({'%n records'|i18n( 'design/admin/audit',, hash( '%n', $c.records ) )}{if $c.breaks}, {'%n breaks, first: %first'|i18n( 'design/admin/audit',, hash( '%n', $c.breaks, '%first', $c.first_break ) )|wash}{/if}{if $c.repairs}, {'%n repaired'|i18n( 'design/admin/audit',, hash( '%n', $c.repairs ) )}{/if})</span></li>
{/foreach}
{if $chains|count|not}<li class="au-note">{'No audit files yet.'|i18n( 'design/admin/audit' )}</li>{/if}
</ul>
{if $verified_today_only}<p class="au-note">{'The live files are large: the chain state above covers the files of today. Run exp:audit verify for all of them.'|i18n( 'design/admin/audit' )}</p>{/if}

{if $channel_names|count}
<p class="au-scope">
    {if $channel}<a href={'audit/recent'|ezurl}>{'All channels'|i18n( 'design/admin/audit' )}</a>{else}<span class="current">{'All channels'|i18n( 'design/admin/audit' )}</span>{/if}
    {foreach $channel_names as $name} | {if eq( $channel, $name )}<span class="current">{$name|wash}</span>{else}<a href={concat( 'audit/recent/(channel)/', $name )|ezurl}>{$name|wash}</a>{/if}{/foreach}
</p>
{/if}
</div>

{if $events|count}
<div class="au-table">
<table class="list" cellspacing="0">
<tr>
    <th>{'Time'|i18n( 'design/admin/audit' )}</th>
    <th>{'Event'|i18n( 'design/admin/audit' )}</th>
    <th>{'Actor'|i18n( 'design/admin/audit' )}</th>
    <th>{'Object'|i18n( 'design/admin/audit' )}</th>
    <th>{'Result'|i18n( 'design/admin/audit' )}</th>
    <th>{'Request'|i18n( 'design/admin/audit' )}</th>
</tr>
{foreach $events as $e sequence array( 'bglight', 'bgdark' ) as $style}
<tr class="{$style}">
    <td class="au-time" title="{$e.time_utc|wash}">{$e.time|wash}</td>
    <td class="au-name"><code>{$e.name|wash}</code><br /><small>{$e.channel|wash} #{$e.seq}, {$e.severity|wash}</small></td>
    <td>{$e.actor|wash}</td>
    <td>{$e.object|wash}</td>
    <td><span class="au-result {$e.result|wash}">{$e.result|wash}</span>{if $e.reason}<br /><small>{$e.reason|wash}</small>{/if}</td>
    <td><code title="{$e.id|wash}">{$e.request|wash}</code>{if $e.engine}<br /><small>{$e.engine|wash}</small>{/if}</td>
</tr>
{/foreach}
</table>
</div>
{else}
<div class="block au-empty">
    <p>{'No audit events yet.'|i18n( 'design/admin/audit' )}</p>
</div>
{/if}

<p class="au-note">{'This page is request %id; opening it is itself recorded (system.audit.read). On the command line: exp:audit tail, exp:audit show <id>, exp:audit verify.'|i18n( 'design/admin/audit',, hash( '%id', $request_id ) )|wash}</p>

{* DESIGN: Content END *}</div></div></div>

</div>
