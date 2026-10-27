{* The note to the administrators about a sign-in from a new device (expUserSessionTracker::sendAdminMail()), when
   site.ini [UserSessionSettings] NewDeviceAdminMail=enabled. Category admin.
   Variables: those of user/new_signin_mail.tpl, and admin_url (the user's sessions page, absolute).
   Sets subject, content_type (text/html) and body_text (the plain-text part).
   The look is design:mail/layout.tpl (HTML) and design:mail/layout_text.tpl (text); this template only passes text
   and links, which the layout washes. *}
{def $na_site = cond( is_set( $site_name ), $site_name, ezini( 'SiteSettings', 'SiteName' ) )
     $na_subject = 'New sign-in of %login on %site'|i18n( 'design/standard/user/new_signin_mail',, hash( '%login', $user.login, '%site', $na_site ) )
     $na_rows = array( hash( 'label', 'Device'|i18n( 'design/standard/user/new_signin_mail' ), 'value', $device ),
                       hash( 'label', 'Time'|i18n( 'design/standard/user/new_signin_mail' ), 'value', $signed_in_at|l10n( 'shortdatetime' ) ) )
     $na_intro = 'The account %login signed in from a device or browser it has not used lately.'|i18n( 'design/standard/user/new_signin_mail',, hash( '%login', $user.login ) )
     $na_buttons = array( hash( 'label', 'View the sessions of this user'|i18n( 'design/standard/user/new_signin_mail' ), 'url', $admin_url, 'kind', 'primary' ) )
     $na_note = 'If the button does not work, copy this address into your browser: %url'|i18n( 'design/standard/mail',, hash( '%url', $admin_url ) )}
{if $ip|ne( '' )}
    {set $na_rows = $na_rows|append( hash( 'label', 'Approximate address'|i18n( 'design/standard/user/new_signin_mail' ), 'value', $ip ) )}
{/if}
{set-block scope=root variable=subject}{$na_subject}{/set-block}
{set-block scope=root variable=content_type}text/html{/set-block}
{set-block scope=root variable=body_text}{include uri='design:mail/layout_text.tpl'
    mail_title=$na_subject mail_intro=$na_intro mail_rows=$na_rows mail_buttons=$na_buttons mail_site_name=$na_site}{/set-block}
{include uri='design:mail/layout.tpl'
    mail_title=$na_subject mail_intro=$na_intro mail_rows=$na_rows mail_buttons=$na_buttons mail_links_note=$na_note
    mail_site_name=$na_site}
{undef $na_site $na_subject $na_rows $na_intro $na_buttons $na_note}
