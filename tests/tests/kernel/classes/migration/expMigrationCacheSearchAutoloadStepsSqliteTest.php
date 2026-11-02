<?php
/**
 * The step types cache and search, and the caches and search index after a run, on SQLite in process: a SQLite file
 * under var/tmp with the kernel schema of kernel/sql/sqlite/schema.sql and the clean data, removed afterwards. No
 * server and no installation's database. Skipped without symfony/yaml.
 *
 *  CS-1 CE-1 the run's cache list, the deploy and the touched objects queued; a reindex at once
 *  CS-2 CE-2 a full reindex of a class queued; a class that does not exist fails
 *  CS-3 CE-3 Reindex=disabled and a dry run
 *  CS-4 CE-4 the caches of a rollback
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationCacheSearchAutoloadStepsEngineChecks.php';

class expMigrationCacheSearchAutoloadStepsSqliteTest extends PHPUnit\Framework\TestCase
{
    use expMigrationCacheSearchAutoloadStepsEngineChecks;

    /** @var eZSQLite3DB|null */
    private $db;

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        $this->contentBegin();
        $this->db = new eZSQLite3DB( array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => $this->runnerDir() . '/kernel.sqlite',
                                            'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false, 'slave_user' => '',
                                            'slave_password' => '', 'slave_database' => '', 'socket' => false, 'charset' => 'utf-8',
                                            'is_internal_charset' => false, 'builtin_encoding' => false, 'connect_retries' => 1,
                                            'use_persistent_connection' => false, 'show_errors' => false ) );
        $this->runnerBegin( $this->db );
        foreach ( array( 'schema.sql', 'cleandata.sql' ) as $sql )
            $this->assertNotFalse( $this->db->insertFile( self::runnerRoot() . '/kernel/sql/sqlite', $sql, false ), "kernel/sql/sqlite/$sql loads" );
    }

    protected function tearDown(): void
    {
        if ( $this->db )
            $this->db->close();
        $this->runnerEnd();
        $this->contentEnd();
    }

    /** CS-1 */
    public function testCachesAndReindex()
    {
        $this->checkCachesAndReindex( $this->db );
    }

    /** CS-2 */
    public function testFullReindex()
    {
        $this->checkFullReindex( $this->db );
    }

    /** CS-3 */
    public function testReindexSettings()
    {
        $this->checkReindexSettings( $this->db );
    }

    /** CS-4 */
    public function testRollbackCaches()
    {
        $this->checkRollbackCaches( $this->db );
    }
}
