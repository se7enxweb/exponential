{* Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 98854ab935), Copyright (C) Mugo Web, GPL v2 *}
{* admin4 sign-in form. The kernel reads the same fields as before: Login, Password, Cookie (remember me),
   LoginButton and RedirectURI. Built for fast use on phones: labels above large fields, the right keyboard and
   autofill hints (username, current-password), Enter moves to the password and then signs in, a show/hide button
   for the password, one full-width button that cannot be sent twice (admin4.js), messages at the top in one box. *}
{* $register, $forgot: the view exists and this user may open it (policy omit list or the user/register,
   user/password policy); an administration where anonymous may not reset a password shows no link to a denied page *}
{def $register = and( ezmodule( 'user/register' ), fetch( 'user', 'can_open', hash( 'uri', 'user/register' ) ) )
     $forgot   = and( ezmodule( 'user/forgotpassword' ), fetch( 'user', 'can_open', hash( 'uri', 'user/forgotpassword' ) ) )
     $remember = and( ezini_hasvariable( 'Session', 'RememberMeTimeout' ), ezini( 'Session', 'RememberMeTimeout' ) )}

<h1 class="a4-login-title">{'Sign in to Exponential'|i18n( 'design/admin/user/login' )}</h1>
<p class="a4-login-sub">{'Administration of %site'|i18n( 'design/admin/user/login', , hash( '%site', ezini( 'SiteSettings', 'SiteName' )|wash ) )}</p>

{if $User:warning.bad_login}
<div class="a4-login-alert message-warning" role="alert">
    <p class="a4-login-alert-title">{'The system could not log you in.'|i18n( 'design/admin/user/login' )}</p>
    {if and( is_set( $User:user_is_not_allowed_to_login ), eq( $User:user_is_not_allowed_to_login, true() ) )}
        <p>{'"%user_login" is not allowed to log in because failed login attempts by this user exceeded allowable number of failed login attempts!'|i18n( 'design/admin/user/login',, hash( '%user_login', $User:login ) )|wash}</p>
        <p>{'Please contact the site administrator.'|i18n( 'design/admin/user/login' )}</p>
    {else}
        <p>{'Make sure that the username and password is correct.'|i18n( 'design/admin/user/login' )} {'All letters must be entered in the correct case.'|i18n( 'design/admin/user/login' )}</p>
    {/if}
</div>
{elseif $site_access.allowed|not}
<div class="a4-login-alert message-warning" role="alert">
    <p class="a4-login-alert-title">{'Access denied!'|i18n( 'design/admin/user/login' )}</p>
    <p>{'You do not have permission to access <%siteaccess_name>.'|i18n( 'design/admin/user/login',, hash( '%siteaccess_name', $site_access.name ) )|wash}</p>
    <p>{'Please contact the site administrator.'|i18n( 'design/admin/user/login' )}</p>
</div>
{/if}

{if first_set( $passkey_warning, false() )}
<div class="a4-login-alert message-warning" role="alert">
    <p class="a4-login-alert-title">{'Could not sign in with the passkey'|i18n( 'design/standard/user/passkeys' )}</p>
    <p>{$passkey_warning|wash}</p>
</div>
{/if}

{* The username field gets the focus on a fresh page; the password field only when the username is already filled in (a failed sign-in).
   With passkeys on, the form also signs in with a passkey: the button under "Sign in" and the browser's passkey
   autofill of the username field (design:javascript/exp_passkey.js, doc/eng-us/guides/passkeys.md). *}
{def $a4_login_name = cond( is_set( $User:login ), $User:login|trim, '' )
     $a4_passkey = first_set( $passkey_options, '' )}
