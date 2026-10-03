<?php
/**
 * The filter, write-failure and re-authentication fixes (doc/bc/6.0/audit.md, formerly Appendix C):
 *
 *  AG-01 — name patterns: *, a prefix ending in .*, a whole name are valid; access.session.login*, access.*.failed,
 *          a bare prefix, an unknown domain and malformed ranks are refused with a reason; the forms match as documented
 *  AG-02 — a malformed (name), (from) or (to) is listed in normalise()'s 'invalid' and finds nothing (SQL 1=0, the PHP
 *          row filter, the file search) instead of everything
 *  AG-03 — the time rule: an explicit Z or +HH:MM offset is that instant, a bare time is read in the zone given (UTC
 *          for exp:audit, the site's zone for the console); "to" includes the day, minute or second given
 *  AG-04 — the file search of exp:audit (zone UTC) finds a record at its UTC time and through an offset
 *  AG-05 — exp:audit: --query is accepted, --q is not an option (-q is quiet); a malformed --name/--from/--to exits 2
 *          with the reason, for search and tail
 *  AG-06 — OnWriteFailure=refuse: allows() refuses names written at once while their channel cannot be written, never
 *          buffered names, never with continue; writeFailed() follows the request's writes
 *  AG-07 — OnWriteFailure=refuse stops an INI write through expIniEditor before anything is written
 *  AG-08 — OnWriteFailure=refuse refuses a signed-in POST to a sensitive module (503 page), never a GET, an anonymous
 *          visitor, user/login, a module outside AlwaysModules[] or a writable audit
 *  AG-09 — ReauthForManage: the form, a wrong password (recorded failed, never the password), the right one (recorded,
 *          remembered for ReauthMinutes for that user only), Cancel, and nothing at all when disabled
 *
 * Live-database style: the kernel is started once on the admin siteaccess (the installation's own database, no test
 * database); records go to throwaway directories under var/tmp/audit-tests/ (expAuditTestFixtures), never to the
 * live audit log. The unwritable log directory is a path below a plain file. The re-authentication user exists only
 * in memory (no row is written).
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/audit/expAuditFilterGuardReauthTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group audit
 */

require_once __DIR__ . '/fixtures/expaudittestfixtures.php';
require_once __DIR__ . '/../ini/fixtures/expinienginetestfixtures.php';

class expAuditFilterGuardReauthTest extends PHPUnit\Framework\TestCase
{
    /** @var string|null */
    protected static $bootError = null;

    /** @var bool */
    protected static $booted = false;

    /** @var string */
    protected $dir;

    /** @var array $_SERVER and $_POST keys this test changes */
    protected $saved = array();

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if ( self::$booted || self::$bootError !== null )
            return;
        try
        {
            $root = dirname( __DIR__, 5 );
            chdir( $root );
            if ( !class_exists( 'eZScript' ) )
                require_once $root . '/autoload.php';
            if ( !eZDB::hasInstance() || !eZDB::instance()->isConnected() )
            {
                $script = eZScript::instance( array( 'use-session' => false, 'use-modules' => true, 'use-extensions' => true ) );
                $script->startup();
                $script->setUseSiteAccess( 'admin' );
                $script->initialize();
            }
            eZExecution::setCleanExit();
            if ( !eZDB::instance()->isConnected() )
                throw new RuntimeException( 'no database connection' );
            self::$booted = true;
        }
        catch ( Throwable $e )
        {
            self::$bootError = $e->getMessage();
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        if ( self::$bootError !== null )
            $this->markTestSkipped( 'Kernel not available: ' . self::$bootError );
        $this->saved = array( 'method' => isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : null, 'post' => $_POST );
        $this->dir = expAuditTestFixtures::setUp( $this->name() );
    }

