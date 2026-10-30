<?php
/**
 * File containing the expHelpAudience class.
 *
 * Who a help page is for, which section of the help it belongs to and what kind of page it is
 * (doc/eng-us/features/6.0/help-system.md, "Audiences, categories and types"). For each of the three the first answer wins:
 *
 * 1. the page's own front matter (audience:, category:, type:), see expHelpFrontMatter;
 * 2. the path rules of exphelp.ini: [AudienceSettings] PathRules[], [CategorySettings] PathRules[] and
 *    [TypeSettings] PathRules[], each "<pattern>;<value>" with a shell pattern on the path relative to the
 *    installation root ("*" also matches "/"), the first matching rule wins;
 * 3. for the audience only, the file name (getting-started-editor.md, mail-preferences-developer.md) and the
 *    opening lines of the page ("for editors", "administrators", "developers", "end users");
 * 4. the default of the page's type: [AudienceSettings] TypeDefaults[<type>] and [CategorySettings] TypeDefaults[<type>].
 *
 * The audiences are editor, admin, developer, user, project-manager, sales-marketing, partner, advocate, designer,
 * hosting, app-creator, website-creator, supporter, sponsor, trainer, press, businesses, government, customer and the
 * audiences of #645 (community, ecosystem, php-developer ... student) ([AudienceSettings] Audiences[]);
 * the categories and their names and icons are [CategorySettings] Categories[], CategoryNames[] and CategoryIcons[]. An extension adds rules
 * and categories in its own exphelp.ini.append.php.
 *
 * It also gives pages whose own title is unclear a clearer title and summary ([TitleSettings], pageTitle() and
 * pageSummary()); a page's front matter title and summary always win.
 *
 * And it gives pages their place in the page tree, the folder lists and the category lists where the settings name
 * one ([TreeSettings] PageOrder[<page id>], pageOrder()): this wins over a page's front matter order, so pages that
 * cannot carry front matter (the project files, plain text) can be placed too.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpAudience
{
    const EDITOR = 'editor';
    const ADMIN = 'admin';
    const DEVELOPER = 'developer';
    const USER = 'user';
    const PROJECT_MANAGER = 'project-manager';
    const SALES_MARKETING = 'sales-marketing';
    const PARTNER = 'partner';
    const ADVOCATE = 'advocate';
    const DESIGNER = 'designer';
    const HOSTING = 'hosting';
    const APP_CREATOR = 'app-creator';
    const WEBSITE_CREATOR = 'website-creator';
    const SUPPORTER = 'supporter';
    const SPONSOR = 'sponsor';
    const CUSTOMER = 'customer';
    const GOVERNMENT = 'government';
    const BUSINESSES = 'businesses';
    const PRESS = 'press';
    const TRAINER = 'trainer';
    const COMMUNITY = 'community';
    const ECOSYSTEM = 'ecosystem';
    const PHP_DEVELOPER = 'php-developer';
    const THEME_DESIGNER = 'theme-designer';
    const RESELLER = 'reseller';
    const BUNDLER = 'bundler';
    const AI_AGENTS = 'ai-agents';
    const SOFTWARE_ENGINEER = 'software-engineer';
    const ADOPTER = 'adopter';
    const MOBILE_APP_DEVELOPER = 'mobile-app-developer';
    const WEB_FRAMEWORK_DEVELOPER = 'web-framework-developer';
    const CMS_DEVELOPER = 'cms-developer';
    const DXP_DEVELOPER = 'dxp-developer';
    const ECOMMERCE = 'ecommerce';
    const INTEGRATOR = 'integrator';
    const DBA = 'dba';
    const PODCASTER = 'podcaster';
    const NON_PROFIT = 'non-profit';
    const LOCAL_BUSINESS = 'local-business';
    const INDIVIDUAL = 'individual';
    const NON_TECHNICAL = 'non-technical';
    const STUDENT = 'student';

    /** The audiences and their names and icons (bootstrap-icons) */
    const AUDIENCES = array(
        'editor'    => array( 'name' => 'Editors', 'icon' => 'pencil' ),
        'admin'     => array( 'name' => 'Administrators', 'icon' => 'shield-lock' ),
        'developer' => array( 'name' => 'Developers', 'icon' => 'code-square' ),
        'user'      => array( 'name' => 'Users', 'icon' => 'person' ),
        'project-manager' => array( 'name' => 'Project managers', 'icon' => 'diagram-3' ),
        'sales-marketing' => array( 'name' => 'Sales & marketing', 'icon' => 'graph-up-arrow' ),
        'partner'         => array( 'name' => 'Partners', 'icon' => 'building' ),
        'advocate'        => array( 'name' => 'Advocates', 'icon' => 'people' ),
        'designer'        => array( 'name' => 'Designers', 'icon' => 'palette' ),
        'hosting'         => array( 'name' => 'Hosting companies', 'icon' => 'database' ),
        'app-creator'     => array( 'name' => 'Application creators', 'icon' => 'phone' ),
        'website-creator' => array( 'name' => 'Website creators', 'icon' => 'globe' ),
        'supporter'       => array( 'name' => 'Supporters', 'icon' => 'star' ),
        'sponsor'         => array( 'name' => 'Sponsors', 'icon' => 'piggy-bank' ),
        'trainer'         => array( 'name' => 'Trainers', 'icon' => 'easel' ),
        'press'           => array( 'name' => 'Press, media and influencers', 'icon' => 'megaphone' ),
        'businesses'      => array( 'name' => 'Businesses', 'icon' => 'briefcase' ),
        'government'      => array( 'name' => 'Governments', 'icon' => 'bank' ),
        'customer'        => array( 'name' => 'Customers', 'icon' => 'person-check' ),
        'community'       => array( 'name' => 'Community', 'icon' => 'chat-left-dots' ),
        'ecosystem'       => array( 'name' => 'Ecosystem', 'icon' => 'globe2' ),
        'php-developer'   => array( 'name' => 'PHP Developers', 'icon' => 'terminal' ),
        'theme-designer'  => array( 'name' => 'Theme designers', 'icon' => 'brush' ),
        'reseller'        => array( 'name' => 'Resellers', 'icon' => 'tag' ),
        'bundler'         => array( 'name' => 'Bundlers', 'icon' => 'box-seam' ),
        'ai-agents'       => array( 'name' => 'AI, agents and agentic', 'icon' => 'cpu' ),
        'software-engineer' => array( 'name' => 'Software Engineers', 'icon' => 'tools' ),
        'adopter'         => array( 'name' => 'Adopters', 'icon' => 'check2-square' ),
        'mobile-app-developer' => array( 'name' => 'Mobile App Developers', 'icon' => 'phone-flip' ),
        'web-framework-developer' => array( 'name' => 'Web Framework Developers', 'icon' => 'diagram-2' ),
        'cms-developer'   => array( 'name' => 'CMS Developers', 'icon' => 'file-earmark-code' ),
        'dxp-developer'   => array( 'name' => 'DXP Developers', 'icon' => 'display' ),
        'ecommerce'       => array( 'name' => 'E-commerce', 'icon' => 'tags' ),
        'integrator'      => array( 'name' => 'Integrators', 'icon' => 'plug' ),
        'dba'             => array( 'name' => 'Database Administrators', 'icon' => 'table' ),
        'podcaster'       => array( 'name' => 'Podcasters, radio and streaming', 'icon' => 'collection-play' ),
        'non-profit'      => array( 'name' => 'Non-profits', 'icon' => 'people' ),
        'local-business'  => array( 'name' => 'Local businesses', 'icon' => 'geo-alt' ),
        'individual'      => array( 'name' => 'Individuals', 'icon' => 'person-circle' ),
        'non-technical'   => array( 'name' => 'Non-technical people', 'icon' => 'chat-square-text' ),
        'student'         => array( 'name' => 'Students and learners', 'icon' => 'book' ),
    );

    /** The categories, in their dashboard order, with names and icons */
    const CATEGORIES = array(
        'getting-started' => array( 'name' => 'Getting started', 'icon' => 'signpost-split' ),
        'manual'          => array( 'name' => 'User manual', 'icon' => 'question-circle' ),
        'editing'       => array( 'name' => 'Editing content', 'icon' => 'pencil-square' ),
        'media'           => array( 'name' => 'Images and media', 'icon' => 'images' ),
        'publishing'      => array( 'name' => 'Publishing', 'icon' => 'journal-check' ),
        'search'          => array( 'name' => 'Search', 'icon' => 'search' ),
        'languages'       => array( 'name' => 'Languages', 'icon' => 'translate' ),
        'account'         => array( 'name' => 'Your account', 'icon' => 'person-lock' ),
        'layouts'         => array( 'name' => 'Layouts', 'icon' => 'layout-sidebar-inset' ),
        'build-two-ways'  => array( 'name' => 'Two ways to build', 'icon' => 'signpost-split' ),
        'shortcuts'       => array( 'name' => 'Keyboard shortcuts', 'icon' => 'input-cursor-text' ),
        'support'         => array( 'name' => 'Help and support', 'icon' => 'info-circle' ),
        'leadership'      => array( 'name' => 'Project leadership', 'icon' => 'diagram-3' ),
        'business'        => array( 'name' => 'Sales and marketing', 'icon' => 'graph-up-arrow' ),
        'partnership'     => array( 'name' => 'Partnership', 'icon' => 'building' ),
        'advocacy'        => array( 'name' => 'Advocacy and community', 'icon' => 'people' ),
        'theme-design'    => array( 'name' => 'Theme design', 'icon' => 'palette' ),
        'templates'       => array( 'name' => 'Templates', 'icon' => 'file-earmark-code' ),
        'extension-development' => array( 'name' => 'Extension development', 'icon' => 'puzzle' ),
        'theme-extensions' => array( 'name' => 'Theme extensions', 'icon' => 'puzzle' ),
        'theme-distribution' => array( 'name' => 'Publishing and distribution', 'icon' => 'box-seam' ),
        'theme-sales' => array( 'name' => 'Selling themes and services', 'icon' => 'tag' ),
        'theme-lifecycle' => array( 'name' => 'Maintenance life cycle', 'icon' => 'arrow-clockwise' ),
        'hosting'         => array( 'name' => 'Hosting', 'icon' => 'database' ),
        'app-creation'    => array( 'name' => 'Building applications', 'icon' => 'phone' ),
        'website-creation' => array( 'name' => 'Building websites', 'icon' => 'globe' ),
        'supporting'      => array( 'name' => 'Supporting Exponential', 'icon' => 'star' ),
        'sponsorship'     => array( 'name' => 'Sponsorship', 'icon' => 'piggy-bank' ),
        'customer-success' => array( 'name' => 'Customer success', 'icon' => 'person-check' ),
        'public-sector'   => array( 'name' => 'Public sector', 'icon' => 'bank' ),
        'business-solutions' => array( 'name' => 'Exponential for businesses', 'icon' => 'briefcase' ),
        'press-media'     => array( 'name' => 'Press and media', 'icon' => 'megaphone' ),
        'training'        => array( 'name' => 'Training', 'icon' => 'easel' ),
        'community'       => array( 'name' => 'Community', 'icon' => 'chat-left-dots' ),
        'ecosystem'       => array( 'name' => 'Ecosystem', 'icon' => 'globe2' ),
        'php-development' => array( 'name' => 'PHP development', 'icon' => 'terminal' ),
        'reselling'       => array( 'name' => 'Reselling', 'icon' => 'tag' ),
        'bundling'        => array( 'name' => 'Bundles and distributions', 'icon' => 'box-seam' ),
        'ai-agents'       => array( 'name' => 'AI and agents', 'icon' => 'cpu' ),
        'software-engineering' => array( 'name' => 'Software engineering', 'icon' => 'tools' ),
        'adoption'        => array( 'name' => 'Adopting Exponential', 'icon' => 'check2-square' ),
        'mobile-apps'     => array( 'name' => 'Mobile apps', 'icon' => 'phone-flip' ),
        'web-frameworks'  => array( 'name' => 'Web frameworks', 'icon' => 'diagram-2' ),
        'cms-development' => array( 'name' => 'CMS development', 'icon' => 'file-earmark-code' ),
        'dxp-development' => array( 'name' => 'DXP development', 'icon' => 'display' ),
        'e-commerce'      => array( 'name' => 'E-commerce', 'icon' => 'tags' ),
        'integration'     => array( 'name' => 'Integration', 'icon' => 'plug' ),
        'database-administration' => array( 'name' => 'Database administration', 'icon' => 'table' ),
        'podcasting-streaming' => array( 'name' => 'Podcasting and streaming', 'icon' => 'collection-play' ),
        'non-profits'     => array( 'name' => 'Non-profit organizations', 'icon' => 'people' ),
        'local-business'  => array( 'name' => 'Local business', 'icon' => 'geo-alt' ),
        'personal-sites'  => array( 'name' => 'Personal sites', 'icon' => 'person-circle' ),
        'plain-language'  => array( 'name' => 'Plain-language help', 'icon' => 'chat-square-text' ),
        'learning'        => array( 'name' => 'Learning Exponential', 'icon' => 'book' ),
        'project'         => array( 'name' => 'The Exponential project', 'icon' => 'globe' ),
        'administration'  => array( 'name' => 'Administration', 'icon' => 'gear' ),
        'features'        => array( 'name' => 'Features', 'icon' => 'stars' ),
        'development'     => array( 'name' => 'Development', 'icon' => 'code-square' ),
        'install-upgrade' => array( 'name' => 'Installation and upgrades', 'icon' => 'box-seam' ),
        'project-files'   => array( 'name' => 'Project files', 'icon' => 'git' ),
        'release-notes'   => array( 'name' => 'Release notes', 'icon' => 'journal-text' ),
        'history'         => array( 'name' => 'History', 'icon' => 'clock-history' ),
        'reference'       => array( 'name' => 'Reference', 'icon' => 'collection' ),
        'extensions'      => array( 'name' => 'Extensions', 'icon' => 'puzzle' ),
    );

    /** The page types and their names */
    const TYPES = array(
        'help' => 'Help', 'guide' => 'Guide', 'feature' => 'Feature', 'bc' => 'Upgrade note', 'install' => 'Installation',
        'changelog' => 'Changelog', 'specification' => 'Specification', 'history' => 'History', 'example' => 'Example',
        'reference' => 'Reference', 'extension' => 'Extension documentation', 'project' => 'Project file',
    );

    /** [TypeSettings] PathRules[] by default */
    const TYPE_RULES = array(
        'doc/eng-us/help/*;help', 'doc/eng-us/guides/*;guide', 'doc/eng-us/features/*;feature', 'doc/eng-us/bc/*;bc', 'doc/changelogs/*;changelog',
        'doc/eng-us/install/*;install', 'doc/eng-us/INSTALL.md;install', 'doc/eng-us/UPDATE.md;install', 'doc/eng-us/UNINSTALL.md;install',
        'doc/eng-us/specifications/*;specification', 'doc/eng-us/history/*;history',
        'doc/eng-us/examples/*;example', 'extension/*;extension', '*;reference',
    );

    /** [AudienceSettings] PathRules[] by default */
    const AUDIENCE_RULES = array(
        'doc/eng-us/help/manual/*;editor,admin,user',
        'doc/eng-us/help/leadership/*;project-manager',
        'doc/eng-us/help/business/*;sales-marketing',
        'doc/eng-us/help/partners/*;partner',
        'doc/eng-us/help/advocates/*;advocate',
        'doc/eng-us/help/designers/*;designer',
        'doc/eng-us/help/hosting/*;hosting',
        'doc/eng-us/help/creators/app-creators/*;app-creator',
        'doc/eng-us/help/creators/website-creators/*;website-creator',
        'doc/eng-us/help/creators/*;app-creator,website-creator',
        'doc/eng-us/help/supporters/*;supporter',
        'doc/eng-us/help/sponsors/*;sponsor',
        'doc/eng-us/help/customers/*;customer',
        'doc/eng-us/help/government/*;government',
        'doc/eng-us/help/businesses/*;businesses',
        'doc/eng-us/help/press/*;press',
        'doc/eng-us/help/trainers/*;trainer',
        'doc/eng-us/help/community/*;community',
        'doc/eng-us/help/ecosystem/*;ecosystem',
        'doc/eng-us/help/php-developers/*;php-developer',
        'doc/eng-us/help/theme-designers/*;theme-designer',
        'doc/eng-us/help/resellers/*;reseller',
        'doc/eng-us/help/bundlers/*;bundler',
        'doc/eng-us/help/ai-agents/*;ai-agents',
        'doc/eng-us/help/software-engineers/*;software-engineer',
        'doc/eng-us/help/adopters/*;adopter',
        'doc/eng-us/help/mobile-app-developers/*;mobile-app-developer',
        'doc/eng-us/help/web-framework-developers/*;web-framework-developer',
        'doc/eng-us/help/cms-developers/*;cms-developer',
        'doc/eng-us/help/dxp-developers/*;dxp-developer',
        'doc/eng-us/help/e-commerce/*;ecommerce',
        'doc/eng-us/help/integrators/*;integrator',
        'doc/eng-us/help/database-administrators/*;dba',
        'doc/eng-us/help/podcasters/*;podcaster',
        'doc/eng-us/help/non-profits/*;non-profit',
        'doc/eng-us/help/local-businesses/*;local-business',
        'doc/eng-us/help/individuals/*;individual',
        'doc/eng-us/help/non-technical-people/*;non-technical',
        'doc/eng-us/help/students/*;student',
        'doc/eng-us/help/project/*;user,editor,admin,developer,partner,supporter',
        'doc/eng-us/help/build-two-ways/*;designer,website-creator,editor,admin,developer',
        'doc/eng-us/help/extension-development/*;developer,designer',
        'doc/eng-us/guides/README.md;editor,admin,developer',
        'doc/eng-us/guides/getting-started.md;admin,editor,developer',
        'doc/eng-us/glossary.md;editor,admin,developer,user',
        'doc/eng-us/guides/content-model-and-editing.md;editor,admin',
        'doc/eng-us/guides/content-history.md;editor,admin',
        'doc/eng-us/guides/content-languages.md;editor,admin',
        'doc/eng-us/guides/drafts-and-pending.md;editor,admin',
        'doc/eng-us/guides/bookmarks.md;editor,user',
        'doc/eng-us/guides/two-factor-login.md;admin,user',
        'doc/eng-us/guides/templates-and-design.md;developer',
        'doc/eng-us/guides/extensions.md;developer,admin',
        'doc/eng-us/guides/rest-api-v1-and-v2.md;developer',
        'doc/eng-us/guides/benchmarking.md;developer',
        'doc/eng-us/guides/test-language-lorem-ipsum.md;developer',
    );

    /** [AudienceSettings] TypeDefaults[] by default */
    const AUDIENCE_DEFAULTS = array(
        'help' => 'editor,user', 'guide' => 'admin', 'feature' => 'admin,developer', 'bc' => 'developer,admin',
        'install' => 'admin,developer', 'changelog' => 'admin,developer', 'specification' => 'developer',
        'history' => 'developer', 'example' => 'developer', 'reference' => 'developer,admin', 'project' => 'developer,admin',
        'extension' => 'editor,admin,developer',
    );

    /** [CategorySettings] PathRules[] by default */
    const CATEGORY_RULES = array(
        'doc/eng-us/help/manual/*;manual',
        'doc/eng-us/help/leadership/*;leadership',
        'doc/eng-us/help/business/*;business',
        'doc/eng-us/help/partners/*;partnership',
        'doc/eng-us/help/advocates/*;advocacy',
        'doc/eng-us/help/designers/templates/*;templates',
        'doc/eng-us/help/designers/*;theme-design',
        'doc/eng-us/help/hosting/*;hosting',
        'doc/eng-us/help/creators/app-creators/*;app-creation',
        'doc/eng-us/help/creators/website-creators/*;website-creation',
        'doc/eng-us/help/creators/*;website-creation',
        'doc/eng-us/help/supporters/*;supporting',
        'doc/eng-us/help/sponsors/*;sponsorship',
        'doc/eng-us/help/customers/*;customer-success',
        'doc/eng-us/help/government/*;public-sector',
        'doc/eng-us/help/businesses/*;business-solutions',
        'doc/eng-us/help/press/*;press-media',
        'doc/eng-us/help/trainers/*;training',
        'doc/eng-us/help/community/*;community',
        'doc/eng-us/help/ecosystem/*;ecosystem',
        'doc/eng-us/help/php-developers/*;php-development',
        'doc/eng-us/help/theme-designers/*;theme-design',
        'doc/eng-us/help/resellers/*;reselling',
        'doc/eng-us/help/bundlers/*;bundling',
        'doc/eng-us/help/ai-agents/*;ai-agents',
        'doc/eng-us/help/software-engineers/*;software-engineering',
        'doc/eng-us/help/adopters/*;adoption',
        'doc/eng-us/help/mobile-app-developers/*;mobile-apps',
        'doc/eng-us/help/web-framework-developers/*;web-frameworks',
        'doc/eng-us/help/cms-developers/*;cms-development',
        'doc/eng-us/help/dxp-developers/*;dxp-development',
        'doc/eng-us/help/e-commerce/*;e-commerce',
        'doc/eng-us/help/integrators/*;integration',
        'doc/eng-us/help/database-administrators/*;database-administration',
        'doc/eng-us/help/podcasters/*;podcasting-streaming',
        'doc/eng-us/help/non-profits/*;non-profits',
        'doc/eng-us/help/local-businesses/*;local-business',
        'doc/eng-us/help/individuals/*;personal-sites',
        'doc/eng-us/help/non-technical-people/*;plain-language',
        'doc/eng-us/help/students/*;learning',
        'doc/eng-us/help/project/*;project',
        'doc/eng-us/help/build-two-ways/*;build-two-ways',
        'doc/eng-us/help/extension-development/*;extension-development',
        'doc/eng-us/guides/README.md;getting-started',
        'doc/eng-us/guides/getting-started.md;getting-started',
        'doc/eng-us/guides/upgrading.md;install-upgrade',
        'doc/eng-us/guides/upgrade-check.md;install-upgrade',
        'doc/eng-us/guides/deploying.md;install-upgrade',
        'doc/eng-us/guides/content-*.md;editing',
        'doc/eng-us/guides/drafts-and-pending.md;editing',
        'doc/eng-us/guides/bookmarks.md;editing',
        'doc/eng-us/guides/two-factor-login.md;account',
        'doc/eng-us/guides/*-developer.md;development',
        'doc/eng-us/guides/templates-and-design.md;development',
        'doc/eng-us/guides/extensions.md;development',
        'doc/eng-us/guides/rest-api-v1-and-v2.md;development',
        'doc/eng-us/guides/benchmarking.md;development',
        'doc/eng-us/guides/test-language-lorem-ipsum.md;development',
        'doc/eng-us/languages.md;languages',
    );

    /** [CategorySettings] TypeDefaults[] by default */
    const CATEGORY_DEFAULTS = array(
        'help' => 'getting-started', 'guide' => 'administration', 'feature' => 'features', 'bc' => 'install-upgrade',
        'install' => 'install-upgrade', 'changelog' => 'release-notes', 'specification' => 'development',
        'history' => 'history', 'example' => 'development', 'reference' => 'reference', 'project' => 'project-files',
        'extension' => 'extensions',
    );

    /** [TreeSettings] PageOrder[] by default: page id => order (the top level of the page tree) */
    const PAGE_ORDER = array(
        'project/README.md' => 0,
        'INSTALL.md' => 10,
        'UPDATE.md' => 12,
        'UNINSTALL.md' => 14,
        'languages.md' => 20,
        'branch-overview.md' => 30,
        'release-overview' => 40,
        'project/SECURITY.md' => 50,
        'project/CONTRIBUTING.md' => 100,
        'project/CODE_OF_CONDUCT.md' => 105,
        'project/COPYRIGHT.md' => 110,
        'README-LICENSE.md' => 120,
        'LICENSE-GFDL-1.3.txt' => 130,
        'project/LICENSE.md' => 140,
        'glossary.md' => 9999,
    );

    /** @var array the configuration: audiences, categories, types, typeRules, audienceRules, audienceDefaults, categoryRules, categoryDefaults */
    protected $config;

    /**
     * @param array $config see $config; missing keys take the defaults of this class
     */
    public function __construct( array $config = array() )
    {
        $this->config = array_merge( self::defaults(), $config );
    }

    /**
     * The configuration from exphelp.ini, with this class's defaults where a setting is missing.
     *
     * @return expHelpAudience
     */
    public static function fromIni()
    {
        $config = array();
        if ( class_exists( 'eZINI' ) && eZINI::exists( 'exphelp.ini' ) )
        {
            $ini = eZINI::instance( 'exphelp.ini' );
            $get = function ( $section, $name ) use ( $ini ) {
                return $ini->hasVariable( $section, $name ) ? $ini->variable( $section, $name ) : null;
            };
            if ( is_array( $ids = $get( 'AudienceSettings', 'Audiences' ) ) && $ids )
                $config['audiences'] = self::named( expHelpAllowlist::listSetting( $ids ), self::AUDIENCES,
                                                    (array)$get( 'AudienceSettings', 'AudienceNames' ), (array)$get( 'AudienceSettings', 'AudienceIcons' ) );
            if ( is_array( $ids = $get( 'CategorySettings', 'Categories' ) ) && $ids )
                $config['categories'] = self::named( expHelpAllowlist::listSetting( $ids ), self::CATEGORIES,
                                                     (array)$get( 'CategorySettings', 'CategoryNames' ), (array)$get( 'CategorySettings', 'CategoryIcons' ) );
            foreach ( array( 'typeRules' => array( 'TypeSettings', 'PathRules' ),
                             'audienceRules' => array( 'AudienceSettings', 'PathRules' ),
                             'categoryRules' => array( 'CategorySettings', 'PathRules' ) ) as $key => $where )
                if ( is_array( $rules = $get( $where[0], $where[1] ) ) && $rules )
                    $config[$key] = array_values( $rules );
            foreach ( array( 'audienceDefaults' => 'AudienceSettings', 'categoryDefaults' => 'CategorySettings' ) as $key => $section )
                if ( is_array( $defaults = $get( $section, 'TypeDefaults' ) ) && $defaults )
                    $config[$key] = array_merge( $key === 'audienceDefaults' ? self::AUDIENCE_DEFAULTS : self::CATEGORY_DEFAULTS, $defaults );
            if ( is_array( $types = $get( 'TypeSettings', 'TypeNames' ) ) && $types )
                $config['types'] = array_merge( self::TYPES, $types );
            foreach ( array( 'titles' => 'Titles', 'summaries' => 'Summaries' ) as $key => $name )
                if ( is_array( $map = $get( 'TitleSettings', $name ) ) && $map )
                    $config[$key] = array_filter( $map, function ( $value, $path ) {
                        return is_string( $path ) && is_string( $value ) && trim( $value ) !== '';
                    }, ARRAY_FILTER_USE_BOTH );
            if ( is_array( $rules = $get( 'TitleSettings', 'TitleRules' ) ) && $rules )
                $config['titleRules'] = array_values( $rules );
            // present (even emptied with PageOrder[]): the settings replace the built-in order
            if ( is_array( $orders = $get( 'TreeSettings', 'PageOrder' ) ) )
                $config['pageOrder'] = self::orderMap( $orders );
        }
        return new self( $config );
    }

    /**
     * The configured audience ids.
     *
     * @return string[]
     */
    public static function all()
    {
        return array_keys( self::fromIni()->audiences() );
    }

    /**
     * The audiences of a page (front matter, path rules, heuristics, type default).
     *
     * @param string $path the path relative to the installation root (doc/...)
     * @param array $frontMatter
     * @param string $type the page type, when known (it wins over the type rules)
     * @param string $text the opening text of the page
     * @return string[]
     */
    public static function forPage( $path, array $frontMatter, $type = '', $text = '' )
    {
        if ( $type !== '' && !isset( $frontMatter['type'] ) )
            $frontMatter['type'] = $type;
        return self::fromIni()->classify( $path, $frontMatter, $text )['audience'];
    }

    /**
     * The translated name of an audience.
     *
     * @param string $audience
     * @return string
     */
    public static function label( $audience )
    {
        $audiences = self::fromIni()->audiences();
        $name = isset( $audiences[$audience] ) ? $audiences[$audience]['name'] : (string)$audience;
        return class_exists( 'ezpI18n' ) ? ezpI18n::tr( 'kernel/exphelp', $name ) : $name;
    }

    /**
     * @return array the built-in configuration
     */
    public static function defaults()
    {
        return array(
            'audiences' => self::AUDIENCES, 'categories' => self::CATEGORIES, 'types' => self::TYPES,
            'typeRules' => self::TYPE_RULES, 'audienceRules' => self::AUDIENCE_RULES,
            'audienceDefaults' => self::AUDIENCE_DEFAULTS, 'categoryRules' => self::CATEGORY_RULES,
            'categoryDefaults' => self::CATEGORY_DEFAULTS,
            'titles' => array(), 'summaries' => array(), 'titleRules' => array(),
            'pageOrder' => self::PAGE_ORDER,
        );
    }

    /**
     * The valid entries of a PageOrder[] setting: a page id (no leading or trailing slash, no "..") => an integer.
     *
     * @param array $orders page id => order, as the settings give it (strings)
     * @return array page id => int
     */
    public static function orderMap( array $orders )
    {
        $map = array();
        foreach ( $orders as $id => $order )
        {
            $id = trim( (string)$id );
            if ( $id === '' || $id[0] === '/' || substr( $id, -1 ) === '/' || in_array( '..', explode( '/', $id ), true ) )
                continue;
            if ( is_int( $order ) || ( is_string( $order ) && preg_match( '/^\s*-?\d{1,9}\s*$/', $order ) ) )
                $map[$id] = (int)$order;
        }
        return $map;
    }

    /**
     * id => array( name, icon ) for a list of ids, from the INI names and icons, else the built-in ones.
     *
     * @param string[] $ids
     * @param array $builtin
     * @param array $names
     * @param array $icons
     * @return array
     */
    protected static function named( array $ids, array $builtin, array $names, array $icons )
    {
        $named = array();
        foreach ( $ids as $id )
        {
            if ( !preg_match( '/^[a-z0-9][a-z0-9-]{0,40}$/', $id ) )
                continue;
            $named[$id] = array(
                'name' => isset( $names[$id] ) ? (string)$names[$id] : ( isset( $builtin[$id] ) ? $builtin[$id]['name'] : ucfirst( str_replace( '-', ' ', $id ) ) ),
                'icon' => isset( $icons[$id] ) && preg_match( '/^[a-z0-9][a-z0-9-]{0,40}$/', $icons[$id] ) ? $icons[$id] : ( isset( $builtin[$id] ) ? $builtin[$id]['icon'] : 'file-earmark' ),
            );
        }
        return $named;
    }

    /**
     * @return array the configuration in use (for the index signature)
     */
    public function config()
    {
        return $this->config;
    }

    /**
     * @return array id => array( 'name', 'icon' )
     */
    public function audiences()
    {
        return $this->config['audiences'];
    }

    /**
     * @return array id => array( 'name', 'icon' )
     */
    public function categories()
    {
        return $this->config['categories'];
    }

    /**
     * @return array id => name
     */
    public function types()
    {
        return $this->config['types'];
    }

    /**
     * Classifies a page.
     *
     * @param string $path the path relative to the installation root
     * @param array $meta its front matter
     * @param string $intro the first lines of its text (the audience heuristics read them)
     * @return array array( 'type' => string, 'audience' => string[], 'category' => string )
     */
    public function classify( $path, array $meta = array(), $intro = '' )
    {
        $type = isset( $meta['type'] ) && is_string( $meta['type'] ) && isset( $this->config['types'][strtolower( $meta['type'] )] )
            ? strtolower( $meta['type'] ) : (string)$this->match( $this->config['typeRules'], $path, 'reference' );
        if ( !isset( $this->config['types'][$type] ) )
            $type = 'reference';

        $audience = isset( $meta['audience'] ) ? $this->audienceList( (array)$meta['audience'] ) : array();
        if ( !$audience )
            $audience = $this->audienceList( explode( ',', (string)$this->match( $this->config['audienceRules'], $path, '' ) ) );
        if ( !$audience )
            $audience = $this->guessAudience( $path, $intro );
        if ( !$audience && isset( $this->config['audienceDefaults'][$type] ) )
            $audience = $this->audienceList( explode( ',', $this->config['audienceDefaults'][$type] ) );
        if ( !$audience )
            $audience = array_slice( array_keys( $this->config['audiences'] ), 0, 1 );

        $category = isset( $meta['category'] ) && is_string( $meta['category'] ) ? strtolower( trim( $meta['category'] ) ) : '';
        if ( !isset( $this->config['categories'][$category] ) )
            $category = (string)$this->match( $this->config['categoryRules'], $path, '' );
        if ( !isset( $this->config['categories'][$category] ) )
            $category = isset( $this->config['categoryDefaults'][$type] ) ? $this->config['categoryDefaults'][$type] : 'reference';
        if ( !isset( $this->config['categories'][$category] ) )
            $category = (string)key( $this->config['categories'] );

        return array( 'type' => $type, 'audience' => $audience, 'category' => $category );
    }

    /**
     * The title a page is shown with when its own is unclear (a raw file name, "README", a bare version, a source code
     * comment). A page's front matter title wins over this and is never passed here. First answer wins:
     * [TitleSettings] Titles[<path>] (exact path relative to the installation root), then the first
     * TitleRules[] "<pattern>;<template>" whose shell pattern matches. A template may use {title} (the title the page
     * gives itself), {name} (its file name, humanized), {fileversion} (the version at the end of the file name,
     * "2.9-3" of CHANGELOG-desc-2.9-3) and {dirversion} (the nearest version directory, "4.5" of features/4.5/x.txt).
     *
     * @param string $path relative to the installation root
     * @param string $title the title the page gives itself
     * @return string
     */
    public function pageTitle( $path, $title )
    {
        $title = (string)$title;
        if ( isset( $this->config['titles'][$path] ) && trim( $this->config['titles'][$path] ) !== '' )
            return trim( $this->config['titles'][$path] );
        $template = self::match( isset( $this->config['titleRules'] ) ? $this->config['titleRules'] : array(), $path, null );
        if ( !is_string( $template ) || $template === '' )
            return $title;
        $base = preg_replace( '/\.(md|markdown|txt|rst)$/i', '', basename( $path ) );
        $fileVersion = preg_match( '/(\d+(?:\.\d+)+(?:-\d+)?[a-z0-9]*)$/i', $base, $m ) ? $m[1] : '';
        $dirVersion = '';
        foreach ( array_reverse( explode( '/', dirname( $path ) ) ) as $segment )
            if ( preg_match( '/^\d+(\.\d+)+$/', $segment ) )
            {
                $dirVersion = $segment;
                break;
            }
        $result = strtr( $template, array( '{title}' => $title, '{name}' => expHelpDocumentIndex::humanize( $base ),
                                           '{fileversion}' => $fileVersion, '{dirversion}' => $dirVersion ) );
        $result = rtrim( trim( (string)preg_replace( '/\s+/u', ' ', $result ) ), '.' );
        return $result !== '' ? $result : $title;
    }

    /**
     * The summary a page is shown with when it has none of its own in front matter: [TitleSettings] Summaries[<path>],
     * else the one given (the page's first paragraph).
     *
     * @param string $path relative to the installation root
     * @param string $summary
     * @return string
     */
    public function pageSummary( $path, $summary )
    {
        if ( isset( $this->config['summaries'][$path] ) && trim( $this->config['summaries'][$path] ) !== '' )
            return trim( $this->config['summaries'][$path] );
        return (string)$summary;
    }

    /**
     * The place of a page in the page tree, the folder lists and the category lists: [TreeSettings] PageOrder[<page
     * id>] when the settings name the page, else the order given (its front matter order, or the default).
     *
     * @param string $id the page id (its path below the language directory, project/README.md, ext/<name>/...)
     * @param int $order
     * @return int
     */
    public function pageOrder( $id, $order )
    {
        $id = (string)$id;
        return isset( $this->config['pageOrder'][$id] ) && is_int( $this->config['pageOrder'][$id] ) ? $this->config['pageOrder'][$id] : (int)$order;
    }

    /**
     * The value of the first "<pattern>;<value>" rule whose pattern matches the path.
     *
     * @param string[] $rules
     * @param string $path
     * @param mixed $default
     * @return mixed
     */
    public static function match( array $rules, $path, $default = null )
    {
        foreach ( $rules as $rule )
        {
            $pos = strrpos( (string)$rule, ';' );
            if ( $pos === false )
                continue;
            if ( fnmatch( trim( substr( $rule, 0, $pos ) ), $path ) )
                return trim( substr( $rule, $pos + 1 ) );
        }
        return $default;
    }

    /**
     * The known audiences of a list, aliases resolved (editors, administrator, developers, users ...), in the
     * configured order.
     *
     * @param array $items
     * @return string[]
     */
    public function audienceList( array $items )
    {
        $aliases = array( 'editors' => 'editor', 'administrator' => 'admin', 'administrators' => 'admin', 'admins' => 'admin',
                          'developers' => 'developer', 'dev' => 'developer', 'users' => 'user', 'enduser' => 'user',
                          'end-user' => 'user', 'end-users' => 'user', 'project-managers' => 'project-manager',
                          'projectmanager' => 'project-manager', 'pm' => 'project-manager', 'sales' => 'sales-marketing',
                          'marketing' => 'sales-marketing', 'sales-and-marketing' => 'sales-marketing',
                          'partners' => 'partner', 'advocates' => 'advocate', 'designers' => 'designer',
                          'hosting-company' => 'hosting', 'hosting-companies' => 'hosting', 'hosts' => 'hosting',
                          'app-creators' => 'app-creator', 'application-creator' => 'app-creator',
                          'application-creators' => 'app-creator', 'website-creators' => 'website-creator',
                          'supporters' => 'supporter', 'sponsors' => 'sponsor',
                          'customers' => 'customer', 'site-owner' => 'customer', 'site-owners' => 'customer',
                          'governments' => 'government', 'public-sector' => 'government', 'agency' => 'government', 'agencies' => 'government',
                          'companies' => 'businesses', 'company' => 'businesses', 'enterprise' => 'businesses', 'enterprises' => 'businesses',
                          'media' => 'press', 'journalist' => 'press', 'journalists' => 'press', 'influencer' => 'press', 'influencers' => 'press', 'press-media' => 'press',
                          'trainers' => 'trainer', 'teacher' => 'trainer', 'teachers' => 'trainer', 'instructor' => 'trainer', 'instructors' => 'trainer',
                          'communities' => 'community', 'community-members' => 'community',
                          'ecosystems' => 'ecosystem',
                          'php-developers' => 'php-developer', 'php' => 'php-developer',
                          'theme-designers' => 'theme-designer', 'themers' => 'theme-designer', 'themer' => 'theme-designer',
                          'resellers' => 'reseller',
                          'bundlers' => 'bundler', 'distributors' => 'bundler',
                          'ai' => 'ai-agents', 'agents' => 'ai-agents', 'agentic' => 'ai-agents', 'ai-agent' => 'ai-agents',
                          'software-engineers' => 'software-engineer', 'engineer' => 'software-engineer', 'engineers' => 'software-engineer',
                          'adopters' => 'adopter',
                          'mobile-app-developers' => 'mobile-app-developer', 'mobile' => 'mobile-app-developer', 'mobile-developers' => 'mobile-app-developer',
                          'web-framework-developers' => 'web-framework-developer', 'framework-developers' => 'web-framework-developer',
                          'cms-developers' => 'cms-developer',
                          'dxp-developers' => 'dxp-developer', 'dxp' => 'dxp-developer',
                          'e-commerce' => 'ecommerce', 'commerce' => 'ecommerce', 'shop' => 'ecommerce', 'merchants' => 'ecommerce',
                          'integrators' => 'integrator', 'system-integrators' => 'integrator',
                          'dbas' => 'dba', 'database-administrator' => 'dba', 'database-administrators' => 'dba',
                          'podcasters' => 'podcaster', 'podcast' => 'podcaster', 'radio' => 'podcaster', 'streaming' => 'podcaster', 'streamers' => 'podcaster',
                          'non-profits' => 'non-profit', 'nonprofit' => 'non-profit', 'nonprofits' => 'non-profit', 'charity' => 'non-profit', 'charities' => 'non-profit', 'ngo' => 'non-profit',
                          'local-businesses' => 'local-business', 'small-business' => 'local-business', 'small-businesses' => 'local-business',
                          'individuals' => 'individual', 'personal' => 'individual',
                          'non-technical-people' => 'non-technical', 'beginners' => 'non-technical', 'plain-language' => 'non-technical',
                          'students' => 'student', 'learner' => 'student', 'learners' => 'student',
                          'all' => '*' );
        $wanted = array();
        foreach ( $items as $item )
        {
            $item = strtolower( trim( (string)$item ) );
            $item = isset( $aliases[$item] ) ? $aliases[$item] : $item;
            if ( $item === '*' )
                return array_keys( $this->config['audiences'] );
            $wanted[$item] = true;
        }
        $list = array();
        foreach ( array_keys( $this->config['audiences'] ) as $id )
            if ( isset( $wanted[$id] ) )
                $list[] = $id;
        return $list;
    }

    /**
     * The audience from the file name (…-editor.md, …-administrator.md, …-developer.md, …-user.md) or the words of
     * the opening lines; empty when neither says.
     *
     * @param string $path
     * @param string $intro
     * @return string[]
     */
    public function guessAudience( $path, $intro )
    {
        if ( preg_match( '/-(web-framework-developer|mobile-app-developer|software-engineer|theme-designer|local-business|php-developer|cms-developer|dxp-developer|non-technical|integrator|non-profit|individual|community|ecosystem|ai-agents|ecommerce|podcaster|reseller|bundler|adopter|student|dba|project-manager|sales-marketing|app-creator|website-creator|partner|advocate|designer|supporter|sponsor|customer|government|businesses|press|trainer|editor|editors|administrator|admin|developer|user)\.[a-z]+$/', basename( $path ), $m ) )
            return $this->audienceList( array( $m[1] ) );
        $intro = strtolower( substr( (string)$intro, 0, 600 ) );
        $found = array();
        foreach ( array( '/\beditors?\b/' => 'editor', '/\b(administrators?|site owners?|operators?)\b/' => 'admin',
                         '/\b(developers?|site builders?)\b/' => 'developer', '/\bend[ -]users?\b/' => 'user' ) as $pattern => $id )
            if ( preg_match( $pattern, $intro ) )
                $found[] = $id;
        return $this->audienceList( $found );
    }
}
