{* The confirmation before sessions are removed (setup/session): all sessions, the sessions of the ticked users, or
   the ticked sessions of one user. Says who is signed out and what happens to the viewer's own session, then posts
   the same button again with ConfirmSessionRemoval. Cancel goes back to the list without changing anything.

   The same file is in design/admin and design/admin4. Guide: doc/guides/sessions.md *}
{include uri='design:setup/session_exp_style.tpl'}

{def $base_uri = concat( '/setup/session', cond( $user_id, concat( '/', $user_id ), '' ) )
     $self_listed = false()}
{foreach $confirm_users as $u}{if $u.is_self}{set $self_listed = true()}{/if}{/foreach}

<div class="context-block exp-sess">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Remove sessions'|i18n( 'design/admin/setup/session' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<form method="post" action={$base_uri|ezurl}>

{if eq( $confirm_type, 'all' )}
<div class="exp-feedback is-bad" role="alert">
    <h2 class="exp-h2">{'Remove all %count sessions?'|i18n( 'design/admin/setup/session',, hash( '%count', $confirm_session_count ) )}</h2>
    <p>{'Everybody who is signed in is signed out, you included: you will see the login page next. Anonymous visitors lose their baskets and the choices kept for their visit.'|i18n( 'design/admin/setup/session' )}</p>
    <p>{'This cannot be undone. To remove only what is no longer used, choose Remove timed out sessions instead.'|i18n( 'design/admin/setup/session' )}</p>
</div>
{else}
<div class="exp-feedback is-warn" role="alert">
    <h2 class="exp-h2">
    {if eq( $confirm_type, 'sessions' )}{'Remove %count sessions of this user?'|i18n( 'design/admin/setup/session',, hash( '%count', $confirm_session_count ) )}
    {elseif $confirm_users|count|eq( 1 )}{'Remove the sessions of this user?'|i18n( 'design/admin/setup/session' )}
    {else}{'Remove the sessions of %users users?'|i18n( 'design/admin/setup/session',, hash( '%users', $confirm_users|count ) )}{/if}
    </h2>
    <p>{'The browsers of these sessions are signed out and have to sign in again. Nothing else changes: the users, their content and their settings stay.'|i18n( 'design/admin/setup/session' )}</p>
    {if or( $self_listed, $confirm_kept_own )}
    <p><strong>{'Your own user is among them: its other sessions are removed, the one you are using now stays.'|i18n( 'design/admin/setup/session' )}</strong></p>
    {/if}
    {if $confirm_users}
    <ul class="exp-confirm-list">
    {foreach $confirm_users as $u}
        <li>{$u.name|wash} <code>{$u.login|wash}</code> &ndash;
            {if eq( $confirm_type, 'sessions' )}{'%count of its sessions'|i18n( 'design/admin/setup/session',, hash( '%count', $confirm_session_count ) )}
            {elseif $u.count|eq( 1 )}{'1 session'|i18n( 'design/admin/setup/session' )}
            {else}{'%count sessions'|i18n( 'design/admin/setup/session',, hash( '%count', $u.count ) )}{/if}
            {if $u.is_self} <span class="exp-badge is-info">{'You'|i18n( 'design/admin/setup/session' )}</span>{/if}
        </li>
    {/foreach}
    </ul>
    {/if}
</div>
{foreach $confirm_user_ids as $id}{if eq( $confirm_type, 'users' )}<input type="hidden" name="UserIDArray[]" value="{$id|wash}" />{/if}{/foreach}
{foreach $confirm_refs as $ref}<input type="hidden" name="SessionRefArray[]" value="{$ref|wash}" />{/foreach}
{/if}

<input type="hidden" name="ConfirmSessionRemoval" value="1" />
<div class="exp-bottombar">
    <div class="exp-actions">
    {if eq( $confirm_type, 'all' )}
        <button class="exp-btn exp-btn-danger" type="submit" name="RemoveAllSessionsButton" value="1">{'Remove all sessions'|i18n( 'design/admin/setup/session' )}</button>
    {else}
        <button class="exp-btn exp-btn-danger" type="submit" name="RemoveSelectedSessionsButton" value="1">{'Remove the sessions'|i18n( 'design/admin/setup/session' )}</button>
    {/if}
        <a class="exp-btn" href={$base_uri|ezurl}>{'Cancel'|i18n( 'design/admin/setup/session' )}</a>
    </div>
</div>

</form>

</div></div></div>
</div>
{undef $base_uri $self_listed}
