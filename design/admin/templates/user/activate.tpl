{* design/admin: the account activation (user/activate, the link of the registration mail) on the card of the
   registration page (.login-card in pagelayout.css), so its text is not black on the sign-in page's dark frame;
   the next step is signing in. *}
{def $ac_done = or( $account_activated, $already_active )}
<div class="context-block login-card">
<div class="box-header">
    <h1 class="context-title">{'Activate account'|i18n( 'design/admin/user' )}</h1>
</div>
<div class="box-content">
<div class="{if $ac_done}message-feedback{else}message-warning{/if}" role="{if $ac_done}status{else}alert{/if}">
<p>
{if $account_activated}
    {if $is_pending}
        {'Your email address has been confirmed. An administrator needs to approve your sign up request, before your login becomes valid.'|i18n( 'design/standard/user' )}
    {else}
        {'Your account is now activated.'|i18n( 'design/standard/user' )}
    {/if}
{elseif $already_active}
    {'Your account is already active.'|i18n( 'design/standard/user' )}
{else}
    {'Sorry, the key submitted was not a valid key. Account was not activated.'|i18n( 'design/standard/user' )}
{/if}
</p>
</div>
</div>
<div class="controlbar">
<div class="block">
<form action={'/user/login'|ezurl} method="get"><input class="defaultbutton" type="submit" value="{'Sign in'|i18n( 'design/admin/user/login' )}" /></form>
</div>
</div>
</div>
{undef $ac_done}
