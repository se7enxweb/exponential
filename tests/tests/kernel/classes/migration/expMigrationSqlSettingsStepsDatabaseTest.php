<?php
/**
 * SQL migrations, INI migrations and the step types schema, data, sql and settings on a real database server:
 * MySQL/MariaDB and PostgreSQL in the database job (tests/runtests.php --dsn ... --db-per-test), or a throwaway MySQL
 * database of ezpThrowawayDatabase. The database has the kernel schema and the clean data. Skipped without one.
 *
 *  QD-1 QE-1 QE-2 a table, a column and indexes by the schema DSL, rows by data and the engine's statements, a
 *            dry run, the rollback; an SQL migration of per-engine files and its down part
 *  QD-2 QE-3      a CREATE TABLE before a failing statement: undone or kept, as the engine does
 *  QD-3 QE-4      an INI migration writes settings/override and a siteaccess with a backup; a dry run writes nothing
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group database
 */

require_once __DIR__ . '/expMigrationSqlSettingsStepsEngineChecks.php';

#[\PHPUnit\Framework\Attributes\Group('database')]
class expMigrationSqlSettingsStepsDatabaseTest extends ezpDatabaseTestCase
{
    use expMigrationSqlSettingsStepsEngineChecks;

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

    /** QD-1 */
    public function testSchemaDataAndSqlFiles()
    {
        $this->checkSchemaDataAndSqlFiles( eZDB::instance() );
    }

    /** QD-2 */
    public function testSchemaChangeTransactions()
    {
        $this->checkSchemaChangeTransactions( eZDB::instance() );
    }

    /** QD-3 */
    public function testIniMigration()
    {
        $this->checkIniMigration( eZDB::instance() );
    }
}
