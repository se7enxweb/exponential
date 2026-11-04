{* The left menu of the help views (menu.ini [Leftmenu_help]), drawn instead of the Setup menu while the Setup tab
   stays marked; each link is shown only to a user who can open it. The names are the help's own (kernel/exphelp). *}
{include uri='design:parts/ini_menu.tpl' ini_section='Leftmenu_help'
         i18n_hash=hash(
    'help',       'Help'|i18n( 'kernel/exphelp' ),
    'dashboard',  'Help Start'|i18n( 'kernel/exphelp' ),
    'contents',   'Contents'|i18n( 'kernel/exphelp' ),
    'search',     'Search the help'|i18n( 'kernel/exphelp' ),
    'extensions', 'Extensions'|i18n( 'kernel/exphelp' ),
    'report',     'Report an error'|i18n( 'kernel/exphelp' ),
)}
