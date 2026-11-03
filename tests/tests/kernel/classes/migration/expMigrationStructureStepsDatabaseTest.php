<?php
/**
 * The structure step types on a real database server: MySQL/MariaDB and PostgreSQL in the database job
 * (tests/runtests.php --dsn ... --db-per-test), or a throwaway MySQL database of ezpThrowawayDatabase. The database has
 * the kernel schema and the clean data. Skipped without one, and without symfony/yaml.
 *
 *  SD-1 SE-1 classes, attributes, sections, users, groups, roles, states through the runner
 *  SD-2 SE-2 steps that undo themselves, rolled back
 *  SD-3 SE-3 the deletes
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group database
 */

require_once __DIR__ . '/expMigrationStructureStepsEngineChecks.php';

#[\PHPUnit\Framework\Attributes\Group('database')]
class expMigrationStructureStepsDatabaseTest extends ezpDatabaseTestCase
{
    use expMigrationStructureStepsEngineChecks;

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

    /** SD-1 */
    public function testStructure()
    {
        $this->checkStructure( eZDB::instance() );
    }

    /** SD-2 */
    public function testStructureRevert()
    {
        $this->checkStructureRevert( eZDB::instance() );
    }

    /** SD-3 */
    public function testStructureDeletes()
    {
        $this->checkStructureDeletes( eZDB::instance() );
    }
}
