<?php
/**
 * "Security activity" (user/security, expUserSecurityActivity): the security events of one account read from the user
 * audit log. No installation: the mapping, the filters, the counts and the descriptions are plain functions; the
 * reading runs on a SQLite database in memory with expuser_audit from the kernel schema.
 *
 *  SA-01 - Every event the page shows is in the audit's taxonomy, with an icon the administration has and a level
 *  SA-02 - The filters: sign-ins and sessions, account changes, needs attention (failures, refusals, new devices ...)
 *  SA-03 - A row is described for the page: who acted (the user, an administrator, nobody signed in), the address as a
 *          network, the details kept (never a secret), the result
 *  SA-04 - The counts of the last days; on a database: one account's events only, newest first, paged and filtered
 *  SA-05 - The module declares user/security; the templates exist in admin, admin4 and standard and admin = admin4;
 *          the sessions page and the account overview link to it
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/../datatypes/eZDatatypeTestFixtures.php';

class expUserSecurityActivityTest extends eZDatatypeTestCase
{
    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    private static function read( $file )
    {
        return file_get_contents( self::root() . '/' . $file );
    }

    protected function setUp(): void
    {
        parent::setUp();
        expUserAudit::reset();
    }

    protected function tearDown(): void
    {
        expUserAudit::reset();
        parent::tearDown();
    }

    /** SA-01 */
    public function testEveryKindIsAKnownEventWithAnIcon()
    {
        $catalogue = expAuditTaxonomy::catalogue();
        $css = self::read( 'design/admin4/stylesheets/components.css' );
        foreach ( expUserSecurityActivity::KINDS as $name => $kind )
        {
            $this->assertArrayHasKey( $name, $catalogue, $name );
            $this->assertStringContainsString( '.a4-bi-' . $kind[1] . ' {', $css, "icon of $name" );
            $this->assertContains( $kind[2], array( 'ok', 'info', 'warn', 'bad' ), $name );
            $this->assertContains( $kind[3], array( 'signin', 'account' ), $name );
            $this->assertTrue( expUserAudit::wantsName( $name, expUserAudit::$defaultEvents ), "$name reaches the user audit log" );
        }
    }

    /** SA-02 */
    public function testFilters()
    {
        $all = expUserSecurityActivity::namesFor( 'all' );
        $this->assertCount( count( expUserSecurityActivity::KINDS ), $all );
        $signin = expUserSecurityActivity::namesFor( 'signin' );
        $account = expUserSecurityActivity::namesFor( 'account' );
        $this->assertContains( 'access.session.login', $signin );
        $this->assertNotContains( 'access.user.password.change', $signin );
        $this->assertContains( 'access.user.two_factor.remove', $account );
        $this->assertEqualsCanonicalizing( $all, array_merge( $signin, $account ) );
        $problems = expUserSecurityActivity::namesFor( 'problems' );
        $this->assertContains( 'access.session.login.failed', $problems );
        $this->assertContains( 'access.session.new_location', $problems );
        $this->assertNotContains( 'access.session.login', $problems );
        $where = expUserSecurityActivity::whereClause( 14, 'nonsense', 'addslashes', 1000 );
        $this->assertStringContainsString( "target_type = 'user' AND target_id = 14", $where );
        $this->assertStringContainsString( 'created >= 1000', $where );
        $this->assertStringContainsString( "'access.session.login'", $where, 'an unknown filter is all' );
    }

    /** SA-03 */
    public function testDescribe()
    {
        $row = array( 'id' => 7, 'created' => 1800000000, 'event_name' => 'access.session.login', 'result' => 'success', 'actor_id' => 14,
                      'actor_login' => 'anna', 'client_ip' => '203.0.113.57', 'siteaccess' => 'site', 'host' => 'www.example.com',
                      'value_after' => json_encode( array( 'handler' => 'standard', 'session' => 3, 'method' => 'password', 'password_hash' => 'x' ) ) );
        $d = expUserSecurityActivity::describe( $row, 14 );
        $this->assertSame( array( 'signin', 'ok', 'self', '203.0.113.x', 'www.example.com' ), array( $d['kind'], $d['level'], $d['by'], $d['ip'], $d['host'] ) );
        $this->assertSame( array( 'method' => 'password' ), $d['details'], 'only the details the page names; never a hash' );

        $admin = expUserSecurityActivity::describe( array( 'event_name' => 'access.session.revoke', 'actor_id' => 1, 'actor_login' => 'admin',
                                                           'value_after' => '{"count":2,"scope":"all"}' ), 14 );
        $this->assertSame( array( 'admin', array( 'count' => '2', 'scope' => 'all' ) ), array( $admin['by'], $admin['details'] ) );
        $failed = expUserSecurityActivity::describe( array( 'event_name' => 'access.session.login.failed', 'actor_id' => 10, 'result' => 'failed' ), 14 );
        $this->assertSame( array( 'signin_failed', 'system', 'failed' ), array( $failed['kind'], $failed['by'], $failed['result'] ),
                           'a failed sign-in is made by nobody signed in, not by the anonymous user' );
        $unknown = expUserSecurityActivity::describe( array( 'event_name' => 'access.user.something' ), 14 );
        $this->assertSame( 'other', $unknown['kind'] );
    }

    /** SA-04 */
    public function testSummarize()
    {
        $s = expUserSecurityActivity::summarize( array( array( 'event_name' => 'access.session.login', 'n' => 5 ),
                                                       array( 'event_name' => 'access.passkey.use', 'n' => 1 ),
                                                       array( 'event_name' => 'access.session.login.failed', 'n' => 3 ),
                                                       array( 'event_name' => 'access.user.password.change', 'n' => 1 ),
                                                       array( 'event_name' => 'access.session.logout', 'n' => 4 ),
                                                       array( 'event_name' => 'content.object.publish', 'n' => 9 ) ), 30 );
        $this->assertSame( array( 'signins' => 6, 'failures' => 3, 'changes' => 1, 'days' => 30 ), $s );
    }

    /** SA-04 */
    public function testOnADatabase()
    {
        if ( !extension_loaded( 'sqlite3' ) )
            $this->markTestSkipped( 'SQLite3 extension is not loaded' );
        $db = new eZSQLite3DB( array(
            'server' => '', 'port' => '', 'user' => '', 'password' => '', 'database' => ':memory:',
            'use_slave_server' => false, 'slave_server' => '', 'slave_port' => '', 'slave_user' => '', 'slave_password' => '',
            'slave_database' => '', 'socket' => false, 'charset' => 'utf-8', 'is_internal_charset' => true,
            'builtin_encoding' => false, 'connect_retries' => 0, 'use_persistent_connection' => 'disabled' ) );
        preg_match( '/CREATE TABLE `expuser_audit` \(.*?\n\);/s', self::read( 'kernel/sql/sqlite/schema.sql' ), $m );
        $db->query( $m[0] );
        eZDB::setInstance( $db );
        expUserAudit::reset();
        $now = time();
        $i = 0;
        foreach ( array( array( 14, 'access.session.login', 300 ), array( 14, 'access.session.login.failed', 200 ), array( 14, 'access.user.password.change', 100 ),
                         array( 15, 'access.session.login', 50 ), array( 14, 'content.object.publish', 10 ), array( 14, 'access.session.login', 40 * 86400 ) ) as $e )
        {
            $i++;
            $db->query( "INSERT INTO expuser_audit ( event_id, created, time_ms, event_name, result, target_type, target_id, actor_id )
                         VALUES ( 'e$i', " . ( $now - $e[2] ) . ', ' . ( ( $now - $e[2] ) * 1000 ) . ", '{$e[1]}', 'success', 'user', {$e[0]}, {$e[0]} )" );
        }
        $this->assertTrue( expUserSecurityActivity::available() );
        $this->assertSame( 4, expUserSecurityActivity::count( 14 ) );
        $this->assertSame( 1, expUserSecurityActivity::count( 14, 'account' ) );
        $events = expUserSecurityActivity::fetch( 14, 'all', 0, 2 );
        $this->assertSame( array( 'password_change', 'signin_failed' ), array_column( $events, 'kind' ), 'newest first, paged' );
        $this->assertSame( array( 'signins' => 1, 'failures' => 1, 'changes' => 1, 'days' => 30 ), expUserSecurityActivity::summary( 14, 30, $now ) );
        $this->assertCount( 5, expUserSecurityActivity::exportForUser( 14 ), 'the export has every row of the account' );
        $this->assertSame( 0, expUserSecurityActivity::count( 0 ) );
    }

    /** SA-05 */
    public function testWiring()
    {
        $Module = $ViewList = $FunctionList = null;
        include self::root() . '/kernel/user/module.php';
        $this->assertSame( array( 'login' ), $ViewList['security']['functions'] );
        $this->assertSame( array( 'UserID' ), $ViewList['security']['params'] );
        $this->assertFileExists( self::root() . '/kernel/user/security.php' );
        foreach ( array( 'design/admin4/templates/user/security.tpl', 'design/admin/templates/user/security.tpl', 'design/standard/templates/user/security.tpl' ) as $tpl )
            $this->assertFileExists( self::root() . '/' . $tpl );
        $this->assertSame( self::read( 'design/admin4/templates/user/security.tpl' ), self::read( 'design/admin/templates/user/security.tpl' ) );
        $this->assertStringContainsString( "'user/security'", self::read( 'design/admin4/templates/user/sessions.tpl' ) );
        $this->assertStringContainsString( "'user/security'", self::read( 'design/standard/templates/user/sessions.tpl' ) );
    }
}
