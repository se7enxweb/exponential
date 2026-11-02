<?php
/**
 * The performance checks of the migration engine on SQLite in process: a SQLite file under var/tmp with the kernel
 * schema and the clean data, removed afterwards. No server and no installation's database. The checks: expMigrationPerformanceEngineChecks.
 *
 *  PS-1 PF-1 1,000 YAML migrations: discovery, ordering, checksums, the run, a second run, the status
 *  PS-2 PF-2 a large data step: 5,000 rows inserted, updated and deleted
 *  PS-3 PF-3 a long run with checkpoints, stopped and resumed
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationPerformanceEngineChecks.php';

class expMigrationPerformanceSqliteTest extends PHPUnit\Framework\TestCase
{
    use expMigrationPerformanceEngineChecks;

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
    }

    /** PS-1 */
    public function testThousandMigrations()
    {
        $this->checkThousandMigrations( $this->db );
    }

    /** PS-2 */
    public function testLargeDataStep()
    {
        $this->checkLargeDataStep( $this->db );
    }

    /** PS-3 */
    public function testLongRunWithCheckpoints()
    {
        $this->checkLongRunWithCheckpoints( $this->db );
    }
}
