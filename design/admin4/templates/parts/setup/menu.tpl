{*
  To be able to define menu entries in ini at the same time as letting i18n
  system translate the names, we use a lookup table for translatable strings.
  If the ini name is not defined here (key), then LinkNames ini value is used.
  For extensions that need to extend this, you either have to override this
  template or let translations use LinkNames as described in menu.ini.

  The Setup overview (setup/setupmenu.tpl) includes this template with setup_menu_layout='tiles': the same links,
  names and access checks, drawn as tiles with the icons, descriptions and figures of menu.ini [Leftmenu_setup]
  (LinkIcons[], LinkDescriptions[], LinkFigureTemplates[]), without the width control.

  admin4's mega menu (setup/megamenu.tpl) sets setup_menu_layout='tiles' and setup_tiles_layout='mega': the same
  links, names, groups, icons and descriptions, drawn as the groups of the panel under the header, without figures.

  The groups Design and Audit stand for their top tabs (menu.ini [Leftmenu_setup] GroupTabs[], GroupSections[]):
  shown only while the tab is not in the top menu, with the links of [Leftmenu_design] and [Leftmenu_audit], named
  here as <group>-<key> with the strings of their own menus. The audit menu's links into channels the user may not
  read are left out as there (fetch( 'audit', 'hidden_menu_links' )).
*}
{if is_unset( $setup_menu_layout )}{def $setup_menu_layout = 'menu'}{/if}
{if is_unset( $setup_tiles_layout )}{def $setup_tiles_layout = ''}{/if}

{include uri='design:parts/ini_menu.tpl' ini_section='Leftmenu_setup'
    menu_layout=$setup_menu_layout
    tiles_layout=$setup_tiles_layout
    section_hidden_links=hash( 'Leftmenu_audit', fetch( 'audit', 'hidden_menu_links', hash( 'section', 'Leftmenu_audit' ) ) )
    i18n_hash=hash(
    'setup',              'Setup'|i18n( 'design/admin/parts/setup/menu' ),
    'overview',           'Overview'|i18n( 'design/admin/parts/setup/menu' ),
    'audit',              'Audit'|i18n( 'design/admin/parts/setup/menu' ),
    'cache',              'Cache management'|i18n( 'design/admin/parts/setup/menu' ),
    'classes',            'Classes'|i18n( 'design/admin/parts/setup/menu' ),
    'collected',          'Collected information'|i18n( 'design/admin/parts/setup/menu' ),
    'contentfinder',      'Content finder'|i18n( 'design/admin/parts/setup/menu' ),
    'extensions',         'Extensions'|i18n( 'design/admin/parts/setup/menu' ),
    'global_setting',     'Global settings'|i18n( 'design/admin/parts/setup/menu' ),
    'ini',                'Ini settings'|i18n( 'design/admin/parts/setup/menu' ),
    'languages',          'Languages'|i18n( 'design/admin/parts/setup/menu' ),
    'mailpreferences',    'E-mail preferences'|i18n( 'design/admin/parts/setup/menu' ),
    'notification',       'Notification'|i18n( 'design/admin/parts/setup/menu' ),
    'pdf_export',         'PDF export'|i18n( 'design/admin/parts/setup/menu', 'PDF export'),
    'packages',           'Packages'|i18n( 'design/admin/parts/setup/menu' ),
    'rad',                'RAD'|i18n( 'design/admin/parts/setup/menu', 'Rapid Application Development'),
    'roles_and_policies', 'Roles and policies'|i18n( 'design/admin/parts/setup/menu' ),
    'rss',                'RSS'|i18n( 'design/admin/parts/setup/menu' ),
    'search_statistics',  'Search statistics'|i18n( 'design/admin/parts/setup/menu' ),
    'sections',           'Sections'|i18n( 'design/admin/parts/setup/menu' ),
    'states',             'States'|i18n( 'design/admin/parts/setup/menu' ),
    'sessions',           'Sessions'|i18n( 'design/admin/parts/setup/menu' ),
    'sitedata',           'Site data'|i18n( 'design/admin/parts/setup/menu' ),
    'system_information', 'System information'|i18n( 'design/admin/parts/setup/menu' ),
    'upgrade_check',      'Upgrade check'|i18n( 'design/admin/parts/setup/menu' ),
    'triggers',           'Triggers'|i18n( 'design/admin/parts/setup/menu' ),
    'url_management',     'Link management'|i18n( 'design/admin/parts/setup/menu' ),
    'url_translator',     'URL translator'|i18n( 'design/admin/parts/setup/menu' ),
    'url_wildcards',      'URL wildcards'|i18n( 'design/admin/parts/setup/menu' ),
    'workflows',          'Workflows'|i18n( 'design/admin/parts/setup/menu' ),
    'workflow_processes', 'Workflow processes'|i18n( 'design/admin/parts/setup/menu' ),
    'maintenance',        'Maintenance'|i18n( 'design/admin/parts/setup/menu' ),
    'cronjobs',           'Cronjobs'|i18n( 'design/admin/parts/setup/menu' ),
    'preload',            'Preload Sites'|i18n( 'design/admin/parts/setup/menu' ),
    'oauth_list',         'oAuth admin'|i18n( 'design/admin/parts/setup/menu' ),
    'look_and_feel',      'Look and feel'|i18n( 'design/admin/parts/visual/menu' ),
    'template_editor',    'Template List / Template Editor'|i18n( 'design/admin/parts/visual/menu' ),
    'menu_management',    'Menus (classic)'|i18n( 'design/admin/parts/visual/menu' ),
    'toolbar_management', 'Toolbar management'|i18n( 'design/admin/parts/visual/menu' ),
    'templates',    'Templates'|i18n( 'design/admin/parts/visual/menu' ),
    'design-look_and_feel',      'Look and feel'|i18n( 'design/admin/parts/visual/menu' ),
    'design-template_editor',    'Template Editor'|i18n( 'design/admin/parts/visual/menu' ),
    'design-toolbar_management', 'Toolbar management'|i18n( 'design/admin/parts/visual/menu' ),
    'design-menu_management',    'Menus (classic)'|i18n( 'design/admin/parts/visual/menu' ),
    'design-templates',          'Templates'|i18n( 'design/admin/parts/visual/menu' ),
    'audit-dashboard', 'Dashboard'|i18n( 'design/admin/audit' ),
    'audit-console',   'Console'|i18n( 'design/admin/audit' ),
    'audit-recent',    'Recent events'|i18n( 'design/admin/audit' ),
    'audit-charts',    'Charts'|i18n( 'design/admin/audit' ),
    'audit-alerts',    'Alerts'|i18n( 'design/admin/audit' ),
    'audit-export',    'Export'|i18n( 'design/admin/audit' ),
    'audit-archives',  'Archives'|i18n( 'design/admin/audit' ),
    'audit-settings',  'Settings'|i18n( 'design/admin/audit' ),
)}

{* Left menu width control (not on the overview page, which draws the menu as tiles, nor in the mega menu). *}
{if $setup_menu_layout|ne( 'tiles' )}
{include uri='design:parts/left_width_control.tpl'}
{/if}
