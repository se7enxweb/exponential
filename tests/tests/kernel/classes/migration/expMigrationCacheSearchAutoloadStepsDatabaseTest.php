<?php
/**
 * The step types cache and search, and the caches and search index after a run, on a real database server:
 * MySQL/MariaDB and PostgreSQL in the database job (tests/runtests.php --dsn ... --db-per-test), or a throwaway MySQL
 * database of ezpThrowawayDatabase. The database has the kernel schema and the clean data. Skipped without one, and
 * without symfony/yaml.
 *
 *  CD-1 CE-1 the run's cache list, the deploy and the touched objects queued; a reindex at once
 *  CD-2 CE-2 a full reindex of a class queued; a class that does not exist fails
 *  CD-3 CE-3 Reindex=disabled and a dry run
 *  CD-4 CE-4 the caches of a rollback
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group database
 */

require_once __DIR__ . '/expMigrationCacheSearchAutoloadStepsEngineChecks.php';

#[\PHPUnit\Framework\Attributes\Group('database')]
class expMigrationCacheSearchAutoloadStepsDatabaseTest extends ezpDatabaseTestCase
{
    use expMigrationCacheSearchAutoloadStepsEngineChecks;

    protected function setUp(): void
    {
        parent::setUp();
        $engine = expKernelTableSchema::type( eZDB::instance() );
        if ( !in_array( $engine, array( 'mysql', 'postgresql', 'oracle' ), true ) )
            $this->markTestSkipped( "not a MySQL, PostgreSQL or Oracle database: $engine" );
        $this->contentBegin();
        $this->runnerBegin( eZDB::instance() );
    }

    protected function tearDown(): void
    {
        $this->runnerEnd();
        $this->contentEnd();
        parent::tearDown();
    }

    /** CD-1 */
    public function testCachesAndReindex()
    {
        $this->checkCachesAndReindex( eZDB::instance() );
    }

    /** CD-2 */
    public function testFullReindex()
    {
        $this->checkFullReindex( eZDB::instance() );
    }

    /** CD-3 */
    public function testReindexSettings()
    {
        $this->checkReindexSettings( eZDB::instance() );
    }

    /** CD-4 */
    public function testRollbackCaches()
    {
        $this->checkRollbackCaches( eZDB::instance() );
    }
}
