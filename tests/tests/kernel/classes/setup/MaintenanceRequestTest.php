<?php
/**
 * While a site is being set up (var/maintenance.json, reason setup) every request to every front controller gets
 * the maintenance page, 503 with Cache-Control no-store and Retry-After, on every engine and from the first
 * request: the marker is read afresh for each one, and no cache answers or stores ahead of it. The site showed
 * through the maintenance page on repeated reloads: the service worker replayed the pages it held (a 503 counted
 * as a server fault), Velocity's cache looked at its pause file twice a second only and stored what workers
 * rendered meanwhile, and with the response cache off it had no pause file at all for the HTTP cache it asks.
 * No database; markers only under var/tmp/maintenance-request-tests/.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class MaintenanceRequestTest extends PHPUnit\Framework\TestCase
{
    private $root;
    private $kernelRoot;
    private $server;
    private $cookie;

    protected function setUp(): void
    {
        $this->kernelRoot = dirname( __DIR__, 5 );
        $this->root = $this->kernelRoot . '/var/tmp/maintenance-request-tests/' . getmypid() . '-' . $this->name();
        if ( !is_dir( $this->root . '/var' ) )
            mkdir( $this->root . '/var', 0775, true );
        @unlink( $this->root . '/' . expMaintenance::MARKER );
        $this->server = $_SERVER;
        $this->cookie = $_COOKIE;
    }

    protected function tearDown(): void
    {
        @unlink( $this->root . '/' . expMaintenance::MARKER );
        $_SERVER = $this->server;
        $_COOKIE = $this->cookie;
    }

    private function setupMarker( array $extra = array() )
    {
        // As the kickstarter's expMaintenance::beginRun() writes it
        file_put_contents( $this->root . '/' . expMaintenance::MARKER, json_encode( $extra + array(
            'reason' => 'setup', 'run' => 'r-test', 'message' => '', 'until' => 0, 'allow_ips' => array(),
            'allow_paths' => array(), 'page' => '', 'since' => time() ) ) . "\n" );
    }

    /** @return array [answered with the maintenance page, the output] */
    private function request( $uri, $remote = '203.0.113.9' )
    {
        $_SERVER['REQUEST_URI'] = $uri;
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REMOTE_ADDR'] = $remote;
        ob_start();
        $sent = expMaintenance::check( $this->root );
        return array( $sent, ob_get_clean() );
    }

    public static function pages()
    {
        return array(
            'front page' => array( '/' ),
            'content page' => array( '/Fit-Healthy' ),
            'module view' => array( '/content/view/full/2' ),
            'about page' => array( '/ezinfo/about' ),
            'siteaccess in the path' => array( '/eng/Fit-Healthy' ),
            'query string' => array( '/?page=2' ),
            'REST API (index_rest.php)' => array( '/api/ezp/v2/content/objects/1' ),
            'tree menu (index_treemenu.php)' => array( '/content/treemenu/?node_id=2' ),
            'ezjscore call' => array( '/ezjscore/call/ezjsc::time' ),
            'user login' => array( '/user/login' ),
            'admin' => array( '/admin/dashboard' ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider('pages')]
    public function testEveryPageIsTheMaintenancePageWhileTheSiteIsSetUp( $uri )
    {
        $this->setupMarker();
        list( $sent, $html ) = $this->request( $uri );
        $this->assertTrue( $sent, $uri . ' is answered with the maintenance page' );
        $this->assertStringContainsString( 'The site is being set up', $html );
    }

    public function testTheMarkerIsReadAfreshForEveryRequestOfTheSameProcess()
    {
        // A persistent worker: one process, many requests
        list( $sent ) = $this->request( '/' );
        $this->assertFalse( $sent, 'no marker: the site answers' );
        $this->setupMarker();
        list( $sent ) = $this->request( '/' );
        $this->assertTrue( $sent, 'the request right after the setup began gets the maintenance page' );
        unlink( $this->root . '/' . expMaintenance::MARKER );
        list( $sent ) = $this->request( '/' );
        $this->assertFalse( $sent, 'the request right after it ended gets the site' );
    }

    public function testTheHeadersKeepThePageOutOfEveryCacheAndSayWhenToComeBack()
    {
        $now = 1800000000;
        $headers = expMaintenance::headers( array( 'reason' => 'setup' ), $now );
        $this->assertStringContainsString( 'no-store', $headers['Cache-Control'] );
        $this->assertStringContainsString( 'private', $headers['Cache-Control'] );
        $this->assertStringNotContainsString( 'public', $headers['Cache-Control'] );
        $this->assertSame( '120', $headers['Retry-After'], 'no end given: ask again in two minutes' );
        $this->assertSame( 'setup', $headers['X-Exp-Maintenance'], 'the service worker tells this 503 from a fault by it' );
        $this->assertSame( 'noindex', $headers['X-Robots-Tag'] );

        $manual = expMaintenance::headers( array( 'reason' => 'manual', 'until' => $now + 600 ), $now );
        $this->assertSame( '600', $manual['Retry-After'], 'an end given: the seconds until then' );
        $this->assertSame( 'maintenance', $manual['X-Exp-Maintenance'] );
    }

    public function testOnlyTheVisitorsTheMarkerNamesAreLetThrough()
    {
        $this->setupMarker( array( 'allow_ips' => array( '198.51.100.4' ), 'allow_paths' => array( '/setup' ) ) );
        $this->assertFalse( $this->request( '/', '198.51.100.4' )[0], 'an allowed address gets the site' );
        $this->assertFalse( $this->request( '/setup/info' )[0], 'an allowed path gets the site' );
        $this->assertTrue( $this->request( '/setupwizard' )[0], 'a path that only starts like it does not' );
        $this->assertTrue( $this->request( '/' )[0], 'everybody else gets the maintenance page' );

        // The web wizard's own browser, by its cookie
        $token = 'wizard-token-for-the-test';
        $this->setupMarker( array( 'allow_token' => hash( 'sha256', $token ), 'lease' => time() + 600 ) );
        $_COOKIE[expMaintenance::WIZARD_COOKIE] = $token;
        $this->assertFalse( $this->request( '/' )[0], 'the wizard\'s browser gets through' );
        $_COOKIE[expMaintenance::WIZARD_COOKIE] = 'another';
        $this->assertTrue( $this->request( '/' )[0], 'another cookie does not' );
    }

    public function testEveryFrontControllerAsksBeforeSettingsCachesAndKernel()
    {
        foreach ( array( 'index.php', 'index_rest.php', 'index_treemenu.php' ) as $script )
        {
            $source = file_get_contents( $this->kernelRoot . '/' . $script );
            $check = strpos( $source, 'if ( expMaintenance::check( __DIR__ ) )' );
            $this->assertNotFalse( $check, $script . ' asks expMaintenance' );
            // Not behind a plain is_file() of the marker: a persistent worker's remembered stat said "not there"
            $this->assertStringNotContainsString( "if ( is_file( __DIR__ . '/var/maintenance.json' ) )", $source, $script );
            // Before autoload.php, which reads config.php and with it the HTTP cache's early exit
            $autoload = strpos( $source, "autoload.php'" );
            $this->assertNotFalse( $autoload, $script );
            $this->assertLessThan( $autoload, $check, $script . ' asks before the autoloader, config.php and every cache' );
        }
    }

    public function testThePersistentWorkersMemoryOfTheMarkerIsForgottenFirst()
    {
        $source = file_get_contents( $this->kernelRoot . '/kernel/classes/expmaintenance.php' );
        $this->assertMatchesRegularExpression( '/Q_WebServer_CompatFileWrapper::forgetPath\(\s*\$file\s*\)/', $source, 'Velocity: only this path is forgotten' );
        $this->assertMatchesRegularExpression( '/clearstatcache\(\s*true,\s*\$file\s*\)/', $source, 'other engines: PHP\'s stat cache of this path' );
        $this->assertMatchesRegularExpression( '/static function state\([^)]*\)\s*\{[^}]*self::markerExists\(/s', $source );
    }

    public function testVelocityGetsThePauseFileWithTheResponseCacheOffToo()
    {
        $source = file_get_contents( $this->kernelRoot . '/kernel/classes/expvelocity.php' );
        $this->assertSame( 1, substr_count( $source, "\$cache['pauseFile'] =" ), 'one place sets it' );
        $off = strpos( $source, "\$cache['enabled'] = false;" );
        $this->assertNotFalse( $off );
        $this->assertGreaterThan( $off, strpos( $source, "\$cache['pauseFile'] =" ),
            'set after both branches, so the HTTP cache the server asks is paused when its own cache is off' );
    }

    public function testTheHttpCacheStoresNothingRenderedDuringMaintenance()
    {
        $source = file_get_contents( $this->kernelRoot . '/kernel/private/classes/httpcache/ezphttpcachelistener.php' );
        $this->assertMatchesRegularExpression( "/expMaintenance::state\(\s*eZSys::rootDir\(\)\s*\)\s*!==\s*false\s*\)\s*return 'maintenance';/", $source );
    }

    public function testTheServiceWorkerNeverShowsAStoredPageWhileTheSiteAnswersItsMaintenancePage()
    {
        $js = file_get_contents( $this->kernelRoot . '/index.js' );
        $this->assertMatchesRegularExpression( "/const VERSION = 'exp-nav-v([0-9]+)';/", $js );
        preg_match( "/const VERSION = 'exp-nav-v([0-9]+)';/", $js, $m );
        $this->assertGreaterThanOrEqual( 5, (int)$m[1], 'v5 drops what v4 stored' );
        $this->assertStringContainsString( "response.headers.has('X-Exp-Maintenance')", $js );
        $this->assertStringContainsString( 'const stored = inMaintenance ? null : await cache.match(request);', $js, 'no stored page while in maintenance' );
        // The refresh behind a stored page: on the maintenance page every stored page goes and the tab is taken there
        $this->assertMatchesRegularExpression( '/if \(isMaintenance\(response\)\) \{\s*\/\/[^\n]*\n\s*\/\/[^\n]*\n\s*await enterMaintenance\(cache\);/', $js );
        $this->assertStringContainsString( 'client.navigate(client.url)', $js );
        $this->assertMatchesRegularExpression( '/keys\.map\(\(k\) => cache\.delete\(k\)\)/', $js );
        // The network is the server, not the browser's HTTP cache answering for it within a max-age
        $this->assertStringContainsString( "fetch(request, { cache: 'no-cache' })", $js );
        $this->assertSame( 0, preg_match( '/[^.\w]fetch\(request\)/', $js ), 'every fetch goes through fromNetwork()' );
    }
}
