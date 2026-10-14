{* The node view's "Audit" tab: the latest audit events about this node (fetch audit/events with the node as
   object), with a link to all of them in the console. Empty for a user without audit/read. admin4 draws the
   events as a timeline (parts/audit_timeline.tpl). *}
{def $audit_tab_module = 'audit'
     $audit_tab_events = fetch( $audit_tab_module, 'events', hash( 'object', hash( 'type', 'node', 'id', $node.node_id ), 'limit', 10 ) )}
{if fetch( $audit_tab_module, 'can_read', hash( 'channel', 'content' ) )}
{if $audit_tab_events|count}
{include uri='design:parts/audit_timeline.tpl' events=$audit_tab_events show_reason=true()}
{else}
<p>{'No audit events about this node.'|i18n( 'design/admin/audit' )}</p>
{/if}
<p><a href={concat( 'audit/console/(object)/node:', $node.node_id )|ezurl}>{'All audit events of this node'|i18n( 'design/admin/audit' )}</a></p>
{/if}
{undef $audit_tab_module $audit_tab_events}
