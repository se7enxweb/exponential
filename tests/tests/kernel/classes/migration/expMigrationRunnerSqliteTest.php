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
 *  RS-5 runnerEnd() leaves nothing of the test's database in the process: a class only that database has is unknown
 *       afterwards (the class ids by identifier are not kept)
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

    /**
     * RS-5: the class ids by identifier that the process read while the test's database was the global one are not
     * kept once runnerEnd() switched back. Kept, a later class filter of the process used the test database's ids (the
     * hide cronjob hid nothing, the content services found nothing). The check reads the in-memory hash itself: after
     * runnerEnd() the global database is the one of the run, and a run without a database has none to ask.
     */
    public function testTheProcessForgetsTheClassesOfTheTestDatabase()
    {
        $this->db->query( "INSERT INTO ezcontentclass ( id, identifier, version ) VALUES ( 9001, 'exp_migration_runner_probe', 0 )" );
        eZContentClass::expireCache();
        $this->assertSame( 9001, (int)eZContentClass::classIDByIdentifier( 'exp_migration_runner_probe' ),
                           'the class is known while the test database is the global one' );
        $hash = new ReflectionProperty( 'eZContentClass', 'identifierHash' );
        $this->assertArrayHasKey( 'exp_migration_runner_probe', $hash->getValue(), 'the process keeps it in memory' );

        $this->db->close();
        $this->db = null;
        $this->runnerEnd();

        $this->assertNull( $hash->getValue(), 'the class ids of the test database are forgotten after runnerEnd()' );
    }
}
