{* The Dashboard tab's mega menu ([Topmenu_dashboard] MegaMenuTemplate; user/megamenu.tpl): menu.ini [Leftmenu_my_mega]
   through parts/ini_menu.tpl, drawn as the mega menu's groups (menu_layout='tiles', tiles_layout='mega'), with the
   choice of its MegaMenuLinks[]. The section has no links of its own: its groups add [Leftmenu_my] (with the links
   extensions append there, Exponential Intelligence among them), [Leftmenu_design] and [Leftmenu_audit]; their
   links are named here as <group>-<key>, with the strings of their own menus. A link with LinkNames[] (an
   extension's) takes that name. The audit menu's links into channels the user may not read are left out, as in the
   audit menu (fetch( 'audit', 'hidden_menu_links' )). *}
{include uri='design:parts/ini_menu.tpl' ini_section='Leftmenu_my_mega'
    menu_layout='tiles'
    tiles_layout='mega'
    section_hidden_links=hash( 'Leftmenu_audit', fetch( 'audit', 'hidden_menu_links', hash( 'section', 'Leftmenu_audit' ) ) )
    i18n_hash=hash(
    'overview-dashboard',       'Dashboard'|i18n( 'design/admin/parts/my/menu' ),
    'work-my_drafts',           'My drafts'|i18n( 'design/admin/parts/my/menu' ),
    'work-my_pending',          'My pending items'|i18n( 'design/admin/parts/my/menu' ),
    'work-collaboration',       'Collaboration'|i18n( 'design/admin/parts/my/menu' ),
    'work-my_bookmarks',        'My bookmarks'|i18n( 'design/admin/parts/my/menu' ),
    'activity-console',         'Audit trail'|i18n( 'design/admin/parts/my/menu' ),
    'account-my_notifications', 'My notification settings'|i18n( 'design/admin/parts/my/menu' ),
    'account-my_mail',          'My e-mail preferences'|i18n( 'design/admin/parts/my/menu' ),
    'account-change_password',  'Change password'|i18n( 'design/admin/parts/my/menu' ),
    'account-edit_profile',     'Edit profile'|i18n( 'design/admin/parts/my/menu' ),
)}
