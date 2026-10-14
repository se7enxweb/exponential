<?php
/**
 * The Velocity tab of the Exp Debug bar: where the request entered and which process rendered the report
 * (lib/ezutils/classes/expdebugbarreport.php, expDebugBarReport::engineFacts()):
 *   - a Velocity worker, the Velocity parent, php-cgi started by Velocity, PHP-FPM behind Apache (or nginx, or
 *     nothing named), mod_php, FrankenPHP, the command line and an unknown SAPI, each with its sentence
 *   - the entry: scheme and port (SERVER_PORT, else the scheme's default) and the forwarded headers seen; headers a
 *     client can send never make a PHP-FPM report look like Velocity
 *   - the rendered tab: the sentence in place of "Not running on Velocity.", the data the script compares with the
 *     address and the load time, no style attributes, the privileged rows only for the privileged
 *   - the script: says a report came from a cache when its port or its age does not fit, every word translated
 *   - eZDebugReportNoStore(): a page with the report is "private, no-store", nothing for no report
 *
 * No database: the server variables and the probe of the process are passed in.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group lib
 * @group ezutils
 */

class expDebugBarReportEngineTest extends PHPUnit\Framework\TestCase
{
    const WORKER_PARENT = '/usr/bin/php -d opcache.enable_cli=1 /srv/site/vendor/se7enxweb/exponential-velocity/qbixserver.php --port=8088 --https-port=8080';

    private $root;

    protected function setUp(): void
    {
        $this->root = dirname( __DIR__, 4 );
    }

    private static function probe( array $over = array() )
    {
        return $over + array( 'sapi' => 'fpm-fcgi', 'velocity' => null, 'pid' => 244909, 'parent_pid' => 1000,
                              'parent_cmd' => 'php-fpm: master process (/opt/plesk/php/8.5/etc/php-fpm.conf)', 'time' => 1791976082.092 );
    }

    private static function velocityServer( array $over = array() )
    {
        return $over + array( 'SERVER_SOFTWARE' => 'QbixServer/1.5.0', 'SERVER_PORT' => '8080', 'HTTPS' => 'on',
                              'REQUEST_SCHEME' => 'https', 'HTTP_HOST' => 'www.example.com:8080' );
    }

    private static function apacheServer( array $over = array() )
    {
        return $over + array( 'SERVER_SOFTWARE' => 'Apache', 'SERVER_PORT' => '443', 'HTTPS' => 'on',
                              'REQUEST_SCHEME' => 'https', 'HTTP_HOST' => 'www.example.com' );
    }

    public function testVelocityWorker()
    {
        $f = expDebugBarReport::engineFacts( self::velocityServer(),
            self::probe( array( 'sapi' => 'cli', 'velocity' => 'v0.0.4.52+a2db229499', 'pid' => 289367, 'parent_cmd' => self::WORKER_PARENT ) ) );
        $this->assertSame( 'velocity-worker', $f['renderer'] );
        $this->assertTrue( $f['velocity'] );
        $this->assertSame( 'Exponential Velocity v0.0.4.52+a2db229499', $f['received_by'] );
        $this->assertSame( 'Velocity worker', $f['rendered_by'] );
        $this->assertSame( 'Received by Velocity on port %port and rendered by a Velocity worker.', $f['sentence'] );
        $this->assertSame( '8080', $f['arguments']['%port'] );
        $this->assertSame( array( 'scheme' => 'https', 'port' => 8080, 'host' => 'www.example.com:8080', 'forwarded' => array() ), $f['entry'] );
        $this->assertSame( 289367, $f['pid'] );
        $this->assertSame( 1791976082.092, $f['rendered_at'] );
    }

    public function testVelocityParentHasNoQbixParent()
    {
        foreach ( array( '/usr/lib/systemd/systemd --switched-root', '', 'bash' ) as $parent )
        {
            $f = expDebugBarReport::engineFacts( self::velocityServer(),
                self::probe( array( 'sapi' => 'cli', 'velocity' => 'v0.0.4.52', 'parent_cmd' => $parent ) ) );
            $this->assertSame( 'velocity-parent', $f['renderer'], $parent );
            $this->assertTrue( $f['velocity'] );
            $this->assertSame( 'Received by Velocity on port %port and rendered by the Velocity parent process (no worker).', $f['sentence'] );
        }
    }

