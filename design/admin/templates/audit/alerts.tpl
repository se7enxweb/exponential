{* audit/alerts: the alerts that fired (system.audit.alert) and the rules in use. Read-only. Variables: alerts
   (event rows plus rule, group, count, window, message, first, last), total, offset, page_size, rules (name, class,
   event, threshold, window, group_by, severity, sinks, problem, fired_url), alerts_enabled, system_allowed,
   can_manage. *}
{include uri='design:audit/style.tpl'}
<div class="context-block au-view" id="exp-audit-alerts">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Audit alerts'|i18n( 'design/admin/audit' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{if $alerts_enabled|not}
<div class="message-warning"><h2>{'Alert rules are switched off ([AuditAlertSettings] Alerts=disabled).'|i18n( 'design/admin/audit' )}</h2></div>
{/if}
{if $system_allowed|not}
<p class="au-note">{'Alerts are records of the system channel, which your access does not include.'|i18n( 'design/admin/audit' )}</p>
{/if}

<h2 class="au-h">{'Fired alerts'|i18n( 'design/admin/audit' )} ({$total})</h2>
{if $alerts|count}
<div class="au-table">
<table class="list" cellspacing="0">
<tr>
    <th>{'Time'|i18n( 'design/admin/audit' )}</th>
    <th>{'Rule'|i18n( 'design/admin/audit' )}</th>
    <th>{'Group'|i18n( 'design/admin/audit' )}</th>
    <th>{'Count'|i18n( 'design/admin/audit' )}</th>
    <th>{'Severity'|i18n( 'design/admin/audit' )}</th>
    <th>{'Events'|i18n( 'design/admin/audit' )}</th>
</tr>
{foreach $alerts as $a sequence array( 'bglight', 'bgdark' ) as $style}
<tr class="{$style}">
    <td class="au-time"><a href={concat( 'audit/event/', $a.id )|ezurl}>{$a.time|wash}</a></td>
    <td><code>{$a.rule|wash}</code>{if $a.message}<br /><span class="au-sub">{$a.message|wash}</span>{/if}</td>
    <td>{$a.group|wash}</td>
    <td>{$a.count}{if $a.window} <span class="au-sub">/ {$a.window} s</span>{/if}</td>
    <td><span class="au-result {if array( 'critical', 'alert', 'emergency' )|contains( $a.severity )}failed{else}refused{/if}">{$a.severity|wash}</span></td>
    <td>{if $a.first}<a href={concat( 'audit/event/', $a.first )|ezurl}>{'first'|i18n( 'design/admin/audit' )}</a>{/if}{if $a.last} &middot; <a href={concat( 'audit/event/', $a.last )|ezurl}>{'last'|i18n( 'design/admin/audit' )}</a>{/if}</td>
</tr>
{/foreach}
</table>
</div>
{else}
<div class="block au-empty"><p>{'No alert has fired.'|i18n( 'design/admin/audit' )}</p></div>
{/if}

<h2 class="au-h">{'Rules'|i18n( 'design/admin/audit' )}</h2>
<div class="au-table">
<table class="list" cellspacing="0">
<tr>
    <th>{'Rule'|i18n( 'design/admin/audit' )}</th>
    <th>{'Kind'|i18n( 'design/admin/audit' )}</th>
    <th>{'Event'|i18n( 'design/admin/audit' )}</th>
    <th>{'Threshold'|i18n( 'design/admin/audit' )}</th>
    <th>{'Severity'|i18n( 'design/admin/audit' )}</th>
    <th>{'Sinks'|i18n( 'design/admin/audit' )}</th>
    <th>{'State'|i18n( 'design/admin/audit' )}</th>
</tr>
{foreach $rules as $r sequence array( 'bglight', 'bgdark' ) as $style}
<tr class="{$style}">
    <td><a href={$r.fired_url|ezurl}><code>{$r.name|wash}</code></a></td>
    <td>{$r.class|wash}</td>
    <td><code>{$r.event|wash}</code></td>
    <td>{if $r.threshold}{$r.threshold|wash} {'in'|i18n( 'design/admin/audit' )} {$r.window|wash} s{if $r.group_by}, {'per'|i18n( 'design/admin/audit' )} {$r.group_by|wash}{/if}{else}&ndash;{/if}</td>
    <td>{$r.severity|wash}</td>
    <td>{$r.sinks|wash}</td>
    <td>{if $r.problem}<span class="au-result failed">{'problem'|i18n( 'design/admin/audit' )}</span> <span class="au-sub">{$r.problem|wash}</span>{else}<span class="au-result success">{'in use'|i18n( 'design/admin/audit' )}</span>{/if}</td>
</tr>
{/foreach}
</table>
</div>
<p class="au-note">{'Rules are set in audit.ini ([AuditAlertSettings] Rules[] and the [AlertRule_*] blocks); write them with exp:ini.'|i18n( 'design/admin/audit' )}</p>

{* DESIGN: Content END *}</div></div></div>
</div>
