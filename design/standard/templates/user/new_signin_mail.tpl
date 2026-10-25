{* "New sign-in to your account" (expUserSessionTracker::sendUserMail()), sent to the account's address when it signs
   in from a browser, system and device kind it has not used lately. Essential mail (category security); the user
   turns it off on "Your devices" (user/sessions). Never a password, a token or the full address.
   Variables: user (eZUser), device ("Firefox 131 on Windows 10/11"), device_type, ip (approximate: 203.0.113.x),
   signed_in_at (timestamp), method, sessions_url (absolute), site_name. Sets $subject.
   The paragraphs are joined by explicit {"\n"}: a line break next to {if} / {/if} is dropped by the template engine. *}
{def $ns_site = cond( is_set( $site_name ), $site_name, ezini( 'SiteSettings', 'SiteName' ) )}
{set-block scope=root variable=subject}{'New sign-in to your account on %site'|i18n( 'design/standard/user/new_signin_mail',, hash( '%site', $ns_site ) )}{/set-block}
{'Hello,'|i18n( 'design/standard/user/new_signin_mail' )}

{'Your account %login on %site was just used to sign in from a device or browser it has not used lately:'|i18n( 'design/standard/user/new_signin_mail',,
    hash( '%login', $user.login, '%site', $ns_site ) )}

  {'Device:'|i18n( 'design/standard/user/new_signin_mail' )} {$device}
  {'Time:'|i18n( 'design/standard/user/new_signin_mail' )} {$signed_in_at|l10n( 'shortdatetime' )}{if $ip|ne( '' )}{"\n"}  {'Approximate address:'|i18n( 'design/standard/user/new_signin_mail' )} {$ip}{/if}{"\n\n"}{'If this was you, you do not need to do anything.'|i18n( 'design/standard/user/new_signin_mail' )}{"\n\n"}{'If it was not you, someone may know your password. Sign that session out on the page of your devices, then change your password:'|i18n( 'design/standard/user/new_signin_mail' )}
{$sessions_url}

{'You can turn these e-mails off on the same page.'|i18n( 'design/standard/user/new_signin_mail' )}
{undef $ns_site}