    public function testPhpCgiStartedByVelocity()
    {
        $f = expDebugBarReport::engineFacts( self::velocityServer(), self::probe( array( 'sapi' => 'cgi-fcgi' ) ) );
        $this->assertSame( 'velocity-cgi', $f['renderer'] );
        $this->assertTrue( $f['velocity'] );
        $this->assertSame( 'Exponential Velocity', $f['received_by'] );
        $this->assertStringContainsString( 'rendered by php-cgi', $f['sentence'] );
    }

    /** The owner's case: the report of PHP-FPM behind Apache (port 443), whatever page later carried it. */
    public function testPhpFpmBehindApache()
    {
        $f = expDebugBarReport::engineFacts( self::apacheServer( array( 'HTTP_X_FORWARDED_FOR' => '192.0.2.10', 'HTTP_X_REAL_IP' => '192.0.2.10' ) ),
                                             self::probe() );
        $this->assertSame( 'php-fpm', $f['renderer'] );
        $this->assertFalse( $f['velocity'] );
        $this->assertSame( 'Apache', $f['received_by'] );
        $this->assertSame( 'PHP-FPM', $f['rendered_by'] );
        $this->assertSame( 'Served by %server / PHP-FPM (port %port), not by Velocity.', $f['sentence'] );
        $this->assertSame( array( '%port' => '443', '%server' => 'Apache', '%sapi' => 'fpm-fcgi' ), $f['arguments'] );
        $this->assertSame( array( 'X-Forwarded-For', 'X-Real-IP' ), $f['entry']['forwarded'] );
        $this->assertSame( 'Served by Apache / PHP-FPM (port 443), not by Velocity.', strtr( $f['sentence'], $f['arguments'] ) );
    }

    public function testServerSoftwareVersionIsLeftOut()
    {
        $f = expDebugBarReport::engineFacts( self::apacheServer( array( 'SERVER_SOFTWARE' => 'nginx/1.26.2', 'SERVER_PORT' => '8443' ) ), self::probe() );
        $this->assertSame( 'nginx', $f['received_by'] );
        $this->assertSame( 'Served by nginx / PHP-FPM (port 8443), not by Velocity.', strtr( $f['sentence'], $f['arguments'] ) );
    }

    public function testPhpFpmWithoutServerSoftware()
    {
        $f = expDebugBarReport::engineFacts( array( 'SERVER_PORT' => '80' ), self::probe() );
        $this->assertSame( 'Served by PHP-FPM (port %port), not by Velocity.', $f['sentence'] );
        $this->assertSame( 'http', $f['entry']['scheme'] );
        $this->assertSame( 'fpm-fcgi', $f['received_by'] );
    }

    /** What a client can send (forwarded headers, a made-up X-Exp-Velocity, a Host with :8080) never turns PHP-FPM into Velocity. */
    public function testClientHeadersDoNotMakeVelocity()
    {
        $f = expDebugBarReport::engineFacts( self::apacheServer( array(
            'HTTP_X_EXP_VELOCITY' => 'proxied;reason=overload', 'HTTP_HOST' => 'www.example.com:8080',
            'HTTP_X_FORWARDED_HOST' => 'www.example.com:8080', 'HTTP_VIA' => '1.1 QbixServer' ) ), self::probe() );
        $this->assertSame( 'php-fpm', $f['renderer'] );
        $this->assertFalse( $f['velocity'] );
        $this->assertSame( 443, $f['entry']['port'] );
        $this->assertSame( array( 'X-Forwarded-Host', 'Via' ), $f['entry']['forwarded'] );
    }

    /** QBIX_SERVER_VERSION alone says Velocity, also when a script changed SERVER_SOFTWARE. */
    public function testVelocityVersionWinsOverServerSoftware()
    {
        $f = expDebugBarReport::engineFacts( self::apacheServer(),
            self::probe( array( 'sapi' => 'cli', 'velocity' => 'v0.0.4.52', 'parent_cmd' => self::WORKER_PARENT ) ) );
        $this->assertSame( 'velocity-worker', $f['renderer'] );
        $this->assertSame( 'Exponential Velocity v0.0.4.52', $f['received_by'] );
    }

