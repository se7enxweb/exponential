<?php
/**
 * exp:migration:generate --from-diff on a real database server: MySQL/MariaDB and PostgreSQL in the database job
 * (tests/runtests.php --dsn ... --db-per-test), or a throwaway MySQL database of ezpThrowawayDatabase. The database has
 * the kernel schema and the clean data; the saved state is a SQLite file with the same clean data. Skipped without one.
 *
 *  DD-1 DF-1 a dump with known differences
 *  DD-2 DF-2 a caption with settings
 *  DD-3 DF-3 no differences, an unknown source
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group database
 */

require_once __DIR__ . '/expMigrationDiffEngineChecks.php';

#[\PHPUnit\Framework\Attributes\Group('database')]
class expMigrationDiffDatabaseTest extends ezpDatabaseTestCase
{
    use expMigrationDiffEngineChecks;

    protected function setUp(): void
    {
        parent::setUp();
        $engine = expKernelTableSchema::type( eZDB::instance() );
        if ( !in_array( $engine, array( 'mysql', 'postgresql' ), true ) )
            $this->markTestSkipped( "not a MySQL or PostgreSQL database: $engine" );
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded (the saved state is a SQLite file)' );
        $this->recordingBegin( eZDB::instance() );
    }

    protected function tearDown(): void
    {
        $this->recordingEnd();
        parent::tearDown();
    }

    /** DD-1 */
    public function testDiffWithDump()
    {
        $this->checkDiffWithDump( eZDB::instance() );
    }

    /** DD-2 */
    public function testDiffWithCaption()
    {
        $this->checkDiffWithCaption( eZDB::instance() );
    }

    /** DD-3 */
    public function testNoDifference()
    {
        $this->checkNoDifference( eZDB::instance() );
    }
}
