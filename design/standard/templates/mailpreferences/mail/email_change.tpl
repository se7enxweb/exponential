{* The confirmation of a new e-mail address (expMailPreferencesService::requestEmailChange()): sent to the new address;
   the account keeps its old address until the link is used. Essential mail.
   Variables: email (the new address), confirm_url (full address), expires (timestamp), kind ('email_change').
   Sets $subject. *}
{def $mp_site = ezini( 'SiteSettings', 'SiteName' )}
{set-block scope=root variable=subject}{'Confirm your new e-mail address for %site'|i18n( 'design/standard/mailpreferences',, hash( '%site', $mp_site ) )}{/set-block}
{'Hello,'|i18n( 'design/standard/mailpreferences' )}

{'Someone asked to use %email for an account on %site. To confirm that this is your address, open this link and press the button:'|i18n( 'design/standard/mailpreferences',, hash( '%email', $email, '%site', $mp_site ) )}
{$confirm_url}

{'The link works until %date.'|i18n( 'design/standard/mailpreferences',, hash( '%date', $expires|l10n( 'shortdatetime' ) ) )}
{'If you did not ask for this, ignore this e-mail: the account keeps its address.'|i18n( 'design/standard/mailpreferences' )}
{undef $mp_site}
