<?php
/**
 * The exp:upgrade bridge on a real database server: MySQL/MariaDB and PostgreSQL in the database job
 * (tests/runtests.php --dsn ... --db-per-test), or a throwaway MySQL database of ezpThrowawayDatabase. The database
 * has the kernel schema (with exp_migration and exp_migration_run; expupgrade_step of a pre-release is added by the checks) and the clean data. Skipped
 * without one.
 *
 *  UD-1 UE-1 the status lists kernel steps and site migrations together (expMigrationUpgradeSourceEngineChecks)
 *  UD-2 UE-2 migrate runs the pending kernel steps first, copies expupgrade_step once and drops it, and is idempotent
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group database
 */

require_once __DIR__ . '/expMigrationRunnerEngineChecks.php';
require_once __DIR__ . '/expMigrationUpgradeSourceEngineChecks.php';

#[\PHPUnit\Framework\Attributes\Group('database')]
class expMigrationUpgradeSourceDatabaseTest extends ezpDatabaseTestCase
{
    use expMigrationRunnerEngineChecks;
    use expMigrationUpgradeSourceEngineChecks;

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

    /** UD-1 */
    public function testStatusListsKernelAndSiteMigrations()
    {
        $this->checkBridgeStatus( eZDB::instance() );
    }

    /** UD-2 */
    public function testMigrateRunsKernelStepsFirst()
    {
        $this->checkBridgeMigrate( eZDB::instance() );
    }
}
