{* The note to the old address when the e-mail address of an account is being changed
   (expMailAddressChange::noticeToOldAddress(); mailpreferences.ini [EmailChangeSettings] NotifyOldAddress). Essential
   mail (category security). It names the new address only masked (a***@example.com).
   Variables: user (eZUser), new_email_masked, site_name.
   Sets subject, content_type (text/html) and body_text (the plain-text part); the texts are those of the built-in
   mail (context kernel/mailpreferences/mail), so a site without this template sends the same words as plain text.
   The look is design:mail/layout.tpl (HTML) and design:mail/layout_text.tpl (text); the layout washes every value. *}
{def $mn_site = cond( is_set( $site_name ), $site_name, ezini( 'SiteSettings', 'SiteName' ) )
     $mn_subject = 'The e-mail address of your account is being changed'|i18n( 'kernel/mailpreferences/mail' )
     $mn_intro = 'Someone asked to change the e-mail address of your account to %email. The change takes effect only when it is confirmed with the link sent to the new address; until then your account keeps this address.'|i18n( 'kernel/mailpreferences/mail',, hash( '%email', $new_email_masked ) )
     $mn_notice = 'If you did not ask for this, change your password and contact us.'|i18n( 'kernel/mailpreferences/mail' )}
{set-block scope=root variable=subject}{$mn_subject}{/set-block}
{set-block scope=root variable=content_type}text/html{/set-block}
{set-block scope=root variable=body_text}{include uri='design:mail/layout_text.tpl'
    mail_title=$mn_subject mail_intro=$mn_intro mail_notice=$mn_notice mail_site_name=$mn_site}{/set-block}
{include uri='design:mail/layout.tpl'
    mail_title=$mn_subject mail_intro=$mn_intro mail_notice=$mn_notice mail_site_name=$mn_site}
{undef $mn_site $mn_subject $mn_intro $mn_notice}
