{* The header's user controls (page_topmenu.tpl): the user menu and the logout button, for signed-in users only.
   The user menu is a disclosure (a button with aria-expanded and aria-controls, a list of links), opened by
   ezadmin_menubar.js; it shows the user's name and login and the links of the right column's "Current user" box,
   which keeps them for a browser without scripts; as there, they lead nowhere on edit and browse pages. Logout is its own icon button beside it, one click away, named
   with the login ("Logout (admin)") in aria-label and title. On edit pages both stay in place but lead nowhere, as
   the menu tabs do there: leaving the edit form goes through its own buttons. The header's cache blocks are keyed by
   the user's object id (pagelayout.tpl): the name and the links are the user's own. *}
{if $current_user.is_logged_in}
{def $a4_user_login = $current_user.login
     $a4_user_name = $current_user.contentobject.name
     $a4_user_label = 'User menu (%name)'|i18n( 'design/admin/pagelayout',, hash( '%name', $current_user.contentobject.name ) )
     $a4_logout_label = 'Logout (%login)'|i18n( 'design/admin/pagelayout',, hash( '%login', $current_user.login ) )}
<div id="header-user" class="header-user">
    <div class="a4-user-menu-wrap">
        <button type="button" id="a4-user-toggle" class="a4-header-btn a4-user-toggle" aria-expanded="false" aria-controls="a4-user-menu"
                title="{$a4_user_label|wash}">
            <span class="a4-icon a4-bi-person-circle" aria-hidden="true"></span>
            <span class="a4-sr-only">{$a4_user_label|wash}</span>
        </button>
        <div id="a4-user-menu" class="a4-user-menu" hidden>
            <p class="a4-user-menu-name">
                <span class="a4-user-menu-display">{$a4_user_name|wash}</span>
                <span class="a4-user-menu-login" translate="no">{$a4_user_login|wash}</span>
            </p>
            <ul class="a4-user-menu-links">
            {if or( $ui_context_edit, eq( $ui_context, 'browse' ) )}
                <li><span class="disabled" aria-disabled="true">{'Change user info'|i18n( 'design/admin/pagelayout' )|wash}</span></li>
                <li><span class="disabled" aria-disabled="true">{'Change password'|i18n( 'design/admin/pagelayout' )|wash}</span></li>
            {else}
                {if $current_user.contentobject.can_edit}
                <li><a href={concat( '/content/edit/', $current_user.contentobject_id, '/' )|ezurl} title="{'Change name, email, password, etc.'|i18n( 'design/admin/pagelayout' )|wash}">{'Change information'|i18n( 'design/admin/pagelayout' )|wash}</a></li>
                {/if}
                {if fetch( 'user', 'can_open', hash( 'uri', 'user/password' ) )}
                <li><a href={'/user/password/'|ezurl}>{'Change password'|i18n( 'design/admin/pagelayout' )|wash}</a></li>
                {/if}
                <li><a href={'/mailpreferences/settings'|ezurl}>{'My e-mail preferences'|i18n( 'design/admin/pagelayout' )|wash}</a></li>
            {/if}
            </ul>
        </div>
    </div>
    {if $ui_context_edit}
    <a class="a4-header-btn a4-logout disabled" role="link" aria-disabled="true" aria-label="{$a4_logout_label|wash}" title="{$a4_logout_label|wash}"><span class="a4-icon a4-bi-box-arrow-right" aria-hidden="true"></span></a>
    {else}
    <a href={'/user/logout'|ezurl} id="header-usermenu-logout" class="a4-header-btn a4-logout" aria-label="{$a4_logout_label|wash}" title="{$a4_logout_label|wash}"><span class="a4-icon a4-bi-box-arrow-right" aria-hidden="true"></span></a>
    {/if}
</div>
{undef $a4_user_login $a4_user_name $a4_user_label $a4_logout_label}
{/if}
