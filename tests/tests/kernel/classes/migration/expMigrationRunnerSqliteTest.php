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
 *  RS-5 runnerEnd() leaves nothing of the test's database in the process: the class and class attribute ids by
 *       identifier are not kept (checked in the process, so it needs no installation database)
 *  RS-6 the class and class attribute ids follow the global database: switched to another one, a class only the first
 *       has is unknown, and known again after switching back, without expireCache()
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
        $this->db = $this->sqlite( 'kernel.sqlite' );
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
     * RS-5: the class and class attribute ids by identifier that the process read while the test's database was the
     * global one are not kept once runnerEnd() switched back. Kept, a later class filter of the process used the test
     * database's ids (the hide cronjob hid nothing, the content services found nothing). The check reads the in-memory
     * hashes themselves: after runnerEnd() the global database is the one of the run, and a run without a database has
     * none to ask.
     */
    public function testTheProcessForgetsTheClassesOfTheTestDatabase()
    {
        $this->addProbeClass( $this->db );
        eZContentClass::expireCache();
        $this->assertSame( 9001, (int)eZContentClass::classIDByIdentifier( 'exp_migration_runner_probe' ),
                           'the class is known while the test database is the global one' );
        $this->assertSame( 9002, (int)eZContentClassAttribute::classAttributeIDByIdentifier( 'exp_migration_runner_probe/probe' ),
                           'the class attribute is known while the test database is the global one' );
        $hash = new ReflectionProperty( 'eZContentClass', 'identifierHash' );
        if ( PHP_VERSION_ID < 80100 )
            $hash->setAccessible( true );
        $this->assertArrayHasKey( 'exp_migration_runner_probe', $hash->getValue(), 'the process keeps it in memory' );

        $this->db->close();
        $this->db = null;
        $this->runnerEnd();

        // read from the process, not from the installation's database: a test run may have none (CI)
        foreach ( array( 'eZContentClass', 'eZContentClassAttribute' ) as $class )
        {
            $hash = new ReflectionProperty( $class, 'identifierHash' );
            if ( PHP_VERSION_ID < 80100 )
                $hash->setAccessible( true );
            $this->assertNull( $hash->getValue(), "$class keeps no ids of the test database after runnerEnd()" );
        }
    }

    /**
     * RS-6: the kernel itself does not answer with the ids of another database: a process that switches the global
     * database (eZDB::setInstance(), as a script or a test does) gets the ids of the database it switched to, without
     * expireCache(), and those of the first one again when it switches back.
     */
    public function testClassIdsFollowTheGlobalDatabase()
    {
        $this->addProbeClass( $this->db );
        eZContentClass::expireCache();
        $this->assertSame( 9001, (int)eZContentClass::classIDByIdentifier( 'exp_migration_runner_probe' ) );
        $this->assertSame( 9002, (int)eZContentClassAttribute::classAttributeIDByIdentifier( 'exp_migration_runner_probe/probe' ) );

        $other = $this->sqlite( 'other.sqlite' );
        $other->query( 'CREATE TABLE ezcontentclass ( id INTEGER, identifier TEXT, version INTEGER )' );
        $other->query( 'CREATE TABLE ezcontentclass_attribute ( id INTEGER, identifier TEXT, contentclass_id INTEGER, version INTEGER )' );
        try
        {
            eZDB::setInstance( $other );
            $this->assertFalse( eZContentClass::classIDByIdentifier( 'exp_migration_runner_probe' ),
                                'a class of the first database is unknown in the other one' );
            $this->assertFalse( eZContentClassAttribute::classAttributeIDByIdentifier( 'exp_migration_runner_probe/probe' ),
                                'a class attribute of the first database is unknown in the other one' );
        }
        finally
        {
            eZDB::setInstance( $this->db );
            $other->close();
        }
        $this->assertSame( 9001, (int)eZContentClass::classIDByIdentifier( 'exp_migration_runner_probe' ),
                           'back on the first database, its class is known again' );
        $this->assertSame( 9002, (int)eZContentClassAttribute::classAttributeIDByIdentifier( 'exp_migration_runner_probe/probe' ),
                           'back on the first database, its class attribute is known again' );
    }

    /**
     * A class and a class attribute only the given database has
     *
     * @param eZDBInterface $db
     */
    private function addProbeClass( $db )
    {
        $db->query( "INSERT INTO ezcontentclass ( id, identifier, version ) VALUES ( 9001, 'exp_migration_runner_probe', 0 )" );
        $db->query( "INSERT INTO ezcontentclass_attribute ( id, identifier, contentclass_id, version, serialized_name_list ) VALUES ( 9002, 'probe', 9001, 0, '' )" );
    }

    /**
     * A connection to a SQLite file in the directory of the test
     *
     * @param string $file
     * @return eZSQLite3DB
     */
    private function sqlite( $file )
    {
        return new eZSQLite3DB( array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => $this->runnerDir() . '/' . $file,
                                       'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false, 'slave_user' => '',
                                       'slave_password' => '', 'slave_database' => '', 'socket' => false, 'charset' => 'utf-8',
                                       'is_internal_charset' => false, 'builtin_encoding' => false, 'connect_retries' => 1,
                                       'use_persistent_connection' => false, 'show_errors' => false ) );
    }
}
