{* The activation mail sent again from the administration (user/unactivated, "Send the activation mail again").

   Variables: user (eZUser), object (its content object), hash (the new activation key), activation_url (the link,
   on the site's own address, not the administration's; shown with https), site_url (site.ini [SiteSettings]
   SiteURL). The subject, content_type (text/html here) and body_text (the plain-text part) are set in set-blocks,
   as in user/registrationinfo.tpl. The link of an earlier mail no longer works.

   The look is design:mail/layout.tpl (HTML) and design:mail/layout_text.tpl (text); this template only passes text
   and links (the layout washes them). *}
{def $ur_site_url = first_set( $site_url, ezini( 'SiteSettings', 'SiteURL' ) )
     $ur_url = first_set( $activation_url, '' )
     $ur_login = ''
     $ur_rows = array()
     $ur_buttons = array()
     $ur_more = array()
     $ur_note = ''}
{set-block scope=root variable=subject}{'Activate your account at %siteurl'|i18n( 'design/admin/user/unactivated',, hash( '%siteurl', $ur_site_url ) )}{/set-block}
{set-block scope=root variable=content_type}text/html{/set-block}
{if $ur_url|begins_with( 'http://' )}{set $ur_url = concat( 'https://', $ur_url|extract( 7 ) )}{/if}
{set $ur_login = concat( 'https://', $ur_site_url, '/user/login' )
     $ur_rows = array( hash( 'label', 'Username'|i18n( 'design/admin/user/unactivated' ), 'value', $user.login ),
                       hash( 'label', 'Email'|i18n( 'design/admin/user/unactivated' ), 'value', $user.email ) )
     $ur_buttons = array( hash( 'label', 'Activate account'|i18n( 'design/admin/user/unactivated' ), 'url', $ur_url, 'kind', 'primary' ) )
     $ur_more = array( hash( 'label', 'Sign in'|i18n( 'design/admin/user/unactivated' ), 'url', $ur_login ) )
     $ur_note = 'If the button does not work, copy this address into your browser: %url'|i18n( 'design/admin/user/unactivated', '', hash( '%url', $ur_url ) )}
{set-block scope=root variable=body_text}{include uri='design:mail/layout_text.tpl'
    mail_title='Activate your account'|i18n( 'design/admin/user/unactivated' )
    mail_intro='You registered at %siteurl but have not activated your account yet.'|i18n( 'design/admin/user/unactivated',, hash( '%siteurl', $ur_site_url ) )
    mail_rows=$ur_rows
    mail_notice='The link of any earlier activation mail no longer works. If you did not register, you can ignore this mail.'|i18n( 'design/admin/user/unactivated' )
    mail_buttons=$ur_buttons
    mail_links=$ur_more
    mail_links_note=$ur_note
    mail_footer='This mail was sent by %siteurl because this email address was used to register an account.'|i18n( 'design/admin/user/unactivated', '', hash( '%siteurl', $ur_site_url ) )}{/set-block}
{include uri='design:mail/layout.tpl'
    mail_title='Activate your account'|i18n( 'design/admin/user/unactivated' )
    mail_intro='You registered at %siteurl but have not activated your account yet.'|i18n( 'design/admin/user/unactivated',, hash( '%siteurl', $ur_site_url ) )
    mail_rows=$ur_rows
    mail_notice='The link of any earlier activation mail no longer works. If you did not register, you can ignore this mail.'|i18n( 'design/admin/user/unactivated' )
    mail_buttons=$ur_buttons
    mail_links=$ur_more
    mail_links_note=$ur_note
    mail_footer='This mail was sent by %siteurl because this email address was used to register an account.'|i18n( 'design/admin/user/unactivated', '', hash( '%siteurl', $ur_site_url ) )}
{undef $ur_site_url $ur_url $ur_login $ur_rows $ur_buttons $ur_more $ur_note}
