<div class="a4-page" id="exp-notify-removed">
    <div class="a4-notification-head"><div><h1>{'Notifications'|i18n( 'design/admin/notification/addingresult' )}</h1></div></div>
{let node=fetch( content, node, hash( node_id, $node_id) )}
    <div class="a4-notice {if $removed}is-ok{else}is-info{/if}" role="status"><p>
{if $removed}
        {'You no longer get notifications for node <%node_name>.'|i18n( 'design/admin/notification/addingresult',, hash( '%node_name', $node.name ) )|wash}
{else}
        {'You did not follow node <%node_name>.'|i18n( 'design/admin/notification/addingresult',, hash( '%node_name', $node.name ) )|wash}
{/if}
    </p></div>
{/let}
    <div class="a4-actions">
        <a class="a4-btn a4-btn-primary" href={first_set( $redirect_uri, $redirect_url, '/' )|ezurl}>{'OK'|i18n( 'design/admin/notification/addingresult' )}</a>
        <a class="a4-btn" href={'notification/settings'|ezurl}>{'My notification settings'|i18n( 'design/admin/notification/addingresult' )}</a>
    </div>
</div>
