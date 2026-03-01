<div class="notification-settings">

    <form name="notification" method="post" action={"/notification/settings/"|ezurl}>

    <div class="attribute-header">
        <h1 class="long">{'Notification settings'|i18n( 'design/ezdemo/notification/settings' )}</h1>
    </div>

    {def $handlers=fetch( 'notification', 'handler_list' )}

        {foreach $handlers as $handler}
            {if eq( $handler.id_string, $handlers.ezsubtree.id_string )}
                {skip}
            {/if}
            {include handler=$handler uri=concat( 'design:notification/handler/', $handler.id_string, '/settings/edit.tpl' )}
        {/foreach}


    <input class="button" type="submit" name="Store" value="{'Apply changes'|i18n('design/ezdemo/notification/settings')}" />

    <br />

    {include handler=$handlers.ezsubtree view_parameters=$view_parameters uri=concat( 'design:notification/handler/', $handlers.ezsubtree.id_string, '/settings/edit.tpl' )}

    </form>

</div>
