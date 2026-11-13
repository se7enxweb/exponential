{* design/admin "Forgot your password?": the card of the registration page (a context-block on the light card,
   .login-card in pagelayout.css) on the sign-in page's dark frame, instead of the standard template's bare form,
   whose black text stood on the dark background. The kernel (user/forgotpassword) reads UserEmail and
   GenerateButton; HashKey is the link from the mail. States: the form (also after an address that is not valid),
   "a mail has been sent" ($link), "a new password was sent" ($generated), and a link that has expired or is invalid
   ($wrong_key, $key_expired). Every state ends with a way back to the sign-in page. *}
<div class="context-block login-card">

<div class="box-header">
    <h1 class="context-title">{'Have you forgotten your password?'|i18n( 'design/standard/user/forgotpassword' )}</h1>
</div>

<div class="box-content">
{if first_set( $demo_mode_text, '' )|ne( '' )}
<div class="message-warning" role="status"><p>{$demo_mode_text|wash}</p></div>
{/if}

{if $link}
<div class="message-feedback" role="status"><p>{'If an account is registered with the email address %1, a mail has been sent to it. This email contains a link you need to click so that we can confirm that the correct user is getting the new password.'|i18n( 'design/standard/user/forgotpassword',, array( $email|wash ) )}</p></div>
{elseif $generated}
<div class="message-feedback" role="status"><p>{'Password was successfully generated and sent to: %1'|i18n( 'design/standard/user/forgotpassword',, array( $email|wash ) )}</p></div>
{elseif and( $wrong_key, first_set( $key_expired, false() ) )}
<div class="message-warning" role="alert">
    <h2>{'This link has expired.'|i18n( 'design/standard/user/forgotpassword' )}</h2>
    <p>{'For your security, a link to reset your password works only for %1 minutes and only once. Ask for a new link below.'|i18n( 'design/standard/user/forgotpassword',, array( first_set( $key_lifetime_minutes, 60 ) ) )}</p>
</div>
<p><a href={'/user/forgotpassword/'|ezurl}>{'Ask for a new link'|i18n( 'design/standard/user/forgotpassword' )}</a></p>
{elseif $wrong_key}
<div class="message-warning" role="alert"><h2>{'The key is invalid or has been used. '|i18n( 'design/standard/user/forgotpassword' )|trim}</h2></div>
<p><a href={'/user/forgotpassword/'|ezurl}>{'Ask for a new link'|i18n( 'design/standard/user/forgotpassword' )}</a></p>
{else}
{if $wrong_email}
<div class="message-warning" role="alert"><h2>{'Please enter a valid email address.'|i18n( 'design/standard/user/forgotpassword' )}</h2></div>
{/if}
<form name="forgotpassword" method="post" action={'/user/forgotpassword/'|ezurl} novalidate="novalidate">
<p>{'If you have forgotten your password we can generate a new one for you. All you need to do is to enter your email address and we will create a new password for you.'|i18n( 'design/standard/user/forgotpassword' )}</p>
<div class="block">
    <label for="email">{'Email'|i18n( 'design/standard/user/forgotpassword' )}:</label>
    <input class="box" type="email" name="UserEmail" id="email" value="{cond( $wrong_email, $wrong_email, '' )|wash}" autocomplete="email" required="required" autofocus="autofocus"{if $wrong_email} aria-invalid="true"{/if} />
</div>
<div class="controlbar">
<div class="block">
    <input class="defaultbutton" type="submit" name="GenerateButton" value="{'Generate new password'|i18n( 'design/standard/user/forgotpassword' )}" />
</div>
</div>
</form>
{/if}
<p class="login-card-back"><a href={'/user/login'|ezurl}>{'Sign in'|i18n( 'design/admin/user/login' )}</a></p>
</div>

</div>
