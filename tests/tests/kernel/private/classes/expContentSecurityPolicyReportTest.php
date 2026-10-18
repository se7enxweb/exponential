<?php
/**
 * The reports of the Content Security Policy (expContentSecurityPolicyReport, the view csp/report), without a
 * database:
 *
 *  CSPR-01  report-uri (application/csp-report) and the Reporting API (application/reports+json) are read; reports
 *           of other types are skipped; a body that is neither is null
 *  CSPR-02  a line is bounded and masked: no line break, no quote or second field from a value, no data: payload,
 *           no query secret, no e-mail address, no user name in a URI, the address masked, the sample cut
 *  CSPR-03  handle(): 405 for GET, 415 for another content type, 413 over MaxReportBytes, 400 for no report, 204
 *           with one line per violation (at most MaxViolationsPerReport)
 *  CSPR-04  the rate limits: 429 over ReportsPerMinute of a client and over ReportsPerMinuteTotal; one
 *           abuse/csp-report-flood line in security.log per client and minute; another client is not limited
 *  CSPR-05  the settings: defaults, bounds, a LogFile that is a path is refused
 *  CSPR-06  the kernel registers the view: module.ini, the module, AnonymousAccessList[], PolicyOmitList[], the
 *           site.ini defaults, the autoload entries
 *  CSPR-07  the form token check leaves a report to csp/report alone, and nothing else
 *  CSPR-08  the view answers a status without a body and never prints the report
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expContentSecurityPolicyReportTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $root;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->root = sys_get_temp_dir() . '/cspr-' . bin2hex( random_bytes( 6 ) );
        mkdir( $this->root . '/log', 0777, true );
        mkdir( $this->root . '/cache', 0777, true );
        expRequestClassifier::setInstance( new expRequestClassifier( array(
            'logDir' => $this->root . '/log', 'cacheDir' => $this->root . '/cache', 'salt' => 'test-salt', 'compileCache' => false ) ) );
    }

    protected function tearDown(): void
    {
        expRequestClassifier::setInstance( null );
        self::remove( $this->root );
    }

    private static function remove( $dir )
    {
        if ( !file_exists( $dir ) )
            return;
        if ( is_file( $dir ) || is_link( $dir ) )
        {
            unlink( $dir );
            return;
        }
        foreach ( scandir( $dir ) as $name )
            if ( $name !== '.' && $name !== '..' )
                self::remove( $dir . '/' . $name );
        rmdir( $dir );
    }

    private function report( array $settings = array() )
    {
        return new expContentSecurityPolicyReport( $settings + array( 'logDir' => $this->root . '/log', 'cacheDir' => $this->root . '/cache/csp' ) );
    }

    private static function context( $ip = '203.0.113.9', $siteaccess = 'admin' )
    {
        return expRequestContext::create( array( 'method' => 'POST', 'uri' => '/csp/report', 'siteaccess' => $siteaccess,
                                                'userAgent' => 'Mozilla/5.0 (X11; Linux x86_64) Test', 'ip' => $ip ) );
    }

    private static function cspReport( array $values = array() )
    {
        return json_encode( array( 'csp-report' => $values + array(
            'document-uri' => 'https://example.test/admin/content/view/full/2',
            'referrer' => '',
            'violated-directive' => 'script-src-elem',
            'effective-directive' => 'script-src-elem',
            'original-policy' => "default-src 'self'; script-src 'self'; report-uri /csp/report",
            'disposition' => 'enforce',
            'blocked-uri' => 'inline',
            'line-number' => 12,
            'column-number' => 5,
            'source-file' => 'https://example.test/admin/content/view/full/2',
            'status-code' => 200,
            'script-sample' => 'alert(1)',
        ) ) );
    }

    private static function reportsJson()
    {
        return json_encode( array(
            array( 'type' => 'csp-violation', 'age' => 10, 'url' => 'https://example.test/admin/setup/info',
                   'user_agent' => 'Mozilla/5.0',
                   'body' => array( 'documentURL' => 'https://example.test/admin/setup/info', 'effectiveDirective' => 'img-src',
                                    'blockedURL' => 'https://maps.example.com/tile/1/2/3.png', 'disposition' => 'report',
                                    'lineNumber' => 0, 'statusCode' => 200, 'sample' => '' ) ),
            array( 'type' => 'deprecation', 'url' => 'https://example.test/', 'body' => array( 'id' => 'x' ) ),
            array( 'type' => 'csp-violation', 'url' => 'https://example.test/admin/content/edit/1',
                   'body' => array( 'documentURL' => 'https://example.test/admin/content/edit/1', 'effectiveDirective' => 'style-src-attr',
                                    'blockedURL' => 'inline', 'disposition' => 'report', 'lineNumber' => 3, 'columnNumber' => 9,
                                    'sourceFile' => 'https://example.test/design/admin4/javascript/a.js', 'sample' => 'color: red' ) ),
        ) );
    }

    private function logLines()
    {
        $file = $this->root . '/log/csp.log';
        return is_file( $file ) ? file( $file, FILE_IGNORE_NEW_LINES ) : array();
    }

    /** CSPR-01 */
    public function testBothFormatsAreRead()
    {
        $v = expContentSecurityPolicyReport::parse( self::cspReport() );
        $this->assertCount( 1, $v );
        $this->assertSame( array( 'script-src-elem', 'enforce', 'inline', 'https://example.test/admin/content/view/full/2', 12, 5, 200, 'alert(1)' ),
                           array( $v[0]['directive'], $v[0]['disposition'], $v[0]['blocked'], $v[0]['document'], $v[0]['line'],
                                  $v[0]['column'], $v[0]['status'], $v[0]['sample'] ) );

        // CSP 2 browsers: no effective-directive, the violated one with its sources
        $old = json_decode( self::cspReport(), true );
        unset( $old['csp-report']['effective-directive'] );
        $old['csp-report']['violated-directive'] = "script-src 'self'";
        $this->assertSame( 'script-src', expContentSecurityPolicyReport::parse( json_encode( $old ) )[0]['directive'] );

        $v = expContentSecurityPolicyReport::parse( self::reportsJson() );
        $this->assertCount( 2, $v, 'the deprecation report is skipped' );
        $this->assertSame( array( 'img-src', 'style-src-attr' ), array_column( $v, 'directive' ) );
        $this->assertSame( 'https://maps.example.com/tile/1/2/3.png', $v[0]['blocked'] );
        $this->assertSame( 'https://example.test/design/admin4/javascript/a.js', $v[1]['source'] );

        // a single report object of the Reporting API
        $one = json_decode( self::reportsJson(), true )[0];
        $this->assertCount( 1, expContentSecurityPolicyReport::parse( json_encode( $one ) ) );
        // only reports of other types: a report, without violations
        $this->assertSame( array(), expContentSecurityPolicyReport::parse( json_encode( array( array( 'type' => 'deprecation', 'body' => array() ) ) ) ) );

        foreach ( array( '', 'null', 'not json', '"a string"', '{"csp-report": "x"}', '{"a": 1}', '[1, 2]', '[{"body": {}}]' ) as $body )
            $this->assertNull( expContentSecurityPolicyReport::parse( $body ), $body );
        // too deep to be a report
        $this->assertNull( expContentSecurityPolicyReport::parse( str_repeat( '[', 40 ) . str_repeat( ']', 40 ) ) );
    }

    /** CSPR-02 */
    public function testALineIsBoundedAndMasked()
    {
        $violation = expContentSecurityPolicyReport::parse( self::cspReport( array(
            'effective-directive' => "script-src\nX-Injected: 1",
            'document-uri' => "https://user:secret@example.test/admin/user/edit?password=hunter2&mail=someone@example.org#frag",
            'blocked-uri' => 'data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg==',
            'source-file' => "https://example.test/a.js\r\ninjected=1 \"quoted\"",
            'script-sample' => "x\"y\\z\n" . str_repeat( 'a', 200 ),
            'disposition' => 'something else',
        ) ) )[0];
        $line = expContentSecurityPolicyReport::line( $violation, 'admin"x', "Agent \"quoted\"\nnext" );
        $this->assertStringNotContainsString( "\n", $line );
        $this->assertStringNotContainsString( "\r", $line );
        $this->assertStringStartsWith( 'csp/unknown blocked=data disposition=report document=', $line, 'a directive with a line break is no directive' );
        $this->assertStringNotContainsString( 'base64', $line, 'a data: URI is its scheme only' );
        $this->assertStringNotContainsString( 'hunter2', $line );
        $this->assertStringNotContainsString( 'someone@example.org', $line );
        $this->assertStringNotContainsString( 'secret@', $line, 'no user name or password of a URI' );
        $this->assertStringNotContainsString( '#frag', $line );
        $this->assertStringContainsString( ' siteaccess=adminx ', $line );
        $this->assertSame( 1, substr_count( $line, ' source=' ), 'a value adds no field of its own' );
        $this->assertSame( 1, preg_match( '/ sample="([^"]*)" ua=/', $line, $m ) );
        $this->assertLessThanOrEqual( 40, mb_strlen( $m[1] ) );
        $this->assertStringNotContainsString( '\\', $m[1] );

        $prefix = expRequestClassifier::prefix( '203.0.113.9', true, mktime( 9, 0, 0, 10, 17, 2026 ) );
        $this->assertSame( '[ Oct 17 2026 09:00:00 ] [203.0.x.x] ', $prefix );

        // the keywords as they are; "-" for none
        foreach ( array( 'inline' => 'inline', 'eval' => 'eval', 'wasm-eval' => 'wasm-eval', 'data:' => 'data', 'blob' => 'blob',
                         'self' => 'self', '' => '-', 'blob:https://example.test/1-2' => 'blob',
                         'https://cdn.example.com/x.js?token=abc' => 'https://cdn.example.com/x.js?token=***' ) as $in => $out )
            $this->assertSame( $out, expContentSecurityPolicyReport::blockedForLog( $in ), (string)$in );

        $long = expContentSecurityPolicyReport::uriField( 'https://example.test/' . str_repeat( 'a', 1000 ) );
        $this->assertLessThanOrEqual( expContentSecurityPolicyReport::MAX_URI + 3, strlen( $long ) );
    }

    /** CSPR-03 */
    public function testHandleAnswersAndWritesOneLinePerViolation()
    {
        $r = $this->report();
        $c = self::context();
        $this->assertSame( 405, $r->handle( 'GET', 'application/csp-report', '', $c )['status'] );
        $this->assertSame( 415, $r->handle( 'POST', 'application/x-www-form-urlencoded', 'a=b', $c )['status'] );
        $this->assertSame( 415, $r->handle( 'POST', 'text/plain', self::cspReport(), $c )['status'] );
        $this->assertSame( 413, $r->handle( 'POST', 'application/csp-report', null, $c )['status'] );
        $this->assertSame( 413, $r->handle( 'POST', 'application/csp-report', str_repeat( ' ', 65537 ), $c )['status'] );
        $this->assertSame( 400, $r->handle( 'POST', 'application/csp-report', 'nope', $c )['status'] );
        $this->assertSame( array(), $this->logLines(), 'nothing written for a refused request' );

        $out = $r->handle( 'POST', 'application/csp-report; charset=utf-8', self::cspReport(), $c, mktime( 9, 0, 0, 10, 17, 2026 ) );
        $this->assertSame( array( 'status' => 204, 'reason' => 'ok', 'lines' => 1 ), $out );
        $out = $r->handle( 'POST', 'Application/Reports+JSON', self::reportsJson(), $c );
        $this->assertSame( 2, $out['lines'] );
        $out = $r->handle( 'POST', 'application/json', json_encode( array( array( 'type' => 'deprecation', 'body' => array() ) ) ), $c );
        $this->assertSame( array( 'status' => 204, 'reason' => 'ok', 'lines' => 0 ), $out, 'a report without a violation is taken' );

        $lines = $this->logLines();
        $this->assertCount( 3, $lines );
        $this->assertSame( '[ Oct 17 2026 09:00:00 ] [203.0.x.x] csp/script-src-elem blocked=inline disposition=enforce '
                           . 'document=https://example.test/admin/content/view/full/2 siteaccess=admin line=12 column=5 '
                           . 'source=https://example.test/admin/content/view/full/2 status=200 sample="alert(1)" ua='
                           . substr( sha1( 'Mozilla/5.0 (X11; Linux x86_64) Test' ), 0, 8 ) . ' "Mozilla/5.0 (X11; Linux x86_64) Test"', $lines[0] );
        $this->assertStringContainsString( 'csp/img-src blocked=https://maps.example.com/tile/1/2/3.png disposition=report', $lines[1] );

        // at most MaxViolationsPerReport lines from one report
        $many = array();
        for ( $i = 0; $i < 30; $i++ )
            $many[] = array( 'type' => 'csp-violation', 'body' => array( 'effectiveDirective' => 'img-src', 'blockedURL' => 'data' ) );
        $out = $this->report( array( 'MaxViolationsPerReport' => 5 ) )->handle( 'POST', 'application/reports+json', json_encode( $many ), $c );
        $this->assertSame( 5, $out['lines'] );
    }

    /** CSPR-04 */
    public function testTheRateLimitsRefuseAndFlagAFloodOnce()
    {
        $now = 1792227600; // the start of a minute
        $r = $this->report( array( 'ReportsPerMinute' => 3, 'ReportsPerMinuteTotal' => 1000 ) );
        $a = self::context( '203.0.113.9' );
        $statuses = array();
        for ( $i = 0; $i < 6; $i++ )
            $statuses[] = $r->handle( 'POST', 'application/csp-report', self::cspReport(), $a, $now + $i )['status'];
        $this->assertSame( array( 204, 204, 204, 429, 429, 429 ), $statuses );
        $this->assertCount( 3, $this->logLines() );
        $this->assertSame( 204, $r->handle( 'POST', 'application/csp-report', self::cspReport(), self::context( '198.51.100.7' ), $now + 7 )['status'],
                           'another client is not limited' );
        // a new minute
        $this->assertSame( 204, $r->handle( 'POST', 'application/csp-report', self::cspReport(), $a, $now + 60 )['status'] );

        $security = file( $this->root . '/log/security.log', FILE_IGNORE_NEW_LINES );
        $this->assertCount( 1, $security, 'one line per client and minute, not one per refused report' );
        $this->assertStringContainsString( 'abuse/csp-report-flood confidence=medium 429 POST /csp/report siteaccess=admin', $security[0] );
        $this->assertStringNotContainsString( '203.0.113.9', $security[0] );
        $h = expLogParser::header( $security[0] );
        $this->assertSame( 'security', $h['format'] );
        $this->assertSame( 'abuse/csp-report-flood', $h['source'] );

        // the total over every client
        $r = $this->report( array( 'ReportsPerMinute' => 100, 'ReportsPerMinuteTotal' => 2 ) );
        $statuses = array();
        foreach ( array( '192.0.2.1', '192.0.2.2', '192.0.2.3', '192.0.2.4' ) as $i => $ip )
            $statuses[] = $r->handle( 'POST', 'application/csp-report', self::cspReport(), self::context( $ip ), $now + 120 + $i )['status'];
        $this->assertSame( array( 204, 204, 429, 429 ), $statuses );
        $this->assertSame( 'total', $r->handle( 'POST', 'application/csp-report', self::cspReport(), self::context( '192.0.2.5' ), $now + 125 )['reason'] );

        // a GET or another content type is not counted
        $r = $this->report( array( 'ReportsPerMinute' => 1 ) );
        $b = self::context( '192.0.2.200' );
        for ( $i = 0; $i < 5; $i++ )
            $r->handle( 'GET', '', '', $b, $now + 180 );
        $this->assertSame( 204, $r->handle( 'POST', 'application/csp-report', self::cspReport(), $b, $now + 181 )['status'] );
    }

    /** CSPR-05 */
    public function testSettings()
    {
        $s = expContentSecurityPolicyReport::cleanSettings( array() );
        $this->assertSame( array( 60, 600, 65536, 20, 'csp.log', true ),
                           array( $s['ReportsPerMinute'], $s['ReportsPerMinuteTotal'], $s['MaxReportBytes'], $s['MaxViolationsPerReport'],
                                  $s['LogFile'], $s['maskIP'] ) );
        $s = expContentSecurityPolicyReport::cleanSettings( array( 'ReportsPerMinute' => '0', 'MaxReportBytes' => '99999999',
                                                                   'MaxViolationsPerReport' => 'x', 'LogFile' => '../../etc/passwd' ) );
        $this->assertSame( 1, $s['ReportsPerMinute'] );
        $this->assertSame( 1048576, $s['MaxReportBytes'] );
        $this->assertSame( 20, $s['MaxViolationsPerReport'] );
        $this->assertSame( 'csp.log', $s['LogFile'], 'never a path, never another file than a .log' );
        $this->assertSame( 'reports.log', expContentSecurityPolicyReport::cleanSettings( array( 'LogFile' => 'var/log/reports.log' ) )['LogFile'] );
        $this->assertSame( $this->root . '/log/csp.log', $this->report()->logFile() );

        foreach ( array( 'application/csp-report', 'application/reports+json; charset=utf-8', ' APPLICATION/JSON ' ) as $type )
            $this->assertTrue( expContentSecurityPolicyReport::isReportContentType( $type ), $type );
        foreach ( array( '', 'text/plain', 'application/x-www-form-urlencoded', 'multipart/form-data; boundary=x', null, array() ) as $type )
            $this->assertFalse( expContentSecurityPolicyReport::isReportContentType( $type ) );
    }

    /** CSPR-06 */
    public function testTheKernelRegistersTheView()
    {
        $root = dirname( __DIR__, 5 );
        $this->assertMatchesRegularExpression( '/^ModuleList\[\]=csp$/m', file_get_contents( $root . '/settings/module.ini' ) );
        $Module = $ViewList = $FunctionList = null;
        include $root . '/kernel/csp/module.php';
        $this->assertSame( array( 'report' ), array_keys( $ViewList ) );
        $this->assertArrayNotHasKey( 'functions', $ViewList['report'], 'no policy: browsers send reports without signing in' );
        $site = file_get_contents( $root . '/settings/site.ini' );
        $this->assertMatchesRegularExpression( '/^AnonymousAccessList\[\]=csp\/report$/m', $site );
        $this->assertMatchesRegularExpression( '/^PolicyOmitList\[\]=csp\/report$/m', $site );
        $this->assertSame( 1, preg_match( '/^\[ContentSecurityPolicy\]\n(.*?)\n\[/ms', $site, $m ) );
        foreach ( array( 'ReportsPerMinute=60', 'ReportsPerMinuteTotal=600', 'MaxReportBytes=65536', 'MaxViolationsPerReport=20', 'LogFile=csp.log' ) as $line )
            $this->assertMatchesRegularExpression( '/^' . preg_quote( $line, '/' ) . '$/m', $m[1] );
        $autoload = include $root . '/autoload/ezp_kernel.php';
        $this->assertSame( 'kernel/private/classes/expcontentsecuritypolicyreport.php', $autoload['expContentSecurityPolicyReport'] );
        $this->assertSame( 'kernel/private/classes/views/csp/report.php', $autoload['Exponential\\View\\Kernel\\Csp\\Report'] );
    }

    /** CSPR-07 */
    public function testTheFormTokenCheckLeavesOnlyReportsToTheViewAlone()
    {
        require_once dirname( __DIR__, 5 ) . '/extension/ezformtoken/event/ezxformtoken.php';
        $report = new eZURI( 'csp/report' );
        foreach ( array( 'application/csp-report', 'application/reports+json', 'application/json; charset=utf-8' ) as $type )
            $this->assertTrue( ezxFormToken::isContentSecurityPolicyReport( $report, $type ), $type );
        foreach ( array( 'application/x-www-form-urlencoded', 'multipart/form-data; boundary=x', 'text/plain', '' ) as $type )
            $this->assertFalse( ezxFormToken::isContentSecurityPolicyReport( $report, $type ), 'a form can send ' . $type );
        foreach ( array( 'content/edit/1', 'csp', 'csp/other', 'user/csp/report', '' ) as $path )
            $this->assertFalse( ezxFormToken::isContentSecurityPolicyReport( new eZURI( $path ), 'application/csp-report' ), $path );
        $this->assertFalse( ezxFormToken::isContentSecurityPolicyReport( 'csp/report', 'application/csp-report' ) );
        // the siteaccess taken off: the relative elements count
        $uri = new eZURI( 'admin/csp/report' );
        $uri->increase( 1 );
        $this->assertTrue( ezxFormToken::isContentSecurityPolicyReport( $uri, 'application/csp-report' ) );
    }

    /** CSPR-08 */
    public function testTheViewSendsNoBody()
    {
        $source = file_get_contents( dirname( __DIR__, 5 ) . '/kernel/private/classes/views/csp/report.php' );
        $this->assertStringNotContainsString( 'echo', $source );
        $this->assertStringNotContainsString( 'print', $source );
        $this->assertStringContainsString( 'eZExecution::cleanExit()', $source );
        $this->assertStringContainsString( "header( 'Cache-Control: no-store' )", $source );
        $this->assertDoesNotMatchRegularExpression( '/catch\s*\(\s*\\\\?(Exception|Throwable)/', $source, 'cleanExit throws under Velocity' );
        $this->assertTrue( class_exists( 'Exponential\\View\\Kernel\\Csp\\Report' ) );
    }
}
