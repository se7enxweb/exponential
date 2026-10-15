{* The Users tab's mega menu ([Topmenu_users] MegaMenuTemplate; user/megamenu.tpl): menu.ini [Leftmenu_user_mega]
   through parts/ini_menu.tpl, drawn as the mega menu's groups (menu_layout='tiles', tiles_layout='mega'), with the
   choice of its MegaMenuLinks[]. The section adds [Leftmenu_user] and [Leftmenu_my] to two of its groups; their
   links are named here as <group>-<key>, with the strings of their own menus. *}
{include uri='design:parts/ini_menu.tpl' ini_section='Leftmenu_user_mega'
    menu_layout='tiles'
    tiles_layout='mega'
    i18n_hash=hash(
    'accounts',                  'Users and user groups'|i18n( 'design/admin/parts/user/menu' ),
    'sessions',                  'Sessions'|i18n( 'design/admin/parts/setup/menu' ),
    'access-roles_and_policies', 'Roles and policies'|i18n( 'design/admin/parts/user/menu' ),
    'access-unactivated',        'Unactivated users'|i18n( 'design/admin/parts/user/menu' ),
    'access-user_hygiene',       'Check user accounts'|i18n( 'design/admin/parts/user/menu' ),
    'account-my_notifications',  'My notification settings'|i18n( 'design/admin/parts/my/menu' ),
    'account-my_mail',           'My e-mail preferences'|i18n( 'design/admin/parts/my/menu' ),
    'account-change_password',   'Change password'|i18n( 'design/admin/parts/my/menu' ),
    'account-edit_profile',      'Edit profile'|i18n( 'design/admin/parts/my/menu' ),
)}
