{* The node view's "Audit" tab: the latest audit events about this node (fetch audit/events with the node as
   object), with a link to all of them in the console. Empty for a user without audit/read. *}
{def $audit_tab_module = 'audit'
     $audit_tab_events = fetch( $audit_tab_module, 'events', hash( 'object', hash( 'type', 'node', 'id', $node.node_id ), 'limit', 10 ) )}
{if fetch( $audit_tab_module, 'can_read', hash( 'channel', 'content' ) )}
{if $audit_tab_events|count}
<table class="list" cellspacing="0">
<tr>
    <th>{'Time'|i18n( 'design/admin/audit' )}</th>
    <th>{'Event'|i18n( 'design/admin/audit' )}</th>
    <th>{'Actor'|i18n( 'design/admin/audit' )}</th>
    <th>{'Result'|i18n( 'design/admin/audit' )}</th>
</tr>
{foreach $audit_tab_events as $e sequence array( 'bglight', 'bgdark' ) as $style}
<tr class="{$style}">
    <td title="{$e.time_utc|wash}">{$e.time|wash}</td>
    <td><a href={concat( 'audit/event/', $e.id )|ezurl} title="{$e.name|wash}">{$e.label|wash}</a></td>
    <td>{$e.actor|wash}</td>
    <td>{$e.result|wash}{if $e.reason} ({$e.reason|wash}){/if}</td>
</tr>
{/foreach}
</table>
{else}
<p>{'No audit events about this node.'|i18n( 'design/admin/audit' )}</p>
{/if}
<p><a href={concat( 'audit/console/(object)/node:', $node.node_id )|ezurl}>{'All audit events of this node'|i18n( 'design/admin/audit' )}</a></p>
{/if}
{undef $audit_tab_module $audit_tab_events}