    protected function tearDown(): void
    {
        expAudit::flush( true );
        expAuditTestFixtures::tearDown();
        expAuditIndexSettings::setOverride( null );
        expAuditReauth::setNow( null );
        expAuditReauth::forget();
        expIniEditor::setRoot( null );
        if ( $this->saved['method'] === null )
            unset( $_SERVER['REQUEST_METHOD'] );
        else
            $_SERVER['REQUEST_METHOD'] = $this->saved['method'];
        $_POST = $this->saved['post'];
        $anonymous = eZUser::fetch( eZUser::anonymousId() );
        if ( $anonymous )
            eZUser::setCurrentlyLoggedInUser( $anonymous, $anonymous->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
        parent::tearDown();
    }

    /** A test configuration whose log directory is below a plain file, so no channel can be written */
    protected function unwritable( $onWriteFailure = 'refuse' )
    {
        $blocker = $this->dir . 'blocker';
        file_put_contents( $blocker, 'a file where the log directory should be' );
        expAuditTestFixtures::configure( $this->dir, array( 'logDir' => $blocker . '/log', 'AuditSettings/OnWriteFailure' => $onWriteFailure ) );
        expAudit::resetAll();
        return $blocker . '/log';
    }

    /** AG-01 */
    public function testNamePatterns()
    {
        foreach ( array( '*', 'access.*', 'access.session.*', 'content.node.remove.*', 'access.session.login', 'access.session.login.failed',
                         'commerce.order.item.remove', 'system.audit.*' ) as $ok )
            $this->assertNull( expAuditTaxonomy::patternProblem( $ok ), $ok );
        $bad = array( 'access.session.login*' => "did you mean 'access.session.login.*'", 'access.*.failed' => 'last rank after a dot',
                      'access.session' => "write 'access.session.*'", 'access' => "write 'access.*'", 'foo.*' => 'not a domain',
                      'Access.*' => 'not a rank', 'access..login.*' => 'not a rank', '' => 'empty', '**' => 'a * may only',
                      'access.session.login.failed.a.b.c' => 'at most 6 ranks', 'access.a.b.c.d.e.*' => 'at most 5 ranks',
                      'access.session.log-in' => 'not a rank' );
        foreach ( $bad as $pattern => $why )
        {
            $problem = expAuditTaxonomy::patternProblem( $pattern );
            $this->assertNotNull( $problem, "'$pattern' is refused" );
            $this->assertStringContainsString( $why, $problem, $pattern );
            $this->assertFalse( expAuditTaxonomy::isValidPattern( $pattern ) );
        }
        $this->assertNotNull( expAuditTaxonomy::patternProblem( array( 'access.*' ) ) );
        // the documented forms match as documented
        $this->assertGreaterThanOrEqual( 0, expAuditTaxonomy::match( 'access.session.*', 'access.session.login' ) );
        $this->assertGreaterThanOrEqual( 0, expAuditTaxonomy::match( 'access.session.*', 'access.session.login.failed' ) );
        $this->assertGreaterThanOrEqual( 0, expAuditTaxonomy::match( 'access.session.login.*', 'access.session.login' ), 'a prefix matches itself' );
        $this->assertGreaterThanOrEqual( 0, expAuditTaxonomy::match( 'access.session.login.*', 'access.session.login.failed' ) );
        $this->assertSame( -1, expAuditTaxonomy::match( 'access.session.*', 'access.sessions.login' ) );
        $this->assertSame( -1, expAuditTaxonomy::match( 'access.session.login', 'access.session.login.failed' ) );
        $this->assertGreaterThanOrEqual( 0, expAuditTaxonomy::match( '*', 'data.export.csv' ) );
    }

    /** AG-02 */
    public function testMalformedFiltersFindNothing()
    {
        $f = expAuditQuery::normalise( array( 'name' => 'access.session.login*', 'channel' => 'access' ) );
        $this->assertArrayNotHasKey( 'name', $f );
        $this->assertArrayHasKey( 'name', $f['invalid'] );
        $this->assertStringContainsString( 'access.session.login.*', $f['invalid']['name'] );
        $q = new expAuditQuery( null, false );
        $this->assertStringContainsString( '1=0', $q->where( $f ) );
        $row = array( 'channel' => 'access', 'name' => 'access.session.login', 'user_id' => 14, 'login' => 'admin', 'result' => 'success',
                      'request_id' => 'r', 'job_id' => '', 'run_id' => '', 'parent_id' => '', 'domain_name' => 'access', 'object_type' => 'user',
                      'object_id' => '14', 'target_type' => '', 'target_id' => '', 'severity' => 6, 'ip' => '', 'time_ms' => 1, 'search_text' => '' );
        $this->assertFalse( expAuditIndexRow::matches( $row, $f ) );
        $this->assertTrue( expAuditIndexRow::matches( $row, expAuditQuery::normalise( array( 'name' => 'access.session.*' ) ) ) );
        foreach ( array( 'from' => '2026-02-30', 'to' => 'yesterday' ) as $k => $v )
        {
            $f = expAuditQuery::normalise( array( $k => $v ) );
            $this->assertArrayHasKey( $k, $f['invalid'], "$k=$v" );
            $this->assertArrayNotHasKey( $k . '_ms', $f );
            $this->assertStringContainsString( '1=0', $q->where( $f ) );
        }
        $this->assertArrayNotHasKey( 'invalid', expAuditQuery::normalise( array( 'name' => 'access.*', 'from' => '2026-10-01', 'to' => '' ) ) );

        // the file search: a valid pattern finds the record, a malformed one finds nothing (not everything)
        expAudit::event( 'access.session.login', array( 'object' => array( 'type' => 'user', 'id' => 14 ) ) );
        expAudit::flush();
        $e = new expAuditExporter( expAuditConfig::get() );
        $this->assertCount( 1, $e->search( expAuditExporter::filters( array( 'name' => 'access.session.*' ) ), 0 ) );
        $this->assertCount( 0, $e->search( expAuditExporter::filters( array( 'name' => 'access.session.login*' ) ), 0 ) );
        $this->assertCount( 0, $e->search( expAuditExporter::filters( array( 'from' => 'not-a-date' ) ), 0 ) );
        $this->assertSame( 'login', expAuditExporter::filters( array( 'query' => 'login' ) )['q'], '--query is the text filter' );
    }

    /** AG-03 */
    public function testTimeRule()
    {
        $at = gmmktime( 22, 15, 0, 10, 2, 2026 ) * 1000;
        $this->assertSame( $at, expAuditQuery::parseTime( '2026-10-02T22:15', false, 'UTC' )['ms'] );
        $this->assertSame( $at, expAuditQuery::parseTime( '2026-10-02 22:15', false, 'UTC' )['ms'] );
        $this->assertSame( $at, expAuditQuery::parseTime( '2026-10-02T22:15Z', false, 'Europe/Berlin' )['ms'], 'Z wins over the zone' );
        $this->assertSame( $at, expAuditQuery::parseTime( '2026-10-03T00:15+02:00', false, 'UTC' )['ms'] );
        $this->assertSame( $at, expAuditQuery::parseTime( '2026-10-03T00:15+0200', false, 'UTC' )['ms'] );
        $this->assertSame( $at, expAuditQuery::parseTime( '2026-10-02T17:15-05', false, 'UTC' )['ms'] );
        $this->assertSame( $at, expAuditQuery::parseTime( '2026-10-03T00:15', false, 'Europe/Berlin' )['ms'], 'bare: the zone given (CEST +2)' );
        $this->assertSame( $at, expAuditQuery::parseTime( '2026-10-03T00:15', false, new DateTimeZone( 'Europe/Berlin' ) )['ms'] );
        $this->assertSame( '2026-10-02T22:15:00Z', expAuditQuery::parseTime( '2026-10-02T22:15', false, 'UTC' )['utc'] );
        // "to" includes the day, the minute or the second given
        $this->assertSame( gmmktime( 0, 0, 0, 10, 3, 2026 ) * 1000, expAuditQuery::parseTime( '2026-10-02', true, 'UTC' )['ms'] );
        $this->assertSame( $at + 60000, expAuditQuery::parseTime( '2026-10-02T22:15', true, 'UTC' )['ms'] );
        $this->assertSame( $at + 31000, expAuditQuery::parseTime( '2026-10-02T22:15:30', true, 'UTC' )['ms'] );
        foreach ( array( '2026-02-30', '2026-10-02T24:00', '2026-10-02T22:60', '2026-10-02T1:00', '2026-10-02T22:15+15:00',
                         '02.10.2026', 'today', '2026-10-02T22:15:00.123', '' ) as $bad )
            $this->assertNull( expAuditQuery::parseTime( $bad, false, 'UTC' ), $bad );
        // the console: bare times in the site's zone, as it prints them
        $site = new DateTime( '2026-10-02 22:15:00', new DateTimeZone( date_default_timezone_get() ) );
        $this->assertSame( $site->getTimestamp() * 1000, expAuditQuery::normalise( array( 'from' => '2026-10-02T22:15' ) )['from_ms'] );
        $this->assertSame( $at, expAuditQuery::normalise( array( 'from' => '2026-10-02T22:15' ), null, 'UTC' )['from_ms'] );
    }

    /** AG-04 */
    public function testCommandFileSearchIsUtc()
    {
        expAudit::setNow( gmmktime( 23, 30, 0, 10, 2, 2026 ) + 0.25 );
        expAudit::event( 'access.session.login', array( 'object' => array( 'type' => 'user', 'id' => 14 ) ) );
        expAudit::flush();
        $e = new expAuditExporter( expAuditConfig::get() );
        $count = function ( array $in ) use ( $e ) {
            return count( $e->search( expAuditExporter::filters( $in + array( 'zone' => 'UTC', 'name' => 'access.session.login' ) ), 0 ) );
        };
        $this->assertSame( 1, $count( array( 'from' => '2026-10-02T23:30', 'to' => '2026-10-02T23:30' ) ), 'the minute printed' );
        $this->assertSame( 1, $count( array( 'from' => '2026-10-02', 'to' => '2026-10-02' ) ), 'the UTC day printed' );
        $this->assertSame( 0, $count( array( 'from' => '2026-10-03' ) ), 'the next UTC day' );
        $this->assertSame( 1, $count( array( 'from' => '2026-10-03T01:30+02:00', 'to' => '2026-10-03T01:30+02:00' ) ), 'through an offset' );
        $this->assertSame( 0, $count( array( 'to' => '2026-10-02T23:29' ) ) );
    }

    /** AG-05 */
    public function testCommandOptions()
    {
        $run = function ( $args ) {
            $root = expAuditTestFixtures::realRoot();
            $cmd = escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( $root . 'bin/php/audit.php' ) . ' ' . $args . ' --allow-root-user 2>&1';
            $out = array();
            exec( 'cd ' . escapeshellarg( $root ) . ' && ' . $cmd, $out, $code );
            return array( $code, implode( "\n", $out ) );
        };
        list( $code, $out ) = $run( 'search --query=login --help' );
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( '--query', $out );
        $this->assertStringNotContainsString( '--q=', $out );
        list( $code, $out ) = $run( 'search --q=login --help' );
        $this->assertNotSame( 0, $code );
        $this->assertStringContainsString( "invalid option `--q'", $out );
        foreach ( array( "search --name='access.session.login*'" => "did you mean 'access.session.login.*'",
                         "search --name=access.session" => "write 'access.session.*'",
                         "tail --name='access.*.failed'" => 'last rank after a dot',
                         'search --from=2026-13-01' => "--from: '2026-13-01' is not a time",
                         'search --to=2026-10-02T25:00' => "--to: '2026-10-02T25:00' is not a time" ) as $args => $why )
        {
            list( $code, $out ) = $run( $args );
            $this->assertSame( 2, $code, "$args: $out" );
            $this->assertStringContainsString( $why, $out, $args );
            $this->assertStringNotContainsString( 'record(s)', $out, 'nothing was searched' );
        }
    }

    /** AG-06 */
    public function testRefuseGuard()
    {
        $this->assertFalse( expAuditGuard::refusing(), 'the shipped default is continue' );
        $this->unwritable( 'refuse' );
        $this->assertTrue( expAuditGuard::refusing() );
        $this->assertFalse( expAuditGuard::allows( 'system.setting.write' ) );
        $last = expAuditGuard::lastRefusal();
        $this->assertSame( 'system', $last['channel'] );
        $this->assertStringContainsString( 'cannot be created', $last['problem'] );
        $this->assertStringContainsString( 'OnWriteFailure=refuse', expAuditGuard::message() );
        $this->assertFalse( expAuditGuard::allows( 'access.role.assign' ) );
        $this->assertFalse( expAuditGuard::allows( 'system.audit.archive' ) );
        $this->assertTrue( expAuditGuard::allows( 'content.node.move' ), 'buffered names are not guarded' );
        $this->assertTrue( expAuditGuard::allows( 'not a name' ) );
        $this->assertNull( expAuditGuard::lastRefusal() );

        // the request's own failed write of a channel refuses even when the probe would pass
        $this->assertFalse( expAudit::writeFailed( 'access' ) );
        expAudit::event( 'access.session.login', array( 'object' => array( 'type' => 'user', 'id' => 14 ) ) );
        $this->assertTrue( expAudit::writeFailed( 'access' ) );
        expAuditTestFixtures::configure( $this->dir, array( 'AuditSettings/OnWriteFailure' => 'refuse' ) );
        $this->assertFalse( expAuditGuard::allows( 'access.role.assign' ), 'the last write of access failed' );
        $this->assertStringContainsString( 'in this request failed', expAuditGuard::lastRefusal()['problem'] );
        $this->assertTrue( expAuditGuard::allows( 'system.setting.write' ), 'a writable channel' );
        expAudit::event( 'access.session.logout' );
        $this->assertFalse( expAudit::writeFailed( 'access' ), 'a successful write clears it' );
        $this->assertTrue( expAuditGuard::allows( 'access.role.assign' ) );

        // continue: never refused
        $this->unwritable( 'continue' );
        $this->assertFalse( expAuditGuard::refusing() );
        $this->assertTrue( expAuditGuard::allows( 'system.setting.write' ) );
        // the audit switched off: nothing to record, nothing refused
        $this->unwritable( 'refuse' );
        expAuditTestFixtures::configure( $this->dir, array( 'logDir' => $this->dir . 'blocker/log', 'AuditSettings/OnWriteFailure' => 'refuse',
                                                            'AuditSettings/Audit' => 'disabled' ) );
        $this->assertTrue( expAuditGuard::allows( 'system.setting.write' ) );
    }

    /** AG-07 */
    public function testRefuseStopsSettingsWrite()
    {
        $root = expIniEngineTestFixtures::makeRoot( 'audit-refuse' );
        $file = $root . 'settings/override/site.ini.append.php';
        $before = file_get_contents( $file );
        $this->unwritable( 'refuse' );
        $editor = new expIniEditor( expIniEditor::scope( 'global' ), 'site.ini' );
        $editor->set( 'DebugSettings', 'DebugOutput', 'enabled' );
        try
        {
            $editor->save( array( 'backup' => false ) );
            $this->fail( 'the write is refused' );
        }
        catch ( expIniException $e )
        {
            $this->assertStringContainsString( 'OnWriteFailure=refuse', $e->getMessage() );
            $this->assertStringContainsString( 'Nothing written', $e->getMessage() );
        }
        $this->assertSame( $before, file_get_contents( $file ), 'nothing written' );

        $this->unwritable( 'continue' );
        $editor = new expIniEditor( expIniEditor::scope( 'global' ), 'site.ini' );
        $editor->set( 'DebugSettings', 'DebugOutput', 'enabled' );
        $editor->save( array( 'backup' => false ) );
        $this->assertStringContainsString( 'DebugOutput=enabled', file_get_contents( $file ), 'continue: written, unrecorded' );
    }

    /** AG-08 */
    public function testRefuseWebPost()
    {
        $admin = eZUser::fetchByName( 'admin' );
        $this->assertInstanceOf( 'eZUser', $admin );
        $this->unwritable( 'refuse' );
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertTrue( expAuditGuard::webAllows( 'role', 'edit' ), 'anonymous visitors are never refused' );
        eZUser::setCurrentlyLoggedInUser( $admin, $admin->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
        $this->assertFalse( expAuditGuard::webAllows( 'role', 'edit' ) );
        $this->assertFalse( expAuditGuard::webAllows( 'setup', 'cache' ) );
        $this->assertFalse( expAuditGuard::webAllows( 'audit', 'dashboard' ) );
        $this->assertTrue( expAuditGuard::webAllows( 'user', 'login' ), 'signing in stays possible' );
        $this->assertTrue( expAuditGuard::webAllows( 'user', 'logout' ) );
        $this->assertTrue( expAuditGuard::webAllows( 'content', 'edit' ), 'content editing is never refused' );
        expAuditGuard::webAllows( 'role', 'edit' );
        $result = expAuditGuard::refusedResult( 'role', 'edit' );
        $this->assertStringContainsString( 'exp-audit-refused', $result['content'] );
        $this->assertStringContainsString( 'access', $result['content'], 'the channel is named' );
        $this->assertStringNotContainsString( $this->dir, $result['content'], 'no path on the page' );
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertTrue( expAuditGuard::webAllows( 'role', 'edit' ), 'GET is never refused' );
        $_SERVER['REQUEST_METHOD'] = 'POST';
        expAuditTestFixtures::configure( $this->dir, array( 'AuditSettings/OnWriteFailure' => 'refuse' ) );
        expAudit::resetAll();
        $this->assertTrue( expAuditGuard::webAllows( 'role', 'edit' ), 'a writable audit' );
        $this->unwritable( 'continue' );
        $this->assertTrue( expAuditGuard::webAllows( 'role', 'edit' ), 'continue' );
    }

    /** AG-09 */
    public function testReauth()
    {
        $password = 'Reauth-test-9f3c!';
        $type = eZUser::hashType();
        $user = new eZUser( array( 'contentobject_id' => 999999017, 'login' => 'reauth-test', 'email' => 'reauth-test@example.invalid',
                                   'password_hash_type' => $type,
                                   'password_hash' => eZUser::createHash( 'reauth-test', $password, eZUser::site(), $type ) ) );
        $other = new eZUser( array( 'contentobject_id' => 999999018, 'login' => 'reauth-other', 'email' => 'reauth-other@example.invalid',
                                    'password_hash_type' => $type, 'password_hash' => 'x' ) );
        $module = eZModule::exists( 'audit' );
        $this->assertInstanceOf( 'eZModule', $module );

        // disabled (the shipped default): never asked
        expAuditIndexSettings::setOverride( array() );
        $this->assertFalse( expAuditReauth::enabled() );
        $this->assertNull( expAuditReauth::gate( $module, 'AuditVerifyNowButton', 'audit/dashboard', 'Verify now', $user ) );

        expAuditIndexSettings::setOverride( array( 'AuditConsoleSettings/ReauthForManage' => 'enabled', 'AuditConsoleSettings/ReauthMinutes' => '10' ) );
        $this->assertTrue( expAuditReauth::required( $user ) );
        $this->assertTrue( expAuditReauth::check( $user, $password ) );
        $this->assertFalse( expAuditReauth::check( $user, 'wrong' ) );
        $this->assertFalse( expAuditReauth::check( $user, array( $password ) ) );
        $this->assertFalse( expAuditReauth::check( $user, '' ) );

        // the form, posting to the action's view with its button
        $_POST = array();
        $form = expAuditReauth::gate( $module, 'AuditVerifyNowButton', 'audit/console/(name)/access.*', 'Verify now', $user );
        $this->assertIsArray( $form );
        $this->assertStringContainsString( 'name="AuditReauthPassword"', $form['content'] );
        $this->assertStringContainsString( 'name="AuditVerifyNowButton"', $form['content'] );
        $this->assertStringContainsString( 'audit/console/(name)/access.*', $form['content'] );
        $this->assertStringNotContainsString( 'message-error', $form['content'] );

        // a wrong password: the form again, recorded as failed, never the password
        $_POST = array( 'AuditVerifyNowButton' => '1', 'AuditReauthButton' => 'x', 'AuditReauthPassword' => 'Wrong-pass-7' );
        $form = expAuditReauth::gate( $module, 'AuditVerifyNowButton', 'audit/dashboard', 'Verify now', $user );
        $this->assertIsArray( $form );
        $this->assertStringContainsString( 'message-error', $form['content'] );
        $this->assertFalse( expAuditReauth::isFresh( $user ) );

        // the right one: the action runs, recorded, remembered
        $_POST['AuditReauthPassword'] = $password;
        $this->assertNull( expAuditReauth::gate( $module, 'AuditVerifyNowButton', 'audit/dashboard', 'Verify now', $user ) );
        $this->assertTrue( expAuditReauth::isFresh( $user ) );
        $this->assertFalse( expAuditReauth::required( $user ) );
        $this->assertFalse( expAuditReauth::isFresh( $other ), 'for that user only' );
        $_POST = array( 'AuditVerifyNowButton' => '1' );
        $this->assertNull( expAuditReauth::gate( $module, 'AuditVerifyNowButton', 'audit/dashboard', 'Verify now', $user ), 'not asked again' );
        expAuditReauth::setNow( time() + 9 * 60 );
        $this->assertFalse( expAuditReauth::required( $user ) );
        expAuditReauth::setNow( time() + 10 * 60 + 1 );
        $this->assertTrue( expAuditReauth::required( $user ), 'ReauthMinutes later it is asked again' );
        expAuditReauth::setNow( null );

        $events = expAuditTestFixtures::events( $this->dir, 'access' );
        $names = array_column( $events, 'name' );
        $this->assertSame( array( 'access.session.reauth.failed', 'access.session.reauth' ), $names );
        $this->assertSame( 'failed', $events[0]['result'] );
        $this->assertSame( 'credentials', $events[0]['reason'] );
        $this->assertSame( 999999017, $events[0]['object']['id'] );
        $this->assertSame( 'audit/dashboard: Verify now', $events[1]['after']['action'] );
        $this->assertSame( 10, $events[1]['after']['minutes'] );
        $raw = '';
        foreach ( glob( $this->dir . 'log/access-*.jsonl' ) as $f )
            $raw .= file_get_contents( $f );
        $this->assertStringNotContainsString( 'Wrong-pass-7', $raw );
        $this->assertStringNotContainsString( $password, $raw );

        // Cancel goes back without the action
        expAuditReauth::forget();
        $_POST = array( 'AuditVerifyNowButton' => '1', 'AuditReauthCancelButton' => 'x' );
        expAuditReauth::gate( $module, 'AuditVerifyNowButton', 'audit/dashboard', 'Verify now', $user );
        $this->assertSame( eZModule::STATUS_REDIRECT, $module->exitStatus() );
        $this->assertCount( 2, expAuditTestFixtures::events( $this->dir, 'access' ), 'Cancel records nothing' );
    }
}
