<?php
/**
 * YAML migrations on SQLite in process: a SQLite file under var/tmp with the kernel schema (kernel/sql/sqlite/schema.sql)
 * and the clean data, removed afterwards. No server and no installation's database. Skipped without symfony/yaml.
 *
 *  YS-1 YE-1 YE-2 the fixtures run through the runner, with loops, references, conditions, the id map and identifiers,
 *       then rolled back by down: and by revert()
 *  YS-2 YE-3 a failing YAML migration: its note is rolled back with it
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationYamlEngineChecks.php';

class expMigrationYamlSqliteTest extends PHPUnit\Framework\TestCase
{
    use expMigrationYamlEngineChecks;

    /** @var eZSQLite3DB|null */
    private $db;

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        $this->yamlBegin();
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
        $this->yamlEnd();
    }

    /** YS-1 */
    public function testYamlRunAndRollback()
    {
        $this->checkYamlRunAndRollback( $this->db );
    }

    /** YS-2 */
    public function testYamlTransactions()
    {
        $this->checkYamlTransactions( $this->db );
    }
}
