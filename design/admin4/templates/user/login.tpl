{* Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 98854ab935), Copyright (C) Mugo Web, GPL v2 *}
{* admin4 sign-in form. The kernel reads the same fields as before: Login, Password, Cookie (remember me),
   LoginButton and RedirectURI. Built for fast use on phones: labels above large fields, the right keyboard and
   autofill hints (username, current-password), Enter moves to the password and then signs in, a show/hide button
   for the password, one full-width button that cannot be sent twice (admin4.js), messages at the top in one box. *}
{* $register: user/register exists and this user may open it (policy omit list or the user/register policy) *}
{def $register = and( ezmodule( 'user/register' ), fetch( 'user', 'can_open', hash( 'uri', 'user/register' ) ) )
     $forgot   = ezmodule( 'user/forgotpassword' )
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

<form name="loginform" class="a4-login-form" method="post" action={'/user/login/'|ezurl} novalidate="novalidate">

    <label class="a4-field-label" for="logintext">{'Username'|i18n( 'design/admin/user/login' )}</label>
    <input class="a4-field" type="text" name="Login" id="logintext" value="{if is_set( $User:login )}{$User:login|wash}{/if}"
           autocomplete="username" autocapitalize="none" autocorrect="off" spellcheck="false" enterkeyhint="next"
           required="required" {if is_set( $User:login )|not}autofocus="autofocus"{/if}
           title="{'Enter a valid username in this field.'|i18n( 'design/admin/user/login' )}" />

    <div class="a4-field-head">
        <label class="a4-field-label" for="passwordtext">{'Password'|i18n( 'design/admin/user/login' )}</label>
        {if $forgot}<a class="a4-login-forgot" href={'/user/forgotpassword'|ezurl}>{'Forgot your password?'|i18n( 'design/admin/user/login' )}</a>{/if}
    </div>
    <div class="a4-field-wrap">
        <input class="a4-field" type="password" name="Password" id="passwordtext"
               autocomplete="current-password" enterkeyhint="go" required="required" {if is_set( $User:login )}autofocus="autofocus"{/if}
               title="{'Enter a valid password in this field.'|i18n( 'design/admin/user/login' )}" />
        <button type="button" class="a4-reveal" aria-controls="passwordtext" aria-pressed="false"
                data-label-show="{'Show password'|i18n( 'design/admin/user/login' )|wash}"
                data-label-hide="{'Hide password'|i18n( 'design/admin/user/login' )|wash}"
                title="{'Show password'|i18n( 'design/admin/user/login' )|wash}" aria-label="{'Show password'|i18n( 'design/admin/user/login' )|wash}">
            <span class="a4-icon a4-bi-eye is-large a4-eye" aria-hidden="true"></span>
            <span class="a4-icon a4-bi-eye-slash is-large a4-eye-off" aria-hidden="true"></span>
        </button>
    </div>

    {if $remember}
    <label class="a4-check"><input type="checkbox" name="Cookie" id="id3" /> <span>{'Remember me'|i18n( 'design/admin/user/login' )}</span></label>
    {/if}

    {if and( is_set( $User:max_num_of_failed_login ), ne( $User:max_num_of_failed_login, false() ) )}
    <p class="a4-login-note">{'The user will not be allowed to login after <b>%max_number_failed</b> failed login attempts.'|i18n( 'design/admin/user/login',, hash( '%max_number_failed', $User:max_num_of_failed_login ) )}</p>
    {/if}

    <button class="a4-login-submit defaultbutton" type="submit" id="loginbutton" name="LoginButton" value="{'Log in'|i18n( 'design/admin/user/login', 'Login button' )}"
            data-label-busy="{'Signing in…'|i18n( 'design/admin/user/login' )|wash}"
            title="{'Click here to log in using the username/password combination entered in the fields above.'|i18n( 'design/admin/user/login' )}">{'Sign in'|i18n( 'design/admin/user/login' )}</button>

    {if $register}
    <p class="a4-login-alt">{'No account yet?'|i18n( 'design/admin/user/login' )} <a href={'/user/register'|ezurl()}>{'Register new account'|i18n( 'design/admin/user/login' )}</a></p>
    {/if}

    <input type="hidden" name="RedirectURI" value="{$User:redirect_uri|wash}" />
</form>
{* The social login buttons of sevenx_authentication_2fa, where that extension is active and enables a provider *}
{if ezmodule( 'user2fa/oauth' )}
    {include uri='design:user2fa/exp_style.tpl'}
    {include uri='design:user2fa/parts/social_buttons.tpl' context='login' redirect=$User:redirect_uri}
{/if}
{undef $register $forgot $remember}
