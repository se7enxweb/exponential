<?php
/**
 * The content step types on a real database server: MySQL/MariaDB and PostgreSQL in the database job
 * (tests/runtests.php --dsn ... --db-per-test), or a throwaway MySQL database of ezpThrowawayDatabase. The database has
 * the kernel schema and the clean data. Skipped without one, and without symfony/yaml.
 *
 *  CD-1 CE-1 content, location and content_version steps through the runner
 *  CD-2 CE-2 steps that undo themselves, rolled back
 *  CD-3 CE-3 a failing migration: its object is rolled back on PostgreSQL and MySQL alike
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group database
 */

require_once __DIR__ . '/expMigrationContentStepsEngineChecks.php';

#[\PHPUnit\Framework\Attributes\Group('database')]
class expMigrationContentStepsDatabaseTest extends ezpDatabaseTestCase
{
    use expMigrationContentStepsEngineChecks;

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
    public function testContentAndLocations()
    {
        $this->checkContentAndLocations( eZDB::instance() );
    }

    /** CD-2 */
    public function testContentRevert()
    {
        $this->checkContentRevert( eZDB::instance() );
    }

    /** CD-3 */
    public function testContentTransactions()
    {
        $this->checkContentTransactions( eZDB::instance() );
    }
}
