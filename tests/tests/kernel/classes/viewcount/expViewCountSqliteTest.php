<?php
/**
 * The view counter on SQLite in process: the kernel schema and the clean data in a SQLite file under var/tmp,
 * an access log next to it, the real URL alias lookup (eZURLAliasML), the real node check and the real write
 * (eZViewCounter::addCounts()), which raises an existing row and adds the missing ones in one transaction.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/migration/expMigrationRunnerEngineChecks.php';
require_once __DIR__ . '/expViewCountTestFiles.php';

class expViewCountSqliteTest extends PHPUnit\Framework\TestCase
{
    use expMigrationRunnerEngineChecks;
    use expViewCountTestFiles;

    /** @var eZSQLite3DB|null */
    private $db;

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        $this->db = new eZSQLite3DB( array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => $this->runnerDir() . '/kernel.sqlite',
                                            'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false, 'slave_user' => '',
                                            'slave_password' => '', 'slave_database' => '', 'socket' => false, 'charset' => 'utf-8',
                                            'is_internal_charset' => false, 'builtin_encoding' => false, 'connect_retries' => 1,
                                            'use_persistent_connection' => false, 'show_errors' => false ) );
        $this->runnerBegin( $this->db );
        foreach ( array( 'schema.sql', 'cleandata.sql' ) as $sql )
            $this->assertNotFalse( $this->db->insertFile( self::runnerRoot() . '/kernel/sql/sqlite', $sql, false ), "kernel/sql/sqlite/$sql loads" );
    }

    protected function tearDown(): void
    {
        if ( $this->db )
            $this->db->close();
        $this->runnerEnd();
        $this->removeRunnerDir();
        $this->removeViewCountDir();
    }

    private function counts()
    {
        $counts = array();
        foreach ( (array)$this->db->arrayQuery( 'SELECT node_id, count FROM ezview_counter ORDER BY node_id' ) as $row )
            $counts[(int)$row['node_id']] = (int)$row['count'];
        return $counts;
    }

    private function run_( $log, array $options = array() )
    {
        $site = new expViewCountSite( array( 'name' => 'site', 'logFile' => $log, 'indexPage' => '/content/view/full/2',
                                             'languages' => array( 'eng-GB' ) ) );
        $config = new expViewCountConfiguration( array( 'state_dir' => $this->viewCountDir() . '/state', 'lock_timeout' => 0 ), array( $site ) );
        return ( new expViewCountUpdater( $config, $options + array( 'legacy_dir' => $this->viewCountDir() . '/legacy', 'top' => 3 ) ) )->run();
    }

    public function testViewsAreCountedIntoTheViewCounter()
    {
        // a row that exists is raised, the others are made
        $this->db->query( 'INSERT INTO ezview_counter ( node_id, count ) VALUES ( 43, 10 )' );
        $log = $this->viewCountDir() . '/logs/access_log';
        file_put_contents( $log, implode( '', array(
            self::combined( '/' ),
            self::combined( '/Media' ),
            self::combined( '/Media/Images' ),
            self::combined( '/media/images/(offset)/10' ),
            self::combined( '/content/view/full/5' ),
            self::combined( '/content/view/full/999999' ),
            self::combined( '/no-such-alias' ),
            self::combined( '/Users', 200, 'Googlebot/2.1' ),
        ) ) );

        $dry = $this->run_( $log, array( 'dry_run' => true ) );
        $this->assertSame( 5, $dry['views'], implode( "\n", expViewCountUpdater::lines( $dry ) ) );
        $this->assertSame( array( 43 => 10 ), $this->counts(), 'a dry run writes nothing' );

        $report = $this->run_( $log );
        $this->assertSame( 0, $report['exit_code'], implode( "\n", expViewCountUpdater::lines( $report ) ) );
        $this->assertTrue( $report['written'] );
        $this->assertSame( array( 2 => 1, 5 => 1, 43 => 11, 51 => 2 ), $this->counts() );
        $this->assertSame( 1, $report['logs'][0]['skipped']['no-node'] );
        $this->assertSame( 1, $report['logs'][0]['skipped']['not-content'] );
        $this->assertSame( 1, $report['logs'][0]['skipped']['bot'] );
        $this->assertSame( 51, $report['top'][0]['node_id'] );
        $this->assertSame( 'Images', $report['top'][0]['name'] );

        // the next run adds only what is new, to the rows that exist now
        file_put_contents( $log, self::combined( '/Media/Images' ) . self::combined( '/Users' ), FILE_APPEND );
        $this->run_( $log );
        $this->assertSame( array( 2 => 1, 5 => 2, 43 => 11, 51 => 3 ), $this->counts() );
        $this->assertSame( 3, eZViewCounter::fetch( 51 )->attribute( 'count' ) );
    }

    public function testAddCountsLeavesOutWhatIsNotACount()
    {
        $this->assertTrue( eZViewCounter::addCounts( array() ) );
        $this->assertTrue( eZViewCounter::addCounts( array( 0 => 5, 2 => 0, 5 => -1, 43 => 2 ) ) );
        $this->assertSame( array( 43 => 2 ), $this->counts() );
    }
}
