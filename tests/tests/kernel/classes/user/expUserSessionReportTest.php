<?php
/**
 * The sign-in report (user/sessionreport, expUserSessionReport, policy user/sessions_manage). No installation: the
 * counting per day and the bar widths are plain functions; the running sessions are read from a SQLite database in
 * memory with expuser_session from the kernel schema.
 *
 *  SR-01 - Counts per day: every day of the window, the newest first, nothing outside it
 *  SR-02 - The width of a bar is a whole percentage of the largest number, at least 1 for anything above 0
 *  SR-03 - On a database: the running sessions of every user, timed-out ones left out (Remember me kept), the users
 *          counted once, the most recently used first
 *  SR-04 - The module declares user/sessionreport with the new policy function sessions_manage; the menu, the
 *          templates (admin = admin4), "Verify it's you" before signing out, and the link from setup/session
 *  SR-05 - Suspicious patterns: an account with repeated failures, one network failing on several accounts, a locked
 *          account, an account signed in from several countries; worst first, nothing below the thresholds
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/../datatypes/eZDatatypeTestFixtures.php';

class expUserSessionReportTest extends eZDatatypeTestCase
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
        expUserSessionTracker::reset();
        expKernelTableSchema::reset();
    }

    protected function tearDown(): void
    {
        expUserSessionTracker::reset();
        expKernelTableSchema::reset();
        parent::tearDown();
    }

    /** SR-01 */
    public function testByDay()
    {
        $now = mktime( 15, 0, 0, 10, 28, 2026 );
        $days = expUserSessionReport::byDay( array( $now, $now - 3600, $now - 86400, $now - 20 * 86400 ), 3, $now );
        $this->assertSame( array( '2026-10-28' => 2, '2026-10-27' => 1, '2026-10-26' => 0 ), $days );
    }

    /** SR-02 */
    public function testPercentStep()
    {
        $this->assertSame( 0, expUserSessionReport::percentStep( 0, 10 ) );
        $this->assertSame( 0, expUserSessionReport::percentStep( 3, 0 ) );
        $this->assertSame( 100, expUserSessionReport::percentStep( 10, 10 ) );
        $this->assertSame( 50, expUserSessionReport::percentStep( 5, 10 ) );
        $this->assertSame( 1, expUserSessionReport::percentStep( 1, 1000 ), 'a day with sign-ins never looks empty' );
    }

    /** SR-03 */
    public function testActiveSessions()
    {
        if ( !extension_loaded( 'sqlite3' ) )
            $this->markTestSkipped( 'SQLite3 extension is not loaded' );
        $db = new eZSQLite3DB( array(
            'server' => '', 'port' => '', 'user' => '', 'password' => '', 'database' => ':memory:',
            'use_slave_server' => false, 'slave_server' => '', 'slave_port' => '', 'slave_user' => '', 'slave_password' => '',
            'slave_database' => '', 'socket' => false, 'charset' => 'utf-8', 'is_internal_charset' => true,
            'builtin_encoding' => false, 'connect_retries' => 0, 'use_persistent_connection' => 'disabled' ) );
        preg_match( '/CREATE TABLE `expuser_session` \(.*?\n\);/s', self::read( 'kernel/sql/sqlite/schema.sql' ), $m );
        $db->query( $m[0] );
        eZDB::setInstance( $db );
        expKernelTableSchema::reset();
        expUserSessionTracker::reset();
        $now = time();
        $timeout = expUserSessionTracker::sessionTimeout();
        $rows = array( array( 14, $now - 60, 0, 0 ), array( 14, $now - 30, 0, 0 ), array( 15, $now - 10, 0, 0 ),
                       array( 16, $now - 600, 0, $now - 500 ), array( 17, $now - $timeout - 100, 0, 0 ),
                       array( 18, $now - $timeout - 100, $now + 86400, 0 ) );
        foreach ( $rows as $i => $r )
        {
            $row = new expUserSession( array( 'user_id' => $r[0], 'token_hash' => hash( 'sha256', 'r' . $i ), 'created' => $r[1], 'last_active' => $r[1],
                'first_ip' => '', 'last_ip' => '', 'user_agent' => '', 'browser' => 'Firefox', 'os' => 'Linux', 'device_type' => 'desktop',
                'method' => 'password', 'remember_until' => $r[2], 'siteaccess' => '', 'ended' => $r[3], 'ended_by' => 0, 'end_reason' => $r[3] ? 'signout' : '' ) );
            $row->store();
        }
        $report = expUserSessionReport::activeSessions( 10, $now );
        $this->assertSame( 4, $report['total'], 'two of 14, one of 15, one kept by Remember me; ended and timed out left out' );
        $this->assertSame( 3, $report['users'] );
        $this->assertSame( array( 15, 14, 14, 18 ), array_column( $report['sessions'], 'user_id' ), 'the most recently used first' );
        $this->assertSame( 2, count( expUserSessionReport::activeSessions( 2, $now )['sessions'] ) );
        $this->assertNull( expUserSessionReport::findUser( '' ) );
    }

    /** SR-05 */
    public function testFindPatterns()
    {
        $failures = array();
        for ( $i = 0; $i < 6; $i++ )
            $failures[] = array( 'user_id' => 14, 'ip' => '198.51.100.' . ( 10 + $i ) );
        foreach ( array( 20, 21, 22 ) as $userID )
            $failures[] = array( 'user_id' => $userID, 'ip' => '203.0.113.7' );
        $failures[] = array( 'user_id' => 23, 'ip' => '192.0.2.1' );
        $patterns = expUserSessionReport::findPatterns( $failures, array( 30 ), array( 40 => array( 'DE', 'US', 'DE' ), 41 => array( 'DE' ) ), 5, 3 );
        $kinds = array();
        foreach ( $patterns as $p )
            $kinds[] = $p['kind'] . ':' . $p['user_id'];
        $this->assertSame( array( 'network:0', 'locked:30', 'failures:14', 'countries:40' ), $kinds, 'worst first; below the thresholds nothing' );
        $this->assertSame( '203.0.113.x', $patterns[0]['network'], 'the network, never the full address' );
        $this->assertSame( 3, $patterns[0]['accounts'] );
        $this->assertSame( 6, $patterns[2]['count'] );
        $this->assertSame( array( 'DE', 'US' ), $patterns[3]['countries'] );
        // six failures of 14 come from one /24: one network, but only one account, so not a "network" pattern
        $this->assertSame( array(), expUserSessionReport::findPatterns( array(), array(), array() ) );
        $this->assertSame( array(), expUserSessionReport::findPatterns( $failures, array(), array(), 0, 99 ), 'threshold 0: no failures pattern' );
    }

    /** SR-05 */
    public function testPatternsInTheTemplates()
    {
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $tpl = self::read( "design/$design/templates/user/sessionreport.tpl" );
            $this->assertStringContainsString( '$report_patterns', $tpl );
            $this->assertStringNotContainsString( 'style=', $tpl );
        }
        $this->assertStringContainsString( "'report_patterns'", self::read( 'kernel/private/classes/views/user/sessionreport.php' ) );
    }

    /** SR-04 */
    public function testWiring()
    {
        $Module = $ViewList = $FunctionList = null;
        include self::root() . '/kernel/user/module.php';
        $this->assertSame( array( 'sessions_manage' ), $ViewList['sessionreport']['functions'] );
        $this->assertArrayHasKey( 'sessions_manage', $FunctionList );
        $this->assertFileExists( self::root() . '/kernel/user/sessionreport.php' );
        $this->assertSame( self::read( 'design/admin4/templates/user/sessionreport.tpl' ), self::read( 'design/admin/templates/user/sessionreport.tpl' ) );
        $menu = eZINI::instance( 'menu.ini' );
        $this->assertSame( 'user/sessionreport', $menu->variable( 'Leftmenu_user', 'Links' )['session_report'] );
        $this->assertContains( 'access-session_report', $menu->variable( 'Leftmenu_user_mega', 'MegaMenuLinks' ) );
        $site = eZINI::instance();
        // the whole report asks "Verify it's you" before it opens (task 273: other users' devices in full)
        $this->assertContains( 'user/sessionreport *', $site->variable( 'ReauthGroup_users', 'Rules' ) );
        $this->assertContains( 'user/sessions self SaveMailButton RevokeOthersButton', $site->variable( 'ReauthGroup_account', 'Rules' ),
                               'signing out all other sessions asks "Verify it\'s you" first' );
        foreach ( array( 'admin', 'admin4' ) as $design )
            $this->assertStringContainsString( "'user/sessionreport'", self::read( "design/$design/templates/setup/session_no_db.tpl" ) );
    }
}
