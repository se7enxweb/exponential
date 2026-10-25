{* The note to the administrators about a sign-in from a new device (expUserSessionTracker::sendAdminMail()), when
   site.ini [UserSessionSettings] NewDeviceAdminMail=enabled. Category admin.
   Variables: those of user/new_signin_mail.tpl, and admin_url (the user's sessions page, absolute). Sets $subject. *}
{def $na_site = cond( is_set( $site_name ), $site_name, ezini( 'SiteSettings', 'SiteName' ) )}
{set-block scope=root variable=subject}{'New sign-in of %login on %site'|i18n( 'design/standard/user/new_signin_mail',, hash( '%login', $user.login, '%site', $na_site ) )}{/set-block}
{'The account %login signed in from a device or browser it has not used lately.'|i18n( 'design/standard/user/new_signin_mail',, hash( '%login', $user.login ) )}

  {'Device:'|i18n( 'design/standard/user/new_signin_mail' )} {$device}
  {'Time:'|i18n( 'design/standard/user/new_signin_mail' )} {$signed_in_at|l10n( 'shortdatetime' )}{if $ip|ne( '' )}{"\n"}  {'Approximate address:'|i18n( 'design/standard/user/new_signin_mail' )} {$ip}{/if}{"\n\n"}{'The sessions of this user:'|i18n( 'design/standard/user/new_signin_mail' )}
{$admin_url}
{undef $na_site}
