<?php
/**
 * Captions and the restore after a failure on a real database server: MySQL/MariaDB (mysqldump, mysql) and
 * PostgreSQL (pg_dump, psql) in the database job (tests/runtests.php --dsn ... --db-per-test), or a throwaway MySQL
 * database of ezpThrowawayDatabase. The database has the kernel schema and the clean data. Skipped without one, or
 * without the engine's tools.
 *
 *  CD-1 CE-1 a failed run restored to its pre caption (expMigrationCaptionEngineChecks)
 *  CD-2 CE-2 the post caption, and its reuse while nothing changed
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group database
 */

require_once __DIR__ . '/expMigrationCaptionEngineChecks.php';

#[\PHPUnit\Framework\Attributes\Group('database')]
class expMigrationCaptionDatabaseTest extends ezpDatabaseTestCase
{
    use expMigrationCaptionEngineChecks;

    protected function setUp(): void
    {
        parent::setUp();
        $engine = expKernelTableSchema::type( eZDB::instance() );
        if ( !in_array( $engine, array( 'mysql', 'postgresql', 'oracle' ), true ) )
            $this->markTestSkipped( "not a MySQL, PostgreSQL or Oracle database: $engine" );
        // Oracle has no built-in dump: CE-3 needs no tool
        if ( $engine !== 'oracle' )
            $this->requireTools( $engine === 'mysql' ? array( 'mysqldump', 'mysql' ) : array( 'pg_dump', 'psql' ) );
        $this->runnerBegin( eZDB::instance() );
    }

    protected function tearDown(): void
    {
        $this->runnerEnd();
        parent::tearDown();
    }

    /** CD-1 */
    public function testRestoreAfterFailure()
    {
        $this->checkRestoreAfterFailure( eZDB::instance() );
    }

    /** CD-2 */
    public function testPostCaptionAndReuse()
    {
        $this->checkPostCaptionAndReuse( eZDB::instance() );
    }
}
