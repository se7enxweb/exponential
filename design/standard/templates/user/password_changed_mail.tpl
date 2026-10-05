{* "Your password was changed" (expPasswordPolicy::sendChangedMail()), sent to the account's address after a change on
   user/password. Essential mail (category security). It never contains the password.
   Variables: user (eZUser), changed_at (timestamp), ip (the address the change came from, '' when not known),
   sessions_ended (how many other sessions were ended at once, null when the handler cannot),
   other_sessions_signed_out (the other sessions end on their next request),
   site_name (the public site). Sets $subject. *}
{def $pc_site = cond( is_set( $site_name ), $site_name, ezini( 'SiteSettings', 'SiteName' ) )}
{set-block scope=root variable=subject}{'Your password on %site was changed'|i18n( 'design/standard/user/password_changed_mail',, hash( '%site', $pc_site ) )}{/set-block}
{'Hello,'|i18n( 'design/standard/user/password_changed_mail' )}

{'The password of your account %login on %site was changed on %date.'|i18n( 'design/standard/user/password_changed_mail',, hash( '%login', $user.login, '%site', $pc_site, '%date', $changed_at|l10n( 'shortdatetime' ) ) )}
{if $ip|ne( '' )}{'The change came from the address %ip.'|i18n( 'design/standard/user/password_changed_mail',, hash( '%ip', $ip ) )}{"\n"}{/if}

{'If you made this change, you do not need to do anything.'|i18n( 'design/standard/user/password_changed_mail' )}{if or( and( is_set( $other_sessions_signed_out ), $other_sessions_signed_out ), and( is_set( $sessions_ended ), $sessions_ended|gt( 0 ) ) )} {'You were signed out on your other devices; sign in there again with the new password.'|i18n( 'design/standard/user/password_changed_mail' )}{/if}

{'If you did not make this change, someone else may know your password: reset it at once with "Forgot your password?" on the login page, and contact us.'|i18n( 'design/standard/user/password_changed_mail' )}
{undef $pc_site}
