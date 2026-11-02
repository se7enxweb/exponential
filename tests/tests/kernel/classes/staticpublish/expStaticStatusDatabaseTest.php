<?php
/**
 * The static publishing record on a real database server: MySQL/MariaDB and PostgreSQL in the database job
 * (tests/runtests.php --dsn ... --db-per-test). Skipped without one. The tables exp_static_run and exp_static_page of
 * the test database are dropped first and made again by the upgrade step.
 *
 *  SS-D1 the upgrade step, write and read (expStaticStatusEngineChecks)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group database
 */

require_once __DIR__ . '/expStaticStatusEngineChecks.php';

#[\PHPUnit\Framework\Attributes\Group('database')]
class expStaticStatusDatabaseTest extends ezpDatabaseTestCase
{
    use expStaticStatusEngineChecks;

    protected $insertDefaultData = false;

    /** SS-D1 */
    public function testOnTheServer()
    {
        $db = eZDB::instance();
        $engine = expKernelTableSchema::type( $db );
        if ( !in_array( $engine, array( 'mysql', 'postgresql' ), true ) )
            $this->markTestSkipped( "not a MySQL or PostgreSQL database: $engine" );
        $this->checkStatusTables( $db );
        $this->checkStatusWriteAndRead( $db );
        $this->checkStatusQueue( $db );
    }
}
