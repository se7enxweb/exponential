{* Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 98854ab935), Copyright (C) Mugo Web, GPL v2 *}
{* Warnings *}
{if first_set( $passkey_warning, false() )}
<div class="message-warning" role="alert">
<h2>{'Could not sign in with the passkey'|i18n( 'design/standard/user/passkeys' )}</h2>
<ul><li>{$passkey_warning|wash}</li></ul>
</div>
{/if}
{if $User:warning.bad_login}
<div class="message-warning">
<h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {'The system could not log you in.'|i18n( 'design/admin/user/login' )}</h2>
<ul>
    {if and( is_set( $User:user_is_not_allowed_to_login ), eq( $User:user_is_not_allowed_to_login, true() ) )}
         <li>{'"%user_login" is not allowed to log in because failed login attempts by this user exceeded allowable number of failed login attempts!'|i18n( 'design/admin/user/login',, hash( '%user_login', $User:login ) )}</li>
         <li>{'Please contact the site administrator.'|i18n( 'design/admin/user/login' )}</li>
    {else}
         <li>{'Make sure that the username and password is correct.'|i18n( 'design/admin/user/login' )}</li>
         <li>{'All letters must be entered in the correct case.'|i18n( 'design/admin/user/login' )}</li>
         <li>{'Please try again or contact the site administrator.'|i18n( 'design/admin/user/login' )}</li>
    {/if}
</ul>
</div>
{else}
{if $site_access.allowed|not}
<div class="message-warning">
<h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {'Access denied!'|i18n( 'design/admin/user/login' )}</h2>
<ul>
    <li>{'You do not have permission to access <%siteaccess_name>.'|i18n( 'design/admin/user/login',, hash( '%siteaccess_name', $site_access.name ) )|wash}</li>
    <li>{'Please contact the site administrator.'|i18n( 'design/admin/user/login' )}</li>
</ul>
</div>
{/if}
{/if}

{* Login window *}
<div class="context-block">

{* With passkeys on, the form also signs in with a passkey: the button in the control bar and the browser's passkey
   autofill of the username field (design:javascript/exp_passkey.js, doc/eng-us/guides/passkeys.md) *}
{def $passkey_options = first_set( $passkey_options, '' )}
<form name="loginform" method="post" action={'/user/login/'|ezurl}{if $passkey_options} data-exp-passkey="login"{if first_set( $passkey_warning, false() )|not} data-exp-passkey-autofill="1"{/if} data-exp-passkey-options="{$passkey_options|wash}"
      data-msg-working="{'Follow the steps of your browser or device.'|i18n( 'design/standard/user/passkeys' )|wash}"
      data-msg-cancelled="{'The passkey dialog was closed. You can sign in with your password instead.'|i18n( 'design/standard/user/passkeys' )|wash}"
      data-msg-failed="{'The passkey could not be used. Please try again, or sign in with your password.'|i18n( 'design/standard/user/passkeys' )|wash}"{/if}>

<div class="login-inputs context-attributes">
    
    {if and( is_set( $User:max_num_of_failed_login ), ne( $User:max_num_of_failed_login, false() ) )}
        <div class="block login-text-wrapper">
        {'The user will not be allowed to login after <b>%max_number_failed</b> failed login attempts.'|i18n( 'design/admin/user/login',, hash( '%max_number_failed', $User:max_num_of_failed_login ) )}
        </div>
    {/if}    

    <div class="block">
        <div class="login-input-wrapper">
            <div id="icon-login"></div>
            <input class="halfbox" type="text" autofocus="autofocus" size="10" name="Login" id="logintext" autocomplete="{if $passkey_options}username webauthn{else}username{/if}" placeholder="{'Username'|i18n( 'design/admin/user/login' )}" tabindex="1" title="{'Enter a valid username in this field.'|i18n( 'design/admin/user/login' )}" />
        </div>
    </div>

    <div class="block">
        <div class="login-input-wrapper">
            <div id="icon-password"></div>
            <input class="halfbox" type="password" size="10" name="Password" id="passwordtext" autocomplete="current-password" placeholder="{'Password'|i18n( 'design/admin/user/login' )}" tabindex="2" title="{'Enter a valid password in this field.'|i18n( 'design/admin/user/login' )}" />
        </div>
    </div>

    {if and( ezini_hasvariable( 'Session', 'RememberMeTimeout' ), ezini( 'Session', 'RememberMeTimeout' ) )}
        <div class="block login-text-wrapper">
            <input type="checkbox" name="Cookie" id="id3" checked="checked" /><label for="id3" class="a4-user-inline-label">{"Remember me"|i18n("design/admin/user/login")}</label>
        </div>
    {/if}

</div>


<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<div class="block">
    <div class="login-input-wrapper">
        <input class="defaultbutton" type="submit" id="loginbutton" name="LoginButton" value="{'Log in'|i18n( 'design/admin/user/login', 'Login button' )}" tabindex="3" title="{'Click here to log in using the username/password combination entered in the fields above.'|i18n( 'design/admin/user/login' )}" />
    </div>
    {if $passkey_options}
    <div class="login-input-wrapper" data-exp-passkey-only="1" hidden="hidden">
        <button class="a4-btn" type="button" data-exp-passkey-start="1" tabindex="4"><span class="a4-icon a4-bi-key" aria-hidden="true"></span> {'Sign in with a passkey'|i18n( 'design/standard/user/passkeys' )}</button>
    </div>
    <p class="a4-notice is-info" data-exp-passkey-status="1" role="status" aria-live="polite" hidden="hidden"></p>
    {/if}
    {* the register link only for a user who may register: user/register in the policy omit list or the policy *}
    {if and( ezmodule( 'user/register' ), fetch( 'user', 'can_open', hash( 'uri', 'user/register' ) ) )}
        <div class="login-text-wrapper">
            {'or'|i18n( 'design/admin/user/login')}
            <br/>
            <a href={'/user/register'|ezurl()}>{'Register new account'|i18n( 'design/admin/user/login')}</a>
        </div>
    {/if}
</div>
{* DESIGN: Control bar END *}</div></div>
</div>

<input type="hidden" name="RedirectURI" value="{$User:redirect_uri|wash}" />

</form>
{if $passkey_options}<script src={'javascript/exp_passkey.js'|ezdesign} defer="defer"></script>{/if}
{undef $passkey_options}
{* The social login buttons of sevenx_authentication_2fa, where that extension is active and enables a provider *}
{if ezmodule( 'user2fa/oauth' )}
    {include uri='design:user2fa/exp_style.tpl'}
    {include uri='design:user2fa/parts/social_buttons.tpl' context='login' redirect=$User:redirect_uri}
{/if}

</div>