<form name="loginform" class="a4-login-form" method="post" action={'/user/login/'|ezurl} novalidate="novalidate"{if $a4_passkey}
      data-exp-passkey="login"{if first_set( $passkey_warning, false() )|not} data-exp-passkey-autofill="1"{/if} data-exp-passkey-options="{$a4_passkey|wash}"
      data-msg-working="{'Follow the steps of your browser or device.'|i18n( 'design/standard/user/passkeys' )|wash}"
      data-msg-cancelled="{'The passkey dialog was closed. You can sign in with your password instead.'|i18n( 'design/standard/user/passkeys' )|wash}"
      data-msg-failed="{'The passkey could not be used. Please try again, or sign in with your password.'|i18n( 'design/standard/user/passkeys' )|wash}"{/if}>

    <label class="a4-field-label" for="logintext">{'Username'|i18n( 'design/admin/user/login' )}</label>
    <input class="a4-field" type="text" name="Login" id="logintext" value="{if is_set( $User:login )}{$User:login|wash}{/if}"
           autocomplete="{if $a4_passkey}username webauthn{else}username{/if}" autocapitalize="none" autocorrect="off" spellcheck="false" enterkeyhint="next"
           required="required" {if $a4_login_name|eq( '' )}autofocus="autofocus"{/if}
           title="{'Enter a valid username in this field.'|i18n( 'design/admin/user/login' )}" />

    <div class="a4-field-head">
        <label class="a4-field-label" for="passwordtext">{'Password'|i18n( 'design/admin/user/login' )}</label>
    </div>
    <div class="a4-field-wrap">
        <input class="a4-field" type="password" name="Password" id="passwordtext"
               autocomplete="current-password" enterkeyhint="go" required="required" {if $a4_login_name|ne( '' )}autofocus="autofocus"{/if}
               title="{'Enter a valid password in this field.'|i18n( 'design/admin/user/login' )}" />
        <button type="button" class="a4-reveal" tabindex="-1" aria-controls="passwordtext" aria-pressed="false"
                data-label-show="{'Show password'|i18n( 'design/admin/user/login' )|wash}"
                data-label-hide="{'Hide password'|i18n( 'design/admin/user/login' )|wash}"
                title="{'Show password'|i18n( 'design/admin/user/login' )|wash}" aria-label="{'Show password'|i18n( 'design/admin/user/login' )|wash}">
            <span class="a4-icon a4-bi-eye is-large a4-eye" aria-hidden="true"></span>
            <span class="a4-icon a4-bi-eye-slash is-large a4-eye-off" aria-hidden="true"></span>
        </button>
    </div>

    {if $remember}
    <label class="a4-check"><input type="checkbox" name="Cookie" id="id3" checked="checked" /> <span>{'Remember me'|i18n( 'design/admin/user/login' )}</span></label>
    {/if}

    {if and( is_set( $User:max_num_of_failed_login ), ne( $User:max_num_of_failed_login, false() ) )}
    <p class="a4-login-note">{'The user will not be allowed to login after <b>%max_number_failed</b> failed login attempts.'|i18n( 'design/admin/user/login',, hash( '%max_number_failed', $User:max_num_of_failed_login ) )}</p>
    {/if}

    <button class="a4-login-submit defaultbutton" type="submit" id="loginbutton" name="LoginButton" value="{'Log in'|i18n( 'design/admin/user/login', 'Login button' )}"
            data-label-busy="{'Signing in…'|i18n( 'design/admin/user/login' )|wash}"
            title="{'Click here to log in using the username/password combination entered in the fields above.'|i18n( 'design/admin/user/login' )}">{'Sign in'|i18n( 'design/admin/user/login' )}</button>

    {if $a4_passkey}
    <button type="button" id="passkeybutton" data-exp-passkey-start="1" data-exp-passkey-only="1" hidden="hidden">
        <span class="a4-icon a4-bi-key is-large" aria-hidden="true"></span>{'Sign in with a passkey'|i18n( 'design/standard/user/passkeys' )}</button>
    <p class="a4-notice is-info" data-exp-passkey-status="1" role="status" aria-live="polite" hidden="hidden"></p>
    {/if}

    {* After the sign-in button, so Tab goes username, password, sign in, then this link *}
    {if $forgot}<p class="a4-login-forgot-row"><a class="a4-login-forgot" href={'/user/forgotpassword'|ezurl}>{'Forgot your password?'|i18n( 'design/admin/user/login' )}</a></p>{/if}

    {if $register}
    <p class="a4-login-alt">{'No account yet?'|i18n( 'design/admin/user/login' )} <a href={'/user/register'|ezurl()}>{'Register new account'|i18n( 'design/admin/user/login' )}</a></p>
    {/if}

    <input type="hidden" name="RedirectURI" value="{$User:redirect_uri|wash}" />
</form>
{if $a4_passkey}{ezscript( array( 'exp_passkey.js' ) )}{/if}
{undef $a4_login_name $a4_passkey}
{* The social login buttons of sevenx_authentication_2fa, where that extension is active and enables a provider *}
{if ezmodule( 'user2fa/oauth' )}
    {include uri='design:user2fa/exp_style.tpl'}
    {include uri='design:user2fa/parts/social_buttons.tpl' context='login' redirect=$User:redirect_uri}
{/if}
{undef $register $forgot $remember}
