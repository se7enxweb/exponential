<?php
/**
 * Captions and the restore after a failure on SQLite in process: a SQLite file under var/tmp with the kernel schema
 * and the clean data, the captions copies of it (VACUUM INTO), the restore through SQLite's backup API; all removed
 * afterwards. No server and no installation's database.
 *
 *  CS-1 CE-1 a failed run restored to its pre caption (expMigrationCaptionEngineChecks)
 *  CS-2 CE-2 the post caption, and its reuse while nothing changed
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationCaptionEngineChecks.php';

class expMigrationCaptionSqliteTest extends PHPUnit\Framework\TestCase
{
    use expMigrationCaptionEngineChecks;

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
    }

    protected function tearDown(): void
    {
        if ( $this->db )
            $this->db->close();
        $this->runnerEnd();
    }

    /** CS-1 */
    public function testRestoreAfterFailure()
    {
        $this->checkRestoreAfterFailure( $this->db );
    }

    /** CS-2 */
    public function testPostCaptionAndReuse()
    {
        $this->checkPostCaptionAndReuse( $this->db );
    }
}
