<?php
/**
 * The classes behind the exp:migration commands on a real database server: MySQL/MariaDB and PostgreSQL in the
 * database job (tests/runtests.php --dsn ... --db-per-test), or a throwaway MySQL database of ezpThrowawayDatabase.
 * The database has the kernel schema (with exp_migration and exp_migration_run) and the clean data. Skipped without
 * one.
 *
 *  CD-1 CE-1 the status
 *  CD-2 CE-2 the simulation: PostgreSQL runs it and rolls it back, MySQL/MariaDB lists the plan with a warning
 *  CD-3 CE-3 resume of an interrupted run
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group database
 */

require_once __DIR__ . '/expMigrationRunnerEngineChecks.php';
require_once __DIR__ . '/expMigrationCommandEngineChecks.php';

#[\PHPUnit\Framework\Attributes\Group('database')]
class expMigrationCommandDatabaseTest extends ezpDatabaseTestCase
{
    use expMigrationRunnerEngineChecks;
    use expMigrationCommandEngineChecks;

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

    /** CD-1 */
    public function testStatus()
    {
        $this->checkStatus( eZDB::instance() );
    }

    /** CD-2 */
    public function testSimulation()
    {
        $this->checkSimulation( eZDB::instance() );
    }

    /** CD-3 */
    public function testResume()
    {
        $this->checkResume( eZDB::instance() );
    }
}
