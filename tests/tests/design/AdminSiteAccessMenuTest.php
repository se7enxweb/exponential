<?php
/**
 * admin4's site menu: the globe in the header opens a list of the installation's siteaccesses (site.ini
 * [AdminSiteAccessMenu], ezpSiteAccessMenu, the operators siteaccess_menu() and siteaccess_menu_key(),
 * parts/a4_header_site.tpl, admin4.js):
 *
 * - the list: the related siteaccesses of the current one (or the available ones), only those of
 *   AvailableSiteAccessList, without the hidden ones; the site the globe opens first, the others grouped into sites
 *   and administrations (AdminSiteAccessList, else SiteDesign admin* or a FrontSiteAccess), the one in use marked;
 *   each with its name (SiteName, else the identifier), locale, language and the address of ezpSiteAccessURL::root();
 * - Show=disabled, a list with the site alone and anonymous pages leave the globe the plain link it was;
 * - the header's cache blocks are keyed by siteaccess_menu_key(), which changes with the list and the request;
 * - the markup: one button in the place of the link (shown by the script, the link stays without it), a disclosure of
 *   plain links with labelled groups and aria-current, escaped values; the script's keys (node, js/site_menu.js);
 * - the styles take their colours from tokens, with forced-colors rules and a phone placement; translations.
 *
 * No database: the settings are injected (eZINI::injectSettings(), put back in tearDown()) for siteaccesses that do
 * not exist, the request is $_SERVER over HTTPS.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminSiteAccessMenuTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminBaseStylesheetsTest.php';

class AdminSiteAccessMenuTest extends PHPUnit\Framework\TestCase
{
    private $server;
    private $injected;
    private $access;
    private $handler;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 3 ) );
        $this->server = $_SERVER;
        $this->injected = ( new ReflectionProperty( 'eZINI', 'injectedSettings' ) )->getValue();
        $this->access = array_key_exists( 'eZCurrentAccess', $GLOBALS ) ? array( $GLOBALS['eZCurrentAccess'] ) : null;
        foreach ( array( 'HTTP_X_FORWARDED_HOST', 'HTTP_X_FORWARDED_PROTO', 'HTTP_X_FORWARDED_PORT', 'HTTP_X_FORWARDED_SERVER', 'SERVER_PORT' ) as $key )
            unset( $_SERVER[$key] );
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['HTTP_HOST'] = 'www.example.invalid';
    }

    protected function assertPreConditions(): void
    {
        $this->handler = set_exception_handler( null );
        restore_exception_handler();
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->server;
        eZINI::injectSettings( $this->injected );
        if ( $this->access === null )
            unset( $GLOBALS['eZCurrentAccess'] );
        else
            $GLOBALS['eZCurrentAccess'] = $this->access[0];
        // Loading a siteaccess's settings registers eZExecution's shutdown and exception handlers once per process
        $current = set_exception_handler( null );
        restore_exception_handler();
        if ( $current === array( 'eZExecution', 'defaultExceptionHandler' ) && $this->handler !== $current )
            restore_exception_handler();
    }

    private static function file( $path )
    {
        return (string)file_get_contents( dirname( __DIR__, 3 ) . '/' . $path );
    }

    private static function css()
    {
        return preg_replace( '#/\*.*?\*/#s', '', self::file( 'design/admin4/stylesheets/admin4.css' ) );
    }

    /**
     * By default the menu lists sites only, and besides the site the globe opens only the siteaccesses the user may
     * log in to (user/login with its SiteAccess limitation); the administrations only with ListAdministrations=enabled
     */
    public function testTheListIsTheUsersSitesUnlessAdministrationsAreAskedFor()
    {
        $user = new class( array() ) extends eZUser {
            public $allowed = array( 'en', 'site_admin' );
            public $asked = array();
            public function canLoginToSiteAccess( $access )
            {
                $this->asked[] = $access['name'];
                return in_array( $access['name'], $this->allowed, true );
            }
        };
        // the default: sites only
        $this->inject( array( 'ListAdministrations' => 'disabled', 'AdminSiteAccessList' => array( 'site_admin' ) ) );
        $this->assertFalse( ezpSiteAccessMenu::settings()['administrations'] );
        $this->assertSame( array( 'site' => array( 'en', 'de' ) ), self::identifiers( ezpSiteAccessMenu::current() ) );
        // the user's: de is not theirs; the site the globe opens is not asked for (the globe links to it anyway)
        $menu = ezpSiteAccessMenu::current( $user );
        $this->assertSame( 'site', $menu['front']['identifier'] );
        $this->assertSame( array( 'site' => array( 'en' ) ), self::identifiers( $menu ) );
        $this->assertNotContains( 'site', $user->asked );
        // with the administrations, still the user's only
        $this->inject( array( 'ListAdministrations' => 'enabled', 'AdminSiteAccessList' => array( 'site_admin' ) ) );
        $this->assertSame( array( 'site' => array( 'en' ), 'admin' => array( 'site_admin' ) ), self::identifiers( ezpSiteAccessMenu::current( $user ) ) );
        $user->allowed = array();
        $this->assertFalse( ezpSiteAccessMenu::current( $user )['enabled'], 'nothing besides the site: the globe stays a link' );
        // the key follows the setting
        $key = ezpSiteAccessMenu::key();
        $this->inject( array( 'ListAdministrations' => 'disabled', 'AdminSiteAccessList' => array( 'site_admin' ) ) );
        $this->assertNotSame( $key, ezpSiteAccessMenu::key(), 'ListAdministrations' );
        // build() without a check lists every one, with it only the allowed ones
        $this->assertSame( array( 'site' => array( 'en' ) ),
                           self::identifiers( ezpSiteAccessMenu::build( array( 'site', 'en', 'de', 'site_admin' ), 'site', '', array( 'site_admin' ),
                                                                        self::info(), self::url(), function ( $name ) { return $name !== 'de'; }, false ) ) );
    }

    /** The settings of five siteaccesses, as build() gets them */
    private static function info()
    {
        $sites = array(
            'site' => array( 'site_name' => 'Example', 'locale' => 'eng-GB', 'lang' => 'en-GB', 'site_design' => 'standard' ),
            'en' => array( 'site_name' => 'Example (English)', 'locale' => 'eng-US', 'lang' => 'en-US', 'site_design' => 'standard' ),
            'de' => array( 'site_name' => '', 'locale' => 'ger-DE', 'lang' => 'de-DE', 'site_design' => 'standard' ),
            'site_admin' => array( 'site_name' => 'Example', 'locale' => 'eng-GB', 'lang' => 'en-GB', 'site_design' => 'admin4' ),
            'intranet' => array( 'site_name' => 'Intranet', 'locale' => 'eng-GB', 'lang' => 'en-GB', 'site_design' => 'intranet',
                                 'front_site_access' => 'site' ),
        );
        return function ( $name ) use ( $sites ) { return $sites[$name]; };
    }

    private static function url()
    {
        return function ( $name ) { return $name === 'gone' ? false : "https://www.example.invalid/$name/"; };
    }

    private static function identifiers( array $menu )
    {
        $groups = array();
        foreach ( $menu['groups'] as $group )
            $groups[$group['identifier']] = array_column( $group['items'], 'identifier' );
        return $groups;
    }

    public function testTheSiteComesFirstAndTheOthersAreGrouped()
    {
        $menu = ezpSiteAccessMenu::build( array( 'site', 'en', 'de', 'gone', 'site_admin', 'intranet', 'en' ), 'site', 'site_admin',
                                          array(), self::info(), self::url() );
        $this->assertTrue( $menu['enabled'] );
        $this->assertSame( array( 'identifier' => 'site', 'name' => 'Example', 'locale' => 'eng-GB', 'lang' => 'en-GB',
                                  'url' => 'https://www.example.invalid/site/', 'display_url' => 'www.example.invalid/site',
                                  'current' => false, 'admin' => false ), $menu['front'] );
        // the site is not repeated; one without an address is left out; a name given twice is listed once; the
        // groups in this order; an administration by its design (admin4) or by naming a site (FrontSiteAccess)
        $this->assertSame( array( 'site' => array( 'en', 'de' ), 'admin' => array( 'site_admin', 'intranet' ) ), self::identifiers( $menu ) );
        $de = $menu['groups'][0]['items'][1];
        $this->assertSame( 'de', $de['name'], 'without a SiteName the identifier names it' );
        $this->assertSame( 'de-DE', $de['lang'] );
        $admin = $menu['groups'][1]['items'][0];
        $this->assertTrue( $admin['current'] );
        $this->assertFalse( $menu['groups'][1]['items'][1]['current'] );
    }

    public function testAdminSiteAccessListDecidesTheGroups()
    {
        $menu = ezpSiteAccessMenu::build( array( 'site', 'en', 'site_admin', 'intranet' ), 'site', '', array( 'en' ), self::info(), self::url() );
        $this->assertSame( array( 'site' => array( 'site_admin', 'intranet' ), 'admin' => array( 'en' ) ), self::identifiers( $menu ) );
    }

    public function testAMenuWithTheSiteAloneIsOff()
    {
        $this->assertFalse( ezpSiteAccessMenu::build( array( 'site' ), 'site', 'site', array(), self::info(), self::url() )['enabled'] );
        $this->assertFalse( ezpSiteAccessMenu::build( array( 'en' ), 'gone', '', array(), self::info(), self::url() )['enabled'], 'no site to open' );
        $this->assertSame( array( 'enabled' => false, 'front' => null, 'groups' => array() ), ezpSiteAccessMenu::build( array( 'en' ), 'site', '', array(), null, null ) );
    }

    /** Settings for siteaccesses that do not exist: site.ini of each is the injected one */
    private function inject( array $menu, array $siteAccessSettings = array(), $current = 'site_admin' )
    {
        $settings = $this->injected;
        $settings['site.ini']['SiteSettings']['DefaultAccess'] = 'site';
        $settings['site.ini']['SiteSettings']['FrontSiteAccess'] = '';
        $settings['site.ini']['SiteSettings']['SiteName'] = 'Example';
        $settings['site.ini']['RegionalSettings']['Locale'] = 'eng-GB';
        $settings['site.ini']['DesignSettings']['SiteDesign'] = 'standard';
        $settings['site.ini']['SiteAccessSettings'] = $siteAccessSettings + array(
            'AvailableSiteAccessList' => array( 'site', 'en', 'de', 'site_admin' ),
            'RelatedSiteAccessList' => array(),
            'MatchOrder' => 'uri',
            'URIMatchType' => 'element',
            'URIMatchElement' => '1',
            'HostMatchMapItems' => array(),
            'RemoveSiteAccessIfDefaultAccess' => 'disabled',
        );
        // the administrations listed unless a test asks otherwise (the default, disabled, has its own test)
        $settings['site.ini']['AdminSiteAccessMenu'] = $menu + array( 'ListAdministrations' => 'enabled' );
        eZINI::injectSettings( $settings );
        $GLOBALS['eZCurrentAccess'] = array( 'name' => $current, 'type' => eZSiteAccess::TYPE_URI, 'uri_part' => array( $current ) );
    }

    public function testTheListComesFromTheSettings()
    {
        $this->inject( array( 'AdminSiteAccessList' => array( 'site_admin' ) ) );
        $settings = ezpSiteAccessMenu::settings();
        $this->assertSame( array( 'show' => true, 'list' => 'related', 'hidden' => array(), 'admin' => array( 'site_admin' ),
                                  'administrations' => true ), $settings );
        // List=related without RelatedSiteAccessList: the available ones
        $this->assertSame( array( 'site', 'en', 'de', 'site_admin' ), ezpSiteAccessMenu::names( $settings ) );

        // the related ones, in their order, only those that are available; the hidden ones left out
        $this->inject( array( 'HiddenSiteAccessList' => array( 'de', '' ) ), array( 'RelatedSiteAccessList' => array( 'de', 'site_admin', 'site', 'elsewhere' ) ) );
        $settings = ezpSiteAccessMenu::settings();
        $this->assertSame( array( 'de' ), $settings['hidden'] );
        $this->assertSame( array( 'site_admin', 'site' ), ezpSiteAccessMenu::names( $settings ) );
        // List=available ignores RelatedSiteAccessList
        $this->inject( array( 'List' => 'available' ), array( 'RelatedSiteAccessList' => array( 'site', 'site_admin' ) ) );
        $this->assertSame( array( 'site', 'en', 'de', 'site_admin' ), ezpSiteAccessMenu::names( ezpSiteAccessMenu::settings() ) );

        // the site the globe opens stays the first entry, hidden or not
        $this->inject( array( 'HiddenSiteAccessList' => array( 'site' ), 'AdminSiteAccessList' => array( 'site_admin' ) ) );
        $menu = ezpSiteAccessMenu::current();
        $this->assertSame( 'site', $menu['front']['identifier'] );
        $this->assertSame( array( 'site' => array( 'en', 'de' ), 'admin' => array( 'site_admin' ) ), self::identifiers( $menu ) );

        $this->inject( array( 'Show' => 'disabled' ) );
        $this->assertFalse( ezpSiteAccessMenu::settings()['show'] );
        $this->assertFalse( ezpSiteAccessMenu::current()['enabled'] );
        $this->assertSame( '', ezpSiteAccessMenu::key() );
    }

    /** The addresses are ezpSiteAccessURL's, for URI and host matching alike, and the names come from each siteaccess's settings */
    public function testTheAddressesAreTheKernelsSiteAccessUrls()
    {
        $this->inject( array( 'AdminSiteAccessList' => array( 'site_admin' ) ) );
        $menu = ezpSiteAccessMenu::current();
        $this->assertTrue( $menu['enabled'] );
        $this->assertSame( 'site', $menu['front']['identifier'] );
        $this->assertSame( ezpSiteAccessURL::front(), $menu['front']['url'] );
        $this->assertSame( array( 'site' => array( 'en', 'de' ), 'admin' => array( 'site_admin' ) ), self::identifiers( $menu ) );
        foreach ( $menu['groups'] as $group )
        {
            foreach ( $group['items'] as $item )
            {
                $this->assertSame( ezpSiteAccessURL::root( $item['identifier'] ), $item['url'], $item['identifier'] );
                $this->assertSame( 'Example', $item['name'] );
                $this->assertSame( 'eng-GB', $item['locale'] );
                $this->assertSame( 'en-GB', $item['lang'] );
            }
        }
        $this->assertStringEndsWith( '/de/', $menu['groups'][0]['items'][1]['url'] );
        $this->assertTrue( $menu['groups'][1]['items'][0]['current'] );

        // host matching: the admin on a host of its own
        $this->inject( array( 'AdminSiteAccessList' => array( 'site_admin' ) ),
                       array( 'MatchOrder' => 'host', 'HostMatchMapItems' => array( 'www.example.invalid;site', 'admin.example.invalid;site_admin' ) ) );
        $menu = ezpSiteAccessMenu::current();
        $this->assertSame( 'https://admin.example.invalid/' . self::index(), $menu['groups'][1]['items'][0]['url'] );
        $this->assertSame( 'admin.example.invalid' . rtrim( '/' . self::index(), '/' ), $menu['groups'][1]['items'][0]['display_url'] );

        // the site the globe opens is the admin's FrontSiteAccess
        $this->inject( array( 'AdminSiteAccessList' => array( 'site_admin' ) ) );
        $settings = ( new ReflectionProperty( 'eZINI', 'injectedSettings' ) )->getValue();
        $settings['site.ini']['SiteSettings']['FrontSiteAccess'] = 'en';
        eZINI::injectSettings( $settings );
        $this->assertSame( 'en', ezpSiteAccessURL::frontSiteAccess() );
        $this->assertSame( 'en', ezpSiteAccessMenu::current()['front']['identifier'] );
        $settings['site.ini']['SiteSettings']['FrontSiteAccess'] = 'elsewhere';
        eZINI::injectSettings( $settings );
        $this->assertSame( 'site', ezpSiteAccessURL::frontSiteAccess(), 'an unknown FrontSiteAccess: DefaultAccess, as front() has it' );
    }

    private static function index()
    {
        $index = trim( (string)eZSys::indexFile( false ), '/' );
        return $index !== '' ? $index . '/' : '';
    }

    public function testTheKeyChangesWithTheListAndTheRequest()
    {
        $this->inject( array() );
        $key = ezpSiteAccessMenu::key();
        $this->assertMatchesRegularExpression( '/^[0-9a-f]{32}$/', $key );
        $this->assertSame( $key, ezpSiteAccessMenu::key() );
        $_SERVER['HTTP_HOST'] = 'other.example.invalid';
        $this->assertNotSame( $key, $host = ezpSiteAccessMenu::key(), 'host' );
        $_SERVER['SERVER_PORT'] = '8443';
        $this->assertNotSame( $host, $port = ezpSiteAccessMenu::key(), 'port' );
        unset( $_SERVER['HTTPS'] );
        $this->assertNotSame( $port, ezpSiteAccessMenu::key(), 'scheme' );
        $_SERVER = $this->server;
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['HTTP_HOST'] = 'www.example.invalid';
        unset( $_SERVER['SERVER_PORT'] );
        $this->assertSame( $key, ezpSiteAccessMenu::key() );
        $this->inject( array( 'HiddenSiteAccessList' => array( 'en' ) ) );
        $this->assertNotSame( $key, ezpSiteAccessMenu::key(), 'list' );
        $this->inject( array( 'AdminSiteAccessList' => array( 'en' ) ) );
        $this->assertNotSame( $key, ezpSiteAccessMenu::key(), 'groups' );
        $this->inject( array(), array(), 'site' );
        $this->assertNotSame( $key, ezpSiteAccessMenu::key(), 'the siteaccess in use' );
    }

    /** Compiled templates call the class at every render: the addresses are the request's */
    public function testTheOperatorsAreWorkedOutAtEveryRender()
    {
        $this->inject( array( 'AdminSiteAccessList' => array( 'site_admin' ) ) );
        $root = dirname( __DIR__, 3 );
        $dir = $root . '/var/tmp/phpunit-site-menu-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( $dir . '/compiled', 0777, true );
        $source = "{siteaccess_menu_key()}|{siteaccess_menu().groups.0.items.0.url}|{siteaccess_menu().front.identifier}";
        file_put_contents( $dir . '/menu.tpl', $source );
        $settings = isset( $GLOBALS['eZTemplateCompilerSettings'] ) ? array( $GLOBALS['eZTemplateCompilerSettings'] ) : null;
        $basics = array_key_exists( 'eZSiteBasics', $GLOBALS ) ? array( $GLOBALS['eZSiteBasics'] ) : null;
        try
        {
            foreach ( array( false, true ) as $compiled )
            {
                $GLOBALS['eZTemplateCompilerSettings']['compile'] = $compiled;
                $GLOBALS['eZTemplateCompilerSettings']['compilation-directory'] = $dir . '/compiled';
                if ( $compiled )
                    unset( $GLOBALS['eZSiteBasics'] );
                else
                    $GLOBALS['eZSiteBasics']['no-cache-adviced'] = true;
                foreach ( array( 'www.example.invalid', 'other.example.invalid' ) as $host )
                {
                    $_SERVER['HTTP_HOST'] = $host;
                    $tpl = new eZTemplate();
                    $tpl->setAutoloadPathList( array( 'lib/eztemplate/classes/', 'kernel/common/' ) );
                    $tpl->autoload();
                    $html = $tpl->fetch( $dir . '/menu.tpl' );
                    $this->assertSame( array(), $tpl->errorLog() );
                    $this->assertSame( ezpSiteAccessMenu::key() . '|https://' . $host . '/' . self::index() . 'en/|site', $html,
                                       ( $compiled ? 'compiled' : 'interpreted' ) . ", $host" );
                }
            }
            $compiledSource = '';
            foreach ( glob( $dir . '/compiled/*.php' ) as $file )
                $compiledSource .= file_get_contents( $file );
            $this->assertStringContainsString( 'ezpSiteAccessMenu::key()', $compiledSource );
            $this->assertStringContainsString( 'ezpSiteAccessMenu::current()', $compiledSource );
        }
        finally
        {
            if ( $settings === null )
                unset( $GLOBALS['eZTemplateCompilerSettings'] );
            else
                $GLOBALS['eZTemplateCompilerSettings'] = $settings[0];
            if ( $basics === null )
                unset( $GLOBALS['eZSiteBasics'] );
            else
                $GLOBALS['eZSiteBasics'] = $basics[0];
            eZDir::recursiveDelete( $dir );
        }
    }

    /**
     * Renders parts/a4_header_site.tpl with the real template engine: a copy in var/tmp reads the menu from the variable
     * $menu instead of siteaccess_menu(), and the site's address from $site instead of siteaccess_url().
     */
    private function render( array $menu, $loggedIn = true )
    {
        $root = dirname( __DIR__, 3 );
        $source = self::file( 'design/admin4/templates/parts/a4_header_site.tpl' );
        $this->assertSame( 1, substr_count( $source, 'siteaccess_menu( $current_user )' ) );
        $this->assertSame( 1, substr_count( $source, 'siteaccess_url()|wash' ) );
        $dir = $root . '/var/tmp/phpunit-header-site-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( $dir, 0777, true );
        $copy = $dir . '/a4_header_site.tpl';
        file_put_contents( $copy, str_replace( array( 'siteaccess_menu( $current_user )', 'siteaccess_url()|wash' ), array( '$menu', '$site|wash' ), $source ) );
        $basics = array_key_exists( 'eZSiteBasics', $GLOBALS ) ? array( $GLOBALS['eZSiteBasics'] ) : null;
        $GLOBALS['eZSiteBasics']['no-cache-adviced'] = true;
        try
        {
            $tpl = eZTemplate::factory();
            $tpl->setVariable( 'current_user', array( 'is_logged_in' => $loggedIn ) );
            $tpl->setVariable( 'menu', $menu );
            $tpl->setVariable( 'site', 'https://www.example.invalid/site/' );
            $html = $tpl->fetch( $copy );
            $this->assertSame( array(), $tpl->errorLog() );
            return $html;
        }
        finally
        {
            eZTemplate::resetInstance();
            if ( $basics === null )
                unset( $GLOBALS['eZSiteBasics'] );
            else
                $GLOBALS['eZSiteBasics'] = $basics[0];
            unlink( $copy );
            rmdir( $dir );
        }
    }

    private static function menu()
    {
        $info = self::info();
        return ezpSiteAccessMenu::build( array( 'site', 'en', 'de', 'site_admin' ), 'site', 'site_admin', array(),
                                         function ( $name ) use ( $info ) {
                                             $data = $info( $name );
                                             if ( $name === 'en' )
                                                 $data['site_name'] = 'A <b>"bold"</b> site';
                                             return $data;
                                         }, self::url() );
    }

    public function testTheGlobeOpensADisclosureOfPlainLinks()
    {
        $html = $this->render( self::menu() );
        $open = ezpI18n::tr( 'design/admin/pagelayout', 'Open the site' );
        $label = ezpI18n::tr( 'design/admin/pagelayout', 'Open the site or another siteaccess' );
        $wash = function ( $text ) { return htmlspecialchars( $text, ENT_QUOTES ); };

        // the link stays (for a browser without the script); the button and its panel wait for the script
        $this->assertSame( 1, substr_count( $html, '<a class="site-preview a4-header-btn" href="https://www.example.invalid/site/" title="' . $wash( $open ) . '" aria-label="' . $wash( $open ) . '">' ) );
        $this->assertStringContainsString( '<div id="a4-site-menu-wrap" class="a4-site-menu-wrap" hidden>', $html );
        $this->assertStringContainsString( '<button type="button" id="a4-site-toggle" class="a4-header-btn a4-site-toggle" aria-expanded="false" aria-controls="a4-site-menu" title="' . $wash( $label ) . '">', $html );
        $this->assertStringContainsString( '<span class="a4-sr-only">' . $wash( $label ) . '</span>', $html, 'the icon button has a name' );
        $this->assertStringContainsString( '<div id="a4-site-menu" class="a4-site-menu" hidden>', $html );
        $this->assertStringNotContainsString( 'role="menu', $html, 'a disclosure of plain links' );
        $this->assertStringNotContainsString( 'target=', $html, 'every address opens in the same window' );

        // the site first, then the groups, each list named by its heading
        $panel = substr( $html, strpos( $html, '<div id="a4-site-menu"' ) );
        preg_match_all( '#<a [^>]*href="([^"]*)"#', $panel, $links );
        $this->assertSame( array( 'https://www.example.invalid/site/', 'https://www.example.invalid/en/', 'https://www.example.invalid/de/',
                                  'https://www.example.invalid/site_admin/' ), $links[1] );
        $this->assertMatchesRegularExpression( '#<a class="a4-site-menu-front" href="https://www.example.invalid/site/">\s*<span class="a4-site-menu-name">' . preg_quote( $wash( $open ), '#' ) . '</span>\s*<span class="a4-site-menu-meta"><span lang="en-GB">Example</span> · <span class="a4-site-menu-url" translate="no">www.example.invalid/site</span></span>#', $panel );
        foreach ( array( 'site' => 'Sites', 'admin' => 'Administration' ) as $group => $heading )
        {
            $this->assertStringContainsString( '<p class="a4-site-menu-heading" id="a4-site-menu-' . $group . '">' . $wash( ezpI18n::tr( 'design/admin/pagelayout', $heading ) ) . '</p>', $panel );
            $this->assertStringContainsString( '<ul class="a4-site-menu-list" aria-labelledby="a4-site-menu-' . $group . '">', $panel );
        }
        $this->assertLessThan( strpos( $panel, 'id="a4-site-menu-admin"' ), strpos( $panel, 'id="a4-site-menu-site"' ) );

        // each entry: name in its language, identifier, locale, address; values escaped
        $this->assertStringContainsString( '<span class="a4-site-menu-name" lang="en-US">A &lt;b&gt;&quot;bold&quot;&lt;/b&gt; site</span>', $panel );
        $this->assertStringContainsString( '<span class="a4-site-menu-meta"><span translate="no">de</span> · <span translate="no">ger-DE</span></span>', $panel );
        $this->assertStringContainsString( '<span class="a4-site-menu-url" translate="no">www.example.invalid/de</span>', $panel );
        // the one in use
        $this->assertSame( 1, substr_count( $panel, 'aria-current' ) );
        $this->assertStringContainsString( '<a href="https://www.example.invalid/site_admin/" aria-current="true">', $panel );
        $this->assertStringContainsString( '<span class="a4-site-menu-here">' . $wash( ezpI18n::tr( 'design/admin/pagelayout', 'in use' ) ) . '</span>', $panel );
    }

    public function testWithoutTheMenuTheGlobeIsTheLinkItWas()
    {
        $off = ezpSiteAccessMenu::build( array( 'site' ), 'site', '', array(), self::info(), self::url() );
        foreach ( array( 'site alone' => array( $off, true ), 'anonymous' => array( self::menu(), false ) ) as $case => list( $menu, $loggedIn ) )
        {
            $html = $this->render( $menu, $loggedIn );
            $this->assertStringContainsString( '<a class="site-preview a4-header-btn" href="https://www.example.invalid/site/"', $html, $case );
            $this->assertStringNotContainsString( 'a4-site-menu', $html, $case );
            $this->assertStringNotContainsString( '<button', $html, $case );
        }
        // anonymous pages (the 401 page draws the frame) do not even ask for the list
        $source = self::file( 'design/admin4/templates/parts/a4_header_site.tpl' );
        $this->assertStringContainsString( "{if \$current_user.is_logged_in}{set \$a4_site_menu = siteaccess_menu( \$current_user )}{/if}", $source );
    }

    /** One template in admin4 and admin4l, inside header cache blocks keyed by siteaccess_menu_key() */
    public function testBothDesignsDrawItInsideKeyedCacheBlocks()
    {
        foreach ( array( 'design/admin4/templates/page_topmenu.tpl', 'design/admin4l/templates/explayouts/block/admin_theme_switch.tpl' ) as $path )
        {
            $tpl = self::file( $path );
            $this->assertSame( 1, substr_count( $tpl, "{include uri='design:parts/a4_header_site.tpl'}" ), $path );
            $this->assertStringNotContainsString( 'siteaccess_url()', $tpl, "$path: the address is the part's" );
            $this->assertLessThan( strpos( $tpl, '<div id="header-theme"' ), strpos( $tpl, "{include uri='design:parts/a4_header_site.tpl'}" ), "$path: before light/dark" );
        }
        foreach ( array( 'design/admin4/templates/pagelayout.tpl', 'design/admin4l/templates/pagelayout.tpl' ) as $path )
            $this->assertSame( 2, preg_match_all( '/\{cache-block keys=array\( [^\n}]*siteaccess_url\(\), siteaccess_menu_key\(\), /', self::file( $path ) ), $path );
        $this->assertStringContainsString( "'ezpSiteAccessMenuOperator'", self::file( 'autoload/ezp_kernel.php' ) );
        $this->assertStringContainsString( "'operator_names' => array( 'siteaccess_menu', 'siteaccess_menu_key' )", self::file( 'kernel/common/eztemplateautoload.php' ) );
    }

    public function testTheSettingsAreDocumentedWithTheirDefaults()
    {
        $ini = self::file( 'settings/site.ini' );
        $section = substr( $ini, strpos( $ini, "[AdminSiteAccessMenu]\n" ) );
        $section = substr( $section, 0, strpos( $section, "\n[", 1 ) );
        foreach ( array( "\nShow=enabled\n", "\nList=related\n", "\nHiddenSiteAccessList[]\n", "\nAdminSiteAccessList[]\n", "\nListAdministrations=disabled\n" ) as $line )
            $this->assertStringContainsString( $line, $section, trim( $line ) );
    }

    public function testTheScriptShowsTheButtonAndKeepsTheKeysOfTheOtherMenus()
    {
        $js = self::file( 'design/admin4/javascript/admin4.js' );
        $this->assertStringContainsString( "disclosure(button, panel);", $js );
        $this->assertStringContainsString( "siteMenu();", $js );
        $this->assertStringContainsString( "#a4-site-toggle[aria-expanded=\"true\"]", self::file( 'design/admin4/javascript/a4_mega_menu.js' ), 'a mega menu does not open over it on hover' );

        $node = trim( (string)shell_exec( 'command -v node 2>/dev/null' ) );
        if ( $node === '' )
            $this->markTestSkipped( 'node is not installed' );
        $out = (string)shell_exec( escapeshellarg( $node ) . ' ' . escapeshellarg( __DIR__ . '/js/site_menu.js' ) . ' '
                                   . escapeshellarg( dirname( __DIR__, 3 ) . '/design/admin4/javascript/admin4.js' ) . ' 2>&1' );
        $runs = array();
        foreach ( array_filter( explode( "\n", trim( $out ) ) ) as $line )
        {
            $run = json_decode( $line, true );
            $this->assertIsArray( $run, $out );
            $runs[$run['scenario']] = $run;
        }
        $closed = array( 'wrapHidden' => false, 'linkHidden' => true, 'expanded' => 'false', 'panelHidden' => true );
        $opened = array( 'wrapHidden' => false, 'linkHidden' => true, 'expanded' => 'true', 'panelHidden' => false );
        $expect = array(
            'init' => array( 'wrapHidden' => false, 'linkHidden' => true, 'expanded' => null, 'panelHidden' => true ),
            'disabled' => array( 'wrapHidden' => true, 'linkHidden' => false, 'expanded' => null, 'panelHidden' => true ),
            'clickOpens' => $opened,
            'clickTwiceCloses' => $closed,
            'arrowDownOpensAndFocusesTheSite' => $opened + array( 'active' => 'front' ),
            'arrowsMove' => $opened + array( 'active' => 'en' ),
            'endAndHome' => $opened + array( 'active' => 'front' ),
            'arrowUpFromTheFirstGoesBack' => $opened + array( 'active' => 'site button' ),
            'escapeClosesAndReturnsTheFocus' => $closed + array( 'active' => 'site button' ),
            'clickOutsideCloses' => $closed,
            'userMenuClosesTheSiteMenu' => $closed + array( 'userExpanded' => 'true' ),
            'focusLeavingCloses' => $closed,
            'siteMenuClosesThePhoneMenu' => $opened + array( 'phoneOpen' => false ),
            'phoneMenuClosesTheSiteMenu' => $closed + array( 'phoneOpen' => true ),
        );
        foreach ( $expect as $scenario => $state )
        {
            $this->assertArrayHasKey( $scenario, $runs, $out );
            $this->assertSame( $state, array_intersect_key( $runs[$scenario], $state ), $scenario );
        }
    }

    public function testTheStylesUseTokensForcedColorsAndAPhonePlacement()
    {
        $css = self::css();
        $rules = AdminBaseStylesheetsTest::rules( $css );
        $checked = 0;
        foreach ( $rules as $selector => $declarations )
        {
            if ( strpos( $selector, 'a4-site-menu' ) === false )
                continue;
            foreach ( $declarations as $property => $value )
            {
                $checked++;
                $this->assertDoesNotMatchRegularExpression( '/#[0-9a-f]{3,8}\b|\brgba?\(|\bhsla?\(|\b(white|black|red|gray|grey)\b/i', $value, "$selector { $property: $value }" );
            }
        }
        $this->assertGreaterThan( 30, $checked );
        // [hidden] wins over the button's display (the link is hidden by the script)
        $this->assertSame( 'none', $rules['.a4-site-menu-wrap[hidden], .a4-header-btn[hidden], .a4-site-menu[hidden]']['display'] );
        $this->assertSame( $rules['.a4-user-menu-links a:focus-visible']['outline'], $rules['.a4-site-menu a:focus-visible']['outline'] );
        $this->assertStringContainsString( '.a4-site-menu { position: absolute; top: calc(100% + var(--a4-space-2)); inset-inline-end: 0; z-index: var(--a4-z-popover);', preg_replace( '/\s+/', ' ', $css ), 'below the globe, as the user menu' );
        $forced = AdminBaseStylesheetsTest::rules( $css, true );
        $this->assertSame( 'ButtonText', $forced['.a4-site-menu']['border-color'] );
        $this->assertSame( '2px solid Highlight', $forced['.a4-site-menu a[aria-current="true"]']['outline'] );
        $flat = preg_replace( '/\s+/', ' ', $css );
        $this->assertStringContainsString( 'max-height: calc(100vh - var(--header-height) - var(--a4-space-5)); max-height: calc(100dvh - var(--header-height) - var(--a4-space-5));', $flat, 'the visible height on iOS, 100vh where dvh is unknown' );
        $this->assertStringContainsString( '@media screen and (max-width: 575.98px) { .a4-site-menu { position: fixed; top: calc(var(--header-height) + var(--a4-space-2)); inset-inline-start: var(--a4-space-4); inset-inline-end: var(--a4-space-4); z-index: var(--a4-z-menu);', $flat, 'under the header on a phone, with the gutters' );
    }

    public function testTheTextsAreTranslated()
    {
        $texts = array( 'ger-DE' => array( 'Open the site or another siteaccess' => 'Website oder einen anderen Siteaccess öffnen',
                                           'Sites' => 'Websites', 'Administration' => 'Administration', 'in use' => 'aktuell' ),
                        'eng-US' => array( 'Open the site or another siteaccess' => 'Open the site or another siteaccess',
                                           'Sites' => 'Sites', 'Administration' => 'Administration', 'in use' => 'in use' ) );
        foreach ( $texts as $language => $pairs )
        {
            $ts = self::file( "share/translations/$language/translation.ts" );
            $context = substr( $ts, strpos( $ts, '<name>design/admin/pagelayout</name>' ) );
            $context = substr( $context, 0, strpos( $context, '</context>' ) );
            foreach ( $pairs as $source => $translation )
                $this->assertSame( 1, substr_count( $context, "<source>$source</source>\n        <translation>$translation</translation>" ), "$language: $source" );
        }
    }
}
