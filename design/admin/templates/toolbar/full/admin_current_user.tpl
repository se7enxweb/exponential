<div id="currentuser">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">

<h4>{'Current user'|i18n( 'design/admin/pagelayout' )}</h4>

{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">

{* the Help center (exphelp/dashboard) for users with exphelp/read while the help is switched on *}
{def $help_center = and( ezini( 'HelpSettings', 'Enabled', 'exphelp.ini' )|eq( 'true' ), fetch( 'user', 'has_access_to', hash( 'module', 'exphelp', 'function', 'read' ) ) )}
{if and( ne( $ui_context, 'edit' ), ne( $ui_context, 'browse' ) )}
    {def $user_node = $current_user.contentobject.main_node
         $current_user_link = $user_node.url_alias|ezurl}
    {if $user_node.can_read}
        <p><img src={'current-user.gif'|ezimage} height="22" width="22" alt="" class="a4-middle" />&nbsp;<a class="a4-current-user-name" dir="auto" href={$current_user_link}>{$current_user.contentobject.name|wash}</a></p>
    {else}
        <p><img src={'current-user-disabled.gif'|ezimage} height="22" width="22" alt="" class="a4-middle" /> <bdi>{$current_user.contentobject.name|wash}</bdi></p>
    {/if}
    {undef $current_user_link $user_node}
    <ul>
    <li><div>
    {if $current_user.contentobject.can_edit}
        <a href={concat( '/content/edit/',  $current_user.contentobject_id, '/' )|ezurl} title="{'Change name, email, password, etc.'|i18n( 'design/admin/pagelayout' )}">{'Change information'|i18n( 'design/admin/pagelayout' )}</a>
    {else}
        <span class="disabled">{'Change user info'|i18n( 'design/admin/pagelayout' )}</span>
    {/if}</div></li>
    {if fetch( 'user', 'can_open', hash( 'uri', 'user/password' ) )}
    <li><div><a href={'/user/password/'|ezurl} title="{'Change password for <%username>.'|i18n( 'design/admin/pagelayout',, hash( '%username', $current_user.contentobject.name ) )|wash}">{'Change password'|i18n( 'design/admin/pagelayout' )}</a></div></li>
    {/if}
    <li><div><a href={'/mailpreferences/settings'|ezurl} title="{'Choose which e-mail you get.'|i18n( 'design/admin/pagelayout' )}">{'My e-mail preferences'|i18n( 'design/admin/pagelayout' )}</a></div></li>
    {if $help_center}
    <li><div><a href={'/exphelp/dashboard'|ezurl}>{'Help center'|i18n( 'design/admin/pagelayout' )}</a></div></li>
    {/if}
    </ul>
{else}
    <p><img src={'current-user-disabled.gif'|ezimage} height="22" width="22" alt="" class="a4-middle" /> <bdi>{$current_user.contentobject.name|wash}</bdi></p>
    <ul>
    <li><div><span class="disabled">{'Change user info'|i18n( 'design/admin/pagelayout' )}</span></div></li>
    <li><div><span class="disabled">{'Change password'|i18n( 'design/admin/pagelayout' )}</span></div></li>
    {if $help_center}
    <li><div><span class="disabled">{'Help center'|i18n( 'design/admin/pagelayout' )}</span></div></li>
    {/if}
    </ul>
{/if}
{undef $help_center}
{* DESIGN: Content END *}</div></div></div>

</div>