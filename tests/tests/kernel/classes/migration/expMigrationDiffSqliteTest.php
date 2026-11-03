<?php
/**
 * exp:migration:generate --from-diff on SQLite in process: the installation is a SQLite file under var/tmp with the
 * kernel schema (kernel/sql/sqlite/schema.sql) and the clean data, the saved state a second copy; both removed
 * afterwards. No server and no installation's database.
 *
 *  DS-1 DF-1 a dump with known differences
 *  DS-2 DF-2 a caption with settings
 *  DS-3 DF-3 no differences, an unknown source
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationDiffEngineChecks.php';

class expMigrationDiffSqliteTest extends PHPUnit\Framework\TestCase
{
    use expMigrationDiffEngineChecks;

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

    /** DS-1 */
    public function testDiffWithDump()
    {
        $this->checkDiffWithDump( $this->db );
    }

    /** DS-2 */
    public function testDiffWithCaption()
    {
        $this->checkDiffWithCaption( $this->db );
    }

    /** DS-3 */
    public function testNoDifference()
    {
        $this->checkNoDifference( $this->db );
    }
}
