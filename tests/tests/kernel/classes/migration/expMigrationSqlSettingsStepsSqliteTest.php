<?php
/**
 * SQL migrations, INI migrations and the step types schema, data, sql and settings on SQLite in process: a SQLite file
 * under var/tmp with the kernel schema of kernel/sql/sqlite/schema.sql and the clean data, removed afterwards. No
 * server and no installation's database.
 *
 *  QS-1 QE-1 QE-2 a table, a column and indexes by the schema DSL, rows by data and the engine's statements, a
 *            dry run, the rollback; an SQL migration of per-engine files and its down part
 *  QS-2 QE-3      a CREATE TABLE before a failing statement: undone or kept, as the engine does
 *  QS-3 QE-4      an INI migration writes settings/override and a siteaccess with a backup; a dry run writes nothing
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationSqlSettingsStepsEngineChecks.php';

class expMigrationSqlSettingsStepsSqliteTest extends PHPUnit\Framework\TestCase
{
    use expMigrationSqlSettingsStepsEngineChecks;

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

    /** QS-1 */
    public function testSchemaDataAndSqlFiles()
    {
        $this->checkSchemaDataAndSqlFiles( $this->db );
    }

    /** QS-2 */
    public function testSchemaChangeTransactions()
    {
        $this->checkSchemaChangeTransactions( $this->db );
    }

    /** QS-3 */
    public function testIniMigration()
    {
        $this->checkIniMigration( $this->db );
    }
}
