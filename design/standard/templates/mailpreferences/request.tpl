{* mailpreferences/request: "Send me a link" for people without an account or not signed in. The answer is the same
   whether the address is known or not, so the page does not tell anybody who is on our lists.
   Variables: state ('form' or 'sent'), email (the address typed, washed by the template), error (text or false),
   valid_hours (how long the link works). *}
{include uri='design:mailpreferences/parts/page_start.tpl'
         title='Manage your e-mail without an account'|i18n( 'design/standard/mailpreferences' )
         intro='Enter your e-mail address. We send you a personal link to a page where you can see and change every kind of e-mail we may send you, or stop all of it.'|i18n( 'design/standard/mailpreferences' )
         crumb=false() wide=false() admin_tab=false()}
<div class="mp-big">
{if $state|eq( 'sent' )}
    <div class="mp-notice mp-notice-success" role="status">
        <p><b>{'Please check your inbox.'|i18n( 'design/standard/mailpreferences' )}</b>
        {'If we send e-mail to %email, a message with your personal link is on its way. The link works for %hours hours.'|i18n( 'design/standard/mailpreferences',, hash( '%email', $email, '%hours', $valid_hours ) )|wash}</p>
        <p>{'Nothing arrived? Look in the spam folder, or try again in a few minutes.'|i18n( 'design/standard/mailpreferences' )}</p>
    </div>
{else}
{if $error}
    <div class="mp-notice mp-notice-error" role="alert"><p>{$error|wash}</p></div>
{/if}
    <div class="mp-card">
        <form method="post" action={'mailpreferences/request'|ezurl}>
            <div class="mp-field">
                <label for="mp-request-email">{'E-mail address'|i18n( 'design/standard/mailpreferences' )}</label>
                <input type="email" id="mp-request-email" name="Email" value="{$email|wash}" required="required" autocomplete="email" inputmode="email" />
            </div>
            <div class="mp-actions"><input class="mp-btn primary" type="submit" name="RequestButton" value="{'Send me the link'|i18n( 'design/standard/mailpreferences' )}" /></div>
        </form>
        <p class="mp-hint">{'Have an account? Sign in and open your e-mail preferences directly.'|i18n( 'design/standard/mailpreferences' )}
        <a href={'user/login'|ezurl}>{'Sign in'|i18n( 'design/standard/mailpreferences' )}</a></p>
    </div>
{/if}
</div>
{include uri='design:mailpreferences/parts/page_end.tpl'}
