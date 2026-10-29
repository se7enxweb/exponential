{* "New sign-in to your account" (expUserSessionTracker::sendUserMail()), sent to the account's address when it signs
   in from a browser, system and device kind it has not used lately, or from a country it has not signed in from
   lately (only with a local GeoIP database). Essential mail (category security); the user turns it off on "Your
   devices" (user/sessions). Never a password, a token or the full address.
   Variables: user (eZUser), device ("Firefox 131 on Windows 10/11"), device_type, ip (approximate: 203.0.113.x),
   location ("Berlin, Germany" or ''), new_device, new_location (what is new), signed_in_at (timestamp), method,
   sessions_url and security_url (absolute), site_name.
   Sets subject, content_type (text/html) and body_text (the plain-text part).
   The look is design:mail/layout.tpl (HTML) and design:mail/layout_text.tpl (text); this template only passes text
   and links, which the layout washes. *}
{def $ns_site = cond( is_set( $site_name ), $site_name, ezini( 'SiteSettings', 'SiteName' ) )
     $ns_is_location = and( is_set( $new_location ), $new_location, or( is_set( $new_device )|not, $new_device|not ) )
     $ns_rows = array( hash( 'label', 'Device'|i18n( 'design/standard/user/new_signin_mail' ), 'value', $device ),
                       hash( 'label', 'Time'|i18n( 'design/standard/user/new_signin_mail' ), 'value', $signed_in_at|l10n( 'shortdatetime' ) ) )
     $ns_title = 'New sign-in to your account'|i18n( 'design/standard/user/new_signin_mail' )
     $ns_intro = cond( $ns_is_location,
                       'Your account %login on %site was just used to sign in from a place it has not signed in from lately:'|i18n( 'design/standard/user/new_signin_mail',,
                           hash( '%login', $user.login, '%site', $ns_site ) ),
                       'Your account %login on %site was just used to sign in from a device or browser it has not used lately:'|i18n( 'design/standard/user/new_signin_mail',,
                           hash( '%login', $user.login, '%site', $ns_site ) ) )
     $ns_paragraphs = array( 'If this was you, you do not need to do anything.'|i18n( 'design/standard/user/new_signin_mail' ) )
     $ns_notice = 'If it was not you, someone may know your password: sign that session out on your devices page, then change your password.'|i18n( 'design/standard/user/new_signin_mail' )
     $ns_buttons = array( hash( 'label', 'Review your devices'|i18n( 'design/standard/user/new_signin_mail' ), 'url', $sessions_url, 'kind', 'primary' ) )
     $ns_note = 'If the button does not work, copy this address into your browser: %url'|i18n( 'design/standard/mail',, hash( '%url', $sessions_url ) )
     $ns_outro = array( 'You can turn these e-mails off on the same page.'|i18n( 'design/standard/user/new_signin_mail' ) )}
{if and( is_set( $location ), $location|ne( '' ) )}
    {set $ns_rows = $ns_rows|append( hash( 'label', 'Approximate location'|i18n( 'design/standard/user/new_signin_mail' ), 'value', $location ) )}
{/if}
{if $ip|ne( '' )}
    {set $ns_rows = $ns_rows|append( hash( 'label', 'Approximate address'|i18n( 'design/standard/user/new_signin_mail' ), 'value', $ip ) )}
{/if}
{if and( is_set( $security_url ), $security_url|ne( '' ) )}
    {set $ns_buttons = $ns_buttons|append( hash( 'label', 'Secure your account'|i18n( 'design/standard/user/security_mail' ), 'url', $security_url, 'kind', 'secondary' ) )}
{/if}
{set-block scope=root variable=subject}{'New sign-in to your account on %site'|i18n( 'design/standard/user/new_signin_mail',, hash( '%site', $ns_site ) )}{/set-block}
{set-block scope=root variable=content_type}text/html{/set-block}
{set-block scope=root variable=body_text}{include uri='design:mail/layout_text.tpl'
    mail_title=$ns_title mail_intro=$ns_intro mail_rows=$ns_rows mail_paragraphs=$ns_paragraphs mail_notice=$ns_notice
    mail_buttons=$ns_buttons mail_outro=$ns_outro mail_site_name=$ns_site}{/set-block}
{include uri='design:mail/layout.tpl'
    mail_title=$ns_title mail_intro=$ns_intro mail_rows=$ns_rows mail_paragraphs=$ns_paragraphs mail_notice=$ns_notice
    mail_buttons=$ns_buttons mail_links_note=$ns_note mail_outro=$ns_outro mail_site_name=$ns_site}
{undef $ns_site $ns_is_location $ns_rows $ns_title $ns_intro $ns_paragraphs $ns_notice $ns_buttons $ns_note $ns_outro}
