{* The "send me a link" e-mail (expMailPreferencesService::requestLink()): a personal link to the preference page, for
   someone without an account or not signed in. Essential mail. Variables: email, manage_url (full address with a
   signed token), expires (timestamp), site_name.
   Sets subject, content_type (text/html) and body_text (the plain-text part).
   The look is design:mail/layout.tpl (HTML) and design:mail/layout_text.tpl (text); this template only passes text
   and links, which the layout washes. *}
{def $mp_site = cond( is_set( $site_name ), $site_name, ezini( 'SiteSettings', 'SiteName' ) )
     $mp_subject = 'Your link to manage e-mail from %site'|i18n( 'design/standard/mailpreferences',, hash( '%site', $mp_site ) )
     $mp_intro = 'You (or someone who typed your address) asked for a link to manage the e-mail %site sends to %email. On that page you can turn each kind of e-mail on or off, or stop all optional e-mail.'|i18n( 'design/standard/mailpreferences',, hash( '%site', $mp_site, '%email', $email ) )
     $mp_buttons = array( hash( 'label', 'Manage my e-mail preferences'|i18n( 'design/standard/mailpreferences' ), 'url', $manage_url, 'kind', 'primary' ) )
     $mp_notice = 'The link works until %date. Please do not forward this e-mail: the link lets anyone change your preferences.'|i18n( 'design/standard/mailpreferences',, hash( '%date', $expires|l10n( 'shortdatetime' ) ) )
     $mp_note = 'If the button does not work, copy this address into your browser: %url'|i18n( 'design/standard/mail',, hash( '%url', $manage_url ) )
     $mp_outro = array( 'If you did not ask for it, you can ignore this e-mail; nothing has changed.'|i18n( 'design/standard/mailpreferences' ) )}
{set-block scope=root variable=subject}{$mp_subject}{/set-block}
{set-block scope=root variable=content_type}text/html{/set-block}
{set-block scope=root variable=body_text}{include uri='design:mail/layout_text.tpl'
    mail_title=$mp_subject mail_intro=$mp_intro mail_notice=$mp_notice mail_buttons=$mp_buttons mail_outro=$mp_outro
    mail_site_name=$mp_site}{/set-block}
{include uri='design:mail/layout.tpl'
    mail_title=$mp_subject mail_intro=$mp_intro mail_notice=$mp_notice mail_buttons=$mp_buttons mail_links_note=$mp_note
    mail_outro=$mp_outro mail_site_name=$mp_site}
{undef $mp_site $mp_subject $mp_intro $mp_buttons $mp_notice $mp_note $mp_outro}
