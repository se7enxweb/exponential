{* The activation mail sent again from the administration (user/unactivated, "Send the activation mail again").

   Variables: user (eZUser), object (its content object), hash (the new activation key), activation_url (the link,
   on the site's own address, not the administration's), site_url (site.ini [SiteSettings] SiteURL). The subject is
   set in a set-block, as in user/registrationinfo.tpl. The link of an earlier mail no longer works. *}
{set-block scope=root variable=subject}{'Activate your account at %siteurl'|i18n( 'design/admin/user/unactivated',, hash( '%siteurl', $site_url ) )}{/set-block}
{'You registered at %siteurl but have not activated your account yet.'|i18n( 'design/admin/user/unactivated',, hash( '%siteurl', $site_url ) )}

{'Username'|i18n( 'design/admin/user/unactivated' )}: {$user.login}

{'Click the following address to activate your account:'|i18n( 'design/admin/user/unactivated' )}
{$activation_url}

{'The link of any earlier activation mail no longer works. If you did not register, you can ignore this mail.'|i18n( 'design/admin/user/unactivated' )}
