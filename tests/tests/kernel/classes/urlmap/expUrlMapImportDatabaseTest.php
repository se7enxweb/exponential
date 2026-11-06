<?php
/**
 * URL map import, export and undo on a real database server: MySQL/MariaDB and PostgreSQL in the database job
 * (tests/runtests.php --dsn ... --db-per-test), with the kernel schema and the clean data. Skipped without one.
 *
 *  UD-1 UE-1 to UE-6 (expUrlMapEngineChecks): the upgrade step, import, resolve, export, overwrite, undo
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group database
 */

require_once dirname( __DIR__ ) . '/migration/expMigrationRunnerEngineChecks.php';
require_once __DIR__ . '/expUrlMapEngineChecks.php';

#[\PHPUnit\Framework\Attributes\Group('database')]
class expUrlMapImportDatabaseTest extends ezpDatabaseTestCase
{
    use expMigrationRunnerEngineChecks;
    use expUrlMapEngineChecks;

    protected function setUp(): void
    {
        parent::setUp();
        $engine = expKernelTableSchema::type( eZDB::instance() );
        if ( !in_array( $engine, array( 'mysql', 'postgresql', 'oracle' ), true ) )
            $this->markTestSkipped( "not a MySQL, PostgreSQL or Oracle database: $engine" );
        $this->runnerBegin( eZDB::instance() );
        expUrlRedirect::resetRequest();
    }

    protected function tearDown(): void
    {
        expUrlRedirect::resetRequest();
        $this->runnerEnd();
        parent::tearDown();
    }

    /** UD-1 */
    public function testImportResolveExportUndo()
    {
        $db = eZDB::instance();
        $this->checkUrlMapTables( $db, self::runnerRoot() );
        $this->checkUrlMapImportResolveExportUndo( $db );
    }
}
