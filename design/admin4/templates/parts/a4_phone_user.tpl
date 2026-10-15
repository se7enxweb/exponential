{* The phone menu's row of user actions (page_topmenu.tpl, below 576px only): the signed-in user's name, leading to the
   user's own object as the name in the header's user menu does, then the links of that menu and logout, as a
   wrapping row of buttons a thumb can hit (44px high). It stands for the header's user menu and logout button, which
   admin4.css takes out of the phone's header row. The conditions are those of parts/a4_header_user.tpl and are kept
   in step with it: on edit and browse pages the links lead nowhere (logout on edit pages only), "Change information"
   needs edit access to the user's object, "Change password" needs user/password. The header's cache blocks are keyed
   by the user's object id (pagelayout.tpl): the name and the links are the user's own. *}
{if $current_user.is_logged_in}
{def $a4_phone_inactive = or( $ui_context_edit, eq( $ui_context, 'browse' ) )
     $a4_phone_node = $current_user.contentobject.main_node
     $a4_phone_href = false()}
{if and( $a4_phone_inactive|not, $a4_phone_node )}
    {if $a4_phone_node.can_read}{set $a4_phone_href = cond( $a4_phone_node.url_alias, $a4_phone_node.url_alias, concat( 'content/view/full/', $a4_phone_node.node_id ) )}{/if}
{/if}
<li class="a4-phone-only a4-phone-user">
    <p class="a4-phone-user-name" id="a4-phone-user-name">
    {if $a4_phone_href}
        <a href={$a4_phone_href|ezurl} title="{'Open your user object'|i18n( 'design/admin/pagelayout' )|wash}"><span class="a4-icon a4-bi-person-circle" aria-hidden="true"></span><span class="a4-phone-user-display">{$current_user.contentobject.name|wash}</span> <span class="a4-phone-user-login" translate="no">{$current_user.login|wash}</span></a>
    {else}
        <span class="a4-icon a4-bi-person-circle" aria-hidden="true"></span><span class="a4-phone-user-display">{$current_user.contentobject.name|wash}</span> <span class="a4-phone-user-login" translate="no">{$current_user.login|wash}</span>
    {/if}
    </p>
    <ul class="a4-phone-user-links" aria-labelledby="a4-phone-user-name">
    {if $a4_phone_inactive}
        <li><span class="disabled" aria-disabled="true"><span class="a4-icon a4-bi-pencil-square" aria-hidden="true"></span>{'Change user info'|i18n( 'design/admin/pagelayout' )|wash}</span></li>
        <li><span class="disabled" aria-disabled="true"><span class="a4-icon a4-bi-key" aria-hidden="true"></span>{'Change password'|i18n( 'design/admin/pagelayout' )|wash}</span></li>
    {else}
        {if $current_user.contentobject.can_edit}
        <li><a href={concat( '/content/edit/', $current_user.contentobject_id, '/' )|ezurl} title="{'Change name, email, password, etc.'|i18n( 'design/admin/pagelayout' )|wash}"><span class="a4-icon a4-bi-pencil-square" aria-hidden="true"></span>{'Change information'|i18n( 'design/admin/pagelayout' )|wash}</a></li>
        {/if}
        {if fetch( 'user', 'can_open', hash( 'uri', 'user/password' ) )}
        <li><a href={'/user/password/'|ezurl}><span class="a4-icon a4-bi-key" aria-hidden="true"></span>{'Change password'|i18n( 'design/admin/pagelayout' )|wash}</a></li>
        {/if}
        <li><a href={'/mailpreferences/settings'|ezurl}><span class="a4-icon a4-bi-envelope" aria-hidden="true"></span>{'My e-mail preferences'|i18n( 'design/admin/pagelayout' )|wash}</a></li>
    {/if}
    {* logout leads nowhere on edit pages only, as the header's logout button *}
    {if $ui_context_edit}
        <li><span class="disabled" aria-disabled="true"><span class="a4-icon a4-bi-box-arrow-right" aria-hidden="true"></span>{'Logout'|i18n( 'design/admin/pagelayout' )|wash}</span></li>
    {else}
        <li><a href={'/user/logout'|ezurl} class="a4-phone-logout"><span class="a4-icon a4-bi-box-arrow-right" aria-hidden="true"></span>{'Logout'|i18n( 'design/admin/pagelayout' )|wash}</a></li>
    {/if}
    </ul>
</li>
{undef $a4_phone_inactive $a4_phone_node $a4_phone_href}
{/if}
