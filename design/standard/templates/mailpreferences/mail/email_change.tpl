{* The confirmation of a new e-mail address (expMailPreferencesService::requestEmailChange()): sent to the new address;
   the account keeps its old address until the link is used. Essential mail.
   Variables: email (the new address), confirm_url (full address), expires (timestamp), kind ('email_change'),
   site_name. Sets subject, content_type (text/html) and body_text (the plain-text part).
   The look is design:mail/layout.tpl (HTML) and design:mail/layout_text.tpl (text); this template only passes text
   and links, which the layout washes. The button opens the confirmation page, which asks once more. *}
{def $mp_site = cond( is_set( $site_name ), $site_name, ezini( 'SiteSettings', 'SiteName' ) )
     $mp_subject = 'Confirm your new e-mail address for %site'|i18n( 'design/standard/mailpreferences',, hash( '%site', $mp_site ) )
     $mp_intro = 'Someone asked to use %email for an account on %site. Please confirm that this address is yours.'|i18n( 'design/standard/mailpreferences',, hash( '%email', $email, '%site', $mp_site ) )
     $mp_buttons = array( hash( 'label', 'Confirm your e-mail address'|i18n( 'design/standard/mailpreferences' ), 'url', $confirm_url, 'kind', 'primary' ) )
     $mp_notice = 'The link works until %date.'|i18n( 'design/standard/mailpreferences',, hash( '%date', $expires|l10n( 'shortdatetime' ) ) )
     $mp_note = 'If the button does not work, copy this address into your browser: %url'|i18n( 'design/standard/mail',, hash( '%url', $confirm_url ) )
     $mp_outro = array( 'If you did not ask for this, ignore this e-mail: the account keeps its address.'|i18n( 'design/standard/mailpreferences' ) )}
{set-block scope=root variable=subject}{$mp_subject}{/set-block}
{set-block scope=root variable=content_type}text/html{/set-block}
{set-block scope=root variable=body_text}{include uri='design:mail/layout_text.tpl'
    mail_title=$mp_subject mail_intro=$mp_intro mail_notice=$mp_notice mail_buttons=$mp_buttons mail_outro=$mp_outro
    mail_site_name=$mp_site}{/set-block}
{include uri='design:mail/layout.tpl'
    mail_title=$mp_subject mail_intro=$mp_intro mail_notice=$mp_notice mail_buttons=$mp_buttons mail_links_note=$mp_note
    mail_outro=$mp_outro mail_site_name=$mp_site}
{undef $mp_site $mp_subject $mp_intro $mp_buttons $mp_notice $mp_note $mp_outro}