    public static function otherSapis()
    {
        return array(
            'mod_php'    => array( 'apache2handler', 'mod-php', 'Served by Apache with mod_php (port %port), not by Velocity.' ),
            'FrankenPHP' => array( 'frankenphp', 'frankenphp', 'Served by FrankenPHP (port %port), not by Velocity.' ),
            'cli'        => array( 'cli', 'cli', 'Rendered on the command line (%sapi), not for a web request.' ),
            'phpdbg'     => array( 'phpdbg', 'cli', 'Rendered on the command line (%sapi), not for a web request.' ),
            'litespeed'  => array( 'litespeed', 'other', 'Served by %server (%sapi, port %port), not by Velocity.' ),
            'cgi alone'  => array( 'cgi-fcgi', 'other', 'Served by %server (%sapi, port %port), not by Velocity.' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('otherSapis')]
    public function testOtherSapis( $sapi, $renderer, $sentence )
    {
        $f = expDebugBarReport::engineFacts( self::apacheServer(), self::probe( array( 'sapi' => $sapi ) ) );
        $this->assertSame( $renderer, $f['renderer'] );
        $this->assertFalse( $f['velocity'] );
        $this->assertSame( $sentence, $f['sentence'] );
    }

    public static function ports()
    {
        return array(
            'https default'     => array( array( 'HTTPS' => 'on' ), 'https', 443 ),
            'http default'      => array( array(), 'http', 80 ),
            'HTTPS off'         => array( array( 'HTTPS' => 'off' ), 'http', 80 ),
            'scheme only'       => array( array( 'REQUEST_SCHEME' => 'https' ), 'https', 443 ),
            'port given'        => array( array( 'SERVER_PORT' => '8088' ), 'http', 8088 ),
            'port not a number' => array( array( 'SERVER_PORT' => '80x', 'HTTPS' => 'on' ), 'https', 443 ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('ports')]
    public function testEntryPort( array $server, $scheme, $port )
    {
        $f = expDebugBarReport::engineFacts( $server, self::probe() );
        $this->assertSame( $scheme, $f['entry']['scheme'] );
        $this->assertSame( $port, $f['entry']['port'] );
    }

    public function testProbeOfThisProcess()
    {
        $p = expDebugBarReport::engineProbe();
        $this->assertSame( PHP_SAPI, $p['sapi'] );
        $this->assertSame( getmypid(), $p['pid'] );
        $this->assertSame( defined( 'QBIX_SERVER_VERSION' ), $p['velocity'] !== null );
        $this->assertEqualsWithDelta( microtime( true ), $p['time'], 5 );
    }

    private static function velocityHtml( $privileged )
    {
        $m = new ReflectionMethod( 'expDebugBarReport', 'velocityHtml' );
        return $m->invoke( null, $privileged );
    }

    public function testRenderedTab()
    {
        $html = self::velocityHtml( true );
        $this->assertStringNotContainsString( 'Not running on Velocity.', $html );
        $this->assertStringNotContainsString( ' style=', $html );
        $this->assertMatchesRegularExpression( '/<div class="exp-debug-engine" data-renderer="cli" data-velocity="0" data-entry-port="\d+" data-rendered-at="\d+\.\d{3}" data-rendered-by="PHP cli ' . getmypid() . '">/', $html );
        $this->assertStringContainsString( 'Rendered on the command line (cli), not for a web request.', $html );
        foreach ( array( 'Entered', 'Received by', 'Rendered by', 'PHP', 'Process', 'Host', 'Rendered at', 'Cached' ) as $row )
            $this->assertStringContainsString( '<tr><th>' . $row . '</th>', $html, $row );
        $this->assertStringContainsString( 'private, no-store', $html );
        $this->assertStringContainsString( 'exp-debug-velocity-cache', $html );

        $visitor = self::velocityHtml( false );
        $this->assertStringNotContainsString( '<th>Process</th>', $visitor );
        $this->assertStringNotContainsString( '<th>Host</th>', $visitor );
        $this->assertStringNotContainsString( 'exp-debug-velocity-cache', $visitor );
        $this->assertStringContainsString( 'data-rendered-by="PHP cli"', $visitor, 'no process id for a visitor' );
    }

    public function testScriptSaysWhenTheReportCameFromACache()
    {
        $js = file_get_contents( $this->root . '/design/standard/javascript/expdebugbar.js' );
        $this->assertStringContainsString( "root.querySelector('.exp-debug-engine')", $js );
        $this->assertStringContainsString( "getAttribute('data-entry-port')", $js );
        $this->assertStringContainsString( "getAttribute('data-rendered-at')", $js );
        $this->assertStringContainsString( '(entry && entry !== port) || age > 120', $js );
        $this->assertStringContainsString( "'data-exp-debug-replayed': '1'", $js );
    }

    public function testNewWordsAreTranslated()
    {
        $words = array(
            'Received by Velocity on port %port and rendered by a Velocity worker.',
            'Received by Velocity on port %port and rendered by the Velocity parent process (no worker).',
            'Received by Velocity on port %port and rendered by php-cgi, which Velocity starts for this script (Q.webserver.cgi.patterns).',
            'Rendered on the command line (%sapi), not for a web request.',
            'Served by %server / PHP-FPM (port %port), not by Velocity.', 'Served by PHP-FPM (port %port), not by Velocity.',
            'Served by Apache with mod_php (port %port), not by Velocity.', 'Served by FrankenPHP (port %port), not by Velocity.',
            'Served by %server (%sapi, port %port), not by Velocity.',
            'port %port', 'forwarded headers: %headers', 'Entered', 'Received by', 'Rendered by', 'Rendered at', 'Cached',
            'No: a page with this report is rendered for each request and kept by no cache (Cache-Control: private, no-store).',
            'Velocity worker', 'Velocity parent process',
            'Not available: opcache_get_status() is disabled for this PHP (disable_functions).', 'Not available: OPcache is not loaded in this PHP.',
            'This report was rendered at %time by %renderer for a request on port %entry, %age before this page was loaded on port %port: the page came from a cache, not from this request.',
            '%count s', '%count min',
        );
        $listed = expDebugBarReport::scriptStrings();
        foreach ( array( 'This report was rendered at %time by %renderer for a request on port %entry, %age before this page was loaded on port %port: the page came from a cache, not from this request.', '%count s', '%count min' ) as $source )
            $this->assertContains( $source, $listed, 'the script looks it up' );
        foreach ( array( 'eng-US', 'ger-DE' ) as $locale )
        {
            $xml = simplexml_load_file( $this->root . "/share/translations/$locale/translation.ts" );
            $context = $xml->xpath( "/TS/context[name='" . expDebugBarReport::CONTEXT . "']" );
            $this->assertCount( 1, $context, $locale );
            $this->assertCount( 0, $context[0]->xpath( 'message[source="Not running on Velocity."]' ), "$locale: the old hint is gone" );
            foreach ( $words as $source )
            {
                $found = $context[0]->xpath( "message[source=\"$source\"]/translation" );
                $this->assertCount( 1, $found, "$locale: $source" );
                $this->assertNotSame( '', trim( (string)$found[0] ), "$locale: $source" );
            }
        }
    }

    public function testNoStoreForAPageWithTheReport()
    {
        require_once $this->root . '/kernel/private/classes/global_functions.php';
        $this->assertFalse( eZDebugReportNoStore( null ), 'debug output off' );
        $this->assertFalse( eZDebugReportNoStore( '' ) );
        $this->assertFalse( eZDebugReportNoStore( "  \n" ) );
        // A report: the header is sent while headers can still be sent. In a whole PHPUnit run earlier tests have
        // printed output, so headers count as sent there and nothing may be sent (header() would warn)
        $this->assertSame( !headers_sent(), eZDebugReportNoStore( '<div id="debug"></div>' ) );

        $source = file_get_contents( $this->root . '/kernel/private/classes/global_functions.php' );
        $this->assertStringContainsString( "header( 'Cache-Control: private, no-store' );", $source );
        $this->assertMatchesRegularExpression( '/\$report = eZDebug::printReport\(.*?\);\s*eZDebugReportNoStore\( \$report \);\s*return \$report;/s', $source );
    }
}
