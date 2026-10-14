{* Asks before a notification is added (or removed): opening /notification/addtonotification/<node> changes nothing; the buttons are a POST
   that carries the form token. Variables: node, node_id, already_exists, redirect_uri. *}
<div class="a4-page" id="exp-notify-add">
    <div class="a4-notification-head"><div>
        <h1>{'Add to my notifications'|i18n( 'design/admin/notification/addconfirm' )}</h1>
    </div></div>
    <form class="a4-card a4-stack" method="post" action={concat( 'notification/addtonotification/', $node_id )|ezurl}>
        <input type="hidden" name="RedirectURI" value="{first_set( $redirect_uri, '/' )|wash}" />
{if $already_exists}
        <h2 class="a4-h2">{'You already follow this item'|i18n( 'design/admin/notification/addconfirm' )}</h2>
        <p class="a4-help"><b>{$node.name|wash}</b> ({$node.class_name|wash}). {'You get an e-mail when something is published below it. You can stop that here.'|i18n( 'design/admin/notification/addconfirm' )}</p>
        <div class="a4-actions">
            <input class="a4-btn a4-btn-outline-danger" type="submit" name="ConfirmRemoveNotification" value="{'Stop notifications for this item'|i18n( 'design/admin/notification/addconfirm' )}" />
            <input class="a4-btn" type="submit" name="CancelNotification" value="{'Back'|i18n( 'design/admin/notification/addconfirm' )}" />
        </div>
{else}
        <h2 class="a4-h2">{'Notify me about updates'|i18n( 'design/admin/notification/addconfirm' )}</h2>
        <p class="a4-help"><b>{$node.name|wash}</b> ({$node.class_name|wash}). {'You get an e-mail when something new is published below this item, as long as you may read it. You can change this under My notification settings.'|i18n( 'design/admin/notification/addconfirm' )}</p>
        <div class="a4-actions">
            <input class="a4-btn a4-btn-primary" type="submit" name="ConfirmAddNotification" value="{'Notify me'|i18n( 'design/admin/notification/addconfirm' )}" />
            <input class="a4-btn" type="submit" name="CancelNotification" value="{'Cancel'|i18n( 'design/admin/notification/addconfirm' )}" />
        </div>
{/if}
    </form>
</div>
