{* Asks before a notification is added or removed: opening the address changes nothing, the buttons are a POST
   that carries the form token. Variables: node, node_id, already_exists, redirect_uri. *}
<div class="maincontentheader">
<h1>{'Add to my notifications'|i18n( 'design/admin/notification/addconfirm' )}</h1>
</div>

<form method="post" action={concat( 'notification/addtonotification/', $node_id )|ezurl}>
<input type="hidden" name="RedirectURI" value="{first_set( $redirect_uri, '/' )|wash}" />
{if $already_exists}
<p>{'You already follow %node_name. You get an e-mail when something is published below it.'|i18n( 'design/admin/notification/addconfirm',, hash( '%node_name', $node.name ) )|wash}</p>
<div class="buttonblock">
    <input class="button" type="submit" name="ConfirmRemoveNotification" value="{'Stop notifications for this item'|i18n( 'design/admin/notification/addconfirm' )}" />
    <input class="button" type="submit" name="CancelNotification" value="{'Back'|i18n( 'design/admin/notification/addconfirm' )}" />
</div>
{else}
<p>{'Get an e-mail when something new is published below %node_name?'|i18n( 'design/admin/notification/addconfirm',, hash( '%node_name', $node.name ) )|wash}</p>
<div class="buttonblock">
    <input class="button" type="submit" name="ConfirmAddNotification" value="{'Notify me'|i18n( 'design/admin/notification/addconfirm' )}" />
    <input class="button" type="submit" name="CancelNotification" value="{'Cancel'|i18n( 'design/admin/notification/addconfirm' )}" />
</div>
{/if}
</form>
