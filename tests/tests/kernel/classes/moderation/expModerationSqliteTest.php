<?php
/**
 * Dashboard > Recent content on SQLite in the process: a database file of its own with the kernel schema of
 * share/db_schema.dba and the clean data (expModerationEngineChecks, ME-1 to ME-6).
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/moderation/expModerationSqliteTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expModerationEngineChecks.php';

class expModerationSqliteTest extends PHPUnit\Framework\TestCase
{
    use expModerationEngineChecks;

    /** @var eZSQLite3DB|null */
    private $db;

    /** @var string */
    private $dir;

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        $root = dirname( __DIR__, 5 );
        $this->dir = $root . '/var/tmp/moderation-engine-' . getmypid() . '-' . bin2hex( random_bytes( 3 ) );
        mkdir( $this->dir, 0775, true );
        $this->db = new eZSQLite3DB( array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => $this->dir . '/kernel.sqlite',
                                            'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false, 'slave_user' => '',
                                            'slave_password' => '', 'slave_database' => '', 'socket' => false, 'charset' => 'utf-8',
                                            'is_internal_charset' => false, 'builtin_encoding' => false, 'connect_retries' => 1,
                                            'use_persistent_connection' => false, 'show_errors' => false ) );
        $this->moderationBegin( $this->db );
        $messages = array();
        $this->assertTrue( expKernelTableSchema::install( array(), $this->db, $root . '/share/db_schema.dba', $messages ),
                           'the kernel schema: ' . implode( '; ', array_filter( $messages, function ( $m ) { return strpos( $m, 'created ' ) !== 0; } ) ) );
        $this->assertNotFalse( $this->db->insertFile( $root . '/kernel/sql/sqlite', 'cleandata.sql', false ), 'kernel/sql/sqlite/cleandata.sql loads' );
    }

    protected function tearDown(): void
    {
        $this->moderationEnd();
        if ( $this->db )
            $this->db->close();
        foreach ( (array)glob( $this->dir . '/*' ) as $file )
            @unlink( $file );
        @rmdir( $this->dir );
        parent::tearDown();
    }

    public function testModerationList()
    {
        $this->checkModerationList( $this->db );
        $plan = $this->db->arrayQuery( 'EXPLAIN QUERY PLAN SELECT id FROM ezcontentobject ORDER BY modified DESC, id DESC LIMIT 25' );
        $this->assertStringContainsString( 'ezcontentobject_modified', json_encode( $plan ), 'the page is read from the index on modified' );
    }
}
