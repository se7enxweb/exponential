{* The e-mail preference page: mailpreferences/settings (signed in), mailpreferences/manage/<token> (a personal link,
   no login) and mailpreferences/admin/user/<id> (an administrator, for a user). The same page in every design; the
   design's parts/page_start.tpl and parts/page_end.tpl frame it. Variables: those of parts/preferences.tpl, and
   user_name (admin mode), can_administrate, notification_settings (true when the subtree notifications are available). *}
{if $mode|eq( 'admin' )}
{include uri='design:mailpreferences/parts/page_start.tpl'
         title='E-mail preferences of %name'|i18n( 'design/standard/mailpreferences',, hash( '%name', $user_name ) )
         intro='You are changing these preferences for the user. Each change is recorded as made by an administrator, with your name.'|i18n( 'design/standard/mailpreferences' )
         crumb=hash( 'url', 'mailpreferences/admin/user', 'text', 'Find another user'|i18n( 'design/admin/mailpreferences' ) )
         wide=false() admin_tab='user'}
{else}
{include uri='design:mailpreferences/parts/page_start.tpl'
         title='E-mail preferences'|i18n( 'design/standard/mailpreferences' )
         intro='Choose which e-mail you want from us. Essential messages about your account are always sent; everything else only if you want it.'|i18n( 'design/standard/mailpreferences' )
         crumb=false() wide=false() admin_tab=false()}
{/if}

    <p class="mp-hint" style="margin: -.5em 0 1em;">{'For: %email'|i18n( 'design/standard/mailpreferences',, hash( '%email', $email ) )|wash}
{if $mode|eq( 'token' )}
    &middot; {'You opened this page with the personal link from one of our e-mails. Please do not forward that e-mail: the link lets anyone change these preferences.'|i18n( 'design/standard/mailpreferences' )}
{/if}
{if and( $mode|eq( 'account' ), first_set( $can_administrate, false() ) )}
    &middot; <a href={'mailpreferences/admin/status'|ezurl}>{'Administration'|i18n( 'design/standard/mailpreferences' )}</a>
{/if}
    </p>

{include uri='design:mailpreferences/parts/preferences.tpl'}

{if and( $mode|eq( 'account' ), first_set( $notification_settings, false() ) )}
    <p class="mp-hint">{'Which pages and items you follow is chosen in the notification settings; whether those notifications are sent at all is chosen here.'|i18n( 'design/standard/mailpreferences' )}
    <a href={'notification/settings'|ezurl}>{'Notification settings'|i18n( 'design/standard/mailpreferences' )}</a></p>
{/if}

{include uri='design:mailpreferences/parts/page_end.tpl'}
