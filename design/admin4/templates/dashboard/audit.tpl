{* The dashboard block "audit" (dashboard.ini [DashboardBlock_audit], shown only with audit/read): the latest
   events of notice severity or worse in the access and system channels, the alerts of the last day, and for
   audit/manage the "audit is off" and "chain broken" warnings. The fetches return nothing to a user without the
   policy or outside its Channel limitation. Drawn with the administration's shared components (components.css):
   no CSS in the template. admin4 draws the events as a timeline (parts/audit_timeline.tpl). *}
{* The module name is a variable, so the fetches are not compiled into direct class calls: a server process
   started before the audit classes existed (a Velocity worker before its restart) gets an empty block, not an error. *}
{def $audit_module = 'audit'}
{def $audit_events = fetch( $audit_module, 'events', hash( 'channel', 'access,system', 'severity', 'notice', 'limit', first_set( $block.number_of_items, 10 ) ) )
     $audit_alerts = fetch( $audit_module, 'count', hash( 'name', 'system.audit.alert', 'from', currentdate()|sub( 86400 )|datetime( 'custom', '%Y-%m-%d' ) ) )
     $audit_manage = fetch( 'user', 'has_access_to', hash( 'module', 'audit', 'function', 'manage' ) )
     $audit_chains = cond( $audit_manage, fetch( $audit_module, 'chain_status', hash( 'verify', false() ) ), array() )
     $audit_broken = array()}
{foreach $audit_chains as $c}{if eq( $c.result, 'broken' )}{set $audit_broken = $audit_broken|append( $c )}{/if}{/foreach}
<div class="au-dash">
<h2>{'Audit: security events'|i18n( 'design/admin/audit' )}</h2>

{if and( $audit_manage, ezini( 'AuditSettings', 'Audit', 'audit.ini' )|ne( 'enabled' ) )}
<p class="a4-notice is-bad" role="alert">{'Audit is switched off: nothing new is recorded.'|i18n( 'design/admin/audit' )}</p>
{/if}
{if $audit_broken|count}
<p class="a4-notice is-bad" role="alert">{'Hash chain broken'|i18n( 'design/admin/audit' )}: {foreach $audit_broken as $c}{$c.channel|wash} ({$c.first_break|wash}){delimiter}, {/delimiter}{/foreach}
    <a href={'audit/archives'|ezurl}>{'Archives'|i18n( 'design/admin/audit' )}</a></p>
{/if}
{if $audit_alerts}
<p class="a4-notice is-warn"><a href={'audit/alerts'|ezurl}>{'%count alerts in the last 24 hours'|i18n( 'design/admin/audit',, hash( '%count', $audit_alerts ) )}</a></p>
{/if}

{if $audit_events|count}
{include uri='design:parts/audit_timeline.tpl' events=$audit_events}
{else}
<p>{'No security events of notice severity or worse.'|i18n( 'design/admin/audit' )}</p>
{/if}
<p class="a4-card-body"><a href={'audit/console/(severity)/notice'|ezurl}>{'All audit events'|i18n( 'design/admin/audit' )}</a></p>
</div>
{undef $audit_module $audit_events $audit_alerts $audit_manage $audit_chains $audit_broken}
