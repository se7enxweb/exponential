{* The "send me a link" e-mail (expMailPreferencesService::requestLink()): a personal link to the preference page, for
   someone without an account or not signed in. Essential mail. Variables: email, manage_url (full address with a
   signed token), expires (timestamp). Sets $subject. *}
{def $mp_site = cond( is_set( $site_name ), $site_name, ezini( 'SiteSettings', 'SiteName' ) )}
{set-block scope=root variable=subject}{'Your link to manage e-mail from %site'|i18n( 'design/standard/mailpreferences',, hash( '%site', $mp_site ) )}{/set-block}
{'Hello,'|i18n( 'design/standard/mailpreferences' )}

{'You (or someone who typed your address) asked for a link to manage the e-mail %site sends to %email. Open it to see every kind of e-mail, turn each on or off, or stop all optional e-mail:'|i18n( 'design/standard/mailpreferences',, hash( '%site', $mp_site, '%email', $email ) )}
{$manage_url}

{'The link works until %date. Please do not forward this e-mail: the link lets anyone change your preferences.'|i18n( 'design/standard/mailpreferences',, hash( '%date', $expires|l10n( 'shortdatetime' ) ) )}
{'If you did not ask for it, you can ignore this e-mail; nothing has changed.'|i18n( 'design/standard/mailpreferences' )}
{undef $mp_site}
