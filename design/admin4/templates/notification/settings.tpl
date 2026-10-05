{* My notification settings (admin4): an overview of what I follow and how it reaches me, then one card per handler.
   Variables from notification/settings: notice, confirm_remove, subscriptions, subscription_total, subscription_all_total,
   subscription_limit, subscription_classes, filter_query, filter_class, view_parameters, can_administrate.
   The old address and the old form fields keep working: /notification/settings, /notification/settings/(offset)/10 *}
{def $handlers = fetch( 'notification', 'handler_list' )
     $digest = false()
     $own = array( 'ezsubtree', 'ezgeneraldigest', 'ezcollaborationnotification' )}
{if is_set( $handlers.ezgeneraldigest )}{set $digest = $handlers.ezgeneraldigest.settings}{/if}
{include uri='design:notification/parts/style.tpl'}

<div class="nf" id="exp-notify">

    <div class="nf-head">
        <div>
            <h1>{'My notification settings'|i18n( 'design/admin/notification/settings' )}</h1>
            <p>{'You are told by e-mail when content you follow changes, and about the collaboration items you take part in. Choose here what you follow and how the messages come.'|i18n( 'design/admin/notification/settings' )}</p>
        </div>
{if $can_administrate}
        <a class="nf-btn" href={'notification/status'|ezurl}>{'Notification status'|i18n( 'design/admin/notification/settings' )}</a>
{/if}
    </div>

    {include uri='design:notification/parts/notice.tpl' notice=first_set( $notice, false() )}

    {include uri='design:mailpreferences/parts/account_link.tpl' context='notification'}

    <ul class="nf-stats">
        <li><a class="nf-stat" href="#nf-follow"><strong>{$subscription_all_total}</strong><span>{'Items I follow'|i18n( 'design/admin/notification/settings' )}</span></a></li>
        <li><a class="nf-stat" href="#nf-digest"><strong>
{if not( $digest )}{'At once'|i18n( 'design/admin/notification/settings' )}
{elseif and( $digest.receive_digest, $digest.digest_type|eq( 3 ) )}{'Daily'|i18n( 'design/admin/notification/settings' )}
{elseif and( $digest.receive_digest, $digest.digest_type|eq( 1 ) )}{'Weekly'|i18n( 'design/admin/notification/settings' )}
{elseif and( $digest.receive_digest, $digest.digest_type|eq( 2 ) )}{'Monthly'|i18n( 'design/admin/notification/settings' )}
{else}{'At once'|i18n( 'design/admin/notification/settings' )}{/if}</strong><span>{'How the messages come'|i18n( 'design/admin/notification/settings' )}</span></a></li>
        <li><div class="nf-stat"><strong>{$user.email|wash}</strong><span>{'Messages go to this address'|i18n( 'design/admin/notification/settings' )}</span></div></li>
    </ul>

    {include name=subtree uri=concat( 'design:notification/handler/', $handlers.ezsubtree.id_string, '/settings/edit.tpl' ) handler=$handlers.ezsubtree
             confirm_remove=$confirm_remove subscriptions=$subscriptions subscription_total=$subscription_total
             subscription_all_total=$subscription_all_total subscription_limit=$subscription_limit
             subscription_classes=$subscription_classes filter_query=$filter_query filter_class=$filter_class
             view_parameters=$view_parameters}

    <div class="nf-layout two">
{if is_set( $handlers.ezgeneraldigest )}
        {include name=digest uri=concat( 'design:notification/handler/', $handlers.ezgeneraldigest.id_string, '/settings/edit.tpl' ) handler=$handlers.ezgeneraldigest}
{/if}
{if is_set( $handlers.ezcollaborationnotification )}
        {include name=collab uri=concat( 'design:notification/handler/', $handlers.ezcollaborationnotification.id_string, '/settings/edit.tpl' ) handler=$handlers.ezcollaborationnotification}
{/if}
{foreach $handlers as $key => $other}
    {if $own|contains( $key )|not}
        <form class="nf-card" method="post" action={'notification/settings'|ezurl}>
            <h2>{$other.name|wash}</h2>
            {include name=other uri=concat( 'design:notification/handler/', $other.id_string, '/settings/edit.tpl' ) handler=$other}
            <div class="nf-actions"><input class="nf-btn primary" type="submit" name="Store" value="{'Save'|i18n( 'design/admin/notification/settings' )}" /></div>
        </form>
    {/if}
{/foreach}
    </div>

</div>
