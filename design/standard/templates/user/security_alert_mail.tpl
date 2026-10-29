{* "Security alert" (expUserSecurityAlerts::send()), sent to the account's address when a passkey was added or removed, two-step sign-in was turned on
   or off, a recovery code was used, sign-ins failed several times in a row, or the account was locked. Essential mail
   (category security): it cannot be turned off by the user. Never a password, a code or the full address.
   Variables: user (eZUser), alert (twofactor_on, twofactor_off, recovery_used, failures, locked, passkey_added,
   passkey_removed), time (timestamp),
   ip (approximate: 203.0.113.x, or ''), method (totp, email ...), left (recovery codes left, -1 unknown), attempts,
   security_url, sessions_url, password_url (absolute), site_name.
   Sets subject, content_type (text/html) and body_text (the plain-text part).
   The look is design:mail/layout.tpl (HTML) and design:mail/layout_text.tpl (text); this template only passes text
   and links, which the layout washes. *}
{def $sa_site = cond( is_set( $site_name ), $site_name, ezini( 'SiteSettings', 'SiteName' ) )
     $sa_title = 'Security alert'|i18n( 'design/standard/user/security_mail' )
     $sa_intro = ''
     $sa_rows = array( hash( 'label', 'Account'|i18n( 'design/standard/user/security_mail' ), 'value', $user.login ),
                       hash( 'label', 'Time'|i18n( 'design/standard/user/security_mail' ), 'value', $time|l10n( 'shortdatetime' ) ) )
     $sa_paragraphs = array()
     $sa_notice = 'If this was not you, someone may know your password: change it now and check where your account is signed in.'|i18n( 'design/standard/user/security_mail' )
     $sa_buttons = array( hash( 'label', 'Secure your account'|i18n( 'design/standard/user/security_mail' ), 'url', $security_url, 'kind', 'primary' ),
                          hash( 'label', 'Review your devices'|i18n( 'design/standard/user/security_mail' ), 'url', $sessions_url, 'kind', 'secondary' ) )
     $sa_note = 'If the button does not work, copy this address into your browser: %url'|i18n( 'design/standard/mail',, hash( '%url', $security_url ) )
     $sa_outro = array( 'This is a security alert about your account. It is sent for every such change and cannot be turned off.'|i18n( 'design/standard/user/security_mail' ) )}
{switch match=$alert}
{case match='twofactor_on'}
    {set $sa_intro = 'Two-step sign-in was turned on for your account %login on %site.'|i18n( 'design/standard/user/security_mail',, hash( '%login', $user.login, '%site', $sa_site ) )
         $sa_paragraphs = array( 'From now on, signing in asks for a code besides your password. If you did this, you do not need to do anything.'|i18n( 'design/standard/user/security_mail' ) )}
{/case}
{case match='twofactor_off'}
    {set $sa_intro = 'Two-step sign-in was turned off for your account %login on %site.'|i18n( 'design/standard/user/security_mail',, hash( '%login', $user.login, '%site', $sa_site ) )
         $sa_paragraphs = array( 'Your account is now protected by your password alone. If you did not do this, turn two-step sign-in on again after you changed your password.'|i18n( 'design/standard/user/security_mail' ) )}
{/case}
{case match='passkey_added'}
    {set $sa_intro = 'A passkey was added to your account %login on %site.'|i18n( 'design/standard/user/security_mail',, hash( '%login', $user.login, '%site', $sa_site ) )
         $sa_paragraphs = array( 'Whoever holds this passkey can sign in to your account without your password. If you did not add it, remove it under Passkeys and change your password.'|i18n( 'design/standard/user/security_mail' ) )}
{/case}
{case match='passkey_removed'}
    {set $sa_intro = 'A passkey was removed from your account %login on %site.'|i18n( 'design/standard/user/security_mail',, hash( '%login', $user.login, '%site', $sa_site ) )
         $sa_paragraphs = array( 'It can no longer sign in to your account. If you did not remove it, check your passkeys and devices.'|i18n( 'design/standard/user/security_mail' ) )}
{/case}
{case match='recovery_used'}
    {set $sa_intro = 'A recovery code was used to sign in to your account %login on %site.'|i18n( 'design/standard/user/security_mail',, hash( '%login', $user.login, '%site', $sa_site ) )
         $sa_paragraphs = array( 'Each recovery code works once. If you lost your phone, set up your authenticator app again and keep the new recovery codes in a safe place.'|i18n( 'design/standard/user/security_mail' ) )}
    {if $left|ge( 0 )}{set $sa_rows = $sa_rows|append( hash( 'label', 'Recovery codes left'|i18n( 'design/standard/user/security_mail' ), 'value', $left ) )}{/if}
{/case}
{case match='failures'}
    {set $sa_intro = 'Someone tried to sign in to your account %login on %site with a wrong password %count times in a row.'|i18n( 'design/standard/user/security_mail',, hash( '%login', $user.login, '%site', $sa_site, '%count', $attempts ) )
         $sa_paragraphs = array( 'If it was you and you forgot your password, ask for a new one. If not, choose a stronger password and turn on two-step sign-in.'|i18n( 'design/standard/user/security_mail' ) )}
{/case}
{case match='locked'}
    {set $sa_intro = 'Your account %login on %site was locked after too many failed sign-ins.'|i18n( 'design/standard/user/security_mail',, hash( '%login', $user.login, '%site', $sa_site ) )
         $sa_paragraphs = array( 'Ask for a new password to sign in again, or contact the administrators of the site.'|i18n( 'design/standard/user/security_mail' ) )
         $sa_buttons = array( hash( 'label', 'Ask for a new password'|i18n( 'design/standard/user/security_mail' ), 'url', $password_url, 'kind', 'primary' ) )|merge( $sa_buttons )
         $sa_note = 'If the button does not work, copy this address into your browser: %url'|i18n( 'design/standard/mail',, hash( '%url', $password_url ) )}
{/case}
{case}
    {set $sa_intro = 'There was a security event on your account %login on %site.'|i18n( 'design/standard/user/security_mail',, hash( '%login', $user.login, '%site', $sa_site ) )}
{/case}
{/switch}
{if $ip|ne( '' )}
    {set $sa_rows = $sa_rows|append( hash( 'label', 'Approximate address'|i18n( 'design/standard/user/security_mail' ), 'value', $ip ) )}
{/if}
{set-block scope=root variable=subject}{switch match=$alert}{case match='twofactor_on'}{'Two-step sign-in was turned on'|i18n( 'design/standard/user/security_mail' )}{/case}{case match='twofactor_off'}{'Two-step sign-in was turned off'|i18n( 'design/standard/user/security_mail' )}{/case}{case match='recovery_used'}{'A recovery code was used'|i18n( 'design/standard/user/security_mail' )}{/case}{case match='passkey_added'}{'A passkey was added to your account'|i18n( 'design/standard/user/security_mail' )}{/case}{case match='passkey_removed'}{'A passkey was removed from your account'|i18n( 'design/standard/user/security_mail' )}{/case}{case match='failures'}{'Failed sign-in attempts on your account'|i18n( 'design/standard/user/security_mail' )}{/case}{case match='locked'}{'Your account was locked'|i18n( 'design/standard/user/security_mail' )}{/case}{case}{'Security alert'|i18n( 'design/standard/user/security_mail' )}{/case}{/switch} - {$sa_site}{/set-block}
{set-block scope=root variable=content_type}text/html{/set-block}
{set-block scope=root variable=body_text}{include uri='design:mail/layout_text.tpl'
    mail_title=$sa_title mail_intro=$sa_intro mail_rows=$sa_rows mail_paragraphs=$sa_paragraphs mail_notice=$sa_notice
    mail_buttons=$sa_buttons mail_outro=$sa_outro mail_site_name=$sa_site}{/set-block}
{include uri='design:mail/layout.tpl'
    mail_title=$sa_title mail_intro=$sa_intro mail_rows=$sa_rows mail_paragraphs=$sa_paragraphs mail_notice=$sa_notice
    mail_buttons=$sa_buttons mail_links_note=$sa_note mail_outro=$sa_outro mail_site_name=$sa_site}
{undef $sa_site $sa_title $sa_intro $sa_rows $sa_paragraphs $sa_notice $sa_buttons $sa_note $sa_outro}
