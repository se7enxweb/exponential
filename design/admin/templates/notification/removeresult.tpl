{let node=fetch( content, node, hash( node_id, $node_id) )}
<div class="maincontentheader">
<h1>{'Notifications'|i18n( 'design/admin/notification/removeresult' )}</h1>
</div>
<p>
{if $removed}
    {'You no longer get notifications for node <%node_name>.'|i18n( 'design/admin/notification/removeresult',, hash( '%node_name', $node.name ) )|wash}
{else}
    {'You did not follow node <%node_name>.'|i18n( 'design/admin/notification/removeresult',, hash( '%node_name', $node.name ) )|wash}
{/if}
</p>
{/let}

<div class="buttonblock">
<form method="post" action={first_set( $redirect_uri, $redirect_url, '/' )|ezurl}>
    <input class="button" type="submit" name="OK" value="{'OK'|i18n( 'design/admin/notification/removeresult' )}" />
</form>
</div>
