<?php
/**
 * The content step types on SQLite in process: a SQLite file under var/tmp with the kernel schema of share/db_schema.dba
 * (expKernelTableSchema::install()) and the clean data, removed afterwards. No server and no installation's database.
 * Skipped without symfony/yaml.
 *
 *  CS-1 CE-1 content, location and content_version steps through the runner
 *  CS-2 CE-2 steps that undo themselves, rolled back
 *  CS-3 CE-3 a failing migration: its object is rolled back with it
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationContentStepsEngineChecks.php';

class expMigrationContentStepsSqliteTest extends PHPUnit\Framework\TestCase
{
    use expMigrationContentStepsEngineChecks;

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
        // the schema of share/db_schema.dba, as the installer makes it: the hand-written kernel/sql/sqlite/schema.sql
        // keys ezcontentobject_attribute on id alone, so no second version of an object could be stored
        $messages = array();
        $this->assertTrue( expKernelTableSchema::install( array(), $this->db, self::runnerRoot() . '/share/db_schema.dba', $messages ),
                           'the kernel schema: ' . implode( '; ', array_filter( $messages, function ( $m ) { return strpos( $m, 'created ' ) !== 0; } ) ) );
        $this->assertNotFalse( $this->db->insertFile( self::runnerRoot() . '/kernel/sql/sqlite', 'cleandata.sql', false ), 'kernel/sql/sqlite/cleandata.sql loads' );
    }

    protected function tearDown(): void
    {
        if ( $this->db )
            $this->db->close();
        $this->runnerEnd();
        $this->contentEnd();
    }

    /** CS-1 */
    public function testContentAndLocations()
    {
        $this->checkContentAndLocations( $this->db );
    }

    /** CS-2 */
    public function testContentRevert()
    {
        $this->checkContentRevert( $this->db );
    }

    /** CS-3 */
    public function testContentTransactions()
    {
        $this->checkContentTransactions( $this->db );
    }
}
