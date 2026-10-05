{* mailpreferences/confirm/<token>: the double opt-in link. Opening the link shows a button; only the button confirms,
   so a mail scanner that opens links does not sign anybody up. No login.
   Variables: state ('confirm', 'done', 'invalid'), kind ('category' or 'email_change'), email, category_name,
   category_description, form_action, manage_url (or false). *}
{include uri='design:mailpreferences/parts/page_start.tpl'
         title=cond( $kind|eq( 'email_change' ), 'Confirm your e-mail address'|i18n( 'design/standard/mailpreferences' ), 'Confirm your subscription'|i18n( 'design/standard/mailpreferences' ) )
         intro=false() crumb=false() wide=false() admin_tab=false()}
<div class="mp-big">
{if $state|eq( 'invalid' )}
    <div class="mp-card">
        <h2>{'This link does not work any more'|i18n( 'design/standard/mailpreferences' )}</h2>
        <p>{'The link may be incomplete, already used or too old. Nothing was changed. To try again, turn the e-mail on again on your preference page; you will get a new link.'|i18n( 'design/standard/mailpreferences' )}</p>
        <div class="mp-actions"><a class="mp-btn" href={'mailpreferences/request'|ezurl}>{'Send me a link to my preferences'|i18n( 'design/standard/mailpreferences' )}</a></div>
    </div>
{elseif $state|eq( 'done' )}
    <div class="mp-notice mp-notice-success" role="status">
{if $kind|eq( 'email_change' )}
        <p><b>{'Thank you, your e-mail address is confirmed.'|i18n( 'design/standard/mailpreferences' )}</b> {'From now on we write to %email.'|i18n( 'design/standard/mailpreferences',, hash( '%email', $email ) )|wash}</p>
{else}
        <p><b>{'Thank you, your subscription is confirmed.'|i18n( 'design/standard/mailpreferences' )}</b> {'%email now gets e-mail of the kind "%category". You can turn it off at any time, with the link at the end of every one of these e-mails or on your preference page.'|i18n( 'design/standard/mailpreferences',, hash( '%email', $email, '%category', $category_name ) )|wash}</p>
{/if}
    </div>
{if $manage_url}
    <div class="mp-actions"><a class="mp-btn" href={$manage_url|ezurl}>{'Manage my e-mail preferences'|i18n( 'design/standard/mailpreferences' )}</a></div>
{/if}
{else}
    <div class="mp-card">
{if $kind|eq( 'email_change' )}
        <h2>{'Use %email for this account?'|i18n( 'design/standard/mailpreferences',, hash( '%email', $email ) )|wash}</h2>
        <p>{'Someone asked to change the e-mail address of an account to this address. Confirm only if that was you.'|i18n( 'design/standard/mailpreferences' )}</p>
{else}
        <h2>{'Send e-mail of the kind "%category" to %email?'|i18n( 'design/standard/mailpreferences',, hash( '%category', $category_name, '%email', $email ) )|wash}</h2>
{if $category_description}<p>{$category_description|wash}</p>{/if}
        <p class="mp-hint">{'Confirm only if you asked for it. If you did not, do nothing: without your confirmation nothing is sent.'|i18n( 'design/standard/mailpreferences' )}</p>
{/if}
        <form method="post" action={$form_action|ezurl}>
            <input type="hidden" name="ConfirmToken" value="1" />
            <div class="mp-actions"><input class="mp-btn primary" type="submit" name="ConfirmButton" value="{'Yes, confirm'|i18n( 'design/standard/mailpreferences' )}" /></div>
        </form>
    </div>
{/if}
</div>
{include uri='design:mailpreferences/parts/page_end.tpl'}
