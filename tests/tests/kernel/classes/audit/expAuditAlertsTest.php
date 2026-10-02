<?php
/**
 * The alert rules (doc/bc/6.0/audit.md, "Alerts (F5)", acceptance test E2): each built-in rule reached, not
 * reached and repeated, an INI rule, de-duplication and the cronjob pass. All events are simulated into a test
 * directory (never the live log).
 *
 *  AL-01 — brute_force: 19 failed logins from 203.0.113.0/24 within 300 s fire nothing; the 20th fires once;
 *          the 40th fires again (the count doubled); a new window after it closed fires again
 *  AL-02 — brute_force_user: 10 failed logins for one account from several networks fire; 9 do not
 *  AL-03 — admin_role_granted: a role granting every module or setup fires; one granting content/read does not
 *  AL-04 — settings_out_of_hours: a settings write at 23:00 fires, one at 10:00 on a weekday does not; ten
 *          writes at night by one user fire once (one per group and window)
 *  AL-05 — mass_delete: 500 removed nodes (a parent with children_omitted counted) fire; 499 do not
 *  AL-06 — audit_disabled: the cronjob pass finds Audit=disabled where the state file said enabled, writes
 *          system.audit.disable although audit is off, and the rule fires (emergency)
 *  AL-07 — chain_broken: verifying a tampered channel records system.audit.chain.broken and the rule fires
 *  AL-08 — an INI rule ([AlertRule_*] Class, Event, Threshold, Window, GroupBy, Severity, Sinks[]) fires
 *  AL-09 — the same records evaluated again (flush, then the cronjob pass) fire nothing more; the cronjob pass
 *          reads only what is new; replay runs a rule without recording anything
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group audit
 */

require_once __DIR__ . '/fixtures/expaudittestfixtures.php';

class expAuditAlertsTest extends PHPUnit\Framework\TestCase
{
    /** Fri 2026-10-02 12:00:00 UTC */
    const T = 1790942400;

