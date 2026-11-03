<?php
/**
 * YAML migrations on a real database server: MySQL/MariaDB and PostgreSQL in the database job (tests/runtests.php
 * --dsn ... --db-per-test), or a throwaway MySQL database of ezpThrowawayDatabase. The database has the kernel schema
 * and the clean data. Skipped without one, and without symfony/yaml.
 *
 *  YD-1 YE-1 YE-2 the fixtures run through the runner, then rolled back by down: and by revert()
 *  YD-2 YE-3 a failing YAML migration: its note is rolled back on PostgreSQL and MySQL alike (no schema change in it)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group database
 */

require_once __DIR__ . '/expMigrationYamlEngineChecks.php';

#[\PHPUnit\Framework\Attributes\Group('database')]
class expMigrationYamlDatabaseTest extends ezpDatabaseTestCase
{
    use expMigrationYamlEngineChecks;

    protected function setUp(): void
    {
        parent::setUp();
        $engine = expKernelTableSchema::type( eZDB::instance() );
        if ( !in_array( $engine, array( 'mysql', 'postgresql', 'oracle' ), true ) )
            $this->markTestSkipped( "not a MySQL, PostgreSQL or Oracle database: $engine" );
        $this->yamlBegin();
        $this->runnerBegin( eZDB::instance() );
    }

    protected function tearDown(): void
    {
        $this->runnerEnd();
        $this->yamlEnd();
        parent::tearDown();
    }

    /** YD-1 */
    public function testYamlRunAndRollback()
    {
        $this->checkYamlRunAndRollback( eZDB::instance() );
    }

    /** YD-2 */
    public function testYamlTransactions()
    {
        $this->checkYamlTransactions( eZDB::instance() );
    }
}
