{include uri='design:notification/parts/style.tpl'}
<div class="nf" id="exp-notify-removed">
    <div class="nf-head"><div><h1>{'Notifications'|i18n( 'design/admin/notification/addingresult' )}</h1></div></div>
{let node=fetch( content, node, hash( node_id, $node_id) )}
    <div class="nf-notice nf-notice-{if $removed}success{else}info{/if}" role="status"><p>
{if $removed}
        {'You no longer get notifications for node <%node_name>.'|i18n( 'design/admin/notification/addingresult',, hash( '%node_name', $node.name ) )|wash}
{else}
        {'You did not follow node <%node_name>.'|i18n( 'design/admin/notification/addingresult',, hash( '%node_name', $node.name ) )|wash}
{/if}
    </p></div>
{/let}
    <div class="nf-actions">
        <a class="nf-btn primary" href={first_set( $redirect_uri, $redirect_url, '/' )|ezurl}>{'OK'|i18n( 'design/admin/notification/addingresult' )}</a>
        <a class="nf-btn" href={'notification/settings'|ezurl}>{'My notification settings'|i18n( 'design/admin/notification/addingresult' )}</a>
    </div>
</div>
