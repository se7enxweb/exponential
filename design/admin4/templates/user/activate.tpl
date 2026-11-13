{* admin4: the account activation (user/activate, the link of the registration mail) in the sign-in page's frame
   (loginpagelayout.tpl, user/login.tpl): a title, the result in a notice and the sign-in button as the next step. *}
{def $ac_done = or( $account_activated, $already_active )}
<h1 class="a4-login-title">{'Activate account'|i18n( 'design/admin/user' )}</h1>
<div class="a4-notice {if $ac_done}is-ok{else}is-bad{/if}" role="{if $ac_done}status{else}alert{/if}">
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
<a class="a4-login-submit" href={'/user/login'|ezurl}>{'Sign in'|i18n( 'design/admin/user/login' )}</a>
{undef $ac_done}