    protected $dir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = expAuditTestFixtures::setUp( $this->name() );
        expAuditAlertEvaluator::$fired = array();
        date_default_timezone_set( 'UTC' );
    }

    protected function tearDown(): void
    {
        expAuditTestFixtures::tearDown();
        parent::tearDown();
    }

    /** @return array[] system.audit.alert records of the test system channel, by rule */
    protected function alerts( $rule = null )
    {
        $out = array();
        foreach ( expAuditWriter::channelFiles( $this->dir . 'log', 'system' ) as $f )
            foreach ( file( $this->dir . 'log/' . $f, FILE_IGNORE_NEW_LINES ) as $l )
            {
                $r = json_decode( $l, true );
                if ( $r['name'] === 'system.audit.alert' && ( $rule === null || $r['after']['rule'] === $rule ) )
                    $out[] = $r;
            }
        return $out;
    }

    /** Failed logins at $t, one per second, from an address. */
    protected function failed( $n, $t, $ip = '203.0.113.7', $userID = 14 )
    {
        for ( $i = 0; $i < $n; $i++ )
        {
            expAudit::setNow( $t + $i );
            expAudit::event( 'access.session.login.failed', array( 'object' => array( 'type' => 'user', 'id' => $userID ),
                                                                 'actor' => array( 'ip' => $ip ), 'result' => 'failed', 'reason' => 'credentials' ) );
        }
    }

    /** AL-01 */
    public function testBruteForce()
    {
        // per account limits do not get in the way here: every attempt names another account
        for ( $i = 0; $i < 19; $i++ )
            $this->failed( 1, self::T + $i, '203.0.113.' . ( 10 + $i ), 1000 + $i );
        $this->assertCount( 0, $this->alerts( 'brute_force' ), '19 do not fire' );
        $this->failed( 1, self::T + 19, '203.0.113.99', 2000 );
        $a = $this->alerts( 'brute_force' );
        $this->assertCount( 1, $a, 'the 20th fires' );
        $this->assertSame( 'critical', $a[0]['severity'] );
        $this->assertSame( '203.0.113.0/24', $a[0]['after']['group'] );
        $this->assertSame( 20, $a[0]['after']['count'] );
        $this->assertSame( array( 'syslog', 'mail' ), $a[0]['after']['sinks'] );
        for ( $i = 0; $i < 19; $i++ )
            $this->failed( 1, self::T + 20 + $i, '203.0.113.50', 3000 + $i );
        $this->assertCount( 1, $this->alerts( 'brute_force' ), 'the 21st to 39th update the open alert' );
        $this->failed( 1, self::T + 39, '203.0.113.51', 4000 );
        $a = $this->alerts( 'brute_force' );
        $this->assertCount( 2, $a, 'the 40th fires again' );
        $this->assertSame( 40, $a[1]['after']['count'] );
        // another network does not count
        $this->failed( 1, self::T + 40, '198.51.100.1', 5000 );
        $this->assertCount( 2, $this->alerts( 'brute_force' ) );
        // a new window after the old one closed
        for ( $i = 0; $i < 20; $i++ )
            $this->failed( 1, self::T + 1000 + $i, '203.0.113.' . ( 100 + $i ), 6000 + $i );
        $this->assertCount( 3, $this->alerts( 'brute_force' ) );
    }

    /** AL-02 */
    public function testBruteForceUser()
    {
        for ( $i = 0; $i < 9; $i++ )
            $this->failed( 1, self::T + $i * 60, '198.51.' . $i . '.1', 14 );
        $this->assertCount( 0, $this->alerts( 'brute_force_user' ) );
        $this->failed( 1, self::T + 600, '192.0.2.1', 14 );
        $a = $this->alerts( 'brute_force_user' );
        $this->assertCount( 1, $a );
        $this->assertSame( '14', $a[0]['after']['group'] );
        $this->assertSame( 'alert', $a[0]['severity'] );
    }

    /** AL-03 */
    public function testAdminRoleGranted()
    {
        expAudit::setNow( self::T );
        expAudit::event( 'access.role.assign', array( 'object' => array( 'type' => 'role', 'id' => 1 ), 'target' => array( 'type' => 'user', 'id' => 99 ),
                                                    'after' => array( 'policies' => array( 'content/read', 'content/pdf' ) ) ) );
        $this->assertCount( 0, $this->alerts( 'admin_role_granted' ), 'content/read is not an admin policy' );
        expAudit::event( 'access.role.assign', array( 'object' => array( 'type' => 'role', 'id' => 2 ), 'target' => array( 'type' => 'user', 'id' => 99 ),
                                                    'after' => array( 'policies' => array( '*/*' ) ) ) );
        expAudit::event( 'access.role.assign', array( 'object' => array( 'type' => 'role', 'id' => 3 ), 'target' => array( 'type' => 'group', 'id' => 12 ),
                                                    'after' => array( 'policies' => array( 'content/read', 'setup/managecache' ) ) ) );
        $a = $this->alerts( 'admin_role_granted' );
        $this->assertCount( 2, $a );
        $this->assertSame( array( '*/*', 'setup/*', 'role/*', 'audit/manage' ), $a[0]['after']['policies'], '*/* grants everything' );
        $this->assertSame( array( 'setup/*' ), $a[1]['after']['policies'] );
        $this->assertSame( 'critical', $a[0]['severity'] );
    }

    /** AL-04 */
    public function testSettingsOutOfHours()
    {
        $write = function ( $t, $user = 14 ) {
            expAudit::setNow( $t );
            expAudit::event( 'system.setting.write', array( 'actor' => array( 'user_id' => $user, 'login' => 'u' . $user ),
                                                          'object' => array( 'type' => 'setting', 'file' => 'site.ini', 'block' => 'DebugSettings', 'variable' => 'DebugOutput' ) ) );
        };
        $write( self::T - 2 * 3600 ); // Fri 10:00
        $this->assertCount( 0, $this->alerts( 'settings_out_of_hours' ), 'inside business hours' );
        for ( $i = 0; $i < 10; $i++ )
            $write( self::T + 11 * 3600 + $i * 60 ); // Fri 23:00 ...
        $a = $this->alerts( 'settings_out_of_hours' );
        $this->assertCount( 1, $a, 'ten writes at night by one user: one alert' );
        $this->assertSame( 'warning', $a[0]['severity'] );
        $this->assertSame( array( 'syslog' ), $a[0]['after']['sinks'] );
        $write( self::T + 86400 - 2 * 3600 + 3600 ); // Sat 11:00: a weekend
        $this->assertCount( 2, $this->alerts( 'settings_out_of_hours' ), 'Saturday is out of hours, another window' );
    }

    /** AL-05 */
    public function testMassDelete()
    {
        expAudit::setNow( self::T );
        $parent = expAudit::begin( 'content.node.remove', array( 'object' => array( 'type' => 'node', 'id' => 89 ) ) );
        for ( $i = 0; $i < 9; $i++ )
            expAudit::event( 'content.node.remove', array( 'parent' => $parent, 'object' => array( 'type' => 'node', 'id' => 1000 + $i ) ) );
        expAudit::end( $parent, array( 'after' => array( 'removed' => 498, 'children_omitted' => 489 ) ) );
        expAudit::flush();
        $this->assertCount( 0, $this->alerts( 'mass_delete' ), '9 children + the parent counting 489 omitted = 499' );
        expAudit::event( 'content.node.remove.trash', array( 'object' => array( 'type' => 'node', 'id' => 2000 ) ) );
        expAudit::flush();
        $a = $this->alerts( 'mass_delete' );
        $this->assertCount( 1, $a, 'the 500th' );
        $this->assertSame( 500, $a[0]['after']['count'] );
        $this->assertSame( '14', $a[0]['after']['group'] );
    }

    /** AL-06 */
    public function testAuditDisabledFoundByTheCronjobPass()
    {
        expAudit::setNow( null );
        $this->assertFalse( expAuditAlertEvaluator::cronjob()['disabled'], 'the first pass only notes the state' );
        $this->assertSame( 'enabled', file_get_contents( $this->dir . 'log/.state' ) );
        expAuditTestFixtures::configure( $this->dir, array( 'AuditSettings/Audit' => 'disabled' ) );
        $this->assertNull( expAudit::event( 'access.session.login', array() ), 'audit is off' );
        $r = expAuditAlertEvaluator::cronjob();
        $this->assertTrue( $r['disabled'] );
        $system = array_map( 'json_decode', file( $this->dir . 'log/' . expAuditWriter::channelFiles( $this->dir . 'log', 'system' )[0], FILE_IGNORE_NEW_LINES ) );
        $names = array_map( function ( $r ) { return $r->name; }, $system );
        $this->assertContains( 'system.audit.disable', $names, 'written although audit is off' );
        $a = $this->alerts( 'audit_disabled' );
        $this->assertCount( 1, $a );
        $this->assertSame( 'emergency', $a[0]['severity'] );
        $this->assertSame( 'disabled', file_get_contents( $this->dir . 'log/.state' ) );
        $this->assertFalse( expAuditAlertEvaluator::cronjob()['disabled'], 'reported once' );
        $this->assertSame( 'intact', expAuditTestFixtures::verifier( $this->dir )->verifyChannel( 'system' )['result'] );
    }

    /** AL-07 */
    public function testChainBroken()
    {
        expAudit::setNow( self::T );
        for ( $i = 0; $i < 5; $i++ )
            expAudit::event( 'content.node.move', array( 'object' => array( 'type' => 'node', 'id' => $i, 'name' => 'Node ' . $i ) ) );
        expAudit::flush();
        $file = $this->dir . 'log/' . expAuditWriter::channelFiles( $this->dir . 'log', 'content' )[0];
        file_put_contents( $file, str_replace( '"Node 3"', '"Node X"', file_get_contents( $file ) ) );
        $report = ( new expAuditMaintenance( null, self::T ) )->verify();
        $this->assertSame( 'broken', $report['content'] );
        $a = $this->alerts( 'chain_broken' );
        $this->assertCount( 1, $a );
        $this->assertSame( 'alert', $a[0]['severity'] );
    }

    /** AL-08 */
    public function testIniRule()
    {
        expAuditTestFixtures::configure( $this->dir, array(
            'AuditAlertSettings/Rules' => array( 'many_moves' ),
            'AlertRule_many_moves/Class' => 'threshold', 'AlertRule_many_moves/Event' => 'content.node.*',
            'AlertRule_many_moves/Threshold' => '3', 'AlertRule_many_moves/Window' => '60', 'AlertRule_many_moves/GroupBy' => 'object.type',
            'AlertRule_many_moves/Severity' => 'notice', 'AlertRule_many_moves/Sinks' => array( 'webhook' ) ) );
        $rules = expAuditAlertEvaluator::rules();
        $this->assertSame( array( 'many_moves' ), array_keys( $rules ) );
        $this->assertSame( '', $rules['many_moves']['problem'] );
        for ( $i = 0; $i < 3; $i++ )
        {
            expAudit::setNow( self::T + $i * 10 );
            expAudit::event( 'content.node.move', array( 'object' => array( 'type' => 'node', 'id' => $i ) ) );
            expAudit::flush();
        }
        $a = $this->alerts( 'many_moves' );
        $this->assertCount( 1, $a );
        $this->assertSame( 'notice', $a[0]['severity'] );
        $this->assertSame( 'node', $a[0]['after']['group'] );
        $this->assertSame( array( 'webhook' ), $a[0]['after']['sinks'] );
        // a broken INI rule is reported, not evaluated
        expAuditTestFixtures::configure( $this->dir, array( 'AuditAlertSettings/Rules' => array( 'nothing_here', 'bad_class' ),
                                                            'AlertRule_bad_class/Class' => 'nosuch', 'AlertRule_bad_class/Event' => 'content.*' ) );
        $rules = expAuditAlertEvaluator::rules();
        $this->assertStringContainsString( 'no [AlertRule_nothing_here] block', $rules['nothing_here']['problem'] );
        $this->assertStringContainsString( 'Class=nosuch', $rules['bad_class']['problem'] );
    }

    /** AL-09 */
    public function testIdempotentCronjobPassAndReplay()
    {
        expAuditAlertEvaluator::cronjob(); // the cursor starts at the end
        $this->failed( 20, self::T );
        $this->assertCount( 1, $this->alerts( 'brute_force' ) );
        $r = expAuditAlertEvaluator::cronjob();
        $this->assertGreaterThanOrEqual( 20, $r['evaluated'], 'the cronjob pass reads the new records' );
        $this->assertSame( 0, $r['alerts'], 'and fires nothing a flush fired already' );
        $this->assertCount( 1, $this->alerts( 'brute_force' ) );
        $this->assertSame( 0, expAuditAlertEvaluator::cronjob()['evaluated'], 'nothing new' );

        $before = count( file( $this->dir . 'log/' . expAuditWriter::channelFiles( $this->dir . 'log', 'system' )[0] ) );
        $replay = expAuditAlertEvaluator::replay( 'brute_force', '2026-10-01' );
        $this->assertCount( 1, $replay['alerts'], 'the rule over the past records, in memory' );
        $this->assertNull( $replay['alerts'][0]['event'] );
        $this->assertSame( $before, count( file( $this->dir . 'log/' . expAuditWriter::channelFiles( $this->dir . 'log', 'system' )[0] ) ), 'nothing recorded' );
    }
}
