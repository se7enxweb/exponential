<?php
/**
 * Dashboard > Recent content on a database server: MySQL/MariaDB and PostgreSQL in the database job
 * (tests/runtests.php --dsn ... --db-per-test), or a throwaway MySQL database of ezpThrowawayDatabase, with the kernel
 * schema and the clean data (expModerationEngineChecks, ME-1 to ME-6). Skipped without one.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group database
 */

require_once __DIR__ . '/expModerationEngineChecks.php';

#[\PHPUnit\Framework\Attributes\Group('database')]
class expModerationDatabaseTest extends ezpDatabaseTestCase
{
    use expModerationEngineChecks;

    protected function setUp(): void
    {
        parent::setUp();
        $engine = expKernelTableSchema::type( eZDB::instance() );
        if ( !in_array( $engine, array( 'mysql', 'postgresql' ), true ) )
            $this->markTestSkipped( "not a MySQL or PostgreSQL database: $engine" );
        $this->moderationBegin( eZDB::instance() );
    }

    protected function tearDown(): void
    {
        $this->moderationEnd();
        parent::tearDown();
    }

    public function testModerationList()
    {
        $this->checkModerationList( eZDB::instance() );
    }
}
