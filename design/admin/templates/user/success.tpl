{* design/admin: the page after a registration (user/success) on the card of the registration page (.login-card
   in pagelayout.css), so its text is not black on the sign-in page's dark frame. The state of the new account
   comes from the kernel as in design/standard/templates/user/success.tpl ($registration_state: verify_email,
   pending_approval, active, signed_in or error), with that template's texts; until 2026-11 this page said
   "Your account was successfully created" to anyone who opened it. *}
{def $su_state = 'active'
     $su_user = first_set( $registered_user, false() )
     $su_title = 'Your account is ready'|i18n( 'design/standard/user/success' )
     $su_texts = array( 'Your account has been created. You can sign in now.'|i18n( 'design/standard/user/success' ) )
     $su_role = 'status'}
{if is_set( $registration_state )}{set $su_state = $registration_state}{elseif first_set( $verify_user_email, false() )}{set $su_state = 'verify_email'}{/if}
{switch match=$su_state}
{case match='verify_email'}
    {set $su_title = 'Check your e-mail'|i18n( 'design/standard/user/success' )
         $su_texts = array( 'Your account has been created. One step is left: confirm your e-mail address.'|i18n( 'design/standard/user/success' ) )}
    {if $su_user}
    {set $su_texts = $su_texts|append( 'We sent a message to %email. Open the link in it to activate your account; then you can sign in.'|i18n( 'design/standard/user/success',, hash( '%email', $su_user.email ) ) )}
    {else}
    {set $su_texts = $su_texts|append( 'We sent a message to the e-mail address you entered. Open the link in it to activate your account; then you can sign in.'|i18n( 'design/standard/user/success' ) )}
    {/if}
    {set $su_texts = $su_texts|append( 'No message after a few minutes? Look in your spam folder.'|i18n( 'design/standard/user/success' ) )}
{/case}
{case match='pending_approval'}
    {set $su_title = 'Your account is waiting for approval'|i18n( 'design/standard/user/success' )
         $su_texts = array( 'Your account has been created. An administrator checks new accounts before they can be used.'|i18n( 'design/standard/user/success' ),
                            'You will get an e-mail as soon as your account is approved; then you can sign in.'|i18n( 'design/standard/user/success' ) )}
{/case}
{case match='signed_in'}
    {set $su_texts = array( 'Your account is ready and you are signed in.'|i18n( 'design/standard/user/success' ) )}
{/case}
{case match='error'}
    {set $su_title = 'We could not find your account'|i18n( 'design/standard/user/success' )
         $su_role = 'alert'
         $su_texts = array( 'The account of this registration does not exist (any more).'|i18n( 'design/standard/user/success' ),
                            'Please register again, or contact us if the problem stays.'|i18n( 'design/standard/user/success' ) )}
{/case}
{case}{/case}
{/switch}
<div class="context-block login-card">
<div class="box-header">
    <h1 class="context-title">{$su_title|wash}</h1>
</div>
<div class="box-content">
<div class="{if $su_state|eq( 'error' )}message-warning{else}message-feedback{/if}" role="{$su_role}" data-exp-result-state="{$su_state|wash}">
    {foreach $su_texts as $su_text}<p>{$su_text|wash}</p>{/foreach}
</div>
</div>
<div class="controlbar">
<div class="block">
{if and( $su_state|eq( 'error' ), fetch( 'user', 'can_open', hash( 'uri', 'user/register' ) ) )}
<form action={'/user/register'|ezurl} method="get"><input class="defaultbutton" type="submit" value="{'Register'|i18n( 'design/standard/user/success' )}" /></form>
{elseif $su_state|eq( 'signed_in' )}
<form action={'/'|ezurl} method="get"><input class="defaultbutton" type="submit" value="{'OK'|i18n( 'design/admin/user' )}" /></form>
{else}
<form action={'/user/login'|ezurl} method="get"><input class="defaultbutton" type="submit" value="{'Sign in'|i18n( 'design/standard/user/success' )}" /></form>
{/if}
</div>
</div>
</div>
{undef $su_state $su_user $su_title $su_texts $su_role}
