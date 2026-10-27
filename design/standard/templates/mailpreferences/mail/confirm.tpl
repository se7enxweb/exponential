{* The double opt-in e-mail (expMailPreferencesService::sendConfirmation()): sent when a person turns on a category that
   needs a confirmation (newsletters, marketing). It is sent as essential mail, even when optional e-mail is off.
   Variables: category (expMailCategory), email, confirm_url (full address), expires (timestamp), kind ('category'),
   site_name. Sets subject, content_type (text/html) and body_text (the plain-text part).
   The look is design:mail/layout.tpl (HTML) and design:mail/layout_text.tpl (text); this template only passes text
   and links, which the layout washes. The button opens the confirmation page, which asks once more. *}
{def $mp_name = $category.name|i18n( 'kernel/mailpreferences/categories' )
     $mp_site = cond( is_set( $site_name ), $site_name, ezini( 'SiteSettings', 'SiteName' ) )
     $mp_description = cond( $category.description|ne( '' ), $category.description|i18n( 'kernel/mailpreferences/categories' ), '' )
     $mp_subject = 'Please confirm: %category from %site'|i18n( 'design/standard/mailpreferences',, hash( '%category', $mp_name, '%site', $mp_site ) )
     $mp_intro = 'You asked to receive "%category" from %site at %email.'|i18n( 'design/standard/mailpreferences',, hash( '%category', $mp_name, '%site', $mp_site, '%email', $email ) )
     $mp_buttons = array( hash( 'label', 'Confirm your subscription'|i18n( 'design/standard/mailpreferences' ), 'url', $confirm_url, 'kind', 'primary' ) )
     $mp_notice = 'The link works until %date.'|i18n( 'design/standard/mailpreferences',, hash( '%date', $expires|l10n( 'shortdatetime' ) ) )
     $mp_note = 'If the button does not work, copy this address into your browser: %url'|i18n( 'design/standard/mail',, hash( '%url', $confirm_url ) )
     $mp_outro = array( 'If you did not ask for this, ignore this e-mail: nothing changes without your confirmation, and we will not write again about it.'|i18n( 'design/standard/mailpreferences' ) )}
{set-block scope=root variable=subject}{$mp_subject}{/set-block}
{set-block scope=root variable=content_type}text/html{/set-block}
{set-block scope=root variable=body_text}{include uri='design:mail/layout_text.tpl'
    mail_title=$mp_subject mail_intro=$mp_intro mail_paragraphs=array( $mp_description ) mail_notice=$mp_notice
    mail_buttons=$mp_buttons mail_outro=$mp_outro mail_site_name=$mp_site}{/set-block}
{include uri='design:mail/layout.tpl'
    mail_title=$mp_subject mail_intro=$mp_intro mail_paragraphs=array( $mp_description ) mail_notice=$mp_notice
    mail_buttons=$mp_buttons mail_links_note=$mp_note mail_outro=$mp_outro mail_site_name=$mp_site}
{undef $mp_name $mp_site $mp_description $mp_subject $mp_intro $mp_buttons $mp_notice $mp_note $mp_outro}
