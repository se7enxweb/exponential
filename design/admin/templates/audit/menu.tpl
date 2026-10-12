{* The left menu of the audit views (menu.ini [Leftmenu_audit]); each link is shown only to a user who can open it.
   A link into a channel the user may not read under the Channel limitation of audit/read (an extension source's
   "AI events" for a user limited to other channels) is left out: fetch( 'audit', 'hidden_menu_links' ). *}
{include uri='design:parts/ini_menu.tpl' ini_section='Leftmenu_audit'
         hidden_links=fetch( 'audit', 'hidden_menu_links', hash( 'section', 'Leftmenu_audit' ) )
         i18n_hash=hash(
    'audit',    'Audit'|i18n( 'design/admin/audit' ),
    'dashboard', 'Dashboard'|i18n( 'design/admin/audit' ),
    'console',  'Console'|i18n( 'design/admin/audit' ),
    'recent',   'Recent events'|i18n( 'design/admin/audit' ),
    'charts',   'Charts'|i18n( 'design/admin/audit' ),
    'alerts',   'Alerts'|i18n( 'design/admin/audit' ),
    'export',   'Export'|i18n( 'design/admin/audit' ),
    'archives', 'Archives'|i18n( 'design/admin/audit' ),
    'settings', 'Settings'|i18n( 'design/admin/audit' ),
)}
