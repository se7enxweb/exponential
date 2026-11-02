<?php
/**
 * The recording of changes on a real database server: MySQL/MariaDB and PostgreSQL in the database job
 * (tests/runtests.php --dsn ... --db-per-test), or a throwaway MySQL database of ezpThrowawayDatabase. The database has
 * the kernel schema and the clean data. Skipped without one.
 *
 *  RD-1 RC-1 RC-2 the tables, a session recording (a class, content, a role assignment, a hidden location), a
 *       site-wide window, stop, plan and discard
 *  RD-2 RC-3 a user published as content: no personal data recorded
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group database
 */

require_once __DIR__ . '/expMigrationRecordingEngineChecks.php';

#[\PHPUnit\Framework\Attributes\Group('database')]
class expMigrationRecordingDatabaseTest extends ezpDatabaseTestCase
{
    use expMigrationRecordingEngineChecks;

    protected function setUp(): void
    {
        parent::setUp();
        $engine = expKernelTableSchema::type( eZDB::instance() );
        if ( !in_array( $engine, array( 'mysql', 'postgresql' ), true ) )
            $this->markTestSkipped( "not a MySQL or PostgreSQL database: $engine" );
        $this->recordingBegin( eZDB::instance() );
    }

    protected function tearDown(): void
    {
        $this->recordingEnd();
        parent::tearDown();
    }

    /** RD-1 */
    public function testRecordSession()
    {
        $this->checkRecordSession( eZDB::instance() );
    }

    /** RD-2 */
    public function testUserLeftOut()
    {
        $this->checkUserLeftOut( eZDB::instance() );
    }
}
