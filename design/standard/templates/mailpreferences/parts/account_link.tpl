{* A prominent link to the e-mail preference page, for the pages that touch e-mail: the user's profile, the notification
   settings, the newsletter pages. Variables: context ('profile', 'notification' or 'newsletter'; optional).
   Signed in: the page itself; not signed in: the "send me a link" page. Inline styles, so it needs nothing of the
   page it is on. *}
{def $mp_context = first_set( $context, 'profile' )
     $mp_signed_in = fetch( 'user', 'current_user' ).is_logged_in}
<div class="mp-account-link" style="display:flex;flex-wrap:wrap;gap:.4em 1em;align-items:center;justify-content:space-between;margin:0 0 1em;padding:.7em .95em;border:1px solid #bcd2f0;border-radius:9px;background:#e8f0fb;color:#1f4a7f;">
    <p style="margin:0;flex:1 1 18em;">
        <b>{'E-mail preferences'|i18n( 'design/standard/mailpreferences' )}:</b>
{if $mp_context|eq( 'notification' )}
        {'Whether notifications are sent at all, how often, and every other kind of e-mail, is chosen on one page.'|i18n( 'design/standard/mailpreferences' )}
{elseif $mp_context|eq( 'newsletter' )}
        {'See and change every kind of e-mail we send you, including newsletters, or stop all optional e-mail, on one page.'|i18n( 'design/standard/mailpreferences' )}
{else}
        {'Choose which e-mail you get from us, download your e-mail data, or stop all optional e-mail.'|i18n( 'design/standard/mailpreferences' )}
{/if}
    </p>
{if $mp_signed_in}
    <a href={'mailpreferences/settings'|ezurl} style="display:inline-block;padding:.4em .95em;border:1px solid #1f4a7f;border-radius:8px;background:#fff;color:#1f4a7f;font-weight:650;text-decoration:none;">{'Open my e-mail preferences'|i18n( 'design/standard/mailpreferences' )}</a>
{else}
    <a href={'mailpreferences/request'|ezurl} style="display:inline-block;padding:.4em .95em;border:1px solid #1f4a7f;border-radius:8px;background:#fff;color:#1f4a7f;font-weight:650;text-decoration:none;">{'Manage my e-mail without an account'|i18n( 'design/standard/mailpreferences' )}</a>
{/if}
</div>
{undef $mp_context $mp_signed_in}
