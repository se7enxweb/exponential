<?php
/**
 * URL map import, export and undo on SQLite in process: a SQLite file under var/tmp with the kernel schema of
 * kernel/sql/sqlite/schema.sql and the clean data, removed afterwards. No server and no installation's database.
 *
 *  US-1 UE-1 the upgrade step 6.0.15-url-map
 *  US-2 UE-2 to UE-6 import, resolve, export, overwrite, undo
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/migration/expMigrationRunnerEngineChecks.php';
require_once __DIR__ . '/expUrlMapEngineChecks.php';

class expUrlMapImportSqliteTest extends PHPUnit\Framework\TestCase
{
    use expMigrationRunnerEngineChecks;
    use expUrlMapEngineChecks;

    /** @var eZSQLite3DB|null */
    private $db;

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        $this->db = new eZSQLite3DB( array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => $this->runnerDir() . '/kernel.sqlite',
                                            'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false, 'slave_user' => '',
                                            'slave_password' => '', 'slave_database' => '', 'socket' => false, 'charset' => 'utf-8',
                                            'is_internal_charset' => false, 'builtin_encoding' => false, 'connect_retries' => 1,
                                            'use_persistent_connection' => false, 'show_errors' => false ) );
        $this->runnerBegin( $this->db );
        foreach ( array( 'schema.sql', 'cleandata.sql' ) as $sql )
            $this->assertNotFalse( $this->db->insertFile( self::runnerRoot() . '/kernel/sql/sqlite', $sql, false ), "kernel/sql/sqlite/$sql loads" );
        expUrlRedirect::resetRequest();
    }

    protected function tearDown(): void
    {
        expUrlRedirect::resetRequest();
        if ( $this->db )
            $this->db->close();
        $this->runnerEnd();
    }

    /** US-1, US-2 */
    public function testImportResolveExportUndo()
    {
        $this->checkUrlMapTables( $this->db, self::runnerRoot() );
        $this->checkUrlMapImportResolveExportUndo( $this->db );
    }
}
