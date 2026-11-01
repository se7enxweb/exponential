<?php
/**
 * The classes behind the exp:migration commands on SQLite in process: a SQLite file under var/tmp with the kernel
 * schema (kernel/sql/sqlite/schema.sql, which has exp_migration and exp_migration_run) and the clean data, removed
 * afterwards. No server and no installation's database.
 *
 *  CS-1 CE-1 the status
 *  CS-2 CE-2 the simulation with diff (SQLite simulates: its transactions undo schema changes)
 *  CS-3 CE-3 resume of an interrupted run
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationRunnerEngineChecks.php';
require_once __DIR__ . '/expMigrationCommandEngineChecks.php';

class expMigrationCommandSqliteTest extends PHPUnit\Framework\TestCase
{
    use expMigrationRunnerEngineChecks;
    use expMigrationCommandEngineChecks;

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

    /** CS-1 */
    public function testStatus()
    {
        $this->checkStatus( $this->db );
    }

    /** CS-2 */
    public function testSimulation()
    {
        $this->checkSimulation( $this->db );
    }

    /** CS-3 */
    public function testResume()
    {
        $this->checkResume( $this->db );
    }
}
