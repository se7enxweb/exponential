<?php
/**
 * Two costs of the audit dashboard, without the site's database:
 *
 *  - Dashboard::channelTotals(): the records per channel of today and of the period are summed from the per-day
 *    counts the volume chart reads, instead of two groupings that scanned the whole table on SQLite;
 *  - expAuditQuery::where() for a name family (name.*): on SQLite a range of the name index together with the
 *    LIKE, which finds exactly the rows the LIKE alone finds (checked on an SQLite database in memory with the
 *    names around the edges of the range); other engines keep the LIKE alone.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/audit/expAuditDashboardVolumeTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expAuditDashboardVolumeTestDb
{
    public $name;

    public function __construct( $name )
    {
        $this->name = $name;
    }

    public function databaseName()
    {
        return $this->name;
    }

    public function escapeString( $s )
    {
        return str_replace( "'", "''", (string)$s );
    }
}

class expAuditDashboardVolumeTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        if ( !class_exists( 'expAuditQuery' ) || !class_exists( 'Exponential\\View\\Kernel\\Audit\\Dashboard' ) )
            $this->markTestSkipped( 'the audit classes are not autoloaded' );
    }

    public function testChannelTotalsSumTheDaysAndTakeTodayApart()
    {
        $perDay = array(
            '2026-10-06' => array( 'content' => 4, 'access' => 10 ),
            '2026-10-11' => array( 'access' => 3, 'system' => 7 ),
            '2026-10-12' => array( 'access' => 2, 'system' => 9, 'ai' => 1 ),
        );
        list( $today, $week ) = \Exponential\View\Kernel\Audit\Dashboard::channelTotals( $perDay, '2026-10-12' );
        $this->assertSame( array( 'system' => 9, 'access' => 2, 'ai' => 1 ), $today );
        $this->assertSame( array( 'system' => 16, 'access' => 15, 'content' => 4, 'ai' => 1 ), $week );
    }

    public function testChannelTotalsOfAnEmptyPeriod()
    {
        $this->assertSame( array( array(), array() ), \Exponential\View\Kernel\Audit\Dashboard::channelTotals( array(), '2026-10-12' ) );
        list( $today, $week ) = \Exponential\View\Kernel\Audit\Dashboard::channelTotals( array( '2026-10-11' => array( 'access' => 3 ) ), '2026-10-12' );
        $this->assertSame( array(), $today );
        $this->assertSame( array( 'access' => 3 ), $week );
    }

    public function testFamilyConditionPerEngine()
    {
        $sqlite = new expAuditQuery( new expAuditDashboardVolumeTestDb( 'sqlite' ), false );
        $this->assertSame( "(name = 'access.session' OR (name >= 'access.session.' AND name < 'access.session/' AND name LIKE 'access.session.%' ESCAPE '\\'))",
                           $sqlite->where( array( 'name' => 'access.session.*' ) ) );
        $mysql = new expAuditQuery( new expAuditDashboardVolumeTestDb( 'mysql' ), false );
        $this->assertSame( "(name = 'access.session' OR name LIKE 'access.session.%')", $mysql->where( array( 'name' => 'access.session.*' ) ) );
        $postgres = new expAuditQuery( new expAuditDashboardVolumeTestDb( 'postgresql' ), false );
        $this->assertStringNotContainsString( '>=', $postgres->where( array( 'name' => 'content.*' ) ) );
        // a single name and every name are unchanged
        $this->assertSame( "name = 'content.node.move'", $sqlite->where( array( 'name' => 'content.node.move' ) ) );
        $this->assertSame( '1=1', $sqlite->where( array( 'name' => '*' ) ) );
    }

    public function testFamilyRangeFindsWhatTheLikeFinds()
    {
        if ( !class_exists( 'PDO' ) || !in_array( 'sqlite', PDO::getAvailableDrivers(), true ) )
            $this->markTestSkipped( 'no PDO SQLite' );
        $pdo = new PDO( 'sqlite::memory:' );
        $pdo->exec( 'CREATE TABLE expaudit_event (id INTEGER PRIMARY KEY, name VARCHAR(128), time_ms BIGINT)' );
        $pdo->exec( 'CREATE INDEX expaudit_event_name ON expaudit_event (name, time_ms)' );
        $names = array( 'access', 'access.session', 'access.session.login', 'access.session.login.failed', 'access.sessions.x',
                        'access.session_x.y', 'access.sessionx', 'access.role.assign', 'accessx.y', 'access/x', 'access-x.y',
                        'system.expai.request', 'system.expai.request.failed', 'system.expai.request.refused', 'system.expai.requests',
                        'system.expai_request.x', 'system.expai.budget.hold', 'content.node.move', 'content.expai_draft.write' );
        $insert = $pdo->prepare( 'INSERT INTO expaudit_event (name, time_ms) VALUES (?, ?)' );
        foreach ( $names as $i => $name )
            $insert->execute( array( $name, 1000 + $i ) );

        $sqlite = new expAuditQuery( new expAuditDashboardVolumeTestDb( 'sqlite' ), false );
        foreach ( array( 'access', 'access.session', 'access.session_x', 'system.expai.request', 'system.expai', 'content', 'system' ) as $prefix )
        {
            $like = "(name = '$prefix' OR name LIKE '" . str_replace( '_', '\\_', $prefix ) . ".%' ESCAPE '\\')";
            $expected = $pdo->query( "SELECT name FROM expaudit_event WHERE $like ORDER BY name" )->fetchAll( PDO::FETCH_COLUMN );
            $found = $pdo->query( 'SELECT name FROM expaudit_event WHERE ' . $sqlite->where( array( 'name' => $prefix . '.*' ) ) . ' ORDER BY name' )
                         ->fetchAll( PDO::FETCH_COLUMN );
            $this->assertSame( $expected, $found, "family $prefix.*" );
            $plan = implode( ' ', $pdo->query( 'EXPLAIN QUERY PLAN SELECT COUNT(*) FROM expaudit_event WHERE ' . $sqlite->where( array( 'name' => $prefix . '.*' ) ) )
                                      ->fetchAll( PDO::FETCH_COLUMN, 3 ) );
            $this->assertStringContainsString( 'expaudit_event_name', $plan, "family $prefix.* uses the name index" );
        }
    }
}
