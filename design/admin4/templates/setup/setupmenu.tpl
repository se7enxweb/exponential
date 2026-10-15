{* Setup > Overview (setup/menu, the Setup tab's first page): every function of the Setup menu as a tile with a
   Bootstrap icon, its name and one line on what it does, in the groups of the left menu.

   The links, their names, their groups and the access checks are the Setup menu's own (menu.ini [Leftmenu_setup],
   parts/setup/menu.tpl, parts/ini_menu.tpl): a function the user may not open has no tile, and a link an extension
   adds to the menu shows under "More" with the plug icon. This template adds only the icon and the description of
   each known link, and the figures of setup/setupmenu_figure.tpl. Guide: doc/features/6.0/admin4-design.md *}
{def $setup_menu_layout = 'tiles'
     $setup_menu_figures = 'design:setup/setupmenu_figure.tpl'
     $setup_menu_tiles = hash(
    'audit',              hash( 'icon', 'a4-bi-journal-check',     'text', 'Who changed what: the audit settings and archives.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'cache',              hash( 'icon', 'a4-bi-arrow-clockwise',   'text', 'Clear or regenerate the caches.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'classes',            hash( 'icon', 'a4-bi-diagram-3',         'text', 'The content classes and their attributes.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'collected',          hash( 'icon', 'a4-bi-inbox',             'text', 'What visitors sent through forms.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'contentfinder',      hash( 'icon', 'a4-bi-search',            'text', 'Find content by its id, remote id or path.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'cronjobs',           hash( 'icon', 'a4-bi-clock',             'text', 'The cronjob parts, their scripts and last runs.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'extensions',         hash( 'icon', 'a4-bi-puzzle',            'text', 'Activate and deactivate extensions.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'global_setting',     hash( 'icon', 'a4-bi-globe',             'text', 'The settings of the whole site.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'ini',                hash( 'icon', 'a4-bi-sliders',           'text', 'Read and edit the INI settings.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'languages',          hash( 'icon', 'a4-bi-translate',         'text', 'The languages content is written in.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'look_and_feel',      hash( 'icon', 'a4-bi-palette',           'text', 'The look of the site.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'mailpreferences',    hash( 'icon', 'a4-bi-envelope-paper',    'text', 'E-mail categories, suppressions and consents.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'maintenance',        hash( 'icon', 'a4-bi-wrench-adjustable', 'text', 'Run the maintenance tasks of the installation.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'menu_management',    hash( 'icon', 'a4-bi-menu-button-wide',  'text', 'The menus of the classic site designs.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'notification',       hash( 'icon', 'a4-bi-bell',              'text', 'Pending notifications and the last runs.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'oauth_list',         hash( 'icon', 'a4-bi-key',               'text', 'The OAuth clients and their access.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'packages',           hash( 'icon', 'a4-bi-box-seam',          'text', 'Import, export and install packages.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'pdf_export',         hash( 'icon', 'a4-bi-file-earmark-pdf',  'text', 'Export parts of the content tree as PDF.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'preload',            hash( 'icon', 'a4-bi-speedometer2',      'text', 'Warm the caches of the sites.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'rad',                hash( 'icon', 'a4-bi-magic',             'text', 'Wizards for template operators and datatypes.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'roles_and_policies', hash( 'icon', 'a4-bi-person-lock',       'text', 'Who may do what, and where.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'rss',                hash( 'icon', 'a4-bi-rss',               'text', 'RSS imports and exports.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'search_statistics',  hash( 'icon', 'a4-bi-bar-chart-line',    'text', 'What visitors searched for.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'sections',           hash( 'icon', 'a4-bi-collection',        'text', 'Sections group content for access and design.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'sessions',           hash( 'icon', 'a4-bi-people',            'text', 'Who is signed in; end sessions.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'sitedata',           hash( 'icon', 'a4-bi-table',             'text', 'The site data rows, read only.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'states',             hash( 'icon', 'a4-bi-toggles',           'text', 'Object state groups and their states.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'system_information', hash( 'icon', 'a4-bi-info-circle',       'text', 'PHP, database and server, with health checks.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'templates',          hash( 'icon', 'a4-bi-file-earmark-code', 'text', 'The templates of the designs and their overrides.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'template_editor',    hash( 'icon', 'a4-bi-code-square',       'text', 'List and edit templates.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'toolbar_management', hash( 'icon', 'a4-bi-tools',             'text', 'The toolbars and their tools.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'triggers',           hash( 'icon', 'a4-bi-lightning',         'text', 'Which workflow runs on which event.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'upgrade_check',      hash( 'icon', 'a4-bi-arrow-up-circle',   'text', 'Compare files and database with this release.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'url_management',     hash( 'icon', 'a4-bi-link-45deg',        'text', 'Check the links in the content.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'url_translator',     hash( 'icon', 'a4-bi-signpost-split',    'text', 'Custom URL aliases and forwards.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'url_wildcards',      hash( 'icon', 'a4-bi-asterisk',          'text', 'URL patterns that redirect or forward.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'workflow_processes', hash( 'icon', 'a4-bi-hourglass-split',   'text', 'Workflows that are running or waiting.'|i18n( 'design/admin/setup/setupmenu' ) ),
    'workflows',          hash( 'icon', 'a4-bi-diagram-2',         'text', 'Workflow groups, workflows and their events.'|i18n( 'design/admin/setup/setupmenu' ) )
    )}

<div class="a4-page a4-setup-overview">

    <div class="a4-pagehead">
        <div class="a4-pagehead-main">
            <h1 class="a4-pagehead-title">{'Setup'|i18n( 'design/admin/setup/setupmenu' )}</h1>
            <p class="a4-pagehead-sub">{'Configure the installation: content model, site and URLs, workflows, users and the system.'|i18n( 'design/admin/setup/setupmenu' )}</p>
        </div>
    </div>

    {include uri='design:parts/setup/menu.tpl'}

</div>
{undef $setup_menu_layout $setup_menu_figures $setup_menu_tiles}
