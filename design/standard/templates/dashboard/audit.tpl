{* The dashboard block "audit" (dashboard.ini [DashboardBlock_audit], shown only with audit/read): the latest
   events of notice severity or worse in the access and system channels, the alerts of the last day, and for
   audit/manage the "audit is off" and "chain broken" warnings. The fetches return nothing to a user without the
   policy or outside its Channel limitation. *}
{* The module name is a variable, so the fetches are not compiled into direct class calls: a server process
   started before the audit classes existed (a Velocity worker before its restart) gets an empty block, not an error. *}
{def $audit_module = 'audit'}
{def $audit_events = fetch( $audit_module, 'events', hash( 'channel', 'access,system', 'severity', 'notice', 'limit', first_set( $block.number_of_items, 10 ) ) )
     $audit_alerts = fetch( $audit_module, 'count', hash( 'name', 'system.audit.alert', 'from', currentdate()|sub( 86400 )|datetime( 'custom', '%Y-%m-%d' ) ) )
     $audit_manage = fetch( 'user', 'has_access_to', hash( 'module', 'audit', 'function', 'manage' ) )
     $audit_chains = cond( $audit_manage, fetch( $audit_module, 'chain_status', hash( 'verify', false() ) ), array() )
     $audit_broken = array()}
{foreach $audit_chains as $c}{if eq( $c.result, 'broken' )}{set $audit_broken = $audit_broken|append( $c )}{/if}{/foreach}
<style type="text/css">
{literal}
.au-dash .au-result { display: inline-block; padding: .05em .45em; border-radius: 9px; font-size: .78em; font-weight: bold; text-transform: uppercase; white-space: nowrap; background: #e3e6eb; color: #3b4252; }
.au-dash .au-result.refused { background: #fde7d9; color: #8a3a0c; }
.au-dash .au-result.failed { background: #fbe3e6; color: #9b001c; }
.au-dash .au-result.success { background: #e1f1e2; color: #1e5e22; }
.au-dash td { vertical-align: top; overflow-wrap: anywhere; }
.au-dash td.au-when { white-space: nowrap; }
.au-dash .au-more { margin: .6em 0 0; }
.au-dash .au-warn { margin: 0 0 .6em; padding: .5em .7em; border-radius: 9px; background: #fbe3e6; color: #9b001c; }
.au-dash .au-note { margin: 0 0 .6em; padding: .5em .7em; border-radius: 9px; background: #fde7d9; color: #8a3a0c; }
{/literal}
</style>
<div class="au-dash">
<h2>{'Audit: security events'|i18n( 'design/admin/audit' )}</h2>

{if and( $audit_manage, ezini( 'AuditSettings', 'Audit', 'audit.ini' )|ne( 'enabled' ) )}
<p class="au-warn" role="alert">{'Audit is switched off: nothing new is recorded.'|i18n( 'design/admin/audit' )}</p>
{/if}
{if $audit_broken|count}
<p class="au-warn" role="alert">{'Hash chain broken'|i18n( 'design/admin/audit' )}: {foreach $audit_broken as $c}{$c.channel|wash} ({$c.first_break|wash}){delimiter}, {/delimiter}{/foreach}
    <a href={'audit/archives'|ezurl}>{'Archives'|i18n( 'design/admin/audit' )}</a></p>
{/if}
{if $audit_alerts}
<p class="au-note"><a href={'audit/alerts'|ezurl}>{'%count alerts in the last 24 hours'|i18n( 'design/admin/audit',, hash( '%count', $audit_alerts ) )}</a></p>
{/if}

{if $audit_events|count}
<table class="list" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <th>{'Time'|i18n( 'design/admin/audit' )}</th>
        <th>{'Event'|i18n( 'design/admin/audit' )}</th>
        <th>{'Actor'|i18n( 'design/admin/audit' )}</th>
        <th>{'Result'|i18n( 'design/admin/audit' )}</th>
    </tr>
    {foreach $audit_events as $e sequence array( 'bglight', 'bgdark' ) as $style}
    <tr class="{$style}">
        <td class="au-when" title="{$e.time_utc|wash}">{$e.time|wash}</td>
        <td><a href={concat( 'audit/event/', $e.id )|ezurl} title="{$e.name|wash}">{$e.label|wash}</a></td>
        <td>{$e.actor|wash}</td>
        <td><span class="au-result {$e.result|wash}">{$e.result|wash}</span></td>
    </tr>
    {/foreach}
</table>
{else}
<p>{'No security events of notice severity or worse.'|i18n( 'design/admin/audit' )}</p>
{/if}
<p class="au-more"><a href={'audit/console/(severity)/notice'|ezurl}>{'All audit events'|i18n( 'design/admin/audit' )}</a></p>
</div>
{undef $audit_module $audit_events $audit_alerts $audit_manage $audit_chains $audit_broken}
