{* mailpreferences/unsubscribe/<token>: the unsubscribe link of an e-mail. No login.
   Variables: state ('confirm' before, 'done' after, 'invalid' for a broken or expired link), email,
   category_name (the kind of e-mail the link is for, or false for all optional e-mail), form_action,
   manage_url (the personal preference page, or false). The one-click unsubscribe of a mail program (RFC 8058) does
   not see this page: it posts to the same address and gets a short answer. *}
{include uri='design:mailpreferences/parts/page_start.tpl'
         title='Unsubscribe'|i18n( 'design/standard/mailpreferences' )
         intro=false() crumb=false() wide=false() admin_tab=false()}
<div class="mp-big">
{if $state|eq( 'invalid' )}
    <div class="mp-card">
        <h2>{'This link does not work any more'|i18n( 'design/standard/mailpreferences' )}</h2>
        <p>{'The link may be incomplete or too old. We can send you a new one: you will get a link to a page where you can turn off any e-mail.'|i18n( 'design/standard/mailpreferences' )}</p>
        <div class="mp-actions"><a class="mp-btn primary" href={'mailpreferences/request'|ezurl}>{'Send me a new link'|i18n( 'design/standard/mailpreferences' )}</a></div>
    </div>
{elseif $state|eq( 'done' )}
    <div class="mp-notice mp-notice-success" role="status">
        <p><b>{'You are unsubscribed.'|i18n( 'design/standard/mailpreferences' )}</b>
        {if $category_name}{'%email gets no more e-mail of the kind "%category".'|i18n( 'design/standard/mailpreferences',, hash( '%email', $email, '%category', $category_name ) )|wash}
        {else}{'%email gets no more optional e-mail from us. Essential messages about an account, such as a password reset, are still sent.'|i18n( 'design/standard/mailpreferences',, hash( '%email', $email ) )|wash}{/if}</p>
    </div>
{if $manage_url}
    <div class="mp-card">
        <h2>{'Your other e-mail'|i18n( 'design/standard/mailpreferences' )}</h2>
        <p>{'See and change everything we may send you, or turn this back on, on your preference page.'|i18n( 'design/standard/mailpreferences' )}</p>
        <div class="mp-actions"><a class="mp-btn" href={$manage_url|ezurl}>{'Manage my e-mail preferences'|i18n( 'design/standard/mailpreferences' )}</a></div>
    </div>
{/if}
{else}
    <div class="mp-card">
        <h2>{if $category_name}{'Stop e-mail of the kind "%category"?'|i18n( 'design/standard/mailpreferences',, hash( '%category', $category_name ) )|wash}{else}{'Stop all optional e-mail?'|i18n( 'design/standard/mailpreferences' )}{/if}</h2>
        <p>{'For: %email'|i18n( 'design/standard/mailpreferences',, hash( '%email', $email ) )|wash}</p>
        <form method="post" action={$form_action|ezurl}>
            <input type="hidden" name="UnsubscribeConfirm" value="1" />
            <div class="mp-actions"><input class="mp-btn primary" type="submit" name="UnsubscribeButton" value="{'Unsubscribe'|i18n( 'design/standard/mailpreferences' )}" /></div>
        </form>
{if $manage_url}
        <p class="mp-hint">{'Or choose exactly what you want to receive:'|i18n( 'design/standard/mailpreferences' )} <a href={$manage_url|ezurl}>{'Manage my e-mail preferences'|i18n( 'design/standard/mailpreferences' )}</a></p>
{/if}
    </div>
{/if}
</div>
{include uri='design:mailpreferences/parts/page_end.tpl'}
