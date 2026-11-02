<?php
/**
 * The migration runner on SQLite in process: a SQLite file under var/tmp with the kernel schema
 * (kernel/sql/sqlite/schema.sql, which has exp_migration and exp_migration_run) and the clean data, removed afterwards.
 * No server and no installation's database.
 *
 *  RS-1 RE-1 a PHP migration that creates a table and one that publishes a folder (expMigrationRunnerEngineChecks)
 *  RS-2 RE-2 transactions: everything a failed migration did is rolled back
 *  RS-3 RE-3 rollback with down()
 *  RS-4 RE-4 batches() with a checkpoint: a failed run goes on from the last committed batch
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationRunnerEngineChecks.php';

class expMigrationRunnerSqliteTest extends PHPUnit\Framework\TestCase
{
    use expMigrationRunnerEngineChecks;

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

    /** RS-1, RS-3 */
    public function testPhpMigrationAndRollback()
    {
        $this->checkPhpMigrationRun( $this->db );
        $this->checkRollback( $this->db );
    }

    /** RS-2 */
    public function testTransactions()
    {
        $this->checkTransactions( $this->db );
    }

    /** RS-4 */
    public function testBatches()
    {
        $this->checkBatches( $this->db );
    }
}
