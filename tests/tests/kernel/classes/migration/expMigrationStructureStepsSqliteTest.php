<?php
/**
 * The structure step types on SQLite in process: a SQLite file under var/tmp with the kernel schema of
 * kernel/sql/sqlite/schema.sql and the clean data, removed afterwards. No server and no installation's database.
 * Skipped without symfony/yaml.
 *
 *  SS-1 SE-1 classes, attributes, sections, users, groups, roles, states through the runner
 *  SS-2 SE-2 steps that undo themselves, rolled back
 *  SS-3 SE-3 the deletes
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationStructureStepsEngineChecks.php';

class expMigrationStructureStepsSqliteTest extends PHPUnit\Framework\TestCase
{
    use expMigrationStructureStepsEngineChecks;

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
        // the shipped SQLite schema: it keys classes and object attributes on (id, version) as the .dba does
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

    /** SS-1 */
    public function testStructure()
    {
        $this->checkStructure( $this->db );
    }

    /** SS-2 */
    public function testStructureRevert()
    {
        $this->checkStructureRevert( $this->db );
    }

    /** SS-3 */
    public function testStructureDeletes()
    {
        $this->checkStructureDeletes( $this->db );
    }
}
