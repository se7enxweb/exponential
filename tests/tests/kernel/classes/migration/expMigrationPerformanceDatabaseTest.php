<?php
/**
 * The performance checks of the migration engine on a real database server: MySQL/MariaDB and PostgreSQL in the
 * database job (tests/runtests.php --dsn ... --db-per-test), or a throwaway MySQL database of ezpThrowawayDatabase.
 * Skipped without one. The checks: expMigrationPerformanceEngineChecks.
 *
 *  PD-1 PF-1 1,000 YAML migrations: discovery, ordering, checksums, the run, a second run, the status
 *  PD-2 PF-2 a large data step: 5,000 rows inserted, updated and deleted
 *  PD-3 PF-3 a long run with checkpoints, stopped and resumed
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group database
 */

require_once __DIR__ . '/expMigrationPerformanceEngineChecks.php';

#[\PHPUnit\Framework\Attributes\Group('database')]
class expMigrationPerformanceDatabaseTest extends ezpDatabaseTestCase
{
    use expMigrationPerformanceEngineChecks;

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

    /** PD-1 */
    public function testThousandMigrations()
    {
        $this->checkThousandMigrations( eZDB::instance() );
    }

    /** PD-2 */
    public function testLargeDataStep()
    {
        $this->checkLargeDataStep( eZDB::instance() );
    }

    /** PD-3 */
    public function testLongRunWithCheckpoints()
    {
        $this->checkLongRunWithCheckpoints( eZDB::instance() );
    }
}
