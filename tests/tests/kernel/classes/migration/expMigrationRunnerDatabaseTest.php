<?php
/**
 * The migration runner on a real database server: MySQL/MariaDB, PostgreSQL and Oracle in the database job
 * (tests/runtests.php --dsn ... --db-per-test), or a throwaway MySQL database of ezpThrowawayDatabase. The database
 * has the kernel schema (with exp_migration and exp_migration_run) and the clean data. Skipped without one.
 *
 *  RD-1 RE-1 a PHP migration that creates a table and one that publishes a folder as the run's user
 *  RD-2 RE-2 transactions: PostgreSQL undoes everything a failed migration did, MySQL keeps the table it created
 *  RD-3 RE-3 rollback with down()
 *  RD-4 RE-4 batches() with a checkpoint: a failed run goes on from the last committed batch
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group database
 */

require_once __DIR__ . '/expMigrationRunnerEngineChecks.php';

#[\PHPUnit\Framework\Attributes\Group('database')]
class expMigrationRunnerDatabaseTest extends ezpDatabaseTestCase
{
    use expMigrationRunnerEngineChecks;

    protected function setUp(): void
    {
        parent::setUp();
        $engine = expKernelTableSchema::type( eZDB::instance() );
        if ( !in_array( $engine, array( 'mysql', 'postgresql', 'oracle' ), true ) )
            $this->markTestSkipped( "not a MySQL, PostgreSQL or Oracle database: $engine" );
        $this->runnerBegin( eZDB::instance() );
    }

    protected function tearDown(): void
    {
        $this->runnerEnd();
        parent::tearDown();
    }

    /** RD-1, RD-3 */
    public function testPhpMigrationAndRollback()
    {
        $this->checkPhpMigrationRun( eZDB::instance() );
        $this->checkRollback( eZDB::instance() );
    }

    /** RD-2 */
    public function testTransactions()
    {
        $this->checkTransactions( eZDB::instance() );
    }

    /** RD-4 */
    public function testBatches()
    {
        $this->checkBatches( eZDB::instance() );
    }
}
