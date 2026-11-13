{* admin4 "Forgot your password?": the sign-in page's frame and form (loginpagelayout.tpl, user/login.tpl), so the
   page reads as the next step of signing in, not as a bare module form. The kernel (user/forgotpassword) reads the
   same fields as the standard template: UserEmail and GenerateButton; HashKey is the link from the mail.
   States: the form (also after an address that is not valid), "a mail has been sent" ($link), "a new password was
   sent" ($generated), and a link that has expired or is invalid ($wrong_key, $key_expired).
   Every state ends with a way back to the sign-in page. Double sends are held back by admin4.js (form.a4-login-form). *}
{def $a4_forgot_email = cond( $wrong_email, $wrong_email, '' )}

<h1 class="a4-login-title">{'Have you forgotten your password?'|i18n( 'design/standard/user/forgotpassword' )}</h1>

{if first_set( $demo_mode_text, '' )|ne( '' )}
<div class="a4-login-alert message-warning" role="status"><p>{$demo_mode_text|wash}</p></div>
{/if}

{if $link}
<div class="a4-notice is-ok" role="status">
    <p>{'If an account is registered with the email address %1, a mail has been sent to it. This email contains a link you need to click so that we can confirm that the correct user is getting the new password.'|i18n( 'design/standard/user/forgotpassword',, array( $email|wash ) )}</p>
</div>
{elseif $generated}
<div class="a4-notice is-ok" role="status">
    <p>{'Password was successfully generated and sent to: %1'|i18n( 'design/standard/user/forgotpassword',, array( $email|wash ) )}</p>
</div>
{elseif and( $wrong_key, first_set( $key_expired, false() ) )}
<div class="a4-login-alert message-warning" role="alert">
    <p class="a4-login-alert-title">{'This link has expired.'|i18n( 'design/standard/user/forgotpassword' )}</p>
    <p>{'For your security, a link to reset your password works only for %1 minutes and only once. Ask for a new link below.'|i18n( 'design/standard/user/forgotpassword',, array( first_set( $key_lifetime_minutes, 60 ) ) )}</p>
</div>
<p class="a4-login-alt"><a href={'/user/forgotpassword/'|ezurl}>{'Ask for a new link'|i18n( 'design/standard/user/forgotpassword' )}</a></p>
{elseif $wrong_key}
<div class="a4-login-alert message-warning" role="alert">
    <p class="a4-login-alert-title">{'The key is invalid or has been used. '|i18n( 'design/standard/user/forgotpassword' )|trim}</p>
</div>
<p class="a4-login-alt"><a href={'/user/forgotpassword/'|ezurl}>{'Ask for a new link'|i18n( 'design/standard/user/forgotpassword' )}</a></p>
{else}
<p class="a4-login-sub">{'If you have forgotten your password we can generate a new one for you. All you need to do is to enter your email address and we will create a new password for you.'|i18n( 'design/standard/user/forgotpassword' )}</p>

{if $wrong_email}
<div class="a4-login-alert message-warning" role="alert">
    <p class="a4-login-alert-title">{'Please enter a valid email address.'|i18n( 'design/standard/user/forgotpassword' )}</p>
</div>
{/if}

<form name="forgotpassword" class="a4-login-form" method="post" action={'/user/forgotpassword/'|ezurl} novalidate="novalidate">
    <label class="a4-field-label" for="email">{'Email'|i18n( 'design/standard/user/forgotpassword' )}</label>
    <input class="a4-field" type="email" name="UserEmail" id="email" value="{$a4_forgot_email|wash}"
           autocomplete="email" autocapitalize="none" autocorrect="off" spellcheck="false" inputmode="email" enterkeyhint="send"
           required="required" autofocus="autofocus"{if $wrong_email} aria-invalid="true"{/if} />

    <button class="a4-login-submit defaultbutton" type="submit" id="generatebutton" name="GenerateButton"
            value="{'Generate new password'|i18n( 'design/standard/user/forgotpassword' )|wash}">{'Generate new password'|i18n( 'design/standard/user/forgotpassword' )}</button>
</form>
{/if}

<p class="a4-login-forgot-row"><a class="a4-login-forgot" href={'/user/login'|ezurl}>{'Sign in'|i18n( 'design/admin/user/login' )}</a></p>
{undef $a4_forgot_email}
