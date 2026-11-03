<?php
/**
 * The migration record on a real database server: MySQL/MariaDB and PostgreSQL in the database job
 * (tests/runtests.php --dsn ... --db-per-test), or a throwaway MySQL database of ezpThrowawayDatabase. Skipped without
 * one. The tables exp_migration and exp_migration_run of the test database are dropped first and made again by the
 * upgrade step; the next step copies the rows of a pre-release expupgrade_step the test writes, then drops it.
 *
 *  MR-D1 create, copy and drop, re-run idempotent, write and read, exp:upgrade's record (expMigrationRecordEngineChecks)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group database
 */

require_once __DIR__ . '/expMigrationRecordEngineChecks.php';

#[\PHPUnit\Framework\Attributes\Group('database')]
class expMigrationRecordDatabaseTest extends ezpDatabaseTestCase
{
    use expMigrationRecordEngineChecks;

    protected $insertDefaultData = false;

    /** MR-D1 */
    public function testOnTheServer()
    {
        $db = eZDB::instance();
        $engine = expKernelTableSchema::type( $db );
        if ( !in_array( $engine, array( 'mysql', 'postgresql', 'oracle' ), true ) )
            $this->markTestSkipped( "not a MySQL, PostgreSQL or Oracle database: $engine" );
        $this->checkCreateAndCopy( $db );
        $this->checkWriteAndRead( $db );
        $this->checkUpgradeRecord( $db );
    }
}
