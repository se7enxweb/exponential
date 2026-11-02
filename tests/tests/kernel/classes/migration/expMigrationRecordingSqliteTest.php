<?php
/**
 * The recording of changes on SQLite in process: a SQLite file under var/tmp with the kernel schema
 * (kernel/sql/sqlite/schema.sql) and the clean data, removed afterwards. No server and no installation's database.
 *
 *  RS-1 RC-1 RC-2 the tables, a session recording (a class, content, a role assignment, a hidden location), a
 *       site-wide window, stop, plan and discard
 *  RS-2 RC-3 a user published as content: no personal data recorded
 *  RS-3 RC-4 a migration generated from what is there
 *  RS-4 RC-5 a site-wide window of time that closes by itself
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationRecordingEngineChecks.php';

class expMigrationRecordingSqliteTest extends PHPUnit\Framework\TestCase
{
    use expMigrationRecordingEngineChecks;

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
        $this->recordingBegin( $this->db );
        foreach ( array( 'schema.sql', 'cleandata.sql' ) as $sql )
            $this->assertNotFalse( $this->db->insertFile( self::runnerRoot() . '/kernel/sql/sqlite', $sql, false ), "kernel/sql/sqlite/$sql loads" );
    }

    protected function tearDown(): void
    {
        if ( $this->db )
            $this->db->close();
        $this->recordingEnd();
    }

    /** RS-1 */
    public function testRecordSession()
    {
        $this->checkRecordSession( $this->db );
    }

    /** RS-2 */
    public function testUserLeftOut()
    {
        $this->checkUserLeftOut( $this->db );
    }

    /** RS-3 */
    public function testGenerateFromExisting()
    {
        $this->checkGenerateFromExisting( $this->db );
    }

    /** RS-4 */
    public function testRecordingWindow()
    {
        $this->checkRecordingWindow( $this->db );
    }
}
