{* The double opt-in e-mail (expMailPreferencesService::sendConfirmation()): sent when a person turns on a category that
   needs a confirmation (newsletters, marketing). It is sent as essential mail, even when optional e-mail is off.
   Variables: category (expMailCategory), email, confirm_url (full address), expires (timestamp), kind ('category').
   Sets $subject. *}
{def $mp_name = $category.name|i18n( 'kernel/mailpreferences/categories' )
     $mp_site = cond( is_set( $site_name ), $site_name, ezini( 'SiteSettings', 'SiteName' ) )
     $mp_description = cond( $category.description|ne( '' ), $category.description|i18n( 'kernel/mailpreferences/categories' ), '' )}
{set-block scope=root variable=subject}{'Please confirm: %category from %site'|i18n( 'design/standard/mailpreferences',, hash( '%category', $mp_name, '%site', $mp_site ) )}{/set-block}
{'Hello,'|i18n( 'design/standard/mailpreferences' )}

{'You asked to receive "%category" from %site at %email.'|i18n( 'design/standard/mailpreferences',, hash( '%category', $mp_name, '%site', $mp_site, '%email', $email ) )}

{$mp_description}

{'To confirm, open this link and press the button:'|i18n( 'design/standard/mailpreferences' )}
{$confirm_url}

{'The link works until %date.'|i18n( 'design/standard/mailpreferences',, hash( '%date', $expires|l10n( 'shortdatetime' ) ) )}
{'If you did not ask for this, ignore this e-mail: nothing changes without your confirmation, and we will not write again about it.'|i18n( 'design/standard/mailpreferences' )}
{undef $mp_name $mp_site $mp_description}
