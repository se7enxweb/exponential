{* "Your password was changed" (expPasswordPolicy::sendChangedMail()), sent to the account's address after a change on
   user/password. Essential mail (category security). It never contains the password.
   Variables: user (eZUser), changed_at (timestamp), ip (the address the change came from, '' when not known),
   sessions_ended (how many other sessions were ended at once, null when the handler cannot),
   other_sessions_signed_out (the other sessions end on their next request),
   site_name (the public site). Sets subject, content_type (text/html) and body_text (the plain-text part).
   The look is design:mail/layout.tpl (HTML) and design:mail/layout_text.tpl (text); this template only passes text
   and links, which the layout washes. The reset link is named with the login page's own label (the same source and
   context as design:user/login.tpl), so the mail quotes exactly what the page says. *}
{def $pc_site = cond( is_set( $site_name ), $site_name, ezini( 'SiteSettings', 'SiteName' ) )
     $pc_forgot = 'user/forgotpassword'|ezurl( 'no', 'full' )
     $pc_paragraphs = array()
     $pc_done = 'If you made this change, you do not need to do anything.'|i18n( 'design/standard/user/password_changed_mail' )
     $pc_title = 'Your password was changed'|i18n( 'design/standard/user/password_changed_mail' )
     $pc_intro = 'The password of your account %login on %site was changed on %date.'|i18n( 'design/standard/user/password_changed_mail',,
                     hash( '%login', $user.login, '%site', $pc_site, '%date', $changed_at|l10n( 'shortdatetime' ) ) )
     $pc_notice = 'If you did not make this change, someone else may know your password: reset it at once with "%forgot" on the login page, and contact us.'|i18n( 'design/standard/user/password_changed_mail',,
                     hash( '%forgot', 'Forgot your password?'|i18n( 'design/standard/user' ) ) )
     $pc_buttons = array()
     $pc_note = ''}
{if $pc_forgot|begins_with( 'http://' )}{set $pc_forgot = concat( 'https://', $pc_forgot|extract( 7 ) )}{/if}
{set $pc_buttons = array( hash( 'label', 'Forgot your password?'|i18n( 'design/standard/user' ), 'url', $pc_forgot, 'kind', 'secondary' ) )
     $pc_note = 'If the button does not work, copy this address into your browser: %url'|i18n( 'design/standard/mail',, hash( '%url', $pc_forgot ) )}
{if $ip|ne( '' )}
    {set $pc_paragraphs = $pc_paragraphs|append( 'The change came from the address %ip.'|i18n( 'design/standard/user/password_changed_mail',, hash( '%ip', $ip ) ) )}
{/if}
{if or( and( is_set( $other_sessions_signed_out ), $other_sessions_signed_out ), and( is_set( $sessions_ended ), $sessions_ended|gt( 0 ) ) )}
    {set $pc_done = concat( $pc_done, ' ', 'You were signed out on your other devices; sign in there again with the new password.'|i18n( 'design/standard/user/password_changed_mail' ) )}
{/if}
{set $pc_paragraphs = $pc_paragraphs|append( $pc_done )}
{set-block scope=root variable=subject}{'Your password on %site was changed'|i18n( 'design/standard/user/password_changed_mail',, hash( '%site', $pc_site ) )}{/set-block}
{set-block scope=root variable=content_type}text/html{/set-block}
{set-block scope=root variable=body_text}{include uri='design:mail/layout_text.tpl'
    mail_title=$pc_title mail_intro=$pc_intro mail_paragraphs=$pc_paragraphs mail_notice=$pc_notice mail_buttons=$pc_buttons
    mail_site_name=$pc_site}{/set-block}
{include uri='design:mail/layout.tpl'
    mail_title=$pc_title mail_intro=$pc_intro mail_paragraphs=$pc_paragraphs mail_notice=$pc_notice mail_buttons=$pc_buttons
    mail_links_note=$pc_note mail_site_name=$pc_site}
{undef $pc_site $pc_forgot $pc_paragraphs $pc_done $pc_title $pc_intro $pc_notice $pc_buttons $pc_note}
